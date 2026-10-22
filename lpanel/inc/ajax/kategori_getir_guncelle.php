<?php
require("../../../loader.php");

/*
 * Panel erisim kontrolu oturum tabanlidir.
 * Onceki surum Referer basligina bagliydi; tarayici veya proxy
 * Referer'i gondermezse istek reddediliyordu. loader.php zaten
 * /lpanel/ altinda yonetici oturumu zorunlu kiliyor; buradaki
 * kontrol ikinci savunma katmanidir.
 */
if (!isset($_SESSION["yonetici_id"]) or !yonetici_mi($_SESSION["yonetici_id"])) {
	die(_LANG('p_erisim_engellendi'));
}

$sinif=isset($_GET["sinif"]) ? $_GET["sinif"] : "";
$tip=isset($_GET["tip"]) ? $_GET["tip"] : "";
$id=(int)($_GET["id"] ?? 0);

$sql=mysqli_prepare($baglan,"select * from kategoriler where ust_kategori=0 and tip=? and sinif=? order by baslik asc");
mysqli_stmt_bind_param($sql,"ss",$tip,$sinif);
mysqli_stmt_execute($sql);
$sonuc=mysqli_stmt_get_result($sql);
$mevcut_kategoriler=bilgi_getir("icerik","kategori",$id);
while ($dizi=mysqli_fetch_array($sonuc)) {
	$kat_id=$dizi["id"];
	$kat_baslik=$dizi["baslik"];
if (strstr($mevcut_kategoriler,"(".$kat_id.")")) {$checked="checked='checked'";} else {$checked="";}
?>
<div class="checkbox" id="kategori">
  <label><input type="checkbox" name="kategori[]" value="<?php echo $kat_id; ?>" <?php echo $checked; ?>><?php echo $kat_baslik; ?></label>
</div>	
<?php alt_kategori_listele_checbox($kat_id,5,$mevcut_kategoriler,$tip,$sinif); ?>								
<?php } ?>