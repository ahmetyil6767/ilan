<?php 
if(count(get_included_files())==1) {die();}
@$cache=tema_ayar("cache");
$cache_klasor="cache/sayfa";
$cache_dosya_adi="sayfa";
$cache_dosyasi="$cache_klasor/$cache_dosya_adi-cache.html";
if ($cache==1 and !@$oturum_id and !$_POST) {
	$cache_klasor="cache/ilan";
	$cache_dosya_adi=$id;
	$cache_dosyasi= "$cache_klasor/$cache_dosya_adi-cache.html";
	$ilan_cache_sure=tema_ayar("ilan_cache_sure");
	$cache_sure=$ilan_cache_sure*60;
	if ($cache_sure>0) {
		if (file_exists($cache_dosyasi) and (time() - ($cache_sure) < filemtime($cache_dosyasi))) {
			include_once($cache_dosyasi);
			exit;
		} else {
			ob_start();
		}
	}		
}

$baslik=getir("icerik","baslik");
$video_url=bilgi("video");
$durum=getir("icerik","durum");
$yazar=getir("icerik","yazar");
$kategoriler=kategori_getir($id,"kategori");
$kategori_sayisi=count($kategoriler);
$son_kategori=$kategoriler[$kategori_sayisi-1];
$iletisim_bilgileri=tema_ayar("iletisim_bilgileri");

@$satin_al=mysqli_real_escape_string($baglan,strip_tags($_POST["satin_al"]));
if ($satin_al) {
	$adet=mysqli_real_escape_string($baglan,strip_tags($_POST["adet"]));
	$not=mysqli_real_escape_string($baglan,strip_tags($_POST["not"]));
	$adet_fiyat=bilgi("fiyat");
	$toplam_tutar=$adet*$adet_fiyat;
	$siparis_toplam_tutar=$adet*str_replace(",",".",$adet_fiyat);
	$para_birimi=bilgi("para_birimi");
	$tarih_saat=tarih_saat();
	$siparis_olustur=mysqli_query($baglan,"insert into siparisler set alici_id='$oturum_id', satici_id='$yazar', tutar='$siparis_toplam_tutar', ilan_id='$id', tarih_saat='$tarih_saat',tip='get', durum='0'");
	$eklenen_siparis_id=mysqli_insert_id($baglan);
	$adet_guncelle=ekstra_bilgi_guncelle("adet",$adet,$eklenen_siparis_id,"siparis");
	if ($not) {$not_guncelle=ekstra_bilgi_guncelle("not",$not,$eklenen_siparis_id,"siparis");}
	$_SESSION["mesaj"]=array(_LANG('msj_siparis_olusturuldu'),"success");
	// Bildirim gönderiliyor
	$lp_link=lpanel_uye_profil_url($oturum_id);
	$oturum_ad_soyad=getir("uyeler","ad_soyad",$oturum_id);
	bildirim_ekle(sprintf(_LANG('bnf_get_siparis_verildi'), $lp_link, $oturum_ad_soyad),$uye_id,0);
	$satici_ad_soyad=getir("uyeler","ad_soyad",$yazar);
	$get_satislarim_url=ozel_url("get-satislarim");
	bildirim_ekle(sprintf(_LANG('bnf_yeni_siparis'), $oturum_ad_soyad, $get_satislarim_url),$yazar,1);
	yonlendir(ozel_url("siparis")."?siparis_id=$eklenen_siparis_id");
}

if (!@$yonetici_id) {
	if ($durum==0 or $durum==2) {
		if (@$oturum_id!=$yazar or !@$oturum_id) {
			yonlendir(site_url());
		}
	}
}

$marka_model_kat=null;
$marka=null;
$seri=null;
$model=null;
$x=1;
foreach ($kategoriler as $kat) {
	$kat_ozel_alan=kategori_bilgi("ozel_alanlar",$kat);
	$kat_guvenlik_ipucu=kategori_bilgi("guvenlik_ipuclari",$kat);
	$kat_teknik_ozellikler=kategori_bilgi("teknik_ozellikler",$kat);
	$kat_sag_reklam=kategori_bilgi("ilan_sag_reklam",$kat);
	$kat_resimsiz=kategori_bilgi("resimsiz",$kat);
	$kat_metrekare_fiyati=kategori_bilgi("metrekare_fiyati",$kat);
	$kat_marka_model=kategori_bilgi("marka_model",$kat);
	if ($kat_guvenlik_ipucu) {
		$guvenlik_ipuclari=$kat_guvenlik_ipucu;
	}
	if ($kat_ozel_alan) {
		$ozel_alanlar=$kat_ozel_alan;
	}
	if ($kat_teknik_ozellikler) {
		$teknik_ozellikler=$kat_teknik_ozellikler;
	}
	if ($kat_sag_reklam) {
		$ilan_sag_reklam=$kat_sag_reklam;
	}	
	if ($kat_resimsiz) {
		$resimsiz_ilan=$kat_resimsiz;
	}	
	if ($kat_metrekare_fiyati) {
		$metrekare_fiyati=$kat_metrekare_fiyati;
	}
	if ($marka_model_kat and $x==3) {
		$marka=$kat;
	}	
	if ($marka_model_kat and $x==4) {
		$seri=$kat;
	}	
	if ($marka_model_kat and $x==5) {
		$model=$kat;
	}	
	if (!$marka_model_kat and $kat_marka_model==1) {
		$marka_model_kat=$kat;
	}
	$x++;
}
$get=bilgi("get");
$stok=bilgi("stok");

tema_header(); ?>
<div class="gri">
	<div class="container">
		<ol class="breadcrumb">
		  <li><a href="<?php echo site_url(); ?>"><?php echo _LANG('p_menu_anasayfa'); ?></a></li>
		  <?php 
			foreach ($kategoriler as $kategori) {
				$kat_baslik=_DIL_BASLIK(getir("kategoriler","baslik",$kategori), $kategori, "kategori");
				$kat_link=kategori_link_getir($kategori);
				echo "<li><a href='$kat_link'>$kat_baslik</a></li>";
			}
		  ?>		  
		</ol>
	</div>
