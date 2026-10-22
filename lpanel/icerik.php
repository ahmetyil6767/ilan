<?php
include("../loader.php");
yonetici_degilse_yonlendir($oturum_id,ayar_getir("site_url")."/$admin_dizini/giris-yap.php");
$tip=isset($_GET["tip"])?$_GET["tip"]:"";
if ($tip=="") {$tip="yazi";}
$tip_ozellikleri=isset($icerik_tipleri[$tip])?$icerik_tipleri[$tip]:array();
$tip_baslik=$tip_ozellikleri["baslik"];
$tip_baslik_cogul=$tip_ozellikleri["baslik_cogul"];
$tip_baslik_isaret=$tip_ozellikleri["baslik_isaret"];
@$tip_ekle_buton=$tip_ozellikleri["ekle_buton"];
@$tip_url=$tip_ozellikleri["url"];
@$tip_icerik=$tip_ozellikleri["icerik"];
@$tip_gorsel=$tip_ozellikleri["gorsel"];
@$tip_gorsel_dizin=$tip_ozellikleri["gorsel_dizin"];
@$tip_galeri=$tip_ozellikleri["galeri"];
@$tip_galeri_dizin=$tip_ozellikleri["galeri_dizin"];
@$tip_tablo_gorsel=$tip_ozellikleri["tablo_gorsel"];
@$tip_kategori=$tip_ozellikleri["kategori"];
@$tip_kategori_detayi=$tip_ozellikleri["kategori_detayi"];
@$tip_kategori_ortak=$tip_ozellikleri["kategori_ortak"];
@$tip_ekstra_siniflar=$tip_ozellikleri["ekstra_siniflar"];
@$tip_yazar=$tip_ozellikleri["yazar"];
@$tip_yazar_baslik=$tip_ozellikleri["yazar_baslik"];
@$tip_etiket=$tip_ozellikleri["etiket"];
@$tip_durum=$tip_ozellikleri["durum"];
@$tip_durumlar=$tip_ozellikleri["durumlar"];
@$tip_ekstra_bilgiler=$tip_ozellikleri["ekstra_bilgiler"];
@$tip_ekstra_detaylar=$tip_ozellikleri["ekstra_detaylar"];
@$tip_tarih_saat_detayi=$tip_ozellikleri["tarih_saat_detayi"];
@$tip_durum_detayi=$tip_ozellikleri["durum_detayi"];
@$tip_yazar_detayi=$tip_ozellikleri["yazar_detayi"];
@$tip_custom_url=$tip_ozellikleri["custom_url"];
@$tip_gizli=$tip_ozellikleri["gizli"];
@$tip_yetki=$tip_ozellikleri["yetki"];
@$tip_datatable_siralama=$tip_ozellikleri["datatable_siralama"];
@$tip_datatable_siralama_tip=$tip_ozellikleri["datatable_siralama_tip"];
@$tip_siralama=$tip_ozellikleri["siralama"];
@$tip_siralama_tip=$tip_ozellikleri["siralama_tip"];
@$tip_resim_boyutlandir=$tip_ozellikleri["resim_boyutlandir"];
@$tip_galeri_boyutlandir=$tip_ozellikleri["galeri_boyutlandir"];
@$tip_galeri_kucuk_resim=$tip_ozellikleri["galeri_kucuk_resim"];
@$tip_baslik_yok=$tip_ozellikleri["baslik_yok"];
@$tip_baslik_detayi=$tip_ozellikleri["baslik_detayi"];
@$tip_ekstra_bilgiler_baslik=$tip_ozellikleri["ekstra_bilgiler_baslik"];
if ($tip_durumlar) {
	@$icerik_durumlari=$tip_durumlar;
}
if (!is_array($tip_resim_boyutlandir)) {
	@$tip_resim_boyutlandir=$resim_boyutlandir;
}
if (!is_array($tip_galeri_boyutlandir)) {
	@$tip_galeri_boyutlandir=$tip_resim_boyutlandir;
}
if (!$tip_galeri_kucuk_resim) {
	@$tip_galeri_kucuk_resim="full";
}
if (!$tip_gorsel_dizin) {
	@$tip_gorsel_dizin="images";
}
if (!$tip_galeri_dizin) {
	@$tip_galeri_dizin="images";
}
if ($tip_datatable_siralama) {
	$datatable_sirala=$tip_datatable_siralama;
	$datatable_sirala_tip=$tip_datatable_siralama_tip;
} else {
	$datatable_sirala=0;
	$datatable_sirala_tip="desc";	
}
if ($tip_siralama) {
	$sirala=$tip_siralama;
	$sirala_tip=$tip_siralama_tip;
} else {
	$sirala="tarih_saat";
	$sirala_tip="desc";	
}
if (!$tip_yazar_baslik) {
	$tip_yazar_baslik="Yazar";
}

if ($tip_kategori_ortak) {$kategori_tip=$tip_kategori_ortak;} else {$kategori_tip=$tip;}

@$id=$_GET["id"];
@$islem=$_GET["islem"];
@$buton=$_POST["buton"];
@$buton_yayinla=$_POST["buton_yayinla"];

if ($islem=="guncelle" or $islem=="sil") {
	if ($_SESSION["oturum_yetki"]=="yazar") {
		if (getir("icerik","yazar",$id)!=$_SESSION["yonetici_id"]) {
			$_SESSION["mesaj"]=array(_LANG('p_yetki_yok'),"danger");
			yonlendir("icerik.php?tip=$tip");
		}
	}
}

