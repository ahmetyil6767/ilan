<?php 
if(count(get_included_files())==1) {die();}
if (!$oturum_id) {
	$_SESSION["mesaj"]=array(_LANG('pn_bu_sayfayi_goruntuleyebilmek_icin_uye_girisi_yapmalisiniz'),".");
	yonlendir(ozel_url("giris-yap"));
	exit();
}

@$buton=mysqli_real_escape_string($baglan,$_POST["buton"]);
if ($buton) {
	$isaret=$_POST["isaret"];
	if ($buton=="okundu") {
		foreach($isaret as $yaz => $x) {
			mysqli_query($baglan,"update bildirimler set okundu='1' where id='$x'");
		}
	}
	if ($buton=="sil") {
		foreach($isaret as $yaz => $x) {
			mysqli_query($baglan,"delete from bildirimler where id='$x'");
		}
	}
	yonlendir(browser_url());
}


$sql=mysqli_query($baglan,"select * from bildirimler where uye_id='$oturum_id' and tip='1' order by tarih_saat desc");
$bildirim_sayisi=mysqli_num_rows($sql);
tema_header(); ?>
<div class="container profil gorusme mt20">
	<?php include("temalar/$aktif_tema/inc/uye-ust.php"); ?>
	
	<div class="row">
		<div class="col-md-9 sidebar">
			<?php include("temalar/$aktif_tema/inc/uye-sol-menu.php"); ?>
		</div>
		<div class="col-md-33">
			<?php include("temalar/$aktif_tema/inc/alert.php"); ?>
			<?php if ($bildirim_sayisi==0) {echo "<div class='alert alert-danger'>Bildirim bulunamadı.</div>";} else { ?>
			<form action="" method="post">
			<div class="panel panel-default limon with_footer">
				<div class="panel-body" style="padding:0 !important;">
					<table class="table table-default table-hover profil padding" style="margin-bottom:0;">
						<thead>
							<tr>
								<th width="1"></th>
								<th>Bildirim</th>
								<th class="visible-sm visible-md visible-lg">Tarih Saat</th>
							</tr>
						</thead>
						<tbody>
							<?php 
							while ($dizi=mysqli_fetch_array($sql)) {
							$bildirim_id=$dizi["id"];
							$bildirim=$dizi["bildirim"];
							$okundu=$dizi["okundu"];
							$bildirim_ts=tarih_saat_bicimlendir($dizi["tarih_saat"]);
							if ($okundu==0) {$class_yaz="class='warning'";} else {$class_yaz=null;}
							?>
							<tr <?php echo $class_yaz; ?>>
								<td>
								<div class="checkbox" style="margin:0;position:relative;top:3px;line-height:auto !important;">
									<label>
									  <input class="isaret" name="isaret[]" value="<?php echo $bildirim_id; ?>" type="checkbox">
									</label>
								  </div>
								</td>								  
								<td>
								<?php echo $bildirim; ?>
								<div class="tarih_saat visible-xs" style="margin-top:3px;font-size:12px;color:#777;"><?php echo $bildirim_ts; ?></div>
								</td>
								<td class="visible-sm visible-md visible-lg"><?php echo $bildirim_ts; ?></td>
							</tr>
							<?php } ?>
						</tbody>
					</table>
				
				</div>
				<div class="panel-footer text-right noselect">
					<a id="tumunu_sec" class="pull-left c_pointer"><?php echo _LANG("p_tumunu_sec"); ?></a>
					<a id="secimleri_kaldir" class="pull-left c_pointer"><?php echo _LANG("p_secimleri_kaldir"); ?></a>
					<button type="submit" name="buton" id="buton" class="btn btn-primary btn-sm" value="okundu"><?php echo _LANG("msg_mark_read"); ?></button>
					<button type="submit" name="buton" id="buton" class="btn btn-danger btn-sm" value="sil">Sil</button>
				<div class="clearfix"></div>
				</div>
			</div>			
			</form>
			<?php } ?>
			
			
		</div>
	</div>
</div>

<?php tema_footer(); ?>

<script type="text/javascript">
$(function(){
	
$("#secimleri_kaldir").hide();
$("#buton").attr("disabled", true);
$("#tumunu_sec").click(function(){
	$(".isaret").prop("checked",true);
	$("#tumunu_sec").hide();
	$("#secimleri_kaldir").show();
});

$("#secimleri_kaldir").click(function(){
	$(".isaret").prop("checked",false);
	$("#secimleri_kaldir").hide();
	$("#tumunu_sec").show();
});		

$("input:checkbox").change(function() {
	var secilen = $('input:checkbox:checked').length;
	if (secilen>0) {
		$("button#buton").attr("disabled", false);
	} else {
		$("button#buton").attr("disabled", true);
	}
});

$("a.c_pointer").click(function() {
	var secilen = $('input:checkbox:checked').length;
	if (secilen>0) {
		$("button#buton").attr("disabled", false);
	} else {
		$("button#buton").attr("disabled", true);
	}
});	

});					
</script>