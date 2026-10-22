# Yedekleme ve Geri Yükleme

Bozulmaya karşı üç katmanlı koruma kullanılıyor. Her katmanın ne olduğu ve nasıl
geri alınacağı aşağıda.

---

## 1. Otomatik yedek (2 günlük pencere) — `otomatik_yedek.ps1`

```powershell
powershell -ExecutionPolicy Bypass -File otomatik_yedek.ps1
```

- DB dump → `SQL\yedekler\otomatik_db_<tarih>.sql`
- Proje dosyaları (uploads dahil, `.git` ve `SQL\yedekler` hariç) → `otomatik_dosyalar_<tarih>.zip`
- `otomatik_*` dosyaları **2 gün** sonra otomatik silinir
- Kayıt: `SQL\yedekler\yedek_log.txt`
- Günlük zamanlanmış görev: `LimonYedek` (her gün 03:30)

## 2. Manuel geri yükleme noktası (kalıcı) — `SQL\yedekler\yedek_bozulma_guvenli_*.sql`

Bozulma şüphesinde **elle** alınan tam DB dökümü. `otomatik_*` dosyaları silinse bile bu kalır.

```powershell
# 1) mevcut DB'yi yedekle (geri dönüş için)
& "C:\wamp64\bin\mysql\mysql8.4.7\bin\mysqldump.exe" -u root --single-transaction `
  --default-character-set=utf8mb4 jaayko_limon > "SQL\yedekler\yedek_onceki_$(Get-Date -Format yyyyMMdd-HHmm).sql"

# 2) mevcut şemayı temizle
& "C:\wamp64\bin\mysql\mysql8.4.7\bin\mysql.exe" -u root -e "DROP DATABASE IF EXISTS jaayko_limon;"

# 3) yedeği geri yükle
& "C:\wamp64\bin\mysql\mysql8.4.7\bin\mysql.exe" -u root --default-character-set=utf8mb4 `
  jaayko_limon < "SQL\yedekler\yedek_bozulma_guvenli_20261022-1110.sql"
```

## 3. Kod sürüm etiketi (git) — `v1.0-gambiya-20261022`

Kodun tamamı bu etikette; `uploads/` ve `SQL/` git dışı olduğu için ayrıca
`SQL\yedekler\yedek_kod_<tarih>.zip` (tüm proje + uploads) alınır.

```bash
git log --oneline            # geçmiş
git diff v1.0-gambiya-20261022..HEAD   # o etiketten sonraki değişiklikler
git checkout v1.0-gambiya-20261022     # o sürüme dön
```

## Yedek alırken uyulacak kurallar

- **DB'ye yazan her adımdan önce** ilgili tabloların yedeğini al
  (`mysqldump ... <tablo> > SQL\yedekler\yedek_<isim>.sql`)
- `SQL\yedekler\yedek_*.sql` dosyalarına **dokunulmaz** (manuel geri yükleme noktaları)
- `otomatik_yedek.ps1` yedeği **2 gün** tutar; uzun vadeli koruma için ayrıca
  `yedek_bozulma_guvenli_<tarih>.sql` ve `yedek_kod_<tarih>.zip` alınır

## Bozulma belirtileri (kontrol listesi)

| Belirti | Kontrol |
|---|---|
| Sayfalar boş / 500 | `C:\wamp64\logs\php_error.log` → son hatalar |
| PHP hataları ekranda | `loader.php` hata ayarı (yerelde `ilan` host → açık, üretimde kapalı) |
| Dil/çeviri bozuk | `php tests\LanguageSystemTest.php` (0 = geçti) |
| Kategori ağacı bozuk | `kategoriler` + `ekstra_bilgi` tabloları birlikte yedeklenmeli |
| Menü/bağlantılar bozuk | `linkler` tablosu yedeği |
| Dosyalar eksik | `SQL\yedekler\yedek_kod_<tarih>.zip` |
