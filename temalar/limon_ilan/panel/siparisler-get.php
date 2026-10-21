<?php 
if(count(get_included_files())==1) {die();}
@$guncelle=$_GET["guncelle"];
@$onay=$_GET["onay"];
@$sil=$_GET["sil"];
@$odeme_yapildi=$_GET["odeme_yapildi"];
if ($odeme_yapildi) {
	mysqli_query($baglan,"update siparisler set satici_odeme='1' where id='$odeme_yapildi'");
	$tarih_saat=tarih_saat();
	$odeme_tarihi_ekle=ekstra_bilgi_guncelle("odeme_tarihi",$tarih_saat,$odeme_yapildi,"siparis");
	$_SESSION["mesaj"]=array(_LANG('pn_odeme_bilgisi_guncellendi'),".");
	
	$siparis_satici_id=getir("siparisler","satici_id",$odeme_yapildi);
	$siparis_detay_url=ozel_url("siparis")."?siparis_id=$odeme_yapildi";
	bildirim_ekle(sprintf(_LANG('bnf_get_odemesi_gonderildi'), $siparis_detay_url),$siparis_satici_id,1);
			
	yonlendir("temalar.php?tema=$aktif_tema&sayfa=panel&islem=siparisler&tip=get");
}
if ($guncelle) {
	@$buton=$_POST["buton"];
	if ($buton) {
		$durum=$_POST["durum"];
		guncelle("siparisler","durum",$durum,$guncelle);
		$_SESSION["mesaj"]=array(_LANG('pn_siparis_guncellendi'),".");
		
		if ($durum==2) {
			$siparis_detay_url=ozel_url("siparis")."?siparis_id=$guncelle";
			$siparis_uye_id=getir("siparisler","alici_id",$guncelle);
			bildirim_ekle(_LANG('pn_get_siparisi_odemeniz_onaylandi_siparis_durumunuzu_gormek_icin_a_href_', array($siparis_detay_url)),$siparis_uye_id,1);

			// Stok güncelleniyor.
			$ilan_id=getir("siparisler","ilan_id",$guncelle);
			$stok=bilgi("stok",$ilan_id);
			$adet=ekstra_bilgi_getir("adet",$guncelle,"siparis");
			$yeni_stok=$stok-$adet;
			$stok_guncelle=bilgi_guncelle("stok",$yeni_stok,$ilan_id);
			// Stok güncellendi.
			
			$siparis_satici_id=getir("siparisler","satici_id",$guncelle);
			bildirim_ekle(_LANG('pn_get_siparisi_odemeniz_alindi_urunu_kargolayabilirsiniz_a_href_siparis_', array($siparis_detay_url,$guncelle)),$siparis_satici_id,1);
		}
		yonlendir("temalar.php?tema=$aktif_tema&sayfa=panel&islem=siparisler&tip=get");		
	}
}

if ($sil) {
	mysqli_query($baglan,"delete from siparisler where id='$sil'");
	mysqli_query($baglan,"delete from ekstra_bilgi where id='$sil' and tip='siparis'");
	mysqli_query($baglan,"delete from dopingler where siparis_id='$sil'");
	$_SESSION["mesaj"]=array(_LANG('pn_siparis_ve_siparise_bagli_kayitlar_basariyla_silindi'),".");
	yonlendir("temalar.php?tema=$aktif_tema&sayfa=panel&islem=siparisler&tip=get");
}
$sql=mysqli_query($baglan,"select * from siparisler where tip='get' order by id desc");
$toplam=mysqli_num_rows($sql);


$pasif_sql=mysqli_query($baglan,"select * from siparisler where tip='get' and durum='0' order by tarih_saat desc");
$pasif_toplam=mysqli_num_rows($pasif_sql);

@$toplu_sil=$_POST["toplu_sil"];
if ($toplu_sil) {
	while ($pasif_dizi=mysqli_fetch_array($pasif_sql)) {
		$pasif_siparis_id=$pasif_dizi["id"];
		mysqli_query($baglan,"delete from siparisler where id='$pasif_siparis_id'");
		mysqli_query($baglan,"delete from ekstra_bilgi where id='$pasif_siparis_id' and tip='siparis'");
		mysqli_query($baglan,"delete from dopingler where siparis_id='$pasif_siparis_id'");	
	}
	$_SESSION["mesaj"]=array(_LANG('pn_tum_bekleyen_get_siparisleri_basariyla_silindi'),".");
	yonlendir("temalar.php?tema=$aktif_tema&sayfa=panel&islem=siparisler&tip=get");		
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
} );
</script>	
<h3 class="pull-left bolum-baslik text-uppercase"><?php echo _LANG("pn_get_siparisleri"); ?></h3>
<form class="form-inline pull-right visible-md visible-lg" action="" method="post">
  <div class="form-group">
    <input type="text" class="form-control" id="kayit" name="kacarli" placeholder="<?php echo _LANG("p_listeleme_adedi"); ?>">
  </div>
  <button type="submit" name="kacarli_buton" class="btn btn-default" value="Onayla">Onayla</button>
