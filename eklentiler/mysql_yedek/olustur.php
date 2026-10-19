<?php
if(count(get_included_files())==1) {die();}
@$buton=$_POST["buton"];
if ($buton) {
	if ($buton=="mysql") {
		EXPORT_TABLES($host,$kullanici,$parola,$veritabani);
	}
}
?>
<div class="baslik">MYSQL YEDEKLEME</div>
<div class="row">
	<div class="col-md-30">
		<p><?php echo _LANG('e_yedek_uyari'); ?></p>
		<p><?php echo _LANG('e_yedek_aciklama'); ?></p>
		<form action="" method="post">
			<button type="submit" name="buton" value="mysql" class="btn btn-default"><i class="fa fa-cloud-download"></i><?php echo _LANG('e_yedek_indir'); ?></button>
		</form>
	</div>
</div>