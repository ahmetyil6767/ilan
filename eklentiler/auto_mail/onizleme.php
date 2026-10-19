<?php 
if(count(get_included_files())==1) {die();}
$id=$_GET["id"];

?>


<h3 class="pull-left bolum-baslik text-uppercase"><?php echo _LANG('e_mail_onizleme'); ?></h3>
<a href="eklentiler.php?eklenti=auto_mail&sayfa=rapor" class="btn btn-default"><?php echo _LANG('e_gonderilen_mailler'); ?></a>
<div class="clearfix mb20"></div>
<div class="table-responsive">
	<table class="table table-bordered table-hover table-ozel">
		<thead>
			<tr>
				<th>ID</th>
				<th>Ad Soyad</th>
				<th>Email Adresi</th>
				<th><?php echo _LANG('p_baslik'); ?></th>
				<th><?php echo _LANG('p_tarih'); ?></th>
				<th>Durum</th>
			</tr>
		</thead>
		<tbody>	
<?php 
$sql=mysqli_query($baglan,"select * from auto_mail where id='$id'");

while ($dizi=mysqli_fetch_array($sql)) {
	$id=$dizi["id"];
	$ad_soyad=$dizi["ad_soyad"];
	$email_adresi=$dizi["email_adresi"];
	$baslik=$dizi["baslik"];
	$mesaj=$dizi["mesaj"];
	$tarih_saat=$dizi["tarih_saat"];
	$durum=$dizi["durum"];
?>
<tr>
	<td><?php echo $id; ?></td>
	<td><?php echo $ad_soyad; ?></td>
	<td><?php echo $email_adresi; ?></td>
	<td><a href="eklentiler.php?eklenti=auto_mail&sayfa=onizleme&id=<?php echo $id; ?>"><?php echo $baslik; ?></a></td>
	<td><?php echo $tarih_saat; ?></td>
	<td><?php if ($durum==1) {echo "<div class='label label-success'>Gönderildi</div>";} else {echo "<div class='label label-danger'>Gönderim bekliyor</div>";} ?></td>
</tr>
<?php }	?>
	</tbody>
</table>
</div>

<div class="panel panel-default">
	<div class="panel-heading"><?php echo $baslik; ?></div>
	<div class="panel-body">
		<?php echo $mesaj; ?>
	</div>
</div>
