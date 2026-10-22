<?php
include("../loader.php");
yonetici_degilse_yonlendir($oturum_id,ayar_getir("site_url")."/$admin_dizini/giris-yap.php");
$l_say=count($liste);

@$get_liste=$_GET["liste"];
@$get_icerik_id=$_GET["icerik_id"];
@$islem=$_GET["islem"];
$tip="liste";
if (!@$get_icerik_id) {
	$get_icerik_id=0;
}
if ($get_liste) {
	if ($get_icerik_id) {
		$liste_bilgileri=$icerik_liste[$get_liste];
		$liste_baslik=$icerik_liste[$get_liste]["baslik"];
		$liste_baslik_cogul=$icerik_liste[$get_liste]["baslik_cogul"];
		$ayar=$icerik_liste[$get_liste]["ayarlar"];
	} else {
		$liste_bilgileri=$liste[$get_liste];
		$liste_baslik=$liste[$get_liste]["baslik"];
		$liste_baslik_cogul=$liste[$get_liste]["baslik_cogul"];
		$ayar=$liste[$get_liste]["ayarlar"];
	}
	@$ekle_buton=$_POST["ekle_buton"];
	if ($ekle_buton) {
		$sira=$_POST["sira"];
		if (!$sira) {
			$sira=50;
		}
		$ekle=mysqli_prepare($baglan,"insert into listeler set sira=?, tip=?, id=?");
  	$sira_int=(int)$sira;
  	$get_icerik_id_int=(int)$get_icerik_id;
  	mysqli_stmt_bind_param($ekle,"isi",$sira_int,$get_liste,$get_icerik_id_int);
  	mysqli_stmt_execute($ekle);
  	mysqli_stmt_close($ekle);
		$id=mysqli_insert_id($baglan);
		foreach ($ayar as $yaz => $x) {	
			$eb_type=$x["tip"];
			$eb_name=$x["id"];
			$deger=$_POST["$eb_name"];
			$mevcut_deger=ekstra_bilgi_getir($eb_name,$id,"liste");
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
				if ($diziden_cikart!=$mevcut_deger) {
					ekstra_bilgi_guncelle($eb_name,$diziden_cikart,$id,$tip);
				}
				$diziden_cikart=null;
			}
			else if ($eb_type=="upload") {
				$eb_boyutlar=$x["boyutlar"];
				$eb_dizin=$x["dizin"];
				$eb_orijinali_sil=$x["orijinali_sil"];
				$eb_gecerli_boyut=$x["gecerli_boyut"];		
				
				// v2 resim upload remove güncellemesi başladı.
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
		$_SESSION["mesaj"]=array(_LANG('p_list_item_added'),"success");
		if ($get_icerik_id) {
			yonlendir("listeler.php?liste=$get_liste&icerik_id=$get_icerik_id");
		} else {
			yonlendir("listeler.php?liste=$get_liste");
		}
	}
if ($islem=="guncelle") {
@$id=$_GET["id"];
@$sira=$_POST["sira"];
@$guncelle_buton=$_POST["guncelle_buton"];
if ($guncelle_buton) {
	if (!$sira and $sira!='0') {
		$toplam=mysqli_num_rows(mysqli_query($baglan,"select * from listeler where tip='$get_liste'"));
		$sira=($toplam+1)*10;
	}
	// Sıra güncelleme
	$liste_sorgu=mysqli_prepare($baglan,"update listeler set sira=? where l_id=?");
  	$sira_int=(int)$sira;
  	$id_int=(int)$id;
  	mysqli_stmt_bind_param($liste_sorgu,"ii",$sira_int,$id_int);
  	mysqli_stmt_execute($liste_sorgu);
  	mysqli_stmt_close($liste_sorgu);
	foreach ($ayar as $yaz => $x) {	
		$eb_type=$x["tip"];
		$eb_name=$x["id"];
		$deger=$_POST["$eb_name"];
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
			if ($diziden_cikart!=$mevcut_deger) {
				ekstra_bilgi_guncelle($eb_name,$diziden_cikart,$id,$tip);
			}
			$diziden_cikart=null;
		}
		else if ($eb_type=="upload") {
			$eb_boyutlar=$x["boyutlar"];
			$eb_dizin=$x["dizin"];
			$eb_orijinali_sil=$x["orijinali_sil"];
			$eb_gecerli_boyut=$x["gecerli_boyut"];		
			
			// v2 resim upload remove güncellemesi başladı.
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
	// Mevcut upload resmi sil düğmesi
	@$upload_kaldir_dizisi=$_POST["upload_kaldir"];
	foreach ($upload_kaldir_dizisi as $name) {
		$mevcut_deger=ekstra_bilgi_getir($name,$id,$tip);
		if ($eb_gecerli_boyut) {
			$mevcut_deger=thumbnail_url("full",$mevcut_deger,$eb_gecerli_boyut);
		}
		sil($mevcut_deger);
		foreach ($eb_boyutlar as $yaz) {
			$thumbnail_url=thumbnail_url($yaz,$mevcut_deger);
			sil($thumbnail_url);
		}
		$ekstra_bilgi_id=ekstra_bilgi_id_getir($name,$id,$tip);
		ekstra_bilgi_sil($ekstra_bilgi_id);
	}
	// v2 resim upload remove güncellemesi bitti.	
	
	$_SESSION["mesaj"]=array(_LANG('p_list_item_updated'),"success");
	if ($get_icerik_id) {
		yonlendir("listeler.php?liste=$get_liste&icerik_id=$get_icerik_id");
	} else {
		yonlendir("listeler.php?liste=$get_liste");
	}
	}
} // islem=="guncelle"
if ($islem=="sil") {
	$id=$_GET["id"];
	$liste_sil=mysqli_prepare($baglan,"delete from listeler where l_id=?");
  	$id_int=(int)$id;
  	mysqli_stmt_bind_param($liste_sil,"i",$id_int);
  	mysqli_stmt_execute($liste_sil);
  	mysqli_stmt_close($liste_sil);
	$ekstra_bilgi_sil=mysqli_prepare($baglan,"delete from ekstra_bilgi where id=? and tip='liste'");
  	$id_int=(int)$id;
  	mysqli_stmt_bind_param($ekstra_bilgi_sil,"i",$id_int);
  	mysqli_stmt_execute($ekstra_bilgi_sil);
  	mysqli_stmt_close($ekstra_bilgi_sil);
	$_SESSION["mesaj"]=array(_LANG('p_list_item_deleted'),"success");
	if ($get_icerik_id) {
		yonlendir("listeler.php?liste=$get_liste&icerik_id=$get_icerik_id");
	} else {
		yonlendir("listeler.php?liste=$get_liste");
	}
}
if ($islem=="yukari_kaydir") {
	$id=$_GET["id"];
	$sira=getir("listeler","sira",$id);
	$toplam_liste=liste_icerik_sayisi($get_liste);
	$sql=mysqli_query($baglan,"select * from listeler where tip='$get_liste' order by sira asc");
	$y=1;
	while ($dizi=mysqli_fetch_array($sql)) {
		$liste_id=$dizi["l_id"];
		if ($liste_id==$id) {
			$kacinci_sirada=$y;
		}
		$y++;
	}
	$olmasi_gereken_sira=$kacinci_sirada-1;
	$yeni_sira=$olmasi_gereken_sira*10;
	$sorgu=mysqli_prepare($baglan,"update listeler set sira=? where l_id=?");
  	$yeni_sira_int=(int)$yeni_sira;
  	$id_int=(int)$id;
  	mysqli_stmt_bind_param($sorgu,"ii",$yeni_sira_int,$id_int);
  	mysqli_stmt_execute($sorgu);
  	mysqli_stmt_close($sorgu);
	$z=1;
	$sql=mysqli_query($baglan,"select * from listeler where tip='$get_liste' and l_id!='$id' order by sira asc");
	while ($dizi=mysqli_fetch_array($sql)) {
		$liste_id=$dizi["l_id"];
		if ($z==$olmasi_gereken_sira) {
			$z++;
		}
		$sira=$z*10;
		$sorgu=mysqli_prepare($baglan,"update listeler set sira=? where l_id=?");
  	$sira_int=(int)$sira;
  	$liste_id_int=(int)$liste_id;
  	mysqli_stmt_bind_param($sorgu,"ii",$sira_int,$liste_id_int);
  	mysqli_stmt_execute($sorgu);
  	mysqli_stmt_close($sorgu);
		$z++;
	}
	$_SESSION["mesaj"]=array(_LANG('p_list_item_moved_up'),"success");
	if ($get_icerik_id) {
		yonlendir("listeler.php?liste=$get_liste&icerik_id=$get_icerik_id");
	} else {
		yonlendir("listeler.php?liste=$get_liste");
	}
}	
if ($islem=="asagi_kaydir") {
	$id=$_GET["id"];
	$sira=getir("listeler","sira",$id);
	$toplam_liste=liste_icerik_sayisi($get_liste);
	$sql=mysqli_query($baglan,"select * from listeler where tip='$get_liste' order by sira asc");
	$y=1;
	while ($dizi=mysqli_fetch_array($sql)) {
		$liste_id=$dizi["l_id"];
		if ($liste_id==$id) {
			$kacinci_sirada=$y;
		}
		$y++;
	}
	$olmasi_gereken_sira=$kacinci_sirada+1;
	$yeni_sira=$olmasi_gereken_sira*10;
	$sorgu=mysqli_prepare($baglan,"update listeler set sira=? where l_id=?");
	$yeni_sira_int=(int)$yeni_sira;
	$id_int=(int)$id;
	mysqli_stmt_bind_param($sorgu,"ii",$yeni_sira_int,$id_int);
	mysqli_stmt_execute($sorgu);
	mysqli_stmt_close($sorgu);
	$z=1;
	$sql=mysqli_query($baglan,"select * from listeler where tip='$get_liste' and l_id!='$id' order by sira asc");
	while ($dizi=mysqli_fetch_array($sql)) {
		$liste_id=$dizi["l_id"];
		if ($z==$olmasi_gereken_sira) {
			$z++;
		}
		$sira=$z*10;
		$sorgu=mysqli_prepare($baglan,"update listeler set sira=? where l_id=?");
	$sira_int=(int)$sira;
	$liste_id_int=(int)$liste_id;
	mysqli_stmt_bind_param($sorgu,"ii",$sira_int,$liste_id_int);
	mysqli_stmt_execute($sorgu);
	mysqli_stmt_close($sorgu);
		$z++;
	}
	$_SESSION["mesaj"]=array(_LANG('p_list_item_moved_down'),"success");
	if ($get_icerik_id) {
		yonlendir("listeler.php?liste=$get_liste&icerik_id=$get_icerik_id");
	} else {
		yonlendir("listeler.php?liste=$get_liste");
	}
}		
}
$panel_sablon=ayar_getir("panel_sablon");
if ($panel_sablon=="fullwidth") {
	$div_class="col-lg-15 col-md-20 col-sm-30 col-xs-60";
} else if ($panel_sablon=="boxed") {
	$div_class="col-lg-20 col-md-20 col-sm-30 col-xs-60";
}
?>
<!DOCTYPE HTML>
<html lang="<?php echo _DIL_HTML_LANG(); ?>" dir="<?php echo _DIL_HTML_YON(); ?>">
<head>
	<meta charset="UTF-8">
	<title>Listeler - <?php echo $lpanel_baslik; ?></title>
	<?php include("inc/header-kod.php"); ?>
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
				<?php if (!$get_liste) { ?>
					<h3 class="pull-left bolum-baslik"><?php echo _LANG('p_listeler_basligi'); ?></h3>
					<!--<a class="btn btn-default pull-right"><i class="fa fa-question"></i><?php echo _LANG('p_nedir'); ?></a>-->
					<div class="clearfix mb20"></div>
					<div class="alert alert-primary"><strong><?php echo $l_say; ?> <?php echo _LANG('p_adet'); ?></strong> <?php echo _LANG('p_liste_alan_yok'); ?></div>
					<div class="row">
						<?php foreach($liste as $x) {
							@$liste_name=$x["id"];
							@$liste_baslik=$x["baslik"];
							@$liste_baslik_cogul=$x["baslik_cogul"];
							@$liste_aciklama=$x["aciklama"];
							$nesne_sayisi=mysqli_num_rows(mysqli_query($baglan,"select id from listeler where tip='$liste_name'"));
							$liste_ayar=$liste[$liste_name]["ayarlar"];
							if (count($liste_ayar)>0) {
								$panel_renk="default";
							} else {
								$panel_renk="danger";
							}
						?>
						<div class="<?php echo $div_class; ?>">
						<div class="panel panel-<?php echo $panel_renk; ?>">
						<div class="panel-heading"><?php echo $liste_baslik_cogul; ?></div>
							<table class="table table-bordered table-hover">
								<tbody>
									<?php if ($liste_aciklama) { ?>
									<tr>
										<th width="140"><?php echo _LANG('p_aciklama'); ?></th>
										<td><?php echo $liste_aciklama; ?></td>
									</tr>
									<?php } ?>
									<tr>
										<th width="140"><?php if ($liste_baslik) {echo $liste_baslik;} else {echo _LANG('p_oge');} ?> <?php echo _LANG('p_sayisi'); ?></th>
										<td><?php echo $nesne_sayisi; ?></td>
									</tr>
								</tbody>
							</table>
							<div class="panel-footer">
							<?php if ($panel_renk=="danger") { ?>
								<a class="btn btn-default btn-sm disabled pull-right"><i class="fa fa-info"></i><?php echo _LANG('p_liste_ayar_alinamadi'); ?></a>
							<?php } else { ?>
								<!-- Split button -->
								<div class="pull-right">
								  <a href="listeler.php?liste=<?php echo $liste_name; ?>" class="btn btn-sm btn-default"><i class="fa fa-pencil" aria-hidden="true"></i><?php echo _LANG('p_yonet'); ?></a>
								</div>
							<?php } ?>
							<div class="clearfix"></div>
							</div>
						</div>
						</div>
						<?php } ?>
					</div>
				<?php } else { ?>
					<h3 class="pull-left bolum-baslik"><?php if ($get_icerik_id) {echo _LANG('p_icerik_listeleri');} else {echo _LANG('p_listeler_basligi');} ?> <small>/ <?php echo $get_liste; ?></small></h3>
					<a href="listeler.php" class="btn btn-primary"><?php echo _LANG('p_liste_anasayfa'); ?></a>
					<div class="clearfix mb20"></div>
					<?php 
					if ($get_icerik_id) {
						$icerik_tip=getir("icerik","tip",$get_icerik_id);
						$icerik_baslik=getir("icerik","baslik",$get_icerik_id);
						$icerik_link=link_getir($get_icerik_id);
					?>
					<div class="panel panel-default">
						<div class="panel-heading"><?php echo $icerik_baslik; ?></div>
						<table class="table table-default">
							<tr>
								<th><?php echo _LANG('p_baslik'); ?></th>
								<td><a href="<?php echo $icerik_link; ?>"><?php echo $icerik_baslik; ?></a></td>
							</tr>
							<tr>
								<th width="100"><?php echo _LANG('p_icerik_tipi'); ?></th>
								<td><?php echo $icerik_tip; ?></td>
							</tr>
						</table>
					</div>
					<?php 	
					}
					?>
					<div class="row">
						<div class="col-md-20">
						<form action="" method="post" enctype="multipart/form-data">
							<div class="panel panel-default">
							<div class="panel-heading"><?php if ($liste_baslik) {echo $liste_baslik;} else {echo _LANG('p_oge');} ?> <?php echo _LANG('p_ekle'); ?></div>
							<?php 
							if (count($ayar)>0) { 
							echo "<ul class='nav nav-tabs' role='tablist'>";
								$say=1;
								$tab_sayisi=0;
								foreach ($ayar as $yaz => $x) {	
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
								<div class="panel-body">
								<?php
								echo "<div class='tab-content'>";
								$tab_say=1;
								foreach ($ayar as $yaz => $x) {
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
								$eb_yaz=null;
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
								form_elemani("text","sira",_LANG('p_sira'));
								// Tab hidden input
								form_elemani("hidden","aktif_tab","Aktif Tab");
								echo "</div>";
								?>
								</div>
								<div class="panel-footer">
									<button type="submit" name="ekle_buton" class="btn btn-primary" value="<?php echo _LANG('gen_add'); ?>"><?php echo _LANG('p_ekle'); ?></button>	
								</div>
								<?php } else {echo "<div class='panel-body'>" . _LANG('p_list_info_not_found') . "</div>";} ?>
							</div>
						</form>
						</div>
						<div class="col-md-40">
							<?php if ($islem=="guncelle") {
							$sira=mysqli_fetch_array(mysqli_query($baglan,"select * from listeler where l_id='$id'"))["sira"];
							?>
							<form action="" method="post" enctype="multipart/form-data">
							<div class="panel panel-default">
								<div class="panel-heading"><?php if ($liste_baslik) {echo "$liste_baslik";} ?> #<?php echo $id; ?><?php echo _LANG('p_sira_ek'); ?> <?php echo $sira; ?></div>
							<?php 
							if (count($ayar)>0) { 
							echo "<ul class='nav nav-tabs' role='tablist'>";
								$say=1;
								$tab_sayisi=0;
								foreach ($ayar as $yaz => $x) {	
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
								<div class="panel-body">
								<?php
								echo "<div class='tab-content'>";
								$tab_say=1;
								foreach ($ayar as $yaz => $x) {
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
								$eb_yaz=ekstra_bilgi_getir($eb_name,$id,"liste");
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
								form_elemani("text","sira",_LANG('p_sira'),$sira);
								// Tab hidden input
								form_elemani("hidden","aktif_tab","Aktif Tab");
								echo "</div>";
								?>
								</div>
								<div class="panel-footer">
									<button type="submit" name="guncelle_buton" class="btn btn-primary pull-right" value="<?php echo _LANG('general_update'); ?>"><?php echo _LANG('p_guncelle'); ?></button>
									<div class="clearfix"></div>
								</div>
								<?php } else {echo "<div class='panel-body'>" . _LANG('p_list_info_not_found') . "</div>";} ?>								
							</div>
							</form>
							<?php } ?>
							<div class="panel panel-default">
								<div class="panel-heading"><?php echo $liste_baslik_cogul; ?></div>
								<div class="panel-body">
								<?php 
								$y=1;
								$toplam_liste=liste_icerik_sayisi($get_liste);
								$sql=mysqli_query($baglan,"select * from listeler where tip='$get_liste' order by sira asc"); while ($liste_dizisi=mysqli_fetch_array($sql)) {
								$liste_id=$liste_dizisi["l_id"];
								$liste_sira=$liste_dizisi["sira"];
								?>
								<div class="panel panel-default">
									<div class="panel-heading" style="background:#f5f5f5 !important;color:#444 !important;border-color:#e5e5e5 !important;">
										<?php if ($liste_baslik) {echo "$liste_baslik";} ?> #<?php echo $liste_id; ?><?php echo _LANG('p_sira_ek'); ?> <?php echo $liste_sira; ?>
										<div class="btn-group pull-right" role="group" aria-label="...">
										  <a href="listeler.php?liste=<?php echo $get_liste; ?>&islem=guncelle&id=<?php echo $liste_id; if ($get_icerik_id) {echo "&icerik_id=$get_icerik_id";} ?>" class="btn btn-primary btn-xs"><i class="fa fa-edit mr0"></i></a>
										  <?php if ($y!=1) { ?><a href="listeler.php?liste=<?php echo $get_liste; ?>&islem=yukari_kaydir&id=<?php echo $liste_id; if ($get_icerik_id) {echo "&icerik_id=$get_icerik_id";} ?>" class="btn btn-default btn-xs"><i class="fa fa-arrow-up mr0"></i></a><?php } ?>
										  <?php if ($y!=$toplam_liste) { ?><a href="listeler.php?liste=<?php echo $get_liste; ?>&islem=asagi_kaydir&id=<?php echo $liste_id; if ($get_icerik_id) {echo "&icerik_id=$get_icerik_id";} ?>" class="btn btn-default btn-xs"><i class="fa fa-arrow-down mr0"></i></a><?php } ?>
										  <a href="listeler.php?liste=<?php echo $get_liste; ?>&islem=sil&id=<?php echo $liste_id; if ($get_icerik_id) {echo "&icerik_id=$get_icerik_id";} ?>" class="btn btn-danger btn-xs" onclick="return confirm('<?php echo _LANG('gen_delete'); ?>')"><i class="fa fa-trash mr0"></i></a>
										</div>
									</div>
									<table class="table table-default table-condensed ">
										<tbody>									
										<?php 
										foreach ($ayar as $yaz => $x) {
											$eb_type=$x["tip"];
											$eb_name=$x["id"];
											$mevcut_deger=ekstra_bilgi_getir($eb_name,$liste_id,"liste");
										?>
										<tr>
											<th width="150"><?php echo $eb_name; ?></th>
											<td><?php if ($mevcut_deger) { if ($eb_type=="upload") {echo "<img src='$mevcut_deger' class='img-responsive panel-gorsel' />";} else {echo $mevcut_deger;}} ?></td>
										</tr>
										<?php } ?>
										</tbody>
									</table>
								</div>
								<?php $y++; } ?>
								</div>
							</div>
						</div>
					</div>
				<?php } ?>
			</div>
			<div class="clearfix mb20"></div>
		</div>
	</div>
</div>
<?php include("inc/alt.php"); ?>
<?php include("inc/footer-kod.php"); ?>
</body>
</html>