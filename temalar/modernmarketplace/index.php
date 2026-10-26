<?php 
if(count(get_included_files())==1) {die();}
@$mesaj=$_GET["m"];
@$page=$_GET["p"];
@$magaza=$_GET["magaza"];
@$magaza_subdomain=$_GET["magaza_subdomain"];
@$email=$_GET["email"];
@$key=$_GET["key"];

if (@$email and @$key) {
	$uye_id=id_getir("uyeler","email_adresi",$email);
	$uye_ad_soyad=getir("uyeler","ad_soyad",$uye_id);
	$email_dogrulama_kodu=uye_bilgi("email_onay_kodu",$uye_id);
	if (!$oturum_id or $oturum_id==$uye_id) {
		if ($key==$email_dogrulama_kodu) {
			guncelle("uyeler","durum",1,$uye_id);
			$_SESSION["mesaj"]=array(sprintf(_LANG('msj_merhaba_dogrulandi'), $uye_ad_soyad),"success");
			yonlendir(ozel_url("giris-yap"));
		} else {
			$_SESSION["mesaj"]=array(_LANG('pn_hatali_e_posta_adresi_veya_dogrulama_kodu'),".");
			yonlendir(site_url());
		}
	} else {
		$_SESSION["mesaj"]=array(_LANG('pn_hata_gecersiz_islem'),"m");
		yonlendir(site_url());
	}
}

if (@$_POST["mk_ara"]) {
	mk_search_yonlendir();
	exit();
}

if ($page=="giris-yap") {
	include("sayfa/giris-yap.php");
	exit();
}
if ($page=="kayit-ol") {
	include("sayfa/kayit-ol.php");
	exit();
}
if ($page=="sifremi-unuttum") {
	include("sayfa/sifremi-unuttum.php");
	exit();
}
if ($page=="profil-guncelle") {
	include("sayfa/profil-guncelle.php");
	exit();
}
if ($page=="profil-bilgilerim") {
	include("sayfa/profil-bilgilerim.php");
	exit();
}
if ($page=="profil-gorseli") {
	include("sayfa/profil-gorseli.php");
	exit();
}
if ($page=="kapak-gorseli") {
	include("sayfa/kapak-gorseli.php");
	exit();
}
if ($page=="mesajlarim") {
	include("sayfa/mesajlarim.php");
	exit();
}
if ($page=="favori-ilanlarim") {
	include("sayfa/favori-ilanlarim.php");
	exit();
}
if ($page=="ilanlarim") {
	include("sayfa/ilanlarim.php");
	exit();
}
if ($page=="magazalarim") {
	include("sayfa/magazalarim.php");
	exit();
}
if ($page=="doping") {
	include("sayfa/doping.php");
	exit();
}
if ($page=="siparislerim") {
	include("sayfa/siparislerim.php");
	exit();
}
if ($page=="get-satislarim") {
	include("sayfa/get-satislarim.php");
	exit();
}
if ($page=="magaza-bilgileri") {
	include("sayfa/magaza-bilgileri.php");
	exit();
}
if ($page=="magaza-profil-gorseli") {
	include("sayfa/magaza-profil-gorseli.php");
	exit();
}
if ($page=="magaza-kapak-gorseli") {
	include("sayfa/magaza-kapak-gorseli.php");
	exit();
}
if ($page=="magaza-plan-detaylari") {
	include("sayfa/magaza-plan-detaylari.php");
	exit();
}
if ($page=="danismanlar") {
	include("sayfa/danismanlar.php");
	exit();
}
if ($page=="siparis") {
	include("sayfa/siparis.php");
	exit();
}
if ($page=="odeme") {
	include("sayfa/odeme.php");
	exit();
}
if ($page=="ilan-ekle") {
	include("sayfa/ilan-ekle.php");
	exit();
}
if ($page=="bildirimlerim") {
	include("sayfa/bildirimlerim.php");
	exit();
}
if ($page=="plan-detaylari") {
	include("sayfa/plan-detaylari.php");
	exit();
}
if ($page=="acil-acil") {
	include("sayfa/acil-acil.php");
	exit();
}
if ($page=="vitrin-ilanlari") {
	include("sayfa/vitrin-ilanlari.php");
	exit();
}
if ($page=="fiyati-dusenler") {
	include("sayfa/fiyati-dusenler.php");
	exit();
}
if ($page=="son-48-saat") {
	include("sayfa/son-48-saat.php");
	exit();
}
if ($page=="son-1-hafta") {
	include("sayfa/son-1-hafta.php");
	exit();
}
if ($page=="son-1-ay") {
	include("sayfa/son-1-ay.php");
	exit();
}
if ($page=="get-ilanlari") {
	include("sayfa/get-ilanlari.php");
	exit();
}
if ($page=="magazalar") {
	include("sayfa/magazalar.php");
	exit();
}
if ($page=="yazilar") {
	include("sayfa/yazilar.php");
	exit();
}
if ($page=="paket-satin-al") {
	include("sayfa/paket-satin-al.php");
	exit();
}
if ($page=="magaza-paket-satin-al") {
	include("sayfa/magaza-paket-satin-al.php");
	exit();
}
if ($page=="yazdir") {
	include("yazdir.php");
	exit();
}
if ($magaza or $magaza_subdomain) {
	include("magaza.php");
	exit();
}
if ($page=="cikis-yap") {
	session_destroy();
	setcookie("auto_login",null,-60*60*24*365*5,"/");
	session_start();
	$_SESSION["mesaj"]=array(_LANG('msg_continue'),"e");
	yonlendir(site_url());
}
if ($page=="iframe") {
	include("sayfa/iframe.php");
	exit();
}


