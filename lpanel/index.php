<?php
include("../loader.php");
yonetici_degilse_yonlendir($oturum_id,ayar_getir("site_url")."/$admin_dizini/giris-yap.php");
@$logout=$_GET["logout"];
if ($logout==1) {
	session_destroy();
	setcookie("auto_login",null,-60*60*24*365*5,"/");
	yonlendir("index.php");
}
?>
<!DOCTYPE HTML>
<html lang="<?php echo _DIL_HTML_LANG(); ?>" dir="<?php echo _DIL_HTML_YON(); ?>">
<head>
	<meta charset="UTF-8">
	<title><?php echo _LANG('p_yonetim_paneli'); ?> - <?php echo $lpanel_baslik; ?></title>
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
			<?php limon(@$panel_anasayfa_ust_kod); ?>
				<div class="row">
					<div class="col-md-40">
						<?php limon(@$panel_anasayfa_sol_kod); ?>
						<div class="hizli_linkler">
							<div class="row">
							<?php
							$y=1;
							sksort($hizli_linkler,"sira",true);
							if ($panel_sablon=="fullwidth") {
								$hizli_link_div="col-lg-12 col-md-15 col-sm-20 col-xs-30";
							} else if ($panel_sablon=="boxed") {
								$hizli_link_div="col-lg-15 col-md-15 col-sm-20 col-xs-30";
							}
							foreach ($hizli_linkler as $yaz => $x) {
							@$text=$x["baslik"];
							@$url=$x["url"];
							@$ikon=$x["ikon"];
							@$yeni_sekme=$x["yeni_sekme"];
							@$yetki=$x["yetki"];
							if (!$yetki or strstr($yetki,"$oturum_yetki") or ($oturum_yetki=="admin")) {
							if ($yeni_sekme==1) {$yeni_sekme_yaz="target='blank'";} else {$yeni_sekme_yaz="";}
							?>								
							<div class="<?php echo $hizli_link_div; ?>">
								<a href="<?php echo $url; ?>" <?php echo $yeni_sekme_yaz; ?>><i class="fa fa-<?php echo $ikon; ?>"></i><br /><?php echo $text; ?></a>
							</div>
							<?php 
							if ($panel_sablon=="fullwidth") {
								if ($y%5==0) {echo "<div class='clearfix visible-lg'></div>";}
							} else if ($panel_sablon=="boxed") {
								if ($y%4==0) {echo "<div class='clearfix visible-lg'></div>";}
							}
							if ($y%4==0) {echo "<div class='clearfix visible-md'></div>";}
							if ($y%3==0) {echo "<div class='clearfix visible-sm'></div>";}
							if ($y%2==0) {echo "<div class='clearfix visible-xs'></div>";}
							$y++; } } ?>							
							</div>
						</div>
						<div class="clearfix mb15"></div>		
						<?php if (ayar_getir("bildirimler")==1) { ?>
						<div class="panel panel-default">
							<div class="table-responsive">
							<table class="table table-bordered bildirimler">
								<thead>
									<tr>
										<th colspan="2"><?php echo _LANG('p_bildirimler_menu'); ?></th>
									</tr>
								</thead>
								<tbody>
								<?php 
								$sql=mysqli_query($baglan,"select * from bildirimler where tip='0' order by id desc limit 0, 10");
								while ($dizi=mysqli_fetch_array($sql)) {
									$id=$dizi["id"];
									$uye_id=$dizi["uye_id"];
									$bildirim=$dizi["bildirim"];
									$tarih_saat=$dizi["tarih_saat"];
								?>
									<tr> 
										<td><?php echo $bildirim; ?></td>
										<td width="180"><i class="fa fa-clock-o"></i><?php echo su_kadar_once($tarih_saat,_LANG('p_once')); ?></td>
									</tr>
								<?php } ?>
								</tbody>
							</table>									
							</div>
							<div class="panel-footer"><a href="bildirimler.php"><?php echo _LANG('p_tumunu_goruntule'); ?></a></div>
						</div>	
						<?php } ?>
					<?php limon(@$panel_anasayfa_sol_alt_kod); ?>						
					</div>
					<div class="col-md-20">
						<?php limon(@$panel_anasayfa_sag_kod); ?>
						
						<?php
						$domain=domain();
						$lisans_bitis_tarih_saat=isset($limonsoft_array["lisans_bitis_tarih_saat"]) ? $limonsoft_array["lisans_bitis_tarih_saat"] : null;
						if ($lisans_bitis_tarih_saat) {
							$lisans_kalan_gun=kac_gun_kaldi($lisans_bitis_tarih_saat);
							$lisans_son_odeme_tarihi=tarih_saat_hesapla("+15 days",$lisans_bitis_tarih_saat);
							if ($lisans_kalan_gun<30) {
								$alert="alert-danger";
							} else if ($lisans_kalan_gun<60) {
								$alert="alert-warning";
							} else {
								$alert="alert alert-dark";
							}
							?>
							<div class="alert <?php echo $alert; ?>">
								<p data-toggle="tooltip" title="<?php echo _LANG('p_lisans_yenileme_uyarisi', array($lisans_son_odeme_tarihi)); ?>"><i class="fa fa-info-circle"></i><?php echo _LANG('p_lisansiniz'); ?> <strong><?php echo $lisans_kalan_gun; ?> <?php echo _LANG('p_gun_ici'); ?></strong> <?php echo _LANG('p_uyelik_bitti'); ?> <?php if (!$limonsoft_bayi) { ?><a href="https://www.limontasarim.com/yonlendir.php?r=lisans&domain=<?php echo $domain; ?>" class="alert-link" target="_blank"><?php echo _LANG('p_sure_uzat'); ?></a><?php } ?></p>
							</div>
						<?php }	
						
						$garanti_bitis_tarih_saat=$limonsoft_array["garanti_bitis_tarih_saat"];
						if ($garanti_bitis_tarih_saat) {
							$garanti_kalan_gun=kac_gun_kaldi($garanti_bitis_tarih_saat);
							$garanti_son_odeme_tarihi=tarih_saat_hesapla("+15 days",$garanti_bitis_tarih_saat);
							if ($garanti_kalan_gun<30) {
								$alert="alert-danger";
							} else if ($garanti_kalan_gun<60) {
								$alert="alert-warning";
							} else {
								$alert="alert alert-white";
							}
							?>
							<div class="alert <?php echo $alert; ?>">
								<p data-toggle="tooltip" title="<?php echo _LANG('p_garanti_kapsam_ipucu', array($garanti_bitis_tarih_saat)); ?>"><i class="fa fa-info-circle"></i><?php if ($garanti_kalan_gun<0) {echo _LANG('p_garanti_sona_ermis', array($garanti_bitis_tarih_saat));} else {echo _LANG('p_garanti_gun_kaldi', array($garanti_kalan_gun));} ?> <?php if (!$limonsoft_bayi) { ?><a href="https://www.limontasarim.com/yonlendir.php?r=garanti&domain=<?php echo $domain; ?>" class="alert-link" target="_blank"><?php echo _LANG('p_sure_uzat'); ?></a><?php } ?></p>
							</div>
							<?php
						}
						
						$hosting_bitis_tarih=isset($limonsoft_array["hosting"]["bitis_tarih"]) ? $limonsoft_array["hosting"]["bitis_tarih"] : null;
						if ($hosting_bitis_tarih) {
							$hosting_kalan_gun=kac_gun_kaldi($hosting_bitis_tarih);
							$hosting_son_odeme_tarihi=tarih_saat_hesapla("+15 days","$hosting_bitis_tarih 00:00:00");
							if ($hosting_kalan_gun<30) {
								$alert="alert-danger";
							} else if ($hosting_kalan_gun<60) {
								$alert="alert-warning";
							} else {
								$alert="alert alert-white";
							}
							?>
							<div class="alert <?php echo $alert; ?>">
							<p data-toggle="tooltip" title="<?php echo _LANG('p_hosting_uyarisi', array($hosting_bitis_tarih, $hosting_son_odeme_tarihi)); ?>"><i class="fa fa-info-circle"></i><?php if ($hosting_kalan_gun<0) {echo _LANG('p_hosting_sona_ermis', array($hosting_bitis_tarih));} else {echo _LANG('p_hosting_gun_kaldi', array($hosting_kalan_gun));} ?> <?php if (!$limonsoft_bayi) { ?><a href="https://www.limontasarim.com/yonlendir.php?r=hosting&domain=<?php echo $domain; ?>" class="alert-link" target="_blank"><?php echo _LANG('p_sure_uzat'); ?></a><?php } ?></p>
							</div>
							<?php
						}
						
						?>
						
						<?php if ($lpanel_genel_durum==1) { ?>
						<div class="home_sidebar mb20 genel_durum">
							<h3><?php echo _LANG('p_genel_durum'); ?></h3>
							<?php
							$toplam_uye=mysqli_num_rows(mysqli_query($baglan,"select * from uyeler where durum='1'"));
							$aktif_eklentiler=mysqli_num_rows(mysqli_query($baglan,"select * from ayarlar where anahtar='eklenti'"));
							$tema_ne=tema_ne();
							?>
							<div class="row">
								<?php foreach($icerik_tipleri as $yaz) {
									$ikon=$yaz["ikon"];
									$name=$yaz["id"];
									$baslik=$yaz["baslik"];
									@$durumlar=$yaz["durumlar"];
									if (is_array($durumlar)) {
										$toplam=mysqli_num_rows(mysqli_query($baglan,"select * from icerik where tip='$name'"));
									} else {
										$toplam=mysqli_num_rows(mysqli_query($baglan,"select * from icerik where tip='$name' and durum='1'"));
									}
									if ($toplam>0) {
								?>
									<div class="col-md-20 col-xs-20" style="margin-bottom:6px;">
										<a href="icerik.php?tip=<?php echo $name; ?>"><i class="fa fa-<?php echo $ikon; ?>" aria-hidden="true"></i><?php echo $toplam; ?> <span class="text-lowercase"><?php echo $baslik; ?></span></a>
									</div>									
								<?php } } ?>
								<div class="col-md-20 col-xs-20">
									<a href="uyeler.php"><i class="fa fa-users" aria-hidden="true"></i><?php echo $toplam_uye; ?> <?php echo _LANG('p_uye'); ?></a>
								</div>
							</div>
							<div class="clearfix mb10"></div>
							<p><?php if ($tema_destegi==1) { ?><i class="fa fa-tv" aria-hidden="true"></i><?php echo _LANG('p_aktif_tema'); ?>: <a href="temalar.php"><?php echo $tema_ne; ?></a> <?php echo _LANG('p_ve'); ?> <?php } else {echo "<i class='fa fa-plug' aria-hidden='true'></i>";} ?><a href="eklentiler.php"><?php echo $aktif_eklentiler; ?> <?php echo _LANG('p_aktif_eklenti'); ?></a></p>
							<div class="clearfix"></div>
							<p style="margin-bottom:0 !important;" class="pull-left"><?php echo _LANG('p_altyapi'); ?>: <a href="http://www.limontasarim.com/?ref_url=<?php echo site_url(); ?>" target="_blank"><?php echo $lpanel_baslik; ?> <?php $versiyon=ayar_getir("versiyon"); echo $versiyon; ?></a></p>
							<?php
							$guncel_versiyon=$limonsoft_array["versiyon"]; 
							if ($guncel_versiyon) {
								$versiyon=ayar_getir("versiyon");
								if (str_replace(".",null,$versiyon)<str_replace(".",null,$guncel_versiyon)) {
									$sistem_guncellemeleri=ayar("sistem_guncellemeleri");
									if ($sistem_guncellemeleri==1) {
										echo "<div id='limonsoft_versiyon' class='pull-left' style='margin-left:15px;'><a href='guncellemeler.php' class='tdn'><span class='label label-danger'><i class='fa fa-refresh'></i>"._LANG('p_ilan_yazilimi_yayinlandi', array($guncel_versiyon))."</span></a></div>";
									} else {
										if ($limonsoft_bayi) {
											echo "<div id='limonsoft_versiyon' class='pull-left' style='margin-left:15px;'><span class='label label-danger'><i class='fa fa-refresh'></i>"._LANG('p_ilan_yazilimi_yayinlandi', array($guncel_versiyon))."</span></div>";
										} else {
											echo "<div id='limonsoft_versiyon' class='pull-left' style='margin-left:15px;'><a href='https://www.limontasarim.com' class='tdn' target='_blank'><span class='label label-danger'><i class='fa fa-refresh'></i>"._LANG('p_ilan_yazilimi_yayinlandi', array($guncel_versiyon))."</span></a></div>";
										}
									}
								}
							}
							?>
							
							<div class="clearfix"></div>
						</div>
						<?php } ?>
						<?php limon(@$panel_anasayfa_sag_alt_kod); ?>
						<div id="limonsoft_duyurular"></div>
					</div>
				</div>
			</div>
			<div class="clearfix mb20"></div>
		</div>
	</div>
</div>
<?php include("inc/alt.php"); ?>
<?php include("inc/footer-kod.php"); ?>
<script type="text/javascript">
$(function(){
	$.ajax({
		type:"post",
		url:"inc/ajax.php?islem=limonsoft_duyurular",
		success: function(cevap){
			$("#limonsoft_duyurular").html(cevap);
		}
	});
});
</script>
</body>
</html>