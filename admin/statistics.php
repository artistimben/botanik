<?php
/**
 * Admin Panel - İstatistikler
 */
require_once 'includes/auth.php';
require_once 'includes/functions.php';

checkAdminLogin();

$adminInfo = getAdminInfo();
$stats = getAdminStats();

$pageTitle = 'İstatistikler';
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - Papatya Botanik Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <?php include 'includes/sidebar.php'; ?>

    <div class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-chart-line"></i> İstatistikler</h1>
            <p>Site istatistiklerinizi görüntüleyin</p>
        </div>

        <div class="content-section">
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                <strong>Yakında:</strong> Detaylı istatistik grafikleri ve raporlar eklenecek.
            </div>
            
            <div class="stats-grid">
                <div class="stat-card stat-primary">
                    <div class="stat-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="stat-details">
                        <h3><?php echo number_format($stats['total_orders']); ?></h3>
                        <p>Toplam Sipariş</p>
                    </div>
                </div>

                <div class="stat-card stat-warning">
                    <div class="stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-details">
                        <h3><?php echo number_format($stats['pending_orders']); ?></h3>
                        <p>Bekleyen Sipariş</p>
                    </div>
                </div>

                <div class="stat-card stat-success">
                    <div class="stat-icon">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                    <div class="stat-details">
                        <h3><?php echo number_format($stats['today_orders']); ?></h3>
                        <p>Bugünkü Sipariş</p>
                    </div>
                </div>

                <div class="stat-card stat-info">
                    <div class="stat-icon">
                        <i class="fas fa-box"></i>
                    </div>
                    <div class="stat-details">
                        <h3><?php echo number_format($stats['total_products']); ?></h3>
                        <p>Aktif Ürün</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/admin.js"></script>
</body>
</html>

