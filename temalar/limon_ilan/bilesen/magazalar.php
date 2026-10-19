<?php 
if(count(get_included_files())==1) {die();}
$ayarlar=array(
array("tip"=>"tab_ac","id"=>"genel_ayarlar","baslik"=>"Genel Ayarlar"),
array("tip"=>"select","id"=>"sutun","baslik"=>_LANG('pn_sutun_sayisi'),"aciklama"=>"Bir satırda gösterilecek mağaza adedi.  Standart: 5","secenekler"=>options("3,4,5,6,7"),"standart"=>6),
array("tip"=>"text","id"=>"adet","baslik"=>_LANG('pn_listeleme_adedi'),"standart"=>6,"aciklama"=>_LANG('ad_magaza_listeleme_adedi_6')),
array("tip"=>"select","id"=>"liste","baslik"=>_LANG('pn_listeleme_kriteri'),"secenekler"=>options("Son Eklenenler:son","|",":"),"standart"=>1),
array("tip"=>"text","id"=>"link_baslik","baslik"=>_LANG('pn_baslik_yanindaki_link_metni')),
array("tip"=>"text","id"=>"link","baslik"=>_LANG('pn_baslik_yanindaki_link_hedefi')),
array("tip"=>"select","id"=>"baslik_stil","baslik"=>_LANG('pn_baslik_stili'),"secenekler"=>options("Standart,Alternatif")),
array("tip"=>"tab_kapat"),
);

$bilesen["magazalar"]=array("id"=>"magazalar","baslik"=>_LANG('stores'),"ayarlar"=>$ayarlar,"fonksiyon"=>"magazalar","aciklama"=>_LANG('e_magaza_listeleme_aciklama'));
function magazalar($id) {
global $baglan;
$baslik=getir("bilesenler","baslik",$id);
$adet=bilesen_ayar("adet",$id);
$sutun=bilesen_ayar("sutun",$id);
$liste=bilesen_ayar("liste",$id);
$link_baslik=bilesen_ayar("link_baslik",$id);
$link=bilesen_ayar("link",$id);
$baslik_stil=bilesen_ayar("baslik_stil",$id);
if ($sutun==2) {
	$md_sutun=2;
	$sm_sutun=2;
	$xs_sutun=2;
	$col_yaz="col-md-21 col-sm-21 col-xs-21";
	$resim_boyut="530_393";
} else if ($sutun==3) {
	$md_sutun=3;
	$sm_sutun=2;
	$xs_sutun=2;
	$col_yaz="col-md-14 col-sm-21 col-xs-21";
	$resim_boyut="530_393";
} else if ($sutun==4) {
	$md_sutun=4;
	$sm_sutun=2;
	$xs_sutun=2;
	$col_yaz="col-md-a4 col-sm-21 col-xs-21";
	$resim_boyut="530_393";
} else if ($sutun==5) {
	$md_sutun=5;
	$sm_sutun=3;
	$xs_sutun=3;
	$col_yaz="col-md-a5 col-sm-14 col-xs-14";
	$resim_boyut="160_120";
} else if ($sutun==6) {
	$md_sutun=6;
	$sm_sutun=3;
	$xs_sutun=3;
	$col_yaz="col-md-7 col-sm-14 col-xs-14";
	$resim_boyut="160_120";
} else if ($sutun==7) {
	$md_sutun=7;
	$sm_sutun=3;
	$xs_sutun=3;
	$col_yaz="col-md-6 col-sm-14 col-xs-14";
	$resim_boyut="160_120";
}
if ($liste=="son") {
	$sql=mysqli_query($baglan,"select * from magazalar where durum='1' order by tarih_saat desc limit $adet");
	while ($dizi=mysqli_fetch_array($sql)) {
		$magaza_id=$dizi["id"];
		$son_dizi[]=$magaza_id;
	}	
}
?>
<div class="bilesen ilan_listeleme">
	<?php if ($baslik) { if ($baslik_stil=="Alternatif") { ?>
	<div class="baslik alternatif">
	<h3><?php echo $baslik; ?></h3>
	<?php if ($link_baslik) { ?><a href="<?php echo $link; ?>" class="pull-right visible-sm visible-md visible-lg"><?php echo $link_baslik; ?></a><?php } ?>
	</div>	
	<?php } else { ?>
	<div class="baslik">
	<?php echo $baslik; ?>
	<?php if ($link_baslik) { ?><a href="<?php echo $link; ?>" class="pull-right visible-sm visible-md visible-lg"><?php echo $link_baslik; ?></a><?php } ?>
	</div>
	<?php } } ?>
	<div class="row">
		<?php
		$a=1;
		if (!isset($son_dizi) or !is_array($son_dizi)) {$son_dizi=array();}
		foreach ($son_dizi as $magaza_id) {
		$magaza_baslik=getir("magazalar","baslik",$magaza_id);
		$magaza_resim=thumbnail_url("150_150",magaza_bilgi("profil_gorseli",$magaza_id));
		if ($magaza_resim=="-150_150.") {
			$magaza_resim=tema_dizini()."/img/yok/150_150.png";
		}
		$magaza_link=magaza_link_getir($magaza_id);
		?>
		<div class="<?php echo $col_yaz; ?>">
			<div class="liste prelative">
				<a href="<?php echo $magaza_link; ?>" title="<?php echo $magaza_baslik; ?>" class="r_ust">
					<img src="<?php echo $magaza_resim; ?>" alt="<?php echo $magaza_baslik; ?>" class="img-responsive"/>
				</a>
				<h3 class="pabsolute bottom left magaza_bilesen_baslik"><a href="<?php echo $magaza_link; ?>" title="<?php echo $magaza_baslik; ?>"><?php echo $magaza_baslik; ?></a></h3>
			</div>
		</div>
		<?php 
		if ($a%$md_sutun==0) {echo "<div class='visible-md visible-lg clearfix'></div>";}
		if ($a%$sm_sutun==0) {echo "<div class='visible-sm clearfix'></div>";}
		if ($a%$xs_sutun==0) {echo "<div class='visible-xs clearfix'></div>";}
		$a++;} ?>
	</div>
</div>	
	

<?php
}
?>