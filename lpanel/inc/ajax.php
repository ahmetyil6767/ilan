<?php
require("../../loader.php");
$islem=$_GET["islem"];
$ref_url=explode("/$admin_dizini",ref_url())[0];
if (site_url()!=$ref_url) {
	die("Hata: Erisim engellendi.");
}
if (!$islem) {
	die("Hata");
}
if ($islem=="eb_guncelle") {
	$guncelle_id=$_POST["guncelle_id"];
	$guncelle_anahtar=$_POST["anahtar"];
	$guncelle_deger=$_POST["deger"];
	$guncelle=mysqli_query($baglan,"update ekstra_bilgi set anahtar='$guncelle_anahtar', deger='$guncelle_deger' where b_id='$guncelle_id'");
}
if ($islem=="eb_sil") {
	$sil_id=$_POST["sil_id"];
	$sil=mysqli_query($baglan,"delete from ekstra_bilgi where b_id='$sil_id'");
}
if ($islem=="eb_ekle") {
	$anahtar=$_POST["anahtar"];
	$deger=$_POST["deger"];
	$icerik_id=$_POST["icerik_id"];
	bilgi_guncelle($anahtar,$deger,$icerik_id);
}
if ($islem=="kategori_ekle") {
	$eklenecek_kategori=$_POST["eklenecek_kategori"];
	if ($eklenecek_kategori) {
		$eklenecek_kategori_ust=$_POST["eklenecek_kategori_ust"];
		$eklenecek_tip=$_POST["tip"];
		$eklenecek_sinif=$_POST["sinif"];
		$eklenecek_url=url_olustur($eklenecek_kategori);
		$ekle=mysqli_query($baglan,"insert into kategoriler set baslik='$eklenecek_kategori', url='$eklenecek_url', ust_kategori='$eklenecek_kategori_ust', tip='$eklenecek_tip', sinif='$eklenecek_sinif'");
		$eklenen_id=mysqli_insert_id($baglan);
		echo $eklenen_id;
	}
}
if ($islem=="kategori_secimi_sifirla") {
	$icerik_id=$_POST["id"];
	$sinif=$_POST["sinif"];
	mysqli_query($baglan,"update icerik set kategori='' where id='$icerik_id'");
}
if ($islem=="limonsoft_duyurular") {
	if ($lpanel_duyurular==1) {
		$site=getenv("HTTP_HOST"); 
		if (substr($site, 0, 4) == "www.") {$site=substr($site,4);} 
		$sidebar_mesaj=file_get_contents("http://www.limontasarim.com/limonsoft.php?ref=panel"); 
		if ($sidebar_mesaj) {
			echo "<div class='home_sidebar mesaj'>".$sidebar_mesaj."</div>";
		}
	}
}
?>