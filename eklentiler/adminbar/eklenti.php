<?php
if(count(get_included_files())==1) {die();}
if (!eklenti_ayar_kontrol("renk","adminbar")) {
	eklenti_ayar_ekle("renk","#333333","adminbar");
}
if (!eklenti_ayar_kontrol("ekle_tip","adminbar")) {
	eklenti_ayar_ekle("ekle_tip",_LANG('tx_yazi_yazi_sayfa_sayfa'),"adminbar");
}
function admin_bar() {
	global $_GET;
	global $_SERVER;
	global $icerik_tipleri;
	@$yonetici_id=$_SESSION["yonetici_id"];
	@$yonetici_ad_soyad=getir("uyeler","ad_soyad",$yonetici_id);
	@$admin_bar_icerik_id=$_GET["id"];
	$php_self=$_SERVER["PHP_SELF"];
	@$admin_bar=null;
	$site_url=site_url();
	$bar_renk=eklenti_ayar_getir("renk","adminbar");
    global $admin_dizini;
	if (@$yonetici_id) {
		if (!strstr(browser_url(),"/$admin_dizini/")) {
		$admin_bar.="
		<div class='admin-bar'>
		<ul>
		<li class='visible-lg visible-md visible-sm'><a href='$site_url/$admin_dizini'><span class='glyphicon glyphicon-cog'></span>Yönetim Paneli</a></li>
		<li class='visible-xs'><a href='$site_url/$admin_dizini'><span class='glyphicon glyphicon-cog'></span>Panel</a></li>";
		if (strstr($php_self, 'kategori.php')) {
			$kategori_tip=bilgi_getir("kategoriler","tip",$admin_bar_icerik_id);
			$kategori_sinif=bilgi_getir("kategoriler","sinif",$admin_bar_icerik_id);
			$admin_bar.="
			<li class='visible-lg visible-md visible-sm'><a href='$site_url/$admin_dizini/kategoriler.php?tip=$kategori_tip&sinif=$kategori_sinif&islem=guncelle&id=$admin_bar_icerik_id'><span class='glyphicon glyphicon-pencil'></span>Kategoriyi Güncelle</a></li>
			<li class='visible-xs'><a href='$site_url/$admin_dizini/kategoriler.php?tip=$kategori_tip&sinif=$kategori_sinif&islem=guncelle&id=$admin_bar_icerik_id'><span class='glyphicon glyphicon-pencil'></span>Güncelle</a></li>
			";
		}
		if (strstr($php_self, 'icerik.php')) {
			$icerik_tip_getir=bilgi_getir("icerik","tip",$admin_bar_icerik_id);
			$admin_bar.="
			<li class='visible-lg visible-md visible-sm'><a href='$site_url/$admin_dizini/icerik.php?tip=$icerik_tip_getir&islem=guncelle&id=$admin_bar_icerik_id'><span class='glyphicon glyphicon-pencil'></span>Güncelle</a></li>
			<li class='visible-xs'><a href='$site_url/$admin_dizini/icerik.php?tip=$icerik_tip_getir&islem=guncelle&id=$admin_bar_icerik_id'><span class='glyphicon glyphicon-pencil'></span>Güncelle</a></li>
			";			
		}
		if (strstr($php_self, 'index.php')) {
			@$magaza=$_GET["magaza"];
			if ($magaza) {
				$admin_bar.="
				<li class='visible-lg visible-md visible-sm'><a href='$site_url/$admin_dizini/temalar.php?tema=limon_ilan&sayfa=panel&islem=magaza-guncelle&id=$magaza'><span class='glyphicon glyphicon-pencil'></span>Güncelle</a></li>
				<li class='visible-xs'><a href='$site_url/$admin_dizini/temalar.php?tema=limon_ilan&sayfa=panel&islem=magaza-guncelle&id=$magaza'><span class='glyphicon glyphicon-pencil'></span>Güncelle</a></li>
				";
			}			
		}
		if (strstr($php_self, 'profil.php')) {
			$admin_bar.="
			<li class='visible-lg visible-md visible-sm'><a href='$site_url/$admin_dizini/uyeler.php?islem=guncelle&id=$admin_bar_icerik_id'><span class='glyphicon glyphicon-pencil'></span>Güncelle</a></li>
			<li class='visible-xs'><a href='$site_url/$admin_dizini/icerik.php?islem=guncelle&id=$admin_bar_icerik_id'><span class='glyphicon glyphicon-pencil'></span>Güncelle</a></li>
			";			
		}		
		$ekle_tip=rtrim(eklenti_ayar_getir("ekle_tip","adminbar"),",");
		if ($ekle_tip) {
			$ekle_tip=str_replace(array("(",")"),null,$ekle_tip);
			$bol_tip=explode(",",$ekle_tip);
			foreach ($bol_tip as $yaz) {
				$tip_parcala=explode(":",$yaz);
				$yaz=$tip_parcala[0];
				$baslik=$tip_parcala[1];
				$admin_bar.="
				<li class='visible-lg visible-md visible-sm'><a href='$site_url/$admin_dizini/icerik.php?tip=$yaz&islem=ekle'><span class='glyphicon glyphicon-plus'></span>$baslik Ekle</a></li>
				<li class='visible-xs'><a href='$site_url/$admin_dizini/icerik.php?tip=$yaz&islem=ekle'><span class='glyphicon glyphicon-plus'></span>$baslik</a></li>
				";
			}			
		}
		for ($i=1;$i<6;$i++) {
			$ekstra_link=eklenti_ayar_getir("link_$i","adminbar");
			if ($ekstra_link) {
				$parcala=explode(",",$ekstra_link);
				$link_title=$parcala[0];
				$link_url=$parcala[1];
				$link_ikon=$parcala[2];
				if (!$link_ikon) {
					$link_ikon="glyphicon glyphicon-link";
				}
				$admin_bar.="
				<li class='visible-lg visible-md visible-sm'><a href='$link_url' target='_blank'><span class='$link_ikon'></span>$link_title</a></li>
				";							
			}
		}
		$admin_bar.="
		<li class='visible-lg visible-md visible-sm pull-right'><a href='$site_url/$admin_dizini/index.php?logout=1'><span class='glyphicon glyphicon-log-out'></span>Çıkış Yap</a></li>
		<li class='visible-lg visible-md visible-sm pull-right'><a href='$site_url/$admin_dizini/uyeler.php?islem=guncelle&id=$yonetici_id'><span class='glyphicon glyphicon-user'></span>Profil Güncelle ($yonetici_ad_soyad)</a></li>		
		</ul>
		</div>
		<style>
		.admin-bar {position:fixed;top:0;width:100%;height:32px;background:$bar_renk;color:#fff;z-index:100000;margin-bottom:32px !important;overflow:hidden !important;}
		.admin-bar ul {padding:0;margin:0;list-style-type:none;}
		.admin-bar ul li {}
		.admin-bar ul li a {float:left;color:#fff;font-size:13px;line-height:32px;text-decoration:none;padding:0 12px;}
		.admin-bar ul li a span.glyphicon, .admin-bar ul li a span.fa {margin-right:8px;}
		.admin-bar ul li a:hover {background:rgba(0,0,0,0.5);}
		body {margin-top:32px;}
		</style>
		";
		} else {
			$admin_bar="";
		}
	} else {
		$admin_bar="";
	}
	return $admin_bar;
}
$foot_kod[]=admin_bar();
?>