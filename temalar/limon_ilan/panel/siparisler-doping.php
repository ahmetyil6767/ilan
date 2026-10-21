<?php 
if(count(get_included_files())==1) {die();}
@$guncelle=$_GET["guncelle"];
@$onay=$_GET["onay"];
@$sil=$_GET["sil"];
@$tip=$_GET["tip"];

if ($onay) {
	guncelle("siparisler","durum",1,$onay);
	$doping_sql=mysqli_query($baglan,"select * from dopingler where siparis_id='$onay'");
	while ($doping_dizi=mysqli_fetch_array($doping_sql)) {
		$onayla=$doping_dizi["id"];
		$gun=$doping_dizi["gun"];
		$doping=$doping_dizi["doping"];
		$ilan_id=$doping_dizi["ilan_id"];
		$tarih_saat=tarih_saat();
		$bitis_tarih_saat=date("Y-m-d H:i:s",strtotime("+$gun days"));
		if ($doping=="guncelim") {
			guncelle("icerik","tarih_saat",$tarih_saat,$ilan_id);
			guncelle("icerik","g_tarih_saat",$tarih_saat,$ilan_id);
		}
		$doping_onay=mysqli_query($baglan,"update dopingler set durum='1', tarih_saat='$tarih_saat', bitis_tarih_saat='$bitis_tarih_saat' where id='$onayla'");				
	}
	$_SESSION["mesaj"]=array(_LANG('pn_siparis_onaylandi_ve_dopingler_aktiflestirildi'),".");
	
	$siparis_detay_url=ozel_url("siparis")."?siparis_id=$onay";
	$siparis_uye_id=getir("siparisler","alici_id",$onay);
	bildirim_ekle(_LANG('pn_dopingleriniz_aktiflestirildi_detayli_bilgi_icin_a_href_siparis_detay_', array($siparis_detay_url)),$siparis_uye_id,1);

	// E-posta gönderimi başladı
	$mail_baslik=tema_ayar("mail_baslik");
	$site_url=site_url();		
	$siparis_uye_ad_soyad=getir("uyeler","ad_soyad",$siparis_uye_id);
	$siparis_uye_email_adresi=getir("uyeler","email_adresi",$siparis_uye_id);
	$ileti="
	<p>Merhaba <strong>$siparis_uye_ad_soyad</strong></p>
	<p>" . _LANG('hb_bu_e_posta_doping_siparisinizin_onaylandigini') . "</p>
	<p>Siteye giriş yapmak için <a href=\"$site_url/sayfa/giris-yap\">buraya</a> tıklayın.</p>";
	auto_mail_add($siparis_uye_ad_soyad,$siparis_uye_email_adresi,"Siparişiniz onaylandı! - $mail_baslik",$ileti);	
	// E-posta gönderimi bitti
	
	
	yonlendir("temalar.php?tema=$aktif_tema&sayfa=panel&islem=siparisler&tip=doping");
}
if ($sil) {
	mysqli_query($baglan,"delete from siparisler where id='$sil'");
	mysqli_query($baglan,"delete from ekstra_bilgi where id='$sil' and tip='siparis'");
	mysqli_query($baglan,"delete from dopingler where siparis_id='$sil'");
	$_SESSION["mesaj"]=array(_LANG('pn_siparis_ve_siparise_bagli_kayitlar_basariyla_silindi'),".");
	yonlendir("temalar.php?tema=$aktif_tema&sayfa=panel&islem=siparisler&tip=doping");
}

$sql=mysqli_query($baglan,"select * from siparisler where tip='doping' order by tarih_saat desc");
$toplam=mysqli_num_rows($sql);

$pasif_sql=mysqli_query($baglan,"select * from siparisler where tip='doping' and durum='0' order by tarih_saat desc");
$pasif_toplam=mysqli_num_rows($pasif_sql);

