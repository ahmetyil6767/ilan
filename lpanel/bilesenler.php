<?php
include("../loader.php");
yonetici_degilse_yonlendir($oturum_id,ayar_getir("site_url")."/$admin_dizini/giris-yap.php");
@$islem=$_GET["islem"];
@$ekle_buton=$_POST["ekle_buton"];
$bilesen_alani_kopya=$bilesen_alani;
if ($ekle_buton) {
	$alan_value=$ekle_buton;
	$bilesen_value=$_POST["bilesen_$alan_value"];
	$kac_bilesen_var=mysqli_num_rows(mysqli_query($baglan,"select * from bilesenler where alan='$alan_value'"));
	$sira=$kac_bilesen_var*10;
	$bilesen_ekle=mysqli_prepare($baglan,"insert into bilesenler set alan=?, bilesen=?, sira=?");
  	$sira_int=(int)$sira;
  	mysqli_stmt_bind_param($bilesen_ekle,"ssi",$alan_value,$bilesen_value,$sira_int);
  	mysqli_stmt_execute($bilesen_ekle);
  	mysqli_stmt_close($bilesen_ekle);
	$eklenen_id=mysqli_insert_id($baglan);	
	
	$ayarlar=$bilesen[$bilesen_value]["ayarlar"];
	foreach ($ayarlar as $yaz => $x) {
		$eb_name=$x["id"];
		$eb_standart=$x["standart"];
		if ($eb_standart) {
			bilesen_ayar_guncelle($eb_name,$eb_standart,$eklenen_id);
		}
	}	

	$_SESSION["mesaj"]=array(_LANG('p_component_added'),"success");
	yonlendir("bilesenler.php?islem=guncelle&bilesen=$eklenen_id");	
}
if ($islem=="guncelle") { 
@$id=$_GET["bilesen"];
$bilesen_alani=bilgi_getir("bilesenler","alan",$id);
$bilesen_value=bilgi_getir("bilesenler","bilesen",$id);
@$guncelle_buton=$_POST["guncelle_buton"];
@$sil_buton=$_POST["sil_buton"];
$tip="bilesen";
	if ($guncelle_buton) {
		$baslik=bilgi_getir("bilesenler","baslik",$id);
		$sira=bilgi_getir("bilesenler","sira",$id);
		$baslik_yeni=$_POST["baslik"];
		$sira_yeni=$_POST["sira"];
		if ($baslik!=$baslik_yeni) {
			$baslik_sorgu=mysqli_prepare($baglan,"update bilesenler set baslik=? where id=?");
  	$id_int=(int)$id;
  	mysqli_stmt_bind_param($baslik_sorgu,"si",$baslik_yeni,$id_int);
  	mysqli_stmt_execute($baslik_sorgu);
  	mysqli_stmt_close($baslik_sorgu);
		}
		if ($sira!=$sira_yeni) {
			$sira_sorgu=mysqli_prepare($baglan,"update bilesenler set sira=? where id=?");
  	$sira_yeni_int=(int)$sira_yeni;
  	$id_int=(int)$id;
  	mysqli_stmt_bind_param($sira_sorgu,"ii",$sira_yeni_int,$id_int);
  	mysqli_stmt_execute($sira_sorgu);
  	mysqli_stmt_close($sira_sorgu);
		}
		$ayar=$bilesen[$bilesen_value]["ayarlar"];
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
		
		$_SESSION["mesaj"]=array(_LANG('p_component_settings_updated'),"success");
		$aktif_tab=$_POST["aktif_tab"];
		if ($aktif_tab) {
			yonlendir("bilesenler.php?islem=guncelle&bilesen=$id&tab=$aktif_tab");
		} else {
			yonlendir("bilesenler.php?islem=guncelle&bilesen=$id");
		}
	}
	if ($sil_buton) {
		$id_int=(int)$id;
  	$eb_sorgu=mysqli_prepare($baglan,"delete from ekstra_bilgi where id=? and tip='bilesen'");
  	mysqli_stmt_bind_param($eb_sorgu,"i",$id_int);
  	mysqli_stmt_execute($eb_sorgu);
  	mysqli_stmt_close($eb_sorgu);
  	$bilesen_sorgu=mysqli_prepare($baglan,"delete from bilesenler where id=?");
  	mysqli_stmt_bind_param($bilesen_sorgu,"i",$id_int);
  	mysqli_stmt_execute($bilesen_sorgu);
  	mysqli_stmt_close($bilesen_sorgu);
		$_SESSION["mesaj"]=array(_LANG('p_component_removed'),"success");
		yonlendir("bilesenler.php?islem=goruntule&alan=$bilesen_alani");	
	}
}
if ($islem=="goruntule") {
	$bilesen_alani=$_GET["alan"];
}
if ($islem=="sil") {
	$id=$_GET["bilesen"];
	$bilesen_alani=$_GET["alan"];
	$id_int=(int)$id;
  	$eb_sorgu=mysqli_prepare($baglan,"delete from ekstra_bilgi where id=? and tip='bilesen'");
  	mysqli_stmt_bind_param($eb_sorgu,"i",$id_int);
  	mysqli_stmt_execute($eb_sorgu);
  	mysqli_stmt_close($eb_sorgu);
  	$bilesen_sorgu=mysqli_prepare($baglan,"delete from bilesenler where id=?");
  	mysqli_stmt_bind_param($bilesen_sorgu,"i",$id_int);
  	mysqli_stmt_execute($bilesen_sorgu);
  	mysqli_stmt_close($bilesen_sorgu);
	$_SESSION["mesaj"]=array(_LANG('p_component_removed'),"success");
	yonlendir("bilesenler.php?islem=goruntule&alan=$bilesen_alani");	
}
if ($islem=="ekle") {
	$alan_value=$_GET["alan"];
	$bilesen_value=$_GET["bilesen"];
	$kac_bilesen_var=mysqli_num_rows(mysqli_query($baglan,"select * from bilesenler where alan='$alan_value'"));
	$sira=$kac_bilesen_var*10;
	$bilesen_ekle=mysqli_prepare($baglan,"insert into bilesenler set alan=?, bilesen=?, sira=?");
  	$sira_int=(int)$sira;
  	mysqli_stmt_bind_param($bilesen_ekle,"ssi",$alan_value,$bilesen_value,$sira_int);
  	mysqli_stmt_execute($bilesen_ekle);
  	mysqli_stmt_close($bilesen_ekle);
	$eklenen_id=mysqli_insert_id($baglan);	
	$_SESSION["mesaj"]=array(_LANG('p_component_added'),"success");
	
	$ayarlar=$bilesen[$bilesen_value]["ayarlar"];
	foreach ($ayarlar as $yaz => $x) {
		$eb_name=$x["id"];
		$eb_standart=$x["standart"];
		if ($eb_standart) {
			bilesen_ayar_guncelle($eb_name,$eb_standart,$eklenen_id);
		}
	}
	yonlendir("bilesenler.php?islem=guncelle&bilesen=$eklenen_id");		
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
	<title><?php echo _LANG("p_bilesenler"); ?> - <?php echo $lpanel_baslik; ?></title>
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
			<?php if (!$islem) { ?>
			<h3 class="pull-left bolum-baslik"><?php echo _LANG('p_bilesenler_basligi'); ?></h3>
			<div class="clearfix mb20"></div>
			<?php $b_say=count($bilesen_alani); if ($b_say>0) { ?>
			<div class="alert alert-primary"><?php echo _LANG('p_temanizda'); ?> <?php echo $b_say; ?> <?php echo _LANG('p_bilesen_alan_yok'); ?></strong></div>
			<?php } else { ?>
			<div class="alert alert-danger"><?php echo _LANG('p_tema_bilesen_yok'); ?></strong></div>
			<?php } ?>
				<div class="row">
					<?php foreach($bilesen_alani as $yaz) { $alan_value=$yaz["id"]; $alan_baslik=$yaz["baslik"]; $alan_aciklama=$yaz["aciklama"]; $alan_resim=$yaz["resim"]; ?>
						<div class="<?php echo $div_class; ?>">
							<div class="panel panel-default">
								<div class="panel-heading"><?php echo e($alan_baslik); ?></div>
								<table class="table table-bordered table-hover">
									<tbody>
										<?php if ($alan_resim) { ?>
										<tr>
											<td colspan="2"><a href="bilesenler.php?islem=goruntule&alan=<?php echo $alan_value; ?>"><img src="<?php echo $alan_resim; ?>" class="img-responsive" /></a></td>
										</tr>										
										<?php } ?>
										<tr>
											<th width="110"><?php echo _LANG('p_bilesen_alani'); ?></th>
											<td><span class="label label-success" style="font-size:12px;"><?php echo $alan_value; ?></span></td>
										</tr>
										<?php if ($alan_aciklama) { ?><tr>
											<th width="110"><?php echo _LANG('p_aciklama'); ?></th>
											<td><?php echo $alan_aciklama; ?></td>
										</tr><?php } ?>
										<tr>
											<th><?php echo _LANG('p_bilesen_sayisi'); ?></th>
											<td><?php $sql=mysqli_query($baglan,"select * from bilesenler where alan='$alan_value'"); echo mysqli_num_rows($sql); ?></td>
										</tr>
									</tbody>
								</table>
								<div class="panel-footer">
									<div class="row">
										<div class="col-md-41">
										<?php if (count($bilesen)>0) { ?>
											<form action="" method="post">
												<div class="input-group">
												  <select name="bilesen_<?php echo $alan_value; ?>" class="form-control">
													<?php foreach ($bilesen as $x) {$bilesen_value=$x["id"]; $bilesen_baslik=$x["baslik"]; ?>
													<option value="<?php echo e($bilesen_value); ?>"><?php echo e($bilesen_baslik); ?></option>
													<?php } ?>
												  </select>
												  <span class="input-group-btn"><button type="submit" name="ekle_buton" class="btn btn-default" value="<?php echo $alan_value; ?>"><?php echo _LANG('gen_add'); ?></button></span>
												</div>									
											</form>	
										<?php } ?>
										</div>
										<div class="col-md-19">
											<a href="bilesenler.php?islem=goruntule&alan=<?php echo $alan_value; ?>" class="btn btn-primary btn-block"><?php echo _LANG('gen_view'); ?></a>
										</div>
									</div>
								</div>
							</div>
						</div>
					<?php } ?>
				</div>
			<?php } else if ($islem=="guncelle") { ?>
			<h3 class="pull-left bolum-baslik"><?php echo _LANG('p_bilesen_guncelle'); ?></h3>
			<a href="bilesenler.php" class="btn btn-primary"><?php echo _LANG('p_bilesen_anasayfa'); ?></a>
			<a href="bilesenler.php?islem=goruntule&alan=<?php echo $bilesen_alani; ?>" class="btn btn-default"><?php echo _LANG("p_duzenlemekten_vazgec"); ?></a>
			<div class="clearfix mb20"></div>
			<div class="row">
				<form action="" method="post" enctype="multipart/form-data">
				<div class="col-md-30">
					<div class="panel panel-default">
						<div class="panel-heading"><?php echo _LANG('p_bilesen_alani_k'); ?> <?php echo $bilesen_alani; ?></div>
						<table class="table table-bordered table-hover">
							<thead>
								<tr>
									<th>#</th>
									<th><?php echo _LANG('p_bilesen'); ?></th>
									<th><?php echo _LANG('p_baslik'); ?></th>
									<th><?php echo _LANG('p_sira'); ?></th>
									<th><?php echo _LANG('p_islemler'); ?></th>
								</tr>
							</thead>
							<tbody>
							<?php $l_sql=mysqli_query($baglan,"select * from bilesenler where alan='$bilesen_alani' order by sira asc");
							while ($l_bilesen_dizi=mysqli_fetch_array($l_sql)) {
								$l_id=$l_bilesen_dizi["id"];
								$l_bilesen=$l_bilesen_dizi["bilesen"];
								$l_baslik=$l_bilesen_dizi["baslik"];
								$l_sira=$l_bilesen_dizi["sira"];
							?>					
								<tr <?php if ($id==$l_id) {echo "class='success'";} ?>>
									<th><?php echo $l_id; ?></th>
									<td><?php echo e($l_bilesen); ?></td>
									<td><?php if ($l_baslik) {echo e($l_baslik);} else {echo "<span class='text-muted'>" . _LANG('p_unnamed') . "</span>";} ?></td>
									<td><?php echo $l_sira; ?></td>
									<td>
										<!-- Split button -->
										<div class="btn-group">
										  <a href="bilesenler.php?islem=guncelle&bilesen=<?php echo $l_id; ?>" class="btn btn-xs btn-primary"><i class="fa fa-cogs" aria-hidden="true"></i><?php echo _LANG('p_ayarlar'); ?></a>
										  <button type="button" class="btn btn-primary btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
											<span class="caret"></span>
											<span class="sr-only"><?php echo _LANG('p_toggle_dropdown'); ?></span>
										  </button>
										  <ul class="dropdown-menu dropdown-menu-right">
											<li><a href="bilesenler.php?islem=sil&bilesen=<?php echo $l_id; ?>&alan=<?php echo $bilesen_alani; ?>" onclick="return confirm('<?php if ($l_baslik) {echo ejs($l_baslik)." - ";} echo ejs($l_bilesen); ?> <?php echo _LANG("p_bilesen_sil_onay"); ?>')"><i class="fa fa-trash fa-fw" aria-hidden="true"></i><?php echo _LANG('p_sil'); ?></a></li>
										  </ul>
										</div>
									</td>
								</tr>
							<?php } ?>
							</tbody>
						</table>
					</div>
				</div>
				<div class="col-md-30">
					<div class="alert alert-info"><strong><?php if (bilgi_getir("bilesenler","baslik",$id)) {echo bilgi_getir("bilesenler","baslik",$id)." - ";} echo $bilesen_value; ?></strong> <?php echo _LANG("p_bilesen_duzenleniyor"); ?> <a href="bilesenler.php?islem=goruntule&alan=<?php echo $bilesen_alani; ?>" class="btn btn-beyaz btn-xs pull-right"><?php echo _LANG('gen_cancel'); ?></a></div>
						<div class="well bilesen_liste">
							<div class="row">
								<div class="col-md-30">
								<?php form_elemani("text","baslik",_LANG('p_bilesen_basligi'),bilgi_getir("bilesenler","baslik",$id)); ?>
								</div>
								<div class="col-md-30">
								<?php form_elemani("text","sira",_LANG('p_sira'),bilgi_getir("bilesenler","sira",$id)); ?>
								</div>					
							</div>
							<?php 
							$ayar=$bilesen[$bilesen_value]["ayarlar"];
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
								<div class="panel panel-default" style="border-top:0;border-radius:0;box-shadow:none;">
								<?php if ($tab_sayisi==0) {echo "<div class='panel-heading'>" . _LANG('p_component_settings') . "</div>"; } ?>
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
								// Tab hidden input
								form_elemani("hidden","aktif_tab",_LANG('p_aktif_tab'));
								echo "</div>";
								?>
								</div>
							</div>
							<?php } ?>	
							<button type="submit" name="guncelle_buton" class="btn btn-primary" value="<?php echo _LANG('general_update'); ?>"><i class="fa fa-pencil"></i><?php echo _LANG('p_guncelle'); ?></button>						
							<button type="submit" name="sil_buton" class="btn btn-danger pull-right" value="<?php echo _LANG('gen_delete'); ?>" onclick="return confirm('<?php echo _LANG('p_component_remove_confirm'); ?>')"><i class="fa fa-trash"></i><?php echo _LANG('p_sil'); ?></button>
						</div>
					</div>
				</form>
			</div>
			<?php } else if ($islem=="goruntule") { ?>
			<?php 
			foreach ($bilesen_alani_kopya as $ba) {
				$ba_id=$ba["id"];
				$ba_baslik=$ba["baslik"];
				if ($ba_id==$bilesen_alani) {
					$yaz_baslik=$ba_baslik;
				}
			}
			
			?>
			<h3 class="pull-left bolum-baslik text-uppercase"><?php echo $yaz_baslik; ?></h3>
			<a href="bilesenler.php" class="btn btn-primary"><?php echo _LANG('p_bilesen_anasayfa'); ?></a>
			<div class="clearfix mb20"></div>
			<div class="row">
				<div class="col-md-30">
					<div class="panel panel-default">
						<div class="panel-heading"><?php echo _LANG('p_bilesen_alani_k'); ?> <?php echo $bilesen_alani; ?></div>
						<table class="table table-bordered table-hover">
							<thead>
								<tr>
									<th>#</th>
									<th><?php echo _LANG('p_bilesen'); ?></th>
									<th><?php echo _LANG('p_baslik'); ?></th>
									<th><?php echo _LANG('p_sira'); ?></th>
									<th><?php echo _LANG('p_islemler'); ?></th>
								</tr>
							</thead>
							<tbody>
							<?php $l_sql=mysqli_query($baglan,"select * from bilesenler where alan='$bilesen_alani' order by sira asc");
							$l_say=mysqli_num_rows($l_sql);
							if ($l_say>0) {
							while ($l_bilesen_dizi=mysqli_fetch_array($l_sql)) {
								$l_id=$l_bilesen_dizi["id"];
								$l_bilesen=$l_bilesen_dizi["bilesen"];
								$l_baslik=$l_bilesen_dizi["baslik"];
								$l_sira=$l_bilesen_dizi["sira"];
							?>					
								<tr <?php if (isset($id) and $id==$l_id) {echo "class='success'";} ?>>
									<th><?php echo $l_id; ?></th>
									<td><?php echo e($l_bilesen); ?></td>
									<td><?php if ($l_baslik) {echo e($l_baslik);} else {echo "<span class='text-muted'>" . _LANG('p_unnamed') . "</span>";} ?></td>
									<td><?php echo $l_sira; ?></td>
									<td>
										<!-- Split button -->
										<div class="btn-group">
										  <a href="bilesenler.php?islem=guncelle&bilesen=<?php echo $l_id; ?>" class="btn btn-xs btn-primary"><i class="fa fa-cogs" aria-hidden="true"></i><?php echo _LANG('p_ayarlar'); ?></a>
										  <button type="button" class="btn btn-primary btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
											<span class="caret"></span>
											<span class="sr-only"><?php echo _LANG('p_toggle_dropdown'); ?></span>
										  </button>
										  <ul class="dropdown-menu dropdown-menu-right">
											<li><a href="bilesenler.php?islem=sil&bilesen=<?php echo $l_id; ?>&alan=<?php echo $bilesen_alani; ?>" onclick="return confirm('<?php if ($l_baslik) {echo ejs($l_baslik)." - ";} echo ejs($l_bilesen); ?> <?php echo _LANG("p_bilesen_sil_onay"); ?>')"><i class="fa fa-trash fa-fw" aria-hidden="true"></i><?php echo _LANG('p_sil'); ?></a></li>
										  </ul>
										</div>
									</td>
								</tr>
							<?php } } else {echo "<tr>
								<td colspan='5'>"._LANG('p_bilesen_yok')."</td>
							</tr>";} ?>
							</tbody>
						</table>
					</div>
				</div>
				<div class="col-md-30">
					<?php if (count($bilesen)>0) { foreach ($bilesen as $x) {@$bilesen_value=$x["id"]; @$bilesen_baslik=$x["baslik"]; @$bilesen_aciklama=$x["aciklama"]; @$bilesen_resim=$x["resim"]; ?>
						<div class="well well_limon">
							<div class="bilesen_liste_ust">
								<h4 class="pull-left"><?php echo e($bilesen_baslik); ?></h4>
								<?php if ($bilesen_resim) { ?><a href="<?php echo $bilesen_resim; ?>" target="_blank" style="margin-left:15px;" data-toggle="tooltip" title="<?php echo _LANG("p_bilesen_gorseli"); ?>" class="bilesen_resim"><i class="fa fa-photo mr0"></i></a><?php } ?> <a href="bilesenler.php?islem=ekle&alan=<?php echo $bilesen_alani; ?>&bilesen=<?php echo $bilesen_value; ?>" class="pull-right btn btn-primary btn-xs"><?php echo _LANG('gen_add'); ?></a>
								<div class="clearfix"></div>
							</div>
							<?php if ($bilesen_aciklama) { ?><p><?php echo $bilesen_aciklama; ?> </p><?php } ?>
							</div>
					<?php } } else {echo "<div class='alert alert-danger'>" . _LANG('p_no_component_to_add') . "</div>";} ?>
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