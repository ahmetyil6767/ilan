<?php 
if(count(get_included_files())==1) {die();}
?>
<h4><?php echo _LANG('hb_magaza_acilabilir_kategoriler'); ?></h4>
<table id="datatable" class="table table-bordered table-hover table-ozel" data-order='[[ 0, "desc" ]]' data-page-length='15'>
	<thead>
		<tr>
			<th width="10">ID</th>
			<th><?php echo _LANG("pn_kategori_adi"); ?></th>
			<th><?php echo _LANG("general_options"); ?></th>
		</tr>
	</thead>
	<tbody>
	<?php 
		$sql=mysqli_query($baglan,"select id from ekstra_bilgi where anahtar='magaza' and deger='1' and tip='kategori'");
		while ($dizi=mysqli_fetch_array($sql)) {
		$id=$dizi["id"];
		$baslik=getir("kategoriler","baslik",$id);
	?>
	<tr>
		<th><?php echo $id; ?></th>
		<td><?php echo $baslik; ?></td>
		<td>
			<a href="kategoriler.php?tip=<?php echo getir("kategoriler","tip",$id); ?>&sinif=<?php echo getir("kategoriler","sinif",$id); ?>&islem=guncelle&id=<?php echo $id; ?>" class="btn btn-default btn-xs"><?php echo _LANG("general_update"); ?></a>
		</td>
	</tr>
	<?php }	?>
	</tbody>
</table>

<h4><?php echo _LANG('hb_taksit_sayisi_sinirlandirilan_kategoriler'); ?></h4>
<table id="datatable" class="table table-bordered table-hover table-ozel" data-order='[[ 0, "desc" ]]' data-page-length='15'>
	<thead>
		<tr>
			<th width="10">ID</th>
			<th><?php echo _LANG("pn_kategori_adi"); ?></th>
			<th><?php echo _LANG("pn_taksit_sayisi"); ?></th>
			<th><?php echo _LANG("general_options"); ?></th>
		</tr>
	</thead>
	<tbody>
	<?php 
		$sql=mysqli_query($baglan,"select id, deger from ekstra_bilgi where anahtar='taksit' and deger!='9' and deger!='' and tip='kategori'");
		while ($dizi=mysqli_fetch_array($sql)) {
		$id=$dizi["id"];
		$deger=$dizi["deger"];
		$baslik=getir("kategoriler","baslik",$id);
	?>
	<tr>
		<th><?php echo $id; ?></th>
		<td><?php echo $baslik; ?></td>
		<td><?php echo $deger; ?></td>
		<td>
			<a href="kategoriler.php?tip=<?php echo getir("kategoriler","tip",$id); ?>&sinif=<?php echo getir("kategoriler","sinif",$id); ?>&islem=guncelle&id=<?php echo $id; ?>" class="btn btn-default btn-xs"><?php echo _LANG("general_update"); ?></a>
		</td>
	</tr>
	<?php }	?>
	</tbody>
</table>

<h4><?php echo _LANG('hb_fiyat_zorunlu_kategoriler'); ?></h4>
<table id="datatable" class="table table-bordered table-hover table-ozel" data-order='[[ 0, "desc" ]]' data-page-length='15'>
	<thead>
		<tr>
			<th width="10">ID</th>
			<th><?php echo _LANG("pn_kategori_adi"); ?></th>
			<th><?php echo _LANG("general_options"); ?></th>
		</tr>
	</thead>
	<tbody>
	<?php 
		$sql=mysqli_query($baglan,"select id from ekstra_bilgi where anahtar='fiyat_zorunlu' and deger='1' and tip='kategori'");
		while ($dizi=mysqli_fetch_array($sql)) {
		$id=$dizi["id"];
		$baslik=getir("kategoriler","baslik",$id);
	?>
	<tr>
		<th><?php echo $id; ?></th>
		<td><?php echo $baslik; ?></td>
		<td>
			<a href="kategoriler.php?tip=<?php echo getir("kategoriler","tip",$id); ?>&sinif=<?php echo getir("kategoriler","sinif",$id); ?>&islem=guncelle&id=<?php echo $id; ?>" class="btn btn-default btn-xs"><?php echo _LANG("general_update"); ?></a>
		</td>
	</tr>
	<?php }	?>
	</tbody>
