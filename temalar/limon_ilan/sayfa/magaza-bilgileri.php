<?php 
if(count(get_included_files())==1) {die();}
if (!$oturum_id) {
	$_SESSION["mesaj"]=array(_LANG('pn_bu_sayfayi_goruntuleyebilmek_icin_uye_girisi_yapmalisiniz'),".");
	yonlendir(ozel_url("giris-yap"));
	exit();
}
$id=mysqli_real_escape_string($baglan,$_GET["id"]);
if (!$id) {
	yonlendir(site_url());
} else {
	if (!is_numeric($id)) {
		yonlendir(ozel_url("magazalarim"));
	}	
	$magaza_sahibi=getir("magazalar","uye_id",$id);
	if ($magaza_sahibi!=$oturum_id) {
		$_SESSION["mesaj"]=array(_LANG('pn_yetkisiz_islem'),".");
		yonlendir(ozel_url("magazalarim"));
	}
}
@$buton=mysqli_real_escape_string($baglan,strip_tags($_POST["buton"]));
if ($buton) {
	$baslik=mysqli_real_escape_string($baglan,strip_tags($_POST["baslik"]));
	$hakkinda=mysqli_real_escape_string($baglan,strip_tags($_POST["hakkinda"]));
	$fb_url=mysqli_real_escape_string($baglan,strip_tags($_POST["fb_url"]));
	$twitter_url=mysqli_real_escape_string($baglan,strip_tags($_POST["twitter_url"]));
	$google_url=mysqli_real_escape_string($baglan,strip_tags($_POST["google_url"]));
	$instagram_url=mysqli_real_escape_string($baglan,strip_tags($_POST["instagram_url"]));
	$magaza_tel=mysqli_real_escape_string($baglan,strip_tags($_POST["magaza_tel"]));
	$guncelle=mysqli_query($baglan,"update magazalar set baslik='$baslik' where id='$id'");
	magaza_bilgi_guncelle("hakkinda",$hakkinda,$id);
	magaza_bilgi_guncelle("fb_url",$fb_url,$id);
	magaza_bilgi_guncelle("twitter_url",$twitter_url,$id);
	magaza_bilgi_guncelle("google_url",$google_url,$id);
	magaza_bilgi_guncelle("instagram_url",$instagram_url,$id);
	magaza_bilgi_guncelle("magaza_tel",$magaza_tel,$id);
	$_SESSION["mesaj"]=array(_LANG('pn_magaza_bilgileri_basariyla_guncellendi'),".");
	yonlendir(ozel_url("magaza-bilgileri")."?id=$id");
}

tema_header(); ?>
<div class="container profil arsiv mt20">
	<?php include("temalar/$aktif_tema/inc/magaza-ust.php"); ?>
	<?php include("temalar/$aktif_tema/inc/magaza-limit-bilgi.php"); ?>
	
	<div class="row">
		<div class="col-md-9 sidebar">
			<?php include("temalar/$aktif_tema/inc/magaza-sol-menu.php"); ?>
		</div>
		<div class="col-md-33">
			<?php include("temalar/$aktif_tema/inc/alert.php"); ?>
			<div class="panel panel-default limon">
	<div class="panel-heading"><?php echo _LANG("mb_panel_magaza_bilgileri"); ?></div>
				<div class="panel-body">
					<?php if ($buton) {
						yonlendir(ozel_url("magaza-bilgileri"));
					}
					?>
					<form action="" method="post">
			
						<?php 
						$baslik=getir("magazalar","baslik",$id);
						$hakkinda=magaza_bilgi("hakkinda",$id);
						$magaza_tel=magaza_bilgi("magaza_tel",$id);
						$fb_url=magaza_bilgi("fb_url",$id);
						$twitter_url=magaza_bilgi("twitter_url",$id);
						$instagram_url=magaza_bilgi("instagram_url",$id);
						
						form_group(array("tip"=>"text","id"=>"baslik","baslik"=>_LANG('p_baslik'),"deger"=>$baslik,"gerekli"=>1));
						form_group(array("tip"=>"textarea","id"=>"hakkinda","baslik"=>_LANG('s_magaza_bilgileri'),"deger"=>$hakkinda,"satir"=>3,"aciklama"=>_LANG('s_magaza_kisa_aciklama')));
						?>
						<div class="form-group">
							<label for="magaza_tel"><?php echo _LANG("s_magaza_telefon"); ?></label>
							<div class="input-group">
								<span class="input-group-addon" style="border-top-right-radius:0;border-bottom-right-radius:0;border-color:#ddd;">+220</span>
								<input type="text" class="form-control cannot_start_with_zero" id="magaza_tel" name="magaza_tel" value="<?php echo $magaza_tel; ?>" placeholder="Ev Telefonu" style="border-top-left-radius:0 !important;border-bottom-left-radius:0 !important;">
							</div>
						</div>
						<?php
						form_group(array("tip"=>"grup_ac"));
						form_group(array("tip"=>"text","id"=>"fb_url","baslik"=>"Facebook Url","deger"=>$fb_url,"grup"=>3));
						form_group(array("tip"=>"text","id"=>"twitter_url","baslik"=>"Twitter Url","deger"=>$twitter_url,"grup"=>3));
						form_group(array("tip"=>"text","id"=>"instagram_url","baslik"=>"Instagram Url","deger"=>$instagram_url,"grup"=>3));
						form_group(array("tip"=>"grup_kapat"));

						?>
			
	<?php form_buton("buton",_LANG("mb_guncelle"),"primary"); ?>
						
					</form>
					
				</div>
			</div>
			
		</div>
	</div>
</div>

<?php tema_footer(); ?>
<script type="text/javascript">
$(function(){
	$("#as_ltd").hide();
	$("#sahis").hide();
	<?php if ($sirket==1) { ?>
	$("#sahis").show();
	<?php } else { ?>
	$("#as_ltd").show();
	<?php } ?>
	$("body").on("change","#sirket",function(){
		var sirket=$(this).val();
		if (sirket==1) {
			$("#as_ltd").slideUp(300);
			$("#sahis").slideDown(300);
		} else {
			$("#sahis").slideUp(300);
			$("#as_ltd").slideDown(300);
		}
	});
});
</script>