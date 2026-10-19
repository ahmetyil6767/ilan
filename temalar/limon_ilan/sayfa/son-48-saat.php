<?php 
$cache=tema_ayar("cache");
if ($cache==1 and !$oturum_id) {
	$cache_klasor="cache/sayfa";
	$cache_dosya_adi="son-48-saat";
	$cache_dosyasi= "$cache_klasor/$cache_dosya_adi-cache.html";
	$sayfa_cache_sure=tema_ayar("sayfa_cache_sure");
	$cache_sure=$sayfa_cache_sure*60;
	if ($cache_sure>0) {
		if (file_exists($cache_dosyasi) and (time() - ($cache_sure) < filemtime($cache_dosyasi))) {
			include_once($cache_dosyasi);
			exit;
		} else {
			ob_start();
		}
	}		
}
$son_dizi=array();
tema_header(); 
$baslangic_date=date("Y-m-d H:i:s",strtotime("-2 days"));
$toplam_sql=mysqli_query($baglan,"select id from icerik where durum='1' and tip='ilan' and tarih_saat>'$baslangic_date' order by tarih_saat desc");
$toplam=mysqli_num_rows($toplam_sql);

@$sayfa=$_GET["sayfa"];
if (!$sayfa) {$sayfa=1;}
$listeleme_adedi=100;
$baslangic=($sayfa-1)*$listeleme_adedi;
$kac_sayfa=ceil($toplam/$listeleme_adedi);

if ($toplam>0) {
	if ($sayfa>$kac_sayfa) {
		yonlendir(ozel_url("son-48-saat")."?sayfa=$kac_sayfa");
	}
}

$sql=mysqli_query($baglan,"select id from icerik where durum='1' and tip='ilan' and tarih_saat>'$baslangic_date' order by tarih_saat desc limit $baslangic, $listeleme_adedi");
while ($dizi=mysqli_fetch_array($sql)) {
	$ilan_id=$dizi["id"];
	$son_dizi[]=$ilan_id;
}
$sayi=count($son_dizi);
$sutun=5;
if ($sutun==2) {
	$md_sutun=2;
	$sm_sutun=2;
	$xs_sutun=2;
	$col_yaz="col-md-21 col-sm-21 col-xs-21";
	$resim_boyut="530_393";
} else if ($sutun==3) {
	$md_sutun=3;
	$sm_sutun=2;
	$xs_sutun=2;
	$col_yaz="col-md-14 col-sm-21 col-xs-21";
	$resim_boyut="530_393";
} else if ($sutun==4) {
	$md_sutun=4;
	$sm_sutun=2;
	$xs_sutun=2;
	$col_yaz="col-md-a4 col-sm-21 col-xs-21";
	$resim_boyut="530_393";
} else if ($sutun==5) {
	$md_sutun=5;
	$sm_sutun=3;
	$xs_sutun=3;
	$col_yaz="col-md-a5 col-sm-14 col-xs-14";
	$resim_boyut="160_120";
} else if ($sutun==6) {
	$md_sutun=6;
	$sm_sutun=3;
	$xs_sutun=3;
	$col_yaz="col-md-7 col-sm-14 col-xs-14";
	$resim_boyut="160_120";
} else if ($sutun==7) {
	$md_sutun=7;
	$sm_sutun=3;
	$xs_sutun=3;
	$col_yaz="col-md-6 col-sm-14 col-xs-14";
	$resim_boyut="160_120";
}
$fiyat_goster="Evet";
?>
<div class="container mt20">
	<div class="row">
		<div class="col-md-9 sidebar">
			<?php bilesen_alani("anasayfa_sidebar"); ?>
		</div>
		<div class="col-md-33">
			<?php include("temalar/$aktif_tema/inc/alert.php"); ?>
			<div class="bilesen">
			<div class="baslik">
			<?php echo _LANG('title_son_48_saat'); ?>
			</div>
			<?php if ($sayi>0) { ?>
			<div class="row">
				<?php
				foreach ($son_dizi as $ilan_id) {
				$ilan_baslik=getir("icerik","baslik",$ilan_id);
				$ilan_resim=resim_getir($resim_boyut,$ilan_id);
				$ilan_link=link_getir($ilan_id);
				if ($fiyat_goster=="Evet") {
					$fiyat=fiyat($ilan_id);
				}
				?>
				<div class="<?php echo $col_yaz; ?>">
					<div class="liste">
						<a href="<?php echo $ilan_link; ?>" title="<?php echo $ilan_baslik; ?>" class="r_ust">
							<img src="<?php echo $ilan_resim; ?>" alt="<?php echo $ilan_baslik; ?>" style="<?php echo @$max_height; ?>;width:auto;" class="img-responsive"/>
							<?php if ($fiyat_goster=="Evet" and $fiyat) {echo "<div class='fiyat_layer'>$fiyat</div>";} ?>
						</a>
						<h3><a href="<?php echo $ilan_link; ?>" title="<?php echo $ilan_baslik; ?>"><?php echo $ilan_baslik; ?></a></h3>
					</div>
				</div>
				<?php } ?>
			</div>
			<?php 
			
			if ($kac_sayfa>1) {
				echo "<div class='clearfix mb20'></div>";
				echo "<div class='text-center'><ul class='pagination'>";
				$sayfa_url=ozel_url("son-48-saat");
				if ($sayfa>1) {
					$onceki_sayfa=$sayfa-1;
					echo "<li><a href='$sayfa_url?sayfa=$onceki_sayfa' aria-label='" . _LANG("p_onceki") . "'><i class='ion-chevron-left'></i></a></li>";
				}		
				for ($x=1;$x<=$kac_sayfa;$x++) {
					if ($x==$sayfa) {$active="class='active'";$link_yaz="";} else {$active="";$link_yaz="href='$sayfa_url?sayfa=$x'";}
					echo "<li $active><a $link_yaz>$x</a></li>";
				}
				if ($sayfa<$kac_sayfa) {
					$sonraki_sayfa=$sayfa+1; 
					echo "<li><a href='$sayfa_url?sayfa=$sonraki_sayfa' aria-label='" . _LANG("p_onceki") . "'><i class='ion-chevron-right'></i></a></li>";
				}
				echo "</ul></div>";
			}
			
			} else {echo "<div class='alert alert-danger'>" . _LANG('s_ilan_bulunamadi') . "</div>";} ?>
			</div>
		</div>
	</div>
</div>
<?php tema_footer(); ?>
<?php 
if (!$oturum_id and !$_SESSION["mesaj"]) {
	klasor_olustur($cache_klasor);
	$cache_dosyasi= "$cache_klasor/$cache_dosya_adi-cache.html";
	touch($cache_dosyasi);
	$ch = fopen($cache_dosyasi, 'w');
	fwrite($ch, ob_get_contents());
	fclose($ch);
	ob_end_flush();
}
?>