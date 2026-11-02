<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Admin - Nailsbygekya</title>
    <link rel="stylesheet" href="views/style.css">
</head>
<body class="form-page">
<main class="form-card">
    <h1>Login Admin</h1>
    <p>Masuk untuk mengelola konten Nailsbygekya.</p>
    <?php if ($error !== ''): ?><p class="error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
    <form method="POST" action="index.php?action=login">
        <label for="username">Username</label>
        <input id="username" type="text" name="username" maxlength="100" required autofocus>

        <label for="password">Password</label>
        <input id="password" type="password" name="password" required>

        <div class="actions-row">
            <button type="submit">Masuk</button>
            <a href="index.php">Kembali</a>
        </div>
    </form>
    <?php if ($canSetupAdmin): ?>
        <p><a href="index.php?action=create">Buat akun admin pertama</a></p>
    <?php endif; ?>
</main>
</body>
</html>
