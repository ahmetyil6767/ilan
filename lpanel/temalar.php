<?php
include("../loader.php");
yonetici_degilse_yonlendir($oturum_id,ayar_getir("site_url")."/$admin_dizini/giris-yap.php");
if(file_exists("../$temalar_dizini")) {
$dizini_ac = opendir("../$temalar_dizini"); 
$tara=scandir ("../$temalar_dizini");
$tema_sayisi=count($tara)-2;
} else {
	echo _LANG('p_tema_dizini_yok');
	exit();//İşlemi durdur
}	
@$islem=$_GET["islem"];
@$tema=$_GET["tema"];
$aktif_tema=tema_ne();
if ($islem=="ayar" and (!$tema or ($tema and $tema!=$aktif_tema))) {
	$tema=$aktif_tema;
}
@$sayfa=$_GET["sayfa"];
if ($islem=="etkinlestir") {
	$mevcut_tema=ayar_getir("tema");
	if ($tema!=$mevcut_tema) {
		ayar_guncelle("tema",$tema);
		$_SESSION["mesaj"]=array(_LANG('p_theme_enabled'),"success");
		include ("../$temalar_dizini/$tema/info.php");
		$tema_ayar=$bilgi["ayar"];
		if ($tema_ayar) {
			include("../$temalar_dizini/$tema/ayar.php");
			foreach ($tema_ayarlari as $yaz => $x) {
				$eb_name=($x["id"] ?? '');
				$eb_standart=$x["standart"];
				if ($eb_standart) {
					ayar_guncelle($tema."-".$eb_name,$eb_standart);
				}
			}
		}
		yonlendir("temalar.php");		
	}
}
if ($islem=="ayar") {
	@$buton=$_POST["buton"];
	include ("../$temalar_dizini/$tema/info.php");
	$tema_baslik=$bilgi["baslik"];
	$tema_duyurulari=array();
	include("../$temalar_dizini/$tema/ayar.php");
	
	// Desteklenen dilleri al
	global $desteklenen_diller;
	$diller = $desteklenen_diller;
	$dil_kodlari = array_keys($diller);
	
	if ($buton) {
		foreach ($tema_ayarlari as $yaz => $x) {
			$eb_type=($x["tip"] ?? '');
			$eb_name=($x["id"] ?? '');
			@$ayar_cevir = isset($x["cevir"]) ? $x["cevir"] : false;
			
			// Çeviri destekli alanlar için her dil kaydet
			if ($ayar_cevir && ($eb_type=="text" || $eb_type=="textarea" || $eb_type=="email")) {
				foreach ($dil_kodlari as $dil_kod) {
					$input_name = ($dil_kod === 'tr') ? $eb_name : $eb_name."_".$dil_kod;
					$deger = isset($_POST[$input_name]) ? $_POST[$input_name] : '';
					
					// Dil-specific anahtar
					$anahtar_dil = ($dil_kod === 'tr') ? $tema."-".$eb_name : $tema."-".$eb_name."_".$dil_kod;
					$mevcut_deger = ayar_getir($anahtar_dil);
					
					if ($deger != $mevcut_deger) {
						ayar_guncelle($anahtar_dil, $deger);
					}
				}
			}
			// Normal alanlar
			else if ($eb_type!="multiple_checkbox" and $eb_type!="upload") {
				
				$deger=$_POST["$eb_name"];
				$mevcut_deger=ayar_getir($tema."-".$eb_name);
				
				if ($eb_type=="location") {
					$lat=$_POST["map_$eb_name-lat"];
					$lon=$_POST["map_$eb_name-lon"];
					$radius=$_POST["map_$eb_name-radius"];
					$zoom=$_POST["map_$eb_name-zoom"];
					$deger="$lat,$lon,$radius,$zoom";
				}
				
				if ($deger!=$mevcut_deger) {
					ayar_guncelle($tema."-".$eb_name,$deger);
				}
			} 
			else if ($eb_type=="multiple_checkbox") {
				$deger=$_POST["$eb_name"];
				foreach ($deger as $yaz) {	
					$diziden_cikart.="($yaz),";
				}
				$mevcut_deger=ayar_getir($tema."-".$eb_name);
				if ($diziden_cikart!=$mevcut_deger) {
					ayar_guncelle($tema."-".$eb_name,$diziden_cikart);
				}
				$diziden_cikart=null;
			}
			else if ($eb_type=="upload") {
				$eb_boyutlar=($x["boyutlar"] ?? '');
				$eb_dizin=($x["dizin"] ?? '');
				$eb_orijinali_sil=($x["orijinali_sil"] ?? '');
				$eb_gecerli_boyut=($x["gecerli_boyut"] ?? '');		
				
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
				$resim_dizisi=(isset($_FILES[$eb_name]) and is_array($_FILES[$eb_name])) ? $_FILES[$eb_name] : array();
				$resim_adi=(isset($resim_dizisi["name"]) and is_array($resim_dizisi["name"])) ? $resim_dizisi["name"] : ''; if (is_array($resim_adi)) { $resim_adi = isset($resim_adi[0]) ? $resim_adi[0] : ''; }
				if ($resim_adi!="") {
					$resim_url=yukle($resim_dizisi,$secenekler);
					$mevcut_deger=tema_ayar($eb_name);
					tema_ayar_guncelle($eb_name,$resim_url);
					if ($mevcut_deger!=$resim_url) {
						if ($eb_gecerli_boyut) {
							$mevcut_deger=thumbnail_url("full",$mevcut_deger,$eb_gecerli_boyut);
						}
						sil($mevcut_deger);
						foreach ((isset($x["boyutlar"]) and is_array($x["boyutlar"])) ? $x["boyutlar"] : array() as $yaz) {
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
			$mevcut_deger=tema_ayar($name);
			if ($eb_gecerli_boyut) {
				$mevcut_deger=thumbnail_url("full",$mevcut_deger,$eb_gecerli_boyut);
			}
			sil($mevcut_deger);
			foreach ($eb_boyutlar as $yaz) {
				$thumbnail_url=thumbnail_url($yaz,$mevcut_deger);
				sil($thumbnail_url);
			}
			tema_ayar_guncelle($name,null);
		}
		// v2 resim upload remove güncellemesi bitti.
		
		$_SESSION["mesaj"]=array(_LANG('p_theme_settings_updated'),"success");
		$aktif_tab=$_POST["aktif_tab"];
		if ($aktif_tab) {
			yonlendir("temalar.php?islem=ayar&tema=$tema&tab=$aktif_tab");
		} else {
			yonlendir("temalar.php?islem=ayar&tema=$tema");
		}		
	}
}
if ($tema_destegi==0) {
	$_SESSION["mesaj"]=array(_LANG('p_theme_support_off'),"danger");
}
$datatable=ayar_getir("datatable");
$panel_sablon=ayar_getir("panel_sablon");
?>
<!DOCTYPE HTML>
<html lang="<?php echo _DIL_HTML_LANG(); ?>" dir="<?php echo _DIL_HTML_YON(); ?>">
<head>
	<meta charset="UTF-8">
	<title><?php if ($tema) {@$tema_sayfa=$_GET["sayfa"]; if ($tema_sayfa) {echo ucwords($tema_sayfa)." -";} else {include ("../$temalar_dizini/$tema/info.php");$tema_baslik=$bilgi["baslik"]; echo "$tema_baslik -";}} else {echo "Temalar -";} ?> <?php echo $lpanel_baslik; ?></title>
	<?php include("inc/header-kod.php"); ?>
	<style>
		/* Dil sekmeleri stilleri */
		.dil-sekmeleri {
			border-bottom: 1px solid #ddd;
			margin-bottom: 15px !important;
		}
		.dil-sekmeleri > li > a {
			padding: 8px 12px;
			font-size: 13px;
			min-width: 45px;
			text-align: center;
		}
		.dil-sekmeleri > li > a:hover {
			background: #f5f5f5;
		}
		.dil-sekmeleri > li.active > a,
		.dil-sekmeleri > li.active > a:hover,
		.dil-sekmeleri > li.active > a:focus {
			background: #fff;
			border: 1px solid #ddd;
			border-bottom-color: transparent;
			color: #337ab7;
			font-weight: bold;
		}
		.dil-icerikleri {
			padding: 10px 0;
			border-top: none;
		}
		.dil-icerikleri .tab-pane {
			animation: fadeIn 0.2s;
		}
		@keyframes fadeIn {
			from { opacity: 0; }
			to { opacity: 1; }
		}
		/* Dil bayrakları için */
		.dil-sekmeleri .bayrak_svg {
			width: 20px;
			height: 14px;
			vertical-align: middle;
			border-radius: 2px;
			box-shadow: 0 1px 2px rgba(0,0,0,0.1);
		}
		/* Responsive */
		@media (max-width: 768px) {
			.dil-sekmeleri > li > a {
				padding: 6px 8px;
				min-width: 38px;
			}
			.dil-sekmeleri .bayrak_svg {
				width: 18px;
				height: 12px;
			}
		}
	</style>
</head>
<html lang="<?php echo _DIL_HTML_LANG(); ?>" dir="<?php echo _DIL_HTML_YON(); ?>">
<head>
	<meta charset="UTF-8">
	<title><?php if ($tema) {@$tema_sayfa=$_GET["sayfa"]; if ($tema_sayfa) {echo ucwords($tema_sayfa)." -";} else {include ("../$temalar_dizini/$tema/info.php");$tema_baslik=$bilgi["baslik"]; echo "$tema_baslik -";}} else {echo "Temalar -";} ?> <?php echo $lpanel_baslik; ?></title>
	<?php include("inc/header-kod.php"); ?>
	<?php if ($datatable!=0) { ?>
	<script>
	$(document).ready(function() {
		$('#listele_table').DataTable({
			"responsive": true,
			"lengthMenu": [ 15, 25, 50, 100 ],
			"language": {
				"decimal":        "",
				"emptyTable":     "<?php echo _LANG("p_no_result"); ?>",
				"info":           "Toplam _TOTAL_ tema bulundu",
				"infoEmpty":      "<?php echo _LANG("p_gosterilecek_yok"); ?>",
				"infoFiltered":   "(_MAX_ toplam temadan)",
				"infoPostFix":    "",
				"thousands":      ",",
				"lengthMenu":     "<?php echo _LANG("p_gosterilecek_kayit"); ?>",
				"loadingRecords": "<?php echo _LANG("p_yukleniyor"); ?>",
				"processing":     "<?php echo _LANG("p_isleniyor"); ?>",
				"search":         "<?php echo _LANG("search"); ?>:",
				"zeroRecords":    "<?php echo _LANG("p_no_theme_matching"); ?>",
				"paginate": {
					"first":      "<?php echo _LANG("p_ilk"); ?>",
					"last":       "<?php echo _LANG("p_son"); ?>",
					"next":       "<?php echo _LANG("p_sonraki"); ?>",
					"previous":   "<?php echo _LANG("p_onceki"); ?>"			
			}
			}
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
				<?php if ($tema_destegi==1) { ?>
				<?php if ($tema and $sayfa) { echo "<div class='page'>";
				include("../$temalar_dizini/$tema/$sayfa.php");
				echo "</div>"; } else { if ($islem!="ayar") { ?>
				<h3 class="pull-left bolum-baslik">TEMALAR</h3>
				<div class="clearfix mb20"></div>
				<div class="table-responsive">
					<table id="listele_table" class="table table-bordered table-hover" data-order='[[ 0, "asc" ]]' data-page-length='15'>
						<thead>
							<tr>
								<th><?php echo _LANG('p_tema'); ?></th>
								<th><?php echo _LANG('p_baslik'); ?></th>
								<th class="visible-md visible-lg"><?php echo _LANG('p_aciklama'); ?></th> 
								<?php if (!$yazilim_bilgisi_bayi) { ?><th><?php echo _LANG('p_developer'); ?></th> <?php } ?>
								<th><?php echo _LANG('p_islemler'); ?></th>
							</tr>
						</thead>
						<tbody>
						<?php 
						$x=1;
						while (($tema = readdir($dizini_ac)) !== false) {
						if(!is_dir($tema) and $tema!="index.html"){
						if (file_exists("../$temalar_dizini/$tema/info.php")) {
							include ("../$temalar_dizini/$tema/info.php");
							$baslik=$bilgi["baslik"];
							@$aciklama=$bilgi["aciklama"];
							@$tema_gelistirici=$bilgi["gelistirici"];
							@$tema_gelistirici_url=$bilgi["gelistirici_url"];
							@$ayar=$bilgi["ayar"];
							@$dokuman=$bilgi["dokuman"];
							@$versiyon=$bilgi["versiyon"];
							$tema_durum=ayar_getir("tema");
							$hata="";
						} else {
							$hata=_LANG('p_theme_info_not_found');
						}
						?>
							<tr <?php if ($hata!="") {echo "class='warning'";} ?>> 
								<td><?php echo $tema; ?></td>
								<?php if ($hata=="") { ?>
								<td><?php echo $baslik; ?> <span class="text-muted" style="font-size:11px;"><?php echo $versiyon; ?></span></td>
								<td class="visible-md visible-lg"><?php echo $aciklama; ?></td>
								<?php if (!$yazilim_bilgisi_bayi) { ?><td><?php if ($tema_gelistirici_url=="") {echo $tema_gelistirici;} else {echo "<a href='$tema_gelistirici_url' target='_blank'>$tema_gelistirici</a>";} ?></td><?php } ?>
								<td>
								<?php if ($tema_durum!=$tema) { ?><a href="temalar.php?islem=etkinlestir&tema=<?php echo $tema; ?>" class="btn btn-xs btn-default"><?php echo _LANG("p_etkinlestir"); ?></a><?php } ?>
								<?php if ($ayar==1 and $tema_durum==$tema) { ?><a href="temalar.php?tema=<?php echo $tema; ?>&islem=ayar" class="btn btn-xs btn-success"><i class="fa fa-cogs" aria-hidden="true"></i><?php echo _LANG('p_ayarlar'); ?></a><?php } ?>
								<?php if ($dokuman!="") {echo "<a href='../$temalar_dizini/$tema/$dokuman' target='_blank' class='btn btn-xs btn-warning'><i class='fa fa-file-text mr0' aria-hidden='true'></i></a>";} ?>
								</td>
								<?php } else { ?>
								<td colspan="5"><?php echo $hata; ?></td>
								<?php } ?>
							</tr>
						<?php $x++; } } closedir($dizini_ac); ?>
						</tbody>
					</table>
				</div>
				<?php } else { ?>
				<h3 class="pull-left bolum-baslik"><?php echo _LANG('p_tema_ayarlari_k'); ?> <small>/ <?php echo $tema; ?></small></h3>
				<a href="temalar.php" class="btn btn-default"><?php echo _LANG('p_temalar'); ?></a>	
				<div class="clearfix mb20"></div>
				<div class="row">
					<div class="col-md-50">
						<?php 
						if ($tema_ayarlari!="") { 
						echo "<ul class='nav nav-tabs' role='tablist'>";
						$say=1;
						$tab_sayisi=0;
						foreach ($tema_ayarlari as $yaz => $x) {
							$eb_type=($x["tip"] ?? '');
							@$eb_name=($x["id"] ?? '');
							@$ayar_text=$x["baslik"];
							if ($say==1) {
								$active="class='active'";
							} else {$active="";}
							if ($eb_type=="tab_ac") {
								echo "<li role='presentation' $active><a href='#$eb_name' aria-controls='$eb_name' role='tab' data-toggle='tab'>$ayar_text</a></li>";
								$tab_sayisi=$tab_sayisi+1;
							}
							$say++; 
						} 
						echo "</ul>";
						?>
						<div class="panel panel-default" style="border-top:0;border-radius:0;box-shadow:none;">
						<?php if ($tab_sayisi==0) {echo "<div class='panel-heading'>$tema_baslik</div>"; } ?>
						<div class="panel-body">
						<form method="post" action="" enctype="multipart/form-data">
						<?php
						// Desteklenen dilleri al
						global $desteklenen_diller;
						$diller = $desteklenen_diller;
						$dil_kodlari = array_keys($diller);
						
						echo "<div class='tab-content'>";
						$tab_say=1;
						foreach ($tema_ayarlari as $yaz => $x) {
						$eb_type=($x["tip"] ?? '');
						@$eb_name=($x["id"] ?? '');
						@$ayar_text=$x["baslik"];
						@$ayar_options=$x["secenekler"];
						@$ayar_required=$x["gerekli"];
						@$ayar_value=$x["deger"];
						@$ayar_rows=$x["satir"];
						@$ayar_standart=$x["standart"];
						@$ayar_aciklama=$x["aciklama"];
						@$ayar_class=$x["sinif"];
						@$ayar_grup=$x["grup"];
						@$ayar_cevir = isset($x["cevir"]) ? $x["cevir"] : false;
						if ($ayar_grup) {
							if ($ayar_grup==2) {
								$grup_ac_kod="<div class='col-md-30'>";
							} else if ($ayar_grup==3) {
								$grup_ac_kod="<div class='col-md-20'>";
							} else if ($ayar_grup==4) {
								$grup_ac_kod="<div class='col-md-15'>";
							} else if ($ayar_grup==5) {
								$grup_ac_kod="<div class='col-md-12'>";
							} else if ($ayar_grup==6) {
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
						
						// Çeviri destekli alanlar için dil sekmeleri göster
						if ($ayar_cevir && ($eb_type=="text" || $eb_type=="textarea" || $eb_type=="email")) {
							// Dil sekmeleri
							echo "<ul class='nav nav-tabs dil-sekmeleri' role='tablist' style='margin-bottom:10px;'>";
							$dil_say = 1;
							foreach ($dil_kodlari as $dil_kod) {
								$dil_bilgi = $diller[$dil_kod];
								$dil_adi = $dil_bilgi['ad'];
								$active_dil = ($dil_kod == 'tr') ? 'class="active"' : '';
								echo "<li role='presentation' $active_dil><a href='#{$eb_name}_{$dil_kod}' aria-controls='{$eb_name}_{$dil_kod}' role='tab' data-toggle='tab' title='{$dil_adi}'>{$dil_bilgi['bayrak']}</a></li>";
							}
							echo "</ul>";
							echo "<div class='tab-content dil-icerikleri'>";
							foreach ($dil_kodlari as $dil_kod) {
								$active_dil = ($dil_kod == 'tr') ? 'active' : '';
								echo "<div role='tabpanel' class='tab-pane $active_dil' id='{$eb_name}_{$dil_kod}'>";
								
								// Bu dil için değeri al
								$anahtar_dil = ($dil_kod === 'tr') ? $tema."-".$eb_name : $tema."-".$eb_name."_".$dil_kod;
								$ayar_yaz = ayar_getir($anahtar_dil);
								if (!$ayar_yaz) {$ayar_yaz = $ayar_standart;}
								
								// Input name: dil kodu ile birlikte (örn: footer_1_baslik_en)
								$input_name = ($dil_kod === 'tr') ? $eb_name : $eb_name."_".$dil_kod;
								
								if ($eb_type=="text" || $eb_type=="email") {
									form_elemani($eb_type,$input_name,$ayar_text,$ayar_yaz,null,null,$ayar_required,$ayar_aciklama);
								} else if ($eb_type=="textarea") {
									if (!$ayar_class) {$ayar_class=null;}
									textarea_olustur($ayar_class,$input_name,$ayar_text,$ayar_yaz,$ayar_rows,$ayar_required,$ayar_aciklama);
								}
								echo "</div>";
							}
							echo "</div>";
						} else {
							// Normal (çevirisiz) alan
							$ayar_yaz=ayar_getir($tema."-".$eb_name);
							if (!$ayar_yaz) {$ayar_yaz=$ayar_standart;}
							if ($eb_type=="text" or $eb_type=="email" or $eb_type=="password" or $eb_type=="date" or $eb_type=="date_time") {
								form_elemani($eb_type,$eb_name,$ayar_text,$ayar_yaz,null,null,$ayar_required,$ayar_aciklama);
							} else if ($eb_type=="textarea") {
								if (!$ayar_class) {$ayar_class=null;}
								textarea_olustur($ayar_class,$eb_name,$ayar_text,$ayar_yaz,$ayar_rows,$ayar_required,$ayar_aciklama);
							} else if ($eb_type=="select") {
								select_olustur($eb_name,$ayar_text,$ayar_options,$ayar_yaz,$ayar_aciklama);
							} else if ($eb_type=="checkbox") {
								checkbox_olustur($eb_name,$ayar_text,$ayar_yaz,$ayar_aciklama);
							} else if ($eb_type=="multiple_checkbox") {
								multiple_checkbox_olustur($eb_name,$ayar_text,$ayar_options,$ayar_yaz,$ayar_aciklama);
							} else if ($eb_type=="aciklama") {
								echo aciklama_olustur($ayar_aciklama);
							} else if ($eb_type=="baslik") {
								echo baslik_olustur($ayar_text,null,null,$ayar_aciklama);
							} else if ($eb_type=="color") {
								colorpicker_olustur($eb_name,$ayar_text,$ayar_yaz,null,null,$ayar_required,$ayar_aciklama);
							} else if ($eb_type=="upload") {
								upload_olustur($eb_name,$ayar_text,$ayar_yaz,$ayar_aciklama);
							} else if ($eb_type=="liste") {
								liste_link($eb_name,$ayar_text,$ayar_aciklama);
							} else if ($eb_type=="grup_ac") {
								grup_ac($eb_name,$ayar_text,$ayar_aciklama);
							} else if ($eb_type=="grup_kapat") {
								grup_kapat();
							} else if ($eb_type=="location") {
								location($eb_name,$ayar_text,$ayar_yaz,$ayar_aciklama);
							} else if ($eb_type=="tab_ac") {
								if ($tab_say==1) {$active="active";} else {$active="";}
								tab_ac($eb_name,$ayar_text,$active);
								$tab_say++;					
							} else if ($eb_type=="tab_kapat") {
								tab_kapat();
							}
						}
						echo $grup_kapat_kod;
						} 
						echo "</div>";
						form_buton("buton",_LANG('p_ayarlar_kaydet'),"primary");
						} else {echo _LANG('p_tema_ayar_yok');}?>			
						<?php form_elemani("hidden","aktif_tab",_LANG('p_aktif_tab')); ?>
						</form>
						</div>
						</div>					
					</div>
					<div class="col-md-10 duyuru_sidebar">
						<?php foreach ($tema_duyurulari as $x) {echo $x;} ?>
					</div>
				</div>
				<?php } } ?>
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