# Proje İnceleme Raporu

**Proje:** ilan.limontasarim.net — Limon Tasarım İlan Scripti  
**İncelenen dizin:** `C:\wamp64\www\ilan.limontasarim.net`  
**Rapor tarihi:** 2026-10-18  

---

## 1. Genel Bakış

Proje, PHP ve MySQL (MariaDB) tabanlı, "Limon CMS" altyapısı üzerine kurulmuş bir **ilan / classified ads** web uygulamasıdır. Ziyaretçi tarafı (`index.php`, `kategori.php`, `icerik.php`, `profil.php`, `magaza.php`, `ara.php`) ile yönetici paneli (`lpanel/`) tek kod tabanında birlikte çalışır. Tema desteği (`temalar/limon_ilan`) ve eklenti sistemi (`eklentiler/`) mevcuttur.

- PHP dosyası: **408**
- Toplam PHP kod satırı: **~71.162**
- Veritabanı tablosu: **24** (`ajax_upload`, `ayarlar`, `icerik`, `kategoriler`, `uyeler`, `yorumlar`, vb.)
- Desteklenen diller: Türkçe, İngilizce, Fransızca, Arapça (`lang/`)
- Apache rewrite ile SEO dostu URL'ler (`.htaccess`)

---

## 2. Teknoloji Yığını

| Katman | Teknoloji |
|---|---|
| Backend | PHP (hedef PHP 8.4, orijinal 7.2) |
| Veritabanı | MySQL/MariaDB 10.2, `mysqli` uzantısı |
| Frontend | HTML/CSS, Bootstrap, jQuery 1.9/3.2/3.3, CKEditor, DataTables |
| Görsel işleme | SimpleImage, ImageResize (GD) |
| Ödeme | İyzico SDK (`temalar/limon_ilan/iyzico`) |
| E-posta | PHPMailer (SMTP eklentisi) |
| Web sunucu | Apache (WAMP), mod_rewrite |

---

## 3. Dizin Yapısı

```
ilan.limontasarim.net/
├── index.php, icerik.php, kategori.php, profil.php, magaza.php, ara.php, 404.php
├── loader.php              # Bootstrap: DB, oturum, eklenti/tema yükleme
├── config/                 # db.php, yapilandir.php, fonksiyonlar*.php, dil.php
├── lang/                   # tr, en, fr, ar
├── lpanel/                 # Yönetim paneli (admin_dizini varsayılan)
│   ├── inc/, js/, lib/
├── temalar/limon_ilan/     # Aktif tema + tema fonksiyonları
├── eklentiler/             # Eklenti modülleri (auto_mail, xml_sitemap, mysql_yedek, ...)
├── uploads/                # Kullanıcı yüklemeleri (profil, kapak, ilan görselleri)
├── SQL/localhost.sql       # Veritabanı şeması ve örnek veri
└── .htaccess
```

---

## 4. Temel İşlevsellik

- **İlan yönetimi:** İlan ekleme/düzenleme, kategori ağacı, galeri, vitrin.
- **Üye sistemi:** Kayıt/giriş, profil, otomatik giriş (cookie), oturum değiştirme (admin → kullanıcı).
- **Mağaza modülü:** Mağaza profili, kapak, plan/doping.
- **Yorum sistemi:** Onaylı/onaysız yorumlar, yasaklı kelime filtresi.
- **Yönetim paneli:** İçerik, kategori, üye, menü, tema, eklenti, ayar, bildirim, hata raporu yönetimi.
- **Eklentiler:** Auto mail, XML sitemap, DB optimize/update, favoriler, postviews, MySQL yedek.
- **Çoklu dil:** UI etiketleri ve statik sayfa gövdeleri dil bazlı.

---

## 5. Güvenlik Bulguları

> **Not:** `not.txt` dosyasında "Tarama tamamlandı; exploit bulunamadı" ifadesi yer almaktadır. Ancak statik kod incelemesi, yapısal güvenlik açıklarının yaygın olduğunu göstermektedir. Aşağıdaki bulgular, **kod yapısından** tespit edilmiştir; dinamik sömürülebilirlik, girdi noktalarına ve yetkilendirmeye bağlıdır.

