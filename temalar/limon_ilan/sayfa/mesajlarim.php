<?php 
if(count(get_included_files())==1) {die();}
if (!$oturum_id) {
	$_SESSION["mesaj"]=array(_LANG('pn_bu_sayfayi_goruntuleyebilmek_icin_uye_girisi_yapmalisiniz'),".");
	yonlendir(ozel_url("giris-yap"));
	exit();
}
@$gorusme_id=$_GET["g"];
if ($gorusme_id) {
	$uye_1=getir("gorusmeler","uye_1",$gorusme_id);
	$uye_2=getir("gorusmeler","uye_2",$gorusme_id);
	
	$sil_1=getir("gorusmeler","sil_1",$gorusme_id);
	$sil_2=getir("gorusmeler","sil_2",$gorusme_id);
	
	$ilan_id=getir("gorusmeler","ilan_id",$gorusme_id);
		
	if ($oturum_id!=$uye_1 and $oturum_id!=$uye_2) {
		$_SESSION["mesaj"]=array(_LANG('pn_bu_gorusmeyi_goruntuleyemezsiniz'),".");
		yonlendir(ozel_url("mesajlarim"));
	}
	$diger_uye_id=diger_uye_id($uye_1,$uye_2);
	$diger_uye_ad_soyad=getir("uyeler","ad_soyad",$diger_uye_id);
	$diger_uye_uyelik=tarih_saat_bicimlendir(getir("uyeler","tarih_saat",$diger_uye_id));
	$diger_uye_resim=uye_bilgi("profil_gorseli",$diger_uye_id);
	$diger_uye_resim_boyut=thumbnail_url("150_150",$diger_uye_resim);
	if (!$diger_uye_resim) {
		$diger_uye_resim_boyut=tema_dizini()."/img/yok/profil.png";
	}
	
	$tarih_saat=getir("gorusmeler","tarih_saat",$gorusme_id);
	$mesaj_sayisi=mesaj_sayisi($gorusme_id);
	$okundu_yap=mysqli_query($baglan,"update mesajlar set okundu='1' where kime='$oturum_id' and gorusme_id='$gorusme_id'");
	$sql=mysqli_query($baglan,"select * from mesajlar where gorusme_id='$gorusme_id' order by tarih_saat asc limit 0, 50");
}
tema_header(); ?>
<div class="container profil gorusme mt20">
	<?php include("temalar/$aktif_tema/inc/uye-ust.php"); ?>
	
	<div class="row">
		<div class="col-md-9 sidebar">
		<?php if ($gorusme_id) {	?>
		<div class="panel panel-default limon detay">
			<div><?php echo _LANG('hb_gorusme_detaylari'); ?></div>
			<div class="panel-body text-center">
				<img src="<?php echo $diger_uye_resim_boyut; ?>" class="img-rounded" style="width:100px;height:auto;"/>
				<h3><?php echo $diger_uye_ad_soyad; ?></h3>
				<span class="uyelik_tarihi">Üyelik: <?php echo $diger_uye_uyelik; ?></span>
				<div style="margin-top:4px;"><span class="uyelik_tarihi"><a href="<?php echo link_getir($ilan_id); ?>" target="_blank"><?php echo getir("icerik","baslik",$ilan_id); ?></a></span></div>
				<button id="gorusme_sil_detay" type="button" class="btn btn-danger btn-xs" value="<?php echo $gorusme_id; ?>" style="margin-top:10px;"><i class="fa fa-trash-o"></i><?php echo _LANG("s_gorusmeyi_sonlandir"); ?></button>
			</div>
		</div>		
		<div class="panel panel-default limon">
			<div><?php echo _LANG('hb_geri_don'); ?></div>
			<div class="panel-body" style="padding:0;">
				<ul>
					<li class="active"><a href="<?php echo ozel_url("mesajlarim"); ?>?g=<?php echo $gorusme_id; ?>"><?php echo _LANG("msg_send"); ?></a></li>
					<li><a href="<?php echo ozel_url("mesajlarim"); ?>"><?php echo _LANG("s_tum_mesajlarim"); ?></a></li>
				</ul>
			</div>
		</div>
		<?php } else {include("temalar/$aktif_tema/inc/uye-sol-menu.php");} ?>

		</div>
		<div class="col-md-33">
			<?php include("temalar/$aktif_tema/inc/alert.php"); ?>
			<?php if (!$gorusme_id) { ?>
			<?php 
			$sql=mysqli_query($baglan,"select * from gorusmeler where (uye_1='$oturum_id' and sil_1='0') or (uye_2='$oturum_id' and sil_2='0') order by tarih_saat desc");
			$say=mysqli_num_rows($sql);
			if ($say>0) { ?>
			
			<div class="panel panel-default limon">
				<div class="panel-body" style="padding:0 !important;border-radius:2px;">
				
				<table class="table table-default table-hover profil" style="margin-bottom:0;">
					<thead>
						<tr>
							<th colspan="2" style="padding-left:10px;"><?php echo _LANG("s_gorusmelerim"); ?></th>
							<th><?php echo _LANG("listing_title"); ?></th>
							<th>Son Mesaj</th>
							<th width="55"></th>
						</tr>
					</thead>
					<tbody>
						<?php 
						while ($gorusme_dizi=mysqli_fetch_array($sql)) {
							$id=$gorusme_dizi["id"];
							$ilan_id=$gorusme_dizi["ilan_id"];
							$okunmamis_mesaj_sayisi=mysqli_num_rows(mysqli_query($baglan,"select id from mesajlar where kime='$oturum_id' and okundu='0' and gorusme_id='$id'"));
							$uye_1=$gorusme_dizi["uye_1"];
							$uye_2=$gorusme_dizi["uye_2"];

							$sil_1=$gorusme_dizi["sil_1"];
							$sil_2=$gorusme_dizi["sil_2"];	
							
							$diger_uye_id=diger_uye_id($uye_1,$uye_2);
							$diger_uye_resim=uye_bilgi("profil_gorseli",$diger_uye_id);
							$diger_uye_resim_boyut=thumbnail_url("150_150",$diger_uye_resim);
							if (!$diger_uye_resim) {
								$diger_uye_resim_boyut=tema_dizini()."/img/yok/profil.png";
							}
							$tarih_saat=$gorusme_dizi["tarih_saat"];
							if ($okunmamis_mesaj_sayisi>0) {$class_yaz="class='warning'";} else {$class_yaz=null;}
						?>
						<tr id="gorusme-<?php echo $id; ?>" <?php echo $class_yaz; ?>>
							<td width="40" style="padding-left:10px;" class="satir_link c_pointer" data-href="<?php echo ozel_url('mesajlarim').'?g='.$id; ?>"><img src="<?php echo $diger_uye_resim_boyut; ?>" alt="" class="img-responsive img-circle" /></td>
							<td class="satir_link c_pointer" data-href="<?php echo ozel_url('mesajlarim').'?g='.$id; ?>">
								<div class="pull-left fw600"><?php echo getir("uyeler","ad_soyad",$diger_uye_id); ?></div>
								<?php if ($okunmamis_mesaj_sayisi>0) {echo "<br /><small style='line-height:10px !important;color:#999;'>($okunmamis_mesaj_sayisi yeni mesaj)</small>";} ?>
							</td>
							<td class="satir_link c_pointer" data-href="<?php echo ozel_url('mesajlarim').'?g='.$id; ?>"><?php echo getir("icerik","baslik",$ilan_id); ?></td>			
							<td class="satir_link c_pointer" data-href="<?php echo ozel_url('mesajlarim').'?g='.$id; ?>"><?php echo tarih_saat_bicimlendir($tarih_saat); ?></td>
							<td>
							<button id="gorusme_sil" type="button" class="btn btn-danger btn-xs" value="<?php echo $id; ?>"><i class="fa fa-trash-o"></i>Sil</button>
							</td>
						</tr>
						<?php } ?>
					</tbody>
				</table>
				</div>
			</div>
			<?php } else {
				echo "<div class='alert alert-danger'>Mesaj bulunamadı.</div>";
			} ?>
			<?php } else { ?>
			<div class="panel panel-default limon with_footer">
				<div><?php echo _LANG('hb_mesajlarim'); ?></div>
				<div class="panel-body" style="padding:0 !important;">
					<div id="mesajlasma" class="scroll"></div>
				</div>
				<div class="panel-footer">
					<?php 
							if (($oturum_id==$uye_1 and $sil_2==1) or ($oturum_id==$uye_2 and $sil_1==1)) {
								echo "<span class='text-danger'>$diger_uye_ad_soyad adlı üye görüşmeyi sonlandırdığı için bu görüşmeyi yanıtlayamazsınız.</span>";
							}	else {	
					?>
					<form id="mesaj_gonder_form" action="" method="post" onsubmit="return false;">
						<input type="hidden" class="form-control" id="mesaj_sayisi" value="<?php echo mesaj_sayisi($gorusme_id); ?>" />
						<input type="hidden" class="form-control" name="kime" value="<?php echo $diger_uye_id; ?>" />
						<div class="input-group ara">
						  <input type="text" class="form-control" name="mesaj" id="mesaj_input" autocomplete="off" />
						  <span class="input-group-btn">
							<button type="submit" id="m_gonder_buton" class="btn btn-primary" value="Gönder"><i class="ion-paper-airplane"></i><?php echo _LANG("post_submit"); ?></button>
						  </span>
						</div>
					</form>
					<?php } ?>
				</div>
			</div>			
			<?php } ?>
		</div>
	</div>
