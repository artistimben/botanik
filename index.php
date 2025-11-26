<?php
/**
 * Papatya Botanik - Ana Sayfa
 * Modern, botanik temalı çiçekçi web sitesi
 */
$page_title = 'Ana Sayfa';
require_once 'includes/header.php';
?>

<!-- Hero Section - Anasayfa Karşılama -->
<section class="hero-section">
    <div class="hero-slider">
        <!-- Slide 1 -->
        <div class="hero-slide active" style="background-image: url('images/önecıkanlar/buketler.png');">
            <div class="hero-overlay"></div>
            <div class="hero-content">
                <h1 class="hero-title animate-fade-in">Doğanın Güzelliğini<br>Sevdiklerinizle Paylaşın</h1>
                <p class="hero-subtitle animate-fade-in-delay-1">Özel günlerinizi en taze çiçeklerle süsleyin</p>
                <div class="hero-buttons animate-fade-in-delay-2">
                    <a href="products.php" class="btn btn-primary">Ürünlerimizi Keşfedin</a>
                    <a href="https://wa.me/<?php echo WHATSAPP_NUMBER; ?>" class="btn btn-outline">
                        <i class="fab fa-whatsapp"></i> WhatsApp ile İletişim
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Scroll Down Indicator -->
    <div class="scroll-indicator">
        <i class="fas fa-chevron-down"></i>
    </div>
</section>

<!-- Öne Çıkan Kategoriler -->
<section class="featured-categories section-padding">
    <div class="container">
        <div class="section-header text-center">
            <h2 class="section-title">Kategorilerimiz</h2>
            <p class="section-subtitle">Her özel anınız için en uygun çiçekleri bulun</p>
        </div>

        <div class="categories-grid">
            <?php foreach ($categories as $key => $category): ?>
            <div class="category-card" data-aos="fade-up">
                <a href="products.php?category=<?php echo $key; ?>" class="category-link">
                    <div class="category-image">
                        <?php
                        // Her kategoriden SADECE İLK resmi al
                        $images = getImagesFromCategory($category['folder'], 1);
                        $firstImage = !empty($images) ? $images[0] : 'images/önecıkanlar/buketler.png';
                        ?>
                        <img src="<?php echo $firstImage; ?>" alt="<?php echo $category['name']; ?>" loading="lazy">
                        <div class="category-overlay">
                            <span class="category-icon"><?php echo $category['icon']; ?></span>
                        </div>
                    </div>
                    <div class="category-info">
                        <h3 class="category-name"><?php echo $category['name']; ?></h3>
                        <p class="category-description"><?php echo $category['description']; ?></p>
                        <span class="category-cta">Görüntüle <i class="fas fa-arrow-right"></i></span>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Hakkımızda Bölümü -->
<section id="about" class="about-section section-padding bg-light">
    <div class="container">
        <div class="about-wrapper">
            <div class="about-image" data-aos="fade-right">
                <img src="images/Yeni Görsel/DSC00717.JPG" 
                     alt="Papatya Botanik Hakkında" 
                     loading="lazy">
                <div class="about-badge">
                    <span class="badge-icon">🌸</span>
                    <span class="badge-text">10+ Yıl<br>Tecrübe</span>
                </div>
            </div>
            <div class="about-content" data-aos="fade-left">
                <h2 class="section-title">Papatya Botanik'e Hoş Geldiniz</h2>
                <p class="about-text">
                    Papatya Botanik olarak, yıllardır sevgi dolu anlarınıza çiçeklerle renk katıyoruz. 
                    Taze, kaliteli ve özenle seçilmiş çiçeklerimizle her özel gününüzü daha da anlamlı 
                    kılmak için buradayız.
                </p>
                <p class="about-text">
                    Profesyonel ekibimiz, her müşterimizin isteklerini özenle dinler ve en uygun 
                    çiçek düzenlemelerini hazırlar. Düğünlerden doğum günlerine, romantik sürprizlerden 
                    kurumsal etkinliklere kadar her türlü organizasyon için hizmet veriyoruz.
                </p>

                <div class="about-features">
                    <div class="feature-item">
                        <i class="fas fa-leaf"></i>
                        <div>
                            <h4>Taze Çiçekler</h4>
                            <p>Her gün taze çiçek tedariği</p>
                        </div>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-heart"></i>
                        <div>
                            <h4>Özel Tasarımlar</h4>
                            <p>İsteklerinize özel düzenlemeler</p>
                        </div>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-truck"></i>
                        <div>
                            <h4>Hızlı Teslimat</h4>
                            <p>Güvenli ve hızlı kargo</p>
                        </div>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-star"></i>
                        <div>
                            <h4>Kalite Garantisi</h4>
                            <p>%100 müşteri memnuniyeti</p>
                        </div>
                    </div>
                </div>

                <a href="products.php" class="btn btn-primary">Ürünlerimizi İnceleyin</a>
            </div>
        </div>
    </div>
</section>

