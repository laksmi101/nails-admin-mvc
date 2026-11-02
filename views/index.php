<?php
$escape = static function ($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
};

$categories = ['tangan' => 'Nails Tangan', 'kaki' => 'Nails Kaki'];
$serviceGroups = ['tangan' => [], 'kaki' => []];
foreach ($services as $service) {
    $serviceGroups[$service['kategori']][] = $service;
}
$catalogGroups = ['tangan' => [], 'kaki' => []];
foreach ($catalog as $item) {
    $catalogGroups[$item['kategori']][] = $item;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nailsbygekya - Menu &amp; Katalog</title>
    <link rel="stylesheet" href="views/style.css">
    <style>
        /* Styling untuk menu garis tiga (hamburger) */
        .hamburger-menu {
            position: relative;
            display: inline-block;
        }
        .hamburger-btn {
            background: #4a3525;
            color: #fff;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1.3rem;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.15);
        }
        .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            top: 50px;
            background: #fff;
            min-width: 200px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            border-radius: 10px;
            overflow: hidden;
            z-index: 1100;
            border: 1px solid #e0d3c1;
        }
        .dropdown-content a, .dropdown-content button {
            display: block;
            width: 100%;
            padding: 12px 18px;
            color: #4a3525;
            text-decoration: none;
            font-size: 0.95rem;
            font-weight: 600;
            text-align: left;
            background: none;
            border: none;
            border-bottom: 1px solid #f5eee6;
            cursor: pointer;
        }
        .dropdown-content a:hover, .dropdown-content button:hover {
            background: #fdfbf7;
        }
    </style>
</head>
<body class="site-page">
<header class="site-header" style="padding: 18px 50px; background: rgba(244, 236, 225, 0.95); backdrop-filter: blur(8px); position: sticky; top: 0; z-index: 1000; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e0d3c1; box-shadow: 0 2px 10px rgba(0,0,0,0.02);">
    
    <!-- Logo di Kiri -->
    <a class="brand" href="#beranda" style="font-size: 1.4rem; font-weight: 800; color: #4a3525; text-decoration: none; display: flex; align-items: center; gap: 8px; letter-spacing: 0.5px;">
        ✨ Nailsbygekya
    </a>

    <!-- Menu Garis Tiga (Hamburger) di Kanan (Login Admin sudah dihapus) -->
    <div class="hamburger-menu">
        <button class="hamburger-btn" onclick="toggleMenu()" title="Menu">
            ☰
        </button>
        <div id="navDropdown" class="dropdown-content">
            <a href="#beranda" onclick="toggleMenu()">Beranda</a>
            <a href="#harga" onclick="toggleMenu()">Menu Harga</a>
            <a href="#katalog" onclick="toggleMenu()">Katalog</a>
            <a href="index.php?action=order">Pesan &amp; Bayar</a>
            <a href="index.php?action=check-status">Cek Status</a>
            
            <?php if (!empty($_SESSION['admin_id'])): ?>
                <a href="index.php?action=manage" style="color: #28a745;">⚙️ Kelola Konten</a>
                <form method="POST" action="index.php?action=logout" style="margin: 0;">
                    <button type="submit" style="color: #d9534f;">🚪 Keluar</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</header>

<!-- Skrip untuk Toggle Menu Garis Tiga -->
<script>
function toggleMenu() {
    var menu = document.getElementById('navDropdown');
    if (menu.style.display === 'block') {
        menu.style.display = 'none';
    } else {
        menu.style.display = 'block';
    }
}
// Tutup menu jika klik di luar area dropdown
window.onclick = function(event) {
    if (!event.target.matches('.hamburger-btn') && !event.target.closest('.hamburger-menu')) {
        var menu = document.getElementById('navDropdown');
        if (menu && menu.style.display === 'block') {
            menu.style.display = 'none';
        }
    }
}
</script>

