<?php 
if(count(get_included_files())==1) {die();}
$ayarlar=array(
array("tip"=>"tab_ac","id"=>"genel_ayarlar","baslik"=>"Genel Ayarlar"),
array("tip"=>"text","id"=>"width","baslik"=>_LANG('pn_slider_genisligi'),"standart"=>"300"),
array("tip"=>"text","id"=>"height","baslik"=>_LANG('pn_slider_yuksekligi'),"standart"=>"300"),
array("tip"=>"select","id"=>"oto_oynat","baslik"=>_LANG('pn_slider_oto_oynat'),"secenekler"=>options("Evet:1|Hayır:2","|",":"),"standart"=>"1"),
array("tip"=>"select","id"=>"efekt","baslik"=>_LANG('pn_slider_efekt'),"secenekler"=>options("Kaydırma:slide|Solma:fade","|",":"),"standart"=>"slide"),
array("tip"=>"text","id"=>"hiz","baslik"=>_LANG('pn_slider_hiz'),"standart"=>"500","aciklama"=>"Geçiş hızı (milisaniye cinsinden)"),
array("tip"=>"liste","id"=>"mobil_slider","baslik"=>_LANG('pn_mobil_slider')),
array("tip"=>"tab_kapat"),
);
$bilesen["mobil_slider"]=array("id"=>"mobil_slider","baslik"=>_LANG('pn_mobil_slider'),"ayarlar"=>$ayarlar,"fonksiyon"=>"mobil_slider","aciklama"=>_LANG('e_mobil_slider_aciklama'));
function mobil_slider($id) {
global $footer_kod;
global $baglan;
$height=bilesen_ayar("height",$id);
$width=bilesen_ayar("width",$id);
$oto_oynat=bilesen_ayar("oto_oynat",$id);
$efekt=bilesen_ayar("efekt",$id);
$hiz=bilesen_ayar("hiz",$id);
if (!$hiz or !is_numeric($hiz)) { $hiz=500; }
if (!$height or !is_numeric($height)) { $height=300; }
if (!$width or !is_numeric($width)) { $width=300; }
if (!$efekt) { $efekt='slide'; }
$sql=mysqli_query($baglan,"select * from listeler where tip='mobil_slider' order by sira asc");
$slider_sayi=mysqli_num_rows($sql);
$cikti=null;
if ($slider_sayi>0) {
?>
<div id="mobil_slides" class="visible-xs" style="margin-bottom:20px;">
	<?php 
	while ($slider_dizi=mysqli_fetch_array($sql)) {
		$slider_id=$slider_dizi["l_id"];
		$gorsel=liste_bilgi("gorsel",$slider_id);
		$url=liste_bilgi("url",$slider_id);
		if ($url) {$cikti.="<a href='$url'>";}
		$cikti.="<img src='$gorsel' class='img-responsive' alt=''>";
		if ($url) {$cikti.="</a>";}
		echo $cikti;
		$cikti=null;
	} ?>
</div>
<div class="clearfix mb5"></div>
<?php }
$play_active = ($oto_oynat==1) ? 'true' : 'false';
$slider_js='<script type="text/javascript">
$(function(){
$("#mobil_slides").slidesjs({
	width: '.$width.',
	height: '.$height.',
	navigation: {
		active: true,
		effect: "'.$efekt.'"
	},
	pagination: {
		active: false,
	},
	play: {
		active: '.$play_active.',
		auto: '.$play_active.',
		interval: 5000,
		swap: true,
		pauseOnHover: true,
		restartDelay: 2500
	},
	effect: {
		slide: { speed: '.$hiz.' },
		fade: { speed: '.$hiz.', crossfade: true }
	},
	callback: {
		loaded: function() {
			var slider_buton_top=($("#mobil_slides").height()-$("#mobil_slides .slidesjs-navigation").height())/2;
			$("#mobil_slides .slidesjs-navigation").css({"top":slider_buton_top});
			$("#mobil_slides .slidesjs-previous").attr("title","'._LANG('f_onceki').'").html("<i class=\"fa fa-angle-left mr0\"></i>");
			$("#mobil_slides .slidesjs-next").attr("title","'._LANG('f_sonraki').'").html("<i class=\"fa fa-angle-right mr0\"></i>");
		}
	}
}).show();
});
</script>';
$footer_kod[]=$slider_js;

} ?>