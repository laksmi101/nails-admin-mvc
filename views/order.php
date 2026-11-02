<?php
$escape = static function ($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
};
$categories = ['tangan' => 'Nails Tangan', 'kaki' => 'Nails Kaki'];
$selected = static function ($field, $value) use ($formData) {
    return (string) ($formData[$field] ?? '') === (string) $value ? 'selected' : '';
};

// Ambil data nama & telepon dari session login pelanggan
$sessionName = $_SESSION['user_name'] ?? '';
$sessionPhone = $_SESSION['user_phone'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pemesanan - Nailsbygekya</title>
    <link rel="stylesheet" href="views/style.css">
</head>
<body class="form-page">
<main class="form-card order-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
        <a class="secondary" href="index.php">← Kembali ke menu</a>
        <!-- Tombol Ganti Akun / Logout Pelanggan -->
        <form method="POST" action="index.php?action=user-logout" style="margin: 0;">
            <button type="submit" style="background: none; border: none; color: #d9534f; cursor: pointer; font-size: 0.85rem; font-weight: 600;">Ganti Pelanggan / Keluar</button>
        </form>
    </div>
    
    <h1>Buat Pemesanan</h1>
    <p>Halo, <strong><?= $escape($sessionName) ?></strong>! Pilih layanan dan jadwal yang kamu inginkan di bawah ini.</p>
    
    <?php if ($error !== ''): ?><p class="error"><?= $escape($error) ?></p><?php endif; ?>

    <?php if (!$services || !$catalog || !$admins): ?>
        <p class="error">Pemesanan belum tersedia. Pastikan menu layanan, katalog desain, dan nail artist sudah ditambahkan oleh admin.</p>
    <?php else: ?>
    <form method="POST" action="index.php?action=order-create">
        <!-- Input Nama (Otomatis dari session & disembunyikan/dikunci agar aman) -->
        <input type="hidden" name="nama_pelanggan" value="<?= $escape($sessionName) ?>">
        <input type="hidden" name="no_telepon" value="<?= $escape($sessionPhone) ?>">

        <div style="background: #fdfbf7; padding: 12px 15px; border-radius: 6px; margin-bottom: 20px; border: 1px solid #e0d3c1; font-size: 0.9rem; color: #4a3525;">
            <div>👤 <strong>Pemesan:</strong> <?= $escape($sessionName) ?></div>
            <div>📱 <strong>No. Telepon:</strong> <?= $escape($sessionPhone) ?></div>
        </div>

        <label for="tanggal_pesan">Tanggal dan waktu</label>
        <input id="tanggal_pesan" type="datetime-local" name="tanggal_pesan" min="<?= $escape(date('Y-m-d\TH:i')) ?>" value="<?= $escape($formData['tanggal_pesan'] ?? '') ?>" required>

        <label for="id_layanan">Layanan</label>
        <select id="id_layanan" name="id_layanan" required>
            <option value="">Pilih layanan</option>
            <?php foreach ($services as $service): ?>
                <option value="<?= (int) $service['id'] ?>" <?= $selected('id_layanan', $service['id']) ?>>
                    <?= $escape($service['nama']) ?> — <?= $escape($service['harga']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="id_katalog">Desain pilihan</label>
        <select id="id_katalog" name="id_katalog" required>
            <option value="">Pilih desain</option>
            <?php foreach ($catalog as $item): ?>
                <option value="<?= (int) $item['id'] ?>" <?= $selected('id_katalog', $item['id']) ?>>
                    <?= $escape($item['judul']) ?> (<?= $escape($categories[$item['kategori']] ?? $item['kategori']) ?>)
                </option>
            <?php endforeach; ?>
        </select>

        <label for="id_admin">Nail artist</label>
        <select id="id_admin" name="id_admin" required>
            <option value="">Pilih nail artist</option>
            <?php foreach ($admins as $admin): ?>
                <option value="<?= (int) $admin['id'] ?>" <?= $selected('id_admin', $admin['id']) ?>>
                    <?= $escape($admin['nama_lengkap']) ?> — <?= $escape($admin['spesialisasi']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="metode_pembayaran">Metode pembayaran</label>
        <select id="metode_pembayaran" name="metode_pembayaran" required>
            <option value="">Pilih metode pembayaran</option>
            <?php foreach (['Transfer Bank', 'E-Wallet', 'Tunai di Studio'] as $method): ?>
                <option value="<?= $escape($method) ?>" <?= $selected('metode_pembayaran', $method) ?>><?= $escape($method) ?></option>
            <?php endforeach; ?>
        </select>
        <p class="hint">Total menggunakan harga awal layanan. Biaya tambahan untuk desain atau panjang kuku akan dikonfirmasi oleh studio.</p>

        <div class="actions-row">
            <button type="submit">Lanjut ke Pembayaran</button>
            <a href="index.php">Batal</a>
        </div>
    </form>
    <?php endif; ?>
</main>
</body>
</html>