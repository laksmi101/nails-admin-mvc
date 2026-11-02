<?php
require_once __DIR__ . '/../models/AdminModel.php';

class AdminController {
    private $model;

    public function __construct() {
        $this->model = new AdminModel();
    }

    public function index() {
        $isAdmin = !empty($_SESSION['admin_id']) &&
            $this->model->getAdminById((int) $_SESSION['admin_id']);
        if (!$isAdmin) {
            unset($_SESSION['admin_id']);
        }
        $admins = $isAdmin ? $this->model->getAllAdmin() : [];

        // --- IMPLEMENTASI SEARCH & PAGINATION (Maksimal 10 data per halaman) ---
        $limit = 10;
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $offset = ($page - 1) * $limit;
        $keyword = trim($_GET['search'] ?? '');

        // Mengambil data layanan berdasarkan pencarian dan pagination
        $totalServices = $this->model->getTotalLayanan($keyword);
        $services = $this->model->getLayananByPage($limit, $offset, $keyword);
        $totalPages = ceil($totalServices / $limit);

        // Mengambil data katalog
        $catalog = $this->model->getAllCatalogItems();
        // ---------------------------------------------------------------------

        require __DIR__ . '/../views/index.php';
    }

    public function manage() {
        $this->requireAdmin();
        $services = $this->model->getAllServices();
        $catalog = $this->model->getAllCatalogItems();
        $owner = $this->model->getOwnerProfile();
        $pemesanan = $this->model->getAllPemesanan(); // <-- Mengambil data pemesanan untuk admin
        $artists = $this->model->getAllAdmin(); // <-- Mengambil data nail artist / staf untuk admin
        require __DIR__ . '/../views/manage.php';
    }

    // --- TAMBAHAN METHOD UNTUK ACC PEMBAYARAN ---
    public function updateStatus() {
        $this->requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit('Metode permintaan tidak diizinkan.');
        }

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $statusPembayaran = trim($_POST['status_pembayaran'] ?? 'Lunas');
        $statusPemesanan = trim($_POST['status_pemesanan'] ?? 'Diterima');

        if (!$id) {
            http_response_code(400);
            exit('ID pemesanan tidak valid.');
        }

        // Memanggil method di model untuk memperbarui status di database
        $updated = $this->model->updateOrderStatus($id, $statusPembayaran, $statusPemesanan);
        if (!$updated) {
            throw new RuntimeException('Status pemesanan gagal diperbarui di database.');
        }

