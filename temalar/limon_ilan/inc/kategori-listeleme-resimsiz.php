<?php if(count(get_included_files())==1) {die();} ?>
<div class="panel-body">
	<table class="table table-condensed table-striped liste">
		<thead>
			<tr>
				<th class="baslik_th" width="280"><?php echo _LANG("listing_title"); ?></th>
				<?php 
				foreach ($kat_detaylar as $detay) {
					$detay_parcala=explode("|",$detay);
					$detay_baslik=isset($detay_parcala[1]) ? $detay_parcala[1] : '';
					echo "<th>$detay_baslik</th>";
				}
				?>
				<th>Fiyat</th>
				<th><?php echo _LANG("listing_date"); ?></th>
				<th>Adres</th>
			</tr>
		</thead>
		<tbody>
		<?php foreach ($vitrin_dizi as $vitrin) {
			$ilan_id=$vitrin;
			$ilan_baslik=getir("icerik","baslik",$vitrin);
			$ilan_tarih=tarih_getir($ilan_id);
			$ilan_fiyat=fiyat($ilan_id);
			$ilan_link=link_getir($ilan_id);
			$ilan_resim=resim_getir("160_120",$ilan_id);
			$class_yaz=null;
			if (doping_sor($ilan_id,"kalin_yazi")) {$class_yaz.=" bold";}
			$etiketler=null;
			$etiketler.="<span class='etiket vitrin' data-toggle='tooltip' data-placement='bottom' title='Vitrin İlanı'><i class='ion-podium mr0'></i></span>";
			if (doping_sor($ilan_id,"acil_acil")) {
				$etiketler.="<span class='etiket acil' data-toggle='tooltip' data-placement='bottom' title='Acil Satılık'><i class='fa fa-clock-o mr0'></i></span>";
			}
		?>
			<tr class="satir_link c_pointer vitrin <?php echo $class_yaz; ?>" data-href="<?php echo $ilan_link; ?>">
				<td class="baslik_td">
				<span class="baslik"><?php echo $ilan_baslik; ?></span>
				<div class="clearfix mb5"></div>
				<?php echo $etiketler; ?>
				</td>
				<?php 
				foreach ($kat_detaylar as $detay) {
					$detay_parcala=explode("|",$detay);
					$detay_anahtar=$detay_parcala[0];
					$deger=bilgi($detay_anahtar,$ilan_id);
					echo "<td>$deger</td>";
				}
				?>								
				<td class="fiyat">
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
				<td><?php echo $ilan_tarih; ?></td>
				<td>
					<div class="il_ilce">
						<span><?php echo il_getir($ilan_id); ?></span>
						<span><?php echo ilce_getir($ilan_id); ?></span>
					</div>
				</td>
			</tr>
		<?php } if (count($vitrin_dizi)>0) { ?>
		<tr class="satir_link c_pointer vitrin" data-href="<?php echo ozel_url("doping"); ?>">
			<td colspan="<?php echo 5+count($kat_detaylar); ?>" class="vitrin_mesaj"><p>İlanınız yukarıda yer alsın, alıcılara daha kısa sürede ulaşın! Detaylı bilgi için tıklayın.</p></td>
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
				$class_yaz.="acil "; 
				$etiketler.="<span class='etiket acil' data-toggle='tooltip' data-placement='bottom' title='Acil Satılık'><i class='fa fa-clock-o mr0'></i></span>";
			}
		?>
			
			<tr class="satir_link c_pointer <?php echo $class_yaz; ?>" data-href="<?php echo $ilan_link; ?>">
				<td class="baslik_td">
				<span class="baslik"><?php echo $ilan_baslik; ?></span>
				<?php echo $etiketler; ?>
				</td>
				<?php 
				foreach ($kat_detaylar as $detay) {
					$detay_parcala=explode("|",$detay);
					$detay_anahtar=$detay_parcala[0];
					$deger=bilgi($detay_anahtar,$ilan_id);
					echo "<td>$deger</td>";
				}
				?>								
				<td class="fiyat">
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
				<td><?php echo $ilan_tarih; ?></td>
				<td>
					<div class="il_ilce">
						<span><?php echo il_getir($ilan_id); ?></span>
						<span><?php echo ilce_getir($ilan_id); ?></span>
					</div>
				</td>
			</tr>
		<?php }  ?>
		

		</tbody>
	</table>				
	
</div>