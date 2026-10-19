<?php
if(count(get_included_files())==1) {die();}
if (mysqli_num_rows(mysqli_query($baglan,"SHOW TABLES LIKE '%favoriler%'"))==0) {
	mysqli_query($baglan,"
	CREATE TABLE IF NOT EXISTS `favoriler` (
	  `f_id` int(10) NOT NULL AUTO_INCREMENT,
	  `id` int(10) NOT NULL,
	  `uye_id` int(10) NOT NULL,
	  `tarih` varchar(20) COLLATE utf8_turkish_ci NOT NULL,
	  PRIMARY KEY (`f_id`),
	  KEY `uye_id` (`uye_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci AUTO_INCREMENT=1 ;
");
}

function favori_ekle($icerik_id,$uye_id=null) {
	global $baglan;
	if (!$uye_id and isset($_SESSION["oturum_id"])) {
		$uye_id=$_SESSION["oturum_id"];
	}
	if (@$uye_id) {
		if (!favori_mi($icerik_id,$uye_id)) {
		$tarih=tarih_saat();
			$ekle=mysqli_query($baglan,"insert into favoriler set id='$icerik_id', uye_id='$uye_id', tarih='$tarih'");
			if ($ekle) {
				return true;
			} else {
				return false;
			}
		}
		else false;
	}
}

function favori_sil($icerik_id,$uye_id=null) {
	global $baglan;
	if (!$uye_id and isset($_SESSION["oturum_id"])) {
		$uye_id=$_SESSION["oturum_id"];
	}
	if ($uye_id) {
		if (favori_mi($icerik_id,$uye_id)) {
			$sil=mysqli_query($baglan,"delete from favoriler where id='$icerik_id' and uye_id='$uye_id'");
			if ($sil) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
}

function favori_mi($icerik_id,$uye_id=null) {
	global $baglan;
	if (!$uye_id and isset($_SESSION["oturum_id"])) {
		$uye_id=$_SESSION["oturum_id"];
	}
	if (isset($uye_id)) {
		$kontrol=mysqli_num_rows(mysqli_query($baglan,"select * from favoriler where id='$icerik_id' and uye_id='$uye_id'"));
		if ($kontrol==1) {
			return true;
		} else {
			return false;
		}
	}
}

function favori_icerikler($uye_id=null) {
	global $baglan;
	if (!$uye_id and isset($_SESSION["oturum_id"])) {
		$uye_id=@$_SESSION["oturum_id"];
	}
	$kontrol=mysqli_query($baglan,"select * from favoriler where uye_id='$uye_id'");
	$say=mysqli_num_rows($kontrol);
	if ($say>0) {
		$idler=null;
		while ($dizi=mysqli_fetch_array($kontrol)) {
			$idler.=$dizi["id"].",";
		}
		return rtrim($idler,",");		
	} else return false;
}

function favori_sayisi($uye_id=null) {
	global $baglan;
	if (!$uye_id and isset($_SESSION["oturum_id"])) {
		$uye_id=$_SESSION["oturum_id"];
	}
	$favori_idler=favori_icerikler($uye_id);
	if ($favori_idler) {
		$bol=explode(",",$favori_idler);
		return count($bol);		
	} else {
		return 0;
	}
}

function kac_kisinin_favorisi($icerik_id=null) {
	global $baglan;
	if (!$icerik_id) {
		$icerik_id=$_GET["id"];
	}
	return $sor=mysqli_num_rows(mysqli_query($baglan,"select id from favoriler where id='$icerik_id'"));
}

?>