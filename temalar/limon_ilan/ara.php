<?php 
if(count(get_included_files())==1) {die();}
if (isset($s) and $s>0) {
	$ilan_tip=getir("icerik","tip",$s);
	if ($ilan_tip=="ilan") {
		yonlendir(link_getir($s));
	}
}

@$buton=$_POST["buton"];
@$sirala=$_GET["sirala"];
@$gorunum=$_GET["gorunum"];
@$islem=$_GET["islem"];
@$iptal=$_GET["iptal"];
@$kat=$_GET["kat"];

$filtre_sql=null;
$kategori_like_yaz =null;

if ($sirala) {
	$_SESSION["sirala"]=$sirala;
	if ($sirala=="gelismis") {
		yonlendir(kategori_link_getir($id));
	}
}
if ($gorunum and ($gorunum=="tablo" or $gorunum=="ikili")) {
	$_SESSION["gorunum"]=$gorunum;
}

if ($iptal) {
	unset($_SESSION["filtreler"][$iptal]);
	if ($iptal=="tab") {
		unset($_SESSION["tab"]);
	}
	yonlendir(site_url()."/ara/$s");
}
if ($islem=="filtre_temizle") {
	unset($_SESSION["filtreler"]);
	unset($_SESSION["tab"]);
	yonlendir(site_url()."/ara/$s");
}
if ($kat) {
	$kat_baslik=getir("kategoriler","baslik",$kat);
	$_SESSION["filtreler"]["kategori"]=array("id"=>"kategori","baslik"=>"Kategori","tip"=>"text","deger"=>$kat_baslik,"kat_id"=>$kat);
}

if (@$_POST["buton"]=="Filtrele") {
	unset($_SESSION["filtreler"]);
	@$_SESSION["kategori_filtreleri"]=$_POST["kategori_filtreleri"];
	$filtreler=$_SESSION["kategori_filtreleri"];
	$filtre_parcala=explode("\r\n",$filtreler);
	// Mevcut filtreler siliniyor
	
	@$_SESSION["filtre_kategori"]=$id;
	@$ozel_alanlar_dizi=diziye_donustur($kat_ozel_alanlar);
	$f=1;
	foreach($filtre_parcala as $key => $link) {
		if($link === '') { 
			unset($filtre_parcala[$key]); 
		} 
	}
	foreach ($filtre_parcala as $filtre) {
		$bol=explode("|",$filtre);
		$filtre_anahtar=$bol[0];	
		$filtre_baslik=$bol[1];	
		$filtre_tip=$bol[2];
		$secenekler=ozel_alan_secenekler($ozel_alanlar_dizi[$filtre_anahtar]);
		if (!$filtre_tip) {
			if (is_array($secenekler)) {
				$filtre_tip="select";
			} else {
				$filtre_tip="text";
			}
		}
		if ($filtre_tip=="min_max") {
			$filtre_degeri_min=$_POST[$filtre_anahtar."_min"];
			$filtre_degeri_max=$_POST[$filtre_anahtar."_max"];
			if ($filtre_anahtar!="0" and $filtre_degeri_min or $filtre_degeri_max) {
				$_SESSION["filtreler"][$filtre_anahtar]=array("id"=>$filtre_anahtar,"baslik"=>$filtre_baslik,"deger"=>$filtre_degeri,"tip"=>$filtre_tip,"deger_min"=>$filtre_degeri_min,"deger_max"=>$filtre_degeri_max);	
			}
		} else {
			$filtre_degeri=$_POST[$filtre_anahtar];
			if ($filtre_anahtar!="0" and $filtre_degeri) {
				$_SESSION["filtreler"][$filtre_anahtar]=array("id"=>$filtre_anahtar,"baslik"=>$filtre_baslik,"deger"=>$filtre_degeri,"tip"=>$filtre_tip,"deger_min"=>$filtre_degeri_min,"deger_max"=>$filtre_degeri_max);
			}
		}
		$filtre_tip=null;
		$filtre_degeri=null;
		$filtre_degeri_min=null;
		$filtre_degeri_max=null;
	$f++;}
	
	// Bölge Filtrelemeleri
	@$post_il=$_POST["il"];
	@$post_ilce=$_POST["ilce"];
	@$post_mahalle=$_POST["mahalle"];
	if ($post_il) {
		$post_il_adi=getir("iller","il_adi",$post_il);
		$_SESSION["filtreler"]["il"]=array("id"=>"il","baslik"=>_LANG('addr_region'),"deger"=>$post_il_adi,"tip"=>"text","deger_id"=>$post_il);
	}
	if ($post_ilce) {
		$post_ilce_adi=getir("ilceler","ilce_adi",$post_ilce);
		$_SESSION["filtreler"]["ilce"]=array("id"=>"ilce","baslik"=>_LANG('filter_district'),"deger"=>$post_ilce_adi,"tip"=>"text","deger_id"=>$post_ilce);
	}
	if ($post_mahalle) {
		$post_mahalle_adi=getir("mahalleler","mahalle_adi",$post_mahalle);
		$_SESSION["filtreler"]["mahalle"]=array("id"=>"mahalle","baslik"=>"Mahalle","deger"=>$post_mahalle_adi,"tip"=>"text","deger_id"=>$post_mahalle);
	}
	// Fiyat filtrelemesi
	$post_fiyat_min=nokta_sil($_POST["fiyat_min"]);
	$post_fiyat_max=nokta_sil($_POST["fiyat_max"]);
	if ($post_fiyat_min or $post_fiyat_max) {
		$_SESSION["filtreler"]["fiyat"]=array("id"=>"fiyat","baslik"=>"Fiyat","tip"=>"min_max","deger_min"=>$post_fiyat_min,"deger_max"=>$post_fiyat_max);
	}
}

