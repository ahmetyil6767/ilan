<?php if(count(get_included_files())==1) {die();} ?>
<?php jaayquick(@$panel_header_kod); ?>
<link rel="stylesheet" href="css/bootstrap.min.css">
<link rel="stylesheet" href="css/font-awesome.min.css">
<link href="https://fonts.googleapis.com/css?family=Montserrat:300,400,500,600,700,800,900&amp;subset=latin-ext" rel="stylesheet">
<script src="js/jquery-1.9.1.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script type="text/javascript" src="js/dataTables.min.js"></script>
<link rel="stylesheet" type="text/css" href="css/dataTables.min.css"/>
<link rel="stylesheet" href="css/jquery.mmenu.all.css">
<script src="js/jquery.mmenu.all.js" type="text/javascript"></script>
<script type="text/javascript" src="js/bootstrap-filestyle.min.js"></script>
<link rel="stylesheet" type="text/css" href="css/bootstrap-colorpicker.min.css"/>
<script type="text/javascript" src="js/bootstrap-colorpicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="css/bootstrap-datepicker.min.css"/>
<script type="text/javascript" src="js/bootstrap-datepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="css/bootstrap-datetimepicker.min.css"/>
<script type="text/javascript" src="js/bootstrap-datetimepicker.min.js"></script>
<script src="js/ckeditor/ckeditor.js"></script>
<script>window.__ckLang = '<?php echo aktif_dil(); ?>'; if (typeof CKEDITOR !== 'undefined') { CKEDITOR.config.language = window.__ckLang; }</script>
<link href="js/fileuploader/jquery.fileuploader.css" media="all" rel="stylesheet">
<script src="js/fileuploader/jquery.fileuploader.min.js" type="text/javascript"></script>
<script src="js/fileuploader/custom.js" type="text/javascript"></script>
<link href="css/tabdrop.css" rel="stylesheet">
<script type="text/javascript" src="js/bootstrap-tabdrop.js"></script>
<link href="css/image-picker.css" rel="stylesheet">
<script type="text/javascript" src="js/image-picker.min.js"></script>
<script type="text/javascript" src="js/jaayquickcms.js"></script>
<script type="text/javascript" src="js/jquery.cookie.js"></script>
<script type="text/javascript" src="https://maps.google.com/maps/api/js?libraries=places"></script>
<script type="text/javascript" src="js/locationpicker.jquery.min.js"></script>
<script type="text/javascript" src="js/jquery.mask.min.js"></script>
<link rel="stylesheet" href="style.css">
<?php if (_DIL_RTL_MI()) { echo '<link rel="stylesheet" href="../temalar/jaayquick/dil-rtl.css?v=1984">'; } ?>
<style>
nav#menu, .sidebar, ul.thumbnails.image_picker_selector li .thumbnail.selected {background:<?php echo ayar_getir("panel_renk"); ?> !important;} 
ul.thumbnails.image_picker_selector li .thumbnail.selected {border-color:<?php echo ayar_getir("panel_renk"); ?> !important;}
.sag .ust, .panel-primary .panel-heading  {background:<?php echo darken_color(ayar_getir("panel_renk"),2); ?> !important;border-color:<?php echo darken_color(ayar_getir("panel_renk"),2); ?> !important;color:rgba(255,255,255,0.9) !important;}
.alt {background:<?php echo darken_color(ayar_getir("panel_renk"),2); ?> !important;}
.sag .ust a, .alt a {color:rgba(255,255,255,0.9);} 
.home_sidebar.mesaj, .alert-primary {background:<?php echo ayar_getir("panel_renk"); ?>;}
.btn-primary {background:<?php echo darken_color(ayar_getir("panel_renk"),2); ?> !important;color:#fff !important;}
.btn-primary:hover {background:<?php echo darken_color(ayar_getir("panel_renk"),3); ?> !important;}
.sidebar .badge, .mm-listview .badge {background:#fff !important;color:<?php echo ayar_getir("panel_renk"); ?>;}
#wrapper > .row {margin:0;padding:0;}
#wrapper {padding-left:0;}
#wrapper.toggled {padding-left:200px;}
#sidebar-wrapper {z-index:1000;position:fixed;left:200px;width:0;height:100%;margin-left:-200px;overflow-y:auto;background:#000;}
#wrapper.toggled #sidebar-wrapper {width:200px;}
#page-content-wrapper {width:100%;position: absolute;}
#wrapper.toggled #page-content-wrapper {position:absolute;margin-right:-200px;}
@media(min-width:768px) {
    #wrapper {padding-left:200px;}
    #wrapper.toggled {padding-left:0;}
    #sidebar-wrapper {width:200px;}
	#wrapper.toggled #sidebar-wrapper {width:0;}
    #page-content-wrapper {position:relative;}
    #wrapper.toggled #page-content-wrapper {position:relative;margin-right:0;}
}
.nav-tabs .dropdown li.active a {background:<?php echo ayar_getir("panel_renk"); ?>;color:#fff;}
.pagination li a {color:#888 !important;}
.pagination li.active a {color:#fff !important;background:<?php echo darken_color(ayar_getir("panel_renk"),2); ?> !important;border-color:<?php echo darken_color(ayar_getir("panel_renk"),2); ?> !important;}
.bilesen_resim {color:<?php echo ayar_getir("panel_renk"); ?> !important;}
.filtre ul li.tumu a {background:<?php echo darken_color(ayar_getir("panel_renk"),2); ?> !important;}
<?php 
if (@$_COOKIE["gelistirici_modu"]==1) {
	echo "#gelistirici_ac {display:none;}";
} else {
	echo "#gelistirici_kapat, #gelistirici_paneli {display:none;}";
}
?>

</style>
<?php
$panel_sablon=ayar_getir("panel_sablon");
if (!$panel_sablon) {$panel_sablon="fullwidth";}
if ($panel_sablon=="fullwidth") {
if (isset($_COOKIE["lpanel_sidebar"]) and $_COOKIE["lpanel_sidebar"]=="0") {
	$kapsa_container="toggled mm-page mm-slideout";
} else {
	$kapsa_container="";
}
$sidebar_div="sidebar";
$sag_div="sag";
?>
<style type="text/css">
.panel,.panel-heading,.panel-footer,table td, table, table th, .well, form *, .nav-tabs *, .btn, .checkbox input:after {border-radius:0 !important;}
.sidebar ul li a {padding-left:8px !important;}
.sidebar ul li ul li a {padding-left:30px !important;}
nav#menu, .sidebar {background-image: linear-gradient(to bottom, <?php echo ayar_getir("panel_renk"); ?> 20%, <?php echo darken_color(ayar_getir("panel_renk"),2); ?> 100%) !important;} 
body {background:#f0f0f0;}
.hizli_linkler a:hover {border-color:rgba(0,0,0,0.15);}
</style>
<?php } else if ($panel_sablon=="boxed") {
if ($_COOKIE["lpanel_sidebar"]=="0") {
	$kapsa_container="container kapsa_container toggled mm-page mm-slideout";
} else {
	$kapsa_container="container kapsa_container";
}	
$sidebar_div="col-md-10 col-xs-10 col-lg-10 visible-sm visible-md visible-lg sidebar";
$sag_div="col-xs-60 col-sm-50 col-md-50 col-lg-50 sag";
$boxed_bg_color=ayar_getir("boxed_bg_color");
$boxed_bg_image=ayar_getir("boxed_bg_image");
?>
<style>
.panel {box-shadow:none !important;}
.home_sidebar {border:0 !important;}
.kapsa_container {overflow:hidden !important;}
.kapsa_container > .row {-webkit-border-top-left-radius: 10px;-webkit-border-top-right-radius: 10px;-moz-border-radius-topleft: 10px;-moz-border-radius-topright: 10px;border-top-left-radius: 10px;border-top-right-radius: 10px;}
.sidebar {position:static !important;padding-top:10px;padding-bottom:20px;padding-left:0;padding-right:0 !important;overflow-y:hidden;overflow-x:visible;}
.sidebar ul li a {padding:7px 10px;}
.sidebar ul li.active > a, .sidebar ul li a:hover {color:rgba(255,255,255,1);border-right:0 solid rgba(255,255,255,0.1);}
.sidebar ul li ul li a {padding-left:32px !important;}
.sag {position:static;background:#fff;-webkit-border-top-left-radius: 10px;-webkit-border-top-right-radius: 10px;-moz-border-radius-topleft: 10px;-moz-border-radius-topright: 10px;border-top-left-radius: 10px;border-top-right-radius: 10px;}
.home_sidebar, .hizli_linkler a, .alert, .formgrup, .checkbox input:after {border-radius:3px !important;-webkit-border-radius:3px !important;-moz-border-radius:3px !important;}
.kapsa_container {margin-top:20px;-webkit-border-top-left-radius: 4px;-webkit-border-top-right-radius: 4px;-moz-border-radius-topleft: 4px;-moz-border-radius-topright: 4px;border-top-left-radius: 4px;border-top-right-radius: 4px;}
.alt {margin-bottom:20px;-webkit-border-bottom-right-radius: 4px;-webkit-border-bottom-left-radius: 4px;-moz-border-radius-bottomright: 4px;-moz-border-radius-bottomleft: 4px;border-bottom-right-radius: 4px;border-bottom-left-radius: 4px;}
@media (min-width: 1400px) {
	.kapsa_container, .footer_container {width:1350px;}
	.kapsa_container {overflow:hidden;}
}
@media (min-width: 768px) {
	.container {padding-left:0;padding-right:0;}
	.sidebar, .sag {padding-bottom:200000px;margin-bottom:-200000px;}
	body {background:<?php echo $boxed_bg_color; ?> url(<?php echo $boxed_bg_image; ?>);background-attachment: fixed;}
}
@media (max-width: 767px) {
	.kapsa_container {margin-top:0;}
	.alt {margin-bottom:0;}
}	
.sidebar {overflow-y:scroll;overflow-x:hidden;}
.hizli_linkler a:hover {border-color:rgba(0,0,0,0.20);}
</style>
<?php }	?>
<?php if (isset($_GET["tab"])) { ?>
<script>
$(document).ready(function(){
	$('a[href="#<?php echo $_GET["tab"]; ?>"]').tab('show');
});
</script>
<?php } ?>
<?php $datatable=ayar_getir("datatable"); if ($datatable!=0) { ?>
<style type="text/css">
#listele_table, #datatable {display:none;}
</style>
<script>
$(document).ready(function(){
	$("#listele_table").show();
	$("#datatable").show();
});
</script>
<?php } ?>
<script type="text/javascript">
$(document).ready(function(){
	var current_tab = $("ul.nav-tabs li.active a").attr('aria-controls');
	$("#aktif_tab").val(current_tab);
	$("ul.nav-tabs li a").click(function(e) {
		e.preventDefault();
		$(this).tab('show');
		var current_tab = $("ul.nav-tabs li.active a").attr('aria-controls');
		$("#aktif_tab").val(current_tab);	
	});

	var timer;
	var delay = 300;
	$(".sidebar ul li.closed").hover(function(){
		var nesne=$(this);
		timer = setTimeout(function(){
			$(nesne).find("ul").slideDown(300);
		}, delay);
	},function(){
		var nesne=$(this);
		clearTimeout(timer);
		$(nesne).find("ul").delay(3000).slideUp(300);
	});	
});
</script>