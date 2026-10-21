# GitHub Copilot — proje talimatı

Bu depo için geçerli kurallar **`AGENTS.md`** dosyasındadır. Onu oku ve uygula.
Mimari/iş akışı bilgisi: `PROJECT_CONTEXT.md`.

Özet:
- Jaayko (`jaayko.gm`) — PHP 8.4 + MySQL ilan uygulaması; **Gambiya** için
  (para birimi GMD, ülke kodu +220, bölge: Region/District — "İl/İlçe/Mahalle" değil).
- Diller tr/en/fr/ar; `lang/` anahtar kümeleri dört dilde birebir aynı olmalı.
- Kod stili: Türkçe değişken/fonksiyon adları (snake_case), `array()` tercih edilir,
  yorumlar Türkçe, yeni dosyalar BOM'suz UTF-8.
- Güvenlik: SQL'de prepared statement, çıktıda `htmlspecialchars`, parolada
  `password_hash`, her yazma işleminde yetki kontrolü.
- Doğrulama: `php -l` → `php tests/LanguageSystemTest.php` → 4 dilli sayfa taraması
  (PHP hatası 0) → `otomatik_yedek.ps1` → Türkçe commit mesajı.
