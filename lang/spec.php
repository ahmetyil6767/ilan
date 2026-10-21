<?php
/*
 * Ozet: kategori ozel alanlari + teknik ozellik etiketlerine ait arayuz
 * cevirileri. Kaynak TR etiket -> array(EN, FR, AR). Yardimci fonksiyon
 * config/fonksiyonlar_meta.php: spec_etiket().
 */
return array_merge(
	require __DIR__ . '/spec_a.php',
	require __DIR__ . '/spec_b.php',
	require __DIR__ . '/spec_c.php'
);
