<?php
if(count(get_included_files())==1) {die();}
$z=0;
foreach($icerik_tipleri as $yaz => $x) {
	$ib_name=$x["id"];
	$ib_baslik=$x["baslik"];
	$ekle_veriler[$z]=array("deger"=>"$ib_name:$ib_baslik","baslik"=>"$ib_baslik");
$z++;
}

$eklenti_ayarlari=array(
array("tip"=>"color","id"=>"renk","baslik"=>"Admin bar arkaplan rengi","standart"=>"#444"),
array("tip"=>"multiple_checkbox","id"=>"ekle_tip","baslik"=>_LANG('tx_bu_icerik_tipleri_icin_ekle_butonlari_olustur'),"secenekler"=>$ekle_veriler,"aciklama"=>_LANG('tx_sectiginiz_icerik_tipleri_icin_ekle_butonlari')),
array("tip"=>"text","id"=>"link_1","baslik"=>"Ekstra link 1","aciklama"=>"Link başlığı, url ve ikon sınıfını virgül ile (,) ayırın. Örn: (JaayQuickWP,http://www.jaayquickwp.com,fa fa-lemon-o)"),
array("tip"=>"text","id"=>"link_2","baslik"=>"Ekstra link 2"),
array("tip"=>"text","id"=>"link_3","baslik"=>"Ekstra link 3"),
array("tip"=>"text","id"=>"link_4","baslik"=>"Ekstra link 4"),
array("tip"=>"text","id"=>"link_5","baslik"=>"Ekstra link 5"),

);
?>