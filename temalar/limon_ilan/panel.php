<?php
if(count(get_included_files())==1) {die();}
$islem=$_GET["islem"];
if ($islem=="magazalar") {
	include("panel/magazalar.php");
} else if ($islem=="magaza-ekle") {
	include("panel/magaza-ekle.php");
} else if ($islem=="magaza-guncelle") {
	include("panel/magaza-guncelle.php");
} else if ($islem=="odemeler") {
	include("panel/odemeler.php");
} else if ($islem=="dopingler") {
	include("panel/dopingler.php");
} else if ($islem=="siparisler") {
	include("panel/siparisler.php");
} else if ($islem=="il_ilce_mahalle") {
	include("panel/il_ilce_mahalle.php");
} else if ($islem=="iller") {
	include("panel/iller.php");
} else if ($islem=="ilceler") {
	include("panel/ilceler.php");
} else if ($islem=="mahalleler") {
	include("panel/mahalleler.php");
} else if ($islem=="paket-kontrol") {
	include("panel/paket-kontrol.php");
} else if ($islem=="iliskilendirilmemis-gorselleri-sil") {
	include("panel/iliskilendirilmemis-gorselleri-sil.php");
} else if ($islem=="reddet") {
	include("panel/reddet.php");
} else if ($islem=="uye-bilgileri") {
	include("panel/uye-bilgileri.php");
} else if ($islem=="cache") {
	include("panel/cache.php");
} else if ($islem=="magazaya-aktar") {
	include("panel/magazaya-aktar.php");
} else if ($islem=="azalan-limitlere-gecis") {
	include("panel/azalan_limitlere_gecis.php");
} else if ($islem=="kategoriye-genel-bakis") {
	include("panel/kategoriye-genel-bakis.php");
} else if ($islem=="ilan-iframe") {
	include("panel/ilan-iframe.php");
} else if ($islem=="toplu-ilan-tarihi-guncelleme") {
	include("panel/toplu-ilan-tarihi-guncelleme.php");
}
?>