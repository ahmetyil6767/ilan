<?php
if(count(get_included_files())==1) {die();}
$eklenti_ayarlari=array(
array("tip"=>"text","id"=>"baslik","baslik"=>_LANG('tx_baslik'),"standart"=>"YAPIM AŞAMASINDA"),
array("tip"=>"textarea","id"=>"metin","baslik"=>"Metin","standart"=>_LANG('tx_bu_web_sayfasini_en_kisa_surede_yayina_hazirla')),
array("tip"=>"upload","id"=>"bg","baslik"=>_LANG('tx_arkaplan_gorseli')),
);
?>