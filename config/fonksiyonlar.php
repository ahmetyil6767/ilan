<?php

/* Dosya seviyesinde guvenli tanim: thumb islemlerinde kullanilan $uzanti her zaman
 * tanimli olmayabiliyor (PHP 8 'Undefined variable' uyarisi veriyordu). */
$uzanti = "";


function yazilim_bilgisi()
{
	global $yazilim_bilgisi_array;
	global $yazilim_bilgisi_bayi;
	global $yazilim_bilgisi_guncel_versiyon;
	global $yazilim_bilgisi_son_guncelleme;
	global $yazilim_bilgisi_remove_copyright;
	@$yazilim_bilgisi_json = ayar(__FUNCTION__);

	if (is_object(json_decode($yazilim_bilgisi_json))) {
		$yazilim_bilgisi_array = json_decode($yazilim_bilgisi_json, true);
		@$yazilim_bilgisi_bayi = $yazilim_bilgisi_array['bayi'];
		@$yazilim_bilgisi_guncel_versiyon = $yazilim_bilgisi_array['versiyon'];
		@$yazilim_bilgisi_son_guncelleme = $yazilim_bilgisi_array['son_guncelleme'];
		@$yazilim_bilgisi_remove_copyright = $yazilim_bilgisi_array['remove_copyright'];
	}
}

function kac_gun_kaldi($tarih_saat)
{
	return round((strtotime($tarih_saat) - time()) / 60 / 60 / 24);
}

function domain($domain = NULL)
{
	if (!$domain) {
		$domain = getenv('HTTP_HOST');
	}

	if ($domain != 'localhost') {
		if (substr($domain, 0, 4) == 'www.') {
			$domain = substr($domain, 4);
		}

		$bol = explode('.', $domain);
		$say = count($bol);

		if ($say == '2') {
			$uzanti = $bol[1];
		}
		else if ('2' < $say) {
			$son = $bol[$say - 1];
			if (($son == 'tr') || ($son == 'uk')) {
				$uzanti = $bol[$say - 2] . '.' . $bol[$say - 1];
			}
			else {
				$uzanti = $bol[$say - 1];
			}
		}
		else {
			/* Tek parcali host (ornegin "ilan") - uzanti atanmazsa hata veriyordu */
			$uzanti = $bol[$say - 1];
		}

		$uzanti_ayir = explode('.' . $uzanti, $domain);
		$uzanti_haric = $uzanti_ayir[0];
		$subdomain_kontrol = substr_count($uzanti_haric, '.');

		if ($subdomain_kontrol == 0) {
			$domain = $uzanti_haric . '.' . $uzanti;
		}
		else {
			$subdomain_hazirlik = explode('.', $uzanti_haric);
			$domain = $subdomain_hazirlik[1] . '.' . $uzanti;
		}
	}

	return $domain;
}

function kac_dk_gecti($tarih_saat)
{
	return (time() - strtotime($tarih_saat)) / 60;
}

function tarih_saat_hesapla($deger, $tarih_saat = NULL)
{
	if (!$tarih_saat) {
		$tarih_saat = tarih_saat();
		return $yeni_tarih_saat = date('Y-m-d H:i:s', strtotime((string) $deger));
	}
	else {
		$mktime = mktime_getir($tarih_saat);
		return $yeni_tarih_saat = date('Y-m-d H:i:s', strtotime((string) $deger, $mktime));
	}
}

function bakim_modu_ac()
{
	ayar_guncelle('bakim_modu', 1);
}

function bakim_modu_kapat()
{
	ayar_guncelle('bakim_modu', 0);
}

function bilgi_getir($table, $bilgi, $id = NULL)
{
	if (!$id) {
		@$id = $_GET['id'];
	}

	global $baglan;
	/*
		$id bos veya sayisal degilse sorgu hic calistirilmiyor.
		Onceki hali 'where id=' . $id ile dogrudan birleştiriyordu; id
		gelmediginde (oturum acmamis ziyaretci, GET'te id yok, vs.)
		bozuk SQL uretiliyordu. PHP 7'de bu yalnizca bir uyariydi ve
		$dizi null kaliyordu; PHP 8'de ise mysqli_fetch_array() false
		aldiginda TypeError firlatip tum sayfayi komple dusuruyordu.
		Ayrica $id artik sorguya girmeden once intval() ile normalize
		ediliyor, boylece gelen deger SQL'e dogrudan yazilmaz.
	*/
	$id = is_numeric($id) ? (int) $id : 0;
	if ($id < 1) {
		return NULL;
	}
	/* Tablo ve sutun adi kullanici girdisi olamaz; deger ise prepared
	   statement ile baglanir. */
	$tablo = sql_ad_guvenli($table);
	$sutun = sql_ad_guvenli($bilgi);
	if ($tablo === '' or $sutun === '') {
		return NULL;
	}
	$sorgu = mysqli_prepare($baglan, 'select ' . $sutun . ' from ' . $tablo . ' where id=?');
	mysqli_stmt_bind_param($sorgu, 'i', $id);
	mysqli_stmt_execute($sorgu);
	$sonuc = mysqli_stmt_get_result($sorgu);
	$dizi = $sonuc ? mysqli_fetch_array($sonuc) : null;
	mysqli_stmt_close($sorgu);
	if (!$dizi) {
		return NULL;
	}
	return $dizi[(string) $sutun];
}

function getir($table, $bilgi, $id = NULL)
{
	if (!$id) {
		$id = @$_GET['id'];
	}

	return bilgi_getir($table, $bilgi, $id);
}

function guncelle($tablo, $sutun, $yeni_deger, $id = NULL)
{
	global $baglan;

	if (!$id) {
		@$id = $_GET['id'];
	}

	/*
		Onceki hali sorgunun dondurdugu degeri mysqli_fetch_array()'ye
		veriyordu. Oysa mysqli_query() UPDATE/INSERT/DELETE icin bir
		mysqli_result degil, true dondurur. PHP 7'de mysqli_fetch_array()
		false/true aldiginda yalnizca uyari verip null donuyordu ve
		"$guncelle" null oldugu icin fonksiyon hep false donuyordu - yani
		bu kod zaten yanlis calisiyordu, sadece sessizce. PHP 8'de ayni
		 satir TypeError firlatip sayfayi komple dusuruyor.

		Dogru kontrol: mysqli_query() hata vermediyse true doner. Bu yuzden
		$sorgu dogrudan kontrol ediliyor ve fetch edilmeye calisilmiyor.
		$id de intval() ile normalize ediliyor.
	*/
	$id = is_numeric($id) ? (int) $id : 0;
	if ($id < 1) {
		return false;
	}
	/* Tablo/sutun adi dogrulanir, deger prepared statement ile baglanir. */
	$tablo = sql_ad_guvenli($tablo);
	$sutun = sql_ad_guvenli($sutun);
	if ($tablo === '' or $sutun === '') {
		return false;
	}
	$sorgu = mysqli_prepare($baglan, 'update ' . $tablo . ' set ' . $sutun . '=? where id=?');
	mysqli_stmt_bind_param($sorgu, 'si', $yeni_deger, $id);
	$basarili = mysqli_stmt_execute($sorgu);
	mysqli_stmt_close($sorgu);
	if ($basarili) {
		return true;
	}
	else {
		return false;
	}
}

function id_getir($tablo, $anahtar, $deger)
{
	global $baglan;
	/*
		Sorgu basarisiz olursa mysqli_query() false doner ve
		mysqli_fetch_array() PHP 8'de TypeError firlatir. Onceki hali
		bunu kontrol etmiyordu; eski surumlerde null donup sessizce
		devam ediyordu. Sonuc onceden null'a cekiliyor.
	*/
	$sorgu = mysqli_query($baglan, 'select id from ' . $tablo . ' where ' . $anahtar . '=\'' . $deger . '\'');
	if (!$sorgu) {
		return NULL;
	}
	$dizi = mysqli_fetch_array($sorgu);
	if (!$dizi) {
		return NULL;
	}
	return $id = $dizi['id'];
}

function tarih_getir($id = NULL)
{
	global $tarih_saat_aylar;

	if (!$id) {
		$id = $_GET['id'];
	}

	$tarih_saat = bilgi_getir('icerik', 'tarih_saat', $id);
	$tarih_saat_parcala = explode(' ', $tarih_saat);
	$tarih = $tarih_saat_parcala[0];
	$tarih_parcala = explode('-', $tarih);
	$gun = ltrim($tarih_parcala[2], '0');
	$ay = $tarih_parcala[1];
	$yil = $tarih_parcala[0];

	if (!$tarih_saat_aylar) {
		$tarih_saat_aylar = _LANG('f_aylar');
	}

	/* Ay adi aktif dile gore secilir (bkz. _DIL_AY_AY). Turkce secili
	 * degilse eski dizi kullanilmaya devam eder. */
	$ay_new = _DIL_AY_AY($ay);
	if ($ay_new === $ay) {
		$aylar_dizi = explode(',', $tarih_saat_aylar);
		if (isset($aylar_dizi[$ay - 1])) { $ay_new = $aylar_dizi[$ay - 1]; }
	}
	return $birlestir = $gun . ' ' . $ay_new . ' ' . $yil;
}

function tarih_bicimlendir($tarih)
{
	global $tarih_saat_aylar;
	$tarih_parcala = explode('-', $tarih);
	@$gun = ltrim($tarih_parcala[2], '0');
	@$ay = $tarih_parcala[1];
	@$yil = $tarih_parcala[0];

	if (!$tarih_saat_aylar) {
		$tarih_saat_aylar = _LANG('f_aylar');
	}

	/* Ay adi aktif dile gore secilir; Turkce yedek asagida kalir. */
	@$ay_new = _DIL_AY_AY($ay);
	if ($ay_new === $ay) {$aylar_dizi = explode(',', $tarih_saat_aylar);
		if (isset($aylar_dizi[$ay - 1])) { $ay_new = $aylar_dizi[$ay - 1]; }	}
	return $birlestir = $gun . ' ' . $ay_new . ' ' . $yil;
}

function saat_getir($id = NULL)
{
	if (!$id) {
		$id = $_GET['id'];
	}

	$tarih_saat = bilgi_getir('icerik', 'tarih_saat', $id);
	$tarih_saat_parcala = explode(' ', $tarih_saat);
	$saat = $tarih_saat_parcala[1];
	$saat_karakter = strlen($saat);
	return $saat = substr($saat, 0, $saat_karakter - 3);
}

function mktime_getir($tarih_saat)
{
	if ($tarih_saat) {
		$tarih_saat_parcala = explode(' ', $tarih_saat);
		$tarih = $tarih_saat_parcala[0];
		$saat = $tarih_saat_parcala[1];
		$tarih_parcala = explode('-', $tarih);
		$saat_parcala = explode(':', $saat);
		$gun = $tarih_parcala[2];
		$ay = $tarih_parcala[1];
		$yil = $tarih_parcala[0];
		$saat = $saat_parcala[0];
		$dakika = $saat_parcala[1];
		$saniye = $saat_parcala[2];
		return $sonuc = mktime($saat, $dakika, $saniye, $ay, $gun, $yil);
	}
}

function uye_bilgi_getir($anahtar, $uye_id = NULL)
{
	if (!$uye_id) {
		$uye_id = isset($_GET['id']) ? $_GET['id'] : 0;
	}

	global $baglan;
	$uye_id_int = (int)$uye_id;
	$sorgu = mysqli_prepare($baglan, 'select * from ekstra_bilgi where anahtar=? and id=? and tip=\'uye\'');
	mysqli_stmt_bind_param($sorgu, 'si', $anahtar, $uye_id_int);
	mysqli_stmt_execute($sorgu);
	$sonuc = mysqli_stmt_get_result($sorgu);
	$dizi = $sonuc ? mysqli_fetch_array($sonuc) : null;
	mysqli_stmt_close($sorgu);
	if ($dizi && $dizi['deger'] !== null) {
		return tirnak_ekle($dizi['deger']);
	}
	return '';
}

function uye_bilgi_guncelle($anahtar, $yeni_deger, $uye_id = NULL)
{
	if (!$uye_id) {
		$uye_id = $_GET['id'];
	}

	global $baglan;
	$uye_id_int = (int)$uye_id;
	$say_sorgu = mysqli_prepare($baglan, 'select * from ekstra_bilgi where anahtar=? and id=? and tip=\'uye\'');
	mysqli_stmt_bind_param($say_sorgu, 'si', $anahtar, $uye_id_int);
	mysqli_stmt_execute($say_sorgu);
	$say = mysqli_stmt_num_rows($say_sorgu);
	mysqli_stmt_close($say_sorgu);

	if ($say == 1) {
		$sorgu = mysqli_prepare($baglan, 'update ekstra_bilgi set deger=? where anahtar=? and id=? and tip=\'uye\'');
		mysqli_stmt_bind_param($sorgu, 'ssi', $yeni_deger, $anahtar, $uye_id_int);
		mysqli_stmt_execute($sorgu);
		mysqli_stmt_close($sorgu);
	}
	else {
		$sorgu = mysqli_prepare($baglan, 'insert into ekstra_bilgi set id=?, anahtar=?, deger=?, tip=\'uye\'');
		mysqli_stmt_bind_param($sorgu, 'iss', $uye_id_int, $anahtar, $yeni_deger);
		mysqli_stmt_execute($sorgu);
		mysqli_stmt_close($sorgu);
	}
}

function ekstra_bilgi_getir($anahtar, $yazi_id, $tip = 'yazi')
{
	global $baglan;
	/*
		bilgi_getir() ile ayni sorun: $yazi_id bos/sayisal degilse
		"... and id= and tip=..." seklinde bozuk SQL uretiliyordu.
		Sorgu basarisiz dondugunde mysqli_fetch_array() false aliyor ve
		PHP 8'de TypeError firlatiyordu. Yonetim panelinde
		kategoriler.php her zaman ekstra_bilgi_getir()'i bos $id ile
		cagirir (yeni kategori ekleme ekrani), yani sayfa butun olarak
		200 + 0 byte donuyordu.

		Once $yazi_id dogrulanip sorguya hic girmiyor, sonra da sonuc
		null'a cekiliyor. Ekstra bilgi yoksa beklenen deger null'dir.
	*/
	$yazi_id = is_numeric($yazi_id) ? (int) $yazi_id : 0;
	if ($yazi_id < 1) {
		return NULL;
	}
	$sorgu = mysqli_prepare($baglan, 'select * from ekstra_bilgi where anahtar=? and id=? and tip=?');
	mysqli_stmt_bind_param($sorgu, 'sis', $anahtar, $yazi_id, $tip);
	mysqli_stmt_execute($sorgu);
	$sonuc = mysqli_stmt_get_result($sorgu);
	$dizi = $sonuc ? mysqli_fetch_array($sonuc) : null;
	mysqli_stmt_close($sorgu);
	if (!$dizi) {
		return NULL;
	}
	return tirnak_ekle($dizi['deger']);
}

function ekstra_bilgi_guncelle($anahtar, $yeni_deger, $id, $tip = 'yazi')
{
	global $baglan;
	$id_int = (int)$id;
	$say_sorgu = mysqli_prepare($baglan, 'select * from ekstra_bilgi where anahtar=? and id=? and tip=?');
	mysqli_stmt_bind_param($say_sorgu, 'sis', $anahtar, $id_int, $tip);
	mysqli_stmt_execute($say_sorgu);
	$say = mysqli_stmt_num_rows($say_sorgu);
	mysqli_stmt_close($say_sorgu);

	if ($say == 1) {
		$sorgu = mysqli_prepare($baglan, 'update ekstra_bilgi set deger=? where anahtar=? and id=? and tip=?');
		mysqli_stmt_bind_param($sorgu, 'ssis', $yeni_deger, $anahtar, $id_int, $tip);
		mysqli_stmt_execute($sorgu);
		mysqli_stmt_close($sorgu);
	}
	else {
		$sorgu = mysqli_prepare($baglan, 'insert into ekstra_bilgi set anahtar=?, deger=?, id=?, tip=?');
		mysqli_stmt_bind_param($sorgu, 'ssis', $anahtar, $yeni_deger, $id_int, $tip);
		mysqli_stmt_execute($sorgu);
		mysqli_stmt_close($sorgu);
	}

	return mysqli_insert_id($baglan);
}

function ekstra_bilgi_ekle($anahtar, $yeni_deger, $id, $tip = 'yazi')
{
	global $baglan;
	$id_int = (int)$id;
	$sorgu = mysqli_prepare($baglan, 'insert into ekstra_bilgi set anahtar=?, deger=?, id=?, tip=?');
	mysqli_stmt_bind_param($sorgu, 'ssis', $anahtar, $yeni_deger, $id_int, $tip);
	mysqli_stmt_execute($sorgu);
	mysqli_stmt_close($sorgu);
	return mysqli_insert_id($baglan);
}

function ekstra_bilgi_id_getir($anahtar, $icerik_id, $tip = 'yazi')
{
	global $baglan;
	$icerik_id_int = (int)$icerik_id;
	$sorgu = mysqli_prepare($baglan, 'select * from ekstra_bilgi where anahtar=? and id=? and tip=?');
	mysqli_stmt_bind_param($sorgu, 'sis', $anahtar, $icerik_id_int, $tip);
	mysqli_stmt_execute($sorgu);
	$sonuc = mysqli_stmt_get_result($sorgu);
	$dizi = $sonuc ? mysqli_fetch_array($sonuc) : null;
	mysqli_stmt_close($sorgu);
	if ($dizi) {
		return $dizi['b_id'];
	}
}

function ekstra_bilgi_sil($id)
{
	global $baglan;
	$id_int = (int)$id;
	$sorgu = mysqli_prepare($baglan, 'delete from ekstra_bilgi where b_id=?');
	mysqli_stmt_bind_param($sorgu, 'i', $id_int);
	mysqli_stmt_execute($sorgu);
	mysqli_stmt_close($sorgu);
}

