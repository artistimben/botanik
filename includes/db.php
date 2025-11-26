<?php
/**
 * Papatya Botanik - Veritabanı Bağlantı Dosyası
 * MySQL veritabanına bağlanmak için kullanılır
 */

// ============================================
// VERİTABANI AYARLARI
// ============================================
define('DB_HOST', 'localhost');           // Veritabanı sunucusu (genelde localhost)
define('DB_NAME', 'acbozcom_papatya_botanik');     // Veritabanı adı
define('DB_USER', 'acbozcom');                // Veritabanı kullanıcı adı (XAMPP'de genelde root)
define('DB_PASS', '?eR4qkr!!cLG');                    // Veritabanı şifresi (XAMPP'de genelde boş)
define('DB_CHARSET', 'utf8mb4');          // Karakter seti (Türkçe karakter için)

// ============================================
// VERİTABANI BAĞLANTISI (PDO)
// ============================================
try {
    // PDO ile veritabanı bağlantısı
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET,
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,          // Hata yönetimi
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,     // Varsayılan fetch modu
            PDO::ATTR_EMULATE_PREPARES => false,                  // Gerçek prepared statements
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"   // Türkçe karakter desteği
        ]
    );
    
    // Bağlantı başarılı
    // echo "Veritabanı bağlantısı başarılı!"; // Test için açabilirsiniz
    
} catch (PDOException $e) {
    // Bağlantı hatası
    die("Veritabanı bağlantı hatası: " . $e->getMessage());
}

// ============================================
// VERİTABANI BAĞLANTISI (MySQLi - Alternatif)
// ============================================
// PDO yerine MySQLi kullanmak isterseniz:
/*
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Bağlantı kontrolü
if ($mysqli->connect_error) {
    die("Veritabanı bağlantı hatası: " . $mysqli->connect_error);
}

// Türkçe karakter desteği
$mysqli->set_charset(DB_CHARSET);
*/

// ============================================
// VERİTABANI YARDIMCI FONKSİYONLAR
// ============================================

/**
 * Veritabanından ayar değeri getir
 * Kullanım: getSettingValue('site_name')
 */
function getSettingValue($key, $default = null) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT setting_value FROM site_settings WHERE setting_key = ?");
        $stmt->execute([$key]);
        $result = $stmt->fetch();
        
        return $result ? $result['setting_value'] : $default;
    } catch (PDOException $e) {
        return $default;
    }
}

/**
 * Tüm ayarları getir
 * Kullanım: $settings = getAllSettings();
 */
function getAllSettings() {
    global $pdo;
    
    try {
        $stmt = $pdo->query("SELECT setting_key, setting_value FROM site_settings");
        $settings = [];
        
        while ($row = $stmt->fetch()) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
        
        return $settings;
    } catch (PDOException $e) {
        return [];
    }
}

/**
 * Aktif kategorileri getir
 * Kullanım: $categories = getActiveCategories();
 */
