<?php if(count(get_included_files())==1) {die();} 
$il_ilce_mahalle=tema_ayar("il_ilce_mahalle");
?>
<div class="panel-body">
	<table class="table table-condensed table-striped liste">
		<thead>
			<tr>
				<th width="110"></th>
				<th class="baslik_th" width="280"><?php echo _LANG("listing_title"); ?></th>
				<?php 
				if (is_array(@$kat_detaylar)) {
					foreach ($kat_detaylar as $detay) {
						$detay_parcala=explode("|",$detay);
						$detay_baslik=$detay_parcala[1];
						echo "<th class='visible-md visible-lg'>$detay_baslik</th>";
					}
				}
				?>
				<th class="visible-sm visible-md visible-lg">Fiyat</th>
				<th class="visible-md visible-lg"><?php echo _LANG("listing_date"); ?></th>
				<th class="visible-sm visible-md visible-lg">Adres</th>
			</tr>
		</thead>
		<tbody>
		<?php if (is_array(@$vitrin_dizi)) {foreach ($vitrin_dizi as $vitrin) {
			$ilan_id=$vitrin;
			$ilan_baslik=getir("icerik","baslik",$vitrin);
			$ilan_tarih=tarih_getir($ilan_id);
			$ilan_fiyat=fiyat($ilan_id);
			$ilan_link=link_getir($ilan_id);
			$ilan_resim=resim_getir("160_120",$ilan_id);
			$class_yaz=null;
			if (doping_sor($ilan_id,"kalin_yazi")) {$class_yaz.=" bold";}
			$etiketler=null;
			$etiketler.="<span class='etiket vitrin' data-toggle='tooltip' data-placement='bottom' title='' . _LANG(\'p_vitrin_ilani\') . ''><i class='ion-podium mr0'></i></span>";
			if (doping_sor($ilan_id,"acil_acil")) {
				$etiketler.="<span class='etiket acil' data-toggle='tooltip' data-placement='bottom' title='" . _LANG('title_urgent') . "'><i class='fa fa-clock-o mr0'></i></span>";
			}
		?>
			<tr class="satir_link c_pointer vitrin vitrin_sticker <?php echo $class_yaz; ?>" data-href="<?php echo $ilan_link; ?>">
				<td style="padding:3px 3px !important;"><a href="<?php echo $ilan_link; ?>"><img src="<?php echo $ilan_resim; ?>" alt="<?php echo $ilan_baslik; ?>" class="resim img-responsive"/></a></td>
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
							if ($para_birimi!="gmd") {
								$fiyat_gmd=fiyat_donustur(bilgi("fiyat",$ilan_id));
								if ($fiyat_gmd) {
									echo "<div class='clearfix mb5'></div><div class='tablo_tl'><i class='ion-arrow-swap'></i>$fiyat_gmd GMD</div>";
								}
							}
						} else {
							if ($sahiplendirme==1) {
								echo "<span class='s_mesaj_tablo'>Sahiplendirme</span>";
							}
							if ($gorusulur==1) {
								echo "<span class='s_mesaj_tablo'>" . _LANG('s_gorusulur') . "</span>";
							}
						}
						?>
						</div>
						<div class="il_ilce il_ilce_mobil">
							<span><?php echo il_getir($ilan_id); ?></span>
							<span><?php echo ilce_getir($ilan_id); ?></span>
						</div>
					</div>
					<div class="clearfix mb5"></div>
					<?php echo $etiketler; ?>
				</td>
				<?php 
				if (is_array(@$kat_detaylar)) {
					foreach ($kat_detaylar as $detay) {
						$detay_parcala=explode("|",$detay);
						$detay_anahtar=$detay_parcala[0];
						$deger=bilgi($detay_anahtar,$ilan_id);
						echo "<td class='visible-md visible-lg'>$deger</td>";
					}
				}
				?>								
				<td class="fiyat visible-sm visible-md visible-lg">
				<?php 
				$sahiplendirme=bilgi("sahiplendirme",$ilan_id);
				$gorusulur=bilgi("gorusulur",$ilan_id);
				if ($sahiplendirme!=1 and $gorusulur!=1) {
					echo $ilan_fiyat;
					$para_birimi=bilgi("para_birimi",$ilan_id);
					if ($para_birimi!="gmd") {
						$fiyat_gmd=fiyat_donustur(bilgi("fiyat",$ilan_id));
						if ($fiyat_gmd) {
							echo "<div class='clearfix mb5'></div><div class='tablo_tl'><i class='ion-arrow-swap'></i>$fiyat_gmd GMD</div>";
						}
					}
				} else {
					if ($sahiplendirme==1) {
						echo "<span class='s_mesaj_tablo'>Sahiplendirme</span>";
					}
					if ($gorusulur==1) {
						echo "<span class='s_mesaj_tablo'>" . _LANG('s_gorusulur') . "</span>";
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
		<?php } } if (is_array(@$vitrin_dizi) and count(@$vitrin_dizi)>0) { ?>
		<tr class="satir_link c_pointer vitrin" data-href="<?php echo ozel_url("doping"); ?>">
			<td colspan="<?php echo 5+count($kat_detaylar); ?>" class="vitrin_mesaj"><p>' . _LANG('s_ilani') . 'nız yukarıda yer alsın, alıcılara daha kısa sürede ulaşın! Detaylı bilgi için tıklayın.</p></td>
		</tr>
		<?php } while ($dizi=mysqli_fetch_array($sql)) {
			$ilan_id=$dizi["id"];
			$ilan_baslik=$dizi["baslik"];
			$ilan_tarih=tarih_getir($ilan_id);
			$ilan_fiyat=fiyat($ilan_id);
			$ilan_link=link_getir($ilan_id);
			$ilan_resim=resim_getir("160_120",$ilan_id);
			$class_yaz=null;
			if (doping_sor($ilan_id,"kalin_yazi")) {$class_yaz.="bold ";}
			$etiketler=null;
			if (doping_sor($ilan_id,"acil_acil")) {
				$class_yaz.="acil acil_sticker "; 
				$etiketler.="<span class='etiket acil' data-toggle='tooltip' data-placement='bottom' title='" . _LANG('title_urgent') . "'><i class='fa fa-clock-o mr0'></i></span>";
			}
			if (doping_sor($ilan_id,"anasayfa_vitrini")) {
				$class_yaz.="vitrin_sticker "; 
				$etiketler.="<span class='etiket vitrin' data-toggle='tooltip' data-placement='bottom' title='' . _LANG(\'p_vitrin_ilani\') . ''><i class='ion-podium mr0'></i></span>";
			}
		?>
			
			<tr class="satir_link c_pointer <?php echo $class_yaz; ?>" data-href="<?php echo $ilan_link; ?>">
				<td style="padding:3px 3px !important;"><a href="<?php echo $ilan_link; ?>"><img src="<?php echo $ilan_resim; ?>" alt="<?php echo $ilan_baslik; ?>" class="resim img-responsive"/></a></td>
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
							if ($para_birimi!="gmd") {
								$fiyat_gmd=fiyat_donustur(bilgi("fiyat",$ilan_id));
								if ($fiyat_gmd) {
									echo "<div class='clearfix mb5'></div><div class='tablo_tl'><i class='ion-arrow-swap'></i>$fiyat_gmd GMD</div>";
								}
							}
						} else {
							if ($sahiplendirme==1) {
								echo "<span class='s_mesaj_tablo'>Sahiplendirme</span>";
							}
							if ($gorusulur==1) {
								echo "<span class='s_mesaj_tablo'>" . _LANG('s_gorusulur') . "</span>";
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
					<?php echo $etiketler; ?>
				</td>
				<?php 
				if (is_array(@$kat_detaylar)) {
					foreach ($kat_detaylar as $detay) {
						$detay_parcala=explode("|",$detay);
						$detay_anahtar=$detay_parcala[0];
						$deger=bilgi($detay_anahtar,$ilan_id);
						echo "<td class='visible-md visible-lg'>$deger</td>";
					}
				}
				?>								
				<td class="fiyat visible-sm visible-md visible-lg">
				<?php 
				$sahiplendirme=bilgi("sahiplendirme",$ilan_id);
				$gorusulur=bilgi("gorusulur",$ilan_id);
				if ($sahiplendirme!=1 and $gorusulur!=1) {
					echo $ilan_fiyat;
					$para_birimi=bilgi("para_birimi",$ilan_id);
					if ($para_birimi!="gmd") {
						$fiyat_gmd=fiyat_donustur(bilgi("fiyat",$ilan_id));
						if ($fiyat_gmd) {
							echo "<div class='clearfix mb5'></div><div class='tablo_tl'><i class='ion-arrow-swap'></i>$fiyat_gmd GMD</div>";
						}
					}
				} else {
					if ($sahiplendirme==1) {
						echo "<span class='s_mesaj_tablo'>Sahiplendirme</span>";
					}
					if ($gorusulur==1) {
						echo "<span class='s_mesaj_tablo'>" . _LANG('s_gorusulur') . "</span>";
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