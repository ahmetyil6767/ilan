<?php if(count(get_included_files())==1) {die();} ?>
<div class="ust">
    <ul class="nav navbar-nav navbar-left visible-sm visible-md visible-lg">
	  <li id="menu-toggle-hide"><a id="menu_kapat" href="#" class="mr15"><i class="fa fa-chevron-circle-left" aria-hidden="true"></i> Menüyü gizle</a></li>
	  <li id="menu-toggle-show"><a id="menu_ac" href="#" class="mr15"><i class="fa fa-chevron-circle-right" aria-hidden="true"></i> Menüyü göster</a></li>
    </ul>
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
	  <a href="#menu" class="mobil_mmenu_open visible-xs"><i class="fa fa-bars mr0" aria-hidden="true"></i></a>
      <a class="navbar-brand" href="index.php"><?php echo is_array($lpanel_ust_baslik) ? implode(", ", $lpanel_ust_baslik) : $lpanel_ust_baslik; ?></a>
    </div>
    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse visible-sm visible-md visible-lg" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav navbar-right">
		<li><a href="<?php echo ayar_getir("site_url"); ?>" class="mr15"><?php echo _LANG("listing_view_site"); ?></a></li>
		<li class="visible-md visible-lg"><a href="uyeler.php?islem=guncelle&id=<?php echo is_array($oturum_id) ? implode(", ", $oturum_id) : $oturum_id; ?>" class="mr15 xsmr0"><?php echo _LANG("nav_update_profile"); ?></a></li>
		<li><a href="<?php echo ayar_getir("site_url"); ?>/<?php echo is_array($admin_dizini) ? implode(", ", $admin_dizini) : $admin_dizini; ?>/index.php?logout=1" class="mr0">Çıkış Yap [<?php echo is_array($oturum_ad_soyad) ? implode(", ", $oturum_ad_soyad) : $oturum_ad_soyad; ?>]</a></li>
      </ul>
    </div><!-- /.navbar-collapse -->
</div>
<?php jaayquick(@$panel_header_alt_kod); ?>
<?php
if (isset($_SESSION["mesaj"]) and is_array($_SESSION["mesaj"]) and isset($_SESSION["mesaj"][0])) {
$mesaj_kaynak = $_SESSION["mesaj"][0];
$mesaj_mesaj = is_array($mesaj_kaynak) ? implode(" ", $mesaj_kaynak) : stripslashes((string)$mesaj_kaynak);
$mesaj_bicim_kaynak=isset($_SESSION["mesaj"][1])?$_SESSION["mesaj"][1]:"";
$mesaj_bicim=is_array($mesaj_bicim_kaynak)?"":$mesaj_bicim_kaynak;
echo "<div class='alert alert-$mesaj_bicim autoclose' style='margin:20px;margin-bottom:0;'>$mesaj_mesaj
<button type='button' class='close' data-dismiss='alert' aria-label='Kapat'>
  <span aria-hidden='true' style='color:#fff !important;' class='glyphicon glyphicon-ok'></span>
</button>
</div>";
$_SESSION["mesaj"]="";
}
?>