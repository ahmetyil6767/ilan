<?php 
$uye_buton=$_POST["uye_buton"];
if ($uye_buton) {
	$uye_id=$_POST["uye_id"];
	$tarih_saat=tarih_saat();
	$ilan_sql=mysqli_query($baglan,"update icerik set tarih_saat='$tarih_saat' where yazar='$uye_id' and tip='ilan' order by tarih_saat asc");
	$etkilenen=mysqli_affected_rows($baglan);
	$_SESSION["mesaj"]=array(sprintf(_LANG('msj_toplu_tarih_guncellendi'), $etkilenen),"success");
	yenile();
}
$magaza_buton=$_POST["magaza_buton"];
if ($magaza_buton) {
	$tarih_saat=tarih_saat();
	$magaza_id=$_POST["magaza_id"];
	$sql=mysqli_query($baglan,"
	SELECT i.id from icerik as i 
	INNER JOIN ekstra_bilgi as em ON i.id=em.id and em.anahtar='magaza' and em.deger='$magaza_id' 
	WHERE i.tip='ilan' order by tarih_saat asc");
	$x=0;
	while ($magaza_ilan_dizi=mysqli_fetch_array($sql)) {
		$ilan_id=$magaza_ilan_dizi["id"];
		guncelle("icerik","tarih_saat",$tarih_saat,$ilan_id);
		$x++;
	}
	$_SESSION["mesaj"]=array(sprintf(_LANG('msj_toplu_tarih_guncellendi'), $x),"success");
	yenile();
}


?>
<h3 class="pull-left bolum-baslik text-uppercase"><?php echo _LANG("pn_toplu_ilan_tarihi_guncelleme"); ?></h3>
<div class="clearfix mb20"></div>
<p><?php echo _LANG('hb_bir_uye_veya_magazaya_ait_tum_ilanlarin_yayin'); ?></p>
<form action="" method="post" style="margin-bottom:20px;border:1px solid #ddd;padding:20px;">
<?php
$uyeler_seciniz[]=array("deger"=>"","baslik"=>_LANG('filter_select'));
$sql=mysqli_query($baglan,"SELECT * from uyeler order by ad_soyad asc");
while ($dizi=mysqli_fetch_array($sql)) {
	$id=$dizi["id"];
	$ad_soyad=$dizi["ad_soyad"];
	$uyeler_seciniz[]=array("deger"=>$id,"baslik"=>$ad_soyad);
}
form_group(array("tip"=>"select","id"=>"uye_id","baslik"=>_LANG('p_uye'),"secenekler"=>$uyeler_seciniz,"gerekli"=>1)); 
form_buton("uye_buton"," . _LANG('tx_uyeye_ait_tum_ilanlarin_tarihini_guncelle') . ");
?>
</form>



<form action="" method="post" style="margin-bottom:20px;border:1px solid #ddd;padding:20px;">
<?php
$magaza_seciniz[]=array("deger"=>"","baslik"=>_LANG('filter_select'));
$sql=mysqli_query($baglan,"SELECT * from magazalar order by baslik asc");
while ($dizi=mysqli_fetch_array($sql)) {
	$id=$dizi["id"];
	$baslik=$dizi["baslik"];
	$magaza_seciniz[]=array("deger"=>$id,"baslik"=>$baslik);
}
form_group(array("tip"=>"select","id"=>"magaza_id","baslik"=>_LANG('pn_magaza_seciniz'),"secenekler"=>$magaza_seciniz,"gerekli"=>1)); 
form_buton("magaza_buton"," . _LANG('tx_magazaya_ait_tum_ilanlarin_tarihini_guncelle') . ");
?>
</form>