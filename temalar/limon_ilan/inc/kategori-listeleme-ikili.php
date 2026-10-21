<?php if(count(get_included_files())==1) {die();} ?>
<div class="panel-body">
	<div class="row">
	<?php 
	$x=1;
	foreach ($vitrin_dizi as $vitrin) {
		$ilan_id=$vitrin;
		$ilan_baslik=getir("icerik","baslik",$vitrin);
		$ilan_tarih=tarih_getir($ilan_id);
		$ilan_fiyat=fiyat($ilan_id);
		$ilan_link=link_getir($ilan_id);
		$ilan_resim=resim_getir("160_120",$ilan_id);
		$class_yaz=null;
		if (doping_sor($ilan_id,"kalin_yazi")) {$class_yaz.="bold ";}
		$etiketler=null;
		$etiketler.="<span class='etiket vitrin' data-toggle='tooltip' data-placement='bottom' title='Vitrin İlanı'><i class='ion-podium mr0'></i></span>";
		if (doping_sor($ilan_id,"acil_acil")) {
			$class_yaz.="acil"; 
			$etiketler.="<span class='etiket acil' data-toggle='tooltip' data-placement='bottom' title='Acil Satılık'><i class='fa fa-clock-o mr0'></i></span>";
		}
		if ($x%2==0) {$sol_sag_yaz="sag";} else {$sol_sag_yaz="sol";}
	?>
	<div class="col-md-21 <?php echo $sol_sag_yaz; ?>">
		<div class="ikili vitrin vitrin_sticker <?php echo $class_yaz; ?> c_pointer satir_link" data-href="<?php echo $ilan_link; ?>">
			<div class="row">
				<div class="col-md-12">
					<a href="<?php echo $ilan_link; ?>"><img src="<?php echo $ilan_resim; ?>" alt="<?php echo $ilan_baslik; ?>" class="resim img-responsive"/></a>
					<?php echo $etiketler; ?>
				</div>
				<div class="col-md-30">
					<div class="baslik" title="<?php echo $ilan_baslik; ?>"><a href="<?php echo $ilan_link; ?>"><?php echo $ilan_baslik; ?></a></div>
					<div class="fiyat">
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
							echo "<span class='s_mesaj_tablo'>Görüşülür</span>";
						}
					}
					?>
					</div>
					<div class="eb"><span class="anahtar"><?php echo _LANG("ilan_no"); ?></span><span class="deger">#<?php echo $ilan_id; ?></span></div>
					<div class="eb"><span class="anahtar"><?php echo _LANG("listing_date"); ?></span><span class="deger"><?php echo $ilan_tarih; ?></span></div>
					<?php 
					foreach ($kat_detaylar as $detay) {
						$detay_parcala=explode("|",$detay);
						$detay_anahtar=$detay_parcala[0];
						$detay_baslik=$detay_parcala[1];
						$deger=bilgi($detay_anahtar,$ilan_id);
						echo "<div class='eb'><span class='anahtar'>$detay_baslik:</span><span class='deger'>$deger</span></div>";
					}
					?>					
					<div class="il_ilce eb">
						<span><?php echo _LANG('hb_il_ilce'); ?></span>
						<span><?php echo il_getir($ilan_id); ?></span>
						<span><?php echo ilce_getir($ilan_id); ?></span>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php $x++; }
	while ($dizi=mysqli_fetch_array($sql)) {
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
		if ($x%2==0) {$sol_sag_yaz="sag";} else {$sol_sag_yaz="sol";}
	?>
	<div class="col-md-21 <?php echo $sol_sag_yaz; ?>">
		<div class="ikili <?php echo $class_yaz; ?> c_pointer satir_link" data-href="<?php echo $ilan_link; ?>">
			<div class="row">
				<div class="col-md-12">
					<a href="<?php echo $ilan_link; ?>"><img src="<?php echo $ilan_resim; ?>" alt="<?php echo $ilan_baslik; ?>" class="resim img-responsive"/></a>
					<?php echo $etiketler; ?>
				</div>
				<div class="col-md-30">
					<div class="baslik" title="<?php echo $ilan_baslik; ?>"><a href="<?php echo $ilan_link; ?>"><?php echo $ilan_baslik; ?></a></div>
					<div class="fiyat">
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
							echo "<span class='s_mesaj_tablo'>Görüşülür</span>";
						}
					}
					?>
					</div>
					<div class="eb"><span class="anahtar"><?php echo _LANG("ilan_no"); ?></span><span class="deger">#<?php echo $ilan_id; ?></span></div>
					<div class="eb"><span class="anahtar"><?php echo _LANG("listing_date"); ?></span><span class="deger"><?php echo $ilan_tarih; ?></span></div>
					<?php 
					foreach ($kat_detaylar as $detay) {
						$detay_parcala=explode("|",$detay);
						$detay_anahtar=$detay_parcala[0];
						$detay_baslik=$detay_parcala[1];
						$deger=bilgi($detay_anahtar,$ilan_id);
						echo "<div class='eb'><span class='anahtar'>$detay_baslik:</span><span class='deger'>$deger</span></div>";
					}
					?>					
					<div class="il_ilce eb">
						<span><?php echo _LANG('hb_il_ilce'); ?></span>
						<span><?php echo il_getir($ilan_id); ?></span>
						<span><?php echo ilce_getir($ilan_id); ?></span>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php $x++;}  ?>
			
	</div>
</div>