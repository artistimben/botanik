<?php
/**
 * Admin Panel - Kategori Yönetimi
 */
require_once 'includes/auth.php';
require_once 'includes/functions.php';

checkAdminLogin();

$adminInfo = getAdminInfo();
$stats = getAdminStats();

$success = '';
$error = '';

// Kategori ekleme/güncelleme
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_category'])) {
        $name = cleanInput($_POST['name']);
        $slug = cleanInput($_POST['slug']);
        $folderName = cleanInput($_POST['folder_name']);
        $icon = cleanInput($_POST['icon']);
        $description = cleanInput($_POST['description']);
        $displayOrder = intval($_POST['display_order']);
        
        $stmt = $conn->prepare("INSERT INTO categories (name, slug, folder_name, icon, description, display_order) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssi", $name, $slug, $folderName, $icon, $description, $displayOrder);
        
        if ($stmt->execute()) {
            $success = 'Kategori başarıyla eklendi!';
        } else {
            $error = 'Kategori eklenirken bir hata oluştu!';
        }
    }
    
    if (isset($_POST['update_category'])) {
        $id = intval($_POST['category_id']);
        $name = cleanInput($_POST['name']);
        $slug = cleanInput($_POST['slug']);
        $folderName = cleanInput($_POST['folder_name']);
        $icon = cleanInput($_POST['icon']);
        $description = cleanInput($_POST['description']);
        $displayOrder = intval($_POST['display_order']);
        $isActive = isset($_POST['is_active']) ? 1 : 0;
        
        $stmt = $conn->prepare("UPDATE categories SET name = ?, slug = ?, folder_name = ?, icon = ?, description = ?, display_order = ?, is_active = ? WHERE id = ?");
        $stmt->bind_param("sssssiii", $name, $slug, $folderName, $icon, $description, $displayOrder, $isActive, $id);
        
        if ($stmt->execute()) {
            $success = 'Kategori başarıyla güncellendi!';
        } else {
            $error = 'Kategori güncellenirken bir hata oluştu!';
        }
    }
}

// Kategori silme
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        $success = 'Kategori başarıyla silindi!';
    } else {
        $error = 'Kategori silinirken bir hata oluştu!';
    }
}

// Kategorileri getir
$categoriesResult = $conn->query("SELECT * FROM categories ORDER BY display_order ASC, name ASC");
$categories = [];
while ($row = $categoriesResult->fetch_assoc()) {
    $categories[] = $row;
}

