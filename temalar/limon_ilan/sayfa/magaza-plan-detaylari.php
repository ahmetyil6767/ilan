<?php 
if(count(get_included_files())==1) {die();}

$id=mysqli_real_escape_string($baglan,$_GET["id"]);

if (limit_politikasi()==2) {
    yonlendir(ozel_url("magaza-paket-satin-al")."/?id=$id");
}

if (!$oturum_id) {
	$_SESSION["mesaj"]=array(_LANG('pn_bu_sayfayi_goruntuleyebilmek_icin_uye_girisi_yapmalisiniz'),".");
	yonlendir(ozel_url("giris-yap"));
	exit();
}

if (!$id) {
	yonlendir(site_url());
} else {
	if (!is_numeric($id)) {
		yonlendir(ozel_url("magazalarim"));
	}	
	$magaza_sahibi=getir("magazalar","uye_id",$id);
	if ($magaza_sahibi!=$oturum_id) {
		$_SESSION["mesaj"]=array(_LANG('pn_yetkisiz_islem'),".");
		yonlendir(ozel_url("magazalarim"));
	} else {
		@$buton=mysqli_real_escape_string($baglan,$_POST["buton"]);
		if ($buton) {
			$paket=mysqli_real_escape_string($baglan,$_POST["paket"]);
			$tarih_saat=tarih_saat();
			$paket_tutar=liste_bilgi("ucret",$paket);
			$kategori=getir("magazalar","kategori",$id);
			
			$paket_kategori_ozel_fiyat=kategori_bilgi("magaza_paket_fiyat-$paket",$kategori);
			if ($paket_kategori_ozel_fiyat) {
				$paket_tutar=$paket_kategori_ozel_fiyat;
			}

			$siparis_olustur=mysqli_query($baglan,"insert into siparisler set alici_id='$oturum_id', magaza_id='$id', paket_id='$paket', tarih_saat='$tarih_saat', tip='magaza', tutar='$paket_tutar'");
			$siparis_id=mysqli_insert_id($baglan);

			// E-posta gönderimi başladı
			$mail_baslik=tema_ayar("mail_baslik");
			$site_url=site_url();
			$ad_soyad_getir=getir("uyeler","ad_soyad",$oturum_id);
			$magaza_baslik=getir("magazalar","baslik",$oturum_id);
			$ileti_admin="
			<p><strong>$ad_soyad_getir</strong> adlı üye $magaza_baslik adlı mağaza için paket yükseltme siparişi oluşturdu</p>
			<p><a href=\"$site_url\">Siteyi Görüntüle</a> - <a href=\"$site_url/$admin_dizini\">Yönetim Paneli</a></p>
			";
			$site_email=ayar("site_email");
			auto_mail_add($mail_baslik,$site_email,"Yeni mağaza paketi yükseltme siparişi: $paket_tutar TL",$ileti_admin);
			// E-posta gönderimi bitti	
			
			yonlendir(ozel_url("siparis")."?siparis_id=$siparis_id");

		}	
	
	}
	
}



