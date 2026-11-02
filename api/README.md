# JaayQuick REST API (`/api`)

Mobil istemci (React Native/Expo) ve diğer istemciler için ince REST köprü.
PHP 8.3/8.4 + MariaDB. Klasördeki kod **loader.php'yi kullanmaz**; onun güvenli
parçalarını (hata loglama, DB bağlantısı, dil/config/tema fonksiyonları) aynı
sırayla kendi bootstrap'ında kurar.

## Rotalar

| Yöntem | Uç | Kimlik | Açıklama |
|---|---|---|---|
| POST | `/api/auth/login` | — | `{email, password}` — `email` alanına e-posta **veya** cep telefonu girilir |
| POST | `/api/auth/register` | — | `{name, email, phone?, password, password_confirmation}` |
| GET | `/api/auth/me` | Bearer | Oturumdaki üye |
| POST | `/api/auth/logout` | Bearer | Token'ı geçersiz kılar |
| GET | `/api/listings` | — | `?search=&category=&region=&district=&price_min=&price_max=&sort=latest|price_asc|price_desc&page=&per_page=` |
| GET | `/api/listings/:id` | — | İlan detayı (yayında olmayan yalnız sahibine görünür) |
| GET | `/api/listings/mine` | Bearer | Üyenin tüm ilanları (`?page=`, `?per_page=`) |
| POST | `/api/listings` | Bearer | multipart/form-data: `title, description, category_id, region, district?, price` + `photos[]` (max önerilen 8, `getimagesize` ile doğrulanır) |
| PUT/PATCH | `/api/listings/:id` | Bearer | JSON: `title?, description?, price?, region?, district?` + opsiyonel `photos[]` |
| DELETE | `/api/listings/:id` | Bearer | İlanı arşivler (`durum=3`, sebep `kullanici_sildi`) |
| GET | `/api/categories` | — | Üst/alt kategori ağacı + ilan sayıları + emoji ikon |
| GET | `/api/regions` | — | Gambiya'nın 8 bölgesi + ilçeleri |
| GET | `/api/districts` | — | `?region=<isim|id>` tek bölgenin ilçeleri |
| GET | `/api/messages` | Bearer | Sohbet listesi (son mesaj + okunmamış bilgisi) |
| POST | `/api/messages` | Bearer | `{listing_id, message}` — satıcıya mesaj; sohbet yoksa kurulur |
| GET | `/api/favorites` | Bearer | Favori ilanlar |
| POST | `/api/favorites` | Bearer | `{listing_id}` favoriye ekle (idempotent) |
| DELETE | `/api/favorites/:id` | Bearer | Favoriden çıkar (`:id` = ilan id) |

## Yanıt biçimi

```jsonc
// Liste / favorites
{ "data": [ {"id": 12, "title": "...", "price": 250000, "currency": "GMD",
             "region": "Kanifing", "photos": [{"url": "...", "thumb": "...", "original": "..."}],
             "status": "published", "created_at": "2026-11-02 10:00:00",
             "category": [12], "seller": {"id": 3, "name": "...", "phone": "+220..."} } ],
  "meta": {"current_page": 1, "per_page": 20, "total": 41, "last_page": 3} }

// Hata
{ "message": "...", "errors": {"alan": "mesaj"} }   // 401/403/404/422/500/503
```

Kimlik doğrulama: HTTP `Authorization: Bearer <64-hex>` başlığı. Token'lar
`api_tokens` tablosunda, 30 gün ömürle; her girişte önceki token silinir
(tek aktif oturum). Kayıt sırasında `uyeler` satırı `durum=1` (aktif, e-posta
onaylı) yaratılır — web tarafındaki `durum=0` + doğrulama e-postası akışından
farklıdır.

## İlan veri modeliyle bağ

- İlan kaydı `icerik` tablosunda `tip='ilan'` dir; fotoğraflar `galeri`
  (resim = tam URL), fiyat/para birimi/bölge/vitrin görseli `ekstra_bilgi`
  (`tip='ilan'`) içinde. API okurken hem `ilan` hem eski `icerik` tipine bakar,
  yazar konvansiyon olarak `ilan` kullanır.
- Fiyatlar site değişimiyle tutarlı biçimde `"1.234,56"` (virgül kesirli) olarak
  saklanır; API içinde sayısal karşılaştırma için REPLACE zinciri ile sayıya
  çevrilir (`price_min`/`price_max` yalnızca GMD kayıtlarında çalışır).
- Üye telefonu `uyeler` tablosunda değil `ekstra_bilgi` (`anahtar='cep_tel'`,
  `tip='uye'`) içindedir.
- İlan limitleri temanın `uye_ilan_limiti()` fonksiyonundan okunur; yoksa
  yayın onayı anında verilir.

## Kurulum / dikkat

- `.htaccess` içindeki kural `/api/...` → `api/index.php?route=$1` yönlendirir;
  Apache kurallarına dokunmak istenmezse `/api/index.php?route=...` de çalışır.
- İlk istekte `api_tokens` (ve eksikse `gorusmeler`/`mesajlar`/`favoriler`)
  tabloları `CREATE TABLE IF NOT EXISTS` ile kurulur. Üretim öncesinde
  `otomatik_yedek.ps1` ile DB yedeği alınmalıdır.
- Hatalar ekrana yazılmaz; `C:\wamp64\logs\php_error.log` içinde `JaayQuick][API`
  etiketiyle loglanır (özel handler bootstrap içinde kuruludur).
- Yüklenen fotoğraflar `uploads/` altına `yukle()` yardımcısıyla gider; PHP
  çalıştırma `uploads/.htaccess` ile zaten kapalıdır.
