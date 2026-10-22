<?php
include("../loader.php");
yonetici_degilse_yonlendir($oturum_id,ayar_getir("site_url")."/$admin_dizini/giris-yap.php");
@$tip=$_GET["tip"];
@$islem=$_GET["islem"];
@$id=$_GET["id"];
@$eb_kategori_id=$_GET["id"];
@$sinif=$_GET["sinif"];
@$get_alt=$_GET["alt"];

if (!$tip) {
	yonlendir(site_url()."/$admin_dizini/");
}
$tip_ozellikleri=$icerik_tipleri[$tip];
$tip_baslik=$tip_ozellikleri["baslik"];
$tip_baslik_cogul=$tip_ozellikleri["baslik_cogul"];
@$tip_kategori=$tip_ozellikleri["kategori"];
@$tip_kategori_ortak=$tip_ozellikleri["kategori_ortak"];
if (!$tip_ozellikleri) {
	yonlendir(site_url()."/$admin_dizini/kategoriler.php");
}
if ($tip_kategori and $tip_kategori_ortak) {
	yonlendir(site_url()."/$admin_dizini/kategoriler.php?tip=$tip_kategori_ortak");
}
if ($sinif=="kategori" or !$sinif or !isset($tip_ozellikleri["ekstra_siniflar"][$sinif])) {
	/* Gecersiz sinif verildiginde varsayilan "kategori" kullanilir;
	 * aksi halde $sinif_name NULL kaliyor ve NOT NULL kolonlara
	 * NULL baglandigi icin kayit eklenemiyordu. */
	$sinif_name="kategori";
	$sinif_baslik="Kategori";
	$sinif_baslik_cogul="Kategoriler";
	@$tip_kategori_ekstra_bilgiler=$tip_ozellikleri["kategori_ekstra_bilgiler"];
} else {
	$sinif_name=$tip_ozellikleri["ekstra_siniflar"][$sinif]["id"];
	$sinif_baslik=$tip_ozellikleri["ekstra_siniflar"][$sinif]["baslik"];
	$sinif_baslik_cogul=$tip_ozellikleri["ekstra_siniflar"][$sinif]["baslik_cogul"];
	$tip_kategori_ekstra_bilgiler=$tip_ozellikleri["ekstra_siniflar"][$sinif]["ekstra_bilgiler"];
}

if (!@$tip_kategori_ekstra_bilgiler) {
	$tip_kategori_ekstra_bilgiler=array();
}

$toplam_kategori_sayisi=mysqli_num_rows(mysqli_query($baglan,"select id from kategoriler where tip='$tip' and sinif='$sinif_name'"));
if ($toplam_kategori_sayisi<0) {
	$kategori_ekle_stil=1;
} else {
	$kategori_ekle_stil=2;
}

