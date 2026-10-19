<?php
require("../../../loader.php");

$ref_url=explode("/$admin_dizini",ref_url())[0];
if (site_url()!=$ref_url) {
	die("Hata: Erisim engellendi.");
}

$sinif=$_GET["sinif"];
$tip=$_GET["tip"];
$id=$_GET["id"];

@$ust_kategori=$_GET["ust_kategori"];
if (!$ust_kategori) {
	$ust_kategori=0;
}
$mevcut_kategoriler=kategori_getir($id,$sinif);
if (count($mevcut_kategoriler)>0) {
	$tum_kategoriler=kategori_getir($id,$sinif);
}
$sql=mysqli_query($baglan,"select * from kategoriler where ust_kategori='$ust_kategori' and tip='$tip' and sinif='$sinif'");
$toplam=mysqli_num_rows($sql);

$x=1;
$tum_kategori_sayisi=count($tum_kategoriler);
foreach ($tum_kategoriler as $yaz) {
$ust=getir("kategoriler","ust_kategori",$yaz);
$kat_baslik=getir("kategoriler","baslik",$yaz);
if ($x==1) { ?>
<div class="form-group">
	<select id="<?php echo $sinif; ?>_tip_2" name="<?php echo $sinif; ?>[]" class="form-control" disabled>
		<option><?php echo _LANG("filter_select"); ?></option>
		<?php 
		while ($dizi=mysqli_fetch_array($sql)) {
		$kat_id=$dizi["id"];
		$kat_baslik=$dizi["baslik"];
		if ($yaz==$kat_id) {$selected="selected='selected'";} else {$selected="";}
		?>
		<option value="<?php echo $kat_id; ?>" <?php echo $selected; ?>><?php echo $kat_baslik; ?></option>
		<?php }  ?>
	</select>
</div>
<?php } else { 
if (!@$alt_kategori_grup) {
	echo "<div id='$sinif-alt'>";
	$alt_kategori_grup=1;
}
?>
<div class="form-group">
	<select id="<?php echo $sinif; ?>_tip_2" name="<?php echo $sinif; ?>[]" class="form-control" disabled>
		<option value="<?php echo $yaz; ?>" selected="selected"><?php echo getir("kategoriler","baslik",$yaz); ?></option>
	</select>
</div>
<?php

}
$x++;
$son=$yaz;
}
$son_alt=alt_kategori_sayisi($son);
if ($x==$tum_kategori_sayisi and $son_alt==0) {echo "</div>";} else {
	$div_kapat="</div>";
}

// Son seçim yapılan kategorinin alt kategorileri mevcutsa ve seçilmemişse
if ($son_alt) {
$sql=mysqli_query($baglan,"select * from kategoriler where ust_kategori='$son' and tip='$tip' and sinif='$sinif'");
$toplam=mysqli_num_rows($sql);
?>
<div class="form-group">
	<select id="<?php echo $sinif; ?>_tip_2" name="<?php echo $sinif; ?>[]" class="form-control">
		<option><?php echo _LANG("filter_select"); ?></option>
		<?php 
		while ($dizi=mysqli_fetch_array($sql)) {
		$kat_id=$dizi["id"];
		$kat_baslik=$dizi["baslik"];
		if ($yaz==$kat_id) {$selected="selected='selected'";} else {$selected="";}
		?>
		<option value="<?php echo $kat_id; ?>" <?php echo $selected; ?>><?php echo $kat_baslik; ?></option>
		<?php }  ?>
	</select>
</div>
<?php } echo $div_kapat; ?>

<script type="text/javascript">
$(function(){
	$("#<?php echo $sinif; ?>-sifirla").show();
	
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