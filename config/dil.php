<?php
/*
 * Çeviri (i18n) altyapısı
 * ------------------------------------------------------------------
 * Yazılımda hiç çeviri katmanı yoktu; tüm metinler Türkçe olarak PHP
 * dosyalarına gömülüydü. Burada şunlar kuruluyor:
 *
 *   - _LANG('anahtar')  : çeviriyi döndürür, anahtar yoksa anahtarın
 *                        kendisini döndürür (yani eksik çeviri sayfayı
 *                        bozmaz, sadece İngilizce/Türkçe metin görünür)
 *   - _LANG_C()        : çeviri + HTML kaçışı
 *   - aktif_dil()      : şu anki dil kodu
 *   - _DIL_HTML_YON()  : 'dir="ltr"' veya 'dir="rtl"'
 *
 * Çeviriler lang/<dil>.php dosyalarında düz dizi olarak tutulur.
 * Türkçe kaynak dildir: ilk çıkarılan değerler doğrudan Türkçe metindir.
 */

if (count(get_included_files()) == 1) { die(); }

// --------------------------------------------------------------- ayarlar

/*
 * Desteklenen diller.
 *   yon    : yazim yonu (Arapça sağdan sola)
 *   ad     : dilin kendi adıyla gösterimi (anahtar dilde)
 *   bayrak : menüde kullanılan kısa rozet
 */
/*
 * Dil tanimlari.
 *
 * bayrak: iki harfli rozet (menu bos kalirsa yedek)
 * svg:    gosterimde kullanilan bayrok. Emoji denendi ama Windows
 *         sunucuda Segoe UI Emoji yok; bayraklar "TR" / "GB" gibi
 *         duz kod olarak cikiyordu. Inline SVG hem her platformda
 *         ayni gorunuyor hem de keskin.
 */
$GLOBALS['desteklenen_diller'] = array(
	'tr' => array('yon' => 'ltr', 'ad' => 'Türkçe', 'bayrak' => 'TR', 'svg' => 'tr'),
	'en' => array('yon' => 'ltr', 'ad' => 'English', 'bayrak' => 'EN', 'svg' => 'en'),
	'fr' => array('yon' => 'ltr', 'ad' => 'Français', 'bayrak' => 'FR', 'svg' => 'fr'),
	'ar' => array('yon' => 'rtl', 'ad' => 'العربية', 'bayrak' => 'AR', 'svg' => 'ar'),
);

/*
 * Bayrak SVG'leri. 24x16 gorunum alani, kenarlar yuvarlatilir
 * (class uzerinden). Renkler bayragin gercek tonlari.
 */
$GLOBALS['dil_bayrak_svg'] = array(

	/* Turkce: kirmizi zemin, beyaz hilal + yildiz
	 * Ilk denemede yildiz path'i ve hilal circle'lari ayni alana
	 * cizildigi icin yildiz kirmiziye boguluyordu. Simdi yildiz sola
	 * (cx~7), hilal saga (cx~14) yerlestirildi; hilal icinde
	 * kirmizi daire onune ekleniyor. */
	'tr' => '<svg viewBox="0 0 24 16" class="bayrak_svg" aria-hidden="true" focusable="false">'
		. '<rect width="24" height="16" fill="#e30a17"/>'
		/* yildiz: 5 koseli, merkez (7,8), dis yaricap ~2.9 */
		. '<path fill="#fff" d="M7 5.1l.71 1.57 1.73.19-1.27 1.19.33 1.71L7 8.85l-1.5.91.33-1.71-1.27-1.19 1.73-.19z"/>'
		/* hilal: beyaz disk + kirmizi kapali disk */
		. '<circle cx="14.8" cy="8" r="3.1" fill="#fff"/>'
		. '<circle cx="16.3" cy="8" r="2.5" fill="#e30a17"/>'
		. '</svg>',

	/* Ingiltere: Union Jack */
	'en' => '<svg viewBox="0 0 24 16" class="bayrak_svg" aria-hidden="true" focusable="false">'
		. '<rect width="24" height="16" fill="#012169"/>'
		. '<path fill="#fff" d="M0 0l24 16M24 0L0 16" stroke="#fff" stroke-width="3.2"/>'
		. '<path fill="#c8102e" d="M0 0l24 16M24 0L0 16" stroke="#c8102e" stroke-width="1.6"/>'
		. '<path stroke="#fff" stroke-width="5" d="M12 0v16M0 8h24"/>'
		. '<path stroke="#c8102e" stroke-width="3" d="M12 0v16M0 8h24"/>'
		. '</svg>',

	/* Fransa: dikey uc serit */
	'fr' => '<svg viewBox="0 0 24 16" class="bayrak_svg" aria-hidden="true" focusable="false">'
		. '<rect width="8" height="16" fill="#002395"/>'
		. '<rect x="8" width="8" height="16" fill="#fff"/>'
		. '<rect x="16" width="8" height="16" fill="#ed2939"/>'
		. '</svg>',

	/* Suudi Arabistan: yesil + beyaz serit + kizil ok */
	'ar' => '<svg viewBox="0 0 24 16" class="bayrak_svg" aria-hidden="true" focusable="false">'
		. '<rect width="24" height="16" fill="#006c35"/>'
		. '<rect y="3.6" width="24" height="2.2" fill="#fff"/>'
		. '<path fill="#fff" d="M13.4 4.2l1.5 2.2h-3z"/>'
		. '</svg>',
);

