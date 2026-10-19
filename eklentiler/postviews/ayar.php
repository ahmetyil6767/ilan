<?php
if(count(get_included_files())==1) {die();}
$z=0;
foreach($icerik_tipleri as $yaz => $x) {
	$ib_name=$x["id"];
	$ib_baslik=$x["baslik"];
	$haric_tut_veriler[$z]=array("deger"=>"$ib_name","baslik"=>"$ib_baslik");
$z++;
}

$eklenti_ayarlari=array(
array("tip"=>"select","id"=>"yonetici_hit","baslik"=>" . _LANG('tx_yonetici_ziyaretleri_sayilsin_mi') . ","standart"=>"evet","secenekler"=>array(array("deger"=>"evet","baslik"=>"Evet"),array("deger"=>"hayir","baslik"=>"Hayır"))),
array("tip"=>"select","id"=>"session","baslik"=>" . _LANG('tx_session_kontrolu_yapilsin_mi') . ","standart"=>"evet","secenekler"=>array(array("deger"=>"evet","baslik"=>"Evet"),array("deger"=>"hayir","baslik"=>"Hayır")),"aciklama"=>"Session kontrolü her yenilemede ziyaret sayısının artmamasını sağlar. \"Evet\" olarak işaretlenirse ziyaretçinin tarayıcıyı kapatana kadar aynı içeriğe yapacağı sadece ilk ziyaret sayılır."),
array("tip"=>"multiple_checkbox","id"=>"haric_tip","baslik"=>" . _LANG('tx_bu_icerik_tiplerini_haric_tut') . ","secenekler"=>$haric_tut_veriler,"aciklama"=>" . _LANG('tx_sectiginiz_icerik_tipleri_icin_okunma_sayilari') . "),
array("tip"=>"multiple_checkbox","id"=>"say","baslik"=>" . _LANG('tx_su_goruntulenme_sayilarini_kaydet') . ","standart"=>"(gun),(hafta),(ay)","secenekler"=>array(array("deger"=>"gun","baslik"=>" . _LANG('tx_gunluk') . "),array("deger"=>"hafta","baslik"=>" . _LANG('tx_haftalik') . "),array("deger"=>"ay","baslik"=>"Aylık")),"aciklama"=>" . _LANG('tx_iceriklerin_toplam_goruntulenme_sayisina_ek_ol') . "),
);

?>