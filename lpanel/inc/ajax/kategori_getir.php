<?php
require("../../../loader.php");

$ref_url=explode("/$admin_dizini",ref_url())[0];
if (site_url()!=$ref_url) {
	die("Hata: Erisim engellendi.");
}

$sinif=$_GET["sinif"];
$tip=$_GET["tip"];

$sql=mysqli_query($baglan,"select * from kategoriler where ust_kategori='0' and tip='$tip' and sinif='$sinif' order by baslik asc");
while ($dizi=mysqli_fetch_array($sql)) {
	$kat_id=$dizi["id"];
	$kat_baslik=$dizi["baslik"];
?>
<div class="checkbox" id="kategori">
  <label><input type="checkbox" name="kategori[]" value="<?php echo $kat_id; ?>"><?php echo $kat_baslik; ?></label>
</div>	
<?php alt_kategori_listele_checbox($kat_id,5,null,$tip,$sinif); ?>								
<?php }


?>