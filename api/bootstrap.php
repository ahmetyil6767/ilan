<?php
/*
 * JaayQuick REST API - ortak bootstrap
 * ------------------------------------
 * loader.php kullanilmaz (oturum yonlendirme, bakim modu, eklenti ciktilari
 * JSON API'ye uygun degil). Loader'in guvenli ozellikleri ayni sirada burada
 * kurulur:
 *
 *   1. Hata ayari: hicbir hata ekrana yazilmaz, [JaayQuick][API] etiketiyle loga yazilir
 *   2. config/db.php ($baglan - mysqli, utf8mb4)
 *   3. session_start + dil altyapisi (config/dil.php oturum kullandigi icin)
 *   4. config/yapilandir.php + fonksiyonlar*.php + aktif tema fonksiyonlari
 *      (yukle(), uye_ilan_limiti(), fiyat() gibi yardimcilar buradan gelir)
 *
 * Girdi temizligi: loader'in $_POST/$_GET mutasyon edilmesi YAPILMAZ; JSON
 * govde php://input'tan okunur ve tum sorgular prepared statement iledir.
 */

if (count(get_included_files()) == 1) {
	die("Bu dosya dogrudan cagrilmamalidir.");
}

ob_start();

// --- Hata ayari: log-only (loader.php ile ayni politika) ---
if (!defined('JAYQUICK_HATA_EKRANA')) {
	define('JAYQUICK_HATA_EKRANA', false);
}
ini_set('display_errors', JAYQUICK_HATA_EKRANA ? '1' : '0');
ini_set('display_startup_errors', '0');
ini_set('html_errors', '0');
ini_set('log_errors', '1');
error_reporting(E_ALL & ~E_DEPRECATED);

set_error_handler(function ($seviye, $mesaj, $dosya = '', $satir = 0) {
	if (!(error_reporting() & $seviye)) {
		return false;
	}
	$ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '-';
	error_log(sprintf('[JaayQuick][API][%d] %s in %s:%d | IP: %s', $seviye, $mesaj, $dosya, $satir, $ip));
	return true;
});

set_exception_handler(function ($e) {
	error_log(sprintf('[JaayQuick][API][HATA] %s: %s in %s:%d', get_class($e), $e->getMessage(), $e->getFile(), $e->getLine()));
	http_response_code(500);
	header('Content-Type: application/json; charset=utf-8');
	echo json_encode(array('message' => 'Internal server error'));
	exit(1);
});

if (!defined('ROOT_DIR')) {
	define('ROOT_DIR', dirname(__DIR__));
}
ini_set('memory_limit', '512M');
ini_set('max_execution_time', 120);
ini_set('date.timezone', 'Europe/Istanbul');

