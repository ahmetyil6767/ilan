<?php
include("../loader.php");
yonetici_degilse_yonlendir($oturum_id,ayar_getir("site_url")."/$admin_dizini/giris-yap.php");
@$sil=$_GET["sil"];
@$tip=$_GET["tip"];
@$islem=$_GET["islem"];
if (!$tip) {
	$tip_value=0;
} else if ($tip=="hata") {
	$tip_value=2;
}
if ($sil) {
	$sil_id_int=(int)$sil;
	$sql=mysqli_prepare($baglan,"delete from bildirimler where id=?");
	mysqli_stmt_bind_param($sql,"i",$sil_id_int);
	mysqli_stmt_execute($sql);
	mysqli_stmt_close($sql);
	if (!$tip) {
		$_SESSION["mesaj"]=array(_LANG('msj_bildirim_silindi'),"success");
		yonlendir("bildirimler.php");
	} else if ($tip=="hata") {
		$_SESSION["mesaj"]=array(_LANG('msj_hata_raporu_silindi'),"success");
		yonlendir("bildirimler.php?tip=hata");
	}
}
if ($islem=="tumunu_sil") {
	if (!$tip) {
		$sql=mysqli_prepare($baglan,"delete from bildirimler where tip=0");
  	mysqli_stmt_execute($sql);
  	mysqli_stmt_close($sql);
		$_SESSION["mesaj"]=array(_LANG('p_all_notifications_deleted'),"success");
		yonlendir("bildirimler.php");
	} else if ($tip=="hata") {
		$sql=mysqli_prepare($baglan,"delete from bildirimler where tip=2");
  	mysqli_stmt_execute($sql);
  	mysqli_stmt_close($sql);
		$_SESSION["mesaj"]=array(_LANG('p_all_error_reports_deleted'),"success");		
		yonlendir("bildirimler.php?tip=hata");
	}
	
}
/* DATATABLE PASİF */
$datatable=ayar_getir("datatable");
if ($datatable==0) {
@$page=$_GET["p"];
@$kacarli_buton=$_POST["kacarli_buton"];
@$kacarli=null;
if ($kacarli_buton) {
	$kacarli=$_POST["kacarli"];
	$_SESSION["kacarli"]=$kacarli;
}
if (!isset($_SESSION["kacarli"])) {
	$kacarli=30;
} else {
	$kacarli=$_SESSION["kacarli"];
}
$toplam=mysqli_num_rows(mysqli_query($baglan,"select * from bildirimler where tip='$tip_value'")); 
$kac_sayfa=ceil($toplam/$kacarli);
if ($page=="") {$page=1;}
$baslangic=($page-1)*$kacarli;
}

if ($datatable==0) {
$sql=mysqli_query($baglan,"select * from bildirimler where tip='$tip_value' order by id desc limit $baslangic, $kacarli");
} else {
$sql=mysqli_query($baglan,"select * from bildirimler where tip='$tip_value'");
$toplam=mysqli_num_rows($sql);
}
/* DATATABLE PASİF */
?>
<!DOCTYPE HTML>
<html lang="<?php echo _DIL_HTML_LANG(); ?>" dir="<?php echo _DIL_HTML_YON(); ?>">
<head>
	<meta charset="UTF-8">
	<title>Bildirimler - <?php echo $lpanel_baslik; ?></title>
	<?php include("inc/header-kod.php"); ?>
	<?php if ($datatable!=0) { ?>
	<script>
	$(document).ready(function() {
		$('#listele_table').DataTable({
			"responsive": true,
			"lengthMenu": [ 15, 25, 50, 100 ],
			"language": {
				"decimal":        "",
				"emptyTable":     "<?php echo _LANG('p_no_result'); ?>",
				"info":           "<?php echo _LANG('p_toplam_kayit'); ?>",
				"infoEmpty":      "<?php echo _LANG('p_gosterilecek_yok'); ?>",
				"infoFiltered":   "<?php echo _LANG('p_max_kayit'); ?>",
				"infoPostFix":    "",
				"thousands":      ",",
				"lengthMenu":     "<?php echo _LANG('p_gosterilecek_kayit'); ?>",
				"loadingRecords": "<?php echo _LANG('p_yukleniyor'); ?>",
				"processing":     "<?php echo _LANG('p_isleniyor'); ?>",
				"search":         "<?php echo _LANG('search'); ?>:",
				"zeroRecords":    "<?php echo _LANG('p_arama_uygun'); ?>",
				"paginate": {
					"first":      "<?php echo _LANG('p_ilk'); ?>",
					"last":       "<?php echo _LANG('p_son'); ?>",
					"next":       "<?php echo _LANG('p_sonraki'); ?>",
					"previous":   "<?php echo _LANG('p_onceki'); ?>"
			}
			}
		});
	} );
	</script>
	<?php } ?>
	<meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
