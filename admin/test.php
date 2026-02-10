<?php
/**
 * Admin Panel Test & Debug Sayfası
 */

// Hata raporlamayı aç
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Admin Panel Test</h1>";
echo "<hr>";

// 1. Session testi
echo "<h2>1. Session Testi</h2>";
session_start();
if (session_status() === PHP_SESSION_ACTIVE) {
    echo "✅ Session başlatıldı<br>";
    echo "Session ID: " . session_id() . "<br>";
} else {
    echo "❌ Session başlatılamadı<br>";
}

// 2. Dosya yolu testi
echo "<h2>2. Dosya Yolu Testi</h2>";
$files = [
    'auth.php' => 'includes/auth.php',
    'functions.php' => 'includes/functions.php',
    'db.php' => '../includes/db.php',
    'config.php' => '../includes/config.php'
];

foreach ($files as $name => $path) {
    if (file_exists($path)) {
        echo "✅ $name bulundu ($path)<br>";
    } else {
        echo "❌ $name bulunamadı ($path)<br>";
    }
}

// 3. Database bağlantı testi
echo "<h2>3. Veritabanı Bağlantı Testi</h2>";
try {
    require_once '../includes/db.php';
    if (isset($conn) && $conn->ping()) {
        echo "✅ Veritabanı bağlantısı başarılı<br>";
        
        // Admin kullanıcı kontrolü
        $result = $conn->query("SELECT COUNT(*) as count FROM admin_users");
        if ($result) {
            $row = $result->fetch_assoc();
            echo "✅ admin_users tablosu mevcut (Kayıt sayısı: {$row['count']})<br>";
        } else {
            echo "❌ admin_users tablosu bulunamadı<br>";
        }
    } else {
        echo "❌ Veritabanı bağlantısı başarısız<br>";
    }
} catch (Exception $e) {
    echo "❌ Veritabanı hatası: " . $e->getMessage() . "<br>";
}

// 4. Auth fonksiyonları testi
echo "<h2>4. Auth Fonksiyonları Testi</h2>";
try {
    require_once 'includes/auth.php';
    echo "✅ auth.php yüklendi<br>";
    
    if (function_exists('checkAdminLogin')) {
        echo "✅ checkAdminLogin() fonksiyonu mevcut<br>";
    }
    
    if (function_exists('getAdminInfo')) {
        echo "✅ getAdminInfo() fonksiyonu mevcut<br>";
    }
} catch (Exception $e) {
    echo "❌ Auth hatası: " . $e->getMessage() . "<br>";
}

// 5. Functions testi
echo "<h2>5. Helper Fonksiyonları Testi</h2>";
try {
    require_once 'includes/functions.php';
    echo "✅ functions.php yüklendi<br>";
    
    if (function_exists('getAdminStats')) {
        echo "✅ getAdminStats() fonksiyonu mevcut<br>";
        
        // İstatistikleri çek
        $stats = getAdminStats();
        echo "✅ İstatistikler alındı:<br>";
        echo "<pre>";
        print_r($stats);
        echo "</pre>";
    }
} catch (Exception $e) {
    echo "❌ Functions hatası: " . $e->getMessage() . "<br>";
}

// 6. Login testi
echo "<h2>6. Login Sayfası Testi</h2>";
echo "<a href='login.php' style='padding: 10px 20px; background: #2d5016; color: white; text-decoration: none; border-radius: 5px;'>Login Sayfasına Git</a><br><br>";

// 7. Index testi (giriş yaptıktan sonra)
echo "<h2>7. Dashboard Testi</h2>";
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    echo "✅ Giriş yapılmış<br>";
    echo "<a href='index.php' style='padding: 10px 20px; background: #2d5016; color: white; text-decoration: none; border-radius: 5px;'>Dashboard'a Git</a><br>";
} else {
    echo "⚠️ Giriş yapılmamış. Önce login.php'den giriş yapın.<br>";
}

echo "<hr>";
echo "<p><strong>Not:</strong> Tüm testler ✅ işaretli ise sistem çalışıyor demektir.</p>";
?>

