<?php
include("../loader.php");
yonetici_degilse_yonlendir($oturum_id,ayar_getir("site_url")."/$admin_dizini/giris-yap.php");
@$islem=$_GET["islem"];
@$menu=$_GET["menu"];
@$menu_id=$_GET["menu_id"];
foreach ($tema_menuleri as $y) {
	$tm_id=$y["id"];
	$tm_baslik=$y["baslik"];
	$tema_menuleri_ayar[]=array("deger"=>$tm_id,"baslik"=>$tm_baslik);
}
if ($menu_id and !$menu) {
	$menu=bilgi_getir("ayarlar","deger",$menu_id);
}
if ($islem=="ekle") {
	@$menu_baslik=$_POST["menu_baslik"];
	@$menu_pozisyon=$_POST["menu_pozisyon"];
	@$buton=$_POST["buton"];
	if ($buton) {
		$sql=mysqli_prepare($baglan,"insert into ayarlar set anahtar='menu', deger=?");
		mysqli_stmt_bind_param($sql,"s",$menu_baslik);
		mysqli_stmt_execute($sql);
		mysqli_stmt_close($sql);
		$menu_id_getir=mysqli_fetch_array(mysqli_query($baglan,"select id from ayarlar where anahtar='menu' and deger='".mysqli_real_escape_string($baglan,$menu_baslik)."'"));
		$menu_id=$menu_id_getir["id"];
		if ($menu_pozisyon) {
			ayar_guncelle("$menu_pozisyon","menu-$menu_id");
		}
		$_SESSION["mesaj"]=array(_LANG('p_menu_created_linked'),"success");
		yonlendir("menuler.php");
	}
}
if ($islem=="menuleri_olustur") {
	
	foreach ($tema_menuleri as $x) {
		$id=$x["id"];
		$baslik=$x["baslik"];
		$sql=mysqli_prepare($baglan,"insert into ayarlar set anahtar='menu', deger=?");
  	mysqli_stmt_bind_param($sql,"s",$baslik);
  	mysqli_stmt_execute($sql);
  	mysqli_stmt_close($sql);
		$eklenen_id=mysqli_insert_id($baglan);
		if ($id) {
			ayar_guncelle("$id","menu-$eklenen_id");
		}
	}
	$_SESSION["mesaj"]=array(_LANG('p_all_menus_linked'),"success");
	yonlendir("menuler.php");
	
}
if ($islem=="sil") {
	// Linkler siliniyor
	$menu_id_int=(int)$menu_id;
	$menu_sil=mysqli_prepare($baglan,"delete from linkler where menu_id=?");
	mysqli_stmt_bind_param($menu_sil,"i",$menu_id_int);
	mysqli_stmt_execute($menu_sil);
	mysqli_stmt_close($menu_sil);
	$menu_sil=mysqli_prepare($baglan,"delete from ayarlar where id=?");
	mysqli_stmt_bind_param($menu_sil,"i",$menu_id_int);
	mysqli_stmt_execute($menu_sil);
	mysqli_stmt_close($menu_sil);
	$menu_iliski_deger="menu-$menu_id_int";
	$menu_iliski_sil=mysqli_prepare($baglan,"delete from ayarlar where deger=?");
	mysqli_stmt_bind_param($menu_iliski_sil,"s",$menu_iliski_deger);
	mysqli_stmt_execute($menu_iliski_sil);
	mysqli_stmt_close($menu_iliski_sil);
	$_SESSION["mesaj"]=array(_LANG('p_menu_links_deleted'),"success");
	yonlendir("menuler.php");
}
if ($islem=="linkler") {
	if (!$menu_id) {
		yonlendir("menuler.php");
	}
@$menu_pozisyonu_guncelle_buton=$_POST["menu_pozisyonu_guncelle_buton"];
if ($menu_pozisyonu_guncelle_buton) {
	$menu_baslik=$_POST["menu_baslik"];
	$menu_pozisyonu=$_POST["menu_pozisyonu"];
	$gecerli_menu=ayar_getir("$menu_pozisyonu");
	if ($menu_pozisyonu) {
		if (menu_pozisyonu($menu_id)) {ayar_sil(menu_pozisyonu($menu_id));}
		ayar_guncelle("$menu_pozisyonu","menu-$menu_id");
	}
	$baslik_degistir=mysqli_prepare($baglan,"update ayarlar set deger=? where id=?");
	$menu_id_int=(int)$menu_id;
	mysqli_stmt_bind_param($baslik_degistir,"si",$menu_baslik,$menu_id_int);
	mysqli_stmt_execute($baslik_degistir);
	mysqli_stmt_close($baslik_degistir);
	$_SESSION["mesaj"]=array(_LANG('p_menu_updated'),"success");
	yonlendir("menuler.php?islem=linkler&menu_id=$menu_id");
}
$link_sayisi=link_sayisi($menu_id);
@$ozel_link_ekle_buton=$_POST["ozel_link_ekle_buton"];
if ($ozel_link_ekle_buton) {
	$baslik=$_POST["baslik"];
	$url=$_POST["url"];
	$ust_link=$_POST["ust_link"];
	$ikon=$_POST["ikon"];
	if ($baslik or $url) {
	$kac_link_var=mysqli_num_rows(mysqli_query($baglan,"select * from linkler where menu_id='$menu_id'"));
	$sira=$kac_link_var*10;
	$sql=mysqli_prepare($baglan,"insert into linkler set baslik=?, url=?, ust_link=?, ikon=?, sira=?, menu_id=?");
  	$ust_link_int=(int)$ust_link;
  	$sira_int=(int)$sira;
  	$menu_id_int=(int)$menu_id;
  	mysqli_stmt_bind_param($sql,"ssssis",$baslik,$url,$ust_link_int,$ikon,$sira_int,$menu_id_int);
  	mysqli_stmt_execute($sql);
  	mysqli_stmt_close($sql);
	$_SESSION["mesaj"]=array(_LANG('p_link_added'),"success");
	} else {
	$_SESSION["mesaj"]=array(_LANG('p_link_title_or_url_required'),"danger");
	}
	yonlendir("menuler.php?islem=linkler&menu_id=$menu_id");
}
foreach ($icerik_tipleri as $yaz => $x) {
$tip_name=$x["id"];
@$tip_gizli=$x["gizli"];
$tip_kategori=$x["kategori"];
if ($tip_gizli!=1) {
@$buton=$_POST[$tip_name."_link_ekle_buton"];
@$secilen_linkler=$_POST[$tip_name];
@$ust_link=$_POST["ust_link_$tip_name"];
if ($buton) {
$kac_link_var=mysqli_num_rows(mysqli_query($baglan,"select * from linkler where menu_id='$menu_id'"));
foreach ($secilen_linkler as $link) {
$sira=$kac_link_var*10;
$baslik=bilgi_getir("icerik","baslik",$link);
$url=link_getir($link);
$link_ekle_sql=mysqli_prepare($baglan,"insert into linkler set baslik=?, url=?, menu_id=?, sira=?, ust_link=?");
  	$menu_id_int=(int)$menu_id;
  	$sira_int=(int)$sira;
  	$ust_link_int=(int)$ust_link;
  	mysqli_stmt_bind_param($link_ekle_sql,"ssiii",$baslik,$url,$menu_id_int,$sira_int,$ust_link_int);
  	mysqli_stmt_execute($link_ekle_sql);
  	mysqli_stmt_close($link_ekle_sql);
$kac_link_var++;
}
$_SESSION["mesaj"]=array(_LANG('p_links_added'),"success");
yonlendir("menuler.php?islem=linkler&menu_id=$menu_id");
} else {
	if ($tip_kategori==1) {
		@$buton=$_POST[$tip_name."_kategori_ekle_buton"];
		@$secilen_linkler=$_POST[$tip_name];
		if ($buton) {
		$kac_link_var=mysqli_num_rows(mysqli_query($baglan,"select * from linkler where menu_id='$menu_id'"));
		foreach ($secilen_linkler as $link) {
		$sira=$kac_link_var*10;
		$baslik=bilgi_getir("kategoriler","baslik",$link);
		$url=kategori_link_getir($link);
		$link_ekle_sql=mysqli_prepare($baglan,"insert into linkler set baslik=?, url=?, menu_id=?, ust_link=?, sira=?");
  	$menu_id_int=(int)$menu_id;
  	$ust_link_int=(int)$ust_link;
  	$sira_int=(int)$sira;
  	mysqli_stmt_bind_param($link_ekle_sql,"ssiii",$baslik,$url,$menu_id_int,$ust_link_int,$sira_int);
  	mysqli_stmt_execute($link_ekle_sql);
  	mysqli_stmt_close($link_ekle_sql);
		$kac_link_var++;
		}
		$_SESSION["mesaj"]=array(_LANG('p_links_added'),"success");
		yonlendir("menuler.php?islem=linkler&menu_id=$menu_id");
		}
	}
}
} }
@$link_sil=$_GET["link_sil"];
if ($link_sil) {
	$link_sil_int=(int)$link_sil;
  	$alt_linkler_update=mysqli_prepare($baglan,"update linkler set ust_link=0 where ust_link=?");
  	mysqli_stmt_bind_param($alt_linkler_update,"i",$link_sil_int);
  	mysqli_stmt_execute($alt_linkler_update);
  	mysqli_stmt_close($alt_linkler_update);
  	$sql=mysqli_prepare($baglan,"delete from linkler where id=?");
  	mysqli_stmt_bind_param($sql,"i",$link_sil_int);
  	mysqli_stmt_execute($sql);
  	mysqli_stmt_close($sql);
	$_SESSION["mesaj"]=array(_LANG('p_link_deleted'),"success");
	yonlendir("menuler.php?islem=linkler&menu_id=$menu_id");
}
@$link_guncelle=$_GET["link_guncelle"];
if ($link_guncelle) {
	$link_bilgileri=mysqli_fetch_array(mysqli_query($baglan,"select * from linkler where id='$link_guncelle'"));
	$guncelle_baslik=$link_bilgileri["baslik"];
	$guncelle_url=$link_bilgileri["url"];
	$guncelle_sira=$link_bilgileri["sira"];
	$guncelle_ikon=$link_bilgileri["ikon"];
	$guncelle_class=$link_bilgileri["class"];
	$guncelle_ust_link=$link_bilgileri["ust_link"];
	@$link_guncelle_buton=$_POST["link_guncelle_buton"];
	if ($link_guncelle_buton) {
	$guncelle_baslik=$_POST["baslik"];
	$guncelle_url=$_POST["url"];
	$guncelle_sira=$_POST["sira"];
	$guncelle_ikon=$_POST["ikon"];
	$guncelle_class=$_POST["class"];
	$guncelle_ust_link=$_POST["ust_link"];
	$sql=mysqli_prepare($baglan,"update linkler set baslik=?, url=?, sira=?, ikon=?, class=?, ust_link=? where id=?");
  	$guncelle_sira_int=(int)$guncelle_sira;
  	$guncelle_ust_link_int=(int)$guncelle_ust_link;
  	$link_guncelle_int=(int)$link_guncelle;
  	mysqli_stmt_bind_param($sql,"ssssiii",$guncelle_baslik,$guncelle_url,$guncelle_sira_int,$guncelle_ikon,$guncelle_class,$guncelle_ust_link_int,$link_guncelle_int);
  	mysqli_stmt_execute($sql);
  	mysqli_stmt_close($sql);
	$_SESSION["mesaj"]=array(_LANG('p_link_updated'),"success");
	yonlendir("menuler.php?islem=linkler&menu_id=$menu_id");
	}
}
// Toplu update baÅŸladÄ±
@$buton_toplu_update=$_POST["buton_toplu_update"];
if ($buton_toplu_update) {
$menu_baslik_dizisi=$_POST["toplu_baslik"];
$menu_sira_dizisi=$_POST["toplu_sira"];
$menu_sil_dizisi=$_POST["toplu_sil"];
$menu_url_dizisi=$_POST["toplu_url"];
$menu_ikon_dizisi=$_POST["toplu_ikon"];
$menu_class_dizisi=$_POST["toplu_class"];
foreach ($menu_baslik_dizisi as $link_id=>$yeni_baslik) {
	$guncelle = mysqli_prepare($baglan,"update linkler set baslik=? where id=?");
  	$link_id_int=(int)$link_id;
  	mysqli_stmt_bind_param($guncelle,"si",$yeni_baslik,$link_id_int);
  	mysqli_stmt_execute($guncelle);
  	mysqli_stmt_close($guncelle);
}
foreach ($menu_sira_dizisi as $link_id=>$yeni_sira) {
	$guncelle = mysqli_prepare($baglan,"update linkler set sira=? where id=?");
  	$yeni_sira_int=(int)$yeni_sira;
  	$link_id_int=(int)$link_id;
  	mysqli_stmt_bind_param($guncelle,"ii",$yeni_sira_int,$link_id_int);
  	mysqli_stmt_execute($guncelle);
  	mysqli_stmt_close($guncelle);
}
foreach ($menu_sil_dizisi as $link_id=>$sil_id) {
	$guncelle = mysqli_prepare($baglan,"delete from linkler where id=?");
  	$sil_id_int=(int)$sil_id;
  	mysqli_stmt_bind_param($guncelle,"i",$sil_id_int);
  	mysqli_stmt_execute($guncelle);
  	mysqli_stmt_close($guncelle);
}
foreach ($menu_url_dizisi as $link_id=>$yeni_url) {
	$guncelle = mysqli_prepare($baglan,"update linkler set url=? where id=?");
  	$link_id_int=(int)$link_id;
  	mysqli_stmt_bind_param($guncelle,"si",$yeni_url,$link_id_int);
  	mysqli_stmt_execute($guncelle);
  	mysqli_stmt_close($guncelle);
}
foreach ($menu_ikon_dizisi as $link_id=>$yeni_ikon) {
	$guncelle = mysqli_prepare($baglan,"update linkler set ikon=? where id=?");
  	$link_id_int=(int)$link_id;
  	mysqli_stmt_bind_param($guncelle,"si",$yeni_ikon,$link_id_int);
  	mysqli_stmt_execute($guncelle);
  	mysqli_stmt_close($guncelle);
}
foreach ($menu_class_dizisi as $link_id=>$yeni_class) {
	$guncelle = mysqli_prepare($baglan,"update linkler set class=? where id=?");
  	$link_id_int=(int)$link_id;
  	mysqli_stmt_bind_param($guncelle,"si",$yeni_class,$link_id_int);
  	mysqli_stmt_execute($guncelle);
  	mysqli_stmt_close($guncelle);
}
}
}
$panel_sablon=ayar_getir("panel_sablon");
if ($panel_sablon=="fullwidth") {
	$div_class="col-lg-15 col-md-20 col-sm-30 col-xs-60";
} else if ($panel_sablon=="boxed") {
	$div_class="col-lg-20 col-md-20 col-sm-30 col-xs-60";
}
?>
<!DOCTYPE HTML>
<html lang="<?php echo _DIL_HTML_LANG(); ?>" dir="<?php echo _DIL_HTML_YON(); ?>">
<head>
	<meta charset="UTF-8">
			<title><?php echo _LANG("p_menuler"); ?> - <?php echo is_array($lpanel_baslik) ? implode(", ", $lpanel_baslik) : $lpanel_baslik; ?></title>
	<?php include("inc/header-kod.php"); ?>
	<meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
