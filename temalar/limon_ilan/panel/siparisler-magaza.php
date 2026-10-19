<?php 
if(count(get_included_files())==1) {die();}
@$guncelle=$_GET["guncelle"];
@$onay=$_GET["onay"];
@$sil=$_GET["sil"];
@$tip=$_GET["tip"];

if ($onay) {
	
    $limit_politikasi=limit_politikasi();
    
    if ($limit_politikasi==1) {
        
        $magaza_id=getir("siparisler","magaza_id",$onay);
        guncelle("magazalar","durum",1,$magaza_id);
        $bitis_tarihi=date("Y-m-d H:i:s",strtotime("+1 year"));
        magaza_bilgi_guncelle("paket_bitis_tarihi",$bitis_tarihi,$magaza_id);
        $_SESSION["mesaj"]=array(_LANG('pn_siparis_onaylandi_ve_magaza_aktiflestirildi'),".");
        
    } else {
        
        $magaza_id=getir("siparisler","magaza_id",$onay);
        $magaza_paket_id=getir("siparisler","paket_id",$onay);
        guncelle("magazalar","durum",1,$magaza_id);
        $bitis_tarihi=date("Y-m-d H:i:s",strtotime("+1 year"));
        magaza_bilgi_guncelle("paket_bitis_tarihi",$bitis_tarihi,$magaza_id);
        $ilan_limit=liste_bilgi("ilan_limit",$magaza_paket_id);
        $foto_limit=liste_bilgi("foto_limit",$magaza_paket_id);
        $danisman_limit=liste_bilgi("danisman_limit",$magaza_paket_id);
        $aninda_onay=liste_bilgi("aninda_onay",$magaza_paket_id);

        $mevcut_ilan_limiti=magaza_ilan_limiti("toplam",$magaza_id);
        $yeni_ilan_limiti=$mevcut_ilan_limiti+$ilan_limit;
        
        magaza_bilgi_guncelle("azalan_ilan_limit",$yeni_ilan_limiti,$magaza_id);
        magaza_bilgi_guncelle("azalan_foto_limit",$foto_limit,$magaza_id);
        magaza_bilgi_guncelle("azalan_danisman_limit",$danisman_limit,$magaza_id);
        magaza_bilgi_guncelle("azalan_aninda_onay",$aninda_onay,$magaza_id);
        magaza_bilgi_guncelle("paket",getir("siparisler","paket_id",$onay),$magaza_id);
        
        guncelle("magazalar","azalan_update",1,$magaza_id);
        $_SESSION["mesaj"]=array(_LANG('pn_siparis_onaylandi_ve_magaza_aktiflestirildi'),".");
    }

	$siparis_detay_url=ozel_url("ilan-ekle")."?magaza=$magaza_id";
	$siparis_uye_id=getir("siparisler","alici_id",$onay);
	bildirim_ekle(_LANG('pn_magaza_siparisiniz_onaylandi_magazaniza_ilan_eklemek_icin_a_href_sipar', array($siparis_detay_url)),$siparis_uye_id,1);
    
  guncelle("siparisler","durum",1,$onay);
	
	// E-posta gönderimi başladı
	$mail_baslik=tema_ayar("mail_baslik");
	$site_url=site_url();		
	$siparis_uye_ad_soyad=getir("uyeler","ad_soyad",$siparis_uye_id);
	$siparis_uye_email_adresi=getir("uyeler","email_adresi",$siparis_uye_id);
	$ileti="
	<p>Merhaba <strong>$siparis_uye_ad_soyad</strong></p>
	<p>" . _LANG('hb_bu_e_posta_magaza_siparisinizin_onaylandigini') . "</p>
	<p>Siteye giriş yapmak için <a href=\"$site_url/sayfa/giris-yap\">buraya</a> tıklayın.</p>";
	auto_mail_add($siparis_uye_ad_soyad,$siparis_uye_email_adresi,"Siparişiniz onaylandı! - $mail_baslik",$ileti);	
	// E-posta gönderimi bitti
	
	yonlendir("temalar.php?tema=$aktif_tema&sayfa=panel&islem=siparisler&tip=magaza");
}
if ($sil) {
	$magaza_id=getir("siparisler","magaza_id",$sil);
	mysqli_query($baglan,"delete from siparisler where id='$sil'");
	mysqli_query($baglan,"delete from ekstra_bilgi where id='$sil' and tip='siparis'");
	$_SESSION["mesaj"]=array(_LANG('pn_siparis_ve_siparise_bagli_kayitlar_basariyla_silindi'),".");
	yonlendir("temalar.php?tema=$aktif_tema&sayfa=panel&islem=siparisler&tip=magaza");
}

