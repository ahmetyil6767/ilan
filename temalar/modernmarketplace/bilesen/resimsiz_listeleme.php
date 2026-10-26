<?php 
if(count(get_included_files())==1) {die();}
$ayarlar=array(
array("tip"=>"tab_ac","id"=>"genel_ayarlar","baslik"=>"Genel Ayarlar"),
array("tip"=>"select","id"=>"kategori","baslik"=>_LANG('pn_kategori'),"secenekler"=>@$kat_op,"aciklama"=>_LANG('ad_listelemede_sadece_bu_kategori')),
array("tip"=>"text","id"=>"adet","baslik"=>_LANG('pn_listeleme_adedi'),"standart"=>21,"aciklama"=>_LANG('ad_ilan_listeleme_adedi_21')),
array("tip"=>"text","id"=>"offset","baslik"=>_LANG('pn_haric_tutulacak_icerik'),"aciklama"=>"Listelemeden offset olarak belirlediğiniz kadar içerik hariç tutulur.","standart"=>0),
array("tip"=>"select","id"=>"liste","baslik"=>_LANG('pn_listeleme_kriteri'),"secenekler"=>options("Yeni İçerikler:yeni|Rastgele İçerikler:rastgele","|",":"),"standart"=>"yeni"),
array("tip"=>"tab_kapat"),
);
$bilesen["resimsiz_listeleme"]=array("id"=>"resimsiz_listeleme","baslik"=>_LANG('pn_resimsiz_ilan_listeleme'),"ayarlar"=>$ayarlar,"fonksiyon"=>"resimsiz_listeleme","aciklama"=>"İş İlanları, Ustalar ve Hizmetler gibi kategoriler için resimsiz ilan listeleme bileşeni.");
function resimsiz_listeleme($id) {
	global $baglan;
	
	// Debug: Hata raporlamayı aç
	$error_reporting_was = error_reporting();
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	
	$baslik=getir("bilesenler","baslik",$id);
	$kategori=bilesen_ayar("kategori",$id);
	$adet=bilesen_ayar("adet",$id);
	$offset=bilesen_ayar("offset",$id);
	$liste=bilesen_ayar("liste",$id);
	if ($liste=="yeni") {
		$order_by_yaz="order by tarih_saat desc";
	} else if ($liste=="rastgele") {
		$order_by_yaz="order by rand()";
	}
	
	$sql_query = "select * from icerik where tip='ilan' and durum='1' and kategori like '%($kategori)%' $order_by_yaz limit $offset, $adet";
	error_log("resimsiz_listeleme (ID: $id) SQL: $sql_query");
	
	$sql = mysqli_query($baglan, $sql_query);
	if (!$sql) {
		echo '' . _LANG('s_sql_hatasi_resimsiz') . ' ' . $id . '): ' . mysqli_error($baglan) . '<br>Sorgu: ' . htmlspecialchars($sql_query) . '</div>';
		error_reporting($error_reporting_was);
		return;
	}
	
$son_dizi=array();
	$son_dizi=null;
	while ($dizi=mysqli_fetch_array($sql)) {
		$ilan_id=$dizi["id"];
		$son_dizi[]=$ilan_id;
	}	
	
	error_reporting($error_reporting_was);	
?>
<div class="bilesen">
	<?php if ($baslik) {echo "<div class='baslik'>$baslik</div>";} ?>
	<?php if (is_array($son_dizi) and count($son_dizi)>0) { ?>
	<ul class="list-group resimsiz">
	<?php
	foreach ($son_dizi as $ilan_id) {
	$ilan_baslik=getir("icerik","baslik",$ilan_id);
	$ilan_link=link_getir($ilan_id);
	$fiyat=fiyat($ilan_id);
	?>
	<a href="<?php echo $ilan_link; ?>" class="list-group-item">
	<div class="ilan_baslik"><?php echo $ilan_baslik; ?></div>
	<div class="fiyat"><?php echo $fiyat; ?></div>
	<div class="il_ilce"><span><?php echo il_getir($ilan_id); ?></span><span><?php echo ilce_getir($ilan_id); ?></span></div>
	</a>
	<?php } ?>
	</ul>
	<?php } else {
		echo _LANG('tx_ilan_bulunamadi');
	}
	?>
	</div>
	<?php 
}
?>