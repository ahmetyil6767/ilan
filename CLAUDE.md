# CLAUDE.md

Bu depo için geçerli talimatlar **`AGENTS.md`** dosyasındadır. Onu oku ve uygula.

Özet hatırlatma (ayrıntı için AGENTS.md):

- **Proje:** Jaayko (`jaayko.gm`) — PHP 8.4 + MySQL ilan uygulaması, Gambiya için.
  Para birimi **GMD**, ülke kodu **+220**, diller **tr/en/fr/ar** (çerez ile değişir).
- **Kurallar:** Türkçe yorum/commit, `array()` tercih edilir, `prepared statement` ile SQL,
  çıktıyı `htmlspecialchars` ile kaçır, yeni dosya **BOM'suz UTF-8**.
- **Doğrulama:** `php -l <dosya>` → `php tests/LanguageSystemTest.php` → 4 dilli link
  taraması (PHP hatası 0 olmalı) → `otomatik_yedek.ps1` → Türkçe commit.
- **Dokunma:** `config/db.php`, `SQL/*.sql`, `lisans.php`, üçüncü parti kod
  (`lpanel/js/ckeditor/**`, `eklentiler/smtp/phpmailer/**`, `temalar/limon_ilan/iyzico/src/**`).
- **Mimari ve iş akışları:** `PROJECT_CONTEXT.md`.

Bilinen tuzaklar: sayfa cache'i bayatlar (`cache/` sil), dil çerezle değişir
(`?dil=` çalışmaz), `count()`/`$_GET` okumalarında `isset` kullan, mahalle özelliği
kaldırıldı, `id_getir("icerik","url",...)` çalışmaz (id sondan `-N` ayrıştırılır).
