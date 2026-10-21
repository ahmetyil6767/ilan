# PROJECT_CONTEXT.md — Jaayko Bilgi Tabanı

**Ne bu dosya:** `AGENTS.md` *kuralları* anlatır (ne yapma, nasıl yap). Bu dosya
*bilgiyi* anlatır: sistem nasıl çalışır, veri nerede durur, hangi iş akışı
hangi dosyaları etkiler, sık karşılaşılan sorunlar neyin kaynaklıdır.

Yeni bir ajan/insan bu projeye başlarken: önce `AGENTS.md`, sonra bu dosya.

---

## 1. Sistem özeti

| Katman | Teknoloji |
|---|---|
| Backend | PHP 8.4 (hedef), `mysqli`, MariaDB/MySQL `utf8mb4` |
| Şablon | Kendi yazdığımız (Twig benzeri) — `tema_header()`, `tema_footer()` |
| Yönetim | `lpanel/` (çekirdek panel) + `temalar/limon_ilan/panel/` (tema paneli) |
| Frontend | Bootstrap 4, jQuery, CKEditor, DataTables, ionicons, FontAwesome |
| Harita | Google Maps JS (koordinat `lat,lon`; Gambiya batı boylamı **negatif**) |
| Ödeme | Havale/EFT (manuel onay) — iyzico SDK pasif |
| Dil | tr / en / fr / ar (çerez tabanlı) |
| Bağımlılık | Composer/npm **yok** — hepsi klasör içinde kopyalanmış |

İstek akışı: `index.php` (yönlendirme) → `loader.php` (bootstrap) →
`config/dil.php` → tema dosyası (`temalar/limon_ilan/...`).

## 2. Veri modeli (26 tablo)

