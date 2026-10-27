<?php 
if(count(get_included_files())==1) {die();}
$footer_genis=tema_ayar("footer_genis");
if ($footer_genis) {echo "<div class='container' style='margin-top:15px;'>$footer_genis</div>";}
?>
</main>
<footer>
<div class="container">
<div class="mk-footer-brand">
	<?php $mk_f_logo = tema_ayar("logo_light"); if (!$mk_f_logo) { $mk_f_logo = "/uploads/images/jaayquick-logo-light.png"; } ?>
	<a href="<?php echo site_url(); ?>"><img src="<?php echo $mk_f_logo; ?>" alt="JaayQuick"></a>
	<div class="tagline"><?php echo _LANG('mk_logo_tagline'); ?></div>
</div>
<div class="mk-footer-cols">
<?php
global $baglan;
$site_telefon=tema_ayar("site_telefon");
$site_email=tema_ayar("site_email");
$facebook_url=tema_ayar("facebook");
$twitter_url=tema_ayar("twitter");
$instagram_url=tema_ayar("instagram");
?>
	<div class="mk-fc">
		<div class="baslik"><?php echo _LANG('mk_footer_quick_links'); ?></div>
		<ul>
			<li><a href="<?php echo site_url(); ?>"><?php echo _LANG('mk_nav_home'); ?></a></li>
			<li><a href="<?php echo site_url(); ?>#kategoriler"><?php echo _LANG('mk_view_all_categories'); ?></a></li>
			<li><a href="<?php echo ozel_url("ilanlarim"); ?>"><?php echo _LANG("nav_my_ads"); ?></a></li>
			<li><a href="<?php echo ozel_url("favori-ilanlarim"); ?>"><?php echo _LANG("nav_my_favorites"); ?></a></li>
			<li><a href="<?php echo ozel_url("mesajlarim"); ?>"><?php echo _LANG("nav_my_messages"); ?></a></li>
			<?php if (isset($_SESSION["oturum_id"]) && $_SESSION["oturum_id"]) { ?>
			<li><a href="<?php echo ozel_url("cikis-yap"); ?>"><?php echo _LANG("nav_logout"); ?></a></li>
			<?php } else { ?>
			<li><a href="<?php echo ozel_url("giris-yap"); ?>"><?php echo _LANG("nav_login"); ?> / <?php echo _LANG("nav_register"); ?></a></li>
			<?php } ?>
		</ul>
	</div>
	<div class="mk-fc">
		<div class="baslik"><?php echo _LANG('mk_footer_categories'); ?></div>
		<ul>
			<?php
			$mk_fkat = mysqli_query($baglan,"select id,baslik from kategoriler where tip='ilan' and ust_kategori='0' order by icerik desc, baslik asc limit 5");
			while ($mk_fk = mysqli_fetch_array($mk_fkat)) {
				$mk_fk_id = $mk_fk["id"];
				$mk_fk_baslik = _DIL_BASLIK($mk_fk["baslik"], $mk_fk_id, "kategori");
				echo '<li><a href="'.kategori_link_getir($mk_fk_id).'">'.htmlspecialchars($mk_fk_baslik,ENT_QUOTES,'UTF-8').'</a></li>';
			}
			?>
		</ul>
	</div>
	<div class="mk-fc">
		<div class="baslik"><?php echo _LANG('mk_footer_support'); ?></div>
		<ul>
			<li><a href="<?php echo link_getir(1); ?>"><?php echo _DIL_BASLIK(getir("kategoriler","baslik",1),1,"kategori") ? _DIL_BASLIK(getir("icerik","baslik",1),1,"sayfa") : ''; ?></a></li>
			<li><a href="<?php echo link_getir(3); ?>"><?php echo _DIL_BASLIK(getir("icerik","baslik",3),3,"sayfa"); ?></a></li>
			<li><a href="<?php echo link_getir(10); ?>"><?php echo _DIL_BASLIK(getir("icerik","baslik",10),10,"sayfa"); ?></a></li>
			<li><a href="<?php echo link_getir(13); ?>"><?php echo _DIL_BASLIK(getir("icerik","baslik",13),13,"sayfa"); ?></a></li>
			<li><a href="<?php echo link_getir(15); ?>"><?php echo _DIL_BASLIK(getir("icerik","baslik",15),15,"sayfa"); ?></a></li>
		</ul>
	</div>
	<div class="mk-fc mk-fc-social">
		<div class="baslik"><?php echo _LANG('mk_footer_follow'); ?></div>
		<div class="mk-social-row">
			<?php $facebook_url=tema_ayar("facebook"); $twitter_url=tema_ayar("twitter"); $instagram_url=tema_ayar("instagram"); $site_whatsapp=tema_ayar("site_whatsapp"); ?>
			<?php if ($facebook_url) { ?><a href="<?php echo $facebook_url; ?>" target="_blank" rel="noopener" class="mk-social-btn" aria-label="Facebook"><i class="fa fa-facebook"></i></a><?php } ?>
			<?php if ($twitter_url) { ?><a href="<?php echo $twitter_url; ?>" target="_blank" rel="noopener" class="mk-social-btn" aria-label="Twitter"><i class="fa fa-twitter"></i></a><?php } ?>
			<?php if ($instagram_url) { ?><a href="<?php echo $instagram_url; ?>" target="_blank" rel="noopener" class="mk-social-btn" aria-label="Instagram"><i class="fa fa-instagram"></i></a><?php } ?>
			<?php if ($site_whatsapp) { ?><a href="https://wa.me/<?php echo tel_uluslararasi($site_whatsapp); ?>" target="_blank" rel="noopener" class="mk-social-btn" aria-label="WhatsApp"><i class="fa fa-whatsapp"></i></a><?php } ?>
		</div>
		<?php if ($site_telefon) { ?><div class="mk-fc-contact"><i class="ion-iphone"></i> <?php echo $site_telefon; ?></div><?php } ?>
		<?php if ($site_email) { ?><div class="mk-fc-contact"><i class="ion-email"></i> <?php echo $site_email; ?></div><?php } ?>
		<?php if ($site_whatsapp) { ?><div class="mk-fc-contact"><i class="fa fa-whatsapp"></i> <?php echo $site_whatsapp; ?></div><?php } ?>
	</div>