$kategori_baslik=kategori_adi_getir($id);
$kategori_ust_kategori=bilgi_getir("kategoriler","ust_kategori",$id);
@$buton=$_POST["buton"];
if ($buton) {
	// Desteklenen dilleri al
	global $desteklenen_diller;
	$diller = $desteklenen_diller;
	$dil_kodlari = array_keys($diller);
	
	// Çeviri alanları
	$cevir_alanlar = array('baslik', 'aciklama', 'seo_title', 'seo_aciklama');
	
	// Ana dil (Türkçe) değerleri - kategoriler tablosuna ve ana anahtara kaydedilecek
	$baslik = addslashes(trim($_POST["baslik"]));
	$ust_kategori = $_POST["ust_kategori"];
	$aciklama = addslashes($_POST["aciklama"]);
	$seo_title = addslashes($_POST["seo_title"]);
	$seo_aciklama = addslashes($_POST["seo_aciklama"]);
	$url = addslashes(trim($_POST["url"]));
	if ($url=="") {
		$url = url_olustur($baslik);
	}
	
	if ($islem!="guncelle") {
		if ($baslik!="") {
			if (!$ust_kategori) {
				$ust_kategori = 0;
			}
			$ekle = mysqli_prepare($baglan, "insert into kategoriler set baslik=?, url=?, ust_kategori=?, tip=?, sinif=?, icerik=0");
	$ust_kategori_int=(int)$ust_kategori;
	$sinif_name_str=(string)$sinif_name;
	mysqli_stmt_bind_param($ekle,"ssiss",$baslik,$url,$ust_kategori_int,$tip,$sinif_name_str);
	mysqli_stmt_execute($ekle);
	mysqli_stmt_close($ekle);
			$eklenen_id = mysqli_insert_id($baglan);
			
			// Çeviri alanlarını kaydet (Türkçe + diğer diller)
			foreach ($cevir_alanlar as $alan) {
				foreach ($dil_kodlari as $dil_kod) {
					$input_name = ($dil_kod === 'tr') ? $alan : $alan . '_' . $dil_kod;
					$anahtar = ($dil_kod === 'tr') ? $alan : $alan . '_' . $dil_kod;
					$deger = isset($_POST[$input_name]) ? addslashes($_POST[$input_name]) : '';
					if ($deger !== '') {
						ekstra_bilgi_guncelle($anahtar, $deger, $eklenen_id, "kategori");
					}
				}
			}
			
			foreach ($tip_kategori_ekstra_bilgiler as $yaz => $x) {
				$eb_type = $x["tip"];
				$eb_name = $x["id"];
				$deger = $_POST["$eb_name"];
				$mevcut_deger = ekstra_bilgi_getir($eb_name, $eklenen_id, "kategori");
				if ($eb_type!="multiple_checkbox" and $eb_type!="upload") {
					
					if ($eb_type=="location") {
						$lat = $_POST["map_$eb_name-lat"];
						$lon = $_POST["map_$eb_name-lon"];
						$radius = $_POST["map_$eb_name-radius"];
						$zoom = $_POST["map_$eb_name-zoom"];
						$deger = "$lat,$lon,$radius,$zoom";
					}
				
					if ($deger!="") {
						ekstra_bilgi_guncelle($eb_name, $deger, $eklenen_id, "kategori");
					}
				}
				else if ($eb_type=="multiple_checkbox") {
					foreach ($deger as $yaz) {	
						$diziden_cikart .= "($yaz),";
					}
					if ($diziden_cikart!="") {
						ekstra_bilgi_guncelle($eb_name, $diziden_cikart, $eklenen_id, "kategori");
					}
					$diziden_cikart = null;
				}
				else if ($eb_type=="upload") {
					$eb_boyutlar = $x["boyutlar"];
					$eb_dizin = $x["dizin"];
					$eb_orijinali_sil = $x["orijinali_sil"];
					$eb_gecerli_boyut = $x["gecerli_boyut"];		
					
					// v2 resim upload remove güncellemesi başladı.
					$secenekler = array();
					if (is_array($eb_boyutlar)) {
						$secenekler["boyutlar"] = $eb_boyutlar;
					}
					if ($eb_dizin) {
						$secenekler["dizin"] = $eb_dizin;
					}
					if ($eb_orijinali_sil) {
						$secenekler["orijinali_sil"] = $eb_orijinali_sil;
					}
					if ($eb_gecerli_boyut) {
						$secenekler["cikti"] = $eb_gecerli_boyut;
					}
					$resim_dizisi = $_FILES["$eb_name"];
					$resim_adi = $resim_dizisi["name"];
					if ($resim_adi!="") {
						$resim_url = yukle($resim_dizisi, $secenekler);
						$mevcut_deger = ekstra_bilgi_getir($eb_name, $eklenen_id, "kategori");
						ekstra_bilgi_guncelle($eb_name, $resim_url, $eklenen_id, "kategori");
					}
				}
			}
		$_SESSION["mesaj"] = array(_LANG('p_adi_ile_kategori_eklendi'), array($baslik), "success");
		$_SESSION["ust_kategori_hazir"] = $ust_kategori;
		
		if ($get_alt) {
			yonlendir("kategoriler.php?tip=$tip&sinif=$sinif_name&alt=$get_alt");
		} else {
			yonlendir("kategoriler.php?tip=$tip&sinif=$sinif_name");
		}
	}
	} else { 
	// $islem="guncelle"
		if ($baslik!="") {
			$guncelle = mysqli_prepare($baglan, "update kategoriler set baslik=?, url=?, ust_kategori=? where id=?");
	$ust_kategori_int=(int)$ust_kategori;
	$guncelle_id_int=(int)$id;
	mysqli_stmt_bind_param($guncelle,"ssii",$baslik,$url,$ust_kategori_int,$guncelle_id_int);
	mysqli_stmt_execute($guncelle);
	mysqli_stmt_close($guncelle);
			
			// Çeviri alanlarını güncelle
			foreach ($cevir_alanlar as $alan) {
				foreach ($dil_kodlari as $dil_kod) {
					$input_name = ($dil_kod === 'tr') ? $alan : $alan . '_' . $dil_kod;
					$anahtar = ($dil_kod === 'tr') ? $alan : $alan . '_' . $dil_kod;
					$deger = isset($_POST[$input_name]) ? addslashes($_POST[$input_name]) : '';
					$mevcut = ekstra_bilgi_getir($anahtar, $id, "kategori");
					if ($deger !== $mevcut) {
						if ($deger !== '') {
							ekstra_bilgi_guncelle($anahtar, $deger, $id, "kategori");
						} else {
							$b_id = ekstra_bilgi_id_getir($anahtar, $id, "kategori");
							ekstra_bilgi_sil($b_id);
						}
					}
				}
			}
			
			foreach ($tip_kategori_ekstra_bilgiler as $yaz => $x) {
				$eb_type = $x["tip"];
				$eb_name = $x["id"];
				$deger = $_POST["$eb_name"];
				$mevcut_deger = ekstra_bilgi_getir($eb_name, $id, "kategori");
				if ($eb_type!="multiple_checkbox" and $eb_type!="upload") {
					if ($eb_type=="location") {
						$lat = $_POST["map_$eb_name-lat"];
						$lon = $_POST["map_$eb_name-lon"];
						$radius = $_POST["map_$eb_name-radius"];
						$zoom = $_POST["map_$eb_name-zoom"];
						$deger = "$lat,$lon,$radius,$zoom";
					}
					if ($deger!=$mevcut_deger) {
						ekstra_bilgi_guncelle($eb_name, $deger, $id, "kategori");
					}
				}
				else if ($eb_type=="multiple_checkbox") {
					foreach ($deger as $yaz) {	
						$diziden_cikart .= "($yaz),";
					}
					if ($diziden_cikart!=$mevcut_deger) {
						ekstra_bilgi_guncelle($eb_name, $diziden_cikart, $id, "kategori");
					}
					$diziden_cikart = null;
				}
				else if ($eb_type=="upload") {
					$eb_boyutlar = $x["boyutlar"];
					$eb_dizin = $x["dizin"];
					$eb_orijinali_sil = $x["orijinali_sil"];
					$eb_gecerli_boyut = $x["gecerli_boyut"];		
					
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
						$mevcut_deger=ekstra_bilgi_getir($eb_name,$id,"kategori");
						ekstra_bilgi_guncelle($eb_name,$resim_url,$id,"kategori");
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
				$mevcut_deger=ekstra_bilgi_getir($name,$id,"kategori");
				if ($eb_gecerli_boyut) {
					$mevcut_deger=thumbnail_url("full",$mevcut_deger,$eb_gecerli_boyut);
				}
				sil($mevcut_deger);
				foreach ($eb_boyutlar as $yaz) {
					$thumbnail_url=thumbnail_url($yaz,$mevcut_deger);
					sil($thumbnail_url);
				}
				$ekstra_bilgi_id=ekstra_bilgi_id_getir($name,$id,"kategori");
				ekstra_bilgi_sil($ekstra_bilgi_id);
			}
			// v2 resim upload remove güncellemesi bitti.			
			
			$_SESSION["mesaj"]=array(_LANG('p_adi_ile_kategori_guncellendi'), array($baslik),"success");
			$aktif_tab=$_POST["aktif_tab"];
			$ust_kategori=$_GET["alt"];
			if ($aktif_tab) {
				yonlendir("kategoriler.php?tip=$tip&sinif=$sinif_name&islem=guncelle&id=$id&tab=$aktif_tab");
			} else {
				yonlendir("kategoriler.php?tip=$tip&sinif=$sinif_name&islem=guncelle&id=$id");
			}
		}
	}
}
if ($islem=="sil") {
	$alt_kat_say=alt_kategori_sayisi($id);
	if ($alt_kat_say>0) {
		$_SESSION["mesaj"]=array(_LANG('p_kategori_alt_kategori_var'),"danger");
		$ust_var_mi=getir("kategoriler","ust_kategori",$id);
		if ($ust_var_mi!=0) {
			yonlendir("kategoriler.php?tip=$tip&sinif=$sinif_name&alt=$ust_var_mi");
		} else {
			yonlendir("kategoriler.php?tip=$tip&sinif=$sinif_name");
		}
	} else {
		$silinen_baslik=bilgi_getir("kategoriler","baslik",$id);
		$ust_var_mi=getir("kategoriler","ust_kategori",$id);
		$sil_id_int=(int)$id;
		$sql=mysqli_prepare($baglan,"delete from kategoriler where id=?");
		mysqli_stmt_bind_param($sql,"i",$sil_id_int);
		mysqli_stmt_execute($sql);
		mysqli_stmt_close($sql);
		$_SESSION["mesaj"]=array(_LANG('p_adi_ile_kategori_silindi'), array($silinen_baslik),"success");
		if ($ust_var_mi!=0) {
			$ust_kategori_alt_sayisi=mysqli_num_rows(mysqli_query($baglan,"select id from kategoriler where ust_kategori='$ust_var_mi'"));
		}
		if ($ust_var_mi!=0 and $ust_kategori_alt_sayisi>0) {
			yonlendir("kategoriler.php?tip=$tip&sinif=$sinif_name&alt=$ust_var_mi");
		} else {
			yonlendir("kategoriler.php?tip=$tip&sinif=$sinif_name");
		}
	}
}

?>
<!DOCTYPE HTML>
<html lang="<?php echo _DIL_HTML_LANG(); ?>" dir="<?php echo _DIL_HTML_YON(); ?>">
<head>
	<meta charset="UTF-8">
	<title><?php echo $sinif_baslik_cogul; ?> / <?php echo $tip_baslik; ?> - <?php echo $lpanel_baslik; ?></title>
	<?php include("inc/header-kod.php"); ?>
	<?php if ($datatable==1) { ?>
	<script>
	$(document).ready(function() {
		$('#listele_table').DataTable({
			"responsive": true,
			"lengthMenu": [ 15, 25, 50, 100 ],
			"language": {
				"decimal":        "",
				"emptyTable":     "<?php echo _LANG("p_no_result"); ?>",
				"info":           "Toplam _TOTAL_ <?php echo $sinif_name; ?> bulundu",
				"infoEmpty":      "<?php echo _LANG("p_gosterilecek_sinif_yok", array($sinif_name)); ?>",
				"infoFiltered":   "<?php echo _LANG("p_max_kayit"); ?>",
				"infoPostFix":    "",
				"thousands":      ",",
				"lengthMenu":     "<?php echo _LANG("p_gosterilecek_sinif", array($sinif_name)); ?>",
				"loadingRecords": "<?php echo _LANG("p_yukleniyor"); ?>",
				"processing":     "<?php echo _LANG("p_isleniyor"); ?>",
				"search":         "<?php echo _LANG("search"); ?>:",
				"zeroRecords":    "<?php echo _LANG("p_arama_uygun"); ?>",
				"paginate": {
					"first":      "<?php echo _LANG("p_ilk"); ?>",
					"last":       "<?php echo _LANG("p_son"); ?>",
					"next":       "<?php echo _LANG("p_sonraki"); ?>",
					"previous":   "<?php echo _LANG("p_onceki"); ?>"			
			}
			},
			"ordering": false
		});
	} );
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
			<?php if ($islem!="guncelle") { ?>
			<h3 class="pull-left bolum-baslik text-uppercase"><?php echo $sinif_baslik_cogul; ?> <small>/ <?php echo $tip_baslik; ?></small></h3>
			<a href="icerik.php?tip=<?php echo $tip; ?>" class="btn btn-default"><?php echo $tip_baslik_cogul; ?></a>
			<div class="clearfix mb25"></div>
			<?php limon(@$panel_kategori_ust_kod); ?>
			<div class="row">
				<div class="col-md-20">
					
					<?php if ($kategori_ekle_stil==1) { ?>
					<form method="post" action="" enctype="multipart/form-data">
						<div class="panel panel-default">
							<div class="panel-heading"><?php echo $sinif_baslik; ?> <?php echo _LANG('p_ekle'); ?></div>
							<div class="panel-body">
									<?php form_elemani("text","baslik",_LANG('p_baslik'),null,null,null,"required"); ?>
									<label for="url"><?php echo _LANG('p_kalici_baglanti'); ?></label>
									<div class="input-group" style="margin-bottom:15px;">
										<div class="input-group-addon"><?php if (ayar_getir("panel_sablon")=="boxed") {echo"{SITE}/$sinif_name/{ID}-";} else {echo site_url()."/$sinif_name/{ID}-";} ?></div>
										<input type="text" class="form-control" id="url" name="url">
										<div class="input-group-addon">/</div>
									</div>								
									<div class="form-group">
										<label for='ust_kategori'><?php echo _LANG('p_ust'); ?> <?php echo $sinif_baslik; ?></label>
										<select name="ust_kategori" class="form-control" id="ust_kategori">
										<option value="0"><?php echo _LANG('p_yok'); ?></option>
										<?php 
										$sql=mysqli_query($baglan,"select * from kategoriler where ust_kategori='0' and tip='$tip' and sinif='$sinif_name'");
										while ($dizi=mysqli_fetch_array($sql)) {
											$id=$dizi["id"];
											$baslik=$dizi["baslik"];
										?>
										<option value="<?php echo $id; ?>" <?php if ($id==$_SESSION["ust_kategori_hazir"]) {echo "selected='selected'";} ?>><?php echo $baslik; ?></option>
										<?php alt_kategori_listele_select($id,5,$_SESSION["ust_kategori_hazir"],$tip,$sinif_name); ?>
										<?php } ?>
										</select>
									</div>
									<?php textarea_olustur(null,"aciklama",_LANG("p_aciklama"),null,3); ?>
									<hr />
									<?php textarea_olustur(null,"seo_title","Seo Title",null,2); ?>
									<?php textarea_olustur(null,"seo_aciklama",_LANG("p_seo_aciklama"),null,3); ?>
							</div>
						</div>	
							<?php if (is_array($tip_kategori_ekstra_bilgiler)) { if (count($tip_kategori_ekstra_bilgiler)>0) { 
							echo "<ul class='nav nav-tabs' role='tablist'>";
								$say=1;
								$tab_sayisi=0;
								foreach ($tip_kategori_ekstra_bilgiler as $yaz => $x) {
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
								<?php if ($tab_sayisi==0) {echo "<div class='panel-heading'>Ekstra Bilgiler</div>"; } ?>
								<div class="panel-body">
								<?php
								echo "<div class='tab-content'>";
								$tab_say=1;
								foreach ($tip_kategori_ekstra_bilgiler as $yaz => $x) {
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
								$eb_yaz=ekstra_bilgi_getir($eb_name,$eb_kategori_id,"kategori");
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
								echo "</div>";
								?>
								</div>
							</div>
						<?php } } ?>
						<?php limon(@$panel_kategori_form_kod); ?>
						<?php form_buton("buton",_LANG('p_ekle'),"primary btn-lg"); ?>
					</form>
					<?php } else { ?>
					<?php if (!$get_alt) { ?>
					<div class="alert alert-info">
						<i class="fa fa-info-circle"></i><?php echo _LANG("p_alt_kategori_ekle_tip"); ?>
					</div>
					<?php } else { ?>
					<div class="alert alert-info">
						<i class="fa fa-info-circle"></i><?php echo _LANG("p_kategori_altinda_islem", array(getir("kategoriler","baslik",$get_alt))); ?>
					</div>
					<?php } ?>
					<form method="post" action="" enctype="multipart/form-data">
						<div class="panel panel-default">
							<div class="panel-heading"><?php if (!$get_alt) {echo "Ana $sinif_baslik ekle";} else {echo "Alt $sinif_baslik ekle";} ?></div>
							<div class="panel-body">
								<?php
								// Desteklenen dilleri al
								global $desteklenen_diller;
								$diller = $desteklenen_diller;
								$dil_kodlari = array_keys($diller);
								
								// Çeviri alanları
								$cevir_alanlar = array('baslik', 'aciklama', 'seo_title', 'seo_aciklama');
								
								// Mevcut değerleri al (güncelleme modunda)
								$mevcut_degerler = array();
								if ($islem == "guncelle" && $id) {
									foreach ($cevir_alanlar as $alan) {
										foreach ($dil_kodlari as $dil_kod) {
											$anahtar = ($dil_kod === 'tr') ? $alan : $alan . '_' . $dil_kod;
											$mevcut_degerler[$anahtar] = ekstra_bilgi_getir($anahtar, $id, "kategori");
										}
									}
								}
								?>
								
									<!-- Dil sekmeleri -->
									<ul class="nav nav-tabs dil-sekmeleri" role="tablist" style="margin-bottom:15px;">
										<?php foreach ($dil_kodlari as $dil_kod) {
											$dil_bilgi = $diller[$dil_kod];
											$active = ($dil_kod == 'tr') ? 'class="active"' : '';
											echo "<li role='presentation' $active><a href='#kategori_dil_{$dil_kod}' aria-controls='kategori_dil_{$dil_kod}' role='tab' data-toggle='tab' title='{$dil_bilgi['ad']}'>{$dil_bilgi['bayrak']}</a></li>";
										} ?>
									</ul>
									<div class="tab-content dil-icerikleri">
										<?php foreach ($dil_kodlari as $dil_kod) {
											$active = ($dil_kod == 'tr') ? 'active' : '';
											echo "<div role='tabpanel' class='tab-pane $active' id='kategori_dil_{$dil_kod}'>";
											
											// Bu dil için değerleri al
											$baslik_val = $mevcut_degerler[$dil_kod === 'tr' ? 'baslik' : 'baslik_' . $dil_kod];
											$aciklama_val = $mevcut_degerler[$dil_kod === 'tr' ? 'aciklama' : 'aciklama_' . $dil_kod];
											$seo_title_val = $mevcut_degerler[$dil_kod === 'tr' ? 'seo_title' : 'seo_title_' . $dil_kod];
											$seo_aciklama_val = $mevcut_degerler[$dil_kod === 'tr' ? 'seo_aciklama' : 'seo_aciklama_' . $dil_kod];
											
											// Input name suffix
											$name_suffix = ($dil_kod === 'tr') ? '' : '_' . $dil_kod;
											
											// Başlık (sadece Türkçe için required)
											$required_attr = ($dil_kod === 'tr') ? 'required' : '';
											?>
											<div class="form-group">
												<label><?php echo _LANG('p_baslik'); ?> <small class="text-muted">(<?php echo htmlspecialchars($dil_bilgi['ad'], ENT_QUOTES, 'UTF-8'); ?>)</small></label>
												<input type="text" class="form-control" name="baslik<?php echo $name_suffix; ?>" value="<?php echo htmlspecialchars($baslik_val); ?>" <?php echo $required_attr; ?>>
											</div>
											
											<?php if ($dil_kod === 'tr') { ?>
											<!-- URL sadece ana dilde -->
											<label for="url"><?php echo _LANG('p_kalici_baglanti'); ?></label>
											<div class="input-group" style="margin-bottom:15px;">
												<div class="input-group-addon"><?php if (ayar_getir("panel_sablon")=="boxed") {echo"{SITE}/$sinif_name/{ID}-";} else {echo site_url()."/$sinif_name/{ID}-";} ?></div>
												<input type="text" class="form-control" id="url" name="url">
												<div class="input-group-addon">/</div>
											</div>
											
											<?php if ($get_alt) { ?>
											<div class="form-group">
												<label for='ust_kategori'><?php echo _LANG('p_ust'); ?> <?php echo $sinif_baslik; ?></label>
												<select name="ust_kategori" class="form-control" id="ust_kategori" readonly>
												<option value="<?php echo $get_alt; ?>"><?php echo getir("kategoriler","baslik",$get_alt); ?></option>
												</select>
											</div>
											<?php } ?>
											<?php } ?>
											
											<!-- Açıklama -->
											<div class="form-group">
												<label><?php echo _LANG("p_aciklama"); ?> <small class="text-muted">(<?php echo htmlspecialchars($dil_bilgi['ad'], ENT_QUOTES, 'UTF-8'); ?>)</small></label>
												<textarea class="form-control" name="aciklama<?php echo $name_suffix; ?>" rows="3"><?php echo htmlspecialchars($aciklama_val); ?></textarea>
											</div>
											
											<?php if ($dil_kod === 'tr') { ?>
											<hr />
											<?php } ?>
											
											<!-- SEO Title -->
											<div class="form-group">
												<label><?php echo _LANG('p_seo_title'); ?> <small class="text-muted">(<?php echo htmlspecialchars($dil_bilgi['ad'], ENT_QUOTES, 'UTF-8'); ?>)</small></label>
												<textarea class="form-control" name="seo_title<?php echo $name_suffix; ?>" rows="2"><?php echo htmlspecialchars($seo_title_val); ?></textarea>
											</div>
											
											<!-- SEO Açıklama -->
											<div class="form-group">
												<label><?php echo _LANG("p_seo_aciklama"); ?> <small class="text-muted">(<?php echo htmlspecialchars($dil_bilgi['ad'], ENT_QUOTES, 'UTF-8'); ?>)</small></label>
												<textarea class="form-control" name="seo_aciklama<?php echo $name_suffix; ?>" rows="3"><?php echo htmlspecialchars($seo_aciklama_val); ?></textarea>
											</div>
											
											<?php echo "</div>";
										} ?>
									</div>
							</div>
						</div>	
							<?php if (count($tip_kategori_ekstra_bilgiler)>0) { 
							echo "<ul class='nav nav-tabs' role='tablist'>";
								$say=1;
								$tab_sayisi=0;
								foreach ($tip_kategori_ekstra_bilgiler as $yaz => $x) {
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
								<?php if ($tab_sayisi==0) {echo "<div class='panel-heading'>Ekstra Bilgiler</div>"; } ?>
								<div class="panel-body">
								<?php
								echo "<div class='tab-content'>";
								$tab_say=1;
								foreach ($tip_kategori_ekstra_bilgiler as $yaz => $x) {
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
								$eb_yaz=ekstra_bilgi_getir($eb_name,$eb_kategori_id,"kategori");
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
								echo "</div>";
								?>
								</div>
							</div>
						<?php } ?>
						<?php limon(@$panel_kategori_form_kod); ?>
						<?php form_buton("buton",_LANG('p_ekle'),"primary btn-lg"); ?>
					</form>					

					<?php } ?>
					
				</div>
				<div class="clearfix mb20 visible-xs"></div>
				<div class="col-md-40">
				<?php limon(@$panel_kategori_sag_kod); ?>
				
				<?php if ($kategori_ekle_stil==1) { ?>
					<div class="table-responsive">
					<table id="listele_table" class="table table-bordered table-hover" data-page-length='15'>
						<thead>
							<tr>
								<th width="10">#</th>
								<th><?php echo _LANG('p_baslik'); ?></th>
								<th class="visible-md visible-lg"><?php echo _LANG('p_aciklama'); ?></th>
								<th><?php echo _LANG('p_icerik_sayisi'); ?></th>
								<th width="135"><?php echo _LANG('p_islemler'); ?></th>
							</tr>
						</thead>
						<tbody>
						<?php 
						$sql=mysqli_query($baglan,"select * from kategoriler where ust_kategori='0' and tip='$tip' and sinif='$sinif_name' order by icerik desc, baslik asc");
						while ($dizi=mysqli_fetch_array($sql,MYSQLI_ASSOC)) {
						$id=$dizi["id"];
						$alt_kategori_sayisi=alt_kategori_sayisi($id);					
						$baslik=$dizi["baslik"];
						$icerik=$dizi["icerik"];
						$aciklama=ozet_olustur(ekstra_bilgi_getir("aciklama",$id,"kategori",$tip),7);
						if ($aciklama) {$aciklama.="...";}
						?>
						<tr> 
							<th><?php echo $id; ?></th>
							<td><a href="kategoriler.php?tip=<?php echo $tip; ?>&sinif=<?php echo $sinif_name; ?>&islem=guncelle&id=<?php echo $id; ?>"><?php echo $baslik; ?></a> <?php if ($alt_kategori_sayisi>0) {echo "<span style='font-size:11px;color:#bbb;margin-left:10px;'>($alt_kategori_sayisi alt kategori)</span>";} ?></td>
							<td class="visible-md visible-lg"><?php echo $aciklama; ?></td>
							<td><?php echo $icerik; ?></td>
							<td>
								<!-- Split button -->
								<div class="btn-group">
								  <a href="kategoriler.php?tip=<?php echo $tip; ?>&sinif=<?php echo $sinif_name; ?>&islem=guncelle&id=<?php echo $id; ?>" class="btn btn-xs btn-primary"><i class="fa fa-pencil" aria-hidden="true"></i><?php echo _LANG('p_guncelle'); ?></a>
								  <button type="button" class="btn btn-primary btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
									<span class="caret"></span>
									<span class="sr-only"><?php echo _LANG('p_toggle_dropdown'); ?></span>
								  </button>
								  <ul class="dropdown-menu dropdown-menu-right">
									<li><a href="<?php echo kategori_link_getir($id); ?>" target="_blank"><i class="fa fa-external-link fa-fw" aria-hidden="true"></i><?php echo _LANG('p_goruntule'); ?></a></li>
									<li><a href="kategoriler.php?tip=<?php echo $tip; ?>&sinif=<?php echo $sinif_name; ?>&islem=sil&id=<?php echo $id; ?>" onclick="return confirm('<?php echo $baslik; ?> <?php echo _LANG("p_kategori_sil_onay"); ?>')"><i class="fa fa-trash fa-fw" aria-hidden="true"></i><?php echo _LANG('p_sil'); ?></a></li>
								  </ul>
								</div>
							</td>
						</tr>
						<?php alt_kategori_listele($id,30,$tip,$sinif_name);?>
						<?php } ?>
						</tbody>
					</table>				
					</div>
				<?php } else { ?>
					<div class="table-responsive">
					<table class="table table-bordered table-hover">
						<thead>
							<tr>
								<th width="10">#</th>
								<th><?php echo _LANG('p_baslik'); ?></th>
								<th><?php echo _LANG('p_alt_kategoriler_sutun'); ?></th>
								<th><?php echo _LANG('p_icerik_sayisi'); ?></th>
								<th width="135"><?php echo _LANG('p_islemler'); ?></th>
							</tr>
						</thead>
						<tbody>
						
						<?php 
						@$ust_kategori=$_GET["alt"];
						if (!@$ust_kategori) {
							$ust_kategori=0;
						} ?>
						<?php if ($ust_kategori!=0) { 
						$kategori_baslik=getir("kategoriler","baslik",$ust_kategori);
						$kategori_ust_kategori=getir("kategoriler","ust_kategori",$ust_kategori);
						?>
						<tr>
							<th style="padding:0;position:relative;"><a href="kategoriler.php?tip=<?php echo $tip; ?>&alt=<?php echo $kategori_ust_kategori; ?>"  class="btn btn-xs btn-default btn-block" style="position:absolute;font-size:16px;line-height:16px;color:#333;padding:9px;border:0;"><i class="fa fa-arrow-left mr0"></i></a></th>
							<th colspan="4"><?php echo _LANG('p_kategori_kolon'); ?> <?php echo getir("kategoriler","baslik",$ust_kategori); ?></th>
						</tr>
						<?php } ?>
						<?php 
						$sql=mysqli_query($baglan,"select * from kategoriler where ust_kategori='$ust_kategori' and tip='$tip' and sinif='$sinif_name' order by icerik desc, baslik asc");
						$toplam=mysqli_num_rows($sql);
						if ($toplam>0) {
						while ($dizi=mysqli_fetch_array($sql,MYSQLI_ASSOC)) {
						$id=$dizi["id"];
						$alt_kategori_sayisi=alt_kategori_sayisi($id);					
						$baslik=$dizi["baslik"];
						$icerik=$dizi["icerik"];
						if (@$aciklama) {$aciklama.="...";}
						?>
						<tr> 
							<th><?php echo $id; ?></th>
							<td><a href="kategoriler.php?tip=<?php echo $tip; ?>&sinif=<?php echo $sinif_name; ?>&islem=guncelle&id=<?php echo $id; ?>"><?php echo $baslik; ?></a> <?php if ($alt_kategori_sayisi>0) {echo "<span style='font-size:11px;color:#bbb;margin-left:10px;'>($alt_kategori_sayisi alt kategori)</span>";} ?></td>
							<td>
							<a href="kategoriler.php?tip=<?php echo $tip; ?>&sinif=<?php echo $sinif_name; ?>&alt=<?php echo $id; ?>" class="btn btn-default btn-xs"><i class="fa fa-arrow-right"></i><?php echo _LANG('p_alt_kategoriler'); ?></a>
							</td>
							<td><?php echo $icerik; ?></td>
							<td>
								<!-- Split button -->
								<div class="btn-group">
								  <a href="kategoriler.php?tip=<?php echo $tip; ?>&sinif=<?php echo $sinif_name; ?>&islem=guncelle&id=<?php echo $id; if ($ust_kategori) {echo "&alt=$ust_kategori";} ?>" class="btn btn-xs btn-primary"><i class="fa fa-pencil" aria-hidden="true"></i><?php echo _LANG('p_guncelle'); ?></a>
								  <button type="button" class="btn btn-primary btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
									<span class="caret"></span>
									<span class="sr-only"><?php echo _LANG('p_toggle_dropdown'); ?></span>
								  </button>
								  <ul class="dropdown-menu dropdown-menu-right">
									<li><a href="<?php echo kategori_link_getir($id); ?>" target="_blank"><i class="fa fa-external-link fa-fw" aria-hidden="true"></i><?php echo _LANG('p_goruntule'); ?></a></li>
									<li><a href="kategoriler.php?tip=<?php echo $tip; ?>&sinif=<?php echo $sinif_name; ?>&alt=<?php echo $id; ?>"><i class="fa fa-plus fa-fw"></i><?php echo _LANG('p_alt_kat_ekle_k'); ?></a></li>
									<li><a href="kategoriler.php?tip=<?php echo $tip; ?>&sinif=<?php echo $sinif_name; ?>&islem=sil&id=<?php echo $id; ?>" onclick="return confirm('<?php echo $baslik; ?> <?php echo _LANG("p_kategori_sil_onay"); ?>')"><i class="fa fa-trash fa-fw" aria-hidden="true"></i><?php echo _LANG('p_sil'); ?></a></li>
								  </ul>
								</div>
							</td>
						</tr>
						<?php } } else {echo "<tr>
							<td colspan='5'>" . _LANG('p_kategori_bulunamadi') . "</td>
						</tr>";} ?>
						</tbody>
					</table>				
					</div>				
				<?php } ?>
				<?php limon(@$panel_kategori_sag_alt_kod); ?>
				</div>
			</div>
			<?php } else { ?>
			<h3 class="pull-left bolum-baslik text-uppercase"><?php echo $sinif_baslik; ?> <?php echo _LANG('p_guncelle_buyuk'); ?> <small>/ <?php echo $tip_baslik; ?></small></h3>
			<?php if ($kategori_ekle_stil==2) { $ust_kategori=getir("kategoriler","ust_kategori",$id); $ust_kat_baslik=getir("kategoriler","baslik",$ust_kategori);?><a href="kategoriler.php?tip=<?php echo $tip; ?>&sinif=<?php echo $sinif_name; ?>&alt=<?php echo $ust_kategori; ?>" class="btn btn-default"><i class="fa fa-arrow-left"></i><?php echo $ust_kat_baslik; ?></a><?php } ?>
			<a href="kategoriler.php?tip=<?php echo $tip; ?>&sinif=<?php echo $sinif_name; ?>" class="btn btn-default"><?php echo $sinif_baslik_cogul; ?> <?php echo _LANG('p_menu_anasayfa'); ?></a>
			<a href="<?php echo kategori_link_getir($id); ?>" class="btn btn-default" target="_blank"><i class="fa fa-external-link mr0"></i></a>
			<div class="clearfix mb20"></div>			

				<form method="post" action="" enctype="multipart/form-data">
					<div class="panel panel-default">
						<div class="panel-body">
							<?php form_elemani("text","baslik",_LANG('p_baslik'),$kategori_baslik,null,null,"required"); ?>
							<label for="url"><?php echo _LANG('p_kalici_baglanti'); ?></label>
							<div class="input-group" style="margin-bottom:15px;">
								<div class="input-group-addon"><?php echo site_url(); ?>/<?php echo $sinif_name; ?>/<?php echo $id; ?>-</div>
								<input type="text" class="form-control" id="url" name="url" value="<?php echo bilgi_getir("kategoriler","url",$id); ?>">
								<div class="input-group-addon">/</div>
							</div>	
							<?php if ($kategori_ekle_stil==1) { ?>
								<div class="form-group">
									<label for='ust_kategori'><?php echo _LANG('p_ust'); ?> <?php echo $sinif_baslik; ?></label>
									<select name="ust_kategori" class="form-control" id="ust_kategori">
									<option value="0"><?php echo _LANG('p_yok'); ?></option>
									<?php 
									$sql=mysqli_query($baglan,"select * from kategoriler where ust_kategori='0' and tip='$tip' and sinif='$sinif_name' order by baslik asc");
									while ($dizi=mysqli_fetch_array($sql)) {
										$id=$dizi["id"];
										$baslik=$dizi["baslik"];
									?>
									<option value="<?php echo $id; ?>" <?php if ($id==$kategori_ust_kategori) {echo "selected='selected'";} ?>><?php echo $baslik; ?></option>
									<?php alt_kategori_listele_select($id,5,$kategori_ust_kategori,$tip,$sinif_name); ?>
									<?php } ?>
									</select>
								</div>
							<?php } else { ?>
								<div class="form-group">
									<label for='ust_kategori'><?php echo _LANG('p_ust'); ?> <?php echo $sinif_baslik; ?></label>
									<select name="ust_kategori" class="form-control" id="ust_kategori" readonly>
									<option value="<?php echo $kategori_ust_kategori; ?>"><?php echo getir("kategoriler","baslik",$kategori_ust_kategori); ?></option>
									</select>
								</div>								
							<?php } ?>
							
							<?php textarea_olustur(null,"aciklama",_LANG("p_aciklama"),$kategori_aciklama,3); ?>
							<hr />
							<?php textarea_olustur(null,"seo_title","Seo Title",$kategori_seo_title,2); ?>
							<?php textarea_olustur(null,"seo_aciklama",_LANG("p_seo_aciklama"),$kategori_seo_aciklama,3); ?>
						</div>
					</div>	
						<?php if (count($tip_kategori_ekstra_bilgiler)>0) { 
						echo "<ul class='nav nav-tabs' role='tablist'>";
							$say=1;
							$tab_sayisi=0;
							foreach ($tip_kategori_ekstra_bilgiler as $yaz => $x) {
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
							<?php if ($tab_sayisi==0) {echo "<div class='panel-heading'>Ekstra Bilgiler</div>"; } ?>
							<div class="panel-body">
							<?php
							echo "<div class='tab-content'>";
							$tab_say=1;
							foreach ($tip_kategori_ekstra_bilgiler as $yaz => $x) {
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
							$eb_yaz=ekstra_bilgi_getir($eb_name,$eb_kategori_id,"kategori");
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
					<?php } ?>
					<?php limon(@$panel_kategori_form_kod); ?>
					<?php form_buton("buton",_LANG('p_guncelle'),"primary"); ?>
					<?php form_elemani("hidden","aktif_tab",_LANG('p_aktif_tab')); ?>
				</form>				
			</div>
			<div class="col-md-25">
			<?php limon(@$panel_kategori_guncelle_sag_kod); ?>
			</div>
		</div>
		<?php } ?>
		</div>
		<div class="clearfix mb20"></div>
	</div>
<?php include("inc/alt.php"); ?>
<?php include("inc/footer-kod.php"); ?>	
</body>
</html>