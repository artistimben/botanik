<header class="admin-header">
    <div class="header-left">
        <button class="sidebar-toggle" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i>
        </button>
        <div class="logo">
            <i class="fas fa-leaf"></i>
            <span>Papatya Botanik</span>
        </div>
    </div>
    
    <div class="header-right">
        <a href="../index.php" class="header-link" target="_blank" title="Siteyi Görüntüle">
            <i class="fas fa-globe"></i>
            <span class="hide-mobile">Siteyi Görüntüle</span>
        </a>
        
        <div class="header-notifications">
            <button class="notification-btn" onclick="toggleNotifications()">
                <i class="fas fa-bell"></i>
                <?php 
                $unreadCount = isset($stats) ? ($stats['pending_orders'] + $stats['unread_messages']) : 0;
                if ($unreadCount > 0): 
                ?>
                    <span class="notification-badge"><?php echo $unreadCount; ?></span>
                <?php endif; ?>
            </button>
            <div class="notifications-dropdown" id="notificationsDropdown">
                <div class="notifications-header">
                    <h3>Bildirimler</h3>
                </div>
                <div class="notifications-body">
                    <?php if (isset($stats) && $stats['pending_orders'] > 0): ?>
                        <a href="orders.php?status=pending" class="notification-item">
                            <i class="fas fa-shopping-cart text-warning"></i>
                            <div>
                                <strong><?php echo $stats['pending_orders']; ?> bekleyen sipariş</strong>
                                <small>İncelemeniz gerekiyor</small>
                            </div>
                        </a>
                    <?php endif; ?>
                    
                    <?php if (isset($stats) && $stats['unread_messages'] > 0): ?>
                        <a href="messages.php" class="notification-item">
                            <i class="fas fa-envelope text-danger"></i>
                            <div>
                                <strong><?php echo $stats['unread_messages']; ?> okunmamış mesaj</strong>
                                <small>Yeni mesajlarınız var</small>
                            </div>
                        </a>
                    <?php endif; ?>
                    
                    <?php if ($unreadCount == 0): ?>
                        <div class="notification-item text-center">
                            <i class="fas fa-check-circle text-success"></i>
                            <p>Tüm bildirimler okundu</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="header-user">
            <button class="user-btn" onclick="toggleUserMenu()">
                <div class="user-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <span class="hide-mobile"><?php echo htmlspecialchars($adminInfo['fullname']); ?></span>
                <i class="fas fa-chevron-down"></i>
            </button>
            <div class="user-dropdown" id="userDropdown">
                <div class="user-info">
                    <strong><?php echo htmlspecialchars($adminInfo['fullname']); ?></strong>
                    <small><?php echo htmlspecialchars($adminInfo['email']); ?></small>
                </div>
                <div class="dropdown-divider"></div>
                <a href="profile.php" class="dropdown-item">
                    <i class="fas fa-user-circle"></i> Profilim
                </a>
                <a href="settings.php" class="dropdown-item">
                    <i class="fas fa-cog"></i> Ayarlar
                </a>
                <div class="dropdown-divider"></div>
                <a href="logout.php" class="dropdown-item text-danger">
                    <i class="fas fa-sign-out-alt"></i> Çıkış Yap
                </a>
            </div>
        </div>
    </div>
</header>