</div>
<div class="container ilan">
	<?php include("temalar/$aktif_tema/inc/alert.php"); ?>
	<?php 
	if ($durum==0) {
		echo "<div class='alert alert-info'>İncelediğiniz ilan taslağını sadece siz görüntüleyebilirsiniz.</div>";
	} else if ($durum==2) {
		echo "<div class='alert alert-info'>İncelediğiniz ilan henüz onay kuyruğundadır. İlan onaylandıktan sonra herkes için görülebilir olacaktır.</div>";	
	} else if ($durum==3) {
		$sebep=bilgi("sebep");
		if ($sebep) {
			echo "<div class='alert alert-danger'>İncelediğiniz ilan yayından kaldırılmıştır. Yayıncı notu: $sebep</div>";	
		} else {
			echo "<div class='alert alert-danger'>" . _LANG('ilan_yayindan_kaldirildi2') . "</div>";	
		}
	} else if ($durum==4) {
		$ilanlarim_url=ozel_url("ilanlarim");
		if ($oturum_id==$yazar) {
			echo "<div class='alert alert-danger'>" . _LANG('ilan_suresi_doldu', array($ilanlarim_url)) . "</div>";
		} else {
			echo "<div class='alert alert-danger'>" . _LANG('ilan_suresi_doldu2') . "</div>";	
		}
	}
	?>
	<div class="ust">
		<div class="row">
			<div class="col-md-31 col-sm-26 ilan-baslik">
				<h1 title="<?php echo $baslik; ?>"><?php echo $baslik; ?></h1>
			</div>
			<div class="clearfix mb10 visible-xs"></div>
			<div class="col-md-11 col-sm-16">
				<div class="buton">
				<?php if (favori_mi($id,$oturum_id)) {$favori=1;} else {$favori=0;} ?>
				<a id="favorilerden_kaldir" class="c_pointer" <?php if ($favori!=1) {echo "style='display:none;'";} ?>><i class="ion-android-star-outline"></i><?php echo _LANG('favori_cikar'); ?></a>
				<a id="favorilere_ekle" class="c_pointer" <?php if ($favori==1) {echo "style='display:none;'";} ?>><i class="ion-android-star"></i><?php echo _LANG('favori_ekle'); ?></a>
				</div>
				<div class="buton">
					<a onclick="frames['frame'].print()" class="c_pointer"><i class="ion-printer"></i><?php echo _LANG('general_print'); ?></a>
				</div>
				<div class="buton sosyal pull-right">
					<?php $icerik_link=link_getir($id); ?>
					<a href="http://www.facebook.com/sharer.php?u=<?php echo $icerik_link; ?>" onclick="window.open(this.href, 'mywin','left=20,top=20,width=500,height=500,toolbar=1,resizable=0'); return false;" target="blank" class="facebook"><i class="ion-social-facebook"></i></a>
					<a href="https://x.com/intent/post?url=<?php echo $icerik_link; ?>&text=<?php echo $baslik; ?>" onclick="window.open(this.href, 'mywin','left=20,top=20,width=500,height=500,toolbar=1,resizable=0'); return false;" target="blank" class="twitter"><i class="ion-social-twitter"></i></a>
					<a href="http://pinterest.com/pin/create/button/?url=<?php echo $icerik_link; ?>&media={URI-encoded URL of the image to pin}&description=<?php echo $baslik; ?>" onclick="window.open(this.href, 'mywin','left=20,top=20,width=500,height=500,toolbar=1,resizable=0'); return false;" target="blank" class="pinterest"><i class="ion-social-pinterest"></i></a>
					<a href="whatsapp://send?text=<?php echo $icerik_link; ?>&t=<?php echo $baslik; ?>" data-action="share/whatsapp/share" target="blank" class="whatsapp visible-xs"><i class="ion-social-whatsapp"></i></a>
				</div>
			</div>
		</div>
	
	</div>
	<div class="row">
		<div class="<?php if ($iletisim_bilgileri!=2) {echo "col-md-31";} else {echo "col-md-42";} ?> <?php if (tema_ayar("iletisim_bilgileri_pozisyon")==2) {echo "pull-right";} ?>">
			<div class="row">
			<?php if (@$resimsiz_ilan!=1) { ?>
				<div class="<?php if ($iletisim_bilgileri!=2) {echo "col-md-27";} else {echo "col-md-20";} ?>  noselect <?php if (tema_ayar("ilan_tablo")==2) {echo "pull-right";} ?>">
					<?php 
					$gorsel=resim_getir("530_393");
					$gorsel_full=thumbnail("full");
					$gorsel_thumb=resim_getir("160_120");
					$gorsel_full_galeri_url=str_replace(array("%20"),array(" "),$gorsel_full);
					$galeri_id_getir=id_getir("galeri","resim",$gorsel_full_galeri_url);
					$onceki_galeri_id=onceki_galeri_id(@$galeri_id_getir);
					$sonraki_galeri_id=sonraki_galeri_id(@$galeri_id_getir);
					$resim_sayisi=resim_sayisi($id);
					?>
					<div class="prelative visible-md visible-lg">
						<img id="genis_gorsel" src="<?php echo $gorsel; ?>" alt="<?php echo htmlspecialchars($baslik, ENT_QUOTES, 'UTF-8'); ?>" class="img-responsive visible-md visible-lg <?php if (@$resim_sayisi>1) {echo "c_pointer";} ?>" />
						<?php if (@$resim_sayisi>1) { ?>
						<a id="onceki" class="onceki ileri_geri_buton galeri_gorsel c_pointer"><i class="ion-chevron-left"></i></a>
						<a id="sonraki" class="sonraki ileri_geri_buton galeri_gorsel c_pointer"><i class="ion-chevron-right"></i></a>
						<?php } ?>
					</div>
					<div id="galeri_sira_bilgi"></div>

					<div class="well gorseller visible-md visible-lg">
						<div class="well-heading">
							<div class="row">
								<div class="col-md-21 col-xs-21"><a id="mega_foto" class="c_pointer" data-id="<?php echo @$galeri_id_getir; ?>"><i class="ion-arrow-expand"></i><?php echo _LANG('ilan_fotograf_buyut'); ?></a></div>
								<div class="col-md-21 col-xs-21"><?php if ($video_url) { ?><a data-toggle="modal" data-target="#video_modal" class="c_pointer"><i class="ion-ios-videocam"></i>Video</a><?php 
								} else {echo "<span style='color:#999 !important;font-weight:600;'><i class='ion-ios-videocam'></i><?php echo _LANG('s_video'); ?></span>";} ?></div>
							</div>
						</div>
						<?php if (@$resim_sayisi>0) { ?>
						<div class="padding scroll">
						<?php 
						$sql=mysqli_query($baglan,"select * from galeri where icerik_id='$id' order by sira asc");
						$x=1;
						while ($galeri_dizisi=mysqli_fetch_array($sql)) {
						$galeri_id=$galeri_dizisi["id"];
						$galeri_url=galeri_url($galeri_id);					
						$galeri_resim=thumbnail_url("160_120",$galeri_dizisi["resim"]);
						$galeri_resim_buyuk=thumbnail_url("530_393",$galeri_dizisi["resim"]);
						$galeri_resim_full=thumbnail_url("full",$galeri_dizisi["resim"]);
						$galeri_baslik=$galeri_dizisi["baslik"];
						if ($x==1) {
							$active_yaz="active";
						} else {
							$active_yaz=null;
						}
						?>
						<a id="resim_<?php echo $galeri_id; ?>" class="galeri_gorsel <?php echo $active_yaz; ?> c_pointer" data-url="<?php echo $galeri_resim_buyuk; ?>" data-id="<?php echo $galeri_id; ?>"><img src="<?php echo $galeri_resim; ?>" alt="<?php echo htmlspecialchars($baslik, ENT_QUOTES, 'UTF-8'); ?>" /></a>
						<a href="<?php echo $galeri_resim_full; ?>" id="lightbox-<?php echo $galeri_id; ?>" data-lightbox="mega_new"></a>
						<?php 
						$x++;} ?>
						</div>
						<?php } ?>
					</div>
					<?php 
					$mobil_slider_sql=mysqli_query($baglan,"select * from galeri where icerik_id='$id' order by sira asc");
					$mobil_slider_sayisi=mysqli_num_rows($mobil_slider_sql);
					if (@$mobil_slider_sayisi) {
					?>
					<div class="visible-xs visible-sm">
						<div id="no_flash_slider"><i class="fa fa-circle-o-notch fa-spin"></i><p><?php echo _LANG('ilan_fotograflar_yukleniyor'); ?></p></div>
						<div id="mobil_slides" style="margin-bottom:10px;display:none;">
							<?php 
							while ($galeri_dizisi=mysqli_fetch_array($mobil_slider_sql)) {
							$galeri_id=$galeri_dizisi["id"];
							$galeri_url=galeri_url($galeri_id);
							$galeri_resim_buyuk=thumbnail_url("530_393",$galeri_dizisi["resim"]);
							$galeri_baslik=$galeri_dizisi["baslik"];
							echo "<img src='$galeri_resim_buyuk' class='img-responsive' alt='" . htmlspecialchars($baslik, ENT_QUOTES, 'UTF-8') . "'>";
							$x++;}
							?>
							
						</div>
					</div>
				<?php } ?>
				<?php if ($video_url) { ?><a data-toggle="modal" class="btn btn-primary btn-block mb15 visible-xs visible-sm" data-target="#video_modal" class="c_pointer"><i class="ion-ios-videocam"></i>İlan videosunu izlemek için tıklayın</a><?php } ?>
				</div>
				<?php } ?>
				
					<div class="<?php if (@$resimsiz_ilan!=1) {
						if ($iletisim_bilgileri!=2) {echo "col-md-15";} else {echo "col-md-20";}
					} else {
						if ($iletisim_bilgileri!=2) {echo "col-md-24";} else {echo "col-md-42";}
					} ?>">
					<?php 
					$sahiplendirme=bilgi("sahiplendirme");
					$gorusulur=bilgi("gorusulur");
					$teklif_usulu=bilgi("teklif_usulu");
					if ($sahiplendirme!=1 and $gorusulur!=1 and $teklif_usulu!=1) {
					?>
					<div class="fiyat"><?php echo fiyat($id); ?>
					<?php 
					$para_birimi=bilgi("para_birimi");
					$fiyat=fiyat($id);
					if ($para_birimi!="gmd" and $fiyat) {
						$fiyat_gmd_bilgi=bilgi("fiyat");
						if (!$fiyat_gmd_bilgi) {
							gmd_karsilik_guncelle();
						}
						$fiyat_gmd=fiyat_donustur(bilgi("fiyat"));
						if ($fiyat_gmd) {
							echo "<span class='tl_karsilik'><i class='ion-arrow-swap ibig'></i>$fiyat_gmd GMD</span>";
						}
					}
					?>
					</div>
					<?php } else {
						if ($sahiplendirme==1) {
							echo "<div class='s_mesaj'>Sahiplendirme <i class='ion-ios-paw'></i></div>";
						} 
						if ($gorusulur==1) {
							echo "<div class='s_mesaj'>" . _LANG('s_gorusulur') . "</div>";
						}
						if ($teklif_usulu==1) {
							echo "<div class='s_mesaj'>" . _LANG('s_teklif_usulu') . "</div>";
						}
					} ?>
					<?php 
					if ($get==1) {
						if ($oturum_id) {
							if ($stok>0) {
								echo "
								<a href='#' data-toggle='modal' data-target='#get_siparis' class='btn btn-primary btn-block'><i class='ion-ios-cart'></i>Hemen Satın Al</a>
								";
							} else {
								echo "
								<div class='btn btn-danger disabled btn-block' disabled><i class='ion-alert'></i>Stok tükendi!</div>
								";
							}
						} else {
						$giris_yap_url=ozel_url("giris-yap")."?m=1&ilan=$id";
						echo "
						<a href='$giris_yap_url' class='btn btn-primary btn-xs btn-get btn-block'><i class='ion-ios-cart'></i>Hemen Satın Al</a>
						";
						}
						$stok=bilgi("stok",$id);
						if ($stok==0) {$stok_alert="stok_tukendi";} else {$stok_alert=null;}
						$kargo_durum=bilgi("kargo_durum",$id);
						if ($kargo_durum!="Online Teslimat") {
							$kargo_firma=bilgi("kargo_firma",$id);
							$kargo_teslimat=bilgi("kargo_teslimat",$id);
						}
						$kargo_durum=bilgi("kargo_durum",$id);
						echo "<div class='get_ilan_bilgi'>";
						echo "<span class='kargo_bilgi $stok_alert'><i class='fa fa-cubes'></i>" . sprintf(_LANG('s_urun_stogu'), $stok) . "</span>";
						if ($kargo_durum) {if ($kargo_durum==" . _LANG('s_ucretsiz_kargo') . ") {$ucretsiz_yaz="ucretsiz";} echo "<span class='kargo_bilgi $ucretsiz_yaz'><i class='fa fa-road'></i>$kargo_durum</span>";}
						if ($kargo_durum!="Online Teslimat") {
							if ($kargo_firma) {echo "<div class='clearfix'></div><span class='kargo_bilgi'><i class='fa fa-truck'></i>$kargo_firma</span>";}
							if ($kargo_teslimat) {if ($kargo_teslimat==" . _LANG('s_ayni_gun') . ") {$ayni_gun_yaz="ayni_gun";} echo "<span class='kargo_bilgi $ayni_gun_yaz'><i class='fa fa-clock-o'></i>$kargo_teslimat</span>";}
						}
						echo "</div>";
						echo "<div class='clearfix mb15'></div>";
					}
					?>
					<?php
					$il=il_getir($id);
					$il_id=bilgi("il");
					$ilce=ilce_getir($id);
					$ilce_id=bilgi("ilce");
					$mahalle=mahalle_getir($id);
					$mahalle_id=bilgi("mahalle");
					if ($il or $ilce or $mahalle) {
					?>
					<ul class="adres">
						<?php if (tema_ayar("bolgesel")=="_LANG('_LANG(\'Hayır\')')") { ?>
						<li><a href="<?php echo kategori_link_getir($son_kategori); ?>/?f_il=<?php echo $il_id; ?>"><?php echo $il; ?></a></li>
						<?php } ?>
						<?php if ($ilce_id) { ?><li><a href="<?php echo kategori_link_getir($son_kategori); ?>/?f_il=<?php echo $il_id; ?>&f_ilce=<?php echo $ilce_id; ?>"><?php echo $ilce; ?></a></li><?php } ?>
						<?php if ($mahalle_id) { ?><li><a href="<?php echo kategori_link_getir($son_kategori); ?>/?f_il=<?php echo $il_id; ?>&f_ilce=<?php echo $ilce_id; ?>&f_mahalle=<?php echo $mahalle_id; ?>"><?php echo $mahalle; ?></a></li><?php } ?>
					</ul>
					<?php } ?>
					<table class="table table-default detay">
						<tr>
							<th><?php echo _LANG('hb_ilan_no'); ?></th>
							<td style="color:red;"><?php echo $id; ?></td>
						</tr>
						<tr>
							<th><?php echo _LANG('hb_ilan_tarihi'); ?></th>
							<td><?php echo tarih_getir(); ?></td>
						</tr>
						<?php
						if (!@$ilan_sag_reklam) {
							$ilan_sag_reklam=tema_ayar("ilan_sag_reklam");
						}

						if ($marka_model_kat) {
							if (isset($marka)) {
								echo "
								<tr>
									<th>"._LANG('o_marka')."</th>
									<td>"._DIL_BASLIK(getir("kategoriler","baslik",$marka), $marka, "kategori")."</td>
								</tr>";
							}
							if (isset($seri)) {
								echo "
								<tr>
									<th>"._LANG('o_seri')."</th>
									<td>"._DIL_BASLIK(getir("kategoriler","baslik",$seri), $seri, "kategori")."</td>
								</tr>";
							}
							if (isset($model)) {
								echo "
								<tr>
									<th>"._LANG('o_model')."</th>
									<td>"._DIL_BASLIK(getir("kategoriler","baslik",$model), $model, "kategori")."</td>
								</tr>";
							}
						}
						
						if ($ozel_alanlar) {
							$alan_dizi=explode("\r\n",$ozel_alanlar);
							$say=count($alan_dizi);
							if ($say>0) {
								foreach ($alan_dizi as $alan) {
									$alan_parcala=explode("|",$alan);
									$anahtar=$alan_parcala[0];
									$baslik=isset($alan_parcala[1]) ? $alan_parcala[1] : null;
									if (!$baslik) {
										$baslik=$anahtar;
									}
									$deger=bilgi($anahtar,$id);
									if (isset($deger)) {
									echo "
									<tr>
										<th>".spec_etiket($baslik)."</th>
										<td><?php echo spec_etiket($deger); ?></td>
									</tr>";
									}
									if ($anahtar=="metrekare") {
										if (@$metrekare_fiyati==1) {
											$fiyat_toplam=(float)str_replace(",", ".", bilgi("fiyat"));
											$m2_fiyati=$deger ? fiyat_donustur($fiyat_toplam/$deger) : "";
											echo "
											<tr>
												<th>" . _LANG('hb_metrekare_fiyati') . "</th>
												<td>$m2_fiyati GMD</td>
											</tr>";										
										}
									}
								}
							}
						}
						?>
					</table>
					<div class="text-center">
						<a id="sikayetim_var_buton" data-toggle="modal" data-target="#sikayet" class="c_pointer"><i class="ion-flag"></i><?php echo _LANG('listing_report'); ?></a>
					</div>
				</div>
				<?php 
				$reklam_336=tema_ayar("resimsiz_tablo_yani");
				if (@$resimsiz_ilan==1) {echo "<div class='col-md-18'>$reklam_336</div>";} ?>
			</div>
			<?php if (@$resimsiz_ilan==1) {echo "<div class='clearfix mb20'></div>";} ?>
			
			<div class="clearfix mb20 visible-xs visible-sm"></div>
			<div id="mobil_iletisim" class="visible-xs visible-sm"></div>
			<div class="detay">
				<div class="panel panel-default beyaz">
					<div class="panel-heading">
					  <ul class="nav nav-tabs ozel" role="tablist">
						<li role="presentation" class="active visible-sm visible-md visible-lg"><a href="#aciklama" aria-controls="aciklama" role="tab" data-toggle="tab"><?php echo _LANG('listing_details'); ?></a></li>
						<li role="presentation" class="active visible-xs"><a href="#aciklama" aria-controls="aciklama" role="tab" data-toggle="tab"><?php echo _LANG('listing_desc'); ?></a></li>
						<?php
						if (tema_ayar("google_maps")==1 and tema_ayar("google_maps_api_key")!="") {
						$koordinat=bilgi("koordinat");$harita_gizle=bilgi("harita_gizle");
						$koordinat_parca = koordinat_ayir($koordinat);
						if ($koordinat and !$harita_gizle and $koordinat_parca) {
							$k_lat = $koordinat_parca['lat'];
							$k_lon = $koordinat_parca['lon'];
						?>
						<li role="presentation"><a href="#konum" aria-controls="konum" role="tab" data-toggle="tab"><i class="ion-location"></i>Konum</a></li>
						<li role="presentation" id="sokak_gorunumu" class="visible-sm visible-md visible-lg" style="display:none;"><a href="#sokak" aria-controls="sokak" role="tab" data-toggle="tab"><i class="fa fa-street-view"></i>Sokak Görünümü</a></li>
						<li role="presentation" id="sokak_gorunumu_mobil" class="visible-xs" style="display:none;"><a href="#sokak" aria-controls="sokak" role="tab" data-toggle="tab"><i class="fa fa-street-view mr0"></i></a></li>
						<?php } } ?>
						<li role="presentation" class="visible-sm visible-md visible-lg"><a href="#benzer" aria-controls="benzer" role="tab" data-toggle="tab"><i class="ion-refresh"></i><?php echo _LANG('listing_similar'); ?></a></li>
						<li role="presentation" class="visible-xs"><a href="#benzer" aria-controls="benzer" role="tab" data-toggle="tab"><i class="ion-refresh mr0"></i></a></li>
					  </ul>
					</div>
					<div class="tab-content">
						<div role="tabpanel" class="tab-pane fade in active" id="aciklama">
							<div class="panel-body">
							<div class="ilan_icerik">
							<?php $icerik=icerik_getir();
							if ($icerik=="") {$icerik="<p>"._LANG('ilan_aciklama_bulunamadi')."</p>";}
							echo $icerik;
							?>
							</div>
							</div>
						</div>
						<?php if (tema_ayar("google_maps")==1 and tema_ayar("google_maps_api_key")!="") { if ($koordinat and !$harita_gizle) {if (strstr($koordinat,"-")) { ?>
						<div role="tabpanel" class="tab-pane fade" id="konum">
							<div class="panel-body">
								<div id="harita" style="width:100%;height:400px;"></div>
							</div>
						</div>
						<div role="tabpanel" class="tab-pane fade" id="sokak">
							<div class="panel-body">
								<div id="sokak_harita" style="width:30%;height:400px;float:left;"></div>
								<div id="pano" style="width:70%;height:400px;float:left;"></div>
							</div>
						</div>
						<?php } } } ?>
						<div role="tabpanel" class="tab-pane fade" id="benzer">
							<div class="panel-body">
							<?php 
							$benzer_kategori=benzer_kategori_id($kategoriler,$id);
							if ($benzer_kategori) {
							$benzer_sql_kat=sql_kategori($benzer_kategori);
							$benzer_limit=15;
							if ($iletisim_bilgileri==2) {
								$benzer_limit=14;
							}
							$benzer_sql=mysqli_query($baglan,"select * from icerik where tip='ilan' and durum='1' and id!='$id' and ($benzer_sql_kat) order by rand() desc limit 0,$benzer_limit");
							$benzer_say=mysqli_num_rows($benzer_sql);
							if ($benzer_say>0) { ?>
							<div class="row">
							<?php while ($benzer_dizi=mysqli_fetch_array($benzer_sql)) {
								$benzer_id=$benzer_dizi["id"];
								$benzer_baslik=$benzer_dizi["baslik"];
								$benzer_url=link_getir($benzer_id);
								$benzer_resim=resim_getir("160_120",$benzer_id);
							?>
							<div class="<?php if ($iletisim_bilgileri==2) {echo "col-md-6";} else {echo "col-md-a5";} ?> col-xs-21">
								<div class="liste">
									<a href="<?php echo $benzer_url; ?>"><img src="<?php echo $benzer_resim; ?>" alt="<?php echo $benzer_baslik; ?>" class="img-responsive"/></a>
									<h3><a href="<?php echo $benzer_url; ?>"><?php echo $benzer_baslik; ?></a></h3>
								</div>
							</div>
							<?php } ?>
							</div>
							<?php } else {echo _LANG('tx_benzer_ilan_bulunamadi');} } else {echo _LANG('tx_benzer_ilan_bulunamadi');} ?>
							</div>
						</div>
					</div>
				</div>
				<?php 
				if (@$teknik_ozellikler) {
				$g=1;
				$grup_bol=explode("\r\n\r\n",$teknik_ozellikler);
				$grup_say=count($grup_bol);
				$mevcut_ozellikler=bilgi("teknik_ozellikler",$id);
				?>
				<div class="panel panel-default jaayquick">
				<div><?php echo _LANG('hb_teknik_ozellikler'); ?></div>
				<div class="panel-body">
				<?php 
				foreach ($grup_bol as $grup) {
					$tekrar_bol=explode("\r\n",$grup);
					$baslik=$tekrar_bol[0];
					$veriler=isset($tekrar_bol[1]) ? $tekrar_bol[1] : '';
					$veriler_bol=explode(",",$veriler);
					foreach($veriler_bol as $key => $link) {
						if($link === '') { 
							unset($veriler_bol[$key]); 
						} 
					}
					echo "<div class='bolum_baslik' style='margin-bottom:8px !important;font-size:13px;font-weight:500;'>" . spec_etiket($baslik) . "</div>";
					echo "<div class='teknik_grup'><div class='row'>";
					foreach ($veriler_bol as $x) {
						if (strstr($mevcut_ozellikler,"($x)")) {$checked_yaz="checked='checked'";} else {$checked_yaz=null;}
						echo "<div class='col-md-a4 col-sm-14 col-xs-21'><div class='checkbox yesil' style='padding:0 !important;margin:0 0 3px 0 !important;'><label class='ozellik' title='" . spec_etiket($x) . "'><input type='checkbox' name='ozellikler[]' value='$x' $checked_yaz disabled/>" . spec_etiket($x) . "</label></div></div>";
					}
					echo "</div></div>";
					if ($g!=$grup_say) {
					echo "<div class='clearfix' style='margin-bottom:15px;'></div>";
					}
				$g++;}
				?>
				</div>
				</div>
				<?php } ?>
							
				<?php if (tema_ayar("ilan_yorumlari")=="Açık") { ?>
				<div class="yorumlar">
					<div class="panel panel-default beyaz">
						<div class="panel-heading"><?php echo _LANG('p_yorumlar'); ?></div>
						<div class="panel-body">							
							<div class="fb-comments" data-href="<?php echo link_getir($id); ?>" data-numposts="5" data-width="100%"></div>
						</div>
					</div>
				</div>
				<?php } ?>
				<?php if (function_exists("postviews")) {
					$okunma=postviews();
					echo "<div class='text-center ziyaret'><i class='ion-eye'></i>" . sprintf(_LANG('s_okunma_kez'), $okunma) . "</div>";
				} ?>
			</div>
		</div>
		<div class="clearfix visible-xs mb20"></div>
		<?php if ($iletisim_bilgileri!=2) { ?>
		<div class="col-md-11 <?php if (tema_ayar("iletisim_bilgileri_pozisyon")==2) {echo "pull-left";} ?>">
			<?php 
			$ilan_magaza=bilgi("magaza",$id);
			$magaza_link=magaza_link_getir($ilan_magaza);
			$magaza_kayit_tarihi=tarih_saat_bicimlendir(getir("magazalar","tarih_saat",$ilan_magaza));
			
			// Üyenin profil iletişim bilgileri yazılacak.
			$yazar=getir("icerik","yazar",$id); 
			$uye_tarih_saat=getir("uyeler","tarih_saat",$yazar);
			$uye_tarih_saat_parcala=explode(" ",$uye_tarih_saat);
			$uyelik_tarihi=tarih_bicimlendir($uye_tarih_saat_parcala[0]);
			$uye_link=uye_link_getir($yazar);
			
			if (@$oturum_id==$yazar) {include("temalar/$aktif_tema/inc/ilan-sahibi-rapor.php");}
			?>
			<div id="iletisim_copy" class="visible-md visible-lg">
				<div class="panel panel-default beyaz iletisim <?php if ($durum==4) {echo "d4 noselect";} ?>">
					<div class="panel-body">
					<div class="blurry">
						<?php
						$cep_tel=uye_bilgi("cep_tel",$yazar);
						$is_tel=uye_bilgi("is_tel",$yazar);
						$ev_tel=uye_bilgi("ev_tel",$yazar);
						if ($cep_tel) {$tel_dizi[]=array("tip"=>"Cep","numara"=>$cep_tel,"ikon"=>"ion-iphone");}
						if ($is_tel) {$tel_dizi[]=array("tip"=>"İş","numara"=>$is_tel,"ikon"=>"ion-earth");}
						if ($ev_tel) {$tel_dizi[]=array("tip"=>"Ev","numara"=>$ev_tel,"ikon"=>"ion-ios-telephone");}
						
						if ($ilan_magaza) {
							$magaza_adi=getir("magazalar","baslik",$ilan_magaza);
							$magaza_resim=thumbnail_url("150_150",magaza_bilgi("profil_gorseli",$ilan_magaza));
							if ($magaza_resim=="-150_150.") {
								$magaza_resim=tema_dizini()."/img/yok/150_150.png";
							}
						?>
						<div class="yukseklik pull-left">
							<a href="<?php echo $magaza_link; ?>"><img class="magaza_gorsel" src="<?php echo $magaza_resim; ?>" alt=""/></a>
						</div>
						<div class="ortala">
							<h3><a href="<?php echo $magaza_link; ?>"><?php echo $magaza_adi; ?></a></h3>
							<p style="margin-bottom:0;">Kayıt tarihi:<br /><?php echo $magaza_kayit_tarihi; ?></p>
						</div>
						<div class="clearfix mb15"></div>
						
						<h3><?php echo getir("uyeler","ad_soyad",$yazar); ?></h3>
						<p><?php echo _LANG("s_uyelik_tarihi"); ?><?php echo $uyelik_tarihi; ?></p>
						
						<?php if ($ilan_magaza) {$magaza_link=magaza_link_getir($ilan_magaza); ?>
							<a href="<?php echo $magaza_link; ?>">Mağazanın tüm ilanlarını görüntüle</a>
						<?php } else { ?>
							<a href="<?php echo $uye_link; ?>"><?php echo _LANG("s_uyenin_ilanlari"); ?></a>
						<?php } ?>
						
						<div class="clearfix mb7"></div>
						<?php foreach ($tel_dizi as $tel) {
						$ara_tel=$tel["numara"];
						if (strstr($ara_tel,"(")) {
							$ara_tel=tel_rakamlari($ara_tel);
						}
						?>
						<a href="tel:+<?php echo tel_uluslararasi($ara_tel); ?>" class="tel_link"><div class="bilgi">
							<i class="<?php echo $tel["ikon"]; ?>"></i>
							<div class="prel">
							<?php echo spec_etiket($tel["tip"]); ?><span><?php echo $ara_tel; ?></span>
							</div>
						</div></a>
						<?php } ?>
						
						<?php 
						$whatsapp_butonu=tema_ayar("whatsapp_butonu");
						if (($whatsapp_butonu==1 or !$whatsapp_butonu) and isset($cep_tel) and $cep_tel!="") { ?>
						<a href="https://wa.me/<?php echo tel_uluslararasi($cep_tel); ?>" class="tel_link" style="text-decoration:none !important;"><div class="bilgi whatsapp">
							<i class="fa fa-whatsapp"></i>
							<div class="prel">
							WhatsApp<span><?php echo $cep_tel; ?></span>
							</div>
						</div></a><?php 
						}
						?>
						
						<?php if ($oturum_id) { 
						if ($oturum_id!=$yazar) { ?>
						<div class="text-center">
							<a><?php echo _LANG('hb_ilan_sahibine_mesaj_gonder'); ?></a>
						</div>
						<?php 
						}
						} else { ?>
						<div class="text-center">
							<a href="<?php echo ozel_url("giris-yap"); ?>?m=1"><?php echo _LANG("s_ilan_sahibine_mesaj"); ?></a>
						</div>						
						<?php }	?>
						
						<?php if ((tema_ayar("mobil_fixed_iletisim")!=2) and isset($cep_tel) and $cep_tel!="") { ?>
						<div class="mobil_fixed_iletisim visible-xs">
							<div class="iletisim_bilgi"><i class="fa fa-user"></i></i>İlan sahibi: <?php echo getir("uyeler","ad_soyad",$yazar); ?> (<?php echo $magaza_adi; ?>)</div>
							<a class="tel" href="tel:+<?php echo tel_uluslararasi($ara_tel); ?>" style="<?php if ($whatsapp_butonu==1 or !$whatsapp_butonu) {echo "width:50%;";} else {echo "width:100%;";} ?>"><i class="fa fa-phone"></i>Ara</a>
							<?php if ($whatsapp_butonu==1 or !$whatsapp_butonu) { ?>
							<a class="whatsapp" href="https://wa.me/<?php echo tel_uluslararasi($cep_tel); ?>" style="width:50%;"><i class="fa fa-whatsapp"></i>WhatsApp</a>
							<?php } ?>
						</div>
						<?php } ?>
						
						<?php 
						} else {
						$ilan_iletisim=bilgi("iletisim",$id);
						if ($ilan_iletisim==1) {
						?>
						<h3><?php echo getir("uyeler","ad_soyad",$yazar); ?></h3>
						<p><?php echo _LANG("s_uyelik_tarihi"); ?><?php echo $uyelik_tarihi; ?></p>
						<a href="<?php echo $uye_link; ?>"><?php echo _LANG("s_uyenin_ilanlari"); ?></a>
						<div class="clearfix mb7"></div>
						<?php foreach ($tel_dizi as $tel) {
						$ara_tel=$tel["numara"];
						?>
						<a href="tel:+<?php echo tel_uluslararasi($ara_tel); ?>" class="tel_link"><div class="bilgi">
							<i class="<?php echo $tel["ikon"]; ?>"></i>
							<div class="prel">
							<?php echo spec_etiket($tel["tip"]); ?><span><?php echo $ara_tel; ?></span>
							</div>
						</div></a>
						<?php } ?>
						
						<?php 
						$whatsapp_butonu=tema_ayar("whatsapp_butonu");
						if (($whatsapp_butonu==1 or !$whatsapp_butonu) and isset($cep_tel) and $cep_tel!="") { ?>
						<a href="https://wa.me/<?php echo tel_uluslararasi($cep_tel); ?>" class="tel_link" style="text-decoration:none !important;"><div class="bilgi whatsapp">
							<i class="fa fa-whatsapp"></i>
							<div class="prel">
							WhatsApp<span><?php echo $cep_tel; ?></span>
							</div>
						</div></a><?php
						}
						?>
						
						<?php if ($oturum_id) { 
						if ($oturum_id!=$yazar) { ?>
						<div class="text-center">
							<a><?php echo _LANG('hb_ilan_sahibine_mesaj_gonder'); ?></a>
						</div>
						<?php 
						}
						} else { ?>
						<div class="text-center">
							<a href="<?php echo ozel_url("giris-yap"); ?>?m=1"><?php echo _LANG("s_ilan_sahibine_mesaj"); ?></a>
						</div>						
						<?php }	?>
						
						<?php if ((tema_ayar("mobil_fixed_iletisim")!=2) and isset($cep_tel) and $cep_tel!="") { ?>
						<div class="mobil_fixed_iletisim visible-xs">
							<div class="iletisim_bilgi"><i class="fa fa-user"></i></i>İlan sahibi: <?php echo getir("uyeler","ad_soyad",$yazar); ?></div>
							<a class="tel" href="tel:+<?php echo tel_uluslararasi($ara_tel); ?>" style="<?php if ($whatsapp_butonu==1 or !$whatsapp_butonu) {echo "width:50%;";} else {echo "width:100%;";} ?>"><i class="fa fa-phone"></i>Ara</a>
							<?php if ($whatsapp_butonu==1 or !$whatsapp_butonu) { ?>
							<a class="whatsapp" href="https://wa.me/<?php echo tel_uluslararasi($cep_tel); ?>" style="width:50%;"><i class="fa fa-whatsapp"></i>WhatsApp</a>
							<?php } ?>
						</div>
						<?php } ?>
						
						<?php } else {
						// Yetkili kişinin bilgileri yazılacak.
						$yetkili=bilgi("yetkili",$id);
						$yetkili_tel=bilgi("yetkili_tel",$id);
						?>
						<h3><?php echo $yetkili; ?></h3>
						<div class="clearfix mb7"></div>
						<div class="bilgi">
							<i class="ion-iphone"></i>
							<div class="prel">
							Cep<a href="tel:+<?php echo tel_uluslararasi($yetkili_tel); ?>" class="tel_link"><span><?php echo $yetkili_tel; ?></span></a>
							</div>
						</div>
						
						<?php if ((tema_ayar("mobil_fixed_iletisim")!=2) and isset($yetkili_tel) and $yetkili_tel!="") { ?>
						<div class="mobil_fixed_iletisim visible-xs">
							<div class="iletisim_bilgi"><i class="fa fa-user"></i>İlan sahibi: <?php echo $yetkili; ?></div>
							<a class="tel" href="tel:+<?php echo tel_uluslararasi($yetkili_tel); ?>" style="<?php if ($whatsapp_butonu==1 or !$whatsapp_butonu) {echo "width:50%;";} else {echo "width:100%;";} ?>"><i class="fa fa-phone"></i>Ara</a>
							<?php if ($whatsapp_butonu==1 or !$whatsapp_butonu) { ?>
							<a class="whatsapp" href="https://wa.me/<?php echo tel_uluslararasi($yetkili_tel); ?>" style="width:50%;"><i class="fa fa-whatsapp"></i>WhatsApp</a>
							<?php } ?>
						</div>
						<?php } ?>
						
						<?php } } ?>
					</div>
					</div>
				</div>
			</div>
			<?php $ilan_standart_guvenlik=tema_ayar("ilan_standart_guvenlik"); if ($ilan_standart_guvenlik) { ?>
			<div class="panel panel-default beyaz ilan_sag">
				<div class="panel-body text-center">
					<?php echo $ilan_standart_guvenlik; ?>
				</div>
			</div>
			<?php } ?>
			
			<?php if (@$guvenlik_ipuclari) { ?>
				<div class="panel panel-default beyaz ilan_sag ipucu">
					<div class="panel-body">
						<h3><?php echo _LANG('hb_guvenlik_ipuclari'); ?></h3>
						<?php echo $guvenlik_ipuclari; ?>
					</div>
				</div>
			<?php } ?>
			
			<?php if ($ilan_sag_reklam) {echo $ilan_sag_reklam;} ?>

		</div>
		<?php } ?>
	</div>
