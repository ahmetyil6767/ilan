<?php 
if(count(get_included_files())==1) {die();}
@$sil=$_GET["sil"];
if ($sil) {
	$il_id=$sil;
	$sil_adi=getir("iller","il_adi",$il_id);
	$sil_ilceler=mysqli_query($baglan,"select id from ilceler where il_id='$il_id'");
	while ($sil_dizi=mysqli_fetch_array($sil_ilceler)) {
		$sil_ilce_id=$sil_dizi["id"];
		$sil=mysqli_query($baglan,"delete from mahalleler where ilce_id='$sil_ilce_id'");
		$sil=mysqli_query($baglan,"delete from ilceler where id='$sil_ilce_id'");		
	}
	$sql=mysqli_query($baglan,"delete from iller where id='$il_id'");		
	$_SESSION["mesaj"]=array(sprintf(_LANG('msj_il_ilce_silindi'), $sil_adi),"success");
	yonlendir("temalar.php?tema=$aktif_tema&sayfa=panel&islem=iller");
}

@$ekle=$_POST["buton"];
if ($ekle) {
	$il_adi=$_POST["il_adi"];
	$query=mysqli_query($baglan,"insert into iller set il_adi='$il_adi'");
	if ($query) {
		$_SESSION["mesaj"]=array(sprintf(_LANG('msj_il_eklendi'), $il_adi),"success");
		yonlendir("temalar.php?tema=$aktif_tema&sayfa=panel&islem=iller");	
	} else {
		$_SESSION["mesaj"]=array(sprintf(_LANG('msj_il_eklenemedi'), $il_adi),"danger");
		yonlendir("temalar.php?tema=$aktif_tema&sayfa=panel&islem=iller");	
	}
}

@$guncelle=$_GET["guncelle"];
if ($guncelle) {
	@$guncelle_buton=$_POST["guncelle_buton"];
	@$guncelle_adi=$_POST["guncelle_adi"];
	if (!$guncelle_adi) {
		$guncelle_adi=getir("iller","il_adi",$guncelle);
	}
	if ($guncelle_buton) {
		$query=mysqli_query($baglan,"update iller set il_adi='$guncelle_adi' where id='$guncelle'");
		if ($query) {
			$_SESSION["mesaj"]=array(sprintf(_LANG('msj_il_guncellendi'), $guncelle_adi),"success");
			yonlendir("temalar.php?tema=$aktif_tema&sayfa=panel&islem=iller");	
		} else {
			$_SESSION["mesaj"]=array(sprintf(_LANG('msj_il_guncellenemedi'), $guncelle_adi),"danger");
			yonlendir("temalar.php?tema=$aktif_tema&sayfa=panel&islem=iller");	
		}
	}
}	
?>
<h3 class="pull-left bolum-baslik text-uppercase"><?php echo _LANG("pn_il_ilce_mahalle"); ?></h3>
<div class="clearfix mb20"></div>
<div class="row">
	<div class="col-md-30">
		<?php if ($guncelle) { ?>
		<form action="" method="post">
			<div class="panel panel-primary">
				<div><?php echo _LANG('hb_ili_guncelle'); ?></div>
				<div class="panel-body">
				<?php form_group(array("tip"=>"text","id"=>"guncelle_adi","baslik"=>_LANG('pn_il_adi'),"deger"=>$guncelle_adi)); ?>
				<?php form_buton("guncelle_buton"," . _LANG('tx_guncelle') . ","primary"); ?>
				</div>
			</div>
		</form>			
		<?php } ?>	
	
		<table id="datatable" class="table table-bordered table-hover table-ozel" data-order='[[ 0, "desc" ]]' data-page-length='15'>
			<thead>
				<tr>
					<th width="10">ID</th>
					<th><?php echo _LANG("addr_region"); ?></th>
					<th><?php echo _LANG("pn_ilceler"); ?></th>
					<th><?php echo _LANG("general_options"); ?></th>
				</tr>
			</thead>
			<tbody>
			<?php
			$sql=mysqli_query($baglan,"select * from iller order by il_adi asc");
			while ($dizi=mysqli_fetch_array($sql)) {
				$id=$dizi["id"];
				$ad=$dizi["il_adi"];
			?>
			<tr>
				<th><?php echo $id; ?></th>
				<td><?php echo $ad; ?></td>
				<td><a href="temalar.php?tema=<?php echo $aktif_tema; ?>&sayfa=panel&islem=ilceler&il=<?php echo $id; ?>" class="btn btn-primary btn-xs"><?php echo _LANG("pn_ilceleri_goruntule"); ?></a></td>
				<td>
					<a href="temalar.php?tema=<?php echo $aktif_tema; ?>&sayfa=panel&islem=iller&sil=<?php echo $id; ?>" onclick="return confirm('<?php echo $ad; ?> ilini ve bu ile bağlı tüm ilçe ve mahalleleri kaldırmak üzeresiniz');" class="btn btn-danger btn-xs">Sil</a>
					<a href="temalar.php?tema=<?php echo $aktif_tema; ?>&sayfa=panel&islem=iller&guncelle=<?php echo $id; ?>" class="btn btn-default btn-xs"><?php echo _LANG("general_update"); ?></a>
				</td>
			</tr>
			<?php }	?>
			</tbody>
		</table>
	</div>
	<div class="col-md-30">
		<form action="" method="post">
			<div class="panel panel-default">
				<div class="panel-heading">Yeni il ekle</div>
				<div class="panel-body">
				<?php form_group(array("tip"=>"text","id"=>"il_adi","baslik"=>_LANG('pn_il_adi'))); ?>
				<?php form_buton("buton","Ekle","primary"); ?>
				</div>
			</div>
		</form>
	</div>
</div>