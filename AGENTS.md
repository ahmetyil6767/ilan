# AGENTS.md — JaayQuick (Gambiya İlan Panosu)

Bu dosya OpenCode ve bu repoda çalışan diğer AI ajanları için proje talimatıdır.
Depoya commit edilir; herkes aynı kuralları alır.

## Proje nedir

**JaayQuick** (`jaayquick.com`) — PHP + MySQL tabanlı **ilan / classified ads** uygulaması.
"JaayQuick" altyapısı üzerine kurulu; ziyaretçi arayüzü ve yönetim paneli
**tek kod tabanında** duruyor. Composer, npm, paket yöneticisi **yoktur** —
bağımlılıklar klasör içine kopyalanmış halde gelir.

- **PHP:** 7.2 için yazıldı, hedef sürüm **8.4** (yerelde 8.4.15 CLI kurulu)
- **DB:** MariaDB / MySQL, `mysqli` eklentisi, `utf8mb4`, veritabanı `jaayquick`
- **Ölçek:** ~228 PHP dosyası (3. parti hariç), ~56.000 satır, 26 tablo
- **URL:** Apache `mod_rewrite` ile SEO dostu (`.htaccess`)

### Ülke / marka bağlamı — Gambiya

Site **Gambiya** için çalışır; Türkiye'ye özgü hiçbir veri kalmamalıdır.

| Konu | Değer |
|---|---|
| Marka | JaayQuick (`jaayquick.com`) — `site_baslik` ayarı "JaayQuick" |
| İletişim | `info@jaayquick.com`, `220 267 74 08` (uluslararası: `+220 267 74 08`) |
| Para birimi | **GMD (dalasi)** — DB'deki tutarlar GMD; `birim_goster()` ziyaretçi birimine çevirir |
| Kur kaynağı | `open.er-api.com` (1 USD = kaç GMD); eski TCMB/TRY kaynağı **kaldırıldı** |
| Telefon | `+220`; yerel numara 7 hane, ülke kodu **girdiğinde** yazılmaz (`s_telefon_sifir` ipucu) |
| Konum | `iller` = 8 Gambia bölgesi (Banjul, Kanifing, West/North Bank, Lower/Central/Upper River, Kuntaur), `ilceler` = 33 ilçe, **`mahalleler` boş — mahalle özelliği kaldırıldı** |
| Ülke kodu kaynakları | `temalar/jaayquick/iyzico/**` içindeki `setCountry("Turkey")` gibi eski değerler temizlendi |

**Kural:** Yeni kodu yazarken Türkiye'ye özgü varsayımlar kullanma (+90, TRY, "İl/İlçe/Mahalle"
terminolojisi, Türk bankası/IBAN'ı, Türkçe para birimi). Bölge: **Region / District**.

## Diller ve çeviri sistemi

Dört dil: **tr (kaynak), en, fr, ar**. Dil `dil` cookie'siyle değişir
(`?dil=xx` değil — çerez üzerinden). `config/dil.php` dil altyapısını kurar,
`aktif_dil()` aktif dili döndürür.

`lang/tr.php`, `en.php`, `fr.php`, `ar.php` — **anahtar kümeleri dört dosyada
birebir aynı olmalı** (test bunu doğrular).

### Çeviri mekanizmaları (hangisini nerede kullanacaksın)

| Mekanizma | Nerede | Not |
|---|---|---|
| `_LANG('anahtar')` | Kod içi tüm arayüz metinleri | Yeni metin: 4 dile de anahtar ekle |
| `spec_etiket('TR metin')` | Sözlük tabanlı **kategori alan etiketleri ve seçenek değerleri** (`lang/spec_a|b|c.php`) | TR metin → EN/FR/AR; kaynak metin sözlükte yoksa aynen görünür |
| `_DIL_BASLIK($baslik, $id, $tip)` | DB'deki başlıklar (`kategori`, `sayfa`, `linker`, `liste`, `bilesen`) | Çeviriler `ekstra_bilgi` içinde `<tip>_<id>` + `baslik_en/fr/ar` anahtarıyla durur |
| `kategori_bilgi($anahtar, $id)` | Kategori alan tanımları (`ozel_alanlar`, `filtreler`, `detaylar`, `teknik_ozellikler`, `guvenlik_ipuclari`) | Aktif dil için `<anahtar>_<dil>` kaydı varsa onu okur, yoksa TR kaynağını |
| `birim_goster($gmd)` | Fiyat gösterimi | Ziyaretçi birimi GMD/USD/EUR/GBP |

**Kural:** Kullanıcı içeriği **çevrilmez** (ilan başlıkları, mağaza/üye adları,
marka-model adları, ilan özellik *değerleri* kendi kaydıyla). Arayüz metinleri
her zaman çevrilir.