        header('Location: index.php?action=manage');
        exit;
    }
    // ---------------------------------------------

    // --- FITUR KELOLA NAIL ARTIST / STAF DI ADMIN ---
    public function editArtist() {
        $this->requireAdmin();
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        $artist = $id ? $this->model->getAdminById($id) : null;
        if ($id && !$artist) {
            http_response_code(404);
            exit('Data nail artist tidak ditemukan.');
        }
        $error = '';
        require __DIR__ . '/../views/artist-form.php';
    }

    public function saveArtist() {
        $this->requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit('Metode permintaan tidak diizinkan.');
        }

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $name = trim($_POST['nama_lengkap'] ?? '');
        $specialization = trim($_POST['spesialisasi'] ?? '');
        $phone = trim($_POST['no_hp'] ?? '');

        if ($username === '' || strlen($username) > 100 ||
            $name === '' || strlen($name) > 150 ||
            $specialization === '' || strlen($specialization) > 120 ||
            $phone === '' || strlen($phone) > 30 || (!$id && $password === '')) {
            http_response_code(422);
            $error = 'Mohon isi semua data nail artist dengan benar.';
            $artist = compact('id', 'username', 'nama_lengkap', 'spesialisasi', 'no_hp');
            require __DIR__ . '/../views/artist-form.php';
            return;
        }

        if ($id) {
            if ($password !== '') {
                $saved = $this->model->updateAdminWithPassword($id, $username, $password, $name, $specialization, $phone);
            } else {
                $saved = $this->model->updateAdmin($id, $username, $name, $specialization, $phone);
            }
        } else {
            if ($this->model->getAdminByUsername($username)) {
                http_response_code(409);
                $error = 'Username sudah digunakan.';
                $artist = compact('username', 'nama_lengkap', 'spesialisasi', 'no_hp');
                require __DIR__ . '/../views/artist-form.php';
                return;
            }
            $saved = $this->model->insertAdmin($username, $password, $name, $specialization, $phone);
        }

        if (!$saved) {
            throw new RuntimeException('Data nail artist gagal disimpan.');
        }

        header('Location: index.php?action=manage');
        exit;
    }

    public function deleteArtist() {
        $this->requireAdmin();
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            http_response_code(400);
            exit('ID nail artist tidak valid.');
        }
        $this->model->deleteAdmin($id);
        header('Location: index.php?action=manage');
        exit;
    }
    // ------------------------------------------------

    // --- FITUR LOGIN & SESSION PELANGGAN (USER) ---
    public function userLogin() {
        if (!empty($_SESSION['user_name'])) {
            header('Location: index.php?action=order');
            exit;
        }

        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nama = trim($_POST['nama_pelanggan'] ?? '');
            $telepon = trim($_POST['no_telepon'] ?? '');

            if ($nama !== '' && strlen($nama) <= 100 && preg_match('/^[0-9+().\-\s]{6,20}$/', $telepon)) {
                $_SESSION['user_name'] = $nama;
                $_SESSION['user_phone'] = $telepon;
                header('Location: index.php?action=order');
                exit;
            } else {
                $error = 'Mohon masukkan nama lengkap dan nomor telepon yang valid.';
            }
        }
        require __DIR__ . '/../views/user_login.php';
    }

    public function userLogout() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit('Metode permintaan tidak diizinkan.');
        }
        unset($_SESSION['user_name']);
        unset($_SESSION['user_phone']);
        header('Location: index.php');
        exit;
    }
    // ---------------------------------------------

    public function order() {
        if (empty($_SESSION['user_name'])) {
            header('Location: index.php?action=user-login');
            exit;
        }

        $services = $this->model->getAllServices();
        $catalog = $this->model->getAllCatalogItems();
        $admins = $this->model->getAllAdmin();
        $formData = [];
        $error = '';
        require __DIR__ . '/../views/order.php';
    }

    public function createOrder() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit('Metode permintaan tidak diizinkan.');
        }

        $formData = [
            'nama_pelanggan' => trim($_POST['nama_pelanggan'] ?? ''),
            'no_telepon' => trim($_POST['no_telepon'] ?? ''),
            'tanggal_pesan' => trim($_POST['tanggal_pesan'] ?? ''),
            'id_layanan' => filter_input(INPUT_POST, 'id_layanan', FILTER_VALIDATE_INT),
            'id_katalog' => filter_input(INPUT_POST, 'id_katalog', FILTER_VALIDATE_INT),
            'id_admin' => filter_input(INPUT_POST, 'id_admin', FILTER_VALIDATE_INT),
            'metode_pembayaran' => $_POST['metode_pembayaran'] ?? '',
        ];

        $services = $this->model->getAllServices();
        $catalog = $this->model->getAllCatalogItems();
        $admins = $this->model->getAllAdmin();
        $service = $formData['id_layanan'] ? $this->model->getServiceById($formData['id_layanan']) : null;
        $catalogItem = $formData['id_katalog'] ? $this->model->getCatalogItemById($formData['id_katalog']) : null;
        $admin = $formData['id_admin'] ? $this->model->getAdminById($formData['id_admin']) : null;
        $appointment = DateTime::createFromFormat('Y-m-d\TH:i', $formData['tanggal_pesan']);
        $dateErrors = DateTime::getLastErrors();
        $validDate = $appointment &&
            $appointment->format('Y-m-d\TH:i') === $formData['tanggal_pesan'] &&
            (!$dateErrors || ($dateErrors['warning_count'] === 0 && $dateErrors['error_count'] === 0)) &&
            $appointment > new DateTime();
        $priceDigits = $service ? preg_replace('/\D+/', '', $service['harga']) : '';
        $total = $priceDigits !== '' ? (float) $priceDigits : 0;
        $paymentMethods = ['Transfer Bank', 'E-Wallet', 'Tunai di Studio'];

        if ($formData['nama_pelanggan'] === '' || strlen($formData['nama_pelanggan']) > 100 ||
            !preg_match('/^[0-9+().\-\s]{6,20}$/', $formData['no_telepon']) ||
            !$validDate || !$service || !$catalogItem || !$admin ||
            !in_array($formData['metode_pembayaran'], $paymentMethods, true) ||
            $total <= 0 || $total > 99999999.99) {
            http_response_code(422);
            $error = 'Periksa kembali nama, nomor telepon, jadwal, layanan, desain, nail artist, dan metode pembayaran.';
            require __DIR__ . '/../views/order.php';
            return;
        }

        $orderId = $this->model->insertPemesanan(
            (int) $catalogItem['id'],
            (int) $service['id'],
            (int) $admin['id'],
            $formData['nama_pelanggan'],
            $formData['no_telepon'],
            $appointment->format('Y-m-d H:i:s'),
            $total,
            $formData['metode_pembayaran']
        );
        if (!$orderId) {
            throw new RuntimeException('Pemesanan gagal disimpan ke database.');
        }

        $_SESSION['latest_order_id'] = (int) $orderId;
        header('Location: index.php?action=payment');
        exit;
    }

    public function payment() {
        $orderId = (int) ($_SESSION['latest_order_id'] ?? 0);
        if (!$orderId) {
            header('Location: index.php?action=order');
            exit;
        }
        $order = $this->model->getPemesananById($orderId);
        if (!$order) {
            unset($_SESSION['latest_order_id']);
            http_response_code(404);
            exit('Data pemesanan tidak ditemukan.');
        }
        require __DIR__ . '/../views/payment.php';
    }

    public function checkStatus() {
        $order = null;
        $error = '';
        if (isset($_GET['id_pemesanan'])) {
            $id = filter_input(INPUT_GET, 'id_pemesanan', FILTER_VALIDATE_INT);
            if ($id) {
                $order = $this->model->getPemesananById($id);
                if (!$order) {
                    $error = 'Nomor pemesanan tidak ditemukan.';
                }
            } else {
                $error = 'Masukkan nomor pemesanan yang valid.';
            }
        }
        require __DIR__ . '/../views/check-status.php';
    }

    public function editOwnerProfile() {
        $this->requireAdmin();
        $owner = $this->model->getOwnerProfile();
        $this->renderOwnerForm($owner, '');
    }

    public function saveOwnerProfile() {
        $this->requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit('Metode permintaan tidak diizinkan.');
        }

        $owner = $this->model->getOwnerProfile();
        $name = trim($_POST['nama'] ?? '');
        $ageInput = trim($_POST['umur'] ?? '');
        $personality = trim($_POST['sifat'] ?? '');
        $relationshipStatus = trim($_POST['status_hubungan'] ?? '');
        $age = filter_var($ageInput, FILTER_VALIDATE_INT);
        if ($name === '' || strlen($name) > 150 ||
            $age === false || $age < 1 || $age > 120 ||
            $personality === '' || strlen($personality) > 500 ||
            $relationshipStatus === '' || strlen($relationshipStatus) > 80) {
            http_response_code(422);
            $error = 'Isi nama, umur 1–120, sifat, dan status dengan benar.';
            $owner = array_merge($owner ?? [], $_POST);
            $this->renderOwnerForm($owner, $error);
            return;
        }

        $newPhoto = null;
        try {
            $newPhoto = $this->storePhotoUpload(empty($owner['foto']), 'owner');
        } catch (InvalidArgumentException $exception) {
            http_response_code(422);
            $owner = array_merge($owner ?? [], $_POST);
            $this->renderOwnerForm($owner, $exception->getMessage());
            return;
        }

        $photo = $newPhoto ?? $owner['foto'];
        try {
            $saved = $this->model->updateOwnerProfile(
                $name,
                $age,
                $personality,
                $relationshipStatus,
                $photo
            );
        } catch (mysqli_sql_exception $exception) {
            if ($newPhoto) {
                $this->removeStoredPhoto($newPhoto);
            }
            throw $exception;
        }

        if (!$saved) {
            if ($newPhoto) {
                $this->removeStoredPhoto($newPhoto);
            }
            throw new RuntimeException('Profil owner gagal disimpan ke database.');
        }

        if ($newPhoto && !empty($owner['foto'])) {
            $this->removeStoredPhoto($owner['foto']);
        }

        header('Location: index.php?action=manage');
        exit;
    }

    public function create() {
        $firstAdminSetup = !$this->model->hasAdmins();
        if (!$firstAdminSetup) {
            $this->requireAdmin();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';
            $name = trim($_POST['nama_lengkap'] ?? '');
            $specialization = trim($_POST['spesialisasi'] ?? '');
            $phone = trim($_POST['no_hp'] ?? '');
            if ($username === '' || strlen($username) > 100 || $password === '' ||
                $name === '' || strlen($name) > 150 ||
                $specialization === '' || strlen($specialization) > 120 ||
                $phone === '' || strlen($phone) > 30) {
                http_response_code(422);
                exit('Mohon isi semua data admin dengan benar.');
            }
            if ($this->model->getAdminByUsername($username)) {
                http_response_code(409);
                exit('Username admin sudah digunakan.');
            }
            $this->model->insertAdmin(
                $username,
                $password,
                $name,
                $specialization,
                $phone
            );
            header('Location: index.php?action=' . (!empty($_SESSION['admin_id']) ? 'manage' : 'login'));
            exit;
        }
        require __DIR__ . '/../views/create.php';
    }

    public function login() {
        if (!empty($_SESSION['admin_id'])) {
            header('Location: index.php?action=manage');
            exit;
        }

        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';
            $admin = $username !== '' ? $this->model->getAdminByUsername($username) : null;
            $storedPassword = $admin['password'] ?? '';
            $passwordInfo = password_get_info($storedPassword);
            $isHashedPassword = !empty($passwordInfo['algo']);
            $validPassword = $admin && $password !== '' && ($isHashedPassword
                ? password_verify($password, $storedPassword)
                : hash_equals($storedPassword, $password));

            if ($validPassword) {
                if (!$isHashedPassword) {
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                    if (!$this->model->updateAdminPassword($admin['id'], $hashedPassword)) {
                        throw new RuntimeException('Password admin gagal diperbarui.');
                    }
                }
                session_regenerate_id(true);
                $_SESSION['admin_id'] = (int) $admin['id'];
                header('Location: index.php?action=manage');
                exit;
            }

            $error = 'Username atau password tidak sesuai.';
        }

        $canSetupAdmin = !$this->model->hasAdmins();
        require __DIR__ . '/../views/login.php';
    }

    public function logout() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit('Metode permintaan tidak diizinkan.');
        }

        $_SESSION = [];
        session_destroy();
        header('Location: index.php');
        exit;
    }

    public function edit() {
        $this->requireAdmin();
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            http_response_code(400);
            exit('ID admin tidak valid.');
        }
        $admin = $this->model->getAdminById($id);
        if (!$admin) {
            http_response_code(404);
            exit('Data admin tidak ditemukan.');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->model->updateAdmin(
                $id,
                $_POST['username'],
                $_POST['nama_lengkap'],
                $_POST['spesialisasi'],
                $_POST['no_hp']
            );
            header('Location: index.php');
            exit;
        }
        require __DIR__ . '/../views/edit.php';
    }

    public function delete() {
        $this->requireAdmin();
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            http_response_code(400);
            exit('ID admin tidak valid.');
        }
        $this->model->deleteAdmin($id);
        header('Location: index.php');
        exit;
    }

    public function editContent() {
        $this->requireAdmin();
        $type = $_GET['type'] ?? '';
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!in_array($type, ['service', 'catalog'], true) || $id === false) {
            http_response_code(400);
            exit('Data yang diminta tidak valid.');
        }

        $content = $id ? $this->model->getContentById($type, $id) : null;
        if ($id && !$content) {
            http_response_code(404);
            exit('Data tidak ditemukan.');
        }

        $this->renderContentForm($type, $content, '');
    }

    public function saveContent() {
        $this->requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit('Metode permintaan tidak diizinkan.');
        }

        $type = $_POST['type'] ?? '';
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if (!in_array($type, ['service', 'catalog'], true) || ($id !== null && $id === false)) {
            http_response_code(400);
            exit('Data yang dikirim tidak valid.');
        }

        $content = $id ? $this->model->getContentById($type, $id) : null;
        if ($id && !$content) {
            http_response_code(404);
            exit('Data tidak ditemukan.');
        }

        $category = $_POST['kategori'] ?? '';
        $categoryLabel = $category === 'tangan' ? 'Nails Tangan' : ($category === 'kaki' ? 'Nails Kaki' : '');
        $title = trim($_POST[$type === 'service' ? 'nama' : 'judul'] ?? '');
        $description = trim($_POST['deskripsi'] ?? '');
        $price = trim($_POST['harga'] ?? '');
        if ($categoryLabel === '' || $title === '' || $description === '' ||
            strlen($title) > 120 || strlen($description) > 255 ||
            ($type === 'service' && ($price === '' || strlen($price) > 80))) {
            http_response_code(422);
            $content = array_merge($content ?? [], $_POST);
            $this->renderContentForm($type, $content, 'Mohon isi semua kolom dengan benar.');
            return;
        }

        $newPhoto = null;
        try {
            if ($type === 'catalog') {
                $newPhoto = $this->storePhotoUpload(!$content, 'gallery');
            }
        } catch (InvalidArgumentException $exception) {
            http_response_code(422);
            $content = array_merge($content ?? [], $_POST);
            $this->renderContentForm($type, $content, $exception->getMessage());
            return;
        }

        try {
            if ($type === 'service') {
                $saved = $id
                    ? $this->model->updateService($id, $category, $title, $description, $price)
                    : $this->model->insertService($category, $title, $description, $price);
            } else {
                $photo = $newPhoto ?? $content['foto'];
                $saved = $id
                    ? $this->model->updateCatalogItem($id, $category, $title, $description, $photo)
                    : $this->model->insertCatalogItem($category, $title, $description, $photo);
            }
        } catch (mysqli_sql_exception $exception) {
            if ($newPhoto) {
                $this->removeStoredPhoto($newPhoto);
            }
            throw $exception;
        }

        if (!$saved) {
            if ($newPhoto) {
                $this->removeStoredPhoto($newPhoto);
            }
            throw new RuntimeException('Data gagal disimpan ke database.');
        }

        if ($newPhoto && $content && !empty($content['foto'])) {
            $this->removeStoredPhoto($content['foto']);
        }

        header('Location: index.php?action=manage');
        exit;
    }

    public function deleteContent() {
        $this->requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit('Metode permintaan tidak diizinkan.');
        }

        $type = $_POST['type'] ?? '';
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if (!in_array($type, ['service', 'catalog'], true) || !$id) {
            http_response_code(400);
            exit('Data yang dikirim tidak valid.');
        }

        $content = $this->model->getContentById($type, $id);
        if (!$content) {
            http_response_code(404);
            exit('Data tidak ditemukan.');
        }
        if (!$this->model->deleteContent($type, $id)) {
            throw new RuntimeException('Data gagal dihapus dari database.');
        }
        if ($type === 'catalog' && !empty($content['foto'])) {
            $this->removeStoredPhoto($content['foto']);
        }

        header('Location: index.php?action=manage');
        exit;
    }

    private function storePhotoUpload($required, $folder) {
        if (!isset($_FILES['foto']) || $_FILES['foto']['error'] === UPLOAD_ERR_NO_FILE) {
            if ($required) {
                throw new InvalidArgumentException('Pilih foto hasil nail art untuk katalog.');
            }
            return null;
        }

        $upload = $_FILES['foto'];
        if ($upload['error'] !== UPLOAD_ERR_OK) {
            throw new InvalidArgumentException('Foto gagal diunggah. Coba pilih file kembali.');
        }
        if ($upload['size'] > 5 * 1024 * 1024) {
            throw new InvalidArgumentException('Ukuran foto maksimal 5 MB.');
        }

        $image = getimagesize($upload['tmp_name']);
        $extensions = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
        ];
        $mime = $image['mime'] ?? '';
        if (!$image || !isset($extensions[$mime])) {
            throw new InvalidArgumentException('Format foto harus JPG, PNG, atau WEBP.');
        }

        if (!in_array($folder, ['gallery', 'owner'], true)) {
            throw new LogicException('Folder unggahan tidak valid.');
        }
        $directory = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . $folder;
        if (!is_dir($directory) && !mkdir($directory, 0755, true) && !is_dir($directory)) {
            throw new RuntimeException('Folder penyimpanan foto tidak dapat dibuat.');
        }

        $filename = bin2hex(random_bytes(16)) . '.' . $extensions[$mime];
        if (!move_uploaded_file($upload['tmp_name'], $directory . DIRECTORY_SEPARATOR . $filename)) {
            throw new RuntimeException('Foto tidak dapat disimpan ke folder katalog.');
        }
        return 'uploads/' . $folder . '/' . $filename;
    }

    private function requireAdmin() {
        if (empty($_SESSION['admin_id'])) {
            header('Location: index.php?action=login');
            exit;
        }

        if (!$this->model->getAdminById((int) $_SESSION['admin_id'])) {
            unset($_SESSION['admin_id']);
            header('Location: index.php?action=login');
            exit;
        }
    }

    private function renderOwnerForm($owner, $error) {
        require __DIR__ . '/../views/owner-form.php';
    }

    private function renderContentForm($type, $content, $error) {
        require __DIR__ . '/../views/content-form.php';
    }

    private function removeStoredPhoto($path) {
        $allowedPrefixes = ['uploads/gallery/', 'uploads/owner/'];
        $isManagedUpload = false;
        foreach ($allowedPrefixes as $prefix) {
            if (strpos($path, $prefix) === 0 && basename($path) === substr($path, strlen($prefix))) {
                $isManagedUpload = true;
                break;
            }
        }
        if (!$isManagedUpload) {
            return;
        }
        $file = dirname(__DIR__) . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $path);
        if (is_file($file) && !unlink($file)) {
            throw new RuntimeException('Foto lama tidak dapat dihapus dari folder katalog.');
        }
    }
}
?>