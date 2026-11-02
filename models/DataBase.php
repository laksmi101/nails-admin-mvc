<?php
class Database {
    private $host = "localhost";
    private $user = "root";
    private $pass = "";
    private $db = "db_uts";
    public $conn;

    public function __construct() {
        $this->conn = new mysqli($this->host, $this->user, $this->pass, $this->db);
        if ($this->conn->connect_error) {
            die("Koneksi database gagal: " . $this->conn->connect_error);
        }

        $this->conn->set_charset("utf8mb4");
        $lockResult = $this->conn->query("SELECT GET_LOCK('nails_admin_mvc_schema', 10)");
        $lock = $lockResult->fetch_row();
        if (!$lock || (int) $lock[0] !== 1) {
            throw new RuntimeException('Tidak dapat mengunci inisialisasi database.');
        }

        $this->conn->query(
            "CREATE TABLE IF NOT EXISTS tb_admin (
                id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(100) NOT NULL,
                password VARCHAR(255) NOT NULL,
                nama_lengkap VARCHAR(150) NOT NULL,
                spesialisasi VARCHAR(120) NOT NULL,
                no_hp VARCHAR(30) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );
        $this->conn->query(
            "CREATE TABLE IF NOT EXISTS tb_layanan (
                id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                kategori VARCHAR(30) NOT NULL,
                nama VARCHAR(120) NOT NULL,
                deskripsi VARCHAR(255) NOT NULL,
                harga VARCHAR(80) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );
        $this->conn->query(
            "CREATE TABLE IF NOT EXISTS tb_katalog (
                id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                kategori VARCHAR(30) NOT NULL,
                judul VARCHAR(120) NOT NULL,
                deskripsi VARCHAR(255) NOT NULL,
                foto VARCHAR(255) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );
        $this->conn->query(
            "CREATE TABLE IF NOT EXISTS tb_owner_profile (
                id TINYINT UNSIGNED NOT NULL PRIMARY KEY,
                nama VARCHAR(150) NOT NULL DEFAULT '',
                umur TINYINT UNSIGNED NULL,
                sifat VARCHAR(500) NOT NULL DEFAULT '',
                status_hubungan VARCHAR(80) NOT NULL DEFAULT 'Lajang',
                foto VARCHAR(255) NOT NULL DEFAULT ''
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );
        $this->conn->query("INSERT IGNORE INTO tb_owner_profile (id) VALUES (1)");

        $this->conn->query(
            "INSERT INTO tb_layanan (kategori, nama, deskripsi, harga)
            SELECT data.kategori, data.nama, data.deskripsi, data.harga
            FROM (
                SELECT 'tangan' AS kategori, 'Manicure basic' AS nama, 'Perawatan kuku dan kutikula' AS deskripsi, 'Rp35.000' AS harga
                UNION ALL SELECT 'tangan', 'Gel polish', 'Warna gel dengan hasil glossy', 'Rp75.000'
                UNION ALL SELECT 'tangan', 'Nail art sederhana', 'Aksen nail art mulai 2 kuku', 'Mulai Rp10.000/kuku'
                UNION ALL SELECT 'tangan', 'Extension gel', 'Panjang dan bentuk kuku pilihan', 'Mulai Rp150.000'
                UNION ALL SELECT 'kaki', 'Pedicure basic', 'Perawatan kuku dan kulit kaki', 'Rp45.000'
                UNION ALL SELECT 'kaki', 'Gel polish kaki', 'Warna gel untuk kuku kaki', 'Rp85.000'
                UNION ALL SELECT 'kaki', 'Nail art kaki', 'Aksen nail art pada kuku kaki', 'Mulai Rp15.000/kuku'
            ) AS data
            WHERE NOT EXISTS (SELECT 1 FROM tb_layanan)"
        );
        $this->conn->query(
            "INSERT INTO tb_katalog (kategori, judul, deskripsi, foto)
            SELECT data.kategori, data.judul, data.deskripsi, ''
            FROM (
                SELECT 'tangan' AS kategori, 'Soft blush' AS judul, 'Nude pink dengan sentuhan kilau' AS deskripsi
                UNION ALL SELECT 'tangan', 'French minimal', 'French tip klasik yang elegan'
                UNION ALL SELECT 'tangan', 'Berry glaze', 'Warna berry dengan efek glossy'
                UNION ALL SELECT 'kaki', 'Milky white', 'Putih susu yang bersih dan manis'
                UNION ALL SELECT 'kaki', 'Cherry red', 'Merah cerah untuk tampilan berani'
                UNION ALL SELECT 'kaki', 'Lavender mood', 'Ungu lembut untuk suasana santai'
            ) AS data
            WHERE NOT EXISTS (SELECT 1 FROM tb_katalog)"
        );
        $this->conn->query("SELECT RELEASE_LOCK('nails_admin_mvc_schema')");
    }
}
?>