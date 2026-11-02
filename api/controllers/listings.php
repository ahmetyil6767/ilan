<?php
/*
 * JaayQuick API - ilan (icerik tip='ilan') controller
 *
 * Site ilani 'icerik' tablosunda durur:
 *   baslik, icerik, tip, durum (0 taslak, 1 yayinda, 2 onayda, 3 kapali, 4 suresi dolmus),
 *   tarih_saat, g_tarih_saat, yazar (uye id), kategori ("(12),(34)" bicimi), url
 * Fiyat/para birimi/bolge/vitrin gorseli 'ekstra_bilgi' (EAV, tip='ilan') icinde;
 * fotograflar 'galeri' tablosunda (resim = tam URL, sira = siralama).
 */

const ILAN_DURUMLAR = array(0 => 'draft', 1 => 'published', 2 => 'pending', 3 => 'removed', 4 => 'expired');

function api_listings_rute($yontem, $alt)
{
	if ($alt === 'mine') {
		if ($yontem !== 'GET') {
			api_hata('Method not allowed', 405);
		}
		api_listings_mine();
		return;
	}

	if ($alt === null) {
		if ($yontem === 'POST') {
			api_listings_create();
			return;
		}
		if ($yontem === 'GET') {
			api_listings_list();
			return;
		}
		api_hata('Method not allowed', 405);
	}

	$id = is_numeric($alt) ? (int)$alt : 0;
	if ($id < 1) {
		api_hata('Route not found', 404);
	}
	if ($yontem === 'GET') {
		api_listings_detail($id);
		return;
	}
	if ($yontem === 'PUT' or $yontem === 'PATCH') {
		api_listings_update($id);
		return;
	}
	if ($yontem === 'DELETE') {
		api_listings_delete($id);
		return;
	}
	api_hata('Method not allowed', 405);
}

/* =========================================================
 *  Ortak: sorgu kurulumu + ilan->API dizisi donusturucu
 * ========================================================= */

function api_ilan_join_esle()
{
	return "
		left join ekstra_bilgi e_f   on e_f.id = c.id and e_f.tip='ilan'  and e_f.anahtar='fiyat'
		left join ekstra_bilgi e_pb  on e_pb.id = c.id and e_pb.tip='ilan' and e_pb.anahtar='para_birimi'
		left join ekstra_bilgi e_il  on e_il.id = c.id and e_il.tip='ilan' and e_il.anahtar='il'
		left join ekstra_bilgi e_ilc on e_ilc.id = c.id and e_ilc.tip='ilan' and e_ilc.anahtar='ilce'
		left join ekstra_bilgi e_go  on e_go.id = c.id and e_go.tip='ilan' and e_go.anahtar='gorsel'
		left join iller il_  on il_.id = CAST(e_il.deger AS UNSIGNED)
		left join ilceler lc on lc.id = CAST(e_ilc.deger AS UNSIGNED)
		left join uyeler u_  on u_.id = c.yazar
	";
}