@$toplu_sil=$_POST["toplu_sil"];
if ($toplu_sil) {
	while ($pasif_dizi=mysqli_fetch_array($pasif_sql)) {
		$pasif_siparis_id=$pasif_dizi["id"];
		mysqli_query($baglan,"delete from siparisler where id='$pasif_siparis_id'");
		mysqli_query($baglan,"delete from ekstra_bilgi where id='$pasif_siparis_id' and tip='siparis'");
		mysqli_query($baglan,"delete from dopingler where siparis_id='$pasif_siparis_id'");
	}
	$_SESSION["mesaj"]=array(_LANG('pn_tum_bekleyen_doping_siparisleri_basariyla_silindi'),".");
	yonlendir("temalar.php?tema=$aktif_tema&sayfa=panel&islem=siparisler&tip=doping");		
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
<h3 class="pull-left bolum-baslik text-uppercase"><?php echo _LANG("pn_doping_siparisleri"); ?></h3>
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
			<th><?php echo _LANG("search_found_ads"); ?></th>
			<th>Tutar</th>
			<th>Tip</th>
			<th><?php echo _LANG("pn_secilen_doping"); ?></th>
			<th>Tarih Saat</th>
			<th>Durum</th>
			<th><?php echo _LANG("general_options"); ?></th>
		</tr>
	</thead>
	<tbody>
<?php 
while ($dizi=mysqli_fetch_array($sql)) {
	$id=$dizi["id"];
	$alici_id=$dizi["alici_id"];
	$ilan_id=$dizi["ilan_id"];
	$tutar=$dizi["tutar"];
	$tarih_saat=$dizi["tarih_saat"];
	$tip=$dizi["tip"];
	$durum=$dizi["durum"];
	$doping_say=mysqli_num_rows(mysqli_query($baglan,"select * from dopingler where siparis_id='$id'"));
?>
<tr>
	<th><?php echo $id; ?></th>
	<td><a href="<?php echo lpanel_uye_profil_url($alici_id); ?>" target="_blank"><?php echo getir("uyeler","ad_soyad",$alici_id); ?> <i class="fa fa-external-link"></i></a></td>
	<td><a href="<?php echo link_getir($ilan_id); ?>" target="_blank"><?php echo getir("icerik","baslik",$ilan_id); ?> <i class="fa fa-external-link"></i></a></td>
	<td><?php echo $tutar; ?> GMD</td>
	<td><?php echo $tip; ?></td>
	<td><?php echo $doping_say; ?></td>
	<td><?php echo $tarih_saat; ?></td>
	<td><?php if ($durum==1) {echo "<span class='label label-success'>Onaylandı</span>";} else {echo "<span class='label label-danger'>Ödeme bekleniyor</span>";} ?></td>
	<td>
	<?php if ($durum==0) { ?>
	<a href="temalar.php?tema=<?php echo $aktif_tema; ?>&sayfa=panel&islem=siparisler&tip=doping&onay=<?php echo $id; ?>" onclick="return confirm('Siparişi onaylamak istediğinizden emin misiniz?')" class="btn btn-primary btn-xs"><?php echo _LANG("pn_hizli_onay"); ?></a>
	<?php } ?>
	<a href="temalar.php?tema=<?php echo $aktif_tema; ?>&sayfa=panel&islem=siparisler&tip=doping&sil=<?php echo $id; ?>" onclick="return confirm('Siparişi ve siparişe bağlı kayıtları silmek istediğinizden emin misiniz?')" class="btn btn-danger btn-xs">Sil</a>
	</td>
</tr>
<?php }	?>
	</tbody>
</table>
</div>
<?php eklenti_sayfalama($kac_sayfa,$page,1,"sayac","gunluk"); ?>

<div class="panel panel-default">
	<div><?php echo _LANG('hb_toplu_islemler'); ?></div>
	<div class="panel-body">
	<form action="" method="post">
		<?php if ($pasif_toplam>0) { ?>
			<p>Ödeme bekleyen <?php echo $pasif_toplam; ?> sipariş bulundu. Ödeme bekleyen tüm siparişleri tek seferde silebilirsiniz.</p>
			<button type="submit" name="toplu_sil" value="Sil" class="btn btn-danger" onclick="return confirm('Tüm bekleyen siparişler silinecek.');"><?php echo _LANG("pn_bekleyen_siparisleri_sil"); ?></button>
		<?php } else { ?>
			<div><?php echo _LANG('hb_uygulanabilir_toplu_islem_yok'); ?></div>
		<?php } ?>
	</form>
	</div>
</div>

<?php } else {echo "<div class='alert alert-danger'>Sipariş bulunamadı.</div>";} ?>
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
				
				if ($tip=="doping") {
					$durum_secenekler=options(_LANG('tx_pasif_0_onaylandi_1'),"|",":");
				} else if ($tip=="get") {
					$durum_secenekler=options(_LANG('tx_pasif_0_tamamlandi_1_odeme_onaylandi_2'),"|",":");
				}
				
				
				?>
			
				<?php form_group(array("tip"=>"text","id"=>"id","baslik"=>_LANG('pn_siparis_id'),"deger"=>$guncelle,"pasif"=>1)); ?>
				<?php form_group(array("tip"=>"text","id"=>"alici_id","baslik"=>_LANG('pn_alici_id'),"deger"=>$alici_id,"pasif"=>1)); ?>
				<?php form_group(array("tip"=>"text","id"=>"satici_id","baslik"=>_LANG('s_satici_adi'),"deger"=>$satici_id,"pasif"=>1)); ?>
				<?php form_group(array("tip"=>"text","id"=>"ilan_id","baslik"=>_LANG('pn_ilan_id'),"deger"=>$ilan_id,"pasif"=>1)); ?>
				<?php form_group(array("tip"=>"text","id"=>"tip","baslik"=>_LANG('pn_siparis_tipi'),"deger"=>$tip,"pasif"=>1)); ?>
				<?php form_group(array("tip"=>"text","id"=>"tip","baslik"=>"Adet","deger"=>$adet,"pasif"=>1)); ?>
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