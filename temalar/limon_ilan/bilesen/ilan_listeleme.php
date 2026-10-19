<?php 
if(count(get_included_files())==1) {die();}
global $kat_op;
$ayarlar=array(
array("tip"=>"tab_ac","id"=>"genel_ayarlar","baslik"=>"Genel Ayarlar"),
array("tip"=>"text","id"=>"adet","baslik"=>_LANG('pn_listeleme_adedi'),"standart"=>21,"aciklama"=>_LANG('ad_ilan_listeleme_adedi_21')),
array("tip"=>"text","id"=>"offset","baslik"=>_LANG('pn_haric_tutulacak_icerik'),"aciklama"=>"Listelemeden offset olarak belirlediğiniz kadar içerik hariç tutulur.","standart"=>0),
array("tip"=>"text","id"=>"link_baslik","baslik"=>_LANG('pn_baslik_yanindaki_link_metni')),
array("tip"=>"text","id"=>"link","baslik"=>_LANG('pn_baslik_yanindaki_link_hedefi')),
array("tip"=>"select","id"=>"baslik_stil","baslik"=>_LANG('pn_baslik_stili'),"secenekler"=>options("Standart,Alternatif")),
array("tip"=>"select","id"=>"listeleme_stil","baslik"=>_LANG('pn_listeleme_stili'),"secenekler"=>options("Standart,Alternatif")),
array("tip"=>"select","id"=>"fiyat_goster","baslik"=>_LANG('pn_fiyat_gosterilsin_mi'),"secenekler"=>options("Evet,Hayır"),"standart"=>"Hayır"),
array("tip"=>"select","id"=>"sutun","baslik"=>_LANG('pn_sutun_sayisi'),"aciklama"=>"Bir satırda gösterilecek ilan adedi.  Standart: 7","secenekler"=>options("2,3,4,5,6,7"),"standart"=>7),
array("tip"=>"select","id"=>"mobil_sutun","baslik"=>_LANG('pn_mobil_sutun_sayisi'),"aciklama"=>"Mobil görünümdeki sütun sayısı. Standart: 3","secenekler"=>options("2,3"),"standart"=>3),
array("tip"=>"select","id"=>"liste","baslik"=>_LANG('pn_listeleme_kriteri'),"secenekler"=>options("Yeni İçerikler:yeni|Popüler İlanlar:populer|Bugünün Popüler İlanları:bugun_populer|Anasayfa Vitrini:anasayfa_vitrin|Acil Acil:acil_acil","|",":"),"standart"=>1),
array("tip"=>"checkbox","id"=>"rastgele","baslik"=>_LANG('pn_ilanlari_rastgele_goster'),"aciklama"=>"Listelemedeki ilanları rastgele göstermenizi sağlar.","kosul"=>"liste:!=(yeni)"),
array("tip"=>"checkbox","id"=>"il_ilce","baslik"=>_LANG('pn_il_ilce_bilgilerini_goster')),
array("tip"=>"multiple_checkbox","id"=>"gosterme","baslik"=>_LANG('pn_su_ana_kategori_ilanlarini_gosterme'),"secenekler"=>$kat_op,"aciklama"=>"Seçtiğiniz kategori ilanlarını listeleden çıkarmanızı sağlar.","kosul"=>"liste:==(yeni)"),
array("tip"=>"tab_kapat"),
);

$bilesen["klasik_listeleme"]=array("id"=>"klasik_listeleme","baslik"=>_LANG('pn_klasik_ilan_listeleme'),"ayarlar"=>$ayarlar,"fonksiyon"=>"klasik_listeleme","aciklama"=>_LANG('e_klasik_listeleme_aciklama'));

