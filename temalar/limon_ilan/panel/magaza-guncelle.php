<?php 
if(count(get_included_files())==1) {die();}
$id=$_GET["id"];
$buton=$_POST["buton"];

if ($buton) {
	$baslik=$_POST["baslik"];
	$subdomain=$_POST["subdomain"];
	$uye_id=$_POST["uye_id"];
	$paket=$_POST["paket"];
	$paket_bitis_tarihi=$_POST["paket_bitis_tarihi"];
	$paket_bitis_tarihi.=":00";
	$kategori=$_POST["kategori"];
	$durum=$_POST["durum"];
	$ozel_limitler=$_POST["ozel_limitler"];
	$ilan_limit=$_POST["ilan_limit"];
	$foto_limit=$_POST["foto_limit"];
	$danisman_limit=$_POST["danisman_limit"];
	$aninda_onay=$_POST["aninda_onay"];
	
	if (!$subdomain) {
		$subdomain_hazirlik=str_replace(array("-"),null,url_olustur($baslik));
		$subdomain=benzersiz_url_olustur(array("tablo"=>"magazalar","sutun"=>"subdomain","baslik"=>$subdomain_hazirlik,"id"=>$id,"url"=>"$subdomain"));
	}
	
	mysqli_query($baglan,"update magazalar set baslik='$baslik', subdomain='$subdomain', uye_id='$uye_id', kategori='$kategori', durum='$durum' where id='$id'");
	magaza_bilgi_guncelle("paket",$paket,$id);
	magaza_bilgi_guncelle("paket_bitis_tarihi",$paket_bitis_tarihi,$id);
	magaza_bilgi_guncelle("ozel_limitler",$ozel_limitler,$id);
	magaza_bilgi_guncelle("ilan_limit",$ilan_limit,$id);
	magaza_bilgi_guncelle("foto_limit",$foto_limit,$id);
	magaza_bilgi_guncelle("danisman_limit",$danisman_limit,$id);
	magaza_bilgi_guncelle("aninda_onay",$aninda_onay,$id);
    
    
    $azalan_ilan_limit=$_POST["azalan_ilan_limit"];
    $azalan_foto_limit=$_POST["azalan_foto_limit"];
    $azalan_danisman_limit=$_POST["azalan_danisman_limit"];
    $azalan_aninda_onay=$_POST["azalan_aninda_onay"];
    
	magaza_bilgi_guncelle("azalan_ilan_limit",$azalan_ilan_limit,$id);
	magaza_bilgi_guncelle("azalan_foto_limit",$azalan_foto_limit,$id);
	magaza_bilgi_guncelle("azalan_danisman_limit",$azalan_danisman_limit,$id);
	magaza_bilgi_guncelle("azalan_aninda_onay",$azalan_aninda_onay,$id);
    
	$profil_gorseli=$_FILES["profil_gorseli"];
	if (is_array($profil_gorseli)) {
		$profil_data = getimagesize($profil_gorseli["tmp_name"]);
		$profil_width = $profil_data[0];
		$profil_height = $profil_data[1];
		if ($profil_width>=150 or $profil_height>=150) {	
			$mevcut_gorsel=magaza_bilgi("profil_gorseli",$id);
			$yuklenen_resim=panel_yukle($profil_gorseli,"magaza_profil",array("50_50","150_150"));

			if ($yuklenen_resim) {
				// Mevcut resmi ve boyutlarını silelim.
				panel_sil(thumbnail_url("50_50",$mevcut_gorsel));
				panel_sil(thumbnail_url("150_150",$mevcut_gorsel));
				panel_sil($mevcut_gorsel);
				magaza_bilgi_guncelle("profil_gorseli",$yuklenen_resim,$id);
				// 10.12.2018 Orijinal resim optimize başladı
				if ($profil_width>=300 and $profil_height>=300) {
					$orijinal_gorsel=$yuklenen_resim;
					$uzanti=uzanti_ne($orijinal_gorsel);
					$bol=explode($uzanti,$orijinal_gorsel);
					$resim_adi=rtrim($bol[0],".");
					$yeni_resim="$resim_adi.$uzanti";
					$parcala=explode("uploads/",$yeni_resim);
					$yol=$parcala[1];
					include_once("../temalar/$aktif_tema/class/SimpleImage.php");
					error_reporting(E_ALL & ~E_NOTICE);
					if ($uzanti=="jpg" or $uzanti=="jpeg") {
						$mime_type="jpeg";
					} else if ($uzanti=="png") {
						$mime_type="png";
					} else if ($uzanti=="gif") {
						$mime_type="gif";
					}
					try {
					  $image = new \claviska\SimpleImage();
					  $image->fromFile($orijinal_gorsel)->thumbnail(300,300)->toFile("uploads/$yol", "image/$mime_type",90);
					} catch(Exception $err) {
					  // Handle errors
					  // echo $err->getMessage();
					}
				}
				// 22.04.2018 Orijinal resim optimize bitti
			}
		}
	}
	
	print_r($_POST);
	
	$kapak_gorseli=$_FILES["kapak_gorseli"];
	if (is_array($kapak_gorseli)) {
		$kapak_data = getimagesize($kapak_gorseli["tmp_name"]);
		$kapak_width = $kapak_data[0];
		$kapak_height = $kapak_data[1];
		if ($kapak_width>=1140 or $kapak_height>=200) {	
			$mevcut_gorsel=magaza_bilgi("kapak_gorseli",$id);
			$yuklenen_resim=panel_yukle($kapak_gorseli,"magaza_kapak",array("1140_200"),"1140_200",1);
			if ($yuklenen_resim) {
				// Mevcut resmi ve boyutlarını silelim.
				panel_sil($mevcut_gorsel);
				magaza_bilgi_guncelle("kapak_gorseli",$yuklenen_resim,$id);
			}
		}
	}
	
	$upload_kaldir=$_POST["upload_kaldir"];
	
	if (is_array($upload_kaldir)) {
		if (in_array("profil_gorseli",$upload_kaldir)) {
			$mevcut_gorsel=magaza_bilgi("profil_gorseli",$id);
			panel_sil(thumbnail_url("50_50",$mevcut_gorsel));
			panel_sil(thumbnail_url("150_150",$mevcut_gorsel));
			panel_sil($mevcut_gorsel);
			magaza_bilgi_guncelle("profil_gorseli",null,$id);
		}
		if (in_array("kapak_gorseli",$upload_kaldir)) {
			$mevcut_gorsel=magaza_bilgi("kapak_gorseli",$id);
			panel_sil($mevcut_gorsel);
			magaza_bilgi_guncelle("kapak_gorseli",null,$id);
		}
	}
	
	$_SESSION["mesaj"]=array(_LANG('pn_magaza_basariyla_guncellendi'),".");
	yonlendir("temalar.php?tema=$aktif_tema&sayfa=panel&islem=magaza-guncelle&id=$id");
}
?>
<h3 class="pull-left bolum-baslik text-uppercase"><?php echo _LANG("pn_magaza_guncelle"); ?></h3>
<a href="temalar.php?tema=<?php echo $aktif_tema; ?>&sayfa=panel&islem=magazalar" class="btn btn-default"><?php echo _LANG("stores"); ?></a>
<a href="<?php echo magaza_link_getir($id); ?>" class="btn btn-default"><i class="fa fa-external-link"></i> Mağazayı görüntüle</a>
<form class="form-inline pull-right visible-md visible-lg" action="" method="post">
  <div class="form-group">
    <input type="text" class="form-control" id="kayit" name="kacarli" placeholder="<?php echo _LANG("p_listeleme_adedi"); ?>">
  </div>
  <button type="submit" name="kacarli_buton" class="btn btn-default" value="Onayla">Onayla</button>
