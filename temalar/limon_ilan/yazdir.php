<?php 
if(count(get_included_files())==1) {die();}
require("config/db.php");
$baglan=mysqli_connect("$host","$kullanici","$parola","$veritabani");
// Check connection
mysqli_query($baglan,"set names utf8mb4");
date_default_timezone_set('Europe/Istanbul');
session_start();
error_reporting(0);
include_once("config/yapilandir.php");
include_once("config/fonksiyonlar.php");
$id=$_GET["id"];

$logo=tema_ayar("logo");
$print_header_bg=tema_ayar("print_header_bg");
$print_header_color=tema_ayar("print_header_color");
$baslik=getir("icerik","baslik",$id);
$fiyat=fiyat($id);
$resim=resim_getir("530_393");
$kategoriler=kategori_getir($id,"kategori");
//print_r($kategoriler);
$kat1=$kategoriler[0];
if (kat1) {
	$kat1_baslik=getir("kategoriler","baslik",$kat1);
}
$kat2=$kategoriler[1];
if (kat2) {
	$kat2_baslik=getir("kategoriler","baslik",$kat2);
}
$kat3=$kategoriler[2];
if (kat3) {
	$kat3_baslik=getir("kategoriler","baslik",$kat3);
}
$kat4=$kategoriler[3];
if ($kat4) {
	$kat4_baslik=getir("kategoriler","baslik",$kat4);
}
$kat5=$kategoriler[4];
if ($kat5) {
	$kat5_baslik=getir("kategoriler","baslik",$kat5);
}
if ($kat1==1) {
	$header_yaz="$kat3_baslik $kat4_baslik";
}
if ($kat1==2) {
	$header_yaz="$kat3_baslik $kat4_baslik $kat5_baslik";
}
foreach ($kategoriler as $kat) {
	$kat_ozel_alan=kategori_bilgi("ozel_alanlar",$kat);
	$kat_guvenlik_ipucu=kategori_bilgi("guvenlik_ipuclari",$kat);
	$kat_teknik_ozellikler=kategori_bilgi("teknik_ozellikler",$kat);
	$kat_sag_reklam=kategori_bilgi("ilan_sag_reklam",$kat);
	$kat_resimsiz=kategori_bilgi("resimsiz",$kat);
	$kat_metrekare_fiyati=kategori_bilgi("metrekare_fiyati",$kat);
	if ($kat_guvenlik_ipucu) {
		$guvenlik_ipuclari=$kat_guvenlik_ipucu;
	}
	if ($kat_ozel_alan) {
		$ozel_alanlar=$kat_ozel_alan;
	}
	if ($kat_teknik_ozellikler) {
		$teknik_ozellikler=$kat_teknik_ozellikler;
	}
	if ($kat_sag_reklam) {
		$ilan_sag_reklam=$kat_sag_reklam;
	}	
	if ($kat_resimsiz) {
		$resimsiz_ilan=$kat_resimsiz;
	}	
	if ($kat_metrekare_fiyati) {
		$metrekare_fiyati=$kat_metrekare_fiyati;
	}
	//echo getir("kategoriler","baslik",$kat);
}

?>
<!DOCTYPE HTML>
<html lang="<?php echo _DIL_HTML_LANG(); ?>" dir="<?php echo _DIL_HTML_YON(); ?>">
<head>
	<meta charset="UTF-8">
	<title><?php echo _LANG('hb_arsiv'); ?></title>
	<link rel="stylesheet" href="<?php echo tema_dizini(); ?>/css/bootstrap.min.css"/>	
	<link rel="stylesheet" href="<?php echo site_url(); ?>/temalar/<?php echo $aktif_tema; ?>/css/print-new.css" />
	<link rel="stylesheet" href="<?php echo tema_dizini(); ?>/css/font-awesome.min.css"/>
	<link href="https://fonts.googleapis.com/css?family=Work+Sans:300,400,500,600,700,800&amp;subset=latin-ext" rel="stylesheet">
	<style type="text/css">
		.header {background:<?php echo $print_header_bg; ?> !important;color:<?php echo $print_header_color; ?> !important;}
	</style>
</head>
<body>