<!-- Galeri Önizleme -->
<section id="gallery" class="gallery-preview section-padding">
    <div class="container">
        <div class="section-header text-center">
            <h2 class="section-title">Çalışmalarımız</h2>
            <p class="section-subtitle">En son projelerimizden örnekler</p>
        </div>

        <div class="gallery-grid">
            <?php
            // ULTRA HIZLI: Sadece 4 kategori göster!
            $galleryImages = [];
            $sampleCategories = ['BUKETLER', 'İSTEME ÇİÇEKLERİ', 'ARAÇ SÜSLEME', 'KOKİNA ARAJMANLAR'];
            
            foreach ($sampleCategories as $cat) {
                $catImages = getImagesFromCategory($cat, 1); // Her kategoriden 1 resim
                if (!empty($catImages)) {
                    $galleryImages[] = [
                        'src' => $catImages[0],
                        'category' => $cat
                    ];
                }
            }
            
            foreach ($galleryImages as $index => $image):
            ?>
            <div class="gallery-item" data-aos="zoom-in" data-aos-delay="<?php echo $index * 100; ?>">
                <img src="<?php echo $image['src']; ?>" 
                     alt="<?php echo $image['category']; ?>" 
                     loading="lazy">
                <div class="gallery-overlay">
                    <button class="gallery-btn" onclick="openLightbox('<?php echo $image['src']; ?>')">
                        <i class="fas fa-search-plus"></i>
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="text-center" style="margin-top: 2rem;">
            <a href="products.php" class="btn btn-outline">Tüm Ürünleri Görüntüle</a>
        </div>
    </div>
</section>

<!-- İletişim Bölümü -->
<section id="contact" class="contact-section section-padding bg-light">
    <div class="container">
        <div class="section-header text-center">
            <h2 class="section-title">Bizimle İletişime Geçin</h2>
            <p class="section-subtitle">Size nasıl yardımcı olabiliriz?</p>
        </div>

        <div class="contact-wrapper">
            <div class="contact-info">
                <div class="contact-card" data-aos="fade-up">
                    <div class="contact-icon">
                        <i class="fas fa-phone"></i>
                    </div>
                    <h3>Telefon</h3>
                    <p><?php echo PHONE_NUMBER; ?></p>
                    <a href="tel:<?php echo str_replace(' ', '', PHONE_NUMBER); ?>" class="btn btn-sm btn-primary">
                        Hemen Ara
                    </a>
                </div>

                <div class="contact-card" data-aos="fade-up" data-aos-delay="100">
                    <div class="contact-icon whatsapp">
                        <i class="fab fa-whatsapp"></i>
                    </div>
                    <h3>WhatsApp</h3>
                    <p>7/24 Mesaj Desteği</p>
                    <a href="https://wa.me/<?php echo WHATSAPP_NUMBER; ?>?text=Merhaba, bilgi almak istiyorum" 
                       class="btn btn-sm btn-success" target="_blank">
                        WhatsApp'tan Yaz
                    </a>
                </div>

                <div class="contact-card" data-aos="fade-up" data-aos-delay="200">
                    <div class="contact-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <h3>E-posta</h3>
                    <p><?php echo EMAIL; ?></p>
                    <a href="mailto:<?php echo EMAIL; ?>" class="btn btn-sm btn-primary">
                        E-posta Gönder
                    </a>
                </div>

                <div class="contact-card" data-aos="fade-up" data-aos-delay="300">
                    <div class="contact-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <h3>Adres</h3>
                    <p><?php echo ADDRESS; ?></p>
                    <a href="#" class="btn btn-sm btn-outline">
                        Haritada Görüntüle
                    </a>
                </div>
            </div>

            <div class="contact-form-wrapper" data-aos="fade-left">
                <div class="contact-form-header">
                    <h3>Hızlı İletişim Formu</h3>
                    <p>Formu doldurun, sizi arayalım</p>
                </div>
                <form class="contact-form" id="contactForm">
                    <div class="form-group">
                        <input type="text" class="form-control" placeholder="Adınız Soyadınız" required>
                    </div>
                    <div class="form-group">
                        <input type="tel" class="form-control" placeholder="Telefon Numaranız" required>
                    </div>
                    <div class="form-group">
                        <textarea class="form-control" rows="4" placeholder="Mesajınız" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-paper-plane"></i> Gönder
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- CTA (Call to Action) Section -->
<section class="cta-section">
    <div class="container">
        <div class="cta-content" data-aos="zoom-in">
            <h2>Özel Günleriniz İçin Hemen Sipariş Verin</h2>
            <p>Profesyonel ekibimiz size en uygun çiçek düzenlemelerini hazırlamak için bekliyor</p>
            <div class="cta-buttons">
                <a href="tel:<?php echo str_replace(' ', '', PHONE_NUMBER); ?>" class="btn btn-light btn-lg">
                    <i class="fas fa-phone"></i> <?php echo PHONE_NUMBER; ?>
                </a>
                <a href="https://wa.me/<?php echo WHATSAPP_NUMBER; ?>" class="btn btn-success btn-lg" target="_blank">
                    <i class="fab fa-whatsapp"></i> WhatsApp
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Lightbox Modal -->
<div id="lightbox" class="lightbox">
    <span class="lightbox-close">&times;</span>
    <img class="lightbox-content" id="lightbox-img">
</div>

<?php require_once 'includes/footer.php'; ?>

