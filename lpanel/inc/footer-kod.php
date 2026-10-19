<?php if(count(get_included_files())==1) {die();} ?>
<nav id="menu">
	<ul>
		<?php include("inc/menu.php"); ?>
		<li><a href="<?php echo ayar_getir("site_url"); ?>" target="_blank"><i class="fa fa-external-link fa-fw" aria-hidden="true"></i><?php echo _LANG("listing_view_site"); ?></a></li>
		<li><a href="<?php echo ayar_getir("site_url"); ?>/?logout=1"><i class="fa fa-sign-out fa-fw" aria-hidden="true"></i><?php echo _LANG("p_cikis_yap"); ?></a></li>
	</ul>
</nav>
<script>
$("#menu-toggle-show").hide();
$("#menu-toggle-hide").click(function(e) {
	e.preventDefault();
	$("#wrapper").toggleClass("toggled");
	$("#menu-toggle-hide").hide();
	$("#menu-toggle-show").show();
	$.cookie("lpanel_sidebar", 0);
});
$("#menu-toggle-show").click(function(e) {
	e.preventDefault();
	$("#wrapper").toggleClass("toggled");
	$("#menu-toggle-show").hide();
	$("#menu-toggle-hide").show();
	$.cookie("lpanel_sidebar", 1);
});
<?php if (@$_COOKIE["lpanel_sidebar"]=="0") { ?>
	$("#menu-toggle-hide").hide();
	$("#menu-toggle-show").show();
<?php } ?>
$(function() {
	$('nav#menu').mmenu({
		navbar: {
			add: true
		},
	});
});
$('.nav-pills, .nav-tabs').tabdrop();
$(document).ready(function() {
	$('#jumpmenu').change(function(){
		// option 1 use a JS redirect in the current window
		window.location.href = $(this).val();
	});
});
<?php if (!strstr(browser_url(), "ayarlar.php") and !strstr(browser_url(), "eklentiler.php") and !strstr(browser_url(), "temalar.php")) { ?>
$(document).ready(function() {
	$("input:not(.nofocus):first").focus();
});	
<?php } ?>
$( document ).ready(function() {
	$("input.nofocus").blur();
});	
$(".autoclose").delay(3000).slideUp(200, function() {
    $(this).alert('close');
});
$(function () {
  $('[data-toggle="tooltip"]').tooltip()
})
/* Oturum kontrolü (5 saniyede bir kontrol edilir. Oturum kapanmışsa login sayfasına yönlendirilir) */
setInterval(function(){
	$.ajax({
		type:"post",
		url:"inc/ajax/login_kontrol.php",
		success: function(cevap){
			if (cevap==0) {
				window.location.reload();
			}
		}
	});	
}, 10000);
$(".no_enter").keypress(function(e){
	if ( e.which == 13 ) e.preventDefault();
});

// Gambiya telefon formati: 7 haneli (or. 700 1234 / 422 1234).
	$("#cep_tel").mask("000 0000",{placeholder: "700 1234"});
$("#is_tel").mask("000 0000",{placeholder: "700 1234"});
$("#ev_tel").mask("000 0000",{placeholder: "422 1234"});
$("#firma_tel").mask("000 0000",{placeholder: "422 1234"});
$("#magaza_tel").mask("000 0000",{placeholder: "422 1234"});
$("#telefon").mask("000 000 00 00",{placeholder: "555 123 45 67"});
$("#yetkili_tel").mask("000 000 00 00",{placeholder: "555 123 45 67"});
$("#tc_no").mask("00000000000");
</script>
<?php limon(@$panel_footer_kod); ?>