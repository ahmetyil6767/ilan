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

// GerÃ§ek istatistikler
$mk_uye_sayisi=(int)mysqli_fetch_array(mysqli_query($baglan,"select COUNT(id) from uyeler"))[0];
$mk_ilan_sayisi=(int)mysqli_fetch_array(mysqli_query($baglan,"select COUNT(id) from icerik where tip='ilan' and durum='1'"))[0];
$mk_magaza_sayisi=(int)mysqli_fetch_array(mysqli_query($baglan,"select COUNT(id) from magazalar"))[0];

// Kategori ikon/renk eÅŸleÅŸtirme
$mk_kat_ikon = array(
	2=>'fa-car', 1=>'fa-home', 8=>'fa-briefcase', 4=>'fa-shopping-bag',
	10=>'fa-paw', 5=>'fa-cogs', 6=>'fa-wrench', 7=>'fa-graduation-cap',
	3=>'fa-cog', 9=>'fa-users', 17766=>'fa-plane', 17741=>'fa-leaf',
	17779=>'fa-globe', 17792=>'fa-anchor',
);
$mk_kat_renk = array(2=>'c-orange',1=>'c-green',8=>'c-blue',4=>'c-sky',10=>'c-purple',5=>'c-teal',6=>'c-violet',7=>'c-pink');

// Kart yardÄ±mcÄ±sÄ±
function mk_kartlar($where_sql, $limit=5, $etiket='') {
	global $baglan;
	$q="select id from icerik where tip='ilan' and durum='1' $where_sql order by tarih_saat desc limit $limit";
	$res=mysqli_query($baglan,$q);
	$ids=array();
	if ($res) { while ($r=mysqli_fetch_array($res)) { $ids[]=$r["id"]; } }
	if (count($ids)==0) { return false; }
	echo '<div class="mk-cards">';
	foreach ($ids as $iid) {
		$baslik=getir("icerik","baslik",$iid);
		$link=link_getir($iid);
		$resim=resim_getir("530_393",$iid);
		$fiyat_str=fiyat($iid);
		$bolge=il_getir($iid);
		$favori=function_exists('favori_mi') ? (favori_mi($iid, isset($_SESSION["oturum_id"])?$_SESSION["oturum_id"]:null)?1:0) : 0;
		$saat=getir("icerik","tarih_saat",$iid);
		$gun=1;
		if ($saat) { $fark=time()-strtotime($saat); if ($fark>0) { $gun=max(1,(int)round($fark/86400)); } }
		$zaman=sprintf(_LANG('mk_days_ago'), $gun);
		?>
		<div class="mk-card">
			<div class="img">
				<a href="<?php echo $link; ?>"><img src="<?php echo $resim; ?>" alt="<?php echo htmlspecialchars($baslik,ENT_QUOTES,'UTF-8'); ?>" loading="lazy"></a>
				<?php if ($etiket) { ?><span class="mk-badge <?php echo ($etiket=='jobs'?'mk-badge-jobs':''); ?>"><?php echo ($etiket=='jobs'?_LANG('mk_jobs'):_LANG('mk_for_sale')); ?></span><?php } ?>
				<button type="button" class="fav <?php echo $favori?'on':''; ?>" data-id="<?php echo $iid; ?>" aria-label="Favori"><i class="ion-heart"></i></button>
			</div>
			<div class="body">
				<h3 class="title"><a href="<?php echo $link; ?>"><?php echo htmlspecialchars($baslik,ENT_QUOTES,'UTF-8'); ?></a></h3>
				<?php if ($fiyat_str) { ?><div class="price"><?php echo $fiyat_str; ?></div><?php } ?>
				<div class="meta">
					<span><i class="ion-clock"></i><?php echo $zaman; ?></span>
					<?php if ($bolge) { ?><span><i class="ion-location"></i><?php echo htmlspecialchars($bolge,ENT_QUOTES,'UTF-8'); ?></span><?php } ?>
				</div>
			</div>
		</div>
		<?php
	}
	echo '</div>';
	return true;
}
?>

