<?php 
if(count(get_included_files())==1) {die();}
$ayarlar=array(
array("tip"=>"tab_ac","id"=>"genel_ayarlar","baslik"=>"Genel Ayarlar"),
array("tip"=>"aciklama","id"=>"bilgi","aciklama"=>"Geniş Slider resim genişliği: 892px, Slider ve Reklam tercihindeki resim genişliği 609px'dir.<br />Yüksekliği aşağıdaki kutucuktan ayarlayabilirsiniz."),
array("tip"=>"select","id"=>"stil","baslik"=>_LANG('pn_slider_stili'),"standart"=>1,"secenekler"=>options("Geniş Slider:1|Slider ve Reklam:2","|",":")),
array("tip"=>"text","id"=>"height","baslik"=>_LANG('pn_slider_yuksekligi'),"standart"=>"250"),
array("tip"=>"upload","id"=>"reklam_img","baslik"=>_LANG('pn_slider_yani_resim_reklam'),"kosul"=>"stil:==(2)","aciklama"=>"Kod ve resim aynı anda yüklendiğinde resim reklam tercih edilir. Önerilen banner genişliği 260px'dir."),
array("tip"=>"text","id"=>"reklam_url","baslik"=>_LANG('pn_slider_yani_resim_reklam_url'),"kosul"=>"stil:==(2)"),
array("tip"=>"textarea","id"=>"reklam_kod","baslik"=>_LANG('pn_slider_yani_reklam_kodu'),"kosul"=>"stil:==(2)","satir"=>6,"aciklama"=>"Görsel yüksekliği, aynı zamanda reklam alanının yüksekliğidir."),
array("tip"=>"select","id"=>"oto_oynat","baslik"=>_LANG('pn_slider_oto_oynat'),"secenekler"=>options("Evet:1|Hayır:2","|",":"),"standart"=>"1"),
array("tip"=>"select","id"=>"efekt","baslik"=>_LANG('pn_slider_efekt'),"secenekler"=>options("Kaydırma:slide|Solma:fade","|",":"),"standart"=>"slide"),
array("tip"=>"text","id"=>"hiz","baslik"=>_LANG('pn_slider_hiz'),"standart"=>"500","aciklama"=>"Geçiş hızı (milisaniye cinsinden)"),
array("tip"=>"liste","id"=>"slider","baslik"=>_LANG('pn_slider')),
array("tip"=>"tab_kapat"),
);
$bilesen["slider"]=array("id"=>"slider","baslik"=>_LANG('pn_slider'),"ayarlar"=>$ayarlar,"fonksiyon"=>"slider","aciklama"=>_LANG('e_slider_aciklama'));
function slider($id) {
global $footer_kod;
global $baglan;
$stil=bilesen_ayar("stil",$id);
if ($stil==2) {$width=609;} else {$width=892;}
$height=bilesen_ayar("height",$id);
$oto_oynat=bilesen_ayar("oto_oynat",$id);
$efekt=bilesen_ayar("efekt",$id);
$hiz=bilesen_ayar("hiz",$id);
if (!$hiz or !is_numeric($hiz)) { $hiz=500; }
if (!$height or !is_numeric($height)) { $height=250; }
if (!$width or !is_numeric($width)) { $width=892; }
if (!$efekt) { $efekt='slide'; }
$reklam_kodu=bilesen_ayar("reklam_kod",$id);
$reklam_img=bilesen_ayar("reklam_img",$id);
$reklam_url=bilesen_ayar("reklam_url",$id);
$sql=mysqli_query($baglan,"select * from listeler where tip='slider' order by sira asc");
$slider_sayi=mysqli_num_rows($sql);
$cikti=null;
if ($slider_sayi>0) { 
if ($stil==1) {
?>
<div class="bilesen visible-md visible-lg">
	<div id="slides">
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
</div>
<div class="clearfix mb5"></div>
<?php } else if ($stil==2) { ?>
<div class="bilesen">
	<div class="row">
		<div class="col-md-29">
			<div id="slides">
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
		</div>
		<div class="col-md-13">
			<div class="text-center">
			<?php 
			if ($reklam_img) {
				if ($reklam_url) {$kod.="<a href='$reklam_url'>";}
				$kod.="<img src='$reklam_img' class='img-responsive' alt='' />";
				if ($reklam_url) {$kod.="</a>";}
			} else {
				$kod=$reklam_kodu;
			}
			echo $kod; ?>
			</div>
		</div>
	</div>
</div>
<div class="clearfix mb5"></div>
<?php }
$play_active = ($oto_oynat==1) ? 'true' : 'false';
$slider_js='<script type="text/javascript">
$(function(){
$("#slides").slidesjs({
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
			var slider_buton_top=($("#slides").height()-$(".slidesjs-navigation").height())/2;
			$(".slidesjs-navigation").css({"top":slider_buton_top});
			$(".slidesjs-previous").attr("title","'._LANG('f_onceki').'").html("<i class=\"fa fa-angle-left mr0\"></i>");
			$(".slidesjs-next").attr("title","'._LANG('f_sonraki').'").html("<i class=\"fa fa-angle-right mr0\"></i>");
		}
	}
});
});
</script>';
$footer_kod[]=$slider_js;

} } ?>