<?php 
if(count(get_included_files())==1) {die();}
if ($oturum_id) {
	yonlendir(profil_link_getir($oturum_id));
	exit();
}
$kurumsal_uyelik=tema_ayar("kurumsal_uyelik");
@$buton=mysqli_real_escape_string($baglan,$_POST["buton"]);
@$ad_soyad=mysqli_real_escape_string($baglan,strip_tags($_POST["ad_soyad"]));
@$email_adresi=mysqli_real_escape_string($baglan,$_POST["email_adresi"]);
@$parola=mysqli_real_escape_string($baglan,$_POST["parola"]);
@$uyelik_tip=mysqli_real_escape_string($baglan,$_POST["uyelik_tip"]);
@$cep_tel=mysqli_real_escape_string($baglan,$_POST["cep_tel"]);
@$sirket=mysqli_real_escape_string($baglan,$_POST["sirket"]);
@$tc_no=mysqli_real_escape_string($baglan,$_POST["tc_no"]);
@$firma_adi=mysqli_real_escape_string($baglan,$_POST["firma_adi"]);
@$vergi_no=mysqli_real_escape_string($baglan,$_POST["vergi_no"]);
@$vergi_dairesi=mysqli_real_escape_string($baglan,$_POST["vergi_dairesi"]);
@$firma_adres=mysqli_real_escape_string($baglan,$_POST["firma_adres"]);
tema_header(); ?>
<div class="container mt50 giris">
	<div class="row">
		<div class="col-md-18 col-md-offset-2">
			<div class="panel panel-default beyaz yukseklik">
	<div class="panel-heading"><?php echo _LANG("ko_panel_kayit_ol"); ?></div>
				<?php
				if ($buton) {
					$uye_mevcut_mu=uye_mevcut_mu($email_adresi);
					if ($uye_mevcut_mu==1) {
	echo "<div class='alert alert-danger autoclose'>" . _LANG('ko_email_kullanimda') . "</div>";
					} else {
						$parola_hash=parola_sakla($parola);
						$tarih_saat=tarih_saat();
						$url=url_olustur($ad_soyad);
						$uye_ekle=mysqli_query($baglan,"insert into uyeler set ad_soyad='$ad_soyad', url='$url', email_adresi='$email_adresi', parola='$parola_hash', tarih_saat='$tarih_saat', yetki='kullanici', durum='0'");
						$uye_id=mysqli_insert_id($baglan);
						// Standart paket seçiliyor
                        $standart_paket=tema_ayar("standart_paket");
                        if ($standart_paket) {
                            uye_bilgi_guncelle("paket",$standart_paket,$uye_id);
                            $bitis_tarihi=strtotime('+1 year',strtotime($tarih_saat));
                            $bitis_tarihi=date('Y-m-d H:i:s' ,$bitis_tarihi);
                            uye_bilgi_guncelle("paket_bitis_tarihi",$bitis_tarihi,$uye_id);							
                        }
                        
                        $limit_politikasi=limit_politikasi();
                        
                        if ($limit_politikasi==2) {
                            uye_bilgi_guncelle("azalan_ilan_limit",liste_bilgi("ilan_limit",$standart_paket),$uye_id);
                            uye_bilgi_guncelle("azalan_kategori_limit",liste_bilgi("kategori_limit",$standart_paket),$uye_id);
                            uye_bilgi_guncelle("azalan_foto_limit",liste_bilgi("foto_limit",$standart_paket),$uye_id);
                            uye_bilgi_guncelle("azalan_aninda_onay",liste_bilgi("aninda_onay",$standart_paket),$uye_id);
                            guncelle("uyeler","azalan_update",1,$uye_id);
                        }
						
						$_SESSION["oturum_id"]=$uye_id;
						$ad_soyad=uye_ad_soyad_getir($uye_id);
	$_SESSION["mesaj"]=array(_LANG('ko_hosgeldiniz', array($ad_soyad)),"success");
						
						// Bildirim gönderiliyor
						$lp_link=lpanel_uye_profil_url($uye_id);
						bildirim_ekle(_LANG('pn_i_class_fa_fa_user_plus_fa_fw_i_a_href_lp_link_ad_soyad_a_kayit_oldu', array($lp_link,$ad_soyad)),$uye_id,0);
						$ie_link=ozel_url("ilan-ekle");
	bildirim_ekle(_LANG('ko_hosgeldiniz_bildirim', array($ad_soyad,$ie_link)),$uye_id,1);
						
						// E-posta gönderimi başladı
						$mail_baslik=tema_ayar("mail_baslik");
						$site_url=site_url();		
						$ileti="
						<p>Merhaba <strong>$ad_soyad</strong></p>
						<p>" . _LANG('hb_web_sitemize_hos_geldiniz_sonraki_ziyaretlerin') . "</p>
						<p>E-posta adresi: $email_adresi<br />Parola: $parola</p>
						<p>Siteye giriş yapmak için <a href=\"$site_url/sayfa/giris-yap\">buraya</a> tıklayın.</p>";
						auto_mail_add($ad_soyad,$email_adresi,"$mail_baslik - Hoşgeldiniz, $ad_soyad",$ileti);	
						
						$ileti_admin=_LANG('ko_yeni_uye_kaydi_bildirim', array($ad_soyad,$site_url,$admin_dizini));
						bildirim_ekle($ileti_admin,$uye_id,0);
						$site_email=ayar("site_email");
						auto_mail_add($mail_baslik,$site_email,_LANG('ko_yeni_uye_kaydi', array($ad_soyad)),$ileti_admin);
						// E-posta gönderimi bitti
						
						$email_dogrulama=tema_ayar("email_dogrulama");
						if ($email_dogrulama==1) {
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
						}
						
						$uyelik_tip_guncelle=uye_bilgi_guncelle("tip",$uyelik_tip,$uye_id);
						if ($cep_tel) {uye_bilgi_guncelle("cep_tel",$cep_tel,$uye_id);}
						
						if ($uyelik_tip=="Kurumsal") {
              if ($tc_no)	{uye_bilgi_guncelle("tc_no",$tc_no,$uye_id);}
							if ($sirket) {uye_bilgi_guncelle("sirket",$sirket,$uye_id);}
							if ($firma_adi)	{uye_bilgi_guncelle("firma_adi",$firma_adi,$uye_id);}
							if ($vergi_no)	{uye_bilgi_guncelle("vergi_no",$vergi_no,$uye_id);}
							if ($vergi_dairesi)	{uye_bilgi_guncelle("vergi_dairesi",$vergi_dairesi,$uye_id);}
							if ($firma_adres)	{uye_bilgi_guncelle("firma_adres",$firma_adres,$uye_id);}
						}
						yonlendir(profil_link_getir($uye_id));
					}
				} ?>					
				<div class="panel-body giris_form kayit gradient" style="padding:30px !important;">
					<form action="" method="post">	
					<?php if ($kurumsal_uyelik=="acik") { ?>
					<div class="form-group">
						<label for="uyelik_tip"><?php echo _LANG("s_uyelik_turu"); ?></label>
	<select class="form-control" id="uyelik_tip" name="uyelik_tip" placeholder="<?php echo _LANG("ko_uyelik_tipi"); ?>">
							<option value="Bireysel">Bireysel</option>
							<option value="Kurumsal">Kurumsal</option>
						</select>
					</div>				
					<?php } else { ?>
						<input type="hidden" class="form-control" id="uyelik_tip" name="uyelik_tip" value="Bireysel">
					<?php } ?>
					<div class="form-group">
						<label for="ad_soyad">Ad Soyad <span style="color:#e74c3c;margin-left:3px;">*</span></label>
						<input type="text" class="form-control" id="ad_soyad" name="ad_soyad" placeholder="Ad Soyad" required>
					</div>		

					<div class="form-group">
						<label for="email_adresi">E-posta Adresi <span style="color:#e74c3c;margin-left:3px;">*</span></label>
						<input type="email" class="form-control" id="email_adresi" name="email_adresi" placeholder="E-posta Adresi" required>
					</div>
					
					<div class="form-group">
						<label for="cep_tel">Cep Telefonu <span style="color:#e74c3c;margin-left:3px;">*</span></label>
						<div class="input-group">
							<span class="input-group-addon" style="border-top-right-radius:0;border-bottom-right-radius:0;color:#e74c3c;border-color:#ddd;">0</span>
							<input type="text" class="form-control cannot_start_with_zero" id="cep_tel" name="cep_tel" placeholder="Cep Telefonu" style="border-top-left-radius:0 !important;border-bottom-left-radius:0 !important;" required>
						</div>
	<p class="text-muted" style="font-size:13px;margin-top:5px;color:#e74c3c;"><?php echo _LANG("ko_telefon_sifir"); ?></p>
					</div>
					
					<div class="form-group">
						<label for="parola">Parola <span style="color:#e74c3c;margin-left:3px;">*</span></label>
						<input type="password" class="form-control" id="parola" name="parola" placeholder="Parola" required>
					</div>

					
					<?php if ($kurumsal_uyelik=="acik") { ?>
					
					<div id="kurumsal" style="display:none;">

					<div class="form-group">
						<label for="tc_no">TC Kimlik No <span style="color:#e74c3c;margin-left:3px;">*</span></label>
						<input type="text" class="form-control" id="tc_no" name="tc_no" maxlength="11" placeholder="TC Kimlik No">
					</div>                
                
					<div class="form-group">
	<label for="sirket"><?php echo _LANG("ko_sirket_turu"); ?> <span style="color:#e74c3c;margin-left:3px;">*</span></label>
	<select class="form-control" id="sirket" name="sirket" placeholder="<?php echo _LANG("ko_sirket"); ?>">
							<option><?php echo _LANG("filter_select"); ?></option>
							<option value="1"><?php echo _LANG("s_sahis_sirketi"); ?></option>
							<option value="2"><?php echo _LANG("s_limited_sirket"); ?></option>
						</select>
					</div>						
										
					<div class="form-group">
	<label for="firma_adi"><?php echo _LANG("ko_firma_unvani"); ?> <span style="color:#e74c3c;margin-left:3px;">*</span></label>
	<input type="text" class="form-control" id="firma_adi" name="firma_adi" placeholder="<?php echo _LANG("ko_firma_unvani_placeholder"); ?>">
					</div>						

					<div class="row">

						<div class="col-md-21">
							<div class="form-group">
								<label for="vergi_no">Vergi No <span style="color:#e74c3c;margin-left:3px;">*</span></label>
								<input type="text" class="form-control" id="vergi_no" name="vergi_no" placeholder="Vergi No">
							</div>
						</div>
						
						<div class="col-md-21">
							<div class="form-group">
								<label for="vergi_dairesi">Vergi Dairesi <span style="color:#e74c3c;margin-left:3px;">*</span></label>
								<input type="text" class="form-control" id="vergi_dairesi" name="vergi_dairesi" placeholder="Vergi Dairesi">
							</div>
						</div>
					</div>
				
					<div class="form-group">
						<label for="firma_adres">Firma Adresi <span style="color:#e74c3c;margin-left:3px;">*</span></label>
						<textarea name="firma_adres" id="firma_adres" class="form-control" rows="4" placeholder="Firma Adresi"></textarea>
					</div>
					
					</div>
					<?php } ?>
					
					<?php 
					$uyelik_sozlesmesi_sayfasi=link_getir(tema_ayar("uyelik_sozlesmesi"));
	form_group(array("tip"=>"checkbox","id"=>"onay","baslik"=>"<a href='$uyelik_sozlesmesi_sayfasi' target='_blank'>" . _LANG('ko_uyelik_sozlesmesi') . "</a> okudum, onaylıyorum.","gerekli"=>1)); ?>
					?>
	<button type="submit" name="buton" id="buton" value="Kayıt ol" class="btn btn-primary"><i class="fa fa-person-add"></i><?php echo _LANG("nav_register"); ?></button>
					
					</form>
				</div>
			</div>
		
		</div>
		<div class="col-md-18 col-md-offset-2">
			<div class="ortala">
				<h3 style="font-size:22px;font-weight:600;"><?php echo _LANG("account_is_member"); ?></h3>
	<p><?php echo _LANG("ko_ilan_vermek"); ?></p>
				<a href="<?php echo ozel_url("giris-yap"); ?>" class="btn btn-default"><i class="ion-person-add"></i><?php echo _LANG("nav_login"); ?></a>
			</div>
		</div>
	</div>
</div>
<div class="clearfix mb50"></div>
<?php tema_footer(); ?>
<script type="text/javascript">
$("#kurumsal").hide();
$("body").on("change","#uyelik_tip",function(){
	var uyelik_tip=$(this).val();
	if (uyelik_tip=="Kurumsal") {
		$("#kurumsal").slideDown(300);
		$("#sirket").attr("required","required");
		$("#firma_adi").attr("required","required");
		$("#vergi_no").attr("required","required");
		$("#vergi_dairesi").attr("required","required");
		$("#firma_adres").attr("required","required");
		$("#tc_no").attr("required","required");
	} else {
		$("#kurumsal").slideUp(300);
		$("#sirket").removeAttr("required");
		$("#firma_adi").removeAttr("required");
		$("#vergi_no").removeAttr("required");
		$("#vergi_dairesi").removeAttr("required");
		$("#firma_adres").removeAttr("required");
		$("#tc_no").removeAttr("required");
	}
});
$("#as_ltd").hide();
$("button#buton").attr("disabled", true);
$("#form_onay input").change(function() {
	if(this.checked) {
		$("button#buton").attr("disabled", false);
	} else {
		$("button#buton").attr("disabled", true);
	}
});
</script>