function idden_ekstra_bilgi_getir($id, $bilgi)
{
	global $baglan;
	$id_int = (int)$id;
	$sorgu = mysqli_prepare($baglan, 'select * from ekstra_bilgi where b_id=?');
	mysqli_stmt_bind_param($sorgu, 'i', $id_int);
	mysqli_stmt_execute($sorgu);
	$sonuc = mysqli_stmt_get_result($sorgu);
	$dizi = $sonuc ? mysqli_fetch_array($sonuc) : null;
	mysqli_stmt_close($sorgu);
	return tirnak_ekle($dizi[$bilgi]);
}

function kategori_id_getir($url)
{
	global $baglan;
	$sor = (mysqli_query($baglan, 'select * from kategoriler where url=\'' . $url . '\'') ? (mysqli_query($baglan, 'select * from kategoriler where url=\'' . $url . '\'') ? mysqli_fetch_array(mysqli_query($baglan, 'select * from kategoriler where url=\'' . $url . '\'')) : null) : null);
	return $sor['id'];
}

function icerik_getir($id = NULL)
{
	if (!$id) {
		$id = $_GET['id'];
	}

	global $icerik_ust_kod;
	global $icerik_alt_kod;

	/*
	 * Statik sayfa gövdesi dil seviyesinde cevrilir.
	 *
	 * Once sadece baslik (_DIL_BASLIK) cevriliyordu; govde icerigi
	 * "icerik" sutununda TEK dilli (Turkce) durdugu icin Hakkimizda,
	 * Gizlilik Politikasi, Kullanim Kosullari gibi sayfalar EN/FR/AR
	 * acilisinda da Turkce metin gosteriyordu.
	 *
	 * Cozum: ceviriler ekstra_bilgi tablosunda
	 *    tip='sayfa', anahtar='icerik_<dil>', id=<sayfa id>
	 * olarak tutulur. Aktif dilde ceviri varsa o basilir; yoksa
	 * Turkce orijinal geri doner (eksik ceviri sayfayi bozmaz).
	 */
	$govde = null;

	if (function_exists('aktif_dil')) {
		global $baglan;
		$aktif = aktif_dil();

		/*
		 * Ceviri yalnizca "sayfa" tipi icerikler icin anlamli.
		 * Ilanlarin kullaniciya ait metinleri cevrilmez.
		 */
		$tip = bilgi_getir('icerik', 'tip', $id);
		if ($tip === 'sayfa' && isset($baglan)) {
			$ceviri_sorgu = mysqli_query($baglan,
				"select deger from ekstra_bilgi
				 where tip='sayfa' and id=" . (int) $id . "
				   and anahtar='icerik_" . $aktif . "'
				 order by b_id desc limit 1");
			if ($ceviri_sorgu) {
				$sat = mysqli_fetch_row($ceviri_sorgu);
				if ($sat && trim($sat[0]) !== '') { $govde = $sat[0]; }
			}
		}
	}

	if ($govde === null) { $govde = bilgi_getir('icerik', 'icerik', $id); }

	$sayfa_icerik = tirnak_ekle($govde);

	if (is_array($icerik_ust_kod)) {
		foreach ($icerik_ust_kod as $yaz) {
			@$icerik_ust_ekle = (string) $yaz;
		}
	}

	if (is_array($icerik_alt_kod)) {
		foreach ($icerik_alt_kod as $yaz) {
			@$icerik_alt_ekle = (string) $yaz;
		}
	}

	return $icerik_return = @$icerik_ust_ekle . $sayfa_icerik . @$icerik_alt_ekle;
}

function ayar_getir($ayar)
{
	global $baglan;
	$dizi = (mysqli_query($baglan, 'select * from ayarlar where anahtar=\'' . $ayar . '\'') ? (mysqli_query($baglan, 'select * from ayarlar where anahtar=\'' . $ayar . '\'') ? mysqli_fetch_array(mysqli_query($baglan, 'select * from ayarlar where anahtar=\'' . $ayar . '\'')) : null) : null);
	if (!$dizi) { return ''; }
	return tirnak_ekle($dizi['deger']);
}

function admin_dizini()
{
	@$admin_dizini = ayar_getir(__FUNCTION__);

	if (!@$admin_dizini) {
		$admin_dizini = 'lpanel';
	}

	return $admin_dizini;
}

function ayar_kontrol($ayar)
{
	global $baglan;
	return $dizi = mysqli_num_rows(mysqli_query($baglan, 'select * from ayarlar where anahtar=\'' . $ayar . '\''));
}

function ayar_guncelle($ayar, $yeni_deger)
{
	global $baglan;
	$say = mysqli_num_rows(mysqli_query($baglan, 'select * from ayarlar where anahtar=\'' . $ayar . '\''));

	if ($say == 1) {
		mysqli_query($baglan, 'update ayarlar set deger=\'' . $yeni_deger . '\' where anahtar=\'' . $ayar . '\'');
	}
	else {
		mysqli_query($baglan, 'insert into ayarlar set anahtar=\'' . $ayar . '\', deger=\'' . $yeni_deger . '\'');
	}
}

function ayar_sil($ayar)
{
	global $baglan;
	mysqli_query($baglan, 'delete from ayarlar where anahtar=\'' . $ayar . '\'');
}

function tema_ayar_getir($ayar, $dil = null)
{
	global $baglan;
	$tema = tema_ne();
	
	// If no language specified, use current active language
	if ($dil === null) {
		$dil = aktif_dil();
	}
	
	// First try to get language-specific setting (e.g., jaayquick-footer_metni_en)
	if ($dil !== 'tr') { // Turkish is source language, no _tr suffix needed
		$anahtar_dil = $tema . '-' . $ayar . '_' . $dil;
		$dizi = (mysqli_query($baglan, "select * from ayarlar where anahtar='" . $anahtar_dil . "'") ? (mysqli_query($baglan, "select * from ayarlar where anahtar='" . $anahtar_dil . "'") ? mysqli_fetch_array(mysqli_query($baglan, "select * from ayarlar where anahtar='" . $anahtar_dil . "'")) : null) : null);
		if ($dizi && $dizi['deger'] !== '') {
			return tirnak_ekle($dizi['deger']);
		}
	}
	
	// Fall back to default setting (e.g., jaayquick-footer_metni)
	$dizi = (mysqli_query($baglan, "select * from ayarlar where anahtar='" . $tema . '-' . $ayar . "'") ? (mysqli_query($baglan, "select * from ayarlar where anahtar='" . $tema . '-' . $ayar . "'") ? mysqli_fetch_array(mysqli_query($baglan, "select * from ayarlar where anahtar='" . $tema . '-' . $ayar . "'")) : null) : null);
	if ($dizi && $dizi['deger'] !== null && $dizi['deger'] !== '') {
		return tirnak_ekle($dizi['deger']);
	}
	return '';
}

function tema_ayar_kontrol($ayar)
{
	global $baglan;
	$tema = tema_ne();
	return $dizi = mysqli_num_rows(mysqli_query($baglan, 'select * from ayarlar where anahtar=\'' . $tema . '-' . $ayar . '\''));
}

function tema_ayar_ekle($ayar, $deger)
{
	global $baglan;
	$tema = tema_ne();
	mysqli_query($baglan, 'insert into ayarlar set anahtar=\'' . $tema . '-' . $ayar . '\', deger=\'' . $deger . '\'');
}

function eklenti_ayar_getir($ayar, $eklenti = NULL)
{
	if (!$eklenti) {
		$eklenti = isset($_GET['eklenti']) ? $_GET['eklenti'] : NULL;
	}

	global $baglan;
	$dizi = (mysqli_query($baglan, 'select * from ayarlar where anahtar=\'' . $eklenti . '-' . $ayar . '\'') ? (mysqli_query($baglan, 'select * from ayarlar where anahtar=\'' . $eklenti . '-' . $ayar . '\'') ? mysqli_fetch_array(mysqli_query($baglan, 'select * from ayarlar where anahtar=\'' . $eklenti . '-' . $ayar . '\'')) : null) : null);
	if ($dizi && $dizi['deger'] !== null) {
		return tirnak_ekle($dizi['deger']);
	}
	return '';
}

function eklenti_ayar_kontrol($ayar, $eklenti = NULL)
{
	if (!$eklenti) {
		$eklenti = $_GET['eklenti'];
	}

	global $baglan;
	return $dizi = mysqli_num_rows(mysqli_query($baglan, 'select * from ayarlar where anahtar=\'' . $eklenti . '-' . $ayar . '\''));
}

function eklenti_ayar_ekle($ayar, $deger, $eklenti = NULL)
{
	if (!$eklenti) {
		$eklenti = $_GET['eklenti'];
	}

	global $baglan;
	mysqli_query($baglan, 'insert into ayarlar set anahtar=\'' . $eklenti . '-' . $ayar . '\', deger=\'' . $deger . '\'');
}

function eklenti_durum($eklenti)
{
	global $baglan;
	$say = mysqli_num_rows(mysqli_query($baglan, 'select * from ayarlar where anahtar=\'eklenti\' and deger=\'' . $eklenti . '\''));
	return $say;
}

function eklenti_etkinlestir($eklenti)
{
	global $baglan;
	$say = mysqli_num_rows(mysqli_query($baglan, 'select * from ayarlar where anahtar=\'eklenti\' and deger=\'' . $eklenti . '\''));

	if ($say != 1) {
		mysqli_query($baglan, 'insert into ayarlar set anahtar=\'eklenti\', deger=\'' . $eklenti . '\'');
	}
}

function eklenti_pasiflestir($eklenti)
{
	global $baglan;
	mysqli_query($baglan, 'delete from ayarlar where anahtar=\'eklenti\' and deger=\'' . $eklenti . '\'');
}

  function site_url()
  {
	global $baglan;

	/*
	 * SITE_URL_YEREL: yerel gelistirme ortaminda (ilan / localhost)
	 * istek adresi kullanilir. Boylece uretim icin kayitli alan adi
	 * (jaayquick.com) sabit kalir, yerelde de testler calisir.
	 * Uretimde alan adi "ilan"/"localhost" olmadigi icin bu yol
	 * devreye girmez.
	 */
	$host = isset($_SERVER['HTTP_HOST']) ? strtolower(explode(':', $_SERVER['HTTP_HOST'])[0]) : '';
	if ($host == 'ilan' or $host == 'localhost' or $host == '127.0.0.1' or $host == '::1') {
		$semasi = (isset($_SERVER['HTTPS']) and $_SERVER['HTTPS'] !== '' and strtolower($_SERVER['HTTPS']) != 'off') ? 'https' : 'http';
		return $semasi . '://' . $host;
	}
	$dizi = (mysqli_query($baglan, 'select * from ayarlar where anahtar=\'site_url\'') ? (mysqli_query($baglan, 'select * from ayarlar where anahtar=\'site_url\'') ? mysqli_fetch_array(mysqli_query($baglan, 'select * from ayarlar where anahtar=\'site_url\'')) : null) : null);
	return $dizi['deger'];
  }

function alt_kategori_sayisi($kategori_id)
{
	global $baglan;
	$sql = mysqli_query($baglan, 'select * from kategoriler where ust_kategori=' . $kategori_id);
	$alt_kat_idler = NULL;

	while ($dizi = mysqli_fetch_array($sql)) {
		$alt_kat_id = $dizi['id'];
		@$alt_kat_idler .= $alt_kat_id . ',';
	}

	$alt_kategori_dizi = explode(',', $alt_kat_idler);
	return $alt_kategori_sayi = count($alt_kategori_dizi) - 1;
}

function kategori_adi_getir($kategori_id)
{
	global $baglan;
	$kategori_dizi = (mysqli_query($baglan, 'select baslik from kategoriler where id=\'' . $kategori_id . '\'') ? (mysqli_query($baglan, 'select baslik from kategoriler where id=\'' . $kategori_id . '\'') ? mysqli_fetch_array(mysqli_query($baglan, 'select baslik from kategoriler where id=\'' . $kategori_id . '\'')) : null) : null);
	return $kategori_adi = ($kategori_dizi and isset($kategori_dizi['baslik'])) ? $kategori_dizi['baslik'] : '';
}

function alt_kat_idler($kat_id = NULL)
{
	if (!$kat_id) {
		$kat_id = $_GET['id'];
	}

	global $baglan;
	$alt_kategori_sayisi = alt_kategori_sayisi($kat_id);
	$alt_kat_idler = $kat_id;

	if (0 < $alt_kategori_sayisi) {
		$alt_kat_sql = mysqli_query($baglan, 'select * from kategoriler where ust_kategori=\'' . $kat_id . '\'');

		while ($alt_kat_dizi = mysqli_fetch_array($alt_kat_sql)) {
			$alt_kat_id = $alt_kat_dizi['id'];
			$alt_kat_idler .= ',' . $alt_kat_id;
		}
	}

	return $alt_kat_idler;
}

function alt_kategori_listele($kategori_id, $padding = 10, $tip = null, $sinif_name = null)
{
	$kademe = 1;
	global $baglan;
	$sql = mysqli_query($baglan, 'SELECT * FROM kategoriler WHERE ust_kategori=\'' . $kategori_id . '\' and tip=\'' . $tip . '\'');

	while ($dizi = mysqli_fetch_array($sql)) {
		$id = $dizi['id'];
		$alt_kategori_sayisi = alt_kategori_sayisi($id);
		$baslik = $dizi['baslik'];
		$icerik = $dizi['icerik'];
		$aciklama = ozet_olustur(ekstra_bilgi_getir('aciklama', $id, 'kategori', $tip), 7);

		if ($aciklama) {
			$aciklama .= '...';
		}

		if (!empty($dizi)) {
			$padding_yaz = $padding . 'px';
			echo "\t\t\t" . '<tr>' . "\n\t\t\t\t" . '<th>';
			echo $id;
			echo '</th>' . "\n\t\t\t\t" . '<td style="padding-left:';
			echo $padding_yaz;
			echo ';"><a href="kategoriler.php?tip=';
			echo $tip;
			echo '&islem=guncelle&id=';
			echo $id;
			echo '">';
			echo $baslik;
			echo '</a> ';

			if (0 < $alt_kategori_sayisi) {
				echo '<span style=\'font-size:11px;color:#bbb;margin-left:10px;\'>(' . $alt_kategori_sayisi . ' alt kategori)</span>';
			}

			echo '</td>' . "\n\t\t\t\t" . '<td class="visible-md visible-lg">';
			echo $aciklama;
			echo '</td>' . "\n\t\t\t\t" . '<td>';
			echo $icerik;
			echo '</td>' . "\n\t\t\t\t" . '<td>' . "\n\t\t\t\t" . '<!-- Split button -->' . "\n\t\t\t\t" . '<div class="btn-group">' . "\n\t\t\t\t" . '  <a href="kategoriler.php?tip=';
			echo $tip;
			echo '&sinif=';
			echo $sinif_name;
			echo '&islem=guncelle&id=';
			echo $id;
			echo '" class="btn btn-xs btn-primary"><i class="fa fa-pencil" aria-hidden="true"></i>' . _LANG("f_guncelle") . '</a>' . "\n\t\t\t\t" . '  <button type="button" class="btn btn-primary btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">' . "\n\t\t\t\t\t" . '<span class="caret"></span>' . "\n\t\t\t\t\t" . '<span class="sr-only">Toggle Dropdown</span>' . "\n\t\t\t\t" . '  </button>' . "\n\t\t\t\t" . '  <ul class="dropdown-menu">' . "\n\t\t\t\t\t" . '<li><a href="';
			echo kategori_link_getir($id);
			echo '" target="_blank"><i class="fa fa-external-link fa-fw" aria-hidden="true"></i>' . _LANG("f_goruntule") . '</a></li>' . "\n\t\t\t\t\t" . '<li><a href="kategoriler.php?tip=';
			echo $tip;
			echo '&sinif=';
			echo $sinif_name;
			echo '&islem=sil&id=';
			echo $id;
			echo '" onclick="return confirm(\'';
			echo $baslik;
			echo ''. _LANG('f_kategori_sil_onay') . '\')"><i class="fa fa-trash fa-fw" aria-hidden="true"></i>' . _LANG("f_sil") . '</a></li>' . "\n\t\t\t\t" . '  </ul>' . "\n\t\t\t\t" . '</div>' . "\n\t\t\t\t" . '</td>' . "\n\t\t\t" . '</tr>' . "\n\t\t\t";
			alt_kategori_listele($id, $padding + 30, $tip);
		}
	}
}

function alt_kategori_listele_select($kategori_id, $bosluk = 2, $value = null, $tip = null, $sinif = null)
{
	global $baglan;
	$sql = mysqli_query($baglan, 'SELECT * FROM kategoriler WHERE ust_kategori=\'' . $kategori_id . '\' and tip=\'' . $tip . '\' and sinif=\'' . $sinif . '\' order by baslik asc');

	while ($dizi = mysqli_fetch_array($sql, MYSQL_ASSOC)) {
		$id = $dizi['id'];
		$baslik = $dizi['baslik'];

		if (!empty($dizi)) {
			$bosluk_yaz = str_repeat('&nbsp;', $bosluk);

			if ($value != '') {
				if ($value == $id) {
					$selected = 'selected=\'selected\'';
				}
				else {
					$selected = '';
				}
			}

			echo '<option value=\'' . $id . '\'' . $selected . '>' . $bosluk_yaz . ' ' . $baslik . '</option>';
			alt_kategori_listele_select($id, $bosluk + 2, $value, $tip, $sinif);
		}
	}
}

function alt_kategori_listele_checbox($kategori_id, $bosluk = 1, $value = null, $tip = null, $sinif = null)
{
	global $baglan;
	$sql = mysqli_query($baglan, 'SELECT * FROM kategoriler WHERE ust_kategori=\'' . $kategori_id . '\' and tip=\'' . $tip . '\' and sinif=\'' . $sinif . '\' order by baslik asc');

	while ($dizi = mysqli_fetch_array($sql, MYSQL_ASSOC)) {
		$id = $dizi['id'];
		$baslik = $dizi['baslik'];

		if (!empty($dizi)) {
			$bosluk_yaz = str_repeat('&nbsp;', $bosluk);

			if ($value != '') {
				if (strstr($value, '(' . $id . ')')) {
					$checked = 'checked=\'checked\'';
				}
				else {
					$checked = '';
				}
			}

			echo '<div class=\'checkbox\'>' . "\n\t\t\t\t" . '  <label>' . $bosluk_yaz . '<input type=\'checkbox\' name=\'kategori[]\' value=\'' . $id . '\' ' . $checked . '>' . $baslik . '</label>' . "\n\t\t\t\t" . '</div>';
			alt_kategori_listele_checbox($id, $bosluk + 6, $value, $tip, $sinif);
		}
	}
}

function alt_link_listele($link_id, $padding = 10, $menu_id = null)
{
	global $baglan;
	$sql = mysqli_query($baglan, 'SELECT * FROM linkler WHERE ust_link=\'' . $link_id . '\' and menu_id=\'' . $menu_id . '\' order by sira asc');

	while ($dizi = mysqli_fetch_array($sql)) {
		$id = $dizi['id'];
		$baslik = $dizi['baslik'];
		$url = $dizi['url'];
		$sira = $dizi['sira'];
		$ikon = $dizi['ikon'];
		$class = $dizi['class'];

		if (!empty($dizi)) {
			$padding_yaz = $padding . 'px';
			echo "\t\t\t" . '<tr>' . "\n\t\t\t\t" . '<td><input class="isaret" type="checkbox" name="toplu_sil[';
			echo $id;
			echo ']" value="';
			echo $id;
			echo '" /></td>' . "\n\t\t\t\t" . '<td style="padding-left:';
			echo $padding_yaz;
			echo ';"><input type="text" class="form-control input-sm" name="toplu_baslik[';
			echo $id;
			echo ']" value="';
			echo $baslik;
			echo '" /></td>' . "\n\t\t\t\t" . '<td><input type="text" class="form-control input-sm" name="toplu_url[';
			echo $id;
			echo ']" value="';
			echo $url;
			echo '" /></td>' . "\n\t\t\t\t" . '<td><input type="text" class="form-control input-sm" name="toplu_sira[';
			echo $id;
			echo ']" value="';
			echo $sira;
			echo '" /></td>' . "\n\t\t\t\t" . '<td><input type="text" class="form-control input-sm" name="toplu_ikon[';
			echo $id;
			echo ']" value="';
			echo $ikon;
			echo '" /></td>' . "\n\t\t\t\t" . '<td><input type="text" class="form-control input-sm" name="toplu_class[';
			echo $id;
			echo ']" value="';
			echo $class;
			echo '" /></td>' . "\n\t\t\t\t" . '<td>' . "\n\t\t\t\t" . '<!-- Split button -->' . "\n\t\t\t\t" . '<div class="btn-group">' . "\n\t\t\t\t" . '  <a href="menuler.php?islem=linkler&menu_id=';
			echo $menu_id;
			echo '&link_guncelle=';
			echo $id;
			echo '" class="btn btn-default"><i class="fa fa-refresh mr0" aria-hidden="true"></i></a>' . "\n\t\t\t\t" . '  <a href="';
			echo $url;
			echo '" target="_blank" class="btn btn-default"><i class="fa fa-external-link mr0" aria-hidden="true"></i></a>' . "\n\t\t\t\t" . '  <a href="menuler.php?islem=linkler&menu_id=';
			echo $menu_id;
			echo '&link_sil=';
			echo $id;
			echo '" class="btn btn-danger"><i class="fa fa-trash mr0" aria-hidden="true"></i></a>' . "\n\t\t\t\t" . '</div>' . "\n\t\t\t\t" . '</td>' . "\n\t\t\t" . '</tr>' . "\n\t\t\t";
			alt_link_listele($id, $padding + $padding, $menu_id);
		}
	}
}

function alt_link_listele_select($link_id, $bosluk = 1, $value = null, $menu_id = null, $link_guncelle = NULL)
{
	global $baglan;
	if (!$link_guncelle && isset($_GET['link_guncelle'])) {
		$link_guncelle = @$_GET['link_guncelle'];
		$sql = mysqli_query($baglan, 'SELECT * FROM linkler WHERE ust_link=\'' . $link_id . '\' and menu_id=\'' . $menu_id . '\' and id!=\'' . $link_guncelle . '\' order by sira asc');
	}
	else {
		$link_guncelle = NULL;
		$sql = mysqli_query($baglan, 'SELECT * FROM linkler WHERE ust_link=\'' . $link_id . '\' and menu_id=\'' . $menu_id . '\' order by sira asc');
	}

	while ($dizi = mysqli_fetch_array($sql)) {
		$id = $dizi['id'];
		$baslik = $dizi['baslik'];

		if (!empty($dizi)) {
			$bosluk_yaz = str_repeat('&nbsp;', $bosluk);

			if ($value != '') {
				if ($value == $id) {
					$selected = 'selected=\'selected\'';
				}
				else {
					$selected = '';
				}
			}

			echo '<option value=\'' . $id . '\'' . $selected . '>' . $bosluk_yaz . ' ' . $baslik . '</option>';
			alt_link_listele_select($id, $bosluk + 6, $value, $menu_id);
		}
	}
}

function autop($text)
{
	return '<p>' . implode('</p>' . "\n\n" . '<p>', preg_split('/\\n(?:\\s*\\n)+/', $text)) . '</p>';
}

function bildirim_ekle($bildirim, $uye_id, $tip = 0, $ek_tip = 0)
{
	global $baglan;

	/*
	 * Iki imza desteklenir:
	 *  1) bildirim_ekle($metin, $uye_id, $tip)
	 *  2) bildirim_ekle($sablon, array($link, $ad), $uye_id, $tip)   (eski imza)
	 * 2. imzada $uye_id dizi gelirse sablon vsprintf ile doldurulur.
	 */
	if (is_array($uye_id)) {
		$ekstra = $uye_id;
		$uye_id = $tip;
		$tip = $ek_tip;
		if (is_array($bildirim)) { $bildirim = implode(' ', $bildirim); }
		elseif (is_array($ekstra) and $ekstra) {
			/* %s yerine sirayla deger yazilir (vsprintf'dan daha esnek:
			 * metindeki ek % isaretleri veya eksik arguman hataya yol acmaz) */
			$sira = 0;
			$bildirim = preg_replace_callback('/%s/', function ($m) use ($ekstra, &$sira) {
				if (isset($ekstra[$sira])) { $deger = $ekstra[$sira]; $sira++; return is_array($deger) ? implode(' ', $deger) : (string)$deger; }
				return '';
			}, $bildirim);
		}
	}
	if (is_array($bildirim)) { $bildirim = implode(' ', $bildirim); }

	$bildirimler = ayar_getir('bildirimler');

	if ($bildirimler == 1) {
		$tarih_saat = tarih_saat();
		$bildirim = tirnak_ekle($bildirim);
		$sql = mysqli_query($baglan, 'insert into bildirimler set bildirim=\'' . $bildirim . '\', uye_id=\'' . $uye_id . '\', tarih_saat=\'' . $tarih_saat . '\', tip=\'' . $tip . '\', okundu=\'0\'');
	}
}

function bildirim_sil($bildirim_id)
{
	global $baglan;
	mysqli_query($baglan, 'delete from bildirimler where id=\'' . $bildirim_id . '\'');
}

function thumbnail_url($boyut, $image_url, $mevcut_boyut = NULL, $url_kontrol = false)
{
	global $uzanti;
	$image_url = bosluk_donustur($image_url);
	$uzanti = '.' . uzanti_ne($image_url);
	$hazirlik = explode($uzanti, $image_url);

	if ($mevcut_boyut) {
		$hazirlik = explode('-' . $mevcut_boyut, $hazirlik[0]);
	}

	if ($boyut == 'full') {
		$son_url = $hazirlik[0] . $uzanti;
	}
	else {
		$son_url = $hazirlik[0] . '-' . $boyut . $uzanti;
	}

	if ($url_kontrol) {
		if (url_kontrol($son_url)) {
			return $son_url;
		}
		else {
			return false;
		}
	}
	else {
		return $son_url;
	}
}

function icerik_image_url($icerik_id, $tip)
{
	$url = bosluk_donustur(ekstra_bilgi_getir('gorsel', $icerik_id, $tip));
	return $url;
}

function icerik_thumbnail_url($boyut, $yazi_id, $tip)
{
	if (!$tip) {
		$tip = bilgi_getir('icerik', 'tip', $yazi_id);
	}

	$image_url = bosluk_donustur(ekstra_bilgi_getir('gorsel', $yazi_id, $tip));

	if ($image_url != '') {
		if ($boyut == 'full') {
			return $image_url;
		}
		else {
			$thumb_url = bosluk_donustur(thumbnail_url($boyut, $image_url));
			return $thumb_url;
		}
	}
}

function bosluk_donustur($string)
{
	return $donustur = str_replace(' ', '%20', (string)$string);
}

function uzanti_ne($file)
{
	if ($file) {
		$ext = pathinfo($file);
		return isset($ext['extension']) ? $ext['extension'] : '';
	}
	return '';
}

function form_elemani($type, $name, $text, $value = NULL, $options = NULL, $disabled = NULL, $required = NULL, $aciklama = NULL)
{
	if ($aciklama) {
		$aciklama_yaz = '<p class=\'help-block\'>' . $aciklama . '</p>';
	}
	else {
		$aciklama_yaz = NULL;
	}

	if ($disabled == 'disabled') {
		$disabled_yaz = 'disabled';
	}
	else {
		$disabled_yaz = '';
	}
	if (($required == 'required') || ($required == 1)) {
		$required = 'required';
		$required_yildiz = '<span style=\'color:#e74c3c;margin-left:3px;\'>*</span>';
	}
	else {
		$required = NULL;
		$required_yildiz = NULL;
	}
	if (($type == 'text') || ($type == 'email') || ($type == 'password')) {
		echo "\n\t" . '<div id=\'form_' . $name . '\' class=\'form-group\'>' . "\n\t\t" . '<label for=\'' . $name . '\'>' . $text . ' ' . $required_yildiz . '</label>' . "\n\t\t" . $aciklama_yaz . "\n\t\t" . '<input type=\'' . $type . '\' class=\'form-control\' id=\'' . $name . '\' name=\'' . $name . '\' placeholder=\'' . $text . '\' value=\'' . $value . '\' ' . $disabled_yaz . ' ' . $required . '>' . "\n\t" . '</div>' . "\n";
	}
	else if ($type == 'hidden') {
		echo '<input type=\'' . $type . '\' id=\'' . $name . '\' name=\'' . $name . '\' placeholder=\'' . $text . '\' value=\'' . $value . '\'>';
	}
	else if ($type == 'date') {
		echo "\n" . '<div id=\'form_' . $name . '\' class=\'form-group\'>' . "\n\t" . '<label for=\'' . $name . '\'>' . $text . ' ' . $required_yildiz . '</label>' . "\n\t" . $aciklama_yaz . "\t\n\t" . '<div class=\'input-group date\' id=\'datepicker_' . $name . '\'>' . "\n\t\t" . '<div class=\'input-group-addon\'>' . "\n\t\t\t" . '<span class=\'glyphicon glyphicon-th\'></span>' . "\n\t\t" . '</div>' . "\n\t\t" . '<input type=\'text\' class=\'form-control\' id=\'' . $name . '\' name=\'' . $name . '\' value=\'' . $value . '\'>' . "\n\t" . '</div>' . "\t\n" . '</div>' . "\n" . '<script type=\'text/javascript\'>' . "\n" . '$(\'#datepicker_' . $name . '\').datepicker({' . "\n" . '    todayBtn: \'linked\'' . "\n" . '});' . "\n" . '</script>' . "\n";
	}
	else if ($type == 'date_time') {
		echo "\n" . '<div id=\'form_' . $name . '\' class=\'form-group\'>' . "\n\t" . '<label for=\'' . $name . '\'>' . $text . ' ' . $required_yildiz . '</label>' . "\n\t" . $aciklama_yaz . "\t\n\t" . '<div class=\'input-group date\' id=\'datetimepicker_' . $name . '\' data-provide=\'datetimepicker\'>' . "\n\t\t" . '<div class=\'input-group-addon\'>' . "\n\t\t\t" . '<span class=\'glyphicon glyphicon-th\'></span>' . "\n\t\t" . '</div>' . "\n\t\t" . '<input type=\'text\' class=\'form-control\' id=\'' . $name . '\' name=\'' . $name . '\' value=\'' . $value . '\'>' . "\n\t" . '</div>' . "\t\n" . '</div>' . "\n" . '<script type=\'text/javascript\'>' . "\n\t" . '$(function () {' . "\n\t\t" . '$(\'#datetimepicker_' . $name . '\').datetimepicker({' . "\n\t\t\t" . 'format: \'yyyy-mm-dd hh:ii\',' . "\n\t\t\t" . 'todayBtn: true,' . "\n\t\t" . '});' . "\n\t" . '});' . "\n" . '</script>' . "\n";
	}
	else if ($type == 'textarea') {
		echo "\n\t" . '<div id=\'form_' . $name . '\' class=\'form-group\'>' . "\n\t\t" . '<label for=\'' . $name . '\'>' . $text . ' ' . $required_yildiz . '</label>' . "\n\t\t" . $aciklama_yaz . "\n\t\t" . '<textarea class=\'form-control\' id=\'' . $name . '\' name=\'' . $name . '\' placeholder=\'' . $text . '\' ' . $disabled . '>' . $value . '</textarea>' . "\n\t" . '</div>' . "\n";
	}
	else if ($type == 'select') {
		$option = explode('|', $options);
		$count = count($option);

		for ($x = 0; $x < $count; $x++) {
			if ($option[$x] == $value) {
				$selected = 'selected=\'selected\'';
			}
			else {
				$selected = '';
			}

			$option_yaz .= '<option value=\'' . $option[$x] . '\'' . $selected . '>' . $option[$x] . '</option>';
		}

		echo "\n" . '<div id=\'form_' . $name . '\' class=\'form-group\'>' . "\n\t" . '<label for=\'' . $name . '\'>' . $text . '</label>' . "\n\t" . $aciklama_yaz . "\n\t" . '<select name=\'' . $name . '\' class=\'form-control\'>' . "\n\t" . $option_yaz . "\n\t" . '</select>' . "\n" . '</div>' . "\n";
	}
}

function colorpicker_olustur($name, $text, $value = NULL, $disabled = NULL, $required = NULL, $aciklama = NULL)
{
	if ($aciklama) {
		$aciklama_yaz = '<p class=\'help-block\'>' . $aciklama . '</p>';
	}
	else {
		$aciklama_yaz = NULL;
	}
	if (($required == 'required') || ($required == 1)) {
		$required = 'required';
		$required_yildiz = '<span style=\'color:#e74c3c;margin-left:3px;\'>*</span>';
	}
	else {
		$required = '';
		$required_yildiz = '';
	}

	echo "\n\t" . '<div id=\'form_' . $name . '\' class=\'form-group\'>' . "\n\t\t" . '<label for=\'' . $name . '\'>' . $text . ' ' . $required_yildiz . '</label>' . "\n\t\t" . $aciklama_yaz . "\n\t" . '<div id=\'colorpicker_' . $name . '\' class=\'input-group colorpicker-component\'>' . "\n\t\t" . '<span class=\'input-group-addon\'><i></i></span>' . "\n\t\t" . '<input type=\'text\' id=\'' . $name . '\' name=\'' . $name . '\' value=\'' . $value . '\' class=\'form-control\' ' . $disabled . ' ' . $required . ' />' . "\n\t" . '</div>' . "\t\n\t" . '</div>' . "\n\t" . '<script>$(\'#colorpicker_' . $name . '\').colorpicker({});</script>' . "\n";
}

function upload_olustur($name, $text, $value = NULL, $aciklama = NULL)
{
	if ($aciklama) {
		$aciklama_yaz = '<p class=\'help-block\'>' . $aciklama . '</p>';
	}
	else {
		$aciklama_yaz = NULL;
	}

	if ($value != '') {
		$uyari = '<div id=\'img-uyari-' . $name . '\' class=\'img-uyari\' style=\'color:red;font-weight:500;\'></div>';
	}
	else {
		$uyari = NULL;
	}

	$uzanti = uzanti_ne($value);

	if ($uzanti != '') {
		if (($uzanti == 'png') || ($uzanti == 'jpg') || ($uzanti == 'gif')) {
			$mevcut_upload = '<a href=\'' . $value . '\' target=\'_blank\'><img src=\'' . $value . '\' alt=\'\' class=\'img-responsive panel-gorsel\' id=\'img-url-' . $name . '\' style=\'max-width:100%;height:auto;margin-bottom:10px;\' /></a>';
	$mevcut_upload_indir = '<div class=\'checkbox\'><label><input type=\'checkbox\' name=\'upload_kaldir[]\' value=\'' . $name . '\'>'. _LANG('f_resmi_kaldir') . '</label></div>';

		}
		else {
			$mevcut_upload = '<input type=\'text\' value=\'' . $value . '\' class=\'form-control\' style=\'margin-bottom:10px; \' readonly/>';
			$mevcut_upload_indir = "\n\t\t" . '<div style=\'float:left;margin-right:20px;\'>' . "\n\t\t\t" . '<a href=\'' . $value . '\' class=\'btn btn-info\'>İndir</a>' . "\n\t\t" . '</div>' . "\n\t\t" . '<div style=\'float:left;\'>' . "\n\t\t\t" . '<div class=\'checkbox\' style=\'margin-top:5px !important;\'><label><input type=\'checkbox\' name=\'upload_kaldir[]\' value=\'' . $name . '\'>Yüklemeyi kaldır</label></div>' . "\n\t\t" . '</div>' . "\n\t\t";
		}
	}
	else {
		$mevcut_upload = NULL;
		$mevcut_upload_indir = NULL;
	}

	echo "\n\t" . '<div id=\'form_' . $name . '\' class=\'form-group\'>' . "\n\t\t" . '<label for=\'imgInp-' . $name . '\'>' . $text . '</label>' . "\n\t\t" . $aciklama_yaz . "\n\t\t" . '<div class=\'row\'>' . "\n\t\t\t" . '<div class=\'col-md-30 col-sm-30 col-xs-60\'>' . "\n\t\t\t\t" . $mevcut_upload . "\n\t\t\t" . '</div>' . "\n\t\t\t" . '<div class=\'col-md-30 col-sm-30 col-xs-60\'>' . "\n\t\t\t\t" . $mevcut_upload_indir . "\n\t\t\t" . '</div>' . "\n\t\t" . '</div>' . "\n" . '        <div class=\'input-group\'>' . "\n" . '            <span class=\'input-group-btn\'>' . "\n" . '                <span class=\'btn btn-default btn-file btn-file\'>' . "\n" . '                    Seçiniz <input type=\'file\' id=\'imgInp-' . $name . '\' name=\'' . $name . '\'>' . "\n" . '                </span>' . "\n" . '            </span>' . "\n" . '            <input type=\'text\' class=\'form-control\' readonly>' . "\n" . '        </div>' . "\n\t\t" . $uyari . "\n\t" . '</div>' . "\n" . '<script>' . "\n" . '$(document).ready( function() {' . "\n" . '    ' . "\t\t\t" . 'function readURL(input) {' . "\n\t\t" . '    if (input.files && input.files[0]) {' . "\n\t\t" . '        var reader = new FileReader();' . "\n\t\t" . '        reader.onload = function (e) {' . "\n\t\t" . '            $(\'#img-url-' . $name . '\').attr(\'src\', e.target.result);' . "\n\t\t" . '            $(\'#img-uyari-' . $name . '\').html(\'<p style=\\\'margin-top:10px;\\\'>Güncelle butonuna basmayı unutmayınız.</p>\');' . "\n\t\t" . '        }' . "\n\t\t" . '        reader.readAsDataURL(input.files[0]);' . "\n\t\t" . '    }' . "\n\t\t" . '}' . "\n\t\t" . '$(\'#imgInp-' . $name . '\').change(function(){' . "\n\t\t" . '    readURL(this);' . "\n\t\t" . '}); ' . "\t\n\t" . '});' . "\n" . '</script>' . "\n";
}

function textarea_olustur($class, $name, $text, $value = NULL, $rows = 3, $required = NULL, $aciklama = NULL)
{
	if ($aciklama) {
		$aciklama_yaz = '<p class=\'help-block\'>' . $aciklama . '</p>';
	}
	else {
		$aciklama_yaz = NULL;
	}
	if (($required == 'required') || ($required == 1)) {
		$required = 'required';
		$required_yildiz = '<span style=\'color:#e74c3c;margin-left:3px;\'>*</span>';
	}
	else {
		$required = '';
		$required_yildiz = '';
	}

	echo "\n" . '<div id=\'form_' . $name . '\' class=\'form-group\'>' . "\n\t" . '<div class=\'form-group\'>' . "\n\t\t" . '<label for=\'' . $name . '\'>' . $text . ' ' . $required_yildiz . '</label>' . "\n\t\t" . $aciklama_yaz . "\n\t\t" . '<textarea class=\'form-control ' . $class . '\' id=\'' . $name . '\' name=\'' . $name . '\' placeholder=\'' . $text . '\' rows=\'' . $rows . '\' ' . $required . '>' . $value . '</textarea>' . "\n\t" . '</div>' . "\n" . '</div>' . "\n";
}

function checkbox_olustur($name, $text, $value = NULL, $aciklama = NULL)
{
	if ($aciklama) {
		$aciklama_yaz = '<p class=\'help-block\'>' . $aciklama . '</p>';
	}
	else {
		$aciklama_yaz = NULL;
	}

	if ($value == 1) {
		$checked = 'checked=\'checked\'';
	}
	else {
		$checked = '';
	}

	echo "\n" . '<div id=\'form_' . $name . '\'>' . "\n\t" . '<div class=\'checkbox\' style=\'margin:5px 0 !important;\'>' . "\n\t\t" . $aciklama_yaz . "\n\t\t" . '<label>' . "\n\t\t" . '  <input type=\'checkbox\' name=\'' . $name . '\' value=\'1\' ' . $checked . '> ' . $text . "\n\t\t" . '</label>' . "\n\t" . '</div>' . "\n" . '</div>' . "\n";
}

function baslik_olustur($text, $size = 15, $weight = 600, $aciklama = NULL)
{
	$panel_renk = ayar_getir('panel_renk');

	if ($aciklama) {
		$aciklama_yaz = '<p>' . $aciklama . '</p>';
	}
	else {
		$aciklama_yaz = NULL;
	}

	echo "\n" . '<div class=\'clearfix\'></div>' . "\n" . '<h5 style=\'font-size:' . $size . ('px;font-weight:' . $weight . ';color:#444;padding-bottom:6px;border-bottom:1px dotted #444;margin:10px 0;float:left;\'>' . $text . '</h5>' . "\n" . '<div class=\'clearfix\'></div>' . "\n" . $aciklama_yaz . "\n");
}

function select_olustur($name, $text, $options = NULL, $value = NULL, $aciklama = NULL, $required = NULL)
{
	if ($aciklama) {
		$aciklama_yaz = '<p class=\'help-block\'>' . $aciklama . '</p>';
	}
	else {
		$aciklama_yaz = NULL;
	}
	if (($required == 'required') || ($required == 1)) {
		$required = 'required';
		$required_yildiz = '<span style=\'color:#e74c3c;margin-left:3px;\'>*</span>';
	}
	else {
		$required = '';
		$required_yildiz = '';
	}

	$option_img_sayisi = 0;

	foreach ($options as $yaz => $x) {
		@$option_value = $x['deger'];
		@$option_baslik = $x['baslik'];
		@$option_img = $x['resim'];

		if ($option_value == $value) {
			$selected = 'selected=\'selected\'';
		}
		else {
			$selected = '';
		}

		if ($option_img) {
			$data_img_yaz = 'data-img-src=\'' . $option_img . '\' data-img-label=\'' . $option_baslik . '\'';
		}
		else {
			$data_img_yaz = NULL;
		}

		@$option_yaz .= '<option ' . $data_img_yaz . ' value=\'' . $option_value . '\'' . $selected . '>' . $option_baslik . '</option>';

		if ($option_img) {
			$option_img_sayisi++;
		}
	}

	echo "\n\t" . '<div id=\'form_' . $name . '\' class=\'form-group\'>' . "\n\t\t" . '<label for=\'' . $name . '\'>' . $text . ' ' . $required_yildiz . '</label>' . "\n\t\t" . $aciklama_yaz . "\n\t\t" . '<select id=\'' . $name . '\' name=\'' . $name . '\' class=\'form-control\' ' . $required . '>' . "\n\t\t" . $option_yaz . "\n\t\t" . '</select>' . "\n\t" . '</div>';

	if (0 < $option_img_sayisi) {
		echo "\n\t" . '<script type=\'text/javascript\'>' . "\n\t\t" . '$(\'#' . $name . '\').imagepicker({' . "\n\t\t\t" . 'show_label:true' . "\n\t\t" . '});' . "\n\t" . '</script>' . "\n\t";
	}
}

function multiple_checkbox_olustur($name, $text, $options = NULL, $value = NULL, $aciklama = NULL)
{
	$aciklama_yaz = NULL;

	if ($aciklama) {
		$aciklama_yaz = '<p class=\'help-block\'>' . $aciklama . '</p>';
	}

	echo '<div id=\'form_' . $name . '\'>' . "\n\t" . '<label for=\'' . $name . '\'>' . $text . '</label>' . "\n\t" . $aciklama_yaz . "\n\t";

	foreach ($options as $yaz => $x) {
		$option_value = $x['deger'];
		$option_baslik = $x['baslik'];

		if (strstr($value, '(' . $option_value . ')')) {
			$checked = 'checked=\'checked\'';
		}
		else {
			$checked = '';
		}

		echo "\n\t" . '<div class=\'checkbox\'>' . "\n\t" . '  <label><input type=\'checkbox\' name=' . $name . ('[] value=\'' . $option_value . '\' ' . $checked . '>' . $option_baslik . '</label>' . "\n\t" . '</div>' . "\n");
	}

	echo '</div>';
}

function aciklama_olustur($aciklama = NULL)
{
	echo "\n" . '<p class=\'help-block\'>' . $aciklama . '</p>' . "\n";
}

function form_buton($name, $text, $class = 'default')
{
	echo "\n\t" . '<button type=\'submit\' class=\'btn btn-' . $class . '\' name=\'' . $name . '\' value=\'' . $text . '\'>' . $text . '</button>' . "\n";
}

function tab_ac($name, $text = NULL, $active = NULL)
{
	echo '<div role=\'tabpanel\' class=\'tab-pane ' . $active . '\' id=\'' . $name . '\'>';
}

function tab_kapat()
{
	echo '</div>';
}

function yonetici_degilse_yonlendir($uye_id, $url)
  {
  	$yetki = $uye_id ? bilgi_getir('uyeler', 'yetki', $uye_id) : null;
	if (($yetki != 'admin') && ($yetki != 'editor') && ($yetki != 'yazar')) {
		if ($url) {
			header('location:' . $url);
		}
  		exit();
	}
}

function yonetici_mi($uye_id)
{
	$yetki = bilgi_getir('uyeler', 'yetki', $uye_id);
	if (($yetki == 'admin') || ($yetki == 'editor') || ($yetki == 'yazar')) {
		return true;
	}
	else {
		return false;
	}
}

function uye_mevcut_mu($login)
{
	global $baglan;
	if ($login) {
		$login = mysqli_real_escape_string($baglan, $login);
		if (strpos($login, '@') !== false) {
			$say = mysqli_num_rows(mysqli_query($baglan, "select * from uyeler where email_adresi='$login'"));
		} else {
			$say = mysqli_num_rows(mysqli_query($baglan, "select u.* from uyeler u inner join ekstra_bilgi eb on eb.id=u.id where eb.anahtar='cep_tel' and eb.tip='uye' and eb.deger='$login'"));
		}
		return ($say == 1);
	}
	return false;
}
function cep_tel_mevcut_mu($cep_tel)
{
	global $baglan;
	if ($cep_tel) {
		$cep_tel = mysqli_real_escape_string($baglan, $cep_tel);
		$say = mysqli_num_rows(mysqli_query($baglan, "select * from ekstra_bilgi where anahtar='cep_tel' and tip='uye' and deger='$cep_tel'"));
		return ($say == 1);
	}
	return false;
}

function telefon_format($tel)
{
	// parantez, bosluk ve tire atilarak sade rakam serisi korunur
	return str_replace(['(', ')', ' ', '-'], NULL, $tel);
}

function uye_sorgula($login, $parola)
{
	global $baglan;
	// girdi dogrudan sorguya yapistirilmaz; prepared statement kullanilir
	if (strpos($login, '@') !== false) {
		$sorgu = mysqli_prepare($baglan, "select id, parola from uyeler where email_adresi=?");
	} else {
		$sorgu = mysqli_prepare($baglan, "select u.id, u.parola from uyeler u inner join ekstra_bilgi eb on eb.id=u.id where eb.anahtar='cep_tel' and eb.tip='uye' and eb.deger=?");
	}
	mysqli_stmt_bind_param($sorgu, 's', $login);
	mysqli_stmt_execute($sorgu);
	mysqli_stmt_bind_result($sorgu, $kayit_id, $kayit_hash);
	$var = mysqli_stmt_fetch($sorgu);
	mysqli_stmt_close($sorgu);
	if (!$var) {
		return 0;
	}
	return parola_dogrula($kayit_id, $parola) ? 1 : 0;
}

function parola_sakla($parola)
{
	// yeni parolalar password_hash ile saklanir (eski md5 yerine)
	return password_hash($parola, PASSWORD_DEFAULT);
}

function parola_guncelle_hash($uye_id, $parola)
{
	global $baglan;
	$yeni_hash = parola_sakla($parola);
	$sorgu = mysqli_prepare($baglan, "update uyeler set parola=? where id=?");
	mysqli_stmt_bind_param($sorgu, 'si', $yeni_hash, $uye_id);
	mysqli_stmt_execute($sorgu);
	mysqli_stmt_close($sorgu);
}

function parola_dogrula($uye_id, $parola)
{
	global $baglan;
	if (!$uye_id) {
		return false;
	}
	$sorgu = mysqli_prepare($baglan, "select parola from uyeler where id=?");
	mysqli_stmt_bind_param($sorgu, 'i', $uye_id);
	mysqli_stmt_execute($sorgu);
	mysqli_stmt_bind_result($sorgu, $kayit_hash);
	$var = mysqli_stmt_fetch($sorgu);
	mysqli_stmt_close($sorgu);
	if (!$var or !$kayit_hash) {
		return false;
	}
	// once modern ozet; kademeli gecis icin rehash yapilir
	if (password_verify($parola, $kayit_hash)) {
		if (password_needs_rehash($kayit_hash, PASSWORD_DEFAULT)) {
			parola_guncelle_hash($uye_id, $parola);
		}
		return true;
	}
	// eski md5 ozetlerine geriye donuk uyum; dogru girilirse
	// sizintiye direncli ozege gecirilir, md5 kayitlari zamanla azalir
	if (hash_equals((string) $kayit_hash, md5($parola))) {
		parola_guncelle_hash($uye_id, $parola);
		return true;
	}
	return false;
}
function uye_id_getir($login)
{
	global $baglan;
	$login = mysqli_real_escape_string($baglan, $login);
	if (strpos($login, '@') !== false) {
		$dizi = (mysqli_query($baglan, "select * from uyeler where email_adresi='$login'") ? (mysqli_query($baglan, "select * from uyeler where email_adresi='$login'") ? mysqli_fetch_array(mysqli_query($baglan, "select * from uyeler where email_adresi='$login'")) : null) : null);
	} else {
		$dizi = (mysqli_query($baglan, "select u.* from uyeler u inner join ekstra_bilgi eb on eb.id=u.id where eb.anahtar='cep_tel' and eb.tip='uye' and eb.deger='$login'") ? (mysqli_query($baglan, "select u.* from uyeler u inner join ekstra_bilgi eb on eb.id=u.id where eb.anahtar='cep_tel' and eb.tip='uye' and eb.deger='$login'") ? mysqli_fetch_array(mysqli_query($baglan, "select u.* from uyeler u inner join ekstra_bilgi eb on eb.id=u.id where eb.anahtar='cep_tel' and eb.tip='uye' and eb.deger='$login'")) : null) : null);
	}
	return isset($dizi['id']) ? $dizi['id'] : null;
}
function uye_ad_soyad_getir($uye_id = NULL)
{
	if (!$uye_id) {
		$uye_id = $_GET['id'];
	}

	global $baglan;
	$dizi = (mysqli_query($baglan, 'select * from uyeler where id=\'' . $uye_id . '\'') ? (mysqli_query($baglan, 'select * from uyeler where id=\'' . $uye_id . '\'') ? mysqli_fetch_array(mysqli_query($baglan, 'select * from uyeler where id=\'' . $uye_id . '\'')) : null) : null);
	return $uye_ad_soyad = $dizi['ad_soyad'];
}

function yonlendir($url)
{
	header('location:' . $url);
	exit();
}

function yenile()
{
	yonlendir(browser_url());
	exit();
}

function mesaj_goster($mesaj, $renk = 'success')
{
	if (($renk != '') && ($mesaj != '')) {
		$_SESSION['mesaj'] = [$mesaj, $renk];
	}
}

function tarih_saat()
{
	return $cikti = date('Y-m-d H:i:s');
}

function sksort(&$array, $subkey = 'id', $sort_ascending = false)
{
	if (count($array)) {
		$temp_array[key($array)] = array_shift($array);
	}

	foreach ($array as $key => $val) {
		$offset = 0;
		$found = false;

		foreach ($temp_array as $tmp_key => $tmp_val) {
			if (!$found && (strtolower($tmp_val[$subkey]) < strtolower($val[$subkey]))) {
				$temp_array = array_merge((array) array_slice($temp_array, 0, $offset), [$key => $val], array_slice($temp_array, $offset));
				$found = true;
			}

			$offset++;
		}

		if (!$found) {
			$temp_array = array_merge($temp_array, [$key => $val]);
		}
	}

	if ($sort_ascending) {
		$array = array_reverse($temp_array);
	}
	else {
		$array = $temp_array;
	}
}

function yorum_ekle($ad_soyad, $email_adresi, $yorum, $icerik_id, $cevap_id = 0, $uye_id = null, $durum = null)
{
	global $baglan;
	$tarih_saat = tarih_saat();
	$yorum = yasakli_kelime_kontrol($yorum);
	$sql = mysqli_query($baglan, 'insert into yorumlar set ad_soyad=\'' . $ad_soyad . '\', email_adresi=\'' . $email_adresi . '\', yorum=\'' . $yorum . '\', icerik_id=\'' . $icerik_id . '\', cevap=\'' . $cevap_id . '\', uye_id=\'' . $uye_id . '\', durum=\'' . $durum . '\', tarih_saat=\'' . $tarih_saat . '\'');

	if ($sql) {
		return true;
	}
	else {
		return false;
	}
}

function jaayquick($cagir)
{
	if ($cagir) {
		foreach ($cagir as $yaz) {
			echo (string) $yaz;
		}
	}
}

function include_dizin()
{
	global $admin_dizini;

	if (strstr($_SERVER['REQUEST_URI'], '/' . $admin_dizini . '/')) {
		return $include_dizin = '..';
	}
	else {
		return $include_dizin = '';
	}
}

function tema_ne()
{
	return $tema = ayar_getir('tema');
}

function tema_header()
{
	global $temalar_dizini;
	global $header_kod;
	global $oturum_id;
	global $yonetici_id;
	$aktif_tema = tema_ne();
	jaayquick($header_kod);
	include './' . $temalar_dizini . '/' . $aktif_tema . '/header.php';
}

function tema_footer()
{
	global $footer_kod;
	global $temalar_dizini;
	global $oturum_id;
	global $yonetici_id;
	$aktif_tema = tema_ne();
	include_once './' . $temalar_dizini . '/' . $aktif_tema . '/footer.php';
	jaayquick($footer_kod);
}

function tema_head()
{
	global $head_kod;
	global $oturum_id;
	global $yonetici_id;
	$aktif_tema = tema_ne();
	jaayquick($head_kod);
}

function tema_foot()
{
	global $foot_kod;
	global $oturum_id;
	global $yonetici_id;
	$aktif_tema = tema_ne();
	jaayquick($foot_kod);
}

function tema_dizini()
{
	global $temalar_dizini;
	$tema = tema_ne();
	$anasayfa = site_url();
	return $dizin = $anasayfa . '/' . $temalar_dizini . '/' . $tema;
}

function ozet_olustur($metin, $kelime_sayisi = 20)
{
	if ($metin) {
		$bol = explode(' ', strip_tags($metin));
		if (is_array($bol) && ($kelime_sayisi < count($bol))) {
			$count = count($bol);
			$ozet = NULL;

			for ($x = 0; $x < $kelime_sayisi; $x++) {
				$ozet .= $bol[$x] . ' ';
			}

			return $ozet;
		}
		else {
			return $metin;
		}
	}
}

function menu_pozisyonu($menu_id)
{
	global $baglan;
	$menu_id_hazirlik = (mysqli_query($baglan, 'select * from ayarlar where deger=\'menu-' . $menu_id . '\'') ? (mysqli_query($baglan, 'select * from ayarlar where deger=\'menu-' . $menu_id . '\'') ? mysqli_fetch_array(mysqli_query($baglan, 'select * from ayarlar where deger=\'menu-' . $menu_id . '\'')) : null) : null);
	return $menu_id = ($menu_id_hazirlik and isset($menu_id_hazirlik['anahtar'])) ? $menu_id_hazirlik['anahtar'] : 0;
}

function link_sayisi($menu_id)
{
	global $baglan;
	$link_sayisi = mysqli_num_rows(mysqli_query($baglan, 'select * from linkler where menu_id=\'' . $menu_id . '\''));

	if ($link_sayisi == '') {
		return 0;
	}
	else {
		return $link_sayisi;
	}
}

function alt_link_sayisi($ust_link_id)
{
	global $baglan;
	$sql = mysqli_query($baglan, 'select * from linkler where ust_link=\'' . $ust_link_id . '\' order by sira asc');
	return mysqli_num_rows($sql);
}

function menu_getir($menu, $ul_ekle = 1, $id_mi = 0, $ul_class = NULL, $li_class = NULL)
{
	global $baglan;

	if ($ul_ekle == 0) {
		$alt_ul_class = 'dropdown-menu';
	}

	if ($id_mi == 1) {
		$menu_id = $menu;
	}
	else {
		$menu_id_hazirlik = ayar_getir((string) $menu);
		$menu_id_parcala = explode('-', $menu_id_hazirlik);
		@$menu_id = $menu_id_parcala[1];
	}

	$sql = mysqli_query($baglan, 'select * from linkler where menu_id=\'' . $menu_id . '\' and ust_link=0 order by sira asc');
	$browser_url = browser_url();

	if ($ul_ekle == 1) {
		echo '<ul class=\'' . $menu . ' ' . $ul_class . '\'>';
	}

	while ($dizi = mysqli_fetch_array($sql)) {
		$dizi_id = $dizi['id'];
		/* Menu basligi dile baglanir; kayit yoksa Turkce orijinal kalir. */
		$dizi_baslik = _DIL_BASLIK($dizi['baslik'], $dizi_id, 'linker');
		$dizi_url = $dizi['url'];
		$dizi_ikon = $dizi['ikon'];
		$dizi_class = $dizi['class'];
		$dizi_alt_link_sayisi = alt_link_sayisi($dizi_id);

		if (0 < $dizi_alt_link_sayisi) {
			$dizi_class_yaz = 'dropdown acilir';
		}
		else {
			$dizi_class_yaz = NULL;
		}

		if ($dizi_ikon) {
			$ikon_yaz = '<span class=\'' . $dizi_ikon . '\'></span>';
		}
		else {
			$ikon_yaz = NULL;
		}

		if ($dizi_url == $browser_url) {
			$dizi_class_yaz = 'active ' . $dizi_class_yaz;
		}

		if ($dizi_class) {
			$dizi_class_yaz = $dizi_class_yaz . ' ' . $dizi_class;
		}

		if ($li_class) {
			$dizi_class_yaz = $dizi_class_yaz . ' ' . $li_class;
		}

		if ($dizi_class_yaz) {
			$dizi_class_yaz = 'class=\'' . $dizi_class_yaz . '\'';
		}

		echo '<li ' . $dizi_class_yaz . '><a href=\'' . $dizi_url . '\' title=\'' . $dizi_baslik . '\'>' . $ikon_yaz . $dizi_baslik . '</a>';
		$dizi_class_yaz = NULL;
		alt_link_menu($dizi_id, @$alt_ul_class);
		echo '</li>';
	}

	if ($ul_ekle == 1) {
		echo '</ul>';
	}
}

function alt_link_menu($link_id, $alt_ul_class = NULL)
{
	global $baglan;
	$sql = mysqli_query($baglan, 'select * from linkler where ust_link=\'' . $link_id . '\' order by sira asc');
	$alt_link_sayi = mysqli_num_rows($sql);
	$browser_url = browser_url();

	if (0 < $alt_link_sayi) {
		if ($alt_ul_class) {
			echo '<ul class=\'' . $alt_ul_class . '\'>';
		}
		else {
			echo '<ul class=\'alt_menu\'>';
		}

		while ($dizi = mysqli_fetch_array($sql)) {
			$dizi_id = $dizi['id'];
			/* Menu basligi dile baglanir; kayit yoksa Turkce orijinal kalir. */
		$dizi_baslik = _DIL_BASLIK($dizi['baslik'], $dizi_id, 'linker');
			$dizi_url = $dizi['url'];
			$dizi_ikon = $dizi['ikon'];
			$dizi_class = $dizi['class'];
			$dizi_alt_link_sayisi = alt_link_sayisi($dizi_id);

			if (0 < $dizi_alt_link_sayisi) {
				$dizi_class_yaz = 'dropdown acilir';
			}
			else {
				$dizi_class_yaz = NULL;
			}

			if ($dizi_ikon) {
				$ikon_yaz = '<span class=\'' . $dizi_ikon . '\'></span>';
			}
			else {
				$ikon_yaz = NULL;
			}

			if ($dizi_url == $browser_url) {
				$dizi_class_yaz = 'active ' . $dizi_class;
			}

			if ($dizi_class) {
				$dizi_class_yaz = $dizi_class_yaz . ' ' . $dizi_class;
			}

			if ($dizi_class_yaz) {
				$dizi_class_yaz = 'class=\'' . $dizi_class_yaz . '\'';
			}

			echo '<li ' . $dizi_class_yaz . '><a href=\'' . $dizi_url . '\' title=\'' . $dizi_baslik . '\'>' . $ikon_yaz . $dizi_baslik . '</a>';
			$dizi_class_yaz = NULL;
			alt_link_menu($dizi_id);
			echo '</li>';
		}

		echo '</ul>';
	}
}

function tirnak_replace($text)
{
	$bul = ['"', '\''];
	$degistir = ['&quot;', '&#39;'];
	return $cikti = str_replace($bul, $degistir, $text);
}

function tirnak_ekle($text)
{
	$bul = ['&quot;', '&#39;'];
	$degistir = ['"', '\''];
	return $cikti = str_replace($bul, $degistir, $text);
}

function darken_color($rgb, $darker = 2)
{
	$hash = (strpos($rgb, '#') !== false ? '#' : '');
	$rgb = (strlen($rgb) == 7 ? str_replace('#', '', $rgb) : (strlen($rgb) == 6 ? $rgb : false));

	if (strlen($rgb) != 6) {
		return $hash . '000000';
	}

	$darker = (1 < $darker ? $darker : 1);
	list($R16, $G16, $B16) = str_split($rgb, 2);
	$R = sprintf('%02X', floor(hexdec($R16) / $darker));
	$G = sprintf('%02X', floor(hexdec($G16) / $darker));
	$B = sprintf('%02X', floor(hexdec($B16) / $darker));
	return $hash . $R . $G . $B;
}

function panel_sayfalama($kac_sayfa, $page, $bilgi = 1, $prefix = null)
{
	if (($kac_sayfa < $page) && ($page != 1)) {
		yonlendir('?p=' . $kac_sayfa);
	}

	if (1 < $kac_sayfa) {
		$site_url = site_url();

		if ($kac_sayfa) {
			$bosluk = ceil($kac_sayfa / 23) * 2.2;
		}

		if ($bilgi == 1) {
			echo '<p class=\'pagination-bilgi\'>' . _LANG('f_pagination_bilgi', array($page, $kac_sayfa)) . ' </p>';
		}

		if (1 < $page) {
			$onceki_sayfa = $page - 1;
		}

		echo '<ul class=\'pagination visible-sm visible-md visible-lg\'>';
		$pagination_mobil = '<div class=\'form-group visible-xs\'><select id=\'jumpmenu\' class=\'form-control\'>';

		if (1 < $page) {
			$onceki_sayfa = $page - 1;
			echo '<li>' . "\n" . '      <a href=\'' . $prefix . $onceki_sayfa . '\' aria-label=\'' . _LANG("f_onceki") . '\'' . '>' . "\n" . '        <span aria-hidden=\'true\'><i class=\'fa fa-angle-left mr0\'></i></span>' . "\n" . '      </a>' . "\n" . '    </li>' . "\n";
		}

		$nokta_basladi = 1;

		for ($x = 1; $x <= $kac_sayfa; $x++) {
			$link_yaz = 'href=\'' . $prefix . $x . '\'';

			if ($x == $page) {
				$active = 'class=\'active\'';
				$link_yaz = '';
				$selected_yaz = 'selected=\'selected\'';
			}
			else {
				$active = '';
				$selected_yaz = '';
			}

			if (23 < $kac_sayfa) {
				if (($x == 1) || ($x == $kac_sayfa) || (($x < ($page - $bosluk)) && (($x % $bosluk) == 0))) {
					echo '<li ' . $active . '><a ' . $link_yaz . '>' . $x . '</a></li>';
				}
				else if ((($page - 5) < $x) && ($x < ($page + 5))) {
					echo '<li ' . $active . '><a ' . $link_yaz . '>' . $x . '</a></li>';
				}
				else if ((($page + $bosluk) < $x) && (($x % $bosluk) == 0)) {
					echo '<li ' . $active . '><a ' . $link_yaz . '>' . $x . '</a></li>';
				}
				else {
				}

				$pagination_mobil .= '<option value=\'' . $prefix . $x . '\' ' . $selected_yaz . '>' . $x . '</option>';
				continue;
			}

			echo '<li ' . $active . '><a ' . $link_yaz . '>' . $x . '</a></li>';
			$pagination_mobil .= '<option value=\'' . $prefix . $x . '\' ' . $selected_yaz . '>' . $x . '</option>';
		}

		if ($page < $kac_sayfa) {
			$sonraki_sayfa = $page + 1;
			echo "\n" . '<li>' . "\n" . '      <a href=\'' . $prefix . $sonraki_sayfa . '\' aria-label=\'' . _LANG("f_sonraki") . '\'' . '>' . "\n" . '        <span aria-hidden=\'true\'><i class=\'fa fa-angle-right mr0\'></i></span>' . "\n" . '      </a>' . "\n" . '    </li>' . "\n";
		}

		echo '</ul>';
		$pagination_mobil .= '</select></div>';
		echo '<div class=\'mobil_sayfalama\'>' . $pagination_mobil . '</div>';
	}
}

function eklenti_sayfalama($kac_sayfa, $page, $bilgi = 1, $eklenti = null, $sayfa = null)
{
	if (1 < $kac_sayfa) {
		$site_url = site_url();
		$bosluk = ceil($kac_sayfa / 23) * 2.2;
		@$search = $_GET['s'];
		$prefix = 'eklentiler.php?eklenti=' . $eklenti . '&sayfa=' . $sayfa . '&p=';

		if ($bilgi == 1) {
			echo '<p class=\'pagination-bilgi\'>' . _LANG('f_pagination_bilgi', array($page, $kac_sayfa)) . ' </p>';
		}

		if (1 < $page) {
			$onceki_sayfa = $page - 1;
		}

		echo '<ul class=\'pagination visible-sm visible-md visible-lg\'>';
		$pagination_mobil = '<div class=\'form-group visible-xs\'><select id=\'jumpmenu\' class=\'form-control\'>';

		if (1 < $page) {
			$onceki_sayfa = $page - 1;
			echo '<li>' . "\n" . '      <a href=\'' . $prefix . $onceki_sayfa . '\' aria-label=\'' . _LANG("f_onceki") . '\'' . '>' . "\n" . '        <span aria-hidden=\'true\'><i class=\'fa fa-angle-left mr0\'></i></span>' . "\n" . '      </a>' . "\n" . '    </li>' . "\n";
		}

		$nokta_basladi = 1;

		for ($x = 1; $x <= $kac_sayfa; $x++) {
			$link_yaz = 'href=\'' . $prefix . $x . '\'';

			if ($x == $page) {
				$active = 'class=\'active\'';
				$link_yaz = '';
				$selected_yaz = 'selected=\'selected\'';
			}
			else {
				$active = '';
				$selected_yaz = '';
			}

			if (23 < $kac_sayfa) {
				if (($x == 1) || ($x == $kac_sayfa) || (($x < ($page - $bosluk)) && (($x % $bosluk) == 0))) {
					echo '<li ' . $active . '><a ' . $link_yaz . '>' . $x . '</a></li>';
				}
				else if ((($page - 5) < $x) && ($x < ($page + 5))) {
					echo '<li ' . $active . '><a ' . $link_yaz . '>' . $x . '</a></li>';
				}
				else if ((($page + $bosluk) < $x) && (($x % $bosluk) == 0)) {
					echo '<li ' . $active . '><a ' . $link_yaz . '>' . $x . '</a></li>';
				}
				else {
				}

				$pagination_mobil .= '<option value=\'' . $prefix . $x . '\' ' . $selected_yaz . '>' . $x . '</option>';
				continue;
			}

			echo '<li ' . $active . '><a ' . $link_yaz . '>' . $x . '</a></li>';
			$pagination_mobil .= '<option value=\'' . $prefix . $x . '\' ' . $selected_yaz . '>' . $x . '</option>';
		}

		if ($page < $kac_sayfa) {
			$sonraki_sayfa = $page + 1;
			echo "\n" . '<li>' . "\n" . '      <a href=\'' . $prefix . $sonraki_sayfa . '\' aria-label=\'' . _LANG("f_sonraki") . '\'' . '>' . "\n" . '        <span aria-hidden=\'true\'><i class=\'fa fa-angle-right mr0\'></i></span>' . "\n" . '      </a>' . "\n" . '    </li>' . "\n";
		}

		echo '</ul>';
		$pagination_mobil .= '</select></div>';
		echo '<div class=\'mobil_sayfalama\'>' . $pagination_mobil . '</div>';
	}
}

function browser_url()
{
	$site_url = site_url();
	$https_kontrol = strstr($site_url, 'https://');

	if ($https_kontrol) {
		$prefix = 'https';
	}
	else {
		$prefix = 'http';
	}

	return $prefix . '://' . $_SERVER['HTTP_HOST'] . urldecode($_SERVER['REQUEST_URI']);
}

function ssl_kontrol()
{
	$site_url = site_url();
	$https_kontrol = strstr($site_url, 'https://');

	if ($https_kontrol) {
		return true;
	}
	else {
		return false;
	}
}

function ref_url()
{
	return urldecode(@$_SERVER['HTTP_REFERER']);
}

function logout($yonlendir)
{
	if (!$yonlendir) {
		$yonlendir = site_url();
	}

	session_destroy();
	yonlendir($yonlendir);
}

function resim_ekle($resim_url, $icerik_id, $baslik, $icerik, $sira)
{
	global $baglan;
	$sql = mysqli_query($baglan, 'insert into galeri set resim=\'' . $resim_url . '\', icerik_id=\'' . $icerik_id . '\', baslik=\'' . $baslik . '\', url=\'galeri\', icerik=\'' . $icerik . '\', sira=\'' . $sira . '\'');
}

function resim_sayisi($icerik_id = NULL)
{
	if (!$icerik_id) {
		$icerik_id = @$_GET['id'];
	}

	if (@$icerik_id) {
		global $baglan;
		$sql = mysqli_query($baglan, 'select * from galeri where icerik_id=\'' . $icerik_id . '\'');
		return mysqli_num_rows($sql);
	}
}

function replaceSpace($string)
{
	$string = preg_replace('/\\s+/', ' ', $string);
	$string = trim($string);
	return $string;
}

function ip_adresi()
{
	global $_SERVER;

	if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
		$ip_adresi = $_SERVER['HTTP_CLIENT_IP'];
	}
	else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		$ip_adresi = $_SERVER['HTTP_X_FORWARDED_FOR'];
	}
	else {
		$ip_adresi = $_SERVER['REMOTE_ADDR'];
	}

	return $ip_adresi;
}

