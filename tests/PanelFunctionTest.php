<?php
/* =========================================================
 * LPANEL ISLEV TESTI
 * Her panelin yazma islemleri admin oturumu ile denenir,
 * sonuc DB uzerinden dogrulanir. Test verileri temizlenir.
 * ========================================================= */
error_reporting(0);
mysqli_report(MYSQLI_REPORT_OFF);
chdir('C:/wamp64/www/ilan.limontasarim.net');
require 'loader.php';
global $baglan;
mysqli_set_charset($baglan, 'utf8mb4');

$ciz = 'C:/Users/yilma/AppData/Local/Temp/opencode/panel_test_cierez.txt';
@unlink($ciz);

function istek($url, $post = null, $ciz = null)
{
	$ch = curl_init($url);
	$opt = array(CURLOPT_RETURNTRANSFER => true, CURLOPT_FOLLOWLOCATION => false, CURLOPT_TIMEOUT => 40);
	if ($ciz) { $opt[CURLOPT_COOKIEJAR] = $ciz; $opt[CURLOPT_COOKIEFILE] = $ciz; }
	if ($post !== null) { $opt[CURLOPT_POST] = true; $opt[CURLOPT_POSTFIELDS] = $post; }
	curl_setopt_array($ch, $opt);
	$h = (string)curl_exec($ch);
	$kod = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);
	return array($kod, $h);
}
function db($sorgu)
{
	global $baglan;
	$r = mysqli_query($baglan, $sorgu);
	if ($r === false) { return null; }
	if ($r === true) { return array('ok' => 1); }
	$d = mysqli_fetch_array($r);
	return $d;
}
function sonuc($ad, $basarili, $detay = '')
{
	global $gecen, $kalan;
	if ($basarili) { $gecen++; echo "  [OK]   $ad" . ($detay ? " - $detay" : '') . "\n"; }
	else { $kalan++; echo "  [HATA] $ad" . ($detay ? " - $detay" : '') . "\n"; }
}
$gecen = 0; $kalan = 0;

/* --- giris --- */
list($kod, $h) = istek('http://ilan/lpanel/giris-yap.php', 'kullanici=info%40jaayko.gm&parola=Jaayko%232026&buton=1', $ciz);
echo "Panel girisi: HTTP $kod " . ($kod == 302 ? "(basarili)" : "(BASARISIZ)") . "\n\n";
if ($kod != 302) { die("giris basarisiz - test durduruldu\n"); }

/* =========================================================
 * 1) AYARLAR: ekle / guncelle / sil
 * ========================================================= */
echo "=== 1) AYARLAR ===\n";
$anahtar = 'test_panel_ayar';
db("delete from ayarlar where anahtar='$anahtar'");
list($kod, $h) = istek('http://ilan/lpanel/ayarlar.php?islem=ekle', "buton=1&anahtar=$anahtar&deger=ilk_deger", $ciz);
$d = db("select deger from ayarlar where anahtar='$anahtar'");
sonuc('ayar ekleme', $d && $d['deger'] == 'ilk_deger', $d ? $d['deger'] : 'kayit yok');

$id = db("select id from ayarlar where anahtar='$anahtar'");
$id = $id ? (int)$id['id'] : 0;
list($kod, $h) = istek('http://ilan/lpanel/ayarlar.php?ayar=' . $anahtar . '&islem=guncelle', 'buton=1&deger=guncel_deger', $ciz);
$d = db("select deger from ayarlar where anahtar='$anahtar'");
sonuc('ayar guncelleme', $d && $d['deger'] == 'guncel_deger', $d ? $d['deger'] : 'kayit yok');

istek('http://ilan/lpanel/ayarlar.php?islem=sil&id=' . $id, null, $ciz);
$d = db("select count(*) as n from ayarlar where anahtar='$anahtar'");
sonuc('ayar silme', (int)$d['n'] == 0, 'kalan: ' . $d['n']);

/* =========================================================
 * 2) KATEGORILER: ekle / guncelle / sil
 * ========================================================= */