tema_header();

// Mobile Ã¶zel kategori menÃ¼sÃ¼ (legacy uyumluluk)
$mobil_ozel_menu=tema_ayar("mobil_ozel_menu");
if ($mobil_ozel_menu!=2) { ?>
<div id="mobil_ozel_menu" class="container-fluid visible-xs visible-sm" style="padding:0;overflow:hidden;">
<?php
$kategori_iptal=tema_ayar("kategori_iptal");
$kategori_iptal=rtrim($kategori_iptal,",");
$kategori_iptal_sql="";
if ($kategori_iptal) {$kategori_iptal_sql=" and id not in ($kategori_iptal)";}
$kategori_sql=mysqli_query($baglan,"select * from kategoriler where tip='ilan' and ust_kategori='0' $kategori_iptal_sql order by baslik asc");
while ($kategori_dizi=mysqli_fetch_array($kategori_sql)) {
	$kategori_id=$kategori_dizi["id"];
	$kategori_baslik=_DIL_BASLIK($kategori_dizi["baslik"], $kategori_id, "kategori");
	$kategori_ikon=kategori_bilgi("ikon",$kategori_id);
	if (!$kategori_ikon) {$kategori_ikon="ion-folder";}
	$kategori_link=kategori_link_getir($kategori_id);
?>
	<a href="<?php echo $kategori_link; ?>" class="mk-mobil-cat"><?php echo $kategori_baslik; ?></a>
<?php } ?>
</div>
<?php } ?>

<?php
// Istatistikler (gercek veriler)
$mk_uye_sayisi=(int)mysqli_fetch_array(mysqli_query($baglan,"select COUNT(id) from uyeler"))[0];
$mk_ilan_sayisi=(int)mysqli_fetch_array(mysqli_query($baglan,"select COUNT(id) from icerik where tip='ilan' and durum='1'"))[0];
$mk_magaza_sayisi=(int)mysqli_fetch_array(mysqli_query($baglan,"select COUNT(id) from magazalar"))[0];

