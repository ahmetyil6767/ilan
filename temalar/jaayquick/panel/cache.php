<?php 
if(count(get_included_files())==1) {die();}
@$temizle=$_GET["temizle"];
if ($temizle==1) {
	temizle("../cache");
	$_SESSION["mesaj"]=array(_LANG('pn_onbellek_temizlendi'),".");
	yonlendir("temalar.php?tema=$aktif_tema&sayfa=panel&islem=cache");
}
function sayibul($klasor) {
	if (file_exists($klasor)) {
		$dizi = array(); // dizi oluştur
		$open = opendir($klasor); // klasör aç
		while($q=readdir($open)) {
			if ($q != "." && $q != "..") {
				$dizi[] = $q;
			}
		}
		$sayi = count($dizi); // dosya sayısı
		closedir($open);  // klasörü kapat 
		return $sayi; // sayıyı çıktı olarak al
	}
}
@$buton=$_POST["buton"];
if ($buton) {
	tema_ayar_guncelle("cache",$_POST["cache"]);
	tema_ayar_guncelle("anasayfa_cache_sure",$_POST["anasayfa_cache_sure"]);
	tema_ayar_guncelle("ilan_cache_sure",$_POST["ilan_cache_sure"]);
	tema_ayar_guncelle("sayfa_cache_sure",$_POST["sayfa_cache_sure"]);
	tema_ayar_guncelle("kategori_cache_sure",$_POST["kategori_cache_sure"]);
	$_SESSION["mesaj"]=array(_LANG('pn_onbellekleme_secenekleri_guncellendi'),".");
	yonlendir("temalar.php?tema=$aktif_tema&sayfa=panel&islem=cache");	
}

?>
<h3 class="pull-left bolum-baslik text-uppercase"><?php echo _LANG("pn_cache_onbellekleme"); ?></h3>
<div class="clearfix mb20"></div>
<div class="row">
	<div class="col-md-15">
		<div class="kutu" style="padding:20px;font-size:18px;background:<?php if (tema_ayar("cache")==1) {echo "#2ecc71";} else {echo "#e74c3c";} ?>;color:#fff;border-radius:3px;margin-bottom:20px;">
			Cache Sistemi
			<div class="rakam" style="font-size:32px;font-weight:700;"><?php if (tema_ayar("cache")==1) {echo "_LANG('general_open')";} else {echo "_LANG('general_closed')";} ?></div>
		</div>
	</div>
	<div class="col-md-15">
		<div class="kutu" style="padding:20px;font-size:18px;background:#777;color:#fff;border-radius:3px;margin-bottom:20px;">
			Önbelleğe alınan sayfa
			<div class="rakam" style="font-size:32px;font-weight:700;"><?php echo sayibul("../cache/ilan")+sayibul("../cache/sayfa")+sayibul("../cache/kategori"); ?></div>
		</div>
	</div>
	<div class="col-md-15">
		<div class="kutu" style="padding:20px;font-size:18px;background:#777;color:#fff;border-radius:3px;margin-bottom:20px;">
			Önbelleği temizle
			<div class="rakam" style="font-size:32px;font-weight:700;"><a href="temalar.php?tema=<?php echo $aktif_tema; ?>&sayfa=panel&islem=cache&temizle=1" class="btn btn-default"><?php echo _LANG("pn_onbellegi_temizle"); ?></a></div>
		</div>	
	</div>
	<div class="col-md-15">
		<div class="kutu" style="padding:20px;font-size:18px;background:#777;color:#fff;border-radius:3px;margin-bottom:20px;">
			Önbellek dosyaları
			<div class="rakam" style="font-size:32px;font-weight:700;"><a href="<?php echo site_url(); ?>/cache/" target="_blank" class="btn btn-default"><?php echo _LANG("pn_cache_dosyalarini_goruntule"); ?></a></div>
		</div>	
	</div>
</div>

<div class="panel panel-default">
	<div><?php echo _LANG('hb_cache_onbellekleme'); ?></div>
	<div class="panel-body">
	<p><?php echo _LANG('hb_onbellekleme_sistemi_dinamik_sayfalarin_belirl'); ?></p>
	<p><?php echo _LANG('hb_web_sitenizi_gizli_sekmede_acarak_farki_test_e'); ?></p>
	<p><i class="fa fa-rocket fa-2x"></i></p>
	<hr />
		<form action="" method="post">
		<?php 
		form_group(array("tip"=>"select","id"=>"cache","baslik"=>_LANG('pn_cache_onbellekleme_sistemi'),"secenekler"=>options("Kapalı:0,Açık:1",",",":"),"deger"=>tema_ayar("cache"),"aciklama"=>"Önbelleğin üzerinden belirlediğiniz kadar süre geçince önbellek yeniden oluşturulur. Kategori sayfası gibi sık güncellenen sayfaların önbellek süresi az, sık değişmeyen sayfaların önbellekleme süreleri ise uzun olmalıdır. Değerler dakika cinsindendir. İçerik sayfası önbellek süresi için önerilen değer 2880 dakika, yani 2 gündür."));
		
		$ilan_cache_sure=tema_ayar("ilan_cache_sure");
		if (!$ilan_cache_sure) {
			$ilan_cache_sure=2880;
		}			
		form_group(array("tip"=>"text","id"=>"ilan_cache_sure","baslik"=>_LANG('pn_ilan_cache_suresi_dakika_onerilen_2880_dakika'),"deger"=>$ilan_cache_sure));	

		$kategori_cache_sure=tema_ayar("kategori_cache_sure");
		if (!$kategori_cache_sure) {
			$kategori_cache_sure=30;
		}			
		form_group(array("tip"=>"text","id"=>"kategori_cache_sure","baslik"=>_LANG('pn_kategori_cache_suresi_dakika_onerilen_30_dakika'),"deger"=>$kategori_cache_sure));
		
		$sayfa_cache_sure=tema_ayar("sayfa_cache_sure");
		if (!$sayfa_cache_sure) {
			$sayfa_cache_sure=120;
		}			
		form_group(array("tip"=>"text","id"=>"sayfa_cache_sure","baslik"=>_LANG('pn_diger_acil_acil_son_24_saat_vs_cache_suresi_dakika_onerilen_120_dakika'),"deger"=>$sayfa_cache_sure));

		form_buton("buton",_LANG('tx_guncelle'),"primary");
		?>
		</form>	
	</div>
</div>