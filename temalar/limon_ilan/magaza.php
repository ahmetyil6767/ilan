<?php 
if(count(get_included_files())==1) {die();}

$il_ilce_mahalle=tema_ayar("il_ilce_mahalle");

@$sirala=$_GET["sirala"];
if ($sirala) {
	$_SESSION["sirala"]=$sirala;
	yonlendir(magaza_link_getir($id));
}

// Null
$kat_detaylar=array();

$magaza_baslik=getir("magazalar","baslik",$id);
$magaza_durum=getir("magazalar","durum",$id);
$magaza_sahibi=getir("magazalar","uye_id",$id);

if ($magaza_durum==0 and $oturum_id!=$magaza_sahibi) {
	$_SESSION["mesaj"]=array(_LANG('pn_bu_magaza_onay_kuyrugundadir'),".");
	yonlendir(site_url());
}
if ($magaza_durum==2 and $oturum_id!=$magaza_sahibi) {
	$_SESSION["mesaj"]=array(sprintf(_LANG('msj_magaza_suresi_doldu'), $magaza_baslik),"danger");
	yonlendir(site_url());
}
if (!$magaza_baslik) {
	$_SESSION["mesaj"]=array(_LANG('pn_boyle_bir_magaza_mevcut_degil'),".");
	yonlendir(site_url());
}
if (@$_SESSION["sirala"]) {
	if ($_SESSION["sirala"]=="tarih_yeni") {
		$orderby_sql="i.tarih_saat desc";
	} else if ($_SESSION["sirala"]=="tarih_eski") {
		$orderby_sql="i.tarih_saat asc";
	} else if ($_SESSION["sirala"]=="fiyat_artan") {
		$orderby_sql="convert(ef.deger, decimal) asc";
	} else if ($_SESSION["sirala"]=="fiyat_azalan") {
		$orderby_sql="convert(ef.deger, decimal) desc";
	} else {
		$orderby_sql="i.tarih_saat desc";
	}
} else {
	$orderby_sql="tarih_saat desc";	
}

