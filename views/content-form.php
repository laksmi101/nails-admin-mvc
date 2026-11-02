<?php
$escape = static function ($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
};
$isService = $type === 'service';
$title = $content[$isService ? 'nama' : 'judul'] ?? '';
$category = $content['kategori'] ?? 'tangan';
$description = $content['deskripsi'] ?? '';
$pageTitle = !empty($content['id']) ? 'Edit ' . ($isService ? 'Layanan' : 'Foto Katalog') : 'Tambah ' . ($isService ? 'Layanan' : 'Foto Katalog');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $escape($pageTitle) ?></title>
    <link rel="stylesheet" href="views/style.css">
</head>
<body class="form-page content-form-page">
<main class="form-card">
    <h1><?= $escape($pageTitle) ?></h1>
    <?php if ($error !== ''): ?><p class="error"><?= $escape($error) ?></p><?php endif; ?>
    <form method="POST" action="index.php?action=content-save" enctype="multipart/form-data">
        <input type="hidden" name="type" value="<?= $isService ? 'service' : 'catalog' ?>">
        <?php if (!empty($content['id'])): ?><input type="hidden" name="id" value="<?= (int) $content['id'] ?>"><?php endif; ?>

        <label for="kategori">Kategori</label>
        <select id="kategori" name="kategori" required>
            <option value="tangan" <?= $category === 'tangan' ? 'selected' : '' ?>>Nails Tangan</option>
            <option value="kaki" <?= $category === 'kaki' ? 'selected' : '' ?>>Nails Kaki</option>
        </select>

        <label for="<?= $isService ? 'nama' : 'judul' ?>"><?= $isService ? 'Nama layanan' : 'Judul foto / desain' ?></label>
        <input id="<?= $isService ? 'nama' : 'judul' ?>" type="text" name="<?= $isService ? 'nama' : 'judul' ?>" maxlength="120" value="<?= $escape($title) ?>" required>

        <label for="deskripsi">Deskripsi</label>
        <textarea id="deskripsi" name="deskripsi" maxlength="255" required><?= $escape($description) ?></textarea>

        <?php if ($isService): ?>
            <label for="harga">Harga</label>
            <input id="harga" type="text" name="harga" maxlength="80" placeholder="Contoh: Rp75.000" value="<?= $escape($content['harga'] ?? '') ?>" required>
        <?php else: ?>
            <label for="foto">Foto hasil nail art</label>
            <input id="foto" type="file" name="foto" accept="image/jpeg,image/png,image/webp" <?= empty($content['foto']) ? 'required' : '' ?>>
            <p class="hint">Format JPG, PNG, atau WEBP. Maksimal 5 MB. Foto opsional jika foto lama sudah ada.</p>
            <?php if (!empty($content['foto'])): ?><img class="preview" src="<?= $escape($content['foto']) ?>" alt="Foto saat ini"><?php endif; ?>
        <?php endif; ?>

        <div class="actions-row">
            <button type="submit">Simpan</button>
            <a href="index.php?action=manage">Batal</a>
        </div>
    </form>
</main>
</body>
</html>
