<?php
if(count(get_included_files())==1) {die();}
$jaayquickpanel_menu[]=array("baslik"=>_LANG('tx_yapim_asamasinda'),"url"=>"eklentiler.php?eklenti=hazirlaniyor&islem=ayar","ikon"=>"lock","sira"=>51);
function yapim_asamasinda() {
	global $admin_dizini;
	if (!$_SESSION["yonetici_id"] and !strstr(browser_url(), "/$admin_dizini/")) {
		include("hazirlaniyor.php");
		die();
	}
}
yapim_asamasinda();
?>