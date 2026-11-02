<?php
/*
 * JaayQuick API - kategoriler + bolgeler controller
 *
 * kategoriler: id, baslik, url, ust_kategori (0 = ust seviye), tip ('ilan'), sinif
 * iller (Gambiya bolgeleri): id, il_adi
 * ilceler: id, il_id, ilce_adi
 */

function api_kategoriler()
{
	// butun ilan kategorileri tek sorguda ust/alt gruplanir
	$tum = api_sorgu_satirlar("select id, baslik, url, ust_kategori from kategoriler where tip='ilan' order by baslik asc");

	$ustler = array();
	$cocuklar = array();
	$kategori_ustu = array();
	foreach ($tum as $kat) {
		$ust = (int)$kat['ust_kategori'];
		if ($ust === 0) {
			$ustler[(int)$kat['id']] = $kat;
		} else {
			$cocuklar[$ust][] = $kat;
		}
		$kategori_ustu[(int)$kat['id']] = $ust;
	}

	// yayindaki ilanlardan kategori dagilimini hesapla (tek sorgu, az kayit)
	$kayitlar = api_sorgu_satirlar("select id, kategori from icerik where tip='ilan' and durum='1'");

	$alt_sayilari = $ust_sayilari = array();
	foreach ($kayitlar as $kayit) {
		preg_match_all('/\((\d+)\)/', (string)$kayit['kategori'], $eslesen);
		foreach ($eslesen[1] as $kid) {
			$kid = (int)$kid;
			$alt_sayilari[$kid] = isset($alt_sayilari[$kid]) ? $alt_sayilari[$kid] + 1 : 1;
			// alt kategori ise uste daha önce toplanmadıysa ust sayisini artir
			if (isset($kategori_ustu[$kid]) and $kategori_ustu[$kid] !== 0) {
				$ust_id = $kategori_ustu[$kid];
				$ust_sayilari[$ust_id] = isset($ust_sayilari[$ust_id]) ? $ust_sayilari[$ust_id] + 1 : 1;
			}
		}
	}

	$veriler = array();
	foreach ($ustler as $ust_id => $kat) {
		$cocuk_list = isset($cocuklar[$ust_id]) ? $cocuklar[$ust_id] : array();
		foreach ($cocuk_list as $cocuk) {
			$alt_id = (int)$cocuk['id'];
			$altlar[] = array(
				'id'    => $alt_id,
				'name'  => $cocuk['baslik'],
				'slug'  => $cocuk['url'],
				'count' => isset($alt_sayilari[$alt_id]) ? $alt_sayilari[$alt_id] : 0,
			);
		}

		$veriler[] = array(
			'id'       => (int)$ust_id,
			'name'     => $kat['baslik'],
			'slug'     => $kat['url'],
			'count'    => isset($ust_sayilari[$ust_id]) ? $ust_sayilari[$ust_id] : 0,
			'icon'     => api_kategori_ikonu($kat['baslik']),
			'children' => $altlar,
		);
	}

	api_basari(array('data' => $veriler));
}

/** Baslik metninden basit bir emoji ikon dondurur */
function api_kategori_ikonu($baslik)
{
	$b = mb_strtolower((string)$baslik, 'UTF-8');
	$map = array(
		'vasıta' => '🚗', 'auto' => '🚗', 'vehicle' => '🚗', 'car' => '🚗',
		'emlak' => '🏠', 'ev' => '🏠', 'property' => '🏠', 'house' => '🏠',
		'telefon' => '📱', 'phone' => '📱', 'mobil' => '📱',
		'elektronik' => '💻', 'electronics' => '💻', 'bilgisayar' => '💻',
		'mobilya' => '🛋️', 'furniture' => '🛋️',
		'giyim' => '👕', 'fashion' => '👕', 'clothing' => '👕',
		'is' => '💼', 'iş' => '💼', 'job' => '💼',
		'hayvan' => '🐾', 'animal' => '🐾', 'pets' => '🐾',
		'hizmet' => '🔧', 'service' => '🔧',
	);
	foreach ($map as $anahtar => $ikon) {
		if (strpos($b, $anahtar) !== false) {
			return $ikon;
		}
	}
	return '🏷️';
}

/** GET /regions - Gambiya bolgeleri (iller) + ilceleri */
function api_bolgeler()
{
	$iller   = api_sorgu_satirlar('select id, il_adi from iller order by id asc');
	$ilceler = api_sorgu_satirlar('select id, il_id, ilce_adi from ilceler order by ilce_adi asc');

	$grup = array();
	foreach ($ilceler as $ilce) {
		$il_id = (int)$ilce['il_id'];
		if (!isset($grup[$il_id])) {
			$grup[$il_id] = array();
		}
		$grup[$il_id][] = array('id' => (int)$ilce['id'], 'name' => $ilce['ilce_adi']);
	}

	$veriler = array();
	foreach ($iller as $il) {
		$il_id = (int)$il['id'];
		$veriler[] = array(
			'id'        => $il_id,
			'name'      => $il['il_adi'],
			'districts' => isset($grup[$il_id]) ? $grup[$il_id] : array(),
		);
	}

	api_basari(array('data' => $veriler));
}

/** GET /districts?region=<isim|id> - tek bolgenin ilceleri */
function api_ilceler($girdi)
{
	$region = isset($girdi['region']) ? trim((string)$girdi['region']) : '';
	if ($region === '') {
		api_hata('Region is required (?region=...)');
	}

	if (is_numeric($region)) {
		$il_id = (int)$region;
	} else {
		$il = api_sorgu_satir('select id from iller where il_adi=? limit 1', array($region));
		if (!$il) {
			api_hata('Region not found', 404);
		}
		$il_id = (int)$il['id'];
	}

	$veriler = array();
	foreach (api_sorgu_satirlar('select id, ilce_adi from ilceler where il_id=? order by ilce_adi asc', array($il_id)) as $ilce) {
		$veriler[] = array('id' => (int)$ilce['id'], 'name' => $ilce['ilce_adi']);
	}
	api_basari(array('data' => $veriler));
}