function api_ilan_where_listesi(&$bagla, $sadece_yayinda = true)
{
	$parcalar = array("c.tip='ilan'");
	if ($sadece_yayinda) {
		$parcalar[] = 'c.durum=1';
	}
	$girdi = api_girdi();

	// arama: baslik + icerik
	$arama = isset($girdi['search']) ? trim((string)$girdi['search']) : '';
	if ($arama !== '') {
		$parcalar[] = '(c.baslik LIKE ? OR c.icerik LIKE ?)';
		$bagla[] = '%' . $arama . '%';
		$bagla[] = '%' . $arama . '%';
	}

	// kategori: icerik.kategori "(12),(...)" -> LIKE '%(12)%'
	$kategori = isset($girdi['category']) ? (int)$girdi['category'] : 0;
	if ($kategori > 0) {
		$parcalar[] = "c.kategori LIKE CONCAT('%(', ?, ')', '%')";
		$bagla[] = (string)$kategori;
	}

	// region: isim (Kanifing...) veya id; iller = Gambiya'nin 8 bolgesi
	$region = isset($girdi['region']) ? trim((string)$girdi['region']) : '';
	if ($region !== '') {
		if (is_numeric($region)) {
			$parcalar[] = 'e_il.deger = ?';
			$bagla[] = (string)(int)$region;
		} else {
			$il = api_sorgu_satir('select id from iller where il_adi=? limit 1', array($region));
			$il_id = $il ? (int)$il['id'] : 0;
			$parcalar[] = 'e_il.deger = ?';
			$bagla[] = (string)$il_id;
		}
	}

	// district (ilce): isim veya id
	$ilce = isset($girdi['district']) ? trim((string)$girdi['district']) : '';
	if ($ilce !== '') {
		if (!is_numeric($ilce)) {
			$ilce_kayit = api_sorgu_satir('select id from ilceler where ilce_adi=? limit 1', array($ilce));
			$ilce = $ilce_kayit ? (string)(int)$ilce_kayit['id'] : '';
		}
		if ($ilce !== '') {
			$parcalar[] = 'e_ilc.deger = ?';
			$bagla[] = (string)(int)$ilce;
		}
	}

	// fiyat araligi: yalniz GMD kayitlari (usd/eur/gbp dogrudan karsilastirilamaz)
	$fiyat_sayisal = "(REPLACE(REPLACE(IFNULL(e_f.deger,''),'.',''),',','.')+0)";
	$fiyat_min = isset($girdi['price_min']) and $girdi['price_min'] !== '' ? (float)$girdi['price_min'] : null;
	$fiyat_max = isset($girdi['price_max']) and $girdi['price_max'] !== '' ? (float)$girdi['price_max'] : null;
	if ($fiyat_min !== null or $fiyat_max !== null) {
		$parcalar[] = "(e_pb.deger='gmd' OR e_pb.deger IS NULL OR e_pb.deger='')";
		if ($fiyat_min !== null) {
			$parcalar[] = "$fiyat_sayisal >= ?";
			$bagla[] = $fiyat_min;
		}
		if ($fiyat_max !== null) {
			$parcalar[] = "$fiyat_sayisal <= ?";
			$bagla[] = $fiyat_max;
		}
	}

	return implode(' AND ', $parcalar);
}

function api_ilan_sira_cumlesi($sirala)
{
	switch ($sirala) {
		case 'price_asc':
			return ' ORDER BY fiyat_sayisal ASC, c.g_tarih_saat DESC';
		case 'price_desc':
			return ' ORDER BY fiyat_sayisal DESC, c.g_tarih_saat DESC';
		case 'latest':
		default:
			return ' ORDER BY c.g_tarih_saat DESC, c.id DESC';
	}
}

function api_ilan_select_cumlesi($sayisal_alias = false)
{
	$select = "
		c.id, c.baslik, c.icerik, c.url, c.tarih_saat, c.g_tarih_saat, c.kategori, c.yazar, c.durum,
		e_f.deger AS fiyat, e_pb.deger AS para_birimi, e_go.deger AS gorsel,
		il_.il_adi AS region, lc.ilce_adi AS district, u_.ad_soyad AS seller_name";
	if ($sayisal_alias) {
		$select .= ",\n\t\t(REPLACE(REPLACE(IFNULL(e_f.deger,''),'.',''),',','.')+0) AS fiyat_sayisal";
	}
	return $select;
}

function api_ilan_tarihi($kayit)
{
	foreach (array('g_tarih_saat', 'tarih_saat') as $kolon) {
		$metin = isset($kayit[$kolon]) ? trim((string)$kayit[$kolon]) : '';
		if ($metin === '') {
			continue;
		}
		if (preg_match('/^\d{4}-\d{2}-\d{2}/', $metin)) {
			return $metin;
		}
		// ilan formu "d.m.Y H:i" formatinda saklayabilir
		$eski = DateTime::createFromFormat('d.m.Y H:i', $metin);
		if ($eski instanceof DateTime) {
			return $eski->format('Y-m-d H:i:s');
		}
		$eski2 = DateTime::createFromFormat('d.m.Y H:i:s', $metin);
		if ($eski2 instanceof DateTime) {
			return $eski2->format('Y-m-d H:i:s');
		}
	}
	return null;
}