| Tablo | İçerik | Notlar |
|---|---|---|
| `ayarlar` | Site/tema ayarları (`anahtar`/`deger`) | Dil varyantları `tema-ayar_dil` son ekiyle: `limon_ilan-footer_1_baslik_en` |
| `icerik` | Hem ilanlar hem statik sayfalar (`tip='ilan'|'sayfa'`) | `url` slug'ı; `/sayfa/<slug>-<id>` |
| `kategoriler` | Kategori ağacı (`ust_kategori` hiyerarşi) | `baslik_en/fr/ar` çevirileri `ekstra_bilgi` içinde |
| `ekstra_bilgi` | Esnek alan deposu (`tip`, `id`, `anahtar`, `deger`) | Çeviriler de burada: `baslik_en`, `icerik_en`, `ozel_alanlar_en`… |
| `linkler` | Menü bağlantıları (`menu_id` → `ayarlar` içindeki `footer_N` → `menu-<id>`) | `url` tam adres olarak saklanır |
| `listeler` | Paketler (`tip='hesaplar'` havale hesapları dahil) | `liste_bilgi("paket_adi", $id)` |
| `bilesenler` | Tema bileşenleri (slider, listeleme) | `baslik_en/fr/ar` çevirileri `ekstra_bilgi` |
| `siparisler` | Paket/doping/mağaza/GET siparişleri | `tip`, `durum` (0 bekliyor, 1 onaylı) |
| `dopingler` | Satın alınan doping (ilgili ilan + süre) | `durum`, `bitis_tarih_saat` |
| `magazalar`, `magaza_uye` | Mağazalar ve üye yetkileri | |
| `uyeler` | Üyeler (`email_adresi` kimlik, `parola` hash) | `parola` **varchar(255)** (bkz. AGENTS.md güvenlik #3) |
| `iller`, `ilceler`, `mahalleler` | Konum | Gambia: 8 bölge / 33 ilçe / mahalle boş |
| `galeri` | İlan görselleri | `resim` tam URL olarak saklanır |
| `yorumlar`, `bildirimler`, `mesajlar`… | Etkileşim | |

**Katman ilişkisi:** `ayarlar` → tema/eklenti → `ekstra_bilgi` → içerik → kategori →
`linkler`/`listeler` → menü/ayar sayfaları. Bir ayarı değiştirmek
`ekstra_bilgi`'ye, bir etiketi çevirmek `lang/` veya `ekstra_bilgi`'ye dokunur.

## 3. Çeviri sistemi — hangi yerde ne kullanılır

```
_arayüz metni ─────────────► _LANG('anahtar')            lang/{tr,en,fr,ar}.php
_kategori alan etiketi ────► spec_etiket('TR metin')     lang/spec_a|b|c.php (sözlük)
_DB başlığı ──────────────► _DIL_BASLIK($baslik,$id,$tip) ekstra_bilgi: baslik_en/fr/ar
_kategori alan tanımı ────► kategori_bilgi('ozel_alanlar',$id) ekstra_bilgi: ozel_alanlar_en
_statik sayfa gövdesi ────► icerik_getir($id)           ekstra_bilgi: icerik_en/fr/ar
```

- `lang/*.php` anahtar kümeleri **dört dilde birebir aynı** olmalı
  (`tests/LanguageSystemTest.php` bunu doğrular; 81 test).
- `spec_etiket()` sözlükte olmayan metni **aynen** döndürür → yeni alan/opsiyon
  eklendiğinde önce sözlüğe ekle, sonra kodu bağla.
- Kullanıcı içeriği (ilan başlığı, mağaza/üye adı, marka-model adı) **çevrilmez**.

## 4. Ödeme akışı (havale)

```
[doping|paket|magaza] sayfası  →  siparisler kaydı (durum=0, tutar GMD)
        ↓  yönlendir: ozel_url("siparis")."?siparis_id=<id>"
siparis.php  →  havale bilgileri (listeler/hesaplar) + referans "JA-<id>"
        ↓  kullanıcı "ödeme bildirimi" (ajax.php?islem=odeme_bildirimi)
yönetici paneli  →  ?onay=<id>  →  durum=1 + özelliklerin aktive edilmesi
        (dopingler durum=1, magaza durum=1, uye.paket/paket_bitis_tarihi güncellenir)
```

- Referans kodu: `JA-<siparis_id>` — sipariş sayfasında `s_...`/`hv_...` lang
  anahtarlarıyla gösterilir.
- Havale hesabı: `listeler` (`tip='hesaplar'`) → `banka_adi`, `ad_soyad`, `iban`.
  Yönetim panelden güncellenir; IBAN boşsa ekranda uyarı çıkar.
- `siparis.php` içindeki iyzico bloğu **yalnızca** `online_odeme=aktif` iken
  çalışır; pasifken hiç çalıştırılmaz.

## 5. Sık karşılaşılan sorunlar ve kök nedenleri

| Belirti | Kök neden | Çözüm |
|---|---|---|
| Sayfada "Undefined array key/variable" | `$_GET`/`$_SESSION`/değişken kontrolsüz okunuyor | `isset(...) ? ... : null` kullan; `count()`'a dizi garantisi ver |
| Çeviri görünmüyor | Anahtar 4 dilde eşleşmiyor / `spec_etiket` sözlükte yok | `lang/` anahtar kümesini eşitle, sözlüğe ekle |
| Sayfa eski içerik gösteriyor | `cache/` bayat | `cache/` içeriğini sil |
| Dil değişmiyor | Dil **cookie** ile değişiyor | Testte `Cookie: dil=fr` kullan, `?dil=fr` değil |
| Sayfa başlığı boş | `meta_title()` içerik id'sini `url` ile arıyor | `/sayfa/x-1` → id sondan (`-N`) ayrıştırılmalı |
| Üye giriş yapamıyor | `parola` kolonu bcrypt'i kesiyordu | Kolon `varchar(255)` yapıldı; `parola_dogrula()` bcrypt + md5 fallback kullanır |
| Haritada işaret yanlış yerde | Boylam negatif, `explode("-")` işareti yiyor | `koordinat_ayir()` kullan |
| `wa.me`/`tel:` bağlantısı bozuk | Ülke kodu elle +90 ekleniyordu | `tel_uluslararasi($tel)` kullan (Gambiya `+220`) |
| 404 başlığı "404 Not Found" | Apache kendi hata sayfasını veriyordu | `.htaccess`: `ErrorDocument 404 /404.php` |
| Kategori başlığı TR kalıyor | Menü `getir("kategoriler","baslik",...)` ile basıyordu | `_DIL_BASLIK(...)` kullan |

## 6. Sık kullanılan yardımcılar

| Yardımcı | Nerede | İşlev |
|---|---|---|
| `tema_ayar($k)` | `config/fonksiyonlar.php` | Tema ayarı okur (çeviri varyantlı) |
| `ayar_getir($k)` / `ayar($k)` | `config/fonksiyonlar.php` | Genel ayar okur |
| `site_url()` | `config/fonksiyonlar.php` | Site kök URL |
| `tema_header()` / `tema_footer()` | tema katmanı | Ortak başlık/altbilgi (SEO + dil) |
| `form_elemani()` / `form_buton()` | tema katmanı | Form alanı üretir (etiketler `_LANG` ile) |
| `menu_getir($menu)` | `config/fonksiyonlar.php` | Menüyü basar (`linkler` tablosundan) |
| `ozel_url($slug)` | `config/fonksiyonlar_url.php` | Sayfa adresi üretir |
| `kategori_link_getir($id)` | `config/fonksiyonlar_url.php` | Kategori adresi |
| `birim_goster($gmd)` | `config/dil.php` | GMD → ziyaretçi birimi |

## 7. Tema dosya haritası (`temalar/limon_ilan/`)

| Yol | İçerik |
|---|---|
| `index.php` | Ana sayfa (bileşenler) |
| `ilan.php` | İlan detay (özellik tablosu, fotoğraf/video, mesaj, güvenlik ipuçları) |
| `ilan-kategori.php` | Kategori listesi + **filtre paneli** (il/ilçe, fiyat, kategori alanları) |
| `magaza.php` | Mağaza sayfası |
| `profil.php` | Üye profili |
| `ara.php` | Arama sonuçları + filtreler |
| `footer.php`, `header.php` | Ortak başlık/altbilgi (dil seçici, para birimi seçici, sosyal) |
| `bilesen/` | Slider, listeleme (ikili/tablo/resimsiz), harita, kategori kutucuğu |
| `inc/` | Ortak parçalar (`alert.php`, `uye-limit-bilgi.php`, `kategori-listeleme-*.php`) |
| `sayfa/` | `kayit-ol`, `giris-yap`, `sifremi-unuttum`, `ilan-ekle`, `doping`, `paket-satin-al`, `siparis`, `siparislerim`, `magazalarim`, `profil-guncelle`… |
| `panel/` | Tema yönetim paneli (sipariş onaylama, kategoriler, ipler, dopingler) |
| `iyzico/` | **Pasif** ödeme SDK'sı (üçüncü parti — elle değiştirme) |

## 8. Yönetim paneli akışları (sipariş onayı)

- Panel dosyaları: `temalar/limon_ilan/panel/siparisler-{doping,get,magaza,paket}.php`
- Onay: `?onay=<siparis_id>` → `durum=1` + ilgili özelliğin aktive edilmesi +
  üyeye `bildirim_ekle()` + e-posta (`auto_mail_add`).
- Bildirim: `ajax.php?islem=odeme_bildirimi` (kullanıcı ödeme bildirir → yöneticiye).

## 9. Değişiklik sonrası kontrol listesi

```bash
php -l <değişen dosya>                       # sözdizimi
php tests/LanguageSystemTest.php             # dil/para testi (0 = geçti)
# tüm iç linkleri 4 dilde tara → PHP hatası olan sayfa 0 olmalı
powershell -ExecutionPolicy Bypass -File otomatik_yedek.ps1
git status && git add <spesifik dosyalar> && git commit -m "Türkçe tek cümle"
```

- Görünüm/JS değiştiyse tarayıcıda gerçek kontrol yap.
- DB'ye yazan bir adım varsa **önce** `SQL/yedekler/yedek_<isim>.sql` al.
- `cache/` içeriğini sil (bayat HTML).

## 10. Bilinen, kabul edilmiş teknik borç

- Sorgular hâlâ string birleştirmeyle kuruluyor (yapısal SQL injection riski) —
  **yeni** kod prepared statement kullanmalı, eskiyi kendiliğinden çevirme.
- CSRF token'ı yok; `lpanel/` yazma işlemlerinde yetki kontrolü teyit edilmeli.
- `lpanel/js/ckeditor`, `eklentiler/smtp/phpmailer` güncel değil.
- `display_errors=1` (yerel) → üretimde **0** yapılmalı.
- SSL yok → tarayıcı "Güvenli değil" uyarısı verir; üretim öncesi sertifika şart.
- Kategori ağacı N+1 sorgu yapıyor; önce ölç, sonra optimize et.
