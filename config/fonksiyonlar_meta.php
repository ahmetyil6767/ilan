<?php
/*
 * Sayfa basligi ve aciklamasi uretimi.
 *
 * Metinler aktif dile baglandi. Iki duzeltme de yapildi:
 *  - "basliga_ekle" ayari bos birakilabilecegi icin birlestirme sonrasi
 *    sondaki bosluk temizleniyor (eski halde <title>Sonuçlar: "" </title>
 *    gibi bos tirnakli, sonda bosluklu basliklar uretiliyordu).
 *  - Arama yapilmadan ara.php acildiginda terim bos oldugu icin
 *    "Sonuçlar: \"\"" yerine duz bir "Arama" basligi donuyor.
 */

if (count(get_included_files()) == 1) { die(); }

/*
 * Kategori ozel alanlari ve teknik ozellik etiketlerini aktif dile cevirir.
 * Sozluk lang/spec.php icinde kayitli; kayit yoksa kaynak metin dokundurulur.
 */
function spec_etiket($etiket)
{
	static $sozluk = null;
	if ($sozluk === null) {
		$sozluk = require dirname(__DIR__) . '/lang/spec.php';
	}
	$etiket = trim((string)$etiket);
	if ($etiket === '' || aktif_dil() == 'tr') { return $etiket; }
	if (isset($sozluk[$etiket])) {
		$idx = aktif_dil() == 'en' ? 0 : (aktif_dil() == 'fr' ? 1 : 2);
		$deger = $sozluk[$etiket][$idx];
		if (is_string($deger) && trim($deger) !== '') { return $deger; }
	}
	return $etiket;
}

/*
 * Basliga eklenen metni birlestirip sondaki bosluklari temizler.
 */
function _META_BIRLESTIR($baslik, $ekle) {
	$ekle = trim((string)$ekle);
	if ($ekle !== '') { $baslik = trim($baslik . ' ' . $ekle); }
	return trim((string)$baslik);
}

function meta_title()
{
	$php_self = $_SERVER["PHP_SELF"];
	$basliga_ekle = ayar_getir("basliga_ekle");
	$site_baslik = ayar_getir("site_baslik");

	/*
	 * index.php altindaki sayfalar: ?p=... ile gelir.
	 * Anahtar = sayfa adi, deger = ceviri anahtari.
	 */
	$sayfaBasliklari = array(
		'giris-yap'           => 'title_login',
		'kayit-ol'            => 'title_register',
		'sifremi-unuttum'     => 'title_forgot',
		'ilan-ekle'           => 'title_free_post',
		'mesajlarim'          => 'title_messages',
		'profil-bilgilerim'   => 'title_profile_info',
		'plan-detaylari'      => 'title_plan_details',
		'bildirimlerim'       => 'title_notifications',
		'favori-ilanlarim'    => 'title_favorites',
		'ilanlarim'           => 'title_my_ads',
		'magazalarim'         => 'title_my_stores',
		'siparislerim'        => 'title_my_orders',
		'get-satislarim'      => 'title_my_sales',
		'siparis'             => 'title_order_summary',
		'profil-guncelle'     => 'title_update_profile',
		'profil-gorseli'      => 'title_upload_photo',
		'kapak-gorseli'       => 'title_upload_cover',
		'magaza-profil-gorseli' => 'title_store_photo',
		'magaza-kapak-gorseli'  => 'title_store_cover',
		'magaza'              => 'title_store',
		'magaza-plan-detaylari' => 'title_store_plan',
		'magaza-bilgileri'    => 'title_update_store',
		'danismanlar'         => 'title_store_advisers',
		'yazilar'             => 'title_all_posts',
		'acil-acil'           => 'title_urgent',
		'fiyati-dusenler'     => 'title_price_drops',
		'vitrin-ilanlari'     => 'title_all_featured',
		'son-48-saat'        => 'title_last_48h',
		'son-1-hafta'         => 'title_last_week',
		'son-1-ay'            => 'title_last_month',
		'doping'              => 'title_buy_boost',
		'paket-satin-al'      => 'title_buy_package',
		'magaza-paket-satin-al' => 'title_store_package',
		'get-ilanlari'        => 'title_get_ads',
		'magazalar'           => 'title_all_stores',
	);

	if (strstr($php_self, 'index.php')) {
		$sayfa = @$_GET["p"];
		$magaza = @$_GET["magaza"];

		if ($sayfa) {
			if (isset($sayfaBasliklari[$sayfa])) {
				$baslik = _LANG($sayfaBasliklari[$sayfa]) . ' - ' . $site_baslik;
			} else {
				$baslik = $site_baslik;
			}
		} else if ($magaza) {
			$magaza_baslik = getir("magazalar", "baslik", $magaza);
			$baslik = $magaza_baslik . ' - ' . _LANG('meta_store_ads') . ' - ' . $site_baslik;
		} else {
			$baslik = $site_baslik;
		}
		return $baslik;
	}

	if (strstr($php_self, 'magaza.php')) {
		$magaza_id = @$_GET["id"];
		$magaza_url = @$_GET["url"];
		if (!$magaza_id) { $magaza_id = id_getir("magazalar", "subdomain", $magaza_url); }
		$magaza_baslik = getir("magazalar", "baslik", $magaza_id);
		return $magaza_baslik . ' - ' . _LANG('meta_store_ads') . ' - ' . $site_baslik;
	}

	if (strstr($php_self, 'kategori.php')) {
		$kategori_id = $_GET["id"];
		$seo = ekstra_bilgi_getir("seo_title", $kategori_id, "kategori");
		if ($seo) { return $seo; }
		$kategori_adi = _DIL_BASLIK(kategori_adi_getir($kategori_id), $kategori_id, "kategori");
		return _META_BIRLESTIR($kategori_adi, $basliga_ekle);
	}

	if (strstr($php_self, 'icerik.php')) {
		$icerik_id = $_GET["id"];
		$icerik_tip = bilgi_getir("icerik", "tip", $icerik_id);
		$seo = ekstra_bilgi_getir("seo_title", $icerik_id, $icerik_tip);
		if ($seo) { return $seo; }
		$icerik_baslik = _DIL_BASLIK(bilgi_getir("icerik", "baslik", $icerik_id), $icerik_id, "sayfa");
		return _META_BIRLESTIR($icerik_baslik, $basliga_ekle);
	}

	if (strstr($php_self, 'ara.php')) {
		$s = trim((string)@$_GET["s"]);
		if ($s === '') { return _LANG('title_search') . ' - ' . $site_baslik; }
		return _LANG('title_search') . ' ' . $s . ' - ' . $site_baslik;
	}

	if (strstr($php_self, 'profil.php')) {
		$ad = bilgi_getir("uyeler", "ad_soyad", $_GET["id"]);
		return _META_BIRLESTIR($ad, $basliga_ekle);
	}

	return $site_baslik;
}

