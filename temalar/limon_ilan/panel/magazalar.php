<?php 
if(count(get_included_files())==1) {die();}
$limit_politikasi=limit_politikasi();
@$sil=$_GET["sil"];
if ($sil) {
	$magaza_adi=getir("magazalar","baslik",$sil);
	mysqli_query($baglan,"delete from magazalar where id='$sil'");
	mysqli_query($baglan,"delete from magaza_uye where magaza_id='$sil'");
	mysqli_query($baglan,"delete from ekstra_bilgi where id='$sil' and tip='magaza'");
	$_SESSION["mesaj"]=array(sprintf(_LANG('msj_magaza_silindi'), $magaza_adi),"success");
	yonlendir("temalar.php?tema=$aktif_tema&sayfa=panel&islem=magazalar");
}

$sql=mysqli_query($baglan,"select * from magazalar order by id desc");
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
<h3 class="pull-left bolum-baslik text-uppercase"><?php echo _LANG("stores"); ?></h3>
<a href="temalar.php?tema=<?php echo $aktif_tema; ?>&sayfa=panel&islem=magaza-ekle" class="btn btn-primary"><?php echo _LANG("pn_magaza_ekle"); ?></a>
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
			<th><?php echo _LANG("p_baslik"); ?></th>
			<th>Subdomain</th>
			<th><?php echo _LANG("p_uye"); ?></th>
			<th>Kategori</th>
			<th>Paket</th>
			<th>Tarih Saat</th>
			<?php if ($limit_politikasi==1) { ?>
			<th><?php echo _LANG("pn_paket_bitis"); ?></th>
			<?php } ?>
			<th>Durum</th>
			<th><?php echo _LANG("general_options"); ?></th>
		</tr>
	</thead>
	<tbody>
<?php 
while ($dizi=mysqli_fetch_array($sql)) {
	$id=$dizi["id"];
	$baslik=$dizi["baslik"];
	$subdomain=$dizi["subdomain"];
	$uye_id=$dizi["uye_id"];
	$uye_ad_soyad=getir("uyeler","ad_soyad",$dizi["uye_id"]);
	$kategori=$dizi["kategori"];
	$kategori_baslik=_DIL_BASLIK(getir("kategoriler","baslik",$dizi["kategori"]), $dizi["kategori"], "kategori");
	$tarih_saat=$dizi["tarih_saat"];
	$durum=$dizi["durum"];
	$paket=magaza_bilgi("paket",$id);
	$paket_bitis_tarihi=magaza_bilgi("paket_bitis_tarihi",$id);
	$paket_adi=liste_bilgi("paket_adi",$paket);
	
	$bugun=tarih_saat();
	if ($paket_bitis_tarihi>$bugun) {
		$bitis_uyari=null;
	} else {
		$bitis_uyari="danger";
	}
		
?>
<tr>
	<th><?php echo $id; ?></th>
	<td><a href="temalar.php?tema=<?php echo $aktif_tema; ?>&sayfa=panel&islem=magaza-guncelle&id=<?php echo $id; ?>" target="_blank"><?php echo $baslik; ?></a> <a href="<?php echo magaza_link_getir($id); ?>" target="_blank"><i class="fa fa-external-link mr0"></i></a></td>
	<td><?php echo $subdomain; ?></td>
	<td><a href="<?php echo lpanel_uye_profil_url($uye_id); ?>"><?php echo $uye_ad_soyad; ?></a> <a href="<?php echo profil_link_getir($uye_id); ?>" target="_blank"><i class="fa fa-external-link mr0"></i></a></td>
	<td><?php echo $kategori_baslik; ?></td>
	<td><?php echo $paket_adi; ?></td>
	<td><?php echo $tarih_saat; ?></td>
	<?php if ($limit_politikasi==1) { ?>
	<td class="<?php echo $bitis_uyari; ?>"><?php echo $paket_bitis_tarihi; ?></td>
	<?php } ?>
	<td><?php if ($durum==1) {echo "<span class='label label-success'>Onaylı</span>";} else if ($durum==0) {echo "<span class='label label-danger'>Pasif</span>";} else if ($durum==2) {echo "<span class='label label-danger'>Süresi doldu</span>";}?></td>
	<td>
	
	<a href="temalar.php?tema=<?php echo $aktif_tema; ?>&sayfa=panel&islem=magaza-guncelle&id=<?php echo $id; ?>" class="btn btn-default btn-xs"><?php echo _LANG("general_update"); ?></a>
	<a href="<?php echo magaza_link_getir($id); ?>" target="_blank" class="btn btn-default btn-xs"><?php echo _LANG("p_goruntule"); ?></a>
	<a href="temalar.php?tema=<?php echo $aktif_tema; ?>&sayfa=panel&islem=magazalar&sil=<?php echo $id; ?>" class="btn btn-danger btn-xs" onclick="return confirm('Dikkat: Bu işlemin geri dönüşü olmayacaktır. Mağazayı silerseniz bu mağaza ve bu mağazaya bağlı tüm ayarlar ve temsilci kayıtları silinecektir.')">Sil</a>

	</td>
</tr>
<?php }	?>
	</tbody>
</table>
</div>