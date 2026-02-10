<?php
/**
 * Admin Panel - Site Ayarları
 */
require_once 'includes/auth.php';
require_once 'includes/functions.php';

checkAdminLogin();
checkAdminRole(['admin']); // Sadece admin

$adminInfo = getAdminInfo();
$stats = getAdminStats();

$success = '';
$error = '';

// Ayarları güncelle
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_settings'])) {
    $updates = 0;
    
    foreach ($_POST as $key => $value) {
        if ($key !== 'update_settings' && strpos($key, 'setting_') === 0) {
            $settingKey = str_replace('setting_', '', $key);
            $settingValue = cleanInput($value);
            
            $stmt = $conn->prepare("UPDATE site_settings SET setting_value = ? WHERE setting_key = ?");
            $stmt->bind_param("ss", $settingValue, $settingKey);
            
            if ($stmt->execute()) {
                $updates++;
            }
        }
    }
    
    if ($updates > 0) {
        $success = "$updates ayar başarıyla güncellendi!";
    } else {
        $error = 'Hiçbir ayar güncellenmedi.';
    }
}

// Tüm ayarları getir
$settingsResult = $conn->query("SELECT * FROM site_settings ORDER BY setting_group, id");
$settings = [];
while ($row = $settingsResult->fetch_assoc()) {
    $settings[$row['setting_group']][] = $row;
}

$pageTitle = 'Site Ayarları';
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
        .settings-tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 30px;
            border-bottom: 2px solid var(--light-color);
        }
        
        .tab-btn {
            padding: 12px 24px;
            background: transparent;
            border: none;
            border-bottom: 3px solid transparent;
            color: var(--dark-color);
            font-size: 15px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .tab-btn:hover {
            color: var(--primary-color);
        }
        
        .tab-btn.active {
            color: var(--primary-color);
            border-bottom-color: var(--primary-color);
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
        }
        
        .setting-item {
            margin-bottom: 25px;
            padding: 20px;
            background: var(--light-color);
            border-radius: 10px;
        }
        
        .setting-item label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--dark-color);
        }
        
        .setting-item small {
            display: block;
            color: var(--dark-color);
            opacity: 0.7;
            margin-top: 5px;
            font-size: 13px;
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
        }
        
        textarea.form-control {
            min-height: 100px;
            resize: vertical;
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <?php include 'includes/sidebar.php'; ?>

    <div class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-cog"></i> Site Ayarları</h1>
            <p>Sitenizin genel ayarlarını buradan yönetin</p>
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
            <!-- Tabs -->
            <div class="settings-tabs">
                <?php 
                $groups = [
                    'general' => 'Genel',
                    'contact' => 'İletişim',
                    'social' => 'Sosyal Medya',
                    'design' => 'Tasarım',
                    'seo' => 'SEO'
                ];
                
                $first = true;
                foreach ($groups as $key => $label): 
                    if (isset($settings[$key])):
                ?>
                    <button class="tab-btn <?php echo $first ? 'active' : ''; ?>" 
                            onclick="switchTab('<?php echo $key; ?>')">
                        <?php echo $label; ?>
                    </button>
                <?php 
                    $first = false;
                    endif;
                endforeach; 
                ?>
            </div>

            <!-- Form -->
            <form method="POST" action="">
                <?php 
                $first = true;
                foreach ($settings as $group => $groupSettings): 
                ?>
                    <div class="tab-content <?php echo $first ? 'active' : ''; ?>" 
                         id="tab-<?php echo $group; ?>">
                        
                        <?php foreach ($groupSettings as $setting): ?>
                            <div class="setting-item">
                                <label for="setting_<?php echo $setting['setting_key']; ?>">
                                    <?php echo htmlspecialchars($setting['setting_label']); ?>
                                </label>
                                
                                <?php if ($setting['setting_type'] === 'textarea'): ?>
                                    <textarea 
                                        name="setting_<?php echo $setting['setting_key']; ?>" 
                                        id="setting_<?php echo $setting['setting_key']; ?>" 
                                        class="form-control"><?php echo htmlspecialchars($setting['setting_value']); ?></textarea>
                                
                                <?php elseif ($setting['setting_type'] === 'color'): ?>
                                    <input type="color" 
                                           name="setting_<?php echo $setting['setting_key']; ?>" 
                                           id="setting_<?php echo $setting['setting_key']; ?>" 
                                           class="form-control"
                                           value="<?php echo htmlspecialchars($setting['setting_value']); ?>"
                                           style="height: 50px;">
                                
                                <?php else: ?>
                                    <input type="<?php echo $setting['setting_type']; ?>" 
                                           name="setting_<?php echo $setting['setting_key']; ?>" 
                                           id="setting_<?php echo $setting['setting_key']; ?>" 
                                           class="form-control"
                                           value="<?php echo htmlspecialchars($setting['setting_value']); ?>">
                                <?php endif; ?>
                                
                                <small>Ayar anahtarı: <?php echo $setting['setting_key']; ?></small>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php 
                    $first = false;
                endforeach; 
                ?>

                <div style="margin-top: 30px; display: flex; gap: 10px;">
                    <button type="submit" name="update_settings" class="btn btn-primary">
                        <i class="fas fa-save"></i> Ayarları Kaydet
                    </button>
                    <a href="index.php" class="btn btn-secondary">
                        <i class="fas fa-times"></i> İptal
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script src="assets/js/admin.js"></script>
    <script>
        function switchTab(tabName) {
            // Tüm tab butonlarından active sınıfını kaldır
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            
            // Tüm tab içeriklerini gizle
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.remove('active');
            });
            
            // Seçili tab'ı göster
            document.querySelector(`[onclick="switchTab('${tabName}')"]`).classList.add('active');
            document.getElementById(`tab-${tabName}`).classList.add('active');
        }
    </script>
</body>
</html>

