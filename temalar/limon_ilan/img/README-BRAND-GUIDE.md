# jaayquick.com - Brand Assets & Logo Kit

Tebrikler! jaayquick.com markası için tasarlanan tüm kurumsal logo ve ikon dosyaları bu pakette yer almaktadır.

---

## 📁 Paket İçeriği
1. `jaayquick-logo-primary.svg`: Beyaz ve açık zeminler için ana yatay logo (Web sitesi header, fatura, e-posta şablonu).
2. `jaayquick-logo-dark.svg`: Koyu zeminler için ters renk (White/Navy/Green) yatay logo (Dark mode header, footer, afişler).
3. `jaayquick-icon-favicon.svg`: 1:1 kare uygulama ikonu ve favicon (Mobil uygulama, tarayıcı sekmesi, profil resmi).
4. `colors.json`: Web geliştirme (Tailwind CSS, CSS variable vb.) için hazır renk paleti.

---

## 🎨 Resmi Renk Paleti (Hex / RGB)
* **Navy Blue (jaay / Ana Kurumsal):** `#0B2545` | rgb(11, 37, 69)
* **Bright Green (quick / Dinamik):** `#7BB31A` | rgb(123, 179, 26)
* **Gambia Flag Red:** `#DF1623` | rgb(223, 22, 35)
* **Gambia Flag Blue:** `#0047AB` | rgb(0, 71, 171)
* **Gambia Flag Green:** `#3A7D44` | rgb(58, 125, 68)
* **Transit Orange (Hızlı Paket/El):** `#F28C28` | rgb(242, 140, 40)

---

## 🔤 Tipografi (Yazı Tipleri)
* **Başlık & Logo:** Montserrat Bold / Comfortaa Bold (Google Fonts ücretsiz)
* **Slogan & Arayüz:** Poppins / Inter / Segoe UI
* **Slogan Metni:** *"The Gambia's Fast Marketplace"* veya *"Buy & Sell Instantly"*

---

## 🚀 Tailwind CSS Yapılandırması (Örnek)
```javascript
module.exports = {
  theme: {
    extend: {
      colors: {
        jaay: {
          navy: '#0B2545',
          green: '#7BB31A',
          orange: '#F28C28',
          red: '#DF1623',
          blue: '#0047AB',
          forest: '#3A7D44',
        }
      }
    }
  }
}
```