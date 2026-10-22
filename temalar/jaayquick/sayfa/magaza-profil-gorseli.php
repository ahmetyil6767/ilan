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
	$mevcut_gorsel=magaza_bilgi("profil_gorseli",$id);
	$file=$_FILES["gorsel"];
	
	$data = getimagesize($file["tmp_name"]);
	$width = $data[0];
	$height = $data[1];

	if ($width<150 or $height<150) {
		$_SESSION["mesaj"]=array(_LANG('pn_lutfen_en_az_150px_genisliginde_ve_150px_yuksekliginde_bir_gorsel_seci'),".");
		yonlendir(ozel_url("profil-gorseli"));		
	} else {
		$yuklenen_resim=resim_yukle($file,"magaza_profil",array("50_50","150_150"));
		if ($yuklenen_resim) {
			// Mevcut resmi ve boyutlarını silelim.
			sil(thumbnail_url("50_50",$mevcut_gorsel));
			sil(thumbnail_url("150_150",$mevcut_gorsel));
			sil($mevcut_gorsel);
			
			// Mevcut resmi ve boyutlarını sildik.
			magaza_bilgi_guncelle("profil_gorseli",$yuklenen_resim,$id);

			// 22.04.2018 Orijinal resim optimize başladı
			if ($width>=300 and $height>=300) {
				$orijinal_gorsel=$yuklenen_resim;
				$uzanti=uzanti_ne($orijinal_gorsel);
				$bol=explode($uzanti,$orijinal_gorsel);
				$resim_adi=rtrim($bol[0],".");
				$yeni_resim="$resim_adi.$uzanti";
				$parcala=explode("uploads/",$yeni_resim);
				$yol=$parcala[1];
				include_once("temalar/$aktif_tema/class/SimpleImage.php");
				error_reporting(E_ALL & ~E_NOTICE);
				if ($uzanti=="jpg" or $uzanti=="jpeg") {
					$mime_type="jpeg";
				} else if ($uzanti=="png") {
					$mime_type="png";
				} else if ($uzanti=="gif") {
					$mime_type="gif";
				}
				try {
				  $image = new \claviska\SimpleImage();
				  $image->fromFile($orijinal_gorsel)->thumbnail(300,300)->toFile("uploads/$yol", "image/$mime_type",90);
				} catch(Exception $err) {
				  // Handle errors
				  // echo $err->getMessage();
				}						
			}
			// 22.04.2018 Orijinal resim optimize bitti			
			
			$_SESSION["mesaj"]=array(_LANG('pn_profil_gorseliniz_basariyla_guncellendi'),".");
			yonlendir(ozel_url("magaza-profil-gorseli")."?id=$id");
		}
	}
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
				<div><?php echo _LANG('hb_magaza_profil_gorseli_yukle'); ?></div>
				<form action="" method="post" enctype="multipart/form-data">
				<div class="panel-body">
					<p><?php echo _LANG('hb_yukleyeceginiz_profil_gorseli_300px_genisligin'); ?></p>
					<div class="form-group">
						<label for="gorsel">Mağaza Profil Görseli <span style="color:#e74c3c;margin-left:3px;">*</span></label>
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
							  '<h3><span><?php echo _LANG("s_yeni_profil_surukle"); ?></span></h3>' +
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