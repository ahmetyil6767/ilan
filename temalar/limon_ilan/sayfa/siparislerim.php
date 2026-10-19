<?php 
if (!$oturum_id) {
	$_SESSION["mesaj"]=array(_LANG('pn_bu_sayfayi_goruntuleyebilmek_icin_uye_girisi_yapmalisiniz'),".");
	yonlendir(ozel_url("giris-yap"));
	exit();
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
			<?php 
			$sql=mysqli_query($baglan,"select * from siparisler where alici_id='$oturum_id'");
			$toplam=mysqli_num_rows($sql);
			if ($toplam>0) {
			?>
			<div class="panel panel-default limon with_footer">
				<div><?php echo _LANG('hb_siparislerim'); ?></div>
				<div class="panel-body" style="padding:0;">
					
					<table class="table table-condensed table-hover profil padding" style="margin-bottom:0;">
						<thead>
							<tr>
								<th><?php echo _LANG("p_baslik"); ?></th>
								<th class="visible-md visible-lg">Tutar</th>
								<th class="visible-md visible-lg">Tarih Saat</th>
								<th class="visible-md visible-lg">Tip</th>
								<th>Durum</th>
								<th><?php echo _LANG("general_options"); ?></th>
							</tr>
						</thead>
						<tbody>
						<?php 
						
						while ($dizi=mysqli_fetch_array($sql)) { 
							$siparis_id=$dizi["id"];
							$siparis_satici_id=$dizi["satici_id"];
							$siparis_tarih_saat=$dizi["tarih_saat"];
							$siparis_tutar=$dizi["tutar"];
							$siparis_tip=$dizi["tip"];
							$siparis_durum=$dizi["durum"];
							$siparis_ilan_id=$dizi["ilan_id"];
							$siparis_magaza_id=$dizi["magaza_id"];
							$siparis_paket_id=$dizi["paket_id"];
							$siparis_paket_baslik=liste_bilgi("paket_adi",$siparis_paket_id);
							$siparis_ilan_baslik=getir("icerik","baslik",$siparis_ilan_id);
							$siparis_alici_onayi=$dizi["alici_onayi"];
						?>
							<tr>
								<td><?php if ($siparis_tip=="magaza") { ?>
								<a href="<?php echo magaza_link_getir($siparis_magaza_id); ?>" target="_blank"><?php echo getir("magazalar","baslik",$siparis_magaza_id); ?></a>
								<?php } else if ($siparis_tip=="paket") {echo "Üyelik paketi siparişi: $siparis_paket_baslik";} else { if ($siparis_ilan_baslik) { ?>
								<a href="<?php echo link_getir($siparis_ilan_id); ?>" target="_blank"><?php echo $siparis_ilan_baslik; ?></a>
								<?php } else {echo "İlan mevcut değil";}
								} 
								?>
								
								<div class="visible-xs visible-sm">
									<?php echo $siparis_tutar; ?> <i class="fa fa-try"></i>
									<?php echo tarih_saat_bicimlendir($siparis_tarih_saat); ?>
									<?php echo $siparis_tip; ?>
								</div>
								
								
								
								</td>
								<td class="fiyat visible-md visible-lg"><?php echo $siparis_tutar; ?> <i class="fa fa-try"></i></td>
								<td class="visible-md visible-lg"><?php echo tarih_saat_bicimlendir($siparis_tarih_saat); ?></td>
								<td class="visible-md visible-lg"><?php echo $siparis_tip; ?></td>
								<td>
								<?php 
								if ($siparis_tip=="doping") {
									if ($siparis_durum==0) {
										echo "<span class='label label-danger borderless'>Bekliyor</span>";
									} else if ($siparis_durum==1) {
										echo "<span class='label label-success borderless'>Onaylandı</span>";
									} 
								} else if ($siparis_tip=="get") {
									if ($siparis_durum==0) {
										echo "<span class='label label-danger borderless'>Ödeme bekleniyor</span>";
									} else if ($siparis_durum==1) {
										echo "<span class='label label-success borderless'>Tamamlandı</span>";
									} else if ($siparis_durum==2) {
										echo "<span class='label label-primary borderless'>Ödeme onaylandı</span>";
									}
								} else if ($siparis_tip=="paket") {
									if ($siparis_durum==0) {
										echo "<span class='label label-danger borderless'>Bekliyor</span>";
									} else if ($siparis_durum==1) {
										echo "<span class='label label-success borderless'>Onaylandı</span>";
									}									
								} else if ($siparis_tip=="magaza") {
									if ($siparis_durum==0) {
										echo "<span class='label label-danger borderless'>Bekliyor</span>";
									} else if ($siparis_durum==1) {
										echo "<span class='label label-success borderless'>Onaylandı</span>";
									}									
								}
								?>
								</td>
								<td>
								<?php if ($siparis_durum==0) { ?>
								<a href="<?php echo ozel_url("siparis")."?siparis_id=$siparis_id"; ?>" class="btn btn-danger btn-xs"><?php echo _LANG("order_pay"); ?></a>
								<?php } else { ?>
								<a href="<?php echo ozel_url("siparis")."?siparis_id=$siparis_id"; ?>" class="btn btn-primary btn-xs">Detay</a>
								<?php } ?>
								</td>
							</tr>
						<?php }  ?>
						</tbody>
					</table>
				</div>
			</div>
			<?php } else { ?>
			<div><?php echo _LANG('hb_siparisiniz_bulunmamaktadir'); ?></div>
			<?php } ?>	
		</div>
	</div>
</div>
<?php tema_footer(); ?>