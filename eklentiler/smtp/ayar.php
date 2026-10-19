<?php
if(count(get_included_files())==1) {die();}
$type=array(
array("deger"=>"","baslik"=>"None"),
array("deger"=>"ssl","baslik"=>"SSL"),
array("deger"=>"tls","baslik"=>"TLS"),
);

$auth=array(
array("deger"=>false,"baslik"=>"Hayır"),
array("deger"=>true,"baslik"=>"Evet"),
);

$eklenti_ayarlari=array(
array("tip"=>"text","id"=>"from_email","baslik"=>"From Email","aciklama"=>_LANG('tx_gonderilen_maillerde_kimden_alaninda_gorunecek_2')),
array("tip"=>"text","id"=>"from_name","baslik"=>"From Name","aciklama"=>_LANG('tx_gonderilen_maillerde_kimden_alaninda_gorunecek')),
array("tip"=>"text","id"=>"smtp_host","baslik"=>"SMTP Host"),
array("tip"=>"select","id"=>"smtp_type","baslik"=>"Type of Encryption","secenekler"=>$type),
array("tip"=>"text","id"=>"smtp_port","baslik"=>"SMTP Port","standart"=>25),
array("tip"=>"select","id"=>"smtp_auth","baslik"=>"SMTP Authentication","secenekler"=>$auth),
array("tip"=>"text","id"=>"smtp_username","baslik"=>"SMTP Username","aciklama"=>_LANG('tx_mail_sunucusuna_baglanilacak_hesap')),
array("tip"=>"password","id"=>"smtp_password","baslik"=>"SMTP Password","aciklama"=>_LANG('tx_mail_sifresi')),
);

$site_url=site_url();
$eklenti_duyurulari[]="
<div>" . _LANG('hb_ayarlari_test_edin') . "</div>
<p>" . _LANG('hb_ayarlarin_dogru_olup_olmadigini_kontrol_etmek') . "</p>
<a href='$site_url/$admin_dizini/eklentiler.php?eklenti=smtp&sayfa=test' class='btn btn-default'><i class='fa fa-send'></i>Test maili gönder</a>
<div class='clearfix mb20'></div>
<div>" . _LANG('hb_mail_gonder') . "</div>
<p>" . _LANG('hb_bir_e_posta_adresine_mail_gonderin') . "</p>
<a href='$site_url/$admin_dizini/eklentiler.php?eklenti=smtp&sayfa=mail' class='btn btn-default'><i class='fa fa-send'></i>Mail gönder</a>
";
?>