$sql=mysqli_query($baglan,"select * from siparisler where tip='magaza' order by tarih_saat desc");
$toplam=mysqli_num_rows($sql);

$pasif_sql=mysqli_query($baglan,"select * from siparisler where tip='magaza' and durum='0' order by tarih_saat desc");
$pasif_toplam=mysqli_num_rows($pasif_sql);

@$toplu_sil=$_POST["toplu_sil"];
if ($toplu_sil) {
	while ($pasif_dizi=mysqli_fetch_array($pasif_sql)) {
		$pasif_siparis_id=$pasif_dizi["id"];
		$magaza_id=getir("siparisler","magaza_id",$pasif_siparis_id);
		mysqli_query($baglan,"delete from siparisler where id='$pasif_siparis_id'");
		mysqli_query($baglan,"delete from ekstra_bilgi where id='$pasif_siparis_id' and tip='siparis'");
		mysqli_query($baglan,"delete from magazalar where id='$magaza_id'");
	}
	$_SESSION["mesaj"]=array(_LANG('pn_tum_bekleyen_magaza_siparisleri_basariyla_silindi'),".");
	yonlendir("temalar.php?tema=$aktif_tema&sayfa=panel&islem=siparisler&tip=magaza");		
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
<h3 class="pull-left bolum-baslik text-uppercase"><?php echo _LANG("pn_magaza_siparisleri"); ?></h3>
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
			<th><?php echo _LANG("s_alici"); ?></th>
			<th><?php echo _LANG("pn_magaza"); ?></th>
			<th>Paket</th>
			<th><?php echo _LANG("s_ilan_limiti"); ?></th>
			<th>Tutar</th>
			<th>Tip</th>
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
	$magaza_id=$dizi["magaza_id"];
	$paket_id=$dizi["paket_id"];
	$tutar=$dizi["tutar"];
	$tarih_saat=$dizi["tarih_saat"];
	$tip=$dizi["tip"];
	$durum=$dizi["durum"];
    
    $paket_baslik=liste_bilgi("paket_adi",$paket_id);
    $ilan_limit=liste_bilgi("ilan_limit",$paket_id);
    
?>
<tr>
	<th><?php echo $id; ?></th>
	<td><a href="<?php echo lpanel_uye_profil_url($alici_id); ?>" target="_blank"><?php echo getir("uyeler","ad_soyad",$alici_id); ?> <i class="fa fa-external-link"></i></a></td>
	<td><a href="temalar.php?tema=<?php echo $aktif_tema; ?>&sayfa=panel&islem=magaza-guncelle&id=<?php echo $magaza_id; ?>" target="_blank"><?php echo getir("magazalar","baslik",$magaza_id); ?></a> <a href="<?php echo magaza_link_getir($magaza_id); ?>" target="_blank"><i class="fa fa-external-link"></i></a></td>
	<td><?php echo $paket_baslik; ?></td>
	<td><?php echo $ilan_limit; ?></td>
	<td><?php echo $tutar; ?> <i class="fa fa-try"></i></td>
	<td><?php echo $tip; ?></td>
	<td><?php echo $tarih_saat; ?></td>
	<td><?php if ($durum==1) {echo "<span class='label label-success'>Onaylandı</span>";} else {echo "<span class='label label-danger'>Ödeme bekleniyor</span>";} ?></td>
	<td>
	<?php if ($durum==0) { ?>
	<a href="temalar.php?tema=<?php echo $aktif_tema; ?>&sayfa=panel&islem=siparisler&tip=magaza&onay=<?php echo $id; ?>" onclick="return confirm('Siparişi onaylamak istediğinizden emin misiniz?')" class="btn btn-primary btn-xs"><?php echo _LANG("pn_hizli_onay"); ?></a>
	<?php } ?>
	<a href="temalar.php?tema=<?php echo $aktif_tema; ?>&sayfa=panel&islem=siparisler&tip=magaza&sil=<?php echo $id; ?>" onclick="return confirm('Siparişi ve siparişe bağlı kayıtları silmek istediğinizden emin misiniz?')" class="btn btn-danger btn-xs">Sil</a>
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