$toplam_sql=mysqli_query($baglan,"
SELECT i.id, i.baslik, i.durum, i.tarih_saat, i.yazar, ef.deger from icerik as i 
INNER JOIN ekstra_bilgi as ef ON i.id=ef.id and ef.anahtar='fiyat' 
INNER JOIN ekstra_bilgi as em ON i.id=em.id and em.anahtar='magaza' and em.deger='$id' 
WHERE i.tip='ilan' and i.durum='1'
");
$toplam_icerik=mysqli_num_rows($toplam_sql);

@$sayfa=$_GET["sayfa"];
if (!$sayfa) {$sayfa=1;}
$listeleme_adedi=tema_ayar("listeleme_adedi");
$baslangic=($sayfa-1)*$listeleme_adedi;
$kac_sayfa=ceil($toplam_icerik/$listeleme_adedi);
$sql=mysqli_query($baglan,"
SELECT i.id, i.baslik, i.durum, i.tarih_saat, i.yazar, ef.deger from icerik as i 
INNER JOIN ekstra_bilgi as ef ON i.id=ef.id and ef.anahtar='fiyat'
INNER JOIN ekstra_bilgi as em ON i.id=em.id and em.anahtar='magaza' and em.deger='$id' 
WHERE i.tip='ilan' and i.durum='1' ORDER BY $orderby_sql limit $baslangic, $listeleme_adedi");
$toplam_ilan=mysqli_num_rows($sql);

tema_header(); ?>
<div class="container profil arsiv mt20">

	<?php include("temalar/$aktif_tema/inc/magaza-ust.php"); ?>
	<?php include("temalar/$aktif_tema/inc/magaza-limit-bilgi.php"); ?>
	
	<div class="row">
		<div class="col-md-9 sidebar">
		
		    <?php if ($oturum_id==getir("magazalar","uye_id",$id)) {include("temalar/$aktif_tema/inc/magaza-sol-menu.php");} ?>
		
			<?php 
			$magaza_hakkinda=magaza_bilgi("hakkinda",$id); 
			if (!$magaza_hakkinda) {
				$magaza_hakkinda=" . _LANG('tx_magaza_hakkinda_bilgi_girilmemis') . ";
			}
			
			$magaza_tel=magaza_bilgi("magaza_tel",$id); 
			$fb_url=magaza_bilgi("fb_url",$id); 
			$twitter_url=magaza_bilgi("twitter_url",$id); 
			$google_url=magaza_bilgi("google_url",$id); 
			$instagram_url=magaza_bilgi("instagram_url",$id); 
			if ($magaza_tel or $fb_url or $twitter_url or $instagram_url) {
				$sosyal=1;
			}
			?>
			<div class="panel panel-default limon tablo">
				<div><?php echo _LANG('hb_magaza_hakkinda'); ?></div>
				<div class="panel-body">
					<p><?php echo $magaza_hakkinda; ?></p>
					<?php if ($oturum_id==$magaza_sahibi) { ?><a href="<?php echo ozel_url("magaza-bilgileri"); ?>?id=<?php echo $id; ?>"><?php echo _LANG("title_update_store"); ?></a>
					<?php } ?>
				</div>
			</div>
			<?php if (@$sosyal==1) { ?>
			<div class="panel panel-default limon magaza_sosyal">
				<div><?php echo _LANG('hb_iletisim_bilgileri'); ?></div>
				<div class="panel-body" style="padding:0 !important;">
					<table class="table table-default profil padding" style="margin-bottom:0;">
						<?php if ($magaza_tel) { ?>
						<tr>
							<td><a href="tel:<?php echo telefon_bicimlendir($magaza_tel); ?>"><i class="fa fa-phone fa-fw"></i><?php echo $magaza_tel; ?></a></td>
						</tr>
						<?php } if ($fb_url) { ?>
						<tr>
							<td><a href="<?php echo $fb_url; ?>" target="_blank"><i class="fa fa-facebook fa-fw"></i>Facebook profili</a></td>
						</tr>
						<?php } if ($twitter_url) { ?>
						<tr>
							<td><a href="<?php echo $twitter_url; ?>" target="_blank"><i class="fa fa-twitter fa-fw"></i>Twitter profili</a></td>
						</tr>
						<?php } if ($instagram_url) { ?>
						<tr>
							<td><a href="<?php echo $instagram_url; ?>" target="_blank"><i class="fa fa-instagram fa-fw"></i>Instagram profili</a></td>
						</tr>
						<?php } ?>
					</table>
				</div>
			</div>
			<?php } ?>
		</div>
		<div class="col-md-33">
			<?php include("temalar/$aktif_tema/inc/alert.php"); ?>
						
			<div class="panel panel-default beyaz sonuc visible-md visible-lg">
				<div class="panel-body">
					<?php if ($toplam_ilan>0) { 
						echo "<strong>$magaza_baslik</strong> adlı mağazanın <span class='sayi'>$toplam_icerik</span> ilanı mevcut.";
					} else {
						echo "<strong>$magaza_baslik</strong> adlı mağazanın yayında ilanı bulunamadı.";
					} ?>
				</div>
			</div>		
			
			<?php if ($toplam_ilan>0) { ?>
			<div class="panel panel-default beyaz tab">
				<div class="panel-heading">
				  <ul class="nav nav-tabs ozel visible-sm visible-md visible-lg" role="tablist">
					<li role="presentation" class="active"><a href="#tumu" aria-controls="tumu" role="tab" data-toggle="tab">Tüm Mağaza İlanları</a></li>
				  </ul>

				  <div class="sag">
					<select id="jump" name="jump">
					  <option value="<?php echo magaza_link_getir($id); ?>?sirala=gelismis" <?php if ($_SESSION["sirala"]=="gelismis" or !$_SESSION["sirala"]) {echo "selected='selected'";} ?>><?php echo _LANG("search_advanced"); ?></option>
					  <option value="<?php echo magaza_link_getir($id); ?>?sirala=fiyat_artan" <?php if ($_SESSION["sirala"]=="fiyat_artan") {echo "selected='selected'";} ?>><?php echo _LANG("search_price_asc"); ?></option>
					  <option value="<?php echo magaza_link_getir($id); ?>?sirala=fiyat_azalan" <?php if ($_SESSION["sirala"]=="fiyat_azalan") {echo "selected='selected'";} ?>><?php echo _LANG("search_price_desc"); ?></option>
					  <option value="<?php echo magaza_link_getir($id); ?>?sirala=tarih_yeni" <?php if ($_SESSION["sirala"]=="tarih_yeni") {echo "selected='selected'";} ?>><?php echo _LANG("search_date_desc"); ?></option>
					  <option value="<?php echo magaza_link_getir($id); ?>?sirala=tarih_eski" <?php if ($_SESSION["sirala"]=="tarih_eski") {echo "selected='selected'";} ?>><?php echo _LANG("search_date_asc"); ?></option>
					</select>
				  </div>
				</div>
				<div class="panel-body">
					<table class="table table-condensed table-striped liste">
						<thead>
							<tr>
								<th width="110"></th>
								<th class="baslik_th" width="280"><?php echo _LANG("listing_title"); ?></th>
								<th class="visible-sm visible-md visible-lg">Fiyat</th>
								<th class="visible-md visible-lg"><?php echo _LANG("listing_date"); ?></th>
								<th class="visible-sm visible-md visible-lg">Adres</th>
							</tr>
						</thead>
						<tbody>
						<?php while ($dizi=mysqli_fetch_array($sql)) { 
							$ilan_id=$dizi["id"];
							$ilan_baslik=$dizi["baslik"];
							$ilan_tarih=tarih_getir($ilan_id);
							$ilan_fiyat=fiyat($ilan_id);
							$ilan_link=link_getir($ilan_id);
							$ilan_resim=resim_getir("160_120",$ilan_id);
						?>
						<tr class="satir_link c_pointer <?php echo @$class_yaz; ?>" data-href="<?php echo $ilan_link; ?>">
							<td><a href="<?php echo $ilan_link; ?>"><img src="<?php echo $ilan_resim; ?>" alt="<?php echo $ilan_baslik; ?>" class="resim img-responsive"/></a></td>
							<td class="baslik_td">
								<span class="baslik"><a href="<?php echo $ilan_link; ?>"><?php echo $ilan_baslik; ?></a></span>
								<div class="visible-xs">
									<div class="fiyat_mobil">
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
									</div>
									<div class="il_ilce">
										<span><?php echo il_getir($ilan_id); ?></span>
										<?php if ($il_ilce_mahalle!=3) { if (ilce_getir($ilan_id)) { ?><span><?php echo ilce_getir($ilan_id); ?></span><?php } } ?>
									</div>
								</div>
								<div class="clearfix mb5"></div>
								<?php echo @$etiketler; ?>
							</td>
							<?php 
							foreach ($kat_detaylar as $detay) {
								$detay_parcala=explode("|",$detay);
								$detay_anahtar=$detay_parcala[0];
								$deger=bilgi($detay_anahtar,$ilan_id);
								echo "<td class='visible-md visible-lg'>$deger</td>";
							}
							?>								
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
							<td class="visible-sm visible-md visible-lg">
								<div class="il_ilce">
									<span><?php echo il_getir($ilan_id); ?></span>
									<?php if ($il_ilce_mahalle!=3) { if (ilce_getir($ilan_id)) { ?><span><?php echo ilce_getir($ilan_id); ?></span><?php } } ?>
								</div>
							</td>
						</tr>
						<?php }  ?>
						</tbody>
					</table>
				</div>
			</div>
			
			<?php 
			if ($kac_sayfa>1) {
				echo "<div class='text-center'><ul class='pagination'>";
				$sayfa_url=magaza_link_getir($id)."?sayfa=";
				if ($sayfa>1) {
					$onceki_sayfa=$sayfa-1;
					echo "<li><a href='$sayfa_url$onceki_sayfa' aria-label='Önceki'><i class='ion-chevron-left'></i></a></li>";
				}		
				for ($x=1;$x<=$kac_sayfa;$x++) {
					$bosluk=ceil($kac_sayfa/20)*2.2;
					if (($x==1 or $x==$kac_sayfa or ($x<$sayfa-$bosluk and $x%$bosluk==0)) or ($x>$sayfa-4 and $x<$sayfa+4) or ($x>$sayfa+$bosluk and $x%$bosluk==0)) {
						if ($x==$sayfa) {$active="class='active'";$link_yaz="";} else {$active="";$link_yaz="href='$sayfa_url$x'";}
						echo "<li $active><a $link_yaz>$x</a></li>";
					}
				}
				if ($sayfa<$kac_sayfa) {
					$sonraki_sayfa=$sayfa+1; 
					echo "<li><a href='$sayfa_url$sonraki_sayfa' aria-label='Önceki'><i class='ion-chevron-right'></i></a></li>";
				}
				echo "</ul></div>";
			}
			} else {
				if (@$uygulanan_filtreler) { 
					echo "<div class='alert alert-danger'>Kriterlerinize uygun ilan bulunamadı.</div>";
				} else {
					echo "<div class='alert alert-danger'>İlan bulunamadı.</div>";
				} 
			} ?>
		</div>
	</div>
</div>

<?php tema_footer(); ?>