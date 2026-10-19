<?php
include("loader.php");
$id=$_GET["id"];
$tip=bilgi_getir("icerik","tip",$id);
// Tema desteği açıksa temanın ilgili single dosyasını include edelim.
if ($tema_destegi==1) {
	if (file_exists("$temalar_dizini/$aktif_tema/$tip.php")) {
		include("$temalar_dizini/$aktif_tema/$tip.php"); 
	} else {
		yonlendir(site_url());
	}
	exit();
}
?>