/** Galerinin vitrin (gorsel) + galeri kayitlari; benzersiz liste doner */
function api_ilan_fotolari($ilan_id, $vitrin_url)
{
	$foto_urls = array();
	if ($vitrin_url !== null and $vitrin_url !== '') {
		$foto_urls[] = (string)$vitrin_url;
	}
	foreach (api_sorgu_satirlar('select resim from galeri where icerik_id=? order by sira asc, id asc', array($ilan_id)) as $gal) {
		$foto_urls[] = (string)$gal['resim'];
	}

	$fotolar = array();
	$gorulmus = array();
	foreach ($foto_urls as $resim_url) {
		$resim_url = trim((string)$resim_url);
		if ($resim_url === '' or isset($gorulmus[$resim_url])) {
			continue;
		}
		$gorulmus[$resim_url] = true;
		$fotolar[] = array(
			'url'      => api_resim_url($resim_url, '530_393'),
			'thumb'    => api_resim_url($resim_url, '160_120'),
			'original' => thumbnail_url('full', $resim_url),
		);
	}
	return $fotolar;
}

/** icerik satirini mobil uygulamanin bekledigi ilan dizisine cevirir */
function api_ilan_dizisi($kayit, $ozet = false)
{
	$ilan_id = (int)$kayit['id'];

	$tanim = trim(strip_tags((string)$kayit['icerik']));
	if ($ozet and function_exists('mb_strlen') and mb_strlen($tanim) > 260) {
		$tanim = mb_substr($tanim, 0, 260) . '…';
	} elseif ($ozet and strlen($tanim) > 260) {
		$tanim = substr($tanim, 0, 260) . '…';
	}

	// fiyat "1.234,56" -> 1234.56
	$fiyat_sayisi = null;
	if (isset($kayit['fiyat']) and $kayit['fiyat'] !== null and (string)$kayit['fiyat'] !== '') {
		$donustur = str_replace(array('.', ','), array('', '.'), (string)$kayit['fiyat']);
		if (is_numeric($donustur)) {
			$fiyat_sayisi = (float)$donustur;
		}
	}

	$para = strtolower((string)$kayit['para_birimi']);
	$birim = in_array($para, array('gmd', 'usd', 'eur', 'gbp'), true) ? strtoupper($para) : 'GMD';

	$kategori_idleri = array();
	if (!empty($kayit['kategori'])) {
		preg_match_all('/\((\d+)\)/', (string)$kayit['kategori'], $eslesen);
		if (isset($eslesen[1])) {
			foreach ($eslesen[1] as $kid) {
				$kategori_idleri[] = (int)$kid;
			}
		}
	}

	$durum_index = (int)$kayit['durum'];
	$durum = isset(ILAN_DURUMLAR[$durum_index]) ? ILAN_DURUMLAR[$durum_index] : 'unknown';

	return array(
		'id'          => $ilan_id,
		'title'       => (string)$kayit['baslik'],
		'description' => $tanim,
		'price'       => $fiyat_sayisi,
		'currency'    => $birim,
		'category'    => $kategori_idleri,
		'region'      => !empty($kayit['region']) ? (string)$kayit['region'] : null,
		'district'    => !empty($kayit['district']) ? (string)$kayit['district'] : null,
		'photos'      => api_ilan_fotolari($ilan_id, isset($kayit['gorsel']) ? $kayit['gorsel'] : null),
		'status'      => $durum,
		'created_at'  => api_ilan_tarihi($kayit),
		'slug'        => (string)$kayit['url'],
		'seller'      => array(
			'id'   => (int)$kayit['yazar'],
			'name' => !empty($kayit['seller_name']) ? (string)$kayit['seller_name'] : null,
		),
	);
}

/* =========================================================
 *  GET /listings  (public, filtreli + sayfali)
 * ========================================================= */

