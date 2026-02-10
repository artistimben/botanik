<?php
/**
 * Admin Panel - Sipariş Yönetimi
 */
require_once 'includes/auth.php';
require_once 'includes/functions.php';

checkAdminLogin();

$adminInfo = getAdminInfo();
$stats = getAdminStats();

// Mesaj değişkenleri
$success = '';
$error = '';

// Sipariş durumu güncelleme
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $orderId = intval($_POST['order_id']);
    $newStatus = cleanInput($_POST['status']);
    $adminNote = cleanInput($_POST['admin_note'] ?? '');
    
    $stmt = $conn->prepare("UPDATE orders SET status = ?, admin_note = ?, updated_at = NOW() WHERE id = ?");
    $stmt->bind_param("ssi", $newStatus, $adminNote, $orderId);
    
    if ($stmt->execute()) {
        $success = 'Sipariş durumu başarıyla güncellendi!';
        
        // WhatsApp bildirim linki oluştur
        $orderStmt = $conn->prepare("SELECT customer_name, customer_phone, order_number FROM orders WHERE id = ?");
        $orderStmt->bind_param("i", $orderId);
        $orderStmt->execute();
        $orderData = $orderStmt->get_result()->fetch_assoc();
        
        if ($orderData) {
            $statusTexts = [
                'confirmed' => 'Onaylandı',
                'preparing' => 'Hazırlanıyor',
                'delivered' => 'Teslim Edildi',
                'cancelled' => 'İptal Edildi'
            ];
            
            $statusText = $statusTexts[$newStatus] ?? $newStatus;
            $message = "Merhaba {$orderData['customer_name']}, \n\nSipariş numaranız: {$orderData['order_number']}\nDurum: {$statusText}\n\nBizi tercih ettiğiniz için teşekkür ederiz.\n\nPapatya Botanik";
            
            $whatsappUrl = sendWhatsAppNotification($orderData['customer_phone'], $message);
        }
    } else {
        $error = 'Sipariş durumu güncellenirken bir hata oluştu!';
    }
}

// Sipariş silme
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $orderId = intval($_GET['id']);
    $stmt = $conn->prepare("DELETE FROM orders WHERE id = ?");
    $stmt->bind_param("i", $orderId);
    
    if ($stmt->execute()) {
        $success = 'Sipariş başarıyla silindi!';
    } else {
        $error = 'Sipariş silinirken bir hata oluştu!';
    }
}

// Filtreleme ve sayfalama
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = 20;
$offset = ($page - 1) * $perPage;

$statusFilter = isset($_GET['status']) ? cleanInput($_GET['status']) : '';
$searchTerm = isset($_GET['search']) ? cleanInput($_GET['search']) : '';

// Sorgu oluştur
$whereClause = [];
$params = [];
$types = '';

if ($statusFilter) {
    $whereClause[] = "o.status = ?";
    $params[] = $statusFilter;
    $types .= 's';
}

if ($searchTerm) {
    $whereClause[] = "(o.order_number LIKE ? OR o.customer_name LIKE ? OR o.customer_phone LIKE ?)";
    $searchParam = '%' . $searchTerm . '%';
    $params[] = $searchParam;
    $params[] = $searchParam;
    $params[] = $searchParam;
    $types .= 'sss';
}

$whereSQL = !empty($whereClause) ? 'WHERE ' . implode(' AND ', $whereClause) : '';

// Toplam sipariş sayısı
$countQuery = "SELECT COUNT(*) as total FROM orders o $whereSQL";
if (!empty($params)) {
    $countStmt = $conn->prepare($countQuery);
    $countStmt->bind_param($types, ...$params);
    $countStmt->execute();
    $totalOrders = $countStmt->get_result()->fetch_assoc()['total'];
} else {
    $totalOrders = $conn->query($countQuery)->fetch_assoc()['total'];
}

$totalPages = ceil($totalOrders / $perPage);

// Siparişleri getir
$query = "
    SELECT o.*, c.name as category_name, p.name as product_name 
    FROM orders o 
    LEFT JOIN categories c ON o.category_id = c.id 
    LEFT JOIN products p ON o.product_id = p.id 
    $whereSQL
    ORDER BY o.created_at DESC 
    LIMIT $perPage OFFSET $offset
