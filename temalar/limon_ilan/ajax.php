<?php 
include_once("../../loader.php");
include_once("../../eklentiler/favori_icerikler/eklenti.php");
include_once("../../eklentiler/mesajlasma/eklenti.php");
include_once("fonksiyonlar.php");

$islem=isset($_GET["islem"])?$_GET["islem"]:null;

if (!strstr(ref_url(),site_url())) {
	die("Hata: Erisim engellendi.");
}
if (!$islem) {
	die("Hata");
}

if ($islem=="ilan_ekle_alt_kategoriler") {
	$oturum_id=$_SESSION["oturum_id"];
	$ust_kategori=isset($_POST["id"])?$_POST["id"]:null;
	$magaza=isset($_GET["magaza"])?$_GET["magaza"]:null;
	
	if (!$magaza) {
		$kategori_limiti=uye_ilan_limiti("kategori",$oturum_id);
		$yayinda_ilan_sayisi=uye_kategori_mevcut_ilan_sayisi($ust_kategori,$oturum_id);
	}

	if ($magaza or $kategori_limiti-$yayinda_ilan_sayisi>0) {
		$ust_kat_sayisi=count(ust_kategoriler($ust_kategori));
		$kademe=$ust_kat_sayisi+1;
		$alt_kategori_sayisi=alt_kategori_sayisi($ust_kategori);
		if ($alt_kategori_sayisi>0) {
		echo "<select name='kategori' id='kategori' class='form-control kategori_sec' size='15' data-kademe='$kademe'><option value='' selected disabled class='visible-xs visible-sm'>Kategori seçiniz</option>";
		
		$kategori_siralama=tema_ayar("ilan_ekle_kategori_siralama");
		if ($kategori_siralama=="baslik_a_z") {
			$kategori_sql=mysqli_query($baglan,"select * from kategoriler where ust_kategori='$ust_kategori' order by baslik asc");
		} else if ($kategori_siralama=="icerik_sayisi") {
			$kategori_sql=mysqli_query($baglan,"select * from kategoriler where ust_kategori='$ust_kategori' order by icerik desc, baslik asc");
		} else {
			$kategori_sql=mysqli_query($baglan,"select * from kategoriler where ust_kategori='$ust_kategori'");
		}
		
		while ($kategori_dizi=mysqli_fetch_array($kategori_sql)) {
			$kategori_id=$kategori_dizi["id"];
			$kategori_baslik=$kategori_dizi["baslik"];
			$kat_dizi[]=array("deger"=>$kategori_id,"baslik"=>$kategori_baslik);
			echo "<option value='$kategori_id'>$kategori_baslik</option>";
		}
		echo "</select>";
		} else {
			$ilan_ekle_url=ozel_url("ilan-ekle");
			if ($magaza) {
				$devam_url="<a href='$ilan_ekle_url?kat=$ust_kategori&magaza=$magaza' class='btn btn-success'><i class='ion-arrow-right-c'></i>Devam</a>";
			} else {
				$devam_url="<a href='$ilan_ekle_url?kat=$ust_kategori' class='btn btn-success'><i class='ion-arrow-right-c'></i>Devam</a>";
			}
			echo "<div class='kategori_sec tamam' id='kat-$ust_kat_sayisi'>
			<i class='ion-android-checkmark-circle'></i>
			<h3>" . _LANG('hb_kategori_secimi_tamamlandi') . "</h3>
			$devam_url
			</div>";
		}
	} else {
		$profil_url=ozel_url("plan-detaylari");
		echo "<div class='kategori_sec tamam limit' id='kat-$ust_kat_sayisi'>
		<i class='ion-alert'></i>
		<h3>Bir sorun var!</h3>
		<p>" . _LANG('hb_bu_kategori_icin_belirlenen_ilan_limitine_ulas') . "</p>
		<p><a href='$profil_url' style='color:#c62828;margin-top:16px;'>Detaylı bilgi</a></p>
		</div>";		
	}
}
if ($islem=="vitrin_degistir") {
	$icerik_id=isset($_POST["icerik_id"])?$_POST["icerik_id"]:null;
	$resim_url=isset($_POST["resim_url"])?$_POST["resim_url"]:null;
	bilgi_guncelle("gorsel",$resim_url,$icerik_id);
	galeri_sira_update($resim_url,$icerik_id);
	echo _LANG('tx_vitrin_gorseli_basariyla_guncellenmistir');
}
if ($islem=="ornek_breadcrumb") {
	$kategori=isset($_POST["id"])?$_POST["id"]:null;
	$kategori_link=kategori_link_getir($kategori);
	$kategori_baslik=_DIL_BASLIK(getir("kategoriler","baslik",$kategori), $kategori, "kategori");
	$ust_kategoriler=ust_kategoriler($kategori);
	$ust_kategoriler_sayi=count($ust_kategoriler);
	echo "<ol class='breadcrumb'>";
	if (is_array($ust_kategoriler) and count($ust_kategoriler_sayi)>0) {
		foreach ($ust_kategoriler as $x) {
			$x_link=kategori_link_getir($x);
			$x_baslik=_DIL_BASLIK(getir("kategoriler","baslik",$x), $x, "kategori");
			echo "<li><a>$x_baslik</a></li>";
		}
	}
	echo "<li><a>$kategori_baslik</a></li>";
	echo "</ol>";
}

if ($islem=="favorilere_ekle") {
	$icerik_id=isset($_POST["id"])?$_POST["id"]:null;
	$uye_id=$_SESSION["oturum_id"];
	if (!$uye_id) {
		echo "<div class='alert alert-danger autoclose'>" . _LANG('gy_uye_girisi_gerekli') . "
		<button type='button' class='close' data-dismiss='alert' aria-label='Close'>
		  <span aria-hidden='true'>&times;</span>
		</button>
		</div>";
	} else {
		if (favori_mi($icerik_id,$uye_id)) {
			echo "<div class='alert alert-danger autoclose'>" . _LANG('favori_zaten_ekli') . "
			<button type='button' class='close' data-dismiss='alert' aria-label='Close'>
			  <span aria-hidden='true'>&times;</span>
			</button>
			</div>";
		} else {
			favori_ekle($icerik_id,$uye_id);
			echo "<div class='alert alert-success autoclose'>" . _LANG('favori_basarili') . "
			<button type='button' class='close' data-dismiss='alert' aria-label='Close'>
			  <span aria-hidden='true'>&times;</span>
			</button>
			</div>";
			echo "<script type='text/javascript'>
			$('#favorilere_ekle').hide();
			$('#favorilerden_kaldir').show();
			$('.autoclose').delay(4000).slideUp(300, function() {
				$(this).alert('close');
			});	
			</script>";
		}
	}
}

if ($islem=="favorilerden_kaldir") {
	$icerik_id=isset($_POST["id"])?$_POST["id"]:null;
	$uye_id=$_SESSION["oturum_id"];
	if (!$uye_id) {
		echo "<div class='alert alert-danger autoclose'>" . _LANG('gy_uye_girisi_gerekli') . "
		<button type='button' class='close' data-dismiss='alert' aria-label='Close'>
		  <span aria-hidden='true'>&times;</span>
		</button>
		</div>";
	} else {
		if (favori_mi($icerik_id,$uye_id)) {
			favori_sil($icerik_id,$uye_id);
			echo "<div class='alert alert-success autoclose'>" . _LANG('favori_cikarildi') . "
			<button type='button' class='close' data-dismiss='alert' aria-label='Close'>
			  <span aria-hidden='true'>&times;</span>
			</button>
			</div>";
			echo "<script type='text/javascript'>
			$('#favorilerden_kaldir').hide();
			$('#favorilere_ekle').show();
			$('.autoclose').delay(4000).slideUp(300, function() {
				$(this).alert('close');
			});
			</script>";			
		} else {
			echo "<div class='alert alert-danger autoclose'>" . _LANG('favori_bulunamadi') . "
			<button type='button' class='close' data-dismiss='alert' aria-label='Close'>
			  <span aria-hidden='true'>&times;</span>
			</button>
			</div>";
		}
	}
}

if ($islem=="vitrin_sec") {
	$resim=isset($_POST["url"])?$_POST["url"]:null;
	$icerik_id=isset($_POST["icerik_id"])?$_POST["icerik_id"]:null;
	$mevcut_gorsel=bilgi("gorsel",$icerik_id);
	if ($resim!=$mevcut_gorsel) {
		bilgi_guncelle("gorsel",$resim,$icerik_id);
		$son_url=thumbnail_url("530_393",$resim);
		echo $son_url;
		galeri_sira_update($resim,$icerik_id);
	} else {
		echo false;
	}
}

