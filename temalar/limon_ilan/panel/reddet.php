<?php 
if(count(get_included_files())==1) {die();}
$ilan_id=$_GET["ilan_id"];

if ($_POST["buton"]) {
	$sebep=$_POST["sebep"];
	$ilan_sahibi=getir("icerik","yazar",$ilan_id);
	$ilan_baslik=getir("icerik","baslik",$ilan_id);
	$kurallar_url=link_getir(tema_ayar("ilan_verme_kurallari"));
	bildirim_ekle(sprintf(_LANG('bnf_ilan_reddedildi'), $ilan_baslik),$ilan_sahibi,1);

	// E-posta gönderimi başladı
	$mail_baslik=tema_ayar("mail_baslik");
	$site_url=site_url();		
	$ilan_sahibi_ad_soyad=getir("uyeler","ad_soyad",$ilan_sahibi);
	$ilan_sahibi_email_adresi=getir("uyeler","email_adresi",$ilan_sahibi);
	if ($sebep) {
		$sebep_yaz="<p>Sebep: $sebep</p>";
	} else {
		$sebep_yaz=null;
	}
	$ileti="
	<p>Merhaba <strong>$ilan_sahibi_ad_soyad</strong></p>
	<p>$ilan_baslik başlıklı ilanınız kurallara uygun olmadığı için reddedilmiştir. Lütfen ilanınızı kurallara uygun şekilde düzenleyerek yayına alınız.</p>
	$sebep_yaz
	<p>İlan verme kurallarını görüntülemek için <a href=\"$kurallar_url\">buraya</a> tıklayın.</p>";
	auto_mail_add($ilan_sahibi_ad_soyad,$ilan_sahibi_email_adresi,"İlanınız reddedildi! - $mail_baslik",$ileti);
	guncelle("icerik","durum",0,$ilan_id);
	
    limit_artir($ilan_id);
    
	$_SESSION["mesaj"]=array(_LANG('pn_ilan_reddedildi_ve_ilan_sahibine_bilgilendirme_e_postasi_gonderildi'),".");
	yonlendir("icerik.php?tip=ilan&islem=guncelle&id=$ilan_id");
}

?>
<form action="" method="post">
<?php 
form_group(array("id"=>"ilan_baslik","tip"=>"text","baslik"=>_LANG('listing_title'),"deger"=>getir("icerik","baslik",$ilan_id),"pasif"=>1));
form_group(array("id"=>"uye_adi","tip"=>"text","baslik"=>_LANG('listing_owner'),"deger"=>getir("uyeler","ad_soyad",getir("icerik","yazar",$ilan_id)),"pasif"=>1));
form_group(array("id"=>"sebep","tip"=>"textarea","baslik"=>"Sebep"));
form_buton("buton"," . _LANG('tx_ilani_reddet') . ");
?>
</form>