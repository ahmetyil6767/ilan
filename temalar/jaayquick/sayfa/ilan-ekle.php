<?php 
if(count(get_included_files())==1) {die();}
if (!$oturum_id) {
	$_SESSION["mesaj"]=array(_LANG('msj_uye_girisi_gerekli'),"danger");
	yonlendir(ozel_url("giris-yap"));
	exit();
}

// null
$limit_alert_kod=null;

// İlan limitleri
$limit_politikasi=limit_politikasi();

// $_GET
@$kat=mysqli_real_escape_string($baglan,$_GET["kat"]);
@$ilan=mysqli_real_escape_string($baglan,$_GET["ilan"]);
@$magaza_id=mysqli_real_escape_string($baglan,$_GET["magaza"]);

// SEÇİM YAP
if ($magaza_id) {
	$magaza_adi=getir("magazalar","baslik",$magaza_id);
	$magaza_kategori=getir("magazalar","kategori",$magaza_id);
	$magaza_kategori_adi=_DIL_BASLIK(getir("kategoriler","baslik",$magaza_kategori), $magaza_kategori, "kategori");
	$magaza_durum=getir("magazalar","durum",$magaza_id);
	$ilan_eklenebilir_magazalar=ilan_eklenebilir_magazalar($oturum_id);
	if (!in_array($magaza_id,$ilan_eklenebilir_magazalar)) {
		$_SESSION["mesaj"]=array(_LANG('msj_magazada_yetkiniz_yok'),"danger");
		yonlendir(ozel_url("ilan-ekle"));
	}
	if ($magaza_durum!=1) {
		$_SESSION["mesaj"]=array(_LANG('msj_magaza_onaylanmadi'),"danger");
		yonlendir(ozel_url("ilan-ekle"));
	}
}
if ($kat) {
	$kategori_durum=getir("kategoriler","durum",$kat);
	if ($kategori_durum==2) {
		$_SESSION["mesaj"]=array(_LANG('msj_kategoride_ilan_eklenemez'),"danger");
		yonlendir(ozel_url("ilan-ekle"));
	}
	$kategori_sinif=getir("kategoriler","sinif",$kat);
	if ($kategori_sinif=="kategori") {
		$alt_kat_sayisi=alt_kategori_sayisi($kat);
		if ($alt_kat_sayisi>0) {
			$_SESSION["mesaj"]=array(_LANG('msj_kategoride_ilan_eklenemez'),"danger");
			yonlendir(ozel_url("ilan-ekle"));
		}
	}
}
@$buton=$_POST["buton"];

