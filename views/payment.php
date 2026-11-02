<?php
$escape = static function ($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
};
$appointmentDate = new DateTime($order['tanggal_pesan']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pembayaran - Nailsbygekya</title>
    <link rel="stylesheet" href="views/style.css">
</head>
<body class="form-page">
<main class="form-card payment-card">
    <div class="payment-status">
        <span class="status-dot" aria-hidden="true"></span>
        <span><?= $escape($order['status_pembayaran']) ?></span>
    </div>
    <h1>Pemesanan berhasil dibuat</h1>
    <p>Silakan konfirmasi pembayaran dengan studio. Status akan diperbarui setelah pembayaran diterima.</p>

    <dl class="payment-details">
        <div><dt>Nomor pemesanan</dt><dd>#<?= (int) $order['id_pemesanan'] ?></dd></div>
        <div><dt>Nama pelanggan</dt><dd><?= $escape($order['nama_pelanggan']) ?></dd></div>
        <div><dt>Jadwal</dt><dd><?= $escape($appointmentDate->format('d-m-Y, H:i')) ?></dd></div>
        <div><dt>Layanan</dt><dd><?= $escape($order['nama_layanan']) ?></dd></div>
        <div><dt>Desain</dt><dd><?= $escape($order['nama_katalog']) ?></dd></div>
        <div><dt>Nail artist</dt><dd><?= $escape($order['nama_admin']) ?></dd></div>
        <div><dt>Metode pembayaran</dt><dd><?= $escape($order['metode_pembayaran']) ?></dd></div>
        <div class="payment-total"><dt>Estimasi total</dt><dd>Rp<?= number_format((float) $order['total_harga'], 0, ',', '.') ?></dd></div>
    </dl>

    <div class="payment-note">
        <?php if ($order['metode_pembayaran'] === 'Tunai di Studio'): ?>
            <strong>Pembayaran tunai</strong>
            <p>Silakan lakukan pembayaran di studio pada jadwal yang telah dipilih.</p>
        <?php elseif ($order['metode_pembayaran'] === 'E-Wallet'): ?>
            <strong>Scan QRIS untuk E-Wallet</strong>
            <p>Silakan pindai (scan) kode QRIS di bawah ini menggunakan aplikasi e-wallet Anda (GoPay, OVO, DANA, dll):</p>
            <div style="text-align: center; margin: 15px 0;">
                <!-- Mengarah ke file qris.png di dalam folder uploads -->
                <img src="uploads/qris.png" alt="QRIS Pembayaran" style="max-width: 180px; height: auto; border-radius: 8px; border: 1px solid #ddd; padding: 5px; background: #fff;">
            </div>
        <?php else: ?>
            <strong>Pembayaran belum dikonfirmasi</strong>
            <p>Hubungi studio untuk mendapatkan detail rekening transfer bank. Jangan mengirim pembayaran ke rekening yang belum dikonfirmasi oleh studio.</p>
        <?php endif; ?>
        <p>Status pemesanan: <?= $escape($order['status_pemesanan']) ?></p>
    </div>
    <div class="actions-row">
        <a class="button" href="index.php">Kembali ke halaman utama</a>
        <a href="index.php?action=order">Buat pemesanan lain</a>
    </div>
</main>
</body>
</html>