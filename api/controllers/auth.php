<?php
/* JaayQuick API - kimlik dogrulama controller */

function api_kullanici_dizisi($uye_id)
{
	$user = array('id' => (int)$uye_id);
	$ad = bilgi_getir('uyeler', 'ad_soyad', $uye_id);
	$email = bilgi_getir('uyeler', 'email_adresi', $uye_id);
	$user['name'] = $ad ? tirnak_ekle($ad) : null;
	$user['email'] = $email ? tirnak_ekle($email) : null;
	$tel = api_ekstra_yazi_oku('cep_tel', 'uye', $uye_id);
	$user['phone'] = $tel;
	return $user;
}

/** ekstra_bilgi okuma aracı (uye tablolari için cep_tel gibi) */
function api_ekstra_yazi_oku($anahtar, $tip, $id)
{
	global $baglan;
	$id = (int)$id;
	if ($id < 1) {
		return null;
	}
	$sorgu = mysqli_prepare($baglan, 'select deger from ekstra_bilgi where anahtar=? and tip=? and id=? limit 1');
	mysqli_stmt_bind_param($sorgu, 'ssi', $anahtar, $tip, $id);
	mysqli_stmt_execute($sorgu);
	mysqli_stmt_store_result($sorgu);
	if (mysqli_stmt_num_rows($sorgu) !== 1) {
		mysqli_stmt_close($sorgu);
		return null;
	}
	mysqli_stmt_bind_result($sorgu, $deger);
	mysqli_stmt_fetch($sorgu);
	mysqli_stmt_close($sorgu);
	return tirnak_ekle($deger);
}

function api_auth_rute($yontem, $alt)
{
	switch ($alt) {
		case 'login':
			if ($yontem !== 'POST') {
				api_hata('Method not allowed', 405);
			}
			api_auth_login();
			break;
		case 'register':
			if ($yontem !== 'POST') {
				api_hata('Method not allowed', 405);
			}
			api_auth_register();
			break;
		case 'me':
			if ($yontem !== 'GET') {
				api_hata('Method not allowed', 405);
			}
			api_auth_me();
			break;
		case 'logout':
			if ($yontem !== 'POST') {
				api_hata('Method not allowed', 405);
			}
			api_auth_logout();
			break;
		default:
			api_hata('Route not found', 404);
	}
}

