<?php 
if(count(get_included_files())==1) {die();}
if (!$oturum_id) {
	$_SESSION["mesaj"]=array(_LANG('pn_bu_sayfayi_goruntuleyebilmek_icin_uye_girisi_yapmalisiniz'),".");
	yonlendir(ozel_url("giris-yap"));
	exit();
}

$limit_politikasi=limit_politikasi();

@$pasif=mysqli_real_escape_string($baglan,$_GET["pasif"]);
@$durum=mysqli_real_escape_string($baglan,$_GET["durum"]);
@$yayinla=mysqli_real_escape_string($baglan,$_GET["yayinla"]);
@$taslak=mysqli_real_escape_string($baglan,$_GET["taslak"]);
if ($pasif) {
	if (!is_numeric($pasif)) {
		yonlendir(ozel_url("ilanlarim"));
	}		
	$ilan_sahibi=getir("icerik","yazar",$pasif);
	if ($oturum_id==$ilan_sahibi) {
		guncelle("icerik","durum",3,$pasif);
		$kategori_dizisi=kategori_getir($pasif);
		foreach($kategori_dizisi as $yaz) {
			kategori_icerik_sayisi_guncelle($yaz);
		}
		$kategori_dizisi=kategori_getir($pasif);
		foreach($kategori_dizisi as $yaz) {
			kategori_icerik_sayisi_guncelle($yaz);
		}
		$_SESSION["mesaj"]=array(_LANG('pn_ilan_basariyla_yayindan_kaldirildi'),".");
		yonlendir(ozel_url("ilanlarim"));
	} else {
		$_SESSION["mesaj"]=array(_LANG('pn_bu_ilan_uzerinde_yetkiniz_bulunmuyor'),".");
		yonlendir(ozel_url("ilanlarim"));		
	}
}
if ($yayinla) {
	if (!is_numeric($yayinla)) {
		yonlendir(ozel_url("ilanlarim"));
	}	
	$ilan_id=$yayinla;
	$ilan_durum=getir("icerik","durum",$ilan_id);
	$ilan_sahibi=getir("icerik","yazar",$ilan_id);
	if ($oturum_id==$ilan_sahibi) {
		$magaza_id=bilgi("magaza",$ilan_id);
		if ($magaza_id) {
			$magaza_baslik=getir("magazalar","baslik",$magaza_id);
		}
		$ilan_ilk_kategori=kategori_getir($ilan_id)[0];
		$kategori_dizisi=kategori_getir($ilan_id);
		if (!$magaza_id) {
			$kalan_ilan_limit=uye_ilan_limiti("kalan",$oturum_id);			
			$kategori_ilan_limit=uye_ilan_limiti("kategori",$oturum_id);
			$kategori_ilan_sayisi=mysqli_num_rows(mysqli_query($baglan,"select * from icerik where yazar='$oturum_id' and durum='1' and kategori like '%($ilan_ilk_kategori)%'"));
			$kategori_kalan_ilan_limit=$kategori_ilan_limit-$kategori_ilan_sayisi;
			$ilan_aninda_onay=uye_ilan_limiti("aninda_onay",$oturum_id);
			if ($kategori_kalan_ilan_limit>0) {
				if ($kalan_ilan_limit>0) {				
					if ($ilan_aninda_onay) {
						guncelle("icerik","durum",1,$ilan_id);
                        limit_dusur($ilan_id);
						$_SESSION["mesaj"]=array(_LANG('pn_ilan_basariyla_yayinlandi'),".");
					} else {						
						guncelle("icerik","durum",2,$ilan_id);
                        limit_dusur($ilan_id);
						$_SESSION["mesaj"]=array(_LANG('pn_ilan_inceleme_icin_gonderildi'),".");
					}
					if ($ilan_durum==4) {
						guncelle("icerik","tarih_saat",tarih_saat(),$ilan_id);
					}
					// Kategori içerik sayılarını güncelle (sadece yayınlandıysa)
					if ($ilan_aninda_onay) {
						foreach($kategori_dizisi as $yaz) {
							kategori_icerik_sayisi_guncelle($yaz);
							$kategori_cache_dosyasi= "cache/kategori/$yaz";
							temizle("$kategori_cache_dosyasi");
						}
					}
					yonlendir(ozel_url("ilanlarim"));
				} else {
					$_SESSION["mesaj"]=array(_LANG('pn_ilan_limitiniz_doldugu_icin_bu_ilani_yayinlayamazsiniz'),".");
					yonlendir(ozel_url("ilanlarim"));
				}
			} else {
				$_SESSION["mesaj"]=array(_LANG('pn_kategori_ilan_limitiniz_doldugu_icin_bu_ilani_yayinlayamazsiniz'),".");
				yonlendir(ozel_url("ilanlarim"));
			}
		} else {
			$kalan_ilan_limit=magaza_ilan_limiti("kalan",$magaza_id);
			$ilan_aninda_onay=magaza_ilan_limiti("aninda_onay",$magaza_id);
			if ($kalan_ilan_limit>0) {			
				if ($ilan_aninda_onay) {
					guncelle("icerik","durum",1,$ilan_id);
                    limit_dusur($ilan_id);
					$_SESSION["mesaj"]=array(_LANG('pn_ilan_basariyla_yayinlandi'),".");
				} else {
					guncelle("icerik","durum",2,$ilan_id);
                    limit_dusur($ilan_id);
					$_SESSION["mesaj"]=array(_LANG('pn_ilan_inceleme_icin_gonderildi'),".");
				}
				if ($ilan_durum==4) {
					guncelle("icerik","tarih_saat",tarih_saat(),$ilan_id);
				}
				// Kategori içerik sayılarını güncelle (sadece yayınlandıysa)
				if ($ilan_aninda_onay) {
					foreach($kategori_dizisi as $yaz) {
						kategori_icerik_sayisi_guncelle($yaz);
						$kategori_cache_dosyasi= "cache/kategori/$yaz";
						temizle("$kategori_cache_dosyasi");
					}
				}
				yonlendir(ozel_url("ilanlarim"));
			} else {
				$_SESSION["mesaj"]=array(_LANG('pn_ilan_limitiniz_doldugu_icin_bu_ilani_yayinlayamazsiniz'),".");
				yonlendir(ozel_url("ilanlarim"));
			}
		}
	} else {
		$_SESSION["mesaj"]=array(_LANG('pn_bu_ilan_uzerinde_yetkiniz_bulunmuyor'),".");
		yonlendir(ozel_url("ilanlarim"));
	}
}
if ($taslak) {
	if (!is_numeric($taslak)) {
		yonlendir(ozel_url("ilanlarim"));
	}
	$ilan_id=$taslak;
	$ilan_durum=getir("icerik","durum",$ilan_id);
	$ilan_sahibi=getir("icerik","yazar",$ilan_id);
	if ($oturum_id==$ilan_sahibi) {
		if ($ilan_durum==2) {
			guncelle("icerik","durum",0,$ilan_id);
			$_SESSION["mesaj"]=array(_LANG('pn_ilan_inceleme_listesinden_cikarildi'),".");
			yonlendir(ozel_url("ilanlarim"));
		} else {
			$_SESSION["mesaj"]=array(_LANG('pn_bu_ilan_inceleme_listesinde_degil'),"!");
			yonlendir(ozel_url("ilanlarim"));		
		}	
	} else {
		$_SESSION["mesaj"]=array(_LANG('pn_bu_ilan_uzerinde_yetkiniz_bulunmuyor'),".");
		yonlendir(ozel_url("ilanlarim"));			
	}
}
@$sil=$_GET["sil"];
if ($sil) {
	if (!is_numeric($sil)) {
		yonlendir(ozel_url("ilanlarim"));
	}
	$ilan_id=$sil;
	$ilan_sahibi=getir("icerik","yazar",$ilan_id);
	if ($oturum_id==$ilan_sahibi) {
		// İçerik siliniyor
		$resim_ara_sql=mysqli_query($baglan,"select * from galeri where icerik_id='$ilan_id'");
		$resim_sayisi=mysqli_num_rows($resim_ara_sql); 
		if ($resim_sayisi>0) {
			while ($galeri_dizi=mysqli_fetch_array($resim_ara_sql)) {
				$galeri_id=$galeri_dizi["id"];
				$resim=$galeri_dizi["resim"];
				sil(thumbnail_url("160_120",$resim));
				sil(thumbnail_url("530_393",$resim));
				sil($resim);
				$sil_sql = mysqli_query($baglan,"delete from galeri where id='$galeri_id'");
			}
		}
		$silinen_baslik=getir("icerik","baslik",$ilan_id);
		$kategori_dizisi=kategori_getir($ilan_id);
		
		$icerik_sil=mysqli_query($baglan,"delete from icerik where id='$ilan_id'");
		$ekstra_bilgi_sil=mysqli_query($baglan,"delete from ekstra_bilgi where id='$ilan_id' and tip='ilan'");
		$favori_sil=mysqli_query($baglan,"delete from favoriler where id='$ilan_id'");
		$_SESSION["mesaj"]=array(sprintf(_LANG('msj_ilan_silindi'), $silinen_baslik),"success");
		
		if (function_exists('lpanel_uye_profil_link')) {
			$oturum_link=lpanel_uye_profil_url($oturum_id);
			bildirim_ekle(sprintf(_LANG('bnf_icerik_silindi_linkli'), $oturum_link, $oturum_ad_soyad, $silinen_baslik),$oturum_id,0);
		} else {
			bildirim_ekle(sprintf(_LANG('bnf_icerik_silindi'), $oturum_ad_soyad, $silinen_baslik),$oturum_id,0);
		}
		
		foreach($kategori_dizisi as $yaz) {
			kategori_icerik_sayisi_guncelle($yaz);
			
			// Kategori cache siliniyor.
			$kategori_cache_dosyasi= "cache/kategori/$yaz";
			temizle("$kategori_cache_dosyasi");
		}
		yonlendir(ozel_url("ilanlarim"));
	} else {
		$_SESSION["mesaj"]=array(_LANG('pn_hata_bu_ilan_uzerinde_yetkiniz_bulunmuyor'),".");
		yonlendir(ozel_url("ilanlarim"));		
	}
}