</div>

<?php echo tema_ayar("footer_metni"); ?>

<div class="mk-footer-bottom">
	<span>© <?php echo date('Y'); ?> Jaayquick.com. <?php echo _LANG('mk_all_rights'); ?></span>
	<span><?php echo _LANG('mk_local_marketplace'); ?> <span class="flag">🇬🇲</span></span>
</div>

</div>
</footer>
<?php if ($oturum_id and tema_ayar("mobil_fixed_ekle_buton")==1) { ?>
<a href="<?php echo ozel_url("ilan-ekle"); ?>" class="fixed-ekle-buton" data-toggle="tooltip" data-placement="left" title="<?php echo _LANG('nav_free_post'); ?>"><i class="ion-plus mr0"></i></a>
<?php } ?>

<!-- ARA Modal -->
<div class="modal fade" id="ara_modal" tabindex="-1" role="dialog" aria-labelledby="ara_modal">
  <div class="modal-dialog" role="document">
	<form action="" method="post">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel"><?php echo _LANG('modal_search_title'); ?></h4>
      </div>
      <div class="modal-body">
	  <?php form_group(array("tip"=>"text","id"=>"s","baslik"=>_LANG('modal_search_keyword'),"aciklama"=>_LANG('search_placeholder'),"gerekli"=>1)); ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _LANG('modal_close'); ?></button>
		<button class="btn btn-primary" name="ara_buton" type="submit" value="<?php echo _LANG('modal_search_button'); ?>"><i class="ion-search"></i><?php echo _LANG('modal_search_button'); ?></button>
      </div>
    </div>
	</form>
  </div>
</div>

