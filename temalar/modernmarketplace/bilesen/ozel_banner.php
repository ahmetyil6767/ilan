<?php 
if(count(get_included_files())==1) {die();}
$ayarlar=array(
array("tip"=>"upload","id"=>"resim","baslik"=>_LANG('pn_resim_seciniz')),
array("tip"=>"text","id"=>"url","baslik"=>"Url","standart"=>"#"),
array("tip"=>"select","id"=>"yeni_sekme","baslik"=>_LANG('pn_yeni_sekmede_ac'),"secenekler"=>options("Evet,Hayır")),
array("tip"=>"select","id"=>"goster_gizle","baslik"=>_LANG('pn_goster_gizle_opsiyonu'),"secenekler"=>options("Evet,Hayır")),
array("tip"=>"multiple_checkbox","id"=>"gosterme","baslik"=>_LANG('pn_surada_gosterme'),"secenekler"=>options("Mobil,Tablet")),
);
$bilesen["ozel_banner"]=array("id"=>"ozel_banner","baslik"=>_LANG('pn_ozel_banner_reklam'),"ayarlar"=>$ayarlar,"fonksiyon"=>"ozel_banner","aciklama"=>"Bileşen alanlarına reklamı göster/gizle fonksiyonlu resim (banner) reklam eklemenizi sağlar.");
function ozel_banner($id) {
global $baglan;
global $footer_kod;
$baslik=getir("bilesenler","baslik",$id);
$resim=bilesen_ayar("resim",$id);
$url=bilesen_ayar("url",$id);
$yeni_sekme=bilesen_ayar("yeni_sekme",$id);
$goster_gizle=bilesen_ayar("goster_gizle",$id);
$gosterme=rtrim(bilesen_ayar("gosterme",$id),",");
$gosterme=str_replace(array("(",")"),null,$gosterme);
$parcala=explode(",",$gosterme);
$class_yaz=null;
if (!in_array("Mobil",$parcala)) {
	$class_yaz.=" visible-xs";
} else if (!in_array("Tablet",$parcala)) {
	$class_yaz.=" visible-sm";
}
if (!$yeni_sekme) {
    $yeni_sekme="Evet";
}
$yeni_sekme_yaz=null;
if ($yeni_sekme=="Evet") {
    $yeni_sekme_yaz="target='_blank'";
}
?>
<div class="bilesen <?php echo $class_yaz; ?> visible-md visible-lg">
<a id="gg_container" href="<?php echo $url; ?>" <?php echo $yeni_sekme_yaz; ?>><img src="<?php echo $resim; ?>" class="img-responsive" alt="" /></a>
<?php if ($goster_gizle=="Evet") {echo "<a href='#' class='goster_gizle pull-right' style='font-size:12px;'" . _LANG('s_reklami_gizle') . "'clearfix'></div>";} ?>
</div>
<?php 
if ($goster_gizle=="Evet") {
$goster_gizle='
<script type="text/javascript">
$("#ozel_banner-'.$id.' .goster_gizle").click(function(){
	$(this).parent("div").find("a#gg_container").toggle();
	var text = $(this).text();
    $(this).text(text == "'. _LANG('tx_reklami_gizle') . '" ? "'. _LANG('tx_reklami_goster') . '" : "'. _LANG('tx_reklami_gizle') . '");
})
</script>';
$footer_kod[]=$goster_gizle;
}
} 
?>