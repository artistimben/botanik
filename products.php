<?php
/**
 * Papatya Botanik - Ürünler Sayfası
 * Tüm kategorilerdeki ürünleri listeler
 */
$page_title = 'Ürünlerimiz';
$hide_top_bar = true;
require_once 'includes/header.php';

// Seçili kategoriyi al
$selectedCategory = isset($_GET['category']) ? $_GET['category'] : 'all';
?>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <h1 class="page-title">Ürünlerimiz</h1>
        <p class="page-subtitle">En taze ve kaliteli çiçekler, özel düzenlemeler</p>
    </div>
</section>

<!-- Kategori Filtreleri -->
<section class="category-filters">
    <div class="container">
        <div class="filter-wrapper">
            <button class="filter-btn <?php echo $selectedCategory == 'all' ? 'active' : ''; ?>"
                onclick="filterCategory('all')">
                <i class="fas fa-th"></i> Tümü
            </button>
            <?php foreach ($categories as $key => $category): ?>
                <button class="filter-btn <?php echo $selectedCategory == $key ? 'active' : ''; ?>"
                    onclick="filterCategory('<?php echo $key; ?>')">
                    <span><?php echo $category['icon']; ?></span>
                    <?php echo $category['name']; ?>
                </button>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Ürünler Grid -->
