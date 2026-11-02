<?php
$escape = static function ($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
};
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cek Status Pemesanan - Nailsbygekya</title>
    <link rel="stylesheet" href="views/style.css">
</head>
<body class="form-page">
<main class="form-card payment-card">
    <h1>Cek Status Pemesanan</h1>
    <p>Masukkan nomor pemesanan Anda untuk melihat status terbaru.</p>

    <form method="GET" action="index.php" style="margin-bottom: 20px;">
        <!-- Sesuaikan action menjadi check-status agar sesuai dengan rute di index.php -->
        <input type="hidden" name="action" value="check-status">
        <div style="display: flex; gap: 10px;">
            <input type="number" name="id_pemesanan" placeholder="Contoh: 1, 2, 3..." value="<?= $escape($_GET['id_pemesanan'] ?? '') ?>" required style="flex: 1; padding: 8px;">
            <button type="submit" class="button" style="padding: 8px 16px;">Cek Status</button>
        </div>
    </form>

    <?php if (!empty($error)): ?>
        <p style="color: #d9534f; font-weight: bold; margin-bottom: 15px;"><?= $escape($error) ?></p>
    <?php endif; ?>

    <?php if (!empty($order)): ?>
        <?php $appointmentDate = new DateTime($order['tanggal_pesan']); ?>
        <div class="payment-status" style="margin-top: 15px;">
            <span class="status-dot" aria-hidden="true"></span>
            <span>Status: <strong><?= $escape($order['status_pemesanan']) ?></strong></span>
        </div>
        <dl class="payment-details" style="margin-top: 15px;">
            <div><dt>Nomor pemesanan</dt><dd>#<?= (int) $order['id_pemesanan'] ?></dd></div>
            <div><dt>Nama pelanggan</dt><dd><?= $escape($order['nama_pelanggan']) ?></dd></div>
            <div><dt>Jadwal</dt><dd><?= $escape($appointmentDate->format('d-m-Y, H:i')) ?></dd></div>
            <div><dt>Layanan</dt><dd><?= $escape($order['nama_layanan']) ?></dd></div>
            <div><dt>Status Pembayaran</dt><dd><?= $escape($order['status_pembayaran']) ?></dd></div>
        </dl>
    <?php endif; ?>

    <div class="actions-row" style="margin-top: 20px;">
        <a class="button" href="index.php">Kembali ke halaman utama</a>
    </div>
</main>
</body>
</html>