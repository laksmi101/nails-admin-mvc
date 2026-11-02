<?php
$escape = static function ($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
};
$categories = ['tangan' => 'Nails Tangan', 'kaki' => 'Nails Kaki'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kelola Menu &amp; Katalog</title>
    <link rel="stylesheet" href="views/style.css">
</head>
<body class="manage-page">
<main class="manage-main">
    <header class="top">
        <div>
            <h1>Kelola Menu &amp; Katalog</h1>
            <p>Atur harga layanan, foto hasil nail art, dan pantau pemesanan pelanggan.</p>
        </div>
        <a class="secondary" href="index.php">Kembali ke halaman utama</a>
    </header>

    <!-- SECTION: Profil Owner -->
    <section class="manage-section">
        <header class="section-head">
            <div>
                <h2>Profil Owner</h2>
                <p>Nama, umur, sifat, status hubungan, dan foto profil.</p>
            </div>
            <a class="button" href="index.php?action=owner-edit">Edit Profil Owner</a>
        </header>
        <?php if (!empty($owner['nama'])): ?>
            <div class="owner-summary">
                <?php if (!empty($owner['foto'])): ?><img class="owner-photo" src="<?= $escape($owner['foto']) ?>" alt="Foto <?= $escape($owner['nama']) ?>"><?php endif; ?>
                <div>
                    <strong><?= $escape($owner['nama']) ?></strong>
                    <p><?= (int) $owner['umur'] ?> tahun · <?= $escape($owner['status_hubungan']) ?></p>
                    <p><?= $escape($owner['sifat']) ?></p>
                </div>
            </div>
        <?php else: ?>
            <p>Profil owner belum dilengkapi.</p>
        <?php endif; ?>
    </section>

    <!-- SECTION: Kelola Nail Artist (Baru) -->
    <section class="manage-section">
        <header class="section-head">
            <div>
                <h2>Kelola Nail Artist / Staf</h2>
                <p>Tambah dan atur daftar nail artist yang tampil pada pilihan pemesanan.</p>
            </div>
            <a class="button" href="index.php?action=artist-edit">+ Tambah Nail Artist</a>
        </header>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Username</th>
                        <th>Nama Lengkap</th>
                        <th>Spesialisasi</th>
                        <th>No. HP</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($artists)): ?>
                    <tr><td class="empty" colspan="6">Belum ada nail artist terdaftar.</td></tr>
                <?php else: foreach ($artists as $index => $artist): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= $escape($artist['username']) ?></td>
                        <td><?= $escape($artist['nama_lengkap']) ?></td>
                        <td><?= $escape($artist['spesialisasi']) ?></td>
                        <td><?= $escape($artist['no_hp']) ?></td>
                        <td class="actions">
                            <a class="secondary" href="index.php?action=artist-edit&amp;id=<?= (int) $artist['id'] ?>">Edit</a>
                            <form method="POST" action="index.php?action=artist-delete" onsubmit="return confirm('Hapus nail artist ini?')">
                                <input type="hidden" name="id" value="<?= (int) $artist['id'] ?>">
                                <button class="delete" type="submit">Hapus</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </section>

    <!-- SECTION: Daftar Pemesanan Masuk -->
    <section class="manage-section">
        <header class="section-head">
            <div>
                <h2>Daftar Pemesanan Masuk</h2>
                <p>Pantau booking, konfirmasi pembayaran, dan ubah status pesanan pelanggan.</p>
            </div>
        </header>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Pelanggan</th>
                        <th>No. HP</th>
                        <th>Jadwal</th>
                        <th>Layanan</th>
                        <th>Total</th>
                        <th>Pembayaran</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($pemesanan)): ?>
                    <tr><td class="empty" colspan="9">Belum ada pemesanan masuk.</td></tr>
                <?php else: foreach ($pemesanan as $index => $row): ?>
                    <?php 
                        $appointmentDate = !empty($row['tanggal_pesan']) ? new DateTime($row['tanggal_pesan']) : null;
                    ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= $escape($row['nama_pelanggan']) ?></td>
                        <td><?= $escape($row['no_telepon']) ?></td>
                        <td><?= $appointmentDate ? $escape($appointmentDate->format('d-m-Y H:i')) : '-' ?></td>
                        <td><?= $escape($row['nama_layanan']) ?></td>
                        <td>Rp<?= number_format((float) $row['total_harga'], 0, ',', '.') ?></td>
                        <td>
                            <span style="padding: 3px 8px; border-radius: 4px; font-size: 0.85rem; background: <?= ($row['status_pembayaran'] === 'Lunas') ? '#d4edda' : '#fff3cd'; ?>; color: <?= ($row['status_pembayaran'] === 'Lunas') ? '#155724' : '#856404'; ?>;">
                                <?= $escape($row['status_pembayaran']) ?>
                            </span>
                        </td>
                        <td><strong><?= $escape($row['status_pemesanan']) ?></strong></td>
                        <td class="actions">
                            <?php if ($row['status_pembayaran'] !== 'Lunas'): ?>
                                <form method="POST" action="index.php?action=update-status" style="display: inline;">
                                    <input type="hidden" name="id" value="<?= (int) $row['id_pemesanan'] ?>">
                                    <input type="hidden" name="status_pembayaran" value="Lunas">
                                    <input type="hidden" name="status_pemesanan" value="Diterima">
                                    <button type="submit" class="button" style="padding: 4px 10px; font-size: 0.8rem; background-color: #28a745; color: #fff; border: none; border-radius: 4px; cursor: pointer;">Acc Pembayaran</button>
                                </form>
                            <?php else: ?>
                                <span style="font-size: 0.85rem; color: #6c757d;">Selesai</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </section>

    <!-- SECTION: Menu Harga -->
    <section class="manage-section">
        <header class="section-head">
            <h2>Menu Harga</h2>
            <a class="button" href="index.php?action=content-edit&amp;type=service">+ Tambah Layanan</a>
        </header>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Kategori</th><th>Layanan</th><th>Deskripsi</th><th>Harga</th><th>Aksi</th></tr></thead>
                <tbody>
                <?php if (!$services): ?>
                    <tr><td class="empty" colspan="5">Belum ada layanan.</td></tr>
                <?php else: foreach ($services as $service): ?>
                    <tr>
                        <td><?= $escape($categories[$service['kategori']]) ?></td>
                        <td><?= $escape($service['nama']) ?></td>
                        <td><?= $escape($service['deskripsi']) ?></td>
                        <td><?= $escape($service['harga']) ?></td>
                        <td class="actions">
                            <a class="secondary" href="index.php?action=content-edit&amp;type=service&amp;id=<?= (int) $service['id'] ?>">Edit</a>
                            <form method="POST" action="index.php?action=content-delete" onsubmit="return confirm('Hapus layanan ini?')">
                                <input type="hidden" name="type" value="service">
                                <input type="hidden" name="id" value="<?= (int) $service['id'] ?>">
                                <button class="delete" type="submit">Hapus</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </section>

    <!-- SECTION: Katalog & Foto -->
    <section class="manage-section">
        <header class="section-head">
            <h2>Katalog &amp; Foto Hasil Customer</h2>
            <a class="button" href="index.php?action=content-edit&amp;type=catalog">+ Tambah Foto</a>
        </header>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Foto</th><th>Kategori</th><th>Judul</th><th>Deskripsi</th><th>Aksi</th></tr></thead>
                <tbody>
                <?php if (!$catalog): ?>
                    <tr><td class="empty" colspan="5">Belum ada foto katalog.</td></tr>
                <?php else: foreach ($catalog as $item): ?>
                    <tr>
                        <td><?php if ($item['foto'] !== ''): ?><img class="thumb" src="<?= $escape($item['foto']) ?>" alt="<?= $escape($item['judul']) ?>"><?php else: ?>Belum ada foto<?php endif; ?></td>
                        <td><?= $escape($categories[$item['kategori']]) ?></td>
                        <td><?= $escape($item['judul']) ?></td>
                        <td><?= $escape($item['deskripsi']) ?></td>
                        <td class="actions">
                            <a class="secondary" href="index.php?action=content-edit&amp;type=catalog&amp;id=<?= (int) $item['id'] ?>">Edit</a>
                            <form method="POST" action="index.php?action=content-delete" onsubmit="return confirm('Hapus foto katalog ini?')">
                                <input type="hidden" name="type" value="catalog">
                                <input type="hidden" name="id" value="<?= (int) $item['id'] ?>">
                                <button class="delete" type="submit">Hapus</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>
</body>
</html>