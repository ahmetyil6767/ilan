<?php 
if(count(get_included_files())==1) {die();}
$sablon=bilgi("sablon");
$menu=bilgi("menu");
if ($sablon==1 or !$sablon) {
	include("sablon/sayfa_standart.php");
} else if ($sablon==2) {
	include("sablon/sayfa_fullwidth.php");
} else if ($sablon==2) {
	include("sablon/sayfa_iletisim.php");
}
?>