<?php 
if(count(get_included_files())==1) {die();}
global $baglan;
if (@$_POST["ara_buton"]) {search_yonlendir();}
?>
<!DOCTYPE HTML>
<html lang="<?php echo _DIL_HTML_LANG(); ?>" dir="<?php echo _DIL_HTML_YON(); ?>">
<head>
<meta charset="UTF-8">
<title><?php echo meta_title(); ?></title>
<meta name="description" content="<?php echo meta_description(); ?>">
<link rel="stylesheet" href="<?php echo tema_dizini(); ?>/css/bootstrap.min.css"/>
<link rel="stylesheet" href="<?php echo tema_dizini(); ?>/css/font-awesome.min.css"/>
<link rel="stylesheet" href="<?php echo tema_dizini(); ?>/css/ionicons.min.css"/>
<link rel="stylesheet" href="<?php echo tema_dizini(); ?>/css/lightbox.min.css"/>
<link rel="stylesheet" href="<?php echo tema_dizini(); ?>/js/trumbowyg/ui/trumbowyg.min.css"/>
<link rel="stylesheet" href="<?php echo tema_dizini(); ?>/css/sidr.bare.min.css"/>
<link rel="stylesheet" href="<?php echo tema_dizini(); ?>/style.css?v=1984"/>
<?php 
// Load RTL CSS for Arabic language
if (_DIL_RTL_MI()) {
	echo '<link rel="stylesheet" href="' . tema_dizini() . '/dil-rtl.css?v=1984"/>';
}
$site_font=tema_ayar("font"); 
if (!$site_font) {$site_font="Work Sans";}
if ($site_font=="Work Sans") { ?>
<link href="https://fonts.googleapis.com/css?family=Work+Sans:300,400,500,600,700,800&amp;subset=latin-ext" rel="stylesheet">
<?php } else if ($site_font=="Roboto") { ?>
<link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700,900&amp;subset=latin-ext" rel="stylesheet">
<?php } else if ($site_font=="Open Sans") { ?>
<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800&amp;subset=latin-ext" rel="stylesheet">
<?php } else if ($site_font=="Lato") { ?>
<link href="https://fonts.googleapis.com/css?family=Lato:300,400,700,900&amp;subset=latin-ext" rel="stylesheet">
<?php } else if ($site_font=="Montserrat") { ?>
<link href="https://fonts.googleapis.com/css?family=Montserrat:300,400,500,600,700,800&amp;subset=latin-ext" rel="stylesheet">
<?php } else if ($site_font=="Oswald") { ?>
<link href="https://fonts.googleapis.com/css?family=Oswald:300,400,500,600,700&amp;subset=latin-ext" rel="stylesheet">
<?php } else if ($site_font=="Ubuntu") { ?>
<link href="https://fonts.googleapis.com/css?family=Ubuntu:300,400,500,700&amp;subset=latin-ext" rel="stylesheet">
<?php } ?>
<?php $script_name=$_SERVER["SCRIPT_NAME"]; if (strstr($script_name,"icerik.php")) {@$id=$_GET["id"]; ?>
<link rel="canonical" href="<?php echo link_getir($id); ?>" />
<meta property="og:url" content="<?php echo link_getir($id); ?>" />
<meta property="og:type" content="website" />
<meta property="og:title" content="<?php echo meta_title(); ?>" />
<meta property="og:description" content="<?php echo meta_description(); ?>" />
<meta property="og:image" content="<?php echo thumbnail("full",$id); ?>" />
<?php } else if (strstr($script_name,"kategori.php")) {@$id=$_GET["id"]; ?>
<link rel="canonical" href="<?php echo kategori_link_getir($id); ?>"/>
<?php } else if (strstr($script_name,"index.php")) {@$magaza=$_GET["magaza"]; if (!@$magaza) { ?>
<link rel="canonical" href="<?php echo site_url(); ?>"/>
<?php } else { ?>
<link rel="canonical" href="<?php echo magaza_link_getir($magaza); ?>"/>
<?php } }
@$sayfa=$_GET["sayfa"]; if ($sayfa>1) { ?>
<meta name="robots" content="noindex">
<?php } ?>
<?php $favicon=tema_ayar("favicon"); if ($favicon) {echo "<link rel='icon' href='$favicon' />";} ?>
<?php echo tema_ayar("head_kod"); ?>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="cache-control" content="max-age=0" />
<meta http-equiv="cache-control" content="no-cache" />
<meta http-equiv="expires" content="0" />
<meta http-equiv="expires" content="Tue, 01 Jan 1980 1:00:00 GMT" />
<meta http-equiv="pragma" content="no-cache" />
<?php 
$pwa_aktif=eklenti_ayar("pwa","pwa");
if ($pwa_aktif==1) { ?>
<link rel="manifest" href="/manifest.json">
<script>navigator.serviceWorker.register('sw.js');</script>
<?php } ?>
<style type="text/css">
<?php
$ana_renk=tema_ayar("ana_renk");
$mobil_menu_bg=tema_ayar("mobil_menu_bg");
$mobil_menu_hover_bg=tema_ayar("mobil_menu_hover_bg");
$arama_formu_bg=tema_ayar("arama_formu_bg");
$arama_formu_focus_bg=tema_ayar("arama_formu_focus_bg");
$arama_formu_focus_color=tema_ayar("arama_formu_focus_color");
$arama_formu_buton_bg=tema_ayar("arama_formu_buton_bg");
$arama_formu_buton_color=tema_ayar("arama_formu_buton_color");
$arama_formu_buton_hover_bg=tema_ayar("arama_formu_buton_hover_bg");
$arama_formu_buton_hover_color=tema_ayar("arama_formu_buton_hover_color");
$ilan_ver_buton_bg=tema_ayar("ilan_ver_buton_bg");
$ilan_ver_buton_color=tema_ayar("ilan_ver_buton_color");
$ilan_ver_buton_hover_bg=tema_ayar("ilan_ver_buton_hover_bg");
$ilan_ver_buton_hover_color=tema_ayar("ilan_ver_buton_hover_color");
$header_bildirim_bg=tema_ayar("header_bildirim_bg");
$header_bildirim_color=tema_ayar("header_bildirim_color");
$header_link_color=tema_ayar("header_link_color");
$header_link_hover_color=tema_ayar("header_link_hover_color");
$header_golge=tema_ayar("header_golge");
$dropdown_border=tema_ayar("dropdown_border");
$ikon_bg=tema_ayar("ikon_bg");
$ikon_color=tema_ayar("ikon_color");
$sticky_header=tema_ayar("sticky_header");
if (!$mobil_menu_bg) {$mobil_menu_bg="#3f475f";}
if (!$mobil_menu_hover_bg) {$mobil_menu_hover_bg="#3f475f";}
?>
<?php if ($site_font=="Arial") { ?>
	body {font-family: Arial,Helvetica Neue,Helvetica,sans-serif;}
<?php } else { ?>
	body {font-family:"<?php echo $site_font; ?>", sans-serif;}
<?php } ?>
header, header .sticky, .yazi .bilgi ul li a, #magaza_bilgi, ul.magaza_sec li a:hover, ul.magaza_sec li.active a, .mobil_header {background: <?php echo $ana_renk; ?>;}
#mmu_panel, .mm#mmu_panel ul li a {background: <?php echo $mobil_menu_bg; ?> !important;}
.mm#mmu_panel ul li a:hover, .mm#mmu_panel ul li a.active {background: <?php echo $mobil_menu_hover_bg; ?> !important;}
ul.renkli_menu li a:hover  {background: <?php echo $ana_renk; ?> !important;}
.yazi h1, .yazi .sidebar .benzer h3 a, .yazi.kategori .icerik h3 a, .yazi .kategori-baslik {color: <?php echo $ana_renk; ?>;}
header .ara input {background:<?php echo $arama_formu_bg; ?>;}
header .ara.focus input {background:<?php echo $arama_formu_focus_bg; ?> !important;color:<?php if ($arama_formu_focus_color) {echo $arama_formu_focus_color;} else {echo $ana_renk;} ?> !important;}
header .ara button {background: <?php echo $arama_formu_buton_bg; ?> !important;color:<?php echo $arama_formu_buton_color; ?> !important;}
header .ara button:hover {background:<?php echo $arama_formu_buton_hover_bg; ?> !important;color:<?php echo $arama_formu_buton_hover_color; ?> !important;}
header .header_link {color:<?php if ($header_link_color) {echo $header_link_color;} else {echo "#fff";} ?> !important;}
header .header_link:hover {color:<?php echo $header_link_hover_color; ?> !important;}
header .uyelik > a.btn-primary, .fixed-ekle-buton {background: <?php echo $ilan_ver_buton_bg; ?> !important;color: <?php echo $ilan_ver_buton_color; ?> !important;}
header .uyelik > a.btn-primary:hover, .fixed-ekle-buton:hover {background: <?php echo $ilan_ver_buton_hover_bg; ?> !important;color: <?php echo $ilan_ver_buton_hover_color; ?> !important;}
header .uyelik > a span.badge {background:<?php echo $header_bildirim_bg; ?>;color:<?php echo $header_bildirim_color; ?>;}
<?php if ($header_golge==1) { ?>
header, header * {box-shadow:none !important;text-shadow:none !important;}
header {border-bottom:1px solid rgba(0,0,0,.1);}
<?php }
if ($dropdown_border==1) { ?>
ul.dropdown-menu {border:1px solid rgba(0,0,0,.07);}
<?php } ?>
.s_istatistik .badge, .slidesjs-next, .slidesjs-previous {background:<?php echo $ikon_bg; ?> !important;color:<?php echo $ikon_color; ?> !important;}
<?php 
$en_ust_bolum=tema_ayar("en_ust_bolum");
if ($en_ust_bolum==1) { ?>
header {padding-top:0;}
#en_ust {background: <?php echo tema_ayar("en_ust_bolum_bg"); ?>;}
#en_ust li a {color: <?php echo tema_ayar("en_ust_link"); ?>;}
<?php } ?>
<?php if ($sticky_header==1) {echo "header {padding:0;}";} ?>

