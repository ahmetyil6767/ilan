<?php
include("../loader.php");
yonetici_degilse_yonlendir($oturum_id,ayar_getir("site_url")."/$admin_dizini/giris-yap.php");
@$id=$_GET["id"];
@$islem=$_GET["islem"];
@$tip="uye";
@$buton=$_POST["buton"];
if ($_SESSION["oturum_yetki"]!="admin") {
	if ($islem=="ekle") {
		$_SESSION["mesaj"]=array(_LANG('p_yetki_yok'),"danger");
		yonlendir("uyeler.php");			
	}
}
if ($buton) {
	$ad_soyad=$_POST["ad_soyad"];
	$url=$_POST["url"];
	if ($url=="") {
		$url=url_olustur($ad_soyad);
	}
	$email_adresi=trim(isset($_POST["email_adresi"])?$_POST["email_adresi"]:"");$cep_tel=trim(isset($_POST["cep_tel"])?$_POST["cep_tel"]:"");
	$parola=$_POST["parola"];
	if ($parola) {
		$parola_kripto=parola_sakla($parola);
	} else if (!$parola and $islem=="ekle"){
		$parola_kripto=parola_sakla($ad_soyad);
	}		
	$durum=$_POST["durum"];
	$yetki=$_POST["yetki"];
	$tarih_saat=tarih_saat();
	if ($islem=="ekle") {
		$uye_mevcut_mu=uye_mevcut_mu($email_adresi);
		if ($uye_mevcut_mu==1) {
		$_SESSION["mesaj"]=array(_LANG('p_email_kullaniluyor'), array($email_adresi),"danger");
		} else {		
		$sql=mysqli_prepare($baglan,"insert into uyeler set ad_soyad=?, url=?, email_adresi=?, parola=?, tarih_saat=?, yetki=?, durum=?");
		mysqli_stmt_bind_param($sql,"sssssss",$ad_soyad,$url,$email_adresi,$parola_kripto,$tarih_saat,$yetki,$durum);
		mysqli_stmt_execute($sql);
		mysqli_stmt_close($sql);
		$eklenen_id=mysqli_insert_id($baglan);
		if ($cep_tel!="") { uye_bilgi_guncelle("cep_tel", $cep_tel, $eklenen_id); }
		foreach ($uye_ekstra_bilgiler as $yaz => $x) {
			$eb_type=$x["tip"];
			$eb_name=$x["id"];
			$deger=$_POST["$eb_name"];
			if ($eb_type!="multiple_checkbox" and $eb_type!="upload") {
				
				if ($eb_type=="location") {
					$lat=$_POST["map_$eb_name-lat"];
					$lon=$_POST["map_$eb_name-lon"];
					$radius=$_POST["map_$eb_name-radius"];
					$zoom=$_POST["map_$eb_name-zoom"];
					$deger="$lat,$lon,$radius,$zoom";
				}
				
				if ($deger!="") {
					ekstra_bilgi_guncelle($eb_name,$deger,$eklenen_id,$tip);
				}
			}
			else if ($eb_type=="multiple_checkbox") {
				foreach ($deger as $yaz) {	
					$diziden_cikart.="($yaz),";
				}
				if ($diziden_cikart!="") {
					ekstra_bilgi_guncelle($eb_name,$diziden_cikart,$eklenen_id,$tip);
				}
				$diziden_cikart=null;
			}
			else if ($eb_type=="upload") {
				$eb_boyutlar=$x["boyutlar"];
				$eb_dizin=$x["dizin"];
				$eb_orijinali_sil=$x["orijinali_sil"];
				$eb_gecerli_boyut=$x["gecerli_boyut"];		
				
				// v2 resim upload remove güncellemesi başladı.
				$secenekler=array();
				if (is_array($eb_boyutlar)) {
					$secenekler["boyutlar"]=$eb_boyutlar;
				}
				if ($eb_dizin) {
					$secenekler["dizin"]=$eb_dizin;
				}
				if ($eb_orijinali_sil) {
					$secenekler["orijinali_sil"]=$eb_orijinali_sil;
				}
				if ($eb_gecerli_boyut) {
					$secenekler["cikti"]=$eb_gecerli_boyut;
				}
				$resim_dizisi=$_FILES["$eb_name"];
				$resim_adi=$resim_dizisi["name"];
				if ($resim_adi!="") {
					$resim_url=yukle($resim_dizisi,$secenekler);
					$mevcut_deger=ekstra_bilgi_getir($eb_name,$eklenen_id,$tip);
					ekstra_bilgi_guncelle($eb_name,$resim_url,$eklenen_id,$tip);
				}
			}
			
		}
		$_SESSION["mesaj"]=array(_LANG('p_adi_ile_uye_eklendi'), array($ad_soyad),"success");
		yonlendir("uyeler.php");
		}
	} else if ($islem=="guncelle") {
		if ($_SESSION["oturum_yetki"]!="admin") {
			$yetki=$_SESSION["oturum_yetki"];
		}
		$id_int=(int)$id;
		$sql=mysqli_prepare($baglan,"update uyeler set ad_soyad=?, url=?, email_adresi=?, durum=?, yetki=? where id=?");
		mysqli_stmt_bind_param($sql,"sssssi",$ad_soyad,$url,$email_adresi,$durum,$yetki,$id_int);
		mysqli_stmt_execute($sql);
		mysqli_stmt_close($sql);
		if ($cep_tel!="") { uye_bilgi_guncelle("cep_tel", $cep_tel, $id); }
		if ($parola!="") {
		$parola_sorgu=mysqli_prepare($baglan,"update uyeler set parola=? where id=?");
		mysqli_stmt_bind_param($parola_sorgu,"si",$parola_kripto,$id_int);
		mysqli_stmt_execute($parola_sorgu);
		mysqli_stmt_close($parola_sorgu);
	}
		foreach ($uye_ekstra_bilgiler as $yaz => $x) {
			$eb_type=$x["tip"];
			$eb_name=$x["id"];
			$deger=$_POST["$eb_name"];
			$mevcut_deger=ekstra_bilgi_getir($eb_name,$id,$tip);
			if ($eb_type!="multiple_checkbox" and $eb_type!="upload") {
				
				if ($eb_type=="location") {
					$lat=$_POST["map_$eb_name-lat"];
					$lon=$_POST["map_$eb_name-lon"];
					$radius=$_POST["map_$eb_name-radius"];
					$zoom=$_POST["map_$eb_name-zoom"];
					$deger="$lat,$lon,$radius,$zoom";
				}
				
				if ($deger!=$mevcut_deger) {
					ekstra_bilgi_guncelle($eb_name,$deger,$id,$tip);
				}
			}
			else if ($eb_type=="multiple_checkbox") {
				foreach ($deger as $yaz) {	
					$diziden_cikart.="($yaz),";
				}
				if ($diziden_cikart!=$mevcut_deger) {
					ekstra_bilgi_guncelle($eb_name,$diziden_cikart,$id,$tip);
				}
				$diziden_cikart=null;
			}
			else if ($eb_type=="upload") {
				$eb_boyutlar=$x["boyutlar"];
				$eb_dizin=$x["dizin"];
				$eb_orijinali_sil=$x["orijinali_sil"];
				$eb_gecerli_boyut=$x["gecerli_boyut"];		
				
				// v2 resim upload remove güncellemesi başladı.
				$secenekler=array();
				if (is_array($eb_boyutlar)) {
					$secenekler["boyutlar"]=$eb_boyutlar;
				}
				if ($eb_dizin) {
					$secenekler["dizin"]=$eb_dizin;
				}
				if ($eb_orijinali_sil) {
					$secenekler["orijinali_sil"]=$eb_orijinali_sil;
				}
				if ($eb_gecerli_boyut) {
					$secenekler["cikti"]=$eb_gecerli_boyut;
				}
				$resim_dizisi=$_FILES["$eb_name"];
				$resim_adi=$resim_dizisi["name"];
				if ($resim_adi!="") {
					$resim_url=yukle($resim_dizisi,$secenekler);
					$mevcut_deger=ekstra_bilgi_getir($eb_name,$id,$tip);
					ekstra_bilgi_guncelle($eb_name,$resim_url,$id,$tip);
					if ($mevcut_deger!=$resim_url) {
						if ($eb_gecerli_boyut) {
							$mevcut_deger=thumbnail_url("full",$mevcut_deger,$eb_gecerli_boyut);
						}
						sil($mevcut_deger);
						foreach ($x["boyutlar"] as $yaz) {
							$thumbnail_url=thumbnail_url($yaz,$mevcut_deger);
							sil($thumbnail_url);
						}
					}
				}
			}
		}		
		// Mevcut upload resmi sil düğmesi
		@$upload_kaldir_dizisi=$_POST["upload_kaldir"];
		foreach ($upload_kaldir_dizisi as $name) {
			$mevcut_deger=ekstra_bilgi_getir($name,$id,$tip);
			if ($eb_gecerli_boyut) {
				$mevcut_deger=thumbnail_url("full",$mevcut_deger,$eb_gecerli_boyut);
			}
			sil($mevcut_deger);
			foreach ($eb_boyutlar as $yaz) {
				$thumbnail_url=thumbnail_url($yaz,$mevcut_deger);
				sil($thumbnail_url);
			}
			$ekstra_bilgi_id=ekstra_bilgi_id_getir($name,$id,$tip);
			ekstra_bilgi_sil($ekstra_bilgi_id);
		}
		// v2 resim upload remove güncellemesi bitti.
		
		$_SESSION["mesaj"]=array(_LANG('p_adi_ile_uye_guncellendi'), array($ad_soyad),"success");
		$aktif_tab=$_POST["aktif_tab"];
		if ($aktif_tab) {
			yonlendir("uyeler.php?islem=guncelle&id=$id&tab=$aktif_tab");
		} else {
			yonlendir("uyeler.php?islem=guncelle&id=$id");
		}
	}
}
if ($islem=="sil") {
	if ($id!=1 and $id!=$oturum_id) {
		$uye_ad_soyad=bilgi_getir("uyeler","ad_soyad",$id);
		$sil_id_int=(int)$id;
		$uye_sil=mysqli_prepare($baglan,"delete from uyeler where id=?");
		mysqli_stmt_bind_param($uye_sil,"i",$sil_id_int);
		mysqli_stmt_execute($uye_sil);
		mysqli_stmt_close($uye_sil);
		$ekstra_bilgi_sil=mysqli_prepare($baglan,"delete from ekstra_bilgi where id=? and tip=?");
		mysqli_stmt_bind_param($ekstra_bilgi_sil,"is",$sil_id_int,$tip);
		mysqli_stmt_execute($ekstra_bilgi_sil);
		mysqli_stmt_close($ekstra_bilgi_sil);
		$_SESSION["mesaj"]=array(_LANG('p_adi_ile_uye_silindi'), array($uye_ad_soyad),"success");
	} else {
		$_SESSION["mesaj"]=array(_LANG('p_kullanici_silinemez'),"danger");
	}
	yonlendir("uyeler.php");
}
if ($islem=="oturum_degistir") {
	$oturum_yetki=getir("uyeler","yetki",$oturum_id);
	if ($oturum_yetki=="admin") {
		$baz_tarih_saat=tarih_saat_hesapla("-15 minutes");
		$kayit_sil=mysqli_prepare($baglan,"delete from kayitlar where tip='oturum_degistir' and tarih_saat<=?");
  	mysqli_stmt_bind_param($kayit_sil,"s",$baz_tarih_saat);
  	mysqli_stmt_execute($kayit_sil);
  	mysqli_stmt_close($kayit_sil);
		if (ref_url()) {
			$redirect_url=ref_url();
		} else {
			$redirect_url="uyeler.php";
		}
		if ($id) {
			$login_uye_id=$id;
			$admin_oturum_id=$oturum_id;
			$kontrol=mysqli_fetch_array(mysqli_query($baglan,"select COUNT(id) from uyeler where id='$login_uye_id'"))[0];
			if ($kontrol==1 and $oturum_id!=$login_uye_id) {
				$kayit_kontrol=mysqli_fetch_array(mysqli_query($baglan,"select id from kayitlar where uye_id='$admin_oturum_id' and veri_1='$login_uye_id'"))[0];
				if (!$kayit_kontrol) {
					$benzersiz=benzersiz();
					session_destroy();
					setcookie("auto_login",null,-60*60*24*365*5,"/");
					session_start();
					$kayit_ekle=mysqli_prepare($baglan,"insert into kayitlar set veri_2=?, uye_id=?, veri_1=?, tip='oturum_degistir'");
  	$admin_oturum_id_int=(int)$admin_oturum_id;
  	$login_uye_id_int=(int)$login_uye_id;
  	mysqli_stmt_bind_param($kayit_ekle,"sii",$benzersiz,$admin_oturum_id_int,$login_uye_id_int);
  	mysqli_stmt_execute($kayit_ekle);
  	mysqli_stmt_close($kayit_ekle); 
					$_SESSION["oturum_id"]=$login_uye_id;
					$_SESSION["oturum_site_url"]=site_url();
					$_SESSION["oturum_degistir"]=array($benzersiz,$admin_oturum_id,$login_uye_id,$redirect_url);
					$admin_ad_soyad=getir("uyeler","ad_soyad",$admin_oturum_id);
					$uye_ad_soyad=getir("uyeler","ad_soyad",$login_uye_id);
					$admin_profil_url=lpanel_uye_profil_url($admin_oturum_id);
					$uye_profil_url=lpanel_uye_profil_url($login_uye_id);
							bildirim_ekle(_LANG('p_oturum_degistir_bildirim', array($admin_profil_url,$admin_ad_soyad,$uye_profil_url,$uye_ad_soyad)),null,0);
					yonlendir(site_url());
				} else {
					$baz_tarih_saat=tarih_saat_hesapla("-15 minutes");
					$tarih_saat=getir("kayitlar","tarih_saat",$kayit_kontrol);
					$gecen_dakika=kac_dk_gecti($tarih_saat);
					$kalan=15*60-tarih_fark($tarih_saat,tarih_saat(),array("birim"=>"saniye"));
					mesaj(_LANG('p_oturum_degistirme_uyari'), array($kalan),"danger");
					yonlendir($redirect_url);
				}
			} else {
				mesaj(_LANG('p_bir_hata_olusu'),"danger");
				yonlendir($redirect_url);
			}	
		}	else {
			mesaj(_LANG('p_bir_hata_olusu'),"danger");
			yonlendir($redirect_url);
		}
	} else {
		mesaj(_LANG('p_yetki_yok'),"danger");
		yonlendir($redirect_url);
	}
}
/* DATATABLE PASİF */
$datatable=ayar_getir("datatable");
if ($datatable==0) {
@$page=$_GET["p"];
@$kacarli_buton=$_POST["kacarli_buton"];
if ($kacarli_buton) {
	$kacarli=$_POST["kacarli"];
	$_SESSION["kacarli"]=$kacarli;
}
if (!isset($_SESSION["kacarli"])) {
	$kacarli=30;
} else {
	$kacarli=$_SESSION["kacarli"];
}
@$filtre_durum=$_GET["durum"];
@$filtre_sql=null;
if (isset($filtre_durum)) {
	$filtre_sql.=" durum='$filtre_durum'";
}
@$filtre_ara=$_GET["ara"];
if ($filtre_ara) {
	if (isset($filtre_durum)) {
		$filtre_sql.=" and ";
	}
	$filtre_sql.=" (ad_soyad like '%$filtre_ara%' or email_adresi like '%$filtre_ara%')";
}

if ($filtre_sql) {
	$filtre_sql_yaz="where $filtre_sql";
} else {
	$filtre_sql_yaz=null;
}

$toplam=mysqli_num_rows(mysqli_query($baglan,"select * from uyeler $filtre_sql_yaz"));
$kac_sayfa=ceil($toplam/$kacarli);
if ($page=="") {$page=1;}
$baslangic=($page-1)*$kacarli;
}
if ($datatable==0) {
$sql=mysqli_query($baglan,"select * from uyeler $filtre_sql_yaz order by id desc limit $baslangic, $kacarli");
} else {
$sql=mysqli_query($baglan,"select * from uyeler order by id desc");
}
/* DATATABLE PASİF */
?>
<!DOCTYPE HTML>
<html lang="<?php echo _DIL_HTML_LANG(); ?>" dir="<?php echo _DIL_HTML_YON(); ?>">
<head>
	<meta charset="UTF-8">
	<title><?php echo _LANG("p_uyeler"); ?> - <?php echo $lpanel_baslik; ?></title>
	<?php include("inc/header-kod.php"); ?>
	<?php if ($datatable!=0) { ?>
	<script>
	$(document).ready(function() {
		$('#listele_table').DataTable({
			"responsive": true,
			"lengthMenu": [ 15, 25, 50, 100 ],
			"language": {
				"decimal":        "",
				"emptyTable":     "<?php echo _LANG("p_sonuc_bulunamadi"); ?>",
				"info":           "<?php echo _LANG("p_toplam_uye_bulundu"); ?>",
				"infoEmpty":      "<?php echo _LANG("p_gosterilecek_uye_yok"); ?>",
				"infoFiltered":   "<?php echo _LANG("p_max_uye"); ?>",
				"infoPostFix":    "",
				"thousands":      ",",
				"lengthMenu":     "<?php echo _LANG("p_gosterilecek_uye"); ?>",
				"loadingRecords": "<?php echo _LANG("p_yukleniyor"); ?>",
				"processing":     "<?php echo _LANG("p_isleniyor"); ?>",
				"search":         "<?php echo _LANG("search"); ?>:",
				"zeroRecords":    "<?php echo _LANG("p_arama_uygun"); ?>",
				"paginate": {
					"first":      "<?php echo _LANG("p_ilk"); ?>",
					"last":       "<?php echo _LANG("p_son"); ?>",
					"next":       "<?php echo _LANG("p_sonraki"); ?>",
					"previous":   "<?php echo _LANG("p_onceki"); ?>"			
			}
			}
		});
	} );
	</script>
	<?php } ?>
	<meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
