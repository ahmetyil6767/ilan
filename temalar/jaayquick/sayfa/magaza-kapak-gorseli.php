<?php 
if(count(get_included_files())==1) {die();}
if (!$oturum_id) {
	$_SESSION["mesaj"]=array(_LANG('pn_bu_sayfayi_goruntuleyebilmek_icin_uye_girisi_yapmalisiniz'),".");
	yonlendir(ozel_url("giris-yap"));
	exit();
}
@$id=mysqli_real_escape_string($baglan,$_GET["id"]);
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
@$buton=$_POST["buton"];
if ($buton) {
	$mevcut_gorsel=magaza_bilgi("kapak_gorseli",$id);
	$file=$_FILES["gorsel"];
	$data = getimagesize($file["tmp_name"]);
	$width = $data[0];
	$height = $data[1];
	if ($width>=1140 and $height>=200) {
		$yuklenen_resim=resim_yukle($file,"magaza_kapak",array("1140_200"),"1140_200",1);
		if ($yuklenen_resim) {
			// Mevcut resmi ve boyutlarını silelim.
			sil($mevcut_gorsel);
			// Mevcut resmi ve boyutlarını sildik.
			magaza_bilgi_guncelle("kapak_gorseli",$yuklenen_resim,$id);
			$_SESSION["mesaj"]=array(_LANG('pn_kapak_gorseliniz_basariyla_guncellendi'),".");
		}
	} else {
		$_SESSION["mesaj"]=array(_LANG('pn_kucuk_bir_gorsel_sectiniz_lutfen_en_az_1140px_genisliginde_ve_200px_yu'),".");
	}
	yonlendir(ozel_url("magaza-kapak-gorseli")."?id=$id");
}


tema_header(); ?>
<div class="container profil arsiv mt20">
	<?php include("temalar/$aktif_tema/inc/magaza-ust.php"); ?>
	
	<div class="row">
		<div class="col-md-9 sidebar">
			
			<?php include("temalar/$aktif_tema/inc/magaza-sol-menu.php"); ?>
			
		</div>
		<div class="col-md-33">
			<?php include("temalar/$aktif_tema/inc/alert.php"); ?>
			<div class="panel panel-default jaayquick with_footer">
				<div><?php echo _LANG('hb_magaza_kapak_gorseli_yukle'); ?></div>
				<form action="" method="post" enctype="multipart/form-data">
				<div class="panel-body">
					<p><?php echo _LANG('hb_yukleyeceginiz_kapak_gorseli_1140px_genisligin'); ?></p>
					<div class="form-group">
						<label for="gorsel"><?php echo _LANG('s_magaza_kapak_gorsel'); ?> <span style="color:#e74c3c;margin-left:3px;">*</span></label>
						<input type="file" id="gorsel" name="gorsel" accept="image/*"/>
					</div>
				</div>
				<div class="panel-footer">
					<button type="submit" class="btn btn-primary" name="buton" id="buton" value="Yükle"><i class="fa fa-cloud-upload"></i><?php echo _LANG("post_upload"); ?></button>
				</div>
				</form>
			</div>
		</div>
	</div>
</div>

<?php tema_footer(); ?>
<link href="<?php echo site_url(); ?>/<?php echo $admin_dizini; ?>/js/fileuploader/jquery.fileuploader.css" media="all" rel="stylesheet">
<script type="text/javascript" src="<?php echo site_url(); ?>/<?php echo $admin_dizini; ?>/js/fileuploader/jquery.fileuploader.min.js"></script>
<script type="text/javascript">
$(function(){	
	$("button#buton").attr("disabled","disabled");
	$("#gorsel").change(function(){
		$("button#buton").removeAttr("disabled","disabled");
	});
	
	$("button#buton").click(function(){
		$(this).addClass('disabled');
	});

	$('input#gorsel').fileuploader({
        changeInput: '<div class="fileuploader-input">' +
					      '<div class="fileuploader-input-inner">' +
						      '<div><i class="ion-images"></i></div>' +
							  '<h3><span><?php echo _LANG("s_yeni_kapak_surukle"); ?></span></h3>' +
							  '<p>ya da</p>' +
							  '<div class="fileuploader-input-button"><span><?php echo _LANG("post_add_photo"); ?></span></div>' +
						  '</div>' +
					  '</div>',
        theme: 'dragdrop',
		limit: 1,
		maxSize: 2,
		captions: {
            feedback: 'İyi görünüyor!',
            feedback2: 'İyi görünüyor!',
            drop: 'İyi görünüyor!'
        },
	});
});
</script>