<?php if (tema_ayar("border_radius")==1) { ?>
	body * {border-radius:0 !important;}
<?php } ?>
<?php if (tema_ayar("tab_tasarimi")==2) { ?>
.nav-tabs.ozel {border-bottom:4px solid <?php echo tema_ayar("aktif_tab_bg"); ?> !important;height:40px;}
.nav-tabs.ozel li a {margin-left:0;margin-right:5px;padding:8px 14px;border:0;border-radius:0;text-shadow:none;background:<?php echo tema_ayar("tab_bg"); ?>;color:<?php echo tema_ayar("tab_color"); ?>;}
.nav-tabs.ozel li.active a {background:<?php echo tema_ayar("aktif_tab_bg"); ?>;color:<?php echo tema_ayar("aktif_tab_color"); ?>;border:0;border-radius:0;}
.nav-tabs.ozel li.active a:before {display:none;}
<?php } ?>
<?php echo tema_ayar("ek_css"); ?>
</style>
</head>
<body>
<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = 'https://connect.facebook.net/tr_TR/sdk.js#xfbml=1&version=v3.1';
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
<?php 
// Header şablonu
$site_header=tema_ayar("header");
if (!$site_header) {
	$site_header="standart";
}
?>
<header class="visible-md visible-lg <?php echo $site_header; ?>">
<?php if (tema_ayar("en_ust_bolum")==1) { ?>
<div id="en_ust">
	<div class="container">
		<ul class="ust_sol pull-left">
		<?php menu_getir("ust_sol_menu",0,0); ?>
		</ul>
		<ul class="ust_sol pull-right">
		<?php menu_getir("ust_sag_menu",0,0); ?>
		</ul>
	</div>
</div>
<?php } ?>
<?php if ($sticky_header==1) { ?><div class="sticky"><?php } ?>
<div class="container">
	<div class="row">
		<div class="col-md-9 col-sm-12 yukseklik_h">
			<div class="logo noselect"><a href="<?php echo site_url(); ?>"><img src="<?php echo tema_ayar("logo"); ?>" class="img-responsive" /></a></div>
		</div>
		<div class="ortala_h">
			<div class="col-md-14 col-md-offset-2 visible-md visible-lg">
				<form action="" method="post">
					<div class="input-group ara <?php if (@$_GET["s"]) {echo "focus";} ?>">
					  <input type="text" id="ara_input" class="form-control" name="s" placeholder="<?php echo _LANG('search_placeholder'); ?>" value="<?php if (!strstr(browser_url(),"siparis")) {echo @$_GET["s"];} ?>" required>
					  <span class="input-group-btn">
						<button class="btn btn-default" name="ara_buton" type="submit" value="Ara"><i class="ion-search"></i></button>
					  </span>
					</div>
				</form>
			</div>
			<div class="col-md-17 col-sm-30 text-right uyelik">
				<?php 
				global $baglan;
				@$oturum_id=$_SESSION["oturum_id"];
				if ($oturum_id) { 
				$ad_soyad=getir("uyeler","ad_soyad",$oturum_id);
				$uye_resim=uye_bilgi("profil_gorseli",$oturum_id);
				$kucuk_resim=thumbnail_url("50_50",$uye_resim);
				$profil_url=profil_link_getir($oturum_id);
				$get_satis=mysqli_num_rows(mysqli_query($baglan,"select id from siparisler where tip='get' and satici_id='$oturum_id'"));
				?>
				<div class="btn-group">
				<a class="btn btn-xs dropdown-toggle header_link" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="font-size:14px;"><?php if ($uye_resim) { ?><img src="<?php echo $kucuk_resim; ?>" alt="<?php echo $ad_soyad; ?>" class="img-circle beyaz-shadow" style="width:32px;height:auto;margin:0 8px 0 0;"/><?php } ?><?php echo $ad_soyad; ?><span class="caret"></span></a>

			  <ul class="dropdown-menu">
				<li><a href="<?php echo $profil_url; ?>"><i class="ion-person"></i><?php echo _LANG('nav_my_profile'); ?></a></li>
				<li><a href="<?php echo ozel_url("mesajlarim"); ?>"><i class="ion-paper-airplane"></i><?php echo _LANG("nav_my_messages"); ?></a></li>
				<li><a href="<?php echo ozel_url("favori-ilanlarim"); ?>"><i class="ion-android-favorite"></i><?php echo _LANG("nav_my_favorites"); ?></a></li>
				<li role="separator" class="divider"></li>
				<li><a href="<?php echo ozel_url("ilanlarim"); ?>"><i class="ion-compose"></i><?php echo _LANG("nav_my_ads"); ?></a></li>
				<li><a href="<?php echo ozel_url("magazalarim"); ?>"><i class="ion-bag"></i><?php echo _LANG("nav_my_stores"); ?></a></li>
				<li><a href="<?php echo ozel_url("magazalarim"); ?>?islem=yeni"><i class="ion-plus"></i><?php echo _LANG("nav_create_store"); ?></a></li>
				<li><a href="<?php echo ozel_url("siparislerim"); ?>"><i class="ion-cash"></i><?php echo _LANG("nav_my_orders"); ?></a></li>
				<?php if ($get_satis>0) { ?><li><a href="<?php echo ozel_url("get-satislarim"); ?>"><i class="ion-android-cart"></i><?php echo _LANG("nav_sales"); ?></a></li><?php } ?>
				<li role="separator" class="divider"></li>
				<li><a href="<?php echo ozel_url("profil-guncelle"); ?>"><i class="ion-person"></i><?php echo _LANG("nav_update_profile"); ?></a></li>
				<li role="separator" class="divider"></li>
				<li><a href="<?php echo ozel_url("cikis-yap"); ?>"><i class="ion-power"></i><?php echo _LANG("nav_logout"); ?></a></li>
			  </ul>
			</div>
				<?php
				$okunmamis_mesaj_sql=mysqli_query($baglan,"select * from mesajlar where kime='$oturum_id' and okundu='0'");
				$okunmamis_mesaj_sayisi=mysqli_num_rows($okunmamis_mesaj_sql);	
				?>	
				<a href="<?php echo ozel_url("mesajlarim"); ?>" class="btn btn-xs yeni_mesaj header_link"><i class="ion-chatbox" style="font-size:20px;"></i><?php if ($okunmamis_mesaj_sayisi>0) { ?><span class="badge"><?php echo $okunmamis_mesaj_sayisi; ?></span><?php } ?></a>	
				<?php
				$okunmamis_bildirim_sql=mysqli_query($baglan,"select * from bildirimler where uye_id='$oturum_id' and okundu='0' and tip='1'");
				$okunmamis_bildirim_sayisi=mysqli_num_rows($okunmamis_bildirim_sql);	 
				?>
				<a href="<?php echo ozel_url("bildirimlerim"); ?>" class="btn btn-xs yeni_mesaj header_link"><i class="ion-ios-bell" style="font-size:20px;"></i><?php if ($okunmamis_bildirim_sayisi>0) { ?><span class="badge"><?php echo $okunmamis_bildirim_sayisi; ?></span><?php } ?></a>			
				<a href="<?php echo ozel_url("ilan-ekle"); ?>" class="btn btn-primary btn-header"><?php echo _LANG("nav_free_post"); ?></a>			
				<?php } else { ?>
				<a href="<?php echo ozel_url("giris-yap"); ?>" class="btn header_link"><?php echo _LANG("nav_login"); ?></a>
				<a href="<?php echo ozel_url("kayit-ol"); ?>" class="btn header_link"><?php echo _LANG("nav_register"); ?></a>
				<a href="<?php echo ozel_url("giris-yap")."?r=ie"; ?>" class="btn btn-primary btn-header"><?php echo _LANG("nav_free_post"); ?></a>
				<?php } ?>	
				<a data-toggle="modal" data-target="#ara_modal" id="sm_ara_buton" class="btn btn-danger btn-header"><i class="ion-search mr0"></i></a>
