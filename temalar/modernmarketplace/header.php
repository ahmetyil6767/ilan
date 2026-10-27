<?php 
if(count(get_included_files())==1) {die();}
global $baglan;
if (@$_POST["ara_buton"]) {search_yonlendir();}
require_once ROOT_DIR . '/config/seo.php';
?>
<!DOCTYPE HTML>
<html lang="<?php echo _DIL_HTML_LANG(); ?>" dir="<?php echo _DIL_HTML_YON(); ?>">
<head>
<meta charset="UTF-8">
<title><?php echo meta_title(); ?></title>
<meta name="description" content="<?php echo meta_description(); ?>">
<meta name="google-site-verification" content="google673bba54dc6bca9d" />
<meta name="msvalidate.01" content="ae87be9ee765c8a2" />
<link rel="stylesheet" href="<?php echo tema_dizini(); ?>/css/bootstrap.min.css"/>
<link rel="stylesheet" href="<?php echo tema_dizini(); ?>/css/font-awesome.min.css"/>
<link rel="stylesheet" href="<?php echo tema_dizini(); ?>/css/ionicons.min.css"/>
<link rel="stylesheet" href="<?php echo tema_dizini(); ?>/css/lightbox.min.css"/>
<link rel="stylesheet" href="<?php echo tema_dizini(); ?>/js/trumbowyg/ui/trumbowyg.min.css"/>
<link rel="stylesheet" href="<?php echo tema_dizini(); ?>/css/sidr.bare.min.css"/>
<link rel="stylesheet" href="<?php echo tema_dizini(); ?>/style.css?v=20261036"/>
<link rel="stylesheet" href="<?php echo tema_dizini(); ?>/css/theme-modern.css?v=20261027g"/>
<?php 
// Load RTL CSS for Arabic language
if (_DIL_RTL_MI()) {
	echo '<link rel="stylesheet" href="' . tema_dizini() . '/dil-rtl.css?v=20261024"/>';
	echo '<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Arabic:wght@400;500;600;700&display=swap" rel="stylesheet">';
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
<?php } else if ($site_font=="Poppins") { ?>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<?php } ?>
<?php $script_name=$_SERVER["SCRIPT_NAME"]; $og_url=site_url() . (isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : ''); $og_image=""; ?>
<meta property="og:type" content="website" />
<meta property="og:site_name" content="<?php echo ayar_getir('site_baslik'); ?>" />
<meta property="og:locale" content="<?php echo _DIL_HTML_LANG(); ?>" />
<meta property="og:title" content="<?php echo meta_title(); ?>" />
<meta property="og:description" content="<?php echo meta_description(); ?>" />
<?php if (strstr($script_name,"icerik.php")) {@$id=$_GET["id"]; $og_url=link_getir($id); $og_image=thumbnail("full",$id); ?>
<link rel="canonical" href="<?php echo link_getir($id); ?>" />
<?php } else if (strstr($script_name,"kategori.php")) {@$id=$_GET["id"]; $og_url=kategori_link_getir($id); ?>
<link rel="canonical" href="<?php echo kategori_link_getir($id); ?>"/>
<?php } else if (strstr($script_name,"index.php")) {@$magaza=$_GET["magaza"]; if (!@$magaza) { $og_url=site_url(); ?>
<link rel="canonical" href="<?php echo site_url(); ?>"/>
<?php } else { $og_url=magaza_link_getir($magaza); ?>
<link rel="canonical" href="<?php echo magaza_link_getir($magaza); ?>"/>
<?php } } ?>
<meta property="og:url" content="<?php echo $og_url; ?>" />
<?php if (!$og_image) { $og_image=tema_ayar("og_image"); if ($og_image) { $og_image=site_url().$og_image; } } ?>
<meta property="og:image" content="<?php echo $og_image ? e($og_image) : ""; ?>" />
<meta property="og:image:width" content="1200" />
<meta property="og:image:height" content="630" />
<meta name="twitter:card" content="summary_large_image" />
<meta name="twitter:title" content="<?php echo meta_title(); ?>" />
<meta name="twitter:description" content="<?php echo meta_description(); ?>" />
<?php echo seo_hreflang_tags(); ?>
<?php $sayfa=@$_GET["sayfa"]; if ($sayfa>1) { ?>
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
<?php echo seo_json_ld(); ?>
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
	body {font-family:"<?php echo $site_font; ?>", <?php echo _DIL_RTL_MI() ? '"Noto Sans Arabic", ' : ''; ?>sans-serif;}
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
<body class="mk-theme">
<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = 'https://connect.facebook.net/tr_TR/sdk.js#xfbml=1&version=v3.1';
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
<?php 
// Header ┼şablonu
$site_header=tema_ayar("header");
if (!$site_header) {
	$site_header="standart";
}
?>
<div class="mk-topbar visible-md visible-lg">
	<div class="container mk-topbar-inner">
		<div class="mk-topbar-left">
			<?php include_once('bilesen/ust-bar-araclar.php'); ?>
		</div>
		<div class="mk-topbar-right">
			<a href="mailto:info@jaayquick.com" class="mk-top-link"><i class="ion-help-buoy"></i> <?php echo _LANG('mk_help_support'); ?></a>
			<span class="mk-top-sep"></span>
			<?php $oturum_id = isset($_SESSION["oturum_id"]) ? $_SESSION["oturum_id"] : null; if ($oturum_id) { ?>
			<a href="<?php echo profil_link_getir($oturum_id); ?>" class="mk-top-link"><i class="ion-person"></i> <?php echo htmlspecialchars(getir("uyeler","ad_soyad",$oturum_id),ENT_QUOTES,'UTF-8'); ?></a>
			<a href="<?php echo ozel_url("cikis-yap"); ?>" class="mk-top-link" title="<?php echo _LANG("nav_logout"); ?>"><i class="ion-power"></i></a>
			<?php } else { ?>
			<a href="<?php echo ozel_url("giris-yap"); ?>" class="mk-top-link"><?php echo _LANG("nav_login"); ?> / <?php echo _LANG("nav_register"); ?></a>
			<?php } ?>
			<a href="<?php echo ozel_url("ilan-ekle"); ?>" class="mk-post-btn"><?php echo _LANG("nav_free_post"); ?></a>
		</div>
	</div>
</div>
<header class="visible-md visible-lg mk-header">
	<div class="container mk-header-inner">
		<div class="mk-logo">
			<a href="<?php echo site_url(); ?>">
				<?php
				$mk_logo = tema_ayar("logo_dark");
				if (!$mk_logo) { $mk_logo = "/uploads/images/jaayquick-logo-dark.png"; }
				?>
				<img src="<?php echo $mk_logo; ?>" alt="JaayQuick">
				<span class="mk-logo-tag"><?php echo _LANG('mk_logo_tagline'); ?></span>
			</a>
		</div>
		<nav class="mk-nav">
			<a href="<?php echo site_url(); ?>" class="mk-nav-link<?php echo (browser_url()==site_url() ? ' active' : ''); ?>"><i class="ion-home"></i> <?php echo _LANG('mk_nav_home'); ?></a>
			<div class="dropdown mk-nav-dropdown">
				<a href="#" class="mk-nav-link dropdown-toggle" data-toggle="dropdown"><i class="ion-grid"></i> <?php echo _LANG('mk_nav_categories'); ?> <i class="ion-chevron-down mk-caret"></i></a>
				<ul class="dropdown-menu mk-cat-dropdown">
					<?php
					$mk_nav_kat = mysqli_query($baglan,"select id,baslik from kategoriler where tip='ilan' and ust_kategori='0' order by icerik desc, baslik asc limit 12");
					while ($mk_nk = mysqli_fetch_array($mk_nav_kat)) {
						$mk_nk_id = $mk_nk["id"];
						$mk_nk_baslik = _DIL_BASLIK($mk_nk["baslik"], $mk_nk_id, "kategori");
						echo '<li><a href="'.kategori_link_getir($mk_nk_id).'">'.htmlspecialchars($mk_nk_baslik,ENT_QUOTES,'UTF-8').'</a></li>';
					}
					?>
				</ul>
			</div>
			<a href="<?php echo ozel_url("ilanlarim"); ?>" class="mk-nav-link"><i class="ion-compose"></i> <?php echo _LANG("nav_my_ads"); ?></a>
			<a href="<?php echo ozel_url("mesajlarim"); ?>" class="mk-nav-link"><i class="ion-chatboxes"></i> <?php echo _LANG("nav_my_messages"); ?></a>
			<a href="<?php echo ozel_url("favori-ilanlarim"); ?>" class="mk-nav-link"><i class="ion-heart"></i> <?php echo _LANG("nav_my_favorites"); ?></a>
		</nav>
		<div class="mk-header-actions">
			<button type="button" class="mk-icon-btn" data-toggle="modal" data-target="#ara_modal" aria-label="<?php echo _LANG('title_search'); ?>"><i class="ion-search"></i></button>
			<?php if ($oturum_id) {
				$mk_msj = mysqli_num_rows(mysqli_query($baglan,"select id from mesajlar where kime='$oturum_id' and okundu='0'"));
				$mk_bld = mysqli_num_rows(mysqli_query($baglan,"select id from bildirimler where uye_id='$oturum_id' and okundu='0' and tip='1'"));
				$mk_resim = uye_bilgi("profil_gorseli",$oturum_id);
			?>
			<a href="<?php echo ozel_url("mesajlarim"); ?>" class="mk-icon-btn" aria-label="<?php echo _LANG('nav_my_messages'); ?>"><i class="ion-chatbox"></i><?php if ($mk_msj>0) { ?><span class="mk-badge"><?php echo $mk_msj; ?></span><?php } ?></a>
			<a href="<?php echo ozel_url("bildirimlerim"); ?>" class="mk-icon-btn" aria-label="<?php echo _LANG('title_notifications'); ?>"><i class="ion-ios-bell"></i><?php if ($mk_bld>0) { ?><span class="mk-badge"><?php echo $mk_bld; ?></span><?php } ?></a>
			<a href="<?php echo profil_link_getir($oturum_id); ?>" class="mk-avatar" aria-label="<?php echo _LANG('nav_my_profile'); ?>"><?php if ($mk_resim) { ?><img src="<?php echo thumbnail_url("50_50",$mk_resim); ?>" alt=""><?php } else { ?><i class="ion-person"></i><?php } ?></a>
			<?php } else { ?>
			<a href="<?php echo ozel_url("giris-yap"); ?>" class="mk-icon-btn" aria-label="<?php echo _LANG('nav_login'); ?>"><i class="ion-person"></i></a>
			<?php } ?>
		</div>
	</div>
</header>
<div class="visible-sm visible-xs">
	<div class="mobil_header">
		<div class="container">
			<div class="row">
				<?php $mbmt=tema_ayar("mobil_buton_mt"); ?>
				<div class="col-xs-8 text-center" <?php if ($mbmt) {echo "style='margin-top:".$mbmt."px';";} ?>>
					<button type="button" class="pull-left" id="mm_buton" href="#mm_panel" aria-label="Menu"><i class="ion-navicon-round mr0"></i></button>
				</div>
				<div class="col-xs-26 yukseklik_h">
					<a href="<?php echo site_url(); ?>"><img src="<?php $header_logo=tema_ayar("logo_light"); if (!$header_logo) { $header_logo=tema_ayar("logo"); } echo $header_logo; ?>" alt="JaayQuick" class="img-responsive img-center mobil_logo"/></a>
				</div>
				<div class="col-xs-8 text-center" <?php if ($mbmt) {echo "style='margin-top:".$mbmt."px';";} ?>>
					<div class="mk-mob-actions">
						<button type="button" data-toggle="modal" data-target="#ara_modal" aria-label="<?php echo _LANG('title_search'); ?>"><i class="ion-search mr0"></i></button>
						<button type="button" id="mmu_buton" href="#mmu_panel" aria-label="Account menu"><i class="ion-person-stalker mr0"></i></button>
					</div>
				</div>
			</div>
		</div>
	</div>
	<form action="" method="post">
<?php include('bilesen/ust-bar-araclar.php'); ?>
		<div class="mobil_ara">
		  <input style="width:88%;border-left:0;" type="text" id="m_ara_input" class="form-control pull-left" name="s" placeholder="<?php echo _LANG('search_placeholder'); ?>" value="<?php if (!strstr(browser_url(),"siparis")) {echo isset($_GET["s"]) ? htmlspecialchars($_GET["s"], ENT_QUOTES, 'UTF-8') : '';} ?>" required>
		  <button style="width:12%;" class="btn btn-default pull-left" name="ara_buton" type="submit" value="<?php echo _LANG('search_button'); ?>" aria-label="<?php echo _LANG('title_search'); ?>"><i class="ion-search mr0"></i></button>
		  <div class="clearfix"></div>
		</div>
	</form>
</div>
<main id="main-content">







