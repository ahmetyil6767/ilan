<?php 
if(count(get_included_files())==1) {die();}
$id=$_GET["id"];

?>
<h3 class="pull-left bolum-baslik text-uppercase"><?php echo getir("uyeler","ad_soyad",$id); ?></h3>
<a href="uyeler.php?islem=guncelle&id=<?php echo $id; ?>" class="btn btn-primary">Güncelle</a>
<a href="uyeler.php?islem=oturum_degistir&id=<?php echo $id; ?>" class="btn btn-info" onclick="return confirm('<?php echo getir("uyeler","ad_soyad",$id); ?> olarak oturum açmak istediğinizden emin misiniz?');"><i class="fa fa-exchange"></i><?php echo getir("uyeler","ad_soyad",$id); ?> <?php echo _LANG('e_olarak_giris'); ?></a>
<div class="clearfix mb20"></div>

<div class="row">
	<div class="col-md-40">

	<div class="panel panel-default">
		<div class="panel-heading"><?php echo _LANG('e_tum_ilanlari'); ?></div>
		
		<div class="table-responsive">
		<table class="table table-default table-condensed">
			<thead>
				<tr>
					<th width="10">#</th>
					<th><?php echo _LANG('p_baslik'); ?></th>
					<th><?php echo _LANG('p_magaza'); ?></th>
					<th>Fiyat</th>
					<th>Tarih Saat</th>
					<th>Durum</th>
					<th><?php echo _LANG('p_islemler'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php 
				$ilan_sql=mysqli_query($baglan,"select * from icerik where tip='ilan' and yazar='$id'");
				$toplam_ilan=mysqli_num_rows($ilan_sql);
				$aktif_magaza_sayisi=mysqli_fetch_array(mysqli_query($baglan,"select COUNT(id) from magazalar where uye_id='$id' and durum='1'"))[0];
				$ilan_idler=null;
				while ($ilan_dizi=mysqli_fetch_array($ilan_sql)) {
				$ilan_id=$ilan_dizi["id"];
				$ilan_baslik=$ilan_dizi["baslik"];
				$ilan_tarih_saat=$ilan_dizi["tarih_saat"];
				$ilan_link=link_getir($ilan_id);
				$ilan_durum=$ilan_dizi["durum"];
				$ilan_idler.=$ilan_id.",";
				?>
				<tr>
					<th><?php echo $ilan_id; ?></th>
					<td><a href="icerik.php?islem=guncelle&tip=ilan&id=<?php echo $ilan_id; ?>" target="_blank"><?php echo $ilan_baslik; ?></a> <a href="<?php echo $ilan_link; ?>" target="_blank"><i class="fa fa-external-link"></i></a></td>
					<td><?php echo bilgi("magaza",$ilan_id); ?></td>
					<td><?php echo fiyat($ilan_id); ?></td>
					<td><?php echo $ilan_tarih_saat; ?></td>
					<td><?php if ($ilan_durum==0) {echo "<span class='label label-danger'>Taslak</span>";} else if ($ilan_durum==1) {echo "<span class='label label-success'>Yayında</span>";} else if ($ilan_durum==2) {echo "<span class='label label-info'>Onay Bekliyor</span>";} else if ($ilan_durum==3) {echo "<span class='label label-warning'>Yayından kaldırılmış</span>";} else if ($ilan_durum==4) {echo "<span class='label label-danger'>Süresi dolmuş</span>";} ?></td>
					<td><a href="icerik.php?tip=ilan&islem=guncelle&id=<?php echo $ilan_id; ?>">Güncelle</a>
					<?php if ($aktif_magaza_sayisi>0) { ?>
						<a href="temalar.php?tema=limon_ilan&sayfa=panel&islem=magazaya-aktar&id=<?php echo $ilan_id; ?>" target="_blank">Mağazaya aktar</a>
					<?php } ?>
					</td>
				</tr>
				<?php } ?>
			</tbody>
		</table>
		</div>	
		<div class="panel-footer">
		Toplam <?php echo $toplam_ilan; ?> ilan bulundu.
		</div>
	</div>
	
	<div class="panel panel-default">
		<div class="panel-heading"><?php echo _LANG('e_giris_kayitlari'); ?></div>
		
		<div class="table-responsive">
		<table class="table table-default table-condensed">
			<thead>
				<tr>
					<th>IP Adresi</th>
					<th>Tarih Saat</th>
				</tr>
			</thead>
			<tbody>
				<?php 
				$ip_sql=mysqli_query($baglan,"select * from kayitlar where tip='ip_kayit' and uye_id='$id' order by tarih_saat desc");
				$toplam_giris=mysqli_num_rows($ip_sql);
				while ($ip_dizi=mysqli_fetch_array($ip_sql)) {
				$ip_adresi=$ip_dizi["veri_1"];
				$ip_tarih_saat=$ip_dizi["tarih_saat"];
				?>
				<tr>
					<th><?php echo $ip_adresi; ?></th>
					<td><?php echo $ip_tarih_saat; ?></td>
				</tr>
				<?php } ?>
			</tbody>
		</table>
		</div>	
		<div class="panel-footer">
		Toplam <?php echo $toplam_giris; ?> kayıt bulundu.
		</div>
	</div>
	
	</div>
	<div class="col-md-20">


        <div class="panel panel-default">
            <div class="panel-heading"><?php echo _LANG('e_uye_bilgileri'); ?></div>

            <div class="table-responsive">
            <table class="table table-default table-condensed">
                <thead>
                    <tr>
                        <th>Anahtar</th>
                        <th><?php echo _LANG('p_deger'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                       <th>Ad Soyad</th>
                       <td><?php echo getir("uyeler","ad_soyad",$id); ?></td>
                    </tr>
                    <tr>
                       <th>Email Adresi</th>
                       <td><?php echo getir("uyeler","email_adresi",$id); ?></td>
                    </tr>
                    <tr>
                       <th>Yetki</th>
                       <td><?php echo getir("uyeler","yetki",$id); ?></td>
                    </tr>
                    <tr>
                       <th><?php echo _LANG('e_uyelik_tarihi'); ?></th>
                       <td><?php echo getir("uyeler","tarih_saat",$id); ?></td>
                    </tr>
                    <?php 
                    $ekstra_bilgi_sql=mysqli_query($baglan,"select * from ekstra_bilgi where tip='uye' and id='$id'");
                    $ekstra_bilgi_say=mysqli_num_rows($ekstra_bilgi_sql);
                    $limit_politikasi=limit_politikasi();
                    while ($ekstra_bilgi_dizi=mysqli_fetch_array($ekstra_bilgi_sql)) {
                    $eb_anahtar=$ekstra_bilgi_dizi["anahtar"];
                    $eb_deger=$ekstra_bilgi_dizi["deger"];
                    
                    ?>
                    <tr>
                        <th><?php echo $eb_anahtar; ?></th>
                        <td><?php echo $eb_deger; ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
            </div>
        </div>


		<div class="panel panel-default">
			<div class="panel-heading">Dopingleri</div>
			
			<div class="table-responsive">
			<table class="table table-default table-condensed">
				<thead>
					<tr>
						<th width="10">#</th>
						<th><?php echo _LANG('p_ilan'); ?></th>
						<th>Doping</th>
						<th>Tarih Saat</th>
						<th><?php echo _LANG('e_bitis_tarih_saat'); ?></th>
						<th>Durum</th>
					</tr>
				</thead>
				<tbody>
					<?php 
					@$ilan_idler=rtrim($ilan_idler,",");
					if (isset($ilan_idler) and $toplam_ilan>0) {
					$sql=mysqli_query($baglan,"select * from dopingler where ilan_id in ($ilan_idler)");
					while ($dizi=mysqli_fetch_array($sql)) {
					$doping_id=$dizi["id"];
					$doping=$dizi["doping"];
					$ilan_id=$dizi["ilan_id"];
					$durum=$dizi["durum"];
					$doping_tarih_saat=$dizi["tarih_saat"];
					$doping_bitis_tarih_saat=$dizi["bitis_tarih_saat"];
					$magaza_id=isset($magaza_id)?$magaza_id:0;
$magaza_link=$magaza_id?magaza_link_getir($magaza_id):"";
					?>
					<tr>
						<th><?php echo $doping_id; ?></th>
						<td><a href="<?php echo link_getir($ilan_id); ?>" target="_blank"><?php echo getir("icerik","baslik",$ilan_id); ?></a></td>
						<td><?php echo $doping; ?></td>
						<td><?php echo $doping_tarih_saat; ?></td>
						<td><?php echo $doping_bitis_tarih_saat; ?></td>
						<td><?php if ($durum==0) {echo "<span class='label label-danger'>Pasif</span>";} else if ($durum==1) {echo "<span class='label label-success'>Aktif</span>";} ?></td>
					</tr>
					<?php } ?>
					<?php } ?>
				</tbody>
			</table>
			</div>
		</div>	

		
		<div class="panel panel-default">
			<div class="panel-heading"><?php echo _LANG('e_tum_magazalari'); ?></div>
			
			<div class="table-responsive">
			<table class="table table-default table-condensed">
				<thead>
					<tr>
						<th width="10">#</th>
						<th><?php echo _LANG('p_baslik'); ?></th>
						<th>Tarih Saat</th>
						<th>Durum</th>
						<th><?php echo _LANG('p_islemler'); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php 
					$magaza_sql=mysqli_query($baglan,"select * from magazalar where uye_id='$id'");
					while ($dizi=mysqli_fetch_array($magaza_sql)) {
					$magaza_id=$dizi["id"];
					$baslik=$dizi["baslik"];
					$magaza_durum=$dizi["durum"];
					$magaza_tarih_saat=$dizi["tarih_saat"];
					$magaza_id=isset($magaza_id)?$magaza_id:0;
$magaza_link=$magaza_id?magaza_link_getir($magaza_id):"";
					?>
					<tr>
						<th><?php echo $magaza_id; ?></th>
						<td><a href="<?php echo $magaza_link; ?>" target="_blank"><?php echo $baslik; ?></a></td>
						<td><?php echo $magaza_tarih_saat; ?></td>
						<td><?php if ($magaza_durum==0) {echo "<span class='label label-danger'>Onay Bekliyor</span>";} else if ($magaza_durum==1) {echo "<span class='label label-success'>Onaylandı</span>";} ?></td>
						<td><a href="temalar.php?tema=limon_ilan&sayfa=panel&islem=magaza-guncelle&id=<?php echo $magaza_id; ?>">Güncelle</a></td>
					</tr>
					<?php } ?>
				</tbody>
			</table>
			</div>	
		
		</div>	
	
	
		<div class="panel panel-default">
			<div class="panel-heading"><?php echo _LANG('e_tum_siparisleri'); ?></div>
			
			<div class="table-responsive">
			<table class="table table-default table-condensed">
				<thead>
					<tr>
						<th width="10">#</th>
						<th><?php echo _LANG('e_satici_id'); ?></th>
						<th>Tutar</th>
						<th>Tip</th>
						<th>Tarih Saat</th>
						<th>Durum</th>
						<th><?php echo _LANG('p_islemler'); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php 
					$sql=mysqli_query($baglan,"select * from siparisler where alici_id='$id'");
					while ($dizi=mysqli_fetch_array($sql)) {
					$siparis_id=$dizi["id"];
					$satici_id=$dizi["satici_id"];
					$tip=$dizi["tip"];
					$tutar=$dizi["tutar"];
					$tarih_saat=$dizi["tarih_saat"];
					$durum=$dizi["durum"];
					$magaza_id=isset($magaza_id)?$magaza_id:0;
$magaza_link=$magaza_id?magaza_link_getir($magaza_id):"";
					?>
					<tr>
						<th><?php echo $siparis_id; ?></th>
						<td><?php echo $satici_id; ?></td>
						<td><?php echo $tutar; ?> GMD</td>
						<td><?php echo $tip; ?></td>
						<td><?php echo $tarih_saat; ?></td>
						<td><?php echo $durum; ?></td>
						<td><a href="temalar.php?tema=limon_ilan&sayfa=panel&islem=magaza-guncelle&id=<?php echo $magaza_id; ?>">Güncelle</a></td>
					</tr>
					<?php } ?>
				</tbody>
			</table>
			</div>	
		</div>	


		<div class="panel panel-default">
			<div class="panel-heading"><?php echo _LANG('e_tum_odemeleri'); ?></div>
			
			<div class="table-responsive">
			<table class="table table-default table-condensed">
				<thead>
					<tr>
						<th width="10">#</th>
						<th><?php echo _LANG('e_gonderen'); ?></th>
						<th>Hesap</th>
						<th>Tarih Saat</th>
					</tr>
				</thead>
				<tbody>
					<?php 
					$sql=mysqli_query($baglan,"select * from odemeler where uye_id='$id'");
					while ($dizi=mysqli_fetch_array($sql)) {
					$odeme_id=$dizi["id"];
					$ad_soyad=$dizi["ad_soyad"];
					$hesap=$dizi["hesap"];
					$tarih_saat=$dizi["tarih_saat"];
					?>
					<tr>
						<th><?php echo $odeme_id; ?></th>
						<td><?php echo $ad_soyad; ?></td>
						<td><?php echo $hesap; ?></td>
						<td><?php echo $tarih_saat; ?></td>
					</tr>
					<?php } ?>
				</tbody>
			</table>
			</div>	

		</div>


		
	</div>
</div>