if ($buton or $buton_yayinla) {
	@$baslik=trim($_POST["baslik"]);
	@$icerik=addslashes($_POST["icerik"]);
	@$kategori_dizisi=$_POST["kategori"];
	@$url=addslashes($_POST["url"]);
	if (is_array($kategori_dizisi)) {
		foreach ($kategori_dizisi as $yaz) {
			if ($yaz!=_LANG('p_select')) {
				$kategori.=",($yaz)";
			}
		}
		$kategori=ltrim($kategori,",");		
	}

	if ($url=="") {
		$url=url_olustur($baslik);
	}
	@$durum=$_POST["durum"];
	if ($tip_yazar==1) {
		@$yazar=$_POST["yazar"];
		if (!$yazar) {
			$yazar=$oturum_id;
		}
	} else {
		@$yazar=getir("icerik","yazar",$id);
		if (!$yazar) {
			$yazar=$oturum_id;
		}
	}

	$tarih_saat=tarih_saat();
	if ($islem=="ekle") {

		if ($buton_yayinla) {$durum=1;}
		$sql=mysqli_prepare($baglan,"insert into icerik set baslik=?, url=?, icerik=?, kategori=?, tarih_saat=?, yazar=?, durum=?, tip=?");
  	$yazar_int=(int)$yazar;
  	$durum_int=(int)$durum;
  	mysqli_stmt_bind_param($sql,"sssssiis",$baslik,$url,$icerik,$kategori,$tarih_saat,$yazar_int,$durum_int,$tip);
  	mysqli_stmt_execute($sql);
  	mysqli_stmt_close($sql);
		$eklenen_id=mysqli_insert_id($baglan);
		foreach ($kategori_dizisi as $kat_yaz) {
			kategori_icerik_sayisi_guncelle($kat_yaz);
		}
		foreach ($tip_ekstra_bilgiler as $yaz => $x) {
			$eb_type=$x["tip"];
			$eb_name=$x["id"];
			@$deger=addslashes($_POST["$eb_name"]);
			if ($eb_type!="multiple_checkbox" and $eb_type!="upload") {
				
				if ($eb_type=="location") {
					$lat=$_POST["map_$eb_name-lat"];
					$lon=$_POST["map_$eb_name-lon"];
					$radius=$_POST["map_$eb_name-radius"];
					$zoom=$_POST["map_$eb_name-zoom"];
					$deger="$lat,$lon,$radius,$zoom";
				}
				
				if ($deger!="") {
					ekstra_bilgi_guncelle($eb_name,$deger,$eklenen_id,$tip);
				}
			}
			else if ($eb_type=="multiple_checkbox") {
				foreach ($deger as $yaz) {	
					$diziden_cikart.="($yaz),";
				}
				if ($diziden_cikart!="") {
					ekstra_bilgi_guncelle($eb_name,$diziden_cikart,$eklenen_id,$tip);
				}
				$diziden_cikart=null;
			}
			else if ($eb_type=="upload") {
				$eb_boyutlar=$x["boyutlar"];
				$eb_dizin=$x["dizin"];
				$eb_orijinali_sil=$x["orijinali_sil"];
				$eb_gecerli_boyut=$x["gecerli_boyut"];		
				
				// v2 resim upload remove gÃ¼ncellemesi baÅŸladÄ±.
				$secenekler=array();
				if (is_array($eb_boyutlar)) {
					$secenekler["boyutlar"]=$eb_boyutlar;
				}
				if ($eb_dizin) {
					$secenekler["dizin"]=$eb_dizin;
				}
				if ($eb_orijinali_sil) {
					$secenekler["orijinali_sil"]=$eb_orijinali_sil;
				}
				if ($eb_gecerli_boyut) {
					$secenekler["cikti"]=$eb_gecerli_boyut;
				}
				$resim_dizisi=$_FILES["$eb_name"];
				$resim_adi=$resim_dizisi["name"];
				if ($resim_adi!="") {
					$resim_url=yukle($resim_dizisi,$secenekler);
					$mevcut_deger=ekstra_bilgi_getir($eb_name,$eklenen_id,$tip);
					ekstra_bilgi_guncelle($eb_name,$resim_url,$eklenen_id,$tip);
				}
			}
		} 
		if ($tip_etiket==1 or $tip_etiket=="") {
			@$etiketler=$_POST["etiketler"];
			if ($etiketler!="") {
				ekstra_bilgi_guncelle("etiketler",$etiketler,$eklenen_id,$tip);
			}
		}
		// SEO AÃ§Ä±klama ekleniyor
		@$seo_title=$_POST["seo_title"];
		if ($seo_title) {
			ekstra_bilgi_guncelle("seo_title",$seo_title,$eklenen_id,$tip);
		}
		@$seo_aciklama=$_POST["seo_aciklama"];
		if ($seo_aciklama) {
			ekstra_bilgi_guncelle("seo_aciklama",$seo_aciklama,$eklenen_id,$tip);
		}
		@$custom_url=$_POST["custom_url"];
		if ($custom_url) {
			ekstra_bilgi_guncelle("custom_url",$custom_url,$eklenen_id,$tip);
		}
		if ($tip_gorsel==1 or $tip_gorsel=="") {
			$resim_dizisi=$_FILES["resim_jaayquick"];
			$resim_adi=$resim_dizisi["name"];
			if ($resim_adi!="") {
				$resim_url=yukle($resim_dizisi,array("dizin"=>"$tip_gorsel_dizin","boyutlar"=>$tip_resim_boyutlandir,"filtre"=>1));
				// Mevcut resim silinsin
				$mevcut_resim=bilgi("gorsel",$eklenen_id);
				bilgi_guncelle("gorsel",$resim_url,$eklenen_id);
			}
		}
		if ($tip_galeri==1 or $tip_galeri=="") {
			$resim_dizisi=$_FILES["jaayquick_galeri"]; 
			$resim_sayisi=count($resim_dizisi["name"]);		
			$yuklenenler=yukle($resim_dizisi,array("dizin"=>"$tip_galeri_dizin","boyutlar"=>$tip_resim_boyutlandir,"filtre"=>1));
			if (is_array($yuklenenler) and count($yuklenenler)>0) {
				foreach ($yuklenenler as $yuklenen) {
					galeri_ekle($yuklenen,$eklenen_id);
				}
			}		
		}
		if ($tip_baslik_yok=="1") {
			$_SESSION["mesaj"]=array(_LANG('p_icerik_eklendi'),"success");
		} else {
			$_SESSION["mesaj"]=array(_LANG('p_adi_ile_icerik_eklendi'), array($baslik),"success");
		}			
		$oturum_ad_soyad=bilgi_getir("uyeler","ad_soyad",$oturum_id);
		$eklenen_link=link_getir($eklenen_id);
		if (function_exists('lpanel_uye_profil_link')) {
			$oturum_link=lpanel_uye_profil_url($oturum_id);
			bildirim_ekle(_LANG('p_bild_icerik_eklendi_link', array($oturum_link,$oturum_ad_soyad,$eklenen_link,$baslik)), $oturum_id, 0);
		} else {
			bildirim_ekle(_LANG('p_bild_icerik_eklendi_plain', array($oturum_ad_soyad,$eklenen_link,$baslik)), $oturum_id, 0);
		}
		if ($durum==1) {
			yonlendir("icerik.php?tip=$tip");		
		} else {
			$aktif_tab=$_POST["aktif_tab"];
			if ($aktif_tab) {
				yonlendir("icerik.php?tip=$tip&islem=guncelle&id=$eklenen_id&tab=$aktif_tab");
			} else {
				yonlendir("icerik.php?tip=$tip&islem=guncelle&id=$eklenen_id");
			}			
		}

	} else if ($islem=="guncelle") {
		if ($buton) {$durum=$durum;} else if ($buton_yayinla) {$durum=1;}
		$tarih_saat=tarih_saat();
		
		$eski_kategoriler=getir("icerik","kategori",$id);
		if ($eski_kategoriler!="") {
			$bul=array("(",")");
			$degistir=array(null,null);
			$eski_kategori_dizisi=explode(",",str_replace($bul,$degistir,$eski_kategoriler));
		}

		if ($buton) {
			$sql=mysqli_prepare($baglan,"update icerik set baslik=?, url=?, icerik=?, durum=?, g_tarih_saat=? where id=?");
  	$durum_int=(int)$durum;
  	$id_int=(int)$id;
  	mysqli_stmt_bind_param($sql,"ssssii",$baslik,$url,$icerik,$durum_int,$tarih_saat,$id_int);
  	mysqli_stmt_execute($sql);
  	mysqli_stmt_close($sql);
		} else if ($buton_yayinla) {
			$sql=mysqli_prepare($baglan,"update icerik set baslik=?, url=?, icerik=?, durum=?, g_tarih_saat=? where id=?");
  	$durum_int=(int)$durum;
  	$id_int=(int)$id;
  	mysqli_stmt_bind_param($sql,"ssssii",$baslik,$url,$icerik,$durum_int,$tarih_saat,$id_int);
  	mysqli_stmt_execute($sql);
  	mysqli_stmt_close($sql);
			$ilan_suresi=tema_ayar("ilan_suresi");
			if ($ilan_suresi==2) {
				$ilan_gun_sayisi=tema_ayar("gun");
				if ($ilan_gun_sayisi>0) {
					$ilan_yayin_tarihi=getir("icerik","tarih_saat",$id);
					$sure_bitis=tarih_saat_hesapla("+$ilan_gun_sayisi days",$ilan_yayin_tarihi);
					if ($sure_bitis<tarih_saat()) {
						guncelle("icerik","tarih_saat",tarih_saat(),$id);
					}
				}
			}
		}
		$mevcut_yazar=getir("icerik","yazar",$id);
		if ($yazar and $yazar!=$mevcut_yazar) {
			guncelle("icerik","yazar",$yazar,$id);
		}
		if (@$kategori) {
			guncelle("icerik","kategori",$kategori,$id);
		}		
		if (is_array($kategori_dizisi)) {
			foreach ($kategori_dizisi as $kat_yaz) {
				kategori_icerik_sayisi_guncelle($kat_yaz);
			}
		}
		if (is_array(@$eski_kategori_dizisi)) {
			foreach ($eski_kategori_dizisi as $kat_eski_yaz) {
				kategori_icerik_sayisi_guncelle($kat_eski_yaz);
			}
		}
		if (is_array($tip_ekstra_bilgiler)) {
			foreach ($tip_ekstra_bilgiler as $yaz => $x) {
				$eb_type=$x["tip"];
				$eb_name=$x["id"];
				@$deger=$_POST["$eb_name"];
				$mevcut_deger=ekstra_bilgi_getir($eb_name,$id,$tip);
				if ($eb_type!="multiple_checkbox" and $eb_type!="upload") {
					
					if ($eb_type=="location") {
						$lat=$_POST["map_$eb_name-lat"];
						$lon=$_POST["map_$eb_name-lon"];
						$radius=$_POST["map_$eb_name-radius"];
						$zoom=$_POST["map_$eb_name-zoom"];
						$deger="$lat,$lon,$radius,$zoom";
					}
					
					if ($deger!=$mevcut_deger) {
						ekstra_bilgi_guncelle($eb_name,$deger,$id,$tip);
					}
				}
				else if ($eb_type=="multiple_checkbox") {
					foreach ($deger as $yaz) {	
						$diziden_cikart.="($yaz),";
					}
					if ($deger!=$mevcut_deger) {
						ekstra_bilgi_guncelle($eb_name,$diziden_cikart,$id,$tip);
					}
					$diziden_cikart=null;
				}
				else if ($eb_type=="upload") {
					$eb_boyutlar=$x["boyutlar"];
					$eb_dizin=$x["dizin"];
					$eb_orijinali_sil=$x["orijinali_sil"];
					$eb_gecerli_boyut=$x["gecerli_boyut"];		
					
					// v2 resim upload remove gÃ¼ncellemesi baÅŸladÄ±.
					$secenekler=array();
					if (is_array($eb_boyutlar)) {
						$secenekler["boyutlar"]=$eb_boyutlar;
					}
					if ($eb_dizin) {
						$secenekler["dizin"]=$eb_dizin;
					}
					if ($eb_orijinali_sil) {
						$secenekler["orijinali_sil"]=$eb_orijinali_sil;
					}
					if ($eb_gecerli_boyut) {
						$secenekler["cikti"]=$eb_gecerli_boyut;
					}
					$resim_dizisi=$_FILES["$eb_name"];
					$resim_adi=$resim_dizisi["name"];
					if ($resim_adi!="") {
						$resim_url=yukle($resim_dizisi,$secenekler);
						$mevcut_deger=ekstra_bilgi_getir($eb_name,$id,$tip);
						ekstra_bilgi_guncelle($eb_name,$resim_url,$id,$tip);
						if ($mevcut_deger!=$resim_url) {
							if ($eb_gecerli_boyut) {
								$mevcut_deger=thumbnail_url("full",$mevcut_deger,$eb_gecerli_boyut);
							}
							sil($mevcut_deger);
							foreach ($x["boyutlar"] as $yaz) {
								$thumbnail_url=thumbnail_url($yaz,$mevcut_deger);
								sil($thumbnail_url);
							}
						}
					}
				}
			}
		}
		
		// SEO AÃ§Ä±klama ekleniyor
		@$seo_title=$_POST["seo_title"];
		if ($seo_title) {
			ekstra_bilgi_guncelle("seo_title",$seo_title,$id,$tip);
		} else {
			$b_id=ekstra_bilgi_id_getir("seo_title",$id,$tip);
			ekstra_bilgi_sil($b_id);
		}
		@$seo_aciklama=$_POST["seo_aciklama"];
		if ($seo_aciklama) {
			ekstra_bilgi_guncelle("seo_aciklama",$seo_aciklama,$id,$tip);
		} else {
			$b_id=ekstra_bilgi_id_getir("seo_aciklama",$id,$tip);
			ekstra_bilgi_sil($b_id);
		}
		@$custom_url=$_POST["custom_url"];
		if ($custom_url) {
			ekstra_bilgi_guncelle("custom_url",$custom_url,$id,$tip);
		} else {
			$b_id=ekstra_bilgi_id_getir("custom_url",$id,$tip);
			ekstra_bilgi_sil($b_id);
		}
		if ($tip_etiket==1 or $tip_etiket=="") {
			@$etiketler=addslashes($_POST["etiketler"]);
			$mevcut_etiketler=ekstra_bilgi_getir("etiketler",$id,$tip);
			if ($etiketler!=$mevcut_etiketler) {
				ekstra_bilgi_guncelle("etiketler",$etiketler,$id,$tip);
			}
		}	
		if ($tip_gorsel==1 or $tip_gorsel=="") {
			$resim_dizisi=$_FILES["resim_jaayquick"];
			$resim_adi=$resim_dizisi["name"];
			if ($resim_adi!="") {
				$resim_url=yukle($resim_dizisi,array("dizin"=>"$tip_gorsel_dizin","boyutlar"=>$tip_resim_boyutlandir,"filtre"=>1));
				// Mevcut resim silinsin
				$mevcut_resim=bilgi("gorsel",$id);
				bilgi_guncelle("gorsel",$resim_url,$id);
				if ($mevcut_resim!="") {
					sil($mevcut_resim);
					foreach ($tip_resim_boyutlandir as $yaz) {
						$thumbnail_url=thumbnail_url($yaz,$mevcut_resim);
						sil($thumbnail_url);
					}
				}
			}
		}
		
		if ($tip_galeri==1 or $tip_galeri=="") {
			$resim_dizisi=$_FILES["jaayquick_galeri"]; 
			$resim_sayisi=count($resim_dizisi["name"]);		
			$yuklenenler=yukle($resim_dizisi,array("dizin"=>"$tip_galeri_dizin","boyutlar"=>$tip_resim_boyutlandir,"filtre"=>1));
			if (is_array($yuklenenler) and count($yuklenenler)>0) {
				foreach ($yuklenenler as $yuklenen) {
					galeri_ekle($yuklenen,$id);
				}
			}
			$mevcut_resim_sayisi=resim_sayisi($id);
			if ($mevcut_resim_sayisi>0) {
				@$resim_baslik_dizisi=$_POST["galeri_baslik"];
				@$resim_url_dizisi=$_POST["galeri_url"];
				@$resim_icerik_dizisi=$_POST["galeri_icerik"];
				@$resim_sira_dizisi=$_POST["galeri_sira"];
				@$resim_sil_dizisi=$_POST["galeri_sil"];
				foreach ($resim_baslik_dizisi as $resim_id=>$yeni_baslik) {
					$guncelle = mysqli_prepare($baglan,"update galeri set baslik=? where id=?");
  	$resim_id_int=(int)$resim_id;
  	mysqli_stmt_bind_param($guncelle,"si",$yeni_baslik,$resim_id_int);
  	mysqli_stmt_execute($guncelle);
  	mysqli_stmt_close($guncelle);
				}
				foreach ($resim_url_dizisi as $resim_id=>$yeni_url) {
					if (!$yeni_url) {
						$yeni_url=url_olustur(getir("galeri","baslik",$resim_id));
					}
					$guncelle = mysqli_prepare($baglan,"update galeri set url=? where id=?");
  	$resim_id_int=(int)$resim_id;
  	mysqli_stmt_bind_param($guncelle,"si",$yeni_url,$resim_id_int);
  	mysqli_stmt_execute($guncelle);
  	mysqli_stmt_close($guncelle);
				}	
				foreach ($resim_icerik_dizisi as $resim_id=>$yeni_icerik) {
					$guncelle = mysqli_prepare($baglan,"update galeri set icerik=? where id=?");
  	$resim_id_int=(int)$resim_id;
  	mysqli_stmt_bind_param($guncelle,"si",$yeni_icerik,$resim_id_int);
  	mysqli_stmt_execute($guncelle);
  	mysqli_stmt_close($guncelle);
				}	
				foreach ($resim_sira_dizisi as $resim_id=>$yeni_sira) {
					$guncelle = mysqli_prepare($baglan,"update galeri set sira=? where id=?");
  	$yeni_sira_int=(int)$yeni_sira;
  	$resim_id_int=(int)$resim_id;
  	mysqli_stmt_bind_param($guncelle,"ii",$yeni_sira_int,$resim_id_int);
  	mysqli_stmt_execute($guncelle);
  	mysqli_stmt_close($guncelle);
				}	
				foreach ($resim_sil_dizisi as $resim_sil_id=>$sil_id) {
					$image_url=getir("galeri","resim",$resim_sil_id);
					tumunu_sil($image_url,$tip_galeri_boyutlandir);				
					$sil = mysqli_prepare($baglan,"delete from galeri where id=?");
  	$resim_sil_id_int=(int)$resim_sil_id;
  	mysqli_stmt_bind_param($sil,"i",$resim_sil_id_int);
  	mysqli_stmt_execute($sil);
  	mysqli_stmt_close($sil);
				}
				// Toplu resim sil
			}			
		}
		
		if ($buton) {
			if ($tip_baslik_yok=="1") {
				$_SESSION["mesaj"]=array(_LANG('p_icerik_guncellendi'),"success");
			} else {
				$_SESSION["mesaj"]=array(_LANG('p_adi_ile_icerik_guncellendi'), array($baslik),"success");
			}			
			$oturum_ad_soyad=bilgi_getir("uyeler","ad_soyad",$oturum_id);
			$icerik_link=link_getir($id);
			if (function_exists('lpanel_uye_profil_link')) {
				$oturum_link=lpanel_uye_profil_url($oturum_id);
				bildirim_ekle(_LANG('p_bild_icerik_guncellendi_link', array($oturum_link,$oturum_ad_soyad,$icerik_link,$baslik)), $oturum_id, 0);
			} else {
				bildirim_ekle(_LANG('p_bild_icerik_guncellendi_plain', array($oturum_ad_soyad,$icerik_link,$baslik)), $oturum_id, 0);
			}	
			$aktif_tab=$_POST["aktif_tab"];
			if ($aktif_tab) {
				yonlendir("icerik.php?tip=$tip&islem=guncelle&id=$id&tab=$aktif_tab");
			} else {
				yonlendir("icerik.php?tip=$tip&islem=guncelle&id=$id");
			}						
		} else if ($buton_yayinla) {
			if ($tip_baslik_yok=="1") {
				$_SESSION["mesaj"]=array(_LANG('p_icerik_guncellendi_yayinlandi'),"success");
			} else {
				$_SESSION["mesaj"]=array(_LANG('p_adi_ile_icerik_guncellendi_yayinlandi'), array($baslik),"success");
			}
			
			$oturum_ad_soyad=bilgi_getir("uyeler","ad_soyad",$oturum_id);
			$icerik_link=link_getir($id);
			if (function_exists('lpanel_uye_profil_link')) {
				$oturum_link=lpanel_uye_profil_url($oturum_id);
				bildirim_ekle(_LANG('p_bild_icerik_yayinlandi_link', array($oturum_link,$oturum_ad_soyad,$icerik_link,$baslik)), $oturum_id, 0);
			} else {
				bildirim_ekle(_LANG('p_bild_icerik_yayinlandi_plain', array($oturum_ad_soyad,$icerik_link,$baslik)), $oturum_id, 0);
			}
			yonlendir("icerik.php?tip=$tip");
		}
		
	}
}
@$bilgi_ekle_buton=$_POST["bilgi_ekle_buton"];
if ($bilgi_ekle_buton and !$buton) {
	$anahtar=$_POST["anahtar"];
	$deger=addslashes($_POST["deger"]);
	if ($anahtar and $deger) {
		ekstra_bilgi_guncelle($anahtar,$deger,$id,$tip);
	}
}
if ($islem=="sil") {
	// Ä°Ã§erik siliniyor
	// Ã–ne Ã§Ä±karÄ±lan gÃ¶rsel varsa sil
	$mevcut_resim=thumbnail("full",$id);
	if ($mevcut_resim) {
		sil($mevcut_resim);
		foreach ($tip_resim_boyutlandir as $yaz) {
			$thumbnail_url=thumbnail_url($yaz,$mevcut_resim);
			sil($thumbnail_url);
		}
	}
	// Varsa galeri gÃ¶rselleri siliniyor
	$resim_ara_sql=mysqli_query($baglan,"select * from galeri where icerik_id='$id'");
	$resim_sayisi=mysqli_num_rows($resim_ara_sql); 
	if ($resim_sayisi>0) {
		while ($resim_dizisi=mysqli_fetch_array($resim_ara_sql)) {
		$resim_sil_id=$resim_dizisi["id"];	
		$image_url=$resim_dizisi["resim"];	
		sil($image_url);
		foreach ($tip_galeri_boyutlandir as $yaz) {
			$thumbnail_url=thumbnail_url($yaz,$image_url);
			sil($thumbnail_url);
		} 					
		$sil = mysqli_prepare($baglan,"delete from galeri where id=?");
  	$resim_sil_id_int=(int)$resim_sil_id;
  	mysqli_stmt_bind_param($sil,"i",$resim_sil_id_int);
  	mysqli_stmt_execute($sil);
  	mysqli_stmt_close($sil);
		}
	}
	$silinen_baslik=bilgi_getir("icerik","baslik",$id);

	$eski_kategoriler=getir("icerik","kategori",$id);
	if ($eski_kategoriler!="") {
		$bul=array("(",")");
		$degistir=array(null,null);
		$eski_kategori_dizisi=explode(",",str_replace($bul,$degistir,$eski_kategoriler));
	}	
	
	$id_int=(int)$id;
  	$icerik_sil=mysqli_prepare($baglan,"delete from icerik where id=?");
  	mysqli_stmt_bind_param($icerik_sil,"i",$id_int);
  	mysqli_stmt_execute($icerik_sil);
  	mysqli_stmt_close($icerik_sil);
  	$ekstra_bilgi_sil=mysqli_prepare($baglan,"delete from ekstra_bilgi where id=? and tip=?");
  	mysqli_stmt_bind_param($ekstra_bilgi_sil,"is",$id_int,$tip);
  	mysqli_stmt_execute($ekstra_bilgi_sil);
  	mysqli_stmt_close($ekstra_bilgi_sil);
	
	if ($tip_baslik_yok=="1") {
		$_SESSION["mesaj"]=array(_LANG('p_icerik_silindi'),"success");
	} else {
		$_SESSION["mesaj"]=array(_LANG('p_adi_ile_icerik_silindi'), array($silinen_baslik),"success");
	}
	
	if (function_exists('lpanel_uye_profil_link')) {
		$oturum_link=lpanel_uye_profil_url($oturum_id);
		bildirim_ekle(_LANG('p_bild_icerik_sildi_link'), array($oturum_link,$oturum_ad_soyad,$silinen_baslik),$oturum_id,0);
	} else {
		bildirim_ekle(_LANG('p_bild_icerik_sildi_plain'), array($oturum_ad_soyad,$silinen_baslik),$oturum_id,0);
	}
	foreach($eski_kategori_dizisi as $yaz) {
		kategori_icerik_sayisi_guncelle($yaz);
	}
	yonlendir("icerik.php?tip=$tip");	
}
if ($islem=="yayinla") {
	// Ä°Ã§erik siliniyor
	$baslik=bilgi_getir("icerik","baslik",$id);
	$tarih_saat=tarih_saat();

	$kategoriler=getir("icerik","kategori",$id);
	if ($kategoriler!="") {
		$bul=array("(",")");
		$degistir=array(null,null);
		$kategori_dizisi=explode(",",str_replace($bul,$degistir,$kategoriler));
	}
	
	$guncelle=mysqli_prepare($baglan,"update icerik set durum=1, g_tarih_saat=? where id=?");
  	$id_int=(int)$id;
  	mysqli_stmt_bind_param($guncelle,"si",$tarih_saat,$id_int);
  	mysqli_stmt_execute($guncelle);
  	mysqli_stmt_close($guncelle);
	$ilan_suresi=tema_ayar("ilan_suresi");
	if ($ilan_suresi==2) {
		$ilan_gun_sayisi=tema_ayar("gun");
		if ($ilan_gun_sayisi>0) {
			$ilan_yayin_tarihi=getir("icerik","tarih_saat",$id);
			$sure_bitis=tarih_saat_hesapla("+$ilan_gun_sayisi days",$ilan_yayin_tarihi);
			if ($sure_bitis<tarih_saat()) {
				guncelle("icerik","tarih_saat",tarih_saat(),$id);
			}
		}
	}
	
	foreach($kategori_dizisi as $yaz) {
		kategori_icerik_sayisi_guncelle($yaz);
	}	
	if ($tip_baslik_yok=="1") {
		$_SESSION["mesaj"]=array(_LANG('p_icerik_yayinlandi'),"success");
	} else {
		$_SESSION["mesaj"]=array(_LANG('p_adi_ile_icerik_yayinlandi'), array($baslik),"success");
	}
	$_SESSION["mesaj"]=array(_LANG('p_adi_ile_icerik_yayinlandi'), array($baslik),"success");
	$oturum_ad_soyad=bilgi_getir("uyeler","ad_soyad",$oturum_id);
	$icerik_link=link_getir($id);
	if (function_exists('lpanel_uye_profil_link')) {
		$oturum_link=lpanel_uye_profil_url($oturum_id);
				bildirim_ekle(_LANG('p_bild_icerik_yayinlandi_link', array($oturum_link,$oturum_ad_soyad,$icerik_link,$baslik)), $oturum_id, 0);
	} else {
				bildirim_ekle(_LANG('p_bild_icerik_yayinlandi_plain', array($oturum_ad_soyad,$icerik_link,$baslik)), $oturum_id, 0);
	}
	yonlendir("icerik.php?tip=$tip");
}
if ($islem=="gorsel_sil") {
	// Mevcut resim silinsin
	$resim=ekstra_bilgi_getir("gorsel",$id,$tip);
	if ($resim!="") {
		sil($resim);
		foreach ($tip_resim_boyutlandir as $yaz) {
			$thumbnail_url=thumbnail_url($yaz,$resim);
			sil($thumbnail_url);
		} 			
		$sil_eb_id=ekstra_bilgi_id_getir("gorsel",$id,$tip);
		ekstra_bilgi_sil($sil_eb_id);
		$_SESSION["mesaj"]=array(_LANG('p_one_cikarilan_gorsel_kaldirildi'),"success");
		yonlendir("icerik.php?tip=$tip&islem=guncelle&id=$id");		
	}
}
@$sil_eb_id=$_GET["sil_eb_id"];
@$guncelle_eb_id=$_GET["guncelle_eb_id"];
if ($sil_eb_id) {
	$sql=mysqli_prepare($baglan,"delete from ekstra_bilgi where b_id=?");
  	$sil_eb_id_int=(int)$sil_eb_id;
  	mysqli_stmt_bind_param($sql,"i",$sil_eb_id_int);
  	mysqli_stmt_execute($sql);
  	mysqli_stmt_close($sql);
	$_SESSION["mesaj"]=array(_LANG('msj_ekstra_bilgi_silindi'),"success");
	yonlendir("icerik.php?tip=$tip&islem=guncelle&id=$id");		
}
if ($guncelle_eb_id) {
	@$bilgi_guncelle_buton=$_POST["bilgi_guncelle_buton"];
	@$anahtar=$_POST["anahtar"];
	@$deger=addslashes($_POST["deger"]);
	if ($bilgi_guncelle_buton) {
		if ($anahtar!="") {
		$sql=mysqli_prepare($baglan,"update ekstra_bilgi set anahtar=?, deger=? where b_id=?");
  	$guncelle_eb_id_int=(int)$guncelle_eb_id;
  	mysqli_stmt_bind_param($sql,"ssi",$anahtar,$deger,$guncelle_eb_id_int);
  	mysqli_stmt_execute($sql);
  	mysqli_stmt_close($sql);
		$_SESSION["mesaj"]=array(_LANG('p_ekstra_bilgi_guncellendi'),"success");
		yonlendir("icerik.php?tip=$tip&islem=guncelle&id=$id");
		} else {
		$_SESSION["mesaj"]=array(_LANG('p_ekstra_bilgi_guncellenemedi'),"danger");
		yonlendir("icerik.php?tip=$tip&islem=guncelle&id=$id&guncelle_eb_id=$guncelle_eb_id");			
		}
	}
}
// Ä°Ã§erik listeleme sql sorgusu ve iÃ§erik sayÄ±sÄ±

