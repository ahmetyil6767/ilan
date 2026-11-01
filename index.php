<?php
include("loader.php");

// E-posta dogrulama baglantisi isleyicisi (?email=...&key=...)
// Kayit sirasinda gonderilen dogrulama e-postasindaki baglanti burada cozumlenir.
if (isset($_GET['email']) && isset($_GET['key']) && $_GET['email'] !== '' && $_GET['key'] !== '') {
	global $baglan;
	$email = trim((string)$_GET['email']);
	$key = trim((string)$_GET['key']);
	$uye_id = 0;
	$s = mysqli_prepare($baglan, "select id from uyeler where email_adresi=? limit 1");
	if ($s) {
		mysqli_stmt_bind_param($s, "s", $email);
		mysqli_stmt_execute($s);
		mysqli_stmt_bind_result($s, $uye_id);
		mysqli_stmt_fetch($s);
		mysqli_stmt_close($s);
	}
	$basarili = false;
	if ($uye_id > 0) {
		$kayitli_kod = ekstra_bilgi_getir('email_onay_kodu', $uye_id, 'uye');
		$kayitli_tarih = ekstra_bilgi_getir('email_onay_kodu_tarih_saat', $uye_id, 'uye');
		// Kod gecerli mi? (case-insensitive, 24 saat icinde)
		if ($kayitli_kod !== NULL && strcasecmp((string)$kayitli_kod, $key) === 0) {
			$suresi_doldu = false;
			if ($kayitli_tarih) {
				$ts = strtotime((string)$kayitli_tarih);
				if ($ts !== false && (time() - $ts) > 86400) {
					$suresi_doldu = true;
				}
			}
			if (!$suresi_doldu) {
				uye_bilgi_guncelle('email_onay_kodu', '', $uye_id);
				uye_bilgi_guncelle('email_onay_kodu_tarih_saat', 'verified:' . date('Y-m-d H:i:s'), $uye_id);
				$basarili = true;
			}
		}
	}
	if ($basarili) {
		$_SESSION["mesaj"] = array(_LANG('msj_email_dogrulama_basarili'), "success");
	} else {
		$_SESSION["mesaj"] = array(_LANG('msj_email_dogrulama_basarisiz'), "danger");
	}
	header('Location: ' . ozel_url("giris-yap"));
	exit();
}

// Tema desteği açıksa temanın index dosyasını include edelim.
if ($tema_destegi==1) {include("$temalar_dizini/$aktif_tema/index.php"); exit();}
?>