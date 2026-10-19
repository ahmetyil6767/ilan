<?php 
if(count(get_included_files())==1) {die();}
if (!$oturum_id) {
	$_SESSION["mesaj"]=array(_LANG('msj_uye_girisi_gerekli'),"danger");
	yonlendir(ozel_url("giris-yap"));
	exit();
}

@$send_mail=$_GET["send_mail"];
if (@$send_mail) {
	$uye_id=id_getir("uyeler","email_adresi",$send_mail);
	if ($oturum_id==$uye_id and getir("uyeler","durum",$oturum_id)==0) {
		
		$ad_soyad=getir("uyeler","ad_soyad",$uye_id);
		$email_adresi=getir("uyeler","email_adresi",$uye_id);
		
		$kontrol_tarihi=uye_bilgi("email_onay_kodu_tarih_saat",$oturum_id);
		if ($kontrol_tarihi) {
			if (kac_dk_gecti($kontrol_tarihi)>3) {
				// E-posta gönderimi başladı
				$mail_baslik=tema_ayar("mail_baslik");
				$site_url=site_url();

				$tarih_saat=tarih_saat();
				$email_onay_kodu=strtoupper(md5(benzersiz()));
				uye_bilgi_guncelle("email_onay_kodu",$email_onay_kodu,$uye_id);
				uye_bilgi_guncelle("email_onay_kodu_tarih_saat",$tarih_saat,$uye_id);
				$uye_email=getir("uyeler","email_adresi",$uye_id);
				$email_dogrulama_url=site_url()."/?email=$uye_email&key=$email_onay_kodu";

				$ileti="
				<p>Merhaba <strong>$ad_soyad</strong></p>
				<p>" . _LANG('hb_web_sitemizin_tum_islevlerinden_faydalanmak_ic') . "</p>
				<p><a href=\'$email_dogrulama_url\' target=\'_blank\'>E-posta adresinizi doğrulamak için tıklayın.</a></p>
				<p>" . _LANG('hb_baglanti_acilmiyorsa_lutfen_asagidaki_url_yi_k') . "</p>
				<p>$email_dogrulama_url</p>
				";
				auto_mail_add($ad_soyad,$email_adresi,"E-posta adresinizi doğrulayın - $mail_baslik",$ileti);	
				$_SESSION["mesaj"]=array(_LANG('msj_eposta_dogrulama_gonderildi'),"success");
			} else {
	$_SESSION["mesaj"]=array(_LANG('pb_dogrulama_gonderildi', array($kontrol_tarihi)),"warning");
			}
		} else {
			// E-posta gönderimi başladı
			$mail_baslik=tema_ayar("mail_baslik");
			$site_url=site_url();

			$tarih_saat=tarih_saat();
			$email_onay_kodu=strtoupper(md5(benzersiz()));
			uye_bilgi_guncelle("email_onay_kodu",$email_onay_kodu,$uye_id);
			uye_bilgi_guncelle("email_onay_kodu_tarih_saat",$tarih_saat,$uye_id);
			$uye_email=getir("uyeler","email_adresi",$uye_id);
			$email_dogrulama_url=site_url()."/?email=$uye_email&key=$email_onay_kodu";

			$ileti="
			<p>Merhaba <strong>$ad_soyad</strong></p>
			<p>" . _LANG('hb_web_sitemizin_tum_islevlerinden_faydalanmak_ic') . "</p>
			<p><a href=\'$email_dogrulama_url\' target=\'_blank\'>E-posta adresinizi doğrulamak için tıklayın.</a></p>
			<p>" . _LANG('hb_baglanti_acilmiyorsa_lutfen_asagidaki_url_yi_k') . "</p>
			<p>$email_dogrulama_url</p>
			";
			auto_mail_add($ad_soyad,$email_adresi,"E-posta adresinizi doğrulayın - $mail_baslik",$ileti);	
			$_SESSION["mesaj"]=array(_LANG('msj_eposta_dogrulama_gonderildi'),"success");
		}
		yonlendir(ozel_url("profil-bilgilerim"));
	} else {
		$_SESSION["mesaj"]=array("Hata","danger");
		yonlendir(ozel_url("profil-bilgilerim"));
	}
}

tema_header(); ?>
<div class="container profil arsiv mt20">
	<?php include("temalar/$aktif_tema/inc/uye-ust.php"); ?>
	<?php include("temalar/$aktif_tema/inc/uye-limit-bilgi.php"); ?>
	<div class="row">
		<div class="col-md-9 sidebar">
			<?php if ($oturum_id==$id) {include("temalar/$aktif_tema/inc/uye-sol-menu.php");} ?>
		</div>
		<div class="col-md-33">		
			<?php include("temalar/$aktif_tema/inc/alert.php"); ?>
			<?php include("temalar/$aktif_tema/inc/profil-bilgileri.php"); ?>
		</div>
	</div>
</div>
<?php tema_footer(); ?>