</div>

<!-- Mesaj Gönder Modal -->
<div class="modal fade" id="mesaj_gonder" tabindex="-1" role="dialog" aria-labelledby="mesaj_gonder">
  <div class="modal-dialog" role="document">
	<form id="mesaj_gonder_form" action="" method="post" onsubmit="return false;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4><?php echo _LANG('hb_mesaj_gonder'); ?></h4>
      </div>
      <div class="modal-body">
		<div id="mesaj_cevap"></div>
		<div id="mesaj_gizle">
			 <div class="form-group">
				<label><?php echo _LANG('hb_ilan_basligi'); ?></label>
				<input type="text" class="form-control" id="gonderilecek" name="gonderilecek" placeholder="<?php echo _LANG('tx_gonderilecek_kisi'); ?>" value="<?php echo getir("icerik","baslik"); ?>" disabled>
			 </div>
			 <div class="form-group">
				<label for="kime"><?php echo _LANG('ilan_gonderilecek_kisi'); ?> <span style="color:#e74c3c;margin-left:3px;">*</span></label>
				<input type="text" class="form-control" id="gonderilecek" name="gonderilecek" placeholder="<?php echo _LANG('tx_gonderilecek_kisi'); ?>" value="<?php echo getir("uyeler","ad_soyad",$yazar); ?>" disabled>
			 </div>
			 <div class="form-group">
				<label for="mesaj">Mesajınız <span style="color:#e74c3c;margin-left:3px;">*</span></label>
				<textarea class="form-control" id="mesaj" name="mesaj" placeholder="<?php echo _LANG('msg_your_message'); ?>" rows="3" required></textarea>
				<input type="hidden" class="form-control" id="kime" name="kime" value="<?php echo $yazar; ?>">
				<input type="hidden" class="form-control" id="ilan_id" name="ilan_id" value="<?php echo $id; ?>">
			 </div>
		 </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Kapat</button>
        <button><?php echo _LANG('hb_gonder'); ?></button>
      </div>
    </div>
	</form>
  </div>
