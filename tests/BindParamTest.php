<?php
/* =========================================================
 * BindParamTest.php
 * mysqli_stmt_bind_param() cagrilarinda tip sayisi ile degisken
 * sayisi esit olmalidir. Aksi halde PHP 8 ArgumentCountError
 * firlatir ve sayfa sessizce (HTTP 200) bozulur.
 *
 * Kullanim: php tests/BindParamTest.php
 * Cikis kodu 0 = testler gecti
 * ========================================================= */

$proje_kok = dirname(__DIR__);
$atlama = '~[\\\\/](SQL|uploads|js|lib|ckeditor|phpmailer|iyzico)[\\\\/]~i';

$dosyalar = array();
$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($proje_kok, FilesystemIterator::SKIP_DOTS));
foreach ($it as $f) {
	$p = $f->getPathname();
	if (substr($p, -4) !== '.php') { continue; }
	if (preg_match($atlama, $p)) { continue; }
	$dosyalar[] = $p;
}
sort($dosyalar);

$hata = 0;
$toplam = 0;
$temiz = 0;

foreach ($dosyalar as $p) {
	$g = file_get_contents($p);
	if (strpos($g, 'mysqli_stmt_bind_param') === false) { continue; }
	$rel = str_replace($proje_kok . '/', '', $p);

	preg_match_all('~mysqli_stmt_bind_param\s*\(\s*[^,]+,\s*"([^"]*)"\s*(.*?)\)\s*;~s', $g, $m, PREG_SET_ORDER);
	foreach ($m as $x) {
		$toplam++;
		$tipler = $x[1];
		$argumanlar = ltrim(trim($x[2]), ',');
		$adet = ($argumanlar === '') ? 0 : count(preg_split('~\s*,\s*~', $argumanlar));
		if (strlen($tipler) !== $adet) {
			$hata++;
			echo "  [HATA] $rel\n";
			echo "         tipler: '$tipler' (" . strlen($tipler) . ") | degisken: $adet\n";
			echo "         " . trim(preg_replace('~\s+~', ' ', 'mysqli_stmt_bind_param(' . $x[0])) . "\n";
		} else {
			$temiz++;
		}
	}
}

echo "Toplam bind_param cagrisi: $toplam | dogru: $temiz | hatali: $hata\n";

if ($hata > 0) {
	echo "\n✗ BASARISIZ\n";
	exit(1);
}
echo "\n✓ ALL TESTS PASSED\n";
exit(0);
