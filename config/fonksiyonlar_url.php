<?php 
if(count(get_included_files())==1) {die();}
function remove_accent($str) {
  $a = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'Ā', 'ā', 'Ă', 'ă', 'Ą', 'ą', 'Ć', 'ć', 'Ĉ', 'ĉ', 'Ċ', 'ċ', 'Č', 'č', 'Ď', 'ď', 'Đ', 'đ', 'Ē', 'ē', 'Ĕ', 'ĕ', 'Ė', 'ė', 'Ę', 'ę', 'Ě', 'ě', 'Ĝ', 'ĝ', 'Ğ', 'ğ', 'Ġ', 'ġ', 'Ģ', 'ģ', 'Ĥ', 'ĥ', 'Ħ', 'ħ', 'Ĩ', 'ĩ', 'Ī', 'ī', 'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı', 'Ĳ', 'ĳ', 'Ĵ', 'ĵ', 'Ķ', 'ķ', 'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ', 'ľ', 'Ŀ', 'ŀ', 'Ł', 'ł', 'Ń', 'ń', 'Ņ', 'ņ', 'Ň', 'ň', 'ŉ', 'Ō', 'ō', 'Ŏ', 'ŏ', 'Ő', 'ő', 'Œ', 'œ', 'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř', 'Ś', 'ś', 'Ŝ', 'ŝ', 'Ş', 'ş', 'Š', 'š', 'Ţ', 'ţ', 'Ť', 'ť', 'Ŧ', 'ŧ', 'Ũ', 'ũ', 'Ū', 'ū', 'Ŭ', 'ŭ', 'Ů', 'ů', 'Ű', 'ű', 'Ų', 'ų', 'Ŵ', 'ŵ', 'Ŷ', 'ŷ', 'Ÿ', 'Ź', 'ź', 'Ż', 'ż', 'Ž', 'ž', 'ſ', 'ƒ', 'Ơ', 'ơ', 'Ư', 'ư', 'Ǎ', 'ǎ', 'Ǐ', 'ǐ', 'Ǒ', 'ǒ', 'Ǔ', 'ǔ', 'Ǖ', 'ǖ', 'Ǘ', 'ǘ', 'Ǚ', 'ǚ', 'Ǜ', 'ǜ', 'Ǻ', 'ǻ', 'Ǽ', 'ǽ', 'Ǿ', 'ǿ'); 
  $b = array('A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o'); 
  return str_replace($a, $b, $str); 
} 
function url_olustur($baslik) {
	$baslik=remove_accent($baslik);
	$lower=mb_strtolower($baslik,"utf-8");
	$bul=array("ö","ü","ç","ğ","ı","ş","ç"," ","'",".","---","&quot;","&#39;","!",",","?",":","&","’","‘","*","+","=","?","(",")","/","^","<",">","|","[","]","{","}","#","%","_","--","@");
	$degistir=array("o","u","c","g","i","s","c","-",null,null,"-",null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,"-",null,null,null,null,null,null,null,null,null,null,"-",null);
	$cikti=urlencode(trim(stripslashes(str_replace($bul,$degistir,$lower)),"-"));
	return $cikti;
}
function link_getir($icerik_id) {
	global $icerik_tipleri;
	$site_url=site_url();
	$kalici_baglanti=bilgi_getir("icerik","url",$icerik_id);
	$tip=bilgi_getir("icerik","tip",$icerik_id);
	@$tip_custom_url=$icerik_tipleri[$tip]["custom_url"];
	if ($tip_custom_url==1) {
		$custom_url=ekstra_bilgi_getir("custom_url",$icerik_id,$tip);
		if ($custom_url) {
			return $link="$site_url/$custom_url";
			exit();
		}
	}
	return $link="$site_url/$kalici_baglanti-$icerik_id";
}
function kategori_link_getir($kategori_id) {
	$kalici_baglanti=bilgi_getir("kategoriler","url",$kategori_id);
	$tip=bilgi_getir("kategoriler","tip",$kategori_id);
	$sinif=bilgi_getir("kategoriler","sinif",$kategori_id);
	$site_url=site_url();
	return $link="$site_url/$kategori_id/$kalici_baglanti";	
}
function uye_link_getir($uye_id) {
	$kalici_baglanti=bilgi_getir("uyeler","url",$uye_id);
	$site_url=site_url();
	return $link="$site_url/uye/$uye_id/$kalici_baglanti";
}
function search_link_getir($kelime,$fix="ara") {
	$kelime=str_replace(" ","+",$kelime);
	$site_url=site_url();
	return $son_url="$site_url/$fix/$kelime";
}
function url_yonlendir() {
	global $admin_dizini;
	// www yönlendirmesi başladı
	$http_host=$_SERVER["HTTP_HOST"];
	if ($http_host!="localhost") {
		$site_url=site_url();
		$browser_url=browser_url();
		$url_nokta_say=substr_count($browser_url,".");
		if ($url_nokta_say==1 and !strstr($site_url, "www")) {
			if (strstr($site_url, "www")) {$www=1;} else {$www=0;}
			if ($www==1 and !strstr($browser_url, "www")) {
				$url_parcala=explode("://",$browser_url);
				yonlendir($url_parcala[0]."://www.".$url_parcala[1]);
			} else if ($www==0 and strstr($browser_url, "www")) {
				$url_parcala=explode("www.",$browser_url);
				yonlendir($url_parcala[0].$url_parcala[1]);
			}
		}
	}
	// www yönlendirmesi bitti.
	$php_self=$_SERVER["PHP_SELF"];
	$debug=0;
	if (strstr($php_self, 'icerik.php') and !strstr($php_self, $admin_dizini)) {
		$icerik_id=$_GET["id"];
		$icerik_id=$_GET["id"];
		$icerik_url=urlencode(bilgi_getir("icerik","url",$icerik_id));
		$tip=bilgi_getir("icerik","tip",$icerik_id);
		$custom_url=ekstra_bilgi_getir("custom_url",$icerik_id,$tip);
		if ($custom_url) {
			$olmasi_gereken=site_url()."/$custom_url";
		} else {
			$olmasi_gereken=site_url()."/$icerik_url-$icerik_id";
		}
		if ($debug!=1) { 
			if (!$icerik_id or !$icerik_url) {
				yonlendir(site_url()."/404.php");
			}
		}
		if (browser_url()!=$olmasi_gereken and count($_GET)==1) {
			yonlendir($olmasi_gereken);
		}
	}
	if (strstr($php_self, 'kategori.php')) {
		$kategori_id=$_GET["id"];
		$kategori_url=urlencode(bilgi_getir("kategoriler","url",$kategori_id));
		$kategori_sinif=bilgi_getir("kategoriler","sinif",$kategori_id);
		$olmasi_gereken=site_url()."/$kategori_id/$kategori_url";
		if ($debug!=1) { 
			if (!$kategori_id or !$kategori_url) {
				yonlendir(site_url()."/404.php");
			}
		}
		if (browser_url()!=$olmasi_gereken and count($_GET)==1) {
			yonlendir($olmasi_gereken);
		}
	}	
	if (strstr($php_self, 'profil.php')) {
		$uye_id=$_GET["id"];
		$uye_url=urlencode(bilgi_getir("uyeler","url",$uye_id));
		$olmasi_gereken=site_url()."/uye/$uye_id/$uye_url";
		if ($debug!=1) { 
			if (!$uye_id or !$uye_url) {
				yonlendir(site_url()."/404.php");
			}
		}
		if (browser_url()!=$olmasi_gereken and count($_GET)==1) {
			yonlendir($olmasi_gereken);
		}
	}
}
function search_yonlendir($fix="ara") {
	$search=$_POST["s"];
	$search=urlencode(str_replace(" ","+",$search));
	$site_url=site_url();
	yonlendir("$site_url/$fix/$search");
}
function benzersiz_url_olustur($array) {
	$tablo=$array["tablo"];
	$sutun=$array["sutun"];
	@$baslik=$array["baslik"];
	@$url=$array["url"];
	@$id=$array["id"];
	if (!$url) {
		$url=url_olustur($baslik);
		$array["url"]=$url;
	} else {
		$url=url_olustur($url);
		$array["url"]=$url;
	}
	return benzersiz_url_kontrol($array);
}
function benzersiz_url_kontrol($array) {
	global $baglan;
	$tablo=$array["tablo"];
	$sutun=$array["sutun"];
	@$url=$array["url"];
	@$id=$array["id"];
	if ($url) {
		$say=mysqli_num_rows(mysqli_query($baglan,"select id from $tablo where $sutun='$url'"));
		if ($id) {
			$mevcut_url=getir($tablo,$sutun,$id);
		}
		if ($say==0 or ($say==1 and @$mevcut_url==$url)) {
			return $url;
		} else {
			$karakter_sayisi=strlen($url);
			$sonda_rakam=0;
			for ($x=1;$x<=$karakter_sayisi;$x++) {
				$karakter=substr($url,-$x);
				if (is_numeric($karakter)) {
					$sonda_rakam++;
					$son_rakam=$karakter;
					$cizgi_sor=substr($url,-$x-1);
					if ($cizgi_sor=="-$son_rakam") {
						@$sabit=substr($url,0,-$x-1);
						$yeni_url=$sabit."-".($son_rakam+1);
						$array["url"]=$yeni_url;
						return benzersiz_url_kontrol($array);
					}
				}
			}
			if ($sonda_rakam==0 or ($sonda_rakam>0 and !@$sabit)) {
				$yeni_url=$url."-2";
				$array["url"]=$yeni_url;
				return benzersiz_url_kontrol($array);
			}
		}
	} else {
		return _LANG('f_url_hata');
	}
}
function subdomain_url($url) {
	$domain=domain();
	$http_protocol=(!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
	return $http_protocol.$url.".".$domain;
}
?>