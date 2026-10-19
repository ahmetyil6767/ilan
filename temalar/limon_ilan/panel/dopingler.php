<?php 
if(count(get_included_files())==1) {die();}
@$onayla=$_POST["onayla"];
@$sil=$_POST["sil"];
@$pasiflestir=$_POST["pasiflestir"];
@$toplu_onay=$_POST["toplu_onay"];
if ($onayla) {
	$gun=getir("dopingler","gun",$onayla);
	$doping=getir("dopingler","doping",$onayla);
	$tarih_saat=tarih_saat();
	$bitis_tarih_saat=date("Y-m-d H:i:s",strtotime("+$gun days"));
	if ($doping=="guncelim") {
		$ilan_id=getir("dopingler","ilan_id",$onayla);
		guncelle("icerik","tarih_saat",$tarih_saat,$ilan_id);
		guncelle("icerik","g_tarih_saat",$tarih_saat,$ilan_id);
	}
	$doping_onay=mysqli_query($baglan,"update dopingler set durum='1', tarih_saat='$tarih_saat', bitis_tarih_saat='$bitis_tarih_saat' where id='$onayla'");
	
	$b_ilan_id=getir("dopingler","ilan_id",$onayla);
	$b_siparis_id=getir("dopingler","siparis_id",$onayla);
	$b_yazar=getir("icerik","yazar",$b_ilan_id);
	$siparis_detay_url=ozel_url("siparis")."?siparis_id=$b_siparis_id";
	bildirim_ekle(_LANG('pn_doping_siparisiniz_onaylandi_detayli_bilgi_icin_a_href_siparis_detay_u', array($siparis_detay_url)),$b_yazar,1);
}
if ($toplu_onay) {
	$pasif_sql=mysqli_query($baglan,"select * from dopingler where durum='0' or durum='2' order by id desc");
	while ($pasif_doping_dizi=mysqli_fetch_array($pasif_sql)) {
		$pasif_doping_id=$pasif_doping_dizi["id"];	
		$gun=getir("dopingler","gun",$pasif_doping_id);
		$doping=getir("dopingler","doping",$pasif_doping_id);
		$tarih_saat=tarih_saat();
		$bitis_tarih_saat=date("Y-m-d H:i:s",strtotime("+$gun days"));
		if ($doping=="guncelim") {
			$ilan_id=getir("dopingler","ilan_id",$pasif_doping_id);
			guncelle("icerik","tarih_saat",$tarih_saat,$ilan_id);
			guncelle("icerik","g_tarih_saat",$tarih_saat,$ilan_id);
		}
		$doping_onay=mysqli_query($baglan,"update dopingler set durum='1', tarih_saat='$tarih_saat', bitis_tarih_saat='$bitis_tarih_saat' where id='$pasif_doping_id'");
		
		$b_ilan_id=getir("dopingler","ilan_id",$pasif_doping_id);
		$b_siparis_id=getir("dopingler","siparis_id",$pasif_doping_id);
		$b_yazar=getir("icerik","yazar",$b_ilan_id);
		$siparis_detay_url=ozel_url("siparis")."?siparis_id=$b_siparis_id";
		bildirim_ekle(_LANG('pn_doping_siparisiniz_onaylandi_detayli_bilgi_icin_a_href_siparis_detay_u', array($siparis_detay_url)),$b_yazar,1);
	}
	$_SESSION["mesaj"]=array(_LANG('pn_pasif_dopingler_toplu_olarak_aktiflestirildi'),".");
	yonlendir("temalar.php?tema=$aktif_tema&sayfa=panel&islem=dopingler");	
	
}
if ($sil) {
	$doping_sil=mysqli_query($baglan,"delete from dopingler where id='$sil'");
}
if ($pasiflestir) {
	$doping_pasiflestir=mysqli_query($baglan,"update dopingler set durum='0' where id='$pasiflestir'");
}
$sql=mysqli_query($baglan,"select * from dopingler order by id desc");
$toplam=mysqli_num_rows($sql);

