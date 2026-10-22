<?php
include("../loader.php");
yonetici_degilse_yonlendir($oturum_id,ayar_getir("site_url")."/$admin_dizini/giris-yap.php");
@$id=$_GET["id"];
@$ayar=$_GET["ayar"];
@$islem=$_GET["islem"];
@$gorunum=$_GET["gorunum"];
if ($gorunum) {
	$_SESSION["ayar_gorunum"]=$gorunum;
	yonlendir("ayarlar.php");	
}
@$buton=$_POST["buton"];
if (!$islem) {
	if ($buton) {
		foreach ($site_ayarlari as $yaz => $x) {
			$eb_type=$x["tip"];
			$eb_name=$x["id"];
			$deger=$_POST["$eb_name"];
			$mevcut_deger=ayar_getir($eb_name);
			if ($eb_type!="multiple_checkbox" and $eb_type!="upload") {
				
				if ($eb_type=="location") {
					$lat=$_POST["map_$eb_name-lat"];
					$lon=$_POST["map_$eb_name-lon"];
					$radius=$_POST["map_$eb_name-radius"];
					$zoom=$_POST["map_$eb_name-zoom"];
					$deger="$lat,$lon,$radius,$zoom";
				}				
				
				if ($deger!=$mevcut_deger) {
					ayar_guncelle($eb_name,$deger);
				}
			} 
			else if ($eb_type=="multiple_checkbox") {
				foreach ($deger as $yaz) {	
					$diziden_cikart.="($yaz),";
				}
				if ($diziden_cikart!=$mevcut_deger) {
					ayar_guncelle($eb_name,$diziden_cikart);
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
					$mevcut_deger=ayar($eb_name,$id);
					ayar_guncelle($eb_name,$resim_url);
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
			$mevcut_deger=ayar($name,$id);
			if ($eb_gecerli_boyut) {
				$mevcut_deger=thumbnail_url("full",$mevcut_deger,$eb_gecerli_boyut);
			}
			sil($mevcut_deger);
			foreach ($eb_boyutlar as $yaz) {
				$thumbnail_url=thumbnail_url($yaz,$mevcut_deger);
				sil($thumbnail_url);
			}
			ayar_guncelle($name,null);
		}
		// v2 resim upload remove güncellemesi bitti.		
		
		$_SESSION["mesaj"]=array(_LANG("p_ayarlar_guncellendi"),"success");
		$aktif_tab=$_POST["aktif_tab"];
		if ($aktif_tab) {
			yonlendir("ayarlar.php?tab=$aktif_tab");
		} else {
			yonlendir("ayarlar.php");
		}
	}	
}
if ($islem=="sil") {
	$anahtar=bilgi_getir("ayarlar","anahtar",$id);
	if ($anahtar!="site_url" and $anahtar!="site_baslik" and $anahtar!="site_email" and $anahtar!="limonsoft" and $anahtar!="panel_renk" and $anahtar!="versiyon" and $anahtar!="basliga_ekle" and $anahtar!="tema" and $anahtar!="bildirimler" and $anahtar!="datatable" and $anahtar!="panel_sablon") {
		$sil_id_int=(int)$id;
		$sql=mysqli_prepare($baglan,"delete from ayarlar where id=?");
		mysqli_stmt_bind_param($sql,"i",$sil_id_int);
		mysqli_stmt_execute($sql);
		mysqli_stmt_close($sql);
		$_SESSION["mesaj"]=array(sprintf(_LANG('msj_ayar_silindi'), $anahtar),"success");
	} else {
		$_SESSION["mesaj"]=array(sprintf(_LANG('msj_ayar_silinemez'), $anahtar),"danger");
	}
	yonlendir("ayarlar.php");	
}
if ($islem=="ekle") {
	if ($buton) {
		$anahtar=$_POST["anahtar"];
		$deger=$_POST["deger"];
	$sql=mysqli_prepare($baglan,"insert into ayarlar set anahtar=?, deger=?");
	mysqli_stmt_bind_param($sql,"ss",$anahtar,$deger);
	mysqli_stmt_execute($sql);
	mysqli_stmt_close($sql);
	$_SESSION["mesaj"]=array(sprintf(_LANG('msj_ayar_eklendi'), $anahtar),"success");
	yonlendir("ayarlar.php");		
	}
}
if ($islem=="guncelle") {
	if ($buton) {
		$anahtar=$ayar;
		$deger=$_POST["deger"];
	$sql=mysqli_prepare($baglan,"update ayarlar set deger=? where anahtar=?");
	mysqli_stmt_bind_param($sql,"ss",$deger,$anahtar);
	mysqli_stmt_execute($sql);
	mysqli_stmt_close($sql);
	$_SESSION["mesaj"]=array(_LANG('p_ayar_guncellendi'),"success");
	yonlendir("ayarlar.php");		
	}
}
/* DATATABLE PASİF */
$datatable=ayar_getir("datatable");
if ($datatable==0) {
@$page=$_GET["p"];
@$kacarli_buton=$_POST["kacarli_buton"];
if ($kacarli_buton) {
	$kacarli=$_POST["kacarli"];
	$_SESSION["kacarli"]=$kacarli;
}
if (!isset($_SESSION["kacarli"])) {
	$kacarli=30;
} else {
	$kacarli=$_SESSION["kacarli"];
}
$toplam=mysqli_num_rows(mysqli_query($baglan,"select * from ayarlar")); 
$kac_sayfa=ceil($toplam/$kacarli);
if ($page=="") {$page=1;}
$baslangic=($page-1)*$kacarli;
}
if ($datatable==0) {
$sql=mysqli_query($baglan,"select * from ayarlar order by id desc limit $baslangic, $kacarli");
} else {
$sql=mysqli_query($baglan,"select * from ayarlar");
}
/* DATATABLE PASİF */
?>
<!DOCTYPE HTML>
<html lang="<?php echo _DIL_HTML_LANG(); ?>" dir="<?php echo _DIL_HTML_YON(); ?>">
<head>
	<meta charset="UTF-8">
	<title><?php echo _LANG("p_site_ayarlari"); ?> - <?php echo $lpanel_baslik; ?></title>
	<?php include("inc/header-kod.php"); ?>
	<?php if ($datatable!=0) { ?>
	<script>
	$(document).ready(function() {
		$('#listele_table').DataTable({
			"responsive": true,
			"lengthMenu": [ 15, 25, 50, 100 ],
			"language": {
				"decimal":        "",
				"emptyTable":     "<?php echo _LANG("p_kayit_bulunamadi"); ?>",
				"info":           "<?php echo _LANG("p_toplam_kayit"); ?>",
				"infoEmpty":      "<?php echo _LANG("p_gosterilecek_yok"); ?>",
				"infoFiltered":   "<?php echo _LANG("p_max_kayit"); ?>",
				"infoPostFix":    "",
				"thousands":      ",",
				"lengthMenu":     "<?php echo _LANG("p_gosterilecek_kayit"); ?>",
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
			<?php if (!@$_SESSION["ayar_gorunum"] or @$_SESSION["ayar_gorunum"]=="klasik") { ?>
			<h3 class="pull-left bolum-baslik">AYARLAR</h3>
			<a href="ayarlar.php?gorunum=liste" class="pull-right"><?php echo _LANG('p_liste_gorunumu'); ?></a>		
			<div class="clearfix mb20"></div>			
				<?php 
				if ($site_ayarlari!="") {
				echo "<ul class='nav nav-tabs' role='tablist'>";
				$say=1;
				$tab_sayisi=0;
				foreach ($site_ayarlari as $yaz => $x) {
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
				<?php if ($tab_sayisi==0) {echo "<div class='panel-heading'>" . _LANG("p_site_ayarlari") . "</div>"; } ?>
				<div class="panel-body">
				<form method="post" action="" enctype="multipart/form-data">
				<?php
				echo "<div class='tab-content'>";
				$tab_say=1;
				foreach ($site_ayarlari as $yaz => $x) {
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
				$ayar_yaz=ayar_getir($eb_name);
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
				// Tab hidden input
				form_elemani("hidden","aktif_tab",_LANG('p_aktif_tab'));
				echo "</div>";
				form_buton("buton",_LANG("p_ayarlar_kaydet"),"primary");
				echo "</form>
				<div class='clearfix'></div>
				</div>
				</div>";
				} else {echo _LANG('p_ayarlar_yapilandirilmamis');}?>			
			<?php } else if ($_SESSION["ayar_gorunum"]=="liste") { ?>
			<h3 class="pull-left bolum-baslik">AYARLAR</h3>
			<a href="ayarlar.php?islem=ekle" class="btn btn-primary"><?php echo _LANG('p_ayar_ekle_k'); ?></a>	
			<a href="ayarlar.php?gorunum=klasik" class="btn btn-default"><?php echo _LANG('p_klasik_gorunum'); ?></a>	
			<?php if ($datatable==0) { ?>			
			<form class="form-inline pull-right kayit_goster visible-md visible-lg" action="" method="post">
			  <div class="form-group">
				<input type="text" class="form-control input-sm nofocus" id="kayit" name="kacarli" placeholder="<?php echo _LANG("p_listeleme_adedi"); ?>">
			  </div>
			  <button type="submit" name="kacarli_buton" class="btn btn-default btn-sm" value="Onayla"><?php echo _LANG('p_onayla'); ?></button>
			</form>
			<?php } ?>			
			<div class="clearfix mb20"></div>
			<?php if ($islem=="ekle") { ?>
				<div class="panel panel-default">
					<div class="panel-heading"><?php echo _LANG('p_ayar_ekle'); ?></div>
					<div class="panel-body">
					<form method="post" action="">
						<?php form_elemani("text","anahtar",_LANG('p_anahtar')); ?>
						<?php form_elemani("text","deger",_LANG('p_deger')); ?>
						<?php form_buton("buton",_LANG('p_ayar_ekle_k')); ?>
					</form>		
					</div>
				</div>	
			<?php } else if ($islem=="guncelle") { ?>
				<div class="panel panel-default">
					<div class="panel-heading"><?php echo _LANG('p_ayar_guncelle'); ?></div>
					<div class="panel-body">
					<form method="post" action="">
						<?php form_elemani("text","anahtar",_LANG('p_anahtar'),$ayar,null,"disabled"); ?>
						<?php form_elemani("text","deger",_LANG('p_deger'),ayar_getir($ayar)); ?>
						<?php form_buton("buton",_LANG('p_ayar_guncelle_k')); ?>
					</form>
					</div>
				</div>
			<?php } ?>
			<div class="table-responsive">
			<table id="listele_table" class="table table-bordered table-hover" data-order='[[ 0, "desc" ]]' data-page-length='50'>
				<thead>
					<tr>
						<th width="10">#</th>
						<th><?php echo _LANG('p_anahtar'); ?></th>
						<th><?php echo _LANG('p_deger'); ?></th>
						<th width="200"><?php echo _LANG('p_islemler'); ?></th>
					</tr>
				</thead>
				<tbody>
				<?php 
				while ($dizi=mysqli_fetch_array($sql)) {
					$id=$dizi["id"];
					$anahtar=$dizi["anahtar"];
					$deger=$dizi["deger"];
				?>
					<tr> 
						<th scope="row"><?php echo $id; ?></th>
						<td><?php echo $anahtar; ?></td>
						<td><?php echo tirnak_ekle($deger); ?></td>
						<td>
							<!-- Split button -->
							<div class="btn-group">
							  <a href="ayarlar.php?islem=guncelle&ayar=<?php echo $anahtar; ?>" class="btn btn-xs btn-primary"><i class="fa fa-pencil" aria-hidden="true"></i><?php echo _LANG('p_guncelle'); ?></a>
							  <?php if ($anahtar!="site_url" and $anahtar!="site_baslik" and $anahtar!="site_email" and $anahtar!="panel_renk" and $anahtar!="versiyon" and $anahtar!="basliga_ekle" and $anahtar!="tema") { ?>
							  <button type="button" class="btn btn-primary btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								<span class="caret"></span>
								<span class="sr-only"><?php echo _LANG('p_toggle_dropdown'); ?></span>
							  </button>
							  <ul class="dropdown-menu">
								<li><a href="ayarlar.php?islem=sil&id=<?php echo $id; ?>" onclick="return confirm('Emin misiniz?')"><i class="fa fa-trash fa-fw" aria-hidden="true"></i><?php echo _LANG('p_sil'); ?></a></li>
							  </ul>
							  <?php } ?>
							</div>						
						</td>
					</tr>
				<?php } ?>
				</tbody>
			</table>
			</div>
			<?php panel_sayfalama($kac_sayfa,$page,1,"ayarlar.php?p="); ?>
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