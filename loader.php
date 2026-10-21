<?php
if(count(get_included_files())==1) exit("Bu bolgeye girmek tehlikeli ve yasaktir.");
ob_start();
// Error Reporting - Debug modu için hataları göster
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
// E_STRICT sabiti PHP 8.4'te kullanimdan kalkti; referans almak kendisi bir
// deprecation uyarisina neden oluyor. PHP 8'de de anlamsiz, bu yuzden kaldirildi.
error_reporting(E_ALL & ~E_DEPRECATED);
// Define
define('ROOT_DIR', dirname( __FILE__ ));
ini_set('memory_limit','-1');
ini_set('max_execution_time', 120);
ini_set('date.timezone', "Europe/Istanbul");
// Db Connect
if (file_exists(ROOT_DIR."/config/db.php")) {
	require(ROOT_DIR."/config/db.php");
	if (@$host and @$kullanici and @$veritabani) {
		@$baglan=mysqli_connect("$host","$kullanici","$parola","$veritabani");
		mysqli_query($baglan,"set names utf8mb4");
		date_default_timezone_set('Europe/Istanbul');
		/*
			PHP 8.1+ mysqli_report() varsayilanini MYSQLI_REPORT_ERROR |
			MYSQLI_REPORT_STRICT yapti: hatali bir sorgu artik false donmek yerine
			mysqli_sql_exception firlatiyor. Bu yazilim 7.2 uzerinde yazilmis ve
			bu davranisa dayanarak yazilmis - ornegin bilgi_getir() tablo
			sorgusuna where id= degerini dogrudan yapistiriyor ve cagiran taraf
			id gecirmediginde (oturum acmamis ziyaretci gibi) bozuk SQL
			uretmis oluyor. PHP 7'de bu yalnizca bir uyariydi ve kod yoluna
			devam ediyordu; 8.1'de ise sayfayi komple bos bir 200 ile
			olduruyor.

			Ekstra baglanti parametresi verilmedigi icin mysqli_report burada
			PHP 7'deki davranisa geri aliniyor. Hedef surum PHP 8.4.
		*/
		mysqli_report(MYSQLI_REPORT_OFF);
	}
}
session_start();
/*
 * Oturum mesaji her sayfada kullanilir; tanimsiz gelirse PHP 8'de
 * "Undefined array key" uyarisi uretip ciktiyi bozuyor. Varsayilan
 * deger atanir; icerik daha sonra sayfa tarafindan yazilir.
 */
if (!isset($_SESSION["mesaj"]) or !is_array($_SESSION["mesaj"])) {
	$_SESSION["mesaj"] = array();
}
/*
 * Dil altyapisi, yapilandirmadan ONCE yuklenir.
 *
 * config/yapilandir.php icerik tiplerini ve ayar etiketlerini tanimlar
 * ve bu degerler artik _LANG() ile sarmalandigi icin _LANG'in
 * tanimli olmasi gerekir. Onceki sirada (yapilandir once, sonra dil)
 * tanimsiz fonksiyon hatasina dusuluyor ve sayfa bos donuyordu.
 *
 * dil.php yalnizca fonksiyon ve sabit tanimlar; yapilandirmaya
 * bagimli bir sey icermez, dolayisiyla erken yuklenmesi guvenlidir.
 */
require("config/dil.php");
require("config/yapilandir.php");
require("config/fonksiyonlar.php");

$aktif_tema=tema_ne();

require("config/fonksiyonlar_ek.php");
require("config/fonksiyonlar_meta.php");
require("config/fonksiyonlar_url.php");

// Versiyon güncelleme başladı
if (ayar_getir("versiyon")!="1.9.8.64") {
	ayar_guncelle("versiyon","1.9.8.64");
}
// Versiyon güncelleme bitti
$_POST=array_map("tirnak_replace", $_POST);
function get_security($degisken) {
	return strip_tags(addSlashes($degisken));
}
$_GET=array_map("get_security", $_GET);

if (!strstr($_SERVER["REQUEST_URI"], "/kurulum.php") and ayar_getir("site_url")=="") {
	$kurulum_url="http://".$_SERVER['SERVER_NAME'].dirname($_SERVER['PHP_SELF'])."/kurulum.php";
	$kurulum_url=str_replace("//kurulum.php","/kurulum.php",$kurulum_url);
	$kurulum_url=str_replace("/lpanel",null,$kurulum_url);
    if (!strstr($script_name, "/kurulum.php") and browser_url()!=$kurulum_url and file_exists(ROOT_DIR."/kurulum.php")) {
		yonlendir($kurulum_url);
	} else {
		hata(_LANG('tx_mysql_sunucusu_ulasilabilir_degil_lutfen_birka'));
		exit();
	}
}