<!-- HERO -->
<section class="mk-hero">
	<div class="mk-hero-bg" style="background-image:url('<?php echo tema_dizini(); ?>/img/hero-gambia.jpg');"></div>
	<div class="container mk-hero-inner">
		<div class="mk-hero-copy">
			<span class="mk-eyebrow"><?php echo _LANG('mk_eyebrow'); ?></span>
			<h1><span class="mk-h1-main"><?php echo _LANG('mk_hero_title_main'); ?></span><br><span class="mk-h1-accent"><?php echo _LANG('mk_hero_title_accent'); ?></span></h1>
			<p><?php echo _LANG('mk_hero_desc'); ?></p>
			<form method="post" action="" class="mk-hero-search">
				<input type="hidden" name="mk_ara" value="1">
				<div class="mk-search-fields">
					<div class="mk-sf mk-sf-key">
						<i class="ion-search"></i>
						<input type="text" name="s" placeholder="<?php echo _LANG('mk_hero_kw_ph'); ?>" value="">
					</div>
					<div class="mk-sf mk-sf-select">
						<i class="ion-grid"></i>
						<select name="kat_id">
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
					<div class="mk-sf mk-sf-select">
						<i class="ion-location"></i>
						<select name="il_id">
							<option value=""><?php echo _LANG('mk_hero_all_regions'); ?></option>
							<?php
							$mk_il_sql=mysqli_query($baglan,"select id,il_adi from iller order by il_adi asc");
							while ($idr=mysqli_fetch_array($mk_il_sql)) {
								echo "<option value='".$idr["id"]."'>".htmlspecialchars($idr["il_adi"],ENT_QUOTES,'UTF-8')."</option>";
							}
							?>
						</select>
					</div>
					<button type="submit" class="mk-search-btn"><i class="ion-search"></i><span><?php echo _LANG('mk_hero_search'); ?></span></button>
				</div>
			</form>
		</div>
	</div>
	<div class="mk-hero-stats-bar">
		<div class="container">
			<div class="mk-hs"><i class="ion-pricetags"></i><div><b><?php echo number_format($mk_ilan_sayisi); ?></b><span><?php echo _LANG('mk_stat_ads'); ?></span></div></div>
			<div class="mk-hs"><i class="ion-person-stalker"></i><div><b><?php echo number_format($mk_uye_sayisi); ?></b><span><?php echo _LANG('mk_stat_members'); ?></span></div></div>
			<div class="mk-hs"><i class="ion-shield-checkmark"></i><div><span><?php echo _LANG('mk_trust_safe'); ?></span></div></div>
			<div class="mk-hs"><i class="ion-star"></i><div><span><?php echo _LANG('mk_trust_local'); ?></span></div></div>
		</div>
	</div>
</section>

