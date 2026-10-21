<?php 
if(count(get_included_files())==1) {die();}

$il_ilce_mahalle=tema_ayar("il_ilce_mahalle");

@$sirala=$_GET["sirala"];
if (@$sirala) {
	$_SESSION["sirala"]=$sirala;
	yonlendir(profil_link_getir($id));
}

// Null
$kat_detaylar=array();

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
WHERE i.tip='ilan' and i.durum='1' and i.yazar='$id'
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
WHERE i.tip='ilan' and i.durum='1' and i.yazar='$id' ORDER BY $orderby_sql limit $baslangic, $listeleme_adedi");
$toplam_ilan=mysqli_num_rows($sql);

tema_header(); ?>
<div class="container profil arsiv mt20">
	<?php include("temalar/$aktif_tema/inc/uye-ust.php"); ?>
    <?php include("temalar/$aktif_tema/inc/uye-limit-bilgi.php"); ?>
	<div class="row">
	<?php if ($oturum_id==$id) { ?>
		<div class="col-md-9 sidebar">
			<?php include("temalar/$aktif_tema/inc/uye-sol-menu.php"); ?>
		</div>
	<?php } ?>
		<div class="<?php if ($oturum_id==$id) {echo "col-md-33";} else {echo "col-md-42";} ?>">
		
			<?php include("temalar/$aktif_tema/inc/alert.php"); ?>
			
			<div class="panel panel-default beyaz sonuc visible-md visible-lg">
				<div class="panel-body">
					<?php if ($toplam_ilan>0) { 
						echo _LANG('profile_listing_count', array($ad_soyad, $toplam_ilan));
					} else {
						echo _LANG('profile_no_listings', array($ad_soyad));
					} ?>
				</div>
			</div>
			
			<?php if (@$uygulanan_filtre_sayisi>0) { ?>
			<div class="panel panel-default beyaz sonuc uygulanan_filtreler">
				<div class="panel-body">
					<ul>
						<?php 
						foreach ($uygulanan_filtreler as $uf) {
							$uf_id=$uf["id"];
							$uf_baslik=$uf["baslik"];
							$uf_deger=$uf["deger"];
							$uf_tip=$uf["tip"];
							$uf_min_deger=$uf["deger_min"];
							$uf_max_deger=$uf["deger_max"];
							if ($uf_tip=="min_max") {
								if ($uf_min_deger) {
									$uf_deger.=" min. $uf_min_deger -";
								}
								if ($uf_max_deger) {
									$uf_deger.=" max. $uf_max_deger -";
								}
							}
							$uf_deger=substr($uf_deger,0,-2);
							echo "<li>$uf_baslik: <a href='filtre_iptal=$uf_id'><span>$uf_deger<i class='ion-android-close'></i></span></a></li>";
						}
						
						if ($uygulanan_filtre_sayisi>1) { ?><li class="temizle"><a href="<?php echo kategori_link_getir($id); ?>"><span><i class="ion-android-close"></i><?php echo _LANG("filter_clear_all"); ?></span></a></li><?php } ?>
					</ul>
				</div>
			</div>	
			<?php } ?>
			
			<?php if ($toplam_ilan>0) { ?>
			<div class="panel panel-default beyaz tab">
				<div class="panel-heading">
				  <ul class="nav nav-tabs ozel visible-sm visible-md visible-lg" role="tablist">
					<li role="presentation" class="active"><a href="#tumu" aria-controls="tumu" role="tab" data-toggle="tab"><?php echo _LANG("search_all_listings"); ?></a></li>
				  </ul>
				  <div class="sag">
					<select id="jump" name="jump">
					  <option value="<?php echo profil_link_getir($id); ?>?sirala=gelismis" <?php if ($_SESSION["sirala"]=="gelismis" or !$_SESSION["sirala"]) {echo "selected='selected'";} ?>><?php echo _LANG("search_advanced"); ?></option>
					  <option value="<?php echo profil_link_getir($id); ?>?sirala=fiyat_artan" <?php if ($_SESSION["sirala"]=="fiyat_artan") {echo "selected='selected'";} ?>><?php echo _LANG("search_price_asc"); ?></option>
					  <option value="<?php echo profil_link_getir($id); ?>?sirala=fiyat_azalan" <?php if ($_SESSION["sirala"]=="fiyat_azalan") {echo "selected='selected'";} ?>><?php echo _LANG("search_price_desc"); ?></option>
					  <option value="<?php echo profil_link_getir($id); ?>?sirala=tarih_yeni" <?php if ($_SESSION["sirala"]=="tarih_yeni") {echo "selected='selected'";} ?>><?php echo _LANG("search_date_desc"); ?></option>
					  <option value="<?php echo profil_link_getir($id); ?>?sirala=tarih_eski" <?php if ($_SESSION["sirala"]=="tarih_eski") {echo "selected='selected'";} ?>><?php echo _LANG("search_date_asc"); ?></option>
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
							<td style="padding:4px !important;"><a href="<?php echo $ilan_link; ?>"><img src="<?php echo $ilan_resim; ?>" alt="<?php echo $ilan_baslik; ?>" class="resim img-responsive"/></a></td>
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
				$sayfa_url=profil_link_getir($id);
				if ($sayfa>1) {
					$onceki_sayfa=$sayfa-1;
					echo "<li><a href='$sayfa_url/$onceki_sayfa' aria-label='Önceki'><i class='ion-chevron-left'></i></a></li>";
				}		
				for ($x=1;$x<=$kac_sayfa;$x++) {
					if ($x==$sayfa) {$active="class='active'";$link_yaz="";} else {$active="";$link_yaz="href='$sayfa_url/$x'";}
					echo "<li $active><a $link_yaz>$x</a></li>";
				}
				if ($sayfa<$kac_sayfa) {
					$sonraki_sayfa=$sayfa+1; 
					echo "<li><a href='$sayfa_url/$sonraki_sayfa' aria-label='Önceki'><i class='ion-chevron-right'></i></a></li>";
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