// Kategori ikon eslestirme (FA4)
$mk_kat_ikon = array(
	2=>'fa-car', 1=>'fa-home', 8=>'fa-briefcase', 4=>'fa-shopping-bag',
	10=>'fa-paw', 5=>'fa-cogs', 6=>'fa-wrench', 7=>'fa-graduation-cap',
	3=>'fa-cog', 9=>'fa-users', 17766=>'fa-plane', 17741=>'fa-leaf',
	17779=>'fa-globe', 17792=>'fa-anchor',
);
?>

<!-- HERO -->
<section class="mk-hero">
	<div class="container">
		<h1><?php echo _LANG('mk_hero_title'); ?></h1>
		<p><?php echo _LANG('mk_hero_desc'); ?></p>
		<form method="post" action="" class="mk-hero-search">
			<input type="hidden" name="mk_ara" value="1">
			<div class="row">
				<div class="col-md-5 col-sm-12">
					<input type="text" name="s" class="form-control" placeholder="<?php echo _LANG('mk_hero_kw_ph'); ?>" value="">
				</div>
				<div class="col-md-3 col-sm-6">
					<select name="kat_id" class="form-control">
						<option value=""><?php echo _LANG('mk_hero_all_cats'); ?></option>
						<?php
						$mk_kat_sql=mysqli_query($baglan,"select id,baslik,icerik from kategoriler where tip='ilan' and ust_kategori='0' order by icerik desc, baslik asc");
						while ($kd=mysqli_fetch_array($mk_kat_sql)) {
							$kid=$kd["id"];
							$kb=_DIL_BASLIK($kd["baslik"],$kid,"kategori");
							echo "<option value='$kid'>".htmlspecialchars($kb,ENT_QUOTES,'UTF-8')."</option>";
						}
						?>
					</select>
				</div>
				<div class="col-md-3 col-sm-6">
					<select name="il_id" class="form-control">
						<option value=""><?php echo _LANG('mk_hero_all_regions'); ?></option>
						<?php
						$mk_il_sql=mysqli_query($baglan,"select id,il_adi from iller order by il_adi asc");
						while ($idr=mysqli_fetch_array($mk_il_sql)) {
							echo "<option value='".$idr["id"]."'>".htmlspecialchars($idr["il_adi"],ENT_QUOTES,'UTF-8')."</option>";
						}
						?>
					</select>
				</div>
				<div class="col-md-1 col-sm-12">
					<button type="submit" class="mk-search-btn btn-block"><i class="ion-search"></i> <?php echo _LANG('mk_hero_search'); ?></button>
				</div>
			</div>
		</form>
		<div class="mk-hero-stats">
			<div class="mk-hero-stat"><span class="n"><?php echo $mk_ilan_sayisi; ?></span><span class="l"><?php echo _LANG('mk_stat_ads'); ?></span></div>
			<div class="mk-hero-stat"><span class="n"><?php echo $mk_uye_sayisi; ?></span><span class="l"><?php echo _LANG('mk_stat_members'); ?></span></div>
			<div class="mk-hero-stat"><span class="n"><?php echo $mk_magaza_sayisi; ?></span><span class="l"><?php echo _LANG('mk_stat_stores'); ?></span></div>
		</div>
	</div>
</section>

