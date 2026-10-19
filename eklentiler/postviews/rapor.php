<?php 
if(count(get_included_files())==1) {die();}
$haric_tipler=rtrim(eklenti_ayar("haric_tip","postviews"),",");
$haric_tipler=explode(",",$haric_tipler);
$pw_say=explode(",",rtrim(eklenti_ayar_getir("say","postviews"),","));
$yazz=null;
?>
<script>
$(document).ready(function() {
	$('#datatable').DataTable({
		"responsive": true,
		"lengthMenu": [ 15, 25, 50, 100 ],
		"language": {
			"decimal":        "",
			"emptyTable":     "<?php echo _LANG('tx_sonuc_bulunamadi'); ?>",
			"info":           "Toplam _TOTAL_ içerik bulundu",
			"infoEmpty":      "<?php echo _LANG('tx_gosterilecek_hic_icerik_yok'); ?>",
			"infoFiltered":   "<?php echo _LANG('tx_max_toplam_icerikten'); ?>",
			"infoPostFix":    "",
			"thousands":      ",",
			"lengthMenu":     "<?php echo _LANG('tx_gosterilecek_icerik_menu'); ?>",
			"loadingRecords": "<?php echo _LANG('tx_yukleniyor'); ?>",
			"processing":     "<?php echo _LANG('tx_isleniyor'); ?>",
			"search":         "Ara:",
			"zeroRecords":    "<?php echo _LANG('tx_aramaniza_uygun_sonuc_bulunamadi'); ?>",
			"paginate": {
				"first":      "İlk",
				"last":       "Son",
				"next":       "Sonraki",
				"previous":   "Önceki"			
		}
		}
	});
} );
</script>	
<h3><?php echo _LANG('hb_postviews_raporlari'); ?></h3>
<a href="eklentiler.php?eklenti=postviews&islem=ayar" class="btn btn-default">Ayarlar</a>
<div class="clearfix mb20"></div>
<div class="table-responsive">
<table id="datatable" class="table table-bordered table-hover" data-order='[[2,"desc"]]' data-page-length='15'>
	<thead>
		<tr>
			<th width="10">ID</th>
			<th><?php echo _LANG('p_baslik'); ?></th>
			<th>Toplam</th>
			<?php if (in_array("(gun)",$pw_say)) { ?>
			<th><?php echo _LANG('e_bugun'); ?></th>
			<th><?php echo _LANG('e_dun'); ?></th>
			<th><?php echo _LANG('e_dun_gore'); ?></th>
			<?php } if (in_array("(hafta)",$pw_say)) { ?>
			<th>Bu hafta</th>
			<th><?php echo _LANG('e_gecen_hafta'); ?></th>
			<th><?php echo _LANG('e_gecen_haftaya_gore'); ?></th>
			<?php } if (in_array("(ay)",$pw_say)) { ?>
			<th>Bu ay</th>
			<th><?php echo _LANG('e_gecen_ay'); ?></th>
			<th><?php echo _LANG('e_gecen_aya_gore'); ?></th>
			<?php } ?>
		</tr>
	</thead>
	<tbody>	
