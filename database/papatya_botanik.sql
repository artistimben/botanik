-- ============================================
-- Papatya Botanik - Veritabanı Yapısı
-- Oluşturulma Tarihi: 2025-11-21
-- Veritabanı: papatya_botanik
-- Karakter Seti: UTF-8
-- ============================================

-- Veritabanını oluştur (eğer yoksa)
CREATE DATABASE IF NOT EXISTS `papatya_botanik` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE `papatya_botanik`;

-- ============================================
-- 1. SİTE AYARLARI TABLOSU
-- Site genelinde kullanılacak ayarlar
-- ============================================
CREATE TABLE IF NOT EXISTS `site_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) NOT NULL COMMENT 'Ayar anahtarı',
  `setting_value` text NOT NULL COMMENT 'Ayar değeri',
  `setting_type` enum('text','textarea','number','email','phone','url','color') DEFAULT 'text' COMMENT 'Ayar tipi',
  `setting_group` varchar(50) DEFAULT 'general' COMMENT 'Ayar grubu',
  `setting_label` varchar(200) DEFAULT NULL COMMENT 'Ayar etiketi',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Site genel ayarları';

-- Site ayarlarını ekle
INSERT INTO `site_settings` (`setting_key`, `setting_value`, `setting_type`, `setting_group`, `setting_label`) VALUES
('site_name', 'Papatya Botanik', 'text', 'general', 'Site Adı'),
('site_title', 'Papatya Botanik - Doğal Çiçek ve Bitki Dünyası', 'text', 'general', 'Site Başlığı'),
('site_description', 'Papatya Botanik ile özel günlerinizi çiçeklerle süsleyin. Buket, arajman, isteme çiçekleri ve daha fazlası.', 'textarea', 'general', 'Site Açıklaması'),
('site_keywords', 'çiçek, buket, botanik, arajman, isteme çiçeği, düğün çiçekleri, kokina, lale, peyzaj', 'textarea', 'seo', 'SEO Anahtar Kelimeler'),
('phone_number', '0555 123 45 67', 'phone', 'contact', 'Telefon Numarası'),
('whatsapp_number', '905551234567', 'phone', 'contact', 'WhatsApp Numarası'),
('email', 'info@papatyabotanik.com', 'email', 'contact', 'E-posta Adresi'),
('address', 'Örnek Mahallesi, Çiçek Sokak No:1, İlçe/Şehir', 'textarea', 'contact', 'Adres'),
('working_hours', 'Pazartesi - Cumartesi: 09:00 - 19:00<br>Pazar: 10:00 - 17:00', 'textarea', 'contact', 'Çalışma Saatleri'),
('instagram', 'papatyabotanik', 'text', 'social', 'Instagram Kullanıcı Adı'),
('facebook', 'papatyabotanik', 'text', 'social', 'Facebook Sayfa Adı'),
('logo_path', 'images/LOGO/şuanki Logo.png', 'text', 'design', 'Logo Yolu'),
('color_primary', '#2d5016', 'color', 'design', 'Ana Renk'),
('color_secondary', '#6b8e23', 'color', 'design', 'İkincil Renk'),
('color_accent', '#f4a460', 'color', 'design', 'Vurgu Rengi'),
('site_status', 'active', 'text', 'general', 'Site Durumu');

-- ============================================
-- 2. KATEGORİLER TABLOSU
-- Ürün kategorileri
-- ============================================
CREATE TABLE IF NOT EXISTS `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL COMMENT 'Kategori adı',
  `slug` varchar(100) NOT NULL COMMENT 'URL dostu isim',
  `folder_name` varchar(100) NOT NULL COMMENT 'Görseller klasör adı',
  `icon` varchar(50) DEFAULT NULL COMMENT 'Kategori ikonu (emoji)',
  `description` text DEFAULT NULL COMMENT 'Kategori açıklaması',
  `display_order` int(11) DEFAULT 0 COMMENT 'Sıralama',
  `is_active` tinyint(1) DEFAULT 1 COMMENT '1=Aktif, 0=Pasif',
  `image_path` varchar(255) DEFAULT NULL COMMENT 'Kategori görseli',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `is_active` (`is_active`),
  KEY `display_order` (`display_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Ürün kategorileri';

-- Kategorileri ekle
INSERT INTO `categories` (`name`, `slug`, `folder_name`, `icon`, `description`, `display_order`, `is_active`) VALUES
('Buketler', 'buketler', 'BUKETLER', '💐', 'Sevdikleriniz için özel buketler', 1, 1),
('Kokina Arajmanlar', 'arajmanlar', 'KOKİNA ARAJMANLAR', '🌸', 'Kokina çiçeklerle özel arajmanlar', 2, 1),
('İsteme Çiçekleri', 'isteme-cicekleri', 'İSTEME ÇİÇEKLERİ', '💍', 'Hayatınızın en özel anı için çiçekler', 3, 1),
('Araç Süsleme', 'arac-susleme', 'ARAÇ SÜSLEME', '🚗', 'Düğün arabalarınızı çiçeklerle süsleyin', 4, 1),
('Çelenkler', 'celenkler', 'ÇELENKLER', '🌹', 'Anlamlı günler için çelenkler', 5, 1),
('Hediyelik Ürünler', 'hediyelik', 'hediyelik', '🎁', 'Özel hediyeler ve sürprizler', 6, 1),
('Laleler', 'lale', 'lale', '🌷', 'Baharın habercisi laleler', 7, 1),
('Peyzaj', 'peyzaj', 'peyzaj', '🌿', 'Bahçe ve peyzaj düzenlemeleri', 8, 1);

-- ============================================
-- 3. ÜRÜNLER TABLOSU
-- Tüm ürünler
-- ============================================
CREATE TABLE IF NOT EXISTS `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL COMMENT 'Kategori ID',
  `name` varchar(200) NOT NULL COMMENT 'Ürün adı',
  `slug` varchar(200) NOT NULL COMMENT 'URL dostu isim',
  `description` text DEFAULT NULL COMMENT 'Ürün açıklaması',
  `short_description` varchar(500) DEFAULT NULL COMMENT 'Kısa açıklama',
  `price` decimal(10,2) DEFAULT NULL COMMENT 'Fiyat (opsiyonel)',
  `image_path` varchar(255) NOT NULL COMMENT 'Ana görsel yolu',
  `is_featured` tinyint(1) DEFAULT 0 COMMENT '1=Öne çıkan, 0=Normal',
  `is_active` tinyint(1) DEFAULT 1 COMMENT '1=Aktif, 0=Pasif',
  `view_count` int(11) DEFAULT 0 COMMENT 'Görüntülenme sayısı',
  `order_count` int(11) DEFAULT 0 COMMENT 'Sipariş sayısı',
  `display_order` int(11) DEFAULT 0 COMMENT 'Sıralama',
  `meta_title` varchar(200) DEFAULT NULL COMMENT 'SEO başlık',
  `meta_description` varchar(500) DEFAULT NULL COMMENT 'SEO açıklama',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `category_id` (`category_id`),
  KEY `is_active` (`is_active`),
  KEY `is_featured` (`is_featured`),
  KEY `display_order` (`display_order`),
  CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Ürünler tablosu';

-- Örnek ürünler (Buketler kategorisi için)
INSERT INTO `products` (`category_id`, `name`, `slug`, `description`, `short_description`, `image_path`, `is_featured`, `is_active`, `display_order`) VALUES
(1, 'Kırmızı Gül Buketi', 'kirmizi-gul-buketi', 'Taze kırmızı güllerden oluşan romantik buket. Sevdiklerinize aşkınızı ifade etmek için mükemmel.', 'Romantik kırmızı gül buketi', 'images/GÖRSELLER/BUKETLER/buket-01.jpg', 1, 1, 1),
(1, 'Beyaz Lilyum Buketi', 'beyaz-lilyum-buketi', 'Zarif beyaz lilyumlardan oluşan özel buket. Saflığı ve temizliği simgeler.', 'Zarif beyaz lilyum buketi', 'images/GÖRSELLER/BUKETLER/buket-02.jpg', 0, 1, 2),
(1, 'Renkli Mevsim Buketi', 'renkli-mevsim-buketi', 'Mevsimin en taze çiçeklerinden oluşan renkli buket. Her ortama uygun.', 'Renkli mevsim çiçekleri', 'images/GÖRSELLER/BUKETLER/buket-03.jpg', 1, 1, 3),
(2, 'Kokina Masa Aranjmanı', 'kokina-masa-aranjmani', 'Kokina çiçeklerle hazırlanmış şık masa aranjmanı. Özel davetleriniz için ideal.', 'Şık kokina masa aranjmanı', 'images/GÖRSELLER/KOKİNA ARAJMANLAR/arajman-01.jpg', 1, 1, 1),
(3, 'İsteme Çiçeği Özel Tasarım', 'isteme-cicegi-ozel-tasarim', 'Hayatınızın en özel anı için özel olarak hazırlanmış isteme çiçeği. Kırmızı güller ve özel süslemeler.', 'Özel tasarım isteme çiçeği', 'images/GÖRSELLER/İSTEME ÇİÇEKLERİ/isteme-01.jpg', 1, 1, 1);

-- ============================================
-- 4. ÜRÜN GALERİSİ
-- Her ürüne ait birden fazla görsel
-- ============================================
CREATE TABLE IF NOT EXISTS `product_gallery` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL COMMENT 'Ürün ID',
  `image_path` varchar(255) NOT NULL COMMENT 'Görsel yolu',
  `image_type` enum('image','video') DEFAULT 'image' COMMENT 'Medya tipi',
  `display_order` int(11) DEFAULT 0 COMMENT 'Sıralama',
  `is_primary` tinyint(1) DEFAULT 0 COMMENT '1=Ana görsel, 0=Diğer',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  KEY `display_order` (`display_order`),
  CONSTRAINT `product_gallery_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Ürün görselleri galerisi';

-- ============================================
-- 5. İLETİŞİM MESAJLARI
-- Formdan gelen mesajlar
-- ============================================
CREATE TABLE IF NOT EXISTS `contact_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `full_name` varchar(100) NOT NULL COMMENT 'Ad Soyad',
  `phone` varchar(20) NOT NULL COMMENT 'Telefon',
  `email` varchar(100) DEFAULT NULL COMMENT 'E-posta (opsiyonel)',
  `message` text NOT NULL COMMENT 'Mesaj',
  `ip_address` varchar(45) DEFAULT NULL COMMENT 'IP adresi',
  `user_agent` varchar(255) DEFAULT NULL COMMENT 'Tarayıcı bilgisi',
  `is_read` tinyint(1) DEFAULT 0 COMMENT '1=Okundu, 0=Okunmadı',
  `is_replied` tinyint(1) DEFAULT 0 COMMENT '1=Cevaplandı, 0=Cevaplandı',
  `admin_note` text DEFAULT NULL COMMENT 'Admin notu',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `read_at` timestamp NULL DEFAULT NULL COMMENT 'Okunma zamanı',
  PRIMARY KEY (`id`),
  KEY `is_read` (`is_read`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='İletişim formu mesajları';

-- ============================================
-- 6. SİPARİŞLER (Gelecek için)
-- WhatsApp/Telefon siparişlerini kaydetmek için
-- ============================================
CREATE TABLE IF NOT EXISTS `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_number` varchar(50) NOT NULL COMMENT 'Sipariş numarası',
  `customer_name` varchar(100) NOT NULL COMMENT 'Müşteri adı',
  `customer_phone` varchar(20) NOT NULL COMMENT 'Müşteri telefonu',
  `customer_email` varchar(100) DEFAULT NULL COMMENT 'Müşteri e-posta',
  `product_id` int(11) DEFAULT NULL COMMENT 'Ürün ID (opsiyonel)',
  `category_id` int(11) DEFAULT NULL COMMENT 'Kategori ID',
  `order_details` text DEFAULT NULL COMMENT 'Sipariş detayları',
  `order_type` enum('whatsapp','phone','form','other') DEFAULT 'whatsapp' COMMENT 'Sipariş türü',
  `status` enum('pending','confirmed','preparing','delivered','cancelled') DEFAULT 'pending' COMMENT 'Durum',
  `total_amount` decimal(10,2) DEFAULT NULL COMMENT 'Toplam tutar',
  `delivery_address` text DEFAULT NULL COMMENT 'Teslimat adresi',
  `delivery_date` date DEFAULT NULL COMMENT 'Teslimat tarihi',
  `admin_note` text DEFAULT NULL COMMENT 'Admin notu',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_number` (`order_number`),
  KEY `product_id` (`product_id`),
  KEY `category_id` (`category_id`),
  KEY `status` (`status`),
  KEY `created_at` (`created_at`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Siparişler tablosu';

-- ============================================
-- 7. ADMIN KULLANICILARI
-- Yönetim paneli için
-- ============================================
CREATE TABLE IF NOT EXISTS `admin_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL COMMENT 'Kullanıcı adı',
  `password` varchar(255) NOT NULL COMMENT 'Şifre (hash)',
  `full_name` varchar(100) NOT NULL COMMENT 'Ad Soyad',
  `email` varchar(100) NOT NULL COMMENT 'E-posta',
  `role` enum('admin','editor','viewer') DEFAULT 'editor' COMMENT 'Rol',
  `is_active` tinyint(1) DEFAULT 1 COMMENT '1=Aktif, 0=Pasif',
  `last_login` timestamp NULL DEFAULT NULL COMMENT 'Son giriş zamanı',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  KEY `is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Admin kullanıcıları';

-- Varsayılan admin kullanıcı ekle
-- Kullanıcı adı: admin
-- Şifre: admin123 (MD5: 0192023a7bbd73250516f069df18b500)
-- ÖNEMLİ: İlk girişte şifreyi mutlaka değiştirin!
INSERT INTO `admin_users` (`username`, `password`, `full_name`, `email`, `role`, `is_active`) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Site Yöneticisi', 'admin@papatyabotanik.com', 'admin', 1);
-- Not: Şifre bcrypt ile hashlenmiş "password" kelimesidir. İlk girişte değiştirin!

-- ============================================
-- 8. SİTE İSTATİSTİKLERİ
-- Günlük ziyaretçi sayısı, görüntüleme vs.
-- ============================================
CREATE TABLE IF NOT EXISTS `site_statistics` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `stat_date` date NOT NULL COMMENT 'İstatistik tarihi',
  `page_views` int(11) DEFAULT 0 COMMENT 'Sayfa görüntüleme',
  `unique_visitors` int(11) DEFAULT 0 COMMENT 'Tekil ziyaretçi',
  `product_views` int(11) DEFAULT 0 COMMENT 'Ürün görüntüleme',
  `whatsapp_clicks` int(11) DEFAULT 0 COMMENT 'WhatsApp tıklama',
  `phone_clicks` int(11) DEFAULT 0 COMMENT 'Telefon tıklama',
  `form_submissions` int(11) DEFAULT 0 COMMENT 'Form gönderimi',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `stat_date` (`stat_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Site istatistikleri';

-- ============================================
-- 9. BLOG/HABERLER (Gelecek için)
-- ============================================
CREATE TABLE IF NOT EXISTS `blog_posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL COMMENT 'Başlık',
  `slug` varchar(200) NOT NULL COMMENT 'URL dostu isim',
  `content` longtext NOT NULL COMMENT 'İçerik',
  `excerpt` text DEFAULT NULL COMMENT 'Özet',
  `featured_image` varchar(255) DEFAULT NULL COMMENT 'Öne çıkan görsel',
  `author_id` int(11) DEFAULT NULL COMMENT 'Yazar ID',
  `category` varchar(100) DEFAULT 'Genel' COMMENT 'Kategori',
  `tags` varchar(500) DEFAULT NULL COMMENT 'Etiketler (virgülle ayrılmış)',
  `is_published` tinyint(1) DEFAULT 0 COMMENT '1=Yayında, 0=Taslak',
  `view_count` int(11) DEFAULT 0 COMMENT 'Görüntülenme',
  `published_at` timestamp NULL DEFAULT NULL COMMENT 'Yayın tarihi',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `author_id` (`author_id`),
  KEY `is_published` (`is_published`),
  CONSTRAINT `blog_posts_ibfk_1` FOREIGN KEY (`author_id`) REFERENCES `admin_users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Blog yazıları';

-- ============================================
-- 10. SLIDER/BANNER YÖNETİMİ
-- ============================================
CREATE TABLE IF NOT EXISTS `sliders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) DEFAULT NULL COMMENT 'Başlık',
  `subtitle` varchar(300) DEFAULT NULL COMMENT 'Alt başlık',
  `description` text DEFAULT NULL COMMENT 'Açıklama',
  `image_path` varchar(255) NOT NULL COMMENT 'Görsel yolu',
  `link_url` varchar(255) DEFAULT NULL COMMENT 'Link URL',
  `link_text` varchar(100) DEFAULT NULL COMMENT 'Link metni',
  `display_order` int(11) DEFAULT 0 COMMENT 'Sıralama',
  `is_active` tinyint(1) DEFAULT 1 COMMENT '1=Aktif, 0=Pasif',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `is_active` (`is_active`),
  KEY `display_order` (`display_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Slider yönetimi';

-- Örnek slider
INSERT INTO `sliders` (`title`, `subtitle`, `description`, `image_path`, `link_url`, `link_text`, `display_order`, `is_active`) VALUES
('Doğanın Güzelliğini Sevdiklerinizle Paylaşın', 'Özel günlerinizi en taze çiçeklerle süsleyin', 'Papatya Botanik olarak her özel anınız için buradayız', 'images/önecıkanlar/buketler.png', 'products.php', 'Ürünlerimizi Keşfedin', 1, 1);

-- ============================================
-- VERİTABANI KURULUMU TAMAMLANDI!
-- ============================================

-- Veritabanı bilgileri özeti:
-- Veritabanı Adı: papatya_botanik
-- Karakter Seti: UTF8MB4 (Türkçe karakter desteği)
-- Toplam Tablo Sayısı: 10
-- 
-- Tablolar:
-- 1. site_settings       - Site ayarları
-- 2. categories          - Ürün kategorileri (8 kategori eklendi)
-- 3. products            - Ürünler (5 örnek ürün eklendi)
-- 4. product_gallery     - Ürün görselleri
-- 5. contact_messages    - İletişim mesajları
-- 6. orders              - Siparişler
-- 7. admin_users         - Admin kullanıcıları (1 admin eklendi)
-- 8. site_statistics     - İstatistikler
-- 9. blog_posts          - Blog yazıları
-- 10. sliders            - Slider yönetimi (1 slider eklendi)
--
-- Varsayılan Admin Giriş Bilgileri:
-- Kullanıcı Adı: admin
-- Şifre: password
-- ⚠️ İLK GİRİŞTE MUTLAKA ŞİFREYİ DEĞİŞTİRİN!
--
-- Kurulum Sonrası:
-- 1. phpMyAdmin'i açın (http://localhost/phpmyadmin)
-- 2. Sol menüden "Import" sekmesine tıklayın
-- 3. Bu dosyayı seçin ve "Go" butonuna basın
-- 4. includes/db.php dosyasını oluşturun (örnek dosya: database/db_example.php)
-- 5. Veritabanı bağlantı bilgilerini girin
--
-- ============================================