// FORM İŞLEMLERİ
if ($buton==_LANG('tx_ilani_kaydet') or $buton==_LANG('tx_ilani_yayinla') or $buton==_LANG('tx_hemen_yayinla')) {
	@$baslik=trim($_POST["baslik"]);
	@$icerik=addslashes($_POST["icerik"]);
	@$kategori_dizisi=$_POST["kategori"];
	@$url=addslashes($_POST["url"]);
	
	if (is_array($kategori_dizisi)) {
		foreach ($kategori_dizisi as $yaz) {
			if ($yaz!=_LANG('p_select')) {
				$kategori.=",($yaz)";
			}
		}
		$kategori=ltrim($kategori,",");		
	}
	
	if ($url=="") {
		$url=url_olustur($baslik);
	}
	
	@$durum=0;
	if ($buton==_LANG('tx_ilani_yayinla')) {$durum=2;}
	if ($buton==_LANG('tx_hemen_yayinla')) {$durum=1;}
	
	// Ekstra bilgiler (fiyat, para birimi, iletişim vb.)
	@$fiyat=trim($_POST["fiyat"]);
	@$fiyat_usd=trim($_POST["fiyat_usd"]);
	@$fiyat_eur=trim($_POST["fiyat_eur"]);
	@$fiyat_gbp=trim($_POST["fiyat_gbp"]);
	@$para_birimi=trim($_POST["para_birimi"]);
	@$iletisim=trim($_POST["iletisim"]);
	@$yetkili=trim($_POST["yetkili"]);
	@$yetkili_tel=trim($_POST["yetkili_tel"]);
	
	// Ozel alanlar
	$ozel_alanlar_str=null;
	foreach ($_POST as $key => $value) {
		if (substr($key, 0, 3)=="oa_") {
			$alan_anahtar=substr($key, 3);
			$alan_deger=addslashes($value);
			if ($alan_deger!="") {
				$ozel_alanlar_str.="$alan_anahtar|$alan_deger,";
			}
		}
	}
	if ($ozel_alanlar_str) {
		$ozel_alanlar_str=rtrim($ozel_alanlar_str,",");
	}
	
	$tarih_saat=tarih_saat();
	$yazar=$oturum_id;
	
	if ($baslik!="" and $kategori!="" and $icerik!="") {
		// İlan limit kontrolü
		$kalan_ilan_limit=uye_ilan_limiti("kalan",$oturum_id);
		$kategori_ilan_limit=uye_ilan_limiti("kategori",$oturum_id);
		$kategori_ilan_sayisi=mysqli_num_rows(mysqli_query($baglan,"select * from icerik where yazar='$oturum_id' and durum='1' and kategori like '%($kategori_dizisi[0])%'"));
		$kategori_kalan_ilan_limit=$kategori_ilan_limit-$kategori_ilan_sayisi;
		$ilan_aninda_onay=uye_ilan_limiti("aninda_onay",$oturum_id);
		
		$limit_kontrol=1;
		if ($limit_politikasi==1) {
			if ($kategori_kalan_ilan_limit<=0) {
				$_SESSION["mesaj"]=array(_LANG('pn_kategori_ilan_limitiniz_doldugu_icin_bu_ilani_yayinlayamazsiniz'),"danger");
				$limit_kontrol=0;
			}
			if ($kalan_ilan_limit<=0) {
				$_SESSION["mesaj"]=array(_LANG('pn_ilan_limitiniz_doldugu_icin_bu_ilani_yayinlayamazsiniz'),"danger");
				$limit_kontrol=0;
			}
		}
		
		if ($limit_kontrol==1) {
			if ($durum==1 and $ilan_aninda_onay!=1) {
				$durum=2; // Onay bekliyor
			}
			
			$sql=mysqli_query($baglan,"insert into icerik set baslik='$baslik', url='$url', icerik='$icerik', kategori='$kategori', tarih_saat='$tarih_saat', yazar='$yazar', durum='$durum', tip='ilan', ozel_alanlar='$ozel_alanlar_str'");
			$eklenen_id=mysqli_insert_id($baglan);
			
			if ($eklenen_id) {
				foreach ($kategori_dizisi as $kat_yaz) {
					kategori_icerik_sayisi_guncelle($kat_yaz);
				}
				
				// Ekstra bilgileri kaydet
				$ekstra_bilgiler=array(
					"fiyat"=>$fiyat,
					"fiyat_usd"=>$fiyat_usd,
					"fiyat_eur"=>$fiyat_eur,
					"fiyat_gbp"=>$fiyat_gbp,
					"para_birimi"=>$para_birimi,
					"iletisim"=>$iletisim,
					"yetkili"=>$yetkili,
					"yetkili_tel"=>$yetkili_tel
				);
				
				foreach ($ekstra_bilgiler as $anahtar => $deger) {
					if ($deger!=="") {
						ekstra_bilgi_guncelle($anahtar,$deger,$eklenen_id,"icerik");
					}
				}
				
				// Magaza ID kaydet
				if ($magaza_id) {
					ekstra_bilgi_guncelle("magaza",$magaza_id,$eklenen_id,"icerik");
					limit_dusur($eklenen_id);
				}
				
				// Resim yükleme
				$tip_gorsel_dizin="ajax";
				$resim_boyutlandir=array("160_120","530_393");
				
				if (isset($_FILES["gorsel"]) and $_FILES["gorsel"]["name"][0]!="") {
					$secenekler=array();
					$secenekler["boyutlar"]=$resim_boyutlandir;
					$secenekler["dizin"]=$tip_gorsel_dizin;
					$secenekler["orijinali_sil"]=1;
					$secenekler["cikti"]="full";
					
					$resim_dizisi=$_FILES["gorsel"];
					$resim_url=yukle($resim_dizisi,$secenekler);
					if ($resim_url) {
						ekstra_bilgi_guncelle("gorsel",$resim_url,$eklenen_id,"icerik");
					}
				}
				
				// Galeri yükleme
				if (isset($_FILES["galeri"]) and $_FILES["galeri"]["name"][0]!="") {
					$secenekler=array();
					$secenekler["boyutlar"]=$resim_boyutlandir;
					$secenekler["dizin"]=$tip_gorsel_dizin;
					$secenekler["orijinali_sil"]=1;
					$secenekler["cikti"]="galeri";
					
					$resim_dizisi=$_FILES["galeri"];
					$resim_url=yukle($resim_dizisi,$secenekler);
					if ($resim_url) {
						ekstra_bilgi_guncelle("galeri",$resim_url,$eklenen_id,"icerik");
					}
				}
				
				$_SESSION["mesaj"]=array(_LANG('pn_ilan_basariyla_eklendi'),"success");
				
				if ($durum==1) {
					$_SESSION["mesaj"]=array(_LANG('pn_ilan_basariyla_yayinlandi'),"success");
				} elseif ($durum==2) {
					$_SESSION["mesaj"]=array(_LANG('pn_ilan_inceleme_icin_gonderildi'),"success");
				}
				
				yonlendir(link_getir($eklenen_id));
			}
		}
	} else {
		if ($baslik=="") $_SESSION["mesaj"]=array(_LANG('msj_baslik_gerekli'),"danger");
		if ($kategori=="") $_SESSION["mesaj"]=array(_LANG('msj_kategori_gerekli'),"danger");
		if ($icerik=="") $_SESSION["mesaj"]=array(_LANG('msj_icerik_gerekli'),"danger");
	}
}

