<?php 
if(count(get_included_files())==1) {die();}
$tema_destegi=1;
$eklentiler_dizini="eklentiler";
$temalar_dizini="temalar";
$default_tema="limon_ilan";
$lpanel_baslik=_LANG('p_lpanel_baslik');
$lpanel_ust_baslik=_LANG('p_lpanel_ust_baslik');
$resize_kalite=90;
$lpanel_genel_durum=1;
$lpanel_duyurular=1;
$tarih_saat_aylar=implode(",",array(_LANG("ay_1"),_LANG("ay_2"),_LANG("ay_3"),_LANG("ay_4"),_LANG("ay_5"),_LANG("ay_6"),_LANG("ay_7"),_LANG("ay_8"),_LANG("ay_9"),_LANG("ay_10"),_LANG("ay_11"),_LANG("ay_12")));
// Null
$bilesen_alani=array();
$liste=array();
$tema_menuleri=array();
// İçerik tipleri. Standart içerik tipi şablonu aşağıdaki gibidir
$icerik_tipleri["yazi"]=array(
"id"=>"yazi",
"baslik"=>_LANG('y_yazi'),
"baslik_cogul"=>_LANG('y_yazilar'),
"baslik_isaret"=>_LANG('y_yaziyi'),
"ikon"=>"pencil",
"gorsel"=>"1",
"galeri"=>"1",
"kategori"=>"1",
"etiket"=>"0",
"icerik"=>"1",
"url"=>"1",
"yazar"=>"1",
"ekle_buton"=>"1",
"tarih_saat_detayi"=>"1",
"durum_detayi"=>"1",
"yazar_detayi"=>"1",
"gizli"=>"0", // Varsayılan 0. Siparişler, Sliderlar gibi yalnızca yöneticiye özel bir içerik tipiyse 1 olmalı.
);
$icerik_tipleri["sayfa"]=array(
"id"=>"sayfa",
"baslik"=>_LANG('y_sayfa'),
"baslik_cogul"=>_LANG('y_sayfalar'),
"baslik_isaret"=>_LANG('y_sayfayi'),
"ikon"=>"pencil",
"gorsel"=>"0",
"galeri"=>"1",
"kategori"=>"0",
"icerik"=>"1",
"url"=>"1",
"etiket"=>"0",
"ekle_buton"=>"1",
);
// Site Ayarları
$acikkapali=array(
array("deger"=>"0","baslik"=>_LANG('y_kapali')),
array("deger"=>"1","baslik"=>_LANG('y_acik')),
);
$panelsablonu=array(
array("deger"=>"fullwidth","baslik"=>_LANG('y_fullwidth')),
array("deger"=>"boxed","baslik"=>_LANG('y_boxed')),
);
$site_ayarlari=array(
array("tip"=>"tab_ac","id"=>"genel_ayarlar","baslik"=>_LANG('y_genel_ayarlar')),
array("tip"=>"text","id"=>"site_url","baslik"=>_LANG('y_site_url')),
array("tip"=>"text","id"=>"site_email","baslik"=>_LANG('y_yonetici_eposta'),"aciklama"=>_LANG('ac_site_email')),
array("tip"=>"select","id"=>"bakim_modu","baslik"=>_LANG('y_bakim_modu'),"secenekler"=>$acikkapali,"aciklama"=>_LANG('ac_bakim_modu')),
array("tip"=>"select","id"=>"sistem_guncellemeleri","baslik"=>_LANG('y_guncelleme_servisi'),"secenekler"=>$acikkapali,"aciklama"=>_LANG('ac_sistem_guncellemeleri')),
array("tip"=>"tab_kapat"),
array("tip"=>"tab_ac","id"=>"baslik_ayarlari","baslik"=>_LANG('y_baslik_ayarlari')),
array("tip"=>"text","id"=>"site_baslik","baslik"=>_LANG('y_site_baslik'),"aciklama"=>_LANG('ac_site_baslik')),
array("tip"=>"text","id"=>"site_aciklama","baslik"=>_LANG('y_site_aciklama'),"aciklama"=>_LANG('ac_site_aciklama')),
array("tip"=>"text","id"=>"basliga_ekle","baslik"=>_LANG('y_basliga_ekle'),"aciklama"=>_LANG('ac_basliga_ekle')),
array("tip"=>"tab_kapat"),
array("tip"=>"tab_ac","id"=>"yorum_ayarlari","baslik"=>_LANG('y_yorum_denetimi')),
array("tip"=>"textarea","id"=>"yasakli_kelimeler","baslik"=>_LANG('yasakli_kelimeler'),"satir"=>5,"aciklama"=>_LANG('ac_yasakli_kelimeler')),
array("tip"=>"textarea","id"=>"yasakli_kelimeler_duzelt","baslik"=>_LANG('yasakli_kelimeler_duzelt'),"satir"=>5,"aciklama"=>_LANG('ac_yasakli_kelimeler_duzelt')),
array("tip"=>"tab_kapat"),
array("tip"=>"tab_ac","id"=>"panel_sablonu","baslik"=>_LANG('y_panel_ayarlari')),
array("tip"=>"text","id"=>"admin_dizini","baslik"=>_LANG('y_admin_dizini'),"standart"=>"lpanel","aciklama"=>_LANG('ac_admin_dizini')),
array("tip"=>"color","id"=>"panel_renk","baslik"=>_LANG('y_panel_renk'),"standart"=>"#505659"),
array("tip"=>"select","id"=>"bildirimler","baslik"=>_LANG('y_bildirimler'),"secenekler"=>$acikkapali),
array("tip"=>"select","id"=>"datatable","baslik"=>_LANG('y_datatable'),"secenekler"=>$acikkapali),
array("tip"=>"select","id"=>"panel_sablon","baslik"=>_LANG('y_panel_sablonu'),"secenekler"=>$panelsablonu),
array("tip"=>"color","id"=>"boxed_bg_color","baslik"=>_LANG('y_boxed_arkaplan_renk'),"kosul"=>"panel_sablon:==(boxed)"),
array("tip"=>"upload","id"=>"boxed_bg_image","baslik"=>_LANG('y_boxed_arkaplan_resim'),"kosul"=>"panel_sablon:==(boxed)"),
array("tip"=>"tab_kapat"),
);
// Standart üye ekstra bilgi alanları
$uye_ekstra_bilgiler=array();
// Üye ekstra detaylar buraya
$uye_ekstra_detaylar=array();
// Standart içerik durumları buraya. Bir içerik tipine özel durumlar belirlemek için diziye durumlar belirteci ekleyin.
$icerik_durumlari=array(
array("deger"=>"0","baslik"=>_LANG('y_taslak'),"renk"=>"danger"),
array("deger"=>"1","baslik"=>_LANG('y_onaylanmis'),"renk"=>"success"),
array("deger"=>"2","baslik"=>_LANG('y_bekliyor'),"renk"=>"info"),
);
// Standart üye durumları
$uye_durumlari=array(
array("deger"=>"0","baslik"=>_LANG('y_pasif'),"renk"=>"danger"),
array("deger"=>"1","baslik"=>_LANG('y_aktif'),"renk"=>"success"),
);
// Standart üye yetkileri
$uye_yetkileri=array(
array("deger"=>"admin","baslik"=>_LANG('y_yonetici')),
array("deger"=>"editor","baslik"=>_LANG('y_editor')),
array("deger"=>"yazar","baslik"=>_LANG('y_yazar')),
array("deger"=>"kullanici","baslik"=>_LANG('y_kullanici')),
);
// Yönetim paneli sidebar linkleri
$limonpanel_menu=array(
array("baslik"=>_LANG('y_uyeler'),"url"=>"uyeler.php","ikon"=>"users","sira"=>30,"yetki"=>"admin,editor","alt_linkler"=>array(array("baslik"=>_LANG('y_uye_ekle'),"url"=>"uyeler.php?islem=ekle","yetki"=>"admin,editor","yetki"=>"admin,editor"))),
array("baslik"=>_LANG('y_eklentiler'),"url"=>"eklentiler.php","ikon"=>"circle","sira"=>49,"yetki"=>"admin,editor"),
array("baslik"=>_LANG('y_ayarlar'),"url"=>"ayarlar.php","ikon"=>"cog","sira"=>100,"yetki"=>"admin")
);