$pasif_sql=mysqli_query($baglan,"select * from dopingler where durum='0' or durum='2' order by id desc");
$pasif_toplam=mysqli_num_rows($pasif_sql);

@$ekle=$_GET["ekle"];
if ($ekle==1) {
	@$ilan_id=$_GET["ilan_id"];

	@$buton=$_POST["buton"];
	if ($buton) {
		$ilan_id=$_POST["ilan_id"];
		$doping=$_POST["doping"];
		$gun=$_POST["gun"];
		$tarih_saat=tarih_saat();
		$bitis_tarih_saat=date("Y-m-d H:i:s",strtotime("+$gun days"));
		
		$ekle=mysqli_query($baglan,"insert into dopingler set durum='1', doping='$doping', tarih_saat='$tarih_saat', bitis_tarih_saat='$bitis_tarih_saat', gun='$gun', ilan_id='$ilan_id'");
		if ($doping=="guncelim") {
			$ilan_id=getir("dopingler","ilan_id",$onayla);
			guncelle("icerik","tarih_saat",$tarih_saat,$ilan_id);
			guncelle("icerik","g_tarih_saat",$tarih_saat,$ilan_id);
		}
		$_SESSION["mesaj"]=array(_LANG('pn_doping_uygulama_basarili'),".");
		yonlendir("temalar.php?tema=$aktif_tema&sayfa=panel&islem=dopingler");
	}
}
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
});
</script>

