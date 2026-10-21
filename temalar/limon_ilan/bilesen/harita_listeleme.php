<?php 
if(count(get_included_files())==1) {die();}
// Harita Listeleme

$iller_sql=mysqli_query($baglan,"select * from iller");
$iller[]=array("deger"=>0,"baslik"=>_LANG('pn_tumu'));
while ($iller_dizi=mysqli_fetch_array($iller_sql)) {
	$il_id=$iller_dizi["id"];
	$il_adi=$iller_dizi["il_adi"];
	$iller[]=array("deger"=>$il_id,"baslik"=>$il_adi);
}

$kat_op[]=array("baslik"=>_LANG('pn_tum_kategoriler'),"deger"=>"tumu");

$harita_listeleme_ayar=array(
array("tip"=>"text","id"=>"yukseklik","baslik"=>_LANG('pn_harita_yuksekligi_px'),"standart"=>350),
array("tip"=>"select","id"=>"kategori","baslik"=>_LANG('pn_su_ilanlari_goster'),"secenekler"=>$kat_op,"aciklama"=>"Seçtiğiniz kategorideki ilanları listeler."),
array("tip"=>"select","id"=>"il","baslik"=>_LANG('pn_su_ildeki_ilanlari_listele'),"secenekler"=>$iller,"aciklama"=>"Seçtiğiniz ildeki ilanları listeler."),
array("tip"=>"text","id"=>"limit","baslik"=>_LANG('pn_harita_liste_limit'),"aciklama"=>_LANG('ad_harita_liste_limit')),
array("tip"=>"text","id"=>"baslangic_konumu","baslik"=>_LANG('pn_baslangic_konumu'),"standart"=>"39.181472, 34.434861","aciklama"=>"Google maps koordinatı. Bir noktaya sağ tıklayıp 'Burası neresi?' linkine basarak koordinatlarını alabilirsiniz."),
array("tip"=>"text","id"=>"harita_zoom","baslik"=>_LANG('pn_harita_zoom'),"standart"=>"10"),
);
$bilesen["harita_listeleme"]=array("id"=>"harita_listeleme","baslik"=>_LANG('pn_harita_listeleme'),"ayarlar"=>$harita_listeleme_ayar,"fonksiyon"=>"harita_listeleme","aciklama"=>_LANG('e_harita_listeleme_aciklama'));
function harita_listeleme($id) {
	global $admin_dizini;
	global $baglan;
	global $footer_kod;
	
	// Debug: Hata raporlamayı aç
	$error_reporting_was = error_reporting();
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	
	$listeleme_adedi=bilesen_ayar("listeleme_adedi",$id);
	$harita_yukseklik=bilesen_ayar("yukseklik",$id);
	$baslangic_konumu=bilesen_ayar("baslangic_konumu",$id);
	$baslangic_konumu_duzelt=str_replace(array(" "),array(null),$baslangic_konumu);
	$il=bilesen_ayar("il",$id);
	$kategori=bilesen_ayar("kategori",$id);
	$baslik=getir("bilesenler","baslik",$id);
	$baslangic_konumu_parcala=explode(",",$baslangic_konumu_duzelt);
	$baslangic_lat=$baslangic_konumu_parcala[0];
	$baslangic_lon=$baslangic_konumu_parcala[1];
	$harita_zoom=bilesen_ayar("harita_zoom",$id);
	if (!$harita_yukseklik) {
		$harita_yukseklik=350;
	}
	if ($il) {
		$il_sql="INNER JOIN ekstra_bilgi as il ON i.id=il.id and il.anahtar='il' and il.deger='$il'";
	}
	$markerkod = "";
	if ($kategori=="tumu") {
		$sql_query = "SELECT i.id, i.baslik, i.durum, i.tarih_saat, i.yazar, ef.deger from icerik as i 
											INNER JOIN ekstra_bilgi as ef ON i.id=ef.id and ef.anahtar='koordinat' and ef.deger!=''
											$il_sql
											WHERE i.tip='ilan' and i.durum='1' limit 100";
	} else {
		$sql_query = "SELECT i.id, i.baslik, i.durum, i.tarih_saat, i.yazar, ef.deger from icerik as i 
											INNER JOIN ekstra_bilgi as ef ON i.id=ef.id and ef.anahtar='koordinat' and ef.deger!=''
											$il_sql
											WHERE i.tip='ilan' and i.durum='1' and i.kategori like '%($kategori)%' limit 100";
	}
	
	error_log("harita_listeleme (ID: $id) SQL: $sql_query");
	
	$ilan_sql = mysqli_query($baglan, $sql_query);
	if (!$ilan_sql) {
		echo '<div class="alert alert-danger">SQL Hatası (harita_listeleme ID: ' . $id . '): ' . mysqli_error($baglan) . '<br>Sorgu: ' . htmlspecialchars($sql_query) . '</div>';
		error_reporting($error_reporting_was);
		return;
	}
	
	while ($dizi=mysqli_fetch_array($ilan_sql)) {
		$ilan_id=$dizi["id"];
		$ilan_baslik=addslashes($dizi["baslik"]);
		$ilan_koordinat=bilgi("koordinat",$ilan_id);
		$ilan_resim=$gorsel_thumb=resim_getir("160_120",$ilan_id);
		$ilan_fiyat=fiyat_donustur(bilgi("fiyat",$ilan_id));
		$ilan_fiyat_birim=bilgi("para_birimi",$ilan_id);
		$ilan_link=link_getir($ilan_id);
		if ($ilan_fiyat_birim=="gmd") {
			$para_birimi="GMD";
		} else if ($ilan_fiyat_birim=="usd") {
			$para_birimi="USD";
		} else if ($ilan_fiyat_birim=="eur") {
			$para_birimi="EUR";
		}
		$koordinat_parca = koordinat_ayir($ilan_koordinat);
		if ($koordinat_parca) {$k_lat=$koordinat_parca['lat']; $k_lon=$koordinat_parca['lon'];
		$markerkod.="
		var marker_$ilan_id=new google.maps.Marker({
			position:new google.maps.LatLng($k_lat,$k_lon),
			title: '$ilan_baslik',
		});
		marker_$ilan_id.setMap(map_$id);
		var contentString_$ilan_id ='<a href=".$ilan_link."><img src=".$ilan_resim." /></a><h5><a href=".$ilan_link.">".$ilan_baslik."</a></h5><h6 style=\"font-weight:700;font-size:14px;color:#e53935;\">".$ilan_fiyat." ".$para_birimi."</h6>';
		var infowindow_$ilan_id = new google.maps.InfoWindow({
			content: contentString_$ilan_id,
			maxWidth: 160,
		});	
		marker_$ilan_id.addListener('click', function() {
			infowindow_$ilan_id.open(map_$id, marker_$ilan_id);
			setTimeout(function () {infowindow_$ilan_id.close();}, 5000);
		});			
		";
		}
	}
	
	error_reporting($error_reporting_was);
	?>
	<div class="bilesen">
		<?php if ($baslik) { ?>
		<div class="baslik">
		<?php echo $baslik; ?>
		<?php if ($link_baslik) { ?><a href="<?php echo $link; ?>" class="pull-right visible-sm visible-md visible-lg"><?php echo $link_baslik; ?></a><?php } ?>
		</div>
		<?php } ?>
		<div id="harita_listeleme_<?php echo $id; ?>" style="width:100%;height:<?php echo $harita_yukseklik; ?>px;margin-bottom:20px;"></div>
	</div>
	<?php 
	$site_url=site_url();
	$google_maps_api_key=tema_ayar("google_maps_api_key");
	if ($baslangic_lat and $baslangic_lon) {
		$harita_listeleme_js='
		<script type="text/javascript" src="https://maps.google.com/maps/api/js?libraries=places&key='.$google_maps_api_key.'"></script>
		<script type="text/javascript" src="'.$site_url.'/'.$admin_dizini.'/js/locationpicker.jquery.min.js"></script>	
		<script type="text/javascript">
		// Google Haritalar Konumu
		var myCenter_'.$id.'=new google.maps.LatLng('.$baslangic_lat.', '.$baslangic_lon.');
		var mapProp_'.$id.' = {
		  center:myCenter_'.$id.',
		  zoom: '.$harita_zoom.',
		  draggable: true,
		  scrollwheel: true,
		  mapTypeId:google.maps.MapTypeId.ROADMAP
		};
		var map_'.$id.'=new google.maps.Map(document.getElementById("harita_listeleme_'.$id.'"),mapProp_'.$id.');
		'.$markerkod.'
		</script>	
		';
		$footer_kod[]=$harita_listeleme_js;
	}
}
?>