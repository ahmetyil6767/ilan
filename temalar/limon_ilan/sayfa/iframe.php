<?php
if(count(get_included_files())==1) {die();}

@$width=$_GET["width"];
if (!$width) {
	$width=200;
}
@$height=$_GET["height"];
if (!$height) {
	$height=600;
}
@$cat=$_GET["cat"];
if (!$cat) {
	$cat=0;
}
@$user=$_GET["user"];
if (!$user) {
	$user=0;
}
@$limit=$_GET["limit"];
if (!$limit) {
	$limit=20;
}
@$baslik=$_GET["title"];
if (!$baslik) {
	$baslik="İLAN PANOSU";
}

if ($cat) {
	$sql_kategori=sql_kategori($cat);
	if ($user) {
		$sql=mysqli_query($baglan,"select * from icerik where tip='ilan' and durum='1' and yazar='$user' and $sql_kategori limit $limit");
	} else {
		$sql=mysqli_query($baglan,"select * from icerik where tip='ilan' and durum='1' and $sql_kategori limit $limit");
	}
} else {
	if ($user) {
		$sql=mysqli_query($baglan,"select * from icerik where tip='ilan' and durum='1' and yazar='$user' limit $limit");
	} else {
		$sql=mysqli_query($baglan,"select * from icerik where tip='ilan' and durum='1' limit $limit");
	}
}


$site_font=tema_ayar("font"); 
if (!$site_font) {$site_font="Work Sans";}
if ($site_font=="Work Sans") { ?>
<link href="https://fonts.googleapis.com/css?family=Work+Sans:300,400,500,600,700,800&amp;subset=latin-ext" rel="stylesheet">
<?php } else if ($site_font=="Roboto") { ?>
<link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700,900&amp;subset=latin-ext" rel="stylesheet">
<?php } else if ($site_font=="Open Sans") { ?>
<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800&amp;subset=latin-ext" rel="stylesheet">
<?php } else if ($site_font=="Lato") { ?>
<link href="https://fonts.googleapis.com/css?family=Lato:300,400,700,900&amp;subset=latin-ext" rel="stylesheet">
<?php } else if ($site_font=="Montserrat") { ?>
<link href="https://fonts.googleapis.com/css?family=Montserrat:300,400,500,600,700,800&amp;subset=latin-ext" rel="stylesheet">
<?php } else if ($site_font=="Oswald") { ?>
<link href="https://fonts.googleapis.com/css?family=Oswald:300,400,500,600,700&amp;subset=latin-ext" rel="stylesheet">
<?php } else if ($site_font=="Ubuntu") { ?>
<link href="https://fonts.googleapis.com/css?family=Ubuntu:300,400,500,700&amp;subset=latin-ext" rel="stylesheet">
<?php } ?>

<link rel="stylesheet" href="<?php echo tema_dizini(); ?>/css/font-awesome.min.css">
<style>
	body {font-family:"<?php echo $site_font; ?>", sans-serif;}
	.iframe {border:1px solid #ddd;width:<?php echo $width; ?>px;max-height:<?php echo $height; ?>px;overflow:scroll;}
	.iframe a.ilan {padding:10px;display:block;border-bottom:1px solid #ddd;text-decoration:none !important;}
	.iframe a.ilan:hover {background:#eee;}
	.iframe .ilan h3 {color:#424242;padding:0;margin:7px 0;font-size:14px;line-height:16px;}
	.iframe .ilan .fiyat {font-weight:bold;color:red;font-size:14px;margin-bottom:5px;}
	.iframe .ilan .il_ilce {color:#aaa;font-size:12px;}
	.iframe .ilan img {max-width:100%;height:auto;}
	.iframe h2 {font-size:22px;margin:0;padding:0;border-bottom:1px solid #ddd;padding:5px 10px;font-size:14px;}
</style>
<marquee behavior="alternate" scrollamount="2"  direction="up" loop="5" onmouseover="this.stop();" onmouseout="this.start();" class="iframe">
<h2><?php echo $baslik; ?></h2>
<?php
while ($dizi=mysqli_fetch_array($sql)) {
	$ilan_id=$dizi["id"];
	$baslik=$dizi["baslik"];
	$resim=resim_getir("530_393",$ilan_id);
	$fiyat=fiyat($ilan_id,true);
	$url=link_getir($ilan_id);
	?>
	<a href="<?php echo $url; ?>" class="ilan" target="_blank">
		<img src="<?php echo $resim; ?>" alt="">
		<h3><?php echo $baslik; ?></h3>
		<div class="fiyat"><?php echo $fiyat; ?></div>
		<div class="il_ilce"><?php echo il_getir($ilan_id); ?>, <?php echo ilce_getir($ilan_id); ?></div>
	</a>
	<?php 
} ?>
</div>