<div id="wrapper" class="<?php echo is_array($kapsa_container) ? implode(", ", $kapsa_container) : $kapsa_container; ?>">
	<div class="row">
		<div id="sidebar-wrapper" class="<?php echo is_array($sidebar_div) ? implode(", ", $sidebar_div) : $sidebar_div; ?>">
		<?php include("inc/sidebar.php"); ?>
		</div>
		<div id="page-content-wrapper" class="<?php echo is_array($sag_div) ? implode(", ", $sag_div) : $sag_div; ?>">
		<?php include("inc/ust.php"); ?>
			<div class="content">
			<?php if (!$islem) { ?>
			<h3 class="pull-left bolum-baslik"><?php echo _LANG('p_menu_basligi'); ?></h3>
			<a href="menuler.php?islem=ekle" class="btn btn-primary"><?php echo _LANG('p_menu_ekle_k'); ?></a>
			<div class="clearfix mb20"></div>
			<?php $v_say=count($tema_menuleri); ?>
			<div class="alert alert-primary"><?php echo sprintf(_LANG('p_menu_destekliyor'), $v_say); ?>
			<?php
			$v=1; foreach ($tema_menuleri as $yaz => $x) {
			if ($v==$v_say) {echo "<strong>". (is_array($x) ? $x["id"] : $x)."</strong>";}
			else {echo "<strong>". (is_array($x) ? $x["id"] : $x)."</strong>, ";}
			$menude_ara[]=$x["id"];
			$v++; }
			?>
			</div>
			<?php
			$sql=mysqli_query($baglan,"select * from ayarlar where anahtar='menu'");
			$say=mysqli_num_rows($sql);
			if ($say==0) {
				echo "<div class='alert alert-danger'>" . _LANG('p_hic_menu_eklenmemis') . "</div>
					<a href='menuler.php?islem=menuleri_olustur' class='btn btn-default'><i class='fa fa-plus'></i>" . _LANG('p_menuleri_otomatik_olustur') . "</a>";
			}
			?>
			<div class="row">
			<?php
			while($dizi=mysqli_fetch_array($sql)) {
			$menu_id=$dizi["id"];
			$menu_baslik=$dizi["deger"];
			$link_sayisi=link_sayisi($menu_id);
			$menu_pozisyonu=menu_pozisyonu($menu_id);
			if (in_array($menu_pozisyonu,$menude_ara)) {
				$menu_tema_destegi=1;
			} else {
				$menu_tema_destegi=0;
			}
			?>
			<div class="<?php echo is_array($div_class) ? implode(", ", $div_class) : $div_class; ?>">
			<div class="panel <?php if ($menu_pozisyonu!="") { if ($menu_tema_destegi==0) {echo "panel-danger";} else {echo "panel-default";} } else {echo "panel-dark";} ?>">
			<div class="panel-heading"><?php echo is_array($menu_baslik) ? implode(", ", $menu_baslik) : $menu_baslik; ?></div>
				<table class="table table-bordered table-hover">
					<tbody>
						<tr>
							<th width="100"><?php echo _LANG('p_menu_alan'); ?></th>
							<td><?php if ($menu_pozisyonu!="") {
								if ($menu_tema_destegi==1) {
									echo "<span class='label label-success' style='font-size:12px;'>$menu_pozisyonu</span>";
								} else {
											echo "<span class='label label-danger' style='font-size:12px;' data-toggle='tooltip' data-placement='right' title='" . _LANG('p_menu_alanini_desteklemiyor') . "'><i class='fa fa-exclamation-circle'></i>$menu_pozisyonu</span>";
								}
											} else {echo "<span class='label label-default' style='font-size:12px;' data-toggle='tooltip' data-placement='right' title='" . _LANG('p_menu_alani_iliskilendirilmemis') . "'><i class='fa fa-exclamation-circle'></i>" . _LANG('p_bos') . "</span>";} ?></td>
						</tr>
						<tr>
							<th><?php echo _LANG('p_link_sayisi'); ?></th>
							<td><?php echo is_array($link_sayisi) ? implode(", ", $link_sayisi) : $link_sayisi; ?></td>
						</tr>
					</tbody>
				</table>
				<div class="panel-footer">
					<!-- Split button -->
					<div class="btn-group pull-right">
					  <a href="menuler.php?islem=linkler&menu_id=<?php echo is_array($menu_id) ? implode(", ", $menu_id) : $menu_id; ?>" class="btn btn-sm btn-default"><i class="fa fa-pencil" aria-hidden="true"></i><?php echo _LANG('p_guncelle'); ?></a>
					  <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						<span class="caret"></span>
						<span class="sr-only"><?php echo _LANG('p_toggle_dropdown'); ?></span>
					  </button>
					  <ul class="dropdown-menu dropdown-menu-right">
						<li><a href="menuler.php?islem=sil&menu_id=<?php echo is_array($menu_id) ? implode(", ", $menu_id) : $menu_id; ?>" onclick="return confirm('<?php echo is_array($menu_baslik) ? implode(", ", $menu_baslik) : $menu_baslik; ?> <?php echo _LANG("p_menu_sil_onay"); ?>')"><i class="fa fa-trash fa-fw" aria-hidden="true"></i><?php echo _LANG('p_kaldir'); ?></a></li>
					  </ul>
					</div>
					<div class="clearfix"></div>
				</div>
			</div>
			</div>
			<?php } ?>
			</div>
			<?php } else if ($islem=="ekle") { ?>
			<h3 class="pull-left bolum-baslik"><?php echo _LANG('p_menu_ekle'); ?></h3>
			<a href="menuler.php" class="btn btn-primary"><?php echo _LANG('p_menuler'); ?></a>
			<div class="clearfix mb20"></div>
			<div class="panel panel-default">
				<div class="panel-heading"><?php echo _LANG('p_menu_olustur'); ?></div>
				<div class="panel-body">
				<form method="post" action="">
					<?php form_elemani("text","menu_baslik",_LANG('p_menu_title_label'),null,null,null,"required"); ?>
					<?php $tema_menuleri_ayar[]=array("deger"=>"","baslik"=>_LANG('p_select')); select_olustur("menu_pozisyon",_LANG('p_menu_position'),$tema_menuleri_ayar,$menu); ?>
					<?php form_buton("buton",_LANG('p_menu_create'),"primary"); ?>
				</form>
				</div>
			</div>
			<?php } else if ($islem=="linkler") { ?>
			
			<h3 class="pull-left bolum-baslik"><?php echo _LANG('p_menu_guncelle'); ?></h3>
			<a href="menuler.php" class="btn btn-primary"><?php echo _LANG('p_menuler'); ?></a>
			<a href="menuler.php?islem=ekle" class="btn btn-default"><?php echo _LANG('p_yeni_menu_ekle'); ?></a>
			<div class="clearfix mb20"></div>
			<div class="row">
				<div class="col-md-15">
					<div class="panel panel-default">
						<div class="panel-heading"><?php echo _LANG('p_ozel_link'); ?></div>
						<div class="panel-body">
						<form method="post" action="">
							<?php form_elemani("text","baslik",_LANG('p_link_title')); ?>
							<?php form_elemani("text","url",_LANG('p_url')); ?>
							<div class="form-group">
								<label for='ust_link'><?php echo _LANG('p_ust_link'); ?></label>
								<select name="ust_link" class="form-control" id="ust_link">
								<option value="0"><?php echo _LANG('p_yok'); ?></option>
								<?php
								$sql=mysqli_query($baglan,"select * from linkler where ust_link='0' and menu_id='$menu_id'");
								while ($dizi=mysqli_fetch_array($sql)) {
									$l_id=$dizi["id"];
									$baslik=$dizi["baslik"];
								?>
								<option value="<?php echo is_array($l_id) ? implode(", ", $l_id) : $l_id; ?>"><?php echo is_array($baslik) ? implode(", ", $baslik) : $baslik; ?> </option>
								<?php alt_link_listele_select($l_id,5,null,$menu_id); ?>
								<?php } ?>
								</select>
							</div>
							<?php form_elemani("text","ikon",_LANG('p_ikon')); ?>
							<?php form_buton("ozel_link_ekle_buton",_LANG('p_ekle'),"primary"); ?>
						</form>
						</div>
					</div>
				<?php foreach ($icerik_tipleri as $yaz => $x) {
					$tip_name=$x["id"];
					$tip_baslik=$x["baslik"];
					@$tip_gizli=$x["gizli"];
					@$tip_kategori=$x["kategori"];
					@$tip_kategori_ortak=$x["kategori_ortak"];
					@$tip_ekstra_siniflar=$x["ekstra_siniflar"];
					if ($tip_kategori_ortak) {$tip_kategori_name=$tip_kategori_ortak;} else {$tip_kategori_name=$tip_name;}
					if ($tip_gizli!=1) {
				 ?>
					<div class="panel panel-default">
						<div class="panel-heading"><?php echo is_array($tip_baslik) ? implode(", ", $tip_baslik) : $tip_baslik; ?></div>
						<div class="panel-body">
						<ul class="nav nav-tabs" role="tablist">
										<li role="presentation" class="active"><a href="#icerik_<?php echo is_array($tip_name) ? implode(", ", $tip_name) : $tip_name; ?>" aria-controls="icerik_<?php echo is_array($tip_name) ? implode(", ", $tip_name) : $tip_name; ?>" role="tab" data-toggle="tab"><?php echo _LANG("p_icerik"); ?></a></li>
							<?php if ($tip_kategori==1) { 
							
							
							?><li role="presentation"><a href="#kategori_<?php echo is_array($tip_name) ? implode(", ", $tip_name) : $tip_name; ?>" aria-controls="kategori_<?php echo is_array($tip_name) ? implode(", ", $tip_name) : $tip_name; ?>" role="tab" data-toggle="tab"><?php echo _LANG('p_kategoriler'); ?></a></li><?php } ?>
							<?php if ($tip_ekstra_siniflar) { 
							foreach ($tip_ekstra_siniflar as $sinif) {
								$sinif_name=$sinif["id"];
								$sinif_baslik=$sinif["baslik"];
								$sinif_baslik_cogul=$sinif["baslik_cogul"];
							?>
							<li role="presentation"><a href="#<?php echo is_array($sinif_name) ? implode(", ", $sinif_name) : $sinif_name; ?>_<?php echo is_array($tip_name) ? implode(", ", $tip_name) : $tip_name; ?>" aria-controls="<?php echo is_array($sinif_name) ? implode(", ", $sinif_name) : $sinif_name; ?>_<?php echo is_array($tip_name) ? implode(", ", $tip_name) : $tip_name; ?>" role="tab" data-toggle="tab"><?php echo is_array($sinif_baslik_cogul) ? implode(", ", $sinif_baslik_cogul) : $sinif_baslik_cogul; ?></a></li>
							<?php } } ?>
						</ul>
						<div class="tab-content">
							<div role="tabpanel" class="tab-pane active" id="icerik_<?php echo is_array($tip_name) ? implode(", ", $tip_name) : $tip_name; ?>">
								<form method="post" action="">
								<div class="multiple_checkbox_group">
									<?php
									$sql=mysqli_query($baglan,"select * from icerik where tip='$tip_name' order by baslik asc");
									while ($dizi=mysqli_fetch_array($sql)) {
									$id=$dizi["id"];
									$baslik=$dizi["baslik"];
									$tip=$dizi["tip"];
									?>
									<div class="checkbox">
									  <label class="unselect"><input class="isaret_<?php echo is_array($tip_name) ? implode(", ", $tip_name) : $tip_name; ?>" type="checkbox" name="<?php echo is_array($tip_name) ? implode(", ", $tip_name) : $tip_name; ?>[]" value="<?php echo is_array($id) ? implode(", ", $id) : $id; ?>"><?php echo is_array($baslik) ? implode(", ", $baslik) : $baslik; ?></label>
									</div>
									<?php } ?>
								</div>
								<div class="form-group">
									<label for='ust_link'><?php echo _LANG('p_ust_link'); ?></label>
									<select name="ust_link_<?php echo is_array($tip_name) ? implode(", ", $tip_name) : $tip_name; ?>" class="form-control" id="ust_link_<?php echo is_array($tip_name) ? implode(", ", $tip_name) : $tip_name; ?>">
									<option value="0"><?php echo _LANG('p_yok'); ?></option>
									<?php
									$sql=mysqli_query($baglan,"select * from linkler where ust_link='0' and menu_id='$menu_id'");
									while ($dizi=mysqli_fetch_array($sql)) {
										$l_id=$dizi["id"];
										$baslik=$dizi["baslik"];
									?>
									<option value="<?php echo is_array($l_id) ? implode(", ", $l_id) : $l_id; ?>"><?php echo is_array($baslik) ? implode(", ", $baslik) : $baslik; ?> </option>
									<?php alt_link_listele_select($l_id,5,null,$menu_id); ?>
									<?php } ?>
									</select>
								</div>								
									<?php form_buton($tip_name."_link_ekle_buton",_LANG('p_ekle'),"primary"); ?>
											<a id="tumunu_sec_<?php echo is_array($tip_name) ? implode(", ", $tip_name) : $tip_name; ?>" class="pull-right" style="cursor:pointer;"><?php echo _LANG("p_tumunu_sec"); ?></a>
											<a id="secimleri_kaldir_<?php echo is_array($tip_name) ? implode(", ", $tip_name) : $tip_name; ?>" class="pull-right" style="cursor:pointer;"><?php echo _LANG("p_secimleri_kaldir"); ?></a>
									<script type="text/javascript">
									$("#secimleri_kaldir_<?php echo is_array($tip_name) ? implode(", ", $tip_name) : $tip_name; ?>").hide();
									$("#tumunu_sec_<?php echo is_array($tip_name) ? implode(", ", $tip_name) : $tip_name; ?>").click(function(){
										$(".isaret_<?php echo is_array($tip_name) ? implode(", ", $tip_name) : $tip_name; ?>").prop("checked",true);
										$("#tumunu_sec_<?php echo is_array($tip_name) ? implode(", ", $tip_name) : $tip_name; ?>").hide();
										$("#secimleri_kaldir_<?php echo is_array($tip_name) ? implode(", ", $tip_name) : $tip_name; ?>").show();
									});
									$("#secimleri_kaldir_<?php echo is_array($tip_name) ? implode(", ", $tip_name) : $tip_name; ?>").click(function(){
										$(".isaret_<?php echo is_array($tip_name) ? implode(", ", $tip_name) : $tip_name; ?>").prop("checked",false);
										$("#secimleri_kaldir_<?php echo is_array($tip_name) ? implode(", ", $tip_name) : $tip_name; ?>").hide();
										$("#tumunu_sec_<?php echo is_array($tip_name) ? implode(", ", $tip_name) : $tip_name; ?>").show();
									});
									</script>
								</form>
							</div>
							<?php if ($tip_kategori==1) { ?>
							<div role="tabpanel" class="tab-pane" id="kategori_<?php echo is_array($tip_name) ? implode(", ", $tip_name) : $tip_name; ?>">
								<?php
								$sql=mysqli_query($baglan,"select * from kategoriler where tip='$tip_kategori_name' and sinif='kategori' order by baslik asc");
								$toplam=mysqli_num_rows($sql);
								if ($toplam<250) { ?>
								<form method="post" action="">								
								<div class="multiple_checkbox_group">
									<?php 
									while ($dizi=mysqli_fetch_array($sql)) {
									$id=$dizi["id"];
									$baslik=$dizi["baslik"];
									$tip=$dizi["tip"];
									?>
									<div class="checkbox">
									  <label class="unselect"><input class="isaret_k_<?php echo is_array($tip_name) ? implode(", ", $tip_name) : $tip_name; ?>" type="checkbox" name="<?php echo is_array($tip_name) ? implode(", ", $tip_name) : $tip_name; ?>[]" value="<?php echo is_array($id) ? implode(", ", $id) : $id; ?>"><?php echo is_array($baslik) ? implode(", ", $baslik) : $baslik; ?></label>
									</div>
									<?php alt_kategori_listele_checbox($id,5,null,$tip_kategori_name,"kategori"); ?>
									<?php } ?>
								</div>
								<div class="form-group">
									<label for='ust_link'><?php echo _LANG('p_ust_link'); ?></label>
									<select name="ust_link_<?php echo is_array($tip_name) ? implode(", ", $tip_name) : $tip_name; ?>" class="form-control" id="ust_link_<?php echo is_array($tip_name) ? implode(", ", $tip_name) : $tip_name; ?>">
									<option value="0"><?php echo _LANG('p_yok'); ?></option>
									<?php
									$sql=mysqli_query($baglan,"select * from linkler where ust_link='0' and menu_id='$menu_id'");
									while ($dizi=mysqli_fetch_array($sql)) {
										$l_id=$dizi["id"];
										$baslik=$dizi["baslik"];
									?>
									<option value="<?php echo is_array($l_id) ? implode(", ", $l_id) : $l_id; ?>"><?php echo is_array($baslik) ? implode(", ", $baslik) : $baslik; ?> </option>
									<?php alt_link_listele_select($l_id,5,null,$menu_id); ?>
									<?php } ?>
									</select>
								</div>
								<?php form_buton($tip_name."_kategori_ekle_buton",_LANG('p_ekle'),"primary"); ?>
											<a id="tumunu_sec_k_<?php echo is_array($tip_name) ? implode(", ", $tip_name) : $tip_name; ?>" class="pull-right" style="cursor:pointer;"><?php echo _LANG("p_tumunu_sec"); ?></a>
											<a id="secimleri_kaldir_k_<?php echo is_array($tip_name) ? implode(", ", $tip_name) : $tip_name; ?>" class="pull-right" style="cursor:pointer;"><?php echo _LANG("p_secimleri_kaldir"); ?></a>
								<script type="text/javascript">
								$("#secimleri_kaldir_k_<?php echo is_array($tip_name) ? implode(", ", $tip_name) : $tip_name; ?>").hide();
								$("#tumunu_sec_k_<?php echo is_array($tip_name) ? implode(", ", $tip_name) : $tip_name; ?>").click(function(){
									$(".isaret_k_<?php echo is_array($tip_name) ? implode(", ", $tip_name) : $tip_name; ?>").prop("checked",true);
									$("#tumunu_sec_k_<?php echo is_array($tip_name) ? implode(", ", $tip_name) : $tip_name; ?>").hide();
									$("#secimleri_kaldir_k_<?php echo is_array($tip_name) ? implode(", ", $tip_name) : $tip_name; ?>").show();
								});
								$("#secimleri_kaldir_k_<?php echo is_array($tip_name) ? implode(", ", $tip_name) : $tip_name; ?>").click(function(){
									$(".isaret_k_<?php echo is_array($tip_name) ? implode(", ", $tip_name) : $tip_name; ?>").prop("checked",false);
									$("#secimleri_kaldir_k_<?php echo is_array($tip_name) ? implode(", ", $tip_name) : $tip_name; ?>").hide();
									$("#tumunu_sec_k_<?php echo is_array($tip_name) ? implode(", ", $tip_name) : $tip_name; ?>").show();
								});
								</script>
								</form>
											<?php } else {echo "<div class='alert alert-danger' style='margin-top:20px;'>" . _LANG('p_fazla_kategori_menu') . "</div>";} ?>
							</div>
							<?php } ?>
							<?php if ($tip_ekstra_siniflar) { 
							foreach ($tip_ekstra_siniflar as $sinif) {
								$sinif_name=$sinif["id"];
								$sinif_baslik=$sinif["baslik"];
								$sinif_baslik_cogul=$sinif["baslik_cogul"];
							?>
							<div role="tabpanel" class="tab-pane" id="<?php echo is_array($sinif_name) ? implode(", ", $sinif_name) : $sinif_name; ?>_<?php echo is_array($tip_name) ? implode(", ", $tip_name) : $tip_name; ?>">
							<?php 
							$sql=mysqli_query($baglan,"select * from kategoriler where ust_kategori='0' and tip='$tip_kategori_name' and sinif='$sinif_name' order by baslik asc");
							$toplam=mysqli_num_rows($sql);
							if ($toplam<250) {
							?>
								<form method="post" action="">
								<div class="multiple_checkbox_group">
									<?php
																
									while ($dizi=mysqli_fetch_array($sql)) {
									$id=$dizi["id"];
									$baslik=$dizi["baslik"];
									$tip=$dizi["tip"];
									?>
									<div class="checkbox">
									  <label class="unselect"><input class="isaret_k_<?php echo is_array($tip_name) ? implode(", ", $tip_name) : $tip_name; ?>_<?php echo is_array($sinif_name) ? implode(", ", $sinif_name) : $sinif_name; ?>" type="checkbox" name="<?php echo is_array($tip_name) ? implode(", ", $tip_name) : $tip_name; ?>[]" value="<?php echo is_array($id) ? implode(", ", $id) : $id; ?>"><?php echo is_array($baslik) ? implode(", ", $baslik) : $baslik; ?></label>
									</div>
									<?php alt_kategori_listele_checbox($id,5,null,$tip_name,$sinif_name); ?>	
									<?php } ?>
								</div>
								<div class="form-group">
									<label for='ust_link'><?php echo _LANG('p_ust_link'); ?></label>
									<select name="ust_link_<?php echo is_array($tip_name) ? implode(", ", $tip_name) : $tip_name; ?>_<?php echo is_array($sinif_name) ? implode(", ", $sinif_name) : $sinif_name; ?>" class="form-control" id="ust_link_<?php echo is_array($tip_name) ? implode(", ", $tip_name) : $tip_name; ?>">
									<option value="0"><?php echo _LANG('p_yok'); ?></option>
									<?php
									$sql=mysqli_query($baglan,"select * from linkler where ust_link='0' and menu_id='$menu_id'");
									while ($dizi=mysqli_fetch_array($sql)) {
										$l_id=$dizi["id"];
										$baslik=$dizi["baslik"];
									?>
									<option value="<?php echo is_array($l_id) ? implode(", ", $l_id) : $l_id; ?>"><?php echo is_array($baslik) ? implode(", ", $baslik) : $baslik; ?></option>
									<?php alt_link_listele_select($l_id,5,null,$menu_id); ?>
									<?php } ?>
									</select>
								</div>
								<?php form_buton($tip_name."_kategori_ekle_buton",_LANG('p_ekle'),"primary"); ?>
											<a id="tumunu_sec_k_<?php echo is_array($tip_name) ? implode(", ", $tip_name) : $tip_name; ?>_<?php echo is_array($sinif_name) ? implode(", ", $sinif_name) : $sinif_name; ?>" class="pull-right" style="cursor:pointer;"><?php echo _LANG("p_tumunu_sec"); ?></a>
											<a id="secimleri_kaldir_k_<?php echo is_array($tip_name) ? implode(", ", $tip_name) : $tip_name; ?>_<?php echo is_array($sinif_name) ? implode(", ", $sinif_name) : $sinif_name; ?>" class="pull-right" style="cursor:pointer;"><?php echo _LANG("p_secimleri_kaldir"); ?></a>
								<script type="text/javascript">
								$("#secimleri_kaldir_k_<?php echo is_array($tip_name) ? implode(", ", $tip_name) : $tip_name; ?>_<?php echo is_array($sinif_name) ? implode(", ", $sinif_name) : $sinif_name; ?>").hide();
								$("#tumunu_sec_k_<?php echo is_array($tip_name) ? implode(", ", $tip_name) : $tip_name; ?>_<?php echo is_array($sinif_name) ? implode(", ", $sinif_name) : $sinif_name; ?>").click(function(){
									$(".isaret_k_<?php echo is_array($tip_name) ? implode(", ", $tip_name) : $tip_name; ?>_<?php echo is_array($sinif_name) ? implode(", ", $sinif_name) : $sinif_name; ?>").prop("checked",true);
									$("#tumunu_sec_k_<?php echo is_array($tip_name) ? implode(", ", $tip_name) : $tip_name; ?>_<?php echo is_array($sinif_name) ? implode(", ", $sinif_name) : $sinif_name; ?>").hide();
									$("#secimleri_kaldir_k_<?php echo is_array($tip_name) ? implode(", ", $tip_name) : $tip_name; ?>_<?php echo is_array($sinif_name) ? implode(", ", $sinif_name) : $sinif_name; ?>").show();
								});
								$("#secimleri_kaldir_k_<?php echo is_array($tip_name) ? implode(", ", $tip_name) : $tip_name; ?>_<?php echo is_array($sinif_name) ? implode(", ", $sinif_name) : $sinif_name; ?>").click(function(){
									$(".isaret_k_<?php echo is_array($tip_name) ? implode(", ", $tip_name) : $tip_name; ?>_<?php echo is_array($sinif_name) ? implode(", ", $sinif_name) : $sinif_name; ?>").prop("checked",false);
									$("#secimleri_kaldir_k_<?php echo is_array($tip_name) ? implode(", ", $tip_name) : $tip_name; ?>_<?php echo is_array($sinif_name) ? implode(", ", $sinif_name) : $sinif_name; ?>").hide();
									$("#tumunu_sec_k_<?php echo is_array($tip_name) ? implode(", ", $tip_name) : $tip_name; ?>_<?php echo is_array($sinif_name) ? implode(", ", $sinif_name) : $sinif_name; ?>").show();
								});
								</script>
								</form>
										<?php } else {echo "<div class='alert alert-danger' style='margin-top:20px;'>" . _LANG('p_fazla_sinif_menu'), array($sinif_baslik) . "</div>";} ?>
							</div>							
							<?php } } ?>
						</div>
						</div>
					</div>
					<?php } } ?>
				</div>
				<div class="col-md-45">
				<?php if ($link_guncelle) { ?>
					<div class="panel panel-default">
						<div class="panel-heading"><?php echo _LANG('p_link_guncelle'); ?></div>
						<div class="panel-body">
						<form method="post" action="">
							<?php form_elemani("text","baslik",_LANG('p_link_title'),$guncelle_baslik); ?>
							<?php form_elemani("text","url",_LANG('p_url'),$guncelle_url); ?>
							<div class="form-group">
								<label for='ust_link'><?php echo _LANG('p_ust_link'); ?></label>
								<select name="ust_link" class="form-control" id="ust_link">
								<option value="0"><?php echo _LANG('p_yok'); ?></option>
								<?php
								$sql=mysqli_query($baglan,"select * from linkler where ust_link='0' and menu_id='$menu_id' and id!='$link_guncelle'");
								while ($dizi=mysqli_fetch_array($sql)) {
									$id=$dizi["id"];
									$baslik=$dizi["baslik"];
								if ($guncelle_ust_link==$id) {$selected="selected='selected'";} else {$selected="";}
								?>
								<option value="<?php echo is_array($id) ? implode(", ", $id) : $id; ?>" <?php echo is_array($selected) ? implode(", ", $selected) : $selected; ?>><?php echo is_array($baslik) ? implode(", ", $baslik) : $baslik; ?></option>
								<?php alt_link_listele_select($id,5,$guncelle_ust_link,$menu_id); ?>
								<?php } ?>
								</select>
							</div>
							<?php form_elemani("text","sira",_LANG('p_sira'),$guncelle_sira); ?>
							<?php form_elemani("text","ikon",_LANG('p_ikon'),$guncelle_ikon); ?>
							<?php form_elemani("text","class","Class",$guncelle_class); ?>
							<?php form_buton("link_guncelle_buton",_LANG('p_link_update'),"primary"); ?>
						</form>
						</div>
					</div>
				<?php } ?>
			<form action="" method="post">
					<div class="panel panel-default">
						<div class="panel-heading"><?php echo is_array($menu) ? implode(", ", $menu) : $menu; ?></div>
						<?php if ($link_sayisi==0) {echo "<div class='panel-body'>" . _LANG('p_no_links') . "</div>";} else { ?>
						<table class="table table-bordered table-hover">
							<thead>
								<tr>
									<th width="10"><?php echo _LANG('p_sil'); ?></th>
									<th width="260"><?php echo _LANG('p_baslik'); ?></th>
									<th width="410"><?php echo _LANG('p_url'); ?></th>
									<th width="80"><?php echo _LANG('p_sira'); ?></th>
									<th width="90"><?php echo _LANG('p_ikon'); ?></th>
									<th width="90">Class</th>
									<th width="170"><?php echo _LANG('p_islemler'); ?></th>
								</tr>
							</thead>
							<tbody>
								<?php $sql=mysqli_query($baglan,"select * from linkler where ust_link='0' and menu_id='$menu_id' order by sira asc"); while ($link_dizisi=mysqli_fetch_array($sql)) {
								$link_id=$link_dizisi["id"];
								$link_baslik=$link_dizisi["baslik"];
								$link_url=$link_dizisi["url"];
								$link_sira=$link_dizisi["sira"];
								$link_ikon=$link_dizisi["ikon"];
								$link_class=$link_dizisi["class"];
								?>
								<tr>
									<td><input class="isaret" type="checkbox" name="toplu_sil[<?php echo is_array($link_id) ? implode(", ", $link_id) : $link_id; ?>]" value="<?php echo is_array($link_id) ? implode(", ", $link_id) : $link_id; ?>" /></td>
									<td><input type="text" class="form-control input-sm" name="toplu_baslik[<?php echo is_array($link_id) ? implode(", ", $link_id) : $link_id; ?>]" value="<?php echo is_array($link_baslik) ? implode(", ", $link_baslik) : $link_baslik; ?>" /></td>
									<td><input type="text" class="form-control input-sm" name="toplu_url[<?php echo is_array($link_id) ? implode(", ", $link_id) : $link_id; ?>]" value="<?php echo is_array($link_url) ? implode(", ", $link_url) : $link_url; ?>" /></td>
									<td><input type="text" class="form-control input-sm" name="toplu_sira[<?php echo is_array($link_id) ? implode(", ", $link_id) : $link_id; ?>]" value="<?php echo is_array($link_sira) ? implode(", ", $link_sira) : $link_sira; ?>" /></td>
									<td><input type="text" class="form-control input-sm" name="toplu_ikon[<?php echo is_array($link_id) ? implode(", ", $link_id) : $link_id; ?>]" value="<?php echo is_array($link_ikon) ? implode(", ", $link_ikon) : $link_ikon; ?>" /></td>
									<td><input type="text" class="form-control input-sm" name="toplu_class[<?php echo is_array($link_id) ? implode(", ", $link_id) : $link_id; ?>]" value="<?php echo is_array($link_class) ? implode(", ", $link_class) : $link_class; ?>" /></td>
									<td>
									<!-- Split button -->
									<div class="btn-group">
									  <a href="menuler.php?islem=linkler&menu_id=<?php echo is_array($menu_id) ? implode(", ", $menu_id) : $menu_id; ?>&link_guncelle=<?php echo is_array($link_id) ? implode(", ", $link_id) : $link_id; ?>" class="btn btn-default"><i class="fa fa-refresh mr0" aria-hidden="true"></i></a>
									  <a href="<?php echo is_array($link_url) ? implode(", ", $link_url) : $link_url; ?>" target="_blank" class="btn btn-default"><i class="fa fa-external-link mr0" aria-hidden="true"></i></a>
									  <a href="menuler.php?islem=linkler&menu_id=<?php echo is_array($menu_id) ? implode(", ", $menu_id) : $menu_id; ?>&link_sil=<?php echo is_array($link_id) ? implode(", ", $link_id) : $link_id; ?>" class="btn btn-danger"><i class="fa fa-trash mr0" aria-hidden="true"></i></a>
									</div>
								</tr>
								<?php alt_link_listele($link_id,30,$menu_id);?>
								<?php } ?>
							</tbody>
						</table>
						<div class="panel-body">
						<a id="tumunu_sec" style="cursor:pointer;"><?php echo _LANG('p_tumunu_sec'); ?></a>
						<a id="secimleri_kaldir" style="cursor:pointer;"><?php echo _LANG('p_secimleri_kaldir'); ?></a>
						<?php form_buton("buton_toplu_update","" . _LANG('p_update_all') . "","primary pull-right"); ?></div>
						<?php } ?>
				</div>
				</form>
				<div class="well well_limon">
					<form action="" method="post">
						<?php form_elemani("text","menu_baslik",_LANG('p_menu_title_label'),$menu,null,null,"required"); ?>
						<?php $tema_menuleri_ayar[]=array("deger"=>"","baslik"=>_LANG('p_select')); select_olustur("menu_pozisyonu",_LANG('p_menu_position'),$tema_menuleri_ayar,menu_pozisyonu($menu_id)); ?>
						<?php form_buton("menu_pozisyonu_guncelle_buton",_LANG('p_guncelle'),"primary"); ?>
					</form>
				</div>
				</div>
			</div>
			<?php } ?>
			</div>
			<div class="clearfix mb20"></div>
		</div>
	</div>
</div>
<?php include("inc/alt.php"); ?>
<?php include("inc/footer-kod.php"); ?>
<script>
$('#myTabs a').click(function (e) {
  e.preventDefault()
  $(this).tab('show')
})
$("#secimleri_kaldir").hide();
$("#tumunu_sec").click(function(){
	$(".isaret").prop("checked",true);
	$("#tumunu_sec").hide();
	$("#secimleri_kaldir").show();
});
$("#secimleri_kaldir").click(function(){
	$(".isaret").prop("checked",false);
	$("#secimleri_kaldir").hide();
	$("#tumunu_sec").show();
});
</script>
</body>
</html>