function kategori_getir($icerik_id, $sinif = 'kategori')
{
	$kategoriler = bilgi_getir('icerik', 'kategori', $icerik_id);
	$cikti = NULL;

	if ($kategoriler != '') {
		$bul = ['(', ')'];
		$degistir = [NULL, NULL];
		$cikti = explode(',', str_replace($bul, $degistir, $kategoriler));
	}

	if (is_array($cikti)) {
		$son = [];

		foreach ($cikti as $kontrol) {
			$sinif_ne = getir('kategoriler', 'sinif', $kontrol);

			if ($sinif == $sinif_ne) {
				$son[] = $kontrol;
			}
		}

		return $son;
	}
}

function kategori_dizisi($kategoriler)
{
	if ($kategoriler != '') {
		$bul = ['(', ')'];
		$degistir = [NULL, NULL];
		return $cikti = explode(',', str_replace($bul, $degistir, $kategoriler));
	}
}

function sql_kategori_icerikleri($kategoriler)
{
	if (is_array($kategoriler)) {
		$kategori_dizi = $kategoriler;
		$rakam = 1;
	}
	else {
		if (!strstr($kategoriler, '(')) {
			$rakam = 1;
		}

		if ($kategoriler != '') {
			$kategori_dizi = explode(',', $kategoriler);
		}
	}

	$sorgu = '(';

	foreach ($kategori_dizi as $ayir) {
		if ($rakam == 1) {
			$ayir = '(' . $ayir . ')';
		}

		$sorgu .= 'kategori like \'%' . $ayir . '%\' or ';
	}

	$sorgu = substr_replace($sorgu, NULL, -4) . ')';
	return $sorgu;
}

