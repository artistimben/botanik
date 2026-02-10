<?php
/**
 * Admin Panel - Mevcut Görselleri Ürün Olarak İçe Aktar
 */
require_once 'includes/auth.php';
require_once 'includes/functions.php';

checkAdminLogin();
checkAdminRole(['admin']);

$adminInfo = getAdminInfo();
$stats = getAdminStats();

$imported = 0;
$skipped = 0;
$errors = [];
$importedProducts = [];

// İçe aktarma işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['import_products'])) {
    // Kategorileri getir
    $categoriesResult = $conn->query("SELECT * FROM categories WHERE is_active = 1");
    $categories = [];
    while ($row = $categoriesResult->fetch_assoc()) {
        $categories[$row['folder_name']] = $row['id'];
    }
    
    // Görseller klasörünü tara
    $baseDir = '../images/GÖRSELLER/';
    
    foreach ($categories as $folderName => $categoryId) {
        $folderPath = $baseDir . $folderName;
        
        if (!is_dir($folderPath)) {
            continue;
        }
        
        $files = scandir($folderPath);
        
        foreach ($files as $file) {
            if ($file == '.' || $file == '..' || is_dir($folderPath . '/' . $file)) {
                continue;
            }
            
            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                continue;
            }
            
            $imagePath = 'images/GÖRSELLER/' . $folderName . '/' . $file;
            
            // Bu görsel zaten ürün olarak eklenmiş mi?
            $checkStmt = $conn->prepare("SELECT id FROM products WHERE image_path = ?");
            $checkStmt->bind_param("s", $imagePath);
            $checkStmt->execute();
            $result = $checkStmt->get_result();
            
            if ($result->num_rows > 0) {
                $skipped++;
                continue;
            }
            
            // Ürün adını dosya adından oluştur
            $productName = pathinfo($file, PATHINFO_FILENAME);
            $productName = str_replace(['-', '_'], ' ', $productName);
            $productName = ucwords($productName);
            
            // Slug oluştur
            $slug = strtolower($productName);
            $slug = str_replace(['ğ', 'ü', 'ş', 'ı', 'ö', 'ç'], ['g', 'u', 's', 'i', 'o', 'c'], $slug);
            $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
            $slug = trim($slug, '-');
            
            // Slug benzersizliğini kontrol et
            $originalSlug = $slug;
            $counter = 1;
            while (true) {
                $checkSlugStmt = $conn->prepare("SELECT id FROM products WHERE slug = ?");
                $checkSlugStmt->bind_param("s", $slug);
                $checkSlugStmt->execute();
                $slugResult = $checkSlugStmt->get_result();
                
                if ($slugResult->num_rows == 0) {
                    break;
                }
                
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }
            
            // Kategoriye göre açıklama
            $categoryName = array_search($categoryId, $categories);
            $shortDescription = $categoryName . ' kategorisinden ' . $productName;
            
            // Ürünü ekle
            $stmt = $conn->prepare("INSERT INTO products (category_id, name, slug, short_description, image_path, is_active, display_order) VALUES (?, ?, ?, ?, ?, 1, ?)");
            $displayOrder = $imported;
            $stmt->bind_param("issssi", $categoryId, $productName, $slug, $shortDescription, $imagePath, $displayOrder);
            
            if ($stmt->execute()) {
                $imported++;
                $importedProducts[] = [
                    'name' => $productName,
                    'category' => $categoryName,
                    'image' => $imagePath
                ];
            } else {
                $errors[] = "Hata: $file - " . $stmt->error;
            }
        }
    }
}

$pageTitle = 'Ürünleri İçe Aktar';
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
        .import-preview {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }
        
        .preview-card {
            background: var(--white);
            border-radius: 10px;
            padding: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .preview-card img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 5px;
        }
        
        .preview-card h4 {
            margin: 10px 0 5px 0;
            font-size: 14px;
        }
        
        .preview-card p {
            margin: 0;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <?php include 'includes/sidebar.php'; ?>

    <div class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-file-import"></i> Ürünleri İçe Aktar</h1>
            <p>Mevcut görselleri otomatik olarak ürün olarak ekleyin</p>
        </div>

        <div class="content-section">
            <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['import_products'])): ?>
                <?php if ($imported > 0): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        <strong>Başarılı!</strong> <?php echo $imported; ?> ürün içe aktarıldı. <?php echo $skipped; ?> ürün zaten mevcut (atlandı).
                    </div>
                    
                    <?php if (!empty($importedProducts)): ?>
                        <h3>İçe Aktarılan Ürünler:</h3>
                        <div class="import-preview">
                            <?php foreach (array_slice($importedProducts, 0, 20) as $product): ?>
                                <div class="preview-card">
                                    <img src="../<?php echo $product['image']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                                    <h4><?php echo htmlspecialchars($product['name']); ?></h4>
                                    <p><?php echo htmlspecialchars($product['category']); ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <?php if (count($importedProducts) > 20): ?>
                            <p style="margin-top: 20px; text-align: center; color: #666;">
                                ... ve <?php echo count($importedProducts) - 20; ?> ürün daha
                            </p>
                        <?php endif; ?>
                    <?php endif; ?>
                    
                    <div style="margin-top: 30px; text-align: center;">
                        <a href="products.php" class="btn btn-primary">
                            <i class="fas fa-box"></i> Ürünleri Görüntüle
                        </a>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning">
                        <i class="fas fa-info-circle"></i>
                        <strong>Bilgi:</strong> İçe aktarılacak yeni ürün bulunamadı. Tüm görseller zaten ürün olarak eklenmiş.
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i>
                        <strong>Hatalar:</strong>
                        <ul style="margin: 10px 0 0 20px;">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    <strong>Toplu İçe Aktarma</strong><br>
                    Bu işlem <strong>images/GÖRSELLER/</strong> klasöründeki tüm görselleri otomatik olarak ürün olarak ekleyecek.
                </div>
                
                <div style="background: var(--light-color); padding: 30px; border-radius: 10px; text-align: center;">
                    <i class="fas fa-file-import" style="font-size: 64px; color: var(--primary-color); margin-bottom: 20px;"></i>
                    <h2 style="margin-bottom: 15px;">Ürünleri İçe Aktar</h2>
                    <p style="color: #666; margin-bottom: 30px;">
                        Tüm kategorilerdeki görseller otomatik olarak ürün olarak eklenecek.<br>
                        Zaten ürün olarak eklenmiş görseller atlanacak.
                    </p>
                    
                    <form method="POST" action="" onsubmit="return confirm('Tüm görselleri içe aktarmak istediğinizden emin misiniz?');">
                        <button type="submit" name="import_products" class="btn btn-primary btn-lg">
                            <i class="fas fa-download"></i> İçe Aktarmayı Başlat
                        </button>
                    </form>
                    
                    <p style="margin-top: 20px; font-size: 13px; color: #999;">
                        <i class="fas fa-info-circle"></i> İşlem birkaç dakika sürebilir
                    </p>
                </div>
                
                <div style="margin-top: 30px;">
                    <h3><i class="fas fa-question-circle"></i> Nasıl Çalışır?</h3>
                    <ul style="line-height: 2;">
                        <li>✅ Her kategorideki görseller taranır</li>
                        <li>✅ Dosya adından ürün adı oluşturulur</li>
                        <li>✅ Otomatik slug (URL) oluşturulur</li>
                        <li>✅ Kategori bilgisi otomatik atanır</li>
                        <li>✅ Zaten eklenmiş ürünler atlanır</li>
                        <li>✅ Tüm ürünler aktif durumda eklenir</li>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="assets/js/admin.js"></script>
</body>
</html>

