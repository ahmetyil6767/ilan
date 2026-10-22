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
$ust_kategori=(int)($_GET["ust_kategori"] ?? 0);

if (!$ust_kategori) {
	$ust_kategori=0;
}
$mevcut_kategoriler=bilgi_getir("icerik","kategori",$id);
if ($mevcut_kategoriler) {
	$tum_kategoriler=kategori_getir($id,$sinif);
}
$sql=mysqli_prepare($baglan,"select * from kategoriler where ust_kategori=? and tip=? and sinif=?");
mysqli_stmt_bind_param($sql,"iss",$ust_kategori,$tip,$sinif);
mysqli_stmt_execute($sql);
$sonuc=mysqli_stmt_get_result($sql);
$toplam=mysqli_num_rows($sonuc);

if ($toplam>0) {
?>
<div class="form-group">
	<select id="<?php echo $sinif; ?>_tip_2" name="<?php echo $sinif; ?>[]" class="form-control">
		<option><?php echo _LANG("filter_select"); ?></option>
		<?php 
		while ($dizi=mysqli_fetch_array($sonuc)) {
			$kat_id=$dizi["id"];
			$kat_baslik=$dizi["baslik"];
			if (strstr($mevcut_kategoriler,"(".$kat_id.")")) {$selected="selected='selected'";} else {$selected="";}
		?>
		<option value="<?php echo $kat_id; ?>" <?php echo $selected; ?>><?php echo e($kat_baslik); ?></option>
		<?php } ?>
	</select>
</div>
<?php } else {echo "<div class='alert alert-success'>" . _LANG('p_kategori_secimi_tamamlandi') . "</div>";} ?>
<div id="<?php echo $sinif; ?>-alt"></div>

<script type="text/javascript">
$(function(){
	$("body").on("change","#<?php echo $sinif; ?>_tip_2",function(){
		deger=$(this).val();
		if (deger!=<?php echo json_encode(_LANG("filter_select")); ?>) {
			$(this).attr("disabled","disabled");
			$("#<?php echo $sinif; ?>-alt").append("<div id='alt_kategori_"+deger+"'></div>");
			$("#alt_kategori_"+deger).load("inc/ajax/kategori_ekle_stil_2.php?id=<?php echo $id; ?>&tip=<?php echo $tip; ?>&sinif=<?php echo $sinif; ?>&ust_kategori="+deger);
			$("#<?php echo $sinif; ?>-sifirla").show();
		}
	});
});
</script>