<?php
if(count(get_included_files())==1) {die();}
$jaayquickpanel_menu[]=array("baslik"=>_LANG('tx_site_tasima_araci'),"url"=>"eklentiler.php?eklenti=site_tasima_araci&sayfa=islem","ikon"=>"refresh","sira"=>1000);


function site_tasima_araci_gorsel() {
	
	global $baglan;
	$site_url=site_url();
	$eb_sql=mysqli_query($baglan,"select * from ekstra_bilgi where deger like '%uploads/%'");
	while ($eb_dizi=mysqli_fetch_array($eb_sql)) {
		$b_id=$eb_dizi["b_id"];
		$b_deger=$eb_dizi["deger"];
		$parcala=explode("uploads/",$b_deger);
		$onceki_yol=$parcala[0];
		$onceki_yol_son=$parcala[1];
		$yeni_yol="$site_url/uploads/$onceki_yol_son";
		$guncelle=mysqli_query($baglan,"update ekstra_bilgi set deger='$yeni_yol' where b_id='$b_id'");
	}
	$ayar_sql=mysqli_query($baglan,"select * from ayarlar where deger like '%uploads/%'");
	while ($ayar_dizi=mysqli_fetch_array($ayar_sql)) {
		$anahtar=$ayar_dizi["anahtar"];
		$deger=$ayar_dizi["deger"];
		$parcala=explode("uploads/",$deger);
		$onceki_yol=$parcala[0];
		$onceki_yol_son=$parcala[1];
		$yeni_yol="$site_url/uploads/$onceki_yol_son";
		ayar_guncelle($anahtar,$yeni_yol);
	}
	$galeri_sql=mysqli_query($baglan,"select * from galeri");
	while ($galeri_dizi=mysqli_fetch_array($galeri_sql)) {
		$g_id=$galeri_dizi["id"];
		$g_resim=$galeri_dizi["resim"];
		$parcala=explode("uploads/",$g_resim);
		$onceki_yol=$parcala[0];
		$onceki_yol_son=$parcala[1];
		$yeni_yol="$site_url/uploads/$onceki_yol_son";
		$guncelle=mysqli_query($baglan,"update galeri set resim='$yeni_yol' where id='$g_id'");
	}
	
	
}


function site_tasima_array_temizle($array) {
	foreach($array as $a => $veri) { 
		if($veri === '') {
			unset($array[$a]); 
		}
	}
	return $array;
}

?>