function api_listings_list()
{
	global $baglan;
	$girdi = api_girdi();

	$page = isset($girdi['page']) ? max(1, (int)$girdi['page']) : 1;
	$per_page = isset($girdi['per_page']) ? (int)$girdi['per_page'] : 20;
	$per_page = min(50, max(1, $per_page));
	$sirala = isset($girdi['sort']) ? (string)$girdi['sort'] : 'latest';

	$bagla = array();
	$where = api_ilan_where_listesi($bagla, true);
	$joins = api_ilan_join_esle();

	// toplam kayit (ayni filtre)
	$toplam = (int)api_sorgu_deger("select count(c.id) from icerik c $joins where $where", $bagla);
	$last_page = max(1, (int)ceil($toplam / $per_page));

	// sayfa
	$limit = $per_page;
	$baslangic = ($page - 1) * $per_page;
	$satirlar = api_sorgu_satirlar(
		'select ' . api_ilan_select_cumlesi(true) . " from icerik c $joins where $where"
		. api_ilan_sira_cumlesi($sirala) . ' LIMIT ? OFFSET ?',
		array_merge($bagla, array($limit, $baslangic))
	);

	$veriler = array();
	foreach ($satirlar as $kayit) {
		$veriler[] = api_ilan_dizisi($kayit, true);
	}

	api_basari(array(
		'data' => $veriler,
		'meta' => array(
			'current_page' => $page,
			'per_page'     => $per_page,
			'total'        => $toplam,
			'last_page'    => $last_page,
		),
	));
}

/* =========================================================
 *  GET /listings/:id  (detail; onayda/taslak yalniz sahibi gorur)
 * ========================================================= */

function api_listings_detail($id)
{
	$uye = api_uye(false);
	$joins = api_ilan_join_esle();
	$kayit = api_sorgu_satir(
		'select ' . api_ilan_select_cumlesi() . " from icerik c $joins where c.id=? limit 1",
		array($id)
	);
	if (!$kayit) {
		api_hata('Listing not found', 404);
	}

	// yayinda olmayan ilanlar yalnizca sahibi tarafindan gorulur
	if ((int)$kayit['durum'] !== 1 and (!$uye or (int)$kayit['yazar'] !== (int)$uye['id'])) {
		api_hata('Listing not found', 404);
	}

	$ilan = api_ilan_dizisi($kayit, false);
	$ilan['seller']['phone'] = api_uye_telefonu((int)$kayit['yazar']);

	api_basari(array('data' => $ilan));
}

/* =========================================================
 *  GET /listings/mine (Bearer) - kullanici ilanlari, tum durumlar
 * ========================================================= */

function api_listings_mine()
{
	$uye = api_uye(true);
	$girdi = api_girdi();
	$page = isset($girdi['page']) ? max(1, (int)$girdi['page']) : 1;
	$per_page = isset($girdi['per_page']) ? min(50, max(1, (int)$girdi['per_page'])) : 20;

	$joins = api_ilan_join_esle();
	$toplam = 0;
	$satir_sayisi = api_sorgu_satir(
		"select count(c.id) AS toplam from icerik c where c.yazar=? and c.tip='ilan'",
		array($uye['id'])
	);
	if ($satir_sayisi) {
		$toplam = (int)$satir_sayisi['toplam'];
	}

	$limit = $per_page;
	$baslangic = ($page - 1) * $per_page;
	$satirlar = api_sorgu_satirlar(
		'select ' . api_ilan_select_cumlesi() . " from icerik c $joins where c.yazar=? and c.tip='ilan'"
		. ' ORDER BY c.g_tarih_saat DESC, c.id DESC LIMIT ? OFFSET ?',
		array($uye['id'], $limit, $baslangic)
	);

	$veriler = array();
	foreach ($satirlar as $kayit) {
		$veriler[] = api_ilan_dizisi($kayit, true);
	}

	api_basari(array(
		'data' => $veriler,
		'meta' => array(
			'current_page' => $page,
			'per_page'     => $per_page,
			'total'        => $toplam,
			'last_page'    => max(1, (int)ceil($toplam / $per_page)),
		),
	));
}

/* =========================================================
 *  POST /listings (Bearer, multipart/form-data)
 * ========================================================= */

/** Mobil'den gelen region adini/id'sini iller tablosundaki id'ye cevirir */
function api_region_id($region)
{
	if ($region === '' or $region === null) {
		return 0;
	}
	if (is_numeric($region)) {
		return (int)$region;
	}
	$il = api_sorgu_satir('select id from iller where il_adi=? limit 1', array($region));
	return $il ? (int)$il['id'] : 0;
}

