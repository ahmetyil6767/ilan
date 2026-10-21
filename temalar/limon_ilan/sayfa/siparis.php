<?php 
if (!$oturum_id) {
	$_SESSION["mesaj"]=array(_LANG('pn_bu_sayfayi_goruntuleyebilmek_icin_uye_girisi_yapmalisiniz'),".");
	yonlendir(ozel_url("giris-yap"));
	exit();
}
$siparis=$_GET["siparis_id"];
if (!$siparis) {
	$_SESSION["mesaj"]=array(_LANG('pn_boyle_bir_siparis_bulunamadi'),".");	
	yonlendir(ozel_url("siparislerim"));
}
if ($siparis) {
	if (!is_numeric($siparis)) {
		yonlendir(ozel_url("siparislerim"));
	}
	$alici_id=getir("siparisler","alici_id",$siparis);
	$satici_id=getir("siparisler","satici_id",$siparis);
	if (!$alici_id) {
		$_SESSION["mesaj"]=array(_LANG('pn_siparis_bilgilerine_ulasilamadi'),".");
		yonlendir(ozel_url("siparislerim"));
	} else if ($alici_id and ($alici_id!=$oturum_id) and ($satici_id!=$oturum_id)) {
		$_SESSION["mesaj"]=array(_LANG('pn_sadece_kendi_siparislerinizin_detayini_goruntuleyebilirsiniz'),".");
		yonlendir(ozel_url("siparislerim"));
	}
	$tip=getir("siparisler","tip",$siparis);

	$ilan_id=getir("siparisler","ilan_id",$siparis);
	$magaza_id=getir("siparisler","magaza_id",$siparis);
	$paket_id=getir("siparisler","paket_id",$siparis);
	$tutar=str_replace(".",",",getir("siparisler","tutar",$siparis));
	$iyzico_tutar=getir("siparisler","tutar",$siparis);
	
	if ($tip=="doping") {
		$tanim="#$ilan_id nolu ilan için doping siparişi";
		$tanim_text="#$ilan_id nolu ilan için doping siparişi";
	} else if ($tip=="get") {
		$ilan_baslik=getir("icerik","baslik",$ilan_id);
		$tanim="#$ilan_id nolu ilan için GET siparişi";
		$tanim_text="#$ilan_id nolu ilan için GET siparişi";
		$get_komisyon=tema_ayar("get_komisyon");
		$net_satici_kazanci=number_format($tutar-(($tutar/100)*$get_komisyon),3,".",",");
	} else if ($tip=="magaza") {
        $limit_politikasi=limit_politikasi();
        if ($limit_politikasi==1) {

            $magaza_baslik=getir("magazalar","baslik",$magaza_id);
            $magaza_paket_adi=liste_bilgi("paket_adi",$paket_id);
            $magaza_link=magaza_link_getir($magaza_id);
            $tanim="<a href='$magaza_link' target='_blank'>$magaza_baslik</a> için $magaza_paket_adi Mağaza üyeliği";
            $tanim_text="" . sprintf(_LANG('s_magaza_uyeligi'), $magaza_baslik, $magaza_paket_adi) . "";

        } else if ($limit_politikasi==2) {
            
            $magaza_baslik=getir("magazalar","baslik",$magaza_id);
            $magaza_paket_adi=liste_bilgi("paket_adi",$paket_id);
            $magaza_paket_ilan_limiti=liste_bilgi("ilan_limit",$paket_id);
            $magaza_link=magaza_link_getir($magaza_id);
            $tanim="<a href='$magaza_link' target='_blank'>$magaza_baslik</a> için $magaza_paket_adi Mağaza üyeliği ($magaza_paket_ilan_limiti ilan)";
            $tanim_text="" . sprintf(_LANG('s_magaza_uyeligi'), $magaza_baslik, $magaza_paket_adi) . "";

        }
	} else if ($tip=="paket") {
        $limit_politikasi=limit_politikasi();
        if ($limit_politikasi==1) {
            $uye_ad_soyad=getir("uyeler","ad_soyad",$alici_id);
            $paket_baslik=liste_bilgi("paket_adi",$paket_id);
            $paket_ilan_limiti=liste_bilgi("ilan_limit",$paket_id);
            $uye_link=uye_link_getir($alici_id);
            $tanim="<a href='$uye_link' target='_blank'>$uye_ad_soyad</a> için $paket_baslik paket üyeliği ($paket_ilan_limiti ilan)";
            $tanim_text="" . sprintf(_LANG('s_paket_uyeligi'), $uye_ad_soyad, $paket_baslik) . "";
            
        } else if ($limit_politikasi==2) {
            
            $uye_ad_soyad=getir("uyeler","ad_soyad",$alici_id);
            $paket_baslik=liste_bilgi("paket_adi",$paket_id);
            $paket_ilan_limiti=liste_bilgi("ilan_limit",$paket_id);
            $uye_link=uye_link_getir($alici_id);
            $tanim="<a href='$uye_link' target='_blank'>$uye_ad_soyad</a> için $paket_baslik paket siparişi ($paket_ilan_limiti ilan)";
            $tanim_text="" . sprintf(_LANG('s_paket_siparis'), $uye_ad_soyad, $paket_baslik) . "";
	    }
	}
}

$siparis_durum=getir("siparisler","durum",$siparis);
$siparis_satici_odeme=getir("siparisler","satici_odeme",$siparis);
$siparis_tip=getir("siparisler","tip",$siparis);
/* Siparis satirinda gosterilecek alternatif bilgi (Magaza paketleri gibi).
 * Bazi siparis turlerinde tanimli degildir; bos satir gosterilmemeli. */
$alternatif_bilgi_baslik=@$alternatif_bilgi_baslik;
$alternatif_bilgi_tanim=@$alternatif_bilgi_tanim;