<div id="mm_panel" class="mm">
	<ul class="ana_menu">
		<li><a href="<?php echo ozel_url("acil-acil"); ?>"><i class="ion-android-alarm-clock" style="background:<?php echo @$ana_renk; ?>;"></i><?php echo _LANG('mobile_menu_urgent'); ?></a></li>
		<li><a href="<?php echo ozel_url("fiyati-dusenler"); ?>"><i class="ion-arrow-graph-down-right" style="background:<?php echo @$ana_renk; ?>;"></i><?php echo _LANG("listing_price_drop"); ?></a></li>
		<li><a href="<?php echo ozel_url("vitrin-ilanlari"); ?>"><i class="ion-podium" style="background:<?php echo @$ana_renk; ?>;"></i><?php echo _LANG("sidebar_featured_all"); ?></a></li>
		<?php 
		global $baglan;
		@$kategori_iptal=tema_ayar("kategori_iptal");
		if (@$kategori_iptal) {
			$kategori_iptal=rtrim($kategori_iptal,",");
			$kategori_iptal_sql=" and id not in ($kategori_iptal)";
		} else {
			$kategori_iptal_sql=null;
		}
		$kategori_siralama=tema_ayar("ilan_ekle_kategori_siralama");
		if ($kategori_siralama=="baslik_a_z") {
			$kategori_sql=mysqli_query($baglan,"select * from kategoriler where tip='ilan' and ust_kategori='0' $kategori_iptal_sql order by baslik asc");
		} else if ($kategori_siralama=="icerik_sayisi") {
			$kategori_sql=mysqli_query($baglan,"select * from kategoriler where tip='ilan' and ust_kategori='0' $kategori_iptal_sql order by icerik desc, baslik");
		} else {
			$kategori_sql=mysqli_query($baglan,"select * from kategoriler where tip='ilan' and ust_kategori='0' $kategori_iptal_sql");
		}
		while ($kategori_dizi=mysqli_fetch_array($kategori_sql)) { 
		$kategori_id=$kategori_dizi["id"];
		$kategori_baslik=$kategori_dizi["baslik"];
		$kategori_ikon=kategori_bilgi("ikon",$kategori_id);
		$kategori_ikon_bg=kategori_bilgi("ikon_bg",$kategori_id);
		$kategori_link=kategori_link_getir($kategori_id);
		if (!$kategori_ikon) {$kategori_ikon="ion-search";}
		?>
		<li><a href="<?php echo $kategori_link; ?>"><i class="<?php echo $kategori_ikon; ?>" style="background:<?php echo $kategori_ikon_bg; ?> !important;"></i><?php echo _DIL_BASLIK($kategori_baslik, $kategori_id, 'kategori'); ?></a></li>
		<?php } ?>
	</ul>
</div>