$GLOBALS['varsayilan_dil'] = 'en';

// ------------------------------------------------------------ para birimi

/*
 * Ziyaretcinin fiyatlari hangi para biriminde gormek istedigi.
 *
 * Veri tabaninda HER ilan fiyatini GMD (Gambia dalasi) saklar; yabanci
 * birimdeki ilanlar usd_kur / eur_kur / gbp_kur kurlariyla GMD'ye
 * cevrilip "fiyat" alaninda tutulur. Kullanicinin birimi sadece
 * GOSTERIM birimidir: fiyat() fonksiyonu GMD'yi ziyaretci birimine
 * geri cevirip basar, veri tabanina hicbir sey yazilmaz.
 *
 * Simdilik sadece GMB (GMD) ve USD destekleniyor; EUR/GBP icin de
 * kur degeri tanimliysa ayni yol kullanilir.
 */
$GLOBALS['desteklenen_birimler'] = array(
	'gmd' => array('ad' => 'Dalasi',    'kod' => 'GMD', 'sembol' => 'D', 'ikon' => 'ion-social-usd'),
	'usd' => array('ad' => 'US Dollar', 'kod' => 'USD', 'sembol' => '$', 'ikon' => 'ion-social-usd'),
	'eur' => array('ad' => 'Euro',      'kod' => 'EUR', 'sembol' => '€', 'ikon' => 'ion-social-euro'),
	'gbp' => array('ad' => 'Pound',     'kod' => 'GBP', 'sembol' => '£', 'ikon' => 'ion-social-usd'),
);

$GLOBALS['varsayilan_birim'] = 'gmd';

/*
 * Aktif gorunum birimini cozer.
 * Oncelik sirasi: ?birim=xx -> cookie -> gmd
 */
function aktif_birim()
{
	global $desteklenen_birimler, $varsayilan_birim;

	if (isset($GLOBALS['_aktif_birim']) && isset($desteklenen_birimler[$GLOBALS['_aktif_birim']])) {
		return $GLOBALS['_aktif_birim'];
	}

	$bulunan = null;

	if (isset($_GET['birim']) && isset($desteklenen_birimler[$_GET['birim']])) {
		$bulunan = $_GET['birim'];
		setcookie('birim', $bulunan, time() + 31536000, '/');
	} elseif (isset($_COOKIE['birim']) && isset($desteklenen_birimler[$_COOKIE['birim']])) {
		$bulunan = $_COOKIE['birim'];
	}

	if (!$bulunan) { $bulunan = $varsayilan_birim; }

	$GLOBALS['_aktif_birim'] = $bulunan;
	return $bulunan;
}

