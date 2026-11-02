<?php
$escape = static fn($value) => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
$fields = [
    'username' => ['Username', 'text'],
    'nama_lengkap' => ['Nama Lengkap', 'text'],
    'spesialisasi' => ['Spesialisasi', 'text'],
    'no_hp' => ['No. HP', 'tel'],
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Data Admin</title>
    <link rel="stylesheet" href="views/style.css">
</head>
<body class="form-page">
<main class="form-card">
    <h1>Edit Data Admin</h1>
    <p>Perbarui informasi admin atau nail artist.</p>
    <form method="POST" action="">
        <?php foreach ($fields as $name => [$label, $type]): ?>
            <label for="<?= $name ?>"><?= $label ?></label>
            <input id="<?= $name ?>" type="<?= $type ?>" name="<?= $name ?>" value="<?= $escape($admin[$name]) ?>" required>
        <?php endforeach; ?>

        <div class="actions-row">
            <button type="submit">Simpan Perubahan</button>
            <a href="index.php">Kembali</a>
        </div>
    </form>
</main>
</body>
</html>