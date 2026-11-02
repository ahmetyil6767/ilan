<?php
/*
 * JaayQuick API - favoriler controller
 * favoriler: f_id (PK), id (icerik id), uye_id, tarih
 */

function api_favorites_rute($yontem, $id)
{
	if ($yontem === 'GET') {
		api_favorites_list();
		return;
	}
	if ($yontem === 'POST') {
		api_favorites_add();
		return;
	}
	if ($yontem === 'DELETE' and $id !== null) {
		api_favorites_remove(is_numeric($id) ? (int)$id : 0);
		return;
	}
	api_hata('Method not allowed', 405);
}

/* =========================================================
 *  GET /favorites (Bearer)
 * ========================================================= */

function api_favorites_list()
{
	$uye = api_uye(true);
	$joins = api_ilan_join_esle();

	$satirlar = api_sorgu_satirlar(
		'select ' . api_ilan_select_cumlesi() . "
		from favoriler fv
		inner join icerik c on c.id = fv.id
		$joins
		where fv.uye_id=? and c.tip='ilan'
		order by fv.f_id desc
		limit 100
		",
		array($uye['id'])
	);

	$veriler = array();
	foreach ($satirlar as $kayit) {
		$ilan = api_ilan_dizisi($kayit, true);
		$ilan['favorited_at'] = null; // favoriler.tarih bicimi degisken; sade tutuldu
		$veriler[] = $ilan;
	}

	api_basari(array('data' => $veriler));
}

/* =========================================================
 *  POST /favorites {listing_id} (Bearer) - favoriye ekle
 * ========================================================= */

function api_favorites_add()
{
	global $baglan;
	$uye = api_uye(true);
	$girdi = api_girdi();

	$ilan_id = isset($girdi['listing_id']) ? (int)$girdi['listing_id'] : 0;
	if ($ilan_id < 1) {
		api_hata('listing_id is required.', 422);
	}

	$ilan = api_sorgu_satir("select id from icerik where id=? and tip='ilan' and durum='1' limit 1", array($ilan_id));
	if (!$ilan) {
		api_hata('Listing not found', 404);
	}

	// zaten favoride ise idempotent davran
	$mevcut = api_sorgu_satir('select f_id from favoriler where id=? and uye_id=? limit 1', array($ilan_id, $uye['id']));
	if ($mevcut) {
		api_basari(array('ok' => true, 'favorited' => true));
	}

	$ekle = api_sorgu('insert into favoriler set id=?, uye_id=?, tarih=?', array($ilan_id, $uye['id'], tarih_saat()));
	if (!$ekle) {
		api_hata('Could not add to favorites.', 500);
	}
	api_basari(array('ok' => true, 'favorited' => true), 201);
}

/* =========================================================
 *  DELETE /favorites/:id (Bearer) - favoriden cikar (icerik id)
 * ========================================================= */

function api_favorites_remove($ilan_id)
{
	$uye = api_uye(true);
	if ($ilan_id < 1) {
		api_hata('Route not found', 404);
	}

	api_sorgu('delete from favoriler where id=? and uye_id=?', array($ilan_id, $uye['id']));
	api_basari(array('ok' => true, 'favorited' => false));
}
