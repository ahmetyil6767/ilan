<?php 
if(count(get_included_files())==1) {die();}
if (!$oturum_id) {
	$_SESSION["mesaj"]=array(_LANG('pn_bu_sayfayi_goruntuleyebilmek_icin_uye_girisi_yapmalisiniz'),".");
	yonlendir(ozel_url("giris-yap"));
	exit();
}
@$ilan=$_GET["ilan"];
@$buton=$_POST["buton"];
$get_satis=mysqli_num_rows(mysqli_query($baglan,"select id from siparisler where tip='get' and satici_id='$oturum_id'"));

$_POST["ad_soyad"]=mysqli_real_escape_string($baglan,strip_tags(trim($_POST["ad_soyad"])));
$_POST["email_adresi"]=mysqli_real_escape_string($baglan,strip_tags(trim($_POST["email_adresi"])));
$_POST["yeni_parola"]=mysqli_real_escape_string($baglan,strip_tags(trim($_POST["yeni_parola"])));
$_POST["yeni_parola_tekrar"]=mysqli_real_escape_string($baglan,strip_tags(trim($_POST["yeni_parola_tekrar"])));
$_POST["tip"]=mysqli_real_escape_string($baglan,strip_tags(trim($_POST["tip"])));
$_POST["firma_tel"]=mysqli_real_escape_string($baglan,strip_tags(trim($_POST["firma_tel"])));
$_POST["sirket"]=mysqli_real_escape_string($baglan,strip_tags(trim($_POST["sirket"])));
$_POST["tc_no"]=mysqli_real_escape_string($baglan,strip_tags(trim($_POST["tc_no"])));
$_POST["vergi_no"]=mysqli_real_escape_string($baglan,strip_tags(trim($_POST["vergi_no"])));
$_POST["vergi_dairesi"]=mysqli_real_escape_string($baglan,strip_tags(trim($_POST["vergi_dairesi"])));
$_POST["firma_adres"]=mysqli_real_escape_string($baglan,strip_tags(trim($_POST["firma_adres"])));
$_POST["firma_adi"]=mysqli_real_escape_string($baglan,strip_tags(trim($_POST["firma_adi"])));
$_POST["cep_tel"]=mysqli_real_escape_string($baglan,strip_tags(trim($_POST["cep_tel"])));
$_POST["is_tel"]=mysqli_real_escape_string($baglan,strip_tags(trim($_POST["is_tel"])));
$_POST["ev_tel"]=mysqli_real_escape_string($baglan,strip_tags(trim($_POST["ev_tel"])));
$_POST["adres"]=mysqli_real_escape_string($baglan,strip_tags(trim($_POST["adres"])));
$_POST["il"]=mysqli_real_escape_string($baglan,strip_tags(trim($_POST["il"])));
$_POST["posta_kodu"]=mysqli_real_escape_string($baglan,strip_tags(trim($_POST["posta_kodu"])));
$_POST["banka_bilgileri"]=mysqli_real_escape_string($baglan,strip_tags(trim($_POST["banka_bilgileri"])));