if ($islem=="foto_sil") {
	$resim=isset($_POST["url"])?$_POST["url"]:null;
	$icerik_id=isset($_POST["icerik_id"])?$_POST["icerik_id"]:null;
	$mevcut_gorsel=bilgi("gorsel",$icerik_id);
	$galeri_id=id_getir("galeri","resim",$resim);
	$thumb_url=thumbnail_url("160_120",$resim);
	$buyuk_url=thumbnail_url("530_393",$resim);
	$full_url=$resim;
	$parcala=explode("uploads/",$thumb_url);
	$yol=$parcala[1];
	unlink("../../uploads/$yol");
	$parcala=explode("uploads/",$buyuk_url);
	$yol=$parcala[1];
	unlink("../../uploads/$yol");
	$parcala=explode("uploads/",$full_url);
	$yol=$parcala[1];
	unlink("../../uploads/$yol");	
	
	mysqli_query($baglan,"delete from galeri where resim='$resim' and icerik_id='$icerik_id'");
	if ($resim==$mevcut_gorsel) {
		bilgi_guncelle("gorsel",null,$icerik_id);	
		echo 2;
	} else {
		echo 1;
	}
}
if ($islem=="iletisim_numaralari") {
	$uye_id=$_SESSION["oturum_id"];
	$cep_tel=telefon_bicimlendir(uye_bilgi("cep_tel",$uye_id));
	$is_tel=telefon_bicimlendir(uye_bilgi("is_tel",$uye_id));
	$ev_tel=telefon_bicimlendir(uye_bilgi("ev_tel",$uye_id));
	
	if ($cep_tel) {$cep_tel_yaz=$cep_tel;} else {$cep_tel_yaz="<span style='color:#e74c3c;'>Belirtilmedi</span>";}
	if ($is_tel) {$is_tel_yaz=$is_tel;} else {$is_tel_yaz="<span style='color:#e74c3c;'>Belirtilmedi</span>";}
	if ($ev_tel) {$ev_tel_yaz=$ev_tel;} else {$ev_tel_yaz="<span style='color:#e74c3c;'>Belirtilmedi</span>";}
	
	$tel_kod.="<div><div class='pull-left'>";
	$tel_kod.="<div>Cep telefonu: $cep_tel_yaz</div>";
	$tel_kod.="<div>İş telefonu: $is_tel_yaz</div>";
	$tel_kod.="<div>Ev telefonu: $ev_tel_yaz</div>";
	$tel_kod.="</div><a class='pull-right btn btn-xs btn-default c_pointer' id='iletisim_guncelle_buton'>İletişim Numaralarını Güncelle</a>
	<div class='clearfix'></div>
	</div>
	<form id='iletisim_guncelle_form' action='' method='post' style='margin-top:15px;'>
		<div class='panel panel-default limon'>
		<div>" . _LANG('hb_iletisim_numaralarini_guncelle') . "</div>
		<div class='panel-body'>

		<div class='form-group'>
			<label for='cep_tel'>Cep Telefonu <span style='color:#e74c3c;margin-left:3px;'>*</span></label>
			<div class='input-group'>
				<span class='input-group-addon' style='border-top-right-radius:0;border-bottom-right-radius:0;color:#e74c3c;border-color:#ddd;'>0</span>
				<input type='text' class='form-control cannot_start_with_zero' id='cep_tel' name='cep_tel' placeholder='Cep Telefonu' value='$cep_tel' style='border-top-left-radius:0 !important;border-bottom-left-radius:0 !important;' required>
			</div>
			<p>" . _LANG('hb_lutfen_telefonunuzu_basinda_sifir_olmadan_tusl') . "</p>
		</div>


		<div class='form-group'>
			<label>" . _LANG('hb_is_telefonu') . "</label>
			<div class='input-group'>
				<span class='input-group-addon' style='border-top-right-radius:0;border-bottom-right-radius:0;color:#e74c3c;border-color:#ddd;'>0</span>
				<input type='text' class='form-control cannot_start_with_zero' id='is_tel' name='is_tel' placeholder='İş Telefonu' value='$is_tel' style='border-top-left-radius:0 !important;border-bottom-left-radius:0 !important;'>
			</div>
			<p>" . _LANG('hb_lutfen_telefonunuzu_basinda_sifir_olmadan_tusl') . "</p>
		</div>
		

		<div class='form-group'>
			<label for='ev_tel'>Ev Telefonu</label>
			<div class='input-group'>
				<span class='input-group-addon' style='border-top-right-radius:0;border-bottom-right-radius:0;color:#e74c3c;border-color:#ddd;'>0</span>
				<input type='text' class='form-control cannot_start_with_zero' id='ev_tel' name='ev_tel' placeholder='Ev Telefonu' value='$ev_tel' style='border-top-left-radius:0 !important;border-bottom-left-radius:0 !important;'>
			</div>
			<p>" . _LANG('hb_lutfen_telefonunuzu_basinda_sifir_olmadan_tusl') . "</p>
		</div>
		
		</div>		
		<div class='panel-footer'>
		<button type='button' id='i_guncelle_buton' class='btn btn-primary'><i class='ion-loop'></i>Güncelle</button>
		</div>
	</div>
	</form>
	
	";
	$tema_dizini=tema_dizini();
	$tel_kod.="
	<script type='text/javascript'>
	$('#cep_tel').mask('999 999 99 99');
	$('#is_tel').mask('999 999 99 99');
	$('#ev_tel').mask('999 999 99 99');
	
$('#cep_tel').mask('000 000 00 00',{placeholder: '536 123 45 67'});
$('#is_tel').mask('000 000 00 00',{placeholder: '536 123 45 67'});
$('#ev_tel').mask('000 000 00 00',{placeholder: '212 123 45 67'});
	
	$('#iletisim_guncelle_form').hide();
	$('#iletisim_guncelle_buton').click(function(){
		$('#iletisim_guncelle_form').slideToggle(500);
	})
	$('body').on('click','#i_guncelle_buton',function(){
		$.ajax({
			type:'post',
			url:'$tema_dizini/ajax.php?islem=iletisim_guncelle',
			data:$('#iletisim_guncelle_form').serialize(),
			success:function(cevap){
				$('input.temizle').val('');
				$('#iletisim_guncelle_form').slideToggle(500);
				$('#iletisim_numaralari').load('$tema_dizini/ajax.php?islem=iletisim_numaralari');
				$('#i_alert').slideUp(250);
				$.notify(
					'İletişim bilgileri başarıyla güncellendi', 
					{ position:'bottom right',className: 'success',}
				);	
			}
		});
	});
	</script>
	";
	if ($tel_kod) {
		echo $tel_kod;
	} else {
		echo "<p>Hesabınıza tanımlanmış iletişim numarası bulunamadı.</p><a href='#'>Ekle</a>";
	}
}
if ($islem=="iletisim_guncelle") {
	$cep_tel=isset($_POST["cep_tel"])?$_POST["cep_tel"]:null;
	$is_tel=isset($_POST["is_tel"])?$_POST["is_tel"]:null;
	$ev_tel=isset($_POST["ev_tel"])?$_POST["ev_tel"]:null;
	$oturum_id=$_SESSION["oturum_id"];
	uye_bilgi_guncelle("cep_tel",$cep_tel,$oturum_id);
	uye_bilgi_guncelle("is_tel",$is_tel,$oturum_id);
	uye_bilgi_guncelle("ev_tel",$ev_tel,$oturum_id);
}
if ($islem=="il_sec") {
	@$ust=isset($_GET["ust"])?$_GET["ust"]:null;
	@$gerekli=isset($_GET["gerekli"])?$_GET["gerekli"]:null;
	@$icerik_id=isset($_GET["icerik_id"])?$_GET["icerik_id"]:null;
	@$il=isset($_GET["il"])?$_GET["il"]:null;
	@$ilce=isset($_GET["ilce"])?$_GET["ilce"]:null;
	@$mahalle=isset($_GET["mahalle"])?$_GET["mahalle"]:null;
	@$sayfa=isset($_GET["sayfa"])?$_GET["sayfa"]:null;
	
	if ($sayfa=="ilan_ekle") {
		$uye_id=$_SESSION["oturum_id"];
		$il_ilce_hazir=uye_bilgi("il_ilce_hazir",$uye_id);
		if ($il_ilce_hazir) {
			$il_ilce_parcala=explode(":",$il_ilce_hazir);
			$il=$il_ilce_parcala[0];
			$ilce=$il_ilce_parcala[1];
			$mahalle=$il_ilce_parcala[2];
		}	
	}
	
	if ($ilce) {
		@$gonder.="&ilce=$ilce";
	}
	if ($mahalle) {
		@$gonder.="&mahalle=$mahalle";
	}
	if ($icerik_id!="") {
		$icerik_il=bilgi("il",$icerik_id);
	}
	if ($gerekli==1) {$gerekli_yaz="required='required'";$gerekli_gonder=1;
	echo "<label for='il'>" . _LANG('ad_bolge') . " <span style='color:#e74c3c;'>*</span></label>";
	} else {$gerekli_yaz=null;$gerekli_gonder=0;}
	echo "<select name='il' id='il' class='form-control' $gerekli_yaz><option value=''><?php echo _LANG('tx_il_seciniz'); ?></option>";
	$sql=mysqli_query($baglan,"select * from iller");
	$selected_yaz=null;
	while ($dizi=mysqli_fetch_array($sql)) {
		$id=$dizi["id"];
		$ad=$dizi["il_adi"];
		if ($icerik_id and $id==$icerik_il) {
			$selected_yaz="selected='selected'";
		} else {
			$selected_yaz=null;
			if ($il and $il==$id) {$selected_yaz="selected='selected'";} else {$selected_yaz=null;}
		}
		echo "<option value='$id' $selected_yaz>$ad</option>";
	}	
	echo "</select>";
	
	if (tema_ayar("il_ilce_mahalle")!=3) {
		$tema_dizini=tema_dizini();
		echo "
		<script type='text/javascript'>
		$('body').on('change','#il',function(){
			var deger=$(this).val();
			var icerik_id='$icerik_id';
			var il=$('#il option:checked').text();
			$('#mahalle_sec').html('');		
			$('#ilce_sec').load('$tema_dizini/ajax.php?islem=ilce_sec&ust='+deger+'&gerekli=$gerekli_gonder'+'');
		});
		</script>	
		";
		if ($icerik_id and $icerik_il) {
		echo "
		<script type='text/javascript'>
			$('#ilce_sec').load('$tema_dizini/ajax.php?islem=ilce_sec&ust=$icerik_il&icerik_id=$icerik_id&gerekli=$gerekli_gonder');
		</script>			
		";
		}
		if ($il) {
		echo "
		<script type='text/javascript'>
			$('#ilce_sec').load('$tema_dizini/ajax.php?islem=ilce_sec&ust=$il&gerekli=$gerekli_gonder'+'');
		</script>	
		";
		}
		if (!empty($il_ilce_hazir) and $mahalle) {
		echo "
		<script type='text/javascript'>
			$('#il').change(function(){

				setTimeout(function() {
					$('#mahalle_sec').html('');
				}, 200);


			});
		</script>	
		";		
		}
	}
}
if ($islem=="ilce_sec") {
	$ust=isset($_GET["ust"])?$_GET["ust"]:null;
	$gerekli=isset($_GET["gerekli"])?$_GET["gerekli"]:null;
	$icerik_id=isset($_GET["icerik_id"])?$_GET["icerik_id"]:null;
	$il=isset($_GET["il"])?$_GET["il"]:null;
	$ilce=isset($_GET["ilce"])?$_GET["ilce"]:null;
	$mahalle=isset($_GET["mahalle"])?$_GET["mahalle"]:null;
	
	if (tema_ayar("il_ilce_mahalle")==2) {
		$gerekli=false;
	}
	
	if ($mahalle) {
		$gonder.="&mahalle=$mahalle";
	}	
	if ($icerik_id!="") {
		$icerik_ilce=bilgi("ilce",$icerik_id);
	}
	if ($gerekli==1) {
		$gerekli_yaz="required='required'";$gerekli_gonder=1;
		echo "<label for='ilce'>" . _LANG('ad_ilce') . " <span style='color:#e74c3c;'>*</span></label>";
	} else {
		$gerekli_yaz=null;$gerekli_gonder=0;
		echo "<label for='ilce'>" . _LANG('ad_ilce') . " <span class='text-muted' style='font-size:11px;font-weight:400;'>" . _LANG('filter_optional') . "</span></label>";
	}
	echo "<select name='ilce' id='ilce' class='form-control' $gerekli_yaz><option value=''>" . _LANG('tx_ilce_seciniz') . "</option>";
	$sql=mysqli_query($baglan,"select * from ilceler where il_id='$ust'");
	$selected_yaz=null;
	while ($dizi=mysqli_fetch_array($sql)) {
		$id=$dizi["id"];
		$ad=$dizi["ilce_adi"];
		if ($icerik_id and $id==$icerik_ilce) {
			$selected_yaz="selected='selected'";
		} else {
			$selected_yaz=null;
			if ($ilce and $ilce==$id) {$selected_yaz="selected='selected'";} else {$selected_yaz=null;}
		}
		echo "<option value='$id' $selected_yaz>$ad</option>";
	}	
	echo "</select>";
	$tema_dizini=tema_dizini();
	echo "
	<script type='text/javascript'>
	$('body').on('change','#ilce',function(){
		var ilce=$('#ilce option:checked').text();
		var deger=$(this).val();
		$('#mahalle_sec').html('');
		$('#mahalle_sec').load('$tema_dizini/ajax.php?islem=mahalle_sec&ust='+deger+'&gerekli=$gerekli_gonder');
	});
	</script>	
	";
	if ($icerik_id and $icerik_ilce) {
	echo "
	<script type='text/javascript'>
		$('#mahalle_sec').load('$tema_dizini/ajax.php?islem=mahalle_sec&ust=$icerik_ilce&icerik_id=$icerik_id&gerekli=$gerekli_gonder');
	</script>			
	";
	}
	if ($ilce) {
	echo "
	<script type='text/javascript'>
		$('#mahalle_sec').load('$tema_dizini/ajax.php?islem=mahalle_sec&ust=$ilce&gerekli=$gerekli_gonder'+'');
	</script>			
	";
	}
}
if ($islem=="mahalle_sec") {
	$ust=isset($_GET["ust"])?$_GET["ust"]:null;
	$gerekli=isset($_GET["gerekli"])?$_GET["gerekli"]:null;
	$icerik_id=isset($_GET["icerik_id"])?$_GET["icerik_id"]:null;
	$ilce=isset($_GET["ilce"])?$_GET["ilce"]:null;
	$mahalle=isset($_GET["mahalle"])?$_GET["mahalle"]:null;	
	
	
	if (tema_ayar("il_ilce_mahalle")==2) {
		$gerekli=false;
	}
	
	if ($icerik_id!="") {
		$icerik_mahalle=bilgi("mahalle",$icerik_id);
	}
	if ($gerekli==1) {$gerekli_yaz="required='required'";$gerekli_gonder=1;
		echo "<label for='mahalle'>Mahalle <span style='color:#e74c3c;'>*</span></label>";
	} else {
		$gerekli_yaz=null;$gerekli_gonder=0;
		echo "<label for='mahalle'>Mahalle <span class='text-muted' style='font-size:11px;font-weight:400;'>(opsiyonel)</span></label>";
	}
	echo "<select name='mahalle' id='mahalle' class='form-control' $gerekli_yaz><option value=''>Mahalle Seçiniz</option>";
	$sql=mysqli_query($baglan,"select * from mahalleler where ilce_id='$ust'");
	$selected_yaz=null;
	while ($dizi=mysqli_fetch_array($sql)) {
		$id=$dizi["id"];
		$ad=$dizi["mahalle_adi"];
		if ($icerik_id and $id==$icerik_mahalle) {
			$selected_yaz="selected='selected'";
		} else {
			$selected_yaz=null;
			if ($mahalle and $mahalle==$id) {$selected_yaz="selected='selected'";} else {$selected_yaz=null;}
		}
		echo "<option value='$id' $selected_yaz>$ad</option>";
	}	
	echo "</select>";
}
if ($islem=="sikayet_gonder") {
	$uye_id=$_SESSION["oturum_id"];
	$ad_soyad=isset($_POST["ad_soyad"])?$_POST["ad_soyad"]:null;
	$mesaj=isset($_POST["mesaj"])?$_POST["mesaj"]:null;
	if ($ad_soyad and $mesaj) {
		$ilan_id=isset($_GET["ilan_id"])?$_GET["ilan_id"]:null;
		$ilan_baslik=getir("icerik","baslik",$ilan_id);
		$ilan_link=link_getir($ilan_id);
		$mail_baslik=tema_ayar("mail_baslik");
		$ileti_admin="
		<p><strong>$ad_soyad</strong> bir şikayet bildirimi gönderdi.</p>
		<p>Üye ID: <strong>$uye_id</strong></p>
		<p>İlan: <strong>$ilan_id</strong></p>
		<p>Kullanıcı mesajı: <strong>$mesaj</strong></p>
		<p><a href=\"$ilan_link\">İlanı Görüntüle</a> - <a href=\"$site_url/$admin_dizini\">Yönetim Paneli</a></p>
		";
		$site_email=ayar("site_email");
		$tarih_saat=tarih_saat();
		$ekle=mysqli_query($baglan,"insert into auto_mail set ad_soyad='$mail_baslik', email_adresi='$site_email', baslik='Yeni şikayet bildirimi: $ad_soyad', mesaj='$ileti_admin', tarih_saat='$tarih_saat', durum='0'");
		// E-posta gönderimi bitti
		echo "
		<div class='alert alert-success autoclose'>
		Şikayet bildiriminiz bize ulaştı. En kısa sürede konu ile ilgileneceğiz.
		<button type='button' class='close' data-dismiss='alert' aria-label='Close'>
		  <span aria-hidden='true'>&times;</span>
		</button>		
		</div>
		<script type='text/javascript'>
		$('.autoclose').delay(4000).slideUp(300, function() {
			$(this).alert('close');
		});	
		</script>
		";
	} else {
		if ($ad_soyad and !$mesaj) {
			$mesaj_yaz=_LANG('tx_lutfen_sikayet_sebebinizi_belirtiniz');
		} else if (!$ad_soyad and !$mesaj) {
			$mesaj_yaz=_LANG('tx_lutfen_ad_soyad_ve_mesaj_alanlarini_doldurunuz');
		}
		echo "
		<div class='alert alert-danger autoclose'>
		$mesaj_yaz
		<button type='button' class='close' data-dismiss='alert' aria-label='Close'>
		  <span aria-hidden='true'>&times;</span>
		</button>		
		</div>
		<script type='text/javascript'>
		$('.autoclose').delay(4000).slideUp(300, function() {
			$(this).alert('close');
		});	
		</script>
		";		
	}
}
if ($islem=="mesaj_gonder") {
	$uye_id=$_SESSION["oturum_id"];
	if ($uye_id) {
		$mesaj=isset($_POST["mesaj"])?$_POST["mesaj"]:null;
		$kime=isset($_POST["kime"])?$_POST["kime"]:null;
		$ilan_id=isset($_GET["ilan_id"])?$_GET["ilan_id"]:null;
		$bildirim=isset($_GET["bildirim"])?$_GET["bildirim"]:null;
		$kime_ad_soyad=getir("uyeler","ad_soyad",$kime);
		$uye_ad_soyad=getir("uyeler","ad_soyad",$uye_id);
		$gorusme_id=gorusme_id($uye_id,$kime,$ilan_id);
		$gorusme_link=ozel_url("mesajlarim")."?g=$gorusme_id";
		if ($gorusme_id and $mesaj) {$mesaj_gonder=mesaj_gonder($mesaj,$gorusme_id);}
		if ($bildirim==1) {
			bildirim_ekle(sprintf(_LANG('bnf_yeni_gorusme'), $gorusme_link, $ilan_id, $uye_ad_soyad),$kime,1);
			echo "
			<div class='alert alert-success autoclose'>
			$kime_ad_soyad adlı üyeye bir mesaj gönderdiniz.
			<button type='button' class='close' data-dismiss='alert' aria-label='Close'>
			  <span aria-hidden='true'>&times;</span>
			</button>		
			</div>
			<script type='text/javascript'>
			$('.autoclose').delay(4000).slideUp(300, function() {
				$(this).alert('close');
			});	
			</script>
			";
			
			// E-posta gönderimi başladı
			$mail_baslik=tema_ayar("mail_baslik");
			$site_url=site_url();		
			$kime_ad_soyad=getir("uyeler","ad_soyad",$kime);
			$uye_ad_soyad=getir("uyeler","ad_soyad",$uye_id);
			$kime_email=getir("uyeler","email_adresi",$kime);
			
			$ileti="
			<p>Merhaba <strong>$kime_ad_soyad</strong></p>
			<p>Mesajınızı görüntülemek için lütfen <a href=\"$gorusme_link\">buraya</a> tıklayın.</p>";
			auto_mail_add($kime_ad_soyad,$kime_email,"$uye_ad_soyad kullanıcısından yeni bir özel mesaj aldınız",$ileti);	
			
		} else {
			echo "
			<div class='alert alert-danger autoclose'>
			Lütfen mesaj alanını doldurunuz.
			<button type='button' class='close' data-dismiss='alert' aria-label='Close'>
			  <span aria-hidden='true'>&times;</span>
			</button>		
			</div>
			<script type='text/javascript'>
			$('.autoclose').delay(4000).slideUp(300, function() {
				$(this).alert('close');
			});	
			</script>
			";		
		}
	} else {
		echo "
		<div class='alert alert-danger autoclose'>
		İlan sahibine mesaj gönderebilmek için üye girişi yapmanız gerekmektedir.
		<button type='button' class='close' data-dismiss='alert' aria-label='Close'>
		  <span aria-hidden='true'>&times;</span>
		</button>		
		</div>
		<script type='text/javascript'>
		$('.autoclose').delay(4000).slideUp(300, function() {
			$(this).alert('close');
		});	
		</script>
		";
	}
	
}
if ($islem=="mesaj_sayisi") {
	$gorusme_id=isset($_GET["gorusme_id"])?$_GET["gorusme_id"]:null;
	$mesaj_sayisi=mesaj_sayisi($gorusme_id);
	echo $mesaj_sayisi;
}
if ($islem=="mesajlari_getir") {
global $footer_kod;
$oturum_id=$_SESSION["oturum_id"];
$gorusme_id=isset($_GET["g"])?$_GET["g"]:null;
$sql=mysqli_query($baglan,"select * from mesajlar where gorusme_id='$gorusme_id' order by tarih_saat asc limit 0, 250");
while ($mesaj_dizi=mysqli_fetch_array($sql)) {
	$uye_id=$mesaj_dizi["uye_id"];
	$tarih_saat=$mesaj_dizi["tarih_saat"];
	$mesaj=$mesaj_dizi["mesaj"];
	if ($oturum_id==$uye_id) {
		$stil_yaz="right";
	} else {
		$stil_yaz="left";
	}
	$kod.="<div class='mesaj $stil_yaz' title='$tarih_saat'>$mesaj</div><div class='clearfix'></div>";
}
echo $kod;
}
if ($islem=="gorusme_sil") {
	$gorusme_id=isset($_POST["gorusme_id"])?$_POST["gorusme_id"]:null;
	$tip=isset($_POST["tip"])?$_POST["tip"]:null;
	$oturum_id=$_SESSION["oturum_id"];
	
	$gorusme_uye_1=getir("gorusmeler","uye_1",$gorusme_id);
	$gorusme_uye_2=getir("gorusmeler","uye_2",$gorusme_id);
	
	if ($gorusme_uye_1==$oturum_id) {
		guncelle("gorusmeler","sil_1",1,$gorusme_id);
	} else if ($gorusme_uye_2==$oturum_id) {
		guncelle("gorusmeler","sil_2",1,$gorusme_id);
	}
	
	mysqli_query($baglan,"update mesajlar set okundu='1' where gorusme_id='$gorusme_id' and kime='$oturum_id'");
	
	if ($tip==2) {
		$_SESSION["mesaj"]=array(_LANG('pn_gorusme_basariyla_sonlandirildi'),".");
	}
	
}
if ($islem=="uye_paket_bilgi") {
	$paket=isset($_GET["paket"])?$_GET["paket"]:null;
    
	$paket_adi=liste_bilgi("paket_adi",$paket);
	$paket_ucret=liste_bilgi("ucret",$paket);
	$avantajlar=liste_bilgi("avantajlar",$paket);
	if ($avantajlar) {
		$avantajlar_son.="<div id='paket_bilgi'><ul class='avantaj'>";
		$parcala=explode("\r\n",$avantajlar);
		foreach ($parcala as $avantaj) {
			if ($avantaj!="") {
				$avantajlar_son.="<li>$avantaj</li>";
			}
		}
		$avantajlar_son.="</ul></div>";
	}
	$paket_ilan_limit=liste_bilgi("ilan_limit",$paket);
	$paket_kategori_limit=liste_bilgi("kategori_limit",$paket);
	$paket_foto_limit=liste_bilgi("foto_limit",$paket);
	$paket_aninda_onay=liste_bilgi("aninda_onay",$paket);
	if ($paket_aninda_onay==1) {$paket_onay_yaz="Evet";} else {$paket_onay_yaz="Hayır";}
	
	if ($paket_ucret) {
		if (limit_politikasi()==2) {
			$fiyat_yaz="<small class='pull-right'>" . sprintf(_LANG('p_tek_seferlik_ucret'), $paket_ucret) . "</small>";
		} else {
			$fiyat_yaz="<small class='pull-right'>" . sprintf(_LANG('p_yillik_ucret'), $paket_ucret) . "</small>";
		}
	} else {$fiyat_yaz=null;}
    
    $ilan_basina_ucret=number_format($paket_ucret/$paket_ilan_limit,2);
    $ibu_explode=explode(".",$ilan_basina_ucret);
    if ($ibu_explode[1]=="00") {
        $ilan_basina_ucret=substr($ilan_basina_ucret,0,-3);
    } else {
        $ilan_basina_ucret=str_replace(".",",",$ilan_basina_ucret);
    }
    $kod.="
    <div id='magaza_bilgi' style='padding:0 0 10px 0;'>
    <div class='padding'>
        <h3>$paket_adi $fiyat_yaz</h3>
    </div>
    <ul>
        <li>Toplam İlan Kredisi: $paket_ilan_limit</li>
        <li>Aynı Kategorideki İlan Limiti: $paket_kategori_limit</li>
        <li>Fotoğraf Limiti: $paket_foto_limit</li>
        <li>Anında Onay: $paket_onay_yaz</li>
        <li>İlan Adet Ücreti: $ilan_basina_ucret GMD</li>
        <li style='font-size:20px;font-weight:700;color:#FDE82E;'>Ücret: $paket_ucret GMD</li>
    </ul>
    </div>
    $avantajlar_son
    ";
	echo $kod;
}
if ($islem=="magaza_bilgi") {
	$kategori=isset($_GET["kategori"])?$_GET["kategori"]:null;
	$kategori_adi=_DIL_BASLIK(getir("kategoriler","baslik",$kategori), $kategori, "kategori");
	$paket=isset($_GET["paket"])?$_GET["paket"]:null;
	$paket_adi=liste_bilgi("paket_adi",$paket);
	$paket_ucret=liste_bilgi("ucret",$paket);
	$avantajlar=liste_bilgi("avantajlar",$paket);
	
	$paket_kategori_ozel_fiyat=kategori_bilgi("magaza_paket_fiyat-$paket",$kategori);
	if ($paket_kategori_ozel_fiyat) {
		$paket_ucret=$paket_kategori_ozel_fiyat;
	}

	if ($avantajlar) {
		$avantajlar_son.="<div id='paket_bilgi'><ul class='avantaj'>";
		$parcala=explode("\r\n",$avantajlar);
		foreach ($parcala as $avantaj) {
			if ($avantaj!="") {
				$avantajlar_son.="<li>$avantaj</li>";
			}
		}
		$avantajlar_son.="</ul></div>";
	}
	$paket_ilan_limit=liste_bilgi("ilan_limit",$paket);
	$paket_kategori_limit=liste_bilgi("kategori_limit",$paket);
	$paket_foto_limit=liste_bilgi("foto_limit",$paket);
	$paket_danisman_limit=liste_bilgi("danisman_limit",$paket);
	$paket_aninda_onay=liste_bilgi("aninda_onay",$paket);
	if ($paket_aninda_onay==1) {$paket_onay_yaz="Evet";} else {$paket_onay_yaz="Hayır";}
	if ($paket_ucret) {
		if (limit_politikasi()==2) {
			$fiyat_yaz="<small class='pull-right'>" . sprintf(_LANG('p_tek_seferlik_ucret'), $paket_ucret) . "</small>";
		} else {
			$fiyat_yaz="<small class='pull-right'>" . sprintf(_LANG('p_yillik_ucret'), $paket_ucret) . "</small>";
		}
	} else {$fiyat_yaz=null;}
    $ilan_basina_ucret=number_format($paket_ucret/$paket_ilan_limit,2);
    $ibu_explode=explode(".",$ilan_basina_ucret);
    if ($ibu_explode[1]=="00") {
        $ilan_basina_ucret=substr($ilan_basina_ucret,0,-3);
    } else {
        $ilan_basina_ucret=str_replace(".",",",$ilan_basina_ucret);
    }
    
    $kod.="
	<div id='magaza_bilgi' style='padding:0 0 10px 0;'>
	<div class='padding'>
		<h1>$kategori_adi</h1>
		<h3>$paket_adi Mağaza $fiyat_yaz</h3>
	</div>
	<ul>
		<li>Toplam İlan Limiti: $paket_ilan_limit</li>
		<li>Fotoğraf Limiti: $paket_foto_limit</li>
		<li>Danışman Limiti: $paket_danisman_limit</li>
		<li>Anında Onay: $paket_onay_yaz</li>
        <li>İlan Adet Ücreti: $ilan_basina_ucret GMD</li>
        <li style='font-size:20px;font-weight:700;color:#FDE82E;'>Ücret: $paket_ucret GMD</li>
	</ul>
	</div>
	$avantajlar_son
	";
	echo $kod;
}
if ($islem=="odeme_bildirimi") {
	$hesap=isset($_POST["hesap"])?$_POST["hesap"]:null;
	$tanim=isset($_POST["tanim"])?$_POST["tanim"]:null;
	$ad_soyad=isset($_POST["gonderen"])?$_POST["gonderen"]:null;
	$uye_id=$_SESSION["oturum_id"];
	$mesaj=isset($_POST["mesaj"])?$_POST["mesaj"]:null;
	$tarih_saat=tarih_saat();
	$bildirim_ekle=mysqli_query($baglan,"insert into odemeler set ad_soyad='$ad_soyad', uye_id='$uye_id', hesap='$hesap', mesaj='$mesaj', tarih_saat='$tarih_saat', aciklama='$tanim'");
	
	if ($bildirim_ekle) {
	
	// E-posta gönderimi başladı
	$mail_baslik=tema_ayar("mail_baslik");
	$site_url=site_url();
	$lp_uye_ad_soyad=getir("uyeler","ad_soyad",$uye_id);
	$lp_link=site_url()."/$admin_dizini/temalar.php?tema=ilan&sayfa=panel&islem=odemeler";
	if (!$mesaj) {$ileti_mesaj=_LANG('tx_not_yazilmadi');} else {
		$ileti_mesaj=$mesaj;
	}
	$ileti_admin="
	<p>$lp_uye_ad_soyad adlı kullanıcı ödeme bildirimi gönderdi.</p>
	<p>Ödeme <strong>$ad_soyad</strong> adına, <strong>$hesap</strong> hesabına gönderildi.</p>
	<p>Açıklama: $tanim</p>
	<p>Kullanıcı notu: $ileti_mesaj</p>
	<p><a href=\"$lp_link\">Ödemeleri Görüntüle</a></p>
	";
	$site_email=ayar("site_email");
	$tarih_saat=tarih_saat();
	$ekle=mysqli_query($baglan,"insert into auto_mail set ad_soyad='$mail_baslik', email_adresi='$site_email', baslik='Yeni ödeme bildirimi: $lp_uye_ad_soyad', mesaj='$ileti_admin', tarih_saat='$tarih_saat', durum='0'");
	
	// E-posta gönderimi bitti
		echo "<div class='alert alert-success autoclose'>Ödeme bildiriminiz bize ulaştı. En kısa sürede işleminizi onaylayacağız.</div>";
	} else {
		echo "<div class='alert alert-danger autoclose'>Ödeme bildiriminiz teknik bir sorundan dolayı gönderilemedi.</div>";
	}
}
if ($islem=="video_onizleme") {
	$video_url=isset($_GET["video_url"])?$_GET["video_url"]:null;
	if ($video_url) {
		$video_id=youtube_video_id($video_url);
		if ($video_id) {
		echo "
		<label>" . _LANG('hb_video_onizleme') . "</label>
		<div class='embed-responsive embed-responsive-16by9 player mb15' itemprop='trailer'>
			<iframe class='embed-responsive-item' src='https://www.youtube.com/embed/$video_id?showinfo=0&amp;autoplay=0&amp;loop=1' frameborder='0' allowfullscreen></iframe>
		</div>
		";
		} else {
			echo _LANG('tx_gecerli_bir_video_url_si_giriniz');
		}
	}
}
if ($islem=="yayindan_kaldir") { 
	$hiddenid=isset($_POST["hiddenid"])?$_POST["hiddenid"]:null;
	$sebep=isset($_POST["sebep"])?$_POST["sebep"]:null;
	$ilan_sahibi=getir("icerik","yazar",$hiddenid);
	$oturum_id=$_SESSION["oturum_id"];
	if ($ilan_sahibi==$oturum_id) {
	guncelle("icerik","durum",3,$hiddenid);
	bilgi_guncelle("sebep",$sebep,$hiddenid);
	echo 1;
	} else {
	echo 2;
	}
}
if ($islem=="iyzico_form") {
	echo '
	<div id="iyzipay-checkout-form" class="responsive"></div>
	';
}
if ($islem=="sonraki_gorsel") {
	$galeri_id=isset($_POST["id"])?$_POST["id"]:null;
	$galeri_sira=getir("galeri","sira",$galeri_id);
	$resim_sayisi=resim_sayisi($id);
	$sonraki=mysqli_query($baglan,"select * from galeri where icerik_id='$id' and sira>$galeri_sira order by sira asc limit 0,1");
	$sonraki_dizi=mysqli_fetch_array($sonraki);
	$sonraki_id=$sonraki_dizi["id"];
	$sonraki_url=thumbnail_url("530_393",getir("galeri","resim",$sonraki_id));
}
if ($islem=="ajax_upload") {
	global $aktif_tema;
	$icerik_id=isset($_POST["icerik_id"])?$_POST["icerik_id"]:null;
	$file=$_FILES["files"];
	$resim_sayisi=count($file["name"]);
	$b=0;
	$watermark=tema_ayar("watermark");
	$orijinal_resim_optimize=tema_ayar("orijinal_resim_optimize");
	if ($watermark==1) {
		$watermark_logo=tema_ayar("watermark_logo");
	}
	$hata=0;
	//print_r($file);
	foreach ($file["name"] as $z) {
		$galeri_name=$z;
		$galeri_tmp_name=$file["tmp_name"][$b];
		if ($galeri_tmp_name) {
			$data = getimagesize($galeri_tmp_name);
			$width = $data[0];
			$height = $data[1];
			if ($width>=0 and $height>=0) {
				$yukle=array("name"=>$galeri_name,"tmp_name"=>$galeri_tmp_name);
				$yuklenen_resim=resim_yukle_ajax($yukle,"ajax",array("160_120","530_393"));
				// Resim yoksa güncelleme ile yüklenen ilk resim vitrin görseli yapılacak.
				$resim_sayisi=resim_sayisi($id);
				if ($resim_sayisi==0 and $b==0) {
					bilgi_guncelle("gorsel",$yuklenen_resim,$id);
				}
				// Vitrin görseli belirleme tamamlandı.
				if ($watermark==1) {
					$watermark_uygula=thumbnail_url("530_393",$yuklenen_resim);
					$uzanti=uzanti_ne($watermark_uygula);
					$bol=explode($uzanti,$watermark_uygula);
					$resim_adi=rtrim($bol[0],".");
					$yeni_resim="$resim_adi.$uzanti";
					$parcala=explode("uploads/",$yeni_resim);
					$yol=$parcala[1];
					include_once("class/SimpleImage.php");
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
					  $image->fromFile($watermark_uygula)->overlay($watermark_logo, "center center")->toFile("../../uploads/$yol", "image/$mime_type",90);
					} catch(Exception $err) {
					  // Handle errors
					  // echo $err->getMessage();
					}
				}
				if ($yuklenen_resim) {
					if ($icerik_id) {
						galeri_ekle($yuklenen_resim,$icerik_id);
						$gorsel_sor=bilgi("gorsel",$icerik_id);
						if (!$gorsel_sor) {
							bilgi_guncelle("gorsel",$yuklenen_resim,$icerik_id);
						}
					} else {
						$kat=isset($_POST["kat"])?$_POST["kat"]:null;
						$uye_id=isset($_POST["uye_id"])?$_POST["uye_id"]:null;
						$tarih_saat=tarih_saat();
						$resim_sayisi=mysqli_fetch_array(mysqli_query($baglan,"select COUNT(id) from ajax_upload where uye_id='$uye_id' and kat='$kat'"))[0];
						$yeni_sira=$resim_sayisi*10;
						$vitrin_sor=mysqli_num_rows(mysqli_query($baglan,"select * from ajax_upload where uye_id='$uye_id' and kat='$kat' and vitrin='1'"));
						if ($vitrin_sor==0) {
							$ekle=mysqli_query($baglan,"insert into ajax_upload set uye_id='$uye_id', kat='$kat', url='$yuklenen_resim', tarih_saat='$tarih_saat', vitrin='1', sira='$yeni_sira'");
						} else {
							$ekle=mysqli_query($baglan,"insert into ajax_upload set uye_id='$uye_id', kat='$kat', url='$yuklenen_resim', tarih_saat='$tarih_saat', sira='$yeni_sira'");
						}
					}
					echo $yuklenen_resim;
					if ($orijinal_resim_optimize==1) {
						$gorsel_max_genislik=tema_ayar("gorsel_max_genislik");
						$gorsel_max_yukseklik=tema_ayar("gorsel_max_yukseklik");
						if ($width>=$gorsel_max_genislik and $height>=$gorsel_max_yukseklik) {
							$orijinal_gorsel=$yuklenen_resim;
							$uzanti=uzanti_ne($orijinal_gorsel);
							$bol=explode($uzanti,$orijinal_gorsel);
							$resim_adi=rtrim($bol[0],".");
							$yeni_resim="$resim_adi.$uzanti";
							$parcala=explode("uploads/",$yeni_resim);
							$yol=$parcala[1];
							include_once("class/SimpleImage.php");
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
							  $image->fromFile($orijinal_gorsel)->autoOrient()->thumbnail($gorsel_max_genislik,$gorsel_max_yukseklik)->toFile("../../uploads/$yol", "image/$mime_type",90);
							} catch(Exception $err) {
							  // Handle errors
							  // echo $err->getMessage();
							}
						}
					} else {
						$orijinal_gorsel=$yuklenen_resim;
						$uzanti=uzanti_ne($orijinal_gorsel);
						$bol=explode($uzanti,$orijinal_gorsel);
						$resim_adi=rtrim($bol[0],".");
						$yeni_resim="$resim_adi.$uzanti";
						$parcala=explode("uploads/",$yeni_resim);
						$yol=$parcala[1];
						include_once("class/SimpleImage.php");
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
						  $image->fromFile($orijinal_gorsel)->autoOrient()->toFile("../../uploads/$yol", "image/$mime_type",90);
						} catch(Exception $err) {
						  // Handle errors
						  // echo $err->getMessage();
						}		
					}
				}
				$b++;
			} else {
				echo "Hata";
			}
		}
	}
}
if ($islem=="resmi_dondur") {

	$url=isset($_POST["url"])?$_POST["url"]:null;
	$kat=isset($_POST["kat"])?$_POST["kat"]:null;
	$icerik_id=isset($_POST["icerik_id"])?$_POST["icerik_id"]:null;

	$full=thumbnail_url("full",$url,"160_120");

	$uzanti=uzanti_ne($full);
	$bol=explode($uzanti,$full);
	$resim_adi=rtrim($bol[0],".");
	$yeni_resim="$resim_adi.$uzanti";
	$parcala=explode("uploads/",$full);
	$yol=$parcala[1];

	include_once("class/SimpleImage.php");
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
		$image->fromFile($full)->rotate(-90)->toFile("../../uploads/$yol", "image/$mime_type",90);
	} catch(Exception $err) {
		// Handle errors
		// echo $err->getMessage();
	}

	ajax_sil(thumbnail_url("160_120",$full));
	ajax_sil(thumbnail_url("530_393",$full));
	
	$cikti=resim_boyutlandir_ajax($full,"ajax",array("160_120","530_393"),"160_120");
	ajax_sil($full);
	echo $cikti;
}
if ($islem=="ilan_gorselleri_getir") {
	$id=isset($_GET["ilan_id"])?$_GET["ilan_id"]:null;
	$galeri_sql=mysqli_query($baglan,"select * from galeri where icerik_id='$id'");
	$resim_sayisi=mysqli_num_rows($galeri_sql);
	if ($resim_sayisi>0) {
	$vitrin_gorseli=thumbnail("530_393",$id);
	if ($vitrin_gorseli) {
	?>
	<label><?php echo _LANG('hb_vitrin_gorseli'); ?></label>
	<img id="vitrin_gorseli" src="<?php echo $vitrin_gorseli; ?>" alt="" class="img-responsive noselect"/>
	<div class="clearfix"></div>
	<?php } else { ?>
	<img id="vitrin_gorseli" alt="" class="img-responsive noselect" style="display:none;"/>
	<?php } ?>	
	<label><?php echo _LANG('hb_yuklenen_fotograflar'); ?></label>
	<div class="clearfix"></div>
	<?php 
	$yuklu_resim=1;
	while ($galeri_dizi=mysqli_fetch_array($galeri_sql)) {
		$galeri_id=$galeri_dizi["id"];
		$galeri_resim=$galeri_dizi["resim"];
		$galeri_kucuk_resim=thumbnail_url("160_120",$galeri_resim);
		if ($yuklu_resim%2==0) {
			$margin_right_hesapla="style='margin-right:0 !important;'";
		} else {
			$margin_right_hesapla=null;
		}
		$vitrin=bilgi("gorsel",$id);
		if ($galeri_resim==$vitrin) {
			$vitrin_mi="vitrin";
		} else {
			$vitrin_mi=null;
		}
		echo "
		<div class='yuklu_resim noselect' $margin_right_hesapla id='resim-$galeri_id'>
			<div class='panel panel-default limon with_footer' style='border-radius:3px;overflow:hidden;'>
				<img src='$galeri_kucuk_resim' alt='' class='img-responsive'/>
				<div class='panel-footer' style='padding:0 !important;border:0;'>
					<div class='row'>
						<div class='col-md-29' style='padding-right:0;'><a href='#' id='vitrin_sec' data_url='$galeri_resim' class='btn btn-default btn-xs btn-block text-center vitrin_buton $vitrin_mi'><i class='ion-image'></i>Vitrin</a></div>
						<div class='col-md-13' style='padding-left:0;'><a href='#' id='foto_sil' data_url='$galeri_resim' data_id='$galeri_id' class='btn btn-default btn-xs btn-block text-center'><i class='ion-trash-b mr0'></i></a></div>
					</div>
					<div class='clearfix'></div>
				</div>
			</div>
		</div>";
	$yuklu_resim++;}
	
	}  // İlan düzenleme sayfası ise
}
if ($islem=="ajax_upload_getir") {
	$uye_id=isset($_GET["uye_id"])?$_GET["uye_id"]:null;
	$kat=isset($_GET["kat"])?$_GET["kat"]:null;
	$galeri_sql=mysqli_query($baglan,"select * from ajax_upload where uye_id='$uye_id' and kat='$kat'");
	$resim_sayisi=mysqli_num_rows($galeri_sql);
	if ($resim_sayisi>0) {
	$vitrin_gorseli=mysqli_fetch_array(mysqli_query($baglan,"select * from ajax_upload where uye_id='$uye_id' and kat='$kat' and vitrin='1'"))["url"];
	if ($vitrin_gorseli) {
	?>
	<label><?php echo _LANG('hb_vitrin_gorseli'); ?></label>
	<img id="vitrin_gorseli" src="<?php echo $vitrin_gorseli; ?>" alt="" class="img-responsive noselect"/>
	<div class="clearfix"></div>
	<?php } else { ?>
	<img id="vitrin_gorseli" alt="" class="img-responsive noselect" style="display:none;"/>
	<?php } ?>
	<label><?php echo _LANG('hb_yuklenen_fotograflar'); ?></label>
	<div class="clearfix"></div>
	<?php 
	$yuklu_resim=1;
	while ($galeri_dizi=mysqli_fetch_array($galeri_sql)) {
		$galeri_id=$galeri_dizi["id"];
		$galeri_resim=$galeri_dizi["url"];
		$galeri_kucuk_resim=thumbnail_url("160_120",$galeri_resim);
		if ($yuklu_resim%2==0) {
			$margin_right_hesapla="style='margin-right:0 !important;'";
		} else {
			$margin_right_hesapla=null;
		}
		$vitrin=bilgi("gorsel",$id);
		if ($galeri_resim==$vitrin) {
			$vitrin_mi="vitrin";
		} else {
			$vitrin_mi=null;
		}
		echo "
		<div class='yuklu_resim noselect' $margin_right_hesapla id='resim-$galeri_id'>
			<div class='panel panel-default limon with_footer' style='border-radius:3px;overflow:hidden;'>
				<img src='$galeri_kucuk_resim' alt='' class='img-responsive'/>
				<div class='panel-footer' style='padding:0 !important;border:0;'>
					<div class='row'>
						<div class='col-md-29' style='padding-right:0;'><a href='#' id='vitrin_sec' data_url='$galeri_resim' class='btn btn-default btn-xs btn-block text-center vitrin_buton $vitrin_mi'><i class='ion-image'></i>Vitrin</a></div>
						<div class='col-md-13' style='padding-left:0;'><a href='#' id='foto_sil' data_url='$galeri_resim' data_id='$galeri_id' class='btn btn-default btn-xs btn-block text-center'><i class='ion-trash-b mr0'></i></a></div>
					</div>
					<div class='clearfix'></div>
				</div>
			</div>
		</div>";
	$yuklu_resim++;}
	
	}  // İlan düzenleme sayfası ise
}
if ($islem=="resim_limitleri") {
	$magaza_id=isset($_GET["magaza_id"])?$_GET["magaza_id"]:null;
	$uye_id=isset($_GET["uye_id"])?$_GET["uye_id"]:null;
	$kat=isset($_GET["kat"])?$_GET["kat"]:null;
	$ilan_id=isset($_GET["ilan_id"])?$_GET["ilan_id"]:null;
	if ($magaza_id) {
		$foto_limit=magaza_ilan_limiti("foto",$magaza_id);
	} else {
		$foto_limit=uye_ilan_limiti("foto",$uye_id);
	}
	if ($ilan_id) {
	$mevcut_resim_sayisi=resim_sayisi($ilan_id);
	$kalan_resim_limiti=$foto_limit-$mevcut_resim_sayisi;
	$vitrin_gorseli=bilgi("gorsel");
	} else {
	$kalan_resim_limiti=$foto_limit;	
	}
	?>
	<ul class="bilgi">
		<?php if ($ilan_id) { ?>
			<li>İlana yüklediğiniz toplam resim: <?php echo $mevcut_resim_sayisi; ?></li>
			<li>Kalan fotoğraf yükleme hakkı: <?php echo $kalan_resim_limiti; ?></li>
		<?php } else { ?>
			<li>Toplam fotoğraf limiti: <?php echo $foto_limit; ?></li>
		<?php } ?>
	</ul>
	<?php
}
if ($islem=="ajax_remove") {
	@$data=isset($_POST["file"])?$_POST["file"]:null;
	@$name=isset($_POST["name"])?$_POST["name"]:null;

	$ilan_id=isset($_GET["ilan_id"])?$_GET["ilan_id"]:null;
	$uye_id=$_SESSION["oturum_id"];
	$galeri_id=$data["galeri_id"];

	if ($galeri_id) {
		$file=getir("galeri","resim",$galeri_id);
	} else {
		$file=site_url()."/uploads/ajax/$name";
	}
	
	echo $file;
	
	// Galeri sorulacak.
	if ($ilan_id) {
		ajax_sil(thumbnail_url("160_120",$file));
		ajax_sil(thumbnail_url("530_393",$file));
		ajax_sil($file);
		$galeri_remove_sql=mysqli_query($baglan,"delete from galeri where icerik_id='$ilan_id' and resim='$file'");
		$ilan_gorseli=bilgi("gorsel",$ilan_id);
		if ($file==$ilan_gorseli) {
			bilgi_guncelle("gorsel",null,$ilan_id);
			$galeri_sql=mysqli_query($baglan,"select * from galeri where icerik_id='$ilan_id' order by sira asc limit 1");
			$galeri_sayisi=mysqli_num_rows($galeri_sql);
			if ($galeri_sayisi>0) {
				$galeri_ilk_resim=mysqli_fetch_array($galeri_sql)["resim"];
				bilgi_guncelle("gorsel",$galeri_ilk_resim,$ilan_id);
			}
		}
	} else {
		ajax_sil(thumbnail_url("160_120",$file));
		ajax_sil(thumbnail_url("530_393",$file));
		ajax_sil($file);
		$ajax_remove_sql=mysqli_query($baglan,"delete from ajax_upload where url='$file' and uye_id='$uye_id'");	
		if ($ajax_remove_sql) {
			echo "resim silindi.";
		}
	}
	
}
if ($islem=="ajax_remove_new") {
	@$icerik_id=isset($_POST["icerik_id"])?$_POST["icerik_id"]:null;
	@$url=isset($_POST["url"])?$_POST["url"]:null;
	@$kat=isset($_POST["kat"])?$_POST["kat"]:null;

	$uye_id=$_SESSION["oturum_id"];
	$galeri_id=$data["galeri_id"];

	$full_url=thumbnail_url("full",$url,"160_120");

	// Galeri sorulacak.
	if ($icerik_id) {
		ajax_sil(thumbnail_url("160_120",$full_url));
		ajax_sil(thumbnail_url("530_393",$full_url));
		ajax_sil($full_url);
		$galeri_remove_sql=mysqli_query($baglan,"delete from galeri where icerik_id='$icerik_id' and resim='$full_url'");
		$ilan_gorseli=bilgi("gorsel",$icerik_id);
		if ($full_url==$ilan_gorseli) {
			bilgi_guncelle("gorsel",null,$icerik_id);
			$galeri_sql=mysqli_query($baglan,"select * from galeri where icerik_id='$icerik_id' order by sira asc limit 1");
			$galeri_sayisi=mysqli_num_rows($galeri_sql);
			if ($galeri_sayisi>0) {
				$galeri_ilk_resim=mysqli_fetch_array($galeri_sql)["resim"];
				bilgi_guncelle("gorsel",$galeri_ilk_resim,$icerik_id);
			}
		}
	} else {
		ajax_sil(thumbnail_url("160_120",$full_url));
		ajax_sil(thumbnail_url("530_393",$full_url));
		ajax_sil($full_url);
		$ajax_remove_sql=mysqli_query($baglan,"delete from ajax_upload where url='$full_url' and uye_id='$uye_id' and kat='$kat'");
		$vitrin_sor=mysqli_fetch_array(mysqli_query($baglan,"select COUNT(id) from ajax_upload where vitrin='1' and uye_id='$uye_id' and kat='$kat'"))[0];
		if ($vitrin_sor==0) {
			mysqli_query($baglan,"update ajax_upload set vitrin='1' where uye_id='$uye_id' and kat='$kat' order by sira asc limit 1");
		}
	}
}
if ($islem=="vitrin_yap") {
	$url=isset($_POST["url"])?$_POST["url"]:null;
	$ilan_id=isset($_POST["icerik_id"])?$_POST["icerik_id"]:null;
	$kat=isset($_POST["kat"])?$_POST["kat"]:null;
	$uye_id=$_SESSION["oturum_id"];
	$tam_boyut=thumbnail_url("full",$url,"160_120");
	if ($ilan_id) {
		$guncelle=bilgi_guncelle("gorsel",$tam_boyut,$ilan_id);
		galeri_sira_update($tam_boyut,$ilan_id);
		if ($guncelle and $guncelle2) {
			echo true;
		} else {
			echo false;
		}
	} else {
		$vitrin_sor=mysqli_query($baglan,"select * from ajax_upload where kat='$kat' and uye_id='$uye_id' and vitrin='1'");
		$vitrin_say=mysqli_num_rows($vitrin_sor);
		if ($vitrin_say==1) {
			$guncelle=mysqli_query($baglan,"update ajax_upload set vitrin='0' where kat='$kat' and uye_id='$uye_id'");
			$guncelle2=mysqli_query($baglan,"update ajax_upload set vitrin='1' where kat='$kat' and uye_id='$uye_id' and url='$tam_boyut'");
			if ($guncelle and $guncelle2) {
				echo true;
			} else {
				echo false;
			}
		} else {
			$guncelle2=mysqli_query($baglan,"update ajax_upload set vitrin='1' where kat='$kat' and uye_id='$uye_id' and url='$tam_boyut'");
			if ($guncelle2) {
				echo true;
			} else {
				echo false;
			}			
		}
	}
}
if ($islem=="galeri_sira_update") {
	$icerik_id=isset($_POST["icerik_id"])?$_POST["icerik_id"]:null;
	$kat=isset($_POST["kat"])?$_POST["kat"]:null;
	$positions=isset($_POST["positions"])?$_POST["positions"]:null;
	$uye_id=$_SESSION["oturum_id"];
	
	$positions_bol=explode(";",$positions);
	$x=1;
	foreach ($positions_bol as $tek_resim) {
		$tek_resim_parcala=explode("=",$tek_resim);
		$tek_resim_url=$tek_resim_parcala[0];
		$tek_resim_sira=$tek_resim_parcala[1]*10;
		$tek_resim_full_url=thumbnail_url("full",$tek_resim_url,"160_120");
		if ($icerik_id) {
			$guncelle=mysqli_query($baglan,"update galeri set sira='$tek_resim_sira' where resim='$tek_resim_full_url' and icerik_id='$icerik_id'");
			if ($x==1) {
				bilgi_guncelle("gorsel",$tek_resim_full_url,$icerik_id);
			}		
			if ($guncelle) {echo _LANG('tx_sira_guncellendi');}
		} else {
			if ($x==1) {
				$guncelle=mysqli_query($baglan,"update ajax_upload set sira='$tek_resim_sira', vitrin='1' where url='$tek_resim_full_url' and uye_id='$uye_id' and kat='$kat'");
			} else {
				$guncelle=mysqli_query($baglan,"update ajax_upload set sira='$tek_resim_sira', vitrin='0' where url='$tek_resim_full_url' and uye_id='$uye_id' and kat='$kat'");
			}
			if ($guncelle) {echo _LANG('tx_sira_guncellendi');}
		}
	$x++;}

}
if ($islem=="thumbnail_url") {
	$url=isset($_POST["url"])?$_POST["url"]:null;
	echo thumbnail_url("160_120",$url);
}
if ($islem=="il_ilce_mahalle_kaydet") {
	$il=isset($_POST["il"])?$_POST["il"]:null;
	$ilce=isset($_POST["ilce"])?$_POST["ilce"]:null;
	$mahalle=isset($_POST["mahalle"])?$_POST["mahalle"]:null;
	$uye_id=$_SESSION["oturum_id"];
	if (!$il) {
		$il=getir("ilceler","il_id",$ilce);
	}
	uye_bilgi_guncelle("il_ilce_hazir","$il:$ilce",$uye_id);
}
if ($islem=="il_ilce_mahalle_unut") {
	$uye_id=$_SESSION["oturum_id"];
	uye_bilgi_guncelle("il_ilce_hazir",null,$uye_id);
}
if ($islem=="galeri_sira_bilgi") {
	$galeri_id=isset($_POST["galeri_id"])?$_POST["galeri_id"]:null;
	$icerik_id=isset($_POST["icerik_id"])?$_POST["icerik_id"]:null;
	$onceki_galeri_id=onceki_galeri_id($galeri_id,$icerik_id);
	$sonraki_galeri_id=sonraki_galeri_id($galeri_id,$icerik_id);
	/* echo "önceki id: $onceki_galeri_id<br />";
	echo "galeri id: $galeri_id<br />";
	echo "sonraki id: $sonraki_galeri_id<br />"; */
	?>
	<script type="text/javascript">
	<?php if ($onceki_galeri_id) {$onceki_galeri_url=thumbnail_url("530_393",getir("galeri","resim",$onceki_galeri_id)); ?>
		$("#onceki").fadeIn(250);
		$("#onceki").attr("data-id","<?php echo $onceki_galeri_id; ?>");
		$("#onceki").attr("data-url","<?php echo $onceki_galeri_url; ?>");
		$("body").on("click","#onceki",function(){
			var url=$(this).attr("data-url");
			var resim_id=$(this).attr("data-id");
			$("#genis_gorsel").attr("src",url);
			$("#mega_foto").attr("data-id",resim_id);
			$(".galeri_gorsel.active").removeClass("active");
			$("#resim_"+resim_id).addClass("active");
		});
		
	<?php } else { ?>
		$("#onceki").fadeOut(250);
	<?php } ?>
	<?php if ($sonraki_galeri_id) {$sonraki_galeri_url=thumbnail_url("530_393",getir("galeri","resim",$sonraki_galeri_id)); ?>
		$("#sonraki").fadeIn(250);
		$("#sonraki").attr("data-id","<?php echo $sonraki_galeri_id; ?>");
		$("#sonraki").attr("data-url","<?php echo $sonraki_galeri_url; ?>");
		$("body").on("click","#sonraki",function(){
			var url=$(this).attr("data-url");
			var resim_id=$(this).attr("data-id");
			$("#genis_gorsel").attr("src",url);
			$("#mega_foto").attr("data-id",resim_id);
			$(".galeri_gorsel.active").removeClass("active");
			$("#resim_"+resim_id).addClass("active");
		});
	<?php } else { ?>
		$("#sonraki").fadeOut(250);
	<?php } ?>
	</script>
	<?php 
}

?>