</form>
<div class="clearfix mb20"></div>
<div class="panel">
	<div class="panel-body">
		<form action="" method="post" enctype="multipart/form-data">
			<?php form_group(array("tip"=>"text","id"=>"baslik","baslik"=>_LANG('s_magaza_adi'),"gerekli"=>1,"deger"=>getir("magazalar","baslik",$id))); ?>
			<?php form_group(array("tip"=>"text","id"=>"subdomain","baslik"=>_LANG('pn_magaza_subdomain'),"deger"=>getir("magazalar","subdomain",$id),"aciklama"=>"Opsiyonel. Mutlaka benzersiz olmalı. Boş bırakırsanız benzersiz subdomain otomatik olarak belirlenecektir.")); ?>
			<?php
			$uyeler_seciniz[]=array("deger"=>"","baslik"=>_LANG('filter_select'));
			$uye_sql=mysqli_query($baglan,"SELECT * from uyeler order by ad_soyad asc");
			while ($uye_dizi=mysqli_fetch_array($uye_sql)) {
				$uye_id=$uye_dizi["id"];
				$uye_ad_soyad=$uye_dizi["ad_soyad"];
				$uyeler_seciniz[]=array("deger"=>$uye_id,"baslik"=>$uye_ad_soyad);
			}
			form_group(array("tip"=>"select","id"=>"uye_id","baslik"=>_LANG('p_uye'),"secenekler"=>$uyeler_seciniz,"gerekli"=>1,"deger"=>getir("magazalar","uye_id",$id))); 

			$magaza_kategorileri[]=array("deger"=>"","baslik"=>_LANG('filter_select'));
			$kategori_sql=mysqli_query($baglan,"SELECT k.id, k.baslik from kategoriler as k INNER JOIN ekstra_bilgi as eb ON k.id=eb.id and eb.anahtar='magaza' and eb.deger='1'");
			while ($kategori_dizi=mysqli_fetch_array($kategori_sql)) {
				$kategori_id=$kategori_dizi["id"];
				$kategori_baslik=$kategori_dizi["baslik"];
				$magaza_kategorileri[]=array("deger"=>$kategori_id,"baslik"=>$kategori_baslik);
			}
			form_group(array("tip"=>"select","id"=>"kategori","baslik"=>"Kategori","secenekler"=>$magaza_kategorileri,"gerekli"=>1,"deger"=>getir("magazalar","kategori",$id))); 

			$paket_seciniz=null;
			$paket_seciniz_sql=mysqli_query($baglan,"select l_id from listeler where tip='magaza_paketleri'");
			while ($paket_seciniz_dizi=mysqli_fetch_array($paket_seciniz_sql)) {
				$paket_id=$paket_seciniz_dizi["l_id"];
				$paket_baslik=liste_bilgi("paket_adi",$paket_id);
				$paket_seciniz[]=array("deger"=>"$paket_id","baslik"=>"$paket_baslik");
			}
			form_group(array("tip"=>"select","id"=>"paket","baslik"=>"Paket","secenekler"=>$paket_seciniz,"gerekli"=>1,"deger"=>magaza_bilgi("paket",$id)));							
			form_group(array("tip"=>"date_time","id"=>"paket_bitis_tarihi","baslik"=>_LANG('pn_bitis_tarihi'),"gerekli"=>1,"deger"=>magaza_bilgi("paket_bitis_tarihi",$id)));			
			form_group(array("tip"=>"select","id"=>"durum","baslik"=>"Durum","secenekler"=>options(_LANG('tx_pasif_0_onayli_1_suresi_doldu_2'),"|",":"),"gerekli"=>1,"deger"=>getir("magazalar","durum",$id)));
			
            echo "<h4>Mağaza Limitleri</h4>";
            $limit_politikasi=limit_politikasi();
            if ($limit_politikasi==1) {
                echo "<div class='alert alert-primary'>Mevcut Limit Politikası: Sabit</div>";
                form_group(array("tip"=>"select","id"=>"ozel_limitler","baslik"=>_LANG('s_ozel_limitler'),"secenekler"=>options("Hayır:0|Evet:1","|",":"),"deger"=>magaza_bilgi("ozel_limitler",$id),"aciklama"=>"Özel limitleri Hayır olarak işaretlerseniz mağazanın paket limitleri, Evet olarak işaretlerseniz ise aşağıda belirleyeceğiniz limitler geçerli olacaktır."));
                form_group(array("tip"=>"text","id"=>"ilan_limit","baslik"=>_LANG('s_ilan_limiti'),"deger"=>magaza_bilgi("ilan_limit",$id)));
                form_group(array("tip"=>"text","id"=>"foto_limit","baslik"=>_LANG('s_fotograf_limiti'),"deger"=>magaza_bilgi("foto_limit",$id)));
                form_group(array("tip"=>"text","id"=>"danisman_limit","baslik"=>_LANG('s_danisman_limiti_k'),"deger"=>magaza_bilgi("danisman_limit",$id)));
                form_group(array("tip"=>"select","id"=>"aninda_onay","baslik"=>_LANG('s_aninda_onay'),"secenekler"=>options("Hayır:0|Evet:1","|",":"),"deger"=>magaza_bilgi("aninda_onay",$id)));        
            } else {
                echo "<div class='alert alert-primary'>Mevcut Limit Politikası: Azalan</div>";
                form_group(array("tip"=>"text","id"=>"azalan_ilan_limit","baslik"=>_LANG('s_ilan_limiti'),"deger"=>magaza_bilgi("azalan_ilan_limit",$id)));
                form_group(array("tip"=>"text","id"=>"azalan_foto_limit","baslik"=>_LANG('s_fotograf_limiti'),"deger"=>magaza_bilgi("azalan_foto_limit",$id)));
                form_group(array("tip"=>"text","id"=>"azalan_danisman_limit","baslik"=>_LANG('s_danisman_limiti_k'),"deger"=>magaza_bilgi("azalan_danisman_limit",$id)));
                form_group(array("tip"=>"select","id"=>"azalan_aninda_onay","baslik"=>_LANG('s_aninda_onay'),"secenekler"=>options("Hayır:0|Evet:1","|",":"),"deger"=>magaza_bilgi("azalan_aninda_onay",$id)));                
            }
            
			upload_olustur("profil_gorseli",_LANG('tx_profil_gorseli'),magaza_bilgi("profil_gorseli",$id),_LANG('tx_en_az_150px_x_150px_kare_formatta_bir_gorsel_y'));
			
			upload_olustur("kapak_gorseli",_LANG('tx_kapak_gorseli'),magaza_bilgi("kapak_gorseli",$id),_LANG('tx_1140x200_px_boyutunda_bir_gorsel_yukleyin_bu_b'));
			?>
			
			
			
			<?php form_buton("buton",_LANG('tx_magaza_guncelle'),"primary"); ?>
		</form>
	</div>
</div>