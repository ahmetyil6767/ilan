<?php if(count(get_included_files())==1) {die();} ?>
<?php if (ayar_getir("panel_sablon")=="boxed") { ?>
<div class="container footer_container text-right">
	<div class="alt">
		<a href="http://www.limontasarim.com/?ref_url=<?php echo site_url(); ?>" target="_blank"><i class="fa fa-lemon-o"></i><?php echo _LANG("p_limontasarim"); ?></a>
	</div>
</div>
<?php } ?>