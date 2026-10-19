<?php 
if(count(get_included_files())==1) {die();}
@$ilce=$_GET["ilce"];
$il=getir("ilceler","il_id",$ilce);
$ilce_adi=getir("ilceler","ilce_adi",$ilce);
$il_adi=getir("iller","il_adi",$il);
@$sil=$_GET["sil"];
if ($sil) {
	$sil_adi=getir("mahalleler","mahalle_adi",$sil);
	$sil=mysqli_query($baglan,"delete from mahalleler where id='$sil'");
	$_SESSION["mesaj"]=array(sprintf(_LANG('msj_mahalle_silindi'), $sil_adi),"success");
	yonlendir("temalar.php?tema=$aktif_tema&sayfa=panel&islem=mahalleler&ilce=$ilce");
}

@$ekle=$_POST["buton"];
if ($ekle) {
	$mahalle_adi=$_POST["mahalle_adi"];
	$query=mysqli_query($baglan,"insert into mahalleler set mahalle_adi='$mahalle_adi', ilce_id='$ilce'");
	if ($query) {
		$_SESSION["mesaj"]=array(sprintf(_LANG('msj_mahalle_eklendi'), $mahalle_adi),"success");
		yonlendir("temalar.php?tema=$aktif_tema&sayfa=panel&islem=mahalleler&ilce=$ilce");	
	} else {
		$_SESSION["mesaj"]=array(sprintf(_LANG('msj_mahalle_eklenemedi'), $mahalle_adi),"danger");
		yonlendir("temalar.php?tema=$aktif_tema&sayfa=panel&islem=mahalleler&ilce=$ilce");	
	}
}

@$guncelle=$_GET["guncelle"];
if ($guncelle) {
	@$guncelle_buton=$_POST["guncelle_buton"];
	@$guncelle_adi=$_POST["guncelle_adi"];
	if (!$guncelle_adi) {
		$guncelle_adi=getir("mahalleler","mahalle_adi",$guncelle);
	}
	if ($guncelle_buton) {
		$query=mysqli_query($baglan,"update mahalleler set mahalle_adi='$guncelle_adi' where id='$guncelle'");
		if ($query) {
			$_SESSION["mesaj"]=array(sprintf(_LANG('msj_mahalle_guncellendi'), $guncelle_adi),"success");
			yonlendir("temalar.php?tema=$aktif_tema&sayfa=panel&islem=mahalleler&ilce=$ilce");	
		} else {
			$_SESSION["mesaj"]=array(sprintf(_LANG('msj_mahalle_guncellenemedi'), $guncelle_adi),"danger");
			yonlendir("temalar.php?tema=$aktif_tema&sayfa=panel&islem=mahalleler&ilce=$ilce");	
		}		
		
	}
}	
?>
<h3 class="pull-left bolum-baslik text-uppercase"><?php echo $ilce_adi; ?> / <?php echo $il_adi; ?></h3>
<a href="temalar.php?tema=<?php echo $aktif_tema; ?>&sayfa=panel&islem=ilceler&il=<?php echo $il; ?>" class="btn btn-primary"><?php echo _LANG("pn_ilcelere_geri_don"); ?></a>
<div class="clearfix mb20"></div>
<div class="row">
	<div class="col-md-30">
	
		<?php if ($guncelle) { ?>
		<form action="" method="post">
			<div class="panel panel-primary">
				<div><?php echo _LANG('hb_mahalleyi_guncelle'); ?></div>
				<div class="panel-body">
				<?php form_group(array("tip"=>"text","id"=>"guncelle_adi","baslik"=>_LANG('pn_mahalle_adi'),"deger"=>$guncelle_adi)); ?>
				<?php form_buton("guncelle_buton"," . _LANG('tx_guncelle') . ","primary"); ?>
				</div>
			</div>
		</form>			
		<?php } ?>
		
		<?php 
		$sql=mysqli_query($baglan,"select * from mahalleler where ilce_id='$ilce' order by mahalle_adi asc");
		$say=mysqli_num_rows($sql);
		if ($say>0) {
		?>
		<table id="datatable" class="table table-bordered table-hover table-ozel" data-order='[[ 0, "desc" ]]' data-page-length='15'>
			<thead>
				<tr>
					<th width="10">ID</th>
					<th><?php echo _LANG("filter_district"); ?></th>
					<th><?php echo _LANG("general_options"); ?></th>
				</tr>
			</thead>
			<tbody>
			<?php while ($dizi=mysqli_fetch_array($sql)) {
				$id=$dizi["id"];
				$ad=$dizi["mahalle_adi"];
			?>
			<tr>
				<th><?php echo $id; ?></th>
				<td><?php echo $ad; ?></td>
				<td>
					<a href="temalar.php?tema=<?php echo $aktif_tema; ?>&sayfa=panel&islem=mahalleler&ilce=<?php echo $ilce; ?>&sil=<?php echo $id; ?>" onclick="return confirm('<?php echo $ad; ?> mahallesini kaldırmak üzeresiniz');" class="btn btn-danger btn-xs">Sil</a>
					<a href="temalar.php?tema=<?php echo $aktif_tema; ?>&sayfa=panel&islem=mahalleler&ilce=<?php echo $ilce; ?>&guncelle=<?php echo $id; ?>" class="btn btn-default btn-xs"><?php echo _LANG("general_update"); ?></a>
				</td>
			</tr>
			<?php }	?>
			</tbody>
		</table>
		<?php } else {echo "<div class='alert alert-danger'>Mahalle bulunamadı</div>";} ?>
	</div>
	<div class="col-md-30">
		<form action="" method="post">
			<div class="panel panel-default">
				<div class="panel-heading">Yeni mahalle ekle</div>
				<div class="panel-body">
				<?php form_group(array("tip"=>"text","id"=>"mahalle_adi","baslik"=>_LANG('pn_mahalle_adi'))); ?>
				<?php form_buton("buton","Ekle","primary"); ?>
				</div>
			</div>
		</form>
	</div>	
</div>