function api_auth_login()
{
	global $baglan;
	$girdi = api_girdi();
	$email = api_metin('email');
	$parola = isset($girdi['password']) ? (string)$girdi['password'] : '';

	if ($email === '' or $parola === '') {
		api_hata('E-mail or phone and password are required.', 422);
	}

	// E-posta ya da telefon ile uye bulunur (uye_sorgula ile ayni davranis)
	$uye_id = 0;
	if (strpos($email, '@') !== false) {
		$sorgu = mysqli_prepare($baglan, 'select id from uyeler where email_adresi=? limit 1');
		mysqli_stmt_bind_param($sorgu, 's', $email);
	} else {
		$sorgu = mysqli_prepare($baglan, '
			select u.id from uyeler u
			inner join ekstra_bilgi eb on eb.id=u.id
			where eb.anahtar="cep_tel" and eb.tip="uye" and eb.deger=?
			limit 1
		');
		mysqli_stmt_bind_param($sorgu, 's', $email);
	}
	mysqli_stmt_execute($sorgu);
	mysqli_stmt_store_result($sorgu);
	if (mysqli_stmt_num_rows($sorgu) === 1) {
		mysqli_stmt_bind_result($sorgu, $uye_id);
		mysqli_stmt_fetch($sorgu);
	}
	mysqli_stmt_close($sorgu);

	if (!$uye_id or !parola_dogrula($uye_id, $parola)) {
		api_hata('Incorrect e-mail or password.', 401);
	}

	// onaylanmamis uye (durum=0) giris yapamaz, onay istenir
	$durum = bilgi_getir('uyeler', 'durum', $uye_id);
	if ((string)$durum === '0') {
		api_hata('Please confirm your e-mail address before logging in.', 403);
	}

	api_token_sil($uye_id); // tek aktif token: yeni giris eskisini atar
	$token = api_token_olustur($uye_id);
	api_basari(array(
		'token' => $token,
		'user' => api_kullanici_dizisi($uye_id),
	));
}

function api_auth_register()
{
	global $baglan;
	$ad_soyad = api_metin('name');
	$email    = strtolower(api_metin('email'));
	$telefon  = api_metin('phone');
	$parola   = '';
	$parola_t = '';
	$girdi = api_girdi();
	$parola  = isset($girdi['password']) ? (string)$girdi['password'] : (isset($_POST['password']) ? (string)$_POST['password'] : '');
	$parola_t = isset($girdi['password_confirmation']) ? (string)$girdi['password_confirmation'] : (isset($_POST['password_confirmation']) ? (string)$_POST['password_confirmation'] : $parola);

	$alanlar = array();
	if ($ad_soyad === '') { $alanlar['name'] = 'Full name is required.'; }
	if ($email === '') { $alanlar['email'] = 'E-mail is required.'; }
	elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $alanlar['email'] = 'E-mail is not valid.'; }
	if (strlen($parola) < 6) { $alanlar['password'] = 'Password must be at least 6 characters.'; }
	elseif ($parola !== $parola_t) { $alanlar['password_confirmation'] = 'Passwords do not match.'; }
	if ($telefon !== '') { $telefon = telefon_format($telefon); }
	if (count($alanlar)) {
		api_hata('Registration failed. Please check the fields.', 422, $alanlar);
	}

	// e-posta benzersiz mi?
	$var = 0;
	$sorgu = mysqli_prepare($baglan, 'select count(id) from uyeler where email_adresi=?');
	mysqli_stmt_bind_param($sorgu, 's', $email);
	mysqli_stmt_execute($sorgu);
	mysqli_stmt_bind_result($sorgu, $var);
	mysqli_stmt_fetch($sorgu);
	mysqli_stmt_close($sorgu);
	if ((int)$var > 0) {
		api_hata('This e-mail is already registered.', 409, array('email' => 'Already registered.'));
	}

	// telefon benzersiz mi?
	if ($telefon !== '') {
		$sorgu = mysqli_prepare($baglan, '
			select count(*) from ekstra_bilgi where anahtar="cep_tel" and tip="uye" and deger=?
		');
		mysqli_stmt_bind_param($sorgu, 's', $telefon);
		mysqli_stmt_execute($sorgu);
		mysqli_stmt_bind_result($sorgu, $var);
		mysqli_stmt_fetch($sorgu);
		mysqli_stmt_close($sorgu);
		if ((int)$var > 0) {
			api_hata('This phone number is already registered.', 409, array('phone' => 'Already registered.'));
		}
	}

	// unique profil slug (url_olustur + benzersiz son ek)
	$tarih_saat = tarih_saat();
	$profil_url = url_olustur($ad_soyad) . '-' . benzersiz();
	$parola_hash = parola_sakla($parola);

	// Mobil akisinda e-posta dogrulamasi olmadigi icin uye dogrudan aktif (durum=1)
	// yaratilir; web tarafi icin dur=0 + dogrulama e-postasi kullanilir.
	$yetki = 'kullanici';
	$durum = 1;
	$sorgu = mysqli_prepare($baglan, 'insert into uyeler (ad_soyad, url, email_adresi, parola, tarih_saat, yetki, durum) values (?, ?, ?, ?, ?, ?, ?)');
	mysqli_stmt_bind_param($sorgu, 'sssssis', $ad_soyad, $profil_url, $email, $parola_hash, $tarih_saat, $yetki, $durum);
	mysqli_stmt_execute($sorgu);
	mysqli_stmt_close($sorgu);

	$uye_id = (int)mysqli_insert_id($baglan);
	if ($uye_id < 1) {
		api_hata('Registration failed.', 500);
	}

	// cep_tel ekstra_bilgi'ye yazilir (site telefonu burada tutar)
	if ($telefon !== '') {
		ekstra_bilgi_guncelle('cep_tel', $telefon, $uye_id, 'uye');
	}

	$token = api_token_olustur($uye_id);
	api_basari(array(
		'token' => $token,
		'user' => api_kullanici_dizisi($uye_id),
	), 201);
}

function api_auth_me()
{
	$uye = api_uye(true);
	api_basari(array('user' => $uye));
}

function api_auth_logout()
{
	$uye = api_uye(false);
	if ($uye) {
		api_token_sil($uye['id']);
	}
	api_basari(array('ok' => true));
}