</form>
<div class="clearfix mb20"></div>
<?php if (!$guncelle) { ?>
<?php if ($toplam>0) { ?>
<div class="table-responsive">
<table id="datatable" class="table table-bordered table-hover table-ozel" data-order='[[ 0, "desc" ]]' data-page-length='15'>
	<thead>
		<tr>
			<th width="10">ID</th>
			<th><?php echo _LANG("pn_alici_id"); ?></th>
			<th><?php echo _LANG("e_satici_id"); ?></th>
			<th><?php echo _LANG("pn_ilan_id"); ?></th>
			<th>Tutar</th>
			<th>Adet</th>
			<th><?php echo _LANG("pn_satici_kazanci"); ?></th>
			<th>Tip</th>
			<th>Tarih Saat</th>
			<th>Durum</th>
			<th><?php echo _LANG("pn_satici_durum"); ?></th>
			<th><?php echo _LANG("pn_alici_onay"); ?></th>
			<th><?php echo _LANG("s_satici_odemesi"); ?></th>
			<th><?php echo _LANG("general_options"); ?></th>
		</tr>
	</thead>
	<tbody>
<?php 
while ($dizi=mysqli_fetch_array($sql)) {
	$id=$dizi["id"];
	$alici_id=$dizi["alici_id"];
	$satici_id=$dizi["satici_id"];
	$ilan_id=$dizi["ilan_id"];
	$tutar=$dizi["tutar"];
	$adet=$dizi["adet"];
	$tarih_saat=$dizi["tarih_saat"];
	$tip=$dizi["tip"];
	$durum=$dizi["durum"];
	$satici_durum=$dizi["satici_durum"];
	$alici_onay=$dizi["alici_onay"];
	$satici_odeme=$dizi["satici_odeme"];
	
	$get_komisyon=tema_ayar("get_komisyon");
	$net_satici_kazanci=$tutar-$tutar/100*$get_komisyon;	
	
?>
<tr>
	<th><?php echo $id; ?></th>
	<td><a href="<?php echo lpanel_uye_profil_url($alici_id); ?>"><?php echo getir("uyeler","ad_soyad",$alici_id); ?></a></td>
	<td><?php if ($tip=="get") { ?><a href="<?php echo lpanel_uye_profil_url($satici_id); ?>"><?php echo getir("uyeler","ad_soyad",$satici_id); ?></a><?php } else {echo "-";} ?></td>
	<td><?php echo $ilan_id; ?></td>
	<td><?php echo $tutar; ?> GMD</td>
	<td><?php echo ekstra_bilgi_getir("adet",$id,"siparis"); ?></td>
	<td><?php echo $net_satici_kazanci; ?> GMD</td>
	<td><?php echo $tip; ?></td>
	<td><?php echo $tarih_saat; ?></td>
	<td><?php 
		if ($durum==0) {
			echo "<span class='label label-danger'><?php echo _LANG('order_pending'); ?></span>";
		} else if ($durum==1) {
			echo "<span class='label label-success'><?php echo _LANG('order_completed'); ?></span>";
		} else if ($durum==2) {
			echo "<span class='label label-primary'><?php echo _LANG('s_odeme_onaylandi'); ?></span>";
		}
		?></td>
	<td>
	<?php 
	if ($satici_durum==0 and $siparis_durum==2) {
		echo "_LANG('s_siparis_alindi')";
	} else if ($satici_durum==1) {
		echo "<span class='label label-warning borderless'>Tedarik ediliyor</span>";
	} else if ($satici_durum==2) {
		echo "_LANG('s_kargoya_hazir')";
	} else if ($satici_durum==3) {
		$kargo_tarihi=ekstra_bilgi_getir("kargo_tarihi",$id,"siparis");
		echo "<span class='label label-success borderless'>Kargoya verildi: $kargo_tarihi</span>";
	} else if ($satici_durum==4) {
		echo "_LANG('order_cancelled')";
	} 
	?>
	</td>
	<td><?php if ($satici_durum==3) {if ($alici_onay==0) {echo "<span class='label label-danger'>Onay bekleniyor</span>";} else {
		$kargo_tarihi=ekstra_bilgi_getir("kargo_tarihi",$id,"siparis");
	echo "<span class='label label-success'>Teslim alındı: $kargo_tarihi</span>";} } ?></td>
	<td><?php 
	if ($alici_onay==1) {
		if ($satici_odeme==1) {
			$odeme_tarihi=ekstra_bilgi_getir("odeme_tarihi",$id,"siparis");
			echo "<span class='label label-success'>Ödeme yapıldı: $odeme_tarihi</span>";
		} else {echo "_LANG('order_unpaid')";} 
	}
	?></td>
	<td>
	<?php if ($alici_onay==1 and $satici_odeme!=1) { ?>
	<a href="temalar.php?tema=<?php echo $aktif_tema; ?>&sayfa=panel&islem=siparisler&tip=get&odeme_yapildi=<?php echo $id; ?>" onclick="return confirm('Ödemeyi yaptığınızı onaylayın. Bu butona bastıktan sonra satıcıya bildirim gönderilecektir.')" class="btn btn-success btn-xs"><?php echo _LANG("pn_satici_odemesi_yapildi"); ?></a>
	<?php } ?>
	<a href="temalar.php?tema=<?php echo $aktif_tema; ?>&sayfa=panel&islem=siparisler&tip=get&guncelle=<?php echo $id; ?>" class="btn btn-default btn-xs"><?php echo _LANG("general_update"); ?></a>
	
	<a href="temalar.php?tema=<?php echo $aktif_tema; ?>&sayfa=panel&islem=siparisler&tip=get&sil=<?php echo $id; ?>" onclick="return confirm('_LANG(\'tx_siparis_silme_onayi\')')" class="btn btn-danger btn-xs">Sil</a>
	</td>
</tr>
<?php }	?>
	</tbody>
</table>
</div>

<div class="panel panel-default">
	<div><?php echo _LANG('hb_toplu_islemler'); ?></div>
	<div class="panel-body">
	<form action="" method="post">
		<?php if ($pasif_toplam>0) { ?>
			<p>Ödeme bekleyen <?php echo $pasif_toplam; ?> sipariş bulundu. Ödeme bekleyen tüm siparişleri tek seferde silebilirsiniz.</p>
			<button type="submit" name="toplu_sil" value="Sil" class="btn btn-danger" onclick="return confirm(_LANG('tx_siparis_silme_onayi'));"><?php echo _LANG("pn_bekleyen_siparisleri_sil"); ?></button>
		<?php } else { ?>
			<div><?php echo _LANG('hb_uygulanabilir_toplu_islem_yok'); ?></div>
		<?php } ?>
	</form>
	</div>
</div>

<?php } else {echo "_LANG('order_not_found')";} ?>
<?php } else { ?>
<div class="row">
	<div class="col-md-20">
		<div class="panel panel-default">
			<div><?php echo _LANG('hb_siparis_guncelle'); ?></div>
			<div class="panel-body">
			<form action="" method="post">
				<?php 
				$alici_id=getir("siparisler","alici_id",$guncelle);
				$satici_id=getir("siparisler","satici_id",$guncelle);
				$ilan_id=getir("siparisler","ilan_id",$guncelle);
				$tutar=getir("siparisler","tutar",$guncelle);
				$tip=getir("siparisler","tip",$guncelle);
				$durum=getir("siparisler","durum",$guncelle);
				$adet=ekstra_bilgi_getir("adet",$guncelle,"siparis");
				$durum_secenekler=options(_LANG('tx_odeme_bekleniyor_0_odeme_onaylandi_2_tamamland'),"|",":");
				?>
			
				<?php form_group(array("tip"=>"text","id"=>"id","baslik"=>_LANG('pn_siparis_id'),"deger"=>$guncelle,"pasif"=>1)); ?>
				<?php form_group(array("tip"=>"text","id"=>"alici","baslik"=>_LANG('s_alici'),"deger"=>getir("uyeler","ad_soyad",$alici_id),"pasif"=>1)); ?>
				<?php form_group(array("tip"=>"text","id"=>"satici","baslik"=>_LANG('s_satici'),"deger"=>getir("uyeler","ad_soyad",$satici_id),"pasif"=>1)); ?>
				<?php form_group(array("tip"=>"text","id"=>"ilan","baslik"=>_LANG('search_found_ads'),"deger"=>getir("icerik","baslik",$ilan_id),"pasif"=>1)); ?>
				<?php form_group(array("tip"=>"text","id"=>"adet","baslik"=>"Adet","deger"=>$adet,"pasif"=>1)); ?>
				<?php form_group(array("tip"=>"select","id"=>"durum","baslik"=>"Durum","deger"=>$durum,"secenekler"=>$durum_secenekler)); ?>
				<?php form_buton("buton",_LANG('tx_guncelle'),"primary"); ?>
			
			
			</form>
			</div>
		</div>
	
	
	</div>
	<div class="col-md-40">
	
	
	</div>
</div>

<?php } ?>