<section class="products-section section-padding">
    <div class="container">
        <?php
        // "Tümünü Göster" parametresi
        $showAll = isset($_GET['show']) && $_GET['show'] == 'all';

        // Kategorilere göre ürünleri listele
        foreach ($categories as $key => $category):
            // Eğer belirli bir kategori seçildiyse sadece onu göster
            if ($selectedCategory != 'all' && $selectedCategory != $key) {
                continue;
            }

            // VERİTABANINDAN ÜRÜNLERİ ÇEK
            if (!$showAll && $selectedCategory == 'all') {
                // Tüm kategoriler: Her birinden SADECE 6 TANE!
                $categoryProducts = getProductsByCategory($category['id'], 6);
            } elseif ($selectedCategory != 'all' && !$showAll) {
                // Tek kategori: 18 ürün
                $categoryProducts = getProductsByCategory($category['id'], 18);
            } else {
                // Tümünü göster: Max 50 ürün
                $categoryProducts = getProductsByCategory($category['id'], 50);
            }

            if (empty($categoryProducts)) {
                continue;
            }
            ?>

            <div class="category-section" data-category="<?php echo $key; ?>"
                style="<?php echo ($selectedCategory != 'all' && $selectedCategory != $key) ? 'display:none;' : ''; ?>">
                <div class="category-header">
                    <div class="category-title-wrapper">
                        <span class="category-icon-large"><?php echo $category['icon']; ?></span>
                        <div>
                            <h2 class="category-title"><?php echo $category['name']; ?></h2>
                            <p class="category-desc"><?php echo $category['description']; ?></p>
                        </div>
                    </div>
                    <div class="product-count">
                        <?php echo count($categoryProducts); ?> Ürün
                    </div>
                </div>

                <div class="products-grid">
                    <?php
                    foreach ($categoryProducts as $product):
                        $image = $product['image_path'] ? $product['image_path'] : 'images/placeholder.jpg';
                        $productName = $product['name'] ? $product['name'] : $category['name'];
                        ?>
                        <div class="product-card">
                            <div class="product-image">
                                <img src="<?php echo $image; ?>" alt="<?php echo $productName; ?>" loading="lazy">
                                <div class="product-overlay">
                                    <button class="product-btn view-btn" onclick="openLightbox('<?php echo $image; ?>')"
                                        title="Büyüt">
                                        <i class="fas fa-search-plus"></i>
                                    </button>
                                    <button class="product-btn whatsapp-btn"
                                        onclick="orderViaWhatsApp('<?php echo $category['name']; ?>', '<?php echo addslashes($productName); ?>', '<?php echo SITE_URL . '/' . $image; ?>')"
                                        title="WhatsApp ile Sipariş">
                                        <i class="fab fa-whatsapp"></i>
                                    </button>
                                    <button class="product-btn call-btn"
                                        onclick="orderViaCall('<?php echo addslashes($productName); ?>')"
                                        title="Telefon ile Sipariş">
                                        <i class="fas fa-phone"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="product-info">
                                <h3 class="product-name"><?php echo $productName; ?></h3>
                                <div class="product-actions">
                                    <a href="https://wa.me/<?php echo WHATSAPP_NUMBER; ?>?text=Merhaba, <?php echo urlencode($category['name'] . ' - ' . $productName . "\n\nÜrün Resmi: " . SITE_URL . '/' . $image); ?>"
                                        class="btn btn-sm btn-success" target="_blank">
                                        <i class="fab fa-whatsapp"></i> Sipariş Ver
                                    </a>
                                    <a href="tel:<?php echo str_replace(' ', '', PHONE_NUMBER); ?>"
                                        class="btn btn-sm btn-outline">
                                        <i class="fas fa-phone"></i> Ara
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <?php
                    // Daha fazla ürün var mı? (Veritabanından tekrar saymaya gerek yok, performans için şimdilik basit tutalım)
                    // Gerçekten kaç tane olduğunu bilmek için ayrı bir query gerekebilir veya limit koymadan çekip PHP'de slice edebiliriz.
                    // Şimdilik 50 limit olduğu için 50'den fazlaysa butonu gösterelim.
                    if (count($categoryProducts) >= ($showAll ? 50 : ($selectedCategory == 'all' ? 6 : 18))):
                        ?>
                        <div style="grid-column: 1 / -1; text-align: center; padding: 2rem;">
                            <a href="products.php?category=<?php echo $key; ?>&show=all" class="btn btn-outline">
                                Tümünü Göster
                            </a>
                        </div>
                    <?php endif; ?>

                    <?php
                    // Videoları da göster - max 6 video
                    $videos_limited = array_slice($videos, 0, 6);
                    foreach ($videos_limited as $index => $video):
                        $videoName = pathinfo($video, PATHINFO_FILENAME);
                        ?>
                        <div class="product-card video-card">
                            <div class="product-image video-container">
                                <video controls poster="">
                                    <source src="<?php echo $video; ?>" type="video/mp4">
                                    Tarayıcınız video etiketini desteklemiyor.
                                </video>
                                <div class="video-badge">
                                    <i class="fas fa-play"></i> Video
                                </div>
                            </div>
                            <div class="product-info">
                                <h3 class="product-name"><?php echo $category['name']; ?> - Video</h3>
                                <div class="product-actions">
                                    <a href="https://wa.me/<?php echo WHATSAPP_NUMBER; ?>?text=Merhaba, <?php echo urlencode($category['name']); ?> video ürün hakkında bilgi almak istiyorum"
                                        class="btn btn-sm btn-success" target="_blank">
                                        <i class="fab fa-whatsapp"></i> Sipariş Ver
                                    </a>
                                    <a href="tel:<?php echo str_replace(' ', '', PHONE_NUMBER); ?>"
                                        class="btn btn-sm btn-outline">
                                        <i class="fas fa-phone"></i> Ara
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

        <?php endforeach; ?>

        <!-- Eğer hiç ürün yoksa -->
        <?php if ($selectedCategory != 'all' && empty($categoryProducts)): ?>
            <div class="no-products">
                <i class="fas fa-flower"></i>
                <h3>Bu kategoride henüz ürün bulunmuyor</h3>
                <p>Diğer kategorilerimize göz atabilirsiniz</p>
                <button onclick="filterCategory('all')" class="btn btn-primary">Tüm Ürünleri Göster</button>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section-alt">
    <div class="container">
        <div class="cta-box">
            <div class="cta-icon">
                <i class="fas fa-headset"></i>
            </div>
            <div class="cta-text">
                <h3>Özel Tasarım mı İstiyorsunuz?</h3>
                <p>Hayalinizdeki çiçek düzenlemesi için bize ulaşın</p>
            </div>
            <div class="cta-actions">
                <a href="https://wa.me/<?php echo WHATSAPP_NUMBER; ?>?text=Merhaba, özel tasarım hakkında bilgi almak istiyorum"
                    class="btn btn-success" target="_blank">
                    <i class="fab fa-whatsapp"></i> WhatsApp
                </a>
                <a href="tel:<?php echo str_replace(' ', '', PHONE_NUMBER); ?>" class="btn btn-primary">
                    <i class="fas fa-phone"></i> Hemen Ara
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Lightbox Modal -->
<div id="lightbox" class="lightbox">
    <span class="lightbox-close">&times;</span>
    <img class="lightbox-content" id="lightbox-img">
    <div class="lightbox-actions">
        <a href="#" id="lightbox-whatsapp" class="btn btn-success" target="_blank">
            <i class="fab fa-whatsapp"></i> WhatsApp ile Sipariş
        </a>
        <a href="tel:<?php echo str_replace(' ', '', PHONE_NUMBER); ?>" class="btn btn-primary">
            <i class="fas fa-phone"></i> Telefon ile Sipariş
        </a>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>