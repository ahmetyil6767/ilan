<?php 
if(count(get_included_files())==1) {die();}
if ($oturum_id==$id) { ?>
<div class="panel panel-default limon">
	<div class="panel-heading"><?php echo _LANG('pp_baslik'); ?></div>
	<div class="panel-body" style="padding-bottom:0 !important;">
	<div class="alert alert-info" style="margin:0 0 5px 0;"><?php echo _LANG('pp_gizli_not'); ?></div>
	<table class="table table-default profil" style="margin-bottom:0;">
		<tr>
			<th width="150"><?php echo _LANG('pp_ad_soyad'); ?></th>
			<td><?php echo getir("uyeler","ad_soyad",$oturum_id); ?></td>
		</tr>
		<tr>
			<th><?php echo _LANG('account_email'); ?></th>
			<td><?php echo getir("uyeler","email_adresi",$oturum_id); ?> <?php 
			$email_dogrulama=tema_ayar("email_dogrulama");
			if ($email_dogrulama==1) {
				if (getir("uyeler","durum",$oturum_id)==0) {
					echo "<span class='label label-danger' style='margin-left:10px;'><?php echo _LANG('pp_dogrulanmadi'); ?></span>";
				} else {
					echo "<span class='label label-success' style='margin-left:10px;'><?php echo _LANG('pp_dogrulandi'); ?></span>";
				}
			}
			?></td>
		</tr>
		<tr>
			<th><?php echo _LANG("s_uyelik_tipi"); ?></th>
			<td><?php echo uye_bilgi("tip",$oturum_id); ?></td>
		</tr>
		<tr>
			<th>Telefon</th>
			<td><?php echo uye_bilgi("cep_tel",$oturum_id); ?></td>
		</tr>		
		<tr>
			<th><?php echo _LANG('pp_tc_kimlik_no'); ?></th>
			<td><?php echo uye_bilgi("tc_no",$oturum_id); ?></td>
		</tr>			
		<?php if (uye_bilgi("tip",$oturum_id)=="Kurumsal") {
		$sirket=uye_bilgi("sirket",$oturum_id);
		?>

		<?php if ($sirket!=1) { ?>
		<tr>
			<th><?php echo _LANG('pp_vergi_no'); ?></th>
			<td><?php echo uye_bilgi("vergi_no",$oturum_id); ?></td>
		</tr>						
		<tr>
			<th><?php echo _LANG('pp_vergi_dairesi'); ?></th>
			<td><?php echo uye_bilgi("vergi_dairesi",$oturum_id); ?></td>
		</tr>							
		<?php } ?>
		<tr>
			<th>Adres</th>
			<td><?php echo uye_bilgi("adres",$oturum_id); ?></td>
		</tr>					
		<?php } ?>
		
	</table>
	</div>
</div>			
<?php } ?>