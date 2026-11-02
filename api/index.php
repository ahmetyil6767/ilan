<?php
/*
 * JaayQuick REST API - giris noktasi
 * ------------------------------------
 * Rotalar:
 *   GET  /api/listings?s=&category=&region=&district=&price_min=&price_max=&sort=&page=&per_page=
 *   GET  /api/listings/mine            (Bearer)
 *   GET  /api/listings/:id
 *   POST /api/listings                 (Bearer, multipart/form-data)
 *   PUT  /api/listings/:id             (Bearer, JSON)
 *   DELETE /api/listings/:id           (Bearer)
 *   GET  /api/categories
 *   GET  /api/regions
 *   GET  /api/districts?region=
 *   POST /api/auth/login               {email | telefon, password}
 *   POST /api/auth/register            {name, email, phone?, password, password_confirmation}
 *   GET  /api/auth/me                  (Bearer)
 *   POST /api/auth/logout              (Bearer)
 *   GET  /api/messages                 (Bearer)
 *   POST /api/messages                 {listing_id, message} (Bearer)
 *   GET  /api/favorites                (Bearer)
 *   POST /api/favorites                {listing_id} (Bearer)
 *   DELETE /api/favorites/:id          (Bearer)
 *
 * .htaccess ile /api/... api/index.php'ye yonlendirilir; Apache degistirmeden
 * dogrudan /api/index.php?route=... seklinde de calisir.
 */

require __DIR__ . '/bootstrap.php';

$yontem = isset($_SERVER['REQUEST_METHOD']) ? strtoupper($_SERVER['REQUEST_METHOD']) : 'GET';

// HTML form/multipart gorevleri yalniza POST gonderilebildigi icin,
// istemciler _method=PUT|DELETE cover saglayabilir (query veya body)
if ($yontem === 'POST') {
	$override = '';
	if (isset($_GET['_method'])) {
		$override = strtoupper((string)$_GET['_method']);
	} elseif (isset($_POST['_method']) and is_string($_POST['_method'])) {
		$override = strtoupper((string)$_POST['_method']);
	} elseif (isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE']) and is_string($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'])) {
		$override = strtoupper((string)$_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE']);
	}
	if (in_array($override, array('PUT', 'PATCH', 'DELETE'), true)) {
		$yontem = $override;
	}
}
$rota   = '';
$argv_degerleri = PHP_SAPI === 'cli' ? (isset($argv) ? $argv : array()) : array();

if (PHP_SAPI === 'cli' and isset($argv_degerleri[1])) {
	// CLI smoke testleri icin: php api/index.php "/listings?per_page=3" GET
	$cli_rota = (string)$argv_degerleri[1];
	$soru = strpos($cli_rota, '?');
	if ($soru !== false) {
		parse_str(substr($cli_rota, $soru + 1), $cli_get);
		if (is_array($cli_get)) {
			foreach ($cli_get as $ck => $cv) {
				$_GET[$ck] = $cv;
			}
		}
		$cli_rota = substr($cli_rota, 0, $soru);
	}
	$rota = $cli_rota;
	if (isset($argv_degerleri[2])) {
		$yontem = strtoupper($argv_degerleri[2]);
	}
} else {
	$rota = isset($_GET['route']) ? $_GET['route'] : '';
	if ($rota === '' and isset($_SERVER['REQUEST_URI'])) {
		$yolu = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
		// "api/..." on ekini cikar (hem /api/ hem derin dizin kurulumlari calisir)
		if (preg_match('#/api/(.*)$#', (string)$yolu, $bulunan)) {
			$rota = $bulunan[1];
		}
	}
}

$rota     = trim((string)$rota, '/');
$parcalar = $rota === '' ? array() : explode('/', $rota);
$girdi    = api_girdi();

if (count($parcalar) === 0) {
	api_hata('Route not found', 404);
}

$birinci = $parcalar[0];
$ikinci  = isset($parcalar[1]) ? $parcalar[1] : null;

// Controller dosyalarinin HEPSI yuklenir (yalnizca fonksiyon tanimi icerirler);
// controller'lar birbirinin yardimci fonksiyonlarini kullanabilir
require __DIR__ . '/controllers/auth.php';
require __DIR__ . '/controllers/listings.php';
require __DIR__ . '/controllers/categories.php';
require __DIR__ . '/controllers/messages.php';
require __DIR__ . '/controllers/favorites.php';

switch ($birinci) {
	case 'auth':
		api_auth_rute($yontem, $ikinci);
		break;

	case 'listings':
		api_listings_rute($yontem, $ikinci);
		break;

	case 'categories':
		api_kategoriler();
		break;

	case 'regions':
		api_bolgeler();
		break;

	case 'districts':
		api_ilceler($girdi);
		break;

	case 'messages':
		api_messages_rute($yontem);
		break;

	case 'favorites':
		api_favorites_rute($yontem, $ikinci);
		break;

	default:
		api_hata('Route not found', 404);
}
