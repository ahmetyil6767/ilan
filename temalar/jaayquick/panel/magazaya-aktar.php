<?php 
if(count(get_included_files())==1) {die();}
$id=$_GET["id"];
$magaza_id=$_GET["magaza_id"];
$magaza_sil=$_GET["magaza_sil"];
$uye_id=getir("icerik","yazar",$id);
if ($id) {
	$mevcut_magaza=bilgi("magaza",$id);
}
if ($id and $magaza_id) {
	bilgi_guncelle("magaza",$magaza_id,$id);
	$baslik=getir("icerik","baslik",$id);
	$magaza_baslik=getir("magazalar","baslik",$magaza_id);
	$_SESSION["mesaj"]=array(sprintf(_LANG('msj_magazaya_aktarildi'), $baslik, $magaza_baslik),"success");
	yonlendir(lpanel_uye_profil_url($uye_id));
}
if ($magaza_sil) {
	bilgi_guncelle("magaza",null,$id);
	$baslik=getir("icerik","baslik",$id);
	$_SESSION["mesaj"]=array(sprintf(_LANG('msj_uyeler_arasi_aktarildi'), $baslik),"success");
	yonlendir(lpanel_uye_profil_url($uye_id));	
}
?>
<h3 class="pull-left bolum-baslik text-uppercase"><?php echo _LANG("e_magazaya_aktar"); ?></h3>
<div class="clearfix mb20"></div>

<div class="table-responsive">
	<table id="datatable" class="table table-bordered table-hover table-ozel" data-order='[[ 0, "desc" ]]' data-page-length='15'>
	
		<tbody>
			<tr>
				<th width="200"><?php echo _LANG("pn_uye_ad_soyad"); ?></th>
				<td><a href="<?php echo profil_link_getir($uye_id); ?>" target="_blank"><?php echo getir("uyeler","ad_soyad",$uye_id)." (ID: $uye_id)"; ?></a></td>
			</tr>	
			<tr>
				<th><?php echo _LANG("listing_title"); ?></th>
				<td><a href="<?php echo link_getir($id); ?>" target="_blank"><?php echo getir("icerik","baslik",$id)." (ID: $id)"; ?></a></td>
			</tr>	
			<tr>
				<th><?php echo _LANG("pn_mevcut_magaza"); ?></th>
				<td><a href="<?php echo magaza_link_getir($mevcut_magaza); ?>" target="_blank"><?php echo getir("magazalar","baslik",$mevcut_magaza)." (ID: $mevcut_magaza)"; ?></a></td>
			</tr>			
			<tr>
				<th><?php echo _LANG('hb_aktif_magaza_sayisi'); ?></th>
				<td><?php echo mysqli_fetch_array(mysqli_query($baglan,"select COUNT(id) from magazalar where durum='1' and uye_id='$uye_id'"))[0]; ?></td>
			</tr>				
		</tbody>
	</table>
</div>
<?php 
$uye_id=getir("icerik","yazar",$id);
$aktif_magaza=mysqli_query($baglan,"select * from magazalar where uye_id='$uye_id' and durum='1'");
$aktif_magaza_sayisi=mysqli_num_rows($aktif_magaza);
if ($aktif_magaza_sayisi>0) {
	while ($aktif_magaza_dizi=mysqli_fetch_array($aktif_magaza)) {
		$a_m_id=$aktif_magaza_dizi["id"];
		$a_m_baslik=$aktif_magaza_dizi["baslik"];
		?>
<div class="panel panel-default">
	<div class="panel-heading"><?php echo $a_m_baslik; ?></div>
	<div class="panel-body">
		<p><?php echo _LANG('s_bu_ilani'); ?> <?php echo $a_m_baslik; ?> adlı mağazaya aktarmak için butona tıklayın.</p>
		<a class="btn btn-primary" href="temalar.php?tema=<?php echo $aktif_tema; ?>&sayfa=panel&islem=magazaya-aktar&id=<?php echo $id; ?>&magaza_id=<?php echo $a_m_id; ?>"><?php echo _LANG("pn_ilani_bu_magazaya_aktar"); ?></a>	
	</div>
</div>
				<?php
			}
		}
		if ($mevcut_magaza) { ?>
			
					
		<div class="panel panel-default">
			<div><?php echo _LANG('hb_uye_hesabina_aktar'); ?></div>
			<div class="panel-body">
				<p><?php echo _LANG('hb_bu_ilan_suan_bir_magazaya_tanimli_ilani_uye_pr'); ?></p>
				<a class="btn btn-primary" href="temalar.php?tema=<?php echo $aktif_tema; ?>&sayfa=panel&islem=magazaya-aktar&id=<?php echo $id; ?>&magaza_sil=1"><?php echo _LANG("pn_ilani_uye_profiline_aktar"); ?></a>	
			</div>
		</div>			
			
			
		<?php }
		
		
		
		
		?>

