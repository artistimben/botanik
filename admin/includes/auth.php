<?php
/**
 * Admin Panel - Kimlik Doğrulama
 */

// Session başlat
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Giriş kontrolü
function checkAdminLogin() {
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        header('Location: login.php');
        exit;
    }
}

// Admin bilgilerini al
function getAdminInfo($key = null) {
    if ($key === null) {
        return [
            'id' => $_SESSION['admin_id'] ?? null,
            'username' => $_SESSION['admin_username'] ?? null,
            'fullname' => $_SESSION['admin_fullname'] ?? null,
            'role' => $_SESSION['admin_role'] ?? null,
            'email' => $_SESSION['admin_email'] ?? null
        ];
    }
    
    return $_SESSION['admin_' . $key] ?? null;
}

// Rol kontrolü
function checkAdminRole($allowedRoles = ['admin']) {
    if (!is_array($allowedRoles)) {
        $allowedRoles = [$allowedRoles];
    }
    
    $currentRole = getAdminInfo('role');
    
    if (!in_array($currentRole, $allowedRoles)) {
        http_response_code(403);
        die('Bu işlem için yetkiniz yok!');
    }
}

// Çıkış yap
function adminLogout() {
    session_unset();
    session_destroy();
    header('Location: login.php');
    exit;
}

// CSRF Token oluştur
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// CSRF Token doğrula
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Giriş yapılmışsa true döndür
function isAdminLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}
?>