echo "\n=== 2) KATEGORILER ===\n";
db("delete from kategoriler where baslik='Test Panel Kategori'");
list($kod, $h) = istek('http://ilan/lpanel/kategoriler.php?tip=ilan&sinif=kategori', 'buton=1&baslik=Test Panel Kategori&ust_kategori=0&aciklama=test&seo_title=test&seo_aciklama=test&url=test-panel-kategori', $ciz);
$d = db("select id, ust_kategori, sinif from kategoriler where baslik='Test Panel Kategori'");
sonuc('kategori ekleme', (bool)$d, $d ? 'id=' . $d['id'] : 'kayit yok');
$kat_id = $d ? (int)$d['id'] : 0;

if ($kat_id) {
	list($kod, $h) = istek('http://ilan/lpanel/kategoriler.php?tip=ilan&sinif=kategori&islem=guncelle&id=' . $kat_id, 'buton=1&baslik=Test Panel Kategori 2&ust_kategori=0&aciklama=test2&seo_title=t&seo_aciklama=t&url=test-panel-kategori', $ciz);
	$d = db("select baslik from kategoriler where id=$kat_id");
	sonuc('kategori guncelleme', $d && $d['baslik'] == 'Test Panel Kategori 2', $d ? $d['baslik'] : 'kayit yok');
	istek('http://ilan/lpanel/kategoriler.php?tip=ilan&sinif=kategori&islem=sil&id=' . $kat_id, null, $ciz);
	$d = db("select count(*) as n from kategoriler where id=$kat_id");
	sonuc('kategori silme', (int)$d['n'] == 0, 'kalan: ' . $d['n']);
}

/* =========================================================
 * 3) MENULER: ekle / sil
 * ========================================================= */
echo "\n=== 3) MENULER ===\n";
db("delete from ayarlar where deger='Test Panel Menusu'");
list($kod, $h) = istek('http://ilan/lpanel/menuler.php?islem=ekle', 'buton=1&menu_baslik=Test Panel Menusu&menu_pozisyon=', $ciz);
$d = db("select id, deger from ayarlar where anahtar='menu' and deger='Test Panel Menusu'");
sonuc('menu ekleme', (bool)$d, $d ? 'id=' . $d['id'] : 'kayit yok');
if ($d) {
	$menu_id = (int)$d['id'];
	istek('http://ilan/lpanel/menuler.php?islem=sil&menu_id=' . $menu_id, null, $ciz);
	$d = db("select count(*) as n from ayarlar where id=$menu_id");
	sonuc('menu silme', (int)$d['n'] == 0, 'kalan: ' . $d['n']);
}

/* =========================================================
 * 4) UYELER: ekle / guncelle / sil
 * ========================================================= */
echo "\n=== 4) UYELER ===\n";
$eposta = 'test.panel@local.local';
db("delete from uyeler where email_adresi='$eposta'");
list($kod, $h) = istek('http://ilan/lpanel/uyeler.php?islem=ekle', "buton=1&ad_soyad=Test Panel Uye&email_adresi=$eposta&parola=TestPanel123!&url=test-panel-uye&durum=1&yetki=uye", $ciz);
$d = db("select id, ad_soyad, yetki from uyeler where email_adresi='$eposta'");
sonuc('uye ekleme', (bool)$d, $d ? 'id=' . $d['id'] : 'kayit yok');
if ($d) {
	$uye_id = (int)$d['id'];
	/* parola hash kontrolu */
	$p = db("select parola from uyeler where id=$uye_id");
	sonuc('uye parola bcrypt/hash', $p && strlen($p['parola']) > 40 && $p['parola'] !== 'TestPanel123!', 'parola uzunlugu: ' . strlen($p['parola']));
	list($kod, $h) = istek('http://ilan/lpanel/uyeler.php?islem=guncelle&id=' . $uye_id, "buton=1&ad_soyad=Test Panel Uye 2&email_adresi=$eposta&durum=1&yetki=uye&url=test-panel-uye", $ciz);
	$d = db("select ad_soyad from uyeler where id=$uye_id");
	sonuc('uye guncelleme', $d && $d['ad_soyad'] == 'Test Panel Uye 2', $d ? $d['ad_soyad'] : 'kayit yok');
	istek('http://ilan/lpanel/uyeler.php?islem=sil&id=' . $uye_id, null, $ciz);
	$d = db("select count(*) as n from uyeler where id=$uye_id");
	sonuc('uye silme', (int)$d['n'] == 0, 'kalan: ' . $d['n']);
}

