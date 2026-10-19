<?php 
if(count(get_included_files())==1) {die();}
$ayarlar=array(
array("tip"=>"multiple_checkbox","id"=>"goster","baslik"=>_LANG('pn_su_ana_kategorileri_dahil_et'),"secenekler"=>$kat_op,"aciklama"=>"Seçtiğiniz kategoriler menüde gösterilecektir"),
);
$bilesen["ikon_menu"]=array("id"=>"ikon_menu","baslik"=>_LANG('pn_renkli_ikon_menu'),"ayarlar"=>$ayarlar,"fonksiyon"=>"ikon_menu","aciklama"=>"Anasayfa bileşen alanları için hazırlanan renkli bir menü bileşeni!");
function ikon_menu($id) {
global $baglan;
global $footer_kod;
$goster=rtrim(bilesen_ayar("goster",$id),",");
	echo "<div class='bilesen visible-sm visible-md visible-lg'><ul class='renkli_menu'>";
	$kategori_sql=mysqli_query($baglan,"select * from kategoriler where id in ($goster)");
	$say=mysqli_num_rows($kategori_sql);
	$bol=100/$say;
	while ($kategori_dizi=mysqli_fetch_array($kategori_sql)) { 
		$kategori_id=$kategori_dizi["id"];
		$kategori_baslik=$kategori_dizi["baslik"];
		$kategori_ikon=kategori_bilgi("ikon",$kategori_id);
		$kategori_ikon_bg=kategori_bilgi("ikon_bg",$kategori_id);
		$kategori_link=kategori_link_getir($kategori_id);
		$kategori_ilan_sayisi=$kategori_dizi["icerik"];
		if (!$kategori_ikon) {$kategori_ikon="ion-search";}
		if (!$kategori_ikon_bg) {$kategori_ikon_bg=tema_ayar("ana_renk");}
		echo "<li>
		<a href='$kategori_link' title='$kategori_baslik' style='background:$kategori_ikon_bg;width:$bol%;'><i class='$kategori_ikon'></i><div class='kat_baslik'>$kategori_baslik</div>
		<span class='sayi'>($kategori_ilan_sayisi ilan)</span>
		</a>
		</li>";
	} 
	echo "</ul>
	<div class='clearfix mb15'></div>
	</div>";	
}
?>