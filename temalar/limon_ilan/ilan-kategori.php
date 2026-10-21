<?php 
if(count(get_included_files())==1) {die();}
$kategori_baslik=_DIL_BASLIK(getir("kategoriler","baslik",$id), $id, "kategori");
$kategori_ilan_sayisi=getir("kategoriler","icerik",$id);
$kategori_link=kategori_link_getir($id);

kategori_icerik_sayisi_guncelle($id);

@$buton=$_POST["buton"];
@$sirala=$_GET["sirala"];
@$gorunum=$_GET["gorunum"];
@$iptal=$_GET["iptal"];
@$islem=$_GET["islem"];

@$f_il=$_GET["f_il"];
@$f_ilce=$_GET["f_ilce"];
@$f_mahalle=$_GET["f_mahalle"];

$filtre_sql=null;
$filtreler_value=null;
$tab_dizi=array();
$vitrin_dizi=array();

if ($f_il or $f_ilce or $f_mahalle) {
	unset($_SESSION["filtreler"]["il"]);
	unset($_SESSION["filtreler"]["ilce"]);
	unset($_SESSION["filtreler"]["mahalle"]);
	if ($f_il) {
		$f_il_adi=getir("iller","il_adi",$f_il);
		$_SESSION["filtreler"]["il"]=array("id"=>"il","baslik"=>_LANG('addr_region'),"deger"=>$f_il_adi,"tip"=>"text","deger_id"=>$f_il);
	}
	if ($f_ilce) {
		$f_ilce_adi=getir("ilceler","ilce_adi",$f_ilce);
		$_SESSION["filtreler"]["ilce"]=array("id"=>"ilce","baslik"=>_LANG('filter_district'),"deger"=>$f_ilce_adi,"tip"=>"text","deger_id"=>$f_ilce);
	}
	if ($f_mahalle) {
		$f_mahalle_adi=getir("mahalleler","mahalle_adi",$f_mahalle);
		$_SESSION["filtreler"]["mahalle"]=array("id"=>"mahalle","baslik"=>"Mahalle","deger"=>$f_mahalle_adi,"tip"=>"text","deger_id"=>$f_mahalle);
	}
	$_SESSION["filtre_kategori"]=$id;
	yonlendir(kategori_link_getir($id));
}

if ($sirala) {
	$_SESSION["sirala"]=$sirala;
	if ($sirala=="gelismis") {
		yonlendir(kategori_link_getir($id));
	}
}
if ($gorunum and ($gorunum=="tablo" or $gorunum=="ikili")) {
	$_SESSION["gorunum"]=$gorunum;
}
$ust_kategoriler=ust_kategoriler($id);
$ust_kategori_sayisi=count($ust_kategoriler);

$filtre_kategoriler=$ust_kategoriler;
$filtre_kategoriler[]=$id;
foreach ($filtre_kategoriler as $kat) {
	$kat_filtreler_sor=kategori_bilgi("filtreler",$kat);
	$kat_ozel_alanlar_sor=kategori_bilgi("ozel_alanlar",$kat);
	$kat_detaylar_sor=kategori_bilgi("detaylar",$kat);
	$kat_resimsiz_sor=kategori_bilgi("resimsiz",$kat);
	$kat_tab_sor=kategori_bilgi("tab",$kat);
	if ($kat_filtreler_sor) {
		$kat_filtreler=$kat_filtreler_sor;
	}
	if ($kat_ozel_alanlar_sor) {
		$kat_ozel_alanlar=$kat_ozel_alanlar_sor;
	}	
	if ($kat_detaylar_sor) {
		$kat_detaylar=$kat_detaylar_sor;
	}
	if ($kat_tab_sor) {
		$kat_tablar=$kat_tab_sor;
	}
	if ($kat_resimsiz_sor) {
		$kat_resimsiz=$kat_resimsiz_sor;
	}
}
if (@$kat_detaylar) {
	@$kat_detaylar=diziye_donustur($kat_detaylar);
} else {
	$kat_detaylar=array();
}

