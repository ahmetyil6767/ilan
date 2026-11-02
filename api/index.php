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
	// CLI smoke testleri icin: php api/index.php "/listings" GET
	$rota = $argv_degerleri[1];
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

switch ($birinci) {
	case 'auth':
		require __DIR__ . '/controllers/auth.php';
		api_auth_rute($yontem, $ikinci);
		break;

	case 'listings':
		require __DIR__ . '/controllers/listings.php';
		api_listings_rute($yontem, $ikinci);
		break;

	case 'categories':
		require __DIR__ . '/controllers/categories.php';
		api_kategoriler();
		break;

	case 'regions':
		require __DIR__ . '/controllers/categories.php';
		api_bolgeler();
		break;

	case 'districts':
		require __DIR__ . '/controllers/categories.php';
		api_ilceler($girdi);
		break;

	case 'messages':
		require __DIR__ . '/controllers/messages.php';
		api_messages_rute($yontem);
		break;

	case 'favorites':
		require __DIR__ . '/controllers/favorites.php';
		api_favorites_rute($yontem, $ikinci);
		break;

	default:
		api_hata('Route not found', 404);
}
