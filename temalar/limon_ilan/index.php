<?php 
if(count(get_included_files())==1) {die();}
@$mesaj=$_GET["m"];
@$page=$_GET["p"];
@$magaza=$_GET["magaza"];
@$magaza_subdomain=$_GET["magaza_subdomain"];
@$email=$_GET["email"];
@$key=$_GET["key"];

if (@$email and @$key) {
	$uye_id=id_getir("uyeler","email_adresi",$email);
	$uye_ad_soyad=getir("uyeler","ad_soyad",$uye_id);
	$email_dogrulama_kodu=uye_bilgi("email_onay_kodu",$uye_id);
	if (!$oturum_id or $oturum_id==$uye_id) {
		if ($key==$email_dogrulama_kodu) {
			guncelle("uyeler","durum",1,$uye_id);
			$_SESSION["mesaj"]=array(sprintf(_LANG('msj_merhaba_dogrulandi'), $uye_ad_soyad),"success");
			yonlendir(ozel_url("giris-yap"));
		} else {
			$_SESSION["mesaj"]=array(_LANG('pn_hatali_e_posta_adresi_veya_dogrulama_kodu'),".");
			yonlendir(site_url());
		}
	} else {
		$_SESSION["mesaj"]=array(_LANG('pn_hata_gecersiz_islem'),"m");
		yonlendir(site_url());
	}
}

if ($page=="giris-yap") {
	include("sayfa/giris-yap.php");
	exit();
}
if ($page=="kayit-ol") {
	include("sayfa/kayit-ol.php");
	exit();
}
if ($page=="sifremi-unuttum") {
	include("sayfa/sifremi-unuttum.php");
	exit();
}
if ($page=="profil-guncelle") {
	include("sayfa/profil-guncelle.php");
	exit();
}
if ($page=="profil-bilgilerim") {
	include("sayfa/profil-bilgilerim.php");
	exit();
}
if ($page=="profil-gorseli") {
	include("sayfa/profil-gorseli.php");
	exit();
}
if ($page=="kapak-gorseli") {
	include("sayfa/kapak-gorseli.php");
	exit();
}
if ($page=="mesajlarim") {
	include("sayfa/mesajlarim.php");
	exit();
}
if ($page=="favori-ilanlarim") {
	include("sayfa/favori-ilanlarim.php");
	exit();
}
if ($page=="ilanlarim") {
	include("sayfa/ilanlarim.php");
	exit();
}
if ($page=="magazalarim") {
	include("sayfa/magazalarim.php");
	exit();
}
if ($page=="doping") {
	include("sayfa/doping.php");
	exit();
}
if ($page=="siparislerim") {
	include("sayfa/siparislerim.php");
	exit();
}
if ($page=="get-satislarim") {
	include("sayfa/get-satislarim.php");
	exit();
}
if ($page=="magaza-bilgileri") {
	include("sayfa/magaza-bilgileri.php");
	exit();
}
if ($page=="magaza-profil-gorseli") {
	include("sayfa/magaza-profil-gorseli.php");
	exit();
}
if ($page=="magaza-kapak-gorseli") {
	include("sayfa/magaza-kapak-gorseli.php");
	exit();
}
if ($page=="magaza-plan-detaylari") {
	include("sayfa/magaza-plan-detaylari.php");
	exit();
}
if ($page=="danismanlar") {
	include("sayfa/danismanlar.php");
	exit();
}
if ($page=="siparis") {
	include("sayfa/siparis.php");
	exit();
}
if ($page=="odeme") {
	include("sayfa/odeme.php");
	exit();
}
if ($page=="ilan-ekle") {
	include("sayfa/ilan-ekle.php");
	exit();
}
if ($page=="bildirimlerim") {
	include("sayfa/bildirimlerim.php");
	exit();
}
if ($page=="plan-detaylari") {
	include("sayfa/plan-detaylari.php");
	exit();
}
if ($page=="acil-acil") {
	include("sayfa/acil-acil.php");
	exit();
}
if ($page=="vitrin-ilanlari") {
	include("sayfa/vitrin-ilanlari.php");
	exit();
}
if ($page=="fiyati-dusenler") {
	include("sayfa/fiyati-dusenler.php");
	exit();
}
if ($page=="son-48-saat") {
	include("sayfa/son-48-saat.php");
	exit();
}
if ($page=="son-1-hafta") {
	include("sayfa/son-1-hafta.php");
	exit();
}
if ($page=="son-1-ay") {
	include("sayfa/son-1-ay.php");
	exit();
}
if ($page=="get-ilanlari") {
	include("sayfa/get-ilanlari.php");
	exit();
}
if ($page=="magazalar") {
	include("sayfa/magazalar.php");
	exit();
}
if ($page=="yazilar") {
	include("sayfa/yazilar.php");
	exit();
}
if ($page=="paket-satin-al") {
	include("sayfa/paket-satin-al.php");
	exit();
}
if ($page=="magaza-paket-satin-al") {
	include("sayfa/magaza-paket-satin-al.php");
	exit();
}
if ($page=="yazdir") {
	include("yazdir.php");
	exit();
}
if ($magaza or $magaza_subdomain) {
	include("magaza.php");
	exit();
}
if ($page=="cikis-yap") {
	session_destroy();
	setcookie("auto_login",null,-60*60*24*365*5,"/");
	session_start();
	$_SESSION["mesaj"]=array(_LANG('msg_continue'),"e");
	yonlendir(site_url());
}
if ($page=="iframe") {
	include("sayfa/iframe.php");
	exit();
}

