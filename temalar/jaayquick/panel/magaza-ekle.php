<?php 
if(count(get_included_files())==1) {die();}
@$buton=$_POST["buton"];
if ($buton) {
	@$baslik=$_POST["baslik"];
	@$subdomain=$_POST["subdomain"];
	@$uye_id=$_POST["uye_id"];
	@$paket=$_POST["paket"];
	@$kategori=$_POST["kategori"];
	@$durum=$_POST["durum"];

	$magaza=magaza_ekle($baslik,$uye_id,$kategori,$durum);
	
	if (!$subdomain) {
		$subdomain_hazirlik=str_replace(array("-"),null,url_olustur($baslik));
		$subdomain=benzersiz_url_olustur(array("tablo"=>"magazalar","sutun"=>"subdomain","baslik"=>$subdomain_hazirlik,"id"=>$magaza,"url"=>"$subdomain"));
		guncelle("magazalar","subdomain",$subdomain,$magaza);
	}
	
	magaza_bilgi_guncelle("paket",$paket,$magaza);

	$ozel_limitler=$_POST["ozel_limitler"];
	$ilan_limit=$_POST["ilan_limit"];
	$foto_limit=$_POST["foto_limit"];
	$danisman_limit=$_POST["danisman_limit"];
	$aninda_onay=$_POST["aninda_onay"];	
	magaza_bilgi_guncelle("ozel_limitler",$ozel_limitler,$magaza);
	
	if (!$ilan_limit) {
		$ilan_limit=liste_bilgi("ilan_limit",$paket);
	}
	if (!$foto_limit) {
		$foto_limit=liste_bilgi("foto_limit",$paket);
	}
	if (!$danisman_limit) {
		$danisman_limit=liste_bilgi("danisman_limit",$paket);
	}
	if (!$aninda_onay) {
		$aninda_onay=liste_bilgi("aninda_onay",$paket);
	}
	
	if (tema_ayar("limit_politikasi")==1) {
		magaza_bilgi_guncelle("ilan_limit",$ilan_limit,$magaza);
		magaza_bilgi_guncelle("foto_limit",$foto_limit,$magaza);
		magaza_bilgi_guncelle("danisman_limit",$danisman_limit,$magaza);
		magaza_bilgi_guncelle("aninda_onay",$aninda_onay,$magaza);
	} else {
		magaza_bilgi_guncelle("azalan_ilan_limit",$ilan_limit,$magaza);
		magaza_bilgi_guncelle("azalan_foto_limit",$foto_limit,$magaza);
		magaza_bilgi_guncelle("azalan_danisman_limit",$danisman_limit,$magaza);
		magaza_bilgi_guncelle("azalan_aninda_onay",$aninda_onay,$magaza);
	}
	
	$_SESSION["mesaj"]=array(_LANG('pn_magaza_basariyla_olusturuldu'),".");
	yonlendir("temalar.php?tema=$aktif_tema&sayfa=panel&islem=magazalar");
}


?>
<h3 class="pull-left bolum-baslik text-uppercase"><?php echo _LANG("pn_magaza_ekle"); ?></h3>
<a href="temalar.php?tema=<?php echo $aktif_tema; ?>&sayfa=panel&islem=magazalar" class="btn btn-default"><?php echo _LANG("stores"); ?></a>
<form class="form-inline pull-right visible-md visible-lg" action="" method="post">
  <div class="form-group">
    <input type="text" class="form-control" id="kayit" name="kacarli" placeholder="<?php echo _LANG("p_listeleme_adedi"); ?>">
  </div>
  <button type="submit" name="kacarli_buton" class="btn btn-default" value="Onayla">Onayla</button>