/*
 * Kur: 1 birim icin kac GMD.
 * Kur tanimli degilse 1 doner; boylece birlik ne olursa olsun GMD
 * degerini 1 katsayisiyla gosterir (bozuk sayi uretmez).
 */
function birim_kur($birim)
{
	$birim = strtolower($birim);
	if ($birim === 'gmd') { return 1; }

	$kaynak = ($birim === 'usd') ? 'usd_kur' : (($birim === 'eur') ? 'eur_kur' : (($birim === 'gbp') ? 'gbp_kur' : null));
	if (!$kaynak) { return 1; }

	$kur = @tema_ayar($kaynak);
	if ($kur && is_numeric($kur) && $kur > 0) { return (float) $kur; }

	return 1;
}

/*
 * GMD tutarini ziyaretcinin secigi birime cevirip bicimlendirir.
 * Ekranda kullanilacak fiyat metni doner (orn: "1.250 USD").
 *
 * @param string|float $gmd_deger  Veri tabanindaki GMD tutari
 * @param string|null  $hedef_birim NULL ise aktif (ziyaretci) birimi
 * @return string
 */
function birim_goster($gmd_deger, $hedef_birim = null)
{
	if ($hedef_birim === null) { $hedef_birim = aktif_birim(); }

	/*
	 * Veri tabanindaki fiyat bicimi: "50000,00" (dogal) ya da
	 * "5.000.000,00" / "1.234.567" (gruplanmis) olabilir.
	 * Bu yuzden once bosluklar temizlenir, sonra SONDAN virgul
	 * varsa onun solu tam sayi kismi, sagı kuruş kismi olarak
	 * ayrilir. Noktalar binlik ayiracidir ve ATILIR.
	 *
	 * Daha once her iki ayirici da siliniyordu; "50000,00" -> "5000000"
	 * olup fiyat 100 kat buyuyordu.
	 */
	$gmd_deger = trim(str_replace(array(' ', "\t", "\n", "\r", "'"), '', (string) $gmd_deger));

	if ($gmd_deger === '') { return ''; }

	$virgul = strrpos($gmd_deger, ',');
	if ($virgul !== false) {
		$tam   = str_replace('.', '', substr($gmd_deger, 0, $virgul));
		$kurus = substr($gmd_deger, $virgul + 1);
		$gmd_deger = $tam . '.' . str_pad(substr($kurus, 0, 2), 2, '0');
	} else {
		$gmd_deger = str_replace('.', '', $gmd_deger);
	}

	if (!is_numeric($gmd_deger)) { return ''; }
	$gmd_deger = (float) $gmd_deger;

	$kod   = isset($GLOBALS['desteklenen_birimler'][$hedef_birim])
		? $GLOBALS['desteklenen_birimler'][$hedef_birim]['kod']
		: strtoupper($hedef_birim);
	/*
	 * FontAwesome 4.7'de fa-usd / fa-eur / fa-gbp ikonlari YOK;
	 * sadece fa-money ve fa-cc var. O yuzden birim isaretleri
	 * sembol olarak basilir ($, €, £, D) - her platformda gorunur
	 * ve yanlislik yaratmaz.
	 */
	$sembol = isset($GLOBALS['desteklenen_birimler'][$hedef_birim])
		? $GLOBALS['desteklenen_birimler'][$hedef_birim]['sembol']
		: '';

	if ($hedef_birim === 'gmd') {
		$sayi = $gmd_deger;
	} else {
		$sayi = $gmd_deger / birim_kur($hedef_birim);
	}

	// Kuruşu yuvarlama: USD/EUR/GBP'de 2 basamak, GMD'de tam sayi
	$ondalik = ($hedef_birim === 'gmd') ? 0 : 2;
	$bicim   = number_format($sayi, $ondalik, ',', '.');

	return $bicim . " <span class='para_birimi_kodu'>" . $kod . "</span>";
}

/*
 * Fiyat degistirme baglantisi uretir: mevcut adresi korur,
 * eski "birim" parametresini cikarip yenisini ekler.
 */
