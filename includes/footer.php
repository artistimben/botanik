    </main>

    <!-- Footer -->
    <footer class="main-footer">
        <div class="container">
            <div class="footer-content">
                <!-- Hakkımızda -->
                <div class="footer-column">
                    <h3>Papatya Botanik</h3>
                    <p>Doğanın güzelliğini sevdiklerinizle paylaşın. Her özel anınız için en taze ve kaliteli çiçekler.</p>
                    <div class="social-links">
                        <?php if (defined('INSTAGRAM')): ?>
                        <a href="https://instagram.com/<?php echo INSTAGRAM; ?>" target="_blank" title="Instagram">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <?php endif; ?>
                        
                        <?php if (defined('FACEBOOK')): ?>
                        <a href="https://facebook.com/<?php echo FACEBOOK; ?>" target="_blank" title="Facebook">
                            <i class="fab fa-facebook"></i>
                        </a>
                        <?php endif; ?>
                        
                        <a href="https://wa.me/<?php echo WHATSAPP_NUMBER; ?>" target="_blank" title="WhatsApp">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                    </div>
                </div>

                <!-- Hızlı Linkler -->
                <div class="footer-column">
                    <h3>Hızlı Linkler</h3>
                    <ul class="footer-links">
                        <li><a href="index.php">Ana Sayfa</a></li>
                        <li><a href="products.php">Ürünlerimiz</a></li>
                        <li><a href="index.php#about">Hakkımızda</a></li>
                        <li><a href="index.php#gallery">Galeri</a></li>
                        <li><a href="index.php#contact">İletişim</a></li>
                    </ul>
                </div>

                <!-- Ürün Kategorileri -->
                <div class="footer-column">
                    <h3>Kategoriler</h3>
                    <ul class="footer-links">
                        <?php 
                        $count = 0;
                        foreach ($categories as $key => $cat): 
                            if ($count < 5): // Sadece ilk 5 kategoriyi göster
                        ?>
                        <li><a href="products.php?category=<?php echo $key; ?>"><?php echo $cat['name']; ?></a></li>
                        <?php 
                            $count++;
                            endif;
                        endforeach; 
                        ?>
                    </ul>
                </div>

                <!-- İletişim Bilgileri -->
                <div class="footer-column">
                    <h3>İletişim</h3>
                    <ul class="footer-contact">
                        <li>
                            <i class="fas fa-phone"></i>
                            <a href="tel:<?php echo str_replace(' ', '', PHONE_NUMBER); ?>"><?php echo PHONE_NUMBER; ?></a>
                        </li>
                        <li>
                            <i class="fas fa-envelope"></i>
                            <a href="mailto:<?php echo EMAIL; ?>"><?php echo EMAIL; ?></a>
                        </li>
                        <li>
                            <i class="fas fa-map-marker-alt"></i>
                            <span><?php echo ADDRESS; ?></span>
                        </li>
                        <li>
                            <i class="fas fa-clock"></i>
                            <span><?php echo WORKING_HOURS; ?></span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Copyright -->
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. Tüm hakları saklıdır.</p>
                <p>Doğal çiçeklerle hayatınıza renk katın 🌸</p>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script src="assets/js/main.js"></script>
</body>
</html>

