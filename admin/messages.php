<?php
/**
 * Admin Panel - Mesaj Yönetimi
 */
require_once 'includes/auth.php';
require_once 'includes/functions.php';

checkAdminLogin();

$adminInfo = getAdminInfo();
$stats = getAdminStats();

$success = '';
$error = '';

// Mesaj okundu işaretle
if (isset($_GET['action']) && $_GET['action'] === 'mark_read' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("UPDATE contact_messages SET is_read = 1, read_at = NOW() WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $success = 'Mesaj okundu olarak işaretlendi!';
    }
}

// Mesaj sil
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("DELETE FROM contact_messages WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $success = 'Mesaj silindi!';
    } else {
        $error = 'Mesaj silinirken hata oluştu!';
    }
}

// Filtreleme
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$whereClause = '';
if ($filter === 'unread') {
    $whereClause = 'WHERE is_read = 0';
} elseif ($filter === 'read') {
    $whereClause = 'WHERE is_read = 1';
}

// Mesajları getir
$messagesResult = $conn->query("SELECT * FROM contact_messages $whereClause ORDER BY created_at DESC LIMIT 100");
$messages = [];
while ($row = $messagesResult->fetch_assoc()) {
    $messages[] = $row;
}

$pageTitle = 'Mesajlar';
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
            <h1><i class="fas fa-envelope"></i> Mesajlar</h1>
            <p><?php echo count($messages); ?> mesaj</p>
        </div>

        <?php if ($success): ?>
            <div class="alert alert-success alert-auto-hide">
                <i class="fas fa-check-circle"></i> <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger alert-auto-hide">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <!-- Filtreler -->
        <div class="content-section" style="margin-bottom: 20px;">
            <div style="display: flex; gap: 10px;">
                <a href="messages.php?filter=all" 
                   class="btn btn-sm <?php echo $filter === 'all' ? 'btn-primary' : 'btn-secondary'; ?>">
                    <i class="fas fa-inbox"></i> Tümü (<?php echo $conn->query("SELECT COUNT(*) as c FROM contact_messages")->fetch_assoc()['c']; ?>)
                </a>
                <a href="messages.php?filter=unread" 
                   class="btn btn-sm <?php echo $filter === 'unread' ? 'btn-warning' : 'btn-secondary'; ?>">
                    <i class="fas fa-envelope"></i> Okunmamış (<?php echo $conn->query("SELECT COUNT(*) as c FROM contact_messages WHERE is_read = 0")->fetch_assoc()['c']; ?>)
                </a>
                <a href="messages.php?filter=read" 
                   class="btn btn-sm <?php echo $filter === 'read' ? 'btn-success' : 'btn-secondary'; ?>">
                    <i class="fas fa-envelope-open"></i> Okunmuş (<?php echo $conn->query("SELECT COUNT(*) as c FROM contact_messages WHERE is_read = 1")->fetch_assoc()['c']; ?>)
                </a>
            </div>
        </div>

        <div class="content-section">
            <?php if (empty($messages)): ?>
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <p>Henüz mesaj bulunmuyor</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Ad Soyad</th>
                                <th>Telefon</th>
                                <th>E-posta</th>
                                <th>Mesaj</th>
                                <th>Tarih</th>
                                <th>Durum</th>
                                <th>İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($messages as $message): ?>
                                <tr style="<?php echo !$message['is_read'] ? 'background: #fff3cd;' : ''; ?>">
                                    <td><strong><?php echo htmlspecialchars($message['full_name']); ?></strong></td>
                                    <td>
                                        <a href="tel:<?php echo $message['phone']; ?>" title="Ara">
                                            <i class="fas fa-phone"></i> <?php echo htmlspecialchars($message['phone']); ?>
                                        </a>
                                    </td>
                                    <td>
                                        <?php if ($message['email']): ?>
                                            <a href="mailto:<?php echo $message['email']; ?>" title="E-posta Gönder">
                                                <?php echo htmlspecialchars($message['email']); ?>
                                            </a>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <button onclick='showMessage(<?php echo json_encode($message); ?>)' 
                                               class="btn btn-sm btn-link" 
                                               style="text-align: left; padding: 0;">
                                            <?php echo htmlspecialchars(substr($message['message'], 0, 60)); ?>
                                            <?php echo strlen($message['message']) > 60 ? '...' : ''; ?>
                                        </button>
                                    </td>
                                    <td>
                                        <small title="<?php echo formatDate($message['created_at']); ?>">
                                            <?php echo timeAgo($message['created_at']); ?>
                                        </small>
                                    </td>
                                    <td>
                                        <?php if ($message['is_read']): ?>
                                            <span class="badge badge-success"><i class="fas fa-check"></i> Okundu</span>
                                        <?php else: ?>
                                            <span class="badge badge-warning"><i class="fas fa-envelope"></i> Okunmadı</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <button onclick='showMessage(<?php echo json_encode($message); ?>)' 
                                                   class="btn btn-sm btn-info" 
                                                   title="Detay">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <?php if (!$message['is_read']): ?>
                                                <a href="?action=mark_read&id=<?php echo $message['id']; ?>&filter=<?php echo $filter; ?>" 
                                                   class="btn btn-sm btn-success" 
                                                   title="Okundu İşaretle">
                                                    <i class="fas fa-check"></i>
                                                </a>
                                            <?php endif; ?>
                                            <a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $message['phone']); ?>" 
                                               class="btn btn-sm btn-success" 
                                               title="WhatsApp" 
                                               target="_blank">
                                                <i class="fab fa-whatsapp"></i>
                                            </a>
                                            <a href="?action=delete&id=<?php echo $message['id']; ?>&filter=<?php echo $filter; ?>" 
                                               class="btn btn-sm btn-danger" 
                                               title="Sil"
                                               onclick="return confirm('Bu mesajı silmek istediğinizden emin misiniz?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Mesaj Detay Modal -->
    <div class="modal-backdrop" id="messageModal" style="display: none;">
        <div class="modal-dialog" style="max-width: 600px;">
            <div class="modal-header">
                <h3><i class="fas fa-envelope"></i> Mesaj Detayı</h3>
                <button type="button" class="modal-close" onclick="closeMessageModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body" id="messageBody">
                <!-- Dinamik içerik -->
            </div>
        </div>
    </div>

    <script src="assets/js/admin.js"></script>
    <script>
        function showMessage(message) {
            const modal = document.getElementById('messageModal');
            const body = document.getElementById('messageBody');
            
            body.innerHTML = `
                <div style="margin-bottom: 20px;">
                    <label style="font-weight: 600; color: #333; display: block; margin-bottom: 5px;">
                        <i class="fas fa-user"></i> Gönderen
                    </label>
                    <p style="margin: 0; padding: 10px; background: #f8f9fa; border-radius: 5px;">
                        <strong>${message.full_name}</strong>
                    </p>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
                    <div>
                        <label style="font-weight: 600; color: #333; display: block; margin-bottom: 5px;">
                            <i class="fas fa-phone"></i> Telefon
                        </label>
                        <p style="margin: 0; padding: 10px; background: #f8f9fa; border-radius: 5px;">
                            <a href="tel:${message.phone}">${message.phone}</a>
                        </p>
                    </div>
                    
                    <div>
                        <label style="font-weight: 600; color: #333; display: block; margin-bottom: 5px;">
                            <i class="fas fa-envelope"></i> E-posta
                        </label>
                        <p style="margin: 0; padding: 10px; background: #f8f9fa; border-radius: 5px;">
                            ${message.email ? `<a href="mailto:${message.email}">${message.email}</a>` : '-'}
                        </p>
                    </div>
                </div>
                
                <div style="margin-bottom: 20px;">
                    <label style="font-weight: 600; color: #333; display: block; margin-bottom: 5px;">
                        <i class="fas fa-comment"></i> Mesaj
                    </label>
                    <p style="margin: 0; padding: 15px; background: #f8f9fa; border-radius: 5px; white-space: pre-wrap;">
                        ${message.message}
                    </p>
                </div>
                
                <div style="margin-bottom: 20px;">
                    <label style="font-weight: 600; color: #333; display: block; margin-bottom: 5px;">
                        <i class="fas fa-clock"></i> Gönderim Zamanı
                    </label>
                    <p style="margin: 0; padding: 10px; background: #f8f9fa; border-radius: 5px;">
                        ${formatDate(message.created_at)}
                    </p>
                </div>
                
                ${message.ip_address ? `
                <div style="margin-bottom: 20px;">
                    <label style="font-weight: 600; color: #333; display: block; margin-bottom: 5px;">
                        <i class="fas fa-network-wired"></i> IP Adresi
                    </label>
                    <p style="margin: 0; padding: 10px; background: #f8f9fa; border-radius: 5px; font-size: 13px;">
                        ${message.ip_address}
                    </p>
                </div>
                ` : ''}
                
                <div style="display: flex; gap: 10px;">
                    ${!message.is_read ? `
                        <a href="?action=mark_read&id=${message.id}&filter=<?php echo $filter; ?>" class="btn btn-success">
                            <i class="fas fa-check"></i> Okundu İşaretle
                        </a>
                    ` : ''}
                    <a href="https://wa.me/${message.phone.replace(/[^0-9]/g, '')}" 
                       class="btn btn-success" 
                       target="_blank">
                        <i class="fab fa-whatsapp"></i> WhatsApp Gönder
                    </a>
                    <button type="button" class="btn btn-secondary" onclick="closeMessageModal()">
                        <i class="fas fa-times"></i> Kapat
                    </button>
                </div>
            `;
            
            modal.style.display = 'flex';
        }
        
        function closeMessageModal() {
            document.getElementById('messageModal').style.display = 'none';
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
        document.getElementById('messageModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeMessageModal();
            }
        });
    </script>
</body>
</html>

