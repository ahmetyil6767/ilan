<?php 
if(count(get_included_files())==1) {die();}
tema_header(); ?>
<div class="container sayfa mt20">
	<div class="row">
		<div class="col-md-34 col-md-offset-4">
			<?php include("temalar/$aktif_tema/inc/alert.php"); ?>
			<h1><?php echo getir("icerik","baslik",$id); ?></h1>
			<div class="content">
				<?php echo icerik_getir($id); ?>
			</div>
		</div>
	</div>
</div>
<?php tema_footer(); ?>