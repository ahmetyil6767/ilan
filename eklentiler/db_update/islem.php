<?php
if(count(get_included_files())==1) {die();}
$buton=$_POST["buton"];
if ($buton) {
mysqli_query($baglan,"SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION,ALLOW_INVALID_DATES';");
mysqli_query($baglan,"ALTER TABLE `ajax_upload` CHANGE `vitrin` `vitrin` INT(1) NULL DEFAULT NULL;");
mysqli_query($baglan,"ALTER TABLE `ajax_upload` ADD `sira` INT(10) NOT NULL AFTER `vitrin`;");
mysqli_query($baglan,"ALTER TABLE `auto_mail` CHANGE `id` `id` INT(10) NOT NULL AUTO_INCREMENT, CHANGE `ad_soyad` `ad_soyad` VARCHAR(80) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL, CHANGE `baslik` `baslik` VARCHAR(160) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL, CHANGE `gonderim_tarih_saat` `gonderim_tarih_saat` VARCHAR(20) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL;");
mysqli_query($baglan,"ALTER TABLE `ayarlar` CHANGE `deger` `deger` TEXT CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL;");
mysqli_query($baglan,"ALTER TABLE `bildirimler` CHANGE `bildirim` `bildirim` TEXT CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL, CHANGE `uye_id` `uye_id` INT(10) NULL DEFAULT NULL, CHANGE `tarih_saat` `tarih_saat` VARCHAR(20) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL, CHANGE `tip` `tip` INT(11) NULL DEFAULT NULL, CHANGE `okundu` `okundu` INT(1) NULL DEFAULT NULL;");
mysqli_query($baglan,"ALTER TABLE `bilesenler` CHANGE `baslik` `baslik` VARCHAR(160) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL, CHANGE `sira` `sira` INT(10) NULL DEFAULT NULL;");
mysqli_query($baglan,"ALTER TABLE `dopingler` CHANGE `bitis_tarih_saat` `bitis_tarih_saat` VARCHAR(200) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL, CHANGE `durum` `durum` INT(1) NULL DEFAULT NULL, CHANGE `siparis_id` `siparis_id` INT(10) NULL DEFAULT NULL;");
mysqli_query($baglan,"ALTER TABLE `ekstra_bilgi` CHANGE `deger` `deger` TEXT CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL;");
mysqli_query($baglan,"ALTER TABLE `favoriler` CHANGE `tarih` `tarih` VARCHAR(20) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL;");
mysqli_query($baglan,"ALTER TABLE `galeri` CHANGE `baslik` `baslik` VARCHAR(160) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL, CHANGE `url` `url` VARCHAR(160) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL, CHANGE `icerik` `icerik` TEXT CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL;");
mysqli_query($baglan,"ALTER TABLE `gorusmeler` CHANGE `tarih_saat` `tarih_saat` VARCHAR(20) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL;");
mysqli_query($baglan,"ALTER TABLE `icerik` CHANGE `baslik` `baslik` VARCHAR(160) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL, CHANGE `url` `url` VARCHAR(160) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL, CHANGE `icerik` `icerik` TEXT CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL, CHANGE `kategori` `kategori` VARCHAR(160) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL, CHANGE `g_tarih_saat` `g_tarih_saat` VARCHAR(20) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL;");
mysqli_query($baglan,"ALTER TABLE `kategoriler` CHANGE `ust_kategori` `ust_kategori` INT(10) NULL DEFAULT NULL, CHANGE `icerik` `icerik` INT(10) NULL DEFAULT NULL, CHANGE `url_update` `url_update` INT(1) NULL DEFAULT NULL;");
mysqli_query($baglan,"ALTER TABLE `kayitlar` CHANGE `veri_2` `veri_2` VARCHAR(200) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL;");
mysqli_query($baglan,"ALTER TABLE `linkler` CHANGE `baslik` `baslik` VARCHAR(160) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL, CHANGE `url` `url` VARCHAR(160) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL, CHANGE `ikon` `ikon` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL, CHANGE `sira` `sira` INT(11) NULL DEFAULT NULL, CHANGE `class` `class` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL, CHANGE `ust_link` `ust_link` INT(10) NULL DEFAULT NULL;");
mysqli_query($baglan,"ALTER TABLE `listeler` CHANGE `id` `id` INT(10) NULL DEFAULT NULL;");
mysqli_query($baglan,"ALTER TABLE `mahalleler` CHANGE `semt_id` `semt_id` INT(11) NULL DEFAULT NULL, CHANGE `posta_kodu` `posta_kodu` VARCHAR(5) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;");
mysqli_query($baglan,"ALTER TABLE `mesajlar` CHANGE `mesaj` `mesaj` TEXT CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL, CHANGE `okundu` `okundu` INT(1) NULL DEFAULT NULL;");
mysqli_query($baglan,"ALTER TABLE `odemeler` CHANGE `ad_soyad` `ad_soyad` VARCHAR(20) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL, CHANGE `hesap` `hesap` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL, CHANGE `tutar` `tutar` INT(10) NULL DEFAULT NULL, CHANGE `mesaj` `mesaj` TEXT CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL, CHANGE `aciklama` `aciklama` VARCHAR(300) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL;");
mysqli_query($baglan,"ALTER TABLE `postviews` CHANGE `tarih` `tarih` VARCHAR(20) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL, CHANGE `son_ziyaret` `son_ziyaret` VARCHAR(20) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL, CHANGE `hit` `hit` INT(10) NULL DEFAULT '0';");
mysqli_query($baglan,"ALTER TABLE `siparisler` CHANGE `satici_id` `satici_id` INT(10) NOT NULL DEFAULT '0', CHANGE `ilan_id` `ilan_id` INT(10) NOT NULL DEFAULT '0', CHANGE `magaza_id` `magaza_id` INT(10) NOT NULL DEFAULT '0', CHANGE `paket_id` `paket_id` INT(10) NOT NULL DEFAULT '0', CHANGE `satici_durum` `satici_durum` INT(1) NOT NULL DEFAULT '0', CHANGE `alici_onay` `alici_onay` INT(1) NOT NULL DEFAULT '0', CHANGE `satici_odeme` `satici_odeme` INT(1) NOT NULL DEFAULT '0';");
mysqli_query($baglan,"ALTER TABLE `uyeler` CHANGE `url` `url` VARCHAR(160) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL, CHANGE `parola` `parola` VARCHAR(40) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL, CHANGE `email_adresi` `email_adresi` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_turkish_ci NULL DEFAULT NULL;");
mysqli_query($baglan,"ALTER TABLE `yorumlar` CHANGE `cevap` `cevap` INT(10) NULL DEFAULT NULL, CHANGE `uye_id` `uye_id` INT(10) NULL DEFAULT NULL;");


mysqli_query($baglan,"ALTER TABLE `gorusmeler` ADD `ilan_id` INT(11) NULL AFTER `uye_2`;");
mysqli_query($baglan,"ALTER TABLE `gorusmeler` ADD `sil_1` INT(11) NULL DEFAULT '0' AFTER `uye_2`;");
mysqli_query($baglan,"ALTER TABLE `gorusmeler` ADD `sil_2` INT(11) NULL DEFAULT '0' AFTER `sil_1`;");
mysqli_query($baglan,"ALTER TABLE `siparisler` CHANGE `tutar` `tutar` VARCHAR(50) NOT NULL;");
	
	
$_SESSION["mesaj"]=array(_LANG('msj_db_update_basarili'),"success");
ayar_guncelle("db_update",197);
yonlendir(browser_url());
}
?>
<div class="baslik">DB UPDATE</div>
<div class="row">
	<div class="col-md-30">
		<p><?php echo _LANG('e_db_update_1_9_7'); ?></p>
		<?php if (!ayar_getir("db_update") or ayar_getir("db_update")!=197) { ?>
		<div class="alert alert-danger"><?php echo _LANG('e_db_guncelle_gerek'); ?></div>
		<?php } else { ?>
		<div class="alert alert-success"><?php echo _LANG('e_db_zaten_guncel'); ?></div>
		<?php } ?>
		<form action="" method="post">
			<button type="submit" name="buton" value="url" class="btn btn-default"><i class="fa fa-refresh"></i><?php echo _LANG('e_db_guncelle'); ?></button>
		</form>
	</div>
</div>