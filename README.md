# JaayQuick (jaayquick.com)

Gambiya için ücretsiz ilan panosu. PHP 8.4 + MySQL, "JaayQuick" altyapısı.
Ziyaretçi arayüzü ve yönetim paneli tek kod tabanında; Composer/npm yoktur.

## Kurulum (Windows / WAMP)

1. **WAMP** kurulu olmalı. Proje kökü: `C:\wamp64\www\jaayquick.com`
2. **Veritabanı:** `jaayquick` adlı boş bir şema oluştur, `SQL/localhost.sql`
   içe aktar (dosya `.gitignore`'da olduğu için repoda yoktur).
3. **DB ayarları:** `config/db.php` yok → `config/db.php.example` dosyasını kopyala
   ve host/veritabanı/kullanıcı/parola alanlarını doldur.
4. **Apache:** `AllowOverride All` açık olmalı (`.htaccess` ile URL'ler
   `/sayfa/<slug>-<id>`, `/<kategori_id>/<slug>` gibi yazılır).
5. **Yerel erişim:** `http://ilan/` (Hosts'ta `ilan` → 127.0.0.1) ya da
   `php -S localhost:8000`.

## Geliştirme komutları

```bash
php -l <dosya>                        # sözdizimi kontrolü
php tests/LanguageSystemTest.php      # dil/para birimi testi (0 = başarılı)
powershell -ExecutionPolicy Bypass -File otomatik_yedek.ps1   # yedek (2 günlük pencere)
```

## Dizinler

| Yol | İçerik |
|---|---|
| `loader.php` | Bootstrap (DB, oturum, dil, tema) |
| `config/` | Çekirdek fonksiyonlar, dil altyapısı, ayar şemaları |
| `lang/` | tr / en / fr / ar sözlükleri + kategori terimleri (`spec_*.php`) |
| `temalar/jaayquick/` | Aktif tema, bileşenler, tema paneli, pasif iyzico SDK'sı |
| `lpanel/` | Çekirdek yönetim paneli |
| `eklentiler/` | Eklenti modülleri (auto_mail, sitemap, favoriler, yedek…) |
| `uploads/` | Kullanıcı yüklemeleri |
| `SQL/yedekler/` | Otomatik ve manuel yedekler |
| `tests/` | Otomatik testler |

## AI ajanları / katkıcılar

- `AGENTS.md` — **kurallar** (kod stili, güvenlik, doğrulama, yedek, git)
- `PROJECT_CONTEXT.md` — **bilgi tabanı** (mimari, veri modeli, iş akışları, tuzaklar)
- `CLAUDE.md`, `GEMINI.md`, `.github/copilot-instructions.md` — kısa yönlendiriciler