";

if (!empty($params)) {
    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $ordersResult = $stmt->get_result();
} else {
    $ordersResult = $conn->query($query);
}

$orders = [];
while ($row = $ordersResult->fetch_assoc()) {
    $orders[] = $row;
}

$pageTitle = 'Sipariş Yönetimi';
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - Papatya Botanik Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/admin.css">
    <style>
        .filter-bar {
            background: var(--white);
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        
        .filter-row {
            display: flex;
            gap: 15px;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .filter-item {
            flex: 1;
            min-width: 200px;
        }
        
        .filter-item label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: var(--dark-color);
            font-size: 14px;
        }
        
        .form-control, .form-select {
            width: 100%;
            padding: 10px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s;
        }
        
        .form-control:focus, .form-select:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(45, 80, 22, 0.1);
        }
        
        .order-detail-modal {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 10000;
            padding: 20px;
        }
        
        .order-detail-modal.show {
            display: flex;
        }
        
        .modal-content {
            background: var(--white);
            border-radius: 15px;
            max-width: 700px;
            width: 100%;
            max-height: 90vh;
            overflow-y: auto;
        }
        
        .modal-header {
            padding: 20px 25px;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .modal-header h3 {
            margin: 0;
            font-size: 20px;
        }
        
        .modal-close {
            background: none;
            border: none;
            font-size: 24px;
            color: var(--dark-color);
            cursor: pointer;
            padding: 5px;
        }
        
        .modal-body {
            padding: 25px;
        }
        
        .detail-group {
            margin-bottom: 20px;
        }
        
        .detail-group label {
            display: block;
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 8px;
        }
        
        .detail-group p {
            color: #495057;
            margin: 0;
        }
        
        .status-buttons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .pagination-nav {
            display: flex;
            justify-content: center;
            margin-top: 30px;
        }
        
        .pagination {
            display: flex;
            gap: 5px;
            list-style: none;
        }
        
        .pagination li a, .pagination li span {
            display: block;
            padding: 8px 12px;
            border-radius: 6px;
            text-decoration: none;
            color: var(--dark-color);
            background: var(--white);
            border: 1px solid #dee2e6;
            transition: all 0.3s;
        }
        
        .pagination li a:hover {
            background: var(--primary-color);
            color: var(--white);
            border-color: var(--primary-color);
        }
        
        .pagination li.active span {
            background: var(--primary-color);
            color: var(--white);
            border-color: var(--primary-color);
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <?php include 'includes/sidebar.php'; ?>

    <div class="main-content">
        <div class="page-header">
            <div>
                <h1><i class="fas fa-shopping-cart"></i> Sipariş Yönetimi</h1>
                <p>Toplam <?php echo number_format($totalOrders); ?> sipariş</p>
            </div>
            <a href="?action=new" class="btn btn-primary">
                <i class="fas fa-plus"></i> Yeni Sipariş
            </a>
        </div>

        <?php if ($success): ?>
            <div class="alert alert-success alert-auto-hide">
                <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                <?php if (isset($whatsappUrl)): ?>
                    <a href="<?php echo $whatsappUrl; ?>" target="_blank" class="btn btn-sm btn-success" style="margin-left: auto;">
                        <i class="fab fa-whatsapp"></i> Müşteriyi Bilgilendir
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger alert-auto-hide">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <!-- Filtrele -->
        <div class="filter-bar">
            <form method="GET" action="">
                <div class="filter-row">
                    <div class="filter-item">
                        <label for="search">Ara</label>
                        <input type="text" 
                               id="search" 
                               name="search" 
                               class="form-control" 
                               placeholder="Sipariş no, müşteri adı, telefon..."
                               value="<?php echo htmlspecialchars($searchTerm); ?>">
                    </div>
                    
                    <div class="filter-item" style="flex: 0.6;">
                        <label for="status">Durum</label>
                        <select name="status" id="status" class="form-select">
                            <option value="">Tümü</option>
                            <option value="pending" <?php echo $statusFilter === 'pending' ? 'selected' : ''; ?>>Bekliyor</option>
                            <option value="confirmed" <?php echo $statusFilter === 'confirmed' ? 'selected' : ''; ?>>Onaylandı</option>
                            <option value="preparing" <?php echo $statusFilter === 'preparing' ? 'selected' : ''; ?>>Hazırlanıyor</option>
                            <option value="delivered" <?php echo $statusFilter === 'delivered' ? 'selected' : ''; ?>>Teslim Edildi</option>
                            <option value="cancelled" <?php echo $statusFilter === 'cancelled' ? 'selected' : ''; ?>>İptal</option>
                        </select>
                    </div>
                    
                    <div class="filter-item" style="flex: 0.4; align-self: flex-end;">
                        <button type="submit" class="btn btn-primary" style="width: 100%;">
                            <i class="fas fa-search"></i> Filtrele
                        </button>
                    </div>
                    
                    <?php if ($searchTerm || $statusFilter): ?>
                    <div class="filter-item" style="flex: 0.3; align-self: flex-end;">
                        <a href="orders.php" class="btn btn-secondary" style="width: 100%;">
                            <i class="fas fa-times"></i> Temizle
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- Siparişler Tablosu -->
        <div class="content-section">
            <div class="table-responsive">
                <?php if (empty($orders)): ?>
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <p>Sipariş bulunamadı</p>
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
                                <th>Tutar</th>
                                <th>Durum</th>
                                <th>Tarih</th>
                                <th>İşlem</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
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
                                    <td><?php echo formatMoney($order['total_amount']); ?></td>
                                    <td><?php echo getOrderStatusBadge($order['status']); ?></td>
                                    <td>
                                        <small title="<?php echo formatDate($order['created_at']); ?>">
                                            <?php echo timeAgo($order['created_at']); ?>
                                        </small>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <button onclick='showOrderDetail(<?php echo json_encode($order); ?>)' 
                                                   class="btn btn-sm btn-info" 
                                                   title="Detay">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $order['customer_phone']); ?>" 
                                               class="btn btn-sm btn-success" 
                                               title="WhatsApp" 
                                               target="_blank">
                                                <i class="fab fa-whatsapp"></i>
                                            </a>
                                            <a href="?action=delete&id=<?php echo $order['id']; ?>" 
                                               class="btn btn-sm btn-danger" 
                                               title="Sil"
                                               onclick="return confirm('Bu siparişi silmek istediğinizden emin misiniz?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <!-- Sayfalama -->
                    <?php if ($totalPages > 1): ?>
                        <nav class="pagination-nav">
                            <ul class="pagination">
                                <?php if ($page > 1): ?>
                                    <li><a href="?page=<?php echo $page - 1; ?><?php echo $statusFilter ? '&status=' . $statusFilter : ''; ?><?php echo $searchTerm ? '&search=' . urlencode($searchTerm) : ''; ?>"><i class="fas fa-chevron-left"></i></a></li>
                                <?php endif; ?>
                                
                                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                    <li <?php echo $i == $page ? 'class="active"' : ''; ?>>
                                        <?php if ($i == $page): ?>
                                            <span><?php echo $i; ?></span>
                                        <?php else: ?>
                                            <a href="?page=<?php echo $i; ?><?php echo $statusFilter ? '&status=' . $statusFilter : ''; ?><?php echo $searchTerm ? '&search=' . urlencode($searchTerm) : ''; ?>"><?php echo $i; ?></a>
                                        <?php endif; ?>
                                    </li>
                                <?php endfor; ?>
                                
                                <?php if ($page < $totalPages): ?>
                                    <li><a href="?page=<?php echo $page + 1; ?><?php echo $statusFilter ? '&status=' . $statusFilter : ''; ?><?php echo $searchTerm ? '&search=' . urlencode($searchTerm) : ''; ?>"><i class="fas fa-chevron-right"></i></a></li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Sipariş Detay Modal -->
    <div class="order-detail-modal" id="orderDetailModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-shopping-cart"></i> Sipariş Detayı</h3>
                <button class="modal-close" onclick="closeOrderDetail()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body" id="orderDetailBody">
                <!-- Dinamik içerik -->
            </div>
        </div>
    </div>

    <script src="assets/js/admin.js"></script>
    <script>
        function showOrderDetail(order) {
            const modal = document.getElementById('orderDetailModal');
            const body = document.getElementById('orderDetailBody');
            
            const statusOptions = [
                { value: 'pending', label: 'Bekliyor' },
                { value: 'confirmed', label: 'Onaylandı' },
                { value: 'preparing', label: 'Hazırlanıyor' },
                { value: 'delivered', label: 'Teslim Edildi' },
                { value: 'cancelled', label: 'İptal' }
            ];
            
            let statusOptionsHTML = '';
            statusOptions.forEach(opt => {
                statusOptionsHTML += `<option value="${opt.value}" ${order.status === opt.value ? 'selected' : ''}>${opt.label}</option>`;
            });
            
            body.innerHTML = `
                <form method="POST" action="">
                    <input type="hidden" name="order_id" value="${order.id}">
                    
                    <div class="detail-group">
                        <label>Sipariş Numarası</label>
                        <p><strong>${order.order_number}</strong></p>
                    </div>
                    
                    <div class="detail-group">
                        <label>Müşteri Bilgileri</label>
                        <p><strong>${order.customer_name}</strong></p>
                        <p><i class="fas fa-phone"></i> ${order.customer_phone}</p>
                        ${order.customer_email ? `<p><i class="fas fa-envelope"></i> ${order.customer_email}</p>` : ''}
                    </div>
                    
                    <div class="detail-group">
                        <label>Sipariş Bilgileri</label>
                        <p><strong>Kategori:</strong> ${order.category_name || 'Belirtilmemiş'}</p>
                        ${order.product_name ? `<p><strong>Ürün:</strong> ${order.product_name}</p>` : ''}
                        <p><strong>Sipariş Türü:</strong> ${order.order_type}</p>
                        ${order.total_amount ? `<p><strong>Tutar:</strong> ${parseFloat(order.total_amount).toLocaleString('tr-TR', {minimumFractionDigits: 2})} ₺</p>` : ''}
                    </div>
                    
                    ${order.order_details ? `
                    <div class="detail-group">
                        <label>Sipariş Detayları</label>
                        <p>${order.order_details}</p>
                    </div>
                    ` : ''}
                    
                    ${order.delivery_address ? `
                    <div class="detail-group">
                        <label>Teslimat Adresi</label>
                        <p>${order.delivery_address}</p>
                    </div>
                    ` : ''}
                    
                    ${order.delivery_date ? `
                    <div class="detail-group">
                        <label>Teslimat Tarihi</label>
                        <p>${order.delivery_date}</p>
                    </div>
                    ` : ''}
                    
                    <div class="detail-group">
                        <label>Sipariş Tarihi</label>
                        <p>${formatDate(order.created_at)}</p>
                    </div>
                    
                    <div class="detail-group">
                        <label for="status">Durum</label>
                        <select name="status" id="status" class="form-select" required>
                            ${statusOptionsHTML}
                        </select>
                    </div>
                    
                    <div class="detail-group">
                        <label for="admin_note">Admin Notu</label>
                        <textarea name="admin_note" id="admin_note" class="form-control" rows="3" placeholder="İç notlarınızı yazın...">${order.admin_note || ''}</textarea>
                    </div>
                    
                    <div class="status-buttons">
                        <button type="submit" name="update_status" class="btn btn-primary">
                            <i class="fas fa-save"></i> Kaydet
                        </button>
                        <a href="https://wa.me/${order.customer_phone.replace(/[^0-9]/g, '')}" 
                           class="btn btn-success" 
                           target="_blank">
                            <i class="fab fa-whatsapp"></i> WhatsApp Gönder
                        </a>
                        <button type="button" class="btn btn-secondary" onclick="closeOrderDetail()">
                            <i class="fas fa-times"></i> Kapat
                        </button>
                    </div>
                </form>
            `;
            
            modal.classList.add('show');
        }
        
        function closeOrderDetail() {
            document.getElementById('orderDetailModal').classList.remove('show');
        }
        
        function formatDate(dateString) {
            const date = new Date(dateString);
            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const year = date.getFullYear();
            const hours = String(date.getHours()).padStart(2, '0');
            const minutes = String(date.getMinutes()).padStart(2, '0');
            return `${day}.${month}.${year} ${hours}:${minutes}`;
        }
        
        // Modal dışına tıklandığında kapat
        document.getElementById('orderDetailModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeOrderDetail();
            }
        });
    </script>
</body>
</html>