<div class="container">

	<!-- POPULAR CATEGORIES -->
	<section class="mk-section" id="kategoriler">
		<div class="mk-section-head">
			<h2><i class="ion-grid mk-head-ic"></i> <?php echo _LANG('mk_sec_categories'); ?></h2>
			<a href="<?php echo site_url(); ?>#kategoriler"><?php echo _LANG('mk_view_all_categories'); ?> <i class="ion-arrow-right-c"></i></a>
		</div>
		<div class="mk-cats">
			<?php
			$mk_kat2=mysqli_query($baglan,"select id,baslik,icerik from kategoriler where tip='ilan' and ust_kategori='0' order by icerik desc, baslik asc");
			$mk_cnt=0;
			while ($kd=mysqli_fetch_array($mk_kat2)) {
				if ($mk_cnt>=8) { break; }
				$kid=$kd["id"];
				$kb=_DIL_BASLIK($kd["baslik"],$kid,"kategori");
				$kl=kategori_link_getir($kid);
				$ki=isset($mk_kat_ikon[$kid])?$mk_kat_ikon[$kid]:'fa-folder-open';
				$kc=isset($mk_kat_renk[$kid])?$mk_kat_renk[$kid]:('c-'.['orange','green','blue','sky','purple','teal','violet','pink'][$mk_cnt % 8]);
				$ksayi=(int)$kd["icerik"];
				$mk_cnt++;
			?>
			<a class="mk-cat" href="<?php echo $kl; ?>">
				<span class="mk-cat-ic <?php echo $kc; ?>"><i class="fa <?php echo $ki; ?>"></i></span>
				<span class="mk-cat-nm"><?php echo htmlspecialchars($kb,ENT_QUOTES,'UTF-8'); ?></span>
				<span class="mk-cat-ct"><?php echo number_format($ksayi); ?> <?php echo _LANG('mk_stat_ads'); ?></span>
			</a>
			<?php } ?>
		</div>
	</section>

	<!-- FEATURED: VEHICLES -->
	<?php if ((int)mysqli_num_rows(mysqli_query($baglan,"select id from icerik where tip='ilan' and durum='1' and kategori like '%(2)%' limit 1"))>0) { ?>
	<section class="mk-section">
		<div class="mk-section-head">
			<h2><i class="fa fa-car mk-head-ic c-orange"></i> <?php echo _LANG('mk_sec_featured'); ?></h2>
			<a href="<?php echo kategori_link_getir(2); ?>"><?php echo _LANG('mk_view_all'); ?> <?php echo _DIL_BASLIK(getir('kategoriler','baslik',2),2,'kategori'); ?> <i class="ion-arrow-right-c"></i></a>
		</div>
		<?php mk_kartlar(" and kategori like '%(2)%'", 5, 'sale'); ?>
	</section>
	<?php } ?>

	<!-- FEATURED: PROPERTY -->
	<?php if ((int)mysqli_num_rows(mysqli_query($baglan,"select id from icerik where tip='ilan' and durum='1' and kategori like '%(1)%' limit 1"))>0) { ?>
	<section class="mk-section">
		<div class="mk-section-head">
			<h2><i class="fa fa-home mk-head-ic c-green"></i> <?php echo _LANG('mk_sec_featured'); ?></h2>
			<a href="<?php echo kategori_link_getir(1); ?>"><?php echo _LANG('mk_view_all'); ?> <?php echo _DIL_BASLIK(getir('kategoriler','baslik',1),1,'kategori'); ?> <i class="ion-arrow-right-c"></i></a>
		</div>
		<?php mk_kartlar(" and kategori like '%(1)%'", 5, 'sale'); ?>
	</section>
	<?php } ?>

	<!-- FEATURED: JOBS -->
	<?php if ((int)mysqli_num_rows(mysqli_query($baglan,"select id from icerik where tip='ilan' and durum='1' and kategori like '%(8)%' limit 1"))>0) { ?>
	<section class="mk-section">
		<div class="mk-section-head">
			<h2><i class="fa fa-briefcase mk-head-ic c-blue"></i> <?php echo _LANG('mk_sec_featured'); ?></h2>
			<a href="<?php echo kategori_link_getir(8); ?>"><?php echo _LANG('mk_view_all'); ?> <?php echo _DIL_BASLIK(getir('kategoriler','baslik',8),8,'kategori'); ?> <i class="ion-arrow-right-c"></i></a>
		</div>
		<?php mk_kartlar(" and kategori like '%(8)%'", 5, 'jobs'); ?>
	</section>
	<?php } ?>

	<!-- CTA -->
	<section class="mk-cta">
		<div class="mk-cta-inner">
			<div>
				<h3><?php echo _LANG('mk_cta_title'); ?></h3>
				<p><?php echo _LANG('mk_cta_desc'); ?></p>
				<a class="mk-cta-btn" href="<?php echo ozel_url('ilan-ekle'); ?>"><?php echo _LANG('mk_cta_button'); ?> <i class="ion-arrow-right-c"></i></a>
			</div>
			<div class="mk-cta-badge"><?php echo _LANG('mk_cta_fast'); ?></div>
		</div>
	</section>

	<?php
	$mk_play=tema_ayar("play_store");
	$mk_apple=tema_ayar("apple_store");
	// Uygulama şeridi: QR her zaman, mağaza rozetleri ayar varsa
	?>
	<!-- MOBILE APP -->
	<section class="mk-app">
		<div class="mk-app-txt">
			<h3><?php echo _LANG('mk_app_title'); ?></h3>
			<p><?php echo _LANG('mk_app_desc'); ?></p>
			<div class="mk-app-badges">
				<?php if ($mk_play) { ?><a href="<?php echo $mk_play; ?>" target="_blank" rel="noopener"><img src="<?php echo tema_dizini(); ?>/img/play-store.png" alt="Google Play"></a><?php } ?>
				<?php if ($mk_apple) { ?><a href="<?php echo $mk_apple; ?>" target="_blank" rel="noopener"><img src="<?php echo tema_dizini(); ?>/img/apple-store.png" alt="App Store"></a><?php } ?>
			</div>
		</div>
		<div class="mk-app-qr">
			<img src="https://api.qrserver.com/v1/create-qr-code/?size=110x110&data=<?php echo urlencode(site_url()); ?>" alt="<?php echo _LANG('mk_scan_visit'); ?>" width="110" height="110">
			<span><?php echo _LANG('mk_scan_visit'); ?></span>
		</div>
	</section>

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
						var txt = xhr.responseText.replace(/^\uFEFF/, '');
						var r = JSON.parse(txt);
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