</table>

<h4><?php echo _LANG('hb_ozel_alan_tanimlanan_kategoriler'); ?></h4>
<table id="datatable" class="table table-bordered table-hover table-ozel" data-order='[[ 0, "desc" ]]' data-page-length='15'>
	<thead>
		<tr>
			<th width="10">ID</th>
			<th><?php echo _LANG("pn_kategori_adi"); ?></th>
			<th><?php echo _LANG("general_options"); ?></th>
		</tr>
	</thead>
	<tbody>
	<?php 
		$sql=mysqli_query($baglan,"select id from ekstra_bilgi where anahtar='ozel_alanlar' and deger!='' and tip='kategori'");
		while ($dizi=mysqli_fetch_array($sql)) {
		$id=$dizi["id"];
		$baslik=getir("kategoriler","baslik",$id);
	?>
	<tr>
		<th><?php echo $id; ?></th>
		<td><?php echo $baslik; ?></td>
		<td>
			<a href="kategoriler.php?tip=<?php echo getir("kategoriler","tip",$id); ?>&sinif=<?php echo getir("kategoriler","sinif",$id); ?>&islem=guncelle&id=<?php echo $id; ?>" class="btn btn-default btn-xs"><?php echo _LANG("general_update"); ?></a>
		</td>
	</tr>
	<?php }	?>
	</tbody>
</table>

<h4><?php echo _LANG('hb_teknik_ozellik_tanimlanan_kategoriler'); ?></h4>
<table id="datatable" class="table table-bordered table-hover table-ozel" data-order='[[ 0, "desc" ]]' data-page-length='15'>
	<thead>
		<tr>
			<th width="10">ID</th>
			<th><?php echo _LANG("pn_kategori_adi"); ?></th>
			<th><?php echo _LANG("general_options"); ?></th>
		</tr>
	</thead>
	<tbody>
	<?php 
		$sql=mysqli_query($baglan,"select id from ekstra_bilgi where anahtar='teknik_ozellikler' and deger!='' and tip='kategori'");
		while ($dizi=mysqli_fetch_array($sql)) {
		$id=$dizi["id"];
		$baslik=getir("kategoriler","baslik",$id);
	?>
	<tr>
		<th><?php echo $id; ?></th>
		<td><?php echo $baslik; ?></td>
		<td>
			<a href="kategoriler.php?tip=<?php echo getir("kategoriler","tip",$id); ?>&sinif=<?php echo getir("kategoriler","sinif",$id); ?>&islem=guncelle&id=<?php echo $id; ?>" class="btn btn-default btn-xs"><?php echo _LANG("general_update"); ?></a>
		</td>
	</tr>
	<?php }	?>
	</tbody>
</table>

<h4><?php echo _LANG('hb_filtre_secenegi_kategoriler'); ?></h4>
<table id="datatable" class="table table-bordered table-hover table-ozel" data-order='[[ 0, "desc" ]]' data-page-length='15'>
	<thead>
		<tr>
			<th width="10">ID</th>
			<th><?php echo _LANG("pn_kategori_adi"); ?></th>
			<th><?php echo _LANG("general_options"); ?></th>
		</tr>
	</thead>
	<tbody>
	<?php 
		$sql=mysqli_query($baglan,"select id from ekstra_bilgi where anahtar='filtreler' and deger!='' and tip='kategori'");
		while ($dizi=mysqli_fetch_array($sql)) {
		$id=$dizi["id"];
		$baslik=getir("kategoriler","baslik",$id);
	?>
	<tr>
		<th><?php echo $id; ?></th>
		<td><?php echo $baslik; ?></td>
		<td>
			<a href="kategoriler.php?tip=<?php echo getir("kategoriler","tip",$id); ?>&sinif=<?php echo getir("kategoriler","sinif",$id); ?>&islem=guncelle&id=<?php echo $id; ?>" class="btn btn-default btn-xs"><?php echo _LANG("general_update"); ?></a>
		</td>
	</tr>
	<?php }	?>
	</tbody>
