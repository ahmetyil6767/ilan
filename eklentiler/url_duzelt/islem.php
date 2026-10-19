<?php
if(count(get_included_files())==1) {die();}
$buton=$_POST["buton"];
if ($buton) {
$sql=mysqli_query($baglan,"select * from kategoriler where (url like '%citroën%') or (url like '%café%') or (url like '%león%') or (url like '%škoda%')");
while ($dizi=mysqli_fetch_array($sql)) {
	$id=$dizi["id"];
	$url=$dizi["url"];
	$bul=array("ë","š","é","ó","café");
	$degistir=array("e","s","e","o","cafe");
	$duzelt=str_replace($bul,$degistir,$url);
	echo $duzelt."<br />";
	guncelle("kategoriler","url",$duzelt,$id);
}
$_SESSION["mesaj"]=array(_LANG('msj_url_duzeltildi'),"success");
ayar_guncelle("url_duzelt",2);
yonlendir(browser_url());
}
$say1=mysqli_fetch_array(mysqli_query($baglan,"select COUNT(id) from kategoriler where url like '%citroën%'"))[0];
$say2=mysqli_fetch_array(mysqli_query($baglan,"select COUNT(id) from kategoriler where url like '%café%'"))[0];
$say3=mysqli_fetch_array(mysqli_query($baglan,"select COUNT(id) from kategoriler where url like '%león%'"))[0];
$say4=mysqli_fetch_array(mysqli_query($baglan,"select COUNT(id) from kategoriler where url like '%škoda%'"))[0];
$toplam=$say1+$say2+$say3+$say4;
?>
<div><?php echo _LANG('hb_url_duzelt'); ?></div>
<div class="row">
	<div class="col-md-30">
		<p><?php echo _LANG('e_url_duzelt_ac'); ?> <?php echo ayar_getir("url_duzelt"); ?></p>
		<?php if (!ayar_getir("url_duzelt") or ayar_getir("url_duzelt")!=2) { ?>
		<div class="alert alert-danger"><?php echo $toplam; ?> hata bulundu.</div>
		<?php } else { ?>
		<div><?php echo _LANG('hb_hata_bulunamadi'); ?></div>
		<?php } ?>
		<form action="" method="post">
			<button type="submit" name="buton" value="url" class="btn btn-default"><i class="fa fa-link"></i>Sorunları gider</button>
		</form>
	</div>
</div>