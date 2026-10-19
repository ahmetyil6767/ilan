<?php
include("../loader.php");
yonetici_degilse_yonlendir($oturum_id,ayar_getir("site_url")."/$admin_dizini/giris-yap.php");

function guncelleme_paketi_sil() {
	global $baglan;
	@$paket_id=mysqli_fetch_array(mysqli_query($baglan,"select id from kayitlar where tip='guncelleme'"))[0];
	if ($paket_id) {
		$paket_url=getir("kayitlar","veri_1",$paket_id);
		sil($paket_url);
		$kayit_sil=mysqli_query($baglan,"delete from kayitlar where tip='guncelleme' and id='$paket_id'");
	}
}

@$paket_id=mysqli_fetch_array(mysqli_query($baglan,"select id from kayitlar where tip='guncelleme'"))[0];

if ($paket_id) {
	$paket_url=getir("kayitlar","veri_1",$paket_id);
	$paket_tarih_saat=getir("kayitlar","tarih_saat",$paket_id);
}

@$islem=$_GET["islem"];
$domain=domain();
$token=api_token();
$guncelleme_sorgula=curl_get_contents("https://www.limontasarim.com/api/v1/yukle.php?domain=$domain&token=$token");
if (is_object(json_decode($guncelleme_sorgula))) {
	$guncelleme_array=json_decode($guncelleme_sorgula,true);
}

@$limonsoft_hata=$guncelleme_array["hata"];
@$limonsoft_lisans=$guncelleme_array["lisans"];
@$limonsoft_garanti=$guncelleme_array["garanti"];
@$limonsoft_garanti_bitis_tarih_saat=$guncelleme_array["garanti_bitis_tarih_saat"];
@$limonsoft_guncel_versiyon=$guncelleme_array["versiyon"];
@$limonsoft_son_guncelleme=$guncelleme_array["son_guncelleme"];
@$limonsoft_remove_copyright=$guncelleme_array["remove_copyright"];

if ($limonsoft_hata) {
	echo $limonsoft_hata;
	exit;
}

@$limonsoft_indir=$_POST["limonsoft_indir"];
if ($limonsoft_indir) {
	if ($limonsoft_garanti!=1) {
		yenile();
		exit;
	}
	/* Yazılım güncelleniyor */
	$zip_sorgula=curl_get_contents("https://www.limontasarim.com/api/v1/indir.php?tip=limonilan&domain=$domain&token=$token");
	$zip_array=json_decode($zip_sorgula,true);
	$zip_url=$zip_array["zip"];
	if (!url_kontrol($zip_url)) {
		$_SESSION["mesaj"]=array(_LANG("p_package_not_reached"),"danger");
		yenile();
		exit;
	}
	
	$yuklenen=cek($zip_url);
	if ($yuklenen) {
		guncelleme_paketi_sil();
		$tarih_saat=tarih_saat();
		$ekle=mysqli_query($baglan,"insert into kayitlar set uye_id='0', veri_1='$yuklenen', tip='guncelleme', tarih_saat='$tarih_saat'");
		if ($ekle) {
			$_SESSION["mesaj"]=array(_LANG("p_package_downloaded_ready"),"success");
		} else {
			$_SESSION["mesaj"]=array(_LANG("p_package_downloaded_not_saved"),"danger");
		}
	} else {
		$_SESSION["mesaj"]=array(_LANG("p_package_not_downloaded"),"danger");
	}
	yenile();
}