// Filtreleme sorguları
if (@$_SESSION["filtreler"]) {
	if (@$id==$_SESSION["filtre_kategori"]) {
		if (is_array(@$filtre_parcala)) {
			foreach ($filtre_parcala as $filtre) {
			$bol=explode("|",$filtre);
			$filtre_anahtar=$bol[0];	
			$filtre_baslik=$bol[1];	
			$filtre_tip=$bol[2];
			
			$secenekler=ozel_alan_secenekler($ozel_alanlar_dizi[$filtre_anahtar]);
			if (!$filtre_tip) {
				if (is_array($secenekler)) {
					$filtre_tip="select";
				} else {
					$filtre_tip="text";
				}
			}
			if ($filtre_tip=="min_max") {
				$filtre_degeri_min=$_POST[$filtre_anahtar."_min"];
				$filtre_degeri_max=$_POST[$filtre_anahtar."_max"];
			} else {
				$filtre_degeri=$_POST[$filtre_anahtar];
			}
			if ($filtre_anahtar!="0") {
				if ($filtre_degeri or $filtre_degeri_min or $filtre_degeri_max) {
					$_SESSION["filtreler"][$filtre_anahtar]=array("id"=>$filtre_anahtar,"baslik"=>$filtre_baslik,"deger"=>$filtre_degeri,"tip"=>$filtre_tip,"deger_min"=>$filtre_degeri_min,"deger_max"=>$filtre_degeri_max);
					if ($filtre_tip=="text" or $filtre_tip=="select") {
						$filtre_sql.=" RIGHT JOIN ekstra_bilgi as e$f ON i.id=e$f.id and e$f.anahtar='$filtre_anahtar' and e$f.deger='$filtre_degeri' ";
					} else if ($filtre_tip=="min_max") {
						if ($filtre_degeri_min and $filtre_degeri_max) {
							$filtre_sql.=" RIGHT JOIN ekstra_bilgi as e$f ON i.id=e$f.id and e$f.anahtar='$filtre_anahtar' and convert(e$f.deger,decimal)>='$filtre_degeri_min' and convert(e$f.deger,decimal)<='$filtre_degeri_max'";
						} else {
							if ($filtre_degeri_min) {
								$filtre_sql.=" RIGHT JOIN ekstra_bilgi as e$f ON i.id=e$f.id and e$f.anahtar='$filtre_anahtar' and convert(e$f.deger,decimal)>='$filtre_degeri_min'";
							}
							if ($filtre_degeri_max) {
								$filtre_sql.=" RIGHT JOIN ekstra_bilgi as e$f ON i.id=e$f.id and e$f.anahtar='$filtre_anahtar' and convert(e$f.deger,decimal)<='$filtre_degeri_max'";
							}
						}
					}		
				}
			}
			$filtre_tip=null;
			$filtre_degeri=null;
			$filtre_degeri_min=null;
			$filtre_degeri_max=null;
			$f++;
			}		
		}		
		if (@$_SESSION["filtreler"]["il"]) {
			$il_id=$_SESSION["filtreler"]["il"]["deger_id"];
			$filtre_sql.=" RIGHT JOIN ekstra_bilgi as il ON i.id=il.id and il.anahtar='il' and il.deger='$il_id'";
		}
		if (@$_SESSION["filtreler"]["ilce"]) {
			$ilce_id=$_SESSION["filtreler"]["ilce"]["deger_id"];
			$filtre_sql.=" RIGHT JOIN ekstra_bilgi as ic ON i.id=ic.id and ic.anahtar='ilce' and ic.deger='$ilce_id'";
			
		}
		if (@$_SESSION["filtreler"]["mahalle"]) {
			$mahalle_id=$_SESSION["filtreler"]["mahalle"]["deger_id"];
			$filtre_sql.=" RIGHT JOIN ekstra_bilgi as mh ON i.id=mh.id and mh.anahtar='mahalle' and mh.deger='$mahalle_id'";
		}
		if (@$_SESSION["filtreler"]["kategori"]) {
			$kat_id=$_SESSION["filtreler"]["kategori"]["kat_id"];
			$kategori_like_yaz="and kategori like '%($kat_id)%'";
		} else {
			$kategori_like_yaz=null;
		}
		if (@$_SESSION["filtreler"]["fiyat"]) {
			$fiyat_min=$_SESSION["filtreler"]["fiyat"]["deger_min"];
			$fiyat_max=$_SESSION["filtreler"]["fiyat"]["deger_max"];
			if ($fiyat_min) {
				$filtre_sql.=" RIGHT JOIN ekstra_bilgi as fn ON i.id=fn.id and fn.anahtar='fiyat' and convert(fn.deger,decimal)>='$fiyat_min'";
			}
			if ($fiyat_max) {
				$filtre_sql.=" RIGHT JOIN ekstra_bilgi as fx ON i.id=fx.id and fx.anahtar='fiyat' and convert(fx.deger,decimal)<='$fiyat_max'";
			}			
		}
	} else {
		unset($_SESSION["filtreler"]);
	}
}
if (is_array(@$_SESSION["filtreler"])) {
$uygulanan_filtre_sayisi=count(@$_SESSION["filtreler"]);
}

