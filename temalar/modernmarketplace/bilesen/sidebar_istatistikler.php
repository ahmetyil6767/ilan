<?php 
if(count(get_included_files())==1) {die();}
$ist_op[]=array("baslik"=>_LANG('stat_total_ads'),"deger"=>"toplam-ilan");
$ist_op[]=array("baslik"=>_LANG('stat_today_ads'),"deger"=>"bugun-ilan");
$ist_op[]=array("baslik"=>_LANG('stat_total_stores'),"deger"=>"toplam-magaza");
$ist_op[]=array("baslik"=>_LANG('stat_total_members'),"deger"=>"toplam-uye");

$ayarlar=array(
array("tip"=>"multiple_checkbox","id"=>"gizle","baslik"=>_LANG('pn_gizlenecek_istatistikler'),"secenekler"=>$ist_op),
);
$bilesen["sidebar_istatistikler"]=array("id"=>"sidebar_istatistikler","baslik"=>_LANG('pn_sidebar_istatistikleri'),"fonksiyon"=>"sidebar_istatistikler","ayarlar"=>$ayarlar,"aciklama"=>"Anasayfa Sidebar bileşen alanlarında kullanılabilecek istatistikler bileşeni.");
function sidebar_istatistikler($id) {
global $baglan;
$baslik=getir("bilesenler","baslik",$id);
$gizle=bilesen_ayar("gizle",$id);
$toplam_ilan=mysqli_num_rows(mysqli_query($baglan,"select id from icerik where tip='ilan' and durum='1'"));
$bugun=date("Y-m-d");
$bugun_ilan=mysqli_num_rows(mysqli_query($baglan,"select id from icerik where tip='ilan' and durum='1' and tarih_saat like '%$bugun%'"));
$toplam_uye=mysqli_num_rows(mysqli_query($baglan,"select id from uyeler"));
$toplam_magaza=mysqli_num_rows(mysqli_query($baglan,"select id from magazalar where durum='1'"));
?>
<div class="bilesen s_istatistik">
<?php if ($baslik) {echo "<div class='baslik'>$baslik</div>";} ?>
<ul class="list-group fix">
	<?php $gizle=(string)$gizle; if (!strstr($gizle,"toplam-ilan")) { ?>
	<li class="list-group-item">
	<i class="ion-stats-bars"></i><?php echo _LANG('stat_total_ads'); ?>
	<span class="badge"><?php echo $toplam_ilan; ?></span>
	</li>
	<?php } if (!strstr($gizle,"bugun-ilan")) { ?>
	<li class="list-group-item">
	<i class="ion-android-time"></i><?php echo _LANG('stat_today_ads'); ?>
	<span class="badge"><?php echo $bugun_ilan; ?></span>
	</li>
	<?php } if (!strstr($gizle,"toplam-magaza")) { ?>
	<li class="list-group-item">
	<i class="ion-bag"></i><?php echo _LANG('stat_total_stores'); ?>
	<span class="badge"><?php echo $toplam_magaza; ?></span>
	</li>
	<?php } if (!strstr($gizle,"toplam-uye")) { ?>
	<li class="list-group-item">
	<i class="ion-person"></i><?php echo _LANG('stat_total_members'); ?>
	<span class="badge"><?php echo $toplam_uye; ?></span>
	</li>
	<?php } ?>
</ul>
</div>
<?php } ?>