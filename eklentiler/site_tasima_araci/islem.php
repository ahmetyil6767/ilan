<?php
if(count(get_included_files())==1) {die();}
$site_url=site_url();
@$onay=$_GET["onay"];
@$buton=$_POST["buton"];
@$onay_buton=$_POST["onay_buton"];
$tarih_saat=tarih_saat();

if ($onay=="link") { 
	if ($onay_buton) {
		$eski_url=$_POST["eski_url"];
		$menu_sql=mysqli_query($baglan,"select * from linkler");
		while ($dizi=mysqli_fetch_array($menu_sql)) {
			$link_id=$dizi["id"];
			$menu_url=$dizi["url"];
			$yeni_url=str_replace(array($eski_url),array($site_url),$menu_url);
			guncelle("linkler","url",$yeni_url,$link_id);
		}
		eklenti_ayar_guncelle("link_update",$tarih_saat);
		yonlendir("eklentiler.php?eklenti=site_tasima_araci&sayfa=islem");
	}
}

if ($buton) {
	if ($buton=="gorsel") {
		site_tasima_araci_gorsel();
		eklenti_ayar_guncelle("gorsel_update",$tarih_saat);
		yonlendir("eklentiler.php?eklenti=site_tasima_araci&sayfa=islem");
	} else if ($buton=="menu") {
		$link_url_sec=1;
		yonlendir("eklentiler.php?eklenti=site_tasima_araci&sayfa=islem&onay=link");
	}
}

$ekstra_bilgi_gorsel_sql=mysqli_query($baglan,"select * from ekstra_bilgi where deger like '%uploads/%'");
$ekstra_bilgi_gorsel_sayisi=mysqli_num_rows($ekstra_bilgi_gorsel_sql);
$galeri_sql=mysqli_query($baglan,"select * from galeri");
$galeri_sayisi=mysqli_num_rows($galeri_sql);
$ayarlar_gorsel_sql=mysqli_query($baglan,"select * from ayarlar where deger like '%uploads/%'");
$ayarlar_gorsel_sayisi=mysqli_num_rows($ayarlar_gorsel_sql);
$toplam_gorsel=$ayarlar_gorsel_sayisi+$ekstra_bilgi_gorsel_sayisi+$galeri_sayisi;

$gorsel_problem=0;
while ($eb_dizi=mysqli_fetch_array($ekstra_bilgi_gorsel_sql)) {
	$deger=$eb_dizi["deger"];
	$parcala=explode("uploads/",$deger);
	$onceki_yol=$parcala[0];
	$onceki_yol_son=$parcala[1];
	$yeni_yol="$site_url/uploads/$onceki_yol_son";
	if ($deger!=$yeni_yol) {
		$gorsel_problem++;
	}
}
while ($eb_dizi=mysqli_fetch_array($ayarlar_gorsel_sql)) {
	$deger=$eb_dizi["deger"];
	$parcala=explode("uploads/",$deger);
	$onceki_yol=$parcala[0];
	$onceki_yol_son=$parcala[1];
	$yeni_yol="$site_url/uploads/$onceki_yol_son";
	if ($deger!=$yeni_yol) {
		$gorsel_problem++;
	}
}
while ($eb_dizi=mysqli_fetch_array($galeri_sql)) {
	$deger=$eb_dizi["resim"];
	$parcala=explode("uploads/",$deger);
	$onceki_yol=$parcala[0];
	$onceki_yol_son=$parcala[1];
	$yeni_yol="$site_url/uploads/$onceki_yol_son";
	if ($deger!=$yeni_yol) {
		$gorsel_problem++;
	}
}

$gorseller_update=eklenti_ayar("gorsel_update");
$gorseller_update_tarih=tarih_saat_bicimlendir($gorseller_update);


// Menü link sorunları

$link_problem=0;
$menu_sql=mysqli_query($baglan,"select * from linkler");
while ($dizi=mysqli_fetch_array($menu_sql)) {
	$menu_url=$dizi["url"];
	if (!strstr($menu_url,$site_url) and $menu_url!=$site_url) {
		$link_problem++;
	}
}

$link_update=eklenti_ayar("link_update");
$link_update_tarih=tarih_saat_bicimlendir($link_update);



?>
<div><?php echo _LANG('hb_site_tasima_araci'); ?></div>

<?php 