<div id="wrapper" class="<?php echo $kapsa_container; ?>">
	<div class="row">
		<div id="sidebar-wrapper" class="<?php echo $sidebar_div; ?>">
		<?php include("inc/sidebar.php"); ?>
		</div>
		<div id="page-content-wrapper" class="<?php echo $sag_div; ?>">
		<?php include("inc/ust.php"); ?>
			<div class="content">
			<?php if (!$islem){ ?>
			<h3 class="pull-left bolum-baslik text-uppercase"><?php echo _LANG('p_uyeler_basligi'); ?></h3>
			<?php if ($_SESSION["oturum_yetki"]=="admin") { ?><a href="uyeler.php?islem=ekle" class="btn btn-primary"><?php echo _LANG('p_uye_ekle_k'); ?></a><?php } ?>
			<?php if ($datatable==0) { ?>			
			<form class="form-inline pull-right kayit_goster visible-md visible-lg" action="" method="post">
			  <div class="form-group">
				<input type="text" class="form-control input-sm nofocus" id="kayit" name="kacarli" placeholder="<?php echo _LANG("p_listeleme_adedi"); ?>">
			  </div>
			  <button type="submit" name="kacarli_buton" class="btn btn-default btn-sm" value="Onayla"><?php echo _LANG('p_onayla'); ?></button>
			</form>
			<?php } ?>
			<div class="clearfix mb20"></div>
			<?php if ($datatable==0) { ?>
			<div class="filtre">
				<div class="row">
					<div class="col-md-45">
						<ul>
							<li class="filtrele"><?php echo _LANG('p_filtrele'); ?></li>
							<?php
							$filtre_durum_link_taban="uyeler.php?filtre=1";
							foreach ($uye_durumlari as $durum => $x) {
								$durum_baslik=$x["baslik"];
								if ($filtre_durum==$durum and isset($filtre_durum)) {$durum_active_yaz="class='active'";} else {$durum_active_yaz=null;}
								if (isset($filtre_ara)) {
									echo "<li $durum_active_yaz><a href='$filtre_durum_link_taban&durum=$durum&ara=$filtre_ara'>$durum_baslik</a></li>";
								} else {
									echo "<li $durum_active_yaz><a href='$filtre_durum_link_taban&durum=$durum'>$durum_baslik</a></li>";
								}
								
							}
							if (isset($filtre_ara)) {$filtre_durum_link_taban.="&ara=$filtre_ara";}
							if (isset($filtre_durum)) {echo "<li class='tumu'><a href='$filtre_durum_link_taban'>" . _LANG('p_filtreyi_kaldir') . "</a></li>";}
							?>
						</ul>
						<?php if (isset($filtre_ara)) {
						?>
						<ul>
							<li class="filtrele" style="margin-left:25px;"><?php echo _LANG('p_arama_sonuclari'); ?></li>
							<?php
								$filtre_ara_taban_link="uyeler.php?filtre=1";
								if (isset($filtre_durum)) {$filtre_ara_taban_link.="&durum=$filtre_durum";}
								echo "<li class='active'><a><i class='fa fa-search'></i>$filtre_ara</a></li>";
								echo "<li class='tumu'><a href='$filtre_ara_taban_link'>" . _LANG('p_filtreyi_kaldir') . "</a></li>";
							?>
						</ul>
						<?php } ?>
					</div>
					<div class="col-md-15">
						<form action="" method="get" class="form-inline ara_form pull-right">
						<?php if (isset($filtre_durum)) {echo "<input type='hidden' name='durum' value='$filtre_durum' />";} ?>
						<div class="input-group">
						  <input type="text" class="form-control input-sm nofocus" name="ara" placeholder="<?php echo _LANG("p_tum_uyelerde_ara"); ?>" <?php if ($filtre_ara) {echo "value='$filtre_ara'";} ?> required>
						  <span class="input-group-btn">
							<button type="submit" class="btn btn-default" value="Ara"><i class="fa fa-search mr0"></i></button>
						  </span>
						</div>

						</form>					
					</div>
				</div>
			</div>
			<?php } ?>			
			<div class="table-responsive">
			<table id="listele_table" class="table table-bordered table-hover" data-order='[[ 0, "desc" ]]' data-page-length='15'>
				<thead>
					<tr>
						<th width="10">#</th>
						<th><?php echo _LANG('p_ad_soyad_k'); ?></th> 
						<th><?php echo _LANG('p_email_adresi'); ?></th>
						<th><?php echo _LANG('p_cep_telefonu'); ?></th>
						<?php if (is_array($uye_ekstra_detaylar)) { foreach ($uye_ekstra_detaylar as $yaz => $x) {
							$detay_baslik=$x["baslik"];
							echo "<th>$detay_baslik</th> ";
						} } ?>				
						<th><?php echo _LANG('p_yetki'); ?></th>
						<th><?php echo _LANG('p_durum'); ?></th>
						<th><?php echo _LANG('p_uyelik'); ?></th>
						<th><?php echo _LANG('p_islemler'); ?></th>
					</tr>
				</thead>
				<tbody>
				<?php 
				while ($dizi=mysqli_fetch_array($sql)) {
					$id=$dizi["id"];
					$ad_soyad=$dizi["ad_soyad"];
					$email_adresi=$dizi["email_adresi"];$cep_tel=ekstra_bilgi_getir("cep_tel",$id,"uye");
					$parola=$dizi["parola"];
					$durum=$dizi["durum"];
					$yetki=$dizi["yetki"];
					$tarih_saat=$dizi["tarih_saat"];
				?>
					<tr> 
						<th scope="row"><?php echo $id; ?></th>
				    <td><a href="uyeler.php?islem=guncelle&id=<?php echo $id; ?>"><?php echo e($ad_soyad); ?></a> <?php if (function_exists('lpanel_uye_profil_link')) {$panel_profil_url=lpanel_uye_profil_url($id); echo "<a href='$panel_profil_url'><i class='fa fa-user'></i></a>";} ?> <a href="<?php echo profil_link_getir($id); ?>"><i class="fa fa-external-link"></i></a>
				    
						<?php if ($id!=1 and $id!=$oturum_id) { $g=isset($g)?$g+1:1; ?><a href="uyeler.php?islem=oturum_degistir&id=<?php echo $id; ?>" onclick="return confirm('<?php echo ejs(_LANG("p_oturum_ac_olarak_onay", array(e($ad_soyad)))); ?>')" data-toggle="tooltip" title="<?php echo e(_LANG("p_oturum_ac_olarak_baslik", array(e($ad_soyad)))); ?>"><i class="fa fa-sign-in fa-fw" aria-hidden="true"></i></a><?php } ?>

				    </td>
						<td><?php echo e($email_adresi); ?></td><?php echo "  "; ?><td><?php echo e($cep_tel); ?></td>
						<?php if (is_array($uye_ekstra_detaylar)) {foreach ($uye_ekstra_detaylar as $yaz => $x) {
							@$detay_name=$x["id"];
							@$detay_tip=$x["tip"];
							if ($detay_tip=="link") {
								$detay_url=$x["url"];
								$detay_title=$x["title"];
								$detay_img=$x["img"];
								if ($detay_img) {
									echo "<td><a href='$detay_url&uye_id=$id'><img src='$detay_img' style=''/></a></td>";
								} else {
									echo "<td><a href='$detay_url&uye_id=$id'>$detay_title</a></td>";
								}
							} else {
							$ekrana_yaz=uye_bilgi($detay_name,$id,$tip);
							echo "<td>$ekrana_yaz</td>";
							}
						} } ?>
						<td><?php echo $yetki; ?></td>
						<td><?php if ($durum==0) {echo "<span class='label label-warning'>" . _LANG('p_pasif') . "</span>";} else if ($durum==1) {echo "<span class='label label-success'>" . _LANG('p_aktif') . "</span>";} else if ($durum==2) {echo "<span class='label label-danger'>" . _LANG('p_banli') . "</span>";} ?></td>
						<td><?php echo $tarih_saat; ?></td>
						<td>
							<?php if ($yetki!="admin" or $oturum_yetki=="admin") { ?>
							<!-- Split button -->
							<div class="btn-group">
							  <a href="uyeler.php?islem=guncelle&id=<?php echo $id; ?>" class="btn btn-xs btn-primary"><i class="fa fa-pencil" aria-hidden="true"></i><?php echo _LANG('p_guncelle'); ?></a>
							  <button type="button" class="btn btn-primary btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								<span class="caret"></span>
								<span class="sr-only"><?php echo _LANG('p_toggle_dropdown'); ?></span>
							  </button>
							  <ul class="dropdown-menu dropdown-menu-right">
								<?php $g=0; if (function_exists('lpanel_uye_profil_url')) { $g=1; ?><li><a href="<?php echo lpanel_uye_profil_url($id); ?>"><i class="fa fa-tv fa-fw" aria-hidden="true"></i><?php echo _LANG('p_goruntule'); ?></a></li><?php } ?>
								<?php if ($id!=1 and $id!=$oturum_id) { $g=isset($g)?$g+1:1; ?><li><a href="uyeler.php?islem=sil&id=<?php echo $id; ?>" onclick="return confirm('<?php echo _LANG("p_emi_misiniz"); ?>')"><i class="fa fa-trash fa-fw" aria-hidden="true"></i><?php echo _LANG('p_sil'); ?></a></li><?php } ?>
								<?php if ($id!=1 and $id!=$oturum_id) { $g=isset($g)?$g+1:1; ?><li><a href="uyeler.php?islem=oturum_degistir&id=<?php echo $id; ?>" onclick="return confirm('<?php echo _LANG("p_oturum_ac_olarak_onay", array($ad_soyad)); ?>')"><i class="fa fa-sign-in fa-fw" aria-hidden="true"></i><?php echo e($ad_soyad); ?> <?php echo _LANG('p_giris_yap_olarak'); ?></a></li><?php } ?>
								<?php if ($g==0) {echo "<li><a href=''>" . _LANG('p_islem_yok') . "</a></li>";}?>
							  </ul>
							</div>
							<?php } ?>
						</td>
					</tr>
				<?php } ?>
				</tbody>
			</table>
			</div>
			<?php 
			
			if (!$filtre_sql) {
				$sayfalama_url_sabit="uyeler.php";
				panel_sayfalama($kac_sayfa,$page,1,$sayfalama_url_sabit."?p=");
			} else {
				$sayfalama_url_sabit="uyeler.php?filtre=1";
				if (isset($filtre_durum)) {$sayfalama_url_sabit.="&durum=$filtre_durum";} 
				if (isset($filtre_ara)) {$sayfalama_url_sabit.="&ara=$filtre_ara";} 
				panel_sayfalama($kac_sayfa,$page,1,$sayfalama_url_sabit."&p="); 
			} ?>
			
			
			<?php } else if ($islem=="ekle") { ?>	
			<h3 class="pull-left bolum-baslik text-uppercase"><?php echo _LANG('p_uye_ekle'); ?></h3>
			<a href="uyeler.php" class="btn btn-default"><?php echo _LANG('p_uyeler'); ?></a>
			<div class="clearfix mb20"></div>
			<form method="post" action="" enctype="multipart/form-data">
			<div class="panel panel-default">
				<div class="panel-body">
					<?php form_elemani("text","ad_soyad",_LANG('p_ad_soyad'),@$ad_soyad,null,null,"required"); ?>
					<label for="url"><?php echo _LANG('p_kalici_baglanti'); ?></label>
					<div class="row">
						<div class="col-md-30">
							<div class="input-group" style="margin-bottom:15px;">
								<div class="input-group-addon"><?php echo site_url(); ?>/profil/{ID}-</div>
								<input type="text" class="form-control" id="url" name="url" value="<?php echo @$url; ?>">
								<div class="input-group-addon">/</div>
							</div>								
						</div>
						<div class="col-md-30">
							<p class="help-block"><i class="fa fa-bell-o"></i><?php echo _LANG('p_url_oto_olusur'); ?></p>
						</div>
					</div>
					<?php form_elemani("email","email_adresi",_LANG('p_email_adresi'),@$email_adresi); ?>
								<?php form_elemani("text","cep_tel","Telefon",@$cep_tel); ?>
					<?php select_olustur("durum","Durum",$uye_durumlari,@$durum); ?>					
					<?php select_olustur("yetki",_LANG('p_yetki'),$uye_yetkileri,"kullanici"); ?>		
					<?php form_elemani("text","parola",_LANG("p_parola"),null,null,null,null,_LANG("p_parola_bos_ipucu")); ?>
				</div>
			</div>
				<?php if (count($uye_ekstra_bilgiler)>0) { 
				echo "<ul class='nav nav-tabs' role='tablist'>";
					$say=1;
					$tab_sayisi=0;
					foreach ($uye_ekstra_bilgiler as $yaz => $x) {
						$eb_type=$x["tip"];
						@$eb_name=$x["id"];
						@$eb_text=$x["baslik"];
						if ($say==1) {
							$active="class='active'";
						} else {$active="";}
						if ($eb_type=="tab_ac") {
							echo "<li role='presentation' $active><a href='#$eb_name' aria-controls='$eb_name' role='tab' data-toggle='tab'>$eb_text</a></li>";
							$tab_sayisi=$tab_sayisi+1;
						}
						$say++; 
					} 
					echo "</ul>";
					?>
					<div class="panel panel-default" style="border-top:0;border-radius:0;box-shadow:none;">
					<?php if ($tab_sayisi==0) {echo "<div class='panel-heading'>Ekstra Bilgiler</div>"; } ?>
					<div class="panel-body">
					<?php
					echo "<div class='tab-content'>";
					$tab_say=1;
					foreach ($uye_ekstra_bilgiler as $yaz => $x) {
					$eb_type=$x["tip"];
					@$eb_name=$x["id"];
					@$eb_text=$x["baslik"];
					@$eb_options=$x["secenekler"];
					@$eb_required=$x["gerekli"];
					@$eb_value=$x["deger"];
					@$eb_rows=$x["satir"];
					@$eb_standart=$x["standart"];
					@$eb_aciklama=$x["aciklama"];
					@$eb_class=$x["sinif"];
					@$eb_grup=$x["grup"];
					
					if ($eb_grup) {
						if ($eb_grup==2) {
							$grup_ac_kod="<div class='col-md-30'>";
						} else if ($eb_grup==3) {
							$grup_ac_kod="<div class='col-md-20'>";
						} else if ($eb_grup==4) {
							$grup_ac_kod="<div class='col-md-15'>";
						} else if ($eb_grup==5) {
							$grup_ac_kod="<div class='col-md-12'>";
						} else if ($eb_grup==6) {
							$grup_ac_kod="<div class='col-md-10'>";
						}
						$grup_kapat_kod="</div>";			
					} else {
						$grup_ac_kod=null;
						$grup_kapat_kod=null;
					}
					@$ayar_kosul=$x["kosul"];
					if ($ayar_kosul) {
						$kosul_parcala=explode(":",$ayar_kosul);
						$diger_ayar=$kosul_parcala[0];
						$kosul_kalan=$kosul_parcala[1];
						$kosul_kosul_parcala=explode("(",$kosul_kalan);
						$kosul_sart=$kosul_kosul_parcala[0];
						$kosul_deger=rtrim($kosul_kosul_parcala[1],")");
						echo "
						<script type='text/javascript'>
							$(function(){
								var kosul='$kosul_sart';
								var deger='$kosul_deger';
								var diger_ayar_value=$('#$diger_ayar').val();
								if (kosul=='==') {
									if (diger_ayar_value!=deger) {
										$('#form_$eb_name').hide();
									}
								} else if (kosul=='!=') {
									if (diger_ayar_value==deger) {
										$('#form_$eb_name').hide();
									}
								}
								$('body').on('change','#$diger_ayar',function(){
									var diger_ayar_value=$('#$diger_ayar').val();
									if (kosul=='==') {
										if (diger_ayar_value==deger) {
											$('#form_$eb_name').show(100);
										} else {
											$('#form_$eb_name').hide(100);
										}
									} else if (kosul=='!=') {
										if (diger_ayar_value!=deger) {
											$('#form_$eb_name').show(100);
										} else {
											$('#form_$eb_name').hide(100);
										}
									}
								});
							});
						</script>
						";
					}					
					echo $grup_ac_kod;
					$eb_yaz=ekstra_bilgi_getir($eb_name,$id,$tip);
					if (!$eb_yaz) {$eb_yaz=$eb_standart;}	
					if ($eb_type=="text" or $eb_type=="email" or $eb_type=="password" or $eb_type=="date" or $eb_type=="date_time") {
						form_elemani($eb_type,$eb_name,$eb_text,$eb_yaz,null,null,$eb_required,$eb_aciklama);
					} else if ($eb_type=="textarea") {
						if (!$eb_class) {$eb_class=null;}
						textarea_olustur($eb_class,$eb_name,$eb_text,$eb_yaz,$eb_rows,$eb_required,$eb_aciklama);
					} else if ($eb_type=="select") {
						select_olustur($eb_name,$eb_text,$eb_options,$eb_yaz,$eb_aciklama);
					} else if ($eb_type=="checkbox") {
						checkbox_olustur($eb_name,$eb_text,$eb_yaz,$eb_aciklama);
					} else if ($eb_type=="multiple_checkbox") {
						multiple_checkbox_olustur($eb_name,$eb_text,$eb_options,$eb_yaz,$eb_aciklama);
					} else if ($eb_type=="aciklama") {
						echo aciklama_olustur($eb_aciklama);
					} else if ($eb_type=="baslik") {
						echo baslik_olustur($eb_text,null,null,$eb_aciklama);
					} else if ($eb_type=="color") {
						colorpicker_olustur($eb_name,$eb_text,$eb_yaz,null,null,$eb_required,$eb_aciklama);
					} else if ($eb_type=="upload") {
						upload_olustur($eb_name,$eb_text,$eb_yaz,$eb_aciklama);
					} else if ($eb_type=="liste") {
						liste_link($eb_name,$eb_text,$eb_aciklama);
					} else if ($eb_type=="grup_ac") {
						grup_ac($eb_name,$eb_text,$eb_aciklama);
					} else if ($eb_type=="grup_kapat") {
						grup_kapat();
					} else if ($eb_type=="location") {
						location($eb_name,$eb_text,$eb_yaz,$eb_aciklama);
					} else if ($eb_type=="tab_ac") {
						if ($tab_say==1) {$active="active";} else {$active="";}
						tab_ac($eb_name,$eb_text,$active);
						$tab_say++;
					} else if ($eb_type=="tab_kapat") {
						tab_kapat();
					}
					echo $grup_kapat_kod;
					}
					echo "</div>";
					?>
					</div>
				</div>
			<?php } ?>				
			<?php form_buton("buton",_LANG('p_uye_ekle_k'),"primary"); ?>
			<?php form_elemani("hidden","aktif_tab",_LANG("p_aktif_tab")); ?>
			</form>	
			<?php } else if ($islem=="guncelle") { ?>	
			<h3 class="pull-left bolum-baslik text-uppercase"><?php echo _LANG('p_uye_guncelle'); ?></h3>
			<a href="uyeler.php?islem=ekle" class="btn btn-primary"><?php echo _LANG('p_uye_ekle_k'); ?></a>
			<a href="uyeler.php" class="btn btn-default"><?php echo _LANG('p_uyeler_anasayfa'); ?></a>
			<div class="clearfix mb20"></div>
			<form method="post" action="" enctype="multipart/form-data">
			<div class="panel panel-default">
				<div class="panel-heading"><?php echo bilgi_getir("uyeler","ad_soyad",$id); ?></div>
				<div class="panel-body">
					<?php form_elemani("text","ad_soyad",_LANG('p_ad_soyad'),bilgi_getir("uyeler","ad_soyad",$id),null,null,"required"); ?>
					<label for="url"><?php echo _LANG('p_kalici_baglanti'); ?></label>
					<div class="row">
						<div class="col-md-30">
							<div class="input-group" style="margin-bottom:15px;">
								<div class="input-group-addon"><?php echo site_url(); ?>/profil/<?php echo $id; ?>-</div>
								<input type="text" class="form-control" id="url" name="url" value="<?php echo bilgi_getir("uyeler","url",$id); ?>">
								<div class="input-group-addon">/</div>
							</div>								
						</div>
						<div class="col-md-30">
							<p class="help-block"><i class="fa fa-bell-o"></i><?php echo _LANG('p_url_oto_olusur'); ?></p>
						</div>
					</div>
								<?php form_elemani("text","cep_tel","Telefon",bilgi_getir("uyeler","cep_tel",$id)); ?>
					<?php form_elemani("email","email_adresi",_LANG('p_email_adresi'),bilgi_getir("uyeler","email_adresi",$id)); ?>			
					<?php select_olustur("durum","Durum",$uye_durumlari,bilgi_getir("uyeler","durum",$id)); ?>						
					<?php if ($_SESSION["oturum_yetki"]=="admin") {select_olustur("yetki",_LANG('p_yetki'),$uye_yetkileri,bilgi_getir("uyeler","yetki",$id));} ?>	
					<?php form_elemani("text","parola",_LANG("p_parola")); ?>
					<?php aciklama_olustur("<i class='fa fa-bell-o'></i>" . _LANG('p_parola_degistirme_ipucu')); ?>
				</div>
			</div>
				<?php if (count($uye_ekstra_bilgiler)>0) { 
				echo "<ul class='nav nav-tabs' role='tablist'>";
					$say=1;
					$tab_sayisi=0;
					foreach ($uye_ekstra_bilgiler as $yaz => $x) {
						$eb_type=$x["tip"];
						@$eb_name=$x["id"];
						@$eb_text=$x["baslik"];
						if ($say==1) {
							$active="class='active'";
						} else {$active="";}
						if ($eb_type=="tab_ac") {
							echo "<li role='presentation' $active><a href='#$eb_name' aria-controls='$eb_name' role='tab' data-toggle='tab'>$eb_text</a></li>";
							$tab_sayisi=$tab_sayisi+1;
						}
						$say++; 
					} 
					echo "</ul>";
					?>
					<div class="panel panel-default" style="border-top:0;border-radius:0;box-shadow:none;">
					<?php if ($tab_sayisi==0) {echo "<div class='panel-heading'>Ekstra Bilgiler</div>"; } ?>
					<div class="panel-body">
					<?php
					echo "<div class='tab-content'>";
					$tab_say=1;
					foreach ($uye_ekstra_bilgiler as $yaz => $x) {
					$eb_type=$x["tip"];
					@$eb_name=$x["id"];
					@$eb_text=$x["baslik"];
					@$eb_options=$x["secenekler"];
					@$eb_required=$x["gerekli"];
					@$eb_value=$x["deger"];
					@$eb_rows=$x["satir"];
					@$eb_standart=$x["standart"];
					@$eb_aciklama=$x["aciklama"];
					@$eb_class=$x["sinif"];
					@$eb_grup=$x["grup"];
					
					if ($eb_grup) {
						if ($eb_grup==2) {
							$grup_ac_kod="<div class='col-md-30'>";
						} else if ($eb_grup==3) {
							$grup_ac_kod="<div class='col-md-20'>";
						} else if ($eb_grup==4) {
							$grup_ac_kod="<div class='col-md-15'>";
						} else if ($eb_grup==5) {
							$grup_ac_kod="<div class='col-md-12'>";
						} else if ($eb_grup==6) {
							$grup_ac_kod="<div class='col-md-10'>";
						}
						$grup_kapat_kod="</div>";			
					} else {
						$grup_ac_kod=null;
						$grup_kapat_kod=null;
					}
					@$ayar_kosul=$x["kosul"];
					if ($ayar_kosul) {
						$kosul_parcala=explode(":",$ayar_kosul);
						$diger_ayar=$kosul_parcala[0];
						$kosul_kalan=$kosul_parcala[1];
						$kosul_kosul_parcala=explode("(",$kosul_kalan);
						$kosul_sart=$kosul_kosul_parcala[0];
						$kosul_deger=rtrim($kosul_kosul_parcala[1],")");
						echo "
						<script type='text/javascript'>
							$(function(){
								var kosul='$kosul_sart';
								var deger='$kosul_deger';
								var diger_ayar_value=$('#$diger_ayar').val();
								if (kosul=='==') {
									if (diger_ayar_value!=deger) {
										$('#form_$eb_name').hide();
									}
								} else if (kosul=='!=') {
									if (diger_ayar_value==deger) {
										$('#form_$eb_name').hide();
									}
								}
								$('body').on('change','#$diger_ayar',function(){
									var diger_ayar_value=$('#$diger_ayar').val();
									if (kosul=='==') {
										if (diger_ayar_value==deger) {
											$('#form_$eb_name').show(100);
										} else {
											$('#form_$eb_name').hide(100);
										}
									} else if (kosul=='!=') {
										if (diger_ayar_value!=deger) {
											$('#form_$eb_name').show(100);
										} else {
											$('#form_$eb_name').hide(100);
										}
									}
								});
							});
						</script>
						";
					}
					echo $grup_ac_kod;
					$eb_yaz=ekstra_bilgi_getir($eb_name,$id,$tip);
					if (!$eb_yaz) {$eb_yaz=$eb_standart;}	
					if ($eb_type=="text" or $eb_type=="email" or $eb_type=="password" or $eb_type=="date" or $eb_type=="date_time") {
						form_elemani($eb_type,$eb_name,$eb_text,$eb_yaz,null,null,$eb_required,$eb_aciklama);
					} else if ($eb_type=="textarea") {
						if (!$eb_class) {$eb_class=null;}
						textarea_olustur($eb_class,$eb_name,$eb_text,$eb_yaz,$eb_rows,$eb_required,$eb_aciklama);
					} else if ($eb_type=="select") {
						select_olustur($eb_name,$eb_text,$eb_options,$eb_yaz,$eb_aciklama);
					} else if ($eb_type=="checkbox") {
						checkbox_olustur($eb_name,$eb_text,$eb_yaz,$eb_aciklama);
					} else if ($eb_type=="multiple_checkbox") {
						multiple_checkbox_olustur($eb_name,$eb_text,$eb_options,$eb_yaz,$eb_aciklama);
					} else if ($eb_type=="aciklama") {
						echo aciklama_olustur($eb_aciklama);
					} else if ($eb_type=="baslik") {
						echo baslik_olustur($eb_text,null,null,$eb_aciklama);
					} else if ($eb_type=="color") {
						colorpicker_olustur($eb_name,$eb_text,$eb_yaz,null,null,$eb_required,$eb_aciklama);
					} else if ($eb_type=="upload") {
						upload_olustur($eb_name,$eb_text,$eb_yaz,$eb_aciklama);
					} else if ($eb_type=="liste") {
						liste_link($eb_name,$eb_text,$eb_aciklama);
					} else if ($eb_type=="grup_ac") {
						grup_ac($eb_name,$eb_text,$eb_aciklama);
					} else if ($eb_type=="grup_kapat") {
						grup_kapat();
					} else if ($eb_type=="location") {
						location($eb_name,$eb_text,$eb_yaz,$eb_aciklama);
					} else if ($eb_type=="tab_ac") {
						if ($tab_say==1) {$active="active";} else {$active="";}
						tab_ac($eb_name,$eb_text,$active);
						$tab_say++;
					} else if ($eb_type=="tab_kapat") {
						tab_kapat();
					}
					echo $grup_kapat_kod;
					}
					echo "</div>";
					?>
					</div>
				</div>
			<?php } ?>				
			<?php form_buton("buton",_LANG("p_uye_guncelle"),"primary"); ?>
			<?php form_elemani("hidden","aktif_tab",_LANG("p_aktif_tab")); ?>
			</form>	
			<?php } ?>
			</div>
			<div class="clearfix mb20"></div>
		</div>
	</div>
</div>
<?php include("inc/alt.php"); ?>
<?php include("inc/footer-kod.php"); ?>
</body>
</html>
