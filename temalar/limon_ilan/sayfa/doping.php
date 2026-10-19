<?php 
if(count(get_included_files())==1) {die();}
if (!$oturum_id) {
	$_SESSION["mesaj"]=array(_LANG('pn_bu_sayfayi_goruntuleyebilmek_icin_uye_girisi_yapmalisiniz'),".");
	yonlendir(ozel_url("giris-yap"));
	exit();
}
$ilan_id=$_GET["ilan_id"];
if ($ilan_id and !is_numeric($ilan_id)) {
	yonlendir(ozel_url("doping"));
}
if ($ilan_id) {
	$ilan_sahibi=getir("icerik","yazar",$ilan_id);
	$oturum_id=$_SESSION["oturum_id"];
	if ($ilan_sahibi!=$oturum_id) {
		$_SESSION["mesaj"]=array(_LANG('pn_sadece_kendi_ilanlariniza_doping_satin_alabilirsiniz'),".");
		yonlendir(ozel_url("doping"));
	}
}
@$buton=$_POST["buton"];
@$anasayfa_vitrini=$_POST["anasayfa_vitrini"];
@$kategori_vitrini=$_POST["kategori_vitrini"];
@$acil_acil=$_POST["acil_acil"];
@$kalin_yazi=$_POST["kalin_yazi"];
@$guncelim=$_POST["guncelim"];

if ($buton) {
	if ($anasayfa_vitrini) {
		$dopingler[]="anasayfa_vitrini";
		$tutar+=$anasayfa_vitrini;
	}
	if ($kategori_vitrini) {
		$dopingler[]="kategori_vitrini";
		$tutar+=$kategori_vitrini;
	}
	if ($acil_acil) {
		$dopingler[]="acil_acil";
		$tutar+=$acil_acil;
	}
	if ($kalin_yazi) {
		$dopingler[]="kalin_yazi";
		$tutar+=$kalin_yazi;
	}
	if ($guncelim) {
		$dopingler[]="guncelim";
		$tutar+=$guncelim;
	}
	$say=count($dopingler);
	if ($say>0) {
		// Sipariş oluştur
		$tarih_saat=tarih_saat();
		$ekle=mysqli_query($baglan,"insert into siparisler set alici_id='$oturum_id', ilan_id='$ilan_id', tutar='$tutar', tarih_saat='$tarih_saat', tip='doping', durum='0'");
		$eklenen_id=mysqli_insert_id($baglan);
		$gun_sayisi=tema_ayar("doping_gecerlilik");
		foreach ($dopingler as $doping) {
			$doping_ekle=mysqli_query($baglan,"insert into dopingler set ilan_id='$ilan_id', doping='$doping', gun='$gun_sayisi', siparis_id='$eklenen_id', tarih_saat='$tarih_saat', durum='0'");
		}
		$ad_soyad_getir=bilgi_getir("uyeler","ad_soyad",$oturum_id);
		if (function_exists('lpanel_uye_profil_link')) {
			$link=lpanel_uye_profil_url($oturum_id);
			bildirim_ekle(sprintf(_LANG('bnf_doping_siparisi_linkli'), $link, $ad_soyad_getir),$oturum_id,0);
		} else {
			bildirim_ekle(sprintf(_LANG('bnf_doping_siparisi'), $ad_soyad_getir),$oturum_id,0);
		}
		
		// E-posta gönderimi başladı
		$mail_baslik=tema_ayar("mail_baslik");
		$site_url=site_url();
		$auto_mail_ilan_baslik=getir("icerik","baslik",$ilan_id);
		$ileti_admin="
		<p><strong>$ad_soyad_getir</strong> adlı üye yeni bir doping siparişi oluşturdu</p>
		<p>İlan: <strong>$auto_mail_ilan_baslik</strong></p>
		<p><a href=\"$site_url\">Siteyi Görüntüle</a> - <a href=\"$site_url/$admin_dizini\">Yönetim Paneli</a></p>
		";
		$site_email=ayar("site_email");
		auto_mail_add($mail_baslik,$site_email,"Yeni doping siparişi: $tutar TL",$ileti_admin);						
		// E-posta gönderimi bitti			
		
		yonlendir(ozel_url("siparis")."?siparis_id=$eklenen_id");
	} else {
		$_SESSION["mesaj"]=array(_LANG('pn_siparis_olusturulurken_bir_sorun_olustu_lutfen_site_yoneticisi_ile_irt'),".");
		if ($ilan_id) {
			yonlendir(ozel_url("doping")."/?ilan_id=$ilan_id");
		} else {
			yonlendir(ozel_url("doping"));
		}
	}
}