<div id="arsiv">

	<?php 
	if ($header_yaz) {

		echo "<div class='header'>$header_yaz</div>";
	}
	?>
	<div id="ust">
		<div class="baslik">
			<h1><?php echo $baslik; ?></h1>
			
			<?php 
			$il=il_getir($id);
			$ilce=ilce_getir($id);
			$mahalle=mahalle_getir($id);
			if ($il or $ilce or $mahalle) {
			?>					
			<ul class="adres">
				<?php if (tema_ayar("bolgesel")=="Hayır") { ?>
				<li><?php echo $il; ?></li>
				<?php } ?>
				<li><?php echo $ilce; ?></li>
				<li><?php echo $mahalle; ?></li>
			</ul>
			<?php } ?>
			<div class="clear"></div>
			<div class="satir fiyat">Fiyat: <strong><?php echo $fiyat; ?></strong></div>
			
		</div>
	</div>
	<div id="sol">
		<table class="table">
			<tr>
				<th><?php echo _LANG("ilan_no"); ?></th>
				<td style="color:red;"><?php echo $id; ?></td>
			</tr>
			<tr>
				<th><?php echo _LANG("listing_date"); ?></th>
				<td><?php echo tarih_getir($id); ?></td>
			</tr>
			<?php
			if ($ozel_alanlar) {
				$alan_dizi=explode("\r\n",$ozel_alanlar);
				$say=count($alan_dizi);
				if ($say>0) {
					foreach ($alan_dizi as $alan) {
						$alan_parcala=explode("|",$alan);
						$anahtar=$alan_parcala[0];
						$baslik=$alan_parcala[1];
						if (!$baslik) {
							$baslik=$anahtar;
						}
						$deger=bilgi($anahtar,$icerik_id);
						if (isset($deger)) {
						echo "
						<tr>
							<th>$baslik</th>
							<td>$deger</td>
						</tr>";
						}
						if ($anahtar=="metrekare") {
							if ($metrekare_fiyati==1) {
								$fiyat_toplam=bilgi("fiyat");
								$m2_fiyati=fiyat_donustur($fiyat_toplam/$deger);
								echo "
								<tr>
									<th><?php echo _LANG('hb_metrekare_fiyati'); ?></th>
									<td>$m2_fiyati <i class='fa fa-try'></i></td>
								</tr>";										
							}
						}
					}
				}
			}
			?>
		</table>
	</div>
	<div id="sag">
		<img src="<?php echo $resim; ?>" alt="" class="img-responsive mb15" />
		<?php 
		$ilan_magaza=bilgi("magaza",$id);
		
		$ilan_iletisim=bilgi("iletisim",$id);
		
		if ($ilan_iletisim==2) {
			$yazar_ad_soyad=bilgi("yetkili",$id);
			$yazar_cep=bilgi("yetkili_tel",$id);
		} else {
			$yazar=getir("icerik","yazar",$id);
			$yazar_ad_soyad=getir("uyeler","ad_soyad",$yazar);
			$yazar_cep=uye_bilgi("cep_tel",$yazar);
			$yazar_is=uye_bilgi("is_tel",$yazar);
			$yazar_ev=uye_bilgi("ev_tel",$yazar);			
		}

		?>
		
		<div class="iletisim">
			<div class="ad_soyad"><?php echo $yazar_ad_soyad; ?></div>

			<table>
				<?php if ($yazar_cep) { ?>
				<tr>
					<th style="width:50%;">Cep</th>
					<td><?php echo $yazar_cep; ?></td>
				</tr>
				<?php } ?>
				<?php if ($yazar_is) { ?>
				<tr>
					<th><?php echo _LANG("phone_work"); ?></th>
					<td><?php echo $yazar_is; ?></td>
				</tr>
				<?php } ?>
				<?php if ($yazar_ev) { ?>
				<tr>
					<th>Ev</th>
					<td><?php echo $yazar_ev; ?></td>
				</tr>
				<?php } ?>
			</table>	
		</div>
	</div>
	<div class="clear"></div>
	<div class="aciklama">
		<?php echo icerik_getir($id); ?>
	</div>	
</div>
<div class="text-center" style="margin-top:20px;margin-bottom:5px !important;">
<img src="<?php echo $logo; ?>" alt="" />
<div class="clear" style="margin-bottom:7px;"></div>
Bu ilan <?php echo site_url(); ?> adresinde yer almaktadır.
</div>
</body>
</html>