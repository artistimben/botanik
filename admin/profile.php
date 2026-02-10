<?php
/**
 * Admin Panel - Profil Sayfası
 */
require_once 'includes/auth.php';
require_once 'includes/functions.php';

checkAdminLogin();

$adminInfo = getAdminInfo();
$stats = getAdminStats();

$pageTitle = 'Profilim';
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
            <h1><i class="fas fa-user-circle"></i> Profilim</h1>
            <p>Profil bilgilerinizi görüntüleyin</p>
        </div>

        <div class="content-section">
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                <strong>Yakında:</strong> Profil düzenleme ve şifre değiştirme özellikleri eklenecek.
            </div>
            
            <div style="max-width: 600px; margin: 0 auto;">
                <div class="setting-item">
                    <label>Ad Soyad</label>
                    <p><strong><?php echo htmlspecialchars($adminInfo['fullname']); ?></strong></p>
                </div>
                
                <div class="setting-item">
                    <label>Kullanıcı Adı</label>
                    <p><strong><?php echo htmlspecialchars($adminInfo['username']); ?></strong></p>
                </div>
                
                <div class="setting-item">
                    <label>E-posta</label>
                    <p><strong><?php echo htmlspecialchars($adminInfo['email']); ?></strong></p>
                </div>
                
                <div class="setting-item">
                    <label>Rol</label>
                    <p>
                        <?php if ($adminInfo['role'] === 'admin'): ?>
                            <span class="badge badge-danger">Admin</span>
                        <?php elseif ($adminInfo['role'] === 'editor'): ?>
                            <span class="badge badge-info">Editor</span>
                        <?php else: ?>
                            <span class="badge badge-secondary">Viewer</span>
                        <?php endif; ?>
                    </p>
                </div>
                
                <div style="margin-top: 30px;">
                    <a href="index.php" class="btn btn-primary">
                        <i class="fas fa-arrow-left"></i> Dashboard'a Dön
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/admin.js"></script>
</body>
</html>

