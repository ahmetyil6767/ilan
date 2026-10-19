<?php
include("../loader.php");
if (@$_SESSION["yonetici_id"]) {
	if (yonetici_mi($_SESSION["yonetici_id"])!=true) {
		session_destroy();
	} else {
		yonlendir("index.php");
	}
}
@$buton=$_POST["buton"];
if ($buton) {
	$cep_tel=trim(addslashes(mysqli_real_escape_string($baglan,$_POST["cep_tel"])));
	$parola=addslashes(mysqli_real_escape_string($baglan,$_POST["parola"]));
}
@$logout=$_GET["logout"];
if ($logout==1) {
	logout("giris-yap.php");
}
?>
<!DOCTYPE HTML>
<html lang="<?php echo _DIL_HTML_LANG(); ?>" dir="<?php echo _DIL_HTML_YON(); ?>">
<head>
	<meta charset="UTF-8">
	<title><?php echo _LANG("p_giris_yap"); ?> - <?php echo $lpanel_baslik; ?></title>
	<?php include("inc/header-kod.php"); ?>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<style>
		/* === JAAYKO ADMIN LOGIN :: SURPRISE REDESIGN === */
		:root {
			--deep: #0b1023;
			--navy: #14224a;
			--indigo: #2c3f8f;
			--amber: #f5b841;
			--cyan: #6dd5ed;
			--danger: #ff4d6d;
			--glass: rgba(255,255,255,0.07);
			--glass2: rgba(255,255,255,0.14);
		}
		* { box-sizing: border-box; }
		html, body { height: 100%; margin: 0; padding: 0; }
		body.giris {
			font-family: "Inter","Segoe UI","Roboto",sans-serif;
			background: radial-gradient(1200px 700px at 15% 20%, #1f3a8a 0%, transparent 60%),
			            radial-gradient(900px 600px at 90% 75%, #7c3aed 0%, transparent 55%),
			            linear-gradient(135deg, #070a14 0%, #111a33 50%, #0b1023 100%);
			color: #e8ecf7;
			overflow: hidden;
			position: relative;
		}
		/* floating blobs */
		.blob {
			position: fixed; border-radius: 50%; filter: blur(40px);
			opacity: 0.55; mix-blend-mode: screen; pointer-events: none;
			animation: drift 14s ease-in-out infinite alternate;
		}
		.b1 { width: 380px; height: 380px; background: #3b82f6; top: -80px; left: -60px; }
		.b2 { width: 420px; height: 420px; background: #f59e0b; bottom: -120px; right: -80px; animation-delay: -6s;
		       opacity: 0.4; }
		.b3 { width: 260px; height: 260px; background: #22d3ee; top: 40%; left: 45%; animation-delay: -3s;
		       opacity: 0.35; mix-blend-mode: normal; }
		@keyframes drift {
			0%   { transform: translate(0,0) scale(1); }
			50%  { transform: translate(40px,-30px) scale(1.12); }
			100% { transform: translate(-25px,25px) scale(0.96); }
		}
		/* grid pattern overlay */
		body.giris::before {
			content: ""; position: fixed; inset: 0;
			background-image:
				linear-gradient(rgba(255,255,255,0.04) 1px, transparent 1px),
				linear-gradient(90deg, rgba(255,255,255,0.04) 1px, transparent 1px);
			background-size: 46px 46px;
			mask-image: radial-gradient(circle at center, black 40%, transparent 85%);
			-webkit-mask-image: radial-gradient(circle at center, black 40%, transparent 85%);
			pointer-events: none;
		}
		.wrap {
			position: relative; z-index: 2;
			min-height: 100vh; display: flex; align-items: center; justify-content: center;
			padding: 32px 18px;
		}
		.shell {
			width: 100%; max-width: 980px;
			display: grid; grid-template-columns: 1fr 1.05fr; gap: 0;
			border-radius: 26px; overflow: hidden;
			box-shadow: 0 30px 80px rgba(0,0,0,0.6), 0 0 0 1px rgba(255,255,255,0.08);
			backdrop-filter: blur(14px);
			background: var(--glass);
		}
		@media (max-width: 780px) {
			.shell { grid-template-columns: 1fr; max-width: 440px; }
			.brand-side { display: none; }
		}
		/* left brand side */
		.brand-side {
			position: relative; padding: 52px 46px;
			background: linear-gradient(160deg, rgba(59,130,246,0.25), rgba(245,158,11,0.08));
			border-right: 1px solid rgba(255,255,255,0.08);
			display: flex; flex-direction: column; justify-content: space-between; min-height: 460px;
		}
		.logo {
			display: flex; align-items: center; gap: 12px;
		}
		.logo-mark {
			width: 46px; height: 46px; border-radius: 12px;
			background: linear-gradient(135deg, #f5b841, #fb923c);
			display: grid; place-items: center;
			font-weight: 900; color: #0b1023; font-size: 22px;
			box-shadow: 0 8px 24px rgba(245,184,65,0.45);
		}
		.logo span { font-size: 20px; font-weight: 700; letter-spacing: 0.5px; }
		.brand-side h1 {
			font-size: 42px; line-height: 1.05; margin: 0 0 16px;
			font-weight: 800; letter-spacing: -1px;
			background: linear-gradient(90deg, #ffffff, #93c5fd);
			-webkit-background-clip: text; background-clip: text; color: transparent;
		}
		.brand-side p { color: #b6bed6; font-size: 15px; line-height: 1.6; margin: 0 0 26px; }
		.feature-list { list-style: none; padding: 0; margin: 0; }
		.feature-list li {
			display: flex; align-items: center; gap: 10px;
			color: #cdd3e8; font-size: 13.5px; padding: 7px 0;
		}
		.feature-list li::before {
			content: ""; width: 6px; height: 6px; border-radius: 50%;
			background: var(--amber); box-shadow: 0 0 10px var(--amber);
		}
		.brand-foot { font-size: 12px; color: #7f86a6; letter-spacing: 0.3px; }
		/* form side */
		.form-side { padding: 46px 44px; background: rgba(7,10,20,0.5); position: relative; }
		.form-side h2 {
			font-size: 26px; font-weight: 700; margin: 0 0 6px;
			background: linear-gradient(90deg, #fff, #a78bfa);
			-webkit-background-clip: text; background-clip: text; color: transparent;
		}
		.form-side .sub { color: #8a93b5; font-size: 13px; margin-bottom: 26px; }
		.field { margin-bottom: 22px; position: relative; }
		.field label {
			display: block; font-size: 11px; text-transform: uppercase;
			letter-spacing: 1.6px; color: #9ca7d6; margin-bottom: 8px; font-weight: 600;
		}
		.input-wrap {
			position: relative; display: flex; align-items: stretch;
			border: 1px solid rgba(255,255,255,0.15); border-radius: 14px;
			background: rgba(255,255,255,0.05); overflow: hidden;
			transition: all .25s ease;
		}
		.input-wrap:focus-within {
			border-color: var(--amber); box-shadow: 0 0 0 3px rgba(245,184,65,0.15);
			background: rgba(255,255,255,0.08);
		}
		.prefix {
			display: flex; align-items: center; padding: 0 14px;
			color: var(--amber); font-weight: 700; font-size: 14px;
			border-right: 1px solid rgba(255,255,255,0.1); background: rgba(245,184,65,0.08);
		}
		.input-wrap input {
			flex: 1; border: none; outline: none; background: transparent;
			color: #f1f4ff; font-size: 15px; padding: 14px 16px; width: 100%;
		}
		.input-wrap input::placeholder { color: #5b6485; }
		.input-wrap i.icon {
			display: flex; align-items: center; padding: 0 16px; color: #5b6485;
		}
		.btn-submit {
			width: 100%; padding: 15px; border: none; border-radius: 14px;
			background: linear-gradient(90deg, #f5b841, #f97316);
			color: #0b1023; font-weight: 800; font-size: 15px; letter-spacing: 0.5px;
			cursor: pointer; position: relative; overflow: hidden;
			box-shadow: 0 10px 28px rgba(245,184,65,0.35);
			transition: transform .15s ease, box-shadow .25s ease;
		}
		.btn-submit:hover {
			transform: translateY(-2px); box-shadow: 0 16px 36px rgba(245,184,65,0.5);
		}
		.btn-submit:active { transform: translateY(0); }
		.btn-submit::after {
			content: ""; position: absolute; inset: 0;
			background: linear-gradient(90deg, transparent, rgba(255,255,255,0.35), transparent);
			transform: translateX(-100%); transition: transform .6s ease;
		}
		.btn-submit:hover::after { transform: translateX(100%); }
		.links {
			margin-top: 20px; text-align: center; font-size: 12.5px; color: #7f86a6;
		}
		.links a { color: var(--amber); text-decoration: none; margin: 0 8px; font-weight: 600; }
		.links a:hover { text-decoration: underline; }
		.alert-box {
			margin-bottom: 18px; padding: 12px 14px; border-radius: 12px;
			background: rgba(255,77,109,0.12); border: 1px solid rgba(255,77,109,0.4);
			color: #ffb3c1; font-size: 13px;
		}
		.wait-box {
			margin-bottom: 18px; padding: 14px; border-radius: 12px;
			background: rgba(245,184,65,0.1); border: 1px solid rgba(245,184,65,0.3);
			color: #f7d68a; font-size: 13px; text-align: center;
		}
		.badge-row {
			display: flex; align-items: center; justify-content: space-between;
			margin-bottom: 26px;
		}
		.secure-pill {
			display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px;
			border-radius: 999px; background: rgba(109,213,237,0.1);
			border: 1px solid rgba(109,213,237,0.3); color: var(--cyan);
			font-size: 11px; font-weight: 600; letter-spacing: 0.6px;
		}
		.secure-pill::before {
			content: ""; width: 7px; height: 7px; border-radius: 50%;
			background: var(--cyan); box-shadow: 0 0 8px var(--cyan);
		}
		.watermark {
			position: absolute; right: 14px; bottom: 10px; font-size: 10px;
			color: rgba(255,255,255,0.18); letter-spacing: 1px;
		}
		/* hide old panel chrome */
		#giris_kutusu > .panel { display: none !important; }
	</style>
</head>
<body class="giris">
	<div class="blob b1"></div>
	<div class="blob b2"></div>
	<div class="blob b3"></div>
	<div class="wrap">
		<div class="shell">
			<aside class="brand-side">
				<div class="logo">
					<div class="logo-mark">J</div>
					<span>Jaayko Admin</span>
				</div>
				<div>
					<h1><?php echo _LANG("p_login_hero_baslik"); ?></h1>
					<p><?php echo _LANG("p_login_hero_aciklama"); ?></p>
					<ul class="feature-list">
						<li><?php echo _LANG("p_login_ozellik_guvenli"); ?></li>
						<li><?php echo _LANG("p_login_ozellik_telefon"); ?></li>
						<li><?php echo _LANG("p_login_ozellik_dil"); ?></li>
						<li><?php echo _LANG("p_login_ozellik_bildirim"); ?></li>
					</ul>
				</div>
				<div class="brand-foot">JAAYKO.GM · v2.0</div>
			</aside>
			<section class="form-side">
				<div class="badge-row">
					<div class="secure-pill"><?php echo _LANG("p_guvenli_baglanti"); ?></div>
					<span style="color:#7f86a6; font-size:11px;">@ 2026</span>
				</div>
				<h2><?php echo _LANG('account_login'); ?></h2>
				<div class="sub"><?php echo _LANG('account_admin_login_hint'); ?></div>

				<?php 
				@$oturum_id=$_SESSION["oturum_id"];
				if ($oturum_id) {
					$oturum_ad_soyad=bilgi_getir("uyeler","ad_soyad",$oturum_id);
						echo "<div class='alert-box'><p>" . _LANG('p_panel_yetki_yok') . "</p><a href='?logout=1' style='color:#ffb3c1;font-weight:700;'>" . _LANG('p_oturumu_kapat') . "</a></div>";
				} else {
					if (time()-@$_SESSION["son_deneme"]>30) {						
				?>
				<form method="post" action="">
					<div class="field">
						<label><?php echo _LANG("p_cep_telefonu"); ?></label>
						<div class="input-wrap">
							<span class="prefix">+220</span>
							<input type="text" name="cep_tel" id="cep_tel" class="form-control" placeholder="700 1234" required autofocus>
						</div>
					</div>
					<div class="field">
						<label><?php echo _LANG("p_parola"); ?></label>
						<div class="input-wrap">
							<i class="icon"><i class="fa fa-lock"></i></i>
							<input type="password" name="parola" class="form-control" placeholder="••••••••" required>
						</div>
					</div>
					<button type="submit" name="buton" class="btn-submit" value="<?php echo _LANG("p_giris_yap"); ?>">
						<i class="fa fa-sign-in"></i> <?php echo _LANG('account_login'); ?>
					</button>
				</form>
				<div class="links">
					<a href="<?php echo site_url(); ?>"><?php echo _LANG("p_siteye_geri_don"); ?></a>
				</div>
				<?php } else {
					$kalan=30-(time()-$_SESSION["son_deneme"]);
					echo "<div class='wait-box'><p>" . _LANG('p_yeniden_dene_bekle', array($kalan)) . "</p><input type='hidden' id='kalan_saniye' value='$kalan' /></div>";
					echo "<script type='text/javascript'>
					setInterval(function(){var saniye=\$('#kalan_saniye').val();if(saniye<1){\$('#deneme_blok').html('<p>" . _LANG('p_yonlendiriliyor_bekle') . "</p>');location.reload();}else{\$('#kalan_saniye').val(saniye-1);\$('#saniye').text(saniye-1);}},1000);</script>";
				}
				if ($buton) { ?>
				<?php
					$_SESSION["son_deneme"]=time();
					$uye_mevcut_mu=uye_mevcut_mu($cep_tel);
					if ($uye_mevcut_mu==1) {
						$kontrol=uye_sorgula($cep_tel,$parola);
						if ($kontrol==1) {
							$uye_id=uye_id_getir($cep_tel);
							$_SESSION["oturum_id"]=$uye_id;
							$yonetici_mi=yonetici_mi($uye_id);
							if ($yonetici_mi==true) {
								$_SESSION["yonetici_id"]=$uye_id;
								$ad_soyad_getir=bilgi_getir("uyeler","ad_soyad",$uye_id);
								if (function_exists('lpanel_uye_profil_link')) {
									$link=lpanel_uye_profil_url($uye_id);
									bildirim_ekle(_LANG('p_panel_giris_yapti_link'), array($link,$ad_soyad_getir),$uye_id,0);
								} else {
									bildirim_ekle(_LANG('p_panel_giris_yapti_plain'), array($ad_soyad_getir),$uye_id,0);
								}
								$_SESSION["mesaj"]=array(_LANG('p_panel_hosgeldiniz'), array($ad_soyad_getir),"success");
								header("location:index.php");
								exit();								
							} else {
								$ad_soyad_getir=bilgi_getir("uyeler","ad_soyad",$uye_id);
								if (function_exists('lpanel_uye_profil_link')) {
									$link=lpanel_uye_profil_url($uye_id);
									bildirim_ekle(_LANG('p_panel_oturum_acadi_link'), array($link,$ad_soyad_getir),$uye_id,0);
								} else {
									bildirim_ekle(_LANG('p_panel_oturum_acadi_plain'), array($ad_soyad_getir),$uye_id,0);
								}
								yonlendir(site_url());
							}
						} else {
							echo "<div class='alert-box'>" . _LANG('p_eksik_hatali_bilgi') . "</div>";
						}
					} else {
						echo "<div class='alert-box'>" . _LANG('p_eksik_hatali_bilgi') . "</div>";
					} ?>
					<?php } } ?>
					
					<?php 
					if (@$_SESSION["mesaj"]!="") {
					$mesaj_mesaj=stripslashes($_SESSION["mesaj"][0]);
					$mesaj_bicim=$_SESSION["mesaj"][1];
					echo "<div class='alert alert-$mesaj_bicim autoclose text-center' style='margin-bottom:0;border-radius:4px !important;'>$mesaj_mesaj
					<button type='button' class='close' data-dismiss='alert' aria-label='Kapat'></button></div>";
					$_SESSION["mesaj"]="";
					}
					?>
				</div>
				<span class="watermark">JAAYKO ADMIN</span>
			<div style="position:absolute; top:18px; right:22px; display:flex; gap:6px;">
<?php $lpanel_diller=array('tr','en'); foreach ($lpanel_diller as $lk) { ?>
				<a href="<?php echo _DIL_DEGISTIR_LINK($lk); ?>" style="padding:5px 9px; border-radius:8px; font-size:11px; font-weight:700; letter-spacing:.5px; text-decoration:none; color:#e8ecf7; background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.15);">
					<?php echo strtoupper($lk); ?>
				</a>
				<?php } ?>
			</div>
			</section>
		</div>
	</div>
	<!-- phone mask -->
	<script type="text/javascript">
	$(function(){
		$("#cep_tel").mask("000 0000",{placeholder:"700 1234"});
	});
	</script>
</body>
</html>