function api_ilce_id($ilce, $il_id)
{
	if ($ilce === '' or $ilce === null) {
		return 0;
	}
	if (is_numeric($ilce)) {
		return (int)$ilce;
	}
	$ilce_kayit = api_sorgu_satir('select id from ilceler where ilce_adi=? and il_id=? limit 1', array($ilce, $il_id));
	return $ilce_kayit ? (int)$ilce_kayit['id'] : 0;
}

/** benzersiz ilan url (slug) uretir */
function api_ilan_url($baslik)
{
	$temel = url_olustur($baslik);
	if ($temel === '') {
		$temel = 'ilan';
	}
	$aday = $temel;
	$i = 1;
	while (api_sorgu_deger('select id from icerik where url=? limit 1', array($aday)) !== null) {
		$aday = $temel . '-' . ($i++);
	}
	return $aday;
}

/** $_FILES['photos'] dizi yapisindan tek dosya yukleyici + guvenlik kontrolu */
function api_yuklenen_foto_urls($alan = 'photos')
{
	if (!isset($_FILES[$alan]) or !isset($_FILES[$alan]['tmp_name']) or !is_array($_FILES[$alan]['name'])) {
		return array('urls' => array(), 'hatalar' => array());
	}
	$urls = array();
	$hatalar = array();
	$kota = is_array($_FILES[$alan]['tmp_name']) ? count($_FILES[$alan]['tmp_name']) : 0;

	for ($i = 0; $i < $kota; $i++) {
		$tmp = $_FILES[$alan]['tmp_name'][$i];
		if ($_FILES[$alan]['error'][$i] !== UPLOAD_ERR_OK or !$tmp or !is_uploaded_file($tmp)) {
			continue;
		}
		// icerik dogrulama: getimagesize ile mime kontrol (uploads PHP calistirmaya kapali; yine de)
		$boyut = @getimagesize($tmp);
		if ($boyut === false) {
			$hatalar[] = "Photo #" . ($i + 1) . ' is not a valid image.';
			continue;
		}
		$izinler = array('image/jpeg', 'image/png', 'image/gif', 'image/webp');
		if (!in_array($boyut['mime'], $izinler, true)) {
			$hatalar[] = "Photo #" . ($i + 1) . ' is not a valid image.';
			continue;
		}

		// dosyayi tek dosya $_FILES yapiya cevir ve yukle() yardimcisiyla kaydet
		$tek_dosya = array(
			'name'     => $_FILES[$alan]['name'][$i],
			'type'     => $_FILES[$alan]['type'][$i],
			'tmp_name' => $tmp,
			'error'    => $_FILES[$alan]['error'][$i],
			'size'     => $_FILES[$alan]['size'][$i],
		);
		$secenekler = array(
			'dizin'  => 'ajax',         // site ilan formu da ayni dizini kullanir
			'boyutlar' => array('160_120', '530_393'),
			'cikti'  => null,           // tam URL doner (orijinal korunur)
		);
		$url = yukle($tek_dosya, $secenekler);
		if ($url) {
			$urls[] = (string)$url;
		} else {
			$hatalar[] = "Photo #" . ($i + 1) . ' could not be uploaded.';
		}
	}
	return array('urls' => $urls, 'hatalar' => $hatalar);
}

