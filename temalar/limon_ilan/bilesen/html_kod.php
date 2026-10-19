<?php 
if(count(get_included_files())==1) {die();}
$ayarlar=array(
array("tip"=>"textarea","id"=>"kod","baslik"=>"Html Kod","satir"=>10),
array("tip"=>"select","id"=>"goster_gizle","baslik"=>_LANG('pn_goster_gizle_opsiyonu'),"secenekler"=>options("Evet,Hayır")),
array("tip"=>"multiple_checkbox","id"=>"gosterme","baslik"=>_LANG('pn_surada_gosterme'),"secenekler"=>options("Mobil,Tablet")),
);
$bilesen["html_kod"]=array("id"=>"html_kod","baslik"=>"HTML Kod","ayarlar"=>$ayarlar,"fonksiyon"=>"html_kod","aciklama"=>"Html kod ekleyin.");
function html_kod($id) {
global $baglan;
global $footer_kod;
$baslik=getir("bilesenler","baslik",$id);
$goster_gizle=bilesen_ayar("goster_gizle",$id);
$html_kod=bilesen_ayar("kod",$id);
$gosterme=bilesen_ayar("gosterme",$id);
$parcala=explode(",",$gosterme);
if (!in_array("(Mobil)",$parcala)) {
	$class_yaz.=" visible-xs";
} else if (!in_array("(Tablet)",$parcala)) {
	$class_yaz.=" visible-sm";
}
?>
<div class="bilesen <?php echo $class_yaz; ?> visible-md visible-lg">
<div id="gg_container"><?php echo $html_kod; ?></div>
<?php if ($goster_gizle=="Evet") {echo "<a href='#' class='goster_gizle pull-right' style='font-size:12px;'>Reklamı gizle</a><div class='clearfix'></div>";} ?>
</div>
<?php 
if ($goster_gizle=="Evet") {
$goster_gizle='
<script type="text/javascript">
$("#html_kod-'.$id.' .goster_gizle").click(function(){
	$(this).parent("div").find("#gg_container").toggle();
	var text = $(this).text();
    $(this).text(text == "'. _LANG('tx_reklami_gizle') . '" ? "'. _LANG('tx_reklami_goster') . '" : "'. _LANG('tx_reklami_gizle') . '");
})
</script>';
$footer_kod[]=$goster_gizle;
}
} 
?>