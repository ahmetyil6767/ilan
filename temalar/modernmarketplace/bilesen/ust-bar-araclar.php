<?php
if (count(get_included_files()) == 1) { die(); }

// Aktif dil ve para birimi - handle URL parameters BEFORE calling aktif_dil()/aktif_birim()
if (isset($_GET['dil'])) {
	$dil_param = strtolower(trim($_GET['dil']));
	$desteklenen_diller = array('tr', 'en', 'fr', 'ar');
	if (in_array($dil_param, $desteklenen_diller)) {
		$_SESSION['dil'] = $dil_param;
		setcookie('dil', $dil_param, time() + 31536000, '/');
	}
}

if (isset($_GET['birim'])) {
	$pb = strtolower(trim($_GET['birim']));
	$desteklenen = array('gmd', 'usd', 'eur', 'gbp');
	if (in_array($pb, $desteklenen)) {
		$_SESSION['para_birimi'] = $pb;
		setcookie('birim', $pb, time() + 31536000, '/');
	}
}

$aktif_dil = function_exists('aktif_dil') ? aktif_dil() : (isset($_SESSION['dil']) ? $_SESSION['dil'] : 'tr');
$aktif_para = function_exists('aktif_birim') ? aktif_birim() : (isset($_SESSION['para_birimi']) ? $_SESSION['para_birimi'] : 'gmd');

// Dil bayragi/etiketleri
$diller = array(
	'tr' => array('TR', 'Türkçe'),
	'en' => array('EN', 'English'),
	'fr' => array('FR', 'Français'),
	'ar' => array('AR', 'العربية'),
);
$paralar = array(
	'gmd' => 'GMD',
	'usd' => 'USD',
	'eur' => 'EUR',
	'gbp' => 'GBP',
);

// Use the backend's _DIL_DEGISTIR_LINK and _BIRIM_DEGISTIR_LINK functions
?>
<style>
.ust-bar-araclar { display: inline-flex; align-items: center; gap: 6px; margin-left: 8px; }
.ust-bar-araclar .dropdown-toggle { background: transparent; border: 1px solid rgba(255,255,255,0.18); color: #fff; border-radius: 14px; padding: 4px 10px; font-size: 11px; font-weight: 600; letter-spacing: .5px; }
.ust-bar-araclar .dropdown-toggle:hover { background: rgba(255,255,255,0.08); color: #fff; }
.ust-bar-araclar .dropdown-menu { min-width: 110px; border-radius: 10px; }
.ust-bar-araclar .dropdown-menu > li > a { font-size: 12px; padding: 6px 12px; }
@media (max-width: 767px) { .ust-bar-araclar { gap: 4px; } .ust-bar-araclar .dropdown-toggle { font-size: 10px; padding: 3px 8px; } }
</style>
<div class="ust-bar-araclar">
	<div class="btn-group">
		<a class="btn btn-xs dropdown-toggle header_link" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
			<i class="fa fa-globe"></i> <?php echo isset($diller[$aktif_dil]) ? $diller[$aktif_dil][0] : 'TR'; ?> <span class="caret"></span>
		</a>
		<ul class="dropdown-menu dropdown-menu-right">
			<?php foreach ($diller as $kod => $bilgi) { ?>
			<li class="<?php echo ($aktif_dil === $kod) ? 'active' : ''; ?>"><a href="<?php echo _DIL_DEGISTIR_LINK($kod); ?>"><?php echo $bilgi[0]; ?> — <?php echo $bilgi[1]; ?></a></li>
			<?php } ?>
		</ul>
	</div>
	<div class="btn-group">
		<a class="btn btn-xs dropdown-toggle header_link" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
			<i class="fa fa-money"></i> <?php echo isset($paralar[$aktif_para]) ? $paralar[$aktif_para] : 'GMD'; ?> <span class="caret"></span>
		</a>
		<ul class="dropdown-menu dropdown-menu-right">
			<?php foreach ($paralar as $kod => $ad) { ?>
			<li class="<?php echo ($aktif_para === $kod) ? 'active' : ''; ?>"><a href="<?php echo _BIRIM_DEGISTIR_LINK($kod); ?>"><?php echo $ad; ?></a></li>
			<?php } ?>
		</ul>
	</div>
</div>