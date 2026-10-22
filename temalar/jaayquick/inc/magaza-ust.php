<?php 
if(count(get_included_files())==1) {die();}
$baslik=getir("magazalar","baslik",$id);
$magaza_sahibi=getir("magazalar","uye_id",$id);
$magaza_durum=getir("magazalar","durum",$id);
$guvenilir=uye_bilgi("guvenilir",$id);
$profil_gorseli=magaza_bilgi("profil_gorseli",$id);

$profil_gorseli_boyut=thumbnail_url("150_150",$profil_gorseli);
if (!$profil_gorseli or url_kontrol($profil_gorseli)==false) {
	$profil_gorseli_boyut=tema_dizini()."/img/yok/profil.png";
}
$kapak_gorseli=magaza_bilgi("kapak_gorseli",$id);
if (!$kapak_gorseli or url_kontrol($kapak_gorseli)==false) {
	$kapak_gorseli=tema_dizini()."/img/yok/kapak.png";
}
$magaza_tarih_saat=getir("magazalar","tarih_saat",$id);
$magaza_tarih_saat_parcala=explode(" ",$magaza_tarih_saat);
$magaza_tarihi=tarih_bicimlendir($magaza_tarih_saat_parcala[0]);
$uyelik_tip=uye_bilgi("tip",$id);
$profil_url=profil_link_getir($id);
?>

<?php if ($magaza_durum==0 and $oturum_id==$magaza_sahibi) { ?>
	<div class="alert alert-info"><i class="ion-information-circled"></i>Bu mağaza henüz onaylanmadı ve sadece mağaza sahibi tarafından erişilebilir.</div>
<?php } if ($magaza_durum==2 and $oturum_id==$magaza_sahibi) { ?>
	<div class="alert alert-danger"><i class="ion-information-circled"></i>Bu mağazanın geçerlilik süresi doldu ve sadece mağaza sahibi tarafından erişilebilir. Mağaza üyeliğinizi yenilemek için <a href="<?php echo ozel_url("magaza-plan-detaylari")."?id=$id"; ?>" style="color:#fff;">buraya</a> tıklayın.</div>
<?php } ?>

<div class="kapak">
	<div id="kapak_gorsel" class="visible-sm visible-md visible-lg">
		<img src="<?php echo $kapak_gorseli; ?>" alt="<?php echo $baslik; ?>" class="img-responsive noselect" />
		<?php if ($oturum_id==$magaza_sahibi) { ?><div id="kapak_yukle" style="display:none;"><a href="<?php echo ozel_url("magaza-kapak-gorseli"); ?>?id=<?php echo $id; ?>" class="btn btn-default btn-xs"><i class="ion-image"></i><?php echo _LANG("title_upload_cover"); ?></a></div><?php } ?>
	</div>
	<div id="profil_gorsel" class="resim visible-sm visible-md visible-lg">
		<img src="<?php echo $profil_gorseli_boyut; ?>" alt="<?php echo $baslik; ?>" class="img-responsive noselect"/>
		<?php if ($oturum_id==$magaza_sahibi) { ?><div id="profil_yukle" style="display:none;"><a href="<?php echo ozel_url("magaza-profil-gorseli"); ?>?id=<?php echo $id; ?>" class="btn btn-default btn-block btn-xs"><i class="ion-image"></i><?php echo _LANG("pn_gorsel_yukle"); ?></a></div><?php } ?>
	</div>
	<div class="alt gorsel_var">
		<img src="<?php echo $profil_gorseli_boyut; ?>" alt="<?php echo $baslik; ?>" class="img-responsive noselect visible-xs mobil_uye_profil"/>
		<div class="pull-left">
			<h1><?php echo $baslik; ?></h1>
			<div class="clearfix"></div>
			<p><?php echo _LANG('ms_kayit_tarihi'); ?> <?php echo $magaza_tarihi; ?></p>
		</div>
		<div class="pull-right">
			<?php if ($magaza_sahibi==$oturum_id) {
			if ($page!="magaza-kapak-gorseli" and $page!="magaza-profil-gorseli" and $page!="magaza-bilgileri" and $page!="danismanlar" and $page!="magaza-plan-detaylari") { 
			?>
			<a href="<?php echo ozel_url("magaza-bilgileri"); ?>?id=<?php echo $id; ?>" class="btn btn-default btn-sm"><i class="ion-settings"></i><?php echo _LANG("store_settings"); ?></a>
			<?php } else { ?>
			<a href="<?php echo magaza_link_getir($id); ?>" class="btn btn-default btn-sm"><i class="ion-bag"></i><?php echo _LANG("store_view"); ?></a>
			<?php } ?>
			<a href="<?php echo ozel_url("ilan-ekle"); ?>?magaza=<?php echo $id; ?>" class="btn btn-primary btn-sm"><i class="ion-plus"></i><?php echo _LANG("store_add_ad"); ?></a>
			<?php } ?>			
		</div>
	</div>
</div>
<div class="clearfix mb20 visible-sm visible-md visible-lg"></div>