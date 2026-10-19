<?php 
if(count(get_included_files())==1) {die();}
$sil_onay=$_POST["sil_onay"];
if ($sil_onay) {
	eklenti_ayar_guncelle("not",$not_defteri_textarea,"not_defteri");
	mesaj_goster("Not defteri temizlendi","success");
	yonlendir("index.php");
}
?>
<form action="" method="post">
<h3 class="baslik"><?php echo _LANG('e_silmeyi_onayla'); ?></h3>
<p><?php echo _LANG('hb_not_defterine_herhangi_bir_sey_yazmadiniz_notl'); ?></p>
<button name="sil_onay" class="btn btn-success" value="Onay">Silmeyi onayla</button>
<a href="index.php" class="btn btn-danger"><?php echo _LANG('p_vazgec'); ?></a>
</form>