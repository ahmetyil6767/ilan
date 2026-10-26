<?php 
if(count(get_included_files())==1) {die();}
if (!$oturum_id) {
	$_SESSION["mesaj"]=array(_LANG('msj_uye_girisi_gerekli'),"danger");
	yonlendir(ozel_url("giris-yap"));
	exit();
}

// null
$limit_alert_kod=null;

// ��lan limitleri
$limit_politikasi=limit_politikasi();

// $_GET
@$kat=mysqli_real_escape_string($baglan,$_GET["kat"]);
@$ilan=mysqli_real_escape_string($baglan,$_GET["ilan"]);
@$magaza_id=mysqli_real_escape_string($baglan,$_GET["magaza"]);

// SE�Ŏ�M YAP
if ($magaza_id) {
	$magaza_adi=getir("magazalar","baslik",$magaza_id);
	$magaza_kategori=getir("magazalar","kategori",$magaza_id);
	$magaza_kategori_adi=_DIL_BASLIK(getir("kategoriler","baslik",$magaza_kategori), $magaza_kategori, "kategori");
	$magaza_durum=getir("magazalar","durum",$magaza_id);
	$ilan_eklenebilir_magazalar=ilan_eklenebilir_magazalar($oturum_id);
	if (!in_array($magaza_id,$ilan_eklenebilir_magazalar)) {
		$_SESSION["mesaj"]=array(_LANG('msj_magazada_yetkiniz_yok'),"danger");
		yonlendir(ozel_url("ilan-ekle"));
	}
	if ($magaza_durum!=1) {
		$_SESSION["mesaj"]=array(_LANG('msj_magaza_onaylanmadi'),"danger");
		yonlendir(ozel_url("ilan-ekle"));
	}
	if ($magaza_kategori) { $kat=$magaza_kategori; }
}
if ($kat) {
	$alt_kat_sayisi = alt_kategori_sayisi($kat);
	if ($alt_kat_sayisi > 0) {
		$_SESSION["mesaj"]=array(_LANG('msj_kategoride_ilan_eklenemez'),"danger");
		yonlendir(ozel_url("ilan-ekle"));
	}
}
@$buton=$_POST["buton"];

