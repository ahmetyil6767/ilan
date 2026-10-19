<?php if(count(get_included_files())==1) {die();} ?>
<div class="btn-group btn-group-justified mb15" role="group" aria-label="...">
	<a href="<?php echo ozel_url("ilan-ekle"); ?>?ilan=<?php echo $id; ?>" class="btn btn-primary" title="İlanı güncelle"><i class="ion-refresh mr0"></i><br /><?php echo _LANG("general_update"); ?></a>
	<a href="<?php echo ozel_url("doping"); ?>?ilan_id=<?php echo $id; ?>" class="btn btn-default" title="<?php echo _LANG('tx_doping_satin_al'); ?>"><i class="fa fa-rocket mr0"></i><br />Doping</a>
	<?php if ($durum==1) { ?>
		<div class="btn-group" role="group">
		<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="caret ml0"></span><br /><?php echo _LANG("pn_diger"); ?></button>
		<ul class="dropdown-menu dropdown-menu-right">
		<li><a href="<?php echo ozel_url("ilanlarim")."?pasif=$id"; ?>" onclick="return confirm('İlanı yayından kaldırmak istiyor musunuz?')"><?php echo _LANG("post_unpublish"); ?></a></li>
		</ul>
		</div>
	<?php } ?>
</div>
<div class="panel panel-default limon" style="margin-bottom:10px;">
	<div><?php echo _LANG('hb_ilan_istatistikleri'); ?></div>
	<div class="panel-body" style="padding:0;">
		<?php 
		$toplam=postviews();
		$bugun=postviews($id,"gun");
		$dun_date=date("d-m-Y",strtotime("-1 day"));
		$dun=postviews($id,"gun",$dun_date);
		$favori=kac_kisinin_favorisi($id);
		?>
		<table class="table table-default profil padding" style="margin-bottom:0;">
		<tr>
		<td>Toplam ziyaret</td>
		<td><?php echo $toplam; ?></td>
		</tr>
		<tr>
		<td><?php echo _LANG('hb_bugun_ziyaret'); ?></td>
		<td><?php echo $bugun; ?><?php if ($dun) { $fark=$bugun-$dun; echo "<small style='float:right;color:#999;'>(düne göre: $fark)</small>";} ?></td>
		</tr>
		<?php if ($dun>0) { ?>
		<tr>
		<td><?php echo _LANG('hb_dun_ziyaret'); ?></td>
		<td><?php echo $dun; ?></td>
		</tr>
		<?php } ?>
		<tr>
		<td><?php echo _LANG('r_favori_sayisi'); ?></td>
		<td><?php echo $favori; ?></td>
		</tr>
		</table>
	</div>
</div>