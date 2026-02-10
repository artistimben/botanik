<?php
/**
 * Admin Panel - Ürün ve Kategori Yönetimi (Birleşik)
 */
require_once 'includes/auth.php';
require_once 'includes/functions.php';

checkAdminLogin();

$adminInfo = getAdminInfo();
$stats = getAdminStats();

$success = '';
$error = '';

// Seçilen kategori
$selectedCategory = isset($_GET['category']) ? intval($_GET['category']) : null;

// Kategori ekleme
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category'])) {
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

// Kategori güncelleme
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_category'])) {
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

// Kategori silme
if (isset($_GET['delete_category']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        $success = 'Kategori başarıyla silindi!';
        $selectedCategory = null;
    } else {
        $error = 'Kategori silinirken bir hata oluştu!';
    }
}

// Ürün ekleme
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $categoryId = intval($_POST['category_id']);
    $name = cleanInput($_POST['name']);
    $slug = cleanInput($_POST['slug']);
    $shortDescription = cleanInput($_POST['short_description']);
    $description = cleanInput($_POST['description']);
    $price = !empty($_POST['price']) ? floatval($_POST['price']) : null;
    $imagePath = cleanInput($_POST['image_path']);
    $isFeatured = isset($_POST['is_featured']) ? 1 : 0;
    $displayOrder = intval($_POST['display_order']);
    
    $stmt = $conn->prepare("INSERT INTO products (category_id, name, slug, short_description, description, price, image_path, is_featured, display_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssdsii", $categoryId, $name, $slug, $shortDescription, $description, $price, $imagePath, $isFeatured, $displayOrder);
    
    if ($stmt->execute()) {
        $success = 'Ürün başarıyla eklendi!';
    } else {
        $error = 'Ürün eklenirken bir hata oluştu!';
    }
}

// Ürün güncelleme
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_product'])) {
    $id = intval($_POST['product_id']);
    $categoryId = intval($_POST['category_id']);
    $name = cleanInput($_POST['name']);
    $slug = cleanInput($_POST['slug']);
    $shortDescription = cleanInput($_POST['short_description']);
    $description = cleanInput($_POST['description']);
    $price = !empty($_POST['price']) ? floatval($_POST['price']) : null;
    $imagePath = cleanInput($_POST['image_path']);
    $isFeatured = isset($_POST['is_featured']) ? 1 : 0;
    $isActive = isset($_POST['is_active']) ? 1 : 0;
    $displayOrder = intval($_POST['display_order']);
    
    $stmt = $conn->prepare("UPDATE products SET category_id = ?, name = ?, slug = ?, short_description = ?, description = ?, price = ?, image_path = ?, is_featured = ?, is_active = ?, display_order = ? WHERE id = ?");
    $stmt->bind_param("issssdsiiii", $categoryId, $name, $slug, $shortDescription, $description, $price, $imagePath, $isFeatured, $isActive, $displayOrder, $id);
    
    if ($stmt->execute()) {
        $success = 'Ürün başarıyla güncellendi!';
    } else {
        $error = 'Ürün güncellenirken bir hata oluştu!';
    }
}

// Ürün silme
if (isset($_GET['delete_product']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        $success = 'Ürün başarıyla silindi!';
    } else {
        $error = 'Ürün silinirken bir hata oluştu!';
    }
}

