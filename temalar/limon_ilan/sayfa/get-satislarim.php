<?php 
if(count(get_included_files())==1) {die();}
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
			
			<div class="panel panel-default limon with_footer">
				<div><?php echo _LANG('hb_get_satislarim'); ?></div>
				<div class="panel-body" style="padding:0;">
				
					<table class="table table-condensed table-hover profil padding" style="margin-bottom:0;">
						<thead>
							<tr>
								<th width="220"><?php echo _LANG("search_found_ads"); ?></th>
								<th>Tutar</th>
								<th><?php echo _LANG("s_net_kazanc"); ?></th>
								<th><?php echo _LANG("s_alici"); ?></th>
								<th>Tarih Saat</th>
								<th>Durum</th>
								<th><?php echo _LANG("s_satici_odemesi"); ?></th>
								<th><?php echo _LANG("general_options"); ?></th>
							</tr>
						</thead>
						<tbody>
						<?php 
						$sql=mysqli_query($baglan,"select * from siparisler where satici_id='$oturum_id' and tip='get' order by tarih_saat desc");
						while ($dizi=mysqli_fetch_array($sql)) { 
							$siparis_id=$dizi["id"];
							$siparis_alici_id=$dizi["alici_id"];
							$siparis_satici_id=$dizi["satici_id"];
							$siparis_tarih_saat=$dizi["tarih_saat"];
							$siparis_tutar=$dizi["tutar"];
							$siparis_durum=$dizi["durum"];
							$siparis_ilan_id=$dizi["ilan_id"];
							$siparis_satici_odeme=$dizi["satici_odeme"];
							
							$get_komisyon=tema_ayar("get_komisyon");
							$kazanc_hesapla=$siparis_tutar-$siparis_tutar/100*$get_komisyon;
							
						?>
							<tr>
								<td><a href="<?php echo link_getir($siparis_ilan_id); ?>" target="_blank"><?php echo getir("icerik","baslik",$siparis_ilan_id); ?></a></td>
								<td class="fiyat"><?php echo $siparis_tutar; ?> GMD</td>
								<td class="fiyat"><?php echo $kazanc_hesapla; ?> GMD</td>
								<td><a href="<?php echo uye_link_getir($siparis_alici_id); ?>" target="_blank"><?php echo getir("uyeler","ad_soyad",$siparis_alici_id); ?></td>
								<td><?php echo tarih_saat_bicimlendir($siparis_tarih_saat); ?></td>
								<td>
								<?php if ($siparis_durum==0) {
									echo "<span class='label label-danger borderless'><?php echo _LANG('order_pending'); ?></span>";
								} else if ($siparis_durum==1) {
									echo "<span class='label label-success borderless'><?php echo _LANG('order_completed'); ?></span>";
								} else if ($siparis_durum==2) {
									echo "<span class='label label-info borderless'><?php echo _LANG('s_odeme_onaylandi'); ?></span>";
								} ?></td>
								<td>
								<?php if ($siparis_satici_odeme==1) {
									$odeme_tarihi=ekstra_bilgi_getir("odeme_tarihi",$siparis_id,"siparis");
									echo "<span class='label label-success borderless' title='$odeme_tarihi'><?php echo _LANG('order_paid'); ?></span>";
								} else {echo "<span class='label label-info borderless'>Bekliyor</span>";} ?>
								</td>
								<td>
								<div class="btn-group" role="group" aria-label="...">
								  <a href="<?php echo ozel_url("siparis")."?siparis_id=$siparis_id"; ?>" class="btn btn-primary btn-xs">Özet</a>
								  
								  <div class="btn-group" role="group">
									<button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="caret ml0"></span></button>
									<ul class="dropdown-menu dropdown-menu-right">
									  <li><a href="<?php echo ozel_url("siparis")."?siparis_id=$siparis_id"; ?>"><?php echo _LANG("s_durum_guncelle"); ?></a></li>
									</ul>
								  </div>
								</div>
								</td>
							</tr>
						<?php }  ?>
						</tbody>
					</table>
				</div>
			</div>
		
		</div>
	</div>
</div>
<?php tema_footer(); ?>