function benzersiz()
{
	return strtoupper(uniqid());
}

function su_kadar_once($tarih_saat, $ekle)
{
	$tarih_saat_parcala = explode(' ', $tarih_saat);
	$tarih = $tarih_saat_parcala[0];
	$saat = $tarih_saat_parcala[1];
	$tarih_parcala = explode('-', $tarih);
	$saat_parcala = explode(':', $saat);
	$gun = $tarih_parcala[2];
	$ay = $tarih_parcala[1];
	$yil = $tarih_parcala[0];
	$saat = $saat_parcala[0];
	$dakika = $saat_parcala[1];
	$saniye = $saat_parcala[2];
	$simdi = time();
	$eski = mktime($saat, $dakika, $saniye, $ay, $gun, $yil);
	$fark = $simdi - $eski;

	if ($fark < 3) {
		$yaz = _LANG('f_simdi');
	}
	else if ($fark < 60) {
		$yaz = _LANG('f_saniye_once', array($fark));
	}
	else if ($fark < 3600) {
		$fark = floor($fark / 60);
		$yaz = _LANG('f_dakika_once', array($fark));
	}
	else if ($fark < 86400) {
		$fark = floor($fark / 60 / 60);
		$yaz = _LANG('f_saat_once', array($fark));
	}
	else if ($fark < 604800) {
		$fark = floor($fark / 60 / 60 / 24);
		$yaz = _LANG('f_gun_once', array($fark));
	}
	else if ($fark < 2592000) {
		$fark = floor($fark / 60 / 60 / 24 / 7);
		$yaz = _LANG('f_hafta_once', array($fark));
	}
	else if ($fark < 31104000) {
		$fark = floor($fark / 60 / 60 / 24 / 30);
		$yaz = _LANG('f_ay_once', array($fark));
	}
	else {
		$fark = floor($fark / 60 / 60 / 24 / 30 / 12);
		$yaz = _LANG('f_yil_once', array($fark));
	}

	return rtrim($yaz);
}

