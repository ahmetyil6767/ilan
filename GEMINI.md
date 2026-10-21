# GEMINI.md

Bu depo için geçerli talimatlar **`AGENTS.md`** dosyasındadır. Onu oku ve uygula.

- **Proje:** Jaayko (`jaayko.gm`) — PHP 8.4 + MySQL ilan uygulaması, Gambiya için.
  Para birimi **GMD**, ülke kodu **+220**, diller **tr/en/fr/ar** (çerez ile değişir).
- **Kurallar:** Türkçe yorum/commit, `array()` tercih edilir, SQL'de prepared statement,
  çıktıyı `htmlspecialchars` ile kaçır, yeni dosya **BOM'suz UTF-8**.
- **Doğrulama:** `php -l <dosya>` → `php tests/LanguageSystemTest.php` → 4 dilli link
  taraması (PHP hatası 0 olmalı) → `otomatik_yedek.ps1` → Türkçe commit.
- **Dokunma:** `config/db.php`, `SQL/*.sql`, `lisans.php`, üçüncü parti kod.
- **Mimari ve iş akışları:** `PROJECT_CONTEXT.md`.