$durum_guncelle_buton=@$_POST["durum_guncelle_buton"];
if ($durum_guncelle_buton) {
	$yeni_durum=$_POST["yeni_durum"];
	mysqli_query($baglan,"update siparisler set satici_durum='$yeni_durum' where id='$siparis'");
	$_SESSION["mesaj"]=array(_LANG('pn_satici_durumu_basariyla_guncellendi'),".");
	if ($yeni_durum==3) {
		$tarih_saat=tarih_saat();
		$kargo_tarihi_ekle=ekstra_bilgi_guncelle("kargo_tarihi",$tarih_saat,$siparis,"siparis");
		
		$siparis_detay_url=ozel_url("siparis")."?siparis_id=$siparis";
		$siparis_alici_id=getir("siparisler","alici_id",$siparis);		
		bildirim_ekle(_LANG('pn_urununuz_kargoya_verildi_detayli_bilgi_icin_a_href_siparis_detay_url_b', array($siparis_detay_url)),$siparis_alici_id,1);
	}
	yonlendir(ozel_url("siparis")."?siparis_id=$siparis");
}

$alici_onay_buton=@$_POST["alici_onay_buton"];
if ($alici_onay_buton) {
	mysqli_query($baglan,"update siparisler set alici_onay='1', durum='1' where id='$siparis'");
	$tarih_saat=tarih_saat();
	$onay_tarihi_ekle=ekstra_bilgi_guncelle("onay_tarihi",$tarih_saat,$siparis,"siparis");	
	$_SESSION["mesaj"]=array(_LANG('pn_urunu_teslim_aldiginizi_onayladiniz'),".");
	
	$siparis_detay_url=ozel_url("siparis")."?siparis_id=$siparis";
	$siparis_satici_id=getir("siparisler","satici_id",$siparis);		
	$siparis_alici_id=getir("siparisler","alici_id",$siparis);		
	$siparis_alici_ad_soyad=getir("uyeler","ad_soyad",$siparis_alici_id);
	bildirim_ekle(sprintf(_LANG('bnf_urun_teslim_alindi'), $siparis_alici_ad_soyad, $siparis_detay_url),$siparis_satici_id,1);	
	
	yonlendir(ozel_url("siparis")."?siparis_id=$siparis");
}
tema_header(); ?>
<div class="container profil odeme mt20">

	<?php include("temalar/$aktif_tema/inc/alert.php"); ?>
	
	<div class="bilgi gradient">
	<h2><?php echo _LANG("title_order_summary"); ?></h2>
	<div class="satir" style="margin-bottom:5px;">Sipariş Tanımı: <span class="fw600"><?php echo $tanim; ?></span></div>
	<?php if ($alternatif_bilgi_baslik) { ?>
	<div class="satir" style="margin-bottom:5px;"><?php echo $alternatif_bilgi_baslik; ?> <span class="fw600"><?php echo $alternatif_bilgi_tanim; ?></span></div>
	<?php } ?>
	<div class="satir" style="margin-bottom:5px;">Toplam Tutar: <span class="fw600"><?php echo $tutar; ?> GMD</span></div>			
	<?php if ($siparis_durum==0) { ?>
	<p><?php echo _LANG('hb_degerli_uyemiz_odeme_yaptiktan_sonra_odeme_bil'); ?></p>
	<?php } ?>
	<?php if (($tip=="doping" or $tip=="magaza" or $tip=="paket") and $siparis_durum==1) { ?>
	<div class="satir" style="margin-bottom:5px;">Durum: <span class="label label-success borderless"><?php echo _LANG("post_approved"); ?></span></div>			
	<?php } ?>
	</div>
	
	
	<?php if ($satici_id==$oturum_id) { if ($siparis_satici_odeme==0) { ?>
	<div class="panel panel-default limon with_footer">
		<div><?php echo _LANG('hb_satici_islemleri'); ?></div>
		<div class="panel-body">
			<p>Sayın <?php echo getir("uyeler","ad_soyad",$oturum_id); ?>, bu siparişte satıcı konumunda bulunuyorsunuz.</p>
			<p style="margin-bottom:0;">Bu siparişten net <?php echo $net_satici_kazanci; ?> GMD kazanacaksınız. GET komisyonu: %<?php echo $get_komisyon; ?> (KDV ve kesintiler dahil)</p>
		</div>
		<?php if ($siparis_durum!=0) { ?>
		<div class="panel-footer">
			<a data-toggle="modal" data-target="#durum_guncelle" class="btn btn-primary"><i class="ion-gear-a"></i><?php echo _LANG("s_durum_guncelle"); ?></a>
			<?php 
			$banka_bilgileri=uye_bilgi("banka_bilgileri",$oturum_id);
			$profil_guncelle_url=ozel_url("profil-guncelle");
			if (!$banka_bilgileri) {echo "<a href='$profil_guncelle_url' class='btn btn-info pull-right'><i class='ion-card'></i>Banka Hesap Bilgileri</a>";} else {
			}
			?>
		</div>
		<?php } ?>
	</div>
	<?php } else {
		$odeme_tarihi=tarih_saat_bicimlendir(ekstra_bilgi_getir("odeme_tarihi",$siparis,"siparis"));
		echo "
		<div class='alert alert-success'>Ödemeniz $odeme_tarihi tarihinde yapılmış ve GET satışı sonlandırılmıştır.</div>
		";		
	} ?>
	
	<div class="modal fade" id="durum_guncelle" tabindex="-1" role="dialog" aria-labelledby="durum_guncelle">
	  <div class="modal-dialog" role="document">
		<form id="durum_guncelle_form" action="" method="post">
		<div class="modal-content">
		  <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h4 class="modal-title" id="myModalLabel"><?php echo _LANG("s_durum_guncelle"); ?></h4>
		  </div>
		  <div class="modal-body">
		  <?php
			$durum_secenekler=options("Sipariş alındı:0|Tedarik ediliyor:1|Kargoya hazırlanıyor:2|Kargoya verildi:3|İptal edildi:4","|",":");
			form_group(array("tip"=>"select","id"=>"durum","baslik"=>_LANG('s_guncel_durum'),"secenekler"=>$durum_secenekler,"deger"=>getir("siparisler","satici_durum",$siparis),"pasif"=>1));
			form_group(array("tip"=>"select","id"=>"yeni_durum","baslik"=>"Yeni Durum","secenekler"=>$durum_secenekler,"deger"=>getir("siparisler","satici_durum",$siparis))); 
		  ?>
		  </div>
		  <div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal">Kapat</button>
			<button type="submit" class="btn btn-primary" name="durum_guncelle_buton" value="Güncelle"><?php echo _LANG("general_update"); ?></button>
		  </div>
		</div>
		</form>
	  </div>	
	</div>	
	<?php } ?>
	
	<?php if ($tip=="get" and $oturum_id==$alici_id and $siparis_durum==1) {echo "<div class='alert alert-success'>" . _LANG('s_get_tamamlandi') . "</div>";} ?> 	
	
	
	<?php if ($tip=="doping") { ?>
	<div class="panel panel-default limon">
		<div><?php echo _LANG('hb_secilen_dopingler'); ?></div>
		<div class="panel-body" style="padding:0;">
			<table class="table table-default profil padding" style="margin-bottom:0;">
				<thead>
					<th><?php echo _LANG("search_found_ads"); ?></th>
					<th>Doping</th>
					<th><?php echo _LANG("s_gun_sayisi"); ?></th>
					<th><?php echo _LANG("s_doping_baslangici"); ?></th>
					<th><?php echo _LANG("s_doping_bitisi"); ?></th>
					<th><?php echo _LANG("s_kalan_gun"); ?></th>
					<th><?php echo _LANG("order_no"); ?></th>
					<th>Durum</th>
				</thead>
				<?php 
				$doping_sql=mysqli_query($baglan,"select * from dopingler where siparis_id='$siparis'");
				while ($doping_dizi=mysqli_fetch_array($doping_sql)) { 
				$doping_id=$doping_dizi["id"];
				$doping=$doping_dizi["doping"];
				$doping_ilan_id=$doping_dizi["ilan_id"];
				$doping_ilan_baslik=getir("icerik","baslik",$doping_ilan_id);
				$doping_durum=$doping_dizi["durum"];
				$doping_gun=$doping_dizi["gun"];
				$doping_tarih_saat=$doping_dizi["tarih_saat"];
				$doping_bitis_tarih_saat=$doping_dizi["bitis_tarih_saat"];
				?>
				<tr>
					<td><?php if ($doping_ilan_baslik) { ?><a href="<?php echo link_getir($doping_ilan_id); ?>" target="_blank"><?php echo $doping_ilan_baslik; ?></a><?php } else {echo " . _LANG('s_ilan_mevcut_degil') . ";} ?></td>
					<td><?php echo $doping; ?></td>
					<td><?php echo $doping_gun; ?></td>
					<td><?php echo $doping_tarih_saat; ?></td>
					<td><?php echo $doping_bitis_tarih_saat; ?></td>
					<td><?php echo tarih_fark($doping_bitis_tarih_saat,$doping_tarih_saat,array("birim"=>"gun")); ?></td>
					<td>#<?php echo $siparis; ?></td>
					<td><?php if ($doping_durum==0) {echo "<span class='label label-warning borderless'>Pasif</span>";} else if ($doping_durum==1) {echo "<span class='label label-success borderless'>Aktif</span>";} else if ($doping_durum==2) {echo "<span class='label label-danger borderless'>" . _LANG('e_suresi_doldu') . "</span>";} ?></td>
				</tr>
				<?php } ?>
			</table>
		</div>			
	</div>
	<?php } else if ($tip=="get") {
	$satici=getir("siparisler","satici_id",$siparis);
	$durum=getir("siparisler","durum",$siparis);
	$satici_durum=getir("siparisler","satici_durum",$siparis);
	$alici_onay=getir("siparisler","alici_onay",$siparis);
	?>
	<div class="panel panel-default limon">
		<div><?php echo _LANG('hb_urunler'); ?></div>
		<div class="panel-body" style="padding:0;">
			<table class="table table-default profil padding" style="margin-bottom:0;">
				<thead>
					<th><?php echo _LANG("search_found_ads"); ?></th>
					<th>Adet</th>
					<th><?php echo _LANG("s_satici"); ?></th>
					<th>Durum</th>
					<?php if ($siparis_durum!=0) { ?><th><?php echo _LANG("pn_satici_bilgisi"); ?></th><?php } ?>
					<?php if ($siparis_durum!=0 and $alici_id==$oturum_id and $satici_durum==3 and $alici_onay==0) { ?>
					<th><?php echo _LANG("s_alici_onayi"); ?></th>
					<?php } ?>
					<?php if ($alici_onay) { ?>
					<th><?php echo _LANG("s_alici_onayi"); ?></th>
					<?php } ?>
				</thead>
				<tr>
					<td><a href="<?php echo link_getir($ilan_id); ?>" target="_blank"><?php echo getir("icerik","baslik",$ilan_id); ?></a></td>
					<td><?php echo ekstra_bilgi_getir("adet",$siparis,"siparis"); ?></td>
					<td><a href="<?php echo uye_link_getir($satici); ?>" target="_blank"><?php echo getir("uyeler","ad_soyad",$satici); ?></a></td>
					<td>
					<?php if ($durum==0) {
						echo "<span class='label label-danger borderless'>" . _LANG('order_pending') . "</span>";
					} else if ($durum==1) {
						echo "<span class='label label-primary borderless'>" . _LANG('order_completed') . "</span>";
					} else if ($durum==2) {
						echo "<span class='label label-success borderless'>" . _LANG('s_odeme_onaylandi') . "</span>";
					} ?>							
					</td>
					<?php if ($siparis_durum!=0) { ?>
					<td>
					<?php if ($satici_durum==0) {
						echo "<span class='label label-light borderless'>" . _LANG('s_siparis_alindi') . "</span>";
					} else if ($satici_durum==1) {
						echo "<span class='label label-warning borderless'>Tedarik ediliyor</span>";
					} else if ($satici_durum==2) {
						echo "<span class='label label-info borderless'>" . _LANG('s_kargoya_hazir') . "</span>";
					} else if ($satici_durum==3) {
						$kargo_tarihi=tarih_saat_bicimlendir(ekstra_bilgi_getir("kargo_tarihi",$siparis,"siparis"));
						echo "<span class='label label-success borderless'>Kargoya verildi: $kargo_tarihi</span>";
					} else if ($satici_durum==4) {
						echo "<span class='label label-danger borderless'>" . _LANG('order_cancelled') . "</span>";
					} ?>
					</td>
					<?php } ?>
					<?php if ($siparis_durum!=0 and $alici_id==$oturum_id and $satici_durum==3 and $alici_onay==0) { ?>
					<td><a data-toggle="modal" data-target="#alici_onay" class="btn btn-primary btn-xs"><i class="ion-checkmark"></i>Onayla</a></td>
					<?php } ?>
					<?php if ($alici_onay) { 
					$onay_tarihi=tarih_saat_bicimlendir(ekstra_bilgi_getir("onay_tarihi",$siparis,"siparis"));
					?>
					<td><span class="label label-success borderless">Teslim alındı <?php echo $onay_tarihi; ?></span></td>
					<?php } ?>
				</tr>
			</table>
		</div>			
	</div>
	<?php 
	$siparis_notu=ekstra_bilgi_getir("not",$siparis,"siparis");
	if ($siparis_notu) { ?>
	<div class="panel panel-default limon">
		<div><?php echo _LANG('hb_alici_notu'); ?></div>
		<div class="panel-body"><?php echo $siparis_notu; ?></div>
	</div>	
	<?php }	?>
	
	<?php if ($alici_id==$oturum_id) { ?>
	<div class="modal fade" id="alici_onay" tabindex="-1" role="dialog" aria-labelledby="alici_onay">
	  <div class="modal-dialog modal-sm" role="document">
		<form id="alici_onay_form" action="" method="post">
		<div class="modal-content noselect">
		  <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h4 class="modal-title" id="myModalLabel"><?php echo _LANG("s_alici_onayi"); ?></h4>
		  </div>
		  <div class="modal-body">
		  <?php form_group(array("tip"=>"checkbox","id"=>"onay","baslik"=>_LANG('s_urun_teslim_beyan'))); ?>
		  </div>
		  <div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal">Kapat</button>
			<button type="submit" class="btn btn-primary" id="alici_onay_buton" name="alici_onay_buton" value="Onayla">Onayla</button>
		  </div>
		</div>
		</form>
	  </div>	
	</div>		
	<?php } ?>
	
	<?php }  ?>
	<?php if ($siparis_durum==0 and $satici_id!=$oturum_id) { ?>
	<div class="panel panel-default limon tablo">
		<div class="panel-heading" style="border:0;padding:0;">
		  <ul class="nav nav-tabs ozel mavi" role="tablist">
			<?php $online_odeme=tema_ayar("online_odeme");
			if ($online_odeme=="aktif") { ?>
			<li role="presentation" class="active"><a href="#online_odeme" aria-controls="online_odeme" role="tab" data-toggle="tab"><i class="ion-card"></i><?php echo _LANG("s_online_odeme"); ?></a></li>
			<?php } ?>
			<li role="presentation" <?php if ($online_odeme=="pasif") {echo "class='active'";} ?>><a href="#bilgiler" aria-controls="bilgiler" role="tab" data-toggle="tab"><i class="ion-cash"></i>Banka Havalesi / EFT</a></li>
		  </ul>
		</div>
		<div class="panel-body" style="padding:0;">

		<form action="" method="post">
		<div class="tab-content">
		<?php if ($online_odeme=="aktif") {  ?>
			<div role="tabpanel" class="tab-pane fade in active" id="online_odeme" style="padding:15px;">
				
				<?php 
				
				$buton=$_POST["buton"];
				if ($buton) {
					if ($_POST["tc_no"]) {
						uye_bilgi_guncelle("tc_no",trim(mysqli_real_escape_string($baglan,strip_tags($_POST["tc_no"]))),$oturum_id);
					}
					if ($_POST["adres"]) {
						uye_bilgi_guncelle("adres",trim(mysqli_real_escape_string($baglan,strip_tags($_POST["adres"]))),$oturum_id);
					}
					if ($_POST["il"]) {
						uye_bilgi_guncelle("il",trim(mysqli_real_escape_string($baglan,strip_tags($_POST["il"]))),$oturum_id);
					}
					if ($_POST["posta_kodu"]) {
						uye_bilgi_guncelle("posta_kodu",trim(mysqli_real_escape_string($baglan,strip_tags($_POST["posta_kodu"]))),$oturum_id);
					}
				}

				$uye_adres=uye_bilgi("adres",$oturum_id);
				$uye_il=uye_bilgi("il",$oturum_id);
				$uye_posta_kodu=uye_bilgi("posta_kodu",$oturum_id);
				$uye_tc_no=uye_bilgi("tc_no",$oturum_id);
				
				if (!$uye_adres or !$uye_il or !$uye_posta_kodu or !$uye_tc_no) {
				?>
				<div class="alert alert-danger">Sayın <?php echo $oturum_ad_soyad; ?>, kredi kartı ile online ödeme yapabilmek için lütfen profilinizdeki eksik bilgileri tamamlayın.</div>
				
				<div class="row">
					<div class="col-md-20">
						<div class="tamamla" style="border:5px solid #eee;padding:15px;">
							<form action="" method="post">
								<?php if (!$uye_tc_no) {form_group(array("tip"=>"text","id"=>"tc_no","baslik"=>_LANG("s_tc_no"),"deger"=>$uye_tc_no,"gerekli"=>1));} ?>
								<?php if (!$uye_adres) {form_group(array("tip"=>"textarea","id"=>"adres","baslik"=>"Adres","satir"=>3,"deger"=>$uye_adres,"aciklama"=>_LANG('s_kargo_fatura_adresi'),"gerekli"=>1));} ?>				
								<?php if (!$uye_il) {
									$il_secenekleri[]=array("deger"=>null,"baslik"=>_LANG('filter_select'));
									$il_secenekleri_getir=mysqli_query($baglan,"select il_adi from iller");
									while ($il_secenekleri_dizi=mysqli_fetch_array($il_secenekleri_getir)) {
										$il_secenekleri[]=array("deger"=>$il_secenekleri_dizi["il_adi"],"baslik"=>$il_secenekleri_dizi["il_adi"]);
									}
									form_group(array("tip"=>"select","id"=>"il","baslik"=>_LANG('addr_region'),"deger"=>uye_bilgi("il",$oturum_id),"secenekler"=>$il_secenekleri,"gerekli"=>1,"deger"=>$uye_il));
								} ?>				
								<?php if (!$uye_posta_kodu) {form_group(array("tip"=>"text","id"=>"posta_kodu","baslik"=>"Posta Kodu","deger"=>$uye_posta_kodu,"gerekli"=>1));} ?>				
								<?php form_buton("buton","Kaydet ve ödeme adımına geç","primary"); ?>
							</form>
						</div>
					</div>
				</div>
				<?php } else { 
				
	
				require_once("temalar/$aktif_tema/iyzico/config.php");

				
				$token=$_POST["token"];
				if ($token) {
					
				$request = new \Iyzipay\Request\RetrieveCheckoutFormRequest();
				$request->setLocale(\Iyzipay\Model\Locale::TR);
				$request->setConversationId("$siparis");
				$request->setToken("$token");
				$checkoutForm = \Iyzipay\Model\CheckoutForm::retrieve($request, Config::options());
				
				//print_r($checkoutForm->getStatus());
				
				$odeme_durum=$checkoutForm->getPaymentStatus();
				$islem_no=$checkoutForm->getpaymentId();
				if ($odeme_durum) {
					if ($odeme_durum=="FAILURE") {
						echo "<div class='alert alert-danger'>Ödeme işlemi sırasında bir hata oluştu. Lütfen farklı bir ödeme yöntemi kullanınız.</div>";
					} else if ($odeme_durum=="SUCCESS") {
						echo "<div class='alert alert-success'>" . sprintf(_LANG('s_odeme_onaylandi_no'), $islem_no) . "</div>";
						
						if ($tip=="magaza") {
                            $limit_politikasi=limit_politikasi();
                            if ($limit_politikasi==1) {
                                guncelle("magazalar","durum",1,$magaza_id);
                                $magaza_baslik=getir("magazalar","baslik",$magaza_id);
                                $bitis_tarihi=date("Y-m-d H:i:s",strtotime("+1 year"));
                                magaza_bilgi_guncelle("paket_bitis_tarihi",$bitis_tarihi,$magaza_id);
                                $_SESSION["mesaj"]=array(_LANG('pn_odemeniz_onaylandi_ve_magazaniz_aktiflestirildi'),"i");
                                bildirim_ekle(sprintf(_LANG('bnf_magaza_onaylandi'), $magaza_baslik),$alici_id,1);
                            } else {
                                
                                $ilan_limit=liste_bilgi("ilan_limit",$paket_id);
                                $foto_limit=liste_bilgi("foto_limit",$paket_id);
                                $danisman_limit=liste_bilgi("danisman_limit",$paket_id);
                                $aninda_onay=liste_bilgi("aninda_onay",$paket_id);
                               
                                $mevcut_ilan_limiti=magaza_ilan_limiti("toplam",$magaza_id);
                                $yeni_ilan_limiti=$mevcut_ilan_limiti+$ilan_limit;
                                
                                magaza_bilgi_guncelle("azalan_ilan_limit",$yeni_ilan_limiti,$magaza_id);
                                magaza_bilgi_guncelle("azalan_foto_limit",$foto_limit,$magaza_id);
                                magaza_bilgi_guncelle("azalan_danisman_limit",$danisman_limit,$magaza_id);
                                magaza_bilgi_guncelle("azalan_aninda_onay",$aninda_onay,$magaza_id);
                                
                                guncelle("magazalar","azalan_update",1,$magaza_id);
                                
                                guncelle("magazalar","durum",1,$magaza_id);
                                $magaza_baslik=getir("magazalar","baslik",$magaza_id);
                                $bitis_tarihi=date("Y-m-d H:i:s",strtotime("+1 year"));
                                magaza_bilgi_guncelle("paket_bitis_tarihi",$bitis_tarihi,$magaza_id);
                                $_SESSION["mesaj"]=array(_LANG('pn_odemeniz_onaylandi_ve_magazaniz_aktiflestirildi'),"i");
                                bildirim_ekle(sprintf(_LANG('bnf_magaza_onaylandi'), $magaza_baslik),$alici_id,1);
                            }
                            guncelle("siparisler","durum",1,$siparis);
							yonlendir(ozel_url("siparis")."?siparis_id=$siparis");
							
						} else if ($tip=="paket") {
                            
                            $limit_politikasi=limit_politikasi();
                            if ($limit_politikasi==1) {
                                uye_bilgi_guncelle("paket",$paket_id,$alici_id);
                                $bitis_tarihi=date("Y-m-d H:i:s",strtotime("+1 year"));
                                uye_bilgi_guncelle("paket_bitis_tarihi",$bitis_tarihi,$alici_id);
                                $_SESSION["mesaj"]=array(_LANG('pn_odemeniz_onaylandi_ve_uyelik_paketiniz_aktiflestirildi'),"i");
                                bildirim_ekle(sprintf(_LANG('bnf_paket_guncellendi'), $paket_baslik),$alici_id,1);    
                            } else if ($limit_politikasi==2) {
                                $alinan_ilan_kredisi=liste_bilgi("ilan_limit",$paket_id);
                                $yeni_ilan_limiti=uye_ilan_limiti("toplam",$alici_id)+$alinan_ilan_kredisi;
                                uye_bilgi_guncelle("azalan_ilan_limit",$yeni_ilan_limiti,$alici_id);
                                uye_bilgi_guncelle("paket",$paket_id,$alici_id);
                                uye_bilgi_guncelle("paket_bitis_tarihi",$bitis_tarihi,$alici_id);
                                $_SESSION["mesaj"]=array(_LANG('pn_odemeniz_onaylandi_ve_satin_aldiginiz_krediler_hesabiniza_yuklendi'),".");
                                bildirim_ekle(_LANG('pn_ilan_paketi_siparisiniz_onaylandi'),$alici_id,1);   
                            }
                            guncelle("siparisler","durum",1,$siparis);
							yonlendir(ozel_url("siparis")."?siparis_id=$siparis");
						} else if ($tip=="get") {
							guncelle("siparisler","durum",2,$siparis);
							$_SESSION["mesaj"]=array(_LANG('pn_odemeniz_onaylandi'),".");
							
							// Stok güncelleniyor.
							$ilan_id=getir("siparisler","ilan_id",$siparis);
							$stok=bilgi("stok",$ilan_id);
							$adet=ekstra_bilgi_getir("adet",$siparis,"siparis");
							$yeni_stok=$stok-$adet;
							$stok_guncelle=bilgi_guncelle("stok",$yeni_stok,$ilan_id);
							// Stok güncellendi.
							
							$siparis_detay_url=ozel_url("siparis")."?siparis_id=$siparis";
							$siparis_uye_id=getir("siparisler","alici_id",$siparis);
							bildirim_ekle(_LANG('pn_get_siparisi_odemeniz_onaylandi_siparis_durumunuzu_gormek_icin_a_href_', array($siparis_detay_url)),$siparis_uye_id,1);
							$siparis_satici_id=getir("siparisler","satici_id",$siparis);
							bildirim_ekle(_LANG('pn_get_siparisi_odemeniz_alindi_urunu_kargolayabilirsiniz_a_href_siparis_', array($siparis_detay_url,$siparis)),$siparis_satici_id,1);
							yonlendir(ozel_url("siparis")."?siparis_id=$siparis");
						
						} else if ($tip=="doping") {
							guncelle("siparisler","durum",1,$siparis);
							$doping_sql=mysqli_query($baglan,"select * from dopingler where siparis_id='$siparis'");
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
							$_SESSION["mesaj"]=array(_LANG('pn_siparisiniz_onaylandi_ve_dopingleriniz_aktiflestirildi'),".");
							$siparis_detay_url=ozel_url("siparis")."?siparis_id=$siparis";
							$siparis_uye_id=getir("siparisler","alici_id",$siparis);
							bildirim_ekle(_LANG('pn_dopingleriniz_aktiflestirildi_detayli_bilgi_icin_a_href_siparis_detay_', array($siparis_detay_url)),$siparis_uye_id,1);
							yonlendir(ozel_url("siparis")."?siparis_id=$siparis");
						}
					}
				} }
				/* Iyzico checkout formu yalnizca ONLINE odeme acikken baslatilir.
				 * Kapali oldugunda (havale/EFT modu) bu blok hic calismaz. */
				if ($online_odeme=="aktif") {
				
					$ad_soyad_parcala=explode(" ",$oturum_ad_soyad);
					$oturum_ad=$ad_soyad_parcala[0];
					$oturum_soyad=$ad_soyad_parcala[1];
					if(!$oturum_soyad) {$oturum_soyad=$oturum_ad;}
					$oturum_email=getir("uyeler","email_adresi",$oturum_id);
					$oturum_uyelik_tarihi=getir("uyeler","tarih_saat",$oturum_id);
					$oturum_adres=uye_bilgi("adres",$oturum_id);
					$oturum_il=uye_bilgi("il",$oturum_id);
					$oturum_posta_kodu=uye_bilgi("posta_kodu",$oturum_id);
					$oturum_tel=uye_bilgi("cep_tel",$oturum_id);
					$oturum_tc_no=uye_bilgi("tc_no",$oturum_id);
					$oturum_ip_adresi=ip_adresi();
					if (!$oturum_tel) {
						$ev_tel=uye_bilgi("ev_tel",$oturum_id);
						if ($ev_tel) {
							$oturum_tel=$ev_tel;
						}
						$is_tel=uye_bilgi("is_tel",$oturum_id);
						if ($is_tel) {
							$oturum_tel=$is_tel;
						}
					}

					$oturum_son_ziyaret=tarih_saat();
					# create request class
					$request = new \Iyzipay\Request\CreateCheckoutFormInitializeRequest();
					$request->setLocale(\Iyzipay\Model\Locale::TR);
					$request->setConversationId("$siparis");
					$request->setPrice("$iyzico_tutar");
					$request->setPaidPrice("$iyzico_tutar");
					$request->setCurrency(\Iyzipay\Model\Currency::TL);
					$request->setBasketId("$siparis");
					$request->setPaymentGroup(\Iyzipay\Model\PaymentGroup::LISTING);
					$request->setCallbackUrl(ozel_url("siparis")."?siparis_id=$siparis");
					
					$tum_kategoriler=kategori_dizisi(getir("icerik","kategori",$ilan_id));
					foreach ($tum_kategoriler as $kat) {
						$kat_taksit=kategori_bilgi("taksit",$kat);					
						if ($kat_taksit) {
							$taksit_secenekleri=$kat_taksit;
						}
					}
					if ($taksit_secenekleri=="yok") {
						$taksit_dizi=array(1);
					} else if ($taksit_secenekleri==2) {
						$taksit_dizi=array(2);
					} else if ($taksit_secenekleri==3) {
						$taksit_dizi=array(2,3);
					} else if ($taksit_secenekleri==6) {
						$taksit_dizi=array(2,3,6);
					} else {
						$taksit_dizi=array(2,3,6,9);
					}
					
					$request->setEnabledInstallments($taksit_dizi);
					
					$buyer = new \Iyzipay\Model\Buyer();
					$buyer->setId("$oturum_id");
					$buyer->setName("$oturum_ad");
					$buyer->setSurname("$oturum_soyad");
					$buyer->setGsmNumber("$oturum_tel");
					$buyer->setEmail("$oturum_email");
					$buyer->setIdentityNumber("$oturum_tc_no");
					$buyer->setLastLoginDate("$oturum_son_ziyaret");
					$buyer->setRegistrationDate("$oturum_uyelik_tarihi");
					$buyer->setRegistrationAddress("$oturum_adres");
					$buyer->setIp("$oturum_ip_adresi");
					$buyer->setCity("$oturum_il");
					$buyer->setCountry("Gambia");
					$buyer->setZipCode("$oturum_posta_kodu");
					$request->setBuyer($buyer);
					
					$shippingAddress = new \Iyzipay\Model\Address();
					$shippingAddress->setContactName("$oturum_ad");
					$shippingAddress->setCity("$oturum_il");
					$shippingAddress->setCountry("Gambia");
					$shippingAddress->setAddress("$oturum_adres");
					$shippingAddress->setZipCode("$oturum_posta_kodu");
					$request->setShippingAddress($shippingAddress);

					$billingAddress = new \Iyzipay\Model\Address();
					$billingAddress->setContactName("$oturum_ad");
					$billingAddress->setCity("$oturum_il");
					$billingAddress->setCountry("Gambia");
					$billingAddress->setAddress("$oturum_adres");
					$billingAddress->setZipCode("$oturum_posta_kodu");
					$request->setBillingAddress($billingAddress);

					$basketItems = array();
					$firstBasketItem = new \Iyzipay\Model\BasketItem();
					$firstBasketItem->setId("$siparis");
					$firstBasketItem->setName("$tanim_text");
					$firstBasketItem->setCategory1("Collectibles");
					$firstBasketItem->setItemType(\Iyzipay\Model\BasketItemType::PHYSICAL);
					$firstBasketItem->setPrice("$iyzico_tutar");
					$basketItems[0] = $firstBasketItem;
					
					$request->setBasketItems($basketItems);
					
					# make request
					$checkoutFormInitialize = \Iyzipay\Model\CheckoutFormInitialize::create($request, Config::options());
					
					# print result
					//print_r($checkoutFormInitialize);
					//print_r($checkoutFormInitialize->getstatus());
					echo $checkoutFormInitialize->getCheckoutFormContent();					

				}

				}
				
				
				?>
				<div id="checkout"></div>
				
				<div id="yukleniyor" class="text-center">
					<i class="fa fa-circle-o-notch fa-spin"></i>
				</div>
			</div>	
			<?php } ?>			
			<div role="tabpanel" class="tab-pane fade in <?php if ($online_odeme=="pasif") {echo "active";} ?>" id="bilgiler">
				<div class="alert alert-info" style="margin:15px 15px 0;">
					<strong><?php echo _LANG("hv_odeme_referansi"); ?>:</strong> JA-<?php echo $siparis; ?> &nbsp;&middot;&nbsp;
					<strong><?php echo _LANG("hv_odeme_tutari"); ?>:</strong> <?php echo $tutar; ?> GMD<br>
					<?php echo _LANG("hv_odeme_aciklama"); ?>
				</div>
				<div class="table-responsive">
				<table class="table table-default table-hover" style="margin-bottom:0;">
					<thead>
						<tr>
							<th>Banka</th>
							<th>Ad Soyad</th>
							<th>IBAN</th>
							<th width="10"></th>
						</tr>
					</thead>
					<tbody>
						<?php 
						$banka_hesaplari=array();
						$hesap_sql=mysqli_query($baglan,"select l_id from listeler where tip='hesaplar'");
						while ($hesap_dizi=mysqli_fetch_array($hesap_sql)) {
							$hesap_id=$hesap_dizi["l_id"];
							$banka=liste_bilgi("banka_adi",$hesap_id);
							$ad_soyad=liste_bilgi("ad_soyad",$hesap_id);
							$iban=liste_bilgi("iban",$hesap_id);
							$banka_hesaplari[]=array("deger"=>$banka,"baslik"=>$banka);
						?>
						<tr>
							<td><?php echo $banka; ?></td>
							<td><?php echo $ad_soyad; ?></td>
							<td><?php echo $iban ? $iban : _LANG("hv_banka_bilgisi_yok"); ?></td>
							<td><a id="odeme_bildirimi_buton" data-toggle="modal" data-target="#odeme_bildirimi" data-title="<?php echo $banka; ?>" class="btn btn-xs btn-default"><i class="ion-paper-airplane"></i><?php echo _LANG("order_notify"); ?></a></td>
						</tr>
						<?php } ?>
						<?php if (!$banka_hesaplari) { ?>
						<tr><td colspan="4"><?php echo _LANG("hv_banka_bilgisi_yok"); ?></td></tr>
						<?php } ?>
					</tbody>						
				</table>
				</div>
				
			</div>

		</div>
		</form>
		</div>
	</div>
	<?php } ?>
	
	<?php if ($satici_id==$oturum_id and $siparis_durum!=0) {
	$cep_tel=uye_bilgi("cep_tel",$alici_id);
	$is_tel=uye_bilgi("is_tel",$alici_id);
	$ev_tel=uye_bilgi("ev_tel",$alici_id);
	$adres=uye_bilgi("adres",$alici_id);
	if (!$cep_tel) {$cep_tel="Belirtilmedi";}
	if (!$is_tel) {$is_tel="Belirtilmedi";}
	if (!$ev_tel) {$ev_tel="Belirtilmedi";}
	if (!$adres) {$adres="Belirtilmedi";}
	
	?>
	<div class="panel panel-default limon ">
		<div><?php echo _LANG('hb_alici_iletisim_bilgileri'); ?></div>
		<div class="panel-body" style="padding:0;">
			<table class="table table-default profil padding" style="margin-bottom:0;">
				<tr>
					<th width="150"><?php echo _LANG("s_alici_adi"); ?></th>
					<td><?php echo getir("uyeler","ad_soyad",$alici_id); ?></td>
				</tr>
				<tr>
					<th>Cep Telefonu</th>
					<td><?php echo $cep_tel; ?></td>
				</tr>				
				<tr>
					<th><?php echo _LANG("s_is_telefonu"); ?></th>
					<td><?php echo $is_tel; ?></td>
				</tr>				
				<tr>
					<th>Ev Telefonu</th>
					<td><?php echo $ev_tel; ?></td>
				</tr>				
				<tr>
					<th>Adres</th>
					<td><?php echo $adres; ?></td>
				</tr>				
			</table>
		
		</div>
	</div>
	<?php } ?>
	<?php if ($alici_id==$oturum_id and $siparis_durum!=0 and $siparis_tip=="get") {
	$cep_tel=uye_bilgi("cep_tel",$satici_id);
	$is_tel=uye_bilgi("is_tel",$satici_id);
	$ev_tel=uye_bilgi("ev_tel",$satici_id);
	$adres=uye_bilgi("adres",$satici_id);
	if (!$cep_tel) {$cep_tel="Belirtilmedi";}
	if (!$is_tel) {$is_tel="Belirtilmedi";}
	if (!$ev_tel) {$ev_tel="Belirtilmedi";}
	if (!$adres) {$adres="Belirtilmedi";}
	
	?>
	<div class="panel panel-default limon ">
		<div><?php echo _LANG('hb_satici_iletisim_bilgileri'); ?></div>
		<div class="panel-body" style="padding:0;">
			<table class="table table-default profil padding" style="margin-bottom:0;">
				<tr>
					<th width="150"><?php echo _LANG("s_satici_adi"); ?></th>
					<td><?php echo getir("uyeler","ad_soyad",$satici_id); ?></td>
				</tr>
				<tr>
					<th>Cep Telefonu</th>
					<td><?php echo $cep_tel; ?></td>
				</tr>				
				<tr>
					<th><?php echo _LANG("s_is_telefonu"); ?></th>
					<td><?php echo $is_tel; ?></td>
				</tr>				
				<tr>
					<th>Ev Telefonu</th>
					<td><?php echo $ev_tel; ?></td>
				</tr>				
			</table>
		
		</div>
	</div>
	<?php } ?>	
	
