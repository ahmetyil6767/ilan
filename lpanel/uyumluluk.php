<?php
include("../loader.php");
yonetici_degilse_yonlendir($oturum_id,ayar_getir("site_url")."/$admin_dizini/giris-yap.php");
?>
<!DOCTYPE HTML>
<html lang="<?php echo _DIL_HTML_LANG(); ?>" dir="<?php echo _DIL_HTML_YON(); ?>">
<head>
	<meta charset="UTF-8">
	<title><?php echo _LANG('p_uyumluluk_merkezi'); ?> - <?php echo $lpanel_baslik; ?></title>
	<?php include("inc/header-kod.php"); ?>
	<meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
<div id="wrapper" class="<?php echo $kapsa_container; ?>">
	<div class="row">
		<div id="sidebar-wrapper" class="<?php echo $sidebar_div; ?>">
		<?php include("inc/sidebar.php"); ?>
		</div>
		<div id="page-content-wrapper" class="<?php echo $sag_div; ?>">
		<?php include("inc/ust.php"); ?>
			<div class="content">
			

			<?php 
			function GetIonCubeLoaderVersion() {
				ob_start();
				phpinfo(INFO_GENERAL);
				$aux = str_replace('&nbsp;', ' ', ob_get_clean());
				if($aux !== false) {
					$pos = stripos($aux, 'ionCube PHP Loader');
					if($pos !== false) {
						$aux = substr($aux, $pos + 18);
						$aux = substr($aux, stripos($aux, ' v') + 2);
						$version = '';
						$c = 0;
						$char = substr($aux, $c++, 1);
						while(strpos('0123456789.', $char) !== false) {
							$version .= $char;
							$char = substr($aux, $c++, 1);
						}
						return $version;
					}
				}
				return false;
			}
			?>
			<div class="alert alert-info"><?php echo _LANG("p_uyumluluk_intro"); ?></div>
			<div class="table-responsive" style="box-shadow:none;">
				<table class="table bg-light table-sm table-bordered">
					<thead>
						<tr>
							<th><?php echo _LANG('p_ayar'); ?></th>
							<th width="5"><?php echo _LANG('p_deger'); ?></th>
							<th width="5"><?php echo _LANG('p_ne_zaman'); ?></th>
							<th><?php echo _LANG('p_aciklama'); ?></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<th width="5">php versiyonu</th>
							<td><?php echo phpversion(); ?></td>
							<td>7.2</td>
							<td><?php echo _LANG('p_uyumluluk_sunucu_php'); ?> <span class="text-muted"><?php echo _LANG('p_uyum_gereksiz'); ?></span></td>
						</tr>
						<tr>
							<th width="5">ioncube loader</th>
							<td><?php echo GetIonCubeLoaderVersion(); ?></td>
							<td>10.2</td>
							<td><?php echo _LANG('p_uyumluluk_sunucu_ioncube'); ?> <span class="text-muted"><?php echo _LANG('p_uyum_gereksiz'); ?></span></td>
						</tr>
						<tr <?php $mbstring=extension_loaded('mbstring'); if (!$mbstring) {echo "class='warning'";} ?>>
							<th width="5">mbstring</th>
							<td><?php echo $mbstring; ?></td>
							<td>1</td>
							<td><?php echo _LANG('p_mbstring'); ?> <?php if (!$mbstring) {echo "<span class='text-danger' data-toggle='tooltip' title='" . _LANG('p_ipucu_mbstring') . "' style='cursor:help;'><i class='fa fa-info-circle clear ml-2 mr-1'></i>" . _LANG('p_duzeltilmesi_gerekiyor') . "</span>";} ?></td>
						</tr>
						<tr <?php $allow_url_fopen=ini_get("allow_url_fopen"); if ($allow_url_fopen!=1) {echo "class='danger'";} ?>>
							<th width="5">allow_url_fopen</th>
							<td><?php echo $allow_url_fopen; ?></td>
							<td>1</td>
							<td><?php echo _LANG('p_allow_url_fopen'); ?> <?php if ($allow_url_fopen!=1) {echo "<span class='text-danger' data-toggle='tooltip' title='" . _LANG('p_ipucu_allow_url_fopen') . "' style='cursor:help;'><i class='fa fa-info-circle clear ml-2 mr-1'></i>" . _LANG('p_duzeltilmesi_gerekiyor') . "</span>";} ?></td>
						</tr>
						<tr <?php $display_errors=ini_get("display_errors"); if ($display_errors!=0) {echo "class='danger'";} ?>>
							<th width="5">display_errors</th>
							<td><?php echo $display_errors; ?></td>
							<td>0</td>
							<td><?php echo _LANG('p_hata_goruntuleme'); ?> <?php if ($display_errors!=0) {echo "<span class='text-danger' data-toggle='tooltip' title='" . _LANG('p_ipucu_hata_goruntuleme') . "' style='cursor:help;'><i class='fa fa-info-circle clear ml-2 mr-1'></i>" . _LANG('p_duzeltilmesi_gerekiyor') . "</span>";} ?></td>
						</tr>
						<tr <?php $memory_limit=ini_get("memory_limit"); if ($memory_limit!="-1" and substr($memory_limit,0,-1)<256) {echo "class='danger'";} ?>>
							<th width="5">memory_limit</th>
							<td><?php echo $memory_limit; ?></td>
							<td>-1 veya 256M</td>
							<td><?php echo _LANG('p_memory_limit'); ?> <?php if ($memory_limit!="-1" and substr($memory_limit,0,-1)<256) {echo "<span class='text-danger' data-toggle='tooltip' title='" . _LANG('p_ipucu_memory_limit') . "' style='cursor:help;'><i class='fa fa-info-circle clear ml-2 mr-1'></i>" . _LANG('p_duzeltilmesi_gerekiyor') . "</span>";} ?></td>
						</tr>
						<tr <?php $max_execution_time=ini_get("max_execution_time"); if ($max_execution_time<120) {echo "class='danger'";} ?>>
							<th width="5">max_execution_time</th>
							<td><?php  echo $max_execution_time; ?></td>
							<td>120</td>
							<td><?php echo _LANG('p_max_execution_time'); ?> <?php if ($max_execution_time<120) {echo "<span class='text-danger' data-toggle='tooltip' title='" . _LANG('p_ipucu_max_execution_time') . "' style='cursor:help;'><i class='fa fa-info-circle clear ml-2 mr-1'></i>" . _LANG('p_duzeltilmesi_gerekiyor') . "</span>";} ?></td>
						</tr>
						<tr <?php $post_max_size=ini_get("post_max_size"); if (substr($post_max_size,0,-1)<64) {echo "class='warning'";} ?>>
							<th width="5">post_max_size</th>
							<td><?php echo $post_max_size; ?></td>
							<td>64M</td>
							<td><?php echo _LANG('p_post_max_size'); ?> <?php if (substr($post_max_size,0,-1)<64) {echo "<span class='text-info' data-toggle='tooltip' title='" . _LANG('p_ipucu_post_max_size', array($post_max_size)) . "' style='cursor:help;'><i class='fa fa-info-circle clear ml-2 mr-1'></i>" . _LANG('p_duzeltilmesi_oneriliyor') . "</span>";} ?></td>
						</tr>
						<tr <?php $upload_max_filesize=ini_get("upload_max_filesize"); if (substr($upload_max_filesize,0,-1)<16) {echo "class='warning'";} ?>>
							<th width="5">upload_max_filesize</th>
							<td><?php echo $upload_max_filesize; ?></td>
							<td>16M</td>
							<td><?php echo _LANG('p_upload_max_filesize'); ?> <?php if (substr($upload_max_filesize,0,-1)<16) {echo "<span class='text-info' data-toggle='tooltip' title='" . _LANG('p_ipucu_upload_max_filesize', array($upload_max_filesize)) . "' style='cursor:help;'><i class='fa fa-info-circle clear ml-2 mr-1'></i>" . _LANG('p_duzeltilmesi_oneriliyor') . "</span>";} ?></td>
						</tr>
						<tr <?php $datetimezone=ini_get("date.timezone"); if ($datetimezone!="Europe/Istanbul") {echo "class='warning'";} ?>>
							<th width="5">date.timezone</th>
							<td><?php echo $datetimezone; ?></td>
							<td>Europe/Istanbul</td>
							<td><?php echo _LANG('p_uyumluluk_sunucu_saat'); ?> <span class="text-muted"><?php echo tarih_saat(); ?></span></td>
						</tr>
					</tbody>
				</table>
			</div>
			
			
			</div>
			<div class="clearfix mb20"></div>
		</div>
	</div>
</div>
<?php include("inc/alt.php"); ?>
<?php include("inc/footer-kod.php"); ?>
</body>
</html>