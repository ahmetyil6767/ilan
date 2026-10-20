<?php 
if(count(get_included_files())==1) {die();}
global $kat_op;
$ayarlar=array(
array("tip"=>"tab_ac","id"=>"genel_ayarlar","baslik"=>"Genel Ayarlar"),
array("tip"=>"text","id"=>"adet","baslik"=>_LANG('pn_listeleme_adedi'),"standart"=>21,"aciklama"=>_LANG('ad_ilan_listeleme_adedi_21')),
array("tip"=>"text","id"=>"offset","baslik"=>_LANG('pn_haric_tutulacak_icerik'),"aciklama"=>"Listelemeden offset olarak belirlediğiniz kadar içerik hariç tutulur.","standart"=>0),
array("tip"=>"text","id"=>"link_baslik","baslik"=>_LANG('pn_baslik_yanindaki_link_metni')),
array("tip"=>"text","id"=>"link","baslik"=>_LANG('pn_baslik_yanindaki_link_hedefi')),
array("tip"=>"color","id"=>"border_color","baslik"=>_LANG('pn_kenarlik_rengi_border_color'),"standart"=>"#e5e5e5"),
array("tip"=>"color","id"=>"border_color_hover","baslik"=>_LANG('pn_hover_kenarlik_rengi_border_color_hover'),"standart"=>"#FBAD37"),
array("tip"=>"select","id"=>"baslik_stil","baslik"=>_LANG('pn_baslik_stili'),"secenekler"=>options("Standart,Alternatif")),
array("tip"=>"select","id"=>"fiyat_goster","baslik"=>_LANG('pn_fiyat_gosterilsin_mi'),"secenekler"=>options("Evet,Hayır"),"standart"=>"Hayır"),
array("tip"=>"select","id"=>"liste","baslik"=>_LANG('pn_listeleme_kriteri'),"secenekler"=>options("Yeni İçerikler:yeni|Popüler İlanlar:populer|Bugünün Popüler İlanları:bugun_populer|Anasayfa Vitrini:anasayfa_vitrin|Acil Acil:acil_acil","|",":"),"standart"=>1),
array("tip"=>"checkbox","id"=>"rastgele","baslik"=>_LANG('pn_ilanlari_rastgele_goster'),"aciklama"=>"Listelemedeki ilanları rastgele göstermenizi sağlar.","kosul"=>"liste:!=(yeni)"),
array("tip"=>"multiple_checkbox","id"=>"gosterme","baslik"=>_LANG('pn_su_ana_kategori_ilanlarini_gosterme'),"secenekler"=>$kat_op,"aciklama"=>"Seçtiğiniz kategori ilanlarını listeleden çıkarmanızı sağlar.","kosul"=>"liste:==(yeni)"),
array("tip"=>"tab_kapat"),
);

$bilesen["ikili_listeleme"]=array("id"=>"ikili_listeleme","baslik"=>_LANG('pn_ikili_ilan_listeleme'),"ayarlar"=>$ayarlar,"fonksiyon"=>"ikili_listeleme","aciklama"=>_LANG('e_ikili_listeleme_aciklama'));

