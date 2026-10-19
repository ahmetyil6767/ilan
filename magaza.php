<?php
include("loader.php");
@$id=$_GET["id"];
@$url=$_GET["url"];
if (!$id) {
	$id=id_getir("magazalar","subdomain",$url);
}
// Tema desteği açıksa temanın magaza dosyasını include edelim.
if ($tema_destegi==1) {include("$temalar_dizini/$aktif_tema/magaza.php"); exit();}