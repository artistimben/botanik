<?php
/**
 * Admin Panel - Resim Yükleme API
 */
session_start();

// Hata raporlama
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

// Admin kontrolü
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Yetkisiz erişim!']);
    exit;
}

// Sadece POST istekleri
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Geçersiz istek!']);
    exit;
}

// Dosya kontrolü
if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'Dosya yüklenemedi!']);
    exit;
}

$file = $_FILES['image'];
$fileName = $file['name'];
$fileTmpName = $file['tmp_name'];
$fileSize = $file['size'];
$fileError = $file['error'];

// Dosya uzantısını al
$fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

// İzin verilen uzantılar
$allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

if (!in_array($fileExt, $allowedExtensions)) {
    echo json_encode(['success' => false, 'message' => 'Sadece JPG, PNG, GIF ve WEBP dosyaları yüklenebilir!']);
    exit;
}

// Dosya boyutu kontrolü (5MB)
if ($fileSize > 5 * 1024 * 1024) {
    echo json_encode(['success' => false, 'message' => 'Dosya boyutu 5MB\'dan küçük olmalıdır!']);
    exit;
}

// Upload klasörünü oluştur
$uploadDir = '../uploads/products/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Benzersiz dosya adı oluştur
$newFileName = uniqid('product_', true) . '.' . $fileExt;
$targetPath = $uploadDir . $newFileName;

// Dosyayı taşı
if (move_uploaded_file($fileTmpName, $targetPath)) {
    // Görüntü boyutunu kontrol et ve küçült (opsiyonel)
    $imagePath = 'uploads/products/' . $newFileName;
    
    // Thumbnail oluştur (opsiyonel)
    createThumbnail($targetPath, $uploadDir . 'thumb_' . $newFileName, 300, 300);
    
    echo json_encode([
        'success' => true,
        'message' => 'Dosya başarıyla yüklendi!',
        'imagePath' => $imagePath,
        'fileName' => $newFileName,
        'thumbnailPath' => 'uploads/products/thumb_' . $newFileName
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Dosya yüklenirken bir hata oluştu!']);
}

/**
 * Thumbnail oluştur
 */
function createThumbnail($source, $destination, $maxWidth, $maxHeight) {
    $imageInfo = getimagesize($source);
    
    if (!$imageInfo) {
        return false;
    }
    
    list($width, $height, $type) = $imageInfo;
    
    // Kaynak resmi yükle
    switch ($type) {
        case IMAGETYPE_JPEG:
            $srcImage = imagecreatefromjpeg($source);
            break;
        case IMAGETYPE_PNG:
            $srcImage = imagecreatefrompng($source);
            break;
        case IMAGETYPE_GIF:
            $srcImage = imagecreatefromgif($source);
            break;
        case IMAGETYPE_WEBP:
            $srcImage = imagecreatefromwebp($source);
            break;
        default:
            return false;
    }
    
    // Yeni boyutları hesapla
    $ratio = min($maxWidth / $width, $maxHeight / $height);
    $newWidth = intval($width * $ratio);
    $newHeight = intval($height * $ratio);
    
    // Yeni resim oluştur
    $dstImage = imagecreatetruecolor($newWidth, $newHeight);
    
    // PNG ve GIF için şeffaflık
    if ($type == IMAGETYPE_PNG || $type == IMAGETYPE_GIF) {
        imagealphablending($dstImage, false);
        imagesavealpha($dstImage, true);
        $transparent = imagecolorallocatealpha($dstImage, 255, 255, 255, 127);
        imagefilledrectangle($dstImage, 0, 0, $newWidth, $newHeight, $transparent);
    }
    
    // Resmi yeniden boyutlandır
    imagecopyresampled($dstImage, $srcImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
    
    // Kaydet
    switch ($type) {
        case IMAGETYPE_JPEG:
            imagejpeg($dstImage, $destination, 90);
            break;
        case IMAGETYPE_PNG:
            imagepng($dstImage, $destination, 9);
            break;
        case IMAGETYPE_GIF:
            imagegif($dstImage, $destination);
            break;
        case IMAGETYPE_WEBP:
            imagewebp($dstImage, $destination, 90);
            break;
    }
    
    // Belleği temizle
    imagedestroy($srcImage);
    imagedestroy($dstImage);
    
    return true;
}
?>