if ($link_problem==0 and $gorsel_problem==0) {
	echo "<div class='alert alert-success'>Sitenizle ilgili sorun görünmüyor. Bu eklentiyi pasifleştirebilirsiniz.</div>";
}
 
?>

<form action="" method="post">
<?php if ($onay=="link") { 
	$array=array();
	$eski_link_sql=mysqli_query($baglan,"select * from linkler");
	while ($dizi=mysqli_fetch_array($eski_link_sql)) {
		$menu_url=$dizi["url"];
		
		$https_parcala=explode("://",$menu_url);
		$http=$https_parcala[0];
		
		$say=strlen($menu_url);
		$parcala=explode("/",$menu_url);
		$parcala=site_tasima_array_temizle($parcala);
		
		$say=count($parcala);
		$son=null;
		$son.="$http://";
		for ($x=1;$x<=$say;$x++) {
			@$son.=$parcala[$x];
			$parcala_say=count($parcala);
			if (@$parcala[$x]!="") {
				if ($x!=$parcala_say) {
					$son.="/";
				}
				if (!in_array($son,$array)) {
					$array[]=$son;
				}				
			}
		}
	}
	foreach ($array as $x => $arr) {
		if ($x<4) {
		$array[$x]=rtrim($arr,"/");
		} else {
			unset($array[$x]);
		}
	}
	?>
	<div class="panel panel-default">
		<div class="panel-heading"><?php echo _LANG('e_tasma_eskiler'); ?></div>
		<div class="panel-body">
			
			<div class="form-group">
			<label for="yeni_url">Yeni Url</label>
			<input type="text" name="yeni_url" class="form-control" value="<?php echo $site_url; ?>" readonly/>
			</div>
		
			<label>Eski Url</label>
			<?php foreach ($array as $yaz) { ?>
			<div class="radio">
			  <label>
				<input type="radio" name="eski_url" id="eski_url" value="<?php echo $yaz; ?>">
				<?php echo $yaz; ?>
			  </label>
			</div>
			<?php } ?>
			<p class="help-block"><?php echo _LANG('e_tasma_eski_url'); ?></p>
			<?php form_buton("onay_buton","Onayla","primary"); ?>
		</div>
	</div>
<?php } else { ?>
<p><?php echo _LANG('e_tasma_aciklama'); ?></p>
<div class="row">
	<div class="col-md-30">
		<div class="panel panel-default">
			<div class="panel-heading"><?php echo _LANG('e_tasma_resimler'); ?></div>
			<div class="panel-body">
				<p><strong>Durum</strong><?php if ($gorsel_problem>0) {echo "<span class='label label-danger ml15'>$gorsel_problem sorun bulundu</span>";} else {echo "<span class='label label-success ml15'>Sorun bulunamadı</span>";} ?></p>
				<p><strong><?php echo _LANG('e_tasma_tanim'); ?></strong></p>
				<p><?php echo _LANG('hb_icerik_galeri_ve_ayar_gorselleri_veritabanina'); ?></p>
				<p><?php if ($gorseller_update) {echo "Son çalıştırılma: <span class='label label-default'>$gorseller_update_tarih</span>";} else {echo "<span class='text-danger'>Henüz çalıştırılmadı.</span>";} ?></p>
				<button type="submit" name="buton" class="btn btn-primary" value="gorsel"><?php echo _LANG('e_url_duzelt_cozum'); ?></a>
			</div>
		</div>

		<div class="panel panel-default">
			<div class="panel-heading"><?php echo _LANG('e_tasma_dolasm'); ?></div>
			<div class="panel-body">
				<p><strong>Durum</strong><?php if ($link_problem>0) {echo "<span class='label label-danger ml15'>$link_problem sorun bulundu</span>";} else {echo "<span class='label label-success ml15'>Sorun bulunamadı</span>";} ?></p>
				<p><strong><?php echo _LANG('e_tasma_tanim'); ?></strong></p>
				<p><?php echo _LANG('hb_menu_link_url_leri_veritabanina_http_www_sitea'); ?></p>
				<p><?php if ($link_update) {echo "Son çalıştırılma: <span class='label label-default'>$link_update_tarih</span>";} else {echo "<span class='text-danger'>Henüz çalıştırılmadı.</span>";} ?></p>
				<button type="submit" name="buton" class="btn btn-primary" value="menu"><?php echo _LANG('e_url_duzelt_cozum'); ?></a>
			</div>
		</div>
	</div>
</div>
<?php } ?>
</form>
</div>