/* DATATABLE PASÄ°F */
$datatable=ayar_getir("datatable");
if ($datatable==0) {
@$page=$_GET["p"];
@$kacarli_buton=$_POST["kacarli_buton"];
if ($kacarli_buton) {
	@$kacarli=$_POST["kacarli"];
	$_SESSION["kacarli"]=$kacarli;
}
if (!isset($_SESSION["kacarli"])) {
	@$kacarli=30;
} else {
	@$kacarli=$_SESSION["kacarli"];
}
@$filtre_durum=$_GET["durum"];
@$filtre_sql=null;
if (isset($filtre_durum)) {
	$filtre_sql.=" and durum='$filtre_durum'";
}
@$filtre_ara=$_GET["ara"];
if ($filtre_ara) {
	$filtre_sql.=" and (baslik like '%$filtre_ara%' or icerik like '%$filtre_ara%')";
}
@$filtre_kategori=$_GET["kategori"];
if (isset($filtre_kategori)) {
	$filtre_sql.=" and (kategori like '%($filtre_kategori)%')";
}
$toplam=mysqli_num_rows(mysqli_query($baglan,"select * from icerik where tip='$tip' $filtre_sql")); 
$kac_sayfa=ceil($toplam/$kacarli);
if ($page=="") {$page=1;}
$baslangic=($page-1)*$kacarli;
}
if ($datatable==0) {
	$sql=mysqli_query($baglan,"select * from icerik where tip='$tip' $filtre_sql order by $sirala $sirala_tip limit $baslangic, $kacarli");
} else {
	$sql=mysqli_query($baglan,"select * from icerik where tip='$tip' $filtre_sql");
}
$icerik_sayisi=mysqli_num_rows($sql);
/* DATATABLE PASÄ°F */
$panel_sablon=ayar_getir("panel_sablon");
if ($panel_sablon=="fullwidth") {
	$div_class="col-md-30";$bilgi_div="col-md-30";
	$icerik_sol_div="col-lg-47 col-md-44";
	$icerik_sag_div="col-lg-13 col-md-16";
} else if ($panel_sablon=="boxed") {
	$div_class="col-md-60";$bilgi_div="hide";
	$icerik_sol_div="col-lg-44 col-md-44";
	$icerik_sag_div="col-lg-16 col-md-16";
}
	
@$toplu_buton=$_POST["toplu_buton"];
if ($toplu_buton) {
    $toplu=$_POST["toplu"];

    if (is_array($toplu) and count($toplu)>0) {
        
        if ($toplu_buton=="onayla") {
            foreach ($toplu as $id) {
                // Ä°Ã§erik yayÄ±nlanÄ±yor
                $baslik=bilgi_getir("icerik","baslik",$id);
                $tarih_saat=tarih_saat();

                $kategoriler=getir("icerik","kategori",$id);
                if ($kategoriler!="") {
                    $bul=array("(",")");
                    $degistir=array(null,null);
                    $kategori_dizisi=explode(",",str_replace($bul,$degistir,$kategoriler));
                }

                $guncelle=mysqli_prepare($baglan,"update icerik set durum=1, g_tarih_saat=? where id=?");
  	$id_int=(int)$id;
  	mysqli_stmt_bind_param($guncelle,"si",$tarih_saat,$id_int);
  	mysqli_stmt_execute($guncelle);
  	mysqli_stmt_close($guncelle);
                foreach($kategori_dizisi as $yaz) {
                    kategori_icerik_sayisi_guncelle($yaz);
                }	
            }
            
            $_SESSION["mesaj"]=array(_LANG('p_icerikler_yayinlandi'),"success");
            
            $oturum_ad_soyad=bilgi_getir("uyeler","ad_soyad",$oturum_id);
            $icerik_link=link_getir($id);
            if (function_exists('lpanel_uye_profil_link')) {
                $oturum_link=lpanel_uye_profil_url($oturum_id);
                bildirim_ekle(_LANG('p_bild_toplu_yayinladi_link'), array($oturum_link,$oturum_ad_soyad),$oturum_id,0);
            } else {
                bildirim_ekle(_LANG('p_bild_toplu_yayinladi_plain'), array($oturum_ad_soyad),$oturum_id,0);
            }
            yonlendir("icerik.php?tip=$tip"); 
            
        } else if ($toplu_buton=="sil") {
            
            
            foreach ($toplu as $id) {
                // Ä°Ã§erik siliniyor
                // Ã–ne Ã§Ä±karÄ±lan gÃ¶rsel varsa sil
                $mevcut_resim=thumbnail("full",$id);
                if ($mevcut_resim) {
                    sil($mevcut_resim);
                    foreach ($tip_resim_boyutlandir as $yaz) {
                        $thumbnail_url=thumbnail_url($yaz,$mevcut_resim);
                        sil($thumbnail_url);
                    }
                }
                // Varsa galeri gÃ¶rselleri siliniyor
                $resim_ara_sql=mysqli_query($baglan,"select * from galeri where icerik_id='$id'");
                $resim_sayisi=mysqli_num_rows($resim_ara_sql); 
                if ($resim_sayisi>0) {
                    while ($resim_dizisi=mysqli_fetch_array($resim_ara_sql)) {
                    $resim_sil_id=$resim_dizisi["id"];	
                    $image_url=$resim_dizisi["resim"];	
                    sil($image_url);
                    foreach ($tip_galeri_boyutlandir as $yaz) {
                        $thumbnail_url=thumbnail_url($yaz,$image_url);
                        sil($thumbnail_url);
                    } 					
                    $sil = mysqli_prepare($baglan,"delete from galeri where id=?");
  	$resim_sil_id_int=(int)$resim_sil_id;
  	mysqli_stmt_bind_param($sil,"i",$resim_sil_id_int);
  	mysqli_stmt_execute($sil);
  	mysqli_stmt_close($sil);
                    }
                }
                $silinen_baslik=bilgi_getir("icerik","baslik",$id);

                $eski_kategoriler=getir("icerik","kategori",$id);
                if ($eski_kategoriler!="") {
                    $bul=array("(",")");
                    $degistir=array(null,null);
                    $eski_kategori_dizisi=explode(",",str_replace($bul,$degistir,$eski_kategoriler));
                }	
                $id_int=(int)$id;
  	$icerik_sil=mysqli_prepare($baglan,"delete from icerik where id=?");
  	mysqli_stmt_bind_param($icerik_sil,"i",$id_int);
  	mysqli_stmt_execute($icerik_sil);
  	mysqli_stmt_close($icerik_sil);
  	$ekstra_bilgi_sil=mysqli_prepare($baglan,"delete from ekstra_bilgi where id=? and tip=?");
  	mysqli_stmt_bind_param($ekstra_bilgi_sil,"is",$id_int,$tip);
  	mysqli_stmt_execute($ekstra_bilgi_sil);
  	mysqli_stmt_close($ekstra_bilgi_sil);
            }
        
            $_SESSION["mesaj"]=array(_LANG('p_icerikler_kalici_silindi'),"success");

            if (function_exists('lpanel_uye_profil_link')) {
                $oturum_link=lpanel_uye_profil_url($oturum_id);
                bildirim_ekle(_LANG('p_bild_toplu_sildi_link'), array($oturum_link,$oturum_ad_soyad),$oturum_id,0);
            } else {
                bildirim_ekle(_LANG('p_bild_toplu_sildi_plain'), array($oturum_ad_soyad),$oturum_id,0);
            }
            foreach($eski_kategori_dizisi as $yaz) {
                kategori_icerik_sayisi_guncelle($yaz);
            }
            yonlendir("icerik.php?tip=$tip");	            

       } else if ($toplu_buton=="taslak") {
            
            
            foreach ($toplu as $id) {
                // Ä°Ã§erik taslaÄŸa dÃ¶nÃ¼ÅŸtÃ¼rÃ¼lÃ¼yor
                guncelle("icerik","durum",0,$id);
                
                $eski_kategoriler=getir("icerik","kategori",$id);
                if ($eski_kategoriler!="") {
                    $bul=array("(",")");
                    $degistir=array(null,null);
                    $eski_kategori_dizisi=explode(",",str_replace($bul,$degistir,$eski_kategoriler));
                }
                
                foreach($eski_kategori_dizisi as $yaz) {
                    kategori_icerik_sayisi_guncelle($yaz);
                }
                
            }
        
            $_SESSION["mesaj"]=array(_LANG('p_icerikler_taslaga_donusturuldu'),"success");

            if (function_exists('lpanel_uye_profil_link')) {
                $oturum_link=lpanel_uye_profil_url($oturum_id);
                bildirim_ekle(_LANG('p_bild_toplu_taslak_link'), array($oturum_link,$oturum_ad_soyad),$oturum_id,0);
            } else {
                bildirim_ekle(_LANG('p_bild_toplu_taslak_plain'), array($oturum_ad_soyad),$oturum_id,0);
            }

            yonlendir("icerik.php?tip=$tip");	            
       }
        
    }
}

