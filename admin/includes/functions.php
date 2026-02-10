<?php
/**
 * Admin Panel - Yardımcı Fonksiyonlar
 */

// Veritabanı bağlantısını kontrol et
if (!isset($conn)) {
    require_once __DIR__ . '/../../includes/db.php';
}

/**
 * İstatistikleri getir
 */
function getAdminStats() {
    global $conn;
    
    $stats = [
        'total_orders' => 0,
        'pending_orders' => 0,
        'today_orders' => 0,
        'total_products' => 0,
        'total_categories' => 0,
        'unread_messages' => 0,
        'today_visitors' => 0,
        'whatsapp_clicks' => 0
    ];
    
    // Toplam sipariş
    $result = $conn->query("SELECT COUNT(*) as count FROM orders");
    if ($result) {
        $stats['total_orders'] = $result->fetch_assoc()['count'];
    }
    
    // Bekleyen siparişler
    $result = $conn->query("SELECT COUNT(*) as count FROM orders WHERE status = 'pending'");
    if ($result) {
        $stats['pending_orders'] = $result->fetch_assoc()['count'];
    }
    
    // Bugünkü siparişler
    $result = $conn->query("SELECT COUNT(*) as count FROM orders WHERE DATE(created_at) = CURDATE()");
    if ($result) {
        $stats['today_orders'] = $result->fetch_assoc()['count'];
    }
    
    // Toplam ürün
    $result = $conn->query("SELECT COUNT(*) as count FROM products WHERE is_active = 1");
    if ($result) {
        $stats['total_products'] = $result->fetch_assoc()['count'];
    }
    
    // Toplam kategori
    $result = $conn->query("SELECT COUNT(*) as count FROM categories WHERE is_active = 1");
    if ($result) {
        $stats['total_categories'] = $result->fetch_assoc()['count'];
    }
    
    // Okunmamış mesajlar
    $result = $conn->query("SELECT COUNT(*) as count FROM contact_messages WHERE is_read = 0");
    if ($result) {
        $stats['unread_messages'] = $result->fetch_assoc()['count'];
    }
    
    // Bugünkü ziyaretçiler
    $result = $conn->query("SELECT unique_visitors FROM site_statistics WHERE stat_date = CURDATE()");
    if ($result && $result->num_rows > 0) {
        $stats['today_visitors'] = $result->fetch_assoc()['unique_visitors'];
    }
    
    // WhatsApp tıklamaları
    $result = $conn->query("SELECT SUM(whatsapp_clicks) as total FROM site_statistics WHERE stat_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)");
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $stats['whatsapp_clicks'] = $row['total'] ?? 0;
    }
    
    return $stats;
}

/**
 * Son siparişleri getir
 */