tema_header(); ?>
<div class="container profil arsiv mt20">
	<?php include("temalar/$aktif_tema/inc/magaza-ust.php"); ?>
	<?php include("temalar/$aktif_tema/inc/magaza-limit-bilgi.php"); ?>
	
	<div class="row">
		<div class="col-md-9 sidebar">
			<?php include("temalar/$aktif_tema/inc/magaza-sol-menu.php"); ?>
		</div>
		<div class="col-md-33">
			<div class="row">
				<div class="col-md-25">
					<div class="panel panel-default limon">
						<div><?php echo _LANG('hb_istatistikler'); ?></div>
						<div class="panel-body" style="padding:0 !important;">
							<table class="table table-default profil padding" style="margin-bottom:0;">
								<tr>
									<td><?php echo _LANG('hb_toplam_ilan'); ?></td>
									<td><?php echo magaza_ilan_limiti("mevcut",$id); ?></td>
								</tr>						
								<tr>
									<td><?php echo _LANG('hb_toplam_yayinda_onay_bekliyor'); ?></td>
									<td><?php echo magaza_ilan_limiti("yayinda",$id); ?></td>
								</tr>
							</table>
						</div>
					</div>
					<?php include("temalar/$aktif_tema/inc/alert.php"); ?>
					<?php 
					$ozel_limitler=magaza_bilgi("ozel_limitler",$id);
					$paket=magaza_bilgi("paket",$id);
					$paket_adi=liste_bilgi("paket_adi",$paket);
					$aninda_onay=magaza_ilan_limiti("aninda_onay",$id);
					?>
					<?php if ($ozel_limitler==1) { ?>
					<div><?php echo _LANG('hb_magazaniz_icin_ozel_limitler_tanimlanmistir'); ?></div>
					<?php } ?>
					<div class="panel panel-default limon">
						<div class="panel-heading">Mevcut Limitler</div>
						<div class="panel-body" style="padding:0 !important;">
							<table class="table table-default profil padding" style="margin-bottom:0;">
								<tr>
									<td width="200">Durum</td>
									<td><?php $magaza_durum=getir("magazalar","durum",$id); if ($magaza_durum==1) {echo "<span class='label label-success borderless'>Onaylı</span>";} else if ($magaza_durum==0) {echo "<span class='label label-danger borderless'>Pasif</span>";} else if ($magaza_durum==2) {echo "<span class='label label-danger borderless'>Süresi doldu</span>";}?></td>
								</tr>							
								<?php if ($ozel_limitler==1) { ?>
								<tr>
									<td><?php echo _LANG('hb_ozel_limitler'); ?></td>
									<td>Evet</td>
								</tr>			
								<?php } else { ?>
								<tr>
									<td>Plan</td>
									<td><?php echo $paket_adi; ?></td>
								</tr>
								<?php } ?>
								<tr>
									<td><?php echo _LANG('hb_ilan_limiti'); ?></td>
									<td><?php echo magaza_ilan_limiti("toplam",$id); ?></td>
								</tr>
								<tr>
									<td><?php echo _LANG('hb_danisman_limiti'); ?></td>
									<td><?php echo magaza_ilan_limiti("danisman",$id); ?></td>
								</tr>
								<tr>
									<td><?php echo _LANG('hb_fotograf_limiti'); ?></td>
									<td><?php echo magaza_ilan_limiti("foto",$id); ?></td>
								</tr>
								<tr>
									<td><?php echo _LANG('hb_aninda_onay'); ?></td>
									<td><?php if ($aninda_onay==1) { ?><span class="label label-success borderless">Evet</span><?php } else { ?><span class="label label-danger borderless"><?php echo _LANG("general_no"); ?></span><?php } ?></td>
								</tr>
							</table>
						</div>
					</div>			
					
					
					<div class="panel panel-default limon">
						<div><?php echo _LANG('hb_plan_yukselt'); ?></div>
						<div class="panel-body">
							<form action="" method="post">
							<?php 
							$paket_seciniz_sql=mysqli_query($baglan,"select l_id from listeler where tip='magaza_paketleri'");
							while ($paket_seciniz_dizi=mysqli_fetch_array($paket_seciniz_sql)) {
								$paket_id=$paket_seciniz_dizi["l_id"];
								$paket_baslik=liste_bilgi("paket_adi",$paket_id);
								$paket_seciniz[]=array("deger"=>"$paket_id","baslik"=>"$paket_baslik");
							}
							
							form_group(array("tip"=>"select","id"=>"mevcut_paket","baslik"=>"Mevcut Paket","secenekler"=>$paket_seciniz,"deger"=>$paket,"pasif"=>1));
							
							$magaza_paket_bitis_tarihi=magaza_bilgi("paket_bitis_tarihi",$id);
							if (!$magaza_paket_bitis_tarihi) {
								$magaza_paket_bitis_tarihi=_LANG('tx_bu_magaza_henuz_onaylanmadi');
							}
								form_group(array("tip"=>"text","id"=>"paket_bitis_tarihi","baslik"=>_LANG('s_paket_bitis'),"deger"=>$magaza_paket_bitis_tarihi,"pasif"=>1));							

							form_group(array("tip"=>"select","id"=>"paket","baslik"=>"Paket","secenekler"=>$paket_seciniz,"deger"=>$paket));
							
							form_group(array("tip"=>"checkbox","id"=>"onay","baslik"=>_LANG('s_magaza_paket_onay'),"gerekli"=>1));
							form_buton("buton",_LANG('tx_siparis_olustur'),"primary");							
							?>
							</form>
						</div>
					</div>
					
				</div>
				<div class="col-md-17">
					<div id="ajax_load"></div>
				</div>
			</div>
			
					<div class="panel panel-default limon tablo">
			
					<div class="panel-body text-center" style="padding:0;">
					<table class="table table-default table-hover" style="margin-bottom:0;">
						<thead class="ortala">
							<tr>
								<th></th>
								<th><?php echo _LANG("s_ilan_limiti"); ?></th>
								<th><?php echo _LANG("s_fotograf_limiti"); ?></th>
								<th><?php echo _LANG("s_danisman_limiti_k"); ?></th>
								<th><?php echo _LANG("s_aninda_onay"); ?></th>
								<th>Teknik destek</th>
                                <th><?php echo _LANG("s_ilan_adet_ucreti"); ?></th>
                                <th><?php echo _LANG("s_ucret"); ?></th>
							</tr>
						</thead>
						<tbody>
							<?php 
							$paket_sql=mysqli_query($baglan,"select l_id from listeler where tip='magaza_paketleri'");
							while ($paket_dizi=mysqli_fetch_array($paket_sql)) {
								$paket_id=$paket_dizi["l_id"];
								$paket_baslik=liste_bilgi("paket_adi",$paket_id);
								$ilan_limit=liste_bilgi("ilan_limit",$paket_id);
								$foto_limit=liste_bilgi("foto_limit",$paket_id);
								$danisman_limit=liste_bilgi("danisman_limit",$paket_id);
								if (!$danisman_limit) {
									$danisman_limit=0;
								}
								$aninda_onay=liste_bilgi("aninda_onay",$paket_id);

                                $paket_ucret=liste_bilgi("ucret",$paket_id);
                                $ilan_basina_ucret=number_format($paket_ucret/$ilan_limit,2);
                                $ibu_explode=explode(".",$ilan_basina_ucret);
                                if ($ibu_explode[1]=="00") {
                                    $ilan_basina_ucret=substr($ilan_basina_ucret,0,-3);
                                } else {
                                    $ilan_basina_ucret=str_replace(".",",",$ilan_basina_ucret);
                                }
							?>
							<tr>
								<th><?php echo $paket_baslik; ?></th>
								<td><?php echo $ilan_limit; ?></td>
								<td><?php echo $foto_limit; ?></td>
								<td><?php echo $danisman_limit; ?></td>
								<td><?php if ($aninda_onay==1) {echo "<span class='label label-success borderless'>Evet</span>";} else {echo "<span class='label label-danger borderless'>Hayır</span>";} ?></td>
								<td>7/24</td>
								<td><?php if ($ilan_basina_ucret) {echo $ilan_basina_ucret." <i class='fa fa-try'></i>";} else {echo "<span class='label label-success borderless'>Ücretsiz</span>";} ?></td>
                                <td><?php if ($paket_ucret) {echo $paket_ucret." <i class='fa fa-try'></i>";} else {echo "<span class='label label-success borderless'>Ücretsiz</span>";} ?></td>
							</tr>
							<?php } ?>
						</tbody>
					</table>								
				</div>
				
			</div>		
			
		</div>
	</div>
</div>
<?php tema_footer(); ?>
<script type="text/javascript">
$(function(){
	var paket=$("#paket").val();
	if (paket!="") {
			$("#ajax_load").load("<?php echo tema_dizini(); ?>/ajax.php?islem=magaza_bilgi&kategori=<?php echo getir("magazalar","kategori",$id); ?>&paket="+paket);
	}	

	$("body").on("change","#paket",function(){
		var paket=$("#paket").val();
		if (paket!="") {
			$("#ajax_load").load("<?php echo tema_dizini(); ?>/ajax.php?islem=magaza_bilgi&kategori=<?php echo getir("magazalar","kategori",$id); ?>&paket="+paket);
		}
	});

	$("button[name=buton]").attr("disabled", true);
	
	$("#form_onay input").change(function() {
		if(this.checked) {
			$("button[name=buton]").attr("disabled", false);
		} else {
			$("button[name=buton]").attr("disabled", true);
		}
	});	
	
});
</script>