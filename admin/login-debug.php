<?php
/**
 * Admin Panel - Giriş Sayfası (DEBUG VERSION)
 */

// Hata raporlamayı aç
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!-- DEBUG: Script başladı -->\n";

session_start();
echo "<!-- DEBUG: Session başlatıldı -->\n";

// Zaten giriş yapılmışsa dashboard'a yönlendir
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    echo "<!-- DEBUG: Zaten giriş yapılmış, yönlendiriliyor -->\n";
    header('Location: index.php');
    exit;
}

echo "<!-- DEBUG: DB dosyası yükleniyor -->\n";
require_once '../includes/db.php';
echo "<!-- DEBUG: DB dosyası yüklendi -->\n";

$error = '';
$success = '';
$debug = [];

// Login işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $debug[] = "POST isteği alındı";
    
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    
    $debug[] = "Username: " . htmlspecialchars($username);
    $debug[] = "Password uzunluğu: " . strlen($password);
    
    if (empty($username) || empty($password)) {
        $error = 'Kullanıcı adı ve şifre gereklidir!';
        $debug[] = "Boş alan hatası";
    } else {
        $debug[] = "Kullanıcı sorgulanıyor...";
        
        // Kullanıcıyı kontrol et
        $stmt = $conn->prepare("SELECT * FROM admin_users WHERE username = ? AND is_active = 1 LIMIT 1");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $debug[] = "Sorgu çalıştırıldı. Bulunan: " . $result->num_rows;
        
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $debug[] = "Kullanıcı bulundu: " . $user['full_name'];
            $debug[] = "Hash: " . substr($user['password'], 0, 20) . "...";
            
            // Şifre kontrolü (password_verify kullanılıyor)
            if (password_verify($password, $user['password'])) {
                $debug[] = "✅ Şifre doğru!";
                
                // Başarılı giriş
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $user['id'];
                $_SESSION['admin_username'] = $user['username'];
                $_SESSION['admin_fullname'] = $user['full_name'];
                $_SESSION['admin_role'] = $user['role'];
                $_SESSION['admin_email'] = $user['email'];
                
                $debug[] = "Session değişkenleri ayarlandı";
                
                // Son giriş zamanını güncelle
                $updateStmt = $conn->prepare("UPDATE admin_users SET last_login = NOW() WHERE id = ?");
                $updateStmt->bind_param("i", $user['id']);
                $updateStmt->execute();
                
                $debug[] = "Son giriş zamanı güncellendi";
                $success = "Giriş başarılı! Yönlendiriliyorsunuz...";
                
                // JavaScript ile yönlendir (header çalışmıyorsa)
                echo "<script>setTimeout(function(){ window.location.href='index.php'; }, 2000);</script>";
                
            } else {
                $error = 'Kullanıcı adı veya şifre hatalı!';
                $debug[] = "❌ Şifre yanlış!";
                
                // Test için password hash'i oluştur
                $testHash = password_hash('password', PASSWORD_DEFAULT);
                $debug[] = "Test hash: " . $testHash;
                $debug[] = "Test verify: " . (password_verify('password', $testHash) ? 'ÇALIŞIYOR' : 'ÇALIŞMIYOR');
            }
        } else {
            $error = 'Kullanıcı adı veya şifre hatalı!';
            $debug[] = "❌ Kullanıcı bulunamadı!";
        }
        
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Girişi (DEBUG) - Papatya Botanik</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #2d5016 0%, #6b8e23 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            max-width: 450px;
            width: 100%;
            animation: slideUp 0.5s ease;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-header {
            background: linear-gradient(135deg, #2d5016 0%, #4a7a1e 100%);
            padding: 40px 30px;
            text-align: center;
            color: white;
        }

        .login-header i {
            font-size: 48px;
            margin-bottom: 15px;
            display: block;
        }

        .login-header h1 {
            font-size: 24px;
            margin-bottom: 5px;
        }

        .login-header p {
            opacity: 0.9;
            font-size: 14px;
        }

        .login-body {
            padding: 40px 30px;
        }

        .alert {
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-error {
            background: #fee;
            border: 1px solid #fcc;
            color: #c33;
        }

        .alert-success {
            background: #efe;
            border: 1px solid #cfc;
            color: #3c3;
        }

        .debug-info {
            background: #f0f0f0;
            border: 1px solid #ccc;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 12px;
            font-family: monospace;
            max-height: 300px;
            overflow-y: auto;
        }

        .debug-info h3 {
            margin-bottom: 10px;
            font-size: 14px;
            color: #333;
        }

        .debug-info ul {
            list-style: none;
            padding: 0;
        }

        .debug-info li {
            padding: 3px 0;
            border-bottom: 1px solid #ddd;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
            font-size: 14px;
        }

        .form-control {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 15px;
            transition: all 0.3s;
            background: #fafafa;
        }

        .form-control:focus {
            outline: none;
            border-color: #6b8e23;
            background: white;
            box-shadow: 0 0 0 3px rgba(107, 142, 35, 0.1);
        }

        .btn {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #2d5016 0%, #4a7a1e 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(45, 80, 22, 0.3);
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <i class="fas fa-leaf"></i>
            <h1>Papatya Botanik</h1>
            <p>Yönetim Paneli Girişi (DEBUG)</p>
        </div>

        <div class="login-body">
            <?php if (!empty($debug)): ?>
                <div class="debug-info">
                    <h3>🔍 Debug Bilgileri:</h3>
                    <ul>
                        <?php foreach ($debug as $info): ?>
                            <li><?php echo htmlspecialchars($info); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <span><?php echo $error; ?></span>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <span><?php echo $success; ?></span>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="username">Kullanıcı Adı</label>
                    <input type="text" 
                           id="username" 
                           name="username" 
                           class="form-control" 
                           placeholder="admin"
                           value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
                           required 
                           autofocus>
                </div>

                <div class="form-group">
                    <label for="password">Şifre</label>
                    <input type="password" 
                           id="password" 
                           name="password" 
                           class="form-control" 
                           placeholder="password"
                           required>
                </div>

                <button type="submit" name="login" class="btn btn-primary">
                    <i class="fas fa-sign-in-alt"></i>
                    <span>Giriş Yap</span>
                </button>
            </form>

            <p style="margin-top: 20px; text-align: center; color: #666; font-size: 12px;">
                <strong>Test Kullanıcısı:</strong><br>
                Kullanıcı Adı: admin<br>
                Şifre: password
            </p>
        </div>
    </div>
</body>
</html>

