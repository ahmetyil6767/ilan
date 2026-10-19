<?php
include("loader.php");
// Tema desteği açıksa temanın 404 dosyasını include edelim.
if ($tema_destegi==1) {
	if (file_exists("$temalar_dizini/$aktif_tema/404.php")) {
		include("$temalar_dizini/$aktif_tema/404.php");
	} else {
		yonlendir(site_url());
	}
	exit();
}
?>