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

/*
 * mysqli_stmt_bind_param( ... ) cagrisinin argumanlarini ayristirir.
 * Tirnak icindeki parantezler ve virguller sayimi bozulmasin diye
 * tirnak durumu takip edilir.
 */
function bind_param_argumanlari($g, $konum)
{
	$ac = strpos($g, '(', $konum);
	if ($ac === false) { return null; }
	$derinlik = 0;
	$tirnak = '';
	$son = null;
	$uzunluk = strlen($g);
	for ($i = $ac; $i < $uzunluk; $i++) {
		$kar = $g[$i];
		if ($tirnak !== '') {
			if ($kar === '\\') { $i++; continue; }
			if ($kar === $tirnak) { $tirnak = ''; }
			continue;
		}
		if ($kar === '"' or $kar === "'") { $tirnak = $kar; continue; }
		if ($kar === '(') { $derinlik++; continue; }
		if ($kar === ')') {
			$derinlik--;
			if ($derinlik === 0) { $son = $i; break; }
		}
	}
	if ($son === null) { return null; }
	$ic = substr($g, $ac + 1, $son - $ac - 1);
	/* ust duzeydeki virgullerle bol */
	$argumanlar = array();
	$derinlik = 0;
	$tirnak = '';
	$parca = '';
	for ($i = 0; $i < strlen($ic); $i++) {
		$kar = $ic[$i];
		if ($tirnak !== '') {
			$parca .= $kar;
			if ($kar === '\\' and $i + 1 < strlen($ic)) { $parca .= $ic[$i + 1]; $i++; continue; }
			if ($kar === $tirnak) { $tirnak = ''; }
			continue;
		}
		if ($kar === '"' or $kar === "'") { $tirnak = $kar; $parca .= $kar; continue; }
		if ($kar === '(' or $kar === '[') { $derinlik++; $parca .= $kar; continue; }
		if ($kar === ')' or $kar === ']') { $derinlik--; $parca .= $kar; continue; }
		if ($kar === ',' and $derinlik === 0) { $argumanlar[] = trim($parca); $parca = ''; continue; }
		$parca .= $kar;
	}
	$argumanlar[] = trim($parca);
	return $argumanlar;
}

$hata = 0;
$toplam = 0;
$temiz = 0;

foreach ($dosyalar as $p) {
	$g = file_get_contents($p);
	if (strpos($g, 'mysqli_stmt_bind_param') === false) { continue; }
	$rel = str_replace($proje_kok . '/', '', $p);

	$offset = 0;
	while (($konum = strpos($g, 'mysqli_stmt_bind_param', $offset)) !== false) {
		/* fonksiyon adi gercekten cagri mi kontrol et */
		$ad_uzunluk = strlen('mysqli_stmt_bind_param');
		$sonraki = substr($g, $konum + $ad_uzunluk, 1);
		if ($sonraki !== '(' and $sonraki !== ' ' and $sonraki !== "\n" and $sonraki !== "\t") {
			$offset = $konum + $ad_uzunluk;
			continue;
		}
		$argumanlar = bind_param_argumanlari($g, $konum);
		$offset = $konum + $ad_uzunluk;
		if ($argumanlar === null or count($argumanlar) < 2) { continue; }
		$toplam++;
		$tipler = trim($argumanlar[1], '"\'');
		$degiskenler = array_slice($argumanlar, 2);
		$adet = count($degiskenler);
		if (strlen($tipler) !== $adet) {
			$hata++;
			echo "  [HATA] $rel\n";
			echo "         tipler: '$tipler' (" . strlen($tipler) . ") | degisken: $adet\n";
			echo "         " . implode(', ', $argumanlar) . "\n";
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