function getActiveCategories() {
    global $pdo;
    
    try {
        $stmt = $pdo->query("
            SELECT * FROM categories 
            WHERE is_active = 1 
            ORDER BY display_order ASC, name ASC
        ");
        
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

/**
 * Kategoriye göre ürünleri getir
 * Kullanım: $products = getProductsByCategory(1);
 */
function getProductsByCategory($category_id, $limit = null) {
    global $pdo;
    
    try {
        $sql = "
            SELECT p.*, c.name as category_name, c.slug as category_slug
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.is_active = 1 AND p.category_id = ?
            ORDER BY p.display_order ASC, p.created_at DESC
        ";
        
        if ($limit) {
            $sql .= " LIMIT " . intval($limit);
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$category_id]);
        
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

/**
 * Tüm aktif ürünleri getir
 * Kullanım: $products = getAllActiveProducts();
 */
function getAllActiveProducts() {
    global $pdo;
    
    try {
        $stmt = $pdo->query("
            SELECT p.*, c.name as category_name, c.slug as category_slug
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.is_active = 1
            ORDER BY c.display_order ASC, p.display_order ASC
        ");
        
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

/**
 * Öne çıkan ürünleri getir
 * Kullanım: $featured = getFeaturedProducts(6);
 */
function getFeaturedProducts($limit = 6) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            SELECT p.*, c.name as category_name, c.slug as category_slug
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.is_active = 1 AND p.is_featured = 1
            ORDER BY p.display_order ASC, p.created_at DESC
            LIMIT ?
        ");
        
        $stmt->execute([intval($limit)]);
        
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

/**
 * İletişim mesajı kaydet
 * Kullanım: saveContactMessage($name, $phone, $message);
 */
function saveContactMessage($name, $phone, $message, $email = null) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO contact_messages 
            (full_name, phone, email, message, ip_address, user_agent) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        
        return $stmt->execute([$name, $phone, $email, $message, $ip, $userAgent]);
    } catch (PDOException $e) {
        return false;
    }
}

/**
 * Ürün görüntülenme sayısını artır
 * Kullanım: incrementProductView($product_id);
 */
function incrementProductView($product_id) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            UPDATE products 
            SET view_count = view_count + 1 
            WHERE id = ?
        ");
        
        return $stmt->execute([$product_id]);
    } catch (PDOException $e) {
        return false;
    }
}

/**
 * İstatistik kaydet
 * Kullanım: recordStatistic('page_views');
 */
function recordStatistic($stat_type) {
    global $pdo;
    
    try {
        $today = date('Y-m-d');
        
        // Bugünün kaydı var mı kontrol et
        $stmt = $pdo->prepare("SELECT id FROM site_statistics WHERE stat_date = ?");
        $stmt->execute([$today]);
        
        if ($stmt->fetch()) {
            // Güncelle
            $stmt = $pdo->prepare("
                UPDATE site_statistics 
                SET {$stat_type} = {$stat_type} + 1 
                WHERE stat_date = ?
            ");
            return $stmt->execute([$today]);
        } else {
            // Yeni kayıt oluştur
            $stmt = $pdo->prepare("
                INSERT INTO site_statistics (stat_date, {$stat_type}) 
                VALUES (?, 1)
            ");
            return $stmt->execute([$today]);
        }
    } catch (PDOException $e) {
        return false;
    }
}

/**
 * Aktif slider'ları getir
 * Kullanım: $sliders = getActiveSliders();
 */
function getActiveSliders() {
    global $pdo;
    
    try {
        $stmt = $pdo->query("
            SELECT * FROM sliders 
            WHERE is_active = 1 
            ORDER BY display_order ASC
        ");
        
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

// ============================================
// GÜVENLİK FONKSİYONLARI
// ============================================

/**
 * HTML karakterlerini temizle (XSS koruması)
 * Kullanım: echo clean($user_input);
 */
function clean($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * SQL injection'a karşı string temizle
 * Kullanım: $safe_string = escapeString($user_input);
 */
function escapeString($string) {
    global $pdo;
    return $pdo->quote($string);
}

/**
 * E-posta adresi doğrula
 * Kullanım: if (validateEmail($email)) { ... }
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Telefon numarası temizle
 * Kullanım: $clean_phone = cleanPhone('0555 123 45 67');
 */
function cleanPhone($phone) {
    return preg_replace('/[^0-9+]/', '', $phone);
}

// ============================================
// KULLANIM ÖRNEKLERİ
// ============================================

/*
// 1. Ayar değeri alma
$site_name = getSettingValue('site_name', 'Papatya Botanik');

// 2. Kategorileri listeleme
$categories = getActiveCategories();
foreach ($categories as $cat) {
    echo $cat['name'] . '<br>';
}

// 3. Ürünleri listeleme
$products = getProductsByCategory(1, 10); // 1 numaralı kategoriden 10 ürün
foreach ($products as $product) {
    echo $product['name'] . ' - ' . $product['price'] . '<br>';
}

// 4. İletişim mesajı kaydetme
if ($_POST) {
    $name = clean($_POST['name']);
    $phone = cleanPhone($_POST['phone']);
    $message = clean($_POST['message']);
    
    if (saveContactMessage($name, $phone, $message)) {
        echo "Mesajınız kaydedildi!";
    }
}

// 5. İstatistik kaydetme
recordStatistic('page_views'); // Sayfa görüntülenme
recordStatistic('whatsapp_clicks'); // WhatsApp tıklama
*/

?>