function yasakli_kelime_kontrol($veri)
{
	$bul = tema_ayar_getir('argo_kelimeler');
	$degistir = tema_ayar_getir('argo_kelimeler_duzelt');
	$bul_array = explode(',', $bul);
	$degistir_array = explode(',', $degistir);
	return str_ireplace($bul_array, $degistir_array, $veri);
}

function yetki_kontrol()
{
	global $admin_dizini;
	global $oturum_yetki;
	global $_GET;
	global $icerik_tipleri;
	$php_self = $_SERVER['PHP_SELF'];

	if ($oturum_yetki == 'editor') {
		if (strstr($php_self, 'ayarlar.php') || strstr($php_self, 'temalar.php') || strstr($php_self, 'menuler.php') || strstr($php_self, 'bilesenler.php')) {
			$_SESSION['mesaj'] = [_LANG('f_yetki_yok'), 'danger'];
			yonlendir(site_url() . ('/' . $admin_dizini . '/'));
		}
		else if (strstr($php_self, 'uyeler.php') && (($_GET['islem'] == 'guncelle') || ($_GET['islem'] == 'sil'))) {
			$profil_yetki = bilgi_getir('uyeler', 'yetki', $_GET['id']);

			if ($profil_yetki == 'admin') {
				$_SESSION['mesaj'] = [_LANG('f_islem_yetki_yok'), 'danger'];
				yonlendir(site_url() . ('/' . $admin_dizini . '/uyeler.php'));
			}
		}
	}

	if ($oturum_yetki == 'yazar') {
		if (strstr($php_self, 'ayarlar.php') || strstr($php_self, 'temalar.php') || strstr($php_self, 'eklentiler.php') || strstr($php_self, 'menuler.php') || strstr($php_self, 'bilesenler.php') || strstr($php_self, 'uyeler.php') || strstr($php_self, 'kategoriler.php')) {
			$_SESSION['mesaj'] = [_LANG('f_yetki_yok'), 'danger'];
			yonlendir(site_url() . ('/' . $admin_dizini . '/'));
		}
	}
	if (strstr($php_self, 'icerik.php') || strstr($php_self, 'kategoriler.php')) {
		$tip = isset($_GET['tip']) ? $_GET['tip'] : '';
		$tip_ozellikleri = isset($icerik_tipleri[$tip]) ? $icerik_tipleri[$tip] : array();
		@$tip_yetki = isset($tip_ozellikleri['yetki']) ? $tip_ozellikleri['yetki'] : null;
		if (($oturum_yetki != 'admin') && $tip_yetki && !strstr($tip_yetki, $oturum_yetki)) {
			$_SESSION['mesaj'] = [_LANG('f_islem_yetki_yok'), 'danger'];
			yonlendir(site_url() . ('/' . $admin_dizini . '/'));
		}
	}
}