$oturum_id=null;
$yonetici_id=null;
if (isset($_SESSION["oturum_id"])) {
	$oturum_id=$_SESSION["oturum_id"];
	$oturum_ad_soyad=uye_ad_soyad_getir($oturum_id);
	@$oturum_site_url=$_SESSION["oturum_site_url"];
	if ($oturum_site_url and site_url()!=$oturum_site_url) {
		session_destroy();
		yonlendir(site_url());
	} else {
		$_SESSION["oturum_site_url"]=site_url();
	}
}
if (isset($_SESSION["yonetici_id"])) {
	$yonetici_id=$_SESSION["yonetici_id"];
	@$yonetici_site_url=$_SESSION["yonetici_site_url"];
	if ($yonetici_site_url and site_url()!=$yonetici_site_url) {
		if (getenv("HTTP_HOST")!="localhost") {
			session_destroy();
			yonlendir(site_url());
		}
	} else {
		$_SESSION["yonetici_site_url"]=site_url();
	}
	$yonetici_ad_soyad=uye_ad_soyad_getir($yonetici_id);
}
url_yonlendir();
$aktif_eklentiler=mysqli_query($baglan,"select * from ayarlar where anahtar='eklenti'");
$aktif_eklenti_sayisi=mysqli_num_rows(mysqli_query($baglan,"select * from ayarlar where anahtar='eklenti'"));
	if ($aktif_eklenti_sayisi>0) {
	while ($aktif_eklenti_dizisi=mysqli_fetch_array($aktif_eklentiler,MYSQLI_ASSOC)) {
		$eklenti_klasor=$aktif_eklenti_dizisi["deger"];
		$eklenti_info_yolu=ROOT_DIR."/$eklentiler_dizini/$eklenti_klasor/index.php";
		if (file_exists($eklenti_info_yolu)) {
			include($eklenti_info_yolu);
			$fonksiyon=$bilgi["fonksiyon"];
			$fonksiyon_yolu=ROOT_DIR."/$eklentiler_dizini/$eklenti_klasor/$fonksiyon";
			if ($fonksiyon) {
				if (file_exists($fonksiyon_yolu)) {
					include($fonksiyon_yolu);
				} else {
					$hata_baz_tarih=date("Y-m-d H:i:s",strtotime("-1 minute"));
					$bildirim_metni="$eklenti_klasor adlı eklentinin fonksiyon dosyasına ulaşılamadı.";
					$hata_bildirimi_sayisi=mysqli_num_rows(mysqli_query($baglan,"select * from bildirimler where bildirim='$bildirim_metni' and tip='2' and tarih_saat>'$hata_baz_tarih'"));
					if ($hata_bildirimi_sayisi>10) {
						bildirim_ekle(sprintf(_LANG('bnf_eklenti_cok_fazla_hata_fonksiyon'), $eklenti_klasor),null,2);
					} else {
						bildirim_ekle($bildirim_metni,null,2);
					}
				}
			}
		} else {
			$hata_baz_tarih=date("Y-m-d H:i:s",strtotime("-1 minute"));
			$bildirim_metni="$eklenti_klasor adlı eklentinin bilgi dosyasına ulaşılamadı.";
			$hata_bildirimi_sayisi=mysqli_num_rows(mysqli_query($baglan,"select * from bildirimler where bildirim='$bildirim_metni' and tip='2' and tarih_saat>'$hata_baz_tarih'"));
			if ($hata_bildirimi_sayisi>10) {
				bildirim_ekle(sprintf(_LANG('bnf_eklenti_cok_fazla_hata_bilgi'), $eklenti_klasor),null,2);
			} else {
				bildirim_ekle($bildirim_metni,null,2);
			}
		}
	}
}
include(ROOT_DIR."/$temalar_dizini/$aktif_tema/info.php");
$fonksiyon_dosyasi=$bilgi["fonksiyon"];
if (file_exists(ROOT_DIR."/$temalar_dizini/$aktif_tema/$fonksiyon_dosyasi")) {
	include(ROOT_DIR."/$temalar_dizini/$aktif_tema/$fonksiyon_dosyasi");
}
/* Lpanel oturum kontrolü başladı */
if (strstr(browser_url(), "/$admin_dizini/") and !strstr(browser_url(), "giris-yap.php")) {
	if (!$_SESSION["yonetici_id"]) {
		session_destroy();
		session_start();
		yonlendir("giris-yap.php");
		exit();
	}
}
/* Lpanel oturum kontrolü bitti */
if (strstr(browser_url(), "/$admin_dizini/")) {
	$oturum_yetki=bilgi_getir("uyeler","yetki",$oturum_id);
	$_SESSION["oturum_yetki"]=$oturum_yetki;
	yetki_kontrol();
	$tema_menu_sayisi=count($tema_menuleri);
	if ($tema_menu_sayisi>0) {
		$limonpanel_menu[]=array("baslik"=>_LANG('tx_menuler'),"url"=>"menuler.php","ikon"=>"link","sira"=>35,"yetki"=>"admin");
	}
	if ($tema_destegi==1) {
		$limonpanel_menu[]=array("baslik"=>"Temalar","url"=>"temalar.php","ikon"=>"tv","sira"=>45,"yetki"=>"admin");
	}
	if (count($bilesen_alani)) {
		$limonpanel_menu[]=array("baslik"=>_LANG('tx_bilesenler'),"url"=>"bilesenler.php","ikon"=>"window-restore","sira"=>40,"yetki"=>"admin");
	}
	if (count($liste)) {
		$limonpanel_menu[]=array("baslik"=>"Listeler","url"=>"listeler.php","ikon"=>"list-ul","sira"=>42,"yetki"=>"admin");
	}
	$sistem_hata_sayisi=mysqli_num_rows(mysqli_query($baglan,"select id from bildirimler where tip='2'"));
	if ($sistem_hata_sayisi>0) {
		$hizli_linkler[]=array("baslik"=>"Hata Raporları <div class='badge'>$sistem_hata_sayisi</div>","url"=>"bildirimler.php?tip=hata","ikon"=>"code","sira"=>1000,"yetki"=>"admin");
	}
	if (ayar("sistem_guncellemeleri")!="0") {
		$limonpanel_menu[]=array("baslik"=>_LANG('tx_sistem_guncellemeleri'),"url"=>"guncellemeler.php","ikon"=>"refresh","sira"=>5000,"yetki"=>"admin");
	}
	if (ayar("bakim_modu")==1) {
		$panel_header_alt_kod[]="<div class='alert alert-danger' style='border-radius:0 !important;margin-bottom:0 !important;'><i class='fa fa-exclamation'></i>Dikkat! Bakım modu açık olduğu için sitenize erişilemez. Bakım modunu kapatmak için <a href='ayarlar.php' class='alert-link' style='color:#fff;'>Ayarlar</a> sayfasına göz atın.</div>";
	}
} else {
	if (ayar("bakim_modu")==1) {
		die(_LANG('tx_daha_iyi_bir_kullanici_deneyimi_icin_sistemimi'));
	}
}