// FORM ���?LEMLER��
if ($buton==_LANG('tx_ilani_kaydet') or $buton==_LANG('tx_ilani_yayinla') or $buton==_LANG('tx_hemen_yayinla')) {
	@$baslik=trim($_POST["baslik"]);
	@$icerik=addslashes($_POST["icerik"]);
	@$kategori_dizisi=$_POST["kategori"];
	@$url=addslashes($_POST["url"]);
	
	$kategori='';
	if (is_array($kategori_dizisi)) {
		foreach ($kategori_dizisi as $yaz) {
			if ($yaz!=_LANG('p_select')) {
				$kategori.=",($yaz)";
			}
		}
		$kategori=ltrim($kategori,",");		
	}
	
	if ($url=="") {
		$url=url_olustur($baslik);
	}
	
	@$durum=0;
	if ($buton==_LANG('tx_ilani_yayinla')) {$durum=2;}
	if ($buton==_LANG('tx_hemen_yayinla')) {$durum=1;}
	
	@$fiyat=trim($_POST["fiyat"]);
	@$fiyat_usd=trim($_POST["fiyat_usd"]);
	@$fiyat_eur=trim($_POST["fiyat_eur"]);
	@$fiyat_gbp=trim($_POST["fiyat_gbp"]);
	@$para_birimi=trim($_POST["para_birimi"]);
	@$iletisim=trim($_POST["iletisim"]);
	@$yetkili=trim($_POST["yetkili"]);
	@$yetkili_tel=trim($_POST["yetkili_tel"]);
	
	$ozel_alanlar_str=null;
	foreach ($_POST as $key => $value) {
		if (substr($key, 0, 3)=="oa_") {
			$alan_anahtar=substr($key, 3);
			$alan_deger=addslashes($value);
			if ($alan_deger!="") {
				$ozel_alanlar_str.="$alan_anahtar|$alan_deger,";
			}
		}
	}
	if ($ozel_alanlar_str) {
		$ozel_alanlar_str=rtrim($ozel_alanlar_str,",");
	}
	
	$tarih_saat=tarih_saat();
	$yazar=$oturum_id;
	
	if ($baslik!="" and $kategori!="" and $icerik!="") {
		$kalan_ilan_limit=(int)uye_ilan_limiti("kalan",$oturum_id);
		$kategori_ilan_limit=(int)uye_ilan_limiti("kategori",$oturum_id);
		$kategori_ilan_sayisi=(int)mysqli_num_rows(mysqli_query($baglan,"select * from icerik where yazar='$oturum_id' and durum='1' and kategori like '%($kategori_dizisi[0])%'"));
		$kategori_kalan_ilan_limit=$kategori_ilan_limit-$kategori_ilan_sayisi;
		$ilan_aninda_onay=(int)uye_ilan_limiti("aninda_onay",$oturum_id);
		
		$limit_kontrol=1;
		if ($limit_politikasi==1) {
			if ($kategori_kalan_ilan_limit<=0) {
				$_SESSION["mesaj"]=array(_LANG('pn_kategori_ilan_limitiniz_doldugu_icin_bu_ilani_yayinlayamazsiniz'),"danger");
				$limit_kontrol=0;
			}
			if ($kalan_ilan_limit<=0) {
				$_SESSION["mesaj"]=array(_LANG('pn_ilan_limitiniz_doldugu_icin_bu_ilani_yayinlayamazsiniz'),"danger");
				$limit_kontrol=0;
			}
		}
		
		if ($limit_kontrol==1) {
			if ($durum==1 and $ilan_aninda_onay!=1) {
				$durum=2;
			}
			
			$sql=mysqli_query($baglan,"insert into icerik set baslik='$baslik', url='$url', icerik='$icerik', kategori='$kategori', tarih_saat='$tarih_saat', yazar='$yazar', durum='$durum', tip='ilan'");
			$eklenen_id=mysqli_insert_id($baglan);
			
			if ($eklenen_id) {
				foreach ($kategori_dizisi as $kat_yaz) {
					kategori_icerik_sayisi_guncelle($kat_yaz);
				}
				
				$ekstra_bilgiler=array(
					"fiyat"=>$fiyat,
					"fiyat_usd"=>$fiyat_usd,
					"fiyat_eur"=>$fiyat_eur,
					"fiyat_gbp"=>$fiyat_gbp,
					"para_birimi"=>$para_birimi,
					"iletisim"=>$iletisim,
					"yetkili"=>$yetkili,
					"yetkili_tel"=>$yetkili_tel,
					"ozel_alanlar"=>$ozel_alanlar_str
				);
				
				foreach ($ekstra_bilgiler as $anahtar => $deger) {
					if ($deger!=="") {
						ekstra_bilgi_guncelle($anahtar,$deger,$eklenen_id,"icerik");
					}
				}
				
				if ($magaza_id) {
					ekstra_bilgi_guncelle("magaza",$magaza_id,$eklenen_id,"icerik");
					limit_dusur($eklenen_id);
				}
				
				$tip_gorsel_dizin="ajax";
				$resim_boyutlandir=array("160_120","530_393");
				
				if (isset($_FILES["gorsel"]) and $_FILES["gorsel"]["name"][0]!="") {
					$secenekler=array();
					$secenekler["boyutlar"]=$resim_boyutlandir;
					$secenekler["dizin"]=$tip_gorsel_dizin;
					$secenekler["orijinali_sil"]=1;
					$secenekler["cikti"]="full";
					
					$resim_dizisi=$_FILES["gorsel"];
					$resim_url=yukle($resim_dizisi,$secenekler);
					if ($resim_url) {
						ekstra_bilgi_guncelle("gorsel",$resim_url,$eklenen_id,"icerik");
					}
				}
				
				if (isset($_FILES["galeri"]) and $_FILES["galeri"]["name"][0]!="") {
					$secenekler=array();
					$secenekler["boyutlar"]=$resim_boyutlandir;
					$secenekler["dizin"]=$tip_gorsel_dizin;
					$secenekler["orijinali_sil"]=1;
					$secenekler["cikti"]="galeri";
					
					$resim_dizisi=$_FILES["galeri"];
					$resim_url=yukle($resim_dizisi,$secenekler);
					if ($resim_url) {
						ekstra_bilgi_guncelle("galeri",$resim_url,$eklenen_id,"icerik");
					}
				}
				
				$_SESSION["mesaj"]=array(_LANG('pn_ilan_basariyla_eklendi'),"success");
				
				if ($durum==1) {
					$_SESSION["mesaj"]=array(_LANG('pn_ilan_basariyla_yayinlandi'),"success");
				} elseif ($durum==2) {
					$_SESSION["mesaj"]=array(_LANG('pn_ilan_inceleme_icin_gonderildi'),"success");
				}
				
				yonlendir(link_getir($eklenen_id));
			}
		}
	} else {
		if ($baslik=="") $_SESSION["mesaj"]=array(_LANG('msj_baslik_gerekli'),"danger");
		if ($kategori=="") $_SESSION["mesaj"]=array(_LANG('msj_kategori_gerekli'),"danger");
		if ($icerik=="") $_SESSION["mesaj"]=array(_LANG('msj_icerik_gerekli'),"danger");
	}
}

// Kategori agaci (cascade icin) - PHP -> JS
$kat_agac_sql=mysqli_query($baglan,"select id,baslik,ust_kategori from kategoriler where tip='ilan' order by baslik asc");
$kat_children=array();
$kat_baslik=array();
$kat_baslik_en=array();
$kat_baslik_fr=array();
$kat_baslik_ar=array();
while ($kd=mysqli_fetch_array($kat_agac_sql)) {
    $kat_children[(int)$kd['ust_kategori']][]=(int)$kd['id'];
    $kat_baslik[(int)$kd['id']]=$kd['baslik'];
    // Çeviriler _DIL_BASLIK fonksiyonu ile alınır (ekstra_bilgi tablosunda baslik_en/fr/ar)
    $kat_baslik_en[(int)$kd['id']]=_DIL_BASLIK($kd['baslik'], $kd['id'], 'kategori', 'en');
    $kat_baslik_fr[(int)$kd['id']]=_DIL_BASLIK($kd['baslik'], $kd['id'], 'kategori', 'fr');
    $kat_baslik_ar[(int)$kd['id']]=_DIL_BASLIK($kd['baslik'], $kd['id'], 'kategori', 'ar');
}
$kat_agac_json=json_encode(array('children'=>$kat_children,'baslik'=>$kat_baslik,'baslik_en'=>$kat_baslik_en,'baslik_fr'=>$kat_baslik_fr,'baslik_ar'=>$kat_baslik_ar), JSON_UNESCAPED_UNICODE);

