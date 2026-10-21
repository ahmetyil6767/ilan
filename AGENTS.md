# AGENTS.md — Limon İlan

Bu dosya OpenCode ve bu repoda çalışan diğer AI ajanları için proje talimatıdır.
Depoya commit edilir; herkes aynı kuralları alır.

## Proje nedir

`ilan.limontasarim.net` — PHP + MySQL tabanlı **ilan / classified ads** uygulaması.
"Limon CMS" altyapısı üzerine kurulu; ziyaretçi arayüzü ve yönetim paneli **tek kod
tabanında** duruyor. Composer, npm, paket yöneticisi **yoktur** — bağımlılıklar
klasör içine kopyalanmış halde gelir.

- **PHP:** 7.2 için yazıldı, hedef sürüm **8.4** (yerelde 8.4.15 CLI kurulu)
- **DB:** MariaDB / MySQL, `mysqli` eklentisi, `utf8mb4`
- **Ölçek:** ~408 PHP dosyası, ~71.000 satır, 24 tablo
- **URL:** Apache `mod_rewrite` ile SEO dostu (`.htaccess`)

## Çalıştırma ve doğrulama

```bash
# Yerel WAMP zaten http://ilan.limontasarim.net üzerinden servis ediyor.
# Alternatif olarak PHP'nin dahili sunucusu:
php -S localhost:8000

# Testler (veritabanı bağlantısı gerekir, loader.php'i çeker)
php tests/LanguageSystemTest.php
```

`tests/LanguageSystemTest.php` çıkış kodu döndürür: `0` = hepsi geçti, `1` = hata var.
Dil ve para birimi sistemini kapsar. **Bir değişiklik yapıp bitirmeden önce çalıştır.**

Şu anda tek otomatik test dosyası bu. Yeni bir modül ekliyorsan aynı kalıpla
kendi testini yaz ve `tests/` altına koy.

## Yedek politikası — 2 gün

Değişiklik yapan her ajan oturum sonunda `otomatik_yedek.ps1`'i çalıştırır:

```powershell
powershell -ExecutionPolicy Bypass -File otomatik_yedek.ps1
```

