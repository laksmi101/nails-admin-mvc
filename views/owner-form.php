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
    <title>Edit Profil Owner</title>
    <link rel="stylesheet" href="views/style.css">
</head>
<body class="form-page">
<main class="form-card">
    <h1>Edit Profil Owner</h1>
    <p>Isi informasi owner dan unggah foto profil. Perubahan akan tampil di halaman utama.</p>
    <?php if ($error !== ''): ?><p class="error"><?= $escape($error) ?></p><?php endif; ?>
    <form method="POST" action="index.php?action=owner-save" enctype="multipart/form-data">
        <label for="nama">Nama owner</label>
        <input id="nama" type="text" name="nama" maxlength="150" value="<?= $escape($owner['nama'] ?? '') ?>" required>

        <label for="umur">Umur</label>
        <input id="umur" type="number" name="umur" min="1" max="120" value="<?= $escape($owner['umur'] ?? '') ?>" required>

        <label for="sifat">Sifat owner</label>
        <textarea id="sifat" name="sifat" maxlength="500" required><?= $escape($owner['sifat'] ?? '') ?></textarea>

        <label for="status_hubungan">Status hubungan</label>
        <input id="status_hubungan" type="text" name="status_hubungan" maxlength="80" value="<?= $escape($owner['status_hubungan'] ?? 'Lajang') ?>" required>

        <label for="foto">Foto owner</label>
        <input id="foto" type="file" name="foto" accept="image/jpeg,image/png,image/webp" <?= empty($owner['foto']) ? 'required' : '' ?>>
        <p class="hint">Format JPG, PNG, atau WEBP. Maksimal 5 MB. Foto lama tetap digunakan jika tidak memilih foto baru.</p>
        <?php if (!empty($owner['foto'])): ?><img class="preview" src="<?= $escape($owner['foto']) ?>" alt="Foto owner saat ini"><?php endif; ?>

        <div class="actions-row">
            <button type="submit">Simpan Profil</button>
            <a href="index.php?action=manage">Batal</a>
        </div>
    </form>
</main>
</body>
</html>
