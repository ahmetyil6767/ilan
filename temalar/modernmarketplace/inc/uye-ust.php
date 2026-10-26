<?php 
if(count(get_included_files())==1) {die();}
if (!@$id) {
$id=$oturum_id;
}
$ad_soyad=getir("uyeler","ad_soyad",$id);
$guvenilir=uye_bilgi("guvenilir",$id);
$profil_gorseli=uye_bilgi("profil_gorseli",$id);
$profil_gorseli_boyut=thumbnail_url("150_150",$profil_gorseli);
if (!$profil_gorseli or url_kontrol($profil_gorseli)==false) {
	$profil_gorseli_boyut=tema_dizini()."/img/yok/profil.png";
}
$kapak_gorseli=uye_bilgi("kapak_gorseli",$id);
if (!$kapak_gorseli or url_kontrol($kapak_gorseli)==false) {
	$kapak_gorseli=tema_dizini()."/img/yok/kapak.png";
}
$uye_tarih_saat=getir("uyeler","tarih_saat",$id);
$uye_tarih_saat_parcala=explode(" ",$uye_tarih_saat);
$uyelik_tarihi=tarih_bicimlendir($uye_tarih_saat_parcala[0]);
$uyelik_tip=uye_bilgi("tip",$id);
$uyelik_paket=uye_bilgi("paket",$id);
$uyelik_paket_adi=liste_bilgi("paket_adi",$uyelik_paket);
$profil_url=profil_link_getir($id);
?>
<?php 
$email_dogrulama=tema_ayar("email_dogrulama");
if ($email_dogrulama==1 and $oturum_id==$id) { 
if (getir("uyeler","durum",$oturum_id)==0) {
$uye_email_onay_kodu_tarih_saat=uye_bilgi("email_onay_kodu_tarih_saat",$oturum_id);
?>
<div class="alert alert-danger" style="margin-bottom:10px;">Sayın <?php echo $oturum_ad_soyad; ?>, lütfen e-posta adresinizi doğrulayın. <?php if ($uye_email_onay_kodu_tarih_saat) { ?>Onay kodu şu tarihte gönderildi: <?php echo $uye_email_onay_kodu_tarih_saat; ?><a href="<?php echo ozel_url("profil-bilgilerim"); ?>?send_mail=<?php echo getir("uyeler","email_adresi",$oturum_id); ?>" class="alert-link" style="margin-left:15px;"><i class="fa fa-send"></i><?php echo _LANG("account_resend"); ?></a><?php } else { ?><a href="<?php echo ozel_url("profil-bilgilerim"); ?>?send_mail=<?php echo getir("uyeler","email_adresi",$oturum_id); ?>" class="alert-link" style="margin-left:15px;"><i class="fa fa-send"></i><?php echo _LANG("account_send_code"); ?></a><?php } ?></div>
<?php } } ?>
<div class="kapak">
	<div id="kapak_gorsel" class="visible-sm visible-md visible-lg">
		<img src="<?php echo $kapak_gorseli; ?>" alt="<?php echo $ad_soyad; ?>" class="img-responsive noselect" />
		<?php if ($oturum_id==$id) { ?><div id="kapak_yukle" style="display:none;"><a href="<?php echo ozel_url("kapak-gorseli"); ?>" class="btn btn-default btn-xs"><i class="ion-image"></i><?php echo _LANG("title_upload_cover"); ?></a></div><?php } ?>
	</div>
	<div id="profil_gorsel" class="resim visible-sm visible-md visible-lg">
		<img src="<?php echo $profil_gorseli_boyut; ?>" alt="<?php echo $ad_soyad; ?>" class="img-responsive noselect"/>
		<?php if ($oturum_id==$id) { ?><div id="profil_yukle" style="display:none;"><a href="<?php echo ozel_url("profil-gorseli"); ?>" class="btn btn-default btn-block btn-xs"><i class="ion-image"></i><?php echo _LANG("pn_gorsel_yukle"); ?></a></div><?php } ?>
	</div>
	<div class="alt gorsel_var">
		<img src="<?php echo $profil_gorseli_boyut; ?>" alt="<?php echo $ad_soyad; ?>" class="img-responsive noselect visible-xs mobil_uye_profil"/>
		<div class="pull-left">
			<h1><?php echo $ad_soyad; ?></h1>
			<?php if ($uyelik_tip=="Kurumsal" and $guvenilir=="Evet") { ?>
				<div class="label label-success text-uppercase"><i class="ion-ribbon-b"></i>Güvenilir <?php echo $uyelik_tip; ?> ÜYE</div>
			<?php } else { ?>
				<div class="label label-primary text-uppercase"><?php echo $uyelik_tip; ?> ÜYE</div>
			<?php } ?>
			<?php 
			if ($oturum_id==$id) {$planlar_url=ozel_url("plan-detaylari");
			echo "<span class='label label-info text-uppercase'><i class='ion-pricetag'></i>$uyelik_paket_adi</span>";} ?>
			<div class="clearfix"></div>
			<p><?php echo _LANG('uy_uyelik_tarihi'); ?> <?php echo $uyelik_tarihi; ?></p>
		</div>
		<div class="pull-right visible-sm visible-md visible-lg">
			<?php if ($id==$oturum_id) {
			if (@$page!="kapak-gorseli" and @$page!="profil-gorseli" and @$page!="profil-guncelle" and @$page!="magazalarim") { ?>
			<a href="<?php echo ozel_url("profil-guncelle"); ?>" class="btn btn-default btn-sm"><i class="ion-settings"></i><?php echo _LANG("nav_update_profile"); ?></a>
			<?php } else { ?>
			<a href="<?php echo profil_link_getir($id); ?>" class="btn btn-default btn-sm"><i class="ion-person"></i><?php echo _LANG("back_to_profile"); ?></a>
			<?php }
			} ?>
		</div>
	</div>
</div>
<div class="clearfix mb20 visible-sm visible-md visible-lg"></div>