### 5.1 Kritik / Yüksek

| # | Bulgu | Konum | Risk |
|---|---|---|---|
| 1 | **SQL Injection (yapısal)** — Sorgular string birleştirme ile kuruluyor; prepared statement kullanılmıyor. `$_GET` yalnızca `addslashes`/`strip_tags` ile filtreleniyor, `$_POST` `tirnak_replace` ile işleniyor (SQL için yetersiz). | `config/fonksiyonlar.php` (çoğu fonksiyon), `loader.php:64-68` | Yüksek |
| 2 | **MD5 ile parola saklama** — `uye_sorgula()` parolayı `md5()` ile karşılaştırıyor. MD5 kriptografik olarak kırık; rainbow table / GPU saldırısına açık. | `config/fonksiyonlar.php:1232` | Yüksek |
| 3 | **Auto-login cookie SQLi** — Cookie'den alınan `$auto_login_key` doğrudan sorguya gömülüyor; escape edilmiyor. | `loader.php:193-195` | Yüksek |
| 4 | **XSS (stored/reflected)** — DB'den gelen değerler ve form alanları `htmlspecialchars` olmadan echo ediliyor (`form_elemani`, menü, yorum, ilan başlıkları). | `config/fonksiyonlar.php` (form_*, menu_getir vb.) | Yüksek |
| 5 | **CSRF koruması yok** — Formlarda CSRF token bulunmuyor; oturum açıkken çapraz site istekleri ile durum değiştirilebilir. | Tüm formlar | Orta-Yüksek |
| 6 | **Dosya yükleme doğrulaması zayıf** — Yalnızca uzantı kontrolü var; MIME/İçerik doğrulaması yok. `resim_yukle` image uzantılarına izin veriyor, ancak fileuploader/CKEditor upload yolları ayrı kontrol gerektirir. | `config/fonksiyonlar.php:2205+`, `lpanel/js/fileuploader/class.fileuploader.php` | Orta |
| 7 | **Yol geçişi (path traversal) riski** — `sil()` fonksiyonu URL'den `/uploads/` sonrası kısmı alıp `unlink` ediyor; manipüle edilmiş URL ile keyfi dosya silme potansiyeli. | `config/fonksiyonlar.php:2305` | Orta |
| 8 | **Kimlik bilgileri kaynakta açık** — `config/db.php` içinde DB host/kullanıcı/parola düz metin. PHP çalıştığı sürece kaynak görünmez, ancak yedek/versiyon kontrolü veya PHP devre dışı kalırsa sızar. | `config/db.php` | Orta |
| 9 | **Kaynakta lisans/uzaktan güncelleme** — `guncellemeler.php`, `limonsoft()` ve sürüm kontrolü uzaktan servislerle konuşuyor; MITM/compromise riski (HTTPS doğrulaması kontrol edilmeli). | `config/fonksiyonlar.php:4+`, `loader.php:59-63` | Orta |

### 5.2 Orta / Düşük

| # | Bulgu | Konum | Risk |
|---|---|---|---|
| 10 | **`get_security` eski API** — `addSlashes` (deprecated) ve `strip_tags`; encoding-aware escaping değil. | `loader.php:65-68` | Orta |
| 11 | **`$_POST` quote replacement** — `tirnak_replace` quote'ları HTML entity'ye çevirir; bu bir güvenlik katmanı değil, veri bütünlüğünü bozabilir. | `loader.php:64` | Düşük |
| 12 | **Hata raporlama kapalı** — `display_errors=0`, `mysqli_report(OFF)`. Üretim için doğru, ancak hatalar sessizce yutuluyor; izleme zor. | `loader.php:4-7,34` | Düşük |
| 13 | **Sınırsız kaynak** — `memory_limit=-1`, `max_execution_time=120`; kötü niyetli/büyük isteklerde DoS riski. | `loader.php:10-11` | Düşük |
| 14 | **Admin yolu tahmin edilebilir** — Varsayılan `lpanel`; `admin_dizini` ayarı ile değiştirilebilir ama varsayılan yaygın. | `config/yapilandir.php:77` | Düşük |
| 15 | **Üçüncü parti bileşenler** — CKEditor, jQuery eski sürümler; bilinen CVE'ler olabilir (güncelleme önerilir). | `lpanel/js/*` | Orta |
| 16 | **Eklenti/tema include** — DB'deki aktif eklenti/tema yolları `include` ile yükleniyor; DB veya dosya sistemi ele geçirilirse RCE. | `loader.php:109-149` | Orta (yetki gerektirir) |

