<?php 
if(count(get_included_files())==1) {die();}
@$il=$_GET["il"];
$il_adi=getir("iller","il_adi",$il);
@$sil=$_GET["sil"];
if ($sil) {
	$sil_adi=getir("ilceler","ilce_adi",$sil);
	$sil_mahalle_sayisi=mysqli_num_rows(mysqli_query($baglan,"select id from mahalleler where ilce_id='$sil'"));
	$sil=mysqli_query($baglan,"delete from mahalleler where ilce_id='$sil'");
	$sil=mysqli_query($baglan,"delete from ilceler where id='$sil'");
	$_SESSION["mesaj"]=array(sprintf(_LANG('msj_ilce_silindi'), $sil_adi, $sil_mahalle_sayisi),"success");
	yonlendir("temalar.php?tema=$aktif_tema&sayfa=panel&islem=ilceler&il=$il");
}

@$ekle=$_POST["buton"];
if ($ekle) {
	$ilce_adi=$_POST["ilce_adi"];
	$query=mysqli_query($baglan,"insert into ilceler set ilce_adi='$ilce_adi', il_id='$il'");
	if ($query) {
		$_SESSION["mesaj"]=array(sprintf(_LANG('msj_ilce_eklendi'), $ilce_adi),"success");
		yonlendir("temalar.php?tema=$aktif_tema&sayfa=panel&islem=ilceler&il=$il");	
	} else {
		$_SESSION["mesaj"]=array(sprintf(_LANG('msj_ilce_eklenemedi'), $ilce_adi),"danger");
		yonlendir("temalar.php?tema=$aktif_tema&sayfa=panel&islem=ilceler&il=$il");	
	}
}

@$guncelle=$_GET["guncelle"];
if ($guncelle) {
	@$guncelle_buton=$_POST["guncelle_buton"];
	@$guncelle_adi=$_POST["guncelle_adi"];
	if (!$guncelle_adi) {
		$guncelle_adi=getir("ilceler","ilce_adi",$guncelle);
	}
	if ($guncelle_buton) {
		$query=mysqli_query($baglan,"update ilceler set ilce_adi='$guncelle_adi' where id='$guncelle'");
		if ($query) {
			$_SESSION["mesaj"]=array(sprintf(_LANG('msj_ilce_guncellendi'), $guncelle_adi),"success");
			yonlendir("temalar.php?tema=$aktif_tema&sayfa=panel&islem=ilceler&il=$il");	
		} else {
			$_SESSION["mesaj"]=array(sprintf(_LANG('msj_ilce_guncellenemedi'), $guncelle_adi),"danger");
			yonlendir("temalar.php?tema=$aktif_tema&sayfa=panel&islem=ilceler&il=$il");	
		}		
		
	}
}	
?>
<h3 class="pull-left bolum-baslik text-uppercase"><?php echo $il_adi; ?></h3>
<a href="temalar.php?tema=<?php echo $aktif_tema; ?>&sayfa=panel&islem=iller" class="btn btn-primary"><?php echo _LANG("pn_illere_geri_don"); ?></a>
<div class="clearfix mb20"></div>
<div class="row">
	<div class="col-md-30">
	
		<?php if ($guncelle) { ?>
		<form action="" method="post">
			<div class="panel panel-primary">
				<div><?php echo _LANG('hb_ilceyi_guncelle'); ?></div>
				<div class="panel-body">
				<?php form_group(array("tip"=>"text","id"=>"guncelle_adi","baslik"=>_LANG('pn_ilce_adi'),"deger"=>$guncelle_adi)); ?>
				<?php form_buton("guncelle_buton",_LANG('tx_guncelle'),"primary"); ?>
				</div>
			</div>
		</form>			
		<?php } ?>
	
	
		<?php 
		$sql=mysqli_query($baglan,"select * from ilceler where il_id='$il' order by ilce_adi asc");
		$say=mysqli_num_rows($sql);
		if ($say>0) {
		?>
		<table id="datatable" class="table table-bordered table-hover table-ozel" data-order='[[ 0, "desc" ]]' data-page-length='15'>
			<thead>
				<tr>
					<th width="10">ID</th>
					<th><?php echo _LANG("filter_district"); ?></th>
					<th>Mahalleler</th>
					<th><?php echo _LANG("general_options"); ?></th>
				</tr>
			</thead>
			<tbody>
			<?php while ($dizi=mysqli_fetch_array($sql)) {
				$id=$dizi["id"];
				$ad=$dizi["ilce_adi"];
				$mahalle_sayisi=mysqli_num_rows(mysqli_query($baglan,"select id from mahalleler where ilce_id='$id'"));
			?>
			<tr>
				<th><?php echo $id; ?></th>
				<td><?php echo $ad; ?></td>
				<td><a href="temalar.php?tema=<?php echo $aktif_tema; ?>&sayfa=panel&islem=mahalleler&ilce=<?php echo $id; ?>" class="btn btn-primary btn-xs"><?php echo _LANG("pn_mahalleleri_goruntule"); ?></a></td>
				<td>
					<a href="temalar.php?tema=<?php echo $aktif_tema; ?>&sayfa=panel&islem=ilceler&il=<?php echo $il; ?>&sil=<?php echo $id; ?>" onclick="return confirm('<?php echo $ad; ?> ilçesini ve bu ilçeye bağlı <?php echo $mahalle_sayisi; ?> mahalleyi kaldırmak üzeresiniz');" class="btn btn-danger btn-xs">Sil</a>
					<a href="temalar.php?tema=<?php echo $aktif_tema; ?>&sayfa=panel&islem=ilceler&il=<?php echo $il; ?>&guncelle=<?php echo $id; ?>" class="btn btn-default btn-xs"><?php echo _LANG("general_update"); ?></a>
				</td>
			</tr>
			<?php }	?>
			</tbody>
		</table>
		<?php } else {echo "<div class='alert alert-danger'>İlçe bulunamadı</div>";} ?>
	</div>
	<div class="col-md-30">
		<form action="" method="post">
			<div class="panel panel-default">
				<div><?php echo _LANG('hb_yeni_ilce_ekle'); ?></div>
				<div class="panel-body">
				<?php form_group(array("tip"=>"text","id"=>"ilce_adi","baslik"=>_LANG('pn_ilce_adi'))); ?>
				<?php form_buton("buton","Ekle","primary"); ?>
				</div>
			</div>
		</form>
	</div>	
</div>