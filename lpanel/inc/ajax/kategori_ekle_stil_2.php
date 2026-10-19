<?php
require("../../../loader.php");

$ref_url=explode("/$admin_dizini",ref_url())[0];
if (site_url()!=$ref_url) {
	die("Hata: Erisim engellendi.");
}

$sinif=$_GET["sinif"];
$tip=$_GET["tip"];
$id=$_GET["id"];
$ust_kategori=$_GET["ust_kategori"];

if (!$ust_kategori) {
	$ust_kategori=0;
}
$mevcut_kategoriler=bilgi_getir("icerik","kategori",$id);
if ($mevcut_kategoriler) {
	$tum_kategoriler=kategori_getir($id,$sinif);
}
$sql=mysqli_query($baglan,"select * from kategoriler where ust_kategori='$ust_kategori' and tip='$tip' and sinif='$sinif'");
$toplam=mysqli_num_rows($sql);

if ($toplam>0) {
?>
<div class="form-group">
	<select id="<?php echo $sinif; ?>_tip_2" name="<?php echo $sinif; ?>[]" class="form-control">
		<option><?php echo _LANG("filter_select"); ?></option>
		<?php 
		while ($dizi=mysqli_fetch_array($sql)) {
			$kat_id=$dizi["id"];
			$kat_baslik=$dizi["baslik"];
			if (strstr($mevcut_kategoriler,"(".$kat_id.")")) {$selected="selected='selected'";} else {$selected="";}
		?>
		<option value="<?php echo $kat_id; ?>" <?php echo $selected; ?>><?php echo $kat_baslik; ?></option>
		<?php } ?>
	</select>
</div>
<?php } else {echo "<div class='alert alert-success'>Kategori seçimi tamamlandı</div>";} ?>
<div id="<?php echo $sinif; ?>-alt"></div>

<script type="text/javascript">
$(function(){
	$("body").on("change","#<?php echo $sinif; ?>_tip_2",function(){
		deger=$(this).val();
		if (deger!='Seçiniz') {
			$(this).attr("disabled","disabled");
			$("#<?php echo $sinif; ?>-alt").append("<div id='alt_kategori_"+deger+"'></div>");
			$("#alt_kategori_"+deger).load("inc/ajax/kategori_ekle_stil_2.php?id=<?php echo $id; ?>&tip=<?php echo $tip; ?>&sinif=<?php echo $sinif; ?>&ust_kategori="+deger);
			$("#<?php echo $sinif; ?>-sifirla").show();
		}
	});
});
</script>