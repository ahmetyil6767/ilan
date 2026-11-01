<?php
// Gecici: PanelFunctionTest istek fonksiyonunu CSRF uyumlu hale getir
$dosya = __DIR__.'/tests/PanelFunctionTest.php';
$icerik = file_get_contents($dosya);

$yeni_fonk = 'function istek($url, $post = null, $ciz = null)
{
	global $baglan;
	// CSRF token: POST islemlerinde token otomatik alinir
	if ($post !== null) {
		$ch0 = curl_init($url);
		$opt0 = array(CURLOPT_RETURNTRANSFER => true, CURLOPT_FOLLOWLOCATION => false, CURLOPT_TIMEOUT => 20);
		if ($ciz) { $opt0[CURLOPT_COOKIEJAR] = $ciz; $opt0[CURLOPT_COOKIEFILE] = $ciz; }
		curl_setopt_array($ch0, $opt0);
		$h0 = (string)curl_exec($ch0);
		curl_close($ch0);
		if (preg_match(\'/name="csrf_token" value="([^"]+)"/\', $h0, $m0)) {
			$post = $post . \'&csrf_token=\' . urlencode($m0[1]);
		}
	}
	$ch = curl_init($url);
	$opt = array(CURLOPT_RETURNTRANSFER => true, CURLOPT_FOLLOWLOCATION => false, CURLOPT_TIMEOUT => 40);
	if ($ciz) { $opt[CURLOPT_COOKIEJAR] = $ciz; $opt[CURLOPT_COOKIEFILE] = $ciz; }
	if ($post !== null) { $opt[CURLOPT_POST] = true; $opt[CURLOPT_POSTFIELDS] = $post; }
	curl_setopt_array($ch, $opt);
	$h = (string)curl_exec($ch);
	$kod = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);
	return array($kod, $h);
}';

// Eski fonksiyonu bul ve degistir
$baslangic = strpos($icerik, 'function istek(');
$bitis = strpos($icerik, "\n}", $baslangic) + 2;
if ($baslangic !== false && $bitis !== false) {
    $icerik = substr($icerik, 0, $baslangic) . $yeni_fonk . substr($icerik, $bitis);
    file_put_contents($dosya, $icerik);
    echo "OK: istek fonksiyonu guncellendi\n";
} else {
    echo "HATA: fonksiyon bulunamadi\n";
}
