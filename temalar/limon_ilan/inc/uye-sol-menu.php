<?php 
if(count(get_included_files())==1) {die();}
$get_satis=mysqli_num_rows(mysqli_query($baglan,"select id from siparisler where tip='get' and satici_id='$oturum_id'"));
?>
<div id="sidebar_pc_menu" class="panel panel-default limon">
	<div><?php echo _LANG('hb_size_ozel_menu'); ?></div>
	<div class="panel-body" style="padding:0;">
		<ul>
			<li <?php if (browser_url()==profil_link_getir($oturum_id)) {echo "class='active'";} ?>><a href="<?php echo profil_link_getir($oturum_id); ?>"><i class="fa fa-user fa-fw"></i><?php echo _LANG('nav_my_profile'); ?></a></li>
			<li <?php if (browser_url()==ozel_url("profil-bilgilerim")) {echo "class='active'";} ?>><a href="<?php echo ozel_url("profil-bilgilerim"); ?>"><i class="fa fa-user-circle fa-fw"></i><?php echo _LANG('title_profile_info'); ?></a></li>
            <?php if (limit_politikasi()==1) { ?>
			    <li <?php if (browser_url()==ozel_url("plan-detaylari")) {echo "class='active'";} ?>><a href="<?php echo ozel_url("plan-detaylari"); ?>"><i class="fa fa-pie-chart fa-fw"></i><?php echo _LANG("title_plan_details"); ?></a></li>
			<?php } else { ?>
			    <li <?php if (browser_url()==ozel_url("paket-satin-al")) {echo "class='active'";} ?>><a href="<?php echo ozel_url("paket-satin-al"); ?>"><i class="fa fa-pie-chart fa-fw"></i><?php echo _LANG("title_buy_package"); ?></a></li>
			<?php } ?>
			<li <?php if (browser_url()==ozel_url("mesajlarim")) {echo "class='active'";} ?>><a href="<?php echo ozel_url("mesajlarim"); ?>"><i class="fa fa-send fa-fw"></i><?php echo _LANG("nav_my_messages"); ?></a></li>
			<li <?php if (browser_url()==ozel_url("bildirimlerim")) {echo "class='active'";} ?>><a href="<?php echo ozel_url("bildirimlerim"); ?>"><i class="fa fa-bell fa-fw"></i><?php echo _LANG('p_bildirimler'); ?></a></li>
			<li <?php if (browser_url()==ozel_url("favori-ilanlarim")) {echo "class='active'";} ?>><a href="<?php echo ozel_url("favori-ilanlarim"); ?>"><i class="fa fa-heart fa-fw"></i><?php echo _LANG("nav_my_favorites"); ?></a></li>
			<li <?php if (browser_url()==ozel_url("ilanlarim")) {echo "class='active'";} ?>><a href="<?php echo ozel_url("ilanlarim"); ?>"><i class="fa fa-list fa-fw"></i><?php echo _LANG("nav_my_ads"); ?></a></li>
			<li <?php if (browser_url()==ozel_url("magazalarim")) {echo "class='active'";} ?>><a href="<?php echo ozel_url("magazalarim"); ?>"><i class="ion-bag fa fa-fw"></i><?php echo _LANG("nav_my_stores"); ?></a></li>
			<li <?php if (browser_url()==ozel_url("siparislerim")) {echo "class='active'";} ?>><a href="<?php echo ozel_url("siparislerim"); ?>"><i class="fa fa-cart-plus fa-fw"></i><?php echo _LANG("nav_my_orders"); ?></a></li>
			<?php if ($get_satis>0) { ?><li <?php if (browser_url()==ozel_url("get-satislarim")) {echo "class='active'";} ?>><a href="<?php echo ozel_url("get-satislarim"); ?>"><i class="fa fa-user fa-fw"></i><?php echo _LANG("nav_sales"); ?></a></li><?php } ?>
		</ul>
	</div>
</div>