<?php if ($ekle==1) { ?>
<h3 class="pull-left bolum-baslik text-uppercase"><?php echo _LANG("pn_doping_ekle"); ?></h3>
<a href="temalar.php?tema=<?php echo $aktif_tema; ?>&sayfa=panel&islem=dopingler" class="btn btn-primary"><?php echo _LANG("pn_dopinglere_don"); ?></a>
<div class="clearfix mb20"></div>
<div class="panel">
	<div class="panel-body">
		<form action="" method="post">
			<?php
			$ilan_seciniz[]=array("deger"=>"","baslik"=>_LANG('filter_select'));
			$sql=mysqli_query($baglan,"SELECT * from icerik where tip='ilan' order by tarih_saat desc");
			while ($dizi=mysqli_fetch_array($sql)) {
				$id=$dizi["id"];
				$baslik=$dizi["baslik"];
				$ilan_seciniz[]=array("deger"=>$id,"baslik"=>$baslik);
			}
			form_group(array("tip"=>"select","id"=>"ilan_id","baslik"=>_LANG('search_found_ads'),"secenekler"=>$ilan_seciniz,"deger"=>$ilan_id,"gerekli"=>1)); 

			$doping_seciniz[]=array("deger"=>"anasayfa_vitrini","baslik"=>"Anasayfa Vitrini");
			$doping_seciniz[]=array("deger"=>"kategori_vitrini","baslik"=>"Kategori Vitrini");
			$doping_seciniz[]=array("deger"=>"acil_acil","baslik"=>"Acil Acil");
			$doping_seciniz[]=array("deger"=>"kalin_yazi","baslik"=>_LANG('listing_thick'));
			$doping_seciniz[]=array("deger"=>"guncelim","baslik"=>_LANG('listing_news'));
		
			form_group(array("tip"=>"select","id"=>"doping","baslik"=>_LANG('pn_doping_seciniz'),"secenekler"=>$doping_seciniz,"gerekli"=>1));							

			$standart_gecerlilik=tema_ayar("doping_gecerlilik");
			form_group(array("tip"=>"text","id"=>"gun","baslik"=>_LANG('s_gun_sayisi'),"deger"=>$standart_gecerlilik));
			
			?>
			<?php form_buton("buton","Doping Ekle","primary"); ?>
		</form>
	</div>
</div>
<?php } else { ?>
<h3 class="pull-left bolum-baslik text-uppercase"><?php echo _LANG("pn_dopingler"); ?></h3>
<a href="temalar.php?tema=<?php echo $aktif_tema; ?>&sayfa=panel&islem=dopingler&ekle=1" class="btn btn-primary">Doping Ekle</a>

<div class="clearfix mb20"></div>
<?php if ($toplam>0) { ?>
<form action="" method="post">
<div class="table-responsive">
<table id="datatable" class="table table-bordered table-hover table-ozel" data-order='[[ 0, "desc" ]]' data-page-length='15'>
	<thead>
		<tr>
			<th width="10">ID</th>
			<th><?php echo _LANG("search_found_ads"); ?></th>
			<th>Doping</th>
			<th><?php echo _LANG("s_gun_sayisi"); ?></th>
			<th><?php echo _LANG("pn_kalan_gun_sayisi"); ?></th>
			<th><?php echo _LANG("pn_bas_tarih_saat"); ?></th>
			<th>Bit. Tarih Saat</th>
			<th><?php echo _LANG("order_title"); ?></th>
			<th>Durum</th>
			<th><?php echo _LANG("general_options"); ?></th>
		</tr>
	</thead>
	<tbody>
<?php 

while ($dizi=mysqli_fetch_array($sql)) {
	$id=$dizi["id"];
	$ilan_id=$dizi["ilan_id"];
	$doping=$dizi["doping"];
	$gun=$dizi["gun"];
	$tarih_saat=$dizi["tarih_saat"];
	$bitis_tarih_saat=$dizi["bitis_tarih_saat"];
	$siparis_id=$dizi["siparis_id"];
	$durum=$dizi["durum"];
?>
<tr>
	<th><?php echo $id; ?></th>
	<td><a href="<?php echo link_getir($ilan_id); ?>" target="_blank"><?php echo getir("icerik","baslik",$ilan_id); ?> <i class="fa fa-external-link"></i></a></td>
	<td><?php echo $doping; ?></td>
	<td><?php echo $gun; ?></td>
	<td><?php echo 
		tarih_fark($bitis_tarih_saat,$tarih_saat,array("birim"=>"gun"));
	?></td>
	<td><?php echo $tarih_saat; ?></td>
	<td><?php echo $bitis_tarih_saat; ?></td>
	<td>#<?php echo $siparis_id; ?></td>
	<td><?php if ($durum==1) {echo "<span class='label label-success'>Aktif</span>";} else if ($durum==2) {echo "<span class='label label-danger'>Süresi doldu</span>";} else {echo "<span class='label label-danger'>Pasif</span>";} ?></td>
	<td>
	<?php if ($durum!=1) { ?><button type="submit" name="onayla" class="btn btn-primary btn-xs" value="<?php echo $id; ?>">Onayla</button><?php } ?>
	<?php if ($durum==1) { ?><button type="submit" name="pasiflestir" class="btn btn-warning btn-xs" value="<?php echo $id; ?>"><?php echo _LANG("p_pasiflestir"); ?></button><?php } ?>
	<button type="submit" name="sil" class="btn btn-danger btn-xs" value="<?php echo $id; ?>" onclick="return confirm('Dopingi silmek istediğinizden emin misiniz?')">Sil</button>
	</td>
</tr>
<?php }	?>
	</tbody>
</table>
</div>
</form>


<div class="panel panel-default">
	<div><?php echo _LANG('hb_toplu_islemler'); ?></div>
	<div class="panel-body">
	<form action="" method="post">
		<?php if ($pasif_toplam>0) { ?>
			<p>Doping süresi dolmuş <?php echo $pasif_toplam; ?> ilan var. Tümünü toplu olarak aktifleştirebilirsiniz. Bu işlem tüm ilanlar için bugünden başlayarak mevcut gün sayısı kadar doping tanımlaması yapar.</p>
			<button type="submit" name="toplu_onay" value="Onayla" class="btn btn-success" onclick="return confirm('Tüm pasif doping istekleri aktifleştirilecek.');"><?php echo _LANG("pn_tumunu_aktiflestir"); ?></button>
		<?php } else { ?>
			<div><?php echo _LANG('hb_uygulanabilir_toplu_islem_yok'); ?></div>
		<?php } ?>
	</form>
	</div>
</div>

<?php } else {echo "<div class='alert alert-danger'>Doping kaydı bulunamadı.</div>";} } ?>