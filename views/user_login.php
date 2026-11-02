<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Pelanggan - Nailsbygekya</title>
    <link rel="stylesheet" href="views/style.css">
</head>
<body class="site-page" style="display: flex; justify-content: center; align-items: center; height: 100vh; background: #f4ece1;">
    <div style="background: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); width: 100%; max-width: 400px; border: 1px solid #e0d3c1;">
        <h2 style="color: #4a3525; margin-bottom: 10px; text-align: center;">💅 Masuk Pelanggan</h2>
        <p style="color: #6c757d; font-size: 0.9rem; text-align: center; margin-bottom: 20px;">Masukkan nama dan nomor HP Anda untuk mulai memesan.</p>
        
        <?php if (!empty($error)): ?>
            <div style="background: #f8d7da; color: #721c24; padding: 10px; border-radius: 6px; margin-bottom: 15px; font-size: 0.85rem;"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST" action="index.php?action=user-login">
            <div style="margin-bottom: 15px;">
                <label style="display: block; font-weight: 600; color: #4a3525; margin-bottom: 5px; font-size: 0.9rem;">Nama Lengkap</label>
                <input type="text" name="nama_pelanggan" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box;" placeholder="Contoh: Gekya">
            </div>
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 600; color: #4a3525; margin-bottom: 5px; font-size: 0.9rem;">No. Telepon / WhatsApp</label>
                <input type="text" name="no_telepon" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box;" placeholder="Contoh: 08123456789">
            </div>
            <button type="submit" style="width: 100%; background: #4a3525; color: #fff; border: none; padding: 12px; border-radius: 6px; font-weight: bold; cursor: pointer;">Masuk &amp; Lanjutkan Pemesanan</button>
        </form>
        <div style="text-align: center; margin-top: 15px;">
            <a href="index.php" style="color: #8c6d53; font-size: 0.85rem; text-decoration: none;">← Kembali ke Beranda</a>
        </div>
    </div>
</body>
</html>