<?php
if(count(get_included_files())==1) {die();}
$buton=$_POST["buton"];
$guncelleme_buton=$_POST["guncelleme_buton"];
$sil=$_GET["sil"];
if ($buton) {
$dosya_adi=$_POST["dosya_adi"];
$tip=$_POST["tip"];
$satirlar=null;
$site_url=site_url();
if ($tip) {
	foreach ($tip as $yaz) {
		$tipler.=$yaz.",";
	}
	$tipler=rtrim($tipler,",");
}
$tarih_saat=tarih_saat();

if (!eklenti_ayar_getir("sitemap_url","xml_sitemap")) {
	eklenti_ayar_ekle("sitemap_url","$dosya_adi","xml_sitemap");
	eklenti_ayar_ekle("olusturulma_tarihi",$tarih_saat,"xml_sitemap");
	eklenti_ayar_ekle("son_guncelleme",$tarih_saat,"xml_sitemap");
	eklenti_ayar_ekle("guncelleme_sikligi",6,"xml_sitemap");
	eklenti_ayar_ekle("icerik_tipleri",$tipler,"xml_sitemap");
	$_SESSION["mesaj"]=array(sprintf(_LANG('msj_sitemap_olusturuldu'), $site_url."/".$dosya_adi),"success");
} else {
	ayar_guncelle("$eklenti-son_guncelleme",$tarih_saat);
	$_SESSION["mesaj"]=array(sprintf(_LANG('msj_sitemap_guncellendi'), $site_url."/".$dosya_adi),"success");
}
sitemap_guncelle();
yonlendir("eklentiler.php?eklenti=xml_sitemap&sayfa=olustur");		
}

if ($sil) {
	$sitemap_url=ayar_getir("$eklenti-sitemap_url");
	ayar_sil("$eklenti-sitemap_url");
	ayar_sil("$eklenti-olusturulma_tarihi");
	ayar_sil("$eklenti-son_guncelleme");
	ayar_sil("$eklenti-guncelleme_sikligi");
	ayar_sil("$eklenti-icerik_tipleri");
	unlink("../$sitemap_url");
	$_SESSION["mesaj"]=array(_LANG('msj_sitemap_silindi'),"success");
	yonlendir("eklentiler.php?eklenti=xml_sitemap&sayfa=olustur");	
}

if ($guncelleme_buton) {
	$guncelleme_sikligi=$_POST["guncelleme_sikligi"];
	if ($guncelleme_sikligi<1) {
		$guncelleme_sikligi=1;
		$_SESSION["mesaj"]=array(_LANG('msj_sitemap_min1saat'),"danger");
	} else {
		$_SESSION["mesaj"]=array(sprintf(_LANG('msj_sitemap_sikligi_degisti'), $guncelleme_sikligi),"success");	
	}
	ayar_guncelle("$eklenti-guncelleme_sikligi",$guncelleme_sikligi,"xml_sitemap");
	yonlendir("eklentiler.php?eklenti=xml_sitemap&sayfa=olustur");		
}
?>
<div class="baslik"><?php echo _LANG('e_sitemap_olustur'); ?></div>
<div class="row">
	<div class="col-md-30">

			<?php if (!eklenti_ayar_getir("olusturulma_tarihi","xml_sitemap")) { ?>
				<p><?php echo _LANG('e_sitemap_yok'); ?></p>
				<form action="" method="post">
					<?php 
					$z=0;
					foreach($icerik_tipleri as $yaz => $x) {
						$ib_name=$x["id"];
						$ib_baslik=$x["baslik"];
						$icerik_tipleri_options[$z]=array("deger"=>"$ib_name","baslik"=>"$ib_baslik");
					$z++;
					}
					?>
					<?php form_elemani("text","dosya_adi",_LANG('tx_sitemap_dosyasi_adi'),"sitemap.xml"); ?>
					<?php multiple_checkbox_olustur("tip",_LANG('tx_icerik_tipleri'),$icerik_tipleri_options,null,_LANG('tx_site_haritanizda_bulunmasini_istemediginiz_ice')); ?>
					<?php form_buton("buton",_LANG('tx_olustur'),"primary"); ?>
					<script type="text/javascript">
					$(function(){
						$("input[type=checkbox]").attr("checked","checked");
					});
					</script>
				</form>
			<?php } else { ?>
				<?php 
				$son_guncelleme=strtotime(eklenti_ayar_getir("son_guncelleme","xml_sitemap"));
				$tarih_saat=strtotime(tarih_saat());
				$guncelleme_sikligi=eklenti_ayar_getir("guncelleme_sikligi","xml_sitemap")*60*60;
				$fark_saat=$guncelleme_sikligi-($tarih_saat-$son_guncelleme);		
				?>
				<p><a href="<?php echo site_url(); ?>/<?php echo eklenti_ayar_getir("sitemap_url","xml_sitemap"); ?>" target="_blank"><?php echo site_url(); ?>/<?php echo eklenti_ayar_getir("sitemap_url","xml_sitemap"); ?></a> <em><?php echo eklenti_ayar_getir("olusturulma_tarihi","xml_sitemap"); ?> <?php echo _LANG('e_sitemap_tarih'); ?></em> <a href="eklentiler.php?eklenti=xml_sitemap&sayfa=olustur&sil=1" onclick="return confirm('Emin misiniz?')" style="background:red;color:#fff;padding:0 1px;">Sil</a></p>
				<p><?php echo _LANG('e_sitemap_hazir'); ?><br />
				Son güncelleme: <?php echo eklenti_ayar_getir("son_guncelleme","xml_sitemap"); ?><br />
				Güncelleme sıklığı: <?php echo eklenti_ayar_getir("guncelleme_sikligi","xml_sitemap"); ?> saat<br />
				İçerik tipleri: <?php echo eklenti_ayar_getir("icerik_tipleri","xml_sitemap"); ?><br />
				Otomatik güncellemeye kalan: <?php echo $fark_saat; ?> saniye</p>
				
				<p><?php echo _LANG('e_sitemap_tarih_sonra'); ?></p>
				
				<form action="" method="post">
					<?php form_elemani("hidden","dosya_adi",_LANG('tx_sitemap_dosyasi_adi'),eklenti_ayar_getir("sitemap_url","xml_sitemap")); ?>
					<?php form_buton("buton",_LANG('tx_sitemap_guncelle'),"primary"); ?>
					<a href="http://www.google.com/webmasters/tools/ping?sitemap=<?php echo site_url()."/".eklenti_ayar_getir("sitemap_url","xml_sitemap"); ?>" class="btn btn-default" target="_blank">Ping</a>
				</form>
				<div class="clearfix mb20"></div>
			<?php } ?>
	</div>
	<div class="col-md-30">
	<?php $guncelleme_sikligi=eklenti_ayar_getir("guncelleme_sikligi","xml_sitemap"); if ($guncelleme_sikligi) { ?>
		<h2 class="alt_baslik"><?php echo _LANG('e_sitemap_otomatik'); ?></h2>
		<p><?php echo _LANG('e_sitemap_her'); ?> <?php echo $guncelleme_sikligi; ?> <?php echo _LANG('e_sitemap_saat'); ?></p>
		<form action="" method="post" class="form-inline">
			<div class="input-group">
				<input type="text" name="guncelleme_sikligi" class="form-control input-sm" value="<?php echo $guncelleme_sikligi; ?>" size="5"/>
				<span class="input-group-addon">saat</span>
			</div>
			<?php form_buton("guncelleme_buton",_LANG('p_guncelle'),"default btn-sm"); ?>
		</form>
	<?php } ?>
	</div>
</div>