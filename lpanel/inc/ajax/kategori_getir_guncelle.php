<?php
require("../../../loader.php");

$ref_url=explode("/$admin_dizini",ref_url())[0];
if (site_url()!=$ref_url) {
	die("Hata: Erisim engellendi.");
}

$sinif=$_GET["sinif"];
$tip=$_GET["tip"];
$id=$_GET["id"];

$sql=mysqli_query($baglan,"select * from kategoriler where ust_kategori='0' and tip='$tip' and sinif='$sinif' order by baslik asc");
$mevcut_kategoriler=bilgi_getir("icerik","kategori",$id);
while ($dizi=mysqli_fetch_array($sql)) {
	$kat_id=$dizi["id"];
	$kat_baslik=$dizi["baslik"];
if (strstr($mevcut_kategoriler,"(".$kat_id.")")) {$checked="checked='checked'";} else {$checked="";}
?>
<div class="checkbox" id="kategori">
  <label><input type="checkbox" name="kategori[]" value="<?php echo $kat_id; ?>" <?php echo $checked; ?>><?php echo $kat_baslik; ?></label>
</div>	
<?php alt_kategori_listele_checbox($kat_id,5,$mevcut_kategoriler,$tip,$sinif); ?>								
<?php } ?>