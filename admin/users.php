<?php
/**
 * Admin Panel - Kullanıcı Yönetimi
 */
require_once 'includes/auth.php';
require_once 'includes/functions.php';

checkAdminLogin();
checkAdminRole(['admin']); // Sadece admin erişebilir

$adminInfo = getAdminInfo();
$stats = getAdminStats();

// Kullanıcıları getir
$usersResult = $conn->query("SELECT * FROM admin_users ORDER BY created_at DESC");
$users = [];
while ($row = $usersResult->fetch_assoc()) {
    $users[] = $row;
}

$pageTitle = 'Admin Kullanıcılar';
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
            <h1><i class="fas fa-users-cog"></i> Admin Kullanıcılar</h1>
            <p><?php echo count($users); ?> kullanıcı</p>
        </div>

        <div class="content-section">
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                <strong>Yakında:</strong> Kullanıcı ekleme, düzenleme ve silme özellikleri eklenecek.
            </div>
            
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Kullanıcı Adı</th>
                            <th>Ad Soyad</th>
                            <th>E-posta</th>
                            <th>Rol</th>
                            <th>Son Giriş</th>
                            <th>Durum</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo $user['id']; ?></td>
                                <td><strong><?php echo htmlspecialchars($user['username']); ?></strong></td>
                                <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td>
                                    <?php if ($user['role'] === 'admin'): ?>
                                        <span class="badge badge-danger">Admin</span>
                                    <?php elseif ($user['role'] === 'editor'): ?>
                                        <span class="badge badge-info">Editor</span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary">Viewer</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo $user['last_login'] ? formatDate($user['last_login']) : 'Hiç giriş yapmadı'; ?></td>
                                <td>
                                    <?php if ($user['is_active']): ?>
                                        <span class="badge badge-success">Aktif</span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary">Pasif</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="assets/js/admin.js"></script>
</body>
</html>