@$tab=$_GET["tab"];
if ($tab) {
	$tab_deger=$_GET["ara"];
	if ($tab!="tumu") {
		$_SESSION["tab"]=array("id"=>$tab,"deger"=>$tab_deger,"kategori"=>$id);
	} else {
		unset($_SESSION["tab"]);
		unset($_SESSION["tab"]["kategori"]);
		$tab_filtre_id=$_SESSION["tab_filtre"]["id"];
		unset($_SESSION["filtreler"]["$tab_filtre_id"]);
		unset($_SESSION["tab_filtre"]);
		yonlendir(kategori_link_getir($id));
	}
}
if (@$_SESSION["tab"]["kategori"]!=$id) {
	unset($_SESSION["tab"]);
}
if ($iptal) {
	unset($_SESSION["filtreler"][$iptal]);
	if ($iptal=="tab") {
		unset($_SESSION["tab"]);
	}
	yonlendir(kategori_link_getir($id));
}
if ($islem=="filtre_temizle") {
	unset($_SESSION["filtreler"]);
	unset($_SESSION["tab"]);
	yonlendir(kategori_link_getir($id));
}

if (@$kat_tablar) {
	$parcala=explode("\r\n",$kat_tablar);
	foreach ($parcala as $parca) {
		@$parca_bol=explode("|",$parca);
		@$parca_anahtar=$parca_bol[0];
		@$parca_deger=$parca_bol[1];
		@$parca_baslik=$parca_bol[2];
		@$parca_ikon=$parca_bol[3];
		$tab_dizi[$parca_deger]=array("id"=>$parca_anahtar,"baslik"=>$parca_baslik,"deger"=>$parca_deger,"ikon"=>$parca_ikon);
	}
}

if (@$_SESSION["tab"] and @$_SESSION["tab"]["id"]!="tumu") {
	$tab_id=$_SESSION["tab"]["id"];
	$tab_deger=$_SESSION["tab"]["deger"];
	$tab_baslik=$tab_dizi[$tab_deger]["baslik"];
	$_SESSION["tab_filtre"]=array("id"=>"$tab_id","baslik"=>$tab_baslik,"deger"=>$tab_deger,"tip"=>"text");
	$_SESSION["filtreler"]["$tab_id"]=array("id"=>"$tab_id","baslik"=>$tab_baslik,"deger"=>$tab_deger,"tip"=>"text");
	$_SESSION["filtre_kategori"]=$id;
	$filtre_sql.="RIGHT JOIN ekstra_bilgi as et ON i.id=et.id and et.anahtar='$tab_id' and et.deger='$tab_deger' ";
}

