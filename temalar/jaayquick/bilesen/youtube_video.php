<?php 
if(count(get_included_files())==1) {die();}
// Youtube Video
$youtube_ayar=array(
array("tip"=>"text","id"=>"youtube_url","baslik"=>"Youtube Url"),
);
$bilesen["youtube_video"]=array("id"=>"youtube_video","baslik"=>_LANG('pn_youtube_video'),"ayarlar"=>$youtube_ayar,"fonksiyon"=>"youtube_video","aciklama"=>_LANG('e_youtube_video_aciklama'));
function youtube_video($id) {
	global $baglan;
	$baslik=getir("bilesenler","baslik",$id);
	$youtube_url=bilesen_ayar("youtube_url",$id);
	$hazirlik=explode("=",$youtube_url); $video_id=$hazirlik[1];
	?>
	<div class="bilesen">
	<?php if ($baslik) { ?><div class="baslik"><?php echo $baslik; ?></div><?php } ?>
	<div class="embed-responsive embed-responsive-16by9 player mb15">
	<iframe class="embed-responsive-item" src="https://www.youtube.com/embed/<?php echo $video_id; ?>?showinfo=0&amp;autoplay=0&amp;loop=1" frameborder="0" allowfullscreen></iframe>
	</div>
	</div>
	<?php 
}
?>