<div class="container">

	<!-- KATEGORILER -->
	<section class="mk-section">
		<div class="mk-section-head">
			<h2><?php echo _LANG('mk_sec_categories'); ?></h2>
		</div>
		<div class="mk-cats">
			<?php
			$mk_kat2=mysqli_query($baglan,"select id,baslik,icerik from kategoriler where tip='ilan' and ust_kategori='0' order by icerik desc, baslik asc");
			$mk_cnt=0;
			while ($kd=mysqli_fetch_array($mk_kat2)) {
				if ($mk_cnt>=8) {break;}
				$mk_cnt++;
				$kid=$kd["id"];
				$kb=_DIL_BASLIK($kd["baslik"],$kid,"kategori");
				$kl=kategori_link_getir($kid);
				$ki=isset($mk_kat_ikon[$kid])?$mk_kat_ikon[$kid]:'fa-folder-open';
				$ksayi=(int)$kd["icerik"];
			?>
			<a class="mk-cat" href="<?php echo $kl; ?>">
				<span class="ic"><i class="fa <?php echo $ki; ?>"></i></span>
				<span>
					<div class="nm"><?php echo htmlspecialchars($kb,ENT_QUOTES,'UTF-8'); ?></div>
					<div class="ct"><?php echo $ksayi; ?> <?php echo _LANG('mk_stat_ads'); ?></div>
				</span>
			</a>
			<?php } ?>
		</div>
	</section>

	<?php
	// Yardimci: ilan kartlari render (belirli WHERE kosulu)
	function mk_render_cards($where_sql, $limit=8) {
		global $baglan;
		$q="select id from icerik where tip='ilan' and durum='1' $where_sql order by tarih_saat desc limit $limit";
		$res=mysqli_query($baglan,$q);
		$ids=array();
		if ($res) { while ($r=mysqli_fetch_array($res)) {$ids[]=$r["id"];} }
		if (count($ids)==0) {return false;}
		echo '<div class="mk-cards">';
		foreach ($ids as $iid) {
			$baslik=getir("icerik","baslik",$iid);
			$link=link_getir($iid);
			$resim=resim_getir("300_225",$iid);
			$fiyat_str=fiyat($iid);
			$bolge=il_getir($iid);
			$favori=function_exists('favori_mi')? (favori_mi($iid, isset($_SESSION["oturum_id"])?$_SESSION["oturum_id"]:null)?1:0) : 0;
			$saat=getir("icerik","tarih_saat",$iid);
			?>
			<div class="mk-card">
				<div class="img">
					<a href="<?php echo $link; ?>"><img src="<?php echo $resim; ?>" alt="<?php echo htmlspecialchars($baslik,ENT_QUOTES,'UTF-8'); ?>" loading="lazy"></a>
					<button type="button" class="fav <?php echo $favori?'on':''; ?>" data-id="<?php echo $iid; ?>" aria-label="Favori"><i class="fa fa-heart"></i></button>
				</div>
				<div class="body">
					<div class="title"><a href="<?php echo $link; ?>"><?php echo htmlspecialchars($baslik,ENT_QUOTES,'UTF-8'); ?></a></div>
					<?php if ($fiyat_str) { ?><div class="price"><?php echo $fiyat_str; ?></div><?php } ?>
					<div class="meta">
						<span><i class="ion-location"></i><?php echo htmlspecialchars($bolge,ENT_QUOTES,'UTF-8'); ?></span>
						<span><i class="ion-clock"></i><?php echo $saat?substr($saat,0,10):''; ?></span>
					</div>
				</div>
			</div>
			<?php
		}
		echo '</div>';
		return true;
	}
	?>

	<!-- VITRIN -->
	<?php
	$vitrin_ids_sql="SELECT i.id from icerik as i INNER JOIN dopingler as dp ON i.id=dp.ilan_id and dp.doping='anasayfa_vitrini' and dp.durum='1' and i.durum='1' order by dp.id desc limit 8";
	$vr=mysqli_query($baglan,$vitrin_ids_sql);
	$vids=array();
	if ($vr) { while ($r=mysqli_fetch_array($vr)) {$vids[]=$r["id"];} }
	if (count($vids)>0) {
	?>
	<section class="mk-section">
		<div class="mk-section-head">
			<h2><?php echo _LANG('mk_sec_featured'); ?></h2>
			<a href="<?php echo site_url(); ?>/vitrin-ilanlari"><?php echo _LANG('mk_view_all'); ?></a>
		</div>
		<?php mk_render_cards(" and id in (".implode(",",$vids).")", 8); ?>
	</section>
	<?php } ?>

	<!-- SON ILANLAR -->
	<section class="mk-section">
		<div class="mk-section-head">
			<h2><?php echo _LANG('mk_sec_latest'); ?></h2>
			<a href="<?php echo site_url(); ?>"><?php echo _LANG('mk_view_all'); ?></a>
		</div>
		<?php mk_render_cards("", 8); ?>
	</section>

	<!-- VASITA -->
	<?php
	$vasita_where=" and kategori like '%(2)%'";
	$vasita_ok = (mysqli_num_rows(mysqli_query($baglan,"select id from icerik where tip='ilan' and durum='1' and kategori like '%(2)%' limit 1"))>0);
	if ($vasita_ok) { ?>
	<section class="mk-section">
		<div class="mk-section-head">
			<h2><?php echo _LANG('mk_sec_vehicles'); ?></h2>
			<a href="<?php echo kategori_link_getir(2); ?>"><?php echo _LANG('mk_view_all'); ?></a>
		</div>
		<?php mk_render_cards($vasita_where, 4); ?>
	</section>
	<?php } ?>

	<!-- EMLAK -->
	<?php
	$emlak_where=" and kategori like '%(1)%'";
	$emlak_ok = (mysqli_num_rows(mysqli_query($baglan,"select id from icerik where tip='ilan' and durum='1' and kategori like '%(1)%' limit 1"))>0);
	if ($emlak_ok) { ?>
	<section class="mk-section">
		<div class="mk-section-head">
			<h2><?php echo _LANG('mk_sec_property'); ?></h2>
			<a href="<?php echo kategori_link_getir(1); ?>"><?php echo _LANG('mk_view_all'); ?></a>
		</div>
		<?php mk_render_cards($emlak_where, 4); ?>
	</section>
	<?php } ?>

	<!-- IS ILANLARI -->
	<?php
	$is_where=" and kategori like '%(8)%'";
	$is_ok = (mysqli_num_rows(mysqli_query($baglan,"select id from icerik where tip='ilan' and durum='1' and kategori like '%(8)%' limit 1"))>0);
	if ($is_ok) { ?>
	<section class="mk-section">
		<div class="mk-section-head">
			<h2><?php echo _LANG('mk_sec_jobs'); ?></h2>
			<a href="<?php echo kategori_link_getir(8); ?>"><?php echo _LANG('mk_view_all'); ?></a>
		</div>
		<?php mk_render_cards($is_where, 4); ?>
	</section>
	<?php } ?>

	<!-- CTA -->
	<div class="mk-cta">
		<div>
			<h3><?php echo _LANG('mk_cta_title'); ?></h3>
			<p><?php echo _LANG('mk_cta_desc'); ?></p>
		</div>
		<a class="btn" href="<?php echo ozel_url('ilan-ekle'); ?>"><?php echo _LANG('mk_cta_button'); ?></a>
	</div>

	<?php bilesen_alani("anasayfa"); ?>

</div>

<script>
(function(){
	var btns = document.querySelectorAll('.mk-card .fav');
	btns.forEach(function(b){
		b.addEventListener('click', function(e){
			e.preventDefault();
			var id = this.getAttribute('data-id');
			var self = this;
			var xhr = new XMLHttpRequest();
			xhr.open('POST', '<?php echo tema_dizini(); ?>/ajax.php?islem=mk_favori', true);
			xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
			xhr.onreadystatechange = function(){
				if (xhr.readyState===4 && xhr.status===200) {
					try {
						var r = JSON.parse(xhr.responseText);
						if (r.ok) {
							if (r.state==='added') self.classList.add('on');
							else self.classList.remove('on');
						} else if (r.need_login) {
							window.location.href = '<?php echo ozel_url("giris-yap"); ?>';
						}
					} catch(err){}
				}
			};
			xhr.send('id='+encodeURIComponent(id));
		});
	});
})();
</script>

<?php tema_footer(); ?>
