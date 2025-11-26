/**
 * Papatya Botanik - Ana JavaScript Dosyası
 * Tüm interaktif özellikler ve animasyonlar
 */

// ========================================
// 1. DOM Yüklendikten Sonra Çalışacak Kodlar
// ========================================
document.addEventListener('DOMContentLoaded', function() {
    
    // Mobil menü toggle
    initMobileMenu();
    
    // Scroll animasyonları
    initScrollAnimations();
    
    // Lightbox (resim büyütme) işlevleri
    initLightbox();
    
    // Contact form
    initContactForm();
    
    // Smooth scroll
    initSmoothScroll();
    
    // Navbar scroll efekti
    initNavbarScroll();
    
    console.log('Papatya Botanik - Site yüklendi ✓');
});

// ========================================
// 2. Mobil Menü İşlevleri
// ========================================
function initMobileMenu() {
    const mobileMenuToggle = document.getElementById('mobileMenuToggle');
    const navMenu = document.getElementById('navMenu');
    
    if (mobileMenuToggle && navMenu) {
        mobileMenuToggle.addEventListener('click', function() {
            navMenu.classList.toggle('active');
            this.classList.toggle('active');
        });
        
        // Menü dışına tıklandığında menüyü kapat
        document.addEventListener('click', function(e) {
            if (!navMenu.contains(e.target) && !mobileMenuToggle.contains(e.target)) {
                navMenu.classList.remove('active');
                mobileMenuToggle.classList.remove('active');
            }
        });
        
        // Menü linkine tıklandığında menüyü kapat
        const navLinks = navMenu.querySelectorAll('.nav-link');
        navLinks.forEach(link => {
            link.addEventListener('click', function() {
                navMenu.classList.remove('active');
                mobileMenuToggle.classList.remove('active');
            });
        });
    }
}

// ========================================
// 3. Scroll Animasyonları (AOS benzeri) - DEVRE DIŞI (Performans için)
// ========================================
function initScrollAnimations() {
    // PERFORMANS İYİLEŞTİRMESİ: Scroll animasyonları devre dışı
    // Çok fazla ürün olduğunda kasıyor, bu yüzden kapatıldı
    
    // Tüm elementlere hemen aos-animate sınıfı ekle (animasyon yok, direkt göster)
    const animatedElements = document.querySelectorAll('[data-aos]');
    animatedElements.forEach(element => {
        element.classList.add('aos-animate');
        element.removeAttribute('data-aos'); // Temizle
    });
    
    return; // Animasyon yok, direkt çık
    
    /* ESKI KOD - GEREKİRSE AKTİF EDİN
    if (animatedElements.length === 0) return;
    
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('aos-animate');
            }
        });
    }, observerOptions);
    
    animatedElements.forEach(element => {
        observer.observe(element);
    });
    */
}

// ========================================
// 4. Lightbox (Resim Büyütme) İşlevleri
// ========================================
let currentImageSrc = '';

function initLightbox() {
    const lightbox = document.getElementById('lightbox');
    const lightboxImg = document.getElementById('lightbox-img');
    const lightboxClose = document.querySelector('.lightbox-close');
    const lightboxWhatsapp = document.getElementById('lightbox-whatsapp');
    
    if (!lightbox) return;
    
    // Lightbox'ı kapat
    if (lightboxClose) {
        lightboxClose.addEventListener('click', closeLightbox);
    }
    
    // Lightbox dışına tıklandığında kapat
    lightbox.addEventListener('click', function(e) {
        if (e.target === lightbox) {
            closeLightbox();
        }
    });
    
    // ESC tuşu ile kapat
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && lightbox.classList.contains('active')) {
            closeLightbox();
        }
    });
}