<?php 
foreach ($haric_tipler as $tip) {
	$yazz.="tip!='$tip' and ";
}
$son=substr($yazz,0,-4);
$sql=mysqli_query($baglan,"select * from icerik where $son"); 
while ($dizi=mysqli_fetch_array($sql)) {
$id=$dizi["id"];
$baslik=getir("icerik","baslik",$id);
$yazar=getir("icerik","yazar",$id);

$hit=postviews($id);
$bugun_hit=postviews($id,"gun");
$dun_hit=postviews($id,"gun",date("d-m-Y",strtotime("-1 day")));
if (in_array("(hafta)",$pw_say)) {
	$bu_hafta_hit=postviews($id,"hafta");
	$gecen_hafta_hit=postviews($id,"hafta",date("W",strtotime("-1 week")));
}
if (in_array("(ay)",$pw_say)) {
	$bu_ay_hit=postviews($id,"ay");
	$gecen_ay_hit=postviews($id,"ay",date("m-Y",strtotime("-1 month")));
}
?>
<tr>
<th><?php echo $id; ?></th>
<td><a href="<?php echo link_getir($id); ?>" target="_blank"><?php echo $baslik; ?></a></td>
<td><?php echo $hit; ?></td>
<?php if (in_array("(gun)",$pw_say)) { ?>
<td <?php $title=null;$ikon=null; if ($dun_hit!=0) {if ($bugun_hit==$dun_hit) {echo "class='success'";$ikon="circle";$fark=$bugun_hit-$dun_hit;} else if ($bugun_hit>$dun_hit) {echo "class='success'";$ikon="arrow-up";$fark=$bugun_hit-$dun_hit; $title="+$fark";} else {echo "class='danger'";$ikon="arrow-down";$fark=$dun_hit-$bugun_hit; $title="-$fark";}} ?>><?php echo $bugun_hit; ?></td>
<td><?php echo $dun_hit; ?></td>
<td <?php $title=null;$ikon=null; if ($dun_hit!=0) {if ($bugun_hit==$dun_hit) {echo "class='success'";$ikon="circle";$fark=$bugun_hit-$dun_hit;} else if ($bugun_hit>$dun_hit) {echo "class='success'";$ikon="arrow-up";$fark=$bugun_hit-$dun_hit; $title="+$fark";} else {echo "class='danger'";$ikon="arrow-down";$fark=$dun_hit-$bugun_hit; $title="-$fark";}} ?>><?php if ($ikon) {echo "<i class='fa fa-$ikon' style='margin-left:10px;'></i><span style='font-size:11px;'>$title</span>";} ?></td>
<?php } if (in_array("(hafta)",$pw_say)) { ?>
<td <?php $title=null;$ikon=null; if ($gecen_hafta_hit!=0) {if ($bu_hafta_hit==$gecen_hafta_hit) {echo "class='success'";$ikon="circle";$fark=$bu_hafta_hit-$gecen_hafta_hit;} else if ($bu_hafta_hit>$gecen_hafta_hit) {echo "class='success'";$ikon="arrow-up";$fark=$bu_hafta_hit-$gecen_hafta_hit; $title="+$fark";} else {echo "class='danger'";$ikon="arrow-down";$fark=$gecen_hafta_hit-$bu_hafta_hit; $title="-$fark";}} ?>><?php echo $bu_hafta_hit; ?></td>
<td><?php echo $gecen_hafta_hit; ?></td>
<td <?php $title=null;$ikon=null; if ($gecen_hafta_hit!=0) {if ($bu_hafta_hit==$gecen_hafta_hit) {echo "class='success'";$ikon="circle";$fark=$bu_hafta_hit-$gecen_hafta_hit;} else if ($bu_hafta_hit>$gecen_hafta_hit) {echo "class='success'";$ikon="arrow-up";$fark=$bu_hafta_hit-$gecen_hafta_hit; $title="+$fark";} else {echo "class='danger'";$ikon="arrow-down";$fark=$gecen_hafta_hit-$bu_hafta_hit; $title="-$fark";}} ?>><?php if ($ikon) {echo "<i class='fa fa-$ikon' style='margin-left:10px;'></i><span style='font-size:11px;'>$title</span>";} ?></td>
<?php } if (in_array("(ay)",$pw_say)) { ?>
<td <?php $title=null;$ikon=null; if ($gecen_ay_hit!=0) {if ($bu_ay_hit==$gecen_ay_hit) {echo "class='success'";$ikon="circle";$fark=$bu_ay_hit-$gecen_ay_hit;} else if ($bu_ay_hit>$gecen_ay_hit) {echo "class='success'";$ikon="arrow-up";$fark=$bu_ay_hit-$gecen_ay_hit; $title="+$fark";} else {echo "class='danger'";$ikon="arrow-down";$fark=$gecen_ay_hit-$bu_ay_hit; $title="-$fark";}} ?>><?php echo $bu_ay_hit; ?></td>
<td><?php echo $gecen_ay_hit; ?></td>
<td <?php $title=null;$ikon=null; if ($gecen_ay_hit!=0) {if ($bu_ay_hit==$gecen_ay_hit) {echo "class='success'";$ikon="circle";$fark=$bu_ay_hit-$gecen_ay_hit;} else if ($bu_ay_hit>$gecen_ay_hit) {echo "class='success'";$ikon="arrow-up";$fark=$bu_ay_hit-$gecen_ay_hit; $title="+$fark";} else {echo "class='danger'";$ikon="arrow-down";$fark=$gecen_ay_hit-$bu_ay_hit; $title="-$fark";}} ?>><?php if ($ikon) {echo "<i class='fa fa-$ikon' style='margin-left:10px;'></i><span style='font-size:11px;'>$title</span>";} ?></td>
<?php } ?>
</tr>
<?php }	?>
</tbody>
</table>
</div>