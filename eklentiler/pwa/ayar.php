<?php
if(count(get_included_files())==1) {die(_LANG('tx_dogrudan_erisim_engellendi'));}

$eklenti_ayarlari=array(
array("tip"=>"grup_ac"),
array("tip"=>"select","id"=>"pwa","baslik"=>_LANG('tx_pwa_uygulamasi_etkinlestirilsin_mi'),"secenekler"=>options("Evet:1,Hayır:0",",",":"),"standart"=>0),
array("tip"=>"text","id"=>"name","baslik"=>_LANG('tx_uygulama_adi_name'),"gerekli"=>1),
array("tip"=>"text","id"=>"short_name","baslik"=>_LANG('tx_uygulama_kisa_adi_short_name'),"gerekli"=>1),    
array("tip"=>"text","id"=>"start_url","baslik"=>"Uygulama Başlangıç URL'si (start_url)"),
array("tip"=>"grup_kapat"),    
array("tip"=>"grup_ac"),
array("tip"=>"color","id"=>"background_color","baslik"=>"Arkaplan Rengi (background_color)","standart"=>"#000000"),
array("tip"=>"color","id"=>"theme_color","baslik"=>"Tema Rengi (theme_color)","standart"=>"#ffffff"),
array("tip"=>"select","id"=>"display","baslik"=>_LANG('tx_gorunum_display'),"standart"=>"standalone","secenekler"=>options("standalone,fullscreen,minimal-ui,browser"),"gerekli"=>1),  
array("tip"=>"text","id"=>"scope","baslik"=>"Kapsam (scope)"),
array("tip"=>"grup_kapat"),    
array("tip"=>"upload","id"=>"icon_192","baslik"=>"Küçük İkon (icon 192x192)"),
array("tip"=>"upload","id"=>"icon_512","baslik"=>"Büyük İkon (icon 512x512)"),
);
?>

<?php 
if (@$_POST["olustur"] or @$_POST["guncelle"]) {
    $manifest=array();
    $manifest["short_name"]=eklenti_ayar("short_name");
    $manifest["name"]=eklenti_ayar("name");

    $manifest["icons"]=array();
    
    $icon_192=eklenti_ayar("icon_192");
    if ($icon_192) {
        $manifest["icons"][]=array(
            "src"=> $icon_192,
            "type"=> upload_mime_type($icon_192),
            "sizes"=> "192x192"        
        );
    }
    $icon_512=eklenti_ayar("icon_512");
    if ($icon_512) {
        $manifest["icons"][]=array(
            "src"=> $icon_512,
            "type"=> upload_mime_type($icon_512),
            "sizes"=> "512x512"        
        );
    }
    
    $manifest["background_color"]=eklenti_ayar("background_color");
    $manifest["display"]=eklenti_ayar("display");
    
    if (eklenti_ayar("start_url")) {
        $manifest["start_url"]=eklenti_ayar("start_url");
    }
    if (eklenti_ayar("scope")) {
        $manifest["scope"]=eklenti_ayar("scope");   
    }
    
    $manifest["theme_color"]=eklenti_ayar("theme_color");
        
    $manifest_json=json_encode($manifest);
     
    $olustur=fopen(ROOT_DIR."/manifest.json", "w"); // dosyamızı açtık	
	fwrite($olustur,$manifest_json);// dosya içeriğimizi oluşturduk.
	fclose($olustur); // dosyamızı kapattık yazma işlemini bitirdik.  
    
$sw_content='self.addEventListener("install", function(event) {
  // Perform install steps
});
self.addEventListener("fetch", function(event) {
});
';
    $sw_olustur=fopen(ROOT_DIR."/sw.js", "w"); // dosyamızı açtık	
    fwrite($sw_olustur,$sw_content);// dosya içeriğimizi oluşturduk.
	fclose($sw_olustur);

    if ($_POST["olustur"]) {
        $_SESSION["mesaj"]=array(_LANG('msj_manifest_olusturuldu'),"success");
    }
    if ($_POST["guncelle"]) {
        $_SESSION["mesaj"]=array(_LANG('msj_manifest_guncellendi'),"success");
    }
   yenile();
}


if (file_exists(ROOT_DIR."/manifest.json")) {
    $manifest_url=site_url()."/manifest.json";
    $manifest_bilgi="<div class='text-success'><i class='icon check clear mr-2'></i>manifest.json dosyası mevcut. <a href='$manifest_url' target='_blank' class='text-reset'><i class='icon linkify clear'></i></a></div>";
} else {
    $manifest_bilgi="<div class='text-danger'><i class='icon times clear mr-2'></i>manifest.json dosyası mevcut değil.</div>";
}

if (file_exists(ROOT_DIR."/sw.js")) {
    $sw_url=site_url()."/sw.js";
    $sw_bilgi="<div class='text-success'><i class='icon check clear mr-2'></i>sw.js (service worker) dosyası mevcut. <a href='$sw_url' target='_blank' class='text-reset'><i class='icon linkify clear'></i></a></div>";
} else {
    $sw_bilgi="<div class='text-danger'><i class='icon times clear mr-2'></i>sw.js (service worker) dosyası mevcut değil.</div>";
}

$butonlar=null;

if (!file_exists(ROOT_DIR."/manifest.json")) {
    $butonlar='<button type="submit" name="olustur" value="'. _LANG('tx_olustur') .'" class="btn btn-primary btn-block"><i class="icon plus"></i>manifest.json ve sw.js dosyalarını oluştur</button>';
} else {
    $butonlar='<button type="submit" name="guncelle" value="'. _LANG('tx_guncelle') .'" class="btn btn-primary btn-block"><i class="icon refresh"></i>manifest.json ve sw.js dosyalarını güncelle</button>';
}

$eklenti_duyurulari[]='
<form action="" method="post">
<div class="panel mb-4">
    <div class="panel-heading">'. _LANG('hb_uygulama_dosyalari') .'</div>
    <div class="panel-body">
    <div class="alert alert-info">'. _LANG('hb_ayarlari_degistirdikten_sonra_asagidaki_butona') .'</div>
    '.$manifest_bilgi.$sw_bilgi.'</div>
    <div class="panel-footer">'.$butonlar.'</div>
</div>
</form>
';

?>