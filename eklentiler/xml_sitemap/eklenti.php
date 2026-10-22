<?php
if(count(get_included_files())==1) {die();}
$jaayquickpanel_menu[]=array("baslik"=>"XML Sitemap","url"=>"eklentiler.php?eklenti=xml_sitemap&sayfa=olustur","ikon"=>"sitemap","sira"=>70);
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
function sitemap_guncelle() {
	global $baglan;
	$basla='<?xml version="1.0" encoding="UTF-8"?>
	<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
	';
	$bitir='</urlset>';
	$dosya_adi=eklenti_ayar_getir("sitemap_url","xml_sitemap");
	$icerik_tipleri=eklenti_ayar_getir("icerik_tipleri","xml_sitemap");
	$tipler_bol=explode(",",$icerik_tipleri);
	$satirlar=null;
	$site_url=site_url();
	// Anasayfa bağlantısı
	$anasayfa_dizi=mysqli_fetch_array(mysqli_query($baglan,"select * from icerik where durum='1' order by id desc limit 1"));
	$anasayfa_tarih_saat=$anasayfa_dizi["tarih_saat"];
	$parcala=explode(" ",$anasayfa_tarih_saat);
	$yaz_tarih=$parcala[0];
	$satir="
	<url>
	  <loc>$site_url</loc>
		<lastmod>$yaz_tarih</lastmod>
		<changefreq>always</changefreq>
		<priority>1.0</priority>
	</url>
	";
	$satirlar.=$satir;
	foreach ($tipler_bol as $tip) {
		// İçerik tipleri bağlantıları
		$sql=mysqli_query($baglan,"select * from icerik where tip='$tip' and durum='1' order by id desc");
		while($dizi=mysqli_fetch_array($sql)){
		$id=$dizi["id"];
		$tarih_saat=$dizi["tarih_saat"];
		$parcala=explode(" ",$tarih_saat);
		$yaz_tarih=$parcala[0];
		$url=link_getir($id);
		$satir="
		<url>
		  <loc>$url</loc>
			<lastmod>$yaz_tarih</lastmod>
			<changefreq>weekly</changefreq>
			<priority>0.5</priority>
		</url>
		";
		$satirlar.=$satir; // veri tabanındaki verileri icerik değişkenimize attık(xml şeklinde).
		}
		// Kategori bağlantıları
		$sql=mysqli_query($baglan,"select * from kategoriler where tip='$tip' and sinif='kategori' order by id desc");
		while($dizi=mysqli_fetch_array($sql)){
		$id=$dizi["id"];
		$url=kategori_link_getir($id);
		$satir="
		<url>
		  <loc>$url</loc>
			<changefreq>daily</changefreq>
			<priority>0.7</priority>
		</url>
		";
		$satirlar.=$satir; // veri tabanındaki verileri icerik değişkenimize attık(xml şeklinde).
		}

	}
	$inc_dizin=include_dizin();
	if ($inc_dizin=="..") {$inc_dizin.="/";}
	$olustur=fopen($inc_dizin.$dosya_adi, "w"); // dosyamızı açıtık	
	fwrite($olustur,$basla.$satirlar.$bitir);// dosya içeriğimizi oluşturduk.
	fclose($olustur); // dosya mızı kapattık yazma işlemini bitirdik.
	$tarih_saat=tarih_saat();
	ayar_guncelle("xml_sitemap-son_guncelleme",$tarih_saat);
}
sitemap_guncelleme_kontrol();
?>