tema_header();

// Mobile özel kategori menüsü başladı.
$mobil_ozel_menu=tema_ayar("mobil_ozel_menu");
if ($mobil_ozel_menu!=2) { ?>
<div id="mobil_ozel_menu" class="container-fluid visible-xs visible-sm" style="padding:0;overflow:hidden;">
<?php 
$kategori_iptal=tema_ayar("kategori_iptal");
$kategori_iptal=rtrim($kategori_iptal,",");
$kategori_iptal_sql="";
if ($kategori_iptal) {
	$kategori_iptal_sql=" and id not in ($kategori_iptal)";
}
$kategori_siralama=tema_ayar("ilan_ekle_kategori_siralama");
if ($kategori_siralama=="baslik_a_z") {
	$kategori_sql=mysqli_query($baglan,"select * from kategoriler where tip='ilan' and ust_kategori='0' $kategori_iptal_sql order by baslik asc");
} else if ($kategori_siralama=="icerik_sayisi") {
	$kategori_sql=mysqli_query($baglan,"select * from kategoriler where tip='ilan' and ust_kategori='0' $kategori_iptal_sql order by icerik desc, baslik");
} else {
	$kategori_sql=mysqli_query($baglan,"select * from kategoriler where tip='ilan' and ust_kategori='0' $kategori_iptal_sql");
}
while ($kategori_dizi=mysqli_fetch_array($kategori_sql)) {
	$kategori_id=$kategori_dizi["id"];
	$kategori_baslik=$kategori_dizi["baslik"];
	$kategori_ikon=kategori_bilgi("ikon",$kategori_id);
	$kategori_ikon_bg=kategori_bilgi("ikon_bg",$kategori_id);
	$kategori_link=kategori_link_getir($kategori_id);
	$kategori_ilan_sayisi=$kategori_dizi["icerik"];
	$ilan_sayilari_ayar=tema_ayar("ilan_sayilari");
	if ($ilan_sayilari_ayar==2 or $ilan_sayilari_ayar==3) {
		$kategori_ilan_sayisi*=$ilan_sayilari_ayar;
	}
	@$alt_kategori_sayisi=alt_kategori_sayisi($kategori_id);		
	if (!$kategori_ikon) {$kategori_ikon="ion-search";}
	$alt_kategoriler=explode(",",alt_kat_idler($kategori_id));
	if (is_array($alt_kategoriler) and count($alt_kategoriler)>0) {
		$tum_alt_kategoriler=null;
		$x=1;
		foreach ($alt_kategoriler as $alt_kategori) {
			$alt_kategori_baslik=getir("kategoriler","baslik",$alt_kategori);
			if ($x!=1) {
				$tum_alt_kategoriler.="$alt_kategori_baslik, ";
			}
			$x++;
		}
		$tum_alt_kategoriler=substr($tum_alt_kategoriler,0,-2);
	}

	?>
	<a href="<?php echo $kategori_link; ?>">
	<div class="row">
		<div class="col-xs-4 ikon">
			<i class="<?php echo $kategori_ikon; ?>" style="background:<?php echo $kategori_ikon_bg; ?> !important;"></i>
		</div>
		<div class="col-xs-35">
			<div class="baslik"><?php echo $kategori_baslik; ?></div>
			<div class="alt_kategoriler"><?php echo $tum_alt_kategoriler; ?></div>
		</div>
		<div class="col-xs-3">
			<i class="fa fa-chevron-right"></i>
		</div>
	</div>
	</a>
	<?php 
}
?>
</div>
<?php } ?>
<div class="container mt20">

	<?php 
	$anasayfa_ust_genis=tema_ayar("anasayfa_ust_genis");
	if ($anasayfa_ust_genis) {echo "<div class='mb10'>$anasayfa_ust_genis</div>";}
	?>
	<?php bilesen_alani("anasayfa_ust"); ?>
	<div class="row">
		<div class="col-md-9 sidebar">
			<?php bilesen_alani("anasayfa_sidebar"); ?>
		</div>
		<div class="col-md-33">
			<?php include("temalar/$aktif_tema/inc/alert.php"); ?>
			<?php if ($mesaj) {
				if ($mesaj==1) {echo "<div class='alert alert-success autoclose'>" . _LANG('msg_see_you_again') . "</div>";}
			} ?>
			<?php bilesen_alani("anasayfa"); ?>
			<div class="row">
				<div class="col-md-30">
					<?php bilesen_alani("anasayfa_sol"); ?>
				</div>
				<div class="col-md-12">
					<?php bilesen_alani("anasayfa_sag"); ?>
				</div>
			</div>
		</div>
	</div>
</div>
<?php tema_footer(); ?>