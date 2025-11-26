<?php
/**
 * Papatya Botanik - Yapılandırma Dosyası
 * Site genelinde kullanılacak sabitler ve ayarlar
 */

// Site Bilgileri
// return $imagePath = "images/GÖRSELLER/";
define('SITE_NAME', 'Papatya Botanik');
define('SITE_TITLE', 'Papatya Botanik - Doğal Çiçek ve Bitki Dünyası');
define('SITE_DESCRIPTION', 'Papatya Botanik ile özel günlerinizi çiçeklerle süsleyin. Buket, arajman, isteme çiçekleri ve daha fazlası.');

// İletişim Bilgileri
define('PHONE_NUMBER', '0555 123 45 67'); // Telefon numaranızı buraya yazın
define('WHATSAPP_NUMBER', '905551234567'); // WhatsApp için 90 ile başlayan format
define('EMAIL', 'info@papatyabotanik.com');
define('ADDRESS', 'Örnek Mahallesi, Çiçek Sokak No:1, İlçe/Şehir');

// Sosyal Medya (opsiyonel)
define('INSTAGRAM', 'papatyabotanik');
define('FACEBOOK', 'papatyabotanik');

// Çalışma Saatleri
define('WORKING_HOURS', 'Pazartesi - Cumartesi: 09:00 - 19:00<br>Pazar: 10:00 - 17:00');

// Kategoriler - Görseller klasöründeki kategorilere göre
$categories = [
    'buketler' => [
        'name' => 'Buketler',
        'folder' => 'BUKETLER',
        'icon' => '💐',
        'description' => 'Sevdikleriniz için özel buketler'
    ],
    'arajmanlar' => [
        'name' => 'Kokina Arajmanlar',
        'folder' => 'KOKİNA ARAJMANLAR',
        'icon' => '🌸',
        'description' => 'Kokina çiçeklerle özel arajmanlar'
    ],
    'isteme-cicekleri' => [
        'name' => 'İsteme Çiçekleri',
        'folder' => 'İSTEME ÇİÇEKLERİ',
        'icon' => '💍',
        'description' => 'Hayatınızın en özel anı için çiçekler'
    ],
    'arac-susleme' => [
        'name' => 'Araç Süsleme',
        'folder' => 'ARAÇ SÜSLEME',
        'icon' => '🚗',
        'description' => 'Düğün arabalarınızı çiçeklerle süsleyin'
    ],
    'celenkler' => [
        'name' => 'Çelenkler',
        'folder' => 'ÇELENKLER',
        'icon' => '🌹',
        'description' => 'Anlamlı günler için çelenkler'
    ],
    'hediyelik' => [
        'name' => 'Hediyelik Ürünler',
        'folder' => 'hediyelik',
        'icon' => '🎁',
        'description' => 'Özel hediyeler ve sürprizler'
    ],
    'lale' => [
        'name' => 'Laleler',
        'folder' => 'lale',
        'icon' => '🌷',
        'description' => 'Baharın habercisi laleler'
    ],
    'peyzaj' => [
        'name' => 'Peyzaj',
        'folder' => 'peyzaj',
        'icon' => '🌿',
        'description' => 'Bahçe ve peyzaj düzenlemeleri'
    ]
];

// Renk Paleti - Botanik/Doğal Temalı
$color_palette = [
    'primary' => '#2d5016',      // Koyu yeşil (doğal, botanik)
    'secondary' => '#6b8e23',    // Zeytin yeşili
    'accent' => '#f4a460',       // Sandy brown (toprak rengi)
    'light' => '#f8f9f5',        // Açık krem
    'white' => '#ffffff',
    'dark' => '#1a1a1a',
    'success' => '#4caf50',
    'info' => '#00bcd4'
];

// Başlatma fonksiyonu
function getImagePath($category, $filename) {
    return "images/GÖRSELLER/{$category}/{$filename}";
}