function _BIRIM_DEGISTIR_LINK($birim)
{
	$adres = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/';

	$yol   = $adres;
	$sorgu = '';
	if (($poz = strpos($adres, '?')) !== false) {
		$yol   = substr($adres, 0, $poz);
		$sorgu = substr($adres, $poz + 1);
	}

	$parametreler = array();
	if ($sorgu !== '') { parse_str($sorgu, $parametreler); }

	unset($parametreler['birim']);
	$parametreler['birim'] = $birim;

	return $yol . '?' . http_build_query($parametreler);
}

// --------------------------------------------------------- dil çözümleme

function aktif_dil()
{
	global $desteklenen_diller, $varsayilan_dil;

	if (isset($GLOBALS['_aktif_dil']) && isset($desteklenen_diller[$GLOBALS['_aktif_dil']])) {
		return $GLOBALS['_aktif_dil'];
	}

	$bulunan = null;

	// 1) URL parametresi: her dil ?dil=xx ile kalici olarak secilebilsin
	if (isset($_GET['dil']) && isset($desteklenen_diller[$_GET['dil']])) {
		$bulunan = $_GET['dil'];
		setcookie('dil', $bulunan, time() + 31536000, '/');
	}
	// 2) Tarayici tercihi (yalnizca ziyaretcinin ilk tercihi olarak)
	elseif (!isset($_COOKIE['dil']) && isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
		// "en-GB,en;q=0.9,tr;q=0.8" -> once kodlari sirayla dene
		$tercih = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
		if (preg_match_all('/([a-z]{2})(?:[-_][a-zA-Z]{2,8})?/i', $tercih, $eslesme)) {
			foreach ($eslesme[1] as $kod) {
				$kod = strtolower($kod);
				if (isset($desteklenen_diller[$kod])) { $bulunan = $kod; break; }
			}
		}
	}
	// 3) Kayitli cookie
	elseif (isset($_COOKIE['dil']) && isset($desteklenen_diller[$_COOKIE['dil']])) {
		$bulunan = $_COOKIE['dil'];
	}
	// 4) Oturum
	elseif (isset($_SESSION['dil']) && isset($desteklenen_diller[$_SESSION['dil']])) {
		$bulunan = $_SESSION['dil'];
	}

	if (!$bulunan) { $bulunan = $varsayilan_dil; }

	$GLOBALS['_aktif_dil'] = $bulunan;
	$_SESSION['dil'] = $bulunan;

	return $bulunan;
}

/*
 * Ayarlar/panel etiketleri icin slug uretici.
 *
 * options(" . _LANG('tx_evet_1_hayir_0') . ") gibi tek bir metin dizisinden hem etiket
 * hem deger ureten yapilar var. Degerler veritabaninda saklandigi icin
 * dokunulmaz; yalnizca gosterilen etiket cevrilir. Anahtar adi etiketin
 * deterministik slug'indan uretildigi icin options() cagrisini degistirmeden
 * tek noktadan ceviri yapabiliyoruz.
 */
function _DIL_ETIKET_SLUG($metin)
{
	$harf = array(
		'ç' => 'c', 'Ç' => 'c', 'ğ' => 'g', 'Ğ' => 'g', 'ı' => 'i', 'İ' => 'i',
		'ö' => 'o', 'Ö' => 'o', 'ş' => 's', 'Ş' => 's', 'ü' => 'u', 'Ü' => 'u',
		'â' => 'a', 'î' => 'i', 'û' => 'u',
	);

	$m = strtr($metin, $harf);
	$m = preg_replace('/[^A-Za-z0-9]+/', '_', $m);

	return strtolower(trim($m, '_'));
}

/*
 * Etiket cevirisi. Anahtar sozlukte yoksa orijinal metni aynen dondurur;
 * boylece cevrilmemis bir secenek etiketi asla anahtar adina degismez.
 */