tema_header(); ?>
<div class="container profil arsiv mt20">
	<?php include("temalar/$aktif_tema/inc/uye-ust.php"); ?>
	
	<div class="row">
		<div class="col-md-9 sidebar">
			<div class="panel panel-default limon menu">
	<div class="panel-heading"><?php echo _LANG("pg_panel_profil_guncelle"); ?></div>
				<div class="panel-body">
					<ul>
						<li class="active"><a href="<?php echo ozel_url("profil-guncelle"); ?>"><?php echo _LANG("nav_update_profile"); ?></a></li>
						<li><a href="<?php echo ozel_url("profil-gorseli"); ?>"><?php echo _LANG("s_profil_degistir"); ?></a></li>
						<li><a href="<?php echo ozel_url("kapak-gorseli"); ?>"><?php echo _LANG("s_kapak_degistir"); ?></a></li>
					</ul>
				</div>
			</div>
		</div>
		
		<div class="col-md-33">
			<?php include("temalar/$aktif_tema/inc/alert.php"); ?>
			<div class="panel panel-default limon tab">
				<div class="panel-heading">
				  <ul class="nav nav-tabs ozel" role="tablist">
					<li role="presentation" class="active"><a href="#bilgiler" aria-controls="bilgiler" role="tab" data-toggle="tab"><i class="ion-person"></i>Profil Bilgileri</a></li>
					<li role="presentation"><a href="#iletisim" aria-controls="iletisim" role="tab" data-toggle="tab"><i class="ion-email"></i><?php echo _LANG("post_contact"); ?></a></li>
					<li role="presentation"><a href="#firma" aria-controls="firma" role="tab" data-toggle="tab"><i class="ion-briefcase"></i>Firma Bilgileri</a></li>
					<li role="presentation"><a href="#satici" aria-controls="satici" role="tab" data-toggle="tab"><i class="ion-briefcase"></i><?php echo _LANG("s_satici_bilgi"); ?></a></li>
				  </ul>
				</div>
				<div class="panel-body">
					<?php if ($buton) {
						if ($_POST["ad_soyad"]) {
							guncelle("uyeler","ad_soyad",$_POST["ad_soyad"],$oturum_id);
						}
						if ($_POST["email_adresi"]) {
							$mevcut_email=getir("uyeler","email_adresi",$oturum_id);
							if (uye_mevcut_mu($_POST["email_adresi"]) and $mevcut_email!=$_POST["email_adresi"]) {
								$email_uyari=1;
							} else {
								guncelle("uyeler","email_adresi",$_POST["email_adresi"],$oturum_id);
								
								if ($_POST["email_adresi"]!=$mevcut_email and tema_ayar("email_dogrulama")==1) {
									guncelle("uyeler","durum",0,$oturum_id);
									uye_bilgi_guncelle("email_onay_kodu",null,$oturum_id);
									uye_bilgi_guncelle("email_onay_kodu_tarih_saat",null,$oturum_id);
								}
								
								
							}
						}
						if ($_POST["yeni_parola"] and $_POST["yeni_parola_tekrar"] and $_POST["yeni_parola"]==$_POST["yeni_parola_tekrar"]) {
							parola_guncelle_hash($oturum_id, trim($_POST["yeni_parola"]));
						}
						if ($_POST["tip"]) {
							uye_bilgi_guncelle("tip",trim($_POST["tip"]),$oturum_id);
						}							
						if ($_POST["firma_tel"]) {
							uye_bilgi_guncelle("firma_tel",trim($_POST["firma_tel"]),$oturum_id);
						}
						if ($_POST["sirket"]) {
							uye_bilgi_guncelle("sirket",trim($_POST["sirket"]),$oturum_id);
						}						
						if ($_POST["tc_no"]) {
							uye_bilgi_guncelle("tc_no",trim($_POST["tc_no"]),$oturum_id);
						}	
						if ($_POST["vergi_no"]) {
							uye_bilgi_guncelle("vergi_no",trim($_POST["vergi_no"]),$oturum_id);
						}
						if ($_POST["vergi_dairesi"]) {
							uye_bilgi_guncelle("vergi_dairesi",trim($_POST["vergi_dairesi"]),$oturum_id);
						}						
						if ($_POST["firma_adres"]) {
							uye_bilgi_guncelle("firma_adres",trim($_POST["firma_adres"]),$oturum_id);
						}
						if ($_POST["firma_adi"]) {
							uye_bilgi_guncelle("firma_adi",trim($_POST["firma_adi"]),$oturum_id);
						}
						if ($_POST["cep_tel"]!=uye_bilgi("cep_tel",$oturum_id)) {
							uye_bilgi_guncelle("cep_tel",trim($_POST["cep_tel"]),$oturum_id);
						}
						if ($_POST["is_tel"]!=uye_bilgi("is_tel",$oturum_id)) {
							uye_bilgi_guncelle("is_tel",trim($_POST["is_tel"]),$oturum_id);
						}
						if ($_POST["ev_tel"]!=uye_bilgi("ev_tel",$oturum_id)) {
							uye_bilgi_guncelle("ev_tel",trim($_POST["ev_tel"]),$oturum_id);
						}
						if ($_POST["adres"]!=uye_bilgi("adres",$oturum_id)) {
							uye_bilgi_guncelle("adres",trim($_POST["adres"]),$oturum_id);
						}
						if ($_POST["il"]!=uye_bilgi("il",$oturum_id)) {
							uye_bilgi_guncelle("il",trim($_POST["il"]),$oturum_id);
						}
						if ($_POST["posta_kodu"]!=uye_bilgi("posta_kodu",$oturum_id)) {
							uye_bilgi_guncelle("posta_kodu",trim($_POST["posta_kodu"]),$oturum_id);
						}
						if ($_POST["banka_bilgileri"]!=uye_bilgi("banka_bilgileri",$oturum_id)) {
							uye_bilgi_guncelle("banka_bilgileri",trim($_POST["banka_bilgileri"]),$oturum_id);
						}
						if ($email_uyari==1) {
	$_SESSION["mesaj"]=array(_LANG('pg_email_guncellenemedi'),"danger");
						} else {
							$_SESSION["mesaj"]=array(_LANG('msg_profile_updated_ok'),"i");
						}
						if ($ilan) {
							if (is_numeric($ilan)) {
								yonlendir(link_getir($ilan));
							} else {
								yonlendir(ozel_url("profil-guncelle"));
							}
						} else {
							yonlendir(ozel_url("profil-guncelle"));
						}
					}
					
					
					?>
					
					
					<form action="" method="post">
					<div class="tab-content">
						<div role="tabpanel" class="tab-pane fade in active" id="bilgiler">
						
						<?php 
						$ad_soyad=getir("uyeler","ad_soyad",$oturum_id);
						$email_adresi=getir("uyeler","email_adresi",$oturum_id);
						$uyelik_tip=uye_bilgi("tip",$oturum_id);
						$firma_adi=uye_bilgi("firma_adi",$oturum_id);
						$firma_tel=uye_bilgi("firma_tel",$oturum_id);
						$sirket=uye_bilgi("sirket",$oturum_id);
						$tc_no=uye_bilgi("tc_no",$oturum_id);
						$vergi_no=uye_bilgi("vergi_no",$oturum_id);
						$vergi_dairesi=uye_bilgi("vergi_dairesi",$oturum_id);
						$adres=uye_bilgi("adres",$oturum_id);
						
						form_group(array("tip"=>"text","id"=>"ad_soyad","baslik"=>"Ad Soyad","deger"=>$ad_soyad,"gerekli"=>1));
						form_group(array("tip"=>"text","id"=>"email_adresi","baslik"=>"Email adresi","deger"=>$email_adresi,"gerekli"=>1));
						
						form_group(array("tip"=>"text","id"=>"tc_no","baslik"=>_LANG("s_tc_no"),"deger"=>$tc_no));
						form_group(array("tip"=>"textarea","id"=>"adres","baslik"=>"Adres","satir"=>3,"deger"=>uye_bilgi("adres",$oturum_id),"aciklama"=>_LANG('s_kargo_adresi')));
						
						$il_secenekleri[]=array("deger"=>null,"baslik"=>_LANG('filter_select'));
						$il_secenekleri_getir=mysqli_query($baglan,"select il_adi from iller");
						while ($il_secenekleri_dizi=mysqli_fetch_array($il_secenekleri_getir)) {
							$il_secenekleri[]=array("deger"=>$il_secenekleri_dizi["il_adi"],"baslik"=>$il_secenekleri_dizi["il_adi"]);
						}
						
						form_group(array("tip"=>"select","id"=>"il","baslik"=>_LANG('addr_region'),"deger"=>uye_bilgi("il",$oturum_id),"secenekler"=>$il_secenekleri));
						form_group(array("tip"=>"text","id"=>"posta_kodu","baslik"=>"Posta Kodu","deger"=>uye_bilgi("posta_kodu",$oturum_id)));						
						
						form_group(array("tip"=>"password","id"=>"yeni_parola","baslik"=>"Yeni Parola","aciklama"=>_LANG('s_parola_degistirme')));
						form_group(array("tip"=>"password","id"=>"yeni_parola_tekrar","baslik"=>"Yeni Parola (Tekrar)"));
						

						?>
						
						</div>
						<div role="tabpanel" class="tab-pane fade" id="iletisim">
						
						<div class="form-group">
							<label for="cep_tel">Cep Telefonu</label>
							<div class="input-group">
								<span class="input-group-addon" style="border-top-right-radius:0;border-bottom-right-radius:0;border-color:#ddd;">+220</span>
								<input type="text" class="form-control cannot_start_with_zero" id="cep_tel" name="cep_tel" value="<?php echo uye_bilgi("cep_tel",$oturum_id); ?>" placeholder="Cep Telefonu" style="border-top-left-radius:0 !important;border-bottom-left-radius:0 !important;">
							</div>
	<p class="text-muted" style="font-size:13px;margin-top:5px;"><?php echo _LANG("pg_telefon_sifir"); ?></p>
						</div>
						<div class="form-group">
							<label for="is_tel"><?php echo _LANG("s_is_telefonu"); ?></label>
							<div class="input-group">
								<span class="input-group-addon" style="border-top-right-radius:0;border-bottom-right-radius:0;border-color:#ddd;">+220</span>
	<input type="text" class="form-control cannot_start_with_zero" id="is_tel" name="is_tel" value="<?php echo uye_bilgi("is_tel",$oturum_id); ?>" placeholder="<?php echo _LANG("pg_is_telefonu"); ?>" style="border-left:0;">
							</div>
						</div>
						<div class="form-group">
							<label for="ev_tel">Ev Telefonu</label>
							<div class="input-group">
								<span class="input-group-addon" style="border-top-right-radius:0;border-bottom-right-radius:0;border-color:#ddd;">+220</span>
								<input type="text" class="form-control cannot_start_with_zero" id="ev_tel" name="ev_tel" value="<?php echo uye_bilgi("ev_tel",$oturum_id); ?>" placeholder="Ev Telefonu" style="border-top-left-radius:0 !important;border-bottom-left-radius:0 !important;">
							</div>
						</div>

						</div>
						<div role="tabpanel" class="tab-pane fade" id="firma">
						<?php 
						form_group(array("tip"=>"select","id"=>"tip","baslik"=>_LANG('s_uyelik_tipi'),"deger"=>$uyelik_tip,"secenekler"=>options("Bireysel,Kurumsal")));
						?>
						<div id="kurumsal" style="<?php if ($uyelik_tip!="Kurumsal") {echo "display:none;";} ?>">
						<?php
							form_group(array("tip"=>"text","id"=>"firma_adi","baslik"=>_LANG('s_firma_unvani'),"deger"=>$firma_adi));
						?>
						<div class="form-group">
							<label for="firma_tel">Firma Telefonu</label>
							<div class="input-group">
								<span class="input-group-addon" style="border-top-right-radius:0;border-bottom-right-radius:0;border-color:#ddd;">+220</span>
								<input type="text" class="form-control cannot_start_with_zero" id="firma_tel" name="firma_tel" value="<?php echo $firma_tel; ?>" placeholder="Firma Telefonu" style="border-top-left-radius:0 !important;border-bottom-left-radius:0 !important;">
							</div>
							<p><?php echo _LANG('hb_lutfen_telefonunuzu_basinda_sifir_olmadan_tusl'); ?></p>
						</div>						
						<?php 
							form_group(array("tip"=>"select","id"=>"sirket","baslik"=>_LANG('s_sirket_turu'),"deger"=>$sirket,"secenekler"=>options("Şahıs:1|Limited veya Anonim Şirket:2","|",":")));
							form_group(array("tip"=>"grup_ac"));	
							form_group(array("tip"=>"text","id"=>"vergi_no","baslik"=>"Vergi No","deger"=>$vergi_no,"grup"=>2));	
							form_group(array("tip"=>"text","id"=>"vergi_dairesi","baslik"=>"Vergi Dairesi","deger"=>$vergi_dairesi,"grup"=>2));	
							form_group(array("tip"=>"grup_kapat"));	
							form_group(array("tip"=>"textarea","id"=>"firma_adres","baslik"=>"Firma Adresi","satir"=>3,"deger"=>uye_bilgi("firma_adres",$oturum_id)));	
						?>
						</div>					
						</div>
						<div role="tabpanel" class="tab-pane fade" id="satici">
						<?php 
						$banka_bilgileri=uye_bilgi("banka_bilgileri",$oturum_id);
						form_group(array("tip"=>"textarea","id"=>"banka_bilgileri","baslik"=>"Banka Hesap Bilgileri","deger"=>$banka_bilgileri,"satir"=>5,"aciklama"=>_LANG('s_satici_odeme_noktasi')));
						?>						
						</div>
						</div>
	<?php form_buton("buton",_LANG("pg_guncelle"),"primary"); ?>
					</form>
				</div>
			</div>
			
		</div>
	</div>
</div>

<?php tema_footer(); ?>
<script type="text/javascript">
$(function(){
	$("#as_ltd").hide();
	$("#sahis").hide();
	<?php if ($sirket==1) { ?>
	$("#sahis").show();
	<?php } else { ?>
	$("#as_ltd").show();
	<?php } ?>
	$("body").on("change","#sirket",function(){
		var sirket=$(this).val();
		if (sirket==1) {
			$("#as_ltd").slideUp(300);
			$("#sahis").slideDown(300);
		} else {
			$("#sahis").slideUp(300);
			$("#as_ltd").slideDown(300);
		}
	});
	
	$("body").on("change","#tip",function(){
		var tip=$(this).val();
		if (tip=="Kurumsal") {
			$("#kurumsal").slideDown(300);
		} else {
			$("#kurumsal").slideUp(300);
		}
	});
});
</script>