if ($durum or $durum=="0") {
	$toplam_sql=mysqli_query($baglan,"select * from icerik where tip='ilan' and durum='$durum' and yazar='$oturum_id'");
} else {
	$toplam_sql=mysqli_query($baglan,"select * from icerik where tip='ilan' and yazar='$oturum_id'");
}
$toplam_icerik=mysqli_num_rows($toplam_sql);

@$sayfa=$_GET["sayfa"];
if (!$sayfa) {$sayfa=1;}
$listeleme_adedi=tema_ayar("listeleme_adedi");
$baslangic=($sayfa-1)*$listeleme_adedi;
$kac_sayfa=ceil($toplam_icerik/$listeleme_adedi);
if ($durum or $durum=="0") {
	$sql=mysqli_query($baglan,"select * from icerik where tip='ilan' and yazar='$oturum_id' and durum='$durum' order by tarih_saat desc limit $baslangic, $listeleme_adedi");
} else {
	$sql=mysqli_query($baglan,"select * from icerik where tip='ilan' and yazar='$oturum_id' order by tarih_saat desc limit $baslangic, $listeleme_adedi");
}
$toplam_ilan=mysqli_num_rows($sql);

tema_header(); ?>
<div class="container profil arsiv mt20">
	<?php include("temalar/$aktif_tema/inc/uye-ust.php"); ?>
	
	<div class="row">
		<div class="col-md-9 sidebar">
			<?php include("temalar/$aktif_tema/inc/uye-sol-menu.php"); ?>
		</div>
		<div class="col-md-33">
			<?php include("temalar/$aktif_tema/inc/alert.php"); ?>
	
			<div class="panel panel-default beyaz tab">
				<div class="panel-heading">
				  <ul class="nav nav-tabs ozel visible-sm visible-md visible-lg" role="tablist">
					<li role="presentation" <?php if (!$durum and $durum!="0") {echo "class='active'";} ?>><a href="<?php echo ozel_url("ilanlarim"); ?>"><?php echo _LANG("title_my_ads"); ?></a></li>
					<li role="presentation" <?php if ($durum==1) {echo "class='active'";} ?>><a href="<?php echo ozel_url("ilanlarim"); ?>?durum=1"><?php echo _LANG("post_published"); ?></a></li>
					<li role="presentation" <?php if ($durum=="0") {echo "class='active'";} ?>><a href="<?php echo ozel_url("ilanlarim"); ?>?durum=0">Taslak</a></li>
					<li role="presentation" <?php if ($durum==2) {echo "class='active'";} ?>><a href="<?php echo ozel_url("ilanlarim"); ?>?durum=2">Onay bekliyor</a></li>
				  </ul>
				  <div class="sag">
					<select>
					  <option><?php echo _LANG("search_advanced"); ?></option>
					  <option value="1"><?php echo _LANG("search_price_asc"); ?></option>
					  <option value="2"><?php echo _LANG("search_price_desc"); ?></option>
					  <option value="3"><?php echo _LANG("search_date_desc"); ?></option>
					  <option value="4"><?php echo _LANG("search_date_asc"); ?></option>
					</select>
				  </div>
				</div>
				<?php if ($toplam_ilan>0) { ?>
				<div class="panel-body">
				
					<table class="table table-condensed table-striped liste">
						<thead>
							<tr>
								<th width="110"></th>
								<th class="baslik_th" width="280"><?php echo _LANG("listing_title"); ?></th>
								<th class="visible-sm visible-md visible-lg">Fiyat</th>
								<th class="visible-md visible-lg"><?php echo _LANG("listing_date"); ?></th>
								<th class="visible-md visible-lg">Durum</th>
								<th width="140"><?php echo _LANG("general_options"); ?></th>
							</tr>
						</thead>
						<tbody>
						<?php while ($dizi=mysqli_fetch_array($sql)) { 
							$ilan_id=$dizi["id"];
							$ilan_baslik=$dizi["baslik"];
							$ilan_durum=$dizi["durum"];
							$ilan_tarih=tarih_getir($ilan_id);
							$ilan_fiyat=fiyat($ilan_id);
							$ilan_link=link_getir($ilan_id);
							$ilan_resim=resim_getir("160_120",$ilan_id);
							$ilan_ziyaret=postviews($ilan_id);
							$ilan_favori=kac_kisinin_favorisi($ilan_id);
							$magaza_id=bilgi("magaza",$ilan_id);
							if ($magaza_id) {
								$magaza_baslik=getir("magazalar","baslik",$magaza_id);
								$magaza_link=magaza_link_getir($magaza_id);
							}
							$ilan_ilk_kategori=kategori_getir($ilan_id)[0];
						?>
							<tr>
								<td style="padding:3px !important;"><a href="<?php echo $ilan_link; ?>"><img src="<?php echo $ilan_resim; ?>" alt="" class="img-responsive"/></a></td>
								<td class="baslik_td">
									<a href="<?php echo $ilan_link; ?>"><?php echo $ilan_baslik; ?></a>
									<p style="color:#777;font-size:12px;margin-top:3px;"><?php echo $ilan_ziyaret; ?> görüntülenme<?php if ($ilan_favori>0) {echo ", $ilan_favori kişinin favorisi";} ?></p>
									<?php if ($magaza_id) {echo "<a href='$magaza_link' target='_blank' style='color:#777;font-size:12px;margin-top:0;font-weight:500;'><i class='ion-bag'></i>$magaza_baslik</a>";} ?>
								</td>
								<td class="fiyat visible-sm visible-md visible-lg">
								<?php 
								$sahiplendirme=bilgi("sahiplendirme",$ilan_id);
								$gorusulur=bilgi("gorusulur",$ilan_id);
								if ($sahiplendirme!=1 and $gorusulur!=1) {
									echo $ilan_fiyat;
									$para_birimi=bilgi("para_birimi",$ilan_id);
									if ($para_birimi!="try") {
										$fiyat_tl=fiyat_donustur(bilgi("fiyat",$ilan_id));
										if ($fiyat_tl) {
											echo "<div class='clearfix mb5'></div><div class='tablo_tl'><i class='ion-arrow-swap'></i>$fiyat_tl <i class='fa fa-try'></i></div>";
										}
									}
								} else {
									if ($sahiplendirme==1) {
										echo "<span class='s_mesaj_tablo'>Sahiplendirme</span>";
									}
									if ($gorusulur==1) {
										echo "<span class='s_mesaj_tablo'>Görüşülür</span>";
									}
								}
								?>
								</td>
								<td class="visible-md visible-lg"><?php echo $ilan_tarih; ?></td>
								<td class="visible-md visible-lg"><?php 
									if ($ilan_durum==0) {
										echo "<span class='label label-danger borderless'>Taslak</span>";
									} else if ($ilan_durum==1) {
										echo "<span class='label label-success borderless'>Yayında</span>";
									} else if ($ilan_durum==2) {
										echo "<span class='label label-info borderless'>Onay bekliyor</span>";
									} else if ($ilan_durum==3) {
										echo "<span class='label label-warning borderless'>Yayından kaldırıldı</span>";
									} else if ($ilan_durum==4) {
										echo "<span class='label label-danger borderless'>Süresi doldu</span>";
									}
								?></td>
								<td>
								<?php
								if (!$magaza_id) {
									$kalan_ilan_limit=uye_ilan_limiti("kalan",$oturum_id);			
									$kategori_ilan_limit=uye_ilan_limiti("kategori",$oturum_id);
									$kategori_ilan_sayisi=mysqli_num_rows(mysqli_query($baglan,"select * from icerik where yazar='$oturum_id' and durum='1' and kategori like '%($ilan_ilk_kategori)%'"));
                                } else {
									$kalan_ilan_limit=magaza_ilan_limiti("kalan",$magaza_id);
								}
								if (!$magaza_id) {
									$ilan_aninda_onay=uye_ilan_limiti("aninda_onay",$oturum_id);
								} else {
									$ilan_aninda_onay=magaza_ilan_limiti("aninda_onay",$magaza_id);
								}
								?>
								<div class="btn-group" role="group" aria-label="...">
								  <a href="<?php echo ozel_url("ilan-ekle")."?ilan=$ilan_id"; ?>" class="btn btn-primary btn-xs"><?php echo _LANG("general_update"); ?></a>
								  
								  <div class="btn-group" role="group">
									<button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="caret ml0"></span></button>
									<ul class="dropdown-menu dropdown-menu-right">
										<?php if ($ilan_durum==1) { ?><li><a href="<?php echo ozel_url("ilanlarim")."?pasif=$ilan_id"; ?>" onclick="return confirm('İlanı yayından kaldırmak istiyor musunuz?')"><?php echo _LANG("post_unpublish"); ?></a></li><?php } 
										else if ($ilan_durum!=2) { 
										if ($magaza_id and $kalan_ilan_limit>0) {
										// Mağaza
										if ($ilan_aninda_onay) { ?>
											<li><a href="<?php echo ozel_url("ilanlarim")."?yayinla=$ilan_id"; ?>" <?php if ($limit_politikasi==2) {echo "onclick='return confirm(\"İlan doğrudan yayınlanacak ve ilan krediniz 1 azaltılacaktır. Onaylıyor musunuz?\");'";} ?>><?php echo _LANG("post_publish_now"); ?></a></li>
										<?php } else { ?>
											<li><a href="<?php echo ozel_url("ilanlarim")."?yayinla=$ilan_id"; ?>" <?php if ($limit_politikasi==2) {echo "onclick='return confirm(\"İlan onay kuyruğuna eklenecek ve ilan krediniz 1 azaltılacaktır. Onaylıyor musunuz?\");'";} ?>><?php echo _LANG("post_publish"); ?></a></li>
										<?php } 
										} else {
										// Üye
										if ($kategori_ilan_limit-$kategori_ilan_sayisi>0 and $kalan_ilan_limit>0) {
										if ($ilan_aninda_onay) { ?>
											<li><a href="<?php echo ozel_url("ilanlarim")."?yayinla=$ilan_id"; ?>" <?php if ($limit_politikasi==2) {echo "onclick='return confirm(\"İlan doğrudan yayınlanacak ve ilan krediniz 1 azaltılacaktır. Onaylıyor musunuz?\");'";} ?>><?php echo _LANG("post_publish_now"); ?></a></li>
										<?php } else { ?>
											<li><a href="<?php echo ozel_url("ilanlarim")."?yayinla=$ilan_id"; ?>" <?php if ($limit_politikasi==2) {echo "onclick='return confirm(\"İlan onay kuyruğuna eklenecek ve ilan krediniz 1 azaltılacaktır. Onaylıyor musunuz?\");'";} ?>><?php echo _LANG("post_publish"); ?></a></li>
										<?php } 
										} else { ?>
											<li><a data-toggle="tooltip" title="Kategori ilan limitiniz dolduğu için bu ilanı yayınlayamazsınız." class="c_pointer" onclick="event.preventDefault();"><?php echo _LANG("s_yayinlanamaz"); ?></a></li>
										<?php } } 
										} else if ($ilan_durum==2) { ?>
											<li><a href="<?php echo ozel_url("ilanlarim")."?taslak=$ilan_id"; ?>"><?php echo _LANG("post_save_draft"); ?></a></li>
										<?php } ?>
									  <li><a href="<?php echo ozel_url("doping")."?ilan_id=$ilan_id"; ?>"><?php echo _LANG("title_buy_boost"); ?></a></li>
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
			<?php 
			if ($kac_sayfa>1) {
				echo "<div class='clearfix mb20'></div>";
				echo "<div class='text-center'><ul class='pagination'>";
				$sayfa_url=ozel_url("ilanlarim");
				if ($sayfa>1) {
					$onceki_sayfa=$sayfa-1;
					echo "<li><a href='$sayfa_url?sayfa=$onceki_sayfa' aria-label='Önceki'><i class='ion-chevron-left'></i></a></li>";
				}		
				for ($x=1;$x<=$kac_sayfa;$x++) {
					if ($x==$sayfa) {$active="class='active'";$link_yaz="";} else {$active="";$link_yaz="href='$sayfa_url?sayfa=$x'";}
					echo "<li $active><a $link_yaz>$x</a></li>";
				}
				if ($sayfa<$kac_sayfa) {
					$sonraki_sayfa=$sayfa+1; 
					echo "<li><a href='$sayfa_url?sayfa=$sonraki_sayfa' aria-label='Önceki'><i class='ion-chevron-right'></i></a></li>";
				}
				echo "</ul></div>";
			} 
			} else { ?>
			<div class="panel panel-default beyaz sonuc">
				<div class="panel-body">
					İlan bulunamadı.
				</div>
			</div>
			<?php } ?>				
			</div>
			

			

		</div>
	</div>
</div>

<?php tema_footer(); ?>
<script type="text/javascript">
$(function(){
	$('[data-toggle="tooltip"]').tooltip();
});
</script>