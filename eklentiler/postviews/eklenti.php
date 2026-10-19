<?php
if(count(get_included_files())==1) {die();}
$limonpanel_menu[]=array("baslik"=>_LANG('tx_postviews_raporlari'),"url"=>"eklentiler.php?eklenti=postviews&sayfa=rapor","ikon"=>"eye","sira"=>51);

if (mysqli_num_rows(mysqli_query($baglan,"SHOW TABLES LIKE '%postviews%'"))==0) {
	mysqli_query($baglan,"
	CREATE TABLE IF NOT EXISTS `postviews` (
	  `v_id` int(11) NOT NULL AUTO_INCREMENT,
	  `id` int(11) COLLATE utf8_turkish_ci NOT NULL,
	  `tip` varchar(10) COLLATE utf8_turkish_ci NOT NULL,
	  `tarih` varchar(20) COLLATE utf8_turkish_ci NOT NULL,
	  `son_ziyaret` varchar(20) COLLATE utf8_turkish_ci NOT NULL,
	  `hit` int(10) NOT NULL,
	  PRIMARY KEY (`v_id`),
	  KEY `hit` (`hit`),
	  KEY `id` (`id`),
	  KEY `tip` (`tip`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci AUTO_INCREMENT=1 ;
");
}

function hit_guncelle() {
	global $baglan;
	global $admin_dizini;
	if (strstr($_SERVER["SCRIPT_NAME"], "icerik.php") and !strstr(browser_url(), "/$admin_dizini/")) {
		$id=$_GET["id"];
		$tip=bilgi_getir("icerik","tip",$id);
		
		$pw_yonetici_hit=eklenti_ayar_getir("yonetici_hit","postviews");
		$pw_session=eklenti_ayar_getir("session","postviews");
		$pw_haric_tipler=eklenti_ayar_getir("haric_tip","postviews");
		$pw_say=explode(",",rtrim(eklenti_ayar_getir("say","postviews"),","));
			
		if (!strstr($pw_haric_tipler, $tip)) {

		if ($pw_yonetici_hit=="hayir" and $_SESSION["yonetici_id"]!="") {
			
		} else {

			$mevcut_hit=ekstra_bilgi_getir("hit",$id,$tip);

			if ($pw_session=="evet") {
				
				@$hit_dizisi=@$_SESSION["hit"];
				if ($hit_dizisi=="") {
					$hit_dizisi=array();
				}

				if (!in_array("$id", $hit_dizisi)) {

					array_push($hit_dizisi,"$id");
					$_SESSION["hit"]=$hit_dizisi;
					
					// Toplam sayım kaydediliyor
					$tarih_saat=tarih_saat();
					$toplam_sor=mysqli_num_rows(mysqli_query($baglan,"select * from postviews where id='$id' and tip='toplam'"));
					if ($toplam_sor>0) {
						mysqli_query($baglan,"update postviews set hit=hit+1, son_ziyaret='$tarih_saat' where id='$id' and tip='toplam'");
					} else {
						mysqli_query($baglan,"insert into postviews set hit='1', id='$id', tip='toplam', son_ziyaret='$tarih_saat'");
					}
					// Günlük sayım işaretliyse kaydediliyor.
					if (in_array("(gun)", $pw_say)) {
						$bugun=date("d-m-Y");
						$gun_sor=mysqli_num_rows(mysqli_query($baglan,"select * from postviews where id='$id' and tip='gun' and tarih='$bugun'"));
						if ($gun_sor>0) {
							mysqli_query($baglan,"update postviews set hit=hit+1, son_ziyaret='$tarih_saat' where id='$id' and tip='gun' and tarih='$bugun'");
						} else {
							mysqli_query($baglan,"insert into postviews set hit='1', id='$id', tip='gun', tarih='$bugun', son_ziyaret='$tarih_saat'");
						}
					}
					// Haftalık sayım işaretliyse kaydediliyor.
					if (in_array("(hafta)", $pw_say)) {
						$bu_hafta=date("W");
						$hafta_sor=mysqli_num_rows(mysqli_query($baglan,"select * from postviews where id='$id' and tip='hafta' and tarih='$bu_hafta'"));
						if ($hafta_sor>0) {
							mysqli_query($baglan,"update postviews set hit=hit+1, son_ziyaret='$tarih_saat' where id='$id' and tip='hafta' and tarih='$bu_hafta'");
						} else {
							mysqli_query($baglan,"insert into postviews set hit='1', id='$id', tip='hafta', tarih='$bu_hafta', son_ziyaret='$tarih_saat'");
						}
					}					
					// Aylık sayım işaretliyse kaydediliyor.
					if (in_array("(ay)", $pw_say)) {
						$bu_ay=date("m-Y");
						$ay_sor=mysqli_num_rows(mysqli_query($baglan,"select * from postviews where id='$id' and tip='ay' and tarih='$bu_ay'"));
						if ($ay_sor>0) {
							mysqli_query($baglan,"update postviews set hit=hit+1, son_ziyaret='$tarih_saat' where id='$id' and tip='ay' and tarih='$bu_ay'");
						} else {
							mysqli_query($baglan,"insert into postviews set hit='1', id='$id', tip='ay', tarih='$bu_ay', son_ziyaret='$tarih_saat'");
						}
					}
					
				}
			} else {
				// Toplam sayım kaydediliyor
				$tarih_saat=tarih_saat();
				$toplam_sor=mysqli_num_rows(mysqli_query($baglan,"select * from postviews where id='$id' and tip='toplam'"));
				if ($toplam_sor>0) {
					mysqli_query($baglan,"update postviews set hit=hit+1, son_ziyaret='$tarih_saat' where id='$id' and tip='toplam'");
				} else {
					mysqli_query($baglan,"insert into postviews set hit='1', id='$id', tip='toplam', son_ziyaret='$tarih_saat'");
				}
				
				// Günlük sayım işaretliyse kaydediliyor.
				if (in_array("(gun)", $pw_say)) {
					$bugun=date("d-m-Y");
					$gun_sor=mysqli_num_rows(mysqli_query($baglan,"select * from postviews where id='$id' and tip='gun' and tarih='$bugun'"));
					if ($gun_sor>0) {
						mysqli_query($baglan,"update postviews set hit=hit+1, son_ziyaret='$tarih_saat' where id='$id' and tip='gun' and tarih='$bugun'");
					} else {
						mysqli_query($baglan,"insert into postviews set hit='1', id='$id', tip='gun', tarih='$bugun', son_ziyaret='$tarih_saat'");
					}
				}
				// Haftalık sayım işaretliyse kaydediliyor.
				if (in_array("(hafta)", $pw_say)) {
					$bu_hafta=date("W");
					$hafta_sor=mysqli_num_rows(mysqli_query($baglan,"select * from postviews where id='$id' and tip='hafta' and tarih='$bu_hafta'"));
					if ($hafta_sor>0) {
						mysqli_query($baglan,"update postviews set hit=hit+1, son_ziyaret='$tarih_saat' where id='$id' and tip='hafta' and tarih='$bu_hafta'");
					} else {
						mysqli_query($baglan,"insert into postviews set hit='1', id='$id', tip='hafta', tarih='$bu_hafta', son_ziyaret='$tarih_saat'");
					}
				}
				// Aylık sayım işaretliyse kaydediliyor.
				if (in_array("(ay)", $pw_say)) {
					$bu_ay=date("m-Y");
					$ay_sor=mysqli_num_rows(mysqli_query($baglan,"select * from postviews where id='$id' and tip='ay' and tarih='$bu_ay'"));
					if ($ay_sor>0) {
						mysqli_query($baglan,"update postviews set hit=hit+1, son_ziyaret='$tarih_saat' where id='$id' and tip='ay' and tarih='$bu_ay'");
					} else {
						mysqli_query($baglan,"insert into postviews set hit='1', id='$id', tip='ay', tarih='$bu_ay', son_ziyaret='$tarih_saat'");
					}
				}				
			}
			
		}
		
	}

	}
}

function postviews($id=null,$tip="toplam",$tarih=null) {
	global $baglan;
	if (!$id) {
		$id=$_GET["id"];
	}
	if ($tip=="gun" or $tip=="hafta" or $tip=="ay") {
		if (!$tarih) {
			if ($tip=="gun") {$tarih=date("d-m-Y");}
			if ($tip=="hafta") {$tarih=date("W");}
			if ($tip=="ay") {$tarih=date("m-Y");}
		}
		$dizi=mysqli_fetch_array(mysqli_query($baglan,"select * from postviews where id='$id' and tarih='$tarih'"));	
	} else {
		$dizi=mysqli_fetch_array(mysqli_query($baglan,"select * from postviews where id='$id' and tip='toplam'"));
	}
	$hit=$dizi["hit"];
	if (!$hit) {$hit=0;}
	return $hit;
}

function hit_yaz($id=null) {
	if (!$id) {
		$id=$_GET["id"];
	}
	return postviews($id);
}

hit_guncelle();
?>