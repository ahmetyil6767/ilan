<?php 
if(count(get_included_files())==1) {die();}
tema_header(); ?>
<div class="container sayfa mt20" style="padding:30px 0;">
	<div class="row">
		<div class="col-md-9 col-md-offset-5 sidebar">
		<?php if ($menu) {menu_getir($menu,1,1);} ?>
		</div>
		<div class="col-md-23">
			<?php include("temalar/$aktif_tema/inc/alert.php"); ?>
			<h1><?php echo _DIL_BASLIK(getir("icerik","baslik",$id), $id, "sayfa"); ?></h1>
			<div class="content">
				<?php echo icerik_getir($id); ?>
				
				<div class="panel panel-default limon">
					<div><?php echo _LANG('hb_iletisim_formu'); ?></div>
					<div class="panel-body">
						<form action="">
							<?php form_control(array("tip"=>"text","id"=>"ad_soyad","baslik"=>"Ad Soyad")); ?>
							
						</form>
					</div>
				</div>
				
			</div>
		</div>
	</div>
</div>
<?php tema_footer(); ?>