// Tüm resimleri getir - ZORUNLU CACHE (Her zaman önbellek kullan!)
function getImagesFromCategory($categoryFolder, $limit = null) {
    // Session'ı MUTLAKA başlat
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    $cacheKey = 'images_' . md5($categoryFolder);
    $cacheTime = 3600; // 1 SAAT cache süresi (ULTRA HIZLI!)
    $forceBuild = isset($_GET['rebuild_cache']); // Zorla yeniden oluştur parametresi
    
    // Cache'de var mı ve süresi dolmamış mı kontrol et
    if (!$forceBuild && 
        isset($_SESSION[$cacheKey]) && 
        isset($_SESSION[$cacheKey . '_time']) && 
        (time() - $_SESSION[$cacheKey . '_time']) < $cacheTime &&
        is_array($_SESSION[$cacheKey]) &&
        !empty($_SESSION[$cacheKey])) {
        
        // CACHE'DEN SERVIS ET (ÇOK HIZLI!)
        $images = $_SESSION[$cacheKey];
        
        // Limit varsa uygula
        if ($limit && count($images) > $limit) {
            return array_slice($images, 0, $limit);
        }
        return $images;
    }
    
    // Cache yok veya süresi dolmuş - YENİDEN OLUŞTUR
    $imagePath = "images/GÖRSELLER/{$categoryFolder}";
    $images = [];
    
    if (is_dir($imagePath)) {
        $files = @scandir($imagePath);
        if ($files !== false) {
            foreach ($files as $file) {
                if ($file != '.' && $file != '..' && !is_dir($imagePath . '/' . $file)) {
                    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                    if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                        $fullPath = $imagePath . '/' . $file;
                        // Dosya gerçekten var mı kontrol et
                        if (file_exists($fullPath)) {
                            $images[] = $fullPath;
                        }
                    }
                }
            }
        }
    }
    
    // ZORUNLU: Cache'e kaydet (boş bile olsa)
    $_SESSION[$cacheKey] = $images;
    $_SESSION[$cacheKey . '_time'] = time();
    $_SESSION[$cacheKey . '_count'] = count($images); // İstatistik için
    
    // Limit varsa uygula
    if ($limit && count($images) > $limit) {
        return array_slice($images, 0, $limit);
    }
    
    return $images;
}

// Video dosyalarını getir - ZORUNLU CACHE
function getVideosFromCategory($categoryFolder) {
    // Session'ı MUTLAKA başlat
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    $cacheKey = 'videos_' . md5($categoryFolder);
    $cacheTime = 3600; // 1 saat
    $forceBuild = isset($_GET['rebuild_cache']);
    
    // Cache kontrolü - ZORUNLU
    if (!$forceBuild &&
        isset($_SESSION[$cacheKey]) && 
        isset($_SESSION[$cacheKey . '_time']) && 
        (time() - $_SESSION[$cacheKey . '_time']) < $cacheTime &&
        is_array($_SESSION[$cacheKey])) {
        
        // CACHE'DEN SERVIS ET
        return $_SESSION[$cacheKey];
    }
    
    // Cache yok veya süresi dolmuş - YENİDEN OLUŞTUR
    $videoPath = "images/GÖRSELLER/{$categoryFolder}";
    $videos = [];
    
    if (is_dir($videoPath)) {
        $files = @scandir($videoPath);
        if ($files !== false) {
            foreach ($files as $file) {
                if ($file != '.' && $file != '..' && !is_dir($videoPath . '/' . $file)) {
                    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                    if (in_array($ext, ['mp4', 'webm', 'ogg'])) {
                        $fullPath = $videoPath . '/' . $file;
                        if (file_exists($fullPath)) {
                            $videos[] = $fullPath;
                        }
                    }
                }
            }
        }
    }
    
    // ZORUNLU: Cache'e kaydet
    $_SESSION[$cacheKey] = $videos;
    $_SESSION[$cacheKey . '_time'] = time();
    $_SESSION[$cacheKey . '_count'] = count($videos);
    
    return $videos;
}

// Cache'i temizle (yeni resim eklendiğinde çağırın)
function clearImageCache($showMessage = false) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    $cleared = 0;
    foreach ($_SESSION as $key => $value) {
        if (strpos($key, 'images_') === 0 || strpos($key, 'videos_') === 0) {
            unset($_SESSION[$key]);
            if (isset($_SESSION[$key . '_time'])) {
                unset($_SESSION[$key . '_time']);
            }
            if (isset($_SESSION[$key . '_count'])) {
                unset($_SESSION[$key . '_count']);
            }
            $cleared++;
        }
    }
    
    if ($showMessage) {
        echo "✅ {$cleared} cache kaydı temizlendi!";
    }
    
    return $cleared;
}

// Cache istatistiklerini göster
function getCacheStats() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    $stats = [
        'total_caches' => 0,
        'total_images' => 0,
        'oldest_cache' => null,
        'newest_cache' => null
    ];
    
    foreach ($_SESSION as $key => $value) {
        if (strpos($key, 'images_') === 0 && !strpos($key, '_time') && !strpos($key, '_count')) {
            $stats['total_caches']++;
            if (isset($_SESSION[$key . '_count'])) {
                $stats['total_images'] += $_SESSION[$key . '_count'];
            }
            
            if (isset($_SESSION[$key . '_time'])) {
                $time = $_SESSION[$key . '_time'];
                if ($stats['oldest_cache'] === null || $time < $stats['oldest_cache']) {
                    $stats['oldest_cache'] = $time;
                }
                if ($stats['newest_cache'] === null || $time > $stats['newest_cache']) {
                    $stats['newest_cache'] = $time;
                }
            }
        }
    }
    
    return $stats;
}
?>