tema_header(); ?>
<div class="container profil arsiv mt20">
	<?php include("temalar/$aktif_tema/inc/uye-ust.php"); ?>
	
	<div class="row">
		<div class="col-md-9 sidebar">
			<div class="panel panel-default jaayquick menu">
				<div class="panel-heading"><?php echo _LANG("menu_ilan_ekle"); ?></div>
				<div class="panel-body">
					<ul>
						<li class="active"><a href="<?php echo ozel_url("ilan-ekle"); ?>"><?php echo _LANG("menu_ilan_ekle"); ?></a></li>
						<li><a href="<?php echo ozel_url("ilanlarim"); ?>"><?php echo _LANG("nav_my_ads"); ?></a></li>
					</ul>
				</div>
			</div>
		</div>
		
		<div class="col-md-33">
			<?php include("temalar/$aktif_tema/inc/alert.php"); ?>
			<div class="panel panel-default jaayquick">
				<div class="panel-heading"><?php echo _LANG("menu_ilan_ekle"); ?></div>
				<div class="panel-body">
					<form action="" method="post" enctype="multipart/form-data" id="ilan_form">
						
						<div class="form-group">
							<label for="baslik"><?php echo _LANG('post_title'); ?> <span style="color:#e74c3c;">*</span></label>
							<input type="text" class="form-control" id="baslik" name="baslik" value="<?php echo @htmlspecialchars($_POST['baslik']); ?>" required>
						</div>
						
						<div class="form-group">
							<label for="kategori"><?php echo _LANG('post_category'); ?> <span style="color:#e74c3c;">*</span></label>
							<?php 
							$kategori_siralama=tema_ayar("ilan_ekle_kategori_siralama");
							if ($kategori_siralama=="baslik_a_z") {
								$kategori_sql=mysqli_query($baglan,"select * from kategoriler where tip='ilan' and ust_kategori='0' order by baslik asc");
							} else if ($kategori_siralama=="icerik_sayisi") {
								$kategori_sql=mysqli_query($baglan,"select * from kategoriler where tip='ilan' and ust_kategori='0' order by icerik desc, baslik");
							} else {
								$kategori_sql=mysqli_query($baglan,"select * from kategoriler where tip='ilan' and ust_kategori='0'");
							}
							?>
							<select class="form-control" id="kategori" name="kategori[]" multiple required style="height:150px;">
								<?php while ($kategori_dizi=mysqli_fetch_array($kategori_sql)) { 
									$kat_id=$kategori_dizi["id"];
									$kat_baslik=$kategori_dizi["baslik"];
									$selected=(in_array($kat_id, @$_POST["kategori"])) ? "selected" : "";
									echo "<option value='$kat_id' $selected>$kat_baslik</option>";
								} ?>
							</select>
							<p class="text-muted small"><?php echo _LANG('pn_ctrl_tusu_ile_coklu_secim'); ?></p>
						</div>
						
						<div class="form-group">
							<label for="icerik"><?php echo _LANG('post_description'); ?> <span style="color:#e74c3c;">*</span></label>
							<textarea class="form-control ckeditor" id="icerik" name="icerik" rows="10" required><?php echo @htmlspecialchars($_POST['icerik']); ?></textarea>
						</div>
						
						<!-- Fiyat ve Para Birimi -->
						<div class="form-group">
							<label><?php echo _LANG('listing_price'); ?></label>
							<div class="row">
								<div class="col-md-12">
									<select class="form-control" id="para_birimi" name="para_birimi" onchange="paraBirimiDegisti(this.value)">
										<option value="gmd" <?php echo (@$_POST['para_birimi']=='gmd' || !@$_POST['para_birimi']) ? 'selected' : ''; ?>>GMD (D)</option>
										<option value="usd" <?php echo (@$_POST['para_birimi']=='usd') ? 'selected' : ''; ?>>USD ($)</option>
										<option value="eur" <?php echo (@$_POST['para_birimi']=='eur') ? 'selected' : ''; ?>>EUR (€)</option>
										<option value="gbp" <?php echo (@$_POST['para_birimi']=='gbp') ? 'selected' : ''; ?>>GBP (£)</option>
									</select>
								</div>
							</div>
							<div class="row mt10" id="fiyat_alanlari">
								<div class="col-md-6" id="fiyat_gmd_alan" style="display:block;">
									<input type="text" class="form-control" name="fiyat" placeholder="Fiyat (GMD)" value="<?php echo @htmlspecialchars($_POST['fiyat']); ?>">
								</div>
								<div class="col-md-6" id="fiyat_usd_alan" style="display:none;">
									<input type="text" class="form-control" name="fiyat_usd" placeholder="Price (USD)" value="<?php echo @htmlspecialchars($_POST['fiyat_usd']); ?>">
								</div>
								<div class="col-md-6" id="fiyat_eur_alan" style="display:none;">
									<input type="text" class="form-control" name="fiyat_eur" placeholder="Price (EUR)" value="<?php echo @htmlspecialchars($_POST['fiyat_eur']); ?>">
								</div>
								<div class="col-md-6" id="fiyat_gbp_alan" style="display:none;">
									<input type="text" class="form-control" name="fiyat_gbp" placeholder="Price (GBP)" value="<?php echo @htmlspecialchars($_POST['fiyat_gbp']); ?>">
								</div>
							</div>
						</div>
						
						<!-- İletişim Bilgileri -->
						<div class="form-group">
							<label><?php echo _LANG('post_contact'); ?></label>
							<select class="form-control" id="iletisim" name="iletisim" onchange="iletisimDegisti(this.value)">
								<option value="1" <?php echo (@$_POST['iletisim']=='1' || !@$_POST['iletisim']) ? 'selected' : ''; ?>><?php echo _LANG('profil_iletisim_bilgileri'); ?></option>
								<option value="2" <?php echo (@$_POST['iletisim']=='2') ? 'selected' : ''; ?>><?php echo _LANG('ozel_iletisim'); ?></option>
							</select>
						</div>
						<div class="row" id="ozel_iletisim_alan" style="display:none;">
							<div class="col-md-12">
								<div class="form-group">
									<label><?php echo _LANG('s_yetkili_kisi'); ?></label>
									<input type="text" class="form-control" name="yetkili" value="<?php echo @htmlspecialchars($_POST['yetkili']); ?>">
								</div>
							</div>
							<div class="col-md-12">
								<div class="form-group">
									<label>Yetkili Telefon</label>
									<input type="text" class="form-control" name="yetkili_tel" value="<?php echo @htmlspecialchars($_POST['yetkili_tel']); ?>">
								</div>
							</div>
						</div>
						
						<!-- Özel Alanlar (Kategoriye özel) -->
						<div id="ozel_alanlar_container">
							<?php 
							if ($kat) {
								$kategoriler=kategori_getir($kat);
								if (is_array($kategoriler)) {
									foreach ($kategoriler as $kat_id) {
										$kat_ozel_alan=kategori_bilgi("ozel_alanlar",$kat_id);
										if ($kat_ozel_alan) {
											$ozel_alanlar=$kat_ozel_alan;
										}
									}
								}
								if (isset($ozel_alanlar)) {
									$alan_dizi=explode("\r\n",$ozel_alanlar);
									echo "<hr><h5>" . _LANG('hb_ozel_alanlar') . "</h5>";
									foreach ($alan_dizi as $alan) {
										$alan_parcala=explode("|",$alan);
										$anahtar=trim($alan_parcala[0]);
										$baslik=trim($alan_parcala[1]);
										if (!$baslik) $baslik=$anahtar;
										@$gerekli=trim($alan_parcala[2]);
										@$secenekler=trim($alan_parcala[3]);
										$deger=@$_POST["oa_$anahtar"];
										$gerekli_yildiz=($gerekli==1) ? "<span style='color:#e74c3c;'>*</span>" : "";
										if (!$secenekler) { ?>
											<div class="form-group">
												<label for="oa_<?php echo $anahtar; ?>"><?php echo $baslik; ?> <?php echo $gerekli_yildiz; ?></label>
												<input type="text" class="form-control" id="oa_<?php echo $anahtar; ?>" name="oa_<?php echo $anahtar; ?>" placeholder="<?php echo $baslik; ?>" value="<?php echo htmlspecialchars($deger); ?>" <?php echo ($gerekli==1) ? 'required' : ''; ?>>
											</div>
										<?php } else { ?>
											<div class="form-group">
												<label for="oa_<?php echo $anahtar; ?>"><?php echo $baslik; ?> <?php echo $gerekli_yildiz; ?></label>
												<select class="form-control" id="oa_<?php echo $anahtar; ?>" name="oa_<?php echo $anahtar; ?>" <?php echo ($gerekli==1) ? 'required' : ''; ?>>
													<option value=""><?php echo _LANG('filter_select'); ?></option>
													<?php 
													$secenekler_dizi=explode(",",$secenekler);
													foreach ($secenekler_dizi as $sec) {
														$sel=($deger==$sec) ? "selected" : "";
														echo "<option value='$sec' $sel>$sec</option>";
													}
													?>
												</select>
											</div>
										<?php }
									}
								}
							}
							?>
						</div>
						
						<!-- Resim Yükleme -->
						<div class="form-group">
							<label><?php echo _LANG('post_photos'); ?></label>
							<div class="row">
								<div class="col-md-6">
									<label class="text-muted small">Kapak Görseli (Ana Görsel)</label>
									<input type="file" class="form-control" name="gorsel" accept="image/*">
								</div>
								<div class="col-md-6">
									<label class="text-muted small">Galeri Görselleri (Çoklu Seçim)</label>
									<input type="file" class="form-control" name="galeri[]" multiple accept="image/*">
								</div>
							</div>
						</div>
						
						<!-- Butonlar -->
						<div class="clearfix mb20"></div>
						<button type="submit" name="buton" value="<?php echo _LANG('tx_ilani_kaydet'); ?>" class="btn btn-default pull-left"><?php echo _LANG('tx_ilani_kaydet'); ?></button>
						<button type="submit" name="buton" value="<?php echo _LANG('tx_ilani_yayinla'); ?>" class="btn btn-info pull-right"><?php echo _LANG('tx_ilani_yayinla'); ?></button>
						<button type="submit" name="buton" value="<?php echo _LANG('tx_hemen_yayinla'); ?>" class="btn btn-success pull-right mr10"><?php echo _LANG('tx_hemen_yayinla'); ?></button>
						<div class="clearfix"></div>
						
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
<?php tema_footer(); ?>
<script>
function paraBirimiDegisti(val) {
	$('#fiyat_alanlari > div').hide();
	$('#fiyat_' + val + '_alan').show();
}
function iletisimDegisti(val) {
	if (val==2) {
		$('#ozel_iletisim_alan').show();
	} else {
		$('#ozel_iletisim_alan').hide();
	}
}
// Initialize on load
$(document).ready(function() {
	paraBirimiDegisti($('#para_birimi').val());
	iletisimDegisti($('#iletisim').val());
});
</script>