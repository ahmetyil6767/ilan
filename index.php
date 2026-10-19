<?php
include("loader.php");
// Tema desteği açıksa temanın index dosyasını include edelim.
if ($tema_destegi==1) {include("$temalar_dizini/$aktif_tema/index.php"); exit();}
?>