?>
<!DOCTYPE HTML>
<html lang="<?php echo _DIL_HTML_LANG(); ?>" dir="<?php echo _DIL_HTML_YON(); ?>">
<head>
	<meta charset="UTF-8">
	<title><?php echo $tip_baslik_cogul; ?> - <?php echo $lpanel_baslik; ?></title>
	<?php include("inc/header-kod.php"); ?>
	<?php if ($datatable!=0) { ?>
	<script>
	$(document).ready(function() {
		$('#listele_table').DataTable({
			"responsive": true,
			"lengthMenu": [ 15, 25, 50, 100 ],
			"language": {
				"decimal":        "",
				"emptyTable":     "<?php echo _LANG('p_no_result'); ?>,
				"info":           "<?php echo _LANG('p_toplam_icerik'); ?>,
				"infoEmpty":      "<?php echo _LANG("p_gosterilecek_icerik_yok"); ?>",
				"infoFiltered":   "<?php echo _LANG('p_max_icerik'); ?>,
				"infoPostFix":    "",
				"thousands":      ",",
				"lengthMenu":     "<?php echo _LANG('p_gosterilecek_icerik'); ?>,
				"loadingRecords": "<?php echo _LANG('p_yukleniyor'); ?>,
				"processing":     "<?php echo _LANG('p_isleniyor'); ?>,
				"search":         "<?php echo _LANG("search"); ?>:",
				"zeroRecords":    "<?php echo _LANG('p_no_content_matching'); ?>,
				"paginate": {
					"first":      "<?php echo _LANG('p_ilk'); ?>,
					"last":       "<?php echo _LANG("p_son"); ?>",
					"next":       "<?php echo _LANG("p_sonraki"); ?>",
					"previous":   "<?php echo _LANG('p_onceki'); ?>			
			}
			}
		});
	});
	</script>	
	<?php } ?>
	<meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
