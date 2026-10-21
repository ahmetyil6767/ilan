<?php 
if(count(get_included_files())==1) {die();}
$ayarlar=array(
array("tip"=>"multiple_checkbox","id"=>"goster","baslik"=>_LANG('pn_su_ana_kategorileri_dahil_et'),"secenekler"=>$kat_op,"aciklama"=>"Seçtiğiniz kategoriler menüde gösterilecektir"),
array("tip"=>"text","id"=>"adet","baslik"=>_LANG('pn_listeleme_adedi'),"standart"=>21,"aciklama"=>_LANG('ad_ilan_listeleme_adedi_21')),
array("tip"=>"select","id"=>"sutun","baslik"=>_LANG('pn_sutun_sayisi'),"aciklama"=>"Bir satırda gösterilecek ilan adedi.  Standart: 7","secenekler"=>options("2,3,4,5,6,7"),"standart"=>7),
);

$bilesen["tab_listeleme"]=array("id"=>"tab_listeleme","baslik"=>_LANG('pn_anasayfa_tab_listeleme'),"ayarlar"=>$ayarlar,"fonksiyon"=>"tab_listeleme","aciklama"=>_LANG('e_tab_listeleme_aciklama'));

function tab_listeleme($id) {
	global $baglan;
	
	// Debug: Hata raporlamayı aç
	$error_reporting_was = error_reporting();
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	
	$goster=bilesen_ayar("goster",$id);
	$adet=bilesen_ayar("adet",$id);
	$sutun=bilesen_ayar("sutun",$id);
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
	$goster_son=str_replace(array("(",")"),null,rtrim($goster,","));
	$katlar=explode(",",$goster_son);
	
	// Debug: SQL sorgusunu logla
	error_log("tab_listeleme (ID: $id) Kategoriler: " . implode(",", $katlar));
	
	$resim_boyut = $resim_boyut ?? "160_120";
	
	error_reporting($error_reporting_was);

?>	
<div class="bilesen tab" style="margin-top:20px;">
	<div class="panel panel-default beyaz">
		<div class="panel-heading">
		  <ul class="nav nav-tabs ozel" role="tablist">
			<?php $x=1; 
			foreach ($katlar as $kat) { 
			$kat_baslik=_DIL_BASLIK(getir("kategoriler","baslik",$kat), $kat, "kategori");
			$kat_ikon=kategori_bilgi("ikon",$kat);
			if ($x==1) {$active_yaz="active";} else {$active_yaz=null;}
			if ($x<4) { ?>
			<li role="presentation" class="<?php echo $active_yaz; ?>"><a href="#<?php echo $kat; ?>" aria-controls="<?php echo $kat; ?>" role="tab" data-toggle="tab"><?php if ($kat_ikon) {echo "<i class='$kat_ikon'></i>";} ?><?php echo $kat_baslik; ?></a></li>
			<?php } else { ?>
			<li role="presentation" class="visible-md visible-lg <?php echo $active_yaz; ?>"><a href="#<?php echo $kat; ?>" aria-controls="<?php echo $kat; ?>" role="tab" data-toggle="tab"><?php if ($kat_ikon) {echo "<i class='$kat_ikon'></i>";} ?><?php echo $kat_baslik; ?></a></li>
			<li role="presentation" class="visible-sm visible-xs <?php echo $active_yaz; ?>"><a href="#<?php echo $kat; ?>" aria-controls="<?php echo $kat; ?>" role="tab" data-toggle="tab"><?php if ($kat_ikon) {echo "<i class='$kat_ikon'></i>";} ?></a></li>
			<?php } $x++;} ?>
		  </ul>
		</div>	
		<div class="panel-body">
			<div class="tab-content">
				<?php $x=1; 
				foreach ($katlar as $kat) { 
				$kat_baslik=_DIL_BASLIK(getir("kategoriler","baslik",$kat), $kat, "kategori");
				$kat_ikon=kategori_bilgi("ikon",$kat);
				if ($x==1) {$active_yaz="active";} else {$active_yaz=null;}
				?>
				<div role="tabpanel" class="tab-pane fade in <?php echo $active_yaz; ?>" id="<?php echo $kat; ?>">
					<div class="row">
						<?php 
						$ilan_sql=mysqli_query($baglan,"select id from icerik where tip='ilan' and durum='1' and kategori like '%($kat)%' order by tarih_saat desc limit 0, $adet");
						$a=1;
						while ($ilan_dizi=mysqli_fetch_array($ilan_sql)) {
						$ilan_id=$ilan_dizi["id"];
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
									<img src="<?php echo $ilan_resim; ?>" alt="<?php echo $ilan_baslik; ?>" style="<?php echo $max_height; ?>;width:auto;" class="img-responsive"/>
									<?php if ($fiyat_goster=="Evet" and $fiyat) {echo "<div class='fiyat_layer'>$fiyat</div>";} ?>
								</a>
								<h3><a href="<?php echo $ilan_link; ?>" title="<?php echo $ilan_baslik; ?>"><?php echo $ilan_baslik; ?></a></h3>
							</div>
						</div>
						<?php 
						if ($a%$md_sutun==0) {echo "<div class='visible-md visible-lg clearfix'></div>";}
						if ($a%$sm_sutun==0) {echo "<div class='visible-sm clearfix'></div>";}
						if ($a%$xs_sutun==0) {echo "<div class='visible-xs clearfix'></div>";}
						$a++;} ?>
					</div>
				</div>
				<?php $x++;} ?>
				<div role="tabpanel" class="tab-pane fade" id="eleman">
					<p>test</p>
				</div>
			</div>
		</div>
	</div>
</div>
<?php
}
?>