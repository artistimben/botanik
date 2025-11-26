# 🌸 Papatya Botanik - Web Sitesi

Papatya Botanik işletmesi için özel olarak tasarlanmış, modern ve responsive web sitesi.

## ✨ Özellikler

- 🎨 **Modern Tasarım**: Botanik temalı, doğal renk paleti
- 📱 **Responsive**: Mobil, tablet ve masaüstü uyumlu
- 🚀 **Hızlı ve Performanslı**: Optimize edilmiş kod yapısı
- 🌿 **Dinamik Galeri**: Kategorilere göre otomatik ürün listeleme
- 💬 **WhatsApp Entegrasyonu**: Direkt sipariş ve iletişim
- 📞 **Telefon Entegrasyonu**: Tek tıkla arama
- 🖼️ **Lightbox**: Resimleri büyütme özelliği
- ⚡ **Smooth Animations**: Yumuşak geçişler ve animasyonlar
- 📦 **Video Desteği**: Ürün videoları gösterimi

## 📁 Klasör Yapısı

```
papatyabotabik2/
│
├── index.php                 # Ana sayfa
├── products.php              # Ürünler sayfası
│
├── includes/                 # PHP include dosyaları
│   ├── config.php           # Site yapılandırması
│   ├── header.php           # Sayfa başlığı
│   └── footer.php           # Sayfa alt bilgisi
│
├── assets/                   # Statik dosyalar
│   ├── css/
│   │   └── style.css        # Ana stil dosyası
│   └── js/
│       └── main.js          # Ana JavaScript dosyası
│
└── images/                   # Görseller
    ├── GÖRSELLER/           # Ürün görselleri (kategorilere göre)
    ├── LOGO/                # Logo dosyaları
    ├── önecıkanlar/         # Öne çıkan görseller
    └── Yeni Görsel/         # Yeni fotoğraflar
```

## 🚀 Kurulum

### 1. Dosyaları Yerleştirin
Tüm dosyalar zaten `C:\xampp\htdocs\botanik\papatyabotabik2\` dizininde.

### 2. XAMPP'ı Başlatın
- XAMPP Control Panel'i açın
- Apache'yi başlatın
- (Opsiyonel) MySQL'i başlatın (gelecekte veritabanı kullanımı için)

### 3. İletişim Bilgilerini Güncelleyin

`includes/config.php` dosyasını açın ve kendi bilgilerinizi girin:

```php
// İletişim Bilgileri
define('PHONE_NUMBER', '0555 123 45 67');        // ← Telefon numaranızı buraya yazın
define('WHATSAPP_NUMBER', '905551234567');        // ← WhatsApp için 90 ile başlayan format
define('EMAIL', 'info@papatyabotanik.com');      // ← E-posta adresiniz
define('ADDRESS', 'Örnek Mah., Çiçek Sok. No:1'); // ← Adresiniz

// Sosyal Medya
define('INSTAGRAM', 'papatyabotanik');            // ← Instagram kullanıcı adınız
define('FACEBOOK', 'papatyabotanik');             // ← Facebook sayfa adınız
```

### 4. Siteyi Açın

Tarayıcınızda şu adresi açın:
```
http://localhost/botanik/papatyabotabik2/
```

## 📱 Sayfalar

### Ana Sayfa (`index.php`)
- Hero slider
- Kategori kartları
- Hakkımızda bölümü
- Galeri önizlemesi
- İletişim formu
- Sabit WhatsApp butonu

### Ürünler Sayfası (`products.php`)
- Kategori filtreleri
- Tüm ürünlerin listesi
- Her ürün için:
  - Resim büyütme (lightbox)
  - WhatsApp ile sipariş
  - Telefon ile sipariş
- Video desteği

## 🎨 Renk Paleti

Site, botanik/doğal temalı renkler kullanır:

- **Primary (Ana Renk)**: `#2d5016` - Koyu yeşil
- **Secondary (İkincil)**: `#6b8e23` - Zeytin yeşili
- **Accent (Vurgu)**: `#f4a460` - Sandy brown (toprak rengi)
- **Light (Açık)**: `#f8f9f5` - Açık krem
- **Success (WhatsApp)**: `#25d366` - WhatsApp yeşili

## 💡 Kullanım

### Yeni Ürün Görseli Eklemek

1. Ürün fotoğrafınızı ilgili kategoriye ekleyin:
   ```
   images/GÖRSELLER/[KATEGORİ_ADI]/yeni-urun.jpg
   ```

