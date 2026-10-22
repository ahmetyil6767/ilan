<?php 
if(count(get_included_files())==1) {die();}
$sql=mysqli_query($baglan,"select * from odemeler order by id desc");
$toplam=mysqli_num_rows($sql);
?>
<script>
$(document).ready(function() {
	$('#datatable').DataTable({
		"responsive": true,
		"lengthMenu": [ 15, 25, 50, 100 ],
		"language": {
			"decimal":        "",
			"emptyTable":     "<?php echo _LANG("p_sonuc_bulunamadi"); ?>",
			"info":           "<?php echo _LANG("p_toplam_icerik"); ?>",
			"infoEmpty":      "<?php echo _LANG("p_gosterilecek_icerik_yok"); ?>",
			"infoFiltered":   "<?php echo _LANG("p_max_icerik"); ?>",
			"infoPostFix":    "",
			"thousands":      ",",
			"lengthMenu":     "<?php echo _LANG("p_gosterilecek_icerik"); ?>",
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
<h3 class="pull-left bolum-baslik text-uppercase"><?php echo _LANG("pn_odeme_bildirimleri"); ?></h3>
<form class="form-inline pull-right visible-md visible-lg" action="" method="post">
  <div class="form-group">
    <input type="text" class="form-control" id="kayit" name="kacarli" placeholder="<?php echo _LANG("p_listeleme_adedi"); ?>">
  </div>
  <button type="submit" name="kacarli_buton" class="btn btn-default" value="Onayla">Onayla</button>
</form>
<div class="clearfix mb20"></div>
<div class="table-responsive">
<table id="datatable" class="table table-bordered table-hover table-ozel" data-order='[[ 0, "desc" ]]' data-page-length='15'>
	<thead>
		<tr>
			<th width="10">ID</th>
			<th><?php echo _LANG("s_gonder_ad_soyad"); ?></th>
			<th>Hangi Bankaya</th>
			<th>Tarih Saat</th>
			<th><?php echo _LANG("p_aciklama"); ?></th>
			<th>Mesaj</th>
		</tr>
	</thead>
	<tbody>
<?php 

while ($dizi=mysqli_fetch_array($sql)) {
	$id=$dizi["id"];
	$uye_id=$dizi["uye_id"];
	$uye_ad_soyad=getir("uyeler","ad_soyad",$dizi["uye_id"]);
	$hesap=$dizi["hesap"];
	$tarih_saat=$dizi["tarih_saat"];
	$aciklama=$dizi["aciklama"];
	$mesaj=$dizi["mesaj"];
?>
<tr>
	<th><?php echo $id; ?></th>
	<td><?php echo $uye_ad_soyad; ?></td>
	<td><?php echo $hesap; ?></td>
	<td><?php echo $tarih_saat; ?></td>
	<td><?php echo $aciklama; ?></td>
	<td><?php echo $mesaj; ?></td>
</tr>
<?php }	?>
	</tbody>
</table>
</div>