<?php 
if(count(get_included_files())==1) {die();}
tema_header(); ?>
<div class="container sayfa mt20">
	<div class="row">
		<div class="col-md-26 col-md-offset-8 text-center">
			<?php include("temalar/$aktif_tema/inc/alert.php"); ?>
			<h1>404. SAYFA BULUNAMADI</h1>
			<div class="content">
				<?php echo _LANG('s_404_aciklama'); ?>
			</div>
		</div>
	</div>
</div>
<?php tema_footer(); ?>