if (isset($_COOKIE["auto_login"]) and !@$_SESSION["oturum_id"]) {
	// otomatik giris anahtari cookie'den gelir; sorguya dogrudan
	// yapistirmak SQL injection acigi olur - prepared statement kullanilir
	$auto_login_key=$_COOKIE["auto_login"];
	$oto_giris_sorgu=mysqli_prepare($baglan,"select id from ekstra_bilgi where anahtar='auto_login_key' and deger=? and tip='uye'");
	mysqli_stmt_bind_param($oto_giris_sorgu,"s",$auto_login_key);
	mysqli_stmt_execute($oto_giris_sorgu);
	mysqli_stmt_bind_result($oto_giris_sorgu,$uye_id);
	mysqli_stmt_fetch($oto_giris_sorgu);
	mysqli_stmt_close($oto_giris_sorgu);
	$uye_id=$uye_id ?? null;
	if ($uye_id) {
		$_SESSION["oturum_id"]=$uye_id;
		if (yonetici_mi($uye_id)==true) {
			$_SESSION["yonetici_id"]=$uye_id;
		}
		$ad_soyad=uye_ad_soyad_getir($uye_id);
		$_SESSION["mesaj"]=array(sprintf(_LANG('msj_oturum_oto_acildi'), $ad_soyad),"success");

		// Bildirim gönderiliyor
		$lp_link=lpanel_uye_profil_url($uye_id);
		bildirim_ekle(sprintf(_LANG('bnf_oturum_acildi'), $lp_link, $ad_soyad),$uye_id,0);

		$ip_adresi=ip_adresi();
		$tarih_saat=tarih_saat();
		mysqli_query($baglan,"insert into kayitlar set uye_id='$uye_id', veri_1='$ip_adresi', tarih_saat='$tarih_saat', tip='ip_kayit'");
		yenile();
	}
}

