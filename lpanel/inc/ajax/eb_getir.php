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

$id=(int)($_GET["id"] ?? 0);
$tip=isset($_GET["tip"]) ? $_GET["tip"] : "";
$sql=mysqli_prepare($baglan,"select * from ekstra_bilgi where id=? and tip=?");
mysqli_stmt_bind_param($sql,"is",$id,$tip);
mysqli_stmt_execute($sql);
$sonuc=mysqli_stmt_get_result($sql);
while ($dizi=mysqli_fetch_array($sonuc)) {
$eb_id=$dizi["b_id"];
$anahtar=$dizi["anahtar"];
$deger=$dizi["deger"];
?>
<tr id="eb_<?php echo htmlspecialchars($eb_id, ENT_QUOTES, 'UTF-8'); ?>"> 
	<td><input type="text" id="anahtar_<?php echo htmlspecialchars($eb_id, ENT_QUOTES, 'UTF-8'); ?>" value="<?php echo htmlspecialchars($anahtar, ENT_QUOTES, 'UTF-8'); ?>" class="form-control input-sm" /></td>
	<td><input type="text" id="deger_<?php echo htmlspecialchars($eb_id, ENT_QUOTES, 'UTF-8'); ?>" value="<?php echo htmlspecialchars($deger, ENT_QUOTES, 'UTF-8'); ?>" class="form-control input-sm" /></td>
	<td><button id="eb_sil_buton" type="button" class="btn btn-xs btn-danger" value="<?php echo htmlspecialchars($eb_id, ENT_QUOTES, 'UTF-8'); ?>"><?php echo _LANG('general_delete'); ?></button>
	<button id="eb_guncelle_buton" type="button" class="btn btn-xs btn-success" value="<?php echo htmlspecialchars($eb_id, ENT_QUOTES, 'UTF-8'); ?>"><?php echo _LANG("general_update"); ?></button>
	<div id="sonuc" style="margin-top:3px;"></div>
	</td>
</tr>
<?php } ?>