function ikili_listeleme($id) {
	global $baglan;
	
	// Debug: Hata raporlamayı aç
	$error_reporting_was = error_reporting();
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	
	$baslik_stil=null;
	$listeleme_stil=null;
	$rastgele=null;
	$baslik=getir("bilesenler","baslik",$id);
	$adet=(int)bilesen_ayar("adet",$id);
	if (!$adet) $adet=21;
	$offset=(int)bilesen_ayar("offset",$id);
	if (!$offset) $offset=0;
	$sutun=(int)bilesen_ayar("sutun",$id);
	$mobil_sutun=(int)bilesen_ayar("mobil_sutun",$id);
	if (!$mobil_sutun) $mobil_sutun=3;
	$border_color=bilesen_ayar("border_color",$id);
	if (!$border_color) $border_color="#e5e5e5";
	$border_color_hover=bilesen_ayar("border_color_hover",$id);
	if (!$border_color_hover) $border_color_hover="#FBAD37";
	$liste=bilesen_ayar("liste",$id);
	if (!$liste) $liste="yeni";
	$link_baslik=bilesen_ayar("link_baslik",$id);
	$link=bilesen_ayar("link",$id);
	$gosterme=bilesen_ayar("gosterme",$id);
	$fiyat_goster=bilesen_ayar("fiyat_goster",$id);
	$baslik_stil=bilesen_ayar("baslik_stil",$id);
	$listeleme_stil=bilesen_ayar("listeleme_stil",$id);
	$rastgele=bilesen_ayar("rastgele",$id);
	if (!$rastgele) $rastgele=0;

	$resim_boyut="160_120";
	
	$kat_ex_yaz=null;
	$son_dizi=array();
	if ($gosterme) {
		$gosterme_son=rtrim($gosterme,",");
		$parcala=explode(",",$gosterme_son);
		foreach ($parcala as $parca) {
			$kat_ex_yaz.=" and kategori not like '%$parca%'";
		}
	}
	if (!$rastgele) {
		$rastgele=0;
	}
	if ($rastgele==1) {
		$order_by_sql=" order by rand()";
		$order_by_pw_sql=" order by rand()";
	} else {
		$order_by_sql=" order by dp.id desc";
		$order_by_pw_sql=" order by pw.hit desc";
	}
	
	// SQL sorgusu hazırla
	$sql_query = "";
	if ($liste=="yeni") {
		$sql_query = "select * from icerik where tip='ilan' and durum='1' $kat_ex_yaz order by tarih_saat desc limit $offset, $adet";
	} else if ($liste=="anasayfa_vitrin") {
		$sql_query = "SELECT i.id from icerik as i INNER JOIN dopingler as dp ON i.id=dp.ilan_id and dp.doping='anasayfa_vitrini' and dp.durum='1' and i.durum='1' $order_by_sql limit $offset, $adet";
	} else if ($liste=="acil_acil") {
		$sql_query = "SELECT i.id from icerik as i INNER JOIN dopingler as dp ON i.id=dp.ilan_id and dp.doping='acil_acil' and dp.durum='1' and i.durum='1' $order_by_sql limit $offset, $adet";
	} else if ($liste=="populer") {
		$sql_query = "SELECT i.id from icerik as i INNER JOIN postviews as pw ON i.id=pw.id and pw.tip='toplam' where i.tip='ilan' and i.durum='1' $order_by_pw_sql limit $offset, $adet";
	} else if ($liste=="bugun_populer") {
		$bugun=date("d-m-Y");
		$sql_query = "SELECT i.id from icerik as i INNER JOIN postviews as pw ON i.id=pw.id and pw.tip='gun' and pw.tarih='$bugun' where i.tip='ilan' and i.durum='1' $order_by_pw_sql limit $offset, $adet";
	} else {
		$sql_query = "select * from icerik where tip='ilan' and durum='1' $kat_ex_yaz order by tarih_saat desc limit $offset, $adet";
	}
	
	// Debug: SQL sorgusunu logla
	error_log("ikili_listeleme (ID: $id) SQL: $sql_query");
	
	$sql = mysqli_query($baglan, $sql_query);
	if (!$sql) {
		echo '<div class="alert alert-danger">SQL Hatası (ikili_listeleme ID: ' . $id . '): ' . mysqli_error($baglan) . '<br>Sorgu: ' . htmlspecialchars($sql_query) . '</div>';
		error_reporting($error_reporting_was);
		return;
	}
	
	while ($dizi=mysqli_fetch_array($sql)) {
		$ilan_id=$dizi["id"];
		$son_dizi[]=$ilan_id;
	}
	
	error_reporting($error_reporting_was);
?>
<style>
    #ikili_listeleme-<?php echo $id; ?> .liste.yatay {border:2px solid <?php echo $border_color; ?>;}
    #ikili_listeleme-<?php echo $id; ?> .liste.yatay:hover {border:2px solid <?php echo $border_color_hover; ?>;}
</style>
<div class="bilesen ilan_listeleme yatay">
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
		if (is_array($son_dizi)) {
		if (count($son_dizi)>0) {
		foreach ($son_dizi as $ilan_id) {
		$ilan_baslik=getir("icerik","baslik",$ilan_id);
		$ilan_resim=resim_getir($resim_boyut,$ilan_id);
		$ilan_link=link_getir($ilan_id);
		if ($fiyat_goster=="Evet") {
			$fiyat=fiyat($ilan_id);
		}
            
        $il=il_getir($ilan_id);
        $il_id=bilgi("il",$ilan_id);
        $ilce=ilce_getir($ilan_id);
        $ilce_id=bilgi("ilce",$ilan_id);
        $mahalle=mahalle_getir($ilan_id);
        $mahalle_id=bilgi("mahalle",$ilan_id);
		?>
		<div class="col-md-21 col-xs-42">
			<div class="satir_link c_pointer liste yatay" data-href="<?php echo $ilan_link; ?>">
			    <div class="row">
			        <div class="col-md-12 col-xs-12" style="padding-right:3px;">
                        <img src="<?php echo $ilan_resim; ?>" alt="<?php echo $ilan_baslik; ?>" class="img-responsive"/>
                    </div>
			        <div class="col-md-30 col-xs-30" style="position:relative;height:83px;">
                        <h3><?php echo $ilan_baslik; ?></h3>
                        <div class="adres"><?php echo "$il / $ilce / $mahalle"; ?></div>
                        <?php if ($fiyat_goster=="Evet" and $fiyat and bilgi("sahiplendirme",$ilan_id)!=1 and bilgi("gorusulur",$ilan_id)!=1) {echo "<div class='fiyat_layer2 visible-md visible-lg'>$fiyat</div>";} ?>
                    </div>
			    </div>
			    <div class="clearfix"></div>
			</div>
		</div>
		<?php 
		if ($a%2==0) {echo "<div class='visible-md visible-lg clearfix'></div>";}
		
		$a++;} } else {echo "<div class='col-md-42'>" . _LANG('listing_not_found') . "</div>";} } ?>
		<div class='visible-xs clearfix'></div>
	</div>
</div>
<?php
}
?>