// Lightbox'ı aç
function openLightbox(imageSrc) {
    const lightbox = document.getElementById('lightbox');
    const lightboxImg = document.getElementById('lightbox-img');
    const lightboxWhatsapp = document.getElementById('lightbox-whatsapp');
    
    if (lightbox && lightboxImg) {
        currentImageSrc = imageSrc;
        lightboxImg.src = imageSrc;
        lightbox.classList.add('active');
        document.body.style.overflow = 'hidden';
        
        // WhatsApp butonunu güncelle
        if (lightboxWhatsapp) {
            const imageName = imageSrc.split('/').pop();
            lightboxWhatsapp.href = `https://wa.me/${getWhatsAppNumber()}?text=Merhaba, ${encodeURIComponent(imageName)} hakkında bilgi almak istiyorum`;
        }
    }
}

// Lightbox'ı kapat
function closeLightbox() {
    const lightbox = document.getElementById('lightbox');
    
    if (lightbox) {
        lightbox.classList.remove('active');
        document.body.style.overflow = '';
    }
}

// ========================================
// 5. WhatsApp Sipariş Fonksiyonları
// ========================================
function orderViaWhatsApp(categoryName, productName) {
    const message = `Merhaba, ${categoryName} - ${productName} hakkında bilgi almak istiyorum`;
    const whatsappUrl = `https://wa.me/${getWhatsAppNumber()}?text=${encodeURIComponent(message)}`;
    window.open(whatsappUrl, '_blank');
}

function orderViaCall(categoryName) {
    const phoneNumber = getPhoneNumber();
    window.location.href = `tel:${phoneNumber}`;
}

// Config'den WhatsApp numarasını al (PHP'den gelen değer)
function getWhatsAppNumber() {
    return window.siteConfig ? window.siteConfig.whatsappNumber : '905551234567';
}

function getPhoneNumber() {
    return window.siteConfig ? window.siteConfig.phoneNumber : '05551234567';
}

// ========================================
// 6. Kategori Filtreleme (Ürünler Sayfası)
// ========================================
function filterCategory(category) {
    const categorySections = document.querySelectorAll('.category-section');
    const filterBtns = document.querySelectorAll('.filter-btn');
    
    // Tüm kategori butonlarından active sınıfını kaldır
    filterBtns.forEach(btn => {
        btn.classList.remove('active');
    });
    
    // Tıklanan butona active sınıfı ekle
    event.target.classList.add('active');
    
    // Kategorileri göster/gizle
    if (category === 'all') {
        categorySections.forEach(section => {
            section.style.display = 'block';
            // Animasyon için
            section.style.animation = 'fadeInUp 0.5s ease';
        });
    } else {
        categorySections.forEach(section => {
            if (section.dataset.category === category) {
                section.style.display = 'block';
                section.style.animation = 'fadeInUp 0.5s ease';
            } else {
                section.style.display = 'none';
            }
        });
    }
    
    // URL'i güncelle (sayfa yenilenmeden)
    const url = new URL(window.location);
    if (category === 'all') {
        url.searchParams.delete('category');
    } else {
        url.searchParams.set('category', category);
    }
    window.history.pushState({}, '', url);
    
    // Sayfayı yukarı kaydır
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

// ========================================
// 7. İletişim Formu
// ========================================
function initContactForm() {
    const contactForm = document.getElementById('contactForm');
    
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Form verilerini al
            const formData = new FormData(contactForm);
            const name = contactForm.querySelector('input[type="text"]').value;
            const phone = contactForm.querySelector('input[type="tel"]').value;
            const message = contactForm.querySelector('textarea').value;
            
            // WhatsApp mesajı oluştur
            const whatsappMessage = `
Yeni İletişim Formu:
-------------------
Ad Soyad: ${name}
Telefon: ${phone}
Mesaj: ${message}
            `.trim();
            
            // WhatsApp'a yönlendir
            const whatsappUrl = `https://wa.me/${getWhatsAppNumber()}?text=${encodeURIComponent(whatsappMessage)}`;
            window.open(whatsappUrl, '_blank');
            
            // Formu temizle
            contactForm.reset();
            
            // Başarı mesajı göster
            showNotification('Mesajınız WhatsApp üzerinden iletilecek!', 'success');
        });
    }
}

