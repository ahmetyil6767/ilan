<?php 
if(count(get_included_files())==1) {die();}
$yazi_kat_op[]=array("deger"=>"tumu","baslik"=>_LANG('pn_tumu'));
$ayarlar=array(
array("tip"=>"tab_ac","id"=>"genel_ayarlar","baslik"=>"Genel Ayarlar"),
array("tip"=>"text","id"=>"adet","baslik"=>_LANG('pn_listeleme_adedi'),"standart"=>10,"aciklama"=>"Gösterilecek toplam yazı sayısı. Standart: 21"),
array("tip"=>"text","id"=>"offset","baslik"=>_LANG('pn_haric_tutulacak_icerik'),"aciklama"=>"Listelemeden offset olarak belirlediğiniz kadar içerik hariç tutulur.","standart"=>0),
array("tip"=>"text","id"=>"link_baslik","baslik"=>_LANG('pn_baslik_yanindaki_link_metni')),
array("tip"=>"text","id"=>"link","baslik"=>_LANG('pn_baslik_yanindaki_link_hedefi')),
array("tip"=>"select","id"=>"sutun","baslik"=>_LANG('pn_sutun_sayisi'),"aciklama"=>"Bir satırda gösterilecek yazı adedi.  Standart: 7","secenekler"=>options("2,3,4,5,6,7"),"standart"=>5),
array("tip"=>"select","id"=>"liste","baslik"=>_LANG('pn_listeleme_kriteri'),"secenekler"=>options("Yeni İçerikler:yeni","|",":"),"standart"=>1),
array("tip"=>"select","id"=>"goster","baslik"=>_LANG('pn_su_kategorinin_iceriklerini_goster'),"secenekler"=>$yazi_kat_op,"standart"=>"tumu"),
array("tip"=>"tab_kapat"),
);

$bilesen["yazi_listeleme"]=array("id"=>"yazi_listeleme","baslik"=>_LANG('pn_yazi_listeleme'),"ayarlar"=>$ayarlar,"fonksiyon"=>"yazi_listeleme","aciklama"=>_LANG('e_yazi_listeleme_aciklama'));

function yazi_listeleme($id) {
	global $baglan;
	
	// Debug: Hata raporlamayı aç
	$error_reporting_was = error_reporting();
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	
	$baslik=getir("bilesenler","baslik",$id);
	$adet=bilesen_ayar("adet",$id);
	$offset=bilesen_ayar("offset",$id);
	$sutun=bilesen_ayar("sutun",$id);
	$liste=bilesen_ayar("liste",$id);
	$link_baslik=bilesen_ayar("link_baslik",$id);
	$link=bilesen_ayar("link",$id);
	$goster=bilesen_ayar("goster",$id);
	if ($sutun==2) {
		$md_sutun=2;
		$sm_sutun=2;
		$xs_sutun=2;
		$col_yaz="col-md-21 col-sm-21 col-xs-21";
		$resim_boyut="560_330";
	} else if ($sutun==3) {
		$md_sutun=3;
		$sm_sutun=2;
		$xs_sutun=2;
		$col_yaz="col-md-14 col-sm-21 col-xs-21";
		$resim_boyut="560_330";
	} else if ($sutun==4) {
		$md_sutun=4;
		$sm_sutun=2;
		$xs_sutun=2;
		$col_yaz="col-md-a4 col-sm-21 col-xs-21";
		$resim_boyut="560_330";
	} else if ($sutun==5) {
		$md_sutun=5;
		$sm_sutun=3;
		$xs_sutun=2;
		$col_yaz="col-md-a5 col-sm-14 col-xs-14";
		$resim_boyut="260_160";
	} else if ($sutun==6) {
		$md_sutun=6;
		$sm_sutun=3;
		$xs_sutun=2;
		$col_yaz="col-md-7 col-sm-14 col-xs-14";
		$resim_boyut="260_160";
	} else if ($sutun==7) {
		$md_sutun=7;
		$sm_sutun=3;
		$xs_sutun=2;
		$col_yaz="col-md-6 col-sm-14 col-xs-14";
		$resim_boyut="260_160";
	}
	if ($goster!="tumu") {
		$kat_sql_yaz=" and kategori like '%($goster)%'";
	}
	
	$son_dizi = array();
	if ($liste=="yeni") {
		$sql_query = "select * from icerik where tip='yazi' and durum='1' $kat_sql_yaz order by tarih_saat desc limit $offset, $adet";
		error_log("yazi_listeleme (ID: $id) SQL: $sql_query");
		
		$sql = mysqli_query($baglan, $sql_query);
		if (!$sql) {
			echo '<div class="alert alert-danger">SQL Hatası (yazi_listeleme ID: ' . $id . '): ' . mysqli_error($baglan) . '<br>Sorgu: ' . htmlspecialchars($sql_query) . '</div>';
			error_reporting($error_reporting_was);
			return;
		}
		
		while ($dizi=mysqli_fetch_array($sql)) {
			$icerik_id=$dizi["id"];
			$son_dizi[]=$icerik_id;
		}	
	}
	
	error_reporting($error_reporting_was);
?>
<div class="bilesen ilan_listeleme">
	<?php if ($baslik) { ?>
	<div class="baslik">
	<?php echo $baslik; ?>
	<?php if ($link_baslik) { ?><a href="<?php echo $link; ?>" class="pull-right visible-sm visible-md visible-lg"><?php echo $link_baslik; ?></a><?php } ?>
	</div>
	<?php } ?>
	<div class="row">
		<?php
		$a=1;
		foreach ($son_dizi as $ilan_id) {
		$ilan_baslik=getir("icerik","baslik",$ilan_id);
		$ilan_resim=resim_getir($resim_boyut,$ilan_id);
		$ilan_link=link_getir($ilan_id);
		if ($fiyat_goster=="Evet") {
			$fiyat=fiyat($ilan_id);
		}
		?>
		<div class="<?php echo $col_yaz; ?>">
			<div class="liste">
				<a href="<?php echo $ilan_link; ?>" title="<?php echo $ilan_baslik; ?>" class="r_ust">
					<img src="<?php echo $ilan_resim; ?>" alt="<?php echo $ilan_baslik; ?>" class="img-responsive"/>
					<?php if ($fiyat_goster=="Evet" and $fiyat) {echo "<div class='fiyat_layer visible-md visible-lg'>$fiyat</div>";} ?>
				</a>
				<h3><a href="<?php echo $ilan_link; ?>" title="<?php echo $ilan_baslik; ?>"><?php echo $ilan_baslik; ?></a></h3>
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