<?php
/**
 * Admin Panel - Dashboard (Ana Sayfa)
 */

// Hata raporlamayı aç
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'includes/auth.php';
require_once 'includes/functions.php';

checkAdminLogin();

$adminInfo = getAdminInfo();
$stats = getAdminStats();
$recentOrders = getRecentOrders(10);

$pageTitle = 'Dashboard';
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
            <h1><i class="fas fa-tachometer-alt"></i> Dashboard</h1>
            <p>Hoş geldiniz, <strong><?php echo htmlspecialchars($adminInfo['fullname']); ?></strong></p>
        </div>

        <!-- İstatistik Kartları -->
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

            <div class="stat-card stat-purple">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-details">
                    <h3><?php echo number_format($stats['today_visitors']); ?></h3>
                    <p>Bugünkü Ziyaretçi</p>
                </div>
            </div>

            <div class="stat-card stat-whatsapp">
                <div class="stat-icon">
                    <i class="fab fa-whatsapp"></i>
                </div>
                <div class="stat-details">
                    <h3><?php echo number_format($stats['whatsapp_clicks']); ?></h3>
                    <p>WhatsApp Tıklama (7 Gün)</p>
                </div>
            </div>

            <div class="stat-card stat-danger">
                <div class="stat-icon">
                    <i class="fas fa-envelope"></i>
                </div>
                <div class="stat-details">
                    <h3><?php echo number_format($stats['unread_messages']); ?></h3>
                    <p>Okunmamış Mesaj</p>
                </div>
            </div>

            <div class="stat-card stat-secondary">
                <div class="stat-icon">
                    <i class="fas fa-th-large"></i>
                </div>
                <div class="stat-details">
                    <h3><?php echo number_format($stats['total_categories']); ?></h3>
                    <p>Aktif Kategori</p>
                </div>
            </div>
        </div>

        <!-- Son Siparişler -->
        <div class="content-section">
            <div class="section-header">
                <h2><i class="fas fa-shopping-bag"></i> Son Siparişler</h2>
                <a href="orders.php" class="btn btn-primary btn-sm">
                    Tümünü Gör <i class="fas fa-arrow-right"></i>
                </a>
            </div>

            <div class="table-responsive">
                <?php if (empty($recentOrders)): ?>
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <p>Henüz sipariş bulunmuyor</p>
                    </div>
                <?php else: ?>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Sipariş No</th>
                                <th>Müşteri</th>
                                <th>Telefon</th>
                                <th>Kategori</th>
                                <th>Tür</th>
                                <th>Durum</th>
                                <th>Tarih</th>
                                <th>İşlem</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentOrders as $order): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($order['order_number']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                    <td>
                                        <a href="tel:<?php echo $order['customer_phone']; ?>">
                                            <i class="fas fa-phone"></i> <?php echo htmlspecialchars($order['customer_phone']); ?>
                                        </a>
                                    </td>
                                    <td><?php echo htmlspecialchars($order['category_name'] ?? 'Belirtilmemiş'); ?></td>
                                    <td><?php echo getOrderTypeBadge($order['order_type']); ?></td>
                                    <td><?php echo getOrderStatusBadge($order['status']); ?></td>
                                    <td>
                                        <small title="<?php echo formatDate($order['created_at']); ?>">
                                            <?php echo timeAgo($order['created_at']); ?>
                                        </small>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="orders.php?id=<?php echo $order['id']; ?>" 
                                               class="btn btn-sm btn-info" 
                                               title="Detay">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $order['customer_phone']); ?>" 
                                               class="btn btn-sm btn-success" 
                                               title="WhatsApp" 
                                               target="_blank">
                                                <i class="fab fa-whatsapp"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>

        <!-- Hızlı İşlemler -->
        <div class="quick-actions">
            <h2><i class="fas fa-bolt"></i> Hızlı İşlemler</h2>
            <div class="quick-actions-grid">
                <a href="orders.php?action=new" class="quick-action-card">
                    <i class="fas fa-plus-circle"></i>
                    <span>Yeni Sipariş</span>
                </a>
                <a href="products.php" class="quick-action-card">
                    <i class="fas fa-box"></i>
                    <span>Ürünler</span>
                </a>
                <a href="categories.php" class="quick-action-card">
                    <i class="fas fa-th-large"></i>
                    <span>Kategoriler</span>
                </a>
                <a href="messages.php" class="quick-action-card">
                    <i class="fas fa-envelope"></i>
                    <span>Mesajlar</span>
                    <?php if ($stats['unread_messages'] > 0): ?>
                        <span class="badge"><?php echo $stats['unread_messages']; ?></span>
                    <?php endif; ?>
                </a>
                <a href="settings.php" class="quick-action-card">
                    <i class="fas fa-cog"></i>
                    <span>Ayarlar</span>
                </a>
                <a href="../index.php" class="quick-action-card" target="_blank">
                    <i class="fas fa-globe"></i>
                    <span>Siteyi Görüntüle</span>
                </a>
            </div>
        </div>
    </div>

    <script src="assets/js/admin.js"></script>
</body>
</html>