---

## 6. Performans ve Bakım

- **N+1 sorgu riski:** Kategori/alt kategori listeleme fonksiyonları recursive ve her seviyede ayrı sorgu çalıştırıyor (`alt_kategori_listele`, `alt_link_listele`).
- **Global state:** Çok sayıda `global $baglan` kullanımı; test edilebilirlik düşük.
- **Uzun fonksiyonlar:** `config/fonksiyonlar.php` ~1600+ satır; tek dosyada yüzlerce fonksiyon.
- **PHP sürüm uyumsuzluğu:** Kod PHP 7.2 için yazılmış, 8.4 hedefleniyor; `mysqli_fetch_array` üzerinde TypeError riski kısmen yamalanmış (yorum blokları mevcut).
- **Yedek/veri:** `mysql_yedek` eklentisi tablo adlarını `SHOW TABLES`'tan alıyor (güvenli), ancak çıktıda `addslashes` kullanımı eski.

---

## 7. Öneriler (Öncelik Sırası)

1. **SQLi kapatma:** Tüm DB erişimini PDO/mysqli prepared statements ile yeniden yaz; `$_GET/$_POST/$_COOKIE` doğrudan sorguya girmesin.
2. **Parola güvenliği:** MD5 yerine `password_hash()` / `password_verify()` (bcrypt/argon2); mevcut MD5 hash'lerini geçişte yeniden hash'le.
3. **XSS:** Çıktı noktalarında `htmlspecialchars($x, ENT_QUOTES, 'UTF-8')` uygula; özellikle kullanıcı girdisi içeren alanlar.
4. **CSRF:** Formlara token ekle, `SameSite` cookie ayarla.
5. **Dosya yükleme:** Uzantı + MIME + `getimagesize` doğrulaması; yükleme dizininde PHP çalıştırmasını engelle (`.htaccess` / `php_flag engine off`).
6. **Kimlik bilgileri:** `config/db.php`'yi web kökü dışına taşı veya `.htaccess` ile erişimi engelle; ortam değişkenlerini kullan.
7. **Oturum güvenliği:** `session_regenerate_id(true)`, güvenli cookie flags (`HttpOnly`, `Secure`, `SameSite`), auto-login token'ı hash'li sakla.
8. **Bağımlılıklar:** CKEditor/jQuery/fileuploader sürümlerini güncel tut; bilinen CVE'leri tara.
9. **Mimari:** Fonksiyonları modüllere böl, autoloader kullan, temel CRUD için repository katmanı.
10. **İzleme:** Üretimde yapılandırılmış loglama ve hata takibi (Sentry vb.) ekle; `display_errors` kapalı kalsın.

---

## 8. Sonuç

Proje, işlevsel olarak kapsamlı bir ilan platformu sunmaktadır. Ancak **güvenlik mimarisi modern standartların gerisindedir**: SQL injection yüzeyi geniş, parola saklama MD5 ile yapılmakta, XSS/CSRF korumaları eksik. `not.txt`'deki "exploit bulunamadı" ifadesi, dinamik bir taramanın sonucu olabilir; statik inceleme **yapısal risklerin** mevcut olduğunu göstermektedir. Üretim ortamına alınmadan önce en azından SQLi, parola hash ve XSS düzeltmeleri yapılmalıdır.

---

*Rapor otomatik statik inceleme ile hazırlanmıştır; dinamik test (pentest) ile doğrulanması önerilir.*