global $baglan;
@$toplam_yorum=mysqli_num_rows(mysqli_query($baglan,"select * from yorumlar"));
if (@$toplam_yorum>0) {
	$yeni_yorum=mysqli_num_rows(mysqli_query($baglan,"select * from yorumlar where durum='0'"));
	if ($yeni_yorum>0) {
		$yeni_yorum_badge="<div class='badge'>$yeni_yorum</div>";
	}
	$limonpanel_menu[]=array("baslik"=>"Yorumlar $yeni_yorum_badge","url"=>"yorumlar.php","ikon"=>"comments","sira"=>20);	
}
$hizli_linkler=array(
array("baslik"=>_LANG('y_tum_yazilar'),"url"=>"icerik.php?tip=yazi","ikon"=>"pencil","sira"=>20),
array("baslik"=>_LANG('y_yeni_yazi'),"url"=>"icerik.php?tip=yazi&islem=ekle","ikon"=>"plus","sira"=>10),
array("baslik"=>_LANG('y_kategoriler'),"url"=>"kategoriler.php?tip=yazi","ikon"=>"folder-o","sira"=>30),
array("baslik"=>_LANG('y_kullanicilar'),"url"=>"uyeler.php","ikon"=>"users","sira"=>40),
array("baslik"=>_LANG('y_genel_ayarlar'),"url"=>"ayarlar.php","ikon"=>"cog","sira"=>50,"yetki"=>"admin"),
);
?>