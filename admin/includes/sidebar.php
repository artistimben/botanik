
<?php
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>

<aside class="admin-sidebar" id="adminSidebar">
    <nav class="sidebar-nav">
        <a href="index.php" class="nav-item <?php echo $currentPage == 'index' ? 'active' : ''; ?>">
            <i class="fas fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
        
        <a href="orders.php" class="nav-item <?php echo $currentPage == 'orders' ? 'active' : ''; ?>">
            <i class="fas fa-shopping-cart"></i>
            <span>Siparişler</span>
            <?php if (isset($stats) && $stats['pending_orders'] > 0): ?>
                <span class="badge"><?php echo $stats['pending_orders']; ?></span>
            <?php endif; ?>
        </a>
        
        <a href="products.php" class="nav-item <?php echo $currentPage == 'products' ? 'active' : ''; ?>">
            <i class="fas fa-boxes"></i>
            <span>Ürünler & Kategoriler</span>
        </a>
        
        <a href="import-products.php" class="nav-item <?php echo $currentPage == 'import-products' ? 'active' : ''; ?>">
            <i class="fas fa-file-import"></i>
            <span>Toplu İçe Aktar</span>
        </a>
        
        <a href="messages.php" class="nav-item <?php echo $currentPage == 'messages' ? 'active' : ''; ?>">
            <i class="fas fa-envelope"></i>
            <span>Mesajlar</span>
            <?php if (isset($stats) && $stats['unread_messages'] > 0): ?>
                <span class="badge"><?php echo $stats['unread_messages']; ?></span>
            <?php endif; ?>
        </a>
        
        <a href="gallery.php" class="nav-item <?php echo $currentPage == 'gallery' ? 'active' : ''; ?>">
            <i class="fas fa-images"></i>
            <span>Galeri</span>
        </a>
        
        <div class="nav-divider"></div>
        
        <a href="statistics.php" class="nav-item <?php echo $currentPage == 'statistics' ? 'active' : ''; ?>">
            <i class="fas fa-chart-line"></i>
            <span>İstatistikler</span>
        </a>
        
        <a href="settings.php" class="nav-item <?php echo $currentPage == 'settings' ? 'active' : ''; ?>">
            <i class="fas fa-cog"></i>
            <span>Ayarlar</span>
        </a>
        
        <?php if (isset($adminInfo) && $adminInfo['role'] == 'admin'): ?>
            <a href="users.php" class="nav-item <?php echo $currentPage == 'users' ? 'active' : ''; ?>">
                <i class="fas fa-users-cog"></i>
                <span>Admin Kullanıcılar</span>
            </a>
        <?php endif; ?>
        
        <div class="nav-divider"></div>
        
        <a href="../index.php" class="nav-item" target="_blank">
            <i class="fas fa-globe"></i>
            <span>Siteyi Görüntüle</span>
        </a>
        
        <a href="logout.php" class="nav-item text-danger">
            <i class="fas fa-sign-out-alt"></i>
            <span>Çıkış Yap</span>
        </a>
    </nav>
    
    <div class="sidebar-footer">
        <div class="sidebar-version">
            <i class="fas fa-leaf"></i>
            <span>v1.0.0</span>
        </div>
    </div>
</aside>

<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

