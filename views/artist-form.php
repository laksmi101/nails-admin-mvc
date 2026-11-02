<?php
$escape = static function ($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
};
$isEdit = !empty($artist['id']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $isEdit ? 'Edit Nail Artist' : 'Tambah Nail Artist' ?></title>
    <link rel="stylesheet" href="views/style.css">
</head>
<body class="manage-page">
<main class="manage-main" style="max-width: 600px; margin: 40px auto;">
    <header class="top">
        <div>
            <h1><?= $isEdit ? 'Edit Nail Artist' : 'Tambah Nail Artist Baru' ?></h1>
            <p>Silakan isi informasi data nail artist di bawah ini.</p>
        </div>
        <a class="secondary" href="index.php?action=manage">Kembali</a>
    </header>

    <?php if (!empty($error)): ?>
        <div style="background: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 20px;">
            <?= $escape($error) ?>
        </div>
    <?php endif; ?>

    <section class="manage-section">
        <form action="index.php?action=artist-save" method="POST">
            <?php if ($isEdit): ?>
                <input type="hidden" name="id" value="<?= (int) $artist['id'] ?>">
            <?php endif; ?>

            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: bold;">Username</label>
                <input type="text" name="username" class="form-control" value="<?= $escape($artist['username'] ?? '') ?>" style="width: 100%; padding: 8px;" required>
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: bold;">Password <?= $isEdit ? '(Kosongkan jika tidak ingin mengubah password)' : '' ?></label>
                <input type="password" name="password" class="form-control" style="width: 100%; padding: 8px;" <?= $isEdit ? '' : 'required' ?>>
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: bold;">Nama Lengkap & Gelar</label>
                <input type="text" name="nama_lengkap" class="form-control" value="<?= $escape($artist['nama_lengkap'] ?? '') ?>" placeholder="Contoh: Ni Putu Eka Mulianingsih" style="width: 100%; padding: 8px;" required>
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: bold;">Spesialisasi</label>
                <input type="text" name="spesialisasi" class="form-control" value="<?= $escape($artist['spesialisasi'] ?? '') ?>" placeholder="Contoh: Treatment Kaki / Nail Art Design" style="width: 100%; padding: 8px;" required>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 5px; font-weight: bold;">No. HP</label>
                <input type="text" name="no_hp" class="form-control" value="<?= $escape($artist['no_hp'] ?? '') ?>" placeholder="Contoh: 0898989008" style="width: 100%; padding: 8px;" required>
            </div>

            <button type="submit" class="button" style="width: 100%; padding: 10px; background: #28a745; color: #fff; border: none; border-radius: 4px; cursor: pointer;">Simpan Data Artist</button>
        </form>
    </section>
</main>
</body>
</html>