<main class="site-main" id="beranda">
    <!-- Hero Section dengan Background beground.jpg & Overlay Elegan -->
    <section class="hero" style="position: relative; padding: 90px 20px; text-align: center; background: linear-gradient(rgba(45, 30, 20, 0.65), rgba(45, 30, 20, 0.65)), url('uploads/gallery/beground.jpg') center/cover no-repeat; border-radius: 12px; margin: 20px auto; max-width: 1100px; box-shadow: 0 6px 25px rgba(0,0,0,0.15); color: #fff;">
        
        <span style="display: inline-block; padding: 6px 16px; background: rgba(255, 255, 255, 0.2); backdrop-filter: blur(4px); color: #fff; border-radius: 20px; font-size: 0.85rem; font-weight: 600; margin-bottom: 15px; letter-spacing: 0.5px; border: 1px solid rgba(255,255,255,0.3);">💅 Professional Nail Studio &amp; Art</span>
        
        <h1 style="font-size: 2.8rem; font-weight: 800; color: #ffffff; margin-bottom: 15px; line-height: 1.2; text-shadow: 0 2px 5px rgba(0,0,0,0.4);">Warna dan detail yang mencerminkan gayamu.</h1>
        
        <p style="font-size: 1.15rem; color: #f0f0f0; max-width: 650px; margin: 0 auto 30px auto; text-shadow: 0 1px 3px rgba(0,0,0,0.4);">Lihat pilihan layanan nail tangan dan kaki terbaik kami, lalu temukan inspirasi desain favoritmu untuk mempercantik penampilan.</p>
        
        <div style="display: flex; justify-content: center; gap: 15px; flex-wrap: wrap;">
            <a href="index.php?action=order" class="button" style="padding: 12px 24px; font-weight: 600; border-radius: 6px; text-decoration: none; background-color: #8c6d53; color: #fff; border: 1px solid #a68568; box-shadow: 0 3px 10px rgba(0,0,0,0.2);">📅 Buat Pemesanan Sekarang</a>
            <a href="#harga" class="button" style="padding: 12px 24px; font-weight: 600; border-radius: 6px; background-color: rgba(255,255,255,0.9); color: #3a2a1d; text-decoration: none; box-shadow: 0 3px 10px rgba(0,0,0,0.2);">🔍 Lihat Menu Harga</a>
        </div>
    </section>

    <section class="section" id="harga">
        <div class="section-heading">
            <h2>Menu Harga</h2>
            <p class="muted">Pilih perawatan yang paling cocok untukmu.</p>
        </div>
        
        <!-- Form Pencarian Server-Side -->
        <div class="search-box">
            <form method="GET" action="index.php">
                <input type="hidden" name="action" value="index">
                <label for="service-search">Cari layanan</label>
                <div style="display: flex; gap: 10px;">
                    <input id="service-search" type="search" name="search" value="<?= $escape($keyword ?? '') ?>" placeholder="Ketik nama layanan, kategori, atau deskripsi..." autocomplete="off" style="flex: 1; padding: 8px;">
                    <button type="submit" class="button" style="padding: 8px 16px;">Cari</button>
                    <?php if (!empty($keyword)): ?>
                        <a href="index.php" class="button" style="background-color: #6c757d; padding: 8px 16px; text-decoration: none;">Reset</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <div class="menu-grid">
            <?php 
            $hasData = false;
            foreach ($serviceGroups as $category => $items): 
                if (empty($items)) continue;
                $hasData = true;
            ?>
                <article class="menu-card" data-service-group>
                    <h3><?= $escape($categories[$category]) ?></h3>
                    <?php foreach ($items as $item): ?>
                        <div class="service" data-service-item>
                            <div>
                                <strong><?= $escape($item['nama']) ?></strong>
                                <small><?= $escape($item['deskripsi']) ?></small>
                            </div>
                            <span class="price"><?= $escape($item['harga']) ?></span>
                        </div>
                    <?php endforeach; ?>
                </article>
            <?php endforeach; ?>
        </div>

        <?php if (!$hasData): ?>
            <p class="empty search-empty">Layanan yang dicari tidak ditemukan.</p>
        <?php endif; ?>

        <!-- Navigasi Pagination -->
        <?php if (isset($totalPages) && $totalPages > 1): ?>
        <nav aria-label="Page navigation" class="mt-4" style="margin-top: 30px; text-align: center;">
            <ul class="pagination" style="display: inline-flex; list-style: none; padding: 0; gap: 5px;">
                <!-- Tombol Previous -->
                <li style="margin: 0;">
                    <a class="button" href="index.php?action=index&page=<?= max(1, $page - 1); ?>&search=<?= urlencode($keyword ?? '') ?>" style="padding: 6px 12px; text-decoration: none; <?= ($page <= 1) ? 'opacity: 0.5; pointer-events: none;' : '' ?>">Previous</a>
                </li>

                <!-- Nomor Halaman -->
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li style="margin: 0;">
                        <a class="button" href="index.php?action=index&page=<?= $i; ?>&search=<?= urlencode($keyword ?? '') ?>" style="padding: 6px 12px; text-decoration: none; <?= ($page == $i) ? 'background-color: #333;' : 'background-color: #ccc; color: #000;' ?>"><?= $i; ?></a>
                    </li>
                <?php endfor; ?>

                <!-- Tombol Next -->
                <li style="margin: 0;">
                    <a class="button" href="index.php?action=index&page=<?= min($totalPages, $page + 1); ?>&search=<?= urlencode($keyword ?? '') ?>" style="padding: 6px 12px; text-decoration: none; <?= ($page >= $totalPages) ? 'opacity: 0.5; pointer-events: none;' : '' ?>">Next</a>
                </li>
            </ul>
        </nav>
        <?php endif; ?>

        <p class="notice" style="margin-top: 20px;">Harga akhir dapat menyesuaikan panjang kuku, tingkat detail nail art, dan pilihan desain.</p>
        <a class="button" href="index.php?action=order">Buat Pemesanan</a>
    </section>

    <section class="section" id="katalog">
        <div class="section-heading">
            <h2>Katalog Nail Art</h2>
            <p class="muted">Inspirasi desain dan hasil nail art pelanggan untuk tangan dan kaki.</p>
        </div>
        <?php foreach ($catalogGroups as $category => $designs): ?>
            <h3><?= $escape($categories[$category]) ?></h3>
            <div class="catalog-grid">
                <?php foreach ($designs as $design): ?>
                    <article class="catalog-card">
                        <?php if ($design['foto'] !== ''): ?>
                            <img class="catalog-photo" src="<?= $escape($design['foto']) ?>" alt="Hasil nail art: <?= $escape($design['judul']) ?>">
                        <?php else: ?>
                            <div class="catalog-placeholder">Foto belum tersedia</div>
                        <?php endif; ?>
                        <div class="catalog-copy">
                            <h3><?= $escape($design['judul']) ?></h3>
                            <p><?= $escape($design['deskripsi']) ?></p>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </section>
</main>
</body>
</html>