function klasik_listeleme($id) {
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
	if (!$sutun) $sutun=7;
	$mobil_sutun=(int)bilesen_ayar("mobil_sutun",$id);
	if (!$mobil_sutun) $mobil_sutun=3;
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
	$il_ilce=bilesen_ayar("il_ilce",$id);
	$il_ilce_mahalle=tema_ayar("il_ilce_mahalle");
	
	// Varsayılan mobil sutun değerleri
	if (!$mobil_sutun || $mobil_sutun==3) {
		$xs_sutun=3;
		$mobil_col_yaz="col-xs-14";
	} else if ($mobil_sutun==2) {
		$xs_sutun=2;
		$mobil_col_yaz="col-xs-21";
	} else {
		$xs_sutun=3;
		$mobil_col_yaz="col-xs-14";
	}
	
	// Varsayılan sutun değerleri
	if ($sutun==2) {
		$md_sutun=2;
		$sm_sutun=2;
		$col_yaz="col-md-21 col-sm-21 $mobil_col_yaz";
		$resim_boyut="530_393";
	} else if ($sutun==3) {
		$md_sutun=3;
		$sm_sutun=3;
		$col_yaz="col-md-14 col-sm-21 $mobil_col_yaz";
		$resim_boyut="530_393";
	} else if ($sutun==4) {
		$md_sutun=4;
		$sm_sutun=3;
		$col_yaz="col-md-a4 col-sm-21 $mobil_col_yaz";
		$resim_boyut="530_393";
	} else if ($sutun==5) {
		$md_sutun=5;
		$sm_sutun=3;
		$col_yaz="col-md-a5 col-sm-14 $mobil_col_yaz";
		$resim_boyut="160_120";
	} else if ($sutun==6) {
		$md_sutun=6;
		$sm_sutun=3;
		$col_yaz="col-md-7 col-sm-14 $mobil_col_yaz";
		$resim_boyut="160_120";
	} else if ($sutun==7) {
		$md_sutun=7;
		$sm_sutun=3;
		$col_yaz="col-md-6 col-sm-14 $mobil_col_yaz";
		$resim_boyut="160_120";
	} else {
		// Varsayılan: 7 sütun
		$md_sutun=7;
		$sm_sutun=3;
		$col_yaz="col-md-6 col-sm-14 $mobil_col_yaz";
		$resim_boyut="160_120";
	}
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
		// Varsayılan: yeni ilanlar
		$sql_query = "select * from icerik where tip='ilan' and durum='1' $kat_ex_yaz order by tarih_saat desc limit $offset, $adet";
	}
	
	// Debug: SQL sorgusunu logla
	error_log("klasik_listeleme (ID: $id) SQL: $sql_query");
	
	$sql = mysqli_query($baglan, $sql_query);
	if (!$sql) {
		echo '<div class="alert alert-danger">SQL Hatası (klasik_listeleme ID: ' . $id . '): ' . mysqli_error($baglan) . '<br>Sorgu: ' . htmlspecialchars($sql_query) . '</div>';
		error_reporting($error_reporting_was);
		return;
	}
	
	while ($dizi=mysqli_fetch_array($sql)) {
		$ilan_id=$dizi["id"];
		$son_dizi[]=$ilan_id;
	}
	
	error_reporting($error_reporting_was);
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
		if (is_array($son_dizi)) {
		if (count($son_dizi)>0) {
		foreach ($son_dizi as $ilan_id) {
		$ilan_baslik=getir("icerik","baslik",$ilan_id);
		$ilan_resim=resim_getir($resim_boyut,$ilan_id);
		$ilan_link=link_getir($ilan_id);
		if ($fiyat_goster=="Evet") {
			$fiyat=fiyat($ilan_id);
		}
		?>
		<div class="<?php echo $col_yaz; ?>">
			<div class="liste <?php if ($listeleme_stil=="Alternatif") {echo "alternatif";} ?>">
				<a href="<?php echo $ilan_link; ?>" title="<?php echo $ilan_baslik; ?>" class="r_ust">
					<img src="<?php echo $ilan_resim; ?>" alt="<?php echo $ilan_baslik; ?>" class="img-responsive"/>
					<?php if ($fiyat_goster=="Evet" and $fiyat and bilgi("sahiplendirme",$ilan_id)!=1 and bilgi("gorusulur",$ilan_id)!=1) {echo "<div class='fiyat_layer visible-md visible-lg'>$fiyat</div>";} ?>
				</a>
				<h3><a href="<?php echo $ilan_link; ?>" title="<?php echo $ilan_baslik; ?>"><?php echo $ilan_baslik; ?></a></h3>
				<?php if ($il_ilce==1) { ?>
				<div class="il_ilce"><?php echo il_getir($ilan_id); ?> <?php  if ($il_ilce_mahalle!=3) { if (ilce_getir($ilan_id)) { ?>/ <?php echo ilce_getir($ilan_id); } } ?></div>
				<?php } ?>
			</div>
		</div>
		<?php 
		if ($a%$md_sutun==0) {echo "<div class='visible-md visible-lg clearfix'></div>";}
		if ($a%$sm_sutun==0) {echo "<div class='visible-sm clearfix'></div>";}
		if ($a%$xs_sutun==0) {echo "<div class='visible-xs clearfix'></div>";}
		$a++;} } else {echo "<div class='col-md-42'>İlan bulunamadı</div>";} } ?>
	</div>
</div>
<?php
}
?>