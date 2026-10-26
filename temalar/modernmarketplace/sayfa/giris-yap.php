<?php 
if(count(get_included_files())==1) {die();}
if ($oturum_id) {
	yonlendir(profil_link_getir($oturum_id));
	exit();
}
@$m=$_GET["m"];
@$ilan=$_GET["ilan"];
@$r=$_GET["r"];
@$buton=mysqli_real_escape_string($baglan,$_POST["buton"]);
@$email_adresi=mysqli_real_escape_string($baglan,$_POST["email_adresi"]);
@$parola=mysqli_real_escape_string($baglan,$_POST["parola"]);
@$oturum_acik=mysqli_real_escape_string($baglan,$_POST["oturum_acik"]);

tema_header(); ?>
<div class="container mt50 giris">
	<div class="row">
		<div class="col-md-36 col-md-offset-2">
			<?php include("temalar/$aktif_tema/inc/alert.php"); 
			if ($m==1) {
				echo "<div class='alert alert-danger autoclose'>" . _LANG('gy_uye_girisi_gerekli') . "</div><div class='clearfix mb10'></div>";
			}
			?>
		</div>
	</div>
	<div class="row dtable">
		<div class="col-md-18 col-md-offset-2">
			
			<div class="panel panel-default beyaz yukseklik">
				<div class="panel-heading"><?php echo _LANG("gy_panel_giris"); ?></div>

					<?php
					if ($buton) {
						$uye_mevcut_mu=uye_mevcut_mu($email_adresi);
						if ($uye_mevcut_mu==1) {
							// EŞLEŞİYOR MU
							$kontrol=uye_sorgula($email_adresi,$parola);
							if ($kontrol==1) {
								$uye_id=uye_id_getir($email_adresi);
								$_SESSION["oturum_id"]=$uye_id;
								if (yonetici_mi($uye_id)==true) {
									$_SESSION["yonetici_id"]=$uye_id;
								}
								$ad_soyad=uye_ad_soyad_getir($uye_id);
								$_SESSION["mesaj"]=array(_LANG('gy_hosgeldiniz', array($ad_soyad)),"success");
								
								// Bildirim gönderiliyor
								$lp_link=lpanel_uye_profil_url($uye_id);
								bildirim_ekle(_LANG('pn_i_class_fa_fa_sign_in_fa_fw_i_a_href_lp_link_ad_soyad_a_oturum_acti', array($lp_link,$ad_soyad)),$uye_id,0);
								
								$ip_adresi=ip_adresi();
								$tarih_saat=tarih_saat();
								mysqli_query($baglan,"insert into kayitlar set uye_id='$uye_id', veri_1='$ip_adresi', tarih_saat='$tarih_saat', tip='ip_kayit'");

								if ($oturum_acik==1) {
									$gun_sayisi=30;
									$auto_login_key=uye_bilgi("auto_login_key",$uye_id);
									if (!$auto_login_key) {
										$auto_login_key=strtoupper(md5($uye_id."-".benzersiz()."-".tarih_saat()));
										uye_bilgi_guncelle("auto_login_key",$auto_login_key,$uye_id);
									}
									setcookie("auto_login",$auto_login_key,time()+60*60*24*$gun_sayisi,"/");
								}
								
								if ($ilan) {
									if (is_numeric($ilan)) {
										yonlendir(link_getir($ilan));
									} else {
										yonlendir(site_url());
									}
								} else {
									if ($r) {
										if ($r=="ie") {
											yonlendir(ozel_url("ilan-ekle"));
										}
									}
									yonlendir(site_url());
								}

							} else {
								echo "<div class='alert alert-danger'>" . _LANG('gy_hatali_email_sifre') . "</div>";
							}
						} else {
							echo "<div class='alert alert-danger'>" . _LANG('gy_kullanici_bulunamadi') . "</div>";
						}	
					}
					?>
				<div class="panel-body giris_form gradient" style="padding:30px !important;">
				<form action="" method="post">

					<div class="form-group">
						<label for="email_adresi"><?php echo _LANG('login_kimlik'); ?> <span style="color:#e74c3c;margin-left:3px;">*</span></label>
						<input type="text" class="form-control" id="email_adresi" name="email_adresi" placeholder="<?php echo _LANG('login_kimlik'); ?>" required>
					</div>
					
					<div class="form-group">
						<label for="parola"><?php echo _LANG('account_password'); ?> <span style="color:#e74c3c;margin-left:3px;">*</span></label>
						<input type="password" class="form-control" id="parola" name="parola" placeholder="<?php echo _LANG('account_password'); ?>" required>
					</div>
					
					<div class="checkbox">
						<label>
							<input type="checkbox" name="oturum_acik" value="1"> <?php echo _LANG("gy_oturum_acik_tut"); ?>
						</label>
					</div>
				
					<button type="submit" name="buton" class="btn btn-primary" value="Giriş Yap"><i class="fa fa-lock"></i><?php echo _LANG("nav_login"); ?></button>
					<a href="<?php echo ozel_url("sifremi-unuttum"); ?>" class="btn btn-grey"><i class="fa fa-question"></i><?php echo _LANG("account_forgot"); ?></a>
				
				</form>
				</div>
			</div>
		
		</div>
		<div class="col-md-18 col-md-offset-2">
			<div class="ortala">
				<h3 style="font-size:22px;font-weight:600;"><?php echo _LANG("account_not_member"); ?></h3>
				<p><?php echo _LANG("gy_hizli_uyelik"); ?></p>
				<a href="<?php echo ozel_url("kayit-ol"); ?>" class="btn btn-default"><i class="ion-person-add"></i><?php echo _LANG("nav_register"); ?></a>
			</div>
		</div>
	</div>
</div>
<div class="clearfix mb50"></div>
<?php tema_footer(); ?>