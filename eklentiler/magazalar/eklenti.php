<?php
if(count(get_included_files())==1) {die();}
if (mysqli_num_rows(mysqli_query($baglan,"SHOW TABLES LIKE '%magazalar%'"))==0) {
	mysqli_query($baglan,"
	CREATE TABLE IF NOT EXISTS `magazalar` (
	  `id` int(10) NOT NULL AUTO_INCREMENT,
	  `baslik` varchar(70) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
	  `kategori` int(10) NOT NULL,
	  `uye_id` int(10) NOT NULL,
	  `tarih_saat` varchar(20) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
	  `durum` int(1) NOT NULL,
	  PRIMARY KEY (`id`),
	  KEY `uye_id` (`uye_id`),
	  KEY `durum` (`durum`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci AUTO_INCREMENT=1 ;	
	");
}
if (mysqli_num_rows(mysqli_query($baglan,"SHOW TABLES LIKE '%magaza_uye%'"))==0) {
	mysqli_query($baglan,"
	CREATE TABLE IF NOT EXISTS `magaza_uye` (
	  `id` int(10) NOT NULL AUTO_INCREMENT,
	  `uye_id` int(10) NOT NULL,
	  `magaza_id` int(10) NOT NULL,
	  PRIMARY KEY (`id`),
	  KEY `magaza_id` (`magaza_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci AUTO_INCREMENT=1 ;	
	");
}
function magaza_ekle($baslik,$uye_id=null,$kategori=null,$durum=0) {
	global $baglan;
	if (!$uye_id) {
		$uye_id=$_SESSION["oturum_id"];
	}
	$tarih_saat=tarih_saat();
	$ekle=mysqli_query($baglan,"insert into magazalar set baslik='$baslik', uye_id='$uye_id', tarih_saat='$tarih_saat', kategori='$kategori', durum='$durum'");
	return $eklenen_id=mysqli_insert_id($baglan);
}
function magaza_sayisi($uye_id=null) {
	global $baglan;
	if (!$uye_id) {
		$uye_id=$_SESSION["oturum_id"];
	}
	return $sor=mysqli_num_rows(mysqli_query($baglan,"select * from magazalar where uye_id='$uye_id'"));
}
function magaza_uye_sayisi($magaza_id=null) {
	global $baglan;
	if (!$magaza_id) {
		$magaza_id=$_GET["id"];
	}
	return $sor=mysqli_num_rows(mysqli_query($baglan,"select * from magaza_uye where magaza_id='$magaza_id'"));
}
function magaza_uyeleri($magaza_id=null) {
	global $baglan;
	if (!$magaza_id) {
		$magaza_id=$_GET["id"];
	}
	$temsilciler=array();
	$sql=mysqli_query($baglan,"select * from magaza_uye where magaza_id='$magaza_id'");
	while ($dizi=mysqli_fetch_array($sql)) {
		$uye_id=$dizi["uye_id"];
		$temsilciler[]=$uye_id;
	}
	return $temsilciler;
}
function uye_temsilcilikleri($uye_id=null) {
	global $baglan;
	if (!$uye_id) {
		$uye_id=$_SESSION["oturum_id"];
	}
	$sql=mysqli_query($baglan,"select * from magaza_uye where uye_id='$uye_id'");
	while ($dizi=mysqli_fetch_array($sql)) {
		$magaza_id=$dizi["magaza_id"];
		$array[]=$magaza_id;
	}
	if (is_array(@$array)) {
		return $array;
	}
}
function magaza_uye_ekle($uye_id,$magaza_id) {
	global $baglan;
	$sor=mysqli_num_rows(mysqli_query($baglan,"select * from magaza_uye where uye_id='$uye_id' and magaza_id='$magaza_id'"));
	if ($sor==0) {
		$ekle=mysqli_query($baglan,"insert into magaza_uye set uye_id='$uye_id', magaza_id='$magaza_id'");
		return true;
	} else {
		return false;
	}
}
function magaza_uye_sil($uye_id,$magaza_id) {
	global $baglan;
	$sor=mysqli_query($baglan,"delete from magaza_uye where uye_id='$uye_id' and magaza_id='$magaza_id'");
}
function magaza_bilgi($bilgi,$magaza_id) {
	return ekstra_bilgi_getir($bilgi,$magaza_id,"magaza");
}
function magaza_bilgi_guncelle($bilgi,$deger,$magaza_id) {
	$guncelle=ekstra_bilgi_guncelle($bilgi,$deger,$magaza_id,"magaza");
}

?>