<div id="wrapper" class="<?php echo $kapsa_container; ?>">
	<div class="row">
		<div id="sidebar-wrapper" class="<?php echo $sidebar_div; ?>">
		<?php include("inc/sidebar.php"); ?>
		</div>
		<div id="page-content-wrapper" class="<?php echo $sag_div; ?>">
		<?php include("inc/ust.php"); ?>			
			<div class="content">
			<?php if (!$islem) { ?>
			<h3 class="pull-left bolum-baslik text-uppercase"><?php echo $tip_baslik_cogul; ?></h3>
			<?php if ($tip_ekle_buton==1 or $tip_ekle_buton==""){ ?>
			<a href="icerik.php?tip=<?php echo $tip; ?>&islem=ekle" class="btn btn-primary"><span class="visible-sm visible-md visible-lg"><?php echo $tip_baslik; ?> ekle</span><span class="visible-xs"><?php echo _LANG('p_ekle'); ?></span></a>
			<?php } ?>
			<?php if ($tip_kategori) { ?>
			<a href="kategoriler.php?tip=<?php echo $kategori_tip; ?>&sinif=kategori" class="btn btn-default">Kategoriler</a>
			<?php } ?>
			
			<?php if (is_array($tip_ekstra_siniflar)) {foreach ($tip_ekstra_siniflar as $sinif) {
				$sinif_id=$sinif["id"];
				$sinif_baslik=$sinif["baslik"];
				$sinif_baslik_cogul=$sinif["baslik_cogul"];
			?>
			<a href="kategoriler.php?tip=<?php echo $kategori_tip; ?>&sinif=<?php echo $sinif_id; ?>" class="btn btn-default"><?php echo $sinif_baslik_cogul; ?></a>
			<?php 
			}}
			?>
			
			<?php if ($datatable==0 and $toplam>0) { ?>
			<form class="form-inline pull-right kayit_goster visible-md visible-lg" action="" method="post">
			  <div class="form-group">
				<input type="text" class="form-control input-sm nofocus" id="kayit" name="kacarli" placeholder="<?php echo _LANG('p_listeleme_adedi'); ?>>
			  </div>
			  <button type="submit" name="kacarli_buton" class="btn btn-default btn-sm" value="Onayla"><?php echo _LANG('p_onayla'); ?></button>
			</form>
			<?php } ?>
			<div class="clearfix mb20"></div>
			<?php if ($datatable==0) { ?>
			<div class="filtre">
				<div class="row">
					<div class="col-md-45">
						<ul>
							<li class="filtrele"><?php echo _LANG('p_filtrele'); ?></li>
							<?php
							$filtre_durum_link_taban="icerik.php?tip=$tip";
							if (isset($filtre_kategori)) {$filtre_durum_link_taban.="&kategori=$filtre_kategori";} 
							if (isset($filtre_ara)) {$filtre_durum_link_taban.="&ara=$filtre_ara";}
							foreach ($icerik_durumlari as $durum => $x) {
								$durum_baslik=$x["baslik"];
								if ($filtre_durum==$durum and isset($filtre_durum)) {$durum_active_yaz="class='active'";} else {$durum_active_yaz=null;}
								$sayi=mysqli_fetch_array(mysqli_query($baglan,"select COUNT(id) from icerik where tip='$tip' and durum='$durum'"))[0];
								if (!$sayi) {
									$sayi=0;
								}
								echo "<li $durum_active_yaz><a href='$filtre_durum_link_taban&durum=$durum'>$durum_baslik ($sayi)</a></li>";
							}
							if (isset($filtre_durum)) {echo "<li class='tumu'><a href='$filtre_durum_link_taban'>" . _LANG('p_filtreyi_kaldir') . "</a></li>";}
							?>
						</ul>
						<?php if (isset($filtre_kategori)) {
						$kategori_sinif=getir("kategoriler","sinif",$filtre_kategori);
						if ($kategori_sinif=="kategori" or !$kategori_sinif) {
							$kategori_sinif_baslik="Kategori";
						} else {
							$kategori_sinif_baslik=$icerik_tipleri[$tip]["ekstra_siniflar"][$kategori_sinif]["baslik"];
						}
						?>
						<ul>
							<li class="filtrele" style="margin-left:25px;"><?php echo $kategori_sinif_baslik; ?>:</li>
							<?php
								$kategori_adi=getir("kategoriler","baslik",$filtre_kategori);
								$kategori_url=kategori_link_getir($filtre_kategori);
								
								$filtre_kategori_taban_link="icerik.php?tip=$tip";
								if (isset($filtre_durum)) {$filtre_kategori_taban_link.="&durum=$filtre_durum";} 
								if (isset($filtre_ara)) {$filtre_kategori_taban_link.="&ara=$filtre_ara";} 
								echo "<li class='active'><a><i class='fa fa-tag'></i>$kategori_adi</a></li>";
								echo "<li class='tumu'><a href='$filtre_kategori_taban_link'>" . _LANG('p_filtreyi_kaldir') . "</a></li>";
							?>
						</ul>
						<?php } if (isset($filtre_ara)) {
						?>
						<ul>
							<li class="filtrele" style="margin-left:25px;"><?php echo _LANG('p_arama_sonuclari'); ?></li>
							<?php
								$filtre_ara_taban_link="icerik.php?tip=$tip";
								if (isset($filtre_durum)) {$filtre_ara_taban_link.="&durum=$filtre_durum";}
								if (isset($filtre_kategori)) {$filtre_ara_taban_link.="&kategori=$filtre_kategori";} 
								echo "<li class='active'><a><i class='fa fa-search'></i>$filtre_ara</a></li>";
								echo "<li class='tumu'><a href='$filtre_ara_taban_link'>" . _LANG('p_filtreyi_kaldir') . "</a></li>";
							?>
						</ul>
						<?php } ?>
					</div>
					<div class="col-md-15">
						<form action="" method="get" class="form-inline ara_form pull-right">
						<input type="hidden" name="tip" value="<?php echo $tip; ?>" />
						<?php if (isset($filtre_durum)) {echo "<input type='hidden' name='durum' value='$filtre_durum' />";} ?>
						<?php if (isset($filtre_kategori)) {echo "<input type='hidden' name='kategori' value='$filtre_kategori' />";} ?>
						<div class="input-group">
						  <input type="text" class="form-control input-sm nofocus" name="ara" placeholder="<?php echo _LANG("p_tum_iceriklerde_ara"); ?>" <?php if ($filtre_ara) {echo "value='$filtre_ara'";} ?> required>
						  <span class="input-group-btn">
							<button type="submit" class="btn btn-default" value="Ara"><i class="fa fa-search mr0"></i></button>
						  </span>
						</div>

						</form>					
					</div>
				</div>
			</div>
			<?php } ?>
			<?php if ($icerik_sayisi>0) { ?>
			<form action="" method="post">
			<div class="table-responsive">
			<table id="listele_table" class="table table-bordered table-hover" data-order='[[<?php echo $datatable_sirala; ?>,"<?php echo $datatable_sirala_tip; ?>"]]' data-page-length='15'>
				<thead>
					<tr>
						<th width="10">#</th>
						<th width="10"><?php echo _LANG('p_id'); ?></th>
						<?php if ($tip_tablo_gorsel) { ?><th><?php echo _LANG('p_gorsel'); ?></th><?php } ?>
						<?php if ($tip_baslik_yok!="1") { ?><th><?php echo $tip_baslik; ?></th><?php } ?>
						<?php if ($tip_kategori_detayi!="0" and ($tip_kategori==1 or $tip_kategori=="")) { ?><th><?php echo _LANG('p_kategori'); ?></th><?php } ?>
						<?php 
						if (is_array($tip_ekstra_detaylar)) { foreach ($tip_ekstra_detaylar as $yaz => $x) {
							@$detay_baslik=$x["baslik"];
							echo "<th>$detay_baslik</th>";
						} } 
						?>				
						<?php if ($tip_yazar_detayi==1 or $tip_yazar_detayi=="") { ?><th><?php echo $tip_yazar_baslik; ?></th><?php } ?>
						<?php if ($tip_tarih_saat_detayi==1 or $tip_tarih_saat_detayi=="") { ?><th width="180"><?php echo _LANG('p_tarih_saat'); ?></th><?php } ?>
						<?php if ($tip_durum_detayi==1 or $tip_durum_detayi=="") { ?><th><?php echo _LANG('p_durum'); ?></th><?php } ?>
						<th width="135"><?php echo _LANG('p_islemler'); ?></th>
					</tr>
				</thead>
				<tbody>
				<?php 
				while ($dizi=mysqli_fetch_array($sql)) {
					$id=$dizi["id"];
					$baslik=$dizi["baslik"];
					$url=$dizi["url"];
					$kategoriler=$dizi["kategori"];
					$kategori_dizisi=kategori_dizisi($kategoriler);
					$tarih_saat=$dizi["tarih_saat"];
					$durum=$dizi["durum"];
					$yazar=$dizi["yazar"];
					$gorsel=icerik_thumbnail_url($tip_tablo_gorsel,$id,$tip);
				?>
					<tr> 
						<th scope="row">
                        
                        <div class="checkbox">
                          <label><input type="checkbox" name="toplu[]" value="<?php echo $id; ?>"></label>
                        </div>
						
						</th>
						<td><?php echo $id; ?></td>
						<?php if ($tip_tablo_gorsel) { ?><td><?php if ($gorsel!="") { ?><a href="icerik.php?tip=<?php echo $tip; ?>&islem=guncelle&id=<?php echo $id; ?>"><img src="<?php echo $gorsel; ?>" alt="" class="img-responsive thumbnail" style="border-radius:0 !important;" /></a><?php } else {echo "<a href='icerik.php?tip=$tip&islem=guncelle&id=$id' style='text-decoration:none !important;'>" . _LANG('p_resim_yok') . "</a>";} ?></td><?php } ?>
						<?php if ($tip_baslik_yok!="1") { ?><td><span <?php if ($tip_tablo_gorsel) {echo "style='font-size:17px;font-weight:500;margin-top:3px;'";} ?>><a href="icerik.php?tip=<?php echo $tip; ?>&islem=guncelle&id=<?php echo $id; ?>"><?php echo e($baslik); ?></a></span> <?php if ($tip_gizli!=1) { ?><a href="<?php echo link_getir($id); ?>" class="tdn siyah ml10" target="_blank"><i class="fa fa-external-link"></i></a><?php } ?><?php if ($tip_gorsel==1 and ekstra_bilgi_getir("gorsel",$id,$tip)=="" and ayar_getir("panel_sablon")=="fullwidth") {echo "<a href='icerik.php?tip=$tip&islem=guncelle&id=$id' class='tdn'><span class='label label-danger ml10'><i class='fa fa-exclamation-triangle' aria-hidden='true'></i>" . _LANG('p_gorsel_bulunamadi') . "</span></a>";} } ?>
						<?php if ($tip_tablo_gorsel) { ?>
						<div class="clearfix" style="margin-bottom:3px;"></div>
						<?php $ozet=ozet_olustur(bilgi_getir("icerik","icerik",$id),35); if ($ozet) {echo "<p style='margin-bottom:0;padding-bottom:0;font-size:13px;line-height:19px;max-height:57px;overflow:hidden;'>$ozet</p>";} ?>
						<?php } ?>
						</td>
						<?php if ($tip_kategori_detayi!="0" and ($tip_kategori==1 or $tip_kategori=="")) { ?><td class="scroll x" style="max-width:200px;">
						<?php 
						if (is_array($kategori_dizisi)) {
							$filtre_kategori_link_taban="icerik.php?tip=$tip";
							if (isset($filtre_durum)) {$filtre_kategori_link_taban.="&durum=$filtre_durum";} 
							if (isset($filtre_ara)) {$filtre_kategori_link_taban.="&ara=$filtre_ara";} 
							foreach ($kategori_dizisi as $yaz) {
								$kategori_adi=kategori_adi_getir($yaz);
								$kategori_url=kategori_link_getir($yaz);
								$kategori_panel_url="$filtre_kategori_link_taban&kategori=$yaz";
								echo "<div class='label label-default' style='margin-right:1px;'><a href='$kategori_panel_url' style='color:#fff !important;text-decoration:none !important;'><i class='fa fa-tag'></i>$kategori_adi</a></div>";
							}							
						}
						?>
						</td><?php } ?>
						<?php if (is_array($tip_ekstra_detaylar)) {foreach ($tip_ekstra_detaylar as $yaz => $x) {
							@$detay_name=$x["id"];
							@$detay_tip=$x["tip"];
							if ($detay_tip=="link") {
								$detay_url=$x["url"];
								$detay_title=$x["title"];
								$detay_img=$x["img"];
								if ($detay_img) {
									echo "<td><a href='$detay_url&icerik_id=$id'><img src='$detay_img' style=''/></a></td>";
								} else {
									echo "<td><a href='$detay_url&icerik_id=$id'>$detay_title</a></td>";
								}
							} else {
							$ekrana_yaz=ekstra_bilgi_getir($detay_name,$id,$tip);
							echo "<td>$ekrana_yaz</td>";
							}
						} } ?>
						<?php if ($tip_yazar_detayi==1 or $tip_yazar_detayi=="") { ?><td>
						<?php if (function_exists('lpanel_uye_profil_link')) {echo lpanel_uye_profil_link($yazar);} else {echo bilgi_getir("uyeler","ad_soyad",$yazar);} ?>
						</td><?php } ?>
						<?php if ($tip_tarih_saat_detayi==1 or $tip_tarih_saat_detayi=="") { ?>
						<td><?php echo tarih_getir($id).", ".saat_getir($id); } ?></td>
						<?php if ($tip_durum_detayi==1 or $tip_durum_detayi=="") { ?><td><?php 
							$durum_dizisi=$icerik_durumlari[$durum];
							$durum_baslik=$durum_dizisi["baslik"];
							$durum_renk=$durum_dizisi["renk"];
							echo "<div class='label label-".$durum_renk."'>$durum_baslik</div>";
						?></td><?php } ?>
						<td>
							
						<?php if ($_SESSION["oturum_yetki"]!="yazar" or ($_SESSION["oturum_yetki"]=="yazar" and getir("icerik","yazar",$id)==$_SESSION["yonetici_id"])) { ?>					
							<!-- Split button -->
							<div class="btn-group" style="width:150px;">
							  <a href="icerik.php?tip=<?php echo $tip; ?>&islem=guncelle&id=<?php echo $id; ?>" class="btn btn-xs btn-primary"><i class="fa fa-pencil" aria-hidden="true"></i><?php echo _LANG('p_guncelle'); ?></a>
							  <button type="button" class="btn btn-primary btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								<span class="caret"></span>
								<span class="sr-only"><?php echo _LANG('p_toggle_dropdown'); ?></span>
							  </button>
							  <ul class="dropdown-menu dropdown-menu-right">
								<?php if ($tip_gizli!=1 and $durum!=1) { ?><li><a href="icerik.php?tip=<?php echo $tip; ?>&islem=yayinla&id=<?php echo $id; ?>" style="position:relative;z-index:50000;"><i class="fa fa-check-circle-o fa-fw" aria-hidden="true"></i><?php echo _LANG('p_yayinla'); ?></a></li><?php } ?>	
								<?php if ($tip_gizli!=1) { ?><li><a href="<?php echo link_getir($id); ?>" target="_blank" style="position:relative;z-index:50000;"><i class="fa fa-external-link fa-fw" aria-hidden="true"></i><?php echo _LANG('p_goruntule'); ?></a></li><?php } ?>	
								<li><a href="icerik.php?tip=<?php echo $tip; ?>&islem=sil&id=<?php echo $id; ?>" onclick="return confirm('<?php echo _LANG("p_emi_misiniz"); ?>')" style="position:relative;z-index:50000;"><i class="fa fa-trash fa-fw" aria-hidden="true"></i><?php echo _LANG('p_sil'); ?></a></li>
							  </ul>
							</div>		
						<?php } ?>
							
						</td>
					</tr>
				<?php } ?>
				</tbody>
				<tfoot style="background:#F9F9F9;">
				    <tr>
				        <td colspan="10">
                            <strong style="margin-right:10px;"><?php echo _LANG('p_toplu_islemler'); ?></strong>
                            <button type="submit" name="toplu_buton" class="btn btn-sm btn-primary" value="onayla" style="margin-right:5px;" onclick="return confirm('<?php echo _LANG("p_tum_icerikleri_onayla_onay"); ?>');"><?php echo _LANG('p_onayla'); ?></button>
                            <button type="submit" name="toplu_buton" class="btn btn-sm btn-warning" value="taslak" style="margin-right:5px;" onclick="return confirm('<?php echo _LANG("p_tum_icerikleri_taslaga_onay"); ?>');"><?php echo _LANG('p_taslaga_donus'); ?></button>
                            <button type="submit" name="toplu_buton" class="btn btn-sm btn-danger" value="sil" style="margin-right:5px;" onclick="return confirm('<?php echo _LANG("p_tum_icerikleri_sil_onay"); ?>');"><?php echo _LANG('p_sil'); ?></button>
                        </td>
				    </tr>
				</tfoot>
			</table>
			</div>
			</form>
			<?php
			if (!$filtre_sql) {
				$sayfalama_url_sabit="icerik.php?tip=$tip&p=";
			} else {
				$sayfalama_url_sabit="icerik.php?tip=$tip";
				if (isset($filtre_durum)) {$sayfalama_url_sabit.="&durum=$filtre_durum";} 
				if (isset($filtre_kategori)) {$sayfalama_url_sabit.="&kategori=$filtre_kategori";} 
				if (isset($filtre_ara)) {$sayfalama_url_sabit.="&ara=$filtre_ara";} 
			}
			panel_sayfalama($kac_sayfa,$page,1,$sayfalama_url_sabit."&p="); 
			} else {
				if (!$filtre_sql) {
				echo "<div class='alert alert-danger'>" . _LANG('p_hic_eklenmemis', array($tip_baslik)) . "</div>";
				} else {
					echo "<div class='alert alert-info'>>" . _LANG('p_kriterlere_uygun_yok') . "</div>";
				}
			} ?>			
			<?php } else if ($islem=="ekle") { ?>
			<h3 class="pull-left bolum-baslik text-uppercase"><?php echo $tip_baslik; ?> <?php echo _LANG('p_ekle_buyuk'); ?></h3>
			<a href="icerik.php?tip=<?php echo $tip; ?>" class="btn btn-default"><?php echo $tip_baslik_cogul; ?></a>
			<div class="clearfix mb20"></div>
			<?php jaayquick(@$panel_icerik_ust_kod); ?>
			<div class="row">
			<form method="post" action="" enctype="multipart/form-data">
				<div class="<?php echo $icerik_sol_div; ?>">
					<?php jaayquick(@$panel_icerik_sol_kod); ?>
							<?php if ($tip_baslik_yok!="1") {form_elemani("text","baslik",$tip_baslik,@$baslik,null,null,1); } ?>
							<?php if ($tip_url==1 or $tip_url=="") { ?>
							<label for="url"><?php echo _LANG('p_kalici_baglanti'); ?></label>
							<div class="row">
								<div class="<?php echo $div_class; ?>">
									<div class="input-group" style="margin-bottom:15px;">
										<div class="input-group-addon"><?php echo site_url(); ?>/</div>
										<input type="text" class="form-control" id="url" name="url" value="<?php echo @$url; ?>">
										<div class="input-group-addon">-{ID}/</div>
									</div>								
								</div>
								<div class="<?php echo $bilgi_div; ?>">
									<p class="help-block"><i class="fa fa-bell-o"></i><?php echo _LANG('p_url_oto_olusur'); ?></p>
								</div>
							</div>
							<?php } ?>
							<?php if ($tip_custom_url==1) {?>							
							<label for="custom_url"><?php echo _LANG('p_ozel_url'); ?></label>
							<div class="row">
								<div class="<?php echo $div_class; ?>">
									<div class="input-group" style="margin-bottom:15px;">
										<div class="input-group-addon"><?php echo site_url(); ?>/</div>
										<input type="text" class="form-control" id="custom_url" name="custom_url">
									</div>
								</div>
								<div class="<?php echo $bilgi_div; ?>">
									<p class="help-block"><i class="fa fa-bell-o"></i><?php echo _LANG('p_htaccess_url'); ?></p>
								</div>
							</div>
							<?php } ?>
							<?php if ($tip_icerik==1 or $tip_icerik=="") {textarea_olustur("ckeditor","icerik",_LANG("p_icerik"));} ?>
					
				
						<?php if ($tip_galeri==1 or $tip_galeri=="") { ?>
							<div class="icerik_galeri"><input type="file" name="jaayquick_galeri" id="jaayquick_galeri" accept="image/*" multiple="multiple"></div>
						<?php } else {
							if ($tip_icerik!=0) {
								echo "<div class='clearfix mb15'></div>";
							}
						} ?>	
					
				<?php if (is_array($tip_ekstra_bilgiler)) { if (count($tip_ekstra_bilgiler)>0) { 
				echo "<ul class='nav nav-tabs' role='tablist'>";
				$say=1;
				$tab_sayisi=0;
				foreach ($tip_ekstra_bilgiler as $yaz => $x) {
					$eb_type=$x["tip"];
					@$eb_name=$x["id"];
					@$eb_text=$x["baslik"];
					if ($say==1) {
						$active="class='active'";
					} else {$active="";}
					if ($eb_type=="tab_ac") {
						echo "<li role='presentation' $active><a href='#$eb_name' aria-controls='$eb_name' role='tab' data-toggle='tab'>$eb_text</a></li>";
						$tab_sayisi=$tab_sayisi+1;
					}
					$say++; 
				} 
				echo "</ul>";
				?>
				<div class="panel panel-default" style="border-top:0;border-radius:0;box-shadow:none;">
				<?php if ($tab_sayisi==0) {if ($tip_ekstra_bilgiler_baslik!="0") {echo "<div class='panel-heading'>Ekstra Bilgiler</div>"; }} ?>
				<div class="panel-body">
				<?php
				echo "<div class='tab-content'>";
				$tab_say=1;
				foreach ($tip_ekstra_bilgiler as $yaz => $x) {
							$eb_type=$x["tip"];
							@$eb_name=$x["id"];
							@$eb_text=$x["baslik"];
							@$eb_options=$x["secenekler"];
							@$eb_required=$x["gerekli"];
							@$eb_value=$x["deger"];
							@$eb_rows=$x["satir"];
							@$eb_standart=$x["standart"];
							@$eb_aciklama=$x["aciklama"];
							@$eb_class=$x["sinif"];
							@$eb_grup=$x["grup"];
							if ($eb_grup) {
								if ($eb_grup==2) {
									$grup_ac_kod="<div class='col-md-30'>";
								} else if ($eb_grup==3) {
									$grup_ac_kod="<div class='col-md-20'>";
								} else if ($eb_grup==4) {
									$grup_ac_kod="<div class='col-md-15'>";
								} else if ($eb_grup==5) {
									$grup_ac_kod="<div class='col-md-12'>";
								} else if ($eb_grup==6) {
									$grup_ac_kod="<div class='col-md-10'>";
								}
								$grup_kapat_kod="</div>";			
							} else {
								$grup_ac_kod=null;
								$grup_kapat_kod=null;
							}
							@$ayar_kosul=$x["kosul"];
							if ($ayar_kosul) {
								$kosul_parcala=explode(":",$ayar_kosul);
								$diger_ayar=$kosul_parcala[0];
								$kosul_kalan=$kosul_parcala[1];
								$kosul_kosul_parcala=explode("(",$kosul_kalan);
								$kosul_sart=$kosul_kosul_parcala[0];
								$kosul_deger=rtrim($kosul_kosul_parcala[1],")");
								echo "
								<script type='text/javascript'>
									$(function(){
										var kosul='$kosul_sart';
										var deger='$kosul_deger';
										var diger_ayar_value=$('#$diger_ayar').val();
										if (kosul=='==') {
											if (diger_ayar_value!=deger) {
												$('#form_$eb_name').hide();
											}
										} else if (kosul=='!=') {
											if (diger_ayar_value==deger) {
												$('#form_$eb_name').hide();
											}
										}
										$('body').on('change','#$diger_ayar',function(){
											var diger_ayar_value=$('#$diger_ayar').val();
											if (kosul=='==') {
												if (diger_ayar_value==deger) {
													$('#form_$eb_name').show(100);
												} else {
													$('#form_$eb_name').hide(100);
												}
											} else if (kosul=='!=') {
												if (diger_ayar_value!=deger) {
													$('#form_$eb_name').show(100);
												} else {
													$('#form_$eb_name').hide(100);
												}
											}
										});
									});
								</script>
								";
							}
							echo $grup_ac_kod;	
							if ($eb_type=="text" or $eb_type=="email" or $eb_type=="password" or $eb_type=="date" or $eb_type=="date_time") {
								form_elemani($eb_type,$eb_name,$eb_text,$eb_standart,null,null,$eb_required,$eb_aciklama,$eb_grup);
							} else if ($eb_type=="textarea") {
								if (!$eb_class) {$eb_class=null;}
								textarea_olustur($eb_class,$eb_name,$eb_text,$eb_standart,$eb_rows,$eb_required,$eb_aciklama);
							} else if ($eb_type=="select") {
								select_olustur($eb_name,$eb_text,$eb_options,$eb_standart,$eb_aciklama);
							} else if ($eb_type=="checkbox") {
								checkbox_olustur($eb_name,$eb_text,$eb_standart,$eb_aciklama);
							} else if ($eb_type=="multiple_checkbox") {
								multiple_checkbox_olustur($eb_name,$eb_text,$eb_options,$eb_standart,$eb_aciklama);
							} else if ($eb_type=="aciklama") {
								echo aciklama_olustur($eb_aciklama);
							} else if ($eb_type=="baslik") {
								echo baslik_olustur($eb_text,null,null,$eb_aciklama);
							} else if ($eb_type=="color") {
								colorpicker_olustur($eb_name,$eb_text,$eb_standart,null,null,$eb_required,$eb_aciklama);
							} else if ($eb_type=="upload") {
								upload_olustur($eb_name,$eb_text,$eb_standart,$eb_aciklama);
							} else if ($eb_type=="liste") {
								liste_link($eb_name,$eb_text,$eb_aciklama);
							} else if ($eb_type=="grup_ac") {
								grup_ac($eb_name,$eb_text,$eb_aciklama);
							} else if ($eb_type=="grup_kapat") {
								grup_kapat();
							} else if ($eb_type=="location") {
								location($eb_name,$eb_text,null,$eb_aciklama);
							} else if ($eb_type=="tab_ac") {
								if ($tab_say==1) {$active="active";} else {$active="";}
								tab_ac($eb_name,$eb_text,$active);
								$tab_say++;
							} else if ($eb_type=="tab_kapat") {
								tab_kapat();
							}
							echo $grup_kapat_kod;
						} 
						echo "</div>";
						?>					
					</div>
				</div>
			<?php }} ?>
				<?php jaayquick(@$panel_icerik_sol_alt_kod); ?>
				</div>
				<div class="<?php echo $icerik_sag_div; ?>">
					<?php jaayquick(@$panel_icerik_sag_kod); ?>

					<?php if ($tip_kategori==1 or $tip_kategori=="") {
					$sinif_name="kategori";
					$toplam_kategori_sayisi=mysqli_num_rows(mysqli_query($baglan,"select id from kategoriler where tip='$tip' and sinif='$sinif_name'"));
					if ($tip!="ilan") {
						$kategori_ekle_stil=1;
					} else {
						$kategori_ekle_stil=2;
					}
					if ($kategori_ekle_stil==1) {
					?>
					<div class="panel panel-default">
						<div class="panel-heading">
						Kategori
						<a href="#" id="kategori_ekle_link" class="pull-right" style="font-weight:500 !important;"><?php echo _LANG('p_yeni_ekle'); ?></a>
						</div>
						<div class="panel-body">
							<div class="form-group">
								<div id="kategori_ekle_form" style="margin-bottom:5px;">
									<div class="input-group">
									  <input id="eklenecek_kategori" type="text" class="form-control" placeholder="Kategori" style="width:50%;">
									  <select id="eklenecek_kategori_ust" class="form-control" style="width:50%;background:#eee">
										<option value=""><?php echo _LANG('p_ust_kat_yok'); ?></option>
										<?php 
										$kategori_ekle_ust_kategori_sql=mysqli_query($baglan,"select * from kategoriler where tip='$tip' and sinif='kategori'");
										while ($kategori_ekle_ust_kategori_dizi=mysqli_fetch_array($kategori_ekle_ust_kategori_sql)) {
											$kategori_ekle_ust_kategori_id=$kategori_ekle_ust_kategori_dizi["id"];
											$kategori_ekle_ust_kategori_baslik=$kategori_ekle_ust_kategori_dizi["baslik"];
											echo "<option value='$kategori_ekle_ust_kategori_id'>$kategori_ekle_ust_kategori_baslik</option>";
										}
										?>
									  </select>
									  <span class="input-group-btn">
										<button id="kategori_ekle_buton" class="btn btn-default" type="button"><i class="fa fa-plus mr0"></i></button>
									  </span>
									</div>
									<div id="kategori_ajax_cevap"></div>
								</div>
								<div id="kategori_getir" class="multiple_checkbox_group_kategori"></div>
							</div>
						</div>
					</div>
					<script type="text/javascript">
					$(function(){
						// HÄ±zlÄ± kategori ekleme
						$("#kategori_ekle_form").hide();
						$("#kategori_ekle_link").click(function(){
						var text = $(this).text();
						$(this).text( text == "Yeni ekle" ? "iptal" : "Yeni ekle");
							$("#kategori_ekle_form").toggle();
						});
						
						$("#kategori_getir").load("inc/ajax/kategori_getir.php?tip=<?php echo $tip; ?>&sinif=kategori");
						
						$("#kategori_ekle_buton").click(function(){
							var eklenecek_kategori=$("#eklenecek_kategori").val();
							var eklenecek_kategori_ust=$("#eklenecek_kategori_ust").val();
							var eklenecek_tip="<?php echo $tip; ?>";
							var eklenecek_sinif="kategori";
							$.ajax({
								type:"post",
								url:"inc/ajax.php?islem=kategori_ekle",
								data:{'eklenecek_kategori':eklenecek_kategori,'eklenecek_kategori_ust':eklenecek_kategori_ust,'tip':eklenecek_tip,'sinif':eklenecek_sinif},
								success:function(cevap){
									$("#kategori_ajax_cevap").html("<div class='alert alert-success alert-xs'>"+eklenecek_kategori+" <?php echo _LANG("p_kategori_eklendi_basarili"); ?></div>").fadeIn().delay(3000).fadeOut(500);
									$("#eklenecek_kategori_ust").append("<option value='"+cevap+"'>"+eklenecek_kategori+"</option>");
									$("#kategori_getir").load("inc/ajax/kategori_getir.php?tip=<?php echo $tip; ?>&sinif=kategori");
									$("#eklenecek_kategori").val("");
								}
							});
						});
					});
					</script>
					<?php } else { // Kategori ekle stil 2 ?>
					<!-- Kategori Ekle Stil 2 -->
					<div class="panel panel-default">
						<div class="panel-heading"><?php echo _LANG('p_kategori'); ?></div>
						<div class="panel-body">
							<div class="form-group">
								<div id="<?php echo $sinif_name; ?>_ekle_stil_2"></div>
							</div>
							<a href="#" id="<?php echo $sinif_name; ?>-sifirla" class="btn btn-danger btn-xs" style="display:none;"><?php echo _LANG("p_secimi_sifirla"); ?></a>
						</div>
					</div>					
					<script type="text/javascript">
					$(function(){
						$("#<?php echo $sinif_name; ?>_ekle_stil_2").load("inc/ajax/kategori_ekle_stil_2.php?tip=<?php echo $tip; ?>&sinif=<?php echo $sinif_name; ?>");
						var kategori_deger=$("#<?php echo $sinif_name; ?>").val();
						$("#<?php echo $sinif_name; ?>-sifirla").click(function(){
							$("#<?php echo $sinif_name; ?>-alt").html("");
							$("#<?php echo $sinif_name; ?>_tip_2").removeAttr("disabled");
							$("#<?php echo $sinif_name; ?>_tip_2").val("");
							$("#<?php echo $sinif_name; ?>-sifirla").hide();
						});

						$('form').bind('submit', function () {
							$(this).find(':input').prop('disabled', false);
						});
					});
					</script>
					<?php } } ?>

					<?php if ($tip_ekstra_siniflar) { 
					foreach ($tip_ekstra_siniflar as $sinif) {
						$sinif_id=$sinif["id"];
						$sinif_baslik=$sinif["baslik"];
					?>
					<div class="panel panel-default">
						<div class="panel-heading">
						<?php echo $sinif_baslik; ?>
						<a href="#" id="<?php echo $sinif_id; ?>_ekle_link" class="pull-right" style="font-weight:500 !important;">Yeni ekle</a>
						</div>
						<div class="panel-body">							
							<div class="form-group">
								<div id="<?php echo $sinif_id; ?>_ekle_form" style="margin-bottom:5px;">
									<div class="input-group">
									  <input id="<?php echo $sinif_id; ?>_eklenecek_kategori" type="text" class="form-control" placeholder="<?php echo $sinif_baslik; ?>" style="width:50%;">
									  <select id="<?php echo $sinif_id; ?>_eklenecek_kategori_ust" class="form-control" style="width:50%;background:#eee">
										<option value=""><?php echo _LANG('p_ust_kat_yok'); ?></option>
										<?php 
										$kategori_ekle_ust_kategori_sql=mysqli_query($baglan,"select * from kategoriler where tip='$tip' and sinif='$sinif_id'");
										while ($kategori_ekle_ust_kategori_dizi=mysqli_fetch_array($kategori_ekle_ust_kategori_sql)) {
											$kategori_ekle_ust_kategori_id=$kategori_ekle_ust_kategori_dizi["id"];
											$kategori_ekle_ust_kategori_baslik=$kategori_ekle_ust_kategori_dizi["baslik"];
											echo "<option value='$kategori_ekle_ust_kategori_id'>$kategori_ekle_ust_kategori_baslik</option>";
										}
										?>
									  </select>									  
									  <span class="input-group-btn">
										<button id="<?php echo $sinif_id; ?>_ekle_buton" class="btn btn-default" type="button"><i class="fa fa-plus mr0"></i></button>
									  </span>
									</div>
									<div id="<?php echo $sinif_id; ?>_ajax_cevap"></div>
								</div>
								<div id="<?php echo $sinif_id; ?>_getir" class="multiple_checkbox_group_kategori"></div>				
							</div>
						</div>
					</div>
					<script type="text/javascript">
					$(function(){
						// HÄ±zlÄ± kategori ekleme
						$("#<?php echo $sinif_id; ?>_ekle_form").hide();
						$("#<?php echo $sinif_id; ?>_ekle_link").click(function(){
						var text = $(this).text();
						$(this).text( text == "Yeni ekle" ? "iptal" : "Yeni ekle");
							$("#<?php echo $sinif_id; ?>_ekle_form").toggle();
						});
						
						$("#<?php echo $sinif_id; ?>_getir").load("inc/ajax/kategori_getir.php?tip=<?php echo $tip; ?>&sinif=<?php echo $sinif_id; ?>");
						
						$("#<?php echo $sinif_id; ?>_ekle_buton").click(function(){
							var eklenecek_kategori=$("#<?php echo $sinif_id; ?>_eklenecek_kategori").val();
							var eklenecek_kategori_ust=$("#<?php echo $sinif_id; ?>_eklenecek_kategori_ust").val();
							var eklenecek_tip="<?php echo $tip; ?>";
							var eklenecek_sinif="<?php echo $sinif_id; ?>";
							$.ajax({
								type:"post",
								url:"inc/ajax.php?islem=kategori_ekle",
								data:{'eklenecek_kategori':eklenecek_kategori,'eklenecek_kategori_ust':eklenecek_kategori_ust,'tip':eklenecek_tip,'sinif':eklenecek_sinif},
								success:function(cevap){
									$("#<?php echo $sinif_id; ?>_ajax_cevap").html("<div class='alert alert-success alert-xs'>"+eklenecek_kategori+" <?php echo _LANG("p_kategori_eklendi_basarili"); ?></div>").fadeIn().delay(3000).fadeOut(500);
									$("#<?php echo $sinif_id; ?>_eklenecek_kategori_ust").append("<option value='"+cevap+"'>"+eklenecek_kategori+"</option>");
									$("#<?php echo $sinif_id; ?>_getir").load("inc/ajax/kategori_getir.php?tip=<?php echo $tip; ?>&sinif=<?php echo $sinif_id; ?>");
									$("#<?php echo $sinif_id; ?>_eklenecek_kategori").val("");
								}
							});
						});
					});
					</script>
					<?php } } ?>
							
					<div class="panel panel-default">
						<div class="panel-body">
							<?php if ($tip_yazar==1 or $tip_yazar=="") { ?>
								<div class="form-group">
									<label for="yazar"><?php echo $tip_yazar_baslik; ?></label>
									<select name="yazar" class="form-control" id="yazar">
									<?php 
									$sql=mysqli_query($baglan,"select * from uyeler order by ad_soyad asc");
									while ($dizi=mysqli_fetch_array($sql)) {
										$uye_id=$dizi["id"];
										$uye_ad_soyad=$dizi["ad_soyad"];
									?>
									<option value="<?php echo $uye_id; ?>" <?php if (@$yazar_hazir) { if ($uye_id==$yazar_hazir) {echo "selected='selected'";}} else { if ($uye_id==$oturum_id) {echo "selected='selected'";} }?>><?php echo $uye_ad_soyad; ?></option>
									<?php } ?>
									</select>
								</div>
							<?php } ?>
							
							<?php if ($tip_durum==1 or $tip_durum=="") {
							select_olustur("durum","Durum",$icerik_durumlari);
							}
							?>
							<?php if ($tip_etiket==1 or $tip_etiket=="") { textarea_olustur("etiket","etiketler","Etiketler",null,3); } ?>
							<?php if ($tip_gizli!=1) {textarea_olustur("seo_title","seo_title","SEO Title",null,2);} ?>
							<?php if ($tip_gizli!=1) {textarea_olustur("seo_aciklama","seo_aciklama",_LANG("p_seo_aciklama"),null,3);} ?>
							<?php jaayquick(@$panel_icerik_sag_buton_ustu_kod); ?>
							<?php if ($tip_gizli!=1) { ?>
							<div class="row">
								<div class="col-md-30 btn_ikili_sol">
									<?php form_buton("buton",_LANG('p_ekle'),"default btn-block btn-lg");	 ?>
								</div>
								<div class="col-md-30 btn_ikili_sag">
									<?php form_buton("buton_yayinla",_LANG('p_yayinla'),"primary btn-block btn-lg btn-icerik-ozel"); ?>
								</div>
							</div>
							<?php } else {
							form_buton("buton","$tip_baslik_isaret ekle","primary btn-block btn-lg btn-icerik-ozel");												
							} ?>							
						</div>
					</div>

					<?php if ($tip_gorsel==1 or $tip_gorsel=="") { ?>
					<div class="panel panel-default">
						<div class="panel-heading"><?php echo _LANG('p_gorsel'); ?></div>
						<div class="panel-body">
							<input type="file" name="resim_jaayquick" id="one_cikarilan_gorsel">
						</div>
					</div>
					<?php } ?>		
				
				<?php jaayquick(@$panel_icerik_sag_alt_kod); ?>
				</div>
				<?php form_elemani("hidden","aktif_tab",_LANG('p_aktif_tab')); ?>
				</form>	
			</div>
			<?php } else if ($islem=="guncelle") { ?>
			<h3 class="pull-left bolum-baslik text-uppercase"><?php echo $tip_baslik; ?> <?php echo _LANG('p_guncelle_buyuk'); ?> <?php if ($tip_gizli!=1) { ?><a href="<?php echo link_getir($id); ?>" class="tdn siyah ml10" target="_blank"><i class="fa fa-external-link"></i></a><?php } ?></h3>
			<?php if ($tip_ekle_buton==1 or $tip_ekle_buton==""){ ?><a href="icerik.php?tip=<?php echo $tip; ?>&islem=ekle" class="btn btn-primary"><?php echo $tip_baslik; ?> ekle</a><?php } ?>
			<?php if ($tip_ekle_buton==1 or $tip_ekle_buton==""){ ?><a href="icerik.php?tip=<?php echo $tip; ?>" class="btn btn-default"><?php echo $tip_baslik_cogul; ?> <?php echo _LANG('p_menu_anasayfa'); ?></a><?php } ?>
			<?php 
			$durum_dizisi=$icerik_durumlari[getir("icerik","durum")];
			$durum_baslik=$durum_dizisi["baslik"];
			$durum_renk=$durum_dizisi["renk"]; 
			?>
			<p class="pull-right" style="color:#aaa;font-size:12px;"><?php echo "<span class='label label-$durum_renk mr15'>$durum_baslik</span>"; ?> <?php echo _LANG("p_olusturulma"); ?> <span style="border-bottom:1px dotted #d0d0d0;color:#999;"><?php echo tarih_saat_bicimlendir(getir("icerik","tarih_saat",$id),1); ?></span><?php $g_tarih_saat=getir("icerik","g_tarih_saat",$id); if ($g_tarih_saat) {$g_tarih_saat_bicim=tarih_saat_bicimlendir($g_tarih_saat,1);echo "" . _LANG('p_son_guncelleme') . "<span style='border-bottom:1px dotted #d0d0d0;color:#999;'>$g_tarih_saat</span>";} ?></p>
			
			
			<div class="clearfix mb20"></div>
			<?php jaayquick(@$panel_icerik_ust_kod); ?>
			<div class="row">
			<form method="post" action="" enctype="multipart/form-data">
				<input type="submit" name="buton" style="display:none;" value="default">
				<div class="<?php echo $icerik_sol_div; ?>">
					<?php jaayquick(@$panel_icerik_sol_kod); ?>
					
							<?php if ($tip_baslik_yok!="1") {form_elemani("text","baslik",$tip_baslik,bilgi_getir("icerik","baslik",$id),null,null,1);} ?>
							<?php if ($tip_url==1 or $tip_url=="") { ?>
							<label for="url"><?php echo _LANG('p_kalici_baglanti'); ?> </label>
							<div class="row">
								<div class="<?php echo $div_class; ?>">
									<div class="input-group" style="margin-bottom:15px;">
										<div class="input-group-addon"><?php echo site_url(); ?>/</div>
										<input type="text" class="form-control" id="url" name="url" value="<?php echo bilgi_getir("icerik","url",$id); ?>">
										<div class="input-group-addon">-<?php echo $id; ?>/</div>
									</div>								
								</div>
								<div class="<?php echo $bilgi_div; ?>">
									<p class="help-block"><i class="fa fa-bell-o"></i><?php echo _LANG('p_url_oto_olusur'); ?></p>
								</div>
							</div>
							<?php } ?>
							<?php if ($tip_custom_url==1) { ?>
							<label for="custom_url"><?php echo _LANG('p_ozel_url'); ?></label>
							<div class="row">
								<div class="<?php echo $div_class; ?>">
									<div class="input-group" style="margin-bottom:15px;">
										<div class="input-group-addon"><?php echo site_url(); ?>/</div>
								<input type="text" class="form-control" id="custom_url" name="custom_url" value="<?php echo ekstra_bilgi_getir("custom_url",$id,$tip); ?>">
									</div>
								</div>
								<div class="<?php echo $bilgi_div; ?>">
									<p class="help-block"><i class="fa fa-bell-o"></i><?php echo _LANG('p_htaccess_url'); ?></p>
								</div>
							</div>							
							<?php } ?>
							<?php if ($tip_icerik==1 or $tip_icerik=="") {textarea_olustur("ckeditor","icerik",_LANG("p_icerik"),bilgi_getir("icerik","icerik",$id));} ?>
					
					<?php if ($tip_galeri==1 or $tip_galeri=="") { ?>
					<div class="icerik_galeri">
					
						<?php 
						$resim_ara_sql=mysqli_query($baglan,"select * from galeri where icerik_id='$id' order by sira asc");
						$resim_sayisi=mysqli_num_rows($resim_ara_sql); if ($resim_sayisi>0) { ?>
						<table class="table">
						<thead>
						<tr>
						<th><?php echo _LANG('p_resim'); ?></th>
						<th width="200"><?php echo _LANG('p_baslik'); ?></th>
						<th width="200"><?php echo _LANG('p_url'); ?></th>
						<th><?php echo _LANG('p_icerik'); ?></th>
						<th width="75"><?php echo _LANG('p_sira'); ?></th>
						<th width="50"><?php echo _LANG('p_sil'); ?></th>
						</tr>
						</thead>
						<tbody>
						<?php while ($resim_dizisi=mysqli_fetch_array($resim_ara_sql)) {
						$resim_id=$resim_dizisi["id"];
						$resim_resim=$resim_dizisi["resim"];
						$resim_baslik=$resim_dizisi["baslik"];
						$resim_url=$resim_dizisi["url"];
						$resim_icerik=$resim_dizisi["icerik"];
						$resim_kucuk_resim=thumbnail_url($tip_galeri_kucuk_resim,$resim_resim);
						$resim_sira=$resim_dizisi["sira"];
						?>
						<tr>
						<td width="90"><a href="<?php echo $resim_resim; ?>" target="_blank"><img src="<?php echo $resim_kucuk_resim; ?>" class='img-responsive yazi_gorsel' /></a></td>
						<td><input type="text" name="galeri_baslik[<?php echo $resim_id; ?>]" class="form-control" value="<?php echo $resim_baslik; ?>" /></td>
						<td><input type="text" name="galeri_url[<?php echo $resim_id; ?>]" class="form-control" value="<?php echo $resim_url; ?>" /></td>
						<td><textarea type="text" name="galeri_icerik[<?php echo $resim_id; ?>]" class="form-control"><?php echo $resim_icerik; ?></textarea></td>
						<td><input type="text" name="galeri_sira[<?php echo $resim_id; ?>]" class="form-control" value="<?php echo $resim_sira; ?>" /></td>
						<td><input type="checkbox" name="galeri_sil[<?php echo $resim_id; ?>]" value="1" /></td>
						</tr>
						<?php 
						} ?>
						</tbody>
						</table>		
						<?php } ?>
						<input type="file" name="jaayquick_galeri" id="jaayquick_galeri" accept="image/*" multiple="multiple">
					</div>
					<?php } else {
						if ($tip_icerik!=0) {
							echo "<div class='clearfix mb15'></div>";
						}
					} ?>	
					<?php if (is_array($tip_ekstra_bilgiler)) { if (count($tip_ekstra_bilgiler)>0) { 
						echo "<ul class='nav nav-tabs' role='tablist'>";
							$say=1;
							$tab_sayisi=0;
							foreach ($tip_ekstra_bilgiler as $yaz => $x) {
								$eb_type=$x["tip"];
								@$eb_name=$x["id"];
								@$eb_text=$x["baslik"];
								if ($say==1) {
									$active="class='active'";
								} else {$active="";}
								if ($eb_type=="tab_ac") {
									echo "<li role='presentation' $active><a href='#$eb_name' aria-controls='$eb_name' role='tab' data-toggle='tab'>$eb_text</a></li>";
									$tab_sayisi=$tab_sayisi+1;
								}
								$say++; 
							} 
							echo "</ul>";
							?>
							<div class="panel panel-default" style="border-top:0;border-radius:0;box-shadow:none;">
							<?php if ($tab_sayisi==0) {if ($tip_ekstra_bilgiler_baslik!="0") {echo "<div class='panel-heading'>Ekstra Bilgiler</div>"; }} ?>
							<div class="panel-body">
							<?php
							echo "<div class='tab-content'>";
							$tab_say=1;
							foreach ($tip_ekstra_bilgiler as $yaz => $x) {
							$eb_type=$x["tip"];
							@$eb_name=$x["id"];
							@$eb_text=$x["baslik"];
							@$eb_options=$x["secenekler"];
							@$eb_required=$x["gerekli"];
							@$eb_value=$x["deger"];
							@$eb_rows=$x["satir"];
							@$eb_standart=$x["standart"];
							@$eb_aciklama=$x["aciklama"];
							@$eb_class=$x["sinif"];
							@$eb_grup=$x["grup"];
							
							if ($eb_grup) {
								if ($eb_grup==2) {
									$grup_ac_kod="<div class='col-md-30'>";
								} else if ($eb_grup==3) {
									$grup_ac_kod="<div class='col-md-20'>";
								} else if ($eb_grup==4) {
									$grup_ac_kod="<div class='col-md-15'>";
								} else if ($eb_grup==5) {
									$grup_ac_kod="<div class='col-md-12'>";
								} else if ($eb_grup==6) {
									$grup_ac_kod="<div class='col-md-10'>";
								}
								$grup_kapat_kod="</div>";			
							} else {
								$grup_ac_kod=null;
								$grup_kapat_kod=null;
							}
							
							@$ayar_kosul=$x["kosul"];
							if ($ayar_kosul) {
								$kosul_parcala=explode(":",$ayar_kosul);
								$diger_ayar=$kosul_parcala[0];
								$kosul_kalan=$kosul_parcala[1];
								$kosul_kosul_parcala=explode("(",$kosul_kalan);
								$kosul_sart=$kosul_kosul_parcala[0];
								$kosul_deger=rtrim($kosul_kosul_parcala[1],")");
								echo "
								<script type='text/javascript'>
									$(function(){
										var kosul='$kosul_sart';
										var deger='$kosul_deger';
										var diger_ayar_value=$('#$diger_ayar').val();
										if (kosul=='==') {
											if (diger_ayar_value!=deger) {
												$('#form_$eb_name').hide();
											}
										} else if (kosul=='!=') {
											if (diger_ayar_value==deger) {
												$('#form_$eb_name').hide();
											}
										}
										$('body').on('change','#$diger_ayar',function(){
											var diger_ayar_value=$('#$diger_ayar').val();
											if (kosul=='==') {
												if (diger_ayar_value==deger) {
													$('#form_$eb_name').show(100);
												} else {
													$('#form_$eb_name').hide(100);
												}
											} else if (kosul=='!=') {
												if (diger_ayar_value!=deger) {
													$('#form_$eb_name').show(100);
												} else {
													$('#form_$eb_name').hide(100);
												}
											}
										});
									});
								</script>
								";
								}
								echo $grup_ac_kod;
								$eb_yaz=ekstra_bilgi_getir($eb_name,$id,$tip);
								if (!$eb_yaz) {$eb_yaz=$eb_standart;}	
								if ($eb_type=="text" or $eb_type=="email" or $eb_type=="password" or $eb_type=="date" or $eb_type=="date_time") {
									form_elemani($eb_type,$eb_name,$eb_text,$eb_yaz,null,null,$eb_required,$eb_aciklama);
								} else if ($eb_type=="textarea") {
									if (!$eb_class) {$eb_class=null;}
									textarea_olustur($eb_class,$eb_name,$eb_text,$eb_yaz,$eb_rows,$eb_required,$eb_aciklama);
								} else if ($eb_type=="select") {
									select_olustur($eb_name,$eb_text,$eb_options,$eb_yaz,$eb_aciklama);
								} else if ($eb_type=="checkbox") {
									checkbox_olustur($eb_name,$eb_text,$eb_yaz,$eb_aciklama);
								} else if ($eb_type=="multiple_checkbox") {
									multiple_checkbox_olustur($eb_name,$eb_text,$eb_options,$eb_yaz,$eb_aciklama);
								} else if ($eb_type=="aciklama") {
									echo aciklama_olustur($eb_aciklama);
								} else if ($eb_type=="baslik") {
									echo baslik_olustur($eb_text,null,null,$eb_aciklama);
								} else if ($eb_type=="color") {
									colorpicker_olustur($eb_name,$eb_text,$eb_yaz,null,null,$eb_required,$eb_aciklama);
								} else if ($eb_type=="upload") {
									upload_olustur($eb_name,$eb_text,$eb_yaz,$eb_aciklama);
								} else if ($eb_type=="liste") {
									liste_link($eb_name,$eb_text,$eb_aciklama);
								} else if ($eb_type=="grup_ac") {
									grup_ac($eb_name,$eb_text,$eb_aciklama);
								} else if ($eb_type=="grup_kapat") {
									grup_kapat();
								} else if ($eb_type=="location") {
									location($eb_name,$eb_text,$eb_yaz,$eb_aciklama);
								} else if ($eb_type=="tab_ac") {
									if ($tab_say==1) {$active="active";} else {$active="";}
									tab_ac($eb_name,$eb_text,$active);
									$tab_say++;
								} else if ($eb_type=="tab_kapat") {
									tab_kapat();
								}
								echo $grup_kapat_kod;
							}
							echo "</div>";
							?>
							</div>
						</div>
			<?php } } ?>
					<?php jaayquick(@$panel_icerik_sol_alt_kod); ?>
					
				<!-- GELÄ°ÅTÄ°RÄ°CÄ° PANELÄ° BAÅLADI -->
				<button type="button" id="gelistirici_ac" class="btn btn-default"><i class="fa fa-toggle-off"></i><?php echo _LANG('p_dev_mod_ac'); ?></button>			
				<button type="button" id="gelistirici_kapat" class="btn btn-danger"><i class="fa fa-toggle-on"></i><?php echo _LANG('p_dev_mod_kapat'); ?></button>
				<div class="clearfix mb20"></div>
				
				<div id="gelistirici_paneli" class="panel panel-default">
					<div class="table-responsive">
						<table class="table table-bordered table-hover">
							<thead>
								<tr>
									<th><?php echo _LANG('p_anahtar'); ?></th>
									<th><?php echo _LANG('p_deger'); ?></th>
									<th width="170"><?php echo _LANG('p_islemler'); ?></th>
								</tr>
							</thead>
							<tbody id="eb_getir"></tbody>
							<tbody>
								<tr class="info"> 
									<td><input type="text" id="ekle_anahtar_<?php echo $id; ?>" class="form-control input-sm" placeholder="Anahtar"/></td>
									<td><input type="text" id="ekle_deger_<?php echo $id; ?>" class="form-control input-sm"  placeholder="<?php echo _LANG("p_deger"); ?>"/></td>
									<td><button id="eb_ekle_buton" type="button" class="btn btn-sm btn-success" value="<?php echo $id; ?>">Ekstra Bilgi Ekle</button></td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
				<!-- GELÄ°ÅTÄ°RÄ°CÄ° PANELÄ° BÄ°TTÄ° -->

				</div>
				<div class="<?php echo $icerik_sag_div; ?>">
					<?php jaayquick(@$panel_icerik_sag_kod); ?>

					<?php if ($tip_kategori==1 or $tip_kategori=="") { 
					$sinif_name="kategori"; 
					$toplam_kategori_sayisi=mysqli_num_rows(mysqli_query($baglan,"select id from kategoriler where tip='$tip' and sinif='$sinif_name'"));
					if ($tip!="ilan") {
						$kategori_ekle_stil=1;
					} else {
						$kategori_ekle_stil=2;
					}
					if ($kategori_ekle_stil==1) {
					?>
					<div class="panel panel-default">
						<div class="panel-heading">
							Kategori
							<a href="#" id="kategori_ekle_link" class="pull-right" style="font-weight:500 !important;"><?php echo _LANG('p_yeni_ekle'); ?></a>
						</div>
						<div class="panel-body">
							<div class="form-group">
								<div id="kategori_ekle_form" style="margin-bottom:5px;">
									<div class="input-group">
									  <input id="eklenecek_kategori" type="text" class="form-control" placeholder="Kategori" style="width:50%;">
									  <select id="eklenecek_kategori_ust" class="form-control" style="width:50%;background:#eee">
										<option value=""><?php echo _LANG('p_ust_kat_yok'); ?></option>
										<?php 
										$kategori_ekle_ust_kategori_sql=mysqli_query($baglan,"select * from kategoriler where tip='$tip' and sinif='kategori'");
										while ($kategori_ekle_ust_kategori_dizi=mysqli_fetch_array($kategori_ekle_ust_kategori_sql)) {
											$kategori_ekle_ust_kategori_id=$kategori_ekle_ust_kategori_dizi["id"];
											$kategori_ekle_ust_kategori_baslik=$kategori_ekle_ust_kategori_dizi["baslik"];
											echo "<option value='$kategori_ekle_ust_kategori_id'>$kategori_ekle_ust_kategori_baslik</option>";
										}
										?>
									  </select>
									  <span class="input-group-btn">
										<button id="kategori_ekle_buton" class="btn btn-default" type="button"><i class="fa fa-plus mr0"></i></button>
									  </span>
									</div>
									<div id="kategori_ajax_cevap"></div>
								</div>
								<div id="kategori_getir" class="multiple_checkbox_group_kategori"></div>
							</div>
						</div>
					</div>
					<script type="text/javascript">
					$(function(){
						// HÄ±zlÄ± kategori ekleme
						$("#kategori_ekle_form").hide();
						$("#kategori_ekle_link").click(function(){
						var text = $(this).text();
						$(this).text( text == "Yeni ekle" ? "iptal" : "Yeni ekle");
							$("#kategori_ekle_form").toggle();
						});
						
						$("#kategori_getir").load("inc/ajax/kategori_getir_guncelle.php?id=<?php echo $id; ?>&tip=<?php echo $tip; ?>&sinif=kategori");
						
						$("#kategori_ekle_buton").click(function(){
							var eklenecek_kategori=$("#eklenecek_kategori").val();
							var eklenecek_kategori_ust=$("#eklenecek_kategori_ust").val();
							var eklenecek_tip="<?php echo $tip; ?>";
							var eklenecek_sinif="kategori";
							$.ajax({
								type:"post",
								url:"inc/ajax.php?islem=kategori_ekle",
								data:{'eklenecek_kategori':eklenecek_kategori,'eklenecek_kategori_ust':eklenecek_kategori_ust,'tip':eklenecek_tip,'sinif':eklenecek_sinif},
								success:function(cevap){
									$("#kategori_ajax_cevap").html("<div class='alert alert-success alert-xs'>"+eklenecek_kategori+" <?php echo _LANG("p_kategori_eklendi_basarili"); ?></div>").fadeIn().delay(3000).fadeOut(500);
									$("#eklenecek_kategori_ust").append("<option value='"+cevap+"'>"+eklenecek_kategori+"</option>");
									$("#kategori_getir").load("inc/ajax/kategori_getir_guncelle.php?id=<?php echo $id; ?>&tip=<?php echo $tip; ?>&sinif=kategori");
									$("#eklenecek_kategori").val("");
								}
							});
						});
					});
					</script>
					<?php } else { ?>
					<!-- Kategori Ekle Stil 2 -->
					<div class="panel panel-default">
						<div class="panel-heading"><?php echo _LANG('p_kategori'); ?></div>
						<div class="panel-body">
							<div class="form-group">
								<div id="<?php echo $sinif_name; ?>_ekle_stil_2"></div>
							</div>
							<a href="#" id="<?php echo $sinif_name; ?>-sifirla" class="btn btn-danger btn-xs" style="display:none;"><?php echo _LANG("p_secimi_sifirla"); ?></a>
						</div>
					</div>					
					<script type="text/javascript">
					$(function(){
						<?php 
						$mevcut_kategoriler=kategori_getir($id,$sinif_name);
						if (count($mevcut_kategoriler)>0) { ?>
						$("#<?php echo $sinif_name; ?>_ekle_stil_2").load("inc/ajax/kategori_guncelle_stil_2.php?id=<?php echo $id; ?>&tip=<?php echo $tip; ?>&sinif=<?php echo $sinif_name; ?>");
						<?php } else { ?>
						$("#<?php echo $sinif_name; ?>_ekle_stil_2").load("inc/ajax/kategori_ekle_stil_2.php?id=<?php echo $id; ?>&tip=<?php echo $tip; ?>&sinif=<?php echo $sinif_name; ?>");
						<?php } ?>
						var kategori_deger=$("#<?php echo $sinif_name; ?>").val();
						$("#<?php echo $sinif_name; ?>-sifirla").click(function(){
							$.ajax({
								type:"post",
								url:"inc/ajax.php?islem=kategori_secimi_sifirla",
								data:{'id':<?php echo $id; ?>},
								success:function(cevap){}
							});
							$("#<?php echo $sinif_name; ?>-alt").html("");
							$("#<?php echo $sinif_name; ?>_tip_2").attr("disabled",false);
							$("#<?php echo $sinif_name; ?>_tip_2").val("");
							$("#<?php echo $sinif_name; ?>-sifirla").hide();
						});
						$('form').bind('submit', function () {
							$(this).find(':input').prop('disabled', false);
						});
					});
					</script>
					
					<?php } } ?>
							
					<?php if ($tip_ekstra_siniflar) {
					foreach ($tip_ekstra_siniflar as $sinif) {
						$sinif_id=$sinif["id"];
						$sinif_baslik=$sinif["baslik"];
					?>
					<div class="panel panel-default">
						<div class="panel-heading">
							<?php echo $sinif_baslik; ?>
							<a href="#" id="<?php echo $sinif_id; ?>_ekle_link" class="pull-right" style="font-weight:500 !important;">Yeni ekle</a>
						</div>					
						<div class="panel-body">
							<div class="form-group">
								<div id="<?php echo $sinif_id; ?>_ekle_form" style="margin-bottom:5px;">
									<div class="input-group">
									  <input id="<?php echo $sinif_id; ?>_eklenecek_kategori" type="text" class="form-control" placeholder="<?php echo $sinif_baslik; ?>" style="width:50%;">
									  <select id="<?php echo $sinif_id; ?>_eklenecek_kategori_ust" class="form-control" style="width:50%;background:#eee">
										<option value=""><?php echo _LANG('p_ust_kat_yok'); ?></option>
										<?php 
										$kategori_ekle_ust_kategori_sql=mysqli_query($baglan,"select * from kategoriler where tip='$tip' and sinif='$sinif_id'");
										while ($kategori_ekle_ust_kategori_dizi=mysqli_fetch_array($kategori_ekle_ust_kategori_sql)) {
											$kategori_ekle_ust_kategori_id=$kategori_ekle_ust_kategori_dizi["id"];
											$kategori_ekle_ust_kategori_baslik=$kategori_ekle_ust_kategori_dizi["baslik"];
											echo "<option value='$kategori_ekle_ust_kategori_id'>$kategori_ekle_ust_kategori_baslik</option>";
										}
										?>
									  </select>									  
									  <span class="input-group-btn">
										<button id="<?php echo $sinif_id; ?>_ekle_buton" class="btn btn-default" type="button"><i class="fa fa-plus mr0"></i></button>
									  </span>
									</div>
									<div id="<?php echo $sinif_id; ?>_ajax_cevap"></div>
								</div>
								<div id="<?php echo $sinif_id; ?>_getir" class="multiple_checkbox_group_kategori"></div>				
							</div>
						</div>
					</div>
					<script type="text/javascript">
					$(function(){
						// HÄ±zlÄ± kategori ekleme
						$("#<?php echo $sinif_id; ?>_ekle_form").hide();
						$("#<?php echo $sinif_id; ?>_ekle_link").click(function(){
						var text = $(this).text();
						$(this).text( text == "Yeni ekle" ? "iptal" : "Yeni ekle");
							$("#<?php echo $sinif_id; ?>_ekle_form").toggle();
						});
						
						$("#<?php echo $sinif_id; ?>_getir").load("inc/ajax/kategori_getir_guncelle.php?id=<?php echo $id; ?>&tip=<?php echo $tip; ?>&sinif=<?php echo $sinif_id; ?>");
						
						$("#<?php echo $sinif_id; ?>_ekle_buton").click(function(){
							var eklenecek_kategori=$("#<?php echo $sinif_id; ?>_eklenecek_kategori").val();
							var eklenecek_kategori_ust=$("#<?php echo $sinif_id; ?>_eklenecek_kategori_ust").val();
							var eklenecek_tip="<?php echo $tip; ?>";
							var eklenecek_sinif="<?php echo $sinif_id; ?>";
							$.ajax({
								type:"post",
								url:"inc/ajax.php?islem=kategori_ekle",
								data:{'eklenecek_kategori':eklenecek_kategori,'eklenecek_kategori_ust':eklenecek_kategori_ust,'tip':eklenecek_tip,'sinif':eklenecek_sinif},
								success:function(cevap){
									$("#<?php echo $sinif_id; ?>_ajax_cevap").html("<div class='alert alert-success alert-xs'>"+eklenecek_kategori+" <?php echo _LANG("p_kategori_eklendi_basarili"); ?></div>").fadeIn().delay(3000).fadeOut(500);
									$("#<?php echo $sinif_id; ?>_eklenecek_kategori_ust").append("<option value='"+cevap+"'>"+eklenecek_kategori+"</option>");
									$("#<?php echo $sinif_id; ?>_getir").load("inc/ajax/kategori_getir_guncelle.php?id=<?php echo $id; ?>&tip=<?php echo $tip; ?>&sinif=<?php echo $sinif_id; ?>");
									$("#<?php echo $sinif_id; ?>_eklenecek_kategori").val("");
								}
							});
						});
					});
					</script>
					<?php } } ?>	

					<div class="panel panel-default">
						<div class="panel-body">
						<?php if ($tip_yazar==1 or $tip_yazar=="") { ?>
							<div class="form-group">
								<label for="yazar"><?php echo $tip_yazar_baslik; ?></label>
								<select name="yazar" class="form-control" id="yazar">
								<?php 
								$mevcut_yazar=bilgi_getir("icerik","yazar",$id);
								$sql=mysqli_query($baglan,"select * from uyeler order by ad_soyad asc");
								while ($dizi=mysqli_fetch_array($sql)) {
									$uye_id=$dizi["id"];
									$uye_ad_soyad=$dizi["ad_soyad"];
								?>
								<option value="<?php echo $uye_id; ?>" <?php if ($uye_id==$mevcut_yazar) {echo "selected='selected'";}?>><?php echo $uye_ad_soyad; ?></option>
								<?php } ?>
								</select>
							</div>
						<?php }
						
						if ($tip_durum==1 or $tip_durum=="") {
						$yazi_durumu=bilgi_getir("icerik","durum",$id);
						select_olustur("durum","Durum",$icerik_durumlari,$yazi_durumu);
						} ?>	
						<?php if ($tip_etiket==1 or $tip_etiket=="") { textarea_olustur("etiket","etiketler","Etiketler",ekstra_bilgi_getir("etiketler",$id,$tip),3); } ?>						
						<?php if ($tip_gizli!=1) { textarea_olustur("seo_title","seo_title","SEO Title",ekstra_bilgi_getir("seo_title",$id,$tip),2); } ?>
						<?php if ($tip_gizli!=1) { textarea_olustur("seo_aciklama","seo_aciklama",_LANG("p_seo_aciklama"),ekstra_bilgi_getir("seo_aciklama",$id,$tip),3); } ?>
						
						<?php jaayquick(@$panel_icerik_sag_buton_ustu_kod); ?>

						<?php if ($tip_gizli!=1) { if ($yazi_durumu==1) {
						form_buton("buton",_LANG("p_isaret_guncelle", array($tip_baslik_isaret)),"primary btn-block btn-lg btn-icerik-ozel");						
						} else { ?>
						<div class="row">
							<div class="col-md-30 btn_ikili_sol">
								<?php form_buton("buton",_LANG('p_guncelle'),"default btn-block btn-lg"); ?>
							</div>
							<div class="col-md-30 btn_ikili_sag">
								<?php form_buton("buton_yayinla",_LANG('p_yayinla'),"primary btn-block btn-lg btn-icerik-ozel"); ?>
							</div>
						</div>
						<?php } } else {
						form_buton("buton",_LANG("p_isaret_guncelle", array($tip_baslik_isaret)),"primary btn-block btn-lg btn-icerik-ozel");														
						} ?>
						<div class="clearfix cizgi"></div>
						<a href="icerik.php?tip=<?php echo $tip; ?>&islem=sil&id=<?php echo $id; ?>" onclick="return confirm('<?php echo _LANG("p_emi_misiniz"); ?>')"><?php echo _LANG("p_ogeyi_sil"); ?></a>
						</div>
					</div>
					<?php if ($tip_gorsel==1 or $tip_gorsel=="") { ?>
					<div class="panel panel-default">
					<div class="panel-heading"><?php echo _LANG('p_gorsel'); ?></div>
						<div class="panel-body">
							<?php $one_cikarilan_gorsel=icerik_image_url($id,$tip); if ($one_cikarilan_gorsel!="") { ?>
								<label for="one_cikarilan_gorsel" style="cursor:pointer;margin-bottom:15px !important;position:relative;width:100%;">
								<?php echo "<img src='$one_cikarilan_gorsel' class='img-responsive yazi_gorsel' />"; ?>
								<a href="icerik.php?tip=<?php echo $tip; ?>&islem=gorsel_sil&id=<?php echo $id; ?>" id="gorsel_kaldir" style="position:absolute;right:0;top:0;" onclick="return confirm('<?php echo _LANG("p_one_cikarilan_gorseli_sil"); ?>')" class="btn btn-danger btn-xs"><?php echo _LANG('gen_remove'); ?></a>
								</label>
								<div class="clearfix"></div>
							<?php } ?>							
							<input type="file" name="resim_jaayquick" id="one_cikarilan_gorsel">
						</div>
					</div>
					<?php } ?>
					
					<?php 
					if (is_array(@$icerik_liste)) {
					if (count($icerik_liste)>0) {
					foreach ($icerik_liste as $x) {
					$liste_id=$x["id"];
					$liste_baslik=$x["baslik"];
					$liste_tip=$x["tip"];
					$tip_listesi=0;
					if ($liste_tip==$tip or strstr($liste_tip,$tip)) {
						$liste_icerik_sayisi=liste_icerik_sayisi($liste_id,$id);
						$liste_kod.="
						<div id='form_$liste_id' class='form-group'>
						$aciklama_yaz
						<div class='liste_link' style='margin-top:0 !important;'>
				<p>" . _LANG('p_liste_ogesi_sayisi', array($liste_icerik_sayisi)) . " $liste_baslik</p>
						<a href='listeler.php?liste=$liste_id&icerik_id=$id' class='btn btn-default' target='_blank'><i class='fa fa-external-link'></i>" . _LANG('p_listeyi_yonet') . "</a>
						</div>
						<div class='clearfix mb10'></div>
						";
						$tip_listesi++;
					}
					}
					if ($tip_listesi>0) {
					?>
					<div class="panel panel-default">
					<div class="panel-heading"><?php echo _LANG('p_listeler'); ?></div>
					<div class="panel-body" style="padding-bottom:0;">
					<?php echo $liste_kod; ?>
					</div>
					</div>
					</div>
			<?php } } } ?>
					
					<?php jaayquick(@$panel_icerik_sag_alt_kod); ?>
				</div>
				<?php form_elemani("hidden","aktif_tab",_LANG('p_aktif_tab')); ?>
				</form>	
			</div>
		<?php } ?>
		</div>
		<div class="clearfix mb20"></div>
