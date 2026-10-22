<?php 
if(count(get_included_files())==1) {die();}
if (!$oturum_id) {
	$_SESSION["mesaj"]=array(_LANG('pn_bu_sayfayi_goruntuleyebilmek_icin_uye_girisi_yapmalisiniz'),".");
	yonlendir(ozel_url("giris-yap"));
	exit();
}
@$id=mysqli_real_escape_string($baglan,$_GET["id"]);
@$islem=mysqli_real_escape_string($baglan,$_GET["islem"]);
@$kaldir=mysqli_real_escape_string($baglan,$_GET["kaldir"]);
if (!$id) {
	yonlendir(site_url());
} else {
	if (!is_numeric($id)) {
		yonlendir(site_url());
	}	
	$magaza_sahibi=getir("magazalar","uye_id",$id);
	if ($magaza_sahibi!=$oturum_id) {
		$_SESSION["mesaj"]=array(_LANG('pn_yetkisiz_islem'),".");
		yonlendir(ozel_url("magazalarim"));
	}
}
if ($islem=="ekle") {
	$buton=mysqli_real_escape_string($baglan,$_POST["buton"]);
	if ($buton) {
		$email_adresi=mysqli_real_escape_string($baglan,$_POST["email_adresi"]);
		$parola=mysqli_real_escape_string($baglan,$_POST["parola"]);
		
		$uye_parola=getir("uyeler","parola",$oturum_id);
		$uye_email_adresi=getir("uyeler","email_adresi",$oturum_id);
		if (parola_dogrula($oturum_id, $parola)) {
			$email_kontrol=uye_mevcut_mu($email_adresi);
			if ($email_kontrol and $email_adresi!=$uye_email_adresi) {
				$temsilci_uye_id=uye_id_getir($email_adresi);
				$temsilci_ad_soyad=getir("uyeler","ad_soyad",$temsilci_uye_id);
				$magaza_uyeleri=magaza_uyeleri($id);
				if (in_array($magaza_uyeleri,$temsilci_uye_id)) {
					$_SESSION["mesaj"]=array(_LANG('pn_bu_kisi_daha_onceden_temsilci_olarak_eklenmis'),".");
				} else {
					magaza_uye_ekle($temsilci_uye_id,$id);
					$_SESSION["mesaj"]=array(sprintf(_LANG('msj_temsilci_eklendi'), $temsilci_ad_soyad),"success");
					yonlendir(ozel_url("danismanlar")."?id=$id");
				}
			} else if ($email_adresi==$uye_email_adresi) {
				$_SESSION["mesaj"]=array(_LANG('pn_magaza_sahipleri_zaten_o_magazadaki_en_yetkili_temsilcilerdir'),".");
			} else {
				$_SESSION["mesaj"]=array(_LANG('pn_bu_e_posta_adresi_ile_kayitli_bir_uye_bulunamadi'),"!");
			}
		} else {
			$_SESSION["mesaj"]=array(_LANG('pn_hatali_parola'),"a");
		}
		
		
	}
}

if ($kaldir) {
	magaza_uye_sil($kaldir,$id);
	$temsilci_ad_soyad=getir("uyeler","ad_soyad",$kaldir);
	$_SESSION["mesaj"]=array(sprintf(_LANG('msj_temsilci_cikarildi'), $temsilci_ad_soyad),"success");
	yonlendir(ozel_url("danismanlar")."?id=$id");	
}

