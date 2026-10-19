<?php 
if(count(get_included_files())==1) {die();}
// Sayfalama ve kayıt listeleme başladı
@$page=$_GET["page"];
@$kacarli_buton=$_POST["kacarli_buton"];
if ($kacarli_buton) {
	@$kacarli=$_POST["kacarli"];
	$_SESSION["kacarli"]=$kacarli;
}
if (!@$_SESSION["kacarli"]) {
	$kacarli=30;
} else {
	$kacarli=$_SESSION["kacarli"];
}

@$tip=$_GET["tip"];
if ($tip=="bekleyen") {
	$toplam=mysqli_num_rows(mysqli_query($baglan,"select * from auto_mail where durum='0' order by tarih_saat desc")); 
} else {
	$toplam=mysqli_num_rows(mysqli_query($baglan,"select * from auto_mail where durum='1'")); 
	$bekleyen_mail_sayisi=mysqli_num_rows(mysqli_query($baglan,"select * from auto_mail where durum='0' order by tarih_saat desc"));
}

$kac_sayfa=ceil($toplam/$kacarli);
if ($page=="") {$page=1;}
$baslangic=($page-1)*$kacarli;
// Sayfalama ve kayıt listeleme bitti

?>

<?php if (!$tip) { ?>
<h3 class="pull-left bolum-baslik text-uppercase"><?php echo _LANG('e_gonderilen_mailler_rapor'); ?></h3>
<a href="eklentiler.php?eklenti=auto_mail&sayfa=rapor&tip=bekleyen" class="btn btn-default">Bekleyen Mailler <?php if ($bekleyen_mail_sayisi>0) {echo "<span class='badge'>$bekleyen_mail_sayisi</span>";} ?></a>
<form class="form-inline pull-right" action="" method="post">
  <div class="form-group">
    <input type="text" class="form-control" id="kayit" name="kacarli" placeholder="50">
  </div>
  <button type="submit" name="kacarli_buton" class="btn btn-default" value="Onayla">Onayla</button>
</form>
<div class="clearfix mb20"></div>

	<table class="table table-bordered table-hover table-ozel">
		<thead>
			<tr>
				<th>ID</th>
				<th>Ad Soyad</th>
				<th>Email Adresi</th>
				<th><?php echo _LANG('p_baslik'); ?></th>
				<th><?php echo _LANG('p_tarih'); ?></th>
				<th><?php echo _LANG('e_gonderim_tarih'); ?></th>
				<th>Durum</th>
			</tr>
		</thead>
		<tbody>	
<?php 
$sql=mysqli_query($baglan,"select * from auto_mail where durum='1' order by tarih_saat desc");

while ($dizi=mysqli_fetch_array($sql)) {
	$id=$dizi["id"];
	$ad_soyad=$dizi["ad_soyad"];
	$email_adresi=$dizi["email_adresi"];
	$baslik=$dizi["baslik"];
	$mesaj=$dizi["mesaj"];
	$tarih_saat=$dizi["tarih_saat"];
	$gonderim_tarih_saat=$dizi["gonderim_tarih_saat"];
	$durum=$dizi["durum"];
?>
<tr>
	<td><?php echo $id; ?></td>
	<td><?php echo $ad_soyad; ?></td>
	<td><?php echo $email_adresi; ?></td>
	<td><a href="eklentiler.php?eklenti=auto_mail&sayfa=onizleme&id=<?php echo $id; ?>"><?php echo $baslik; ?></a></td>
	<td><?php echo $tarih_saat; ?></td>
	<td><?php echo $gonderim_tarih_saat; ?></td>
	<td><?php if ($durum==1) {echo "<div class='label label-success'>Gönderildi</div>";} else {echo "<div class='label label-danger'>Gönderim bekliyor</div>";} ?></td>
</tr>
<?php }	?>
	</tbody>
</table>



<?php } else if ($tip=="bekleyen") { ?>

<h3 class="pull-left bolum-baslik text-uppercase"><?php echo _LANG('e_bekleyen_mailler'); ?></h3>
<a href="eklentiler.php?eklenti=auto_mail&sayfa=rapor" class="btn btn-default"><?php echo _LANG('e_gonderilen_mailler'); ?></a>
<form class="form-inline pull-right" action="" method="post">
  <div class="form-group">
    <input type="text" class="form-control" id="kayit" name="kacarli" placeholder="50">
  </div>
  <button type="submit" name="kacarli_buton" class="btn btn-default" value="Onayla">Onayla</button>
</form>
<div class="clearfix mb20"></div>

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
$sql=mysqli_query($baglan,"select * from auto_mail where durum='0'");

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

<?php eklenti_sayfalama($kac_sayfa,$page,1,30,"rapor"); ?>

<?php } ?>