<?php
include("../loader.php");
yonetici_degilse_yonlendir($oturum_id,ayar_getir("site_url")."/$admin_dizini/giris-yap.php");
if(file_exists("../$eklentiler_dizini")) {
$dizini_ac = opendir("../$eklentiler_dizini"); 
$tara=scandir ("../$eklentiler_dizini");
$eklenti_sayisi=count($tara)-2;
} else {
	echo _LANG('p_eklenti_klasoru_yok');
	exit();//İşlemi durdur
}
@$islem=$_GET["islem"];
@$eklenti=$_GET["eklenti"];
@$sayfa=$_GET["sayfa"];
if ($islem=="etkinlestir") {
	eklenti_etkinlestir($eklenti);
	$_SESSION["mesaj"]=array(_LANG('p_plugin_enabled', array($eklenti)),"success");
	include ("../$eklentiler_dizini/$eklenti/index.php");
	$eklenti_ayar=$bilgi["ayar"];
	if ($eklenti_ayar==1) {
		include("../$eklentiler_dizini/$eklenti/ayar.php");
		foreach ($eklenti_ayarlari as $yaz => $x) {
			$eb_name=$x["id"];
			$eb_standart=$x["standart"];
			if ($eb_standart) {
				ayar_guncelle($eklenti."-".$eb_name,$eb_standart);
			}
		}
	}
	yonlendir("eklentiler.php");
}
if ($islem=="pasiflestir") {
	eklenti_pasiflestir($eklenti);
	$_SESSION["mesaj"]=array("Eklenti pasiflestirildi: $eklenti","success");
	yonlendir("eklentiler.php");	
}
if ($islem=="ayar") {
	@$buton=$_POST["buton"];
	include ("../$eklentiler_dizini/$eklenti/index.php");
	$eklenti_baslik=$bilgi["baslik"];
	$eklenti_duyurulari=array();
	include("../$eklentiler_dizini/$eklenti/ayar.php");
	if ($buton) {
		foreach ($eklenti_ayarlari as $yaz => $x) {
			$eb_type=$x["tip"];
			$eb_name=$x["id"];
			$deger=$_POST["$eb_name"];
			$mevcut_deger=ayar_getir($eklenti."-".$eb_name);
			if ($eb_type!="multiple_checkbox" and $eb_type!="upload") {
				
				if ($eb_type=="location") {
					$lat=$_POST["map_$eb_name-lat"];
					$lon=$_POST["map_$eb_name-lon"];
					$radius=$_POST["map_$eb_name-radius"];
					$zoom=$_POST["map_$eb_name-zoom"];
					$deger="$lat,$lon,$radius,$zoom";
				}
				
				if ($deger!=$mevcut_deger) {
					ayar_guncelle($eklenti."-".$eb_name,$deger);
				}
			} 
			else if ($eb_type=="multiple_checkbox") {
				foreach ($deger as $yaz) {	
					$diziden_cikart.="($yaz),";
				}
				if ($diziden_cikart!=$mevcut_deger) {
					ayar_guncelle($eklenti."-".$eb_name,$diziden_cikart);
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
					$mevcut_deger=eklenti_ayar($eb_name,$id);
					eklenti_ayar_guncelle($eb_name,$resim_url);
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
			$mevcut_deger=eklenti_ayar($name,$id);
			if ($eb_gecerli_boyut) {
				$mevcut_deger=thumbnail_url("full",$mevcut_deger,$eb_gecerli_boyut);
			}
			sil($mevcut_deger);
			foreach ($eb_boyutlar as $yaz) {
				$thumbnail_url=thumbnail_url($yaz,$mevcut_deger);
				sil($thumbnail_url);
			}
			eklenti_ayar_guncelle($name,null);
		}
		// v2 resim upload remove güncellemesi bitti.
		
		$_SESSION["mesaj"]=array(_LANG('p_plugin_settings_updated'),"success");
		$aktif_tab=$_POST["aktif_tab"];
		if ($aktif_tab) {
			yonlendir("eklentiler.php?islem=ayar&eklenti=$eklenti&tab=$aktif_tab");
		} else {
			yonlendir("eklentiler.php?islem=ayar&eklenti=$eklenti");
		}		
	}
}
$datatable=ayar_getir("datatable");
$panel_sablon=ayar_getir("panel_sablon");
?>
<!DOCTYPE HTML>
<html lang="<?php echo _DIL_HTML_LANG(); ?>" dir="<?php echo _DIL_HTML_YON(); ?>">
<head>
	<meta charset="UTF-8">
	<title><?php if ($eklenti) {$eklenti_sayfa=$_GET["sayfa"]; if ($eklenti_sayfa) {echo ucwords($eklenti_sayfa)." -";} else {include ("../$eklentiler_dizini/$eklenti/index.php"); $eklenti_baslik=$bilgi["baslik"]; echo "$eklenti_baslik -";}} else {echo "Eklentiler -";} ?> <?php echo $lpanel_baslik; ?></title>
	<?php include("inc/header-kod.php"); ?>
	<?php if ($datatable!=0) { ?>
	<script>
	$(document).ready(function() {
		$('#listele_table').DataTable({
			"responsive": true,
			"lengthMenu": [ 15, 25, 50, 100 ],
			"language": {
				"decimal":        "",
				"emptyTable":     "<?php echo _LANG('p_no_result'); ?>",
				"info":           "Toplam _TOTAL_ eklenti bulundu",
				"infoEmpty":      "<?php echo _LANG('p_gosterilecek_yok'); ?>",
				"infoFiltered":   "(_MAX_ toplam eklentiden)",
				"infoPostFix":    "",
				"thousands":      ",",
				"lengthMenu":     "<?php echo _LANG('p_gosterilecek_kayit'); ?>",
				"loadingRecords": "<?php echo _LANG('p_yukleniyor'); ?>",
				"processing":     "<?php echo _LANG('p_isleniyor'); ?>",
				"search":         "<?php echo _LANG("search"); ?>:",
				"zeroRecords":    "<?php echo _LANG('p_no_plugin_matching'); ?>",
				"paginate": {
					"first":      "<?php echo _LANG('p_ilk'); ?>",
					"last":       "<?php echo _LANG("p_son"); ?>",
					"next":       "<?php echo _LANG("p_sonraki"); ?>",
					"previous":   "<?php echo _LANG('p_onceki'); ?>"			
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
				<?php if ($eklenti and $sayfa) { echo "<div class='page'>";
				include("../$eklentiler_dizini/$eklenti/$sayfa.php");
				echo "</div>"; } else { if ($islem!="ayar") { ?>
				<h3 class="pull-left bolum-baslik"><?php echo _LANG('p_eklentiler_basligi'); ?></h3>
				<div class="clearfix mb20"></div>
				<div class="table-responsive">
				<table id="listele_table" class="table table-bordered table-hover" data-order='[[ 0, "asc" ]]' data-page-length='15'>
					<thead>
						<tr>
							<th><?php echo _LANG('p_eklenti'); ?></th>
							<th><?php echo _LANG('p_baslik'); ?></th>
							<th class="visible-md visible-lg"><?php echo _LANG('p_aciklama'); ?></th> 
							<?php if (!$limonsoft_bayi) { ?><th><?php echo _LANG('p_developer'); ?></th> <?php } ?>
							<th><?php echo _LANG('p_durum'); ?></th> 
							<th><?php echo _LANG('p_islemler'); ?></th>
						</tr>
					</thead>
					<tbody>
					<?php 
					$x=1;
					while (($eklenti = readdir($dizini_ac)) !== false) {
					if(!is_dir($eklenti) and $eklenti!="index.html"){
					if (file_exists("../$eklentiler_dizini/$eklenti/index.php")) {
						include ("../$eklentiler_dizini/$eklenti/index.php");
						$baslik=$bilgi["baslik"];
						@$aciklama=$bilgi["aciklama"];
						@$ayar=$bilgi["ayar"];
						@$gerekli=$bilgi["gerekli"];
						@$dokuman=$bilgi["dokuman"];
						@$fonksiyon=$bilgi["fonksiyon"];
						@$gelistirici=$bilgi["gelistirici"];
						@$gelistirici_url=$bilgi["gelistirici_url"];
						@$versiyon=$bilgi["versiyon"];
						$eklenti_durum=eklenti_durum($eklenti);
						$hata="";
						if (!file_exists("../$eklentiler_dizini/$eklenti/$fonksiyon")) {
							$hata="<?php echo _LANG('p_plugin_function_not_found'); ?>";
						}
					} else {
						$hata="<?php echo _LANG('p_plugin_info_not_found'); ?>";
					}
					?>
						<tr <?php if ($hata!="") {echo "class='warning'";} ?>> 
							<td><?php echo $eklenti; ?></td>
							<?php if ($hata=="") { ?>
							<td><?php echo $baslik; ?> <span class="text-muted" style="font-size:11px;"><?php echo $versiyon; ?></span></td>
							<td class="visible-md visible-lg"><?php echo $aciklama; ?></td>
							<?php if (!$limonsoft_bayi) { ?><td><?php if ($gelistirici_url=="") {echo $gelistirici;} else {echo "<a href='$gelistirici_url' target='_blank'>$gelistirici</a>";} ?></td><?php } ?>
							<td><?php if ($eklenti_durum==0) {echo "<span class='label label-danger'>Pasif</span>";} else { ?><span class='label label-success'><?php echo _LANG('p_aktif'); ?></span><?php } ?></td>
							<td><?php if ($eklenti_durum==0) { ?><a href="eklentiler.php?islem=etkinlestir&eklenti=<?php echo $eklenti; ?>" class="btn btn-xs btn-primary"><i class="fa fa-toggle-off" aria-hidden="true"></i><?php echo _LANG('p_etkinlestir'); ?></a><?php } else { if ($gerekli!=1) { ?><a href="eklentiler.php?islem=pasiflestir&eklenti=<?php echo $eklenti; ?>" class="btn btn-xs btn-default"><i class="fa fa-toggle-on" aria-hidden="true"></i><?php echo _LANG('p_pasiflestir'); ?></a><?php }} ?><?php if (($ayar==1 or $ayar) and $eklenti_durum==1) { if ($ayar==1) {$ayar_url="eklentiler.php?eklenti=$eklenti&islem=ayar";} else {$ayar_url="eklentiler.php?eklenti=$eklenti&sayfa=$ayar";} ?><a href="<?php echo $ayar_url; ?>" class="btn btn-xs btn-success" style="margin-left:3px;margin-right:0 !important;"><i class="fa fa-cogs mr0" aria-hidden="true"></i></a><?php } ?>
							<?php if ($dokuman!="") {echo "<a href='../$eklentiler_dizini/$eklenti/$dokuman' target='_blank' class='btn btn-xs btn-warning'><i class='fa fa-file-text mr0' aria-hidden='true'></i></a>";} ?>
							</td>
							<?php } else { ?>
							<td colspan="6"><?php echo $hata; ?></td>
							<?php } ?>
						</tr>
					<?php $x++; } } closedir($dizini_ac); ?>
					</tbody>
				</table>
				</div>
				<?php } else { ?>
				<h3 class="pull-left bolum-baslik"><?php echo _LANG('p_eklenti_ayarlari'); ?> <small>/ <?php echo $eklenti; ?></small></h3>
				<a href="eklentiler.php" class="btn btn-default"><?php echo _LANG('p_eklentiler_k'); ?></a>	
				<div class="clearfix mb20"></div>
				<div class="row">
					<div class="col-md-45">
						<?php
						if ($eklenti_ayarlari!="") {
						echo "<ul class='nav nav-tabs' role='tablist'>";
						$say=1;
						$tab_sayisi=0;
						foreach ($eklenti_ayarlari as $yaz => $x) {
							$eb_type=$x["tip"];
							@$eb_name=$x["id"];
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
						<?php if ($tab_sayisi==0) {echo "<div class='panel-heading'>$eklenti_baslik</div>"; } ?>
						<div class="panel-body">
						<form method="post" action="" enctype="multipart/form-data">
						<?php
						echo "<div class='tab-content'>";
						$tab_say=1;
						foreach ($eklenti_ayarlari as $yaz => $x) {
						$eb_type=$x["tip"];
						@$eb_name=$x["id"];
						@$ayar_text=$x["baslik"];
						@$ayar_options=$x["secenekler"];
						@$ayar_required=$x["gerekli"];
						@$ayar_value=$x["deger"];
						@$ayar_rows=$x["satir"];
						@$ayar_standart=$x["standart"];
						@$ayar_aciklama=$x["aciklama"];
						@$ayar_class=$x["sinif"];
						@$ayar_grup=$x["grup"];
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
						$ayar_yaz=ayar_getir($eklenti."-".$eb_name);
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
						echo $grup_kapat_kod;
						} 
						echo "</div>";
						form_buton("buton","<?php echo _LANG('p_ayarlar_kaydet'); ?>","primary");
						} else {echo _LANG('p_eklenti_ayar_yok');}?>			
						<?php form_elemani("hidden","aktif_tab","Aktif Tab"); ?>
						</form>
						</div>
						</div>
					</div>
					<div class="col-md-15 duyuru_sidebar">
						<?php if (is_array($eklenti_duyurulari)) {foreach ($eklenti_duyurulari as $x) {echo $x;}} ?>
					</div>
				</div>
				<?php } } ?>
			</div>	
			<div class="clearfix mb20"></div>
		</div>
	</div>
</div>
<?php include("inc/alt.php"); ?>
<?php include("inc/footer-kod.php"); ?>
</body>
</html>