## Ödeme — havale / EFT

- **Aktif yöntem: banka havalesi (manuel onay).** `odeme_sistemi=havale`,
  `online_odeme=pasif`, `iyzico_odeme=pasif`.
- Akış: kullanıcı paket/doping/mağaza siparişi verir → `siparisler` kaydı
  (`durum=0`) → `siparis.php` havale bilgilerini ve **ödeme referansını**
  (`JA-<siparis_id>`) gösterir → kullanıcı "ödeme bildirimi" gönderir
  (`ajax.php?islem=odeme_bildirimi`) → yönetici panelden siparişi onaylar
  (`?onay=<id>`) → ilan/mağaza/doping/paket özellikleri aktive olur.
- Havale hesap bilgileri `listeler` tablosunda `tip='hesaplar'` kayıtlarında
  (`banka_adi`, `ad_soyad`, `iban`) — yönetim panelden güncellenir.
- **İyzico SDK'sı pasif durumda** (`temalar/jaayquick/iyzico/`). Kod
  `siparis.php` içinde hâlâ var ama `online_odeme=aktif` değilse çalışmaz.

## Çalıştırma ve doğrulama

```bash
# Yerel WAMP zaten http://ilan üzerinden servis ediyor (Host: ilan).
# Alternatif: php -S localhost:8000

# Testler (veritabanı bağlantısı gerekir, loader.php'i çeker)
php tests/LanguageSystemTest.php     # çıkış kodu 0 = hepsi geçti
php tests/BindParamTest.php          # prepared statement tip/değişken tutarlılığı
php tests/PanelFunctionTest.php      # lpanel CRUD işlev testi (admin oturumu gerekir)
php tests/PanelLanguageTest.php      # lpanel 4 dilde sayfa taraması
```

**Bir değişiklik yapıp bitirmeden önce şu sırayla doğrula:**

1. `php -l <değişen dosya>` — sözdizimi (her dosya için)
2. `php tests/LanguageSystemTest.php` — dil/para birimi testi (0 = geçti)
3. `php tests/BindParamTest.php` — `mysqli_stmt_bind_param` tip sayısı = değişken
   sayısı olmalı (aksi halde PHP 8 `ArgumentCountError` fırlatır ve sayfa
   sessizce HTTP 200 döner)
4. Değişiklik paneldeyse `php tests/PanelFunctionTest.php` (CRUD uçtan uca)
   ve `php tests/PanelLanguageTest.php` (4 dil × 12 sayfa → 0 sorun)
5. Tüm iç linkleri 4 dilde tarayan smoke test (ör. `tests/` altındaki tarama
   betikleri) → **PHP hatası olan sayfa sayısı 0** olmalı
6. Tarayıcıda gerçek kontrol (1-2 sayfa) — özellikle görünüm/JS değiştiyse
7. `otomatik_yedek.ps1` → commit

`cache/` altındaki HTML cache'i **bayatlayabilir** — değişiklik sonrası
`cache/` içeriğini sil, yoksa eski HTML sunulur.

## Yedek politikası — 2 gün

Değişiklik yapan her ajan oturum sonunda `otomatik_yedek.ps1`'i çalıştırır:

```powershell
powershell -ExecutionPolicy Bypass -File otomatik_yedek.ps1
```

