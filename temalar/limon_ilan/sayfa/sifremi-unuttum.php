<?php 
if(count(get_included_files())==1) {die();}
if ($oturum_id) {
	yonlendir(profil_link_getir($oturum_id));
	exit();
}
@$buton=$_POST["buton"];
@$email_adresi=mysqli_real_escape_string($baglan,$_POST["email_adresi"]);

@$email=$_GET["email"];
@$dogrulama=$_GET["dogrulama"];

if ($email and $dogrulama) {
	$uye_id=uye_id_getir($email);
	$uye_dogrulama_kontrol=uye_bilgi("parola_dogrulama",$uye_id);
	if ($uye_dogrulama_kontrol==$dogrulama) {
		$adim_2=1;
	} else {
		$_SESSION["mesaj"]=array(_LANG('msj_hatali_dogrulama_kodu'),"danger");
	}
}

@$olustur_buton=$_POST["olustur_buton"];
if ($olustur_buton) {
	$parola=mysqli_real_escape_string($baglan,$_POST["parola"]);
	$parola_tekrar=mysqli_real_escape_string($baglan,$_POST["parola_tekrar"]);
	if ($parola==$parola_tekrar) {
		parola_guncelle_hash($uye_id, $parola);
		$dogrulama_sil=uye_bilgi_guncelle("parola_dogrulama",null);
		$_SESSION["mesaj"]=array(_LANG('msj_parola_guncellendi'),"success");
		yonlendir(ozel_url("giris-yap"));
	}
}
tema_header(); ?>
<div class="container mt50 giris">
	<div class="row dtable">
		<div class="col-md-18 col-md-offset-12">
			<?php include("temalar/$aktif_tema/inc/alert.php"); ?>
			<div class="clearfix mb10"></div>
			<?php if (@$adim_2==1) { ?>
			<div class="panel panel-default beyaz yukseklik">
	<div class="panel-heading"><?php echo _LANG("su_panel_yeni_sifre"); ?></div>
					<?php
					if ($buton) {
						$uye_mevcut_mu=uye_mevcut_mu($email_adresi);
						if ($uye_mevcut_mu==1) {
							$uye_id=uye_id_getir($email_adresi);
							$gecici_dogrulama_kodu=strtoupper(md5(md5(rand(10000000,99999999999999999))));
							uye_bilgi_guncelle("parola_dogrulama",$gecici_dogrulama_kodu,$uye_id);
							
							// E-posta gönderimi başladı
							$mail_baslik=tema_ayar("mail_baslik");
							$site_url=site_url();		
							$mail_ad_soyad=getir("uyeler","ad_soyad",$uye_id);
							$ileti="
							<p>Merhaba <strong>$mail_ad_soyad</strong></p>
							<p>" . _LANG('hb_sifrenizi_sifirlamak_icin_lutfen_asagidaki_lin') . "</p>
							<p>Doğrulama anahtarı: $gecici_dogrulama_kodu</p>
							<p><a href=\"$site_url/sayfa/sifremi-unuttum/?email=$email_adresi&dogrulama=$gecici_dogrulama_kodu\">Yeni Şifre Oluştur</a></p>";
							auto_mail_add($mail_baslik,$email_adresi,"Şifre Sıfırlama Onayı - $mail_baslik",$ileti);	
			
							$_SESSION["mesaj"]=array(_LANG('msj_sifre_sifirlama_gonderildi'),"success");
							yonlendir(site_url());
							
						} else {
	echo "<div class='alert alert-danger'>" . _LANG('su_kullanici_bulunamadi') . "</div>";
						}	
					}
					?>
				<div class="panel-body giris_form gradient" style="padding:30px !important;">
				<form action="" method="post">

					<div class="form-group">
						<label for="parola">Parola</label>
						<input type="password" class="form-control" id="parola" name="parola" placeholder="Parola">
					</div>
					
					<div class="form-group">
						<label for="parola">Parola Tekrar</label>
						<input type="password" class="form-control" id="parola_tekrar" name="parola_tekrar" placeholder="Parola Tekrar">
					</div>
					
					<button type="submit" name="olustur_buton" class="btn btn-primary" value="Gönder"><i class="fa fa-lock"></i>Gönder</button>
				
				</form>
				</div>
			</div>
			
			<?php } else { ?>
			<div class="panel panel-default beyaz yukseklik">
	<div class="panel-heading"><?php echo _LANG("su_panel_sifremi_unuttum"); ?></div>
					<?php
					if ($buton) {
						$uye_mevcut_mu=uye_mevcut_mu($email_adresi);
						if ($uye_mevcut_mu==1) {
							$uye_id=uye_id_getir($email_adresi);
							$gecici_dogrulama_kodu=strtoupper(md5(rand(1000,9999)));
							uye_bilgi_guncelle("parola_dogrulama",$gecici_dogrulama_kodu,$uye_id);
							
							// E-posta gönderimi başladı
							$mail_baslik=tema_ayar("mail_baslik");
							$site_url=site_url();		
							$mail_ad_soyad=getir("uyeler","ad_soyad",$uye_id);
							$ileti="
							<p>Merhaba <strong>$mail_ad_soyad</strong></p>
							<p>" . _LANG('hb_sifrenizi_sifirlamak_icin_lutfen_asagidaki_lin') . "</p>
							<p>Doğrulama anahtarı: $gecici_dogrulama_kodu</p>
							<p><a href=\"$site_url/sayfa/sifremi-unuttum/?email=$email_adresi&dogrulama=$gecici_dogrulama_kodu\">Yeni Şifre Oluştur</a></p>";
	auto_mail_add($ad_soyad,$email_adresi,_LANG("su_sifre_sifirlama_onayi", array($mail_baslik)),$ileti);
			
							$_SESSION["mesaj"]=array(_LANG('msj_sifre_sifirlama_gonderildi'),"success");
							yonlendir(site_url());
							
						} else {
							echo "<div class='alert alert-danger'>Bu email adresi ile kayıtlı kullanıcı bulunmamaktadır.</div>";
						}	
					}
					?>
				<div class="panel-body giris_form gradient" style="padding:30px !important;">
				<form action="" method="post">

	<p><?php echo _LANG("su_eposta_girin"); ?></p>
					<div class="form-group">
						<label for="email_adresi">E-posta Adresi</label>
						<input type="email" class="form-control" id="email_adresi" name="email_adresi" placeholder="E-posta Adresi" required>
					</div>
					
					<button type="submit" name="buton" class="btn btn-primary" value="Gönder"><i class="fa fa-lock"></i>Gönder</button>
				
				</form>
				</div>
			</div>
			<?php } ?>
			
		</div>

	</div>
</div>
<div class="clearfix mb50"></div>
<?php tema_footer(); ?>