$pageTitle = 'Kategori Yönetimi';
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
        .category-card {
            background: var(--white);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            gap: 20px;
            transition: all 0.3s;
        }
        
        .category-card:hover {
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .category-icon {
            font-size: 48px;
            min-width: 60px;
            text-align: center;
        }
        
        .category-info {
            flex: 1;
        }
        
        .category-info h3 {
            margin: 0 0 5px 0;
            font-size: 18px;
        }
        
        .category-info p {
            margin: 0;
            color: var(--dark-color);
            opacity: 0.7;
            font-size: 14px;
        }
        
        .category-meta {
            font-size: 12px;
            color: var(--dark-color);
            opacity: 0.5;
            margin-top: 5px;
        }
        
        .category-actions {
            display: flex;
            gap: 5px;
        }
        
        .modal-form {
            max-width: 600px;
            margin: 0 auto;
        }
        
        .modal-form .form-group {
            margin-bottom: 20px;
        }
        
        .modal-form label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--dark-color);
        }
        
        .modal-backdrop {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.7);
            z-index: 9999;
            align-items: center;
            justify-content: center;
        }
        
        .modal-backdrop.show {
            display: flex;
        }
        
        .modal-dialog {
            background: var(--white);
            border-radius: 15px;
            max-width: 700px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
        }
        
        .modal-header {
            padding: 20px 25px;
            border-bottom: 1px solid #e9ecef;
        }
        
        .modal-body {
            padding: 25px;
        }
        
        .checkbox-label {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
        }
        
        .checkbox-label input {
            width: 20px;
            height: 20px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <?php include 'includes/sidebar.php'; ?>

    <div class="main-content">
        <div class="page-header">
            <div>
                <h1><i class="fas fa-th-large"></i> Kategori Yönetimi</h1>
                <p><?php echo count($categories); ?> kategori</p>
            </div>
            <button onclick="openAddModal()" class="btn btn-primary">
                <i class="fas fa-plus"></i> Yeni Kategori
            </button>
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

        <div class="content-section">
            <?php if (empty($categories)): ?>
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <p>Henüz kategori bulunmuyor</p>
                </div>
            <?php else: ?>
                <?php foreach ($categories as $category): ?>
                    <div class="category-card">
                        <div class="category-icon">
                            <?php echo $category['icon']; ?>
                        </div>
                        <div class="category-info">
                            <h3><?php echo htmlspecialchars($category['name']); ?></h3>
                            <p><?php echo htmlspecialchars($category['description']); ?></p>
                            <div class="category-meta">
                                Klasör: <?php echo htmlspecialchars($category['folder_name']); ?> | 
                                Slug: <?php echo htmlspecialchars($category['slug']); ?> | 
                                Sıra: <?php echo $category['display_order']; ?>
                            </div>
                        </div>
                        <div class="category-actions">
                            <?php if ($category['is_active']): ?>
                                <span class="badge badge-success">Aktif</span>
                            <?php else: ?>
                                <span class="badge badge-secondary">Pasif</span>
                            <?php endif; ?>
                            <button onclick='editCategory(<?php echo json_encode($category); ?>)' 
                                   class="btn btn-sm btn-info">
                                <i class="fas fa-edit"></i>
                            </button>
                            <a href="?action=delete&id=<?php echo $category['id']; ?>" 
                               class="btn btn-sm btn-danger"
                               onclick="return confirm('Bu kategoriyi silmek istediğinizden emin misiniz?')">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Kategori Ekleme/Düzenleme Modal -->
    <div class="modal-backdrop" id="categoryModal">
        <div class="modal-dialog">
            <div class="modal-header">
                <h3 id="modalTitle">Yeni Kategori</h3>
                <button type="button" class="modal-close" onclick="closeModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" action="" class="modal-form" id="categoryForm">
                    <input type="hidden" name="category_id" id="category_id">
                    
                    <div class="form-group">
                        <label for="name">Kategori Adı *</label>
                        <input type="text" name="name" id="name" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="slug">Slug (URL) *</label>
                        <input type="text" name="slug" id="slug" class="form-control" required>
                        <small>Örnek: buketler, arajmanlar</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="folder_name">Klasör Adı *</label>
                        <input type="text" name="folder_name" id="folder_name" class="form-control" required>
                        <small>images/GÖRSELLER/ altındaki klasör adı</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="icon">İkon (Emoji)</label>
                        <input type="text" name="icon" id="icon" class="form-control" placeholder="💐">
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Açıklama</label>
                        <textarea name="description" id="description" class="form-control" rows="3"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="display_order">Sıra Numarası</label>
                        <input type="number" name="display_order" id="display_order" class="form-control" value="0">
                    </div>
                    
                    <div class="form-group" id="activeCheckboxGroup" style="display:none;">
                        <label class="checkbox-label">
                            <input type="checkbox" name="is_active" id="is_active" value="1" checked>
                            <span>Aktif</span>
                        </label>
                    </div>
                    
                    <div style="display: flex; gap: 10px;">
                        <button type="submit" name="add_category" id="submitBtn" class="btn btn-primary">
                            <i class="fas fa-save"></i> Kaydet
                        </button>
                        <button type="button" class="btn btn-secondary" onclick="closeModal()">
                            <i class="fas fa-times"></i> İptal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="assets/js/admin.js"></script>
    <script>
        function openAddModal() {
            document.getElementById('modalTitle').textContent = 'Yeni Kategori';
            document.getElementById('categoryForm').reset();
            document.getElementById('category_id').value = '';
            document.getElementById('submitBtn').name = 'add_category';
            document.getElementById('activeCheckboxGroup').style.display = 'none';
            document.getElementById('categoryModal').classList.add('show');
        }
        
        function editCategory(category) {
            document.getElementById('modalTitle').textContent = 'Kategori Düzenle';
            document.getElementById('category_id').value = category.id;
            document.getElementById('name').value = category.name;
            document.getElementById('slug').value = category.slug;
            document.getElementById('folder_name').value = category.folder_name;
            document.getElementById('icon').value = category.icon;
            document.getElementById('description').value = category.description;
            document.getElementById('display_order').value = category.display_order;
            document.getElementById('is_active').checked = category.is_active == 1;
            document.getElementById('submitBtn').name = 'update_category';
            document.getElementById('activeCheckboxGroup').style.display = 'block';
            document.getElementById('categoryModal').classList.add('show');
        }
        
        function closeModal() {
            document.getElementById('categoryModal').classList.remove('show');
        }
        
        // Modal dışına tıklandığında kapat
        document.getElementById('categoryModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });
        
        // Slug otomatik oluştur
        document.getElementById('name').addEventListener('input', function() {
            if (!document.getElementById('category_id').value) { // Sadece yeni kategoride
                const slug = this.value.toLowerCase()
                    .replace(/ğ/g, 'g')
                    .replace(/ü/g, 'u')
                    .replace(/ş/g, 's')
                    .replace(/ı/g, 'i')
                    .replace(/ö/g, 'o')
                    .replace(/ç/g, 'c')
                    .replace(/[^a-z0-9]+/g, '-')
                    .replace(/^-+|-+$/g, '');
                document.getElementById('slug').value = slug;
            }
        });
    </script>
</body>
</html>

