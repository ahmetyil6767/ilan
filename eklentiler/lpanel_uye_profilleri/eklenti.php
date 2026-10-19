<?php 
if(count(get_included_files())==1) {die();}
function lpanel_uye_profil_link($uye_id) {
	$uye_ad_soyad=bilgi_getir("uyeler","ad_soyad",$uye_id);
	echo "<a href='eklentiler.php?eklenti=lpanel_uye_profilleri&sayfa=profil&id=$uye_id'>$uye_ad_soyad</a>";
}
function lpanel_uye_profil_url($uye_id) {
	$uye_ad_soyad=bilgi_getir("uyeler","ad_soyad",$uye_id);
	return "eklentiler.php?eklenti=lpanel_uye_profilleri&sayfa=profil&id=$uye_id";
}
?>