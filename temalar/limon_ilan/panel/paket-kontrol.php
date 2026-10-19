<?php 
if(count(get_included_files())==1) {die();}
$tarih_saat=date("Y-m-d H:i:s");
$uye_paket_suresi_bitmis_sql=mysqli_query($baglan,"SELECT u.id, e.deger from uyeler as u INNER JOIN ekstra_bilgi as e ON u.id=e.id and e.tip='uye' and e.anahtar='paket_bitis_tarihi' and e.deger<='$tarih_saat'");
$uye_paket_suresi_bitmis_say=mysqli_num_rows($uye_paket_suresi_bitmis_sql);

$magaza_paket_suresi_bitmis_sql=mysqli_query($baglan,"SELECT m.id, e.deger from magazalar as m INNER JOIN ekstra_bilgi as e ON m.id=e.id and e.tip='magaza' and e.anahtar='paket_bitis_tarihi' and e.deger<='$tarih_saat' where m.durum='1'");
$magaza_paket_suresi_bitmis_say=mysqli_num_rows($magaza_paket_suresi_bitmis_sql);

@$uye_buton=$_POST["uye_buton"];
@$magaza_buton=$_POST["magaza_buton"];
@$uye_tanimla_buton=$_POST["uye_tanimla_buton"];
@$magaza_tanimla_buton=$_POST["magaza_tanimla_buton"];
if ($uye_buton) {
	if ($uye_paket_suresi_bitmis_say>0) {
		$standart_paket=tema_ayar("standart_paket");
		$x=0;
		while ($uye_paket_update_dizi=mysqli_fetch_array($uye_paket_suresi_bitmis_sql)) {
			$uye_id=$uye_paket_update_dizi["id"];
			if ($standart_paket) {
				uye_bilgi_guncelle("paket",$standart_paket,$uye_id);
				$bitis_tarihi=date("Y-m-d H:i:s",strtotime("+1 year"));
				uye_bilgi_guncelle("paket_bitis_tarihi",$bitis_tarihi,$uye_id);
			}
		$x++;}
		if ($x>0) {
			if ($x==1) {
				$ad_soyad=getir("uyeler","ad_soyad",$uye_id);
				if (function_exists('lpanel_uye_profil_link')) {
					$link=lpanel_uye_profil_url($uye_id);
					bildirim_ekle(_LANG("pn_i_class_fa_fa_sticky_note_o_fa_fw_i_a_href_link_ad_soyad_a_adli_uyenin", array($link, $ad_soyad)),null,0);
				} else {
					bildirim_ekle(sprintf(_LANG('bnf_uye_paketi_doldu'), $ad_soyad),null,0);
				}
			} else {
				bildirim_ekle(sprintf(_LANG('bnf_uyelerin_paketi_doldu'), $x),null,0);
			}
		}
	}
	$_SESSION["mesaj"]=array(_LANG("pn_islem_basarili"),"success");
	yonlendir("temalar.php?tema=$aktif_tema&sayfa=panel&islem=paket-kontrol");
}
if ($magaza_buton) {
	if ($magaza_paket_suresi_bitmis_say>0) {
		$x=0;
		while ($magaza_paket_update_dizi=mysqli_fetch_array($magaza_paket_suresi_bitmis_sql)) {
			$magaza_id=$magaza_paket_update_dizi["id"];
			$magaza_pasiflestir=guncelle("magazalar","durum",2,$magaza_id);
		$x++;}
		if ($x>0) {
			if ($x==1) {
				$magaza_baslik=getir("magazalar","baslik",$magaza_id);
				bildirim_ekle(sprintf(_LANG('bnf_magaza_pasiflestirildi'), $magaza_baslik),null,0);
			} else {
				bildirim_ekle(sprintf(_LANG('bnf_magazalar_pasiflestirildi'), $x),null,0);
			}
		}		
	}
	$_SESSION["mesaj"]=array(_LANG("pn_islem_basarili"),"success");
	yonlendir("temalar.php?tema=$aktif_tema&sayfa=panel&islem=paket-kontrol");
}
if ($uye_tanimla_buton) {
	$tum_uyeler_sql=mysqli_query($baglan,"select * from uyeler");
	$x=0;
	while ($tum_uyeler_dizi=mysqli_fetch_array($tum_uyeler_sql)) {
		$uye_id=$tum_uyeler_dizi["id"];
		$uye_paket_bitis=uye_bilgi("paket_bitis_tarihi",$uye_id);
		if (!$uye_paket_bitis) {
			$uye_tarih_saat=$tum_uyeler_dizi["tarih_saat"];
			$bitis_tarihi=strtotime('+1 year',strtotime($uye_tarih_saat));
			$bitis_tarihi=date('Y-m-d H:i:s' ,$bitis_tarihi);
			uye_bilgi_guncelle("paket_bitis_tarihi",$bitis_tarihi,$uye_id);
		$x++;}
	}
	bildirim_ekle(sprintf(_LANG('bnf_uye_paket_bitis_tanimlandi'), $x),null,0);
	tema_ayar_guncelle("uye_paket_bitis_sureleri",1);
	yonlendir("temalar.php?tema=$aktif_tema&sayfa=panel&islem=paket-kontrol");
}
if ($magaza_tanimla_buton) {
	$tum_magazalar_sql=mysqli_query($baglan,"select * from magazalar");
	$x=0;
	while ($tum_magazalar_dizi=mysqli_fetch_array($tum_magazalar_sql)) {
		$magaza_id=$tum_magazalar_dizi["id"];
		$magaza_paket_bitis=magaza_bilgi("paket_bitis_tarihi",$magaza_id);
		if (!$magaza_paket_bitis) {
			$magaza_tarih_saat=$tum_magazalar_dizi["tarih_saat"];
			$bitis_tarihi=strtotime('+1 year',strtotime($magaza_tarih_saat));
			$bitis_tarihi=date('Y-m-d H:i:s' ,$bitis_tarihi );
			magaza_bilgi_guncelle("paket_bitis_tarihi",$bitis_tarihi,$magaza_id);
		$x++;}
	}
	bildirim_ekle(sprintf(_LANG('bnf_magaza_paket_bitis_tanimlandi'), $x),null,0);
	tema_ayar_guncelle("magaza_bitis_sureleri",1);
	yonlendir("temalar.php?tema=$aktif_tema&sayfa=panel&islem=paket-kontrol");
}
?>