<div id="mmu_panel" class="mm">
	<?php 
	@$oturum_id=$_SESSION["oturum_id"];
	if ($oturum_id) { 
	$ad_soyad=getir("uyeler","ad_soyad",$oturum_id);
	$uye_resim=uye_bilgi("profil_gorseli",$oturum_id);
	$kucuk_resim=thumbnail_url("50_50",$uye_resim);
	$profil_url=profil_link_getir($oturum_id);
	$get_satis=mysqli_num_rows(mysqli_query($baglan,"select id from siparisler where tip='get' and satici_id='$oturum_id'"));
	$okunmamis_mesaj_sql=mysqli_query($baglan,"select * from mesajlar where kime='$oturum_id' and okundu='0'");
	$okunmamis_mesaj_sayisi=mysqli_num_rows($okunmamis_mesaj_sql);	
	$okunmamis_bildirim_sql=mysqli_query($baglan,"select * from bildirimler where uye_id='$oturum_id' and okundu='0' and tip='1'");
	$okunmamis_bildirim_sayisi=mysqli_num_rows($okunmamis_bildirim_sql);	 		
	?>
	<ul>
	<li><a href="<?php echo ozel_url("ilan-ekle"); ?>"><i class="ion-plus"></i><?php echo _LANG("nav_free_post"); ?></a></li>
	<li><a href="<?php echo $profil_url; ?>"><i class="ion-person"></i><?php echo _LANG('nav_my_profile'); ?></a></li>
	<li><a href="<?php echo ozel_url("profil-bilgilerim"); ?>"><i class="ion-clipboard"></i><?php echo _LANG('title_profile_info'); ?></a></li>
	<li><a href="<?php echo ozel_url("profil-guncelle"); ?>"><i class="ion-person"></i><?php echo _LANG("nav_update_profile"); ?></a></li>
	<?php 
    $limit_politikasi=limit_politikasi();
    if ($limit_politikasi==1) {
    ?>
	    <li><a href="<?php echo ozel_url("plan-detaylari"); ?>"><i class="ion-stats-bars"></i><?php echo _LANG("title_plan_details"); ?></a></li>
	<?php } else if ($limit_politikasi==2) { ?>
	    <li><a href="<?php echo ozel_url("paket-satin-al"); ?>"><i class="fa fa-pie-chart"></i><?php echo _LANG("title_buy_package"); ?></a></li>
    <?php } ?>
	<li><a href="<?php echo ozel_url("mesajlarim"); ?>"><i class="ion-paper-airplane"></i><?php echo _LANG("nav_my_messages"); ?></a></li>
	<li><a href="<?php echo ozel_url("bildirimlerim"); ?>"><i class="ion-funnel"></i><?php echo _LANG('p_bildirimler'); ?></a></li>
	<li><a href="<?php echo ozel_url("favori-ilanlarim"); ?>"><i class="ion-android-favorite"></i><?php echo _LANG("nav_my_favorites"); ?></a></li>
	<li role="separator" class="divider"></li>
	<li><a href="<?php echo ozel_url("ilanlarim"); ?>"><i class="ion-compose"></i><?php echo _LANG("nav_my_ads"); ?></a></li>
	<li><a href="<?php echo ozel_url("magazalarim"); ?>"><i class="ion-bag"></i><?php echo _LANG("nav_my_stores"); ?></a></li>
	<li><a href="<?php echo ozel_url("siparislerim"); ?>"><i class="ion-cash"></i><?php echo _LANG("nav_my_orders"); ?></a></li>
	<?php if ($get_satis>0) { ?><li><a href="<?php echo ozel_url("get-satislarim"); ?>"><i class="ion-android-cart"></i><?php echo _LANG("nav_sales"); ?></a></li><?php } ?>
	<li role="separator" class="divider"></li>
	<li><a href="<?php echo ozel_url("cikis-yap"); ?>"><i class="ion-power"></i><?php echo _LANG("nav_logout"); ?></a></li>
	</ul>
	<?php } else { ?>
	<ul>
	<li><a href="<?php echo ozel_url("giris-yap"); ?>"><i class="ion-person"></i><?php echo _LANG("nav_login"); ?></a></li>
	<li><a href="<?php echo ozel_url("kayit-ol"); ?>"><i class="ion-person-add"></i><?php echo _LANG("nav_register"); ?></a></li>
	<li><a href="<?php echo ozel_url("giris-yap")."?r=ie"; ?>"><i class="ion-plus"></i><?php echo _LANG("nav_free_post"); ?></a></li>
	</ul>
	<?php } ?>
</div>

<div style="display:none;">
<?php echo tema_ayar("footer_kod"); ?>
</div>
<script type="text/javascript" src="<?php echo tema_dizini(); ?>/js/jquery-3.3.1.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script type="text/javascript" src="<?php echo tema_dizini(); ?>/js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?php echo tema_dizini(); ?>/js/lightbox.min.js"></script>
<script type="text/javascript" src="<?php echo tema_dizini(); ?>/js/jquery.slides.min.js"></script>
<script type="text/javascript" src="<?php echo tema_dizini(); ?>/js/notify.min.js"></script>
<script type="text/javascript" src="<?php echo tema_dizini(); ?>/js/jquery.mask.min.js"></script>
<script type="text/javascript" src="<?php echo tema_dizini(); ?>/js/jquery.sticky.js"></script>
<script type="text/javascript" src="<?php echo tema_dizini(); ?>/js/main.js?v=41"></script>
<script type="text/javascript" src="<?php echo tema_dizini(); ?>/js/jquery.sidr.min.js"></script>
<?php tema_foot(); ?>
<script>
(function(){var o=new MutationObserver(function(m){m.forEach(function(x){x.addedNodes.forEach(function(n){if(n&&n.nodeType===1){if(n.classList&&n.classList.contains('lb-close'))n.setAttribute('href','#');if(n.querySelectorAll){n.querySelectorAll('.lb-close').forEach(function(e){e.setAttribute('href','#');});}}});});});if(document.body)o.observe(document.body,{childList:true,subtree:true});})();
</script>
</body>
</html>