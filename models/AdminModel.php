<?php
require_once __DIR__ . '/DataBase.php';

class AdminModel {
    private $db;

    public function __construct() {
        $this->db = (new Database())->conn;
    }

    public function getAllAdmin() {
        $result = $this->db->query("SELECT id, username, nama_lengkap, spesialisasi, no_hp FROM tb_admin");
        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        return $rows;
    }

    public function insertAdmin($username, $password, $nama_lengkap, $spesialisasi, $no_hp) {
        $password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("INSERT INTO tb_admin (username, password, nama_lengkap, spesialisasi, no_hp) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $username, $password, $nama_lengkap, $spesialisasi, $no_hp);
        return $stmt->execute();
    }

    public function getAdminByUsername($username) {
        $stmt = $this->db->prepare("SELECT id, username, password FROM tb_admin WHERE username = ? LIMIT 1");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function hasAdmins() {
        $result = $this->db->query("SELECT 1 FROM tb_admin LIMIT 1");
        return (bool) $result->fetch_row();
    }

    public function updateAdminPassword($id, $password) {
        $stmt = $this->db->prepare("UPDATE tb_admin SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $password, $id);
        return $stmt->execute();
    }

    public function getAdminById($id) {
        $stmt = $this->db->prepare("SELECT id, username, nama_lengkap, spesialisasi, no_hp FROM tb_admin WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function updateAdmin($id, $username, $nama_lengkap, $spesialisasi, $no_hp) {
        $stmt = $this->db->prepare("UPDATE tb_admin SET username = ?, nama_lengkap = ?, spesialisasi = ?, no_hp = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $username, $nama_lengkap, $spesialisasi, $no_hp, $id);
        return $stmt->execute();
    }

    public function deleteAdmin($id) {
        $stmt = $this->db->prepare("DELETE FROM tb_admin WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function getAllServices() {
        $result = $this->db->query("SELECT * FROM tb_layanan ORDER BY kategori, id");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // --- FITUR PAGINATION & SEARCH UNTUK LAYANAN ---
    public function getTotalLayanan($keyword = "") {
        if (!empty($keyword)) {
            $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM tb_layanan WHERE nama LIKE ? OR kategori LIKE ? OR deskripsi LIKE ?");
            $searchTerm = "%" . $keyword . "%";
            $stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();
            return $result['total'];
        } else {
            $result = $this->db->query("SELECT COUNT(*) as total FROM tb_layanan");
            return $result->fetch_assoc()['total'];
        }
    }

    public function getLayananByPage($limit, $offset, $keyword = "") {
        if (!empty($keyword)) {
            $stmt = $this->db->prepare("SELECT * FROM tb_layanan WHERE nama LIKE ? OR kategori LIKE ? OR deskripsi LIKE ? ORDER BY kategori, id LIMIT ? OFFSET ?");
            $searchTerm = "%" . $keyword . "%";
            $stmt->bind_param("sssii", $searchTerm, $searchTerm, $searchTerm, $limit, $offset);
            $stmt->execute();
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        } else {
            $stmt = $this->db->prepare("SELECT * FROM tb_layanan ORDER BY kategori, id LIMIT ? OFFSET ?");
            $stmt->bind_param("ii", $limit, $offset);
            $stmt->execute();
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        }
    }
    // ---------------------------------------------

    public function getServiceById($id) {
        $stmt = $this->db->prepare("SELECT id, nama, harga FROM tb_layanan WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getAllCatalogItems() {
        $result = $this->db->query("SELECT * FROM tb_katalog ORDER BY kategori, id");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // --- FITUR PAGINATION & SEARCH UNTUK KATALOG ---
    public function getTotalKatalog($keyword = "") {
        if (!empty($keyword)) {
            $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM tb_katalog WHERE judul LIKE ? OR kategori LIKE ? OR deskripsi LIKE ?");
            $searchTerm = "%" . $keyword . "%";
            $stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();
            return $result['total'];
        } else {
            $result = $this->db->query("SELECT COUNT(*) as total FROM tb_katalog");
            return $result->fetch_assoc()['total'];
        }
    }

    public function getKatalogByPage($limit, $offset, $keyword = "") {
        if (!empty($keyword)) {
            $stmt = $this->db->prepare("SELECT * FROM tb_katalog WHERE judul LIKE ? OR kategori LIKE ? OR deskripsi LIKE ? ORDER BY kategori, id LIMIT ? OFFSET ?");
            $searchTerm = "%" . $keyword . "%";
            $stmt->bind_param("sssii", $searchTerm, $searchTerm, $searchTerm, $limit, $offset);
            $stmt->execute();
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        } else {
            $stmt = $this->db->prepare("SELECT * FROM tb_katalog ORDER BY kategori, id LIMIT ? OFFSET ?");
            $stmt->bind_param("ii", $limit, $offset);
            $stmt->execute();
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        }
    }
    // ---------------------------------------------

    public function getCatalogItemById($id) {
        $stmt = $this->db->prepare("SELECT id, judul FROM tb_katalog WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function insertPemesanan($catalogId, $serviceId, $adminId, $customerName, $phone, $date, $total, $paymentMethod) {
        $stmt = $this->db->prepare(
            "INSERT INTO tb_pemesanan
                (id_katalog, id_layanan, id_admin, nama_pelanggan, no_telepon, tanggal_pesan, total_harga, metode_pembayaran)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param(
            "iiisssds",
            $catalogId,
            $serviceId,
            $adminId,
            $customerName,
            $phone,
            $date,
            $total,
            $paymentMethod
        );
        if (!$stmt->execute()) {
            return false;
        }
        return $this->db->insert_id;
    }

    public function getPemesananById($id) {
        $stmt = $this->db->prepare(
            "SELECT p.id_pemesanan, p.nama_pelanggan, p.no_telepon, p.tanggal_pesan,
                    p.total_harga, p.metode_pembayaran, p.status_pembayaran, p.status_pemesanan,
                    l.nama AS nama_layanan, k.judul AS nama_katalog, a.nama_lengkap AS nama_admin
            FROM tb_pemesanan p
            INNER JOIN tb_layanan l ON l.id = p.id_layanan
            INNER JOIN tb_katalog k ON k.id = p.id_katalog
            INNER JOIN tb_admin a ON a.id = p.id_admin
            WHERE p.id_pemesanan = ?"
        );
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // --- AMBIL SEMUA DATA PEMESANAN UNTUK PANEL ADMIN ---
    public function getAllPemesanan() {
        $result = $this->db->query(
            "SELECT p.id_pemesanan, p.nama_pelanggan, p.no_telepon, p.tanggal_pesan,
                    p.total_harga, p.metode_pembayaran, p.status_pembayaran, p.status_pemesanan,
                    l.nama AS nama_layanan, k.judul AS nama_katalog, a.nama_lengkap AS nama_admin
            FROM tb_pemesanan p
            INNER JOIN tb_layanan l ON l.id = p.id_layanan
            INNER JOIN tb_katalog k ON k.id = p.id_katalog
            INNER JOIN tb_admin a ON a.id = p.id_admin
            ORDER BY p.id_pemesanan DESC"
        );
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    // --- TAMBAHAN METHOD UPDATE STATUS PEMESANAN (ACC PEMBAYARAN) ---
    public function updateOrderStatus($id, $statusPembayaran, $statusPemesanan) {
        $stmt = $this->db->prepare("UPDATE tb_pemesanan SET status_pembayaran = ?, status_pemesanan = ? WHERE id_pemesanan = ?");
        $stmt->bind_param("ssi", $statusPembayaran, $statusPemesanan, $id);
        return $stmt->execute();
    }
    // -------------------------------------------------------------

    public function getOwnerProfile() {
        $result = $this->db->query("SELECT * FROM tb_owner_profile WHERE id = 1");
        return $result->fetch_assoc();
    }

    public function updateOwnerProfile($name, $age, $personality, $relationshipStatus, $photo) {
        $stmt = $this->db->prepare(
            "UPDATE tb_owner_profile
            SET nama = ?, umur = ?, sifat = ?, status_hubungan = ?, foto = ?
            WHERE id = 1"
        );
        $stmt->bind_param("sisss", $name, $age, $personality, $relationshipStatus, $photo);
        return $stmt->execute();
    }

    public function getContentById($type, $id) {
        $table = $type === 'service' ? 'tb_layanan' : 'tb_katalog';
        $stmt = $this->db->prepare("SELECT * FROM {$table} WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function insertService($category, $name, $description, $price) {
        $stmt = $this->db->prepare("INSERT INTO tb_layanan (kategori, nama, deskripsi, harga) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $category, $name, $description, $price);
        return $stmt->execute();
    }

    public function updateService($id, $category, $name, $description, $price) {
        $stmt = $this->db->prepare("UPDATE tb_layanan SET kategori = ?, nama = ?, deskripsi = ?, harga = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $category, $name, $description, $price, $id);
        return $stmt->execute();
    }

    public function insertCatalogItem($category, $title, $description, $photo) {
        $stmt = $this->db->prepare("INSERT INTO tb_katalog (kategori, judul, deskripsi, foto) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $category, $title, $description, $photo);
        return $stmt->execute();
    }

    public function updateCatalogItem($id, $category, $title, $description, $photo) {
        $stmt = $this->db->prepare("UPDATE tb_katalog SET kategori = ?, judul = ?, deskripsi = ?, foto = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $category, $title, $description, $photo, $id);
        return $stmt->execute();
    }

    public function deleteContent($type, $id) {
        $table = $type === 'service' ? 'tb_layanan' : 'tb_katalog';
        $stmt = $this->db->prepare("DELETE FROM {$table} WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
?>