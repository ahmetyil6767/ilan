<?php 
if(count(get_included_files())==1) {die();}
if (!$oturum_id) {
	$_SESSION["mesaj"]=array(_LANG('pn_bu_sayfayi_goruntuleyebilmek_icin_uye_girisi_yapmalisiniz'),".");
	yonlendir(ozel_url("giris-yap"));
	exit();
}
$kat_detaylar=array();
$fav_temizle=favori_icerikler($oturum_id);
$fav_dizi=explode(",",$fav_temizle);
foreach ($fav_dizi as $fav) {
	$icerik_baslik=getir("icerik","baslik",$fav);
	if (!$icerik_baslik) {
		favori_sil($fav,$oturum_id);
	}
}
$favori_ilan=favori_sayisi($oturum_id);
if ($favori_ilan>0) {
	$favori_icerikler=favori_icerikler($oturum_id);
	@$sayfa=$_GET["sayfa"];
	if (!$sayfa) {$sayfa=1;}
	$listeleme_adedi=tema_ayar("listeleme_adedi");
	$baslangic=($sayfa-1)*$listeleme_adedi;
	$kac_sayfa=ceil($favori_ilan/$listeleme_adedi);
	$sql=mysqli_query($baglan,"select * from icerik where id in ($favori_icerikler) limit $baslangic, $listeleme_adedi");
	$toplam_ilan=mysqli_num_rows($sql);	
}
tema_header(); ?>
<div class="container profil arsiv mt20">
	<?php include("temalar/$aktif_tema/inc/uye-ust.php"); ?>
	
	<div class="row">
		<div class="col-md-9 sidebar">
			<?php include("temalar/$aktif_tema/inc/uye-sol-menu.php"); ?>
		</div>
		<div class="col-md-33">
			<?php include("temalar/$aktif_tema/inc/alert.php"); ?>

			<?php if ($favori_ilan>0) { ?>

			<div class="panel panel-default beyaz tab">
				<div class="panel-heading visible-sm visible-md visible-lg">
				  <ul class="nav nav-tabs ozel" role="tablist">
					<li role="presentation" class="active"><a href="#tumu" aria-controls="tumu" role="tab" data-toggle="tab"><?php echo _LANG("s_favori_ilanlar"); ?></a></li>
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
				<div class="panel-body">
					<table class="table table-condensed table-striped liste">
						<thead>
							<tr>
								<th width="110"></th>
								<th class="baslik_th" width="280"><?php echo _LANG("listing_title"); ?></th>
								<th class="visible-sm visible-md visible-lg"><?php echo _LANG('listing_price'); ?></th>
								<th class="visible-md visible-lg"><?php echo _LANG("listing_date"); ?></th>
								<th class="visible-sm visible-md visible-lg"><?php echo _LANG('listing_location'); ?></th>
								<th><?php echo _LANG("general_options"); ?></th>
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
							$ilan_ziyaret=postviews($ilan_id);
							$ilan_favori=kac_kisinin_favorisi($ilan_id);							
						?>
						<tr class="<?php echo @$class_yaz; ?> noselect" id="favori_<?php echo $ilan_id; ?>">
							<td><a href="<?php echo $ilan_link; ?>" target="_blank"><img src="<?php echo $ilan_resim; ?>" alt="<?php echo $ilan_baslik; ?>" class="resim img-responsive"/></a></td>
							<td class="baslik_td">
								<span class="baslik"><a href="<?php echo $ilan_link; ?>" target="_blank"><?php echo $ilan_baslik; ?></a></span>
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
											echo "<span class='s_mesaj_tablo'><?php echo _LANG('ilan_sahiplendirme'); ?></span>";
										}
										if ($gorusulur==1) {
											echo "<span class='s_mesaj_tablo'><?php echo _LANG('ilan_gorusulur'); ?></span>";
										}
									}
									?>
									</div>
									<div class="il_ilce">
										<span><?php echo il_getir($ilan_id); ?></span>
										<span><?php echo ilce_getir($ilan_id); ?></span>
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
								if ($para_birimi!="gmd") {
									$fiyat_gmd=fiyat_donustur(bilgi("fiyat",$ilan_id));
									if ($fiyat_gmd) {
										echo "<div class='clearfix mb5'></div><div class='tablo_tl'><i class='ion-arrow-swap'></i>$fiyat_gmd GMD</div>";
									}
								}
							} else {
								if ($sahiplendirme==1) {
									echo "<span class='s_mesaj_tablo'><?php echo _LANG('ilan_sahiplendirme'); ?></span>";
								}
								if ($gorusulur==1) {
									echo "<span class='s_mesaj_tablo'><?php echo _LANG('ilan_gorusulur'); ?></span>";
								}
							}
							?>
							</td>
							<td class="visible-md visible-lg"><?php echo $ilan_tarih; ?></td>
							<td class="visible-sm visible-md visible-lg">
								<div class="il_ilce">
									<span><?php echo il_getir($ilan_id); ?></span>
									<span><?php echo ilce_getir($ilan_id); ?></span>
								</div>
							</td>
							<td class="nolink">
								<button type="button" value="<?php echo $ilan_id; ?>" class="favorilerden_kaldir btn btn-danger btn-xs"><i class="fa fa-trash"></i><?php echo _LANG('general_delete'); ?></button>
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
				$sayfa_url=ozel_url("favori-ilanlarim");
				if ($sayfa>1) {
					$onceki_sayfa=$sayfa-1;
					echo "<li><a href='$sayfa_url?sayfa=$onceki_sayfa' aria-label='<?php echo _LANG('f_onceki'); ?>'><i class='ion-chevron-left'></i></a></li>";
				}		
				for ($x=1;$x<=$kac_sayfa;$x++) {
					if ($x==$sayfa) {$active="class='active'";$link_yaz="";} else {$active="";$link_yaz="href='$sayfa_url?sayfa=$x'";}
					echo "<li $active><a $link_yaz>$x</a></li>";
				}
				if ($sayfa<$kac_sayfa) {
					$sonraki_sayfa=$sayfa+1; 
					echo "<li><a href='$sayfa_url?sayfa=$sonraki_sayfa' aria-label='<?php echo _LANG('f_sonraki'); ?>'><i class='ion-chevron-right'></i></a></li>";
				}
				echo "</ul></div>";
			}
			?>

			
			<?php } else { ?>
			<div><?php echo _LANG('hb_hic_favori_ilaniniz_yok'); ?></div>
			<?php } ?>
		
		</div>
	</div>
</div>

<?php tema_footer(); ?>
<script type="text/javascript">
$(function(){
	$("body").on("click",".favorilerden_kaldir",function(){
		var ilan_id=$(this).val();
		$.ajax({
			type:"post",
			url:"<?php echo tema_dizini(); ?>/ajax.php?islem=favorilerden_kaldir",
			data:{'id':ilan_id},
			success:function(cevap){
				$("#favori_"+ilan_id).fadeOut(500);
			}
		});
	});	
});
</script>