- DB dump (`otomatik_db_*.sql`) + proje dosyalarının zip'i (`otomatik_dosyalar_*.zip`,
  `.git` ve `SQL/yedekler` hariç, `uploads` dahil) → `SQL\yedekler\` altına yazar.
- `otomatik_*` dosyalarını kendisi 2 güne kadar tutar, eskisini siler
  (`yedek_*.sql` adlı manuel geri yükleme noktalarına **dokunmaz**).
- Kayıt: `SQL\yedekler\yedek_log.txt`. Günlük zamanlanmış görev `LimonYedek`
  (her gün 03:30) da aynı script'i koşturur; mysqldump ile root şifresiz dump alır.
- Ek olarak **her DB'de veri değiştiren adımdan önce** o adımı etkileyen tabloların
  ayrı `yedek_<isim>.sql` yedeği alınır (mevcut kural, aynen geçerli).

## Veritabanı

`config/db.php` **repoda yok** (`.gitignore`'da). Yeni ortamda:

```bash
cp config/db.php.example config/db.php
# sonra host/veritabani/kullanici/parola alanlarını doldur
```

Şema kaynağı `SQL/localhost.sql` — ama o dosya da `.gitignore`'da, çünkü
`uyeler` tablosunda gerçek parola özetleri ve e-posta adresleri var.

Ayarlar (`ayarlar` tablosu) çoğunlukla **veritabanında** durur, kodda değil.
Tema ayarları `tema_ayar("anahtar")` ile okunur; API anahtarları (İyzico, Google
Maps) de oradan gelir — **sabit olarak koda yazma**.

## Dizin haritası

| Yol | Sorumluluk |
|---|---|
| `loader.php` | Bootstrap: hata ayarı, DB bağlantısı, oturum, eklenti/tema yükleme. Her istek buradan geçer. |
| `config/` | `db.php` (gizli), `yapilandir.php` (tema/ayar şemaları), `fonksiyonlar*.php` (çekirdek), `dil.php` |
| `lang/` | `tr.php`, `en.php`, `fr.php`, `ar.php` — anahtar kümesi dört dosyada **birebir aynı** olmalı |
| `lpanel/` | Yönetim paneli. `admin_dizini` ayarıyla adı değiştirilebilir (varsayılan `lpanel`) |
| `temalar/limon_ilan/` | Aktif tema + `bilesen/` (bileşenler) + `iyzico/` (ödeme SDK'sı) |
| `eklentiler/` | Eklenti modülleri (auto_mail, xml_sitemap, mysql_yedek, favoriler...) |
| `uploads/` | Kullanıcı yüklemeleri. PHP çalıştırma `.htaccess` ile kapalı. |
| `SQL/` | Şema/dump klasörü. İçerik repoda değil. |
| `tests/` | Otomatik testler |

**Üçüncü parti kod — elle değiştirme:**
`lpanel/js/ckeditor/**`, `eklentiler/smtp/phpmailer/**`,
`temalar/limon_ilan/iyzico/src/**`. Güncelleme yapacaksan paketin kendi
sürümünden gelmeli; yamalar ayrı dosyada tutulmalı.

## Kod kuralları

Mevcut üslubu koru. Refactor gereksiz yere genişletme.

- **Değişken ve fonksiyon adları Türkçe, snake_case:** `$baglan`, `$veritabani`,
  `$uye_ekstra_bilgiler`, `alt_kategori_listele()`, `form_elemani()`.
  Yeni isim de Türkçe ve mevcut stile uygun olsun.
- **`array()` tercih edilir**, kısa `[]` sözdizimi az kullanılıyor.
- **Girinti:** dosyadan dosyaya değişiyor (4 boşluk veya tab). Düzenlediğin
  dosyanın kendi girintisini koru, dosyayı baştan girintileme.
- **Dosya başında koruma:** `loader.php` ve `config/yapilandir.php` doğrudan
  çağrılmayı engelleyen `count(get_included_files())` kontrolü içerir. Yeni
  giriş noktası dosyası yazarsan aynısını ekle.
- **Yorumlar Türkçe.** Bazı dosyalar (ör. `loader.php`) Türkçe diyakritik
  kullanmadan yazılmış (`icin` değil `için`) — bu dosyada o üslubu sürdür.

### Kodlama (encoding) — dikkat

- Tüm dosyalar **UTF-8**. Yeni dosya **BOM'suz UTF-8** olmalı.
- Depoda **BOM'lu 55 dosya** var (`lang/*.php` dahil). Bunlar ekrana 3 görünmez
  bayt basar ve sonraki `header()` çağrılarını "headers already sent" ile kırar.
  **Yeni dosyaya BOM ekleme.** Mevcutları temizlemek ayrı bir iş; kapsam
  dışıysa dokunma.
- Dosyayı kaydederken `charset`'i değiştirme. Editörün dosyayı UTF-8'e
  çevirmesine izin verme.

### Hata ayarı — üretim farkı

`loader.php` şu anda **debug modunda**: `display_errors=1`, `memory_limit=-1`,
`max_execution_time=120`. Bu yerel geliştirme içindir.

Sunucuya yüklemeden önce `display_errors` **0** olmalı. Bu değişikliği
"küçük bir düzeltme" gibi sunma — ayrı ve bilinçli bir adım.

## Güvenlik kuralları

`PROJE_RAPORU.md` bu kod tabanındaki 16 bilinen açığı listeler ve her oturumda
yüklenir. **Yeni açık ekleme.** Mevcut desenleri körü körüne kopyalama.

1. **SQL — string birleştirme yapma.** Tüm sorgular `mysqli_query($baglan,
   "select ... where id=$id")` biçiminde kuruluyor ve bu **zaten yapısal bir
   SQL injection açığı**. Yeni kod yazarken prepared statement kullan:
   ```php
   $sorgu = mysqli_prepare($baglan, "select * from uyeler where id=?");
   mysqli_stmt_bind_param($sorgu, "i", $id);
   mysqli_stmt_execute($sorgu);
   ```
   `addslashes()`, `strip_tags()`, `get_security()` ve `tirnak_replace()`
   SQL için **koruma değildir**. Bunlara güvenme.

2. **Çıktıyı kaçır.** `htmlspecialchars($deger, ENT_QUOTES, 'UTF-8')`.
   Veritabanından gelen metni (`baslik`, `menu`, yorum gövdesi, üye adı)
   olduğu gibi `echo` etme. `form_elemani()` ve menü fonksiyonları dahil.

3. **Parola.** `uye_sorgula()` MD5 karşılaştırıyor (`fonksiyonlar.php:1232`).
   MD5 kırıktır. **Yeni** parola yollarında `password_hash()` /
   `password_verify()` kullan. Mevcut hash'leri geriye dönük uyum
   bozulmadan değiştir — geçiş `password_needs_rehash()` ile kademeli olmalı.

4. **Yetki kontrolü her yerde.** `$_GET['yetki']`, oturumdaki yetki veya URL
   segmenti üzerinden karar verme. `lpanel/` altındaki her yazma işlemi
   yetki kontrolü içermeli.

5. **Gizli dosyalara dokunma / okuma:**
   - `config/db.php` — gerçek MySQL parolası
   - `SQL/*.sql` — üye parola özetleri, e-posta adresleri
   - `lisans.php` — lisans anahtarı

   Bunlar `.gitignore`'da ve `opencode.jsonc` ile modelin okuması da engelli.
   `config/db.php.example` vardı; iş yapmak için onu kullan.

6. **Dosya yükleme.** Uzantı kontrolü tek başına yetersiz. Yeni yükleme yolu
   yazıyorsan `getimagesize()` ile içerik doğrula ve `uploads/.htaccess`'in
   PHP çalıştırmayı engellediğinden emin ol.

## Bilinen tuzaklar

- `loader.php` içinde `mysqli_report(MYSQLI_REPORT_OFF)` bilinçli olarak
  ayarlanmış. PHP 8.1+ varsayılanı `MYSQLI_REPORT_STRICT` olduğu için, kod
  7.2'de "düzeltme" gibi görünen hataları fırlatıp sayfayı bozuyor. Bu satırı
  "temizlik" diye kaldırma.
- `loader.php` içinde `addslashes`/`strip_tags` ile yapılan global girdi
  temizliği güvenlik katmanı değil, veri bozucu bir işlemdir.
- Kategori ağacı fonksiyonları özyinelemeli ve her seviyede ayrı sorgu
  çalıştırıyor (N+1). Performans sorunu çözmek istersen önce ölç.
- `config/fonksiyonlar.php` ~1600 satır, yüzlerce fonksiyon içeriyor. Fonksiyon
  bulmak için `grep` kullan; dosyayı baştan sona okumaya çalışma.
- Web kökündeki `move_settings.php` tek seferlik bir geçiş betiğidir ve veritabanına
  yazma yapar. İşi bitmişse silinmeli; sunucuya yüklenmemeli.

## Git

- `main` dalı, remote: `origin` → `https://github.com/ahmetyil6767/ilan.git`
- **`.gitignore`'a uy.** `uploads/foto/`, `uploads/ajax/`, `SQL/*.sql` ve
  `config/db.php` asla commit edilmez. Yanlışlıkla `git add .` yapma —
  önce `git status` kontrol et.
- Commit mesajları Türkçe, ne yaptığını söyleyen tek cümle.
- `config/db.php.example`, `.gitignore` ve `.gitattributes` repodadır; bunları
  düzenlemek yeni ortam kurulumunu etkiler.