</div>

<?php tema_footer(); ?>
<script type="text/javascript">
$(function(){
	$("#mesajlasma").load("<?php echo tema_dizini(); ?>/ajax.php?islem=mesajlari_getir&g=<?php echo $gorusme_id; ?>",function(){
		$("#mesajlasma").scrollTop(400000000000000);
	});
	setInterval(function(){
		$("#mesajlasma").load("<?php echo tema_dizini(); ?>/ajax.php?islem=mesajlari_getir&g=<?php echo $gorusme_id; ?>");
		var mesaj_sayisi=$("#mesaj_sayisi").val();
		
		$.ajax({
			type:"post",
			url:"<?php echo tema_dizini(); ?>/ajax.php?islem=mesaj_sayisi&gorusme_id=<?php echo $gorusme_id; ?>",
			success:function(cevap){
				var yeni_mesaj_sayisi=cevap;
				if (yeni_mesaj_sayisi>mesaj_sayisi) {
					$("#mesajlasma").scrollTop(400000000000000);
					$("#mesaj_sayisi").val(yeni_mesaj_sayisi);
				}
			}
		});
		


	}, 1500);
	$("body").on("submit","#mesaj_gonder_form",function(){
		$.ajax({
			type:"post",
			url:"<?php echo tema_dizini(); ?>/ajax.php?islem=mesaj_gonder&ilan_id=<?php echo $ilan_id; ?>",
			data:$("#mesaj_gonder_form").serialize(),
			success:function(cevap){
				$("#mesaj_input").val('');
				$("#mesajlasma").load("<?php echo tema_dizini(); ?>/ajax.php?islem=mesajlari_getir&g=<?php echo $gorusme_id; ?>");
				$("#mesajlasma").scrollTop(400000000000000);
			}
		});
	});
	
	$("body").on("click","#gorusme_sil",function(){
		var onay=confirm("<?php echo _LANG('tx_gorusmeyi_sonlandirmak_istediginizden_emin_mis'); ?>");
		if (onay==true) {
			var gorusme_id=$(this).val();
			$.ajax({
				type:"post",
				url:"<?php echo tema_dizini(); ?>/ajax.php?islem=gorusme_sil",
				data:{'gorusme_id':gorusme_id},
				success:function(cevap){
					$("#gorusme-"+gorusme_id).fadeOut(500);
				}
			});
		}
	});
	$("body").on("click","#gorusme_sil_detay",function(){
		var onay=confirm("<?php echo _LANG('tx_gorusmeyi_sonlandirmak_istediginizden_emin_mis'); ?>");
		if (onay==true) {
			var gorusme_id=$(this).val();
			$.ajax({
				type:"post",
				url:"<?php echo tema_dizini(); ?>/ajax.php?islem=gorusme_sil",
				data:{'gorusme_id':gorusme_id,'tip':2},
				success:function(cevap){
					window.location = "<?php echo ozel_url('mesajlarim'); ?>";
				}
			});
		}
	});
	
})
</script>