function meta_description()
{
	$php_self = $_SERVER["PHP_SELF"];

	if (strstr($php_self, 'index.php')) {
		$sayfa = @$_GET["p"];
		$magaza = @$_GET["magaza"];
		$aciklama = ayar_getir("site_aciklama");
		if ($sayfa || $magaza) { return $aciklama; }
		return $aciklama;
	}

	if (strstr($php_self, 'magaza.php')) {
		$magaza_id = @$_GET["id"];
		$magaza_url = @$_GET["url"];
		if (!$magaza_id) { $magaza_id = id_getir("magazalar", "subdomain", $magaza_url); }
		$magaza_baslik = getir("magazalar", "baslik", $magaza_id);
		return $magaza_baslik . ' ' . _LANG('meta_store_ads');
	}

	if (strstr($php_self, 'kategori.php')) {
		$kategori_id = $_GET["id"];
		$seo = ekstra_bilgi_getir("seo_aciklama", $kategori_id, "kategori");
		if ($seo) { return $seo; }
		return ekstra_bilgi_getir("aciklama", $kategori_id, "kategori");
	}

	if (strstr($php_self, 'icerik.php')) {
		$icerik_id = $_GET["id"];
		$icerik_tip = bilgi_getir("icerik", "tip", $icerik_id);
		$seo = ekstra_bilgi_getir("seo_aciklama", $icerik_id, $icerik_tip);
		if ($seo !== "" && $seo !== null) { return $seo; }
		$ozet = strip_tags(bilgi_getir("icerik", "icerik", $icerik_id));
		$ozet = trim(preg_replace('/\s+/u', ' ', $ozet));
		if ($ozet === '') { return ayar_getir("site_aciklama"); }
		return mb_strlen($ozet) > 150 ? mb_substr($ozet, 0, 150) . '...' : $ozet;
	}

	if (strstr($php_self, 'ara.php')) {
		$s = trim((string)@$_GET["s"]);
		if ($s === '') { return ayar_getir("site_aciklama"); }
		return _LANG('meta_search_results') . ': "' . $s . '"';
	}

	if (strstr($php_self, 'profil.php')) {
		$ad = bilgi_getir("uyeler", "ad_soyad", $_GET["id"]);
		return $ad . ' ' . _LANG('meta_profile_of');
	}

	return ayar_getir("site_aciklama");
}

/*
 * Telefon yardimcilari (Gambiya).
 *
 * Sitede tel: ve wa.me baglantilari Gambia ulke koduyla (+220 / 220)
 * kurulur; Türkiye'ye ozel 90 oneki kullanilamaz.
 *
 * tel_rakamlari()      : numaradaki yalnizca rakamlari dondurur.
 * tel_uluslararasi()   : wa.me / tel:+ baglantilari icin tam uluslararasi
 *                        numara dondurur. Uye telefonu ulke kodu olmadan
 *                        girildigi icin (s_telefon_sifir ipucu) 220 onek
 *                        eklenir; zaten 220 ile baslayan numara degismez.
 */
function tel_rakamlari($tel)
{
	return preg_replace('/\D/', '', (string) $tel);
}

function tel_uluslararasi($tel, $ulke_kodu = '220')
{
	$rakamlar = tel_rakamlari($tel);
	if ($rakamlar === '') { return ''; }

	if (substr($rakamlar, 0, strlen($ulke_kodu)) == $ulke_kodu && strlen($rakamlar) > strlen($ulke_kodu)) {
		return $rakamlar;
	}

	$rakamlar = ltrim($rakamlar, '0');
	return $ulke_kodu . $rakamlar;
}