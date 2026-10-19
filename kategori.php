<?php
include("loader.php");
$id=$_GET["id"];
$tip=bilgi_getir("kategoriler","tip",$id);
$sinif=bilgi_getir("kategoriler","sinif",$id);
if (!$sinif) {
	$sinif="kategori";
}
// Tema desteği açıksa temanın ilgili kategori dosyasını include edelim.
if ($tema_destegi==1) {
	if (file_exists("$temalar_dizini/$aktif_tema/$sinif-$id.php")) {
		include("$temalar_dizini/$aktif_tema/$sinif-$id.php"); 
	} else {
		if (file_exists("$temalar_dizini/$aktif_tema/$tip-$sinif.php")) {
			include("$temalar_dizini/$aktif_tema/$tip-$sinif.php"); 
		} else if (file_exists("$temalar_dizini/$aktif_tema/$sinif-$tip.php")) {
			include("$temalar_dizini/$aktif_tema/$sinif-$tip.php"); 
		} else if (file_exists("$temalar_dizini/$aktif_tema/$sinif.php")) {
			include("$temalar_dizini/$aktif_tema/$sinif.php"); 
		} else {
			yonlendir(site_url());
		}		
	}
	exit();
}
?>