function api_listings_create()
{
	global $baglan;
	$uye = api_uye(true);
	$girdi = api_girdi();

	$baslik = api_metin('title');
	$aciklama = api_metin('description');
	$kategori_id = isset($girdi['category_id']) ? (int)$girdi['category_id'] : (isset($girdi['categoryId']) ? (int)$girdi['categoryId'] : 0);
	$region = api_metin('region');
	$ilce = api_metin('district');
	$fiyat = api_sayi('price');

	$hatalar = array();
	if ($baslik === '') { $hatalar['title'] = 'Title is required.'; }
	if ($aciklama === '') { $hatalar['description'] = 'Description is required.'; }
	if ($kategori_id < 1) { $hatalar['category_id'] = 'Category is required.'; }
	if ($fiyat === null || $fiyat < 0) { $hatalar['price'] = 'Price is required.'; }

	// kategori kontrol: tip='ilan' olanlar
	if ($kategori_id >= 1) {
		$kategori = api_sorgu_satir("select id, tip from kategoriler where id=? limit 1", array($kategori_id));
		if (!$kategori) {
			$hatalar['category_id'] = 'Category does not exist.';
		} else {
			// kategori tipine uygunluk: ilan kategorileri tip='ilan'
			if (trim((string)$kategori['tip']) !== 'ilan' and trim((string)$kategori['tip']) !== '') {
				$hatalar['category_id'] = 'Category does not accept listings.';
			}
		}
	}

	if (count($hatalar)) {
		api_hata('Validation failed.', 422, $hatalar);
	}

	$il_id  = api_region_id($region);
	$ilce_id = api_ilce_id($ilce, $il_id);
	if ($region !== '' and $il_id < 1) {
		api_hata('Region is not valid.', 422, array('region' => 'Unknown region.'));
	}

	// ilan limitleri (tema fonksiyonlari; yoksa atlanir)
	$durum = 1;
	if (function_exists('uye_ilan_limiti')) {
		$aninda_onay = (int)uye_ilan_limiti('aninda_onay', $uye['id']);
		$kalan_ilan = (int)uye_ilan_limiti('kalan', $uye['id']);
		if ($kalan_ilan <= 0) {
			api_hata('Your listing limit is full', 422);
		}
		$durum = $aninda_onay === 1 ? 1 : 2; // aninda onay yetkisi yoksa incelemeye gider
	}

	// icerik kaydi (tip='ilan')
	$ilan_url = api_ilan_url($baslik);
	$kategori_str = '(' . $kategori_id . ')';
	// mobil girdisi duz metindir; HTML olarak goruntulendigi icin kacirilir
	$icerik_html = nl2br(htmlspecialchars($aciklama, ENT_QUOTES, 'UTF-8'));
	$tarih = tarih_saat();
	$sonuc = api_sorgu(
		"insert into icerik set baslik=?, url=?, icerik=?, kategori=?, tarih_saat=?, g_tarih_saat=?, yazar=?, durum=?, tip='ilan'",
		array($baslik, $ilan_url, $icerik_html, $kategori_str, $tarih, $tarih, $uye['id'], $durum)
	);
	if (!$sonuc) {
		api_hata('Listing could not be created.', 500);
	}
	$ilan_id = (int)mysqli_insert_id($baglan);

	// kategori ilan sayisini site ile ayni tut
	if (function_exists('kategori_icerik_sayisi_guncelle')) {
		kategori_icerik_sayisi_guncelle($kategori_id);
	}

	// ekstra bilgiler (site konvansiyonu: tip='ilan')
	api_ekstra_yaz('fiyat', api_fiyat_metni($fiyat), $ilan_id);
	api_ekstra_yaz('para_birimi', 'gmd', $ilan_id);
	if ($il_id) {
		api_ekstra_yaz('il', (string)$il_id, $ilan_id);
	}
	if ($ilce_id) {
		api_ekstra_yaz('ilce', (string)$ilce_id, $ilan_id);
	}

	// fotograflar
	$foto_sonuc = api_yuklenen_foto_urls();
	if (count($foto_sonuc['urls'])) {
		foreach ($foto_sonuc['urls'] as $sira => $foto_url) {
			api_galeri_ekle($foto_url, $ilan_id, ($sira + 1) * 10);
		}
		// vitrin gorseli = ilk fotoğraf
		api_ekstra_yaz('gorsel', $foto_sonuc['urls'][0], $ilan_id);
	}

	// vitrin (ilk fotoğraf) sonrası yanıt
	$kayit = api_sorgu_satir('select ' . api_ilan_select_cumlesi() . ' from icerik c ' . api_ilan_join_esle() . ' where c.id=? limit 1', array($ilan_id));
	$ilan = $kayit ? api_ilan_dizisi($kayit, false) : array('id' => $ilan_id);

	$mesaj = $durum === 1 ? 'Your listing is published.' : 'Your listing has been submitted for review.';
	api_basari(array(
		'data'    => $ilan,
		'message' => $mesaj,
		'errors'  => count($foto_sonuc['hatalar']) ? $foto_sonuc['hatalar'] : null,
	), 201);
}

