<?php
include("../loader.php");
yonetici_degilse_yonlendir($oturum_id,ayar_getir("site_url")."/$admin_dizini/giris-yap.php");
@$islem=$_GET["islem"];
@$id=$_GET["id"];
if ($islem=="onayla") {
	$onay_id_int=(int)$id;
	$sql=mysqli_prepare($baglan,"update yorumlar set durum=1 where id=?");
	mysqli_stmt_bind_param($sql,"i",$onay_id_int);
	mysqli_stmt_execute($sql);
	mysqli_stmt_close($sql);
	$_SESSION["mesaj"]=array(_LANG('p_comment_approved'),"success");
	yonlendir("yorumlar.php");
}
if ($islem=="sil") {
	$sil_id_int=(int)$id;
	$sql=mysqli_prepare($baglan,"delete from yorumlar where id=?");
	mysqli_stmt_bind_param($sql,"i",$sil_id_int);
	mysqli_stmt_execute($sql);
	mysqli_stmt_close($sql);
	$_SESSION["mesaj"]=array(_LANG('msj_yorum_silindi'),"success");
	yonlendir("yorumlar.php");
}
if ($islem=="guncelle") {
	@$buton=$_POST["buton"];
	if ($buton) {
	$ad_soyad=$_POST["ad_soyad"];
	$email_adresi=$_POST["email_adresi"];
	$yorum=$_POST["yorum"];
	$durum=$_POST["durum"];
	$guncelle_id_int=(int)$id;
	$sql=mysqli_prepare($baglan,"update yorumlar set ad_soyad=?, email_adresi=?, yorum=?, durum=? where id=?");
	mysqli_stmt_bind_param($sql,"ssssi",$ad_soyad,$email_adresi,$yorum,$durum,$guncelle_id_int);
	mysqli_stmt_execute($sql);
	mysqli_stmt_close($sql);
	$_SESSION["mesaj"]=array(_LANG('p_comment_updated'),"success");
	yonlendir("yorumlar.php");
	}
}
/* DATATABLE PASİF */
$datatable=ayar_getir("datatable");
if ($datatable==0) {
$page=isset($_GET["p"])?$_GET["p"]:1;
$kacarli_buton=isset($_POST["kacarli_buton"])?$_POST["kacarli_buton"]:"";
if ($kacarli_buton) {
	$kacarli=isset($_POST["kacarli"])?$_POST["kacarli"]:"";
	$_SESSION["kacarli"]=$kacarli;
}
if (empty($_SESSION["kacarli"])) {
	$kacarli=30;
} else {
	$kacarli=$_SESSION["kacarli"];
}
$toplam=mysqli_num_rows(mysqli_query($baglan,"select * from yorumlar")); 
$kac_sayfa=ceil($toplam/$kacarli);
if ($page=="") {$page=1;}
$baslangic=($page-1)*$kacarli;
}
if ($datatable==0) {
$sql=mysqli_query($baglan,"select * from yorumlar order by id desc limit $baslangic, $kacarli");
} else {
$sql=mysqli_query($baglan,"select * from yorumlar");
}
$yorum_sayisi=mysqli_num_rows($sql);
/* DATATABLE PASİF */
?>
<!DOCTYPE HTML>
<html lang="<?php echo _DIL_HTML_LANG(); ?>" dir="<?php echo _DIL_HTML_YON(); ?>">
<head>
	<meta charset="UTF-8">
	<title>Yorumlar - <?php echo $lpanel_baslik; ?></title>
	<?php include("inc/header-kod.php"); ?>
	<?php if ($datatable!=0) { ?>
	<script>
	$(document).ready(function() {
		$('#listele_table').DataTable({
			"responsive": true,
			"lengthMenu": [ 15, 25, 50, 100 ],
			"language": {
				"decimal":        "",
				"emptyTable":     "<?php echo _LANG("p_no_result"); ?>",
				"info":           "Toplam _TOTAL_ yorum bulundu",
				"infoEmpty":      "<?php echo _LANG("p_gosterilecek_yorum_yok"); ?>",
				"infoFiltered":   "(_MAX_ toplam yorumdan)",
				"infoPostFix":    "",
				"thousands":      ",",
				"lengthMenu":     "<?php echo _LANG("p_gosterilecek_kayit"); ?>",
				"loadingRecords": "<?php echo _LANG("p_yukleniyor"); ?>",
				"processing":     "<?php echo _LANG("p_isleniyor"); ?>",
				"search":         "<?php echo _LANG("search"); ?>:",
				"zeroRecords":    "<?php echo _LANG("p_arama_uygun"); ?>",
				"paginate": {
					"first":      "<?php echo _LANG("p_ilk"); ?>",
					"last":       "<?php echo _LANG("p_son"); ?>",
					"next":       "<?php echo _LANG("p_sonraki"); ?>",
					"previous":   "<?php echo _LANG("p_onceki"); ?>"			
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
			<?php if (!$islem) { ?>
			<h3 class="pull-left bolum-baslik"><?php echo _LANG('p_yorumlar_k'); ?></h3>
			<?php if ($datatable==0) { ?>			
			<form class="form-inline pull-right kayit_goster visible-md visible-lg" action="" method="post">
			  <div class="form-group">
				<input type="text" class="form-control input-sm nofocus" id="kayit" name="kacarli" placeholder="<?php echo _LANG('p_listeleme_adedi'); ?>">
			  </div>
			  <button type="submit" name="kacarli_buton" class="btn btn-default btn-sm" value="Onayla"><?php echo _LANG('p_onayla'); ?></button>
			</form>
			<?php } ?>		
			<div class="clearfix mb20"></div>
			<?php if ($yorum_sayisi>0) { ?>
			<div class="table-responsive">
			<table id="listele_table" class="table table-bordered table-hover" data-order='[[ 0, "desc" ]]' data-page-length='15'>
				<thead>
					<tr>
						<th width="10">#</th>
						<th width="140"><?php echo _LANG('p_ad_soyad'); ?></th>
						<th><?php echo _LANG('p_yorum'); ?></th>
						<th><?php echo _LANG('p_icerik'); ?></th>
						<th><?php echo _LANG('p_durum'); ?></th>
						<th width="135"><?php echo _LANG('p_islemler'); ?></th>
					</tr>
				</thead>
				<tbody>
				<?php
				while ($dizi=mysqli_fetch_array($sql)) {
					$id=$dizi["id"];
					$ad_soyad=$dizi["ad_soyad"];
					$email_adresi=$dizi["email_adresi"];
					$yorum=$dizi["yorum"];
					$durum=$dizi["durum"];
					$icerik_id=$dizi["icerik_id"];
					$icerik_url=link_getir($icerik_id);
					$uye_id=$dizi["uye_id"];
					if ($uye_id) {
						$ad_soyad=bilgi_getir("uyeler","ad_soyad",$uye_id);
					}
					$icerik_baslik=bilgi_getir("icerik","baslik",$icerik_id);
				?>
					<tr> 
						<th scope="row"><?php echo $id; ?></th>
						<td><?php if (function_exists('lpanel_uye_profil_link') and $uye_id) {lpanel_uye_profil_link($uye_id);} else {echo e($ad_soyad);} ?></td>
						<td><?php echo e(ozet_olustur($yorum,15)); ?></td>
						<td><a href="<?php echo $icerik_url; ?>"><?php echo $icerik_baslik; ?></a></td>
						<td><?php if ($durum==0) {echo "<div class='label label-danger'>Pasif</div>";} else if ($durum==1) {echo "<div class='label label-success'>Aktif</div>";} ?></td>
						<td>
						<!-- Split button -->
						<div class="btn-group">
						  <a href="yorumlar.php?islem=guncelle&id=<?php echo $id; ?>" class="btn btn-xs btn-primary"><i class="fa fa-pencil" aria-hidden="true"></i><?php echo _LANG('p_guncelle'); ?></a>
						  <button type="button" class="btn btn-primary btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							<span class="caret"></span>
							<span class="sr-only"><?php echo _LANG('p_toggle_dropdown'); ?></span>
						  </button>
						  <ul class="dropdown-menu dropdown-menu-right">
							<?php if ($durum==0) {?><li><a href="yorumlar.php?islem=onayla&id=<?php echo $id; ?>"><i class="fa fa-check"></i><?php echo _LANG('p_onayla'); ?></a></li><?php } ?>
							<li><a href="<?php echo $icerik_url; ?>#yorum_<?php echo $id; ?>"><i class="fa fa-tv fa-fw" aria-hidden="true"></i><?php echo _LANG('p_goruntule'); ?></a></li>
							<li><a href="yorumlar.php?islem=sil&id=<?php echo $id; ?>" onclick="return confirm('Emin misiniz?')"><i class="fa fa-trash fa-fw" aria-hidden="true"></i><?php echo _LANG('p_sil'); ?></a></li>
						  </ul>
						</div>						
						</td>
					</tr>
				<?php } ?>
				</tbody>
			</table>
			</div>
			<?php panel_sayfalama($kac_sayfa,$page,1,"yorumlar.php?p="); ?>
			<?php } else {echo "<div class='alert alert-danger'>". _LANG('p_no_comments'). "</div>";} ?>	
			<?php } else if ($islem=="guncelle") { ?>
			<div class="panel panel-default">
				<div class="panel-heading"><?php echo _LANG('p_yorum_guncelle'); ?></a></div>
				<div class="panel-body">
				<form method="post" action="">
					<?php form_elemani("text","ad_soyad",_LANG('p_ad_soyad'),bilgi_getir("yorumlar","ad_soyad",$id)); ?>
					<?php form_elemani("text","email_adresi",_LANG('p_email_adresi'),bilgi_getir("yorumlar","ad_soyad",$id)); ?>
					<?php textarea_olustur(null,"yorum","Yorum",bilgi_getir("yorumlar","yorum",$id),5); ?>
					<script>
					CKEDITOR.replace( 'yorum', {
						customConfig: '/custom/ckeditor_mini.js'
					});
					</script>
					<?php select_olustur("durum","Durum",array(array("deger"=>0,"baslik"=>"Pasif"),array("deger"=>1,"baslik"=>"Aktif")),bilgi_getir("yorumlar","durum",$id)); ?>
					<?php form_buton("buton",_LANG('p_guncelle'),"primary"); ?>
				</form>		
				</div>
				<div class="panel-footer"><a href="yorumlar.php?islem=sil&id=<?php echo $id; ?>" onclick="return confirm('Emin misiniz?')" class="btn btn-danger pull-right"><?php echo _LANG('gen_delete'); ?></a>
				<div class="clearfix"></div>
				</div>
			</div>	
			<?php } ?>
			</div>
			<div class="clearfix mb20"></div>
		</div>
	</div>
</div>
<?php include("inc/alt.php"); ?>
<?php include("inc/footer-kod.php"); ?>
</body>
</html>