</div>
<!-- Şikayet Modal -->
<div class="modal fade" id="sikayet" tabindex="-1" role="dialog" aria-labelledby="sikayet">
  <div class="modal-dialog" role="document">
	<form id="sikayet_gonder_form" action="" method="post" onsubmit="return false;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel"><?php echo _LANG('report_form_title'); ?></h4>
      </div>
      <div class="modal-body">
		<div id="mesaj_cevap"></div>
		<div id="mesaj_gizle">
			 <div class="form-group">
				<label for="ad_soyad">Ad Soyad <span style="color:#e74c3c;margin-left:3px;">*</span></label>
				<input type="text" class="form-control" id="ad_soyad" name="ad_soyad" placeholder="<?php echo _LANG('account_name'); ?>" value="<?php if ($oturum_id) {echo getir("uyeler","ad_soyad",$oturum_id); }?>">
			 </div>
			 <div class="form-group">
				<label for="mesaj"><?php echo _LANG('report_reason'); ?> <span style="color:#e74c3c;margin-left:3px;">*</span></label>
				<textarea class="form-control" id="mesaj" name="mesaj" placeholder="<?php echo _LANG('report_reason'); ?>" rows="3" required></textarea>
				<input type="hidden" class="form-control" id="ilan_id" name="ilan_id" value="<?php echo $id; ?>">
			 </div>
		 </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Kapat</button>
        <button type="submit" id="s_gonder_buton" class="btn btn-danger"><i class="ion-paper-airplane"></i><?php echo _LANG('post_submit'); ?></button>
      </div>
    </div>
	</form>
  </div>