tema_header(); 
?>
<div class="container profil arsiv mt20 doping">
	<?php include("temalar/$aktif_tema/inc/uye-ust.php"); ?>
	<div class="row">
		<div class="col-md-9 sidebar">
			<?php if ($oturum_id==$id) {include("temalar/$aktif_tema/inc/uye-sol-menu.php");} ?>
		</div>
		<div class="col-md-33">
			<?php include("temalar/$aktif_tema/inc/alert.php"); ?>
			
			<div class="panel panel-default limon">
				<?php if ($ilan_id) { ?>
				<div><?php echo _LANG('hb_doping_satin_al'); ?></div>
				<div class="panel-body" style="padding:0;">
					<table class="table table-condensed table-striped liste">
						<thead>
							<tr>
								<th width="110"></th>
								<th class="baslik" width="280"><?php echo _LANG("listing_title"); ?></th>
								<th>Fiyat</th>
								<th><?php echo _LANG("listing_date"); ?></th>
								<th>Durum</th>
								<th><?php echo _LANG("general_options"); ?></th>
							</tr>
						</thead>
						<tbody>
						<?php 
						$sql=mysqli_query($baglan,"select * from icerik where id='$ilan_id'");
						while ($dizi=mysqli_fetch_array($sql)) { 
							$ilan_id=$dizi["id"];
							$ilan_baslik=$dizi["baslik"];
							$ilan_durum=$dizi["durum"];
							$ilan_tarih=tarih_getir($ilan_id);
							$ilan_fiyat=fiyat($ilan_id);
							$ilan_link=link_getir($ilan_id);
							$ilan_resim=resim_getir("160_120",$ilan_id);
							$ilan_ziyaret=postviews($ilan_id);
							$ilan_favori=kac_kisinin_favorisi($ilan_id);
						?>
							<tr>
								<td><a href="<?php echo $ilan_link; ?>" target="_blank"><img src="<?php echo $ilan_resim; ?>" alt="" class="img-responsive"/></a></td>
								<td class="baslik">
									<a href="<?php echo $ilan_link; ?>" target="_blank"><?php echo $ilan_baslik; ?></a>
								</td>
								<td class="fiyat"><?php echo $ilan_fiyat; ?></td>
								<td><?php echo $ilan_tarih; ?></td>
								<td><?php 
									if ($ilan_durum==0) {
										echo "<span class='label label-danger borderless'>Taslak</span>";
									} else if ($ilan_durum==1) {
										echo "<span class='label label-success borderless'>Yayında</span>";
									} else if ($ilan_durum==2) {
										echo "<span class='label label-info borderless'>Onay Bekliyor</span>";
									} else if ($ilan_durum==3) {
										echo "<span class='label label-warning borderless'>Yayından Kaldırıldı</span>";
									} else if ($ilan_durum==4) {
										echo "<span class='label label-danger borderless'>Süresi dolmuş</span>";
									}
								?></td>
								<td>
					
								<div class="btn-group" role="group" aria-label="...">
								  <a href="<?php echo ozel_url("ilan-ekle")."?ilan=$ilan_id"; ?>" class="btn btn-primary btn-xs"><?php echo _LANG("general_update"); ?></a>
								  
								  <div class="btn-group" role="group">
									<button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="caret ml0"></span></button>
									<ul class="dropdown-menu dropdown-menu-right">
									  <?php if ($ilan_durum==1) { ?><li><a href="<?php echo ozel_url("ilanlarim")."?pasif=$ilan_id"; ?>" onclick="return confirm('İlanı yayından kaldırmak istiyor musunuz?')"><?php echo _LANG("post_unpublish"); ?></a></li><?php } ?>
									  <li><a href="<?php echo ozel_url("doping")."?id=$ilan_id"; ?>"><?php echo _LANG("title_buy_boost"); ?></a></li>
									  <li><a href="<?php echo ozel_url("ilanlarim")."?sil=$ilan_id"; ?>" onclick="return confirm('İlanı kalıcı olarak silmek istiyor musunuz? Bu işlemin geri dönüşü olmayacaktır.')">Sil</a></li>
									</ul>
								  </div>
								</div>								

								</td>
							</tr>
						<?php }  ?>
						</tbody>
					</table>
				</div>
				<?php } else { ?>
				<div><?php echo _LANG('hb_lutfen_bir_ilan_seciniz'); ?></div>
				<div class="panel-body">
				<select id="jump" name="jump" class="form-control">
					<option><?php echo _LANG("p_ilan_seciniz"); ?></option>
					<?php 
					$ilanlar_sql=mysqli_query($baglan,"select id, baslik from icerik where tip='ilan' and yazar='$oturum_id'");
					while ($ilan_dizi=mysqli_fetch_array($ilanlar_sql)) { 
					$ilan_sec_id=$ilan_dizi["id"];
					$ilan_sec_baslik=$ilan_dizi["baslik"];
					?>
					<option value="<?php echo ozel_url("doping"); ?>?ilan_id=<?php echo $ilan_sec_id; ?>"><?php echo $ilan_sec_baslik; ?></option>
					<?php } ?>
				</select>
				</div>
				<div><?php echo _LANG('hb_doping_satin_alma_islemine_devam_edebilmek_ici'); ?></div>
				<?php }  ?>
			</div>
			
			<?php if ($ilan_id) { ?>
			<form action="" method="post">
			<div class="row">
				<div class="col-md-30" id="doping_ekle">
					<div class="row">
						<div class="col-md-21">
							<?php 
							$anasayfa_vitrini=tema_ayar("anasayfa_vitrini");
							$kategori_vitrini=tema_ayar("kategori_vitrini");
							$acil_acil=tema_ayar("acil_acil");
							$kalin_yazi=tema_ayar("kalin_yazi");
							$guncelim=tema_ayar("guncelim");
							$doping_gecerlilik=tema_ayar("doping_gecerlilik");
							if (!$anasayfa_vitrini) {
								$anasayfa_vitrini=0;
							}
							if (!$kategori_vitrini) {
								$kategori_vitrini=0;
							}
							if (!$acil_acil) {
								$acil_acil=0;
							}
							if (!$kalin_yazi) {
								$kalin_yazi=0;
							}
							if (!$guncelim) {
								$guncelim=0;
							}
							?>
							<div class="panel panel-default limon with_footer">
								<div class="panel-heading">Anasayfa Vitrini</div>
								<div class="panel-body">
									<p><?php echo _LANG('hb_ilaniniz_anasayfa_vitrininde_yer_alarak_daha_c'); ?></p>
									<?php if (!doping_sor($ilan_id,"anasayfa_vitrini")) { ?>
									<div class="sec">
										<div class="checkbox noselect">
											<label>
											  <input type="checkbox" name="anasayfa_vitrini" value="<?php echo $anasayfa_vitrini; ?>"> Dopingi Seç
											</label>
											<span class="pull-right"><?php echo $anasayfa_vitrini; ?> <i class="fa fa-try"></i> / <?php echo "$doping_gecerlilik gün"; ?></span>
										</div>
									</div>
									<?php } else {echo "<div class='sec aktif'><i class='ion-android-checkmark-circle'></i>Doping aktif</div>";} ?>
								</div>
							</div>
						</div>
						<div class="col-md-21">
							<div class="panel panel-default limon with_footer">
								<div class="panel-heading">Kategori Vitrini</div>
								<div class="panel-body">
									<p><?php echo _LANG('hb_ilaniniz_kategori_vitrininde_yer_alarak_daha_c'); ?></p>
									<?php if (!doping_sor($ilan_id,"kategori_vitrini")) { ?>
									<div class="sec">
									<div class="checkbox noselect">
										<label>
										  <input type="checkbox" name="kategori_vitrini" value="<?php echo $kategori_vitrini; ?>"> Dopingi Seç
										</label>
										<span class="pull-right"><?php echo $kategori_vitrini; ?> <i class="fa fa-try"></i> / <?php echo "$doping_gecerlilik gün"; ?></span>
									</div>
									</div>
									<?php } else {echo "<div class='sec aktif'><i class='ion-android-checkmark-circle'></i>Doping aktif</div>";} ?>
								</div>
							</div>
						</div>
						<div class="col-md-21">
							<div class="panel panel-default limon with_footer">
								<div class="panel-heading"><?php echo _LANG('mobile_menu_urgent'); ?></div>
								<div class="panel-body">
									<p><?php echo _LANG('s_acil_acil_bolum'); ?></p>
									<?php if (!doping_sor($ilan_id,"acil_acil")) { ?>
									<div class="sec">
									<div class="checkbox noselect">
										<label>
										  <input type="checkbox" name="acil_acil" value="<?php echo $acil_acil; ?>"> Dopingi Seç
										</label>
										<span class="pull-right"><?php echo $acil_acil; ?> <i class="fa fa-try"></i> / <?php echo "$doping_gecerlilik gün"; ?></span>
									</div>
									</div>
									<?php } else {echo "<div class='sec aktif'><i class='ion-android-checkmark-circle'></i>Doping aktif</div>";} ?>
								</div>
							</div>
						</div>
						<div class="col-md-21">
							<div class="panel panel-default limon with_footer">
								<div><?php echo _LANG('hb_kalin_yazi'); ?></div>
								<div class="panel-body">
									<p><?php echo _LANG('hb_ilaniniz_kalin_yazi_ile_diger_ilanlardan_farkl'); ?></p>
									<?php if (!doping_sor($ilan_id,"kalin_yazi")) { ?>
									<div class="sec">
									<div class="checkbox noselect">
										<label>
										  <input type="checkbox" name="kalin_yazi" value="<?php echo $kalin_yazi; ?>"> Dopingi Seç
										</label>
										<span class="pull-right"><?php echo $kalin_yazi; ?> <i class="fa fa-try"></i> / <?php echo "$doping_gecerlilik gün"; ?></span>
									</div>
									</div>
									<?php } else {echo "<div class='sec aktif'><i class='ion-android-checkmark-circle'></i>Doping aktif</div>";} ?>
								</div>
							</div>
						</div>
						<div class="col-md-21">
							<div class="panel panel-default limon with_footer">
								<div><?php echo _LANG('hb_guncelim'); ?></div>
								<div class="panel-body">
									<p><?php echo _LANG('hb_ilaniniziniz_yeni_bir_ilan_gibi_ust_siralarda'); ?></p>
									<div class="sec">
									<div class="checkbox noselect">
										<label>
										  <input type="checkbox" name="guncelim" value="<?php echo $guncelim; ?>"> Dopingi Seç
										</label>
										<span class="pull-right"><?php echo $guncelim; ?> <i class="fa fa-try"></i> / <?php echo "$doping_gecerlilik gün"; ?></span>
									</div>
									</div>
								</div>
							</div>
						</div>
					</div>				
						
				</div>
				<div class="col-md-12">
					<div id="magaza_bilgi" style="color:#fff;">
						<h1>Toplam Tutar</h1>
						<h3 style="margin-bottom:25px;"><span id="toplam_tutar">0</span><i class="fa fa-try"></i></h3>
						<?php 
						$doping_kurallari_sayfasi=link_getir(tema_ayar("doping_kurallari"));
						form_group(array("tip"=>"checkbox","id"=>"onay","baslik"=>"<a href='$doping_kurallari_sayfasi' target='_blank' style='color:#fff;'>Doping kurallarını</a> okudum, onaylıyorum.","gerekli"=>1));
						form_buton("buton",_LANG('tx_siparis_olustur'),"primary"); ?>
					</div>
				</div>
			</div>
			</form>
			<?php } ?>
			
			
		</div>
	</div>
</div>
<?php tema_footer(); ?>
<script type="text/javascript">
$(function(){

	$("#doping_ekle input").click(function(event) {
		updateTotal();
	});
	function updateTotal() {
		var total = 0;
		$("#doping_ekle input:checked").each(function() {
			total += parseFloat(this.value);
		});
		$('#toplam_tutar').text(total.toFixed(2));
	}	
	$("button[name=buton]").attr("disabled", true);
	$("#form_onay input").change(function() {
		if(this.checked) {
			$("button[name=buton]").attr("disabled", false);
		} else {
			$("button[name=buton]").attr("disabled", true);
		}
	});	
})

</script>