// Kategorileri getir
$categoriesResult = $conn->query("
    SELECT c.*, 
           COUNT(p.id) as product_count 
    FROM categories c 
    LEFT JOIN products p ON c.id = p.category_id 
    GROUP BY c.id 
    ORDER BY c.display_order ASC, c.name ASC
");
$categories = [];
while ($row = $categoriesResult->fetch_assoc()) {
    $categories[] = $row;
}

// İlk kategoriyi varsayılan olarak seç
if ($selectedCategory === null && !empty($categories)) {
    $selectedCategory = $categories[0]['id'];
}

// Seçilen kategorinin ürünlerini getir
$products = [];
if ($selectedCategory) {
    $stmt = $conn->prepare("
        SELECT p.*, c.name as category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        WHERE p.category_id = ?
        ORDER BY p.display_order ASC, p.created_at DESC
    ");
    $stmt->bind_param("i", $selectedCategory);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}

// Seçilen kategori bilgisini al
$currentCategory = null;
foreach ($categories as $cat) {
    if ($cat['id'] == $selectedCategory) {
        $currentCategory = $cat;
        break;
    }
}

$pageTitle = 'Ürün ve Kategori Yönetimi';
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
        .content-wrapper {
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 20px;
            align-items: start;
        }
        
        .categories-sidebar {
            background: var(--white);
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            position: sticky;
            top: 85px;
            max-height: calc(100vh - 105px);
            overflow-y: auto;
        }
        
        .categories-sidebar h3 {
            margin: 0 0 15px 0;
            font-size: 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .category-item {
            padding: 12px 15px;
            margin-bottom: 8px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: var(--dark-color);
            border: 2px solid transparent;
        }
        
        .category-item:hover {
            background: var(--light-color);
            border-color: var(--primary-color);
        }
        
        .category-item.active {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: var(--white);
            border-color: var(--primary-color);
        }
        
        .category-icon {
            font-size: 24px;
            width: 35px;
            text-align: center;
        }
        
        .category-info {
            flex: 1;
        }
        
        .category-info h4 {
            margin: 0;
            font-size: 14px;
        }
        
        .category-info small {
            font-size: 11px;
            opacity: 0.8;
        }
        
        .category-actions {
            display: flex;
            gap: 5px;
            opacity: 0;
            transition: opacity 0.3s;
        }
        
        .category-item:hover .category-actions {
            opacity: 1;
        }
        
        .category-item.active .category-actions {
            opacity: 1;
        }
        
        .products-content {
            background: var(--white);
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        
        .products-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--light-color);
        }
        
        .products-header h2 {
            margin: 0;
            font-size: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .product-card {
            background: var(--light-color);
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            display: flex;
            gap: 15px;
            transition: all 0.3s;
        }
        
        .product-card:hover {
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .product-image {
            width: 100px;
            height: 100px;
            border-radius: 8px;
            object-fit: cover;
            flex-shrink: 0;
        }
        
        .product-image-placeholder {
            width: 100px;
            height: 100px;
            border-radius: 8px;
            background: var(--white);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            color: var(--dark-color);
            opacity: 0.3;
        }
        
        .product-info {
            flex: 1;
        }
        
        .product-info h3 {
            margin: 0 0 5px 0;
            font-size: 16px;
        }
        
        .product-info p {
            margin: 0 0 8px 0;
            color: var(--dark-color);
            opacity: 0.7;
            font-size: 13px;
        }
        
        .product-meta {
            display: flex;
            gap: 15px;
            font-size: 12px;
            color: var(--dark-color);
            opacity: 0.6;
        }
        
        .product-actions {
            display: flex;
            flex-direction: column;
            gap: 5px;
            justify-content: center;
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
            overflow-y: auto;
            padding: 20px;
        }
        
        .modal-backdrop.show {
            display: flex;
        }
        
        .modal-dialog {
            background: var(--white);
            border-radius: 15px;
            max-width: 800px;
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
        }
        
        .modal-body {
            padding: 25px;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--dark-color);
            font-size: 14px;
        }
        
        .form-control, .form-select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            font-family: inherit;
        }
        
        .form-control:focus, .form-select:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(45, 80, 22, 0.1);
        }
        
        textarea.form-control {
            min-height: 100px;
            resize: vertical;
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
        
        @media (max-width: 1024px) {
            .content-wrapper {
                grid-template-columns: 1fr;
            }
            
            .categories-sidebar {
                position: relative;
                top: 0;
                max-height: none;
            }
        }
        
        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .product-card {
                flex-direction: column;
            }
            
            .product-image,
            .product-image-placeholder {
                width: 100%;
                height: 200px;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <?php include 'includes/sidebar.php'; ?>

    <div class="main-content">
        <div class="page-header">
            <div>
                <h1><i class="fas fa-boxes"></i> Ürün ve Kategori Yönetimi</h1>
                <p><?php echo count($categories); ?> kategori, <?php echo array_sum(array_column($categories, 'product_count')); ?> toplam ürün</p>
            </div>
            <div style="display: flex; gap: 10px;">
                <a href="import-products.php" class="btn btn-info">
                    <i class="fas fa-file-import"></i> Toplu İçe Aktar
                </a>
            </div>
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

        <div class="content-wrapper">
            <!-- Sol Taraf: Kategoriler -->
            <div class="categories-sidebar">
                <h3>
                    <span><i class="fas fa-th-large"></i> Kategoriler</span>
                    <button onclick="openAddCategoryModal()" class="btn btn-sm btn-primary" title="Yeni Kategori">
                        <i class="fas fa-plus"></i>
                    </button>
                </h3>
                
                <?php foreach ($categories as $cat): ?>
                    <a href="?category=<?php echo $cat['id']; ?>" 
                       class="category-item <?php echo $selectedCategory == $cat['id'] ? 'active' : ''; ?>">
                        <span class="category-icon"><?php echo $cat['icon']; ?></span>
                        <div class="category-info">
                            <h4><?php echo htmlspecialchars($cat['name']); ?></h4>
                            <small><?php echo $cat['product_count']; ?> ürün</small>
                        </div>
                        <div class="category-actions">
                            <button onclick="event.preventDefault(); editCategory(<?php echo htmlspecialchars(json_encode($cat)); ?>);" 
                                   class="btn btn-sm btn-info" 
                                   title="Düzenle">
                                <i class="fas fa-edit"></i>
                            </button>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>

            <!-- Sağ Taraf: Ürünler -->
            <div class="products-content">
                <?php if ($currentCategory): ?>
                    <div class="products-header">
                        <h2>
                            <span><?php echo $currentCategory['icon']; ?></span>
                            <span><?php echo htmlspecialchars($currentCategory['name']); ?></span>
                            <span style="font-size: 14px; font-weight: normal; opacity: 0.7;">
                                (<?php echo count($products); ?> ürün)
                            </span>
                        </h2>
                        <button onclick="openAddProductModal(<?php echo $selectedCategory; ?>)" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Yeni Ürün
                        </button>
                    </div>

                    <?php if (empty($products)): ?>
                        <div class="empty-state">
                            <i class="fas fa-box-open"></i>
                            <p>Bu kategoride henüz ürün yok</p>
                            <button onclick="openAddProductModal(<?php echo $selectedCategory; ?>)" class="btn btn-primary">
                                <i class="fas fa-plus"></i> İlk Ürünü Ekle
                            </button>
                        </div>
                    <?php else: ?>
                        <?php foreach ($products as $product): ?>
                            <div class="product-card">
                                <?php if (!empty($product['image_path']) && file_exists('../' . $product['image_path'])): ?>
                                    <img src="../<?php echo htmlspecialchars($product['image_path']); ?>" 
                                         alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                         class="product-image">
                                <?php else: ?>
                                    <div class="product-image-placeholder">
                                        <i class="fas fa-image"></i>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="product-info">
                                    <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                                    <p><?php echo htmlspecialchars($product['short_description']); ?></p>
                                    
                                    <div class="product-meta">
                                        <?php if ($product['price']): ?>
                                            <span><i class="fas fa-tag"></i> <?php echo formatMoney($product['price']); ?></span>
                                        <?php endif; ?>
                                        <span><i class="fas fa-sort-numeric-up"></i> Sıra: <?php echo $product['display_order']; ?></span>
                                    </div>
                                    
                                    <div style="margin-top: 8px; display: flex; gap: 8px;">
                                        <?php if ($product['is_active']): ?>
                                            <span class="badge badge-success">Aktif</span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary">Pasif</span>
                                        <?php endif; ?>
                                        
                                        <?php if ($product['is_featured']): ?>
                                            <span class="badge badge-warning"><i class="fas fa-star"></i> Öne Çıkan</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <div class="product-actions">
                                    <button onclick='editProduct(<?php echo json_encode($product); ?>)' 
                                           class="btn btn-sm btn-info">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <a href="?category=<?php echo $selectedCategory; ?>&delete_product=1&id=<?php echo $product['id']; ?>" 
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirm('Bu ürünü silmek istediğinizden emin misiniz?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-th-large"></i>
                        <p>Lütfen bir kategori seçin</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Kategori Modal -->
    <div class="modal-backdrop" id="categoryModal">
        <div class="modal-dialog" style="max-width: 600px;">
            <div class="modal-header">
                <h3 id="categoryModalTitle"><i class="fas fa-th-large"></i> Yeni Kategori</h3>
                <button type="button" class="modal-close" onclick="closeCategoryModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" action="" id="categoryForm">
                    <input type="hidden" name="category_id" id="category_id">
                    
                    <div class="form-group">
                        <label for="cat_name">Kategori Adı *</label>
                        <input type="text" name="name" id="cat_name" class="form-control" required>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="cat_slug">Slug (URL) *</label>
                            <input type="text" name="slug" id="cat_slug" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="cat_folder_name">Klasör Adı *</label>
                            <input type="text" name="folder_name" id="cat_folder_name" class="form-control" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="cat_icon">İkon (Emoji)</label>
                            <input type="text" name="icon" id="cat_icon" class="form-control" placeholder="💐">
                        </div>
                        
                        <div class="form-group">
                            <label for="cat_display_order">Sıra Numarası</label>
                            <input type="number" name="display_order" id="cat_display_order" class="form-control" value="0">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="cat_description">Açıklama</label>
                        <textarea name="description" id="cat_description" class="form-control" rows="2"></textarea>
                    </div>
                    
                    <div class="form-group" id="catActiveCheckboxGroup" style="display:none;">
                        <label class="checkbox-label">
                            <input type="checkbox" name="is_active" id="cat_is_active" value="1" checked>
                            <span>Aktif</span>
                        </label>
                    </div>
                    
                    <div style="display: flex; gap: 10px;">
                        <button type="submit" name="add_category" id="categorySubmitBtn" class="btn btn-primary">
                            <i class="fas fa-save"></i> Kaydet
                        </button>
                        <button type="button" class="btn btn-secondary" onclick="closeCategoryModal()">
                            <i class="fas fa-times"></i> İptal
                        </button>
                        <a href="?category=<?php echo $selectedCategory; ?>&delete_category=1&id=" 
                           id="deleteCategoryBtn"
                           class="btn btn-danger" 
                           style="margin-left: auto; display: none;"
                           onclick="return confirm('Bu kategoriyi ve tüm ürünlerini silmek istediğinizden emin misiniz?')">
                            <i class="fas fa-trash"></i> Kategoriyi Sil
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Ürün Modal -->
    <div class="modal-backdrop" id="productModal">
        <div class="modal-dialog">
            <div class="modal-header">
                <h3 id="productModalTitle"><i class="fas fa-box"></i> Yeni Ürün</h3>
                <button type="button" class="modal-close" onclick="closeProductModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" action="?category=<?php echo $selectedCategory; ?>" id="productForm">
                    <input type="hidden" name="product_id" id="product_id">
                    <input type="hidden" name="category_id" id="product_category_id" value="<?php echo $selectedCategory; ?>">
                    
                    <div class="form-group">
                        <label for="name">Ürün Adı *</label>
                        <input type="text" name="name" id="name" class="form-control" required>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="slug">Slug (URL) *</label>
                            <input type="text" name="slug" id="slug" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="price">Fiyat (₺)</label>
                            <input type="number" name="price" id="price" class="form-control" step="0.01" min="0">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="short_description">Kısa Açıklama</label>
                        <input type="text" name="short_description" id="short_description" class="form-control" maxlength="500">
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Detaylı Açıklama</label>
                        <textarea name="description" id="description" class="form-control" rows="3"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="image_path">Ürün Görseli</label>
                        
                        <div style="margin-bottom: 15px;">
                            <input type="file" 
                                   id="imageFile" 
                                   accept="image/*"
                                   style="display: none;"
                                   onchange="uploadImage(this)">
                            <button type="button" 
                                    class="btn btn-secondary" 
                                    onclick="document.getElementById('imageFile').click()">
                                <i class="fas fa-upload"></i> Resim Yükle
                            </button>
                            <span id="uploadStatus" style="margin-left: 10px; font-size: 13px;"></span>
                        </div>
                        
                        <div id="imagePreview" style="display: none; margin-bottom: 15px;">
                            <img id="previewImg" 
                                 src="" 
                                 alt="Önizleme" 
                                 style="max-width: 100%; max-height: 200px; border-radius: 8px; border: 2px solid #e0e0e0;">
                        </div>
                        
                        <details style="margin-top: 10px;">
                            <summary style="cursor: pointer; color: #666; font-size: 13px;">
                                <i class="fas fa-chevron-down"></i> Veya manuel yol girin
                            </summary>
                            <div style="margin-top: 10px;">
                                <input type="text" 
                                       name="image_path" 
                                       id="image_path" 
                                       class="form-control" 
                                       placeholder="images/GÖRSELLER/...">
                            </div>
                        </details>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="display_order">Sıra Numarası</label>
                            <input type="number" name="display_order" id="display_order" class="form-control" value="0">
                        </div>
                        
                        <div class="form-group">
                            <label>Seçenekler</label>
                            <label class="checkbox-label">
                                <input type="checkbox" name="is_featured" id="is_featured" value="1">
                                <span>Öne Çıkan Ürün</span>
                            </label>
                        </div>
                    </div>
                    
                    <div class="form-group" id="productActiveCheckboxGroup" style="display:none;">
                        <label class="checkbox-label">
                            <input type="checkbox" name="is_active" id="is_active" value="1" checked>
                            <span>Aktif</span>
                        </label>
                    </div>
                    
                    <div style="display: flex; gap: 10px;">
                        <button type="submit" name="add_product" id="productSubmitBtn" class="btn btn-primary">
                            <i class="fas fa-save"></i> Kaydet
                        </button>
                        <button type="button" class="btn btn-secondary" onclick="closeProductModal()">
                            <i class="fas fa-times"></i> İptal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="assets/js/admin.js"></script>
    <script>
        // Kategori Modal Fonksiyonları
        function openAddCategoryModal() {
            document.getElementById('categoryModalTitle').innerHTML = '<i class="fas fa-th-large"></i> Yeni Kategori';
            document.getElementById('categoryForm').reset();
            document.getElementById('category_id').value = '';
            document.getElementById('categorySubmitBtn').name = 'add_category';
            document.getElementById('catActiveCheckboxGroup').style.display = 'none';
            document.getElementById('deleteCategoryBtn').style.display = 'none';
            document.getElementById('categoryModal').classList.add('show');
        }
        
        function editCategory(category) {
            document.getElementById('categoryModalTitle').innerHTML = '<i class="fas fa-edit"></i> Kategori Düzenle';
            document.getElementById('category_id').value = category.id;
            document.getElementById('cat_name').value = category.name;
            document.getElementById('cat_slug').value = category.slug;
            document.getElementById('cat_folder_name').value = category.folder_name;
            document.getElementById('cat_icon').value = category.icon;
            document.getElementById('cat_description').value = category.description;
            document.getElementById('cat_display_order').value = category.display_order;
            document.getElementById('cat_is_active').checked = category.is_active == 1;
            document.getElementById('categorySubmitBtn').name = 'update_category';
            document.getElementById('catActiveCheckboxGroup').style.display = 'block';
            
            const deleteBtn = document.getElementById('deleteCategoryBtn');
            deleteBtn.style.display = 'inline-flex';
            deleteBtn.href = '?category=<?php echo $selectedCategory; ?>&delete_category=1&id=' + category.id;
            
            document.getElementById('categoryModal').classList.add('show');
        }
        
        function closeCategoryModal() {
            document.getElementById('categoryModal').classList.remove('show');
        }
        
        // Ürün Modal Fonksiyonları
        function openAddProductModal(categoryId) {
            document.getElementById('productModalTitle').innerHTML = '<i class="fas fa-box"></i> Yeni Ürün';
            document.getElementById('productForm').reset();
            document.getElementById('product_id').value = '';
            document.getElementById('product_category_id').value = categoryId;
            document.getElementById('productSubmitBtn').name = 'add_product';
            document.getElementById('productActiveCheckboxGroup').style.display = 'none';
            document.getElementById('imagePreview').style.display = 'none';
            document.getElementById('productModal').classList.add('show');
        }
        
        function editProduct(product) {
            document.getElementById('productModalTitle').innerHTML = '<i class="fas fa-edit"></i> Ürün Düzenle';
            document.getElementById('product_id').value = product.id;
            document.getElementById('product_category_id').value = product.category_id;
            document.getElementById('name').value = product.name;
            document.getElementById('slug').value = product.slug;
            document.getElementById('short_description').value = product.short_description || '';
            document.getElementById('description').value = product.description || '';
            document.getElementById('price').value = product.price || '';
            document.getElementById('image_path').value = product.image_path;
            document.getElementById('display_order').value = product.display_order;
            document.getElementById('is_featured').checked = product.is_featured == 1;
            document.getElementById('is_active').checked = product.is_active == 1;
            document.getElementById('productSubmitBtn').name = 'update_product';
            document.getElementById('productActiveCheckboxGroup').style.display = 'block';
            
            if (product.image_path) {
                const preview = document.getElementById('imagePreview');
                const previewImg = document.getElementById('previewImg');
                previewImg.src = '../' + product.image_path;
                preview.style.display = 'block';
            }
            
            document.getElementById('productModal').classList.add('show');
        }
        
        function closeProductModal() {
            document.getElementById('productModal').classList.remove('show');
        }
        
        // Resim yükleme
        async function uploadImage(input) {
            const file = input.files[0];
            if (!file) return;
            
            if (!file.type.startsWith('image/')) {
                alert('Lütfen bir resim dosyası seçin!');
                return;
            }
            
            if (file.size > 5 * 1024 * 1024) {
                alert('Dosya boyutu 5MB\'dan küçük olmalıdır!');
                return;
            }
            
            const uploadStatus = document.getElementById('uploadStatus');
            uploadStatus.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Yükleniyor...';
            uploadStatus.style.color = '#17a2b8';
            
            const formData = new FormData();
            formData.append('image', file);
            
            try {
                const response = await fetch('upload-image.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    uploadStatus.innerHTML = '<i class="fas fa-check-circle"></i> Yüklendi!';
                    uploadStatus.style.color = '#28a745';
                    
                    document.getElementById('image_path').value = result.imagePath;
                    
                    const preview = document.getElementById('imagePreview');
                    const previewImg = document.getElementById('previewImg');
                    previewImg.src = '../' + result.imagePath;
                    preview.style.display = 'block';
                    
                    setTimeout(() => {
                        uploadStatus.innerHTML = '';
                    }, 2000);
                } else {
                    uploadStatus.innerHTML = '<i class="fas fa-exclamation-circle"></i> ' + result.message;
                    uploadStatus.style.color = '#dc3545';
                }
            } catch (error) {
                console.error('Upload hatası:', error);
                uploadStatus.innerHTML = '<i class="fas fa-exclamation-circle"></i> Yükleme başarısız!';
                uploadStatus.style.color = '#dc3545';
            }
        }
        
        // Slug otomatik oluştur
        document.getElementById('name').addEventListener('input', function() {
            if (!document.getElementById('product_id').value) {
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
        
        document.getElementById('cat_name').addEventListener('input', function() {
            if (!document.getElementById('category_id').value) {
                const slug = this.value.toLowerCase()
                    .replace(/ğ/g, 'g')
                    .replace(/ü/g, 'u')
                    .replace(/ş/g, 's')
                    .replace(/ı/g, 'i')
                    .replace(/ö/g, 'o')
                    .replace(/ç/g, 'c')
                    .replace(/[^a-z0-9]+/g, '-')
                    .replace(/^-+|-+$/g, '');
                document.getElementById('cat_slug').value = slug;
            }
        });
        
        // Modal dışına tıklandığında kapat
        document.getElementById('categoryModal').addEventListener('click', function(e) {
            if (e.target === this) closeCategoryModal();
        });
        
        document.getElementById('productModal').addEventListener('click', function(e) {
            if (e.target === this) closeProductModal();
        });
    </script>
</body>
</html>