function yorum_sayisi($icerik_id)
{
	global $baglan;
	return mysqli_num_rows(mysqli_query($baglan, 'select id from yorumlar where durum=\'1\' and icerik_id=\'' . $icerik_id . '\''));
}

function icerik_sayisi($tip, $durum = NULL)
{
	global $baglan;

	if (!$durum) {
		$durum_yaz = '';
	}
	else {
		$durum_yaz = 'and durum=\'' . $durum . '\'';
	}

	return mysqli_num_rows(mysqli_query($baglan, 'select id from icerik where tip=\'' . $tip . '\' ' . $durum_yaz));
}

function bilesen_ayar_getir($anahtar, $id)
{
	return ekstra_bilgi_getir($anahtar, $id, 'bilesen');
}

function bilesen_ayar($anahtar, $id)
{
	return bilesen_ayar_getir($anahtar, $id);
}

function bilesen_ayar_guncelle($anahtar, $deger, $id)
{
	return ekstra_bilgi_guncelle($anahtar, $deger, $id, 'bilesen');
}

function bilesen_alani($alan)
{
	global $baglan;
	global $bilesen;
	
	// Debug: hata raporlamayı geçici olarak aç
	$error_reporting_was = error_reporting();
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	
	$sql = mysqli_query($baglan, 'select * from bilesenler where alan=\'' . $alan . '\' order by sira asc');
	
	if (!$sql) {
		echo '<div class="alert alert-danger">' . _LANG('s_bilesen_sorgu_hatasi') . '(' . $alan . '): ' . mysqli_error($baglan) . '</div>';
		error_reporting($error_reporting_was);
		return;
	}
	
	$say = mysqli_num_rows($sql);

	if (0 < $say) {
		while ($dizi = mysqli_fetch_array($sql)) {
			$id = $dizi['id'];
			$bilesen_value = $dizi['bilesen'];
			$baslik = $dizi['baslik'];
			
			if (!isset($bilesen[$bilesen_value])) {
				echo '<div class="alert alert-warning">' . _LANG('s_bilesen_tanimsiz') . ' ' . htmlspecialchars($bilesen_value) . ' (ID: ' . $id . ')</div>';
				continue;
			}
			
			if (!isset($bilesen[$bilesen_value]['fonksiyon'])) {
				echo '<div class="alert alert-warning">' . _LANG('s_bilesen_fonksiyon_yok') . ' ' . htmlspecialchars($bilesen_value) . ' (ID: ' . $id . ')</div>';
				continue;
			}
			
			$fonksiyon = $bilesen[$bilesen_value]['fonksiyon'];
			
			if (!function_exists($fonksiyon)) {
				echo '<div class="alert alert-danger">' . _LANG('s_bilesen_fonksiyon_bulunamadi') . ' ' . htmlspecialchars($fonksiyon) . ' (ID: ' . $id . ')</div>';
				continue;
			}
			
			echo '<div id=\'' . $bilesen_value . '-' . $id . '\'>';
			// Hata yakalama ile fonksiyon çağrısı
			try {
				echo $fonksiyon($id);
			} catch (Exception $e) {
				echo '<div class="alert alert-danger">' . _LANG('s_bilesen_hatasi') . '(' . htmlspecialchars($bilesen_value) . '): ' . $e->getMessage() . '</div>';
			} catch (Error $e) {
				echo '<div class="alert alert-danger">' . _LANG('s_bilesen_fatal_hatasi') . '(' . htmlspecialchars($bilesen_value) . '): ' . $e->getMessage() . '</div>';
			}
			echo '</div>';
		}
	} else {
		// Debug: bileşen yoksa bilgi ver
		// echo '<div class="alert alert-info">Bu alana (' . $alan . ') atanmış bileşen yok.</div>';
	}
	
	error_reporting($error_reporting_was);
}

function ozet($kelime, $id = NULL)
{
	if (!$id) {
		$id = $_GET['id'];
	}

	$icerik = getir('icerik', 'icerik', $id);
	return ozet_olustur($icerik, $kelime);
}

function thumbnail($boyut, $yazi_id = NULL)
{
	if (!$yazi_id) {
		$yazi_id = $_GET['id'];
	}

	$tip = getir('icerik', 'tip', $yazi_id);
	return icerik_thumbnail_url($boyut, $yazi_id, $tip);
}

function bilgi($anahtar, $yazi_id = NULL)
{
	if (!@$yazi_id) {
		$yazi_id = @$_GET['id'];
	}

	if (@$yazi_id) {
		$tip = getir('icerik', 'tip', $yazi_id);
		return ekstra_bilgi_getir($anahtar, $yazi_id, $tip);
	}
}

function bilgi_guncelle($anahtar, $yeni_deger, $yazi_id = NULL)
{
	if (!$yazi_id) {
		$yazi_id = $_GET['id'];
	}

	$tip = getir('icerik', 'tip', $yazi_id);
	return ekstra_bilgi_guncelle($anahtar, $yeni_deger, $yazi_id, $tip);
}

function sql_kategori($kategoriler)
{
	return sql_kategori_icerikleri($kategoriler);
}

function sql_kategori_not($kategoriler)
{
	if (is_array($kategoriler)) {
		$kategori_dizi = $kategoriler;
		$rakam = 1;
	}
	else {
		if (!strstr($kategoriler, '(')) {
			$rakam = 1;
		}

		if ($kategoriler != '') {
			$kategori_dizi = explode(',', $kategoriler);
		}
	}

	$sorgu = '(';

	foreach ($kategori_dizi as $ayir) {
		if ($rakam == 1) {
			$ayir = '(' . $ayir . ')';
		}

		$sorgu .= 'kategori not like \'%' . $ayir . '%\' and ';
	}

	$sorgu = substr_replace($sorgu, NULL, -4) . ')';
	return $sorgu;
}

function tarih_saat_bicimlendir($tarih_saat, $saniye = 0)
{
	$tarih_saat_parcala = explode(' ', $tarih_saat);
	$tarih = $tarih_saat_parcala[0];
	@$saat = $tarih_saat_parcala[1];

	if ($saniye == 0) {
		$saat = substr($saat, 0, -3);
	}

	return tarih_bicimlendir($tarih) . (', ' . $saat);
}

function uye_bilgi($anahtar, $uye_id = NULL)
{
	if (!$uye_id) {
		$uye_id = $_GET['id'];
	}

	return uye_bilgi_getir($anahtar, $uye_id);
}

function kategori_bilgi($anahtar, $kategori_id = NULL)
{
	if (!$kategori_id) {
		$kategori_id = $_GET['id'];
	}

	/*
	 * Kategori alan tanimlari (ozel alanlar, filtreler, detaylar,
	 * teknik ozellikler) icin dil kaydi varsa onu oku.
	 * Kayit yoksa Turkce (kaynak) icerik doner.
	 */
	$ceviriAnahtarlari = array('ozel_alanlar', 'filtreler', 'detaylar', 'teknik_ozellikler', 'guvenlik_ipuclari');
	if (in_array($anahtar, $ceviriAnahtarlari) and function_exists('aktif_dil')) {
		$dil = aktif_dil();
		if ($dil and $dil != 'tr') {
			$ceviri = ekstra_bilgi_getir($anahtar . '_' . $dil, $kategori_id, 'kategori');
			if ($ceviri) {
				return $ceviri;
			}
		}
	}

	return ekstra_bilgi_getir($anahtar, $kategori_id, 'kategori');
}

function kategori_icerik_sayisi($kategori_id, $durum = 1)
{
	global $baglan;
	return mysqli_num_rows(mysqli_query($baglan, 'select id from icerik where kategori like \'%(' . $kategori_id . ')%\' and durum=\'' . $durum . '\''));
}

function url_kontrol($url)
{
	$headers = @get_headers($url);
	if (!$headers or !isset($headers[0])) { return false; }
	return stripos($headers[0], '200 OK') ? true : false;
}

function profil_link_getir($id = NULL)
{
	if (!$id) {
		$id = $_GET['id'];
	}

	return uye_link_getir($id);
}

function resim_yukle($file, $klasor, $boyutlandir = NULL, $cikti = NULL, $orijinal_sil = 0)
{
	global $admin_dizini;
	$resim_dizisi = $file;
	$resim_adi_dizi = $resim_dizisi['name'];
	$resim_adi = str_replace([' ', ',', 'ş', 'ç', 'ğ', 'ö', 'ü', 'ı'], ['-', NULL, 's', 'c', 'g', 'o', 'u', 'i'], $resim_adi_dizi);
	$resim_uzanti_hazirlik = uzanti_ne($resim_adi);
	$resim_uzanti = strtolower($resim_uzanti_hazirlik);
	if (($resim_uzanti == 'png') || ($resim_uzanti == 'jpg') || ($resim_uzanti == 'gif') || ($resim_uzanti == 'jpeg')) {
		$kontrol = 1;
		// uzanti beyaz listesinde olsa da dosya icerigini dogrula: gecersiz
		// gorsel icerik yuklenmesin (uzanti kontrolu tek basina yetersiz)
		$icerik_dogrulama = getimagesize((string) ($resim_dizisi['tmp_name'] ?? ''));
		if ($icerik_dogrulama === false) {
			$kontrol = 0;
		}

		if (!$klasor) {
			$klasor = 'images';
		}
	}
	else if (!$klasor) {
		$klasor = 'files';
	}
	if ($resim_adi && ($kontrol == 1)) {
		if (!file_exists('uploads/' . $klasor)) {
			mkdir('uploads/' . $klasor);
		}

		$kaynak = $resim_dizisi['tmp_name'];
		$hedef = 'uploads/' . $klasor . '/' . $resim_adi;

		if (file_exists((string) $hedef)) {
			$parcala = explode('.' . $resim_uzanti_hazirlik, $resim_adi);
			$benzersiz = benzersiz();
			$resim_ilk_kisim = str_replace(['.'], [NULL], $parcala[0]);
			$resim_adi = $resim_ilk_kisim . ('-' . $benzersiz . '.' . $resim_uzanti);
		}
		else {
			$parcala = explode('.' . $resim_uzanti_hazirlik, $resim_adi);
			$resim_ilk_kisim = str_replace(['.'], [NULL], $parcala[0]);
			$resim_adi = $resim_ilk_kisim . ('.' . $resim_uzanti);
		}

		$hedef = 'uploads/' . $klasor . '/' . $resim_adi;
		$site_url = site_url();
		$resim_url = $site_url . ('/uploads/' . $klasor . '/') . $resim_adi;
		$yukle = move_uploaded_file($kaynak, $hedef);
		chmod($hedef, 438);

		if ($optimize) {
			include_once $admin_dizini . '/js/ImageResize.php';
			$image = new ImageResize($hedef);
			$hazirlik = explode('.', $resim_adi);
			$resim_adi_sabit = $hazirlik[0];
			$imagesize = getimagesize($hedef);
			$width = $imagesize[0];
			$height = $imagesize[1];
			$image->save($hedef, NULL, $resize_kalite);
		}

		if ($kontrol == 1) {
			if ($boyutlandir) {
				if (0 < count($boyutlandir)) {
					include_once $admin_dizini . '/js/ImageResize.php';
					$image = new ImageResize($hedef);
					$hazirlik = explode('.', $resim_adi);
					$resim_adi_sabit = $hazirlik[0];
					$imagesize = getimagesize($hedef);
					$width = $imagesize[0];
					$height = $imagesize[1];

					foreach ($boyutlandir as $yaz) {
						$parcala = explode('_', $yaz);
						$resim_width = $parcala[0];
						$resim_height = $parcala[1];
						$resim_son = $resim_width . '_' . $resim_height;
						if (($resim_width <= $width) && ($resim_height <= $height)) {
							$image->crop($resim_width, $resim_height)->save('uploads/' . $klasor . '/' . $resim_adi_sabit . '-' . $resim_son . '.' . $resim_uzanti, NULL, $resize_kalite);
						}
						else {
							$image->resize($resim_width, $resim_height, $allow_enlarge = true)->save('uploads/' . $klasor . '/' . $resim_adi_sabit . '-' . $resim_son . '.' . $resim_uzanti, NULL, $resize_kalite);
						}
					}
				}
			}
		}

		if (!$cikti) {
			return $resim_url;
		}
		else {
			$return_url = thumbnail_url($cikti, $resim_url);

			if ($orijinal_sil == 1) {
				unlink($hedef);
			}

			return $return_url;
		}
	}
	else {
		return false;
	}
}

function sil($url)
{
	if (empty($url)) { return false; }
	$ayir = explode('/uploads/', $url);
	if (count($ayir) < 2 || empty($ayir[1])) { return false; }
	$resim_adi = $ayir[1];
	$tam_yol = ROOT_DIR . ('/uploads/' . $resim_adi);
	if (is_file($tam_yol)) {
		$sil = unlink($tam_yol);
		if ($sil) {
			return true;
		}
	}
	return false;
}