2. Site otomatik olarak yeni görseli algılayacak ve listeye ekleyecektir.

### Yeni Kategori Eklemek

1. `images/GÖRSELLER/` klasörü altında yeni klasör oluşturun
2. `includes/config.php` dosyasındaki `$categories` dizisine yeni kategori ekleyin:

```php
'yeni-kategori' => [
    'name' => 'Yeni Kategori',
    'folder' => 'YENİ_KLASOR_ADI',
    'icon' => '🌺',  // Kategori ikonu (emoji)
    'description' => 'Kategori açıklaması'
]
```

## 📞 İletişim Özellikleri

### WhatsApp Entegrasyonu
- Sabit floating buton (sağ altta)
- Her üründe WhatsApp sipariş butonu
- İletişim formundan WhatsApp'a yönlendirme
- Otomatik mesaj şablonları

### Telefon Entegrasyonu
- Header'da "Hemen Ara" butonu
- Her üründe telefon butonu
- Tek tıkla arama

## 🔧 Özelleştirme

### CSS Değişkenleri

`assets/css/style.css` dosyasındaki CSS değişkenlerini düzenleyerek renkleri değiştirebilirsiniz:

```css
:root {
    --color-primary: #2d5016;
    --color-secondary: #6b8e23;
    --color-accent: #f4a460;
    /* ... diğer değişkenler */
}
```

### Logo Değiştirme

Logo dosyasını değiştirmek için:
1. Yeni logo dosyanızı `images/LOGO/` klasörüne ekleyin
2. `includes/header.php` dosyasında logo yolunu güncelleyin

## 🌐 Tarayıcı Desteği

- ✅ Chrome (önerilen)
- ✅ Firefox
- ✅ Safari
- ✅ Edge
- ✅ Mobil tarayıcılar

## 📊 Performans İpuçları

1. **Resim Optimizasyonu**: Büyük görselleri 1920px genişliğe kadar optimize edin
2. **Lazy Loading**: Resimler otomatik olarak lazy load edilir
3. **Caching**: Tarayıcı önbellekleme etkin
4. **Minification**: Canlı ortama almadan önce CSS/JS dosyalarını minify edin

## 🆘 Sorun Giderme

### Resimler Görünmüyor
- Dosya yollarını kontrol edin
- Resim dosya adlarında Türkçe karakter olmamasına dikkat edin
- Dosya izinlerini kontrol edin

### WhatsApp Butonu Çalışmıyor
- `includes/config.php` dosyasındaki WhatsApp numarasını kontrol edin
- Format: `905551234567` (90 ile başlamalı, boşluk olmamalı)

### Mobil Menü Açılmıyor
- JavaScript dosyasının yüklendiğinden emin olun
- Tarayıcı konsolunda hata olup olmadığını kontrol edin

## 📝 Güncellemeler

### Versiyon 1.0 (İlk Sürüm)
- ✅ Ana sayfa tasarımı
- ✅ Ürünler sayfası
- ✅ 8 ürün kategorisi
- ✅ WhatsApp entegrasyonu
- ✅ Responsive tasarım
- ✅ Lightbox galeri
- ✅ İletişim formu
- ✅ Smooth animasyonlar

## 🎯 Gelecek Özellikler (Opsiyonel)

- [ ] Admin paneli
- [ ] Ürün yönetim sistemi
- [ ] Online sipariş sistemi
- [ ] Müşteri kayıt sistemi
- [ ] Blog/Haberler bölümü
- [ ] Çoklu dil desteği
- [ ] SEO optimizasyonu

## 👨‍💻 Geliştirici Notları

### Kod Yapısı
- **PHP**: Modüler yapı (includes sistemi)
- **CSS**: CSS Variables + BEM benzeri isimlendirme
- **JavaScript**: Vanilla JS (framework kullanılmadı)
- **Responsive**: Mobile-first yaklaşım

### Kod Yorumları
Tüm kodlar Türkçe yorumlarla açıklanmıştır. Kullanım örnekleri kod içinde mevcuttur.

## 📄 Lisans

Bu proje Papatya Botanik için özel olarak geliştirilmiştir.

## 🙏 Destek

Sorularınız için:
- 📧 E-posta ile iletişime geçin
- 💬 WhatsApp üzerinden ulaşın
- 📞 Telefon ile arayın

---

**🌸 Papatya Botanik - Doğanın güzelliğini sevdiklerinizle paylaşın!**