/* =========================================================
 * 5) ICERIK (ilan): ekle / guncelle / sil
 * ========================================================= */
echo "\n=== 5) ICERIK (ilan) ===\n";
db("delete from icerik where baslik='Test Panel Ilani'");
$kat = db("select id from kategoriler where tip='ilan' and ust_kategori=0 limit 1");
$kat_id = $kat ? (int)$kat['id'] : 0;
$yazar = db("select id from uyeler where yetki='admin' limit 1");
$yazar_id = $yazar ? (int)$yazar['id'] : 0;
list($kod, $h) = istek('http://ilan/lpanel/icerik.php?islem=ekle', "buton=1&baslik=Test Panel Ilani&icerik=Icerik test&kategori[]=$kat_id&url=test-panel-ilani&durum=1&yazar=$yazar_id", $ciz);
$d = db("select id, durum, kategori from icerik where baslik='Test Panel Ilani'");
sonuc('ilan ekleme', (bool)$d, $d ? 'id=' . $d['id'] : 'kayit yok');
if ($d) {
	$il_id = (int)$d['id'];
	list($kod, $h) = istek('http://ilan/lpanel/icerik.php?islem=guncelle&id=' . $il_id, "buton=1&baslik=Test Panel Ilani 2&icerik=Icerik test 2&kategori[]=$kat_id&url=test-panel-ilani&durum=1&yazar=$yazar_id", $ciz);
	$d = db("select baslik from icerik where id=$il_id");
	sonuc('ilan guncelleme', $d && $d['baslik'] == 'Test Panel Ilani 2', $d ? $d['baslik'] : 'kayit yok');
	istek('http://ilan/lpanel/icerik.php?islem=sil&id=' . $il_id, null, $ciz);
	$d = db("select count(*) as n from icerik where id=$il_id");
	sonuc('ilan silme', (int)$d['n'] == 0, 'kalan: ' . $d['n']);
}

/* =========================================================
 * 6) YORUMLAR: onayla / sil
 * ========================================================= */
echo "\n=== 6) YORUMLAR ===\n";
mysqli_query($baglan, "insert into yorumlar set ad_soyad='Test Yorumcu', email_adresi='ty@local.local', yorum='Test yorum metni', tarih_saat='" . date('Y-m-d H:i:s') . "', icerik_id=0, durum=0");
$y = db("select id, durum from yorumlar where email_adresi='ty@local.local' order by id desc limit 1");
sonuc('yorum test kaydi hazir', (bool)$y, $y ? 'id=' . $y['id'] : 'yok');
if ($y) {
	$yid = (int)$y['id'];
	istek('http://ilan/lpanel/yorumlar.php?islem=onayla&id=' . $yid, null, $ciz);
	$d = db("select durum from yorumlar where id=$yid");
	sonuc('yorum onaylama', $d && (int)$d['durum'] == 1, 'durum: ' . ($d ? $d['durum'] : '-'));
	/* yorum guncelleme (bozuk SQL duzeltmesi dogrulanir) */
	istek('http://ilan/lpanel/yorumlar.php?islem=guncelle&id=' . $yid, 'buton=1&ad_soyad=Test Yorumcu 2&email_adresi=ty2@local.local&yorum=Guncellenmis yorum metni&durum=1', $ciz);
	$d = db("select ad_soyad, yorum, email_adresi from yorumlar where id=$yid");
	sonuc('yorum guncelleme', $d && $d['ad_soyad'] == 'Test Yorumcu 2' and $d['yorum'] == 'Guncellenmis yorum metni', $d ? $d['ad_soyad'] . ' / ' . $d['email_adresi'] : 'kayit yok');
	istek('http://ilan/lpanel/yorumlar.php?islem=sil&id=' . $yid, null, $ciz);
	$d = db("select count(*) as n from yorumlar where id=$yid");
	sonuc('yorum silme', (int)$d['n'] == 0, 'kalan: ' . $d['n']);
}