@$limonsoft_yukle=$_POST["limonsoft_yukle"];
if ($limonsoft_yukle and $paket_url) {
	if ($limonsoft_garanti!=1) {
		yenile();
		exit;
	}
	bakim_modu_ac();
	/* Admin dizini değiştirilmiş ise eski haline getiriliyor */
	$eski_admin_dizini=admin_dizini();
	if ($eski_admin_dizini!="lpanel") {
		$dizin_degistir=rename("../$eski_admin_dizini","../lpanel");
		if ($dizin_degistir) {
			ayar_guncelle("admin_dizini","lpanel");
		}
	}
	/* Admin dizini değiştirildi */

	$yuklenen=$paket_url;
	$yol_hazirlik=explode(site_url()."/",$yuklenen);
	$dosya=$yol_hazirlik[1];
	if ($dosya) {
		$zip = new ZipArchive;
		if ($zip->open(ROOT_DIR."/$dosya") === true) {
			for($i = 0; $i < $zip->numFiles; $i++) {
				$filename = $zip->getNameIndex($i);
				$fileinfo = pathinfo($filename);
				if ($i==0) {
					$haric=$filename;
				}
				if ($filename!="$haric") {
					$kaynak="zip://".ROOT_DIR."/$dosya#".$filename;
					$filename = str_replace($haric,null,$filename);
					$hedef=ROOT_DIR."/".$filename;
					if (substr($filename,"-1")=="/") {
						klasor_olustur($filename);
					} else {
						copy($kaynak,$hedef);
					}
				}
			}
			$zip->close();
		}

		$_SESSION["mesaj"]=array(_LANG("p_software_updated"),"success");
		bildirim_ekle(_LANG('p_uzaktan_guncellendi_bildirim'),null,0);
		sil($yuklenen);
	}
	bakim_modu_kapat();
	guncelleme_paketi_sil();
	limonsoft_sorgula(true);
	yenile();
}

$limonsoft_sil=$_POST["limonsoft_sil"];
if ($limonsoft_sil) {
	guncelleme_paketi_sil();
	$_SESSION["mesaj"]=array(_LANG("p_update_package_deleted"),"success");
	yenile();
}

$limonsoft_versiyon=ayar("versiyon");
?>
<!DOCTYPE HTML>
<html lang="<?php echo _DIL_HTML_LANG(); ?>" dir="<?php echo _DIL_HTML_YON(); ?>">
<head>
	<meta charset="UTF-8">
	<title><?php echo _LANG("p_guncellemeler"); ?> - <?php echo $lpanel_baslik; ?></title>
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
			<h3 class="pull-left bolum-baslik"><?php echo _LANG('p_guncellemeler_basligi'); ?></h3>
			<div class="clearfix mb20"></div>
			<?php if ($limonsoft_garanti==1) { ?>
			<form action="" method="post" id="form">
				
				<?php if ($paket_id) { ?>
				<div class="table-responsive">
					<table class="table table-bordered bg-light">
						<thead>
							<tr>
								<th><?php echo _LANG('p_durum'); ?></th>
								<th><?php echo _LANG('p_tarihte_indirildi'); ?></th>
								<th><?php echo _LANG('p_islemler'); ?></th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td><?php echo _LANG('p_paket_hazir'); ?></td>
								<td><?php echo $paket_tarih_saat; ?></td>
								<td class="butonlar">
									<button type="submit" name="limonsoft_yukle" value="1" class="btn btn-success btn-xs ajax_buton" onclick="return onayla('<?php echo _LANG("p_yukleme_onay"); ?>');"><i class="fa fa-refresh"></i><?php echo _LANG('p_yuklemeyi_baslat'); ?></button>
									<button type="submit" name="limonsoft_sil" value="1" class="btn btn-danger btn-xs" onclick="return confirm('<?php echo _LANG("p_yukleme_paketi_sil_onay"); ?>');"><i class="fa fa-trash"></i><?php echo _LANG('p_sil'); ?></button>
								</td>
							</tr>
						</tbody>
						</table>
					</div>
				<?php } ?>
				
				<div class="table-responsive">
					<table class="table table-bordered bg-light">
						<thead>
							<tr>
								<th><?php echo _LANG('p_kurulu_versiyon'); ?></th>
								<th><?php echo _LANG('p_durum'); ?></th>
								<th><?php echo _LANG('p_guncel_versiyon'); ?></th>
								<th><?php echo _LANG('p_son_guncelleme'); ?></th>
								<th><?php echo _LANG('p_islemler'); ?></th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td><?php echo $limonsoft_versiyon; ?></td>
								<td><?php if ($limonsoft_versiyon<$limonsoft_guncel_versiyon) {echo "<div class='label label-danger'>" . _LANG("p_update_required") . "</div>";} else {echo "<div class='label label-success'>" . _LANG("p_system_up_to_date") . "</div>";} ?></td>
								<td><?php echo $limonsoft_guncel_versiyon; ?></td>
								<td><?php echo $limonsoft_son_guncelleme; ?></td>
								<td class="butonlar">
									<?php if ($limonsoft_versiyon<$limonsoft_guncel_versiyon) { ?>
										<button type="submit" name="limonsoft_indir" value="1" class="btn btn-success btn-xs ajax_buton" onclick="return onayla('<?php echo _LANG("p_yazilim_indir_onay"); ?>',1);"><i class="fa fa-refresh"></i><?php echo _LANG('p_yazilimi_indir'); ?></button>
									<?php } else { ?>
										<button type="submit" name="limonsoft_indir" value="1" class="btn btn-danger btn-xs ajax_buton" onclick="return onayla('<?php echo _LANG("p_yazilim_yeniden_indir_onay"); ?>',1);"><i class="fa fa-undo"></i><?php echo _LANG('p_yeniden_indir'); ?></button>
									<?php } ?>
								</td>
							</tr>
						</tbody>
						</table>
					</div>
				</form>

				<div id="yukleniyor" class="text-center p-5" style="display:none;">
					<i class="fa fa-spinner fa-spin fa-4x mb20"></i>
					<div class="mesaj"></div>
				</div>
				<?php } else {
					if (!$limonsoft_garanti_bitis_tarih_saat) {
							echo "<div class='alert alert-danger'>" . _LANG('p_lokal_sunucu_uyari') . "</div>";
					} else {
						if ($limonsoft_garanti_bitis_tarih_saat<tarih_saat()) {
							echo "<div class='alert alert-danger'>" . _LANG('p_garanti_sona_erdi_uyari'), array($limonsoft_garanti_bitis_tarih_saat) . "</div>";
						} else {
							echo "<div class='alert alert-warning'>" . _LANG('p_guncelleme_devam_uyari') . "</div>";
						}
					}
				} ?>
				<?php // print_r($limonsoft_array); ?>
				
				<div class="uyarilar" style="padding:20px;background:#ffe0e0;border:1px solid #ffa3a3;margin-top:50px;">
					<h4><?php echo _LANG('p_olasi_hatalar'); ?></h4>
					<ul style="padding:0 0 0 15px;margin:0;">
						<li><?php echo _LANG("p_yukleme_ipucu_1"); ?> <a href='ayarlar.php' target='_blank'><?php echo _LANG('p_ayarlar'); ?></li>
						<li><?php echo _LANG("p_yukleme_ipucu_2"); ?></li>
						<li><?php echo _LANG('p_htmlde_500'); ?> <br /><?php echo _LANG('cpanel_ipucu'); ?></li>
						<li><?php echo _LANG('p_baska_hata'); ?></li>
					</ul>
				</div>
				
			</div>
		</div>
	</div>