$magaza_uye_sayisi=magaza_uye_sayisi($id);
$magaza_uyeleri=magaza_uyeleri($id);
$magaza_temsilci_limiti=magaza_ilan_limiti("danisman",$id);
tema_header(); ?>
<div class="container profil arsiv mt20">
	<?php include("temalar/$aktif_tema/inc/magaza-ust.php"); ?>
	
	<div class="row">
		<div class="col-md-9 sidebar">
			
			<?php include("temalar/$aktif_tema/inc/magaza-sol-menu.php"); ?>
			
			<?php if ($magaza_uye_sayisi<$magaza_temsilci_limiti) { ?>
			<a href="<?php echo ozel_url("danismanlar"); ?>?id=<?php echo $id; ?>&islem=ekle" class="btn btn-primary btn-block mb15"><i class="ion-plus"></i><?php echo _LANG("s_danisman_ekle"); ?></a>
			<?php } ?>
		</div>
		<div class="col-md-33">
			<?php include("temalar/$aktif_tema/inc/alert.php"); ?>
			
			<?php if (!$islem) { ?>
			<?php 
			if ($magaza_uye_sayisi==0) {
				echo "<div class='alert alert-danger'><?php echo _LANG('s_danisman_yok'); ?></div>";
				
				if ($magaza_uye_sayisi-$magaza_temsilci_limiti==0) {
					echo "<div class='alert alert-info'>
					<p>Mağaza paketiniz için belirlenmiş satış danışmanı limiti: $magaza_temsilci_limiti</p>
					<p>" . _LANG('hb_satis_danismani_ekleyebilmek_icin_lutfen_magaz') . "</p>
					</div>";
				}
			
			
			} else { ?>
			
			<div class="alert alert-info">
			Bu mağazada en fazla <?php echo magaza_ilan_limiti("danisman",$id); ?> satış danışmanı yetkilendirebilirsiniz.
			</div>
			
			<div class="panel panel-default jaayquick with_footer">
				<div><?php echo _LANG('hb_satis_danismanlari'); ?></div>
					
					<div class="panel-body" style="padding:0 !important;">
					<table class="table table-default table-hover profil padding" style="margin-bottom:0;">
						<thead>
							<tr>
								<th>Ad Soyad</th>
								<th><?php echo _LANG('hb_ilan_sayisi'); ?></th>
								<th><?php echo _LANG("general_options"); ?></th>
							</tr>
						</thead>
						<tbody>				
						<?php 
						foreach ($magaza_uyeleri as $uye) {
						$ad_soyad=getir("uyeler","ad_soyad",$uye);
						$ilan_sayisi=mysqli_num_rows(mysqli_query($baglan,"select * from icerik where tip='ilan' and yazar='$uye'"));
						
						?>
						<tr>
							<td><a href="<?php echo uye_link_getir($uye); ?>"><?php echo $ad_soyad; ?></a></td>
							<td><?php echo $ilan_sayisi; ?></td>
							<td>
							
							<div class="btn-group" role="group" aria-label="...">
							  <a href="<?php echo ozel_url("danismanlar")."?id=$id&kaldir=$uye"; ?>" class="btn btn-danger btn-xs">Yetkiyi Al</a>
							</div>							
							
							</td>
						</tr>
						<?php } ?>
						
						</tbody>
					</table>

					</div>
						
				<?php }  ?>
			</div>
			<?php } else if ($islem=="ekle") { ?>
			
			<?php if ($magaza_uye_sayisi-$magaza_temsilci_limiti==0) {
				echo "<div class='alert alert-info'>
				<p>Mağaza paketiniz için belirlenmiş satış danışmanı limiti: $magaza_temsilci_limiti</p>
				<p>" . _LANG('hb_satis_danismani_ekleyebilmek_icin_lutfen_magaz') . "</p>
				</div>";
			} else { ?>
			<div class="panel panel-default jaayquick">
				<div><?php echo _LANG('hb_magaza_satis_danismani_ekle'); ?></div>
				<div class="panel-body">
					<div class="row">
						<div class="col-md-25">						

							<form action="" method="post">
							<?php form_group(array("tip"=>"text","id"=>"email_adresi","baslik"=>"E-posta Adresi","gerekli"=>1,"aciklama"=>_LANG('s_temsilci_eposta'))); ?>
							<?php form_group(array("tip"=>"password","id"=>"parola","baslik"=>"Parola","gerekli"=>1,"aciklama"=>_LANG('s_danisman_sifre'))); ?>
							<?php form_buton("buton",_LANG('tx_danisman_ekle'),"primary"); ?>
							</form>
						</div>
					</div>
				</div>
			</div>	
			<div class="alert alert-info"><i class="ion-alert-circled"></i>Satış danışmanları mağaza adına ilan ekleyebilirler ancak mağaza bilgilerini güncelleyemezler.</div>					
			<?php } } ?>
			
			
			
			
		</div>
	</div>
</div>

<?php tema_footer(); ?>
<link href="<?php echo site_url(); ?>/<?php echo $admin_dizini; ?>/js/fileuploader/jquery.fileuploader.css" media="all" rel="stylesheet">
<script type="text/javascript" src="<?php echo site_url(); ?>/<?php echo $admin_dizini; ?>/js/fileuploader/jquery.fileuploader.min.js"></script>
<script type="text/javascript">
$(function(){	
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