function _DIL_ETIKET($metin)
{
	$slug = _DIL_ETIKET_SLUG($metin);

	if ($slug === '') { return $metin; }

	$sozluk = _DIL_SOZLUK();
	$k      = 'opt_' . $slug;

	return isset($sozluk[$k]) ? $sozluk[$k] : $metin;
}

function _DIL_HTML_YON()
{
	global $desteklenen_diller;
	$d = aktif_dil();
	return isset($desteklenen_diller[$d]['yon']) ? $desteklenen_diller[$d]['yon'] : 'ltr';
}

function _DIL_RTL_MI()
{
	return (_DIL_HTML_YON() === 'rtl');
}

/*
 * Sayfa adresine ?dil=xx ekleyerek dil degistirme baglantisi uretir.
 * Mevcut sorgu parametreleri korunur, eski "dil" parametresi cikarilir.
 */
function _DIL_DEGISTIR_LINK($kod)
{
	$adres = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/';
	// Sorgu dizesini ayir
	$yol = $adres;
	$sorgu = '';
	if (($poz = strpos($adres, '?')) !== false) {
		$yol   = substr($adres, 0, $poz);
		$sorgu = substr($adres, $poz + 1);
	}
	$parametreler = array();
	if ($sorgu !== '') {
		parse_str($sorgu, $parametreler);
	}
	unset($parametreler['dil']);
	$parametreler['dil'] = $kod;
	return $yol . '?' . http_build_query($parametreler);
}

// ------------------------------------------------------------- çeviri

/*
 * Çeviri sözlüğü yalnızca bir kez yüklenir.
 *
 * İngilizce temel olarak alınır ve aktif dilin dosyası üzerine bindirilir.
 * Böylece henüz çevrilmemiş bir anahtar için anahtarın kendisi değil,
 * İngilizce metin görünür ve sayfa bozulmaz.
 */
function _DIL_SOZLUK()
{
	static $sozluk = null;
	if ($sozluk !== null) { return $sozluk; }

	$sozluk = array();
	$dizin   = dirname(__FILE__) . '/../lang/';

	// 1) İngilizce referans
	if (file_exists($dizin . 'en.php')) {
		$en = include($dizin . 'en.php');
		if (is_array($en)) { $sozluk = $en; }
	}

	// 2) Aktif dil üzerine bindir
	$dil = aktif_dil();
	if ($dil !== 'en' && file_exists($dizin . $dil . '.php')) {
		$aktif = include($dizin . $dil . '.php');
		if (is_array($aktif)) { $sozluk = array_merge($sozluk, $aktif); }
	}

	return $sozluk;
}

/*
 * Ana çeviri fonksiyonu.
 *
 * Kullanım:  echo _LANG('ilan_ver');
 * Anahtar listede yoksa anahtarın kendisi döner. Bu bilinçli bir
 * tercihtir: yarım kalmış bir çeviride sayfa bozulmaz, çevrilmemiş
 * yerler İngilizce anahtar metni olarak durur ve bir sonraki turda
 * kolayca bulunur.
 */
function _LANG($anahtar, $degistir = array())
{
	$sozluk = _DIL_SOZLUK();

	$metin = isset($sozluk[$anahtar]) ? $sozluk[$anahtar] : $anahtar;

	if (is_array($degistir) && $degistir) {
		$metin = vsprintf($metin, $degistir);
	}

	return $metin;
}

/*
 * HTML kaçışlı çeviri. Değişken değerlerinden gelen veri ekrana
 * basıldığı için kullanılır.
 */
function _LANG_C($anahtar, $degistir = array())
{
	return htmlspecialchars(_LANG($anahtar, $degistir), ENT_QUOTES, 'UTF-8');
}

/*
 * Ay adi. Sozlukteki ay_1..ay_12 anahtarlarindan okunur; butun diller
 * (tr dahil) bu anahtarlari icerir. Anahtar yoksa sayi doner ve cagrilan
 * kod Turkce yedek diziye duser.
 */
