<?php
if(count(get_included_files())==1) {die();}
$buton=$_POST["buton"];
if ($buton) {

$sql=mysqli_query($baglan,"ALTER TABLE `ajax_upload` ADD INDEX(`kat`)");
$sql=mysqli_query($baglan,"ALTER TABLE `ajax_upload` ADD INDEX(`uye_id`)");

$sql=mysqli_query($baglan,"ALTER TABLE `auto_mail` ADD INDEX(`durum`)");

$sql=mysqli_query($baglan,"ALTER TABLE `bilesenler` ADD INDEX(`alan`)");

$sql=mysqli_query($baglan,"ALTER TABLE `ayarlar` ADD INDEX(`anahtar`)");

$sql=mysqli_query($baglan,"ALTER TABLE `bildirimler` ADD INDEX(`uye_id`)");
$sql=mysqli_query($baglan,"ALTER TABLE `bildirimler` ADD INDEX(`tip`)");

$sql=mysqli_query($baglan,"ALTER TABLE `bildirimler` ADD INDEX(`tip`)");

$sql=mysqli_query($baglan,"ALTER TABLE `dopingler` ADD INDEX(`doping`)");
$sql=mysqli_query($baglan,"ALTER TABLE `dopingler` ADD INDEX(`durum`)");

$sql=mysqli_query($baglan,"ALTER TABLE `ekstra_bilgi` ADD INDEX(`id`)");
$sql=mysqli_query($baglan,"ALTER TABLE `ekstra_bilgi` ADD INDEX(`anahtar`)");

$sql=mysqli_query($baglan,"ALTER TABLE `favoriler` ADD INDEX(`uye_id`)");

$sql=mysqli_query($baglan,"ALTER TABLE `galeri` ADD INDEX(`icerik_id`)");
$sql=mysqli_query($baglan,"ALTER TABLE `galeri` ADD INDEX(`sira`)");
$sql=mysqli_query($baglan,"ALTER TABLE `galeri` ADD INDEX(`resim`)");

$sql=mysqli_query($baglan,"ALTER TABLE `gorusmeler` ADD INDEX(`uye_1`)");
$sql=mysqli_query($baglan,"ALTER TABLE `gorusmeler` ADD INDEX(`uye_2`)");

$sql=mysqli_query($baglan,"ALTER TABLE `icerik` ADD INDEX(`baslik`)");
$sql=mysqli_query($baglan,"ALTER TABLE `icerik` ADD INDEX(`kategori`)");
$sql=mysqli_query($baglan,"ALTER TABLE `icerik` ADD INDEX(`yazar`)");
$sql=mysqli_query($baglan,"ALTER TABLE `icerik` ADD INDEX(`durum`)");

$sql=mysqli_query($baglan,"ALTER TABLE `ilceler` ADD INDEX(`il_id`)");

$sql=mysqli_query($baglan,"ALTER TABLE `kategoriler` ADD INDEX(`tip`)");
$sql=mysqli_query($baglan,"ALTER TABLE `kategoriler` ADD INDEX(`sinif`)");
$sql=mysqli_query($baglan,"ALTER TABLE `kategoriler` ADD INDEX(`ust_kategori`)");

$sql=mysqli_query($baglan,"ALTER TABLE `kayitlar` ADD INDEX(`uye_id`)");
$sql=mysqli_query($baglan,"ALTER TABLE `kayitlar` ADD INDEX(`tip`)");

$sql=mysqli_query($baglan,"ALTER TABLE `linkler` ADD INDEX(`sira`)");

$sql=mysqli_query($baglan,"ALTER TABLE `listeler` ADD INDEX(`tip`)");

$sql=mysqli_query($baglan,"ALTER TABLE `magazalar` ADD INDEX(`uye_id`)");
$sql=mysqli_query($baglan,"ALTER TABLE `magazalar` ADD INDEX(`durum`)");

$sql=mysqli_query($baglan,"ALTER TABLE `magaza_uye` ADD INDEX(`magaza_id`)");

$sql=mysqli_query($baglan,"ALTER TABLE `mahalleler` ADD INDEX(`ilce_id`)");

$sql=mysqli_query($baglan,"ALTER TABLE `mesajlar` ADD INDEX(`uye_id`)");
$sql=mysqli_query($baglan,"ALTER TABLE `mesajlar` ADD INDEX(`kime`)");
$sql=mysqli_query($baglan,"ALTER TABLE `mesajlar` ADD INDEX(`gorusme_id`)");

$sql=mysqli_query($baglan,"ALTER TABLE `odemeler` ADD INDEX(`uye_id`)");

$sql=mysqli_query($baglan,"ALTER TABLE `postviews` ADD INDEX(`id`)");
$sql=mysqli_query($baglan,"ALTER TABLE `postviews` ADD INDEX(`hit`)");

$sql=mysqli_query($baglan,"ALTER TABLE `siparisler` ADD INDEX(`tip`)");
$sql=mysqli_query($baglan,"ALTER TABLE `siparisler` ADD INDEX(`durum`)");

$sql=mysqli_query($baglan,"ALTER TABLE `uyeler` ADD INDEX(`ad_soyad`)");
$sql=mysqli_query($baglan,"ALTER TABLE `uyeler` ADD UNIQUE(`email_adresi`)");

$sql=mysqli_query($baglan,"ALTER TABLE `yorumlar` ADD INDEX(`icerik_id`)");

$_SESSION["mesaj"]=array(_LANG('msj_db_optimize_tamam'),"success");
ayar_guncelle("db_optimize",1);
yonlendir(browser_url());

}
$db_optimize=ayar_getir("db_optimize");
?>
<div class="baslik">DB OPTIMIZE</div>
<div class="row">
	<div class="col-md-30">
		<p><?php echo _LANG('e_db_optimize_bir_kez'); ?></p>
		<p><?php echo _LANG('e_db_optimize_sonra'); ?></p>
		<?php if ($db_optimize!=1) { ?>
		<form action="" method="post">
			<button type="submit" name="buton" value="mysql" class="btn btn-default"><i class="fa fa-database"></i><?php echo _LANG('e_db_optimize_calistir'); ?></button>
		</form>
		<?php } else { ?>
		<div class="alert alert-success"><?php echo _LANG('e_db_optimize_tamam'); ?></div>
		<?php } ?>
	</div>
</div>