// ========================================
// 8. Bildirim Gösterme
// ========================================
function showNotification(message, type = 'info') {
    // Basit bir notification sistemi
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 1rem 1.5rem;
        background: ${type === 'success' ? '#4caf50' : '#2196f3'};
        color: white;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        z-index: 10000;
        animation: slideInRight 0.3s ease;
    `;
    
    document.body.appendChild(notification);
    
    // 3 saniye sonra kaldır
    setTimeout(() => {
        notification.style.animation = 'slideOutRight 0.3s ease';
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 3000);
}

// ========================================
// 9. Smooth Scroll (Yumuşak Kaydırma)
// ========================================
function initSmoothScroll() {
    // Tüm # ile başlayan linklere smooth scroll ekle
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            
            // Sadece # değilse
            if (href !== '#') {
                const targetId = href.substring(1);
                const targetElement = document.getElementById(targetId);
                
                if (targetElement) {
                    e.preventDefault();
                    
                    const navbarHeight = document.querySelector('.main-header').offsetHeight;
                    const targetPosition = targetElement.offsetTop - navbarHeight;
                    
                    window.scrollTo({
                        top: targetPosition,
                        behavior: 'smooth'
                    });
                }
            }
        });
    });
}

// ========================================
// 10. Navbar Scroll Efekti
// ========================================
function initNavbarScroll() {
    const navbar = document.querySelector('.main-header');
    let lastScroll = 0;
    
    if (!navbar) return;
    
    window.addEventListener('scroll', function() {
        const currentScroll = window.pageYOffset;
        
        // Aşağı scroll - shadow ekle
        if (currentScroll > 100) {
            navbar.style.boxShadow = '0 4px 12px rgba(0,0,0,0.1)';
        } else {
            navbar.style.boxShadow = '0 2px 4px rgba(0,0,0,0.1)';
        }
        
        lastScroll = currentScroll;
    });
}

// ========================================
// 11. Lazy Loading Images (Performans için)
// ========================================
function initLazyLoading() {
    const images = document.querySelectorAll('img[loading="lazy"]');
    
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src || img.src;
                    img.classList.add('loaded');
                    imageObserver.unobserve(img);
                }
            });
        });
        
        images.forEach(img => imageObserver.observe(img));
    }
}

// ========================================
// 12. Scroll Indicator (Scroll progress bar)
// ========================================
function initScrollIndicator() {
    const scrollIndicator = document.querySelector('.scroll-indicator');
    
    if (scrollIndicator) {
        scrollIndicator.addEventListener('click', function() {
            window.scrollTo({
                top: window.innerHeight,
                behavior: 'smooth'
            });
        });
    }
}

// Sayfa yüklendiğinde scroll indicator'ı başlat
initScrollIndicator();

// ========================================
// 13. Utility Fonksiyonlar
// ========================================

// Telefon numarasını formatla
function formatPhoneNumber(phone) {
    return phone.replace(/\s+/g, '');
}

// URL parametresini al
function getUrlParameter(name) {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(name);
}

// ========================================
// 14. Page Load Optimizations
// ========================================

// Sayfa tamamen yüklendiğinde
window.addEventListener('load', function() {
    // Lazy loading'i başlat
    initLazyLoading();
    
    // Preloader varsa kaldır
    const preloader = document.querySelector('.preloader');
    if (preloader) {
        preloader.style.opacity = '0';
        setTimeout(() => {
            preloader.style.display = 'none';
        }, 300);
    }
});

// ========================================
// 15. CSS Animasyon Keyframes (dinamik)
// ========================================
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
`;
document.head.appendChild(style);

// ========================================
// 16. Console Log Styling
// ========================================
console.log(
    '%c🌸 Papatya Botanik %c',
    'background: #2d5016; color: white; padding: 10px 20px; border-radius: 5px; font-size: 16px; font-weight: bold;',
    ''
);
console.log(
    '%cWeb sitesi başarıyla yüklendi! 🌿',
    'color: #6b8e23; font-size: 14px; font-weight: bold;'
);

// ========================================
// Global fonksiyonları dışa aktar (window'a ekle)
// ========================================
window.openLightbox = openLightbox;
window.closeLightbox = closeLightbox;
window.filterCategory = filterCategory;
window.orderViaWhatsApp = orderViaWhatsApp;
window.orderViaCall = orderViaCall;

