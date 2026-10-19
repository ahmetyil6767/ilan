<?php
include("loader.php");
$id=$_GET["id"];
$tip=bilgi_getir("kategoriler","tip",$id);
// Tema desteği açıksa temanın profil.php dosyasını include edelim.
if ($tema_destegi==1) {
	if (file_exists("$temalar_dizini/$aktif_tema/profil.php")) {
		include("$temalar_dizini/$aktif_tema/profil.php"); 
	} else {
		yonlendir(site_url());	
	}
	exit();
}
?>