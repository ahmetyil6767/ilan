<?php
if(count(get_included_files())==1) {die();}
function tks_tum_alt_kategoriler($kategori_id) {
	global $baglan;
	global $array;
	$array[]=$kategori_id;
	$sql=mysqli_query($baglan,"select id from kategoriler where ust_kategori='$kategori_id'");
	$say=mysqli_num_rows($sql);
	if ($say>0) {
		while ($dizi=mysqli_fetch_array($sql)) {
			$kategori_id=$dizi["id"];
			tks_tum_alt_kategoriler($kategori_id);
		}
	}
	return $array;
}

$sil_id=$_GET["sil_id"];
$silinen=$_GET["silinen"];
if ($sil_id) {
	$silinecek=tks_tum_alt_kategoriler($sil_id);
	$x=0;
	foreach ($silinecek as $sil) {
		$kategori_bilgi_sil=mysqli_query($baglan,"delete from ekstra_bilgi where id='$sil' and tip='kategori'");
		$kategori_sil=mysqli_query($baglan,"delete from kategoriler where id='$sil'");		
	$x++;}
	yonlendir("eklentiler.php?eklenti=toplu_kategori_sil&sayfa=sil&silinen=$x");
}
$toplam=mysqli_num_rows(mysqli_query($baglan,"select id from kategoriler"));
?>
<div class="baslik"><?php echo _LANG('e_topluk_kategori_sil'); ?></div>
<?php if ($silinen) { ?>
<div class="alert alert-success"><?php echo $silinen; ?> kategori silindi.</div>
<?php } ?>
<div class="row">
	<div class="col-md-30">
		<div class="panel panel-default">
			<div class="panel-body">Toplam kategori: <?php echo $toplam; ?></div>
		</div>
		<?php 
		
		$sql=mysqli_query($baglan,"select id,baslik from kategoriler where ust_kategori='0'");
		$say=mysqli_num_rows($sql);
		while ($dizi=mysqli_fetch_array($sql)) {
			$kategori_id=$dizi["id"];
			$kategori_baslik=$dizi["baslik"];
			echo "$kategori_id - $kategori_baslik"; ?> 
			<a href='eklentiler.php?eklenti=toplu_kategori_sil&sayfa=sil&sil_id=<?php echo $kategori_id; ?>' onclick="return confirm('Emin misiniz?')">[sil]</a>
			<br />
		<?php } ?>
	</div>
</div>