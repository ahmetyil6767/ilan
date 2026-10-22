<?php if(count(get_included_files())==1) {die();} ?>
<?php 
$browser_url=browser_url();
$panel_anasayfa_url="index.php";
$panel_anasayfa_url=site_url()."/$admin_dizini/".$panel_anasayfa_url;
if ($panel_anasayfa_url==$browser_url) {$active_yaz="class='active'";} else {$active_yaz="";}
?>
<li <?php echo $active_yaz; ?>><a href="<?php echo $panel_anasayfa_url; ?>"><i class="fa fa-home fa-fw" aria-hidden="true"></i>Anasayfa</a></li>
<?php
foreach ($icerik_tipleri as $yaz => $x) {
	@$t_name=$x["id"];
	@$t_baslik=$x["baslik"];
	@$t_baslik_cogul=$x["baslik_cogul"];
	@$t_kategori=$x["kategori"];
	@$t_kategori_ortak=$x["kategori_ortak"];
	@$t_ekstra_siniflar=$x["ekstra_siniflar"];
	@$t_etiket=$x["etiket"];
	@$t_ikon=$x["ikon"];
	@$t_yetki=$x["yetki"];
	@$t_ekle_link=$x["ekle_link"];
	@$t_menu=$x["menu"];
	@$t_kategori_link=$x["kategori_link"];
	@$alt_linkler_kod=null;
	if ($t_menu!="0") {
		if ($t_kategori_ortak) {$t_kategori_name=$t_kategori_ortak;} else {$t_kategori_name=$t_name;}
		$t_url="icerik.php?tip=$t_name";
		$son_url_tip=site_url()."/$admin_dizini/".$t_url;
		if ($son_url_tip==$browser_url or (strstr($browser_url,$son_url_tip) and isset($_GET["p"]))) {$active_yaz="active";$alt_link_active=1;} else {$active_yaz="";}

		if ($_SESSION["oturum_yetki"]!="yazar") {
			if (($t_kategori=="1" and $t_kategori_link!="0" and !$t_kategori_ortak) or ($t_ekstra_siniflar) or ($t_ekle_link!="0")) {
				$alt_linkler_kod.="<ul>";
			}
			if ($t_ekle_link!="0") {
				$ekle_url="$son_url_tip&islem=ekle";
				if ($ekle_url==$browser_url or strstr($browser_url,$ekle_url)) {$active_alt_yaz="active";$alt_link_active=1;} else {$active_alt_yaz="";}
				$alt_linkler_kod.="<li class='$active_alt_yaz'><a href='$ekle_url'>" . e($t_baslik) . " " . _LANG('p_ekle') . "</a></li>";
			}
			if ($t_kategori=="1" and !$t_kategori_ortak and $t_kategori_link!="0") {
				$k_url="kategoriler.php?tip=$t_kategori_name&sinif=kategori";
				$son_url_kat=site_url()."/$admin_dizini/".$k_url;
				if ($son_url_kat==$browser_url or strstr($browser_url,$son_url_kat)) {$active_alt_yaz="class='active'";$alt_link_active=1;} else {$active_alt_yaz="";}
				$alt_linkler_kod.="<li $active_alt_yaz><a href='$son_url_kat'>" . _LANG('p_kategoriler') . "</a></li>";
			}
			if ($t_ekstra_siniflar) {
				foreach ($t_ekstra_siniflar as $sinif) {
					$s_name=$sinif["id"];
					$s_baslik=$sinif["baslik_cogul"];
					$s_link=$sinif["link"];
					if ($s_link!="0") {
						$s_url="kategoriler.php?tip=$t_kategori_name&sinif=$s_name";
						$son_url_sinif=site_url()."/$admin_dizini/".$s_url;
						if ($son_url_sinif==$browser_url or strstr($browser_url,$son_url_sinif)) {$active_alt_yaz="class='active'";$alt_link_active=1;} else {$active_alt_yaz="";}
						$alt_linkler_kod.="<li $active_alt_yaz><a href='$son_url_sinif'>$s_baslik</a></li>";
					}					
				}
			}
			if (($t_kategori=="1" and $t_kategori_link!="0" and !$t_kategori_ortak) or ($t_ekstra_siniflar) or ($t_ekle_link!="0")) {
				$alt_linkler_kod.="</ul>";
			}
		}
		if (!$t_yetki or strstr($t_yetki,"$oturum_yetki") or $oturum_yetki=="admin") {
			if (isset($alt_link_active) and $alt_link_active==1) {
				$open_yaz="open";
			} else {
				$open_yaz="closed";
			}
			echo "<li class='$active_yaz $open_yaz'><a href='$son_url_tip'><i class='fa fa-$t_ikon fa-fw' aria-hidden='true'></i>$t_baslik_cogul</a>";
			echo $alt_linkler_kod;
			echo "</li>";
			$alt_linkler_kod=null;
			$alt_link_active=0;
		}
		
	}
}
if ($_SESSION["oturum_yetki"]!="yazar") { 
	sksort($limonpanel_menu,"sira",true);
	foreach ($limonpanel_menu as $yaz => $x) {
		@$lp_url=$x["url"];
		@$lp_url=site_url()."/$admin_dizini/".$lp_url;
		@$lp_text=$x["baslik"];
		@$lp_ikon=$x["ikon"];
		@$lp_sira=$x["sira"];
		@$lp_yetki=$x["yetki"];
		@$alt_linkler=$x["alt_linkler"];
		if (!$alt_linkler) {
			$alt_linkler=array();
		}
		if (!$lp_yetki or strstr($lp_yetki,"$oturum_yetki") or $oturum_yetki=="admin") {
			$link_kodlari=null;
			$alt_link_kodlari=null;
			$alt_link_active=null;
			$link_active=null;			
			if ($lp_url==$browser_url or ((strstr($browser_url,$lp_url) and !strstr($browser_url,"sayfa=")) and (strstr($browser_url,$lp_url) and !strstr($browser_url,"?liste=")) and (strstr($browser_url,$lp_url) and !strstr($browser_url,"islem=ayar")))) {
					$active_yaz="active";
					$link_active=1;
				} else {
					$active_yaz="";
				}
			if (count($alt_linkler)==0) {
				$link_kodlari.="<li class='$active_yaz'><a href='$lp_url'><i class='fa fa-$lp_ikon fa-fw' aria-hidden='true'></i>$lp_text</a></li>";
			} else {
				foreach ($alt_linkler as $alt_link) {
					@$lp_alt_url=$alt_link["url"];
					@$lp_alt_son_url=site_url()."/$admin_dizini/".$lp_alt_url;
					@$lp_alt_text=$alt_link["baslik"];
					@$lp_alt_ikon=$alt_link["ikon"];
					@$lp_alt_sira=$alt_link["sira"];
					if ($lp_alt_son_url==$browser_url) {
						$active_alt_yaz="active";
						$alt_link_active=1;
					} else {
						$active_alt_yaz="";
					}
					if (isset($lp_alt_ikon)) {
						$lp_alt_ikon_yaz="<i class='fa fa-$lp_alt_ikon fa-fw' aria-hidden='true'></i>";
					} else {
						$lp_alt_ikon_yaz=null;
					}
					$alt_link_kodlari.="<li class='$active_alt_yaz'><a href='$lp_alt_son_url'>$lp_alt_ikon_yaz$lp_alt_text</a>";
				}	
				if ($alt_link_active==1 or $link_active==1) {
					$open_yaz="open";
				} else {
					$open_yaz="closed";
				}
				if ($alt_link_active==1) {
					$active_yaz=null;
				}
				$link_kodlari.="<li class='$active_yaz $open_yaz'><a href='$lp_url'><i class='fa fa-$lp_ikon fa-fw' aria-hidden='true'></i>$lp_text</a>";
				$link_kodlari.="<ul>";
				$link_kodlari.=$alt_link_kodlari;
				$link_kodlari.="</ul>";
				$link_kodlari.="</li>";
			}
			echo $link_kodlari;
		}
	}
}
?>