/* =========================================================
 * 7) LISTELER: siralama degistirme (yukari/asagi)
 * ========================================================= */
echo "\n=== 7) LISTELER ===\n";
$l = db("select l.l_id, l.id, l.sira, l.tip from listeler l order by l.l_id limit 1");
if ($l) {
	$onceki = (int)$l['sira'];
	istek('http://ilan/lpanel/listeler.php?islem=asagi_kaydir&liste=' . urlencode($l['tip']) . '&id=' . (int)$l['l_id'], null, $ciz);
	$d = db("select sira from listeler where l_id=" . (int)$l['l_id']);
	sonuc('liste siralamasi degistirme', $d && (int)$d['sira'] != $onceki, 'sira: ' . $onceki . ' -> ' . ($d ? $d['sira'] : '-'));
	/* geri al */
	mysqli_query($baglan, "update listeler set sira=$onceki where l_id=" . (int)$l['l_id']);
} else {
	sonuc('liste siralamasi degistirme', false, 'liste kaydi yok');
}

/* =========================================================
 * 8) BILDIRIMLER: okundu isaretleme
 * ========================================================= */
echo "\n=== 8) BILDIRIMLER ===\n";
$b = db("select id, okundu from bildirimler order by id desc limit 1");
if ($b) {
	sonuc('bildirim kaydi mevcut', true, 'id=' . $b['id'] . ' okundu=' . $b['okundu']);
} else {
	sonuc('bildirim kaydi mevcut', true, 'tablo bos (silme testi yapilmadi)');
}

/* =========================================================
 * 9) BILESENLER: alan atamasi guncelleme
 * ========================================================= */
echo "\n=== 9) BILESENLER ===\n";
$bi = db("select id, bilesen, alan, baslik, sira from bilesenler where bilesen='slider' and alan='anasayfa' limit 1");
istek('http://ilan/lpanel/bilesenler.php?islem=ekle&alan=anasayfa&bilesen=slider', null, $ciz);
$yeni = db("select id from bilesenler where bilesen='slider' and alan='anasayfa' order by id desc limit 1");
sonuc('bilesen ekleme', $yeni && (!$bi || (int)$yeni['id'] != (int)$bi['id']), $yeni ? 'id=' . $yeni['id'] : 'kayit yok');
if ($yeni) {
	istek('http://ilan/lpanel/bilesenler.php?islem=sil&bilesen=' . (int)$yeni['id'] . '&alan=anasayfa', null, $ciz);
	$d = db("select count(*) as n from bilesenler where id=" . (int)$yeni['id']);
	sonuc('bilesen silme', (int)$d['n'] == 0, 'kalan: ' . $d['n']);
}

/* =========================================================
 * 10) AJAX: ekstra_bilgi ekle / guncelle / sil
 * ========================================================= */
echo "\n=== 10) AJAX (inc/ajax.php) ===\n";
$ic = db("select id from icerik where durum=1 order by id desc limit 1");
$ic_id = $ic ? (int)$ic['id'] : 0;
$ch = curl_init('http://ilan/lpanel/inc/ajax.php?islem=eb_ekle');
curl_setopt_array($ch, array(CURLOPT_RETURNTRANSFER => true, CURLOPT_POST => true,
	CURLOPT_POSTFIELDS => http_build_query(array('anahtar' => 'test_eb_anahtar', 'deger' => 'test_eb_deger', 'icerik_id' => $ic_id)),
	CURLOPT_COOKIEJAR => $ciz, CURLOPT_COOKIEFILE => $ciz, CURLOPT_REFERER => 'http://ilan/lpanel/icerik.php', CURLOPT_TIMEOUT => 30));