</table>


<h4><?php echo _LANG('hb_tablo_detayi_tanimlanan_kategoriler'); ?></h4>
<table id="datatable" class="table table-bordered table-hover table-ozel" data-order='[[ 0, "desc" ]]' data-page-length='15'>
	<thead>
		<tr>
			<th width="10">ID</th>
			<th><?php echo _LANG("pn_kategori_adi"); ?></th>
			<th><?php echo _LANG("general_options"); ?></th>
		</tr>
	</thead>
	<tbody>
	<?php 
		$sql=mysqli_query($baglan,"select id from ekstra_bilgi where anahtar='detaylar' and deger!='' and tip='kategori'");
		while ($dizi=mysqli_fetch_array($sql)) {
		$id=$dizi["id"];
		$baslik=getir("kategoriler","baslik",$id);
	?>
	<tr>
		<th><?php echo $id; ?></th>
		<td><?php echo $baslik; ?></td>
		<td>
			<a href="kategoriler.php?tip=<?php echo getir("kategoriler","tip",$id); ?>&sinif=<?php echo getir("kategoriler","sinif",$id); ?>&islem=guncelle&id=<?php echo $id; ?>" class="btn btn-default btn-xs"><?php echo _LANG("general_update"); ?></a>
		</td>
	</tr>
	<?php }	?>
	</tbody>
</table>

<h4><?php echo _LANG('hb_sekme_tab_tanimlanan_kategoriler'); ?></h4>
<table id="datatable" class="table table-bordered table-hover table-ozel" data-order='[[ 0, "desc" ]]' data-page-length='15'>
	<thead>
		<tr>
			<th width="10">ID</th>
			<th><?php echo _LANG("pn_kategori_adi"); ?></th>
			<th><?php echo _LANG("general_options"); ?></th>
		</tr>
	</thead>
	<tbody>
	<?php 
		$sql=mysqli_query($baglan,"select id from ekstra_bilgi where anahtar='tab' and deger!='' and tip='kategori'");
		while ($dizi=mysqli_fetch_array($sql)) {
		$id=$dizi["id"];
		$baslik=getir("kategoriler","baslik",$id);
	?>
	<tr>
		<th><?php echo $id; ?></th>
		<td><?php echo $baslik; ?></td>
		<td>
			<a href="kategoriler.php?tip=<?php echo getir("kategoriler","tip",$id); ?>&sinif=<?php echo getir("kategoriler","sinif",$id); ?>&islem=guncelle&id=<?php echo $id; ?>" class="btn btn-default btn-xs"><?php echo _LANG("general_update"); ?></a>
		</td>
	</tr>
	<?php }	?>
	</tbody>
</table>

<h4><?php echo _LANG('hb_kategoriye_ozel_reklam_eklenen_kategoriler'); ?></h4>
<table id="datatable" class="table table-bordered table-hover table-ozel" data-order='[[ 0, "desc" ]]' data-page-length='15'>
	<thead>
		<tr>
			<th width="10">ID</th>
			<th><?php echo _LANG("pn_kategori_adi"); ?></th>
			<th><?php echo _LANG("general_options"); ?></th>
		</tr>
	</thead>
	<tbody>
	<?php 
		$sql=mysqli_query($baglan,"select id from ekstra_bilgi where anahtar='ilan_sag_reklam' and deger!='' and tip='kategori'");
		while ($dizi=mysqli_fetch_array($sql)) {
		$id=$dizi["id"];
		$baslik=getir("kategoriler","baslik",$id);
	?>
	<tr>
		<th><?php echo $id; ?></th>
		<td><?php echo $baslik; ?></td>
		<td>
			<a href="kategoriler.php?tip=<?php echo getir("kategoriler","tip",$id); ?>&sinif=<?php echo getir("kategoriler","sinif",$id); ?>&islem=guncelle&id=<?php echo $id; ?>" class="btn btn-default btn-xs"><?php echo _LANG("general_update"); ?></a>
		</td>
	</tr>
	<?php }	?>
	</tbody>
</table>




