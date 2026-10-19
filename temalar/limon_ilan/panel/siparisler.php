<?php 
if(count(get_included_files())==1) {die();}
@$tip=$_GET["tip"];
if ($tip=="get") {
	include("siparisler-get.php");
} else if ($tip=="doping") {
	include("siparisler-doping.php");
}  else if ($tip=="magaza") {
	include("siparisler-magaza.php");
}   else if ($tip=="paket") {
	include("siparisler-paket.php");
} 
if (!$tip) {
?>
<h3 class="pull-left bolum-baslik text-uppercase"><?php echo _LANG("pn_siparis_turu_seciniz"); ?></h3>
<div class="clearfix mb20"></div>
<div class="row">
	<div class="col-md-15">
		<ul class="list-group">
			<a href="temalar.php?tema=<?php echo $aktif_tema; ?>&sayfa=panel&islem=siparisler&tip=get" class="list-group-item"><i class="fa fa-angle-right fa-fw"></i>GET Siparişleri <?php echo $get_siparis_badge; ?></a>
			<a href="temalar.php?tema=<?php echo $aktif_tema; ?>&sayfa=panel&islem=siparisler&tip=magaza" class="list-group-item"><i class="fa fa-angle-right fa-fw"></i>Mağaza Siparişleri <?php echo $magaza_siparis_badge; ?></a>
			<a href="temalar.php?tema=<?php echo $aktif_tema; ?>&sayfa=panel&islem=siparisler&tip=doping" class="list-group-item"><i class="fa fa-angle-right fa-fw"></i>Doping Siparişleri <?php echo $doping_siparis_badge; ?></a>
			<a href="temalar.php?tema=<?php echo $aktif_tema; ?>&sayfa=panel&islem=siparisler&tip=paket" class="list-group-item"><i class="fa fa-angle-right fa-fw"></i>Üye Paket Siparişleri <?php echo $paket_siparis_badge; ?></a>
		</ul>
	</div>
</div>
<?php } ?>