</div>
<?php if ($video_url) { 
$video_id=youtube_video_id($video_url);
?>
<!-- Video Modal -->
<div class="modal fade" id="video_modal" tabindex="-1" role="dialog" aria-labelledby="video_modal">
  <div class="modal-dialog" role="document">
	<form id="mesaj_gonder_form" action="" method="post">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4><?php echo _LANG('hb_tanitim_videosu'); ?></h4>
      </div>
      <div class="modal-body">
		<div class="embed-responsive embed-responsive-16by9 player mb15" itemprop="trailer">
			<iframe class="embed-responsive-item" src="https://www.youtube.com/embed/<?php echo $video_id; ?>?showinfo=0&amp;autoplay=0&amp;loop=1" frameborder="0" allowfullscreen></iframe>
		</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Kapat</button>
      </div>
    </div>
	</form>
  </div>
</div>
<?php } ?>
<?php if ($get==1) {
?>
<!-- GET Sipariş -->
<div class="modal fade" id="get_siparis" tabindex="-1" role="dialog" aria-labelledby="get_siparis">
  <div class="modal-dialog" role="document">
	<form id="get_siparis_form" action="" method="post">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4><?php echo _LANG('hb_siparis_formu'); ?></h4>
      </div>
      <div class="modal-body">
		<?php form_group(array("tip"=>"text","id"=>"urun","baslik"=>_LANG('tx_ilan_basligi'),"deger"=>getir("icerik","baslik",$id),"pasif"=>1)); ?>

		<div id="form_adet" class="form-group">
			<label for="adet">Adet <span style="color:#e74c3c;margin-left:3px;">*</span></label>
			<input type="number" class="form-control" id="adet" name="adet" placeholder="Adet" value="1" max="<?php echo $stok; ?>" required="">
		</div>
		  
		<?php form_group(array("tip"=>"textarea","id"=>"not","baslik"=>_LANG('tx_siparis_notu'),"satir"=>3)); ?>
		<?php 
		$cep_tel=uye_bilgi("cep_tel",$oturum_id);
		$is_tel=uye_bilgi("is_tel",$oturum_id);
		$ev_tel=uye_bilgi("ev_tel",$oturum_id);
		$adres=uye_bilgi("adres",$oturum_id);
		if (!$cep_tel) {$cep_tel="Belirtilmedi";}
		if (!$is_tel) {$is_tel="Belirtilmedi";}
		if (!$ev_tel) {$ev_tel="Belirtilmedi";}
		if (!$adres) {$adres="Belirtilmedi";}
		?>
		<div class="well ozel">
			<p><?php echo _LANG('hb_satici_ile_paylasilacak_iletisim_bilgileriniz'); ?></p>
			<p class="help-block">
			Cep Telefonu: <?php echo $cep_tel; ?><br />
			İş Telefonu: <?php echo $is_tel; ?><br />
			Ev Telefonu: <?php echo $ev_tel; ?>
			</p>
			<p class="help-block" style="margin-bottom:0;">Adres: <?php echo $adres; ?></p>
		</div>
		<div class="alert alert-light">
		<p>İletişim bilgilerinizi dilediğiniz zaman <a href="<?php echo ozel_url("profil-guncelle"); ?>?ilan=<?php echo $id; ?>">buraya</a> tıklayarak güncelleyebilirsiniz.</p>
		</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Kapat</button>
		<?php 
			if ($oturum_id!=$yazar) {
				echo '<button type="submit" class="btn btn-primary" name="satin_al" value="Onayla">Siparişi Onayla</button>';
			} else {
				echo '<button type="submit" class="btn btn-danger" name="satin_al" value="Onayla" disabled>Sizin ürününüz</button>';
			}
		?>
        
      </div>
    </div>
	</form>
  </div>
</div>
<?php } ?>
<iframe src="<?php echo site_url(); ?>/sayfa/yazdir/?id=<?php echo $id; ?>" style="display:none;" name="frame"></iframe>
<?php tema_footer(); ?>
<?php if (!empty($koordinat) and !empty($k_lat) and !empty($k_lon)) { ?>
<script type="text/javascript" src="https://maps.google.com/maps/api/js?libraries=places&key=<?php echo tema_ayar("google_maps_api_key"); ?>"></script>
<script type="text/javascript" src="<?php echo site_url(); ?>/<?php echo $admin_dizini; ?>/js/locationpicker.jquery.min.js"></script>
<?php } ?>
<script type="text/javascript">
$(function(){
	$("#no_flash_slider").hide();
	var gorsel_yukseklik=$("#genis_gorsel").height();
	var ileri_geri_buton_yukseklik=$("#onceki").height();
	$(".ileri_geri_buton").css({"top":(gorsel_yukseklik-ileri_geri_buton_yukseklik)/2+"px"});
	
	<?php if (@$mobil_slider_sayisi>=2) { ?>
	$("#mobil_slides").slidesjs({
		width: '530',
		height: '393',
		pagination: {
			active: false,
		},
	}).show();
	
	var slider_buton_top=($("#mobil_slides").height()-$(".slidesjs-navigation").height())/2;
	$(".slidesjs-navigation").css({"top":slider_buton_top});
	$(".slidesjs-previous").html("<i class=\"fa fa-angle-left mr0\"></i>");
	$(".slidesjs-next").html("<i class=\"fa fa-angle-right mr0\"></i>");	
	<?php } else { ?>
	$("#mobil_slides").show();
	<?php } ?>
	
	$('[data-toggle="tooltip"]').tooltip();

	<?php if (@$galeri_id_getir) { ?>
	$.ajax({
		type:"post",
		url:"<?php echo tema_dizini(); ?>/ajax.php?islem=galeri_sira_bilgi",
		data:{'galeri_id':<?php echo @$galeri_id_getir; ?>,'icerik_id':<?php echo $id; ?>},
		success:function(cevap){
			$("#galeri_sira_bilgi").html(cevap);
		}
	});

	$(".galeri_gorsel").click(function(){
		var galeri_id=$(this).attr("data-id");
		$.ajax({
			type:"post",
			url:"<?php echo tema_dizini(); ?>/ajax.php?islem=galeri_sira_bilgi",
			data:{'galeri_id':galeri_id,'icerik_id':<?php echo $id; ?>},
			success:function(cevap){
				$("#galeri_sira_bilgi").html(cevap);
			}
		});
	});
	
	$("#genis_gorsel").click(function(e){
		var posX = $(this).offset().left,posY = $(this).offset().top;
		var posX_offset = e.pageX - posX;
		var sinir=$(this).width()/2;
		if (posX_offset<sinir) {
			var onceki=$("#onceki").attr("data-id");
			if (onceki) {
				$(".onceki").trigger("click");
			} else {
				//alert("önceki ama ilk görsel");
			}
		} else {
			var sonraki=$("#sonraki").attr("data-id");
			if (sonraki) {
				$(".sonraki").trigger("click");
			} else {
				//alert("sonraki ama son görsel");
			}
		}
	});
	
	<?php } ?>

	$("#favorilere_ekle").click(function(){
		$.ajax({
			type:"post",
			url:"<?php echo tema_dizini(); ?>/ajax.php?islem=favorilere_ekle",
			data:{'id':<?php echo $id; ?>},
			success:function(cevap){
				$("#mesaj_kutusu .alert").hide();
				$("#mesaj_kutusu").append(cevap);
			}
		});
	});
	$("#favorilerden_kaldir").click(function(){
		$.ajax({
			type:"post",
			url:"<?php echo tema_dizini(); ?>/ajax.php?islem=favorilerden_kaldir",
			data:{'id':<?php echo $id; ?>},
			success:function(cevap){
				$("#mesaj_kutusu .alert").hide();
				$("#mesaj_kutusu").append(cevap);
			}
		});
	});	
	
	$("body").on("submit","#mesaj_gonder_form",function(){
		$.ajax({
			type:"post",
			url:"<?php echo tema_dizini(); ?>/ajax.php?islem=mesaj_gonder&bildirim=1&ilan_id=<?php echo $id; ?>",
			data:$("#mesaj_gonder_form").serialize(),
			success:function(cevap){
				$('#mesaj_gonder #mesaj').val('');
				$('#mesaj_gonder').modal('toggle');
				$("#mesaj_kutusu").append(cevap);
			}
		});
	});	
	
	$("body").on("submit","#sikayet_gonder_form",function(){
		$.ajax({
			type:"post",
			url:"<?php echo tema_dizini(); ?>/ajax.php?islem=sikayet_gonder&ilan_id=<?php echo $id; ?>",
			data:$("#sikayet_gonder_form").serialize(),
			success:function(cevap){
				$('#sikayet #mesaj').val('');
				$('#sikayet').modal('toggle');
				$("#mesaj_kutusu").append(cevap);
			}
		});
	});	
	
	<?php if (!empty($koordinat) and !empty($k_lat) and !empty($k_lon)) { ?>
	// Google Haritalar Konumu
	var myCenter=new google.maps.LatLng(<?php echo $k_lat; ?>, <?php echo $k_lon; ?>);
	var marker=new google.maps.Marker({
		position:myCenter
	});
	function konum() {
	  var mapProp = {
		  center:myCenter,
		  zoom: 13,
		  draggable: true,
		  scrollwheel: true,
		  mapTypeId:google.maps.MapTypeId.ROADMAP
	  };
	  var map=new google.maps.Map(document.getElementById("harita"),mapProp);
	  marker.setMap(map);
	  google.maps.event.addListener(marker, 'click', function() {
		infowindow.setContent(contentString);
		infowindow.open(map, marker);
	  }); 
	};
	
	var streetViewService = new google.maps.StreetViewService();
	var STREETVIEW_MAX_DISTANCE = 50;
	var latLng = new google.maps.LatLng(<?php echo $k_lat; ?>, <?php echo $k_lon; ?>);
	streetViewService.getPanoramaByLocation(latLng, STREETVIEW_MAX_DISTANCE, function (streetViewPanoramaData, status) {
		if (status === google.maps.StreetViewStatus.OK) {
			function sokak_gorunumu() {
				var merkez = new google.maps.LatLng(<?php echo $k_lat; ?>, <?php echo $k_lon; ?>);
				var mapOptions = {
					center: merkez,
					zoom: 13
				};
				var map = new google.maps.Map(document.getElementById('sokak_harita'), mapOptions);
				assertNonNull(map, "map");
				var pov = {
					  heading: 270,
					  pitch: 0
					};
				assertNonNull(pov, "pov");
				var panoramaOptions = {
					  position: merkez,
					  pov: pov
					};
				assertNonNull(panoramaOptions, "panoramaOptions");
				var panoDiv = document.getElementById('pano');
				var panorama = new google.maps.StreetViewPanorama(panoDiv, panoramaOptions);
				assertNonNull(panorama, "panorama");
				map.setStreetView(panorama);
			}
			function assertNonNull(object, name) {
				assert(object !== null, name + " is null. Initialize it!");
			}
			function assert(condition, message) {
				message = message || "Assertion failed!";
				if (!condition) {
					alert(message);
					throw message;
				}
			}
			$("li#sokak_gorunumu").show();
			$("li#sokak_gorunumu_mobil").show();	
		} else {
			$("li#sokak_gorunumu").remove();
		}
		// Bootstrap Fix
		$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
			konum();
			sokak_gorunumu();
		});
	});	
	<?php } ?>
	
	var iletisim_kod=$("#iletisim_copy").html();
	$("#mobil_iletisim").html(iletisim_kod);

	
});

</script>

<?php 
if (!$oturum_id and empty($_SESSION["mesaj"])) {
	if (@$cache_klasor) {
		klasor_olustur($cache_klasor);
		$cache_dosyasi= "$cache_klasor/$cache_dosya_adi-cache.html";
		touch($cache_dosyasi);
		$ch = fopen($cache_dosyasi, 'w');
		fwrite($ch, ob_get_contents());
		fclose($ch);
		ob_end_flush();
	}
}
?>