// --- JSON yardimcilari ---
function api_json_cevap($veri, $kod = 200)
{
	while (ob_get_level() > 0) {
		ob_end_clean();
	}
	http_response_code($kod);
	header('Content-Type: application/json; charset=utf-8');
	echo json_encode($veri, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
	exit(0);
}

function api_basari($veri, $kod = 200)
{
	api_json_cevap($veri, $kod);
}

function api_hata($mesaj, $kod = 400, $alanlar = null)
{
	$cevap = array('message' => $mesaj);
	if ($alanlar) {
		$cevap['errors'] = $alanlar;
	}
	api_json_cevap($cevap, $kod);
}

// --- Veritabani (config/db.php + env, loader.php ile ayni mantik) ---
if (file_exists(ROOT_DIR . '/config/db.php')) {
	require ROOT_DIR . '/config/db.php';
	if (getenv('DB_HOST')) { $host = getenv('DB_HOST'); }
	if (getenv('DB_NAME')) { $veritabani = getenv('DB_NAME'); }
	if (getenv('DB_USER')) { $kullanici = getenv('DB_USER'); }
	if (getenv('DB_PASS') !== false) { $parola = getenv('DB_PASS'); }
	if (@$host and @$kullanici and @$veritabani) {
		mysqli_report(MYSQLI_REPORT_OFF);
		@$baglan = mysqli_connect("$host", "$kullanici", "$parola", "$veritabani");
		if (!$baglan) {
			error_log('[JaayQuick][API][DB] Veritabani baglantisi kurulamadi: ' . mysqli_connect_error());
			api_hata('Database connection failed', 503);
		}
		mysqli_query($baglan, 'set names utf8mb4');
		date_default_timezone_set('Europe/Istanbul');
		mysqli_report(MYSQLI_REPORT_OFF);
	} else {
		api_hata('Database not configured', 503);
	}
} else {
	api_hata('config/db.php not found', 503);
}

session_start();
if (!isset($_SESSION['dil'])) {
	$_SESSION['dil'] = null;
}

// --- Site altyapisi (loader ile ayni sira) ---
require ROOT_DIR . '/config/dil.php';
require ROOT_DIR . '/config/yapilandir.php';
require ROOT_DIR . '/config/fonksiyonlar.php';

$aktif_tema = tema_ne();

require ROOT_DIR . '/config/fonksiyonlar_ek.php';
require ROOT_DIR . '/config/fonksiyonlar_meta.php';
require ROOT_DIR . '/config/fonksiyonlar_url.php';

// Tema yardimcilari (uye_ilan_limiti, fiyat ... loader da ayni sekilde ekler)
$tema_info_yolu = ROOT_DIR . '/' . $temalar_dizini . '/' . $aktif_tema . '/info.php';
if (file_exists($tema_info_yolu)) {
	$tema_info = include $tema_info_yolu;
	if (is_array($tema_info) and @$tema_info['fonksiyon']) {
		$tema_fonksiyon_yolu = ROOT_DIR . '/' . $temalar_dizini . '/' . $aktif_tema . '/' . $tema_info['fonksiyon'];
		if (file_exists($tema_fonksiyon_yolu)) {
			require $tema_fonksiyon_yolu;
		}
	}
}

// --- API tablolari (ilk istekte garanti edilir) ---
// api_tokens: mobil kimlik dogrulamasi
// gorusmeler/mesajlar: eklenti/mesajlasma semasina ek ilan_id + sil_1/sil_2
mysqli_query($baglan, "
	CREATE TABLE IF NOT EXISTS `api_tokens` (
		`token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
		`uye_id` int(10) NOT NULL,
		`olusturma` datetime NOT NULL,
		`bitis` datetime NOT NULL,
		`son_kullanim` datetime DEFAULT NULL,
		PRIMARY KEY (`token`),
		KEY `uye_id` (`uye_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
");

mysqli_query($baglan, "
	CREATE TABLE IF NOT EXISTS `gorusmeler` (
		`id` int(10) NOT NULL AUTO_INCREMENT,
		`uye_1` int(10) NOT NULL,
		`uye_2` int(10) NOT NULL,
		`tarih_saat` varchar(20) NOT NULL,
		`ilan_id` int(10) DEFAULT NULL,
		`sil_1` int(1) NOT NULL DEFAULT '0',
		`sil_2` int(1) NOT NULL DEFAULT '0',
		PRIMARY KEY (`id`),
		KEY `uye_1` (`uye_1`),
		KEY `uye_2` (`uye_2`),
		KEY `ilan_id` (`ilan_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");

mysqli_query($baglan, "
	CREATE TABLE IF NOT EXISTS `mesajlar` (
		`id` int(10) NOT NULL AUTO_INCREMENT,
		`gorusme_id` int(10) NOT NULL,
		`uye_id` int(10) NOT NULL,
		`kime` int(10) NOT NULL,
		`mesaj` text NOT NULL,
		`tarih_saat` varchar(20) NOT NULL,
		`okundu` int(1) NOT NULL DEFAULT '0',
		PRIMARY KEY (`id`),
		KEY `gorusme_id` (`gorusme_id`),
		KEY `uye_id` (`uye_id`),
		KEY `kime` (`kime`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");

mysqli_query($baglan, "
	CREATE TABLE IF NOT EXISTS `favoriler` (
		`f_id` int(10) NOT NULL AUTO_INCREMENT,
		`id` int(10) NOT NULL,
		`uye_id` int(10) NOT NULL,
		`tarih` varchar(20) DEFAULT NULL,
		PRIMARY KEY (`f_id`),
		KEY `uye_id` (`uye_id`),
		KEY `id` (`id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");

/* =========================================================
 *  Token yardimcilari
 * ========================================================= */

function api_token_olustur($uye_id)
{
	global $baglan;
	$token = bin2hex(random_bytes(32));
	$olusturma = date('Y-m-d H:i:s');
	$bitis = date('Y-m-d H:i:s', strtotime('+30 days'));
	$sorgu = mysqli_prepare($baglan, 'insert into api_tokens (token, uye_id, olusturma, bitis) values (?, ?, ?, ?)');
	mysqli_stmt_bind_param($sorgu, 'siss', $token, $uye_id, $olusturma, $bitis);
	mysqli_stmt_execute($sorgu);
	mysqli_stmt_close($sorgu);
	return $token;
}

/** Tek uyenin butun tokenlarini gecersiz kilar (logout) */
function api_token_sil($uye_id)
{
	global $baglan;
	$sorgu = mysqli_prepare($baglan, 'delete from api_tokens where uye_id=?');
	mysqli_stmt_bind_param($sorgu, 'i', $uye_id);
	mysqli_stmt_execute($sorgu);
	mysqli_stmt_close($sorgu);
}

function api_token_uye($token)
{
	global $baglan;
	if (!$token or strlen($token) !== 64 or !ctype_xdigit($token)) {
		return null;
	}
	$sorgu = mysqli_prepare($baglan, '
		select t.uye_id, u.id, u.ad_soyad, u.email_adresi, u.yetki, u.durum, u.url
		from api_tokens t
		inner join uyeler u on u.id = t.uye_id
		where t.token = ? and t.bitis > now()
		limit 1
	');
	mysqli_stmt_bind_param($sorgu, 's', $token);
	mysqli_stmt_execute($sorgu);
	mysqli_stmt_store_result($sorgu);
	if (mysqli_stmt_num_rows($sorgu) !== 1) {
		mysqli_stmt_close($sorgu);
		return null;
	}
	mysqli_stmt_bind_result($sorgu, $token_uye_id, $k_id, $k_ad, $k_email, $k_yetki, $k_durum, $k_url);
	mysqli_stmt_fetch($sorgu);
	mysqli_stmt_close($sorgu);

	// son kullanimi tazele
	$guncelle = mysqli_prepare($baglan, 'update api_tokens set son_kullanim=now() where token=?');
	mysqli_stmt_bind_param($guncelle, 's', $token);
	mysqli_stmt_execute($guncelle);
	mysqli_stmt_close($guncelle);

	if ((int)$k_durum !== 1) {
		return null;
	}
	return array(
		'id' => (int)$k_id,
		'ad_soyad' => $k_ad,
		'email_adresi' => $k_email,
		'yetki' => $k_yetki,
		'url' => $k_url,
	);
}

function api_bearer_token()
{
	$header = isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : '';
	if (!$header and function_exists('apache_request_headers')) {
		$basliklar = apache_request_headers();
		foreach ($basliklar as $anahtar => $deger) {
			if (strtolower($anahtar) === 'authorization') {
				$header = $deger;
				break;
			}
		}
	}
	if (stripos($header, 'Bearer ') === 0) {
		return trim(substr($header, 7));
	}
	return null;
}

/** Korunan endpoint'lerde uye dondurur; oturum yoksa 401 ile sonlandirir. */
function api_uye($gerekli = true)
{
	$uye = api_token_uye(api_bearer_token());
	if ($uye) {
		return $uye;
	}
	if ($gerekli) {
		api_hata('Unauthorized', 401);
	}
	return null;
}

/* =========================================================
 *  Girdi yardimcilari
 * ========================================================= */

/** JSON govde + multipart $_POST + query string birlestirilir (sadele kesilir) */
function api_girdi()
{
	static $girdi = null;
	if ($girdi === null) {
		$girdi = array();
		$govde = file_get_contents('php://input');
		if ($govde) {
			$cozumlenmis = json_decode($govde, true);
			if (is_array($cozumlenmis)) {
				$girdi = $cozumlenmis;
			}
		}
		if (is_array($_POST)) {
			foreach ($_POST as $anahtar => $deger) {
				if (!isset($girdi[$anahtar]) or $girdi[$anahtar] === '' or is_null($girdi[$anahtar])) {
					$girdi[$anahtar] = $deger;
				}
			}
		}
		if (is_array($_GET)) {
			foreach ($_GET as $anahtar => $deger) {
				if ($anahtar !== 'route' and !isset($girdi[$anahtar])) {
					$girdi[$anahtar] = $deger;
				}
			}
		}
	}
	return $girdi;
}

/** ekstra_bilgi'den (tip='uye') uye telefonunu okur (cep_tel) */
function api_uye_telefonu($uye_id)
{
	return api_sorgu_deger("select deger from ekstra_bilgi where anahtar='cep_tel' and tip='uye' and id=? limit 1", array((int)$uye_id));
}

/** JSON/multipart girdiden temiz metin (kontrol istemciye aittir; sorgu prepared) */
function api_metin($anahtar, $varsayilan = '')
{
	$girdi = api_girdi();
	if (!isset($girdi[$anahtar]) or is_array($girdi[$anahtar])) {
		return $varsayilan;
	}
	return trim(strip_tags((string)$girdi[$anahtar]));
}

/** "1.234,56" / "1234.56" gibi fiyat metnini sayiya cevirir */
function api_sayi($anahtar)
{
	$girdi = api_girdi();
	if (!isset($girdi[$anahtar]) or is_array($girdi[$anahtar])) {
		return null;
	}
	if ($girdi[$anahtar] === '' or (string)$girdi[$anahtar] === 'null') {
		return null;
	}
	$metin = str_replace(array('.', ','), array('', '.'), (string)$girdi[$anahtar]);
	return is_numeric($metin) ? $metin + 0 : null;
}

/** Site konvansiyonuna uygun fiyat metni: "1.234,56" */
function api_fiyat_metni($sayi)
{
	return number_format((float)$sayi, 2, ',', '.');
}

/** Ekstra_bilgi (EAV) okuma araci; hem 'ilan' hem eski 'icerik' tipine bakar */
function api_ekstra($anahtar, $icerik_id)
{
	global $baglan;
	$icerik_id = (int)$icerik_id;
	if ($icerik_id < 1) {
		return null;
	}
	$sorgu = mysqli_prepare($baglan, 'select deger from ekstra_bilgi where anahtar=? and id=? and tip in (?, ?) limit 1');
	$tip1 = 'ilan';
	$tip2 = 'icerik';
	mysqli_stmt_bind_param($sorgu, 'siss', $anahtar, $icerik_id, $tip1, $tip2);
	mysqli_stmt_execute($sorgu);
	mysqli_stmt_store_result($sorgu);
	if (mysqli_stmt_num_rows($sorgu) !== 1) {
		mysqli_stmt_close($sorgu);
		return null;
	}
	mysqli_stmt_bind_result($sorgu, $deger);
	mysqli_stmt_fetch($sorgu);
	mysqli_stmt_close($sorgu);
	return tirnak_ekle($deger);
}

/** Ekstra bilgiyi ilan tipine yaz (site okuma konvansiyonu: tip='ilan') */
function api_ekstra_yaz($anahtar, $deger, $icerik_id)
{
	ekstra_bilgi_guncelle($anahtar, $deger, $icerik_id, 'ilan');
}

/** Resim referansindan gecerli boyut URL'si uretir */
function api_resim_url($resim_url, $boyut = '530_393')
{
	$resim_url = bosluk_donustur(trim((string)$resim_url));
	if ($resim_url === '') {
		return null;
	}
	$boyutlu = thumbnail_url($boyut, $resim_url);
	if (is_string($boyutlu) and $boyutlu !== '') {
		return $boyutlu;
	}
	$tam = thumbnail_url('full', $resim_url);
	return is_string($tam) ? $tam : $resim_url;
}

/* =========================================================
 *  Sorgu yardimcilari
 *  mysqli_stmt_bind_param Once starts referans istedigi icin
 *  integer-bilinen klasik referans deseni kullanilir (tum PHP sürümlerinde guvenli).
 *  Sonuçlar mysqli_stmt_get_result ile assoc dizi olarak okunur.
 * ========================================================= */

function api_sorgu($sql, $bagla = array())
{
	global $baglan;
	$stmt = mysqli_prepare($baglan, $sql);
	if (!$stmt) {
		error_log('[JaayQuick][API][SQL] Hazirlik basarisiz: ' . mysqli_error($baglan) . ' | ' . $sql);
		return false;
	}
	if (!empty($bagla)) {
		$tipler = '';
		foreach ($bagla as $deger) {
			if (is_int($deger)) {
				$tipler .= 'i';
			} elseif (is_float($deger)) {
				$tipler .= 'd';
			} else {
				$tipler .= 's';
			}
		}
		// bind_param(...$refs) icin referans dizisi yalnizca DEGERLERI icerir;
		// tipler ayrica ilk arguman olarak gecer
		$referanslar = array();
		foreach (array_keys($bagla) as $anahtar) {
			$referanslar[] = &$bagla[$anahtar];
		}
		mysqli_stmt_bind_param($stmt, $tipler, ...$referanslar);
	}
	mysqli_stmt_execute($stmt);
	return $stmt;
}

/** birden çok satır assoc dizi olarak */
function api_sorgu_satirlar($sql, $bagla = array())
{
	$stmt = api_sorgu($sql, $bagla);
	if (!$stmt) {
		return array();
	}
	$sonuc = mysqli_stmt_get_result($stmt);
	$diziler = $sonuc ? mysqli_fetch_all($sonuc, MYSQLI_ASSOC) : array();
	mysqli_stmt_close($stmt);
	return $diziler;
}

/** tek satır assoc (yoksa null) */
function api_sorgu_satir($sql, $bagla = array())
{
	$stmt = api_sorgu($sql, $bagla);
	if (!$stmt) {
		return null;
	}
	$sonuc = mysqli_stmt_get_result($stmt);
	$satir = $sonuc ? mysqli_fetch_assoc($sonuc) : null;
	mysqli_stmt_close($stmt);
	return $satir;
}

/** tek hücre değer */
function api_sorgu_deger($sql, $bagla = array())
{
	$satir = api_sorgu_satir($sql, $bagla);
	return $satir ? array_values($satir)[0] : null;
}