</form>
<div class="clearfix mb20"></div>
<div class="panel">
	<div class="panel-body">
		<form action="" method="post">
			<?php form_group(array("tip"=>"text","id"=>"baslik","baslik"=>_LANG('s_magaza_adi'),"gerekli"=>1)); ?>
			<?php form_group(array("tip"=>"text","id"=>"subdomain","baslik"=>_LANG('pn_magaza_subdomain'),"aciklama"=>"Opsiyonel. Mutlaka benzersiz olmalı. Boş bırakırsanız benzersiz subdomain otomatik olarak belirlenecektir.")); ?>
			<?php
			$uyeler_seciniz[]=array("deger"=>"","baslik"=>_LANG('filter_select'));
			$sql=mysqli_query($baglan,"SELECT * from uyeler order by ad_soyad asc");
			while ($dizi=mysqli_fetch_array($sql)) {
				$id=$dizi["id"];
				$ad_soyad=$dizi["ad_soyad"];
				$uyeler_seciniz[]=array("deger"=>$id,"baslik"=>$ad_soyad);
			}
			form_group(array("tip"=>"select","id"=>"uye_id","baslik"=>_LANG('p_uye'),"secenekler"=>$uyeler_seciniz,"gerekli"=>1)); 

			$magaza_kategorileri[]=array("deger"=>"","baslik"=>_LANG('filter_select'));
			$kategori_sql=mysqli_query($baglan,"SELECT k.id, k.baslik from kategoriler as k INNER JOIN ekstra_bilgi as eb ON k.id=eb.id and eb.anahtar='magaza' and eb.deger='1'");
			while ($kategori_dizi=mysqli_fetch_array($kategori_sql)) {
				$kategori_id=$kategori_dizi["id"];
				$kategori_baslik=$kategori_dizi["baslik"];
				$magaza_kategorileri[]=array("deger"=>$kategori_id,"baslik"=>$kategori_baslik);
			}
			form_group(array("tip"=>"select","id"=>"kategori","baslik"=>"Kategori","secenekler"=>$magaza_kategorileri,"gerekli"=>1)); 

			$paket_seciniz_sql=mysqli_query($baglan,"select l_id from listeler where tip='magaza_paketleri'");
			while ($paket_seciniz_dizi=mysqli_fetch_array($paket_seciniz_sql)) {
				$paket_id=$paket_seciniz_dizi["l_id"];
				$paket_baslik=liste_bilgi("paket_adi",$paket_id);
				$magaza_paket_seciniz[]=array("deger"=>"$paket_id","baslik"=>"$paket_baslik");
			}
			form_group(array("tip"=>"select","id"=>"paket","baslik"=>"Paket","secenekler"=>$magaza_paket_seciniz,"gerekli"=>1));							
			form_group(array("tip"=>"select","id"=>"durum","baslik"=>"Durum","secenekler"=>options(_LANG('tx_pasif_0_onayli_1_suresi_doldu_2'),"|",":"),"gerekli"=>1));

			form_group(array("tip"=>"select","id"=>"ozel_limitler","baslik"=>_LANG('s_ozel_limitler'),"secenekler"=>options("_LANG('tx_hayir_0_evet_1')","|",":"),"aciklama"=>"Özel limitleri Hayır olarak işaretlerseniz mağazanın paket limitleri, Evet olarak işaretlerseniz ise aşağıda belirleyeceğiniz limitler geçerli olacaktır."));
			form_group(array("tip"=>"text","id"=>"ilan_limit","baslik"=>_LANG('s_ilan_limiti')));
			form_group(array("tip"=>"text","id"=>"foto_limit","baslik"=>_LANG('s_fotograf_limiti')));
			form_group(array("tip"=>"text","id"=>"danisman_limit","baslik"=>_LANG('s_danisman_limiti_k')));
			form_group(array("tip"=>"select","id"=>"aninda_onay","baslik"=>_LANG('s_aninda_onay'),"secenekler"=>options("_LANG('tx_hayir_0_evet_1')","|",":")));
			
			?>
			<?php form_buton("buton",_LANG('tx_magaza_olustur'),"primary"); ?>
		</form>
	</div>
</div>