<div id="wrapper" class="<?php echo $kapsa_container; ?>">
	<div class="row">
		<div id="sidebar-wrapper" class="<?php echo $sidebar_div; ?>">
		<?php include("inc/sidebar.php"); ?>
		</div>
		<div id="page-content-wrapper" class="<?php echo $sag_div; ?>">
		<?php include("inc/ust.php"); ?>
		<div class="content">
		<?php if (!isset($tip)) { ?>
			<h3 class="pull-left bolum-baslik"><?php echo _LANG('p_bildirimler'); ?></h3>
			<a href="bildirimler.php?tip=hata" class="btn btn-default"><?php echo _LANG('p_hata_raporlari'); ?></a>
			<?php if ($toplam>0) { ?><a href="bildirimler.php?islem=tumunu_sil" class="btn btn-danger" onclick="return confirm('<?php echo _LANG("p_bildirim_tum_sil_onay"); ?>')"><i class="fa fa-trash"></i><?php echo _LANG('p_tumunu_sil'); ?></a><?php } ?>
			<?php if ($datatable==0) { ?>			
			<form class="form-inline pull-right kayit_goster visible-md visible-lg" action="" method="post">
			  <div class="form-group">
				<input type="text" class="form-control input-sm nofocus" id="kayit" name="kacarli" placeholder="<?php echo _LANG('p_listeleme_adedi'); ?>"
			  </div>
			  <button type="submit" name="kacarli_buton" class="btn btn-default btn-sm" value="Onayla"><?php echo _LANG('p_onayla'); ?></button>
			</form>
			<?php } ?>		
			<div class="clearfix mb20"></div>
			<div class="table-responsive">
			<table id="listele_table" class="table table-bordered table-hover" data-order='[[ 1, "desc" ]]' data-page-length='15'>
				<thead>
					<tr>
						<th><?php echo _LANG('p_bildirim'); ?></th>
						<th><?php echo _LANG('p_tarih_saat'); ?></th>
						<th class="text-center" width="10"><?php echo _LANG('p_sil'); ?></th>
					</tr>
				</thead>
				<tbody>
				<?php
				while ($dizi=mysqli_fetch_array($sql)) {
					$id=$dizi["id"];
					$bildirim=$dizi["bildirim"];
					$tarih_saat=$dizi["tarih_saat"];
				?>
					<tr> 
						<td><?php echo $bildirim; ?></td>
						<td><?php echo $tarih_saat; ?></td>
						<td class="text-center"><a href="bildirimler.php?sil=<?php echo $id; ?>" class="text-danger"><i class="fa fa-trash mr0"></i></a></td>
					</tr>
				<?php } ?>
				</tbody>
			</table>
			</div>
			<?php panel_sayfalama($kac_sayfa,$page,1,"bildirimler.php?p="); ?>
		<?php } else if ($tip=="hata") { ?>
			<h3 class="pull-left bolum-baslik"><?php echo _LANG('p_hata_raporlari_k'); ?></h3>
			<a href="bildirimler.php" class="btn btn-default"><?php echo _LANG('p_bildirimler_menu'); ?></a>
			<?php if ($toplam>0) { ?><a href="bildirimler.php?tip=hata&&tip=hata&islem=tumunu_sil" class="btn btn-danger" onclick="return confirm('<?php echo _LANG("p_hata_rapor_tum_sil_onay"); ?>')"><?php echo _LANG('p_tumunu_sil'); ?></a><?php } ?>
			<?php if ($datatable==0 and $toplam>0) { ?>			
				<form class="form-inline pull-right kayit_goster visible-md visible-lg" action="" method="post">
				  <div class="form-group">
					<input type="text" class="form-control input-sm nofocus" id="kayit" name="kacarli" placeholder="<?php echo _LANG('p_listeleme_adedi'); ?>"
				  </div>
				  <button type="submit" name="kacarli_buton" class="btn btn-default btn-sm" value="Onayla"><?php echo _LANG('p_onayla'); ?></button>
				</form>
			<?php } ?>		
			<div class="clearfix mb20"></div>
			<?php if ($toplam>0) { ?>
			<div class="table-responsive">
			<table id="listele_table" class="table table-bordered table-hover" data-order='[[ 1, "desc" ]]' data-page-length='15'>
				<thead>
					<tr>
						<th><?php echo _LANG('p_rapor'); ?></th>
						<th><?php echo _LANG('p_tarih_saat'); ?></th>
						<th class="text-center" width="10"><?php echo _LANG('p_sil'); ?></th>
					</tr>
				</thead>
				<tbody>
				<?php
				while ($dizi=mysqli_fetch_array($sql)) {
					$id=$dizi["id"];
					$bildirim=$dizi["bildirim"];
					$tarih_saat=$dizi["tarih_saat"];
				?>
					<tr> 
						<td><?php echo $bildirim; ?></td>
						<td><?php echo $tarih_saat; ?></td>
						<td class="text-center"><a href="bildirimler.php?tip=hata&sil=<?php echo $id; ?>" class="text-danger"><i class="fa fa-trash mr0"></i></a></td>
					</tr>
				<?php } ?>
				</tbody>
			</table>
			</div>
			<?php panel_sayfalama($kac_sayfa,$page,1,"bildirimler.php?tip=hata&p="); ?>
			<?php } else {echo _LANG('p_hata_yok');} ?>
		<?php } ?>
		<div class="clearfix mb20"></div>
		</div>
	</div>
</div>
<?php include("inc/alt.php"); ?>
<?php include("inc/footer-kod.php"); ?>
</body>
</html>