</div>
</div>
</div>

<?php include("inc/alt.php"); ?>
<?php include("inc/footer-kod.php"); ?>
<script type="text/javascript">
	$(document).ready(function(){
		
		$("#eb_getir").load("inc/ajax/eb_getir.php?id=<?php echo $id; ?>&tip=<?php echo $tip; ?>");

		$("#gelistirici_ac").click(function(){
			$(this).hide();
			$.cookie("gelistirici_modu", 1);
			$("#gelistirici_kapat").show();
			$("#gelistirici_paneli").show();
		});
		
		$("#gelistirici_kapat").click(function(){
			$(this).hide();
			$.cookie("gelistirici_modu", 0);
			$("#gelistirici_ac").show();
			$("#gelistirici_paneli").hide();
		});		
		
		$("body").on("click","#eb_guncelle_buton",function(){
			var eb_id=$(this).val();
			var yeni_deger=$("#deger_"+eb_id).val();
			var yeni_anahtar=$("#anahtar_"+eb_id).val();
			
			$.ajax({
				type:"post",
				url:"inc/ajax.php?islem=eb_guncelle",
				data:{'guncelle_id':eb_id,'anahtar':yeni_anahtar,'deger':yeni_deger},
				success:function(cevap){
					$("#eb_"+eb_id+" #sonuc").text("<?php echo _LANG("p_basariliyla_guncellendi"); ?>").fadeIn().delay(3000).fadeOut(500);
					$("#eb_"+eb_id).addClass("success");
				}
			});
		});
		$("body").on("click","#eb_sil_buton",function(){
			var eb_id=$(this).val();
			
			$.ajax({
				type:"post",
				url:"inc/ajax.php?islem=eb_sil",
				data:{'sil_id':eb_id},
				success:function(cevap){
					$("#eb_"+eb_id).addClass("danger").fadeOut(1000);
				}
			});
		});
		$("body").on("click","#eb_ekle_buton",function(){
			var icerik_id=$(this).val();
			var ekle_anahtar=$("#ekle_anahtar_"+icerik_id).val();
			var ekle_deger=$("#ekle_deger_"+icerik_id).val();
			
			$.ajax({
				type:"post",
				url:"inc/ajax.php?islem=eb_ekle",
				data:{'icerik_id':icerik_id,'anahtar':ekle_anahtar,'deger':ekle_deger},
				success:function(cevap){
					$("#eb_getir").load("inc/ajax/eb_getir.php?id=<?php echo $id; ?>&tip=<?php echo $tip; ?>");
					$("#ekle_anahtar_"+icerik_id).val("");
					$("#ekle_deger_"+icerik_id).val("");
				}
			});
		});
	});
</script>
</body>
</html>