function galeri_id($offset = NULL)
{
	global $baglan;

	if (!$offset) {
		$offset = 0;
	}

	$icerik_id = $_GET['id'];
	$galeri_sayfa = $_GET['g'];
	$sql_offset = ($galeri_sayfa - 1) + $offset;
	$resim_sayisi = resim_sayisi($icerik_id);
	$sor = mysqli_query($baglan, 'select * from galeri where icerik_id=\'' . $icerik_id . '\' order by sira asc limit ' . $sql_offset . ',1');
	$dizi = mysqli_fetch_array($sor);
	if ((($offset < 0) && (1 < ($galeri_sayfa - $offset))) || ((0 < $offset) && (($galeri_sayfa + $offset) <= $resim_sayisi)) || !$offset) {
		return $galeri_id = $dizi['id'];
	}
}

function galeri_url($galeri_id)
{
	global $baglan;

	if ($galeri_id) {
		$icerik_id = getir('galeri', 'icerik_id', $galeri_id);
		$sira = getir('galeri', 'sira', $galeri_id);
		$url = getir('galeri', 'url', $galeri_id);
		$onceki_galeri = mysqli_num_rows(mysqli_query($baglan, 'select * from galeri where icerik_id=\'' . $icerik_id . '\' and sira<' . $sira));
		$x = $onceki_galeri + 1;

		if ($url) {
			return link_getir($icerik_id) . '/' . $url . '-' . $x;
		}
	}
}

function galeri_ilk_resim($icerik_id = NULL)
{
	global $baglan;

	if (!$icerik_id) {
		$icerik_id = $_GET['id'];
	}

	$dizi = (mysqli_query($baglan, 'select * from galeri where icerik_id=\'' . $icerik_id . '\' order by sira asc limit 1') ? (mysqli_query($baglan, 'select * from galeri where icerik_id=\'' . $icerik_id . '\' order by sira asc limit 1') ? mysqli_fetch_array(mysqli_query($baglan, 'select * from galeri where icerik_id=\'' . $icerik_id . '\' order by sira asc limit 1')) : null) : null);
	return $dizi['id'];
}

function tema_ayar_guncelle($ayar, $yeni_deger, $dil = null)
{
	$tema = tema_ne();
	
	// If no language specified, use current active language
	if ($dil === null) {
		$dil = aktif_dil();
	}
	
	// If Turkish (source language), save to default key
	if ($dil === 'tr') {
		ayar_guncelle($tema . '-' . $ayar, $yeni_deger);
	} else {
		// Save to language-specific key
		ayar_guncelle($tema . '-' . $ayar . '_' . $dil, $yeni_deger);
	}
}

function eklenti_ayar_guncelle($ayar, $yeni_deger, $eklenti = NULL)
{
	if (!$eklenti) {
		$eklenti = $_GET['eklenti'];
	}

	ayar_guncelle($eklenti . '-' . $ayar, $yeni_deger);
}

function tema_ayar($ayar, $dil = null)
{
	return tema_ayar_getir($ayar, $dil);
}

function eklenti_ayar($ayar, $eklenti = NULL)
{
	if (!$eklenti) {
		$eklenti = $_GET['eklenti'];
	}

	return eklenti_ayar_getir($ayar, $eklenti);
}

function menu_link_sayisi($menu_name)
{
	$menu_deger = ayar_getir($menu_name);
	$parcala = explode('-', $menu_deger);
	$menu_id = $parcala[1];
	return link_sayisi($menu_id);
}

function eklenti_sayfa_url($sayfa, $eklenti = NULL)
{
	if (!$eklenti) {
		$eklenti = $_GET['eklenti'];
	}

	$site_url = site_url();
	return 'eklentiler.php?eklenti=' . $eklenti . '&sayfa=' . $sayfa;
}

function eklenti_sayfa($sayfa, $eklenti)
{
	return eklenti_sayfa_url($sayfa, $eklenti);
}

function options($veriler, $bol = NULL, $bol2 = NULL)
{
	if (!$bol) {
		$bol = ',';
	}

	$parcala = explode($bol, $veriler);

	foreach ($parcala as $x) {
		if ($bol2) {
			$parcala = explode($bol2, $x);
			$baslik = _DIL_ETIKET(strip_tags($parcala[0]));
			// satirda ayrac yoksa deger olarak baslik kullanilir; aksi halde
			// $parcala[1] tanimlanmamis kalir ve "Undefined array key 1" uyarisi cikar
			$value = strip_tags($parcala[1] ?? $parcala[0]);
		}
		else {
			$value = $x;
			$baslik = _DIL_ETIKET($x);
		}

		$yeni_dizi[] = ['deger' => $value, 'baslik' => $baslik];
	}

	return $yeni_dizi;
}

function ayar($ayar)
{
	return ayar_getir($ayar);
}

function bilgi_ekle($anahtar, $yeni_deger, $id = NULL)
{
	if (!$id) {
		$id = $_GET['id'];
	}

	$tip = getir('icerik', 'tip', $id);
	return ekstra_bilgi_ekle($anahtar, $yeni_deger, $id, $tip);
}

function liste_bilgi($anahtar, $liste_id)
{
	return ekstra_bilgi_getir($anahtar, $liste_id, 'liste');
}

function liste_icerik_sayisi($tip, $icerik_id = NULL)
{
	global $baglan;

	if (!$icerik_id) {
		$icerik_id = 0;
	}

	return mysqli_num_rows(mysqli_query($baglan, 'select id from listeler where tip=\'' . $tip . '\' and id=\'' . $icerik_id . '\''));
}

function liste_link($name, $text, $aciklama = NULL)
{
	$icerik_sayisi = liste_icerik_sayisi($name);
	$aciklama_yaz = NULL;

	if ($aciklama) {
		$aciklama_yaz = '<p>' . $aciklama . '</p>';
	}

	echo '<div id=\'form_' . $name . '\' class=\'form-group\'>' . "\n" . '<label for=\'' . $name . '\'>' . $text . '</label>' . "\n" . $aciklama_yaz . "\n" . '<div class=\'liste_link\'>' . "\n" . '<p><strong>' . $icerik_sayisi . ' liste öğesi:</strong> ' . $text . '</p>' . "\n" . '<a href=\'listeler.php?liste=' . $name . '\' class=\'btn btn-default\' target=\'_blank\'><i class=\'fa fa-external-link\'></i>Listeyi yönet</a>' . "\n" . '</div>' . "\n" . '</div>';
}

function grup_ac($id, $baslik = NULL, $aciklama = NULL)
{
	if ($baslik) {
		$baslik_yaz = '<label>' . $baslik . '</label>';
	}
	else {
		$baslik_yaz = NULL;
	}

	if ($aciklama) {
		$aciklama_yaz = '<p class=\'help-block\'>' . $aciklama . '</p>';
	}
	else {
		$aciklama_yaz = NULL;
	}

	echo "\n\t" . '<div class=\'formgrup\'>' . "\n\t" . $baslik_yaz . "\n\t" . $aciklama_yaz . "\n\t" . '<div class=\'row\'>';
}

function grup_kapat()
{
	echo "\n\t" . '<div class=\'clearfix\'></div>' . "\n\t" . '</div>' . "\n\t" . '</div>';
}

function location($id, $baslik, $deger = NULL, $aciklama = NULL)
{
	if ($deger) {
		$parcala = explode(',', $deger);
		$lat = $parcala[0];
		$lon = $parcala[1];
		$radius = $parcala[2];
		$zoom = $parcala[3];
	}
	else {
		$lat = '41.011219';
		$lon = '28.964845';
		$radius = '300';
		$zoom = '9';
	}

	echo "\n\t" . '<div id=\'form_' . $id . '\' class=\'form-group\'>' . "\n\t\t" . '<label for=\'' . $id . '\'>' . $baslik . '</label>' . "\n\t\t" . $aciklama_yaz . "\t\t\n\t\t" . '<div class=\'row\'>' . "\n\t\t\t" . '<div class=\'col-md-50\'>' . "\n\t\t\t\t" . '<div id=\'map_' . $id . '\' style=\'width: 100%; height: 290px;\'></div>' . "\n\t\t\t" . '</div>' . "\n\t\t\t" . '<div class=\'col-md-10\'>' . "\n\t\t\t\t" . '<div style=\'margin-bottom:5px;\'>Adres: <input type=\'text\' class=\'form-control\' id=\'map_' . $id . '-address\' readonly/></div>' . "\n\t\t\t\t" . '<div style=\'margin-bottom:5px;\'>Enlem: <input type=\'text\' class=\'form-control\' id=\'map_' . $id . '-lat\' name=\'map_' . $id . '-lat\'/></div>' . "\n\t\t\t\t" . '<div style=\'margin-bottom:5px;\'>Boylam: <input type=\'text\' class=\'form-control\' id=\'map_' . $id . '-lon\' name=\'map_' . $id . '-lon\'/></div>' . "\t\t\n\t\t\t\t" . '<div style=\'margin-bottom:5px;\'>Sınır: <input type=\'text\' class=\'form-control\' id=\'map_' . $id . '-radius\' name=\'map_' . $id . '-radius\'/></div>' . "\n\t\t\t\t" . '<div style=\'margin-bottom:5px;\'>Zoom: <input type=\'text\' class=\'form-control\' id=\'map_' . $id . '-zoom\' name=\'map_' . $id . '-zoom\' value=\'' . $zoom . '\'/></div>' . "\t\t\n\t\t\t" . '</div>' . "\n\t\t" . '</div>' . "\n\t" . '</div>' . "\n\t" . '<script>$(\'#map_' . $id . '\').locationpicker({' . "\n\t\t" . 'location: {latitude: ' . $lat . ', longitude: ' . $lon . '},' . "\n\t\t" . 'radius: ' . $radius . ',' . "\n\t\t" . 'zoom: ' . $zoom . ',' . "\n\t\t" . 'inputBinding: {' . "\n\t\t\t" . 'latitudeInput: $(\'#map_' . $id . '-lat\'),' . "\n\t\t\t" . 'longitudeInput: $(\'#map_' . $id . '-lon\'),' . "\n\t\t\t" . 'radiusInput: $(\'#map_' . $id . '-radius\'),' . "\n\t\t\t" . 'locationNameInput: $(\'#map_' . $id . '-address\')' . "\n\t\t" . '}' . "\n\t\t" . '});' . "\n\t" . '</script>' . "\n\t";
}

function kategori_icerik_sayisi_guncelle($kategori_id)
{
	$yeni_sayi = kategori_icerik_sayisi($kategori_id, 1);
	guncelle('kategoriler', 'icerik', $yeni_sayi, $kategori_id);
}

function menu_adi_getir($menu_id)
{
	$menu_id = explode('-', ayar($menu_id))[1];
	$baslik = getir('ayarlar', 'deger', $menu_id);
	return $baslik;
}

function menu_linkleri_getir($menu_id)
{
	global $baglan;
	$menu_id = explode('-', ayar($menu_id))[1];
	$sql = mysqli_query($baglan, 'select * from linkler where menu_id=\'' . $menu_id . '\' and ust_link=0 order by sira asc');

	while ($dizi = mysqli_fetch_array($sql)) {
		$dizi_id = $dizi['id'];
		/* Menu basligi dile baglanir; kayit yoksa Turkce orijinal kalir. */
		$dizi_baslik = _DIL_BASLIK($dizi['baslik'], $dizi_id, 'linker');
		$dizi_url = $dizi['url'];
		$dizi_ikon = $dizi['ikon'];
		$dizi_class = $dizi['class'];
		echo '<a href=\'' . $dizi_url . '\' title=\'' . $dizi_baslik . '\'>' . $ikon_yaz . $dizi_baslik . '</a>';
	}
}

function curl_get_contents($url)
{
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
	curl_setopt($ch, CURLOPT_TIMEOUT, 3);
	return $result = curl_exec($ch);
}

function klasor_olustur($dizin)
{
	$dizin = rtrim($dizin, '/');
	$bol = explode('/', $dizin);
	$x = 1;
	$kademe = NULL;

	foreach ($bol as $tek_klasor) {
		if (1 < $x) {
			$kademe .= '/';
		}

		$kademe .= (string) $tek_klasor;

		if (!file_exists(ROOT_DIR . ('/' . $kademe))) {
			mkdir(ROOT_DIR . ('/' . $kademe));
			chmod(ROOT_DIR . ('/' . $kademe), 493);
			touch(ROOT_DIR . ('/' . $kademe . '/index.html'));
		}

		$x++;
	}
}

function yukle($file, $secenekler = NULL)
{
	global $admin_dizini;
	global $baglan;
	global $resize_kalite;
	global $standart_resim_boyutlari;
	$boyutlar = [];
	@$dizin = $secenekler['dizin'];
	@$multiple = $secenekler['multiple'];
	@$boyutlar = $secenekler['boyutlar'];
	@$cikti = $secenekler['cikti'];
	@$orijinal_sil = $secenekler['orijinal_sil'];
	@$orijinal_optimize = $secenekler['orijinal_optimize'];
	@$tip = $secenekler['tip'];
	@$iliski_id = $secenekler['iliski_id'];
	@$iliski = $secenekler['iliski'];
	@$adlandir = $secenekler['adlandir'];
	@$adlandir_onek = $secenekler['adlandir_onek'];
	@$watermark = $secenekler['watermark'];
	@$kucultme_tercihi = $secenekler['kucultme_tercihi'];
	@$buyutme_tercihi = $secenekler['buyutme_tercihi'];
	@$upload_resize_kalite = $secenekler['resize_kalite'];
	@$webp = $secenekler['webp'];
	@$filtre = $secenekler['filtre'];

	if (!$kucultme_tercihi) {
		$kucultme_tercihi = tema_ayar('resize_kucult');
	}

	if (!$buyutme_tercihi) {
		$buyutme_tercihi = tema_ayar('resize_buyut');
	}

	if (!$kucultme_tercihi) {
		$kucultme_tercihi = 'aspectratio';
	}

	if (!$buyutme_tercihi) {
		$buyutme_tercihi = 'aspectratio';
	}

	if (!$upload_resize_kalite) {
		$upload_resize_kalite = $resize_kalite;
	}

	$yil = date('Y');
	$ay = date('m');
	$gun = date('d');

	if (!$tip) {
		$tip = 'upload';
	}

	if (!$iliski) {
		$iliski = NULL;
	}

	if ($iliski_id) {
		$sakla_iliski_id = $iliski_id;
	}
	if (is_array($standart_resim_boyutlari) && (0 < count($standart_resim_boyutlari))) {
		if (is_array($boyutlar)) {
			$boyutlar = array_merge($boyutlar, $standart_resim_boyutlari);
		}
		else {
			$boyutlar = $standart_resim_boyutlari;
		}
	}

	if (!is_array($boyutlar)) { $boyutlar = array(); }
$boyutlar = array_unique($boyutlar);

	if ($file) {
		if (is_array($file['name'])) {
			$yuklenenler = NULL;
			$x = 0;

			foreach ($file['name'] as $name) {
				$array['name'] = $file['name'][$x];
				$array['type'] = $file['type'][$x];
				$array['tmp_name'] = $file['tmp_name'][$x];
				$array['error'] = $file['error'][$x];
				$array['size'] = $file['size'][$x];
				$yuklenen = yukle($array, $secenekler);

				if ($yuklenen) {
					$yuklenenler[] = $yuklenen;
				}

				$x++;
			}

			return $yuklenenler;
		}
		else {
			$resim_uzanti = uzanti($file['name']);
			$resim_adi_bol = explode((string) $resim_uzanti, $file['name']);
			$resim_orijinal_ad = $resim_adi_bol[0];

			if (30 < strlen($resim_orijinal_ad)) {
				$resim_orijinal_ad = substr($resim_orijinal_ad, 0, 30);
			}

			@$resim_name = url_olustur($resim_orijinal_ad);

			if (!$adlandir) {
				$resim_name = $resim_name . '-' . benzersiz();
			}
			else if ($adlandir == 1) {
				$resim_name = strtoupper(uniqid());
			}
			else if ($adlandir == 2) {
				$resim_name = strtoupper(md5(uniqid(mt_rand(), true)));
			}

			if ($adlandir_onek) {
				$resim_name = $adlandir_onek . $resim_name;
			}

			$resim_name = url_olustur($resim_name);
			$resim_adi = $resim_name . '.' . $resim_uzanti;
			$kaynak = $file['tmp_name'];
			if (($resim_uzanti == 'png') || ($resim_uzanti == 'jpg') || ($resim_uzanti == 'gif') || ($resim_uzanti == 'jpeg') || ($resim_uzanti == 'webp')) {
				$tur = 'image';

				if (!$dizin) {
					$dizin = 'images';
				}
			}
			else {
				$tur = 'file';

				if (!$dizin) {
					$dizin = 'files';
				}
			}

			if ($resim_adi) {
				$tam_dizin = $dizin . '/' . $yil . '/' . $ay . '/' . $gun;
				klasor_olustur('uploads/' . $tam_dizin);
				$hedef = ROOT_DIR . ('/uploads/' . $tam_dizin . '/' . $resim_adi);
				$resim_url = site_url() . ('/uploads/' . $tam_dizin . '/') . $resim_adi;
				$yukle = move_uploaded_file($kaynak, $hedef);

				if ($yukle) {
					chmod($hedef, 438);
					include_once ROOT_DIR . ('/' . $admin_dizini . '/lib/SimpleImage.php');

					if ($tur == 'image') {
						$resim = new claviska\SimpleImage();
						$resim->fromFile($hedef);

						// Otomatik boyutlandırma (resize option varsa)
						if (isset($secenekler['resize']) && is_array($secenekler['resize']) && count($secenekler['resize']) >= 2) {
							$resim->thumbnail($secenekler['resize'][0], $secenekler['resize'][1]);
						}

						if (@$filtre == 1) {
							$resim->contrast(-8)->brighten(4);
						}

						$resim->toFile($hedef, NULL, $upload_resize_kalite);

						if (0 < count($boyutlar)) {
							boyutlandir($resim_url, $boyutlar, $secenekler);
						}

						if (isset($watermark)) {
							if (is_array($watermark)) {
								@$watermark_logo = $watermark['full'];
							}
							else {
								@$watermark_logo = $watermark;
							}

							if ($watermark_logo) {
								watermark($resim_url, $watermark_logo, $secenekler);
							}
						}

						if ($webp == 1) {
							webp_olustur($resim_url, $secenekler);
						}
					}

					if (!$cikti) {
						return $resim_url;
					}
					else {
						$return_url = thumbnail_url($cikti, $resim_url);

						if ($orijinal_sil == 1) {
							sil($resim_url);
						}

						return $return_url;
					}
				}
			}
		}
	}
	else {
		return false;
	}
}