<?php 
$limit_politikasi=limit_politikasi();
if ($limit_politikasi==2) {
    echo "<h4>BU İŞLEV KULLANILAMAZ</h4><p>Azalan Limit Politikası kurallarına göre üyelik ve mağaza paketlerinde süre kısıtlaması bulunmamaktadır. İlan hakkı bulunan kullanıcılarınız ve mağazalar süre kısıtlamasız ilan ekleyebilirler.</p>";
    exit();
}

?>

<div class="row">
	<div class="col-md-30">
		<div class="panel panel-default">
		<div class="panel-heading"><i class="fa fa-user"></i>PAKET SÜRESİ DOLAN ÜYELER</div>
		<div class="panel-body">
		<?php if ($uye_paket_suresi_bitmis_say>0) { ?>
		<p>Paket süresi dolan <?php echo $uye_paket_suresi_bitmis_say; ?> üye var.</p>
		<div class="well">
		<?php 
		while ($uye_paket_suresi_bitmis_dizi=mysqli_fetch_array($uye_paket_suresi_bitmis_sql)) {
			$uye_id=$uye_paket_suresi_bitmis_dizi["id"];
			$uye_ad_soyad=getir("uyeler","ad_soyad",$uye_id);
			$uye_link=profil_link_getir($uye_id);
			echo "<a href='$uye_link' target='_blank'>$uye_ad_soyad</a><a href='uyeler.php?islem=guncelle&id=$uye_id' target='_blank' style='margin-left:5px;'><i class='fa fa-edit'></i></a><br />";
		}
		?>
		</div>		
		<form action="" method="post">
		<button><?php echo _LANG('hb_suresi_dolan_uyeleri_standart_pakete_dusur'); ?></button>
		</form>
		<?php } else { ?>
		Paket süresi dolan hiç üye yok.
		<?php } ?>
		</div>
		</div>
		<div class="panel panel-default">
		<div class="panel-heading"><i class="fa fa-shopping-bag"></i>PAKET SÜRESİ DOLAN MAĞAZALAR</div>
		<div class="panel-body">
		<?php if ($magaza_paket_suresi_bitmis_say>0) { ?>
		<p>Paket süresi dolan <?php echo $magaza_paket_suresi_bitmis_say; ?> mağaza var.</p>
		<div class="well">
		<?php 
		while ($magaza_paket_suresi_bitmis_dizi=mysqli_fetch_array($magaza_paket_suresi_bitmis_sql)) {
			$magaza_id=$magaza_paket_suresi_bitmis_dizi["id"];
			$magaza_link=magaza_link_getir($magaza_id);
			$magaza_baslik=getir("magazalar","baslik",$magaza_id);
			echo "<a href='$magaza_link' target='_blank'>$magaza_baslik</a><a href='temalar.php?tema=$aktif_tema&sayfa=panel&islem=magaza-guncelle&id=$magaza_id' target='_blank' style='margin-left:5px;'><i class='fa fa-edit'></i></a><br />";
		}
		?>
		</div>
		<form action="" method="post">
		<button><?php echo _LANG('hb_suresi_dolan_magazalari_pasiflestir'); ?></button>
		</form>
		<?php } else { ?>
		Paket süresi dolan hiç mağaza yok.
		<?php } ?>
		</div>
		</div>	
		<?php $uye_paket_bitis_sureleri=tema_ayar("uye_paket_bitis_sureleri"); if ($uye_paket_bitis_sureleri!=1) { ?>
		<div class="panel panel-default">
		<div><?php echo _LANG('hb_uye_paket_bitis_surelerini_tanimla'); ?></div>
		<div class="panel-body">
		<p><?php echo _LANG('hb_paket_bitis_sureleri_tanimlanmayan_uyeler_mevc'); ?></p>
		<form action="" method="post">
		<button><?php echo _LANG('hb_paket_bitis_surelerini_tanimla'); ?></button>
		</form>
		</div>
		</div>	
		<?php } ?>	
		<?php $magaza_bitis_sureleri=tema_ayar("magaza_bitis_sureleri"); if ($magaza_bitis_sureleri!=1) { ?>
		<div class="panel panel-default">
		<div><?php echo _LANG('hb_magaza_bitis_surelerini_tanimla'); ?></div>
		<div class="panel-body">
		<p><?php echo _LANG('hb_paket_bitis_sureleri_tanimlanmayan_magazalar_m'); ?></p>
		<form action="" method="post">
		<button><?php echo _LANG('hb_paket_bitis_surelerini_tanimla'); ?></button>
		</form>
		</div>
		</div>	
		<?php } ?>
	</div>
</div>