// Secili yol (ust -> alt) hesapla ($kat varsa)
$secili_yol=array();
$cur=(int)$kat;
$guard=0;
while ($cur>0 && $guard<30) {
	$secili_yol[]=$cur;
	$ust=(int)getir('kategoriler','ust_kategori',$cur);
	if ($ust<=0 || $ust==$cur) { break; }
	$cur=$ust;
	$guard++;
}
$secili_yol=array_reverse($secili_yol);
$secili_yol_json=json_encode($secili_yol, JSON_UNESCAPED_UNICODE);

tema_header(); ?>
<style>
.wiz-progress { display:flex; justify-content:space-between; margin:0 0 24px; padding:0; list-style:none; position:relative; }
.wiz-progress::before { content:''; position:absolute; top:18px; left:0; right:0; height:3px; background:#e6eaf2; z-index:0; }
.wiz-progress li { position:relative; z-index:1; text-align:center; flex:1; }
.wiz-progress .dot { width:36px; height:36px; line-height:36px; border-radius:50%; background:#fff; border:2px solid #c9d3ea; color:#999; font-weight:700; margin:0 auto 6px; transition:all .25s; }
.wiz-progress li.active .dot { background:#1a2b7a; border-color:#1a2b7a; color:#fff; box-shadow:0 0 0 4px rgba(26,43,122,.15); }
.wiz-progress li.done .dot { background:#2d7a3e; border-color:#2d7a3e; color:#fff; }
.wiz-progress .lbl { font-size:12px; color:#888; font-weight:500; }
.wiz-progress li.active .lbl { color:#1a2b7a; font-weight:700; }
.wiz-step { display:none; }
.wiz-step.active { display:block; animation:fadeIn .25s; }
@keyframes fadeIn { from{opacity:0;transform:translateY(6px);} to{opacity:1;transform:none;} }
.wiz-nav { display:flex; justify-content:space-between; margin-top:18px; padding-top:14px; border-top:1px solid #eef1f8; }
.wiz-nav .btn { border-radius:8px; padding:9px 20px; font-weight:600; }
.ilan-card { background:#fff; border:1px solid #e6eaf2; border-radius:14px; box-shadow:0 6px 24px rgba(26,43,122,.06); overflow:hidden; margin-bottom:20px; }
.ilan-card-head { background:linear-gradient(135deg,#1a2b7a 0%,#2d4fb3 100%); color:#fff; padding:16px 22px; }
.ilan-card-head h3 { margin:0; font-size:18px; font-weight:600; }
.ilan-card-head p { margin:4px 0 0; font-size:13px; opacity:.85; }
.ilan-card-body { padding:22px; }
.form-section { margin-bottom:22px; }
.form-section-title { font-size:15px; font-weight:600; color:#1a2b7a; margin:0 0 12px; padding-bottom:8px; border-bottom:2px solid #eef1f8; display:flex; align-items:center; gap:8px; }
.form-section-title i { color:#d62828; }

/* ============================================
   MILLER COLUMNS — Modern Category Selector
   ============================================ */
.miller-columns {
    background: #fafbfc;
    border: 1px solid #e8ebf0;
    border-radius: 12px;
    overflow: hidden;
    min-height: 420px;
}

.miller-header {
    padding: 16px 20px;
    border-bottom: 1px solid #e8ebf0;
    background: #fff;
}

.miller-title {
    margin: 0;
    font-size: 15px;
    font-weight: 600;
    color: #1a2b7a;
    display: flex;
    align-items: center;
    gap: 8px;
}
.miller-title i { color: #d62828; font-size: 16px; }

.miller-breadcrumb {
    margin-top: 10px;
    min-height: 28px;
    font-size: 13px;
    color: #666;
}

.miller-crumb {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 10px;
    background: #f0f2f5;
    border-radius: 6px;
    color: #444;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.15s;
    user-select: none;
}
.miller-crumb:hover { background: #e4e7ec; color: #1a2b7a; }
.miller-crumb.active { background: #1a2b7a; color: #fff; cursor: default; }
.miller-crumb.active:hover { background: #1a2b7a; }
.miller-crumb i { font-size: 11px; color: #999; }
.miller-crumb.active i { color: #fff; }
.miller-crumb .crumb-sep { margin: 0 4px; color: #bbb; font-size: 10px; }

/* Columns Wrapper — Horizontal Scroll */
.miller-columns-wrapper {
    display: flex;
    flex-direction: row;
    gap: 8px;
    overflow-x: auto;
    padding: 16px 20px;
    min-height: 400px;
    max-height: 480px;
    scrollbar-width: thin;
    scrollbar-color: #cbd5e1 transparent;
    justify-content: flex-start;
}
.miller-columns-wrapper::-webkit-scrollbar { height: 6px; }
.miller-columns-wrapper::-webkit-scrollbar-track { background: transparent; }
.miller-columns-wrapper::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }

/* Column Card */
.miller-column {
    flex: 0 0 220px;
    min-width: 220px;
    max-width: 220px;
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    overflow-y: auto;
    max-height: 440px;
    box-shadow: 0 1px 3px rgba(26,43,122,0.04);
    display: flex;
    flex-direction: column;
}
.miller-column:not(:last-child) { margin-right: 0; }

.miller-column-header {
    padding: 10px 12px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #999;
    background: #fafbfc;
    border-bottom: 1px solid #eef1f8;
}

.miller-list {
    list-style: none;
    margin: 0;
    padding: 4px 0;
}

.miller-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 12px;
    margin: 2px 8px;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.12s;
    font-size: 13.5px;
    color: #2d3748;
    font-weight: 500;
    border: 1px solid transparent;
    position: relative;
}
.miller-item:hover { background: #f0f3f8; color: #1a2b7a; }

/* Selected State */
.miller-item.selected {
    background: #e2e8f0;
    color: #1a2b7a;
    border-color: #1a2b7a;
}
.miller-item.selected::after {
    content: '\f105';
    font-family: 'FontAwesome';
    font-size: 11px;
    color: #1a2b7a;
    margin-left: auto;
    padding-right: 4px;
}

.miller-item i.item-icon {
    margin-right: 10px;
    width: 16px;
    text-align: center;
    color: #aaa;
    font-size: 12px;
}
.miller-item.selected i.item-icon { color: #1a2b7a; }

.miller-empty {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100%;
    min-height: 200px;
    color: #bbb;
    font-size: 13px;
    text-align: center;
    padding: 20px;
}

.miller-column-divider {
    width: 1px;
    background: linear-gradient(180deg, transparent, #e8ebf0 20%, #e8ebf0 80%, transparent);
    margin: 0 4px;
    flex-shrink: 0;
}

.cat-wiz-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    padding: 16px 20px;
    background: #fafbfc;
    border-top: 1px solid #e8ebf0;
    border-radius: 0 0 12px 12px;
}
.cat-wiz-status { color: #2d7a3e; font-weight: 600; font-size: 13px; }

@media (max-width: 768px) { 
    .miller-column { flex: 0 0 180px; min-width: 180px; max-width: 180px; }
    .miller-columns-wrapper { gap: 4px; padding: 12px; }
}
</style>

<div class="container profil arsiv mt20">
	<?php include("temalar/$aktif_tema/inc/uye-ust.php"); ?>
	
	<div class="row">
		<div class="col-md-9 sidebar">
			<div class="panel panel-default jaayquick menu">
				<div class="panel-heading"><?php echo _LANG("menu_ilan_ekle"); ?></div>
				<div class="panel-body">
					<ul>
						<li class="active"><a href="<?php echo ozel_url("ilan-ekle"); ?>"><?php echo _LANG("menu_ilan_ekle"); ?></a></li>
						<li><a href="<?php echo ozel_url("ilanlarim"); ?>"><?php echo _LANG("nav_my_ads"); ?></a></li>
					</ul>
				</div>
			</div>
		</div>
		
		<div class="col-md-33">
			<?php include("temalar/$aktif_tema/inc/alert.php"); ?>
			<div class="ilan-form-wrap">
			<div class="ilan-card">
				<div class="ilan-card-head">
					<h3><i class="fa fa-plus-circle"></i> <?php echo _LANG("menu_ilan_ekle"); ?></h3>
					<p><?php echo _LANG('post_title'); ?> · <?php echo _LANG('post_category'); ?> · <?php echo _LANG('listing_price'); ?></p>
				</div>
				<div class="ilan-card-body">
					
					<!-- Progress bar -->
					<ul class="wiz-progress" id="wizProgress">
						<li class="active" data-step="1"><div class="dot">1</div><div class="lbl"><?php echo _LANG('post_category'); ?></div></li>
						<li data-step="2"><div class="dot">2</div><div class="lbl"><?php echo _LANG('post_description'); ?></div></li>
						<li data-step="3"><div class="dot">3</div><div class="lbl"><?php echo _LANG('listing_price'); ?></div></li>
						<li data-step="4"><div class="dot">4</div><div class="lbl"><?php echo _LANG('post_photos'); ?></div></li>
					</ul>
					
					<form action="" method="post" enctype="multipart/form-data" id="ilan_form">
						
						<!-- STEP 1: Kategori -->
						<div class="wiz-step active" data-step="1">
							<div class="form-section">
								<div class="form-section-title"><i class="fa fa-sitemap"></i> <?php echo _LANG('post_category'); ?> <span style="color:#e74c3c;">*</span></div>
								<div class="miller-columns" id="millerColumns">
									<div class="miller-header">
										<h5 class="miller-title"><i class="fa fa-layer-group"></i> <?php echo _LANG('wiz_title_kategori'); ?></h5>
										<div class="miller-breadcrumb" id="millerBreadcrumb">
											<span class="miller-crumb root" data-level="0"><i class="fa fa-home"></i> <?php echo _LANG('miller_root'); ?></span>
										</div>
									</div>
									<div class="miller-columns-wrapper" id="millerColumnsWrapper"></div>
									<div id="kategoriHiddenWrap"></div>
									<div class="cat-wiz-actions" id="catWizActions" style="margin-top:16px; display:none; justify-content:space-between; align-items:center;">
										<button type="button" class="btn btn-default" id="catBackBtn"><i class="fa fa-arrow-left"></i> <?php echo _LANG('wiz_back'); ?></button>
										<span class="cat-wiz-status" id="catWizStatus" style="color:#2d7a3e; font-weight:600;"></span>
										<button type="button" class="btn btn-primary wiz-next" data-next="2" id="catConfirmBtn" disabled><i class="fa fa-arrow-right"></i> <?php echo _LANG('wiz_next'); ?></button>
									</div>
									<noscript>
										<select class="form-control" name="kategori[]" multiple required style="height:150px;">
											<?php
											$ns_sql=mysqli_query($baglan,"select * from kategoriler where tip='ilan' and ust_kategori='0' order by baslik asc");
											while ($ns_d=mysqli_fetch_array($ns_sql)) {
												$sel=(isset($_POST["kategori"]) && is_array($_POST["kategori"]) && in_array($ns_d["id"],$_POST["kategori"]))?'selected':'';
												echo "<option value='{$ns_d["id"]}' $sel>{$ns_d["baslik"]}</option>";
											}
											?>
										</select>
									</noscript>
								</div>
						</div>
							<div class="wiz-nav">
								<span></span>
								<button type="button" class="btn btn-primary wiz-next" data-next="2"><i class="fa fa-arrow-right"></i> <?php echo _LANG('wiz_next'); ?></button>
							</div>
						</div>
						
						<!-- STEP 2: Detaylar -->
						<div class="wiz-step" data-step="2">
							<div class="form-section">
								<div class="form-section-title"><i class="fa fa-heading"></i> <?php echo _LANG('post_title'); ?></div>
								<div class="form-group">
									<label for="baslik"><?php echo _LANG('post_title'); ?> <span style="color:#e74c3c;">*</span></label>
									<input type="text" class="form-control" id="baslik" name="baslik" value="<?php echo @htmlspecialchars($_POST['baslik']); ?>" required placeholder="<?php echo _LANG('post_title'); ?>">
								</div>
							</div>
							<div class="form-section">
								<div class="form-section-title"><i class="fa fa-align-left"></i> <?php echo _LANG('post_description'); ?></div>
								<div class="form-group">
									<label for="icerik"><?php echo _LANG('post_description'); ?> <span style="color:#e74c3c;">*</span></label>
									<textarea class="form-control ckeditor" id="icerik" name="icerik" rows="10" required><?php echo @htmlspecialchars($_POST['icerik']); ?></textarea>
								</div>
							</div>
							
							<!-- Ozel alanlar -->
							<div id="ozel_alanlar_container">
								<?php 
								if ($kat) {
									$kategoriler=kategori_getir($kat);
									if (is_array($kategoriler)) {
										foreach ($kategoriler as $kat_id) {
											$kat_ozel_alan=kategori_bilgi("ozel_alanlar",$kat_id);
											if ($kat_ozel_alan) {
												$ozel_alanlar=$kat_ozel_alan;
											}
										}
									}
									if (isset($ozel_alanlar)) {
										$alan_dizi=explode("\r\n",$ozel_alanlar);
										echo "<div class='form-section'><div class='form-section-title'><i class='fa fa-tags'></i> " . _LANG('hb_ozel_alanlar') . "</div>";
										foreach ($alan_dizi as $alan) {
											$alan_parcala=explode("|",$alan);
											$anahtar=trim($alan_parcala[0]);
											$baslik=trim($alan_parcala[1]);
											if (!$baslik) $baslik=$anahtar;
											@$gerekli=trim($alan_parcala[2]);
											@$secenekler=trim($alan_parcala[3]);
											$deger=@$_POST["oa_$anahtar"];
											$gerekli_yildiz=($gerekli==1) ? "<span style='color:#e74c3c;'>*</span>" : "";
											if (!$secenekler) { ?>
												<div class="form-group">
													<label for="oa_<?php echo $anahtar; ?>"><?php echo $baslik; ?> <?php echo $gerekli_yildiz; ?></label>
													<input type="text" class="form-control" id="oa_<?php echo $anahtar; ?>" name="oa_<?php echo $anahtar; ?>" placeholder="<?php echo $baslik; ?>" value="<?php echo htmlspecialchars($deger); ?>" <?php echo ($gerekli==1) ? 'required' : ''; ?>>
												</div>
											<?php } else { ?>
												<div class="form-group">
													<label for="oa_<?php echo $anahtar; ?>"><?php echo $baslik; ?> <?php echo $gerekli_yildiz; ?></label>
													<select class="form-control" id="oa_<?php echo $anahtar; ?>" name="oa_<?php echo $anahtar; ?>" <?php echo ($gerekli==1) ? 'required' : ''; ?>>
														<option value=""><?php echo _LANG('filter_select'); ?></option>
														<?php 
														$secenekler_dizi=explode(",",$secenekler);
														foreach ($secenekler_dizi as $sec) {
															$sel=($deger==$sec) ? "selected" : "";
															echo "<option value='$sec' $sel>$sec</option>";
														}
														?>
													</select>
												</div>
											<?php }
										}
										echo "</div>";
									}
								}
								?>
							</div>
							
							<div class="wiz-nav">
								<button type="button" class="btn btn-default wiz-prev" data-prev="1"><i class="fa fa-arrow-left"></i> <?php echo _LANG('wiz_back'); ?></button>
								<button type="button" class="btn btn-primary wiz-next" data-next="3"><i class="fa fa-arrow-right"></i> <?php echo _LANG('wiz_next'); ?></button>
							</div>
						</div>
						
						<!-- STEP 3: Fiyat / Iletisim -->
						<div class="wiz-step" data-step="3">
							<div class="form-section">
								<div class="form-section-title"><i class="fa fa-money"></i> <?php echo _LANG('listing_price'); ?></div>
								<div class="form-group">
									<div class="row">
										<div class="col-md-4">
											<label><?php echo _LANG('listing_price'); ?></label>
											<select class="form-control" id="para_birimi" name="para_birimi" onchange="paraBirimiDegisti(this.value)">
												<option value="gmd" <?php echo (@$_POST['para_birimi']=='gmd' || !@$_POST['para_birimi']) ? 'selected' : ''; ?>>GMD (D)</option>
												<option value="usd" <?php echo (@$_POST['para_birimi']=='usd') ? 'selected' : ''; ?>>USD ($)</option>
												<option value="eur" <?php echo (@$_POST['para_birimi']=='eur') ? 'selected' : ''; ?>>EUR (€)</option>
												<option value="gbp" <?php echo (@$_POST['para_birimi']=='gbp') ? 'selected' : ''; ?>>GBP (£)</option>
											</select>
										</div>
									</div>
									<div class="row mt10" id="fiyat_alanlari">
										<div class="col-md-3" id="fiyat_gmd_alan" style="display:block;">
											<input type="text" class="form-control" name="fiyat" placeholder="<?php echo _LANG('post_price'); ?> (GMD)" value="<?php echo @htmlspecialchars($_POST['fiyat']); ?>">
										</div>
										<div class="col-md-3" id="fiyat_usd_alan" style="display:none;">
											<input type="text" class="form-control" name="fiyat_usd" placeholder="Price (USD)" value="<?php echo @htmlspecialchars($_POST['fiyat_usd']); ?>">
										</div>
										<div class="col-md-3" id="fiyat_eur_alan" style="display:none;">
											<input type="text" class="form-control" name="fiyat_eur" placeholder="Price (EUR)" value="<?php echo @htmlspecialchars($_POST['fiyat_eur']); ?>">
										</div>
										<div class="col-md-3" id="fiyat_gbp_alan" style="display:none;">
											<input type="text" class="form-control" name="fiyat_gbp" placeholder="Price (GBP)" value="<?php echo @htmlspecialchars($_POST['fiyat_gbp']); ?>">
										</div>
									</div>
								</div>
							</div>
							
							<div class="form-section">
								<div class="form-section-title"><i class="fa fa-phone"></i> <?php echo _LANG('post_contact'); ?></div>
								<div class="form-group">
									<label><?php echo _LANG('post_contact'); ?></label>
									<select class="form-control" id="iletisim" name="iletisim" onchange="iletisimDegisti(this.value)">
										<option value="1" <?php echo (@$_POST['iletisim']=='1' || !@$_POST['iletisim']) ? 'selected' : ''; ?>><?php echo _LANG('profil_iletisim_bilgileri'); ?></option>
										<option value="2" <?php echo (@$_POST['iletisim']=='2') ? 'selected' : ''; ?>><?php echo _LANG('ozel_iletisim'); ?></option>
									</select>
								</div>
								<div class="row" id="ozel_iletisim_alan" style="display:none;">
									<div class="col-md-6">
										<div class="form-group">
											<label><?php echo _LANG('s_yetkili_kisi'); ?></label>
											<input type="text" class="form-control" name="yetkili" value="<?php echo @htmlspecialchars($_POST['yetkili']); ?>">
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label><?php echo _LANG('p_cep_telefonu'); ?></label>
											<input type="text" class="form-control" name="yetkili_tel" value="<?php echo @htmlspecialchars($_POST['yetkili_tel']); ?>">
										</div>
									</div>
								</div>
							</div>
							
							<div class="wiz-nav">
								<button type="button" class="btn btn-default wiz-prev" data-prev="2"><i class="fa fa-arrow-left"></i> <?php echo _LANG('wiz_back'); ?></button>
								<button type="button" class="btn btn-primary wiz-next" data-next="4"><i class="fa fa-arrow-right"></i> <?php echo _LANG('wiz_next'); ?></button>
							</div>
						</div>
						
						<!-- STEP 4: Fotograf -->
						<div class="wiz-step" data-step="4">
							<div class="form-section">
								<div class="form-section-title"><i class="fa fa-camera"></i> <?php echo _LANG('post_photos'); ?></div>
								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<label class="text-muted small"><?php echo _LANG('s_kapak_gorseli'); ?></label>
											<input type="file" class="form-control" name="gorsel" accept="image/*">
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label class="text-muted small"><?php echo _LANG('s_galeri_gorselleri'); ?></label>
											<input type="file" class="form-control" name="galeri[]" multiple accept="image/*">
										</div>
									</div>
								</div>
							</div>
							
							<div class="wiz-nav">
								<button type="button" class="btn btn-default wiz-prev" data-prev="3"><i class="fa fa-arrow-left"></i> <?php echo _LANG('wiz_back'); ?></button>
								<div class="pull-right">
									<button type="submit" name="buton" value="<?php echo _LANG('tx_ilani_kaydet'); ?>" class="btn btn-default btn-ilan"><i class="fa fa-save"></i> <?php echo _LANG('tx_ilani_kaydet'); ?></button>
									<button type="submit" name="buton" value="<?php echo _LANG('tx_ilani_yayinla'); ?>" class="btn btn-info btn-ilan"><i class="fa fa-paper-plane"></i> <?php echo _LANG('tx_ilani_yayinla'); ?></button>
									<button type="submit" name="buton" value="<?php echo _LANG('tx_hemen_yayinla'); ?>" class="btn btn-success btn-ilan"><i class="fa fa-bolt"></i> <?php echo _LANG('tx_hemen_yayinla'); ?></button>
								</div>
								<div class="clearfix"></div>
							</div>
						</div>
						
					</form>
				</div>
			</div>
			</div>
		</div>
	</div>
</div>
<?php tema_footer(); ?>
<script>
// === Miller Columns Kategori Seçici ===
var KAT_TREE = <?php echo $kat_agac_json; ?>;
var SECILI_YOL = <?php echo $secili_yol_json; ?>;
var MAGAZA_MODE = <?php echo $magaza_id ? 'true' : 'false'; ?>;
var SERVER_KAT = <?php echo (int)$kat; ?>;
var AKTIF_DIL = "<?php echo aktif_dil(); ?>"; // tr, en, fr, ar

var catPath = [];
var columnsCache = {};
var maxColumns = 5;

function katChildren(pid){ return (KAT_TREE.children && KAT_TREE.children[pid]) ? KAT_TREE.children[pid] : []; }
function katTitle(id){
    var baslikKey = 'baslik';
    if (AKTIF_DIL === 'en') baslikKey = 'baslik_en';
    else if (AKTIF_DIL === 'fr') baslikKey = 'baslik_fr';
    else if (AKTIF_DIL === 'ar') baslikKey = 'baslik_ar';
    return (KAT_TREE[baslikKey] && KAT_TREE[baslikKey][id]) ? KAT_TREE[baslikKey][id] : ('#'+id);
}
function isLeaf(id){ return katChildren(id).length === 0; }

function updateHidden(path){
	var wrap = document.getElementById('kategoriHiddenWrap');
	wrap.innerHTML='';
	path.forEach(function(id){
		var inp = document.createElement('input');
		inp.type='hidden'; inp.name='kategori[]'; inp.value=id;
		wrap.appendChild(inp);
	});
}

function updateMillerBreadcrumb(path){
	var el = document.getElementById('millerBreadcrumb');
	if (!el) return;
	if (!path.length) {
		el.innerHTML = '<span class="miller-crumb root" data-level="0"><i class="fa fa-home"></i> ' + "<?php echo _LANG('miller_root'); ?>" + '</span>';
		return;
	}
	var html = '<span class="miller-crumb root" data-level="0"><i class="fa fa-home"></i> ' + "<?php echo _LANG('miller_root'); ?>" + '</span>';
	path.forEach(function(id, idx){
		var isLast = idx === path.length - 1;
		var isLeafNode = isLeaf(id);
		var cls = 'miller-crumb' + (isLast ? ' active' : '') + (isLeafNode && isLast ? ' leaf' : '');
		html += '<span class="miller-crumb-sep">/</span><span class="'+cls+'" data-level="'+(idx+1)+'" data-id="'+id+'">'+katTitle(id)+'</span>';
	});
	el.innerHTML = html;
	
	el.querySelectorAll('.miller-crumb[data-level]').forEach(function(crumb){
		crumb.addEventListener('click', function(){
			var level = parseInt(this.dataset.level);
			if (level < catPath.length) {
				catPath = catPath.slice(0, level);
				renderMillerColumns();
			}
		});
	});
}

function renderMillerColumns(){
	var wrapper = document.getElementById('millerColumnsWrapper');
	if (!wrapper) return;
	
	var existingColumns = wrapper.querySelectorAll('.miller-column');
	var currentColumns = existingColumns.length;
	
	var targetColumns = catPath.length + 1;
	if (catPath.length > 0 && !isLeaf(catPath[catPath.length-1])) {
		targetColumns++;
	}
	targetColumns = Math.min(targetColumns, maxColumns);
	
	for (var i = currentColumns; i < targetColumns; i++) {
		var col = document.createElement('div');
		col.className = 'miller-column';
		col.dataset.level = i;
		wrapper.appendChild(col);
	}
	
	for (var i = targetColumns; i < currentColumns; i++) {
		if (existingColumns[i]) existingColumns[i].remove();
	}
	
	for (var level = 0; level < targetColumns; level++) {
		var col = wrapper.querySelector('.miller-column[data-level="'+level+'"]');
		if (!col) continue;
		
		var parentId = level === 0 ? 0 : catPath[level-1];
		var items = katChildren(parentId);
		var selectedId = catPath[level] || null;
		
		renderColumn(col, level, items, selectedId);
	}
	
	updateMillerBreadcrumb(catPath);
	updateHidden(catPath);
	updateActions();
}

function renderColumn(col, level, items, selectedId){
	var hasChildren = items.length > 0;
	var headerText = level === 0 ? "<?php echo _LANG('miller_root'); ?>" : (katTitle(catPath[level-1]) || 'Seçim');
	
	var html = '<div class="miller-column-header">'+headerText+'</div>';
	
	if (!hasChildren) {
		html += '<div class="miller-empty"><i class="fa fa-check-circle"></i> ' + "<?php echo _LANG('miller_kategori_secildi'); ?>" + '</div>';
	} else {
		html += '<ul class="miller-list">';
		items.forEach(function(id){
			var isSelected = id === selectedId;
			var itemLeaf = isLeaf(id);
			var hasChild = !itemLeaf;
			var itemClass = 'miller-item' + (isSelected ? ' selected' : '') + (hasChild ? ' has-children' : '');
			html += '<li class="'+itemClass+'" data-id="'+id+'" data-level="'+level+'">';
			html += '<i class="item-icon fa '+ (isLeaf(id) ? 'fa-tag' : 'fa-folder') +'"></i>';
			html += '<span>'+katTitle(id)+'</span>';
			if (hasChild) html += '<i class="fa fa-chevron-right chevron"></i>';
			html += '</li>';
		});
		html += '</ul>';
	}
	
	col.innerHTML = html;
	
	col.querySelectorAll('.miller-item').forEach(function(item){
		item.addEventListener('click', function(){
			var id = parseInt(this.dataset.id);
			var lvl = parseInt(this.dataset.level);
			
			catPath = catPath.slice(0, lvl);
			catPath.push(id);
			
			var colItems = this.closest('.miller-column').querySelectorAll('.miller-item');
			colItems.forEach(function(i){ i.classList.remove('selected'); });
			this.classList.add('selected');
			
			var wrapper = document.getElementById('millerColumnsWrapper');
			var allCols = wrapper.querySelectorAll('.miller-column');
			for (var i = lvl + 1; i < allCols.length; i++) {
				allCols[i].remove();
			}
			
			if (isLeaf(id)) {
				catPath = catPath.slice(0, lvl + 1);
				renderMillerColumns();
				updateActions();
				if (id !== SERVER_KAT) {
					var url = new URL(window.location.href);
					url.searchParams.set('kat', id);
					window.location.href = url.toString();
				}
			} else {
				catPath = catPath.slice(0, lvl + 1);
				renderMillerColumns();
			}
		});
	});
}

function updateActions(){
	var wrapper = document.getElementById('millerColumnsWrapper');
	if (!wrapper) return;
	
	var hasSelection = catPath.length > 0;
	var lastSelected = catPath.length > 0 ? catPath[catPath.length-1] : null;
	var isLeafSelected = lastSelected && isLeaf(lastSelected);
	
	var actions = document.getElementById('catWizActions');
	var status = document.getElementById('catWizStatus');
	var confirmBtn = document.getElementById('catConfirmBtn');
	var backBtn = document.getElementById('catBackBtn');
	
	if (actions) {
		actions.style.display = catPath.length > 0 ? 'flex' : 'none';
	}
	
	if (status) {
		if (isLeafSelected) {
			status.innerHTML = '<i class="fa fa-check-circle"></i> ' + "<?php echo _LANG('miller_kategori_secildi'); ?>" + ': <strong>'+katTitle(lastSelected)+'</strong>';
		} else if (hasSelection) {
			status.innerHTML = '<i class="fa fa-folder-open"></i> Seçili: <strong>'+katTitle(lastSelected)+'</strong> - ' + "<?php echo _LANG('miller_alt_kategori_sec'); ?>";
		} else {
			status.innerHTML = '';
		}
	}
	
	if (confirmBtn) {
		confirmBtn.disabled = !isLeafSelected;
	}
	
	if (backBtn) {
		backBtn.style.display = catPath.length > 1 ? 'inline-flex' : 'none';
	}
}

function catGoBack(){
	if (catPath.length <= 1) return;
	catPath.pop();
	renderMillerColumns();
}

function initMillerColumns(){
	var wrapper = document.getElementById('millerColumnsWrapper');
	if (!wrapper) return;
	
	if (wrapper.children.length === 0) {
		var col = document.createElement('div');
		col.className = 'miller-column';
		col.dataset.level = 0;
		wrapper.appendChild(col);
	}
	
	if (SECILI_YOL && SECILI_YOL.length) {
		catPath = SECILI_YOL.slice();
	} else {
		catPath = [];
	}
	
	renderMillerColumns();
}

document.addEventListener('DOMContentLoaded', function(){
    var wrapper = document.getElementById('millerColumnsWrapper');
    if (wrapper) {
        initMillerColumns();
    }
    
    var backBtn = document.getElementById('catBackBtn');
    if (backBtn) {
        backBtn.addEventListener('click', catGoBack);
    }
    
    if (document.readyState === 'loading') {
    } else {
        var wrapper = document.getElementById('millerColumnsWrapper');
        if (wrapper) { initMillerColumns(); }
        var backBtn = document.getElementById('catBackBtn');
        if (backBtn) { backBtn.addEventListener('click', catGoBack); }
    }
});;

// Wizard
var currentStep = 1;
function showStep(n){
	currentStep = n;
	document.querySelectorAll('.wiz-step').forEach(function(el){ el.classList.remove('active'); });
	var s = document.querySelector('.wiz-step[data-step="'+n+'"]');
	if (s) s.classList.add('active');
	document.querySelectorAll('#wizProgress li').forEach(function(li){
		var st = parseInt(li.dataset.step);
		li.classList.remove('active','done');
		if (st === n) li.classList.add('active');
		else if (st < n) li.classList.add('done');
	});
	window.scrollTo({top:0, behavior:'smooth'});
}
function validateStep(n){
	if (n === 1) {
		var inputs = document.querySelectorAll('#kategoriHiddenWrap input[name="kategori[]"]');
		if (!inputs.length) { alert('<?php echo _LANG('msj_kategori_gerekli'); ?>'); return false; }
	}
	if (n === 2) {
		var baslik = document.getElementById('baslik').value.trim();
		if (!baslik) { alert('<?php echo _LANG('msj_baslik_gerekli'); ?>'); document.getElementById('baslik').focus(); return false; }
		var icerik = '';
		if (window.CKEDITOR && CKEDITOR.instances && CKEDITOR.instances.icerik) { icerik = CKEDITOR.instances.icerik.getData(); }
		else { var ta = document.getElementById('icerik'); if (ta) icerik = ta.value; }
		if (!icerik || !icerik.replace(/<[^>]*>/g,'').trim()) { alert('<?php echo _LANG('msj_icerik_gerekli'); ?>'); return false; }
	}
	return true;
}
document.querySelectorAll('.wiz-next').forEach(function(btn){
	btn.addEventListener('click', function(){
		var next = parseInt(this.dataset.next);
		if (validateStep(currentStep)) showStep(next);
	});
});
document.querySelectorAll('.wiz-prev').forEach(function(btn){
	btn.addEventListener('click', function(){ showStep(parseInt(this.dataset.prev)); });
});
document.querySelectorAll('#wizProgress li').forEach(function(li){
	li.style.cursor='pointer';
	li.addEventListener('click', function(){ var st=parseInt(this.dataset.step); if (st < currentStep) showStep(st); });
});

function paraBirimiDegisti(val) {
	$('#fiyat_alanlari > div').hide();
	$('#fiyat_' + val + '_alan').show();
}
function iletisimDegisti(val) {
	if (val==2) { $('#ozel_iletisim_alan').show(); }
	else { $('#ozel_iletisim_alan').hide(); }
}
$(document).ready(function() {
	paraBirimiDegisti($('#para_birimi').val());
	iletisimDegisti($('#iletisim').val());
});
</script>
