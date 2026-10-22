<?php
require("../../loader.php");
$islem=$_GET["islem"];
/*
 * Panel erisim kontrolu oturum tabanlidir.
 * Onceki surum Referer basligina bagliydi; tarayici veya proxy
 * Referer'i gondermezse istek reddediliyordu. loader.php zaten
 * /lpanel/ altinda yonetici oturumu zorunlu kiliyor; buradaki
 * kontrol ikinci savunma katmanidir.
 */
if (!isset($_SESSION["yonetici_id"]) or !yonetici_mi($_SESSION["yonetici_id"])) {
	die(_LANG('p_erisim_engellendi'));
}
if (!$islem) {
	die(_LANG('p_erisim_engellendi'));
}
if ($islem=="eb_guncelle") {
	$guncelle_id=(int)($_POST["guncelle_id"] ?? 0);
	$guncelle_anahtar=isset($_POST["anahtar"]) ? $_POST["anahtar"] : "";
	$guncelle_deger=isset($_POST["deger"]) ? $_POST["deger"] : "";
	$guncelle_sorgu=mysqli_prepare($baglan,"update ekstra_bilgi set anahtar=?, deger=? where b_id=?");
	mysqli_stmt_bind_param($guncelle_sorgu,"ssi",$guncelle_anahtar,$guncelle_deger,$guncelle_id);
	mysqli_stmt_execute($guncelle_sorgu);
	mysqli_stmt_close($guncelle_sorgu);
}
if ($islem=="eb_sil") {
	$sil_id=(int)($_POST["sil_id"] ?? 0);
	$sil_sorgu=mysqli_prepare($baglan,"delete from ekstra_bilgi where b_id=?");
	mysqli_stmt_bind_param($sil_sorgu,"i",$sil_id);
	mysqli_stmt_execute($sil_sorgu);
	mysqli_stmt_close($sil_sorgu);
}
if ($islem=="eb_ekle") {
	$anahtar=isset($_POST["anahtar"]) ? $_POST["anahtar"] : "";
	$deger=isset($_POST["deger"]) ? $_POST["deger"] : "";
	$icerik_id=(int)($_POST["icerik_id"] ?? 0);
	if ($anahtar!="" and $icerik_id) { bilgi_guncelle($anahtar,$deger,$icerik_id); }
}
if ($islem=="kategori_ekle") {
	$eklenecek_kategori=isset($_POST["eklenecek_kategori"]) ? $_POST["eklenecek_kategori"] : "";
	if ($eklenecek_kategori) {
		$eklenecek_kategori_ust=(int)($_POST["eklenecek_kategori_ust"] ?? 0);
		$eklenecek_tip=isset($_POST["tip"]) ? $_POST["tip"] : "";
		$eklenecek_sinif=isset($_POST["sinif"]) ? $_POST["sinif"] : "";
		$eklenecek_url=url_olustur($eklenecek_kategori);
		$ekle_sorgu=mysqli_prepare($baglan,"insert into kategoriler set baslik=?, url=?, ust_kategori=?, tip=?, sinif=?");
		mysqli_stmt_bind_param($ekle_sorgu,"ssiss",$eklenecek_kategori,$eklenecek_url,$eklenecek_kategori_ust,$eklenecek_tip,$eklenecek_sinif);
		mysqli_stmt_execute($ekle_sorgu);
		mysqli_stmt_close($ekle_sorgu);
		$eklenen_id=mysqli_insert_id($baglan);
		echo $eklenen_id;
	}
}
if ($islem=="kategori_secimi_sifirla") {
	$icerik_id=(int)($_POST["id"] ?? 0);
	$sinif=isset($_POST["sinif"]) ? $_POST["sinif"] : "";
	if ($icerik_id) {
		$sifirla_sorgu=mysqli_prepare($baglan,"update icerik set kategori='' where id=?");
		mysqli_stmt_bind_param($sifirla_sorgu,"i",$icerik_id);
		mysqli_stmt_execute($sifirla_sorgu);
		mysqli_stmt_close($sifirla_sorgu);
	}
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