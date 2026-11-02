<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= empty($_SESSION['admin_id']) ? 'Buat Admin Pertama' : 'Tambah Admin Baru' ?></title>
    <link rel="stylesheet" href="views/style.css">
</head>
<body class="form-page">
<main class="form-card">
    <h1><?= empty($_SESSION['admin_id']) ? 'Buat Admin Pertama' : 'Tambah Admin' ?></h1>
    <p><?= empty($_SESSION['admin_id']) ? 'Buat akun untuk mengakses kelola konten.' : 'Isi informasi admin atau nail artist baru.' ?></p>
    <form method="POST" action="">
        <label for="username">Username</label>
        <input id="username" type="text" name="username" required>

        <label for="password">Password</label>
        <input id="password" type="password" name="password" required>

        <label for="nama_lengkap">Nama Lengkap</label>
        <input id="nama_lengkap" type="text" name="nama_lengkap" required>

        <label for="spesialisasi">Spesialisasi</label>
        <input id="spesialisasi" type="text" name="spesialisasi" required>

        <label for="no_hp">No. HP</label>
        <input id="no_hp" type="tel" name="no_hp" required>

        <div class="actions-row">
            <button type="submit">Simpan</button>
            <a href="index.php">Kembali</a>
        </div>
    </form>
</main>
</body>
</html>