if (@$_SESSION["oturum_degistir"]) {
	$oturum_degistir=$_SESSION["oturum_degistir"];
	if (is_array($oturum_degistir)) {
		@$oturum_degistir_security=$oturum_degistir[0];
		@$oturum_degistir_eski_id=$oturum_degistir[1];
		@$oturum_degistir_yeni_id=$oturum_degistir[2];
		$kontrol=mysqli_fetch_array(mysqli_query($baglan,"select id from kayitlar where tip='oturum_degistir' and veri_2='$oturum_degistir_security' and uye_id='$oturum_degistir_eski_id' and veri_1='$oturum_degistir_yeni_id'"))[0] ?? null;
		if ($kontrol) {
			@$oturum_switch_button=$_POST["oturum_switch_button"];
			if ($oturum_switch_button) {
				$uye_kontrol=mysqli_fetch_array(mysqli_query($baglan,"select COUNT(id) from uyeler where id='$oturum_degistir_eski_id'"))[0] ?? null;
				if ($uye_kontrol==1) {
					session_destroy();
					setcookie("auto_login",null,-60*60*24*365*5,"/");
					session_start();
					$_SESSION["oturum_id"]=$oturum_degistir_eski_id;
					$_SESSION["oturum_site_url"]=site_url();
					if (getir("uyeler","yetki",$oturum_degistir_eski_id)=="admin") {
						$_SESSION["yonetici_id"]=$oturum_degistir_eski_id;
						$_SESSION["yonetici_site_url"]=site_url();
					}
					$admin_ad_soyad=getir("uyeler","ad_soyad",$oturum_degistir_eski_id);
					$uye_ad_soyad=getir("uyeler","ad_soyad",$oturum_id);
					$admin_profil_url=lpanel_uye_profil_url($oturum_degistir_eski_id);
					$uye_profil_url=lpanel_uye_profil_url($oturum_id);
					bildirim_ekle(sprintf(_LANG('bnf_oturum_degistirildi'), $admin_profil_url, $admin_ad_soyad, $uye_profil_url, $uye_ad_soyad),null,0);
					mysqli_query($baglan,"delete from kayitlar where id='$kontrol'");
					$_SESSION["mesaj"]=array(sprintf(_LANG('msj_oturum_sonlandirildi'), $uye_ad_soyad),"success");
					yonlendir(site_url()."/$admin_dizini");
				} else {
					unset($_SESSION["oturum_degistir"]);
					yenile();
				}
			}
			
			$tarih_saat=getir("kayitlar","tarih_saat",$kontrol);
			guncelle("kayitlar","tarih_saat",tarih_saat(),$kontrol);
			
			$oturum_degistir_eski_ad_soyad=getir("uyeler","ad_soyad",$oturum_degistir_eski_id);
			$oturum_degistir_yeni_ad_soyad=getir("uyeler","ad_soyad",$oturum_degistir_yeni_id);
			$site_url=site_url();
			$panel_url=$site_url."/$admin_dizini";
			$panel_bg=ayar("panel_renk");
			$oturum_switcher_html=null;
			$oturum_switcher_html.="
			<form method='post' action='' class='limonsoft_oturum_switcher'><button type='submit' name='oturum_switch_button' value='Değiştir' onclick='return confirm(\"$oturum_degistir_eski_ad_soyad oturumuna geçmek istediğinizden emin misiniz?\");'>$oturum_degistir_eski_ad_soyad oturumuna geçiş yap. (Mevcut oturum: $oturum_degistir_yeni_ad_soyad, Son aktivite: $tarih_saat)</button></form>
			<style>
			.limonsoft_oturum_switcher button {position:fixed;z-index:9999999;top:0;left:0;padding:0;box-shadow:none !important;background:$panel_bg;color:#fff;display:block;width:100%;border:0;margin:0 auto;font-size:13px;height:36px;transition:.1s;}
			.limonsoft_oturum_switcher button:hover {background:#fff;color:$panel_bg;}
			.adminbar {display:none;}
			body {position:relative;top:36px;}
			</style>
			";
			$foot_kod[]=$oturum_switcher_html;
		} else {
			unset($_SESSION["oturum_degistir"]);
			yenile();
		}
	}
}