- DB dump (`otomatik_db_*.sql`) + proje dosyalarının zip'i (`otomatik_dosyalar_*.zip`,
  `.git` ve `SQL/yedekler` hariç, `uploads` dahil) → `SQL\yedekler\` altına yazar.
- `otomatik_*` dosyalarını kendisi 2 güne kadar tutar, eskisini siler
  (`yedek_*.sql` adlı manuel geri yükleme noktalarına **dokunmaz**).
- Kayıt: `SQL\yedekler\yedek_log.txt`. Günlük zamanlanmış görev `JaayQuickYedek`
  (her gün 03:30) da aynı script'i koşturur.
- Ek olarak **her DB'de veri değiştiren adımdan önce** o adımı etkileyen tabloların
  ayrı `yedek_<isim>.sql` yedeği alınır.

## Veritabanı

`config/db.php` **repoda yok** (`.gitignore`'da). Yeni ortamda:

```bash
cp config/db.php.example config/db.php
# sonra host/veritabani/kullanici/parola alanlarını doldur
```

Şema kaynağı `SQL/localhost.sql` — o da `.gitignore`'da (üye parola özetleri ve
e-posta adresleri içeriyor).

Ayarlar (`ayarlar` tablosu) çoğunlukla **veritabanında** durur, kodda değil.
Tema ayarları `tema_ayar("anahtar")` ile okunur; API anahtarları de oradan gelir —
**sabit olarak koda yazma**.

## Dizin haritası

| Yol | Sorumluluk |
|---|---|
| `loader.php` | Bootstrap: hata ayarı, DB, oturum, eklenti/tema yükleme. Her istek buradan geçer. |
| `config/` | `db.php` (gizli), `yapilandir.php`, `fonksiyonlar*.php` (çekirdek), `dil.php`, `fonksiyonlar_meta.php` (SEO/çeviri yardımcıları) |
| `lang/` | `tr.php`, `en.php`, `fr.php`, `ar.php` + `spec_a|b|c.php` (sözlük) |
| `lpanel/` | Yönetim paneli. `admin_dizini` ayarıyla adı değiştirilebilir (varsayılan `lpanel`) |
| `temalar/jaayquick/` | Aktif tema + `bilesen/` + `panel/` (tema içi yönetim) + `iyzico/` (pasif SDK) |
| `eklentiler/` | Eklenti modülleri (auto_mail, xml_sitemap, mysql_yedek, favoriler...) |
| `uploads/` | Kullanıcı yüklemeleri. PHP çalıştırma `.htaccess` ile kapalı. |
| `SQL/` | Şema/dump + `SQL/yedekler/` (otomatik + manuel yedekler) |
| `tests/` | Otomatik testler |
| `cache/` | Sayfa HTML cache'i (üretimde; bayatlayabilir) |
| `AGENTS.md` | **Kurallar** (bu dosya) |
| `PROJECT_CONTEXT.md` | **Bilgi tabanı**: mimari, veri modeli, iş akışları, sorun giderme |
| `CLAUDE.md`, `GEMINI.md`, `.github/copilot-instructions.md` | Diğer araçlar için kısa yönlendirici |

**Üçüncü parti kod — elle değiştirme:**
`lpanel/js/ckeditor/**`, `eklentiler/smtp/phpmailer/**`,
`temalar/jaayquick/iyzico/src/**`.

## Kod kuralları

Mevcut üslubu koru. Refactor gereksiz yere genişletme.

- **Değişken ve fonksiyon adları Türkçe, snake_case:** `$baglan`, `$veritabani`,
  `$uye_ekstra_bilgiler`, `alt_kategori_listele()`, `form_elemani()`.
- **`array()` tercih edilir**, kısa `[]` sözdizimi az kullanılıyor.
- **Girinti:** dosyadan dosyaya değişiyor. Düzenlediğin dosyanın girintisini koru.
- **Dosya başında koruma:** `loader.php` ve `config/yapilandir.php` içinde
  `count(get_included_files())` kontrolü var. Yeni giriş noktası dosyası yazarsan
  aynısını ekle.
- **Yorumlar Türkçe.** `loader.php` Türkçe diyakritiksiz yazılmıştır (`icin`
  değil `için`) — o dosyada o üslubu sürdür.

### Kodlama (encoding) — dikkat

- Tüm dosyalar **UTF-8**. Yeni dosya **BOM'suz UTF-8** olmalı.
- Depoda BOM'lu dosyalar var (`lang/*.php` dahil) — bunlar "headers already sent"
  kırar. **Yeni dosyaya BOM ekleme**, mevcutlara dokunma.
- Dosyayı kaydederken `charset`'i değiştirme.

### Hata ayarı — hatalar ekrana yazılmaz

**Kural: hiçbir PHP hatası ekrana basılmaz.** Tüm hatalar sunucu loguna yazılır.

| Katman | Ayar | Nerede |
|---|---|---|
| Çalışma zamanı | `display_errors=0`, `html_errors=0`, `log_errors=1` + özel hata yakalayıcı | `loader.php` (ilk satırlar) |
| php.ini | `display_errors = Off`, `display_startup_errors = Off`, `html_errors = Off`, `xdebug.display_errors=0` | `C:\wamp64\bin\php\php8.4.15\php.ini` |
| WAMP vhost | `php_admin_value display_errors Off` + `display_startup_errors Off` + `html_errors Off` | `conf/extra/httpd-vhosts-ilan.conf` |

- `php_admin_value` **`ini_set()` ile ezilemez**; bu yüzden vhost ve php.ini
  mutlaka yukarıdaki gibi ayarlanmış olmalı. `loader.php` içindeki hata
  yakalayıcı ise Apache yeniden başlatılana kadar ekranı temiz tutar.
- Hata günlüğü: `C:\wamp64\logs\php_error.log` (paylaşılan; bu projenin
  kayıtları `[JaayQuick]` etiketiyle ayrılır).
- **Hata tespiti artık HTML'e değil loga bakarak yapılır:**
  `tests/PanelLanguageTest.php` testi logu okur ve yeni hataları raporlar.
- Geliştirme sırasında hataları görmek istersen
  `config/hata_goster.php` dosyası oluşturup içine
  `<?php define('JAÄYQUICK_HATA_EKRANA', true);` yaz (vhost/php.ini de
  `display_errors` açık olmalı).
- CLI (geliştirme araçları) hataları ekranda görmeye devam eder.

## Güvenlik kuralları

`PROJE_RAPORU.md` bu kod tabanındaki 16 bilinen açığı listeler ve her oturumda
yüklenir. **Yeni açık ekleme.**

1. **SQL — string birleştirme yapma.** Sorgular hâlâ `mysqli_query($baglan,
   "select ... where id=$id")` biçiminde ve bu **yapısal bir SQL injection açığı**.
   Yeni kodda prepared statement kullan:
   ```php
   $sorgu = mysqli_prepare($baglan, "select * from uyeler where id=?");
   mysqli_stmt_bind_param($sorgu, "i", $id);
   mysqli_stmt_execute($sorgu);
   ```
   `addslashes()`, `strip_tags()`, `get_security()`, `tirnak_replace()` SQL için
   **koruma değildir**.

2. **Çıktıyı kaçır.** `htmlspecialchars($deger, ENT_QUOTES, 'UTF-8')`.

3. **Parola.** `password_hash()` / `password_verify()` kullan; geçiş
   `password_needs_rehash()` ile kademeli. **Not:** `uyeler.parola` kolonu
   `varchar(255)`'e çıkarıldı — bcrypt 60 karakter, `varchar(40)` sessizce
   kırpıyordu ve rehash edilen üyeler giriş yapamıyordu.

4. **Yetki kontrolü her yerde** — `lpanel/` altındaki her yazma işlemi.

5. **Gizli dosyalara dokunma / okuma:** `config/db.php`, `SQL/*.sql`, `lisans.php`.

6. **Dosya yükleme.** `getimagesize()` ile içerik doğrula; `uploads/.htaccess`
   PHP çalıştırmayı engelliyor.

7. **CSRF ve çıktı kaçırma** hâlâ eksik olan alanlar (raporda listeli) — yeni
   form yazarken en azından `htmlspecialchars` kullan.

## Bilinen tuzaklar

- `loader.php` içinde `mysqli_report(MYSQLI_REPORT_OFF)` bilinçli ayarlı;
  kaldırma ("temizlik" değil).
- `loader.php` içindeki global `addslashes`/`strip_tags` girdi temizliği
  güvenlik katmanı değil, veri bozucu bir işlemdir.
- Kategori ağacı özyinelemeli, her seviyede ayrı sorgu (N+1) — önce ölç.
- `config/fonksiyonlar.php` ~1600 satır; fonksiyon bulmak için `grep` kullan.
- **PHP 8 uyumu:** dizi/eleman olmayan değişken okumaları (`$_GET["x"]`,
  `$_SESSION["x"]`, `$degisken`) "Undefined array key / variable" üretip sayfayı
  bozar. Yeni kodda `isset(...)` / `??` kullan; dizi/nesne olmayan yerlerde
  `count()` çağırma.
- **Sayfa cache'i** (`cache/`) bayat HTML sunabilir — değişiklikten sonra sil.
- Dil değişimi **cookie** ile olur; `?dil=xx` sorgu parametresi çalışmaz
  (testlerde `Cookie: dil=fr` kullan).
- `id_getir("icerik","url",$sayfa)` çalışmaz: `url` kolonunda id yoktur;
  `/sayfa/hakkimizda-1` gibi adreslerde id sondan ayrıştırılır (`-N`).
- Web kökündeki `move_settings.php` tek seferlik geçiş betiğidir; sunucuya
  yüklenmemeli.

## Yardımcı fonksiyonlar (bu projeye özel)

`config/fonksiyonlar_meta.php`:

- `tel_rakamlari($tel)` — sadece rakamları döndürür.
- `tel_uluslararasi($tel, $ulke_kodu='220')` — `tel:` / `wa.me` bağlantıları
  için tam numara (ülke kodu zaten varsa dokunmaz, baştaki 0'ları atar).
- `koordinat_ayir($koordinat)` — "13.45,-16.57", "13.45--16.57",
  "-13.45-16.57" formatlarını lat/lon olarak ayırır (Gambiya batı boylamı
  negatif olduğu için `explode("-")` yetmez).
- `meta_title()` / `meta_description()` — adres çubuğu ve meta açıklaması.

## Git

- `main` dalı, remote: `origin` → `https://github.com/ahmetyil6767/ilan.git`
- **`.gitignore`'a uy.** `uploads/foto/`, `uploads/ajax/`, `SQL/*.sql`,
  `config/db.php` asla commit edilmez. `git add .` yapma — önce `git status`.
- Commit mesajları Türkçe, ne yaptığını söyleyen tek cümle.
- `config/db.php.example`, `.gitignore` ve `.gitattributes` repodadır.