</div>
<?php include("inc/alt.php"); ?>
<?php include("inc/footer-kod.php"); ?>
</body>
</html>
<script>
$("#yukleniyor").hide();
function yukleniyor(saniye=4000) {
	$("#form").hide();
	$("#yukleniyor .mesaj").text("<?php echo _LANG("p_yukleme_baslatildi"); ?>");
	$("#yukleniyor").show();
	var i=0;
	window.setInterval(function(){
		if (i%3==2) {
			var text="<?php echo _LANG("p_yukleme_suruyor"); ?>";
		} else if (i%3==1) {
			var text="<?php echo _LANG("p_yukleme_bekle_tip"); ?>";
		} else {
			var text="<?php echo _LANG("p_yukleme_islemi_suruyor"); ?>";
		}
		$("#yukleniyor .mesaj").text(text).show();
	i++;
	}, saniye);
}
function onayla(text,tip) {
	if (confirm(text)) {
		if (tip==1) {
			indiriliyor();
		} else {
			yukleniyor();
		}
		return true;
	} else {
		return false;
	}
}
function indiriliyor(saniye=4000) {
	$("#form").hide();
	$("#yukleniyor .mesaj").text("<?php echo _LANG("p_indirme_baslatildi"); ?>");
	$("#yukleniyor").show();
	var i=0;
	window.setInterval(function(){
		if (i%3==2) {
			var text="<?php echo _LANG("p_indirme_suruyor"); ?>";
		} else if (i%3==1) {
			var text="<?php echo _LANG("p_yukleme_bekle_tip"); ?>";
		} else {
			var text="<?php echo _LANG("p_indirme_islemi_suruyor"); ?>";
		}
		$("#yukleniyor .mesaj").text(text).show();
	i++;
	}, saniye);
}
</script>