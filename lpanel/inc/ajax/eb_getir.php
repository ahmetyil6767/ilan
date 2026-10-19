<?php
require("../../../loader.php");

$ref_url=explode("/$admin_dizini",ref_url())[0];
if (site_url()!=$ref_url) {
	die("Hata: Erisim engellendi.");
}

$id=$_GET["id"];
$tip=$_GET["tip"];
$sql=mysqli_query($baglan,"select * from ekstra_bilgi where id='$id' and tip='$tip'");
while ($dizi=mysqli_fetch_array($sql,MYSQLI_ASSOC)) {
$eb_id=$dizi["b_id"];
$anahtar=$dizi["anahtar"];
$deger=$dizi["deger"];
?>
<tr id="eb_<?php echo $eb_id; ?>"> 
	<td><input type="text" id="anahtar_<?php echo $eb_id; ?>" value="<?php echo $anahtar; ?>" class="form-control input-sm" /></td>
	<td><input type="text" id="deger_<?php echo $eb_id; ?>" value="<?php echo $deger; ?>" class="form-control input-sm" /></td>
	<td><button id="eb_sil_buton" type="button" class="btn btn-xs btn-danger" value="<?php echo $eb_id; ?>">Sil</button>
	<button id="eb_guncelle_buton" type="button" class="btn btn-xs btn-success" value="<?php echo $eb_id; ?>"><?php echo _LANG("general_update"); ?></button>
	<div id="sonuc" style="margin-top:3px;"></div>
	</td>
</tr>
<?php } ?>