<?php
include("loader.php");
@$s=$_GET["s"];
// Tema desteği açıksa temanın search ara.php include edelim.
if ($tema_destegi==1) {include("$temalar_dizini/$aktif_tema/ara.php"); exit();}
?>