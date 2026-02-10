<?php
/**
 * Admin Panel - Galeri Yönetimi
 */
require_once 'includes/auth.php';
require_once 'includes/functions.php';

checkAdminLogin();

$adminInfo = getAdminInfo();
$stats = getAdminStats();

$pageTitle = 'Galeri Yönetimi';
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
            <h1><i class="fas fa-images"></i> Galeri Yönetimi</h1>
            <p>Görsellerinizi buradan yönetin</p>
        </div>

        <div class="content-section">
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                <strong>Yakında:</strong> Görsel yükleme ve yönetim özellikleri eklenecek.
            </div>
            
            <div class="empty-state">
                <i class="fas fa-images"></i>
                <h3>Galeri Yönetimi</h3>
                <p>Bu özellik yakında eklenecek.</p>
                <p>Şu anda görseller <strong>images/GÖRSELLER/</strong> klasöründen otomatik olarak alınıyor.</p>
                <p style="margin-top: 20px;">
                    <a href="index.php" class="btn btn-primary">
                        <i class="fas fa-arrow-left"></i> Dashboard'a Dön
                    </a>
                </p>
            </div>
        </div>
    </div>

    <script src="assets/js/admin.js"></script>
</body>
</html>