$toplam_sql=mysqli_query($baglan,"
SELECT i.id, i.baslik, i.durum, i.tarih_saat, i.icerik, ef.deger from icerik as i 
INNER JOIN ekstra_bilgi as ef ON i.id=ef.id and ef.anahtar='fiyat' 
$filtre_sql
WHERE i.tip='ilan' and i.durum='1' and (i.baslik like '%$s%' or i.icerik like '%$s%') $kategori_like_yaz
");
$toplam_icerik=mysqli_num_rows($toplam_sql);

$toplam_magaza_sql=mysqli_query($baglan,"select * from magazalar where baslik like '%$s%' and durum='1'");
$toplam_magaza=mysqli_num_rows($toplam_magaza_sql);

// Kategorileri alıyoruz.

while ($ilan_kat_dizi=mysqli_fetch_array($toplam_sql)) {
	$icerik_id=$ilan_kat_dizi["id"];
	$icerik_kat=getir("icerik","kategori",$icerik_id);
	@$kategoriler.=$icerik_kat.",";
}
@$kategoriler=rtrim($kategoriler,",");

if (@$_SESSION["sirala"]) {
	if ($_SESSION["sirala"]=="tarih_yeni") {
		$orderby_sql="i.tarih_saat desc";
	} else if ($_SESSION["sirala"]=="tarih_eski") {
		$orderby_sql="i.tarih_saat asc";
	} else if ($_SESSION["sirala"]=="fiyat_artan") {
		$orderby_sql="convert(ef.deger, decimal) asc";
	} else if ($_SESSION["sirala"]=="fiyat_azalan") {
		$orderby_sql="convert(ef.deger, decimal) desc";
	} else {
		$orderby_sql="i.tarih_saat desc";
	}
} else {
	$orderby_sql="tarih_saat desc";	
}

@$sayfa=$_GET["sayfa"];
if (!$sayfa) {$sayfa=1;}
//$listeleme_adedi=1;
$listeleme_adedi=tema_ayar("listeleme_adedi");
$baslangic=($sayfa-1)*$listeleme_adedi;
$kac_sayfa=ceil($toplam_icerik/$listeleme_adedi);
$sql=mysqli_query($baglan,"
SELECT i.id, i.baslik, i.durum, i.tarih_saat, i.icerik, ef.deger from icerik as i 
INNER JOIN ekstra_bilgi as ef ON i.id=ef.id and ef.anahtar='fiyat' 
$filtre_sql
WHERE i.tip='ilan' and i.durum='1' and (i.baslik like '%$s%' or i.icerik like '%$s%') $kategori_like_yaz ORDER BY $orderby_sql limit $baslangic, $listeleme_adedi");
$toplam_ilan=mysqli_num_rows($sql);

tema_header(); ?>
<div class="container mt20 arsiv">
	<div class="row">
		<div class="col-md-9 sidebar">
			<?php if ($kategoriler) { ?>
			<div class="panel panel-default beyaz">
				<div class="panel-heading">Kategoriler</div>
				<div class="panel-body">
					<ul class="ana_menu scroll">
					<?php 

					$kategoriler_sql=mysqli_query($baglan,"select id, baslik, icerik from kategoriler where ust_kategori='0' and id in ($kategoriler)");
					while ($kategori_dizi=mysqli_fetch_array($kategoriler_sql)) {
						$kategori_id=$kategori_dizi["id"];
						$kategori_baslik=$kategori_dizi["baslik"];
						$kategori_ilan_sayisi=$kategori_dizi["icerik"];
						$kategori_link=kategori_link_getir($kategori_id);
						$site_url=site_url();
						echo "<li><a href='$site_url/ara/$s/?kat=$kategori_id'>$kategori_baslik</a>";
						$alt_kategori_sayisi=alt_kategori_sayisi($kategori_id);
						if ($alt_kategori_sayisi>0) {
							echo "<ul>";
							$alt_sql=mysqli_query($baglan,"select id, baslik, icerik from kategoriler where ust_kategori='$kategori_id' and id in ($kategoriler)");
							while ($alt_dizi=mysqli_fetch_array($alt_sql)) {
								$alt_id=$alt_dizi["id"];
								$alt_baslik=$alt_dizi["baslik"];
								$alt_ilan_sayisi=$alt_dizi["icerik"];
								$alt_link="$site_url/ara/$s/?kat=$alt_id";
								echo "<li><a href='$alt_link'>$alt_baslik</a></li>";
							}
							echo "</ul>";
						} else {
							echo "</li>";
						}
					}
					?>
					</ul>

				</div>
			</div>
			<?php } ?>
			<div id="filtrele" class="visible-md visible-lg">
				<form action="" method="post">
				<div class="panel panel-default beyaz filtre">
					<div class="panel-heading">Adres</div>
					<div class="panel-body">
						<?php $bolgesel=tema_ayar("bolgesel"); if ($bolgesel=="Hayır") { ?>
						<div id="il_sec" style="margin-bottom:10px;"><?php no_flash_select(" . _LANG('tx_il_seciniz') . "); ?></div>
						<?php } ?>
						<div id="ilce_sec" style="margin-bottom:10px;"></div>
						<div id="mahalle_sec"></div>
					</div>	
				</div>
				<div class="panel panel-default beyaz filtre">
					<div class="panel-heading">Fiyat</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-md-21">
								<input type="text" name="fiyat_min" id="fiyat_min" value="<?php echo @$_SESSION["filtreler"]["fiyat"]["deger_min"]; ?>" placeholder="min. TL" class="form-control fiyat_mask" data-a-sep="." data-a-dec="," />
							</div>
							<div class="col-md-21">
								<input type="text" name="fiyat_max" id="fiyat_max" value="<?php echo @$_SESSION["filtreler"]["fiyat"]["deger_max"]; ?>" placeholder="max. TL" class="form-control fiyat_mask" data-a-sep="." data-a-dec="," />
							</div>
						</div>
					</div>	
				</div>
				<button type="submit" name="buton" id="buton" class="btn btn-primary btn-block" value="Filtrele"><i class="fa fa-filter"></i>Filtrele</button>
				</form>
			</div>
		</div>
		<div class="col-md-33">
			<div class="panel panel-default beyaz sonuc visible-md visible-lg">
				<div class="panel-body">
					<span class="kategori fw500">"<?php echo $s; ?>"</span> aramasına uygun 
					<?php if ($toplam_icerik>0) { ?>
					<span class="sayi"><?php echo $toplam_icerik; ?></span> ilan <?php if ($toplam_magaza>0) {echo "ve <span class='sayi'>$toplam_magaza</span> mağaza";} ?> bulundu.
					<?php } else if ($toplam_icerik==0 and $toplam_magaza>0) {
						echo "<span class='sayi'>$toplam_magaza</span> mağaza bulundu.";
					} else { ?>
					</span> ilan bulunamadı.
					<?php } ?>
				</div>
			</div>
			<?php if (@$uygulanan_filtre_sayisi>0) { ?>
			<div class="panel panel-default beyaz sonuc uygulanan_filtreler">
				<div class="panel-body">
					<ul>
						<?php 
						foreach ($_SESSION["filtreler"] as $uf) {
							$uf_id=$uf["id"];
							$uf_baslik=$uf["baslik"];
							@$uf_deger=$uf["deger"];
							@$uf_tip=$uf["tip"];
							@$uf_min_deger=$uf["deger_min"];
							@$uf_max_deger=$uf["deger_max"];
							if ($uf_tip=="min_max") {
								if ($uf_min_deger) {
									$uf_deger.=" min. $uf_min_deger -";
								}
								if ($uf_max_deger) {
									$uf_deger.=" max. $uf_max_deger -";
								}
								$uf_deger=substr($uf_deger,0,-2);
							}
							$fil_url=site_url()."/ara/$s";
							echo "<li>$uf_baslik: <a href='$fil_url/?iptal=$uf_id'><span>$uf_deger<i class='ion-android-close'></i></span></a></li>";
							$uf_deger=null;
						}
						
						?>
						
						<?php if (@$uygulanan_filtre_sayisi>0) { ?><li class="temizle"><a href="<?php echo site_url(); ?>/ara/<?php echo $s; ?>/?islem=filtre_temizle"><span><i class="ion-android-close"></i><?php echo _LANG("filter_clear_all"); ?></span></a></li><?php } ?>
					</ul>
				</div>
			</div>	
			<?php } ?>
			<div class="panel panel-default beyaz tab mt15">
				<div class="panel-heading">
				  <ul class="nav nav-tabs ozel noselect visible-sm visible-md visible-lg" role="tablist">
					<li role="presentation" <?php if ($toplam_magaza==0 or $toplam_icerik>0) {echo "class='active'";} ?>><a href="#tumu" role="tab" data-toggle="tab">Tüm Sonuçlar <?php if ($toplam_icerik>0) {echo "<span class='badge'>$toplam_icerik</span>";} ?></a></li>
					<li role="presentation" <?php if ($toplam_icerik==0 and $toplam_magaza>0) {echo "class='active'";} ?>><a href="#magazalar" aria-controls="magazalar" role="tab" data-toggle="tab">Tüm Mağazalar <?php if ($toplam_magaza>0) {echo "<span class='badge'>$toplam_magaza</span>";} ?></a></li>
				  </ul>
				  <div class="sag">
					<ul class="listeleme noselect visible-sm visible-md visible-lg">
						<li><?php echo _LANG('hb_gorunum'); ?></li>
						<li <?php if (@$_SESSION["gorunum"]=="tablo" or !@$_SESSION["gorunum"]) {echo "class='active'";} ?>><a href="<?php echo site_url(); ?>/ara/?s=<?php echo $s; ?>&gorunum=tablo"><i class="glyphicon glyphicon-th-list" aria-hidden="true"></i></a></li>
						<li <?php if (@$_SESSION["gorunum"]=="ikili") {echo "class='active'";} ?>><a href="<?php echo site_url(); ?>/ara/?s=<?php echo $s; ?>&gorunum=ikili"><i class="glyphicon glyphicon-th" aria-hidden="true"></i></a></li>
					</ul>
					<select id="jump" name="jump">
					  <option value="<?php echo site_url(); ?>/ara/<?php echo $s; ?>/?sirala=gelismis" <?php if (@$_SESSION["sirala"]=="gelismis" or !@$_SESSION["sirala"]) {echo "selected='selected'";} ?>><?php echo _LANG("search_advanced"); ?></option>
					  <option value="<?php echo site_url(); ?>/ara/<?php echo $s; ?>/?sirala=fiyat_artan" <?php if (@$_SESSION["sirala"]=="fiyat_artan") {echo "selected='selected'";} ?>><?php echo _LANG("search_price_asc"); ?></option>
					  <option value="<?php echo site_url(); ?>/ara/<?php echo $s; ?>/?sirala=fiyat_azalan" <?php if (@$_SESSION["sirala"]=="fiyat_azalan") {echo "selected='selected'";} ?>><?php echo _LANG("search_price_desc"); ?></option>
					  <option value="<?php echo site_url(); ?>/ara/<?php echo $s; ?>/?sirala=tarih_yeni" <?php if (@$_SESSION["sirala"]=="tarih_yeni") {echo "selected='selected'";} ?>><?php echo _LANG("search_date_desc"); ?></option>
					  <option value="<?php echo site_url(); ?>/ara/<?php echo $s; ?>/?sirala=tarih_eski" <?php if (@$_SESSION["sirala"]=="tarih_eski") {echo "selected='selected'";} ?>><?php echo _LANG("search_date_asc"); ?></option>
					</select>
				  </div>
				</div>				
				<div class="tab-content">
					<div role="tabpanel" class="tab-pane fade <?php if ($toplam_magaza==0 or $toplam_icerik>0) {echo "in active";} ?>" id="tumu">
					<?php if ($toplam_ilan>0) {
					if (@$_SESSION["gorunum"]=="tablo" or !@$_SESSION["gorunum"]) {
						include("temalar/$aktif_tema/inc/kategori-listeleme-tablo.php");
					} else {
						include("temalar/$aktif_tema/inc/kategori-listeleme-ikili.php");
					}
					if ($kac_sayfa>1) {
						echo "<div class='clearfix mb20'></div>";
						echo "<div class='text-center'><ul class='pagination'>";
						$sayfa_url=search_link_getir($s);
						if ($sayfa>1) {
							$onceki_sayfa=$sayfa-1;
							echo "<li><a href='$sayfa_url/$onceki_sayfa' aria-label='Önceki'><i class='ion-chevron-left'></i></a></li>";
						}		
						for ($x=1;$x<=$kac_sayfa;$x++) {
							$bosluk=ceil($kac_sayfa/20)*2.2;
							if (($x==1 or $x==$kac_sayfa or ($x<$sayfa-$bosluk and $x%$bosluk==0)) or ($x>$sayfa-4 and $x<$sayfa+4) or ($x>$sayfa+$bosluk and $x%$bosluk==0)) {
								if ($x==$sayfa) {$active="class='active'";$link_yaz="";} else {$active="";$link_yaz="href='$sayfa_url/$x'";}
								echo "<li $active><a $link_yaz>$x</a></li>";
							}
						}
						if ($sayfa<$kac_sayfa) {
							$sonraki_sayfa=$sayfa+1; 
							echo "<li><a href='$sayfa_url/$sonraki_sayfa' aria-label='Önceki'><i class='ion-chevron-right'></i></a></li>";
						}
						echo "</ul></div>";
					}
					} else { 
						if (@$uygulanan_filtreler) { 
							echo "<div class='alert alert-danger'>Kriterlerinize uygun ilan bulunamadı.</div>";
						} else {
							echo "<div class='alert alert-danger'>İlan bulunamadı.</div>";
						}
					} ?>
					</div>
					<div role="tabpanel" class="tab-pane fade <?php if (($toplam_icerik==0 and $toplam_magaza>0)) {echo "in active";} ?>" id="magazalar">
						<?php if ($toplam_magaza>0) { ?>
						<div class="panel-body">
							<div class="row">
							<?php 
							$x=1;
							while ($magaza_dizi=mysqli_fetch_array($toplam_magaza_sql)) {
								$magaza_id=$magaza_dizi["id"];
								$magaza_baslik=$magaza_dizi["baslik"];
								$magaza_link=magaza_link_getir($magaza_id);
								$magaza_resim=thumbnail_url("150_150",magaza_bilgi("profil_gorseli",$magaza_id));
								if ($magaza_resim=="-150_150.") {
									$magaza_resim=tema_dizini()."/img/yok/150_150.png";
								}
								if ($x%2==0) {$sol_sag_yaz="sag";} else {$sol_sag_yaz="sol";}
								$magaza_tarih_saat=tarih_saat_bicimlendir($magaza_dizi["tarih_saat"]);
								$magaza_ilan_sayisi=mysqli_num_rows(mysqli_query($baglan,"SELECT i.id, i.baslik, i.durum, i.tarih_saat, i.yazar from icerik as i 
								INNER JOIN ekstra_bilgi as em ON i.id=em.id and em.anahtar='magaza' and em.deger='$magaza_id' 
								WHERE i.tip='ilan' and i.durum='1'"));
								$magaza_tel=magaza_bilgi("magaza_tel",$magaza_id);
							?>
							<div class="col-md-21 <?php echo $sol_sag_yaz; ?>">
							<a href="<?php echo $magaza_link; ?>">
								<div class="ikili">
									<div class="row">
										<div class="col-md-13">
											<img src="<?php echo $magaza_resim; ?>" alt="<?php echo $magaza_baslik; ?>" class="resim img-responsive"/>
										</div>
										<div class="col-md-29">
											<div class="baslik" title="<?php echo $magaza_baslik; ?>"><?php echo $magaza_baslik; ?></div>
											<div class="fiyat">
												<?php echo $magaza_ilan_sayisi; ?> ilan
											</div>						
											<div class="eb"><span class="anahtar"><?php echo _LANG("account_reg_date"); ?></span><span class="deger"><?php echo $magaza_tarih_saat; ?></span></div>
											<?php if ($magaza_tel) {?><div class="eb"><span class="anahtar"><?php echo _LANG("store_contact"); ?></span><span class="deger"><?php echo $magaza_tel; ?></span></div><?php } ?>
										</div>
									</div>
								</div>
							</a>
							</div>
							<?php $x++;}  ?>
							</div>
						</div>
						<?php } else {
							echo "<div class='alert alert-danger'>Mağaza bulunamadı.</div>";
						} ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php tema_footer(); ?>
<?php 
$gonder=null;
if (@$_SESSION["filtreler"]["il"]) {
	$il_id=$_SESSION["filtreler"]["il"]["deger_id"];
	$gonder.="&il=$il_id";
}
if (@$_SESSION["filtreler"]["ilce"]) {
	$ilce_id=$_SESSION["filtreler"]["ilce"]["deger_id"];
	$gonder.="&ilce=$ilce_id";
}
if (@$_SESSION["filtreler"]["mahalle"]) {
	$mahalle_id=$_SESSION["filtreler"]["mahalle"]["deger_id"];
	$gonder.="&mahalle=$mahalle_id";
} 
?>
<script type="text/javascript" src="<?php echo tema_dizini(); ?>/js/autoNumeric.js"></script>
<script type="text/javascript">
$(function(){
<?php if ($bolgesel=="Hayır" or !$bolgesel) { ?>
$("#il_sec").load("<?php echo tema_dizini(); ?>/ajax.php?islem=il_sec&tip=filtre<?php echo $gonder; ?>");
<?php } else { $ana_sehir=tema_ayar("ana_sehir"); ?>
$("#ilce_sec").load("<?php echo tema_dizini(); ?>/ajax.php?islem=ilce_sec&ust=<?php echo $ana_sehir; ?>&tip=filtre<?php echo $gonder; ?>");
<?php } ?>
$('[data-toggle="tooltip"]').tooltip();
$('.fiyat_mask').autoNumeric('init',{mDec:0});
});
</script>