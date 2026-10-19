<?php
if(count(get_included_files())==1) {die();}
$eklenti_ayarlari=array(
array("tip"=>"select","id"=>"gonder","baslik"=>_LANG('tx_mail_gonder'),"secenekler"=>options(_LANG('tx_evet_hayir')),"aciklama"=>_LANG('tx_mail_gonderimlerini_acip_kapatmanizi_saglar'),"standart"=>"Evet"),
);
?>