</div>
<!-- Modal -->
<div class="modal fade" id="odeme_bildirimi" tabindex="-1" role="dialog" aria-labelledby="odeme_bildirimi">
  <div class="modal-dialog" role="document">
	<form id="odeme_bildirimi_form" action="" method="post">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel"><?php echo _LANG("order_notify"); ?></h4>
      </div>
      <div class="modal-body">
		<?php form_group(array("tip"=>"select","id"=>"hesap","baslik"=>_LANG('s_banka_hesabi'),"secenekler"=>$banka_hesaplari,"gerekli"=>1)); ?>
		<?php form_group(array("tip"=>"text","id"=>"gonderen","baslik"=>_LANG('s_gonder_ad_soyad'),"gerekli"=>1)); ?>
		<?php form_group(array("tip"=>"textarea","id"=>"mesaj","baslik"=>_LANG('s_eklemek_istedik'),"satir"=>4)); ?>
		<?php form_group(array("tip"=>"hidden","id"=>"tanim","baslik"=>_LANG('s_odeme_tanimi'),"deger"=>$tanim_text)); ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Kapat</button>
        <button type="button" id="m_gonder_buton" class="btn btn-primary"><?php echo _LANG("s_bildirim_gonder"); ?></button>
      </div>
    </div>
	</form>
  </div>
</div>
<?php tema_footer(); ?>
<script type="text/javascript">
$("button#alici_onay_buton").attr("disabled", true);

$("#form_onay input").change(function() {
	if(this.checked) {
		$("button#alici_onay_buton").attr("disabled", false);
	} else {
		$("button#alici_onay_buton").attr("disabled", true);
	}
});
$("body").on("click","#odeme_bildirimi_buton",function(){
	var banka=$(this).attr("data-title");
	$("#hesap").val(banka);
});
$("body").on("click","#m_gonder_buton",function(){
	$.ajax({
		type:"post",
		url:"<?php echo tema_dizini(); ?>/ajax.php?islem=odeme_bildirimi",
		data:$("#odeme_bildirimi_form").serialize(),
		success:function(cevap){
			$('#odeme_bildirimi #mesaj').val('');
			$('#odeme_bildirimi').modal('toggle');
			$("#mesaj_kutusu").append(cevap);
		}
	});
});	

$("#checkout").load("<?php echo tema_dizini(); ?>/ajax.php?islem=iyzico_form",function(){
	$("#yukleniyor").hide();
});

</script>