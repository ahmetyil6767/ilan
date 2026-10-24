<?php
if(count(get_included_files())==1) {die();}
$jaayquickpanel_menu[]=array("baslik"=>"XML Sitemap","url"=>"eklentiler.php?eklenti=xml_sitemap&sayfa=olustur","ikon"=>
"sitemap","sira"=>70);

function sitemap_guncelleme_kontrol() {
	$sitemap_url=eklenti_ayar_getir("sitemap_url","xml_sitemap");
	$son_guncelleme=strtotime(eklenti_ayar_getir("son_guncelleme","xml_sitemap"));
	if ($sitemap_url) {
		$tarih_saat=strtotime(tarih_saat());
		$guncelleme_sikligi=eklenti_ayar_getir("guncelleme_sikligi","xml_sitemap")*60*60;
		$fark_saat=$guncelleme_sikligi-($tarih_saat-$son_guncelleme);
		if ($fark_saat<0) {
			sitemap_guncelle();
		}
	}
}

/* hreflang <xhtml:link> etiketleri - 4 dil icin */
function sitemap_hreflang_satirlari($url) {
	$satirlar = '';
	$diller = array('tr','en','fr','ar');
	foreach ($diller as $dil) {
		$href = $url . (strpos($url, '?') === false ? '?' : '&') . 'dil=' . $dil;
		$satirlar .= "\t\t<xhtml:link rel=\"alternate\" hreflang=\"" . $dil . "\" href=\"" . $href . "\" />\n";
	}
	$satirlar .= "\t\t<xhtml:link rel=\"alternate\" hreflang=\"x-default\" href=\"" . $url . "\" />\n";
	return $satirlar;
}

/* Tek bir <url> blogu olusturur */
function sitemap_url_blogu($url, $lastmod = null, $changefreq = 'weekly', $priority = '0.5') {
	$satir = "\t<url>\n";
	$satir .= "\t\t<loc>" . $url . "</loc>\n";
	if ($lastmod) {
		$satir .= "\t\t<lastmod>" . $lastmod . "</lastmod>\n";
	}
	$satir .= sitemap_hreflang_satirlari($url);
	$satir .= "\t\t<changefreq>" . $changefreq . "</changefreq>\n";
	$satir .= "\t\t<priority>" . $priority . "</priority>\n";
	$satir .= "\t</url>\n";
	return $satir;
}

function sitemap_dosya_yaz($dosya_adi, $govde) {
	$inc_dizin = ROOT_DIR . "/";
	if (!is_dir($inc_dizin)) { return false; }
	$olustur = fopen($inc_dizin . $dosya_adi, "w");
	if (!$olustur) { return false; }
	fwrite($olustur, $govde);
	fclose($olustur);
	return true;
}

function sitemap_guncelle() {
	global $baglan;

	// Tema fonksiyonlarini (magaza_link_getir, tema_ayar, url_olustur vb.)
	// yukle - eklenti dosyasi tema fonksiyonlarindan once include edilir.
	$tema = function_exists('tema_ne') ? tema_ne() : '';
	if ($tema && file_exists(ROOT_DIR . '/temalar/' . $tema . '/fonksiyonlar.php')) {
		include_once(ROOT_DIR . '/temalar/' . $tema . '/fonksiyonlar.php');
	}

	$site_url = site_url();
	$today = date('Y-m-d');

	$baslik_urlset = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
	$baslik_urlset .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"' . "\n";
	$baslik_urlset .= '        xmlns:xhtml="http://www.w3.org/1999/xhtml">' . "\n";
	$bitir_urlset = '</urlset>';

	/* ---- 1) Sayfalar (anasayfa + sayfa tipli icerikler) ---- */
	$satirlar = '';
	// Anasayfa
	$satirlar .= sitemap_url_blogu($site_url, $today, 'always', '1.0');
	// Sayfa tipli icerikler
	$sql = mysqli_query($baglan, "select id, tarih_saat from icerik where tip='sayfa' and durum='1' order by id desc");
	if ($sql) {
		while ($dizi = mysqli_fetch_array($sql)) {
			$id = $dizi['id'];
			$parcala = explode(' ', $dizi['tarih_saat']);
			$tarih = $parcala[0];
			$url = link_getir($id);
			$satirlar .= sitemap_url_blogu($url, $tarih, 'weekly', '0.6');
		}
	}
	$govde = $baslik_urlset . $satirlar . $bitir_urlset . "\n";
	sitemap_dosya_yaz('sitemap_pages.xml', $govde);

	/* ---- 2) Kategoriler ---- */
	$satirlar = '';
	$tipler = array('ilan','sayfa');
	foreach ($tipler as $tip) {
		$sql = mysqli_query($baglan, "select id from kategoriler where tip='" . mysqli_real_escape_string($baglan, $tip) . "' and sinif='kategori' order by id desc");
		if ($sql) {
			while ($dizi = mysqli_fetch_array($sql)) {
				$id = $dizi['id'];
				$url = kategori_link_getir($id);
				$satirlar .= sitemap_url_blogu($url, null, 'daily', '0.7');
			}
		}
	}
	$govde = $baslik_urlset . $satirlar . $bitir_urlset . "\n";
	sitemap_dosya_yaz('sitemap_categories.xml', $govde);

	/* ---- 3) Magazalar ---- */
	$satirlar = '';
	$sql = mysqli_query($baglan, "select id, tarih_saat from magazalar where durum='1' order by id desc");
	if ($sql) {
		while ($dizi = mysqli_fetch_array($sql)) {
			$id = $dizi['id'];
			$parcala = explode(' ', $dizi['tarih_saat']);
			$tarih = $parcala[0];
			$url = magaza_link_getir($id);
			$satirlar .= sitemap_url_blogu($url, $tarih, 'weekly', '0.6');
		}
	}
	$govde = $baslik_urlset . $satirlar . $bitir_urlset . "\n";
	sitemap_dosya_yaz('sitemap_stores.xml', $govde);

	/* ---- 4) Ilanlar ---- */
	$satirlar = '';
	$sql = mysqli_query($baglan, "select id, tarih_saat from icerik where tip='ilan' and durum='1' order by id desc");
	if ($sql) {
		while ($dizi = mysqli_fetch_array($sql)) {
			$id = $dizi['id'];
			$parcala = explode(' ', $dizi['tarih_saat']);
			$tarih = $parcala[0];
			$url = link_getir($id);
			$satirlar .= sitemap_url_blogu($url, $tarih, 'weekly', '0.5');
		}
	}
	$govde = $baslik_urlset . $satirlar . $bitir_urlset . "\n";
	sitemap_dosya_yaz('sitemap_ads.xml', $govde);

	/* ---- 5) Index (sitemap.xml) ---- */
	$index = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
	$index .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
	$alt_dosyalar = array('sitemap_pages.xml','sitemap_categories.xml','sitemap_stores.xml','sitemap_ads.xml');
	foreach ($alt_dosyalar as $alt) {
		$index .= "\t<sitemap>\n";
		$index .= "\t\t<loc>" . $site_url . "/" . $alt . "</loc>\n";
		$index .= "\t\t<lastmod>" . $today . "</lastmod>\n";
		$index .= "\t</sitemap>\n";
	}
	$index .= '</sitemapindex>' . "\n";
	sitemap_dosya_yaz('sitemap.xml', $index);

	$tarih_saat = tarih_saat();
	ayar_guncelle("xml_sitemap-son_guncelleme", $tarih_saat);
}

sitemap_guncelleme_kontrol();
?>
