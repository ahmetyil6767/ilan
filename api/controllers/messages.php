<?php
/*
 * JaayQuick API - mesajlasma controller
 *
 * gorusmeler: id, uye_1, uye_2, tarih_saat, ilan_id, sil_1, sil_2
 * mesajlar:   id, gorusme_id, uye_id, kime, mesaj, tarih_saat, okundu
 * (eklenti/mesajlasma ile ayni tablolar; API sadece okuma + yeni mesaj saglar)
 */

function api_messages_rute($yontem)
{
	if ($yontem === 'GET') {
		api_messages_list();
		return;
	}
	if ($yontem === 'POST') {
		api_messages_send();
		return;
	}
	api_hata('Method not allowed', 405);
}

/* =========================================================
 *  GET /messages (Bearer) - sohbet konu listesi
 * ========================================================= */

function api_messages_list()
{
	$uye = api_uye(true);

	// gelecek gorusmeler: karsi uye, ilan basligi ve gorseliyle
	$gorusmeler = api_sorgu_satirlar(
		'
		select g.id, g.uye_1, g.uye_2, g.ilan_id, g.tarih_saat,
		       k.ad_soyad AS karsi_ad, c.baslik AS ilan_baslik
		from gorusmeler g
		left join uyeler k on k.id = CASE WHEN g.uye_1 = ? THEN g.uye_2 ELSE g.uye_1 END
		left join icerik c on c.id = g.ilan_id
		where (g.uye_1 = ? and g.sil_1 = 0) or (g.uye_2 = ? and g.sil_2 = 0)
		order by g.tarih_saat desc, g.id desc
		limit 100
		',
		array($uye['id'], $uye['id'], $uye['id'])
	);

	$message_veriler = array();
	if (count($gorusmeler)) {
		$gorusme_ids = array();
		foreach ($gorusmeler as $g) {
			$gorusme_ids[] = (int)$g['id'];
		}
		$id_string = implode(',', array_map('intval', $gorusme_ids));
		$tum_mesajlar = api_sorgu_satirlar(
			"select id, gorusme_id, uye_id, mesaj, tarih_saat, okundu from mesajlar where gorusme_id in ($id_string) order by id asc"
		);

		// gorusme bazinda en son mesaji ve okunmamis sayisini topla
		$son_mesajlar = array();
		foreach ($tum_mesajlar as $m) {
			$gid = (int)$m['gorusme_id'];
			$son_mesajlar[$gid] = $m; // id asc sirali: son iteration en son mesaj
		}

		foreach ($gorusmeler as $g) {
			$gid = (int)$g['id'];
			$son = isset($son_mesajlar[$gid]) ? $son_mesajlar[$gid] : null;
			$message_veriler[] = array(
				'id'            => $gid,
				'sender_name'   => $g['karsi_ad'] ? (string)$g['karsi_ad'] : 'JaayQuick member',
				'message'       => $son ? (string)$son['mesaj'] : '',
				'created_at'    => $son ? (string)$son['tarih_saat'] : (string)$g['tarih_saat'],
				'unread'        => $son and (int)$son['okundu'] === 0 and (int)$son['uye_id'] !== (int)$uye['id'],
				'listing_id'    => $g['ilan_id'] ? (int)$g['ilan_id'] : null,
				'listing_title' => $g['ilan_baslik'] ? (string)$g['ilan_baslik'] : null,
			);
		}
	}

	api_basari(array('data' => $message_veriler));
}

/* =========================================================
 *  POST /messages {listing_id, message} (Bearer) - saticiya mesaj
 * ========================================================= */

function api_messages_send()
{
	global $baglan;
	$uye = api_uye(true);
	$girdi = api_girdi();

	$ilan_id = isset($girdi['listing_id']) ? (int)$girdi['listing_id'] : 0;
	$mesaj = isset($girdi['message']) ? trim(strip_tags((string)$girdi['message'])) : '';

	if ($ilan_id < 1 or $mesaj === '') {
		api_hata('listing_id and message are required.', 422);
	}

	$ilan = api_sorgu_satir("select id, yazar from icerik where id=? and tip='ilan' limit 1", array($ilan_id));
	if (!$ilan) {
		api_hata('Listing not found', 404);
	}

	$satici_id = (int)$ilan['yazar'];
	if ($satici_id === (int)$uye['id']) {
		api_hata('You cannot message yourself about your own listing.', 422);
	}

	// var olan sohbeti tekrar kullan; yoksa olustur
	$gorusme = api_sorgu_satir(
		'select id from gorusmeler where ilan_id=? and ((uye_1=? and uye_2=?) or (uye_2=? and uye_1=?)) limit 1',
		array($ilan_id, $uye['id'], $satici_id, $uye['id'], $satici_id)
	);

	if ($gorusme) {
		$gorusme_id = (int)$gorusme['id'];
	} else {
		$tarih = tarih_saat();
		$ekle = api_sorgu(
			'insert into gorusmeler set uye_1=?, uye_2=?, tarih_saat=?, ilan_id=?, sil_1=0, sil_2=0',
			array($uye['id'], $satici_id, $tarih, $ilan_id)
		);
		if (!$ekle) {
			api_hata('Could not start conversation.', 500);
		}
		$gorusme_id = (int)mysqli_insert_id($baglan);
	}

	$tarih = tarih_saat();
	$ekle = api_sorgu(
		'insert into mesajlar set gorusme_id=?, uye_id=?, kime=?, mesaj=?, tarih_saat=?, okundu=0',
		array($gorusme_id, $uye['id'], $satici_id, $mesaj, $tarih)
	);
	if (!$ekle) {
		api_hata('Message could not be sent.', 500);
	}

	// gorusmeyi yukari tasi (tes liste bu siralanir)
	api_sorgu('update gorusmeler set tarih_saat=? where id=?', array($tarih, $gorusme_id));

	api_basari(array('ok' => true, 'gorusme_id' => $gorusme_id), 201);
}