function getRecentOrders($limit = 10) {
    global $conn;
    
    $stmt = $conn->prepare("
        SELECT o.*, c.name as category_name, p.name as product_name 
        FROM orders o 
        LEFT JOIN categories c ON o.category_id = c.id 
        LEFT JOIN products p ON o.product_id = p.id 
        ORDER BY o.created_at DESC 
        LIMIT ?
    ");
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $orders = [];
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
    
    return $orders;
}

/**
 * Sipariş durumu badge
 */
function getOrderStatusBadge($status) {
    $badges = [
        'pending' => '<span class="badge badge-warning"><i class="fas fa-clock"></i> Bekliyor</span>',
        'confirmed' => '<span class="badge badge-info"><i class="fas fa-check"></i> Onaylandı</span>',
        'preparing' => '<span class="badge badge-primary"><i class="fas fa-box"></i> Hazırlanıyor</span>',
        'delivered' => '<span class="badge badge-success"><i class="fas fa-check-circle"></i> Teslim Edildi</span>',
        'cancelled' => '<span class="badge badge-danger"><i class="fas fa-times-circle"></i> İptal</span>'
    ];
    
    return $badges[$status] ?? '<span class="badge badge-secondary">Bilinmiyor</span>';
}

/**
 * Sipariş türü badge
 */
function getOrderTypeBadge($type) {
    $badges = [
        'whatsapp' => '<span class="badge badge-whatsapp"><i class="fab fa-whatsapp"></i> WhatsApp</span>',
        'phone' => '<span class="badge badge-phone"><i class="fas fa-phone"></i> Telefon</span>',
        'form' => '<span class="badge badge-form"><i class="fas fa-envelope"></i> Form</span>',
        'other' => '<span class="badge badge-secondary"><i class="fas fa-question"></i> Diğer</span>'
    ];
    
    return $badges[$type] ?? '<span class="badge badge-secondary">Bilinmiyor</span>';
}

/**
 * Tarih formatla
 */
function formatDate($date, $format = 'd.m.Y H:i') {
    if (empty($date)) return '-';
    $timestamp = strtotime($date);
    return date($format, $timestamp);
}

/**
 * Para formatla
 */
function formatMoney($amount) {
    if (empty($amount)) return '-';
    return number_format($amount, 2, ',', '.') . ' ₺';
}

/**
 * Zaman farkı (örn: 2 saat önce)
 */
function timeAgo($datetime) {
    $timestamp = strtotime($datetime);
    $diff = time() - $timestamp;
    
    if ($diff < 60) {
        return $diff . ' saniye önce';
    } elseif ($diff < 3600) {
        return floor($diff / 60) . ' dakika önce';
    } elseif ($diff < 86400) {
        return floor($diff / 3600) . ' saat önce';
    } elseif ($diff < 604800) {
        return floor($diff / 86400) . ' gün önce';
    } else {
        return formatDate($datetime);
    }
}

/**
 * Başarı mesajı göster
 */
function showSuccess($message) {
    return '<div class="alert alert-success"><i class="fas fa-check-circle"></i> ' . htmlspecialchars($message) . '</div>';
}

/**
 * Hata mesajı göster
 */
function showError($message) {
    return '<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> ' . htmlspecialchars($message) . '</div>';
}

/**
 * Bilgi mesajı göster
 */
function showInfo($message) {
    return '<div class="alert alert-info"><i class="fas fa-info-circle"></i> ' . htmlspecialchars($message) . '</div>';
}

/**
 * Uyarı mesajı göster
 */
function showWarning($message) {
    return '<div class="alert alert-warning"><i class="fas fa-exclamation-triangle"></i> ' . htmlspecialchars($message) . '</div>';
}

/**
 * Sayfalama oluştur
 */
function createPagination($currentPage, $totalPages, $baseUrl) {
    if ($totalPages <= 1) return '';
    
    $html = '<nav class="pagination-nav"><ul class="pagination">';
    
    // Önceki sayfa
    if ($currentPage > 1) {
        $html .= '<li><a href="' . $baseUrl . '?page=' . ($currentPage - 1) . '"><i class="fas fa-chevron-left"></i></a></li>';
    }
    
    // Sayfa numaraları
    for ($i = 1; $i <= $totalPages; $i++) {
        if ($i == $currentPage) {
            $html .= '<li class="active"><span>' . $i . '</span></li>';
        } else {
            $html .= '<li><a href="' . $baseUrl . '?page=' . $i . '">' . $i . '</a></li>';
        }
    }
    
    // Sonraki sayfa
    if ($currentPage < $totalPages) {
        $html .= '<li><a href="' . $baseUrl . '?page=' . ($currentPage + 1) . '"><i class="fas fa-chevron-right"></i></a></li>';
    }
    
    $html .= '</ul></nav>';
    
    return $html;
}

/**
 * WhatsApp mesajı gönder (API entegrasyonu)
 */
function sendWhatsAppNotification($phone, $message) {
    // Bu fonksiyon için WhatsApp Business API veya bir servis kullanılabilir
    // Şimdilik sadece bir template oluşturuyoruz
    
    // Telefonu temizle
    $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
    
    // WhatsApp web linki oluştur
    $whatsappUrl = "https://wa.me/" . $cleanPhone . "?text=" . urlencode($message);
    
    return $whatsappUrl;
}

/**
 * Sipariş numarası oluştur
 */
function generateOrderNumber() {
    return 'ORD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
}
?>