function cek($url, $secenekler = NULL)
{
	global $admin_dizini;
	global $baglan;
	global $resize_kalite;
	global $standart_resim_boyutlari;
	@$dizin = $secenekler['dizin'];
	@$multiple = $secenekler['multiple'];
	@$boyutlar = $secenekler['boyutlar'];
	@$cikti = $secenekler['cikti'];
	@$orijinal_sil = $secenekler['orijinal_sil'];
	@$orijinal_optimize = $secenekler['orijinal_optimize'];
	@$tip = $secenekler['tip'];
	@$iliski_id = $secenekler['iliski_id'];
	@$iliski = $secenekler['iliski'];
	@$adlandir = $secenekler['adlandir'];
	@$adlandir_onek = $secenekler['adlandir_onek'];
	@$watermark = $secenekler['watermark'];
	@$kucultme_tercihi = $secenekler['kucultme_tercihi'];
	@$buyutme_tercihi = $secenekler['buyutme_tercihi'];
	@$upload_resize_kalite = $secenekler['resize_kalite'];
	@$webp = $secenekler['webp'];
	@$ext = pathinfo($url);
	$orj_resim_adi = $ext['basename'];
	$orj_uzanti = $ext['extension'];

	if (!$kucultme_tercihi) {
		$kucultme_tercihi = tema_ayar('resize_kucult');
	}

	if (!$buyutme_tercihi) {
		$buyutme_tercihi = tema_ayar('resize_buyut');
	}

	if (!$kucultme_tercihi) {
		$kucultme_tercihi = 'aspectratio';
	}

	if (!$buyutme_tercihi) {
		$buyutme_tercihi = 'aspectratio';
	}

	if (!$upload_resize_kalite) {
		$upload_resize_kalite = $resize_kalite;
	}

	$yil = date('Y');
	$ay = date('m');
	$gun = date('d');

	if (!$tip) {
		$tip = 'upload';
	}

	if (!$iliski) {
		$iliski = NULL;
	}

	if ($iliski_id) {
		$sakla_iliski_id = $iliski_id;
	}
	if (is_array($standart_resim_boyutlari) && (0 < count($standart_resim_boyutlari))) {
		if (is_array($boyutlar)) {
			$boyutlar = array_merge($boyutlar, $standart_resim_boyutlari);
		}
		else {
			$boyutlar = $standart_resim_boyutlari;
		}
	}

	if (!is_array($boyutlar)) { $boyutlar = array(); }
$boyutlar = array_unique($boyutlar);
	$resim_uzanti = uzanti($orj_resim_adi);
	$resim_adi_bol = explode((string) $resim_uzanti, $orj_resim_adi);
	$resim_orijinal_ad = $resim_adi_bol[0];

	if (30 < strlen($resim_orijinal_ad)) {
		$resim_orijinal_ad = substr($resim_orijinal_ad, 0, 30);
	}

	@$resim_name = url_olustur($resim_orijinal_ad);

	if (!$adlandir) {
		$resim_name = $resim_name . '-' . benzersiz();
	}
	else if ($adlandir == 1) {
		$resim_name = strtoupper(uniqid());
	}
	else if ($adlandir == 2) {
		$resim_name = strtoupper(md5(uniqid(mt_rand(), true)));
	}

	if ($adlandir_onek) {
		$resim_name = $adlandir_onek . $resim_name;
	}

	$resim_name = url_olustur($resim_name);
	$resim_adi = $resim_name . '.' . $resim_uzanti;
	$resim_uzanti = uzanti($resim_adi);
	if (($resim_uzanti == 'png') || ($resim_uzanti == 'jpg') || ($resim_uzanti == 'gif') || ($resim_uzanti == 'jpeg') || ($resim_uzanti == 'webp')) {
		$tur = 'image';

		if (!$dizin) {
			$dizin = 'images';
		}
	}
	else {
		$tur = 'file';

		if (!$dizin) {
			$dizin = 'files';
		}
	}

	if ($resim_adi) {
		$tam_dizin = $dizin . '/' . $yil . '/' . $ay . '/' . $gun;
		klasor_olustur('uploads/' . $tam_dizin);
		$hedef = ROOT_DIR . ('/uploads/' . $tam_dizin . '/' . $resim_adi);
		$resim_url = site_url() . ('/uploads/' . $tam_dizin . '/') . $resim_adi;
		$yukle = copy($url, $hedef);

		if ($yukle) {
			chmod($hedef, 438);
			include_once ROOT_DIR . ('/' . $admin_dizini . '/lib/SimpleImage.php');

			if ($tur == 'image') {
				$resim = new claviska\SimpleImage();
				$resim->fromFile($hedef)->contrast(-8)->brighten(4)->toFile($hedef, NULL, $upload_resize_kalite);

				if (0 < count($boyutlar)) {
					boyutlandir($resim_url, $boyutlar, $secenekler);
				}

				if (isset($watermark)) {
					if (is_array($watermark)) {
						@$watermark_logo = $watermark['full'];
					}
					else {
						@$watermark_logo = $watermark;
					}

					if ($watermark_logo) {
						watermark($resim_url, $watermark_logo, $secenekler);
					}
				}

				if ($webp == 1) {
					webp_olustur($resim_url, $secenekler);
				}
			}

			if (!$cikti) {
				return $resim_url;
			}
			else {
				$return_url = thumbnail_url($cikti, $resim_url);

				if ($orijinal_sil == 1) {
					sil($resim_url);
				}

				return $return_url;
			}
		}
	}
}

function boyutlandir($url, $boyutlar, $secenekler = NULL)
{
	global $admin_dizini;
	$tur = tur($url);

	if ($tur == 'resim') {
		global $resize_kalite;
		@$kucultme_tercihi = $secenekler['kucultme_tercihi'];
		@$buyutme_tercihi = $secenekler['buyutme_tercihi'];
		@$upload_resize_kalite = $secenekler['resize_kalite'];
		@$watermark = $secenekler['watermark'];
		@$webp = $secenekler['webp'];

		if (!$kucultme_tercihi) {
			$kucultme_tercihi = tema_ayar('resize_kucult');
		}

		if (!$buyutme_tercihi) {
			$buyutme_tercihi = tema_ayar('resize_buyut');
		}

		if (!$kucultme_tercihi) {
			$kucultme_tercihi = 'aspectratio';
		}

		if (!$buyutme_tercihi) {
			$buyutme_tercihi = 'aspectratio';
		}

		if (!$upload_resize_kalite) {
			$upload_resize_kalite = $resize_kalite;
		}

		include_once ROOT_DIR . ('/' . $admin_dizini . '/lib/SimpleImage.php');
		@$ext = pathinfo($url);
		$dirname = $ext['dirname'];
		$basename = $ext['basename'];
		$filename = $ext['filename'];
		$resim_uzanti = $ext['extension'];
		$imagesize = getimagesize($url);
		$resim_genislik = $imagesize[0];
		$resim_yukseklik = $imagesize[1];
		$tam_dizin_hazirlik = explode('uploads/', $dirname);
		$tam_dizin = $tam_dizin_hazirlik[1];

		if (0 < count($boyutlar)) {
			foreach ($boyutlar as $yeni_boyut) {
				$image = new claviska\SimpleImage();
				$image->fromFile($url);
				$yeni_boyut_parcala = explode('_', $yeni_boyut);
				$yeni_genislik = $yeni_boyut_parcala[0];
				$yeni_yukseklik = $yeni_boyut_parcala[1];
				$yeni_hedef = ROOT_DIR . ('/uploads/' . $tam_dizin . '/' . $filename . '-' . $yeni_boyut . '.' . $resim_uzanti);
				$yeni_img_url = site_url() . ('/uploads/' . $tam_dizin . '/' . $filename . '-' . $yeni_boyut . '.' . $resim_uzanti);
				$yeni_boyut_yol = 'uploads/' . $tam_dizin . '/' . $filename . '-' . $yeni_boyut . '.' . $resim_uzanti;
				if (($yeni_genislik <= $resim_genislik) && ($yeni_yukseklik <= $resim_yukseklik)) {
					if ($kucultme_tercihi == 'crop') {
						$image->thumbnail($yeni_genislik, $yeni_yukseklik)->toFile($yeni_hedef, NULL, $upload_resize_kalite);
					}
					else if ($kucultme_tercihi == 'resize') {
						$image->resize($yeni_genislik, $yeni_yukseklik)->toFile($yeni_hedef, NULL, $upload_resize_kalite);
					}
					else if ($kucultme_tercihi == 'aspectratio') {
						$aspectratio = $image->getAspectRatio();

						if (1 <= $aspectratio) {
							$image->resize($yeni_genislik, NULL);
						}
						else {
							$image->resize(NULL, $yeni_yukseklik);
						}

						$zemin_image = new claviska\SimpleImage();
						$zemin_image->fromNew($yeni_genislik, $yeni_yukseklik, '#fff')->overlay($image, 'center center')->toFile($yeni_hedef, NULL, $upload_resize_kalite);
					}
				}
				else {
					if ($buyutme_tercihi == 'resize') {
						$image->resize($yeni_genislik, $yeni_yukseklik)->toFile($yeni_hedef, NULL, $upload_resize_kalite);
					}
					else if ($buyutme_tercihi == 'aspectratio') {
						$image->bestFit($yeni_genislik, $yeni_yukseklik);
						$zemin_image = new claviska\SimpleImage();
						$zemin_image->fromNew($yeni_genislik, $yeni_yukseklik, '#fff')->overlay($image, 'center center')->toFile($yeni_hedef, NULL, $upload_resize_kalite);
					}
					else if ($buyutme_tercihi == 'standart') {
						$aspectratio = $image->getAspectRatio();

						if (1 <= $aspectratio) {
							$image->resize($yeni_genislik, NULL);
						}
						else {
							$image->resize(NULL, $yeni_yukseklik);
						}

						$image->bestFit($yeni_genislik, $yeni_yukseklik);
						$zemin_image = new claviska\SimpleImage();
						$zemin_image->fromNew($yeni_genislik, $yeni_yukseklik, '#fff')->overlay($image, 'center center')->toFile($yeni_hedef, NULL, $upload_resize_kalite);
					}

					echo $yeni_img_url . '<br />';
				}

				if (isset($watermark)) {
					if (is_array($watermark)) {
						@$watermark_logo = $watermark[$yeni_boyut];
					}
					else {
						@$watermark_logo = $watermark;
					}

					watermark($yeni_img_url, $watermark_logo, $secenekler);
				}

				if ($webp == 1) {
					webp_olustur($yeni_img_url, $secenekler);
				}
			}
		}
	}
	else {
		return false;
	}
}

function watermark($url, $watermark, $secenekler = NULL)
{
	global $admin_dizini;
	$tur = tur($url);

	if ($tur == 'resim') {
		global $resize_kalite;
		@$position = $secenekler['position'];
		@$upload_resize_kalite = $secenekler['resize_kalite'];

		if (!$position) {
			$position = 'center center';
		}

		if (!$upload_resize_kalite) {
			$upload_resize_kalite = $resize_kalite;
		}

		include_once ROOT_DIR . ('/' . $admin_dizini . '/lib/SimpleImage.php');
		@$ext = pathinfo($url);
		$dirname = $ext['dirname'];
		$basename = $ext['basename'];
		$filename = $ext['filename'];
		$resim_uzanti = $ext['extension'];
		$tam_dizin_hazirlik = explode('uploads/', $dirname);
		$tam_dizin = $tam_dizin_hazirlik[1];
		$hedef = ROOT_DIR . ('/uploads/' . $tam_dizin . '/' . $filename . '.' . $resim_uzanti);
		$yeni_img_url = site_url() . ('/uploads/' . $tam_dizin . '/' . $filename . '.' . $resim_uzanti);
		$image = new claviska\SimpleImage();
		$image->fromFile($url);

		if ($watermark) {
			$image->overlay($watermark, $position)->toFile($hedef, NULL, $upload_resize_kalite);
			return true;
		}
	}
	else {
		return false;
	}
}

function webp_olustur($url, $secenekler = NULL)
{
	global $admin_dizini;
	$tur = tur($url);

	if ($tur == 'resim') {
		@$upload_resize_kalite = $secenekler['resize_kalite'];

		if (!$upload_resize_kalite) {
			global $resize_kalite;
			$upload_resize_kalite = $resize_kalite;
		}

		include_once ROOT_DIR . ('/' . $admin_dizini . '/lib/SimpleImage.php');
		@$ext = pathinfo($url);
		$dirname = $ext['dirname'];
		$basename = $ext['basename'];
		$filename = $ext['filename'];
		$resim_uzanti = $ext['extension'];

		if ($resim_uzanti != 'webp') {
			$tam_dizin_hazirlik = explode('uploads/', $dirname);
			$tam_dizin = $tam_dizin_hazirlik[1];
			$webp_hedef = ROOT_DIR . ('/uploads/' . $tam_dizin . '/' . $filename . '.webp');
			$webp_url = site_url() . ('/uploads/' . $tam_dizin . '/' . $filename . '.webp');

			if (!upload_kontrol($webp_url)) {
				$image = new claviska\SimpleImage();
				$image->fromFile($url)->toFile($webp_hedef, 'image/webp', $upload_resize_kalite);
				return $webp_url;
			}
			else {
				return false;
			}
		}
	}
	else {
		return false;
	}
}

function tur($deger, $uzanti = false)
{
	if ($uzanti) {
		$file_uzanti = $deger;
	}
	else {
		$file_uzanti = uzanti($deger);
	}
	if (($file_uzanti == 'jpg') || ($file_uzanti == 'jpeg') || ($file_uzanti == 'png') || ($file_uzanti == 'webp') || ($file_uzanti == 'gif')) {
		return 'resim';
	}
	else {
		return 'dosya';
	}
}

function uzanti($file)
{
	if ($file) {
		@$ext = pathinfo($file);
		return strtolower($ext['extension']);
	}
}

function galeri_ekle($url, $icerik_id, $sira = NULL)
{
	global $baglan;

	if (!$sira) {
		$say = (mysqli_query($baglan, 'select COUNT(id) from galeri where icerik_id=\'' . $icerik_id . '\'') ? (mysqli_query($baglan, 'select COUNT(id) from galeri where icerik_id=\'' . $icerik_id . '\'') ? mysqli_fetch_array(mysqli_query($baglan, 'select COUNT(id) from galeri where icerik_id=\'' . $icerik_id . '\'')) : null) : null)[0];
		$sira = $say * 10;
	}

	$sql = mysqli_query($baglan, 'insert into galeri set resim=\'' . $url . '\', icerik_id=\'' . $icerik_id . '\', baslik=\'galeri\', url=\'galeri\', sira=\'' . $sira . '\'');
}

function tumunu_sil($url, $array = NULL)
{
	sil($url);

	if (is_array($array)) {
		foreach ($array as $arr) {
			$boyut_url = thumbnail_url($arr, $url);
			sil($boyut_url);
		}
	}
}

function page_header($baslik, $buton = NULL, $sag_buton = NULL, $sag_bilgi = NULL, $mesaj = NULL)
{
	global $admin_dizini;

	if (is_array($array)) {
		@$baslik = $array['baslik'];
		@$buton = $array['buton'];
		@$sag_buton = $array['sag_buton'];
		@$sag_bilgi = $array['sag_bilgi'];
		@$mesaj = $array['mesaj'];
	}
	else {
		$baslik = $array;
	}

	if (!isset($mesaj)) {
		$mesaj = true;
	}

	if (@$baslik) {
		echo '<h3 class="pull-left bolum-baslik text-uppercase">';
		echo $baslik;
		echo '</h3>' . "\n";
		echo $buton;
		echo '<div class="clearfix mb20"></div>' . "\n";
	}
	else {
		echo '<div class=\'alert alert-danger\'>' . _LANG('f_baslik_tanimlanmamis') . '</div>';
	}
}

function is_localhost()
{
	if (getenv('HTTP_HOST') == 'localhost') {
		return true;
	}
	else {
		return false;
	}
}

function upload_mime_type($url)
{
	global $baglan;
	@$ext = pathinfo($url);
	$yol = ROOT_DIR . '/' . ltrim(str_replace(site_url(), NULL, $url), '/');
	return mime_content_type($yol);
}

function api_token()
{
	$date = date('YmdH');
	$domain = domain();
	return strtoupper(md5(md5($domain . '-' . date('YmdH')) . md5('jaayquickilan')));
}

/*
 * HTML cikti kacisi. Panelde ve temada kullanici kaynakli veriler
 * (uye adi, e-posta, yorum metni, kategori basligi vb.) ekrana
 * basilirken kullanilir.
 */
function e($deger)
{
	return htmlspecialchars((string)$deger, ENT_QUOTES, 'UTF-8');
}

/*
 * JavaScript baglaminda cikti kacisi (onclick="return confirm('...')").
 * HTML ozel karakterleri once kacirilir, ardindan tirnak ve ters
 * slash karakterleri JS dizisi icin kacirilir.
 */
function ejs($deger)
{
	return addslashes(htmlspecialchars((string)$deger, ENT_QUOTES, 'UTF-8'));
}
/*
 * Tablo ve sutun adlari SQL metnine gomulur; kullanici girdisiyle
 * degistirilemeleri icin yalnizca harf, rakam ve alt cizgi kabul edilir.
 */
function sql_ad_guvenli($ad)
{
	$ad = (string)$ad;
	return preg_match('/^[A-Za-z0-9_]+$/', $ad) ? $ad : '';
}
function copyright()
{
	global $yazilim_bilgisi_remove_copyright;
	global $foot_kod;

	if ($yazilim_bilgisi_remove_copyright != 1) {
		// ilanpiyasasi.com artik kullanilmiyor; telif modulunden cikti beklenmiyor.
	}
}

if (count(get_included_files()) == 1) {
	exit();
}

$include_dizin = include_dizin();
$admin_dizini = admin_dizini();
yazilim_bilgisi();
copyright();

?>