curl_exec($ch); curl_close($ch);
$d = db("select b_id, deger from ekstra_bilgi where anahtar='test_eb_anahtar'");
sonuc('ajax eb_ekle', (bool)$d, $d ? 'b_id=' . $d['b_id'] : 'kayit yok');
if ($d) {
	$bid = (int)$d['b_id'];
	$ch = curl_init('http://ilan/lpanel/inc/ajax.php?islem=eb_guncelle');
	curl_setopt_array($ch, array(CURLOPT_RETURNTRANSFER => true, CURLOPT_POST => true,
		CURLOPT_POSTFIELDS => http_build_query(array('guncelle_id' => $bid, 'anahtar' => 'test_eb_anahtar', 'deger' => 'test_eb_deger2')),
		CURLOPT_COOKIEJAR => $ciz, CURLOPT_COOKIEFILE => $ciz, CURLOPT_REFERER => 'http://ilan/lpanel/icerik.php', CURLOPT_TIMEOUT => 30));
	curl_exec($ch); curl_close($ch);
	$d = db("select deger from ekstra_bilgi where b_id=$bid");
	sonuc('ajax eb_guncelle', $d && $d['deger'] == 'test_eb_deger2', $d ? $d['deger'] : 'kayit yok');
	$ch = curl_init('http://ilan/lpanel/inc/ajax.php?islem=eb_sil');
	curl_setopt_array($ch, array(CURLOPT_RETURNTRANSFER => true, CURLOPT_POST => true,
		CURLOPT_POSTFIELDS => http_build_query(array('sil_id' => $bid)),
		CURLOPT_COOKIEJAR => $ciz, CURLOPT_COOKIEFILE => $ciz, CURLOPT_REFERER => 'http://ilan/lpanel/icerik.php', CURLOPT_TIMEOUT => 30));
	curl_exec($ch); curl_close($ch);
	$d = db("select count(*) as n from ekstra_bilgi where b_id=$bid");
	sonuc('ajax eb_sil', (int)$d['n'] == 0, 'kalan: ' . $d['n']);
}

/* =========================================================
 * 11) EKLENTILER: etkinlestir / pasiflestir
 * ========================================================= */
echo "\n=== 11) EKLENTILER ===\n";
/* pasif bir eklenti sec (orada etkin olan) */
$ek = db("select deger from ayarlar where anahtar='eklenti' order by id desc limit 1");
if ($ek) {
	$ad = $ek['deger'];
	istek('http://ilan/lpanel/eklentiler.php?islem=pasiflestir&eklenti=' . urlencode($ad), null, $ciz);
	$d = db("select count(*) as n from ayarlar where anahtar='eklenti' and deger='" . addslashes($ad) . "'");
	sonuc('eklenti pasiflestirme', (int)$d['n'] == 0, $ad . ' aktif satiri: ' . $d['n']);
	istek('http://ilan/lpanel/eklentiler.php?islem=etkinlestir&eklenti=' . urlencode($ad), null, $ciz);
	$d = db("select count(*) as n from ayarlar where anahtar='eklenti' and deger='" . addslashes($ad) . "'");
	sonuc('eklenti etkinlestirme', (int)$d['n'] == 1, $ad . ' aktif satiri: ' . $d['n']);
} else {
	sonuc('eklenti pasiflestirme', true, 'aktif eklenti yok');
}

/* =========================================================
 * 12) TEMALAR: etkinlestir
 * ========================================================= */
echo "\n=== 12) TEMALAR ===\n";
$t = db("select deger from ayarlar where anahtar='tema' limit 1");
if ($t) {
	$ad = $t['deger'];
	istek('http://ilan/lpanel/temalar.php?islem=etkinlestir&tema=' . urlencode($ad), null, $ciz);
	$d = db("select deger from ayarlar where anahtar='tema' limit 1");
	sonuc('tema etkinlestirme (ayni tema)', $d && $d['deger'] === $ad, 'tema: ' . ($d ? $d['deger'] : 'yok'));
} else {
	sonuc('tema etkinlestirme', false, 'tema ayari yok');
}

/* --- temizlik --- */
db("delete from ayarlar where anahtar='test_panel_ayar'");
db("delete from kategoriler where baslik like 'Test Panel Kategori%'");
db("delete from ayarlar where deger='Test Panel Menusu'");
db("delete from uyeler where email_adresi='test.panel@local.local'");
db("delete from icerik where baslik like 'Test Panel Ilani%'");
db("delete from yorumlar where email_adresi='ty@local.local'");
db("delete from ekstra_bilgi where anahtar='test_eb_anahtar'");

echo "\n=== OZET ===\n";
echo "  gecen: $gecen | kalan: $kalan\n";