function _DIL_AY_AY($ay)
{
	$ay    = (int) $ay;
	if ($ay < 1 || $ay > 12) { return $ay; }
	$sozluk = _DIL_SOZLUK();
	return isset($sozluk['ay_' . $ay]) ? $sozluk['ay_' . $ay] : $ay;
}/*
 * Veritabanindaki basliklarin cevirisi.
 *
 * Sayfa ve menu basliklari tek bir "baslik" sutununda duruyor; ilan
 * basliklari gibi kullaniciya ait icerik cevrilmemeli. Bu yuzden
 * ceviri ekstra_bilgi tablosunda anahtar = baslik_<dil> olarak tutulur:
 *
 *    ekstra_bilgi(anahtar='baslik_en', id=<sayfa veya link id>, tip='sayfa')
 *
 * Kayit yoksa ya da bos ise verilen varsayilan (orijinal Turkce) metin
 * doner; eksik ceviri sayfayi bozmaz.
 */
function _DIL_BASLIK($varsayilan, $id = 0, $tip = '')
{
	global $baglan;

	$id = (int) $id;
	if (!$id || !$tip || !isset($baglan)) { return $varsayilan; }

	static $onbellek = array();
	$anahtar = 'baslik_' . aktif_dil();

	if (array_key_exists($anahtar, $onbellek) && isset($onbellek[$anahtar][$tip])
		&& array_key_exists($id, $onbellek[$anahtar][$tip])) {
		$deger = $onbellek[$anahtar][$tip][$id];
	} else {
		$deger = null;
		$sorgu = mysqli_query($baglan, "select deger from ekstra_bilgi where anahtar='" . $anahtar . "' and id=" . $id . " and tip='" . $tip . "' limit 1");
		if ($sorgu) {
			$satir = mysqli_fetch_row($sorgu);
			if ($satir && trim($satir[0]) !== '') { $deger = $satir[0]; }
		}
		if (!isset($onbellek[$anahtar])) { $onbellek[$anahtar] = array(); }
		if (!isset($onbellek[$anahtar][$tip])) { $onbellek[$anahtar][$tip] = array(); }
		$onbellek[$anahtar][$tip][$id] = $deger;
	}

	return $deger !== null ? $deger : $varsayilan;
}
/*
 * <html lang="..."> icin dil etiketi.
 * Arapca icin "ar", digerleri icin normal iki harfli kod doner.
 */
function _DIL_HTML_LANG()
{
	$d = aktif_dil();
	return $d === 'ar' ? 'ar' : $d;
}

/*
 * Bilesen "link_baslik" metninin cevirisi.
 *
 * _DIL_BASLIK() anahtari sabit olarak 'baslik_<dil>' olusturur; bu
 * yuzden ayni bilesenin BASLIGI ile link_basligi ayni anahtari
 * kullanamaz. Burada ayri bir anahtar semasi kullanilir:
 *    ekstra_bilgi(tip='bilesen', anahtar='link_baslik_<dil>', id=<bilesen.id>)
 *
 * Ceviri yoksa verilen Turkce varsayilan doner.
 */
function _BILESEN_LINK_BASLIK($varsayilan, $bilesen_id)
{
	global $baglan;

	$bilesen_id = (int) $bilesen_id;
	if (!$bilesen_id || !$varsayilan || !isset($baglan)) { return $varsayilan; }

	static $onbellek = array();
	$anahtar = 'link_baslik_' . aktif_dil();

	if (array_key_exists($anahtar, $onbellek) && array_key_exists($bilesen_id, $onbellek[$anahtar])) {
		return $onbellek[$anahtar][$bilesen_id];
	}

	$deger = null;
	$sorgu = mysqli_query($baglan, "select deger from ekstra_bilgi where anahtar='" . $anahtar . "' and id=" . $bilesen_id . " and tip='bilesen' limit 1");
	if ($sorgu) {
		$satir = mysqli_fetch_row($sorgu);
		if ($satir && trim($satir[0]) !== '') { $deger = $satir[0]; }
	}

	if (!isset($onbellek[$anahtar])) { $onbellek[$anahtar] = array(); }
	$onbellek[$anahtar][$bilesen_id] = $deger;

	return $deger !== null ? $deger : $varsayilan;
}
