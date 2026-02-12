<?php
/**
 * Papatya Botanik - Header (Üst Kısım)
 * Tüm sayfalarda kullanılacak üst kısım
 */

// Session'ı hemen başlat (ZORUNLU CACHE için gerekli!)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo SITE_DESCRIPTION; ?>">
    <meta name="keywords" content="çiçek, buket, botanik, arajman, isteme çiçeği, düğün çiçekleri">
    <meta name="author" content="Papatya Botanik">

    <title><?php echo isset($page_title) ? $page_title . ' - ' . SITE_NAME : SITE_TITLE; ?></title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="images/LOGO/şuanki Logo.png">

    <!-- CSS -->
    <link rel="stylesheet" href="assets/css/style.css">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Config değerlerini JavaScript'e aktar -->
    <script>
        // PHP'den JavaScript'e veri aktarımı
        window.siteConfig = {
            whatsappNumber: '<?php echo WHATSAPP_NUMBER; ?>',
            phoneNumber: '<?php echo str_replace(' ', '', PHONE_NUMBER); ?>',
            siteName: '<?php echo SITE_NAME; ?>',
            siteUrl: '<?php echo SITE_URL; ?>'
        };
    </script>
</head>

<body>
    <!-- WhatsApp Floating Button -->
    <a href="https://wa.me/<?php echo WHATSAPP_NUMBER; ?>?text=Merhaba,%20ürünleriniz%20hakkında%20bilgi%20almak%20istiyorum"
        class="whatsapp-float" target="_blank" title="WhatsApp ile iletişime geç">
        <i class="fab fa-whatsapp"></i>
    </a>

    <!-- Header -->
    <header class="main-header">
        <nav class="navbar">
            <div class="container">
                <div class="nav-wrapper">
                    <!-- Logo -->
                    <a href="index.php" class="logo">
                        <img src="images/LOGO/şuanki Logo.png" alt="Papatya Botanik Logo">
                        <span class="logo-text"><?php echo SITE_NAME; ?></span>
                    </a>

                    <!-- Navigation Menu -->
                    <ul class="nav-menu" id="navMenu">
                        <li><a href="index.php"
                                class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">Ana
                                Sayfa</a></li>
                        <li><a href="products.php"
                                class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'products.php' ? 'active' : ''; ?>">Ürünlerimiz</a>
                        </li>
                        <li><a href="index.php#about" class="nav-link">Hakkımızda</a></li>
                        <li><a href="index.php#gallery" class="nav-link">Galeri</a></li>
                        <li><a href="index.php#contact" class="nav-link">İletişim</a></li>
                        <li><a href="tel:<?php echo str_replace(' ', '', PHONE_NUMBER); ?>" class="btn-call">
                                <i class="fas fa-phone"></i> Hemen Ara
                            </a></li>
                    </ul>

                    <!-- Mobile Menu Toggle -->
                    <button class="mobile-menu-toggle" id="mobileMenuToggle">
                        <span></span>
                        <span></span>
                        <span></span>
                    </button>
                </div>
            </div>
        </nav>

        <!-- Top Info Bar (sadece ana sayfada görünecek) -->
        <?php if (basename($_SERVER['PHP_SELF']) == 'index.php' && !isset($hide_top_bar)): ?>
            <div class="top-info-bar">
                <div class="container">
                    <div class="info-items">
                        <div class="info-item">
                            <i class="fas fa-phone"></i>
                            <span><?php echo PHONE_NUMBER; ?></span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-clock"></i>
                            <span>Hızlı Teslimat</span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-truck"></i>
                            <span>Ücretsiz Kargo</span>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </header>

    <!-- Main Content Wrapper -->
    <main class="main-content">