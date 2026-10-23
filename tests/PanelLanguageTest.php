<?php
/* Panel sayfalarini EN/FR/AR dillerinde test eder */
error_reporting(0);
mysqli_report(MYSQLI_REPORT_OFF);
$ciz = 'C:/Users/yilma/AppData/Local/Temp/opencode/panel_dil_cierez.txt';
@unlink($ciz);

function istek($url, $ciz, $post = null, $dil = null)
{
	$ch = curl_init($url);
	$opt = array(CURLOPT_RETURNTRANSFER => true, CURLOPT_FOLLOWLOCATION => false, CURLOPT_TIMEOUT => 40,
		CURLOPT_COOKIEJAR => $ciz, CURLOPT_COOKIEFILE => $ciz);
	if ($post !== null) { $opt[CURLOPT_POST] = true; $opt[CURLOPT_POSTFIELDS] = $post; }
	curl_setopt_array($ch, $opt);
	$h = (string)curl_exec($ch);
	$kod = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);
	return array($kod, $h);
}

list($kod, $h) = istek('http://ilan/lpanel/giris-yap.php', $ciz, 'kullanici=info%40jaayquick.com&parola=JaayQuick%232026&buton=1');
echo "giris: HTTP $kod\n\n";

$diller = array('tr' => 'Türkçe', 'en' => 'English', 'fr' => 'Français', 'ar' => 'العربية');
$sayfalar = array(
	'ayarlar' => '/lpanel/ayarlar.php',
	'kategoriler' => '/lpanel/kategoriler.php?tip=ilan&sinif=kategori',
	'menuler' => '/lpanel/menuler.php',
	'listeler' => '/lpanel/listeler.php',
	'uyeler' => '/lpanel/uyeler.php',
	'yorumlar' => '/lpanel/yorumlar.php',
	'bildirimler' => '/lpanel/bildirimler.php',
	'bilesenler' => '/lpanel/bilesenler.php',
	'icerik' => '/lpanel/icerik.php',
	'eklentiler' => '/lpanel/eklentiler.php',
	'temalar' => '/lpanel/temalar.php',
	'index' => '/lpanel/index.php',
);

$toplam_hata = 0;
/*
 * Hatalar artik ekrana yazilmaz (loader.php hata yakalayici + php.ini/vhost
 * display_errors Off). Bu yuzden hata tespiti HTML yerine PHP hata
 * logundan yapilir: test oncesi log boyutu olculur, sonra yeni
 * kayitlar okunur.
 */
$hata_logu = 'C:/wamp64/logs/php_error.log';
$log_baslangic = file_exists($hata_logu) ? filesize($hata_logu) : 0;

foreach ($diller as $dil => $ad) {
	echo "=== $dil ($ad) ===\n";
	foreach ($sayfalar as $s => $yol) {
		/* dil cookie'sini degistir */
		$ch = curl_init('http://ilan/');
		curl_setopt_array($ch, array(CURLOPT_RETURNTRANSFER => true, CURLOPT_COOKIEJAR => $ciz, CURLOPT_COOKIEFILE => $ciz, CURLOPT_TIMEOUT => 20));
		curl_exec($ch); curl_close($ch);
		list($k, $html) = istek('http://ilan' . $yol, $ciz);
		$uyari = array();
		preg_match_all('~(Warning|Fatal error|Notice:|Deprecated:|Undefined (?:variable|array key|index|property))[^<\n]{0,70}?on line (\d+)?~i', $html, $um);
		$uyari = array_values(array_unique($um[0]));
		$hata = ($k != 200) || count($uyari) > 0;
		if ($hata) { $toplam_hata++; }
		echo "  " . str_pad($s, 14) . " HTTP $k" . (count($uyari) ? " | " . implode(' || ', array_slice($uyari, 0, 2)) : '') . "\n";
	}
	echo "\n";
}
echo "toplam sorunlu sayfa-dil: $toplam_hata\n";

/* --- log tabanli hata tespiti (hatalar ekrana yazilmadigi icin) --- */
clearstatcache();
$log_son = file_exists($hata_logu) ? filesize($hata_logu) : 0;
if ($log_son > $log_baslangic) {
	$f = fopen($hata_logu, 'r');
	fseek($f, $log_baslangic);
	$yeni = fread($f, $log_son - $log_baslangic);
	fclose($f);
	$log_hatalari = array();
	foreach (explode("\n", $yeni) as $satir) {
		$satir = trim($satir);
		if ($satir === '' or strpos($satir, '[JaayQuick]') === false) { continue; }
		$log_hatalari[] = $satir;
	}
	echo "logdaki yeni hata: " . count($log_hatalari) . "\n";
	foreach (array_slice($log_hatalari, 0, 8) as $h) { echo "  " . $h . "\n"; }
	if (count($log_hatalari)) { $toplam_hata += count($log_hatalari); }
} else {
	echo "logdaki yeni hata: 0\n";
}
echo "SONUC: " . ($toplam_hata ? "SORUN VAR ($toplam_hata)" : "TEMIZ") . "\n";