/** galeri kaydi (galeri_ekle string birlestirir; burada prepared) */
function api_galeri_ekle($resim_url, $ilan_id, $sira = 0)
{
	$mevcut = (int)api_sorgu_deger('select count(id) AS sayi from galeri where icerik_id=?', array($ilan_id));
	$son_sira = $sira > 0 ? $sira : ($mevcut * 10 + 10);
	return api_sorgu(
		'insert into galeri set resim=?, icerik_id=?, baslik="galeri", url="galeri", sira=?',
		array($resim_url, $ilan_id, $son_sira)
	) !== false;
}

/* =========================================================
 *  PUT /listings/:id (Bearer + JSON) - sahibi metin/ekstra gunceller
 * ========================================================= */

function api_listings_update($id)
{
	$uye = api_uye(true);

	$kayit = api_sorgu_satir("select id, yazar, durum, tip from icerik where id=? limit 1", array($id));
	if (!$kayit or $kayit['tip'] !== 'ilan') {
		api_hata('Listing not found', 404);
	}
	if ((int)$kayit['yazar'] !== (int)$uye['id'] and $uye['yetki'] !== 'admin') {
		api_hata('Forbidden', 403);
	}

	$girdi = api_girdi();
	$alanlar = array();
	if (isset($girdi['title']) and $girdi['title'] !== '') { $alanlar[] = array('baslik', api_metin('title')); }
	if (isset($girdi['description']) and $girdi['description'] !== '') {
		$alanlar[] = array('icerik', nl2br(htmlspecialchars(api_metin('description'), ENT_QUOTES, 'UTF-8')));
	}
	if (isset($girdi['price']) and $girdi['price'] !== '' and api_sayi('price') !== null) {
		api_ekstra_yaz('fiyat', api_fiyat_metni(api_sayi('price')), $id);
	}
	if (isset($girdi['region']) and $girdi['region'] !== '') {
		$il_id = api_region_id($girdi['region']);
		if ($il_id > 0) {
			api_ekstra_yaz('il', (string)$il_id, $id);
		}
	}
	if (isset($girdi['district']) and $girdi['district'] !== '') {
		$il_id = (int)(api_ekstra('il', $id) ?: 0);
		$ilce_id = api_ilce_id($girdi['district'], $il_id);
		if ($ilce_id > 0) {
			api_ekstra_yaz('ilce', (string)$ilce_id, $id);
		}
	}

	foreach ($alanlar as list($kolon, $deger)) {
		api_sorgu("update icerik set $kolon=? where id=? limit 1", array($deger, $id));
	}

	// yeni görseller eklenirse galeriye eklenir
	$foto_sonuc = api_yuklenen_foto_urls();
	if (count($foto_sonuc['urls'])) {
		$kac = (int)api_sorgu_deger('select count(id) AS sayi from galeri where icerik_id=?', array($id));
		foreach ($foto_sonuc['urls'] as $sira => $foto_url) {
			api_galeri_ekle($foto_url, $id, ($kac + $sira + 1) * 10);
		}
		$mevcut_gorsel = api_ekstra('gorsel', $id);
		if (empty($mevcut_gorsel)) {
			api_ekstra_yaz('gorsel', $foto_sonuc['urls'][0], $id);
		}
	}

	$kayit = api_sorgu_satir('select ' . api_ilan_select_cumlesi() . ' from icerik c ' . api_ilan_join_esle() . ' where c.id=? limit 1', array($id));
	api_basari(array('data' => $kayit ? api_ilan_dizisi($kayit, false) : null, 'errors' => count($foto_sonuc['hatalar']) ? $foto_sonuc['hatalar'] : null));
}

/* =========================================================
 *  DELETE /listings/:id (Bearer) - durum=3 + sebep
 * ========================================================= */

function api_listings_delete($id)
{
	$uye = api_uye(true);

	$kayit = api_sorgu_satir('select id, yazar, tip from icerik where id=? limit 1', array($id));
	if (!$kayit or $kayit['tip'] !== 'ilan') {
		api_hata('Listing not found', 404);
	}
	if ((int)$kayit['yazar'] !== (int)$uye['id'] and $uye['yetki'] !== 'admin') {
		api_hata('Forbidden', 403);
	}

	api_sorgu('update icerik set durum=3 where id=? limit 1', array($id));
	api_ekstra_yaz('sebep', 'kullanici_sildi', $id);

	api_basari(array('ok' => true));
}
