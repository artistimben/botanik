<?php
/**
 * Ürün Detay Sayfası
 */
require_once 'includes/config.php';

// Ürün ID'sini al
$productId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($productId <= 0) {
    header('Location: products.php');
    exit;
}

// Ürün bilgilerini getir
try {
    $stmt = $pdo->prepare("
        SELECT p.*, c.name as category_name, c.slug as category_slug
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
        WHERE p.id = ? AND p.is_active = 1
    ");
    $stmt->execute([$productId]);
    $product = $stmt->fetch();

    if (!$product) {
        header('Location: products.php');
        exit;
    }
} catch (PDOException $e) {
    header('Location: products.php');
    exit;
}

// Sayfa başlığı
$pageTitle = $product['name'] . ' - ' . SITE_NAME;
$pageDescription = $product['short_description'] ?? $product['name'];

require_once 'includes/header.php';
?>

<style>
    .product-detail-container {
        max-width: 1200px;
        margin: 2rem auto;
        padding: 2rem;
    }

    .product-detail-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 3rem;
        margin-top: 2rem;
    }

    .product-image-main {
        width: 100%;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        cursor: pointer;
        transition: transform 0.3s ease;
    }

    .product-image-main:hover {
        transform: scale(1.02);
    }

    .product-info-detail {
        padding: 2rem;
    }

    .product-title {
        font-size: 2rem;
        color: var(--primary-color);
        margin-bottom: 1rem;
    }

    .product-category {
        display: inline-block;
        background: var(--primary-color);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.9rem;
        margin-bottom: 1.5rem;
    }

    .product-description {
        font-size: 1.1rem;
        line-height: 1.8;
        color: #555;
        margin-bottom: 2rem;
    }

    .product-actions-detail {
        display: flex;
        gap: 1rem;
        margin-top: 2rem;
    }

    .btn-large {
        padding: 1rem 2rem;
        font-size: 1.1rem;
        border-radius: 8px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
    }

    .btn-whatsapp {
        background: #25D366;
        color: white;
    }

    .btn-whatsapp:hover {
        background: #20BA5A;
        transform: translateY(-2px);
    }

    .btn-call {
        background: var(--primary-color);
        color: white;
    }

    .btn-call:hover {
        background: var(--secondary-color);
        transform: translateY(-2px);
    }

    .btn-back {
        background: #f0f0f0;
        color: #333;
        margin-bottom: 2rem;
    }

    .btn-back:hover {
        background: #e0e0e0;
    }

    @media (max-width: 768px) {
        .product-detail-grid {
            grid-template-columns: 1fr;
            gap: 2rem;
        }

        .product-actions-detail {
            flex-direction: column;
        }
    }
</style>

<div class="product-detail-container">
    <a href="products.php?category=<?php echo $product['category_slug']; ?>" class="btn-large btn-back">
        <i class="fas fa-arrow-left"></i> Geri Dön
    </a>

    <div class="product-detail-grid">
        <div class="product-image-section">
            <img src="<?php echo $product['image_path']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>"
                class="product-image-main" onclick="openLightbox('<?php echo $product['image_path']; ?>')">
        </div>

        <div class="product-info-detail">
            <span class="product-category">
                <i class="fas fa-tag"></i>
                <?php echo $product['category_name']; ?>
            </span>

            <h1 class="product-title">
                <?php echo htmlspecialchars($product['name']); ?>
            </h1>

            <?php if ($product['short_description']): ?>
                <p class="product-description">
                    <?php echo nl2br(htmlspecialchars($product['short_description'])); ?>
                </p>
            <?php endif; ?>

            <?php if ($product['description']): ?>
                <div class="product-description">
                    <?php echo nl2br(htmlspecialchars($product['description'])); ?>
                </div>
            <?php endif; ?>

            <div class="product-actions-detail">
                <a href="https://wa.me/<?php echo WHATSAPP_NUMBER; ?>?text=<?php
                   echo urlencode('Merhaba, ' . $product['name'] . ' ürünü hakkında bilgi almak istiyorum.' . "\n\n" . SITE_URL . '/product-detail.php?id=' . $product['id']);
                   ?>"
                    class="btn-large btn-whatsapp"
                    target="_blank">
                    <i class="fab fa-whatsapp"></i> WhatsApp ile Sipariş Ver
                </a>

                <a href="tel:<?php echo str_replace(' ', '', PHONE_NUMBER); ?>" class="btn-large btn-call">
                    <i class="fas fa-phone"></i> Hemen Ara
                </a>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>