if (@$_POST["buton"]=="Filtrele" or (is_array(@$_SESSION["filtreler"]) and $id==$_SESSION["filtre_kategori"])) {
	if (@$_POST["buton"]=="Filtrele") {
		unset($_SESSION["filtreler"]);
	}
	
	$filtreler=$_SESSION["kategori_filtreleri"];
	
	$filtre_parcala=explode("\r\n",$filtreler);
	// Mevcut filtreler siliniyor
	
	$_SESSION["filtre_kategori"]=$id;
	if (@$kat_ozel_alanlar) {
		$ozel_alanlar_dizi=diziye_donustur($kat_ozel_alanlar);
	} else {
		$ozel_alanlar_dizi=array();
	}
	
	$f=1;
	foreach($filtre_parcala as $key => $link) {
		if($link === '') { 
			unset($filtre_parcala[$key]); 
		} 
	}
	foreach ($filtre_parcala as $filtre) {
		$bol=explode("|",$filtre);
		$filtre_anahtar=$bol[0];	
		$filtre_baslik=spec_etiket($bol[1]);	
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
			@$filtre_degeri_min=$_POST[$filtre_anahtar."_min"];
			@$filtre_degeri_max=$_POST[$filtre_anahtar."_max"];
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
	$post_fiyat_min=nokta_sil(strip_tags($_POST["fiyat_min"]));
	$post_fiyat_max=nokta_sil(strip_tags($_POST["fiyat_max"]));
	if ($post_fiyat_min or $post_fiyat_max) {
		$_SESSION["filtreler"]["fiyat"]=array("id"=>"fiyat","baslik"=>"Fiyat","tip"=>"min_max","deger_min"=>$post_fiyat_min,"deger_max"=>$post_fiyat_max);
	}
}

/*
echo "<pre>";
print_r($_SESSION["filtreler"]);
echo "</pre>";
*/

// Filtreleme sorguları
if (@$_SESSION["filtreler"]) {
	if ($id==$_SESSION["filtre_kategori"]) {
		if (is_array(@$_SESSION["filtreler"])) {
			foreach ($_SESSION["filtreler"] as $filtre) {
			$bol=explode("|",$filtre);
			$filtre_anahtar=$filtre["id"];	
			$filtre_baslik=$filtre["baslik"];	
			$filtre_tip=$filtre["tip"];	
			
			$secenekler=ozel_alan_secenekler($ozel_alanlar_dizi[$filtre_anahtar]);
			if (!$filtre_tip) {
				if (is_array($secenekler)) {
					$filtre_tip="select";
				} else {
					$filtre_tip="text";
				}
			}
			if ($filtre_tip=="min_max") {
				$filtre_degeri_min=$filtre["deger_min"];
				$filtre_degeri_max=$filtre["deger_max"];
			} else {
				$filtre_degeri=$filtre["deger"];
			}

			if ($filtre_anahtar!="0" and $filtre_anahtar!="il" and $filtre_anahtar!="ilce" and $filtre_anahtar!="mahalle" and $filtre_anahtar!="fiyat") {
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
		unset($_SESSION["filtre_kategori"]);
	}
}

@$uygulanan_filtre_sayisi=count((array)$_SESSION["filtreler"]);

$sql_kategori=sql_kategori($id);
$kv_ilan_sayisi=tema_ayar("kv_ilan_sayisi");
if (!$kv_ilan_sayisi) {
	$kv_ilan_sayisi=5;
}

$kategori_vitrini_sql=mysqli_query($baglan,"SELECT i.id from icerik as i INNER JOIN dopingler as dp ON i.id=dp.ilan_id and dp.doping='kategori_vitrini' and dp.durum='1' and i.durum='1' $filtre_sql where $sql_kategori order by rand() limit 0, $kv_ilan_sayisi");
$kategori_vitrini_sayisi=mysqli_num_rows($kategori_vitrini_sql);
if ($kategori_vitrini_sayisi>0) {
$haric_idler=null;
while ($dizi=mysqli_fetch_array($kategori_vitrini_sql)) {
	$vitrin_id=$dizi["id"];
	$vitrin_dizi[]=$vitrin_id;
	$haric_idler.="$vitrin_id,";
}
$haric_idler=rtrim($haric_idler,",");
} else {
	$haric_idler=0;
}
$toplam_sql=mysqli_query($baglan,"
SELECT i.id, i.baslik, i.durum, i.tarih_saat, ef.deger from icerik as i 
LEFT JOIN ekstra_bilgi as ef ON i.id=ef.id and ef.anahtar='fiyat' 
$filtre_sql
WHERE i.tip='ilan' and i.durum='1' and $sql_kategori and i.id not in ($haric_idler)
");
$toplam_icerik=mysqli_num_rows($toplam_sql);

$toplam_gosterilen_icerik=$toplam_icerik+$kategori_vitrini_sayisi;

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
SELECT i.id, i.baslik, i.durum, i.tarih_saat, ef.deger from icerik as i 
LEFT JOIN ekstra_bilgi as ef ON i.id=ef.id and ef.anahtar='fiyat' 
$filtre_sql
WHERE i.tip='ilan' and i.durum='1' and $sql_kategori and i.id not in ($haric_idler) ORDER BY $orderby_sql limit $baslangic, $listeleme_adedi");
$toplam_ilan=mysqli_num_rows($sql);

$cache=tema_ayar("cache");
if ($cache==1 and !@$oturum_id and !$_POST and !@$_GET["tab"] and !@$_GET["sirala"] and !@$_GET["gorunum"]) {
	$cache_klasor="cache/kategori/$id";
	$cache_dosya_adi="sayfa-$sayfa";
	$cache_dosyasi= "$cache_klasor/$cache_dosya_adi-cache.html";
	$kategori_cache_sure=tema_ayar("kategori_cache_sure");
	$cache_sure=$kategori_cache_sure*60;
	if ($cache_sure>0) {
		if (file_exists($cache_dosyasi) and (time() - ($cache_sure) < filemtime($cache_dosyasi))) {
			include_once($cache_dosyasi);
			exit;
		} else {
			ob_start();
		}
	}		
}

tema_header(); ?>

<div class="container mt20 arsiv">
	<?php 
	$kategori_ust_gorsel=kategori_bilgi("ust_gorsel",$id);
	$kategori_ust_gorsel_url=kategori_bilgi("ust_gorsel_url",$id);
	if ($kategori_ust_gorsel) {
		if ($kategori_ust_gorsel_url) {
			echo "<a href='$kategori_ust_gorsel_url'><img src='$kategori_ust_gorsel' alt='$kategori_baslik' class='mb20 img-responsive' style='border-radius:2px;'/></a>";
		} else {
			echo "<img src='$kategori_ust_gorsel' alt='$kategori_baslik' class='mb20 img-responsive' style='border-radius:2px;'/>";
		}
	} ?>

	<div class="row">
		<div class="col-md-9 sidebar">
			<div class="panel panel-default beyaz">
				<div class="panel-body" id="mm_panel_copy">
					<ul class="ana_menu scroll">
					<?php 
					$ilan_sayilari_ayar=tema_ayar("ilan_sayilari");
					if ($ust_kategori_sayisi>0) {
						$x=1;
						foreach ($ust_kategoriler as $ust) {
							$ust_link=kategori_link_getir($ust);
							$ust_baslik=_DIL_BASLIK(getir("kategoriler","baslik",$ust), $ust, "kategori");
							$ust_ilan_sayisi=getir("kategoriler","icerik",$ust);
							if ($ilan_sayilari_ayar==2 or $ilan_sayilari_ayar==3) {
								$ust_ilan_sayisi*=$ilan_sayilari_ayar;
							}							
							$ust_kategoriler_detayli[$x]=array("id"=>$ust,"baslik"=>$ust_baslik,"link"=>$ust_link);
							echo "<li><a href='$ust_link'>$ust_baslik</a><span class='toplam'>($ust_ilan_sayisi)</span>
								<ul>
							";
						$x++;}
					}
					if ($ilan_sayilari_ayar==2 or $ilan_sayilari_ayar==3) {
						$kategori_ilan_sayisi*=$ilan_sayilari_ayar;
					}
					echo "<li><a href='$kategori_link'>$kategori_baslik</a><span class='toplam'>($kategori_ilan_sayisi)</span>";
					$alt_kategori_sayisi=alt_kategori_sayisi($id);
					if ($alt_kategori_sayisi>0) {
						echo "<ul>";
						
						$kategori_siralama=tema_ayar("kategori_siralama");
						if ($kategori_siralama=="baslik_a_z") {
							$alt_sql=mysqli_query($baglan,"select id, baslik, icerik from kategoriler where ust_kategori='$id' order by baslik asc");
						}	else if ($kategori_siralama=="icerik_sayisi") {
							$alt_sql=mysqli_query($baglan,"select id, baslik, icerik from kategoriler where ust_kategori='$id' order by icerik desc, baslik");
						}	else {
							$alt_sql=mysqli_query($baglan,"select id, baslik, icerik from kategoriler where ust_kategori='$id'");
						}
						
						while ($alt_dizi=mysqli_fetch_array($alt_sql)) {
							$alt_id=$alt_dizi["id"];
							$alt_baslik=_DIL_BASLIK($alt_dizi["baslik"], $alt_id, "kategori");
							$alt_ilan_sayisi=$alt_dizi["icerik"];
							if ($ilan_sayilari_ayar==2 or $ilan_sayilari_ayar==3) {
								$alt_ilan_sayisi*=$ilan_sayilari_ayar;
							}							
							$alt_link=kategori_link_getir($alt_id);
							echo "<li><a href='$alt_link'>$alt_baslik</a><span class='toplam'>($alt_ilan_sayisi)</span></li>";
						}
						echo "</ul>";
					} else {
						echo "</li>";
					}
					if ($ust_kategori_sayisi>0) {
						foreach ($ust_kategoriler as $ust) {
							echo "</ul>";
						}
					}
					?>
					</ul>
				</div>
			</div>
			
			<button id="filtre_panel_buton" class="btn btn-danger btn-block visible-xs visible-sm mb15"><i class="fa fa-filter"></i><?php echo _LANG("filter_button"); ?></button>
		
			<form action="" method="post" class="filtre_sm_hide">
			<div class="filtre_mobil_scroll scroll">
				<div class="panel panel-default beyaz filtre">
					<div class="panel-heading">Adres</div>
					<div class="panel-body">
					
						<?php $bolgesel=tema_ayar("bolgesel"); if ($bolgesel=="Hayır") { ?>
						<div id="il_sec" style="margin-bottom:10px;"><?php no_flash_select(_LANG('tx_il_seciniz')); ?></div>
						<?php } ?>
						<div id="ilce_sec" style="margin-bottom:10px;"></div>
						<div id="mahalle_sec"></div>
					</div>	
				</div>
				<div class="panel panel-default beyaz filtre">
					<div class="panel-heading">Fiyat</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-xs-21">
								<input type="text" name="fiyat_min" id="fiyat_min" value="<?php echo @$_SESSION["filtreler"]["fiyat"]["deger_min"]; ?>" placeholder="min. TL" class="form-control fiyat_mask" data-a-sep="." data-a-dec="," />
							</div>
							<div class="col-xs-21">
								<input type="text" name="fiyat_max" id="fiyat_max" value="<?php echo @$_SESSION["filtreler"]["fiyat"]["deger_max"]; ?>" placeholder="max. TL" class="form-control fiyat_mask" data-a-sep="." data-a-dec="," />
							</div>
						</div>
					</div>	
				</div>			
				
				<?php 		
				if (@$kat_filtreler) {
					$filtreler_dizi=diziye_donustur($kat_filtreler);
					$ozel_alanlar_dizi=diziye_donustur($kat_ozel_alanlar);
					
					@$say=count($filtreler_dizi);
					if ($say>0) {
						foreach ($filtreler_dizi as $filtre) {
							$filtre_parcala=explode("|",$filtre);
							$filtre_anahtar=$filtre_parcala[0];
							$filtre_baslik=isset($filtre_parcala[1]) ? $filtre_parcala[1] : null;
							@$filtre_tip=$filtre_parcala[2];
							$secenekler=ozel_alan_secenekler($ozel_alanlar_dizi[$filtre_anahtar]);
							if (!$filtre_tip) {
								if (is_array($secenekler)) {
									$filtre_tip="select";
								} else {
									$filtre_tip="text";
								}
							}
							$filtreler_value.="$filtre_anahtar|$filtre_baslik|$filtre_tip\r\n";
							$_SESSION["kategori_filtreleri"]=$filtreler_value;
							if (!$filtre_baslik) {
								$filtre_baslik=$filtre_anahtar;
							}
							if (@$_SESSION["filtreler"][$filtre_anahtar]) {
								$acik_kapali="acik";
							} else {
								$acik_kapali="kapali";
							}
							?>
							
							<div class="panel panel-default beyaz filtre kapali <?php echo $acik_kapali; ?>">
								<a id="toggle"><div class="panel-heading"><?php echo $filtre_baslik; ?></div></a>
								<div class="panel-body">
								  
								<?php if ($filtre_tip=="select") { ?>
								<div class="form-group">
									<select class="form-control" id="<?php echo $filtre_anahtar; ?>" name="<?php echo $filtre_anahtar; ?>" placeholder="<?php echo $filtre_baslik; ?>">
									<option value=""><?php echo _LANG("filter_select"); ?></option>
									<?php foreach ($secenekler as $secenek) {
										$filtre_secili=(isset($_SESSION["filtreler"][$filtre_anahtar]["deger"]) ? $_SESSION["filtreler"][$filtre_anahtar]["deger"] : null);
										if ($filtre_secili==$secenek) {$selected_yaz="selected='selected'";} else {$selected_yaz=null;}
										echo "<option value='$secenek' $selected_yaz>$secenek</option>";
									} ?>
									</select>
								</div>
								<?php } else if ($filtre_tip=="text") { ?>
								<div class="form-group">
									<input type="text" class="form-control" id="<?php echo $filtre_anahtar; ?>" name="<?php echo $filtre_anahtar; ?>" value="<?php echo isset($_SESSION["filtreler"][$filtre_anahtar]["deger"]) ? $_SESSION["filtreler"][$filtre_anahtar]["deger"] : ''; ?>" placeholder="<?php echo $filtre_baslik; ?>"/>
								</div>
								<?php } else if ($filtre_tip=="min_max") { ?>
								<div class="row">
									<div class="col-xs-21">
										<input type="text" name="<?php echo $filtre_anahtar; ?>_min" id="<?php echo $filtre_anahtar; ?>_min" value="<?php echo @$_SESSION["filtreler"][$filtre_anahtar]["deger_min"]; ?>" placeholder="min." class="form-control"/>
									</div>
									<div class="col-xs-21">
										<input type="text" name="<?php echo $filtre_anahtar; ?>_max" id="<?php echo $filtre_anahtar; ?>_max" value="<?php echo @$_SESSION["filtreler"][$filtre_anahtar]["deger_max"]; ?>" placeholder="max." class="form-control"/>
									</div>
								</div>
								<?php } ?>
								
								</div>	
							</div>
							<?php 
						}
					}
				}
				?>
			</div>
			<button type="submit" name="buton" id="buton" class="btn btn-primary btn-block" value="Filtrele"><i class="fa fa-filter"></i>Filtrele</button>
			</form>
		
		</div>
		<div class="col-md-33">
			<div class="panel panel-default beyaz sonuc visible-md visible-lg">
				<div class="panel-body">
					<span class="kategori">
					<?php if ($ust_kategori_sayisi>0) {
						$x=1; 
						foreach ($ust_kategoriler_detayli as $ust) {
							$ust_id=$ust["id"];
							$ust_baslik=$ust["baslik"];
							$ust_link=$ust["link"];
							echo "<a href='$ust_link'>$ust_baslik</a>";
						$x++;}
					} ?><a href="<?php echo kategori_link_getir($id); ?>"><?php echo _DIL_BASLIK(getir("kategoriler","baslik"), $id, "kategori"); ?></a>
					</span> <?php echo _LANG('search_kategorisinde'); ?>
					<?php if (@$uygulanan_filtre_sayisi>0) {echo _LANG('kriterlere_uygun_kisa');} ?>
					<?php if ($toplam_gosterilen_icerik>0) { ?>
					<span class="sayi"><?php echo $toplam_gosterilen_icerik; ?></span> <?php echo _LANG('cat_ads_found'); ?>
					<?php } else { ?>
					</span> <?php echo _LANG('listing_not_found'); ?>
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
							@$uf_min_deger=number_format($uf["deger_min"],0,",",".");
							@$uf_max_deger=number_format($uf["deger_max"],0,",",".");
							if ($uf_tip=="min_max") {
								if ($uf_min_deger) {
									$uf_deger.=" min. $uf_min_deger -";
								}
								if ($uf_max_deger) {
									$uf_deger.=" max. $uf_max_deger -";
								}
								$uf_deger=substr($uf_deger,0,-2);
							}
							$fil_url=kategori_link_getir($id);
							echo "<li>$uf_baslik: <a href='$fil_url/?iptal=$uf_id'><span>$uf_deger<i class='ion-android-close'></i></span></a></li>";
							$uf_deger=null;
						}
						
						?>
						
						<?php if ($uygulanan_filtre_sayisi>0) { ?><li class="temizle"><a href="<?php echo kategori_link_getir($id); ?>?islem=filtre_temizle"><span><i class="ion-android-close"></i><?php echo _LANG("filter_clear_all"); ?></span></a></li><?php } ?>
					</ul>
				</div>
			</div>	
			<?php } ?>

			
			<div class="panel panel-default beyaz tab mt15">
				<div class="panel-heading">
				  <ul class="nav nav-tabs ozel noselect visible-sm visible-md visible-lg" role="tablist">
					<?php $tab_taban_url=kategori_link_getir($id);
					$tab_sayisi=count($tab_dizi);
					if ($tab_sayisi>0) {
						$tumu_url="$tab_taban_url?tab=tumu";
					} else {
						$tumu_url="#";
					}
					?>
					<li role="presentation" <?php if (!@$_SESSION["tab"] or @$_SESSION["tab"]["id"]=="tumu" or $tab_sayisi==0) {echo "class='active'";} ?>><a href="<?php echo $tumu_url; ?>"><?php echo _LANG("search_all_listings"); ?></a></li>
					<?php
					$kod=null;
					foreach ($tab_dizi as $tab) {
						$tab_id=$tab["id"];
						$tab_deger=$tab["deger"];
						$tab_baslik=$tab["baslik"];
						$tab_ikon=$tab["ikon"];
						if (@$_SESSION["tab"]["id"]==$tab_id and @$_SESSION["tab"]["deger"]==$tab_deger) {$active_yaz="class='active'";} else {$active_yaz=null;}
						if ($tab_ikon) {$tab_ikon_yaz="<i class='$tab_ikon'></i>";} else {$tab_ikon_yaz=null;}
						$kod.="<li role='presentation' $active_yaz><a href='$tab_taban_url?tab=$tab_id&ara=$tab_deger'>$tab_ikon_yaz$tab_deger</a></li>";
					}
					echo $kod;
					?>
				  </ul>
				  <div class="sag">
				  <?php if (!@$kat_resimsiz) { ?>
					<ul class="listeleme noselect visible-sm visible-md visible-lg">
						<li><?php echo _LANG('hb_gorunum'); ?></li>
						<li <?php if (@$_SESSION["gorunum"]=="tablo" or !@$_SESSION["gorunum"]) {echo "class='active'";} ?>><a href="<?php echo kategori_link_getir($id); ?>?gorunum=tablo"><i class="glyphicon glyphicon-th-list" aria-hidden="true"></i></a></li>
						<li <?php if (@$_SESSION["gorunum"]=="ikili") {echo "class='active'";} ?>><a href="<?php echo kategori_link_getir($id); ?>?gorunum=ikili"><i class="glyphicon glyphicon-th" aria-hidden="true"></i></a></li>
					</ul>
				  <?php } ?>
					<select id="jump" name="jump">
					  <option value="<?php echo kategori_link_getir($id); ?>?sirala=gelismis" <?php if (@$_SESSION["sirala"]=="gelismis" or !@$_SESSION["sirala"]) {echo "selected='selected'";} ?>><?php echo _LANG("search_advanced"); ?></option>
					  <option value="<?php echo kategori_link_getir($id); ?>?sirala=fiyat_artan" <?php if (@$_SESSION["sirala"]=="fiyat_artan") {echo "selected='selected'";} ?>><?php echo _LANG("search_price_asc"); ?></option>
					  <option value="<?php echo kategori_link_getir($id); ?>?sirala=fiyat_azalan" <?php if (@$_SESSION["sirala"]=="fiyat_azalan") {echo "selected='selected'";} ?>><?php echo _LANG("search_price_desc"); ?></option>
					  <option value="<?php echo kategori_link_getir($id); ?>?sirala=tarih_yeni" <?php if (@$_SESSION["sirala"]=="tarih_yeni") {echo "selected='selected'";} ?>><?php echo _LANG("search_date_desc"); ?></option>
					  <option value="<?php echo kategori_link_getir($id); ?>?sirala=tarih_eski" <?php if (@$_SESSION["sirala"]=="tarih_eski") {echo "selected='selected'";} ?>><?php echo _LANG("search_date_asc"); ?></option>
					</select>
				  </div>
				  
				</div>
				
				<?php
				if (!@$kat_resimsiz) {
					if (@$_SESSION["gorunum"]=="tablo" or !@$_SESSION["gorunum"]) {
						include("temalar/$aktif_tema/inc/kategori-listeleme-tablo.php");
					} else {
						include("temalar/$aktif_tema/inc/kategori-listeleme-ikili.php");
					}					
				} else {
					include("temalar/$aktif_tema/inc/kategori-listeleme-resimsiz.php");
				}

				if ($kac_sayfa>1) {
					echo "<div class='clearfix mb20'></div>";
					echo "<div class='text-center'><ul class='pagination'>";
					$sayfa_url=kategori_link_getir($_GET["id"]);
					if ($sayfa>1) {
						$onceki_sayfa=$sayfa-1;
						echo "<li><a href='$sayfa_url/$onceki_sayfa' aria-label='<?php echo _LANG('f_onceki'); ?>'><i class='ion-chevron-left'></i></a></li>";
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
						echo "<li><a href='$sayfa_url/$sonraki_sayfa' aria-label='<?php echo _LANG('f_sonraki'); ?>'><i class='ion-chevron-right'></i></a></li>";
					}
					echo "</ul></div>";
				}
				?>

				<?php if ($toplam_ilan==0) {
					if (@$uygulanan_filtreler) { 
						echo "<div class='alert alert-danger mt20'>" . _LANG('kriterlere_uygun_ilan_bulunamadi') . "</div>";
					} else {
						echo "<div class='alert alert-danger mt15'>" . _LANG('listing_not_found') . "</div>";
					}
				} ?>
			</div>

			<?php 
			$kategori_listeleme_alt=tema_ayar("kategori_listeleme_alt");
			if ($kategori_listeleme_alt) {echo "<div class='mb10'>$kategori_listeleme_alt</div>";}
			?>
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

$("#filtre_panel_buton").click(function(){
	$(".filtre_sm_hide").slideToggle(500);	
});

</script>
<?php 
if (!$oturum_id and empty($_SESSION["mesaj"]) and !empty($cache_klasor) and isset($cache_dosya_adi)) {
	klasor_olustur($cache_klasor);
	$cache_dosyasi= "$cache_klasor/$cache_dosya_adi-cache.html";
	touch($cache_dosyasi);
	$ch = fopen($cache_dosyasi, 'w');
	fwrite($ch, ob_get_contents());
	fclose($ch);
	ob_end_flush();
}
?>