<?php include_once('bilesen/ust-bar-araclar.php'); ?>
			</div>
		</div>
	</div>
</div>
<?php if ($sticky_header==1) { ?></div><?php } ?>
</header>
<div class="visible-sm visible-xs">
	<div class="mobil_header">
		<div class="container">
			<div class="row">
				<?php $mbmt=tema_ayar("mobil_buton_mt"); ?>
				<div class="col-xs-8 text-center" <?php if ($mbmt) {echo "style='margin-top:".$mbmt."px';";} ?>>
					<button type="button" class="pull-left" id="mm_buton" href="#mm_panel"><i class="ion-navicon-round mr0"></i></button>
				</div>
				<div class="col-xs-26 yukseklik_h">
					<a href="<?php echo site_url(); ?>"><img src="<?php echo tema_ayar("logo"); ?>" class="img-responsive img-center mobil_logo"/></a>
				</div>
				<div class="col-xs-8 text-center" <?php if ($mbmt) {echo "style='margin-top:".$mbmt."px';";} ?>>
					<button type="button" class="pull-right" id="mmu_buton" href="#mmu_panel"><i class="ion-person-stalker mr0"></i></button>
				</div>
			</div>
		</div>
	</div>
	<form action="" method="post">
<?php include_once('bilesen/ust-bar-araclar.php'); ?>
		<div class="mobil_ara">
		  <input style="width:88%;border-left:0;" type="text" id="m_ara_input" class="form-control pull-left" name="s" placeholder="<?php echo _LANG('search_placeholder'); ?>" value="<?php if (!strstr(browser_url(),"siparis")) {echo @$_GET["s"];} ?>" required>
		  <button style="width:12%;" class="btn btn-default pull-left" name="ara_buton" type="submit" value="Ara"><i class="ion-search mr0"></i></button>
		  <div class="clearfix"></div>
		</div>
	</form>
</div>