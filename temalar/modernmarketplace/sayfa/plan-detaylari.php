<?php 
if(count(get_included_files())==1) {die();}

if (limit_politikasi()==2) {
    yonlendir(ozel_url("paket-satin-al"));
} 

if (!$oturum_id) {
	$_SESSION["mesaj"]=array(_LANG('pn_bu_sayfayi_goruntuleyebilmek_icin_uye_girisi_yapmalisiniz'),".");
	yonlendir(ozel_url("giris-yap"));
	exit();
}

@$buton=mysqli_real_escape_string($baglan,$_POST["buton"]);
if ($buton) {
	$paket=mysqli_real_escape_string($baglan,$_POST["paket"]);
	$tarih_saat=tarih_saat();
	$paket_tutar=liste_bilgi("ucret",$paket);
	if ($paket_tutar>0) {
	$siparis_olustur=mysqli_query($baglan,"insert into siparisler set alici_id='$oturum_id', paket_id='$paket', tarih_saat='$tarih_saat', tip='paket', tutar='$paket_tutar', durum='0'");
	$siparis_id=mysqli_insert_id($baglan);

	// E-posta gönderimi başladı
	$mail_baslik=tema_ayar("mail_baslik");
	$site_url=site_url();
	$ad_soyad_getir=getir("uyeler","ad_soyad",$oturum_id);
	$ileti_admin="
	<p><strong>$ad_soyad_getir</strong> adlı üye yeni bir üyelik paketi siparişi oluşturdu</p>
	<p><a href=\"$site_url\">Siteyi Görüntüle</a> - <a href=\"$site_url/$admin_dizini\">Yönetim Paneli</a></p>
	";
	$site_email=ayar("site_email");
	auto_mail_add($mail_baslik,$site_email,sprintf(_LANG('b_yeni_uyelik_paketi_siparis'), $paket_tutar),$ileti_admin);
	// E-posta gönderimi bitti	
	
	yonlendir(ozel_url("siparis")."?siparis_id=$siparis_id");
	} else {
	$_SESSION["mesaj"]=array(_LANG('pn_lutfen_farkli_bir_paket_seciniz'),".");
	yonlendir(ozel_url("plan-detaylari"));		
	}
}
tema_header(); ?>
<div class="container profil arsiv mt20">
	<?php include("temalar/$aktif_tema/inc/uye-ust.php"); ?>
	<div class="row">
		<div class="col-md-9 sidebar">
			<?php if ($oturum_id==$id) {include("temalar/$aktif_tema/inc/uye-sol-menu.php");} ?>
		</div>
		<div class="col-md-33">
			<div class="row">
				<div class="col-md-25">
					<?php include("temalar/$aktif_tema/inc/alert.php"); ?>
					
					<?php if ($limit_politikasi==1) { ?>
					
					<div class="panel panel-default jaayquick">
						<div><?php echo _LANG('hb_istatistikler'); ?></div>
						<div class="panel-body" style="padding:0 !important;">
							<table class="table table-default profil padding" style="margin-bottom:0;">
								<tr>
									<td><?php echo _LANG('hb_toplam_ilan_limiti'); ?></td>
									<td colspan="2"><?php echo uye_ilan_limiti("toplam",$oturum_id); ?></td>
								</tr>
								<tr>
									<td><?php echo _LANG('hb_toplam_yayinda_onay_bekliyor'); ?></td>
									<td><?php echo uye_ilan_limiti("yayinda",$oturum_id); ?></td>
									<td>(Mağazaya kayıtlı: <?php echo uye_ilan_limiti("yayinda_magaza",$oturum_id); ?>)</td>
								</tr>	
								<tr>
									<td><?php echo _LANG('hb_mevcut_ilan'); ?></td>
									<td><?php echo uye_ilan_limiti("mevcut",$oturum_id); ?></td>
									<td>(Mağazaya kayıtlı: <?php echo uye_ilan_limiti("mevcut_magaza",$oturum_id); ?>)</td>
								</tr>								
								<tr>
									<td><?php echo _LANG('hb_kalan_ilan_limiti'); ?></td>
									<td colspan="2"><?php echo uye_ilan_limiti("kalan",$oturum_id); ?></td>
								</tr>
							</table>
						</div>
					</div>

					<?php 
					$ozel_limitler=uye_bilgi("ozel_limitler",$oturum_id);
					$paket=uye_bilgi("paket",$oturum_id);
					$paket_adi=liste_bilgi("paket_adi",$paket);
					$aninda_onay=uye_ilan_limiti("aninda_onay",$oturum_id);
					?>
					<?php if ($ozel_limitler==1) { ?>
					<div><?php echo _LANG('hb_profiliniz_icin_ozel_limitler_tanimlanmistir'); ?></div>
					<?php } ?>
					<div class="panel panel-default jaayquick">
						<div class="panel-heading">Mevcut Limitler</div>
						<div class="panel-body" style="padding:0 !important;">
							<table class="table table-default profil padding" style="margin-bottom:0;">
								<tr>
									<td width="200">Plan</td>
									<td><?php echo $paket_adi; ?></td>
								</tr>						
								<?php if ($ozel_limitler==1) { ?>
								<tr>
									<td><?php echo _LANG('hb_size_ozel_limit'); ?></td>
									<td>Evet</td>
								</tr>			
								<?php } ?>
								<tr>
									<td><?php echo _LANG('hb_toplam_ilan_limiti'); ?></td>
									<td><?php echo uye_ilan_limiti("toplam",$oturum_id); ?></td>
								</tr>
								<tr>
									<td><?php echo _LANG('hb_kategori_ilan_limiti'); ?></td>
									<td><?php echo uye_ilan_limiti("kategori",$oturum_id); ?></td>
								</tr>
								<tr>
									<td><?php echo _LANG('hb_fotograf_limiti'); ?></td>
									<td><?php echo uye_ilan_limiti("foto",$oturum_id); ?></td>
								</tr>
								<tr>
									<td><?php echo _LANG('hb_aninda_onay'); ?></td>
									<td><?php if ($aninda_onay==1) { ?><span class="label label-success borderless">Evet</span><?php } else { ?><span class="label label-danger borderless"><?php echo _LANG("general_no"); ?></span><?php } ?></td>
								</tr>
							</table>
						</div>
					</div>			
					
					<div class="panel panel-default jaayquick">
						<div><?php echo _LANG('hb_plan_yukselt'); ?></div>
						<div class="panel-body">
							<form action="" method="post">
							<?php 
							$paket_seciniz_sql=mysqli_query($baglan,"select l_id from listeler where tip='uyelik_paketleri'");
							while ($paket_seciniz_dizi=mysqli_fetch_array($paket_seciniz_sql)) {
								$paket_id=$paket_seciniz_dizi["l_id"];
								$paket_baslik=liste_bilgi("paket_adi",$paket_id);
								$paket_seciniz[]=array("deger"=>"$paket_id","baslik"=>"$paket_baslik");
							}
							form_group(array("tip"=>"select","id"=>"mevcut_paket","baslik"=>"Mevcut Paket","secenekler"=>$paket_seciniz,"deger"=>uye_bilgi("paket",$oturum_id),"pasif"=>1));
							form_group(array("tip"=>"text","id"=>"paket_bitis_tarihi","baslik"=>_LANG('s_paket_bitis'),"deger"=>uye_bilgi("paket_bitis_tarihi",$oturum_id),"pasif"=>1));
							form_group(array("tip"=>"select","id"=>"paket","baslik"=>"Paket","secenekler"=>$paket_seciniz,"deger"=>uye_bilgi("paket",$oturum_id)));
							form_group(array("tip"=>"checkbox","id"=>"onay","baslik"=>_LANG('s_uyelik_paket_onay'),"gerekli"=>1));
							form_buton("buton",_LANG('tx_siparis_olustur'),"primary");
							?>
							</form>
						</div>
					</div>
					
					<?php } else { ?>
					Azalan limitler
					
					<div class="panel panel-default jaayquick">
						<div><?php echo _LANG('hb_istatistikler'); ?></div>
						<div class="panel-body" style="padding:0 !important;">
							<table class="table table-default profil padding" style="margin-bottom:0;">
								<tr>
									<td><?php echo _LANG('hb_kalan_ilan_hakki'); ?></td>
									<td colspan="2"><?php echo uye_ilan_limiti("toplam",$oturum_id); ?></td>
								</tr>
								<tr>
									<td><?php echo _LANG('hb_toplam_yayinda_onay_bekliyor'); ?></td>
									<td><?php echo uye_ilan_limiti("yayinda",$oturum_id); ?></td>
									<td>(Mağazaya kayıtlı: <?php echo uye_ilan_limiti("yayinda_magaza",$oturum_id); ?>)</td>
								</tr>	
								<tr>
									<td><?php echo _LANG('hb_mevcut_ilan'); ?></td>
									<td><?php echo uye_ilan_limiti("mevcut",$oturum_id); ?></td>
									<td>(Mağazaya kayıtlı: <?php echo uye_ilan_limiti("mevcut_magaza",$oturum_id); ?>)</td>
								</tr>								
								<tr>
									<td><?php echo _LANG('hb_kalan_ilan_limiti'); ?></td>
									<td colspan="2"><?php echo uye_ilan_limiti("kalan",$oturum_id); ?></td>
								</tr>
							</table>
						</div>
					</div>
					
					
					<?php } ?>
			
				</div>
				<div class="col-md-17">
					<div id="ajax_load"></div>
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
			$("#ajax_load").load("<?php echo tema_dizini(); ?>/ajax.php?islem=uye_paket_bilgi&paket="+paket);
	}	

	$("body").on("change","#paket",function(){
		var paket=$("#paket").val();
		if (paket!="") {
			$("#ajax_load").load("<?php echo tema_dizini(); ?>/ajax.php?islem=uye_paket_bilgi&paket="+paket);
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