<?php
if(count(get_included_files())==1) {die();}
if (mysqli_num_rows(mysqli_query($baglan,"SHOW TABLES LIKE '%gorusmeler%'"))==0) {
	mysqli_query($baglan,"
	CREATE TABLE IF NOT EXISTS `gorusmeler` (
	  `id` int(10) NOT NULL AUTO_INCREMENT,
	  `uye_1` int(10) NOT NULL,
	  `uye_2` int(10) NOT NULL,
	  `tarih_saat` varchar(20) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
	  PRIMARY KEY (`id`),
	  KEY `uye_1` (`uye_1`),
	  KEY `uye_2` (`uye_2`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci AUTO_INCREMENT=1 ;	
	");
}
if (mysqli_num_rows(mysqli_query($baglan,"SHOW TABLES LIKE '%mesajlar%'"))==0) {
	mysqli_query($baglan,"
	CREATE TABLE IF NOT EXISTS `mesajlar` (
	  `id` int(10) NOT NULL AUTO_INCREMENT,
	  `gorusme_id` int(10) NOT NULL,
	  `uye_id` int(10) NOT NULL,
	  `kime` int(10) NOT NULL,
	  `mesaj` text CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
	  `tarih_saat` varchar(20) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
	  `okundu` int(1) NOT NULL,
	  PRIMARY KEY (`id`),
	  KEY `uye_id` (`uye_id`),
	  KEY `kime` (`kime`),
	  KEY `gorusme_id` (`gorusme_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci AUTO_INCREMENT=1 ;	
	");
}
function gorusme_id($uye_1,$uye_2,$ilan_id) {
	global $baglan;
	$sor=mysqli_query($baglan,"select * from gorusmeler where ilan_id='$ilan_id' and (((uye_1='$uye_1' and uye_2='$uye_2') or (uye_2='$uye_1' and uye_1='$uye_2')) and (sil_1='0' and sil_2='0'))");
	$say=mysqli_num_rows($sor);
	if ($say==1) {
		$gorusme_dizi=mysqli_fetch_array($sor);
		$gorusme_id=$gorusme_dizi["id"];
	} else {
		$tarih_saat=tarih_saat();
		$ekle=mysqli_query($baglan,"insert into gorusmeler set uye_1='$uye_1', uye_2='$uye_2', tarih_saat='$tarih_saat', ilan_id='$ilan_id'");
		$gorusme_id=mysqli_insert_id($baglan);
	}
	return $gorusme_id;
}
function mesaj_gonder($mesaj,$gorusme_id) {
	global $baglan;
	$uye_id=$_SESSION["oturum_id"];
	$uye_1=getir("gorusmeler","uye_1",$gorusme_id);
	$uye_2=getir("gorusmeler","uye_2",$gorusme_id);
	$mesaj=strip_tags(trim($mesaj));
	$kime=diger_uye_id($uye_1,$uye_2);
	if ($mesaj!="" and $uye_id) {
		$tarih_saat=tarih_saat();
		$ekle=mysqli_query($baglan,"insert into mesajlar set uye_id='$uye_id', kime='$kime', mesaj='$mesaj', gorusme_id='$gorusme_id', tarih_saat='$tarih_saat', okundu='0'");
		$gorusme_update=guncelle("gorusmeler","tarih_saat",$tarih_saat,$gorusme_id);
		if ($ekle) {
			return true;
		} else {
			return false;
		}	
	}
}
function diger_uye_id($uye_1,$uye_2) {
	$oturum_id=$_SESSION["oturum_id"];
	if ($uye_1==$oturum_id) {
		return $uye_2;
	} else {
		return $uye_1;
	}
}
function mesaj_sayisi($gorusme_id) {
	global $baglan;
	return $sayi=mysqli_num_rows(mysqli_query($baglan,"select * from mesajlar where gorusme_id='$gorusme_id'"));
}

?>