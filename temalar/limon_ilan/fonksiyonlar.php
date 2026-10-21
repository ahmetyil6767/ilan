<?php
if(count(get_included_files())==1) {die();}
$limit_politikasi=limit_politikasi();
if (strstr(browser_url(), "/$admin_dizini/")) {
$tema_ne=tema_ne();

$toplam_siparis=mysqli_num_rows(mysqli_query($baglan,"select id from siparisler where durum='0'"));
if ($toplam_siparis>0) {
	$toplam_siparis_badge="<div class='badge'>$toplam_siparis</div>";
} else {$toplam_siparis_badge=null;}
$get_siparis=mysqli_num_rows(mysqli_query($baglan,"select id from siparisler where durum='0' and tip='get'"));
if ($get_siparis>0) {
	$get_siparis_badge="<div class='badge'>$get_siparis</div>";
} else {$get_siparis_badge=null;}
$magaza_siparis=mysqli_num_rows(mysqli_query($baglan,"select id from siparisler where durum='0' and tip='magaza'"));
if ($magaza_siparis>0) {
	$magaza_siparis_badge="<div class='badge'>$magaza_siparis</div>";
} else {$magaza_siparis_badge=null;}
$doping_siparis=mysqli_num_rows(mysqli_query($baglan,"select id from siparisler where durum='0' and tip='doping'"));
if ($doping_siparis>0) {
	$doping_siparis_badge="<div class='badge'>$doping_siparis</div>";
} else {$doping_siparis_badge=null;}
$paket_siparis=mysqli_num_rows(mysqli_query($baglan,"select id from siparisler where durum='0' and tip='paket'"));
if ($paket_siparis>0) {
	$paket_siparis_badge="<div class='badge'>$paket_siparis</div>";
} else {$paket_siparis_badge=null;}

$limonpanel_menu[]=array("baslik"=>_LANG('menu_ilan_sistemi_ayarlari'),"url"=>"temalar.php?islem=ayar","ikon"=>"cogs","sira"=>250);
$limonpanel_menu[]=array("baslik"=>_LANG('stores'),"url"=>"temalar.php?tema=$tema_ne&sayfa=panel&islem=magazalar","ikon"=>"shopping-bag","sira"=>10,"alt_linkler"=>array(array("baslik"=>_LANG('pn_magaza_ekle'),"url"=>"temalar.php?tema=$tema_ne&sayfa=panel&islem=magaza-ekle")));
$limonpanel_menu[]=array("baslik"=>_LANG('pn_odeme_bildirimleri'),"url"=>"temalar.php?tema=$tema_ne&sayfa=panel&islem=odemeler","ikon"=>"money","sira"=>15);
$limonpanel_menu[]=array("baslik"=>"" . sprintf(_LANG('s_menu_siparisler'), $toplam_siparis_badge) . "","url"=>"temalar.php?tema=$tema_ne&sayfa=panel&islem=siparisler","ikon"=>"shopping-cart","sira"=>15,"alt_linkler"=>array(
array("baslik"=>"" . sprintf(_LANG('s_menu_get_siparisler'), $get_siparis_badge) . "","url"=>"temalar.php?tema=$tema_ne&sayfa=panel&islem=siparisler&tip=get"),
array("baslik"=>"" . sprintf(_LANG('s_menu_magaza_siparisler'), $magaza_siparis_badge) . "","url"=>"temalar.php?tema=$tema_ne&sayfa=panel&islem=siparisler&tip=magaza"),
array("baslik"=>"" . sprintf(_LANG('s_menu_doping_siparisler'), $doping_siparis_badge) . "","url"=>"temalar.php?tema=$tema_ne&sayfa=panel&islem=siparisler&tip=doping"),
array("baslik"=>"" . sprintf(_LANG('s_menu_paket_siparisler'), $paket_siparis_badge) . "","url"=>"temalar.php?tema=$tema_ne&sayfa=panel&islem=siparisler&tip=paket"),
));
$limonpanel_menu[]=array("baslik"=>"Dopingler","url"=>"temalar.php?tema=$tema_ne&sayfa=panel&islem=dopingler","ikon"=>"rocket","sira"=>16);
$limonpanel_menu[]=array("baslik"=>_LANG('menu_uyelik_paketleri'),"url"=>"listeler.php?liste=uyelik_paketleri","ikon"=>"sticky-note-o","sira"=>17);
$limonpanel_menu[]=array("baslik"=>_LANG('menu_magaza_paketleri'),"url"=>"listeler.php?liste=magaza_paketleri","ikon"=>"sticky-note-o","sira"=>19);

$limonpanel_menu[]=array("baslik"=>_LANG('pn_il_ilce_mahalle'),"url"=>"temalar.php?tema=$tema_ne&sayfa=panel&islem=iller","ikon"=>"map-marker","sira"=>22);
$limonpanel_menu[]=array("baslik"=>_LANG('menu_toplu_uye_bilgileri'),"url"=>"temalar.php?tema=$tema_ne&sayfa=panel&islem=uye-bilgileri","ikon"=>"file-text-o","sira"=>30);
$limonpanel_menu[]=array("baslik"=>_LANG('menu_toplu_ilan_tarihi'),"url"=>"temalar.php?tema=$tema_ne&sayfa=panel&islem=toplu-ilan-tarihi-guncelleme","ikon"=>"clock-o","sira"=>30);
$limonpanel_menu[]=array("baslik"=>_LANG('menu_azalan_limitlere_gecis'),"url"=>"temalar.php?tema=$tema_ne&sayfa=panel&islem=azalan-limitlere-gecis","ikon"=>"refresh","sira"=>31);
$limonpanel_menu[]=array("baslik"=>_LANG('menu_kategoriye_genel_bakis'),"url"=>"temalar.php?tema=$tema_ne&sayfa=panel&islem=kategoriye-genel-bakis","ikon"=>"list","sira"=>31);
$limonpanel_menu[]=array("baslik"=>"Uyumluluk Merkezi","url"=>"uyumluluk.php","ikon"=>"thermometer-full","sira"=>32,"yetki"=>"admin");
$limonpanel_menu[]=array("baslik"=>_LANG('pn_ilan_iframe'),"url"=>"temalar.php?tema=$tema_ne&sayfa=panel&islem=ilan-iframe","ikon"=>"code","sira"=>33,"yetki"=>"admin");

if ($limit_politikasi==1) {
	$limonpanel_menu[]=array("baslik"=>"Paket Kontrolleri","url"=>"temalar.php?tema=$tema_ne&sayfa=panel&islem=paket-kontrol","ikon"=>"clock-o","sira"=>21);
}    
    
$hizli_linkler=null;
$ilan_ekle_url=ozel_url("ilan-ekle");
$hizli_linkler[]=array("baslik"=>_LANG('menu_ilan_ekle'),"url"=>$ilan_ekle_url,"ikon"=>"plus","yeni_sekme"=>1,"sira"=>0);
$hizli_linkler[]=array("baslik"=>_LANG('search_all_listings'),"url"=>"icerik.php?tip=ilan","ikon"=>"list","sira"=>10);
$hizli_linkler[]=array("baslik"=>_LANG('menu_siparisler'),"url"=>"temalar.php?tema=$aktif_tema&sayfa=panel&islem=siparisler","ikon"=>"shopping-cart","sira"=>15);
$hizli_linkler[]=array("baslik"=>_LANG('menu_yeni_uye_ekle'),"url"=>"uyeler.php?islem=ekle","ikon"=>"user-plus","sira"=>20);
$hizli_linkler[]=array("baslik"=>_LANG('menu_yeni_magaza_ekle'),"url"=>"temalar.php?tema=$aktif_tema&sayfa=panel&islem=magaza-ekle","ikon"=>"shopping-bag","sira"=>30);
$hizli_linkler[]=array("baslik"=>_LANG('menu_anasayfa_yapilandir'),"url"=>"bilesenler.php","ikon"=>"home","sira"=>40);
$hizli_linkler[]=array("baslik"=>_LANG('menu_menuleri_duzenle'),"url"=>"menuler.php","ikon"=>"link","sira"=>45);
$hizli_linkler[]=array("baslik"=>_LANG('menu_slider_yonetimi'),"url"=>"listeler.php?liste=slider","ikon"=>"picture-o","sira"=>46);
$hizli_linkler[]=array("baslik"=>_LANG('menu_banka_hesaplari'),"url"=>"listeler.php?liste=hesaplar","ikon"=>"money","sira"=>47);
$hizli_linkler[]=array("baslik"=>_LANG('menu_suresi_biten_uye_magaza'),"url"=>"temalar.php?tema=$aktif_tema&sayfa=panel&islem=paket-kontrol","ikon"=>"clock-o","sira"=>48);
$hizli_linkler[]=array("baslik"=>_LANG('menu_yazi_ekle'),"url"=>"icerik.php?tip=yazi&islem=ekle","ikon"=>"plus","sira"=>50);
$hizli_linkler[]=array("baslik"=>"Sayfa Ekle","url"=>"icerik.php?tip=sayfa&islem=ekle","ikon"=>"plus","sira"=>60);

$hizli_linkler[]=array("baslik"=>_LANG('p_tema_ayarlari_k'),"url"=>"temalar.php?islem=ayar","ikon"=>"cogs","sira"=>120);
$hizli_linkler[]=array("baslik"=>_LANG('listing_view_site'),"url"=>site_url(),"ikon"=>"external-link","yeni_sekme"=>1,"sira"=>140);
$hizli_linkler[]=array("baslik"=>"Uyumluluk Merkezi","url"=>"uyumluluk.php","ikon"=>"thermometer-full","sira"=>150,"yetki"=>"admin");

$tema_menuleri[]=array("id"=>"footer_1","baslik"=>_LANG('pn_footer_menu_1'));
$tema_menuleri[]=array("id"=>"footer_2","baslik"=>_LANG('pn_footer_menu_2'));
$tema_menuleri[]=array("id"=>"footer_3","baslik"=>_LANG('pn_footer_menu_3'));
$tema_menuleri[]=array("id"=>"footer_4","baslik"=>_LANG('pn_footer_menu_4'));
$tema_menuleri[]=array("id"=>"footer_5","baslik"=>_LANG('pn_footer_menu_5'));
$tema_menuleri[]=array("id"=>"ust_sol_menu","baslik"=>_LANG('pn_ust_sol_menu'));
$tema_menuleri[]=array("id"=>"ust_sag_menu","baslik"=>_LANG('pn_ust_sag_menu'));

$menuler[]=array("deger"=>"","baslik"=>_LANG('menu_menu_seciniz'));
$sql=mysqli_query($baglan,"select * from ayarlar where anahtar='menu'");
while ($menu_dizi=mysqli_fetch_array($sql)) {
	$menu_id=$menu_dizi["id"];
	$menu_baslik=$menu_dizi["deger"];
	$menuler[]=array("deger"=>"$menu_id","baslik"=>"$menu_baslik");
}

$sayfa_ekstra_bilgiler=array(
	array("tip"=>"tab_ac","id"=>"ayarlar","baslik"=>_LANG('pn_sayfa_secenekleri')),
	array("tip"=>"select","id"=>"sablon","baslik"=>_LANG('pn_sayfa_sablonu'),"secenekler"=>options("Standart:1|Fullwidth:2","|",":")),
	array("tip"=>"select","id"=>"menu","baslik"=>_LANG('p_menu'),"secenekler"=>$menuler,"kosul"=>"sablon:==(1)"),
	array("tip"=>"select","id"=>"iletisim_formu","baslik"=>_LANG('pn_iletisim_formu_dahil_et'),"secenekler"=>options("Evet:1|Hayır:","|",":")),
	array("tip"=>"tab_kapat"),
);
$sayfa_ekstra_detaylar=array(
	array("id"=>"menu","baslik"=>_LANG('p_menu')),
);
$icerik_tipleri["sayfa"]["galeri"]="0";
$icerik_tipleri["sayfa"]["yazar"]="0";
$icerik_tipleri["sayfa"]["ekstra_bilgiler"]=$sayfa_ekstra_bilgiler;
$icerik_tipleri["sayfa"]["ekstra_detaylar"]=$sayfa_ekstra_detaylar;

$icerik_tipleri["yazi"]["galeri"]="1";
$icerik_tipleri["yazi"]["yazar"]="0";
$icerik_tipleri["yazi"]["resim_boyutlandir"]=array("560_330","260_160");

$kat_op_sql=mysqli_query($baglan,"select * from kategoriler where ust_kategori='0' and tip='ilan'");
while ($kat_op_dizi=mysqli_fetch_array($kat_op_sql)) {
	$kat_op_id=$kat_op_dizi["id"];
	$kat_op_baslik=$kat_op_dizi["baslik"];
	$kat_op[]=array("deger"=>$kat_op_id,"baslik"=>$kat_op_baslik);
}
$yazi_kat_op_sql=mysqli_query($baglan,"select * from kategoriler where ust_kategori='0' and tip='yazi'");
while ($yazi_kat_op_dizi=mysqli_fetch_array($yazi_kat_op_sql)) {
	$yazi_kat_op_id=$yazi_kat_op_dizi["id"];
	$yazi_kat_op_baslik=$yazi_kat_op_dizi["baslik"];
	$yazi_kat_op[]=array("deger"=>$yazi_kat_op_id,"baslik"=>$yazi_kat_op_baslik);
}
if ($limit_politikasi==1) {
    $uye_ekstra_detaylar=array(
    array("id"=>"tip","baslik"=>_LANG('s_uyelik_tipi')),
    array("id"=>"paket","baslik"=>"Paket"),
    array("id"=>"paket_bitis_tarihi","baslik"=>_LANG('pn_paket_bitis')),
    );   
} else if ($limit_politikasi==2) {
    $uye_ekstra_detaylar=array(
    array("id"=>"azalan_ilan_limit","baslik"=>_LANG('pn_kalan_ilan_kredisi')),
    array("id"=>"azalan_kategori_limit","baslik"=>"Kat. Limit"),
    array("id"=>"azalan_foto_limit","baslik"=>"Foto Limit"),
    array("id"=>"azalan_aninda_onay","baslik"=>_LANG('s_aninda_onay')),
    );   
}


$paket_seciniz_sql=mysqli_query($baglan,"select l_id from listeler where tip='uyelik_paketleri'");
while ($paket_seciniz_dizi=mysqli_fetch_array($paket_seciniz_sql)) {
	$paket_id=$paket_seciniz_dizi["l_id"];
	$paket_baslik=liste_bilgi("paket_adi",$paket_id);
	$paket_seciniz[]=array("deger"=>"$paket_id","baslik"=>"$paket_baslik");
}
    
$limit_politikasi=limit_politikasi();    
if ($limit_politikasi==1) {
    $uye_ekstra_bilgiler=array(
        array("tip"=>"tab_ac","id"=>"bilgiler","baslik"=>"Genel Bilgiler"),
        array("tip"=>"select","id"=>"ozel_limitler","baslik"=>_LANG('pn_uyeye_ozel_limitler'),"secenekler"=>options("Evet:1|Hayır:0","|",":"),"standart"=>0),
        array("tip"=>"text","id"=>"uye_ilan_limit","baslik"=>_LANG('s_toplam_ilan_limiti'),"kosul"=>"ozel_limitler:==(1)"),
        array("tip"=>"text","id"=>"uye_kategori_limit","baslik"=>"Toplam Kategori Limiti","kosul"=>"ozel_limitler:==(1)","aciklama"=>_LANG('tx_azalan_limit_politikasinda_islevsizdir')),
        array("tip"=>"text","id"=>"uye_foto_limit","baslik"=>_LANG('pn_toplam_fotograf_limiti'),"kosul"=>"ozel_limitler:==(1)"),
        array("tip"=>"select","id"=>"uye_aninda_onay","baslik"=>_LANG('s_aninda_onay'),"secenekler"=>options("Hayır:0|Evet:1","|",":"),"kosul"=>"ozel_limitler:==(1)"),	
        array("tip"=>"select","id"=>"paket","baslik"=>"Paket","secenekler"=>$paket_seciniz),
        array("tip"=>"date_time","id"=>"paket_bitis_tarihi","baslik"=>_LANG('s_paket_bitis')),
        array("tip"=>"select","id"=>"guvenilir","baslik"=>_LANG('pn_guvenilir'),"secenekler"=>options("Seçiniz,Evet,Hayır"),"kosul"=>"tip:==(Kurumsal)"),
        array("tip"=>"select","id"=>"tip","baslik"=>_LANG('s_uyelik_tipi'),"secenekler"=>options("Bireysel,Kurumsal")),
        array("tip"=>"tab_kapat"),
        array("tip"=>"tab_ac","id"=>"iletisim_bilgileri","baslik"=>_LANG('post_contact')),
        array("tip"=>"text","id"=>"cep_tel","baslik"=>"Cep Telefonu"),
        array("tip"=>"text","id"=>"is_tel","baslik"=>_LANG('s_is_telefonu')),
        array("tip"=>"text","id"=>"ev_tel","baslik"=>"Ev Telefonu"),
        array("tip"=>"text","id"=>"tc_no","baslik"=>"TC Kimlik No"),
        array("tip"=>"text","id"=>"adres","baslik"=>"Kargo Adresi"),
        array("tip"=>"text","id"=>"il","baslik"=>_LANG('addr_region')),
        array("tip"=>"text","id"=>"posta_kodu","baslik"=>"Posta Kodu"),
        array("tip"=>"textarea","id"=>"banka_bilgileri","baslik"=>"Banka Bilgileri","satir"=>5),
        array("tip"=>"textarea","id"=>"hakkinda","baslik"=>_LANG('pn_hakkinda'),"satir"=>5),
        array("tip"=>"tab_kapat"),
        array("tip"=>"tab_ac","id"=>"firma_bilgileri","baslik"=>"Firma Bilgileri"),
        array("tip"=>"text","id"=>"firma_adi","baslik"=>_LANG('s_firma_unvani'),"kosul"=>"tip:==(Kurumsal)"),
        array("tip"=>"text","id"=>"firma_tel","baslik"=>"Firma Telefonu","kosul"=>"tip:==(Kurumsal)"),
        array("tip"=>"select","id"=>"faaliyet","baslik"=>_LANG('pn_faaliyet_alani'),"secenekler"=>$kat_op,"kosul"=>"tip:==(Kurumsal)"),
        array("tip"=>"textarea","id"=>"firma_adres","baslik"=>"Adres","kosul"=>"tip:==(Kurumsal)"),
        array("tip"=>"select","id"=>"sirket","baslik"=>_LANG('pn_sirket'),'secenekler'=>options("Seçiniz:0|Şahıs:1|Limited veya Anonim Şirket:2","|",":"),"kosul"=>"tip:==(Kurumsal)"),
        array("tip"=>"text","id"=>"tc_no","baslik"=>"TC Kimlik No","kosul"=>"tip:==(Kurumsal)"),
        array("tip"=>"text","id"=>"vergi_no","baslik"=>_LANG('pn_vergi_numarasi'),"kosul"=>"tip:==(Kurumsal)"),
        array("tip"=>"text","id"=>"vergi_dairesi","baslik"=>"Vergi Dairesi","kosul"=>"tip:==(Kurumsal)"),
        array("tip"=>"baslik","id"=>"firma_aciklama","baslik"=>"Bilgi","aciklama"=>_LANG('tx_bireysel_uyelerde_kullanilamaz')),
        array("tip"=>"tab_kapat"),
        array("tip"=>"tab_ac","id"=>"foto","baslik"=>_LANG('pn_fotograflar')),
        array("tip"=>"upload","id"=>"profil_gorseli","dizin"=>"profil","baslik"=>_LANG('pn_profil_fotografi'),"boyutlar"=>array("50_50","150_150")),
        array("tip"=>"upload","id"=>"kapak_gorseli","dizin"=>"kapak","baslik"=>_LANG('pn_kapak_fotografi'),"boyutlar"=>array("1140_200"),"gecerli_boyut"=>"1140_200","orijinali_sil"=>1),
        array("tip"=>"tab_kapat"),
    );        
} else if ($limit_politikasi==2) {
    $uye_ekstra_bilgiler=array(
        array("tip"=>"tab_ac","id"=>"bilgiler","baslik"=>"Genel Bilgiler"),
        array("tip"=>"text","id"=>"azalan_ilan_limit","baslik"=>_LANG('pn_azalan_kalan_ilan_limiti'),"aciklama"=>"Üyenin kalan ilan hakkıdır. Azalan limitlerde geçerlidir."),
        array("tip"=>"text","id"=>"azalan_kategori_limit","baslik"=>"Azalan Kategori Limiti","aciklama"=>_LANG('tx_uyenin_bir_kategoriye_ekleyebilecegi_maksimum')),
        array("tip"=>"text","id"=>"azalan_foto_limit","baslik"=>_LANG('pn_azalan_fotograf_limiti'),"aciklama"=>"Üyenin bir ilanına ekleyebileceği maksimum fotoğraf adedidir. Azalan limitlerde geçerlidir."),
        array("tip"=>"select","id"=>"azalan_aninda_onay","baslik"=>_LANG('pn_azalan_aninda_onay'),"secenekler"=>options("Hayır:0|Evet:1","|",":"),"aciklama"=>"Üyenin ilanını doğrudan yayınlama yetkisidir. Azalan limitlerde geçerlidir."),	
        array("tip"=>"select","id"=>"guvenilir","baslik"=>_LANG('pn_guvenilir'),"secenekler"=>options("Seçiniz,Evet,Hayır"),"kosul"=>"tip:==(Kurumsal)"),
        array("tip"=>"select","id"=>"tip","baslik"=>_LANG('s_uyelik_tipi'),"secenekler"=>options("Bireysel,Kurumsal")),
        array("tip"=>"tab_kapat"),
        array("tip"=>"tab_ac","id"=>"iletisim_bilgileri","baslik"=>_LANG('post_contact')),
        array("tip"=>"text","id"=>"cep_tel","baslik"=>"Cep Telefonu"),
        array("tip"=>"text","id"=>"is_tel","baslik"=>_LANG('s_is_telefonu')),
        array("tip"=>"text","id"=>"ev_tel","baslik"=>"Ev Telefonu"),
        array("tip"=>"text","id"=>"tc_no","baslik"=>"TC Kimlik No"),
        array("tip"=>"text","id"=>"adres","baslik"=>"Kargo Adresi"),
        array("tip"=>"text","id"=>"il","baslik"=>_LANG('addr_region')),
        array("tip"=>"text","id"=>"posta_kodu","baslik"=>"Posta Kodu"),
        array("tip"=>"textarea","id"=>"banka_bilgileri","baslik"=>"Banka Bilgileri","satir"=>5),
        array("tip"=>"textarea","id"=>"hakkinda","baslik"=>_LANG('pn_hakkinda'),"satir"=>5),
        array("tip"=>"tab_kapat"),
        array("tip"=>"tab_ac","id"=>"firma_bilgileri","baslik"=>"Firma Bilgileri"),
        array("tip"=>"text","id"=>"firma_adi","baslik"=>_LANG('s_firma_unvani'),"kosul"=>"tip:==(Kurumsal)"),
        array("tip"=>"text","id"=>"firma_tel","baslik"=>"Firma Telefonu","kosul"=>"tip:==(Kurumsal)"),
        array("tip"=>"select","id"=>"faaliyet","baslik"=>_LANG('pn_faaliyet_alani'),"secenekler"=>$kat_op,"kosul"=>"tip:==(Kurumsal)"),
        array("tip"=>"textarea","id"=>"firma_adres","baslik"=>"Adres","kosul"=>"tip:==(Kurumsal)"),
        array("tip"=>"select","id"=>"sirket","baslik"=>_LANG('pn_sirket'),'secenekler'=>options("Seçiniz:0|Şahıs:1|Limited veya Anonim Şirket:2","|",":"),"kosul"=>"tip:==(Kurumsal)"),
        array("tip"=>"text","id"=>"tc_no","baslik"=>"TC Kimlik No","kosul"=>"tip:==(Kurumsal)"),
        array("tip"=>"text","id"=>"vergi_no","baslik"=>_LANG('pn_vergi_numarasi'),"kosul"=>"tip:==(Kurumsal)"),
        array("tip"=>"text","id"=>"vergi_dairesi","baslik"=>"Vergi Dairesi","kosul"=>"tip:==(Kurumsal)"),
        array("tip"=>"baslik","id"=>"firma_aciklama","baslik"=>"Bilgi","aciklama"=>_LANG('tx_bireysel_uyelerde_kullanilamaz')),
        array("tip"=>"tab_kapat"),
        array("tip"=>"tab_ac","id"=>"foto","baslik"=>_LANG('pn_fotograflar')),
        array("tip"=>"upload","id"=>"profil_gorseli","dizin"=>"profil","baslik"=>_LANG('pn_profil_fotografi'),"boyutlar"=>array("50_50","150_150")),
        array("tip"=>"upload","id"=>"kapak_gorseli","dizin"=>"kapak","baslik"=>_LANG('pn_kapak_fotografi'),"boyutlar"=>array("1140_200"),"gecerli_boyut"=>"1140_200","orijinali_sil"=>1),
        array("tip"=>"tab_kapat"),
    );       
}
    

$ilan_ekstra_bilgiler=array(
	array("tip"=>"tab_ac","id"=>"bilgiler","baslik"=>_LANG('s_ilan_bilgileri')),
	array("tip"=>"text","id"=>"fiyat","baslik"=>"Fiyat","kosul"=>"para_birimi:==(gmd)"),
	array("tip"=>"text","id"=>"fiyat_usd","baslik"=>"Fiyat USD","kosul"=>"para_birimi:==(usd)"),
	array("tip"=>"text","id"=>"fiyat_eur","baslik"=>"Fiyat EUR","kosul"=>"para_birimi:==(eur)"),
	array("tip"=>"text","id"=>"fiyat_gbp","baslik"=>"Fiyat GBP","kosul"=>"para_birimi:==(gbp)"),
	array("tip"=>"select","id"=>"para_birimi","baslik"=>"Para Birimi","secenekler"=>options("gmd,usd,eur,gbp")),
	array("tip"=>"select","id"=>"iletisim","baslik"=>_LANG('ust_contact'),"secenekler"=>options("Profil iletişim bilgileri:1|Özel:2","|",":")),
	array("tip"=>"text","id"=>"yetkili","baslik"=>_LANG('s_yetkili_kisi'),"kosul"=>"iletisim:==(2)"),
	array("tip"=>"text","id"=>"yetkili_tel","baslik"=>"Yetkili Telefon","kosul"=>"iletisim:==(2)"),
	array("tip"=>"tab_kapat"),
);
$ilan_ekstra_detaylar=array(
array("id"=>"fiyat","baslik"=>"Fiyat"),
array("id"=>"magaza","baslik"=>_LANG('pn_magaza_id')),
);

$ilan_kategori_ekstra_bilgiler=array(
	array("tip"=>"tab_ac","id"=>"bilgiler","baslik"=>"Genel Ayarlar"),
	array("tip"=>"color","id"=>"ikon_bg","baslik"=>_LANG('pn_ikon_arkaplan_rengi'),"aciklama"=>"Sol menüde kategori ikonunun arkaplanına uygulanır."),
	array("tip"=>"text","id"=>"ikon","baslik"=>_LANG('p_ikon'),"aciklama"=>"FontAwesome, Glyphicons ve IonIcons sınıfları kullanılabilir."),
	array("tip"=>"upload","id"=>"ust_gorsel","baslik"=>_LANG('pn_kategori_ust_gorsel'),"aciklama"=>"Kategori sayfasının üst kısımda gösterebileceğiniz özel bir resim. Genişlik: 1040px;"),
	array("tip"=>"text","id"=>"ust_gorsel_url","baslik"=>_LANG('pn_kategori_ust_gorsel_url'),"aciklama"=>"İsteğe bağlı kategori üst görseline link verebilirsiniz."),
	array("tip"=>"select","id"=>"fiyat_zorunlu","baslik"=>"Bu kategorideki ilanlarda fiyat belirtmek zorunlu olsun mu?","secenekler"=>options(_LANG('tx_hayir_0_evet_1'),"|",":")),
	array("tip"=>"select","id"=>"marka_model","baslik"=>_LANG('pn_marka_model_gosterilsin'),"secenekler"=>options("Hayır:0|Evet:1","|",":"),"aciklama"=>"Otomobil, Motosiklet gibi ilan kategorilerinde kategori başlıklarını otomatik olarak marka, serii, model şeklinde gösterebilirsiniz."),
	array("tip"=>"select","id"=>"metrekare_fiyati","baslik"=>_LANG('pn_metrekare_fiyati_gosterilsin_mi'),"secenekler"=>options("Hayır:0|Evet:1","|",":"),"aciklama"=>"Arsa, Konut gibi ilan kategoriler için opsiyonel olarak metrekare fiyatı gösterebilirsiniz."),	
	array("tip"=>"select","id"=>"get","baslik"=>_LANG('pn_bu_kategoride_get_guvenilir_e_ticaret_ilanlari_acilabilir_mi'),"secenekler"=>options("Hayır:0|Evet:1","|",":")),	
	array("tip"=>"select","id"=>"taksit","baslik"=>_LANG('pn_taksit_sayisi'),"secenekler"=>options("0:yok,2:2,3:3,6:6,9:9",",",":"),"standart"=>"9"),
	array("tip"=>"select","id"=>"resimsiz","baslik"=>_LANG('pn_resimsiz_ilanlar_modu'),"secenekler"=>options("Hayır:0|Evet:1","|",":"),"aciklama"=>"Usta ve Hizmetler, Özel Ders Verenler, İş İlanları gibi kategoriler için resimsiz ilan modu uygulayabilirsiniz."),	
	array("tip"=>"select","id"=>"sahiplendirme","baslik"=>_LANG('pn_sahiplendirme_secenegi'),"secenekler"=>options("Hayır:0|Sahiplendirme:1|Zorunlu Sahiplendirme:2","|",":"),"aciklama"=>"Hayvanlar Alemi kategorisi için sahiplendirme ilanlarını mümkün kılar."),	
	array("tip"=>"select","id"=>"gorusulur","baslik"=>_LANG('pn_gorusulur_fiyat_secenegi'),"secenekler"=>options("Hayır:0|Görüşülür:1","|",":"),"aciklama"=>"Hayvanlar Alemi vb. ilan kategorileri için üyelerin fiyat alanına görüşülür yazmalarına olanak sağlar."),	
	array("tip"=>"select","id"=>"teklif_usulu","baslik"=>_LANG('pn_teklif_usulu_fiyat_secenegi'),"secenekler"=>options("Hayır:0|Evet:1","|",":"),"aciklama"=>"Bu kategoriye ilan ekleyen kullanıcılar dilerlerse, fiyat alanına Teklif Usulü yazabilirler."),	
	array("tip"=>"tab_kapat"),
	array("tip"=>"tab_ac","id"=>"ozel_alan","baslik"=>_LANG('pn_ozel_alan_ayarlari')),
	array("tip"=>"textarea","satir"=>20,"id"=>"ozel_alanlar","baslik"=>_LANG('pn_ozel_alanlar'),"aciklama"=>"Her satıra bir adet özel alan giriniz.<br />Format: ozel_alan_id|Başlık|Gerekli|Seçenekler (Gerekli: 1 ya da 0 değeri alabilir; Seçenekler opsiyoneldir, girilirse select, girilmezse input olacaktır.)<br />Örnek: takas|Takas|1|Evet,Hayır"),
	array("tip"=>"tab_kapat"),
	array("tip"=>"tab_ac","id"=>"teknik","baslik"=>_LANG('pn_ozellik_ayarlari')),
	array("tip"=>"textarea","satir"=>20,"id"=>"teknik_ozellikler","baslik"=>_LANG('pn_ozellikler'),"aciklama"=>"Grup adı ve bir satır aşağıdan başlayarak özellikleri virgülle ayırarak giriniz. Son özellikten sonra diğer gruba geçmek için iki boşluk bırakın."),
	array("tip"=>"aciklama","satir"=>20,"id"=>"teknik_ozellikler_aciklama","aciklama"=>"<strong>Örnek</strong><br />Güvenlik<br />Sürücü Hava Yastığı,Yolcu Hava Yastığı,Yan Hava Yastığı,<br /><br />Konfor<br />Klima,Elektronik Klima,Hız Sabitleyici,Deri Koltuk"),
	array("tip"=>"tab_kapat"),
	array("tip"=>"tab_ac","id"=>"filtre","baslik"=>_LANG('pn_kategori_filtre_ayarlari')),
	array("tip"=>"textarea","satir"=>20,"id"=>"filtreler","baslik"=>"Filtreler","aciklama"=>"Her satıra bir adet filtre girin. 3.seçenek min_max değeri alabilir, opsiyoneldir.<br />vites|Vites<br />yakit|Yakıt<br />km|Kilometre|min_max"),
	array("tip"=>"tab_kapat"),
	array("tip"=>"tab_ac","id"=>"tablo_detaylar","baslik"=>_LANG('pn_kategori_tablo_detaylari')),
	array("tip"=>"textarea","satir"=>20,"id"=>"detaylar","baslik"=>"Detaylar","aciklama"=>"Her satıra bir adet detay girin.<br />Format: ozel_alan_id|Başlık<br />Örnek: oda_sayisi|Oda Sayısı"),
	array("tip"=>"tab_kapat"),
	array("tip"=>"tab_ac","id"=>"tablo_sekme","baslik"=>_LANG('pn_sekme_tab_yonetimi')),
	array("tip"=>"textarea","satir"=>20,"id"=>"tab","baslik"=>"Sekmeler","aciklama"=>"Her satıra bir adet sekme girin.<br />Format: anahtar|deger|Başlık|ikon<br />Örnek: kimden|Sahibinden|Sahibinden|ion-home"),
	array("tip"=>"tab_kapat"),
	array("tip"=>"tab_ac","id"=>"diger","baslik"=>_LANG('pn_diger_ayarlar')),
	array("tip"=>"textarea","satir"=>7,"sinif"=>"ckeditor","id"=>"guvenlik_ipuclari","baslik"=>_LANG('ust_safety'),"aciklama"=>"Bu kategorideki tüm ilanların sağ kısmında görünecektir."),
	array("tip"=>"tab_kapat"),
	array("tip"=>"tab_ac","id"=>"reklam","baslik"=>_LANG('pn_reklam_yonetimi')),
	array("tip"=>"textarea","satir"=>7,"id"=>"ilan_sag_reklam","baslik"=>_LANG('pn_ilan_sag_reklam'),"aciklama"=>"Bu kategorideki ilanların sağ kısmında görünecek reklam."),
	array("tip"=>"tab_kapat"),
);

$ilan_kategori_ekstra_bilgiler[]=array("tip"=>"tab_ac","id"=>"magaza_secenekleri","baslik"=>_LANG('pn_magaza_secenekleri'));
$ilan_kategori_ekstra_bilgiler[]=array("tip"=>"select","id"=>"magaza","baslik"=>_LANG('pn_bu_kategoride_magaza_acilabilir_mi'),"secenekler"=>options("Hayır:0|Evet:1","|",":"));

$magaza_paketleri_sql=mysqli_query($baglan,"select l_id from listeler where tip='magaza_paketleri'");
$magaza_paket_say=mysqli_num_rows($magaza_paketleri_sql);
if ($magaza_paket_say>0) {
	$ilan_kategori_ekstra_bilgiler[]=array("tip"=>"grup_ac","baslik"=>_LANG('pn_kategoriye_ozel_fiyatlandirma'),"aciklama"=>"Mağaza paketlerini bu kategori için özel olarak fiyatlandırabilirsiniz.");
	while ($magaza_paket_dizisi=mysqli_fetch_array($magaza_paketleri_sql)) {
		$magaza_paket_id=$magaza_paket_dizisi["l_id"];
		$paket_baslik=liste_bilgi("paket_adi",$magaza_paket_id);
		$paket_ucret=liste_bilgi("ucret",$magaza_paket_id);
		$ilan_kategori_ekstra_bilgiler[]=array("tip"=>"text","id"=>"magaza_paket_fiyat-$magaza_paket_id","baslik"=>"" . sprintf(_LANG('s_paket_ozel_fiyat'), $paket_baslik) . "","aciklama"=>"" . sprintf(_LANG('s_paket_standart_fiyat'), $paket_ucret) . "","kosul"=>"magaza:==(1)","grup"=>5);
	}
	$ilan_kategori_ekstra_bilgiler[]=array("tip"=>"grup_kapat");
}
$ilan_kategori_ekstra_bilgiler[]=array("tip"=>"tab_kapat");

$ilan_durumlari[]=array("deger"=>"0","baslik"=>"Taslak","renk"=>"warning");
$ilan_durumlari[]=array("deger"=>"1","baslik"=>_LANG('post_published'),"renk"=>"success");
$ilan_durumlari[]=array("deger"=>"2","baslik"=>"Onay bekliyor","renk"=>"info");
$ilan_durumlari[]=array("deger"=>"3","baslik"=>_LANG('e_yayindan_kaldirildi'),"renk"=>"danger");
$ilan_durumlari[]=array("deger"=>"4","baslik"=>_LANG('e_suresi_doldu'),"renk"=>"danger");

$icerik_tipleri["ilan"]=array(
	"id"=>"ilan",
	"baslik"=>_LANG('search_found_ads'),
	"baslik_cogul"=>_LANG('tx_ilanlar'),
	"baslik_isaret"=>"" . _LANG('s_ilani') . "",
	"ikon"=>"calendar-check-o",
	"gorsel"=>"0",
	"galeri"=>"1",
	"galeri_kucuk_resim"=>"160_120",
	"kategori"=>"1",
	"etiket"=>"0",
	"icerik"=>"1",
	"url"=>"1",
	"yazar"=>"1",
	"ekle_buton"=>"1",
	"ekstra_bilgiler"=>$ilan_ekstra_bilgiler,
	"kategori_ekstra_bilgiler"=>$ilan_kategori_ekstra_bilgiler,
	"ekstra_detaylar"=>$ilan_ekstra_detaylar,
	"tarih_saat_detayi"=>"1",
	"durum_detayi"=>"1",
	"yazar_detayi"=>"1",
	"gorsel_dizin"=>"ajax",
	"galeri_dizin"=>"ajax",
	"resim_boyutlandir"=>array("160_120","530_393"),
	"durumlar"=>$ilan_durumlari,
);	

function ilan_ozel_alanlar_form() {
	$kod=null;
	if (isset($_GET["id"])) {
		@$icerik_id=$_GET["id"];
	}
	if (isset($_GET["tip"])) {
		@$icerik_tip=$_GET["tip"];
	}
	if (isset($icerik_tip) and $icerik_tip=="ilan" and isset($icerik_id)) { 
		@$kategoriler=kategori_getir($icerik_id);
		if (is_array($kategoriler)) {
		foreach ($kategoriler as $kat) {
			$kat_ozel_alan=kategori_bilgi("ozel_alanlar",$kat);
			if ($kat_ozel_alan) {
				$ozel_alanlar=$kat_ozel_alan;
			}
		}
		}
		if (isset($ozel_alanlar)) {
			$alan_dizi=explode("\r\n",$ozel_alanlar);
			$say=count($alan_dizi);
			$kod.="<div class='panel panel-default'>
			<div class='panel-heading'>" . _LANG('hb_ozel_alanlar') . "</div>
			<div class='panel-body'><div class='row'>";
			foreach ($alan_dizi as $alan) {
				$alan_parcala=explode("|",$alan);
				$anahtar=trim($alan_parcala[0]);
				$baslik=trim($alan_parcala[1]);
				if (!$baslik) {
					$baslik=$anahtar;
				}
				@$gerekli=trim($alan_parcala[2]);
				@$secenekler=trim($alan_parcala[3]);
				$deger=bilgi($anahtar,$icerik_id);
				if ($gerekli==1) {
					$gerekli_yildiz="<span style='color:#e74c3c;margin-left:3px;'>*</span>";
				} else {
					$gerekli_yildiz=null;
				}
				if (!$secenekler) {
					$kod.="
					<div class='col-md-10'>
						<div class='form-group'>
							<label for='$anahtar'>$baslik $gerekli_yildiz</label>
							<input type='text' id='$anahtar' name='oa_$anahtar' class='form-control' placeholder='$baslik' value='$deger' />
						</div>
					</div>
					";
				} else {
					$secenekler_dizi=explode(",",$secenekler);
					@$options.="<option value=''><?php echo _LANG('filter_select'); ?></option>";
					foreach ($secenekler_dizi as $secenek) {
						$secenek=trim($secenek);
						if ($secenek==$deger) {
							$selected_yaz="selected='selected'";
						} else {
							$selected_yaz=null;
						}
						$options.="<option value='$secenek' $selected_yaz>$secenek</option>";
					}
					$kod.="
					<div class='col-md-10'>
						<div class='form-group'>
							<label for='$anahtar'>$baslik $gerekli_yildiz</label>
							<select id='$anahtar' name='oa_$anahtar' class='form-control' placeholder='$baslik' value='$deger' />
								$options
							</select>
						</div>
					</div>
					";				
					$options=null;
					$secenekler=null;
					$selected_yaz=null;
				}
			}
			$kod.="</div></div></div>";
		} else {
			@$mevcut_kategoriler=kategori_getir($icerik_id);
			if (is_array($mevcut_kategoriler)) {
				if (count($mevcut_kategoriler)==0) {
					$kod="<div class='alert alert-info'>Kategoriye özel bilgileri eklemek için lütfen kategori seçiminizi yapın ve ilanı taslak olarak kaydedin.</div>";
				} else {
					$kod="<div class='alert alert-danger'>" . _LANG('s_ozel_alan_yapilandirilmadi') . "</div>";
				}
			}
			
		}
		return $kod;
	}
}
function ilan_ozel_alanlar_kaydet() {
	global $baglan;
	if (strstr(browser_url(),"icerik.php")) {
		if (isset($_GET["id"])) {
			$icerik_id=$_GET["id"];
		}
		if (isset($_GET["tip"])) {
			$icerik_tip=$_GET["tip"];
		}
		if (!isset($icerik_id)) {
			$icerik_id=mysqli_fetch_array(mysqli_query($baglan,"SHOW TABLE STATUS LIKE 'icerik'"))["Auto_increment"];
		}
		$kategoriler=kategori_getir($icerik_id);
		$ozel_alanlar=null;
		if (is_array($kategoriler)) {
			foreach ($kategoriler as $kat) {
				$kat_ozel_alan=kategori_bilgi("ozel_alanlar",$kat);
				if ($kat_ozel_alan) {
					$ozel_alanlar=$kat_ozel_alan;
				}
			}
		}
		if ($ozel_alanlar) {
			$alan_dizi=explode("\r\n",$ozel_alanlar);
			$say=count($alan_dizi);
			if ($_POST and $say>0) {
				foreach ($alan_dizi as $alan) {
					$alan_parcala=explode("|",$alan);
					$anahtar=trim($alan_parcala[0]);
					$deger=$_POST["oa_$anahtar"];
					if ($deger or $deger==="" or $deger=="0") {ekstra_bilgi_guncelle($anahtar,$deger,$icerik_id,$icerik_tip);}
				}
			}
		}
	}
}

$panel_icerik_sol_alt_kod[]=ilan_ozel_alanlar_form();
$panel_icerik_sol_alt_kod[]=ilan_ozel_alanlar_kaydet();

function il_ilce_mahalle() {
	global $baglan;
	global $aktif_tema;
	$kod=null;
	if (isset($_GET["id"])) {
		$icerik_id=$_GET["id"];
	}
	if (isset($_GET["tip"])) {
		$icerik_tip=$_GET["tip"];
	}
	if (isset($_GET["id"]) and @$icerik_tip=="ilan") {
		$icerik_il=bilgi("il",$icerik_id);
		$icerik_ilce=bilgi("ilce",$icerik_id);
		$icerik_mahalle=bilgi("mahalle",$icerik_id);
		$tema_dizini=tema_dizini();
		$kod.='
		<div class="panel panel-default">
			<div class="panel-heading">' . _LANG('hb_il_ilce_mahalle') . '</div>
			<div class="panel-body">
				<div id="il_sec" class="form-group"></div>
				<div id="ilce_sec" class="form-group"></div>
				<div id="mahalle_sec" class="form-group"></div>
			</div>
		</div>
		<script type="text/javascript">
			$("#il_sec").load("'.$tema_dizini.'/panel/ajax.php?islem=il_sec&icerik_id='.$icerik_id.'&il='.$icerik_il.'&ilce='.$icerik_ilce.'&mahalle='.$icerik_mahalle.'");
		</script>
		';
		return $kod;
	}
}
function il_ilce_mahalle_kaydet() {
	global $baglan;
	if (isset($_GET["id"])) {
		$icerik_id=$_GET["id"];
	}
	if (isset($_GET["tip"])) {
		$icerik_tip=$_GET["tip"];
	}
	if (!isset($icerik_id)) {
		$icerik_id=mysqli_fetch_array(mysqli_query($baglan,"SHOW TABLE STATUS LIKE 'icerik'"))["Auto_increment"];
	}
	
	@$il=$_POST["il"];
	@$ilce=$_POST["ilce"];
	@$mahalle=$_POST["mahalle"];
	
	if ($il) {
		ekstra_bilgi_guncelle("il",$il,$icerik_id,"ilan");
	}
	if ($ilce) {
		ekstra_bilgi_guncelle("ilce",$ilce,$icerik_id,"ilan");
	}
	if ($mahalle) {
		ekstra_bilgi_guncelle("mahalle",$mahalle,$icerik_id,"ilan");
	}
}
$panel_icerik_sag_alt_kod[]=il_ilce_mahalle();
$panel_icerik_sag_alt_kod[]=il_ilce_mahalle_kaydet();

function iliskilendirilmemis_gorselleri_sil() {
	global $baglan;
	$ajax_upload_sure=tema_ayar("ajax_upload_sure");
	$simdi=time();
	$sinir_tarih_saat=date("Y-m-d H:i:s",strtotime("-$ajax_upload_sure minutes"));
	$suresi_dolmus_resim_sql=mysqli_query($baglan,"select * from ajax_upload where tarih_saat<'$sinir_tarih_saat'");
	$suresi_dolmus_resim_sayisi=mysqli_num_rows($suresi_dolmus_resim_sql);
	if ($suresi_dolmus_resim_sayisi>0) {
		// Resimleri sil
		$x=0;
		while ($ajax_upload_dizi=mysqli_fetch_array($suresi_dolmus_resim_sql)) {
			$ajax_upload_id=$ajax_upload_dizi["id"];
			$ajax_upload_url=$ajax_upload_dizi["url"];
			sil(thumbnail_url("160_120",$ajax_upload_url));
			sil(thumbnail_url("530_393",$ajax_upload_url));
			sil($ajax_upload_url);
			$ajax_remove_sql=mysqli_query($baglan,"delete from ajax_upload where id='$ajax_upload_id'");
		$x++;
		}
		if ($x>0) {
			bildirim_ekle(_LANG('pn_i_class_fa_fa_trash_o_fa_fw_i_bir_ilanla_iliskilendirilmemis_x_gorsel_', array($x)),null,0);
		}
	}	
}
iliskilendirilmemis_gorselleri_sil();

function doping_uygula_link() {
	global $baglan;
	global $aktif_tema;
	$kod=null;
	if (isset($_GET["id"])) {
		$icerik_id=$_GET["id"];
		$kod.="
		<a href='temalar.php?tema=$aktif_tema&sayfa=panel&islem=dopingler&ekle=1&ilan_id=$icerik_id' target='_blank' class='btn btn-default'><i class='fa fa-rocket'></i>Doping Uygula</a>
		";	
		return $kod;
	}
}
$panel_icerik_sol_alt_kod[]=doping_uygula_link();

function ilan_reddet_link() {
	global $baglan;
	global $aktif_tema;
	$kod=null;
	if (isset($_GET["id"]) and getir("icerik","durum",$_GET["id"])==2) {
		$icerik_id=$_GET["id"];
		$kod.="
		<a href='temalar.php?tema=$aktif_tema&sayfa=panel&islem=reddet&ilan_id=$icerik_id' class='btn btn-danger'><i class='fa fa-ban'></i>" . _LANG('s_ilani') . " Reddet</a>
		";	
		return $kod;
	}
}
$panel_icerik_sol_alt_kod[]=ilan_reddet_link();

function onay_bildirimi_gonder() {
	global $baglan;
	global $aktif_tema;
	$islem=null;
	$tip=null;
	if (isset($_GET["islem"])) {
		@$islem=$_GET["islem"];
	}
	if (isset($_GET["tip"])) {
		@$tip=$_GET["tip"];
	}
	if (($islem=="guncelle" and $tip=="ilan" and isset($_POST["buton_yayinla"])) or ($islem=="yayinla" and $tip=="ilan")) {
		$ilan_id=$_GET["id"];
		$ilan_sahibi=getir("icerik","yazar",$ilan_id);
		$ilan_url=link_getir($ilan_id);
		bildirim_ekle(_LANG('pn_ilanin_onaylandi_ilani_goruntulemek_icin_a_href_ilan_url_buraya_a_tikl', array($ilan_url)),$ilan_sahibi,1);
		
		// E-posta gönderimi başladı
		$mail_baslik=tema_ayar("mail_baslik");
		$site_url=site_url();		
		$ilan_sahibi_ad_soyad=getir("uyeler","ad_soyad",$ilan_sahibi);
		$ilan_sahibi_email_adresi=getir("uyeler","email_adresi",$ilan_sahibi);

		$ileti="
		<p>Merhaba <strong>$ilan_sahibi_ad_soyad</strong></p>
		p" . _LANG('hb_bu_e_posta_ilaninizin_onaylandigini_bildirmek') . "</p>
		<p>" . _LANG('s_ilani') . "nızı ziyaretçi gözüyle görüntülemek için <a href=\"" . sprintf(_LANG('tx_buraya_tiklayin2'), $ilan_url) . "";
		auto_mail_add($ilan_sahibi_ad_soyad,$ilan_sahibi_email_adresi,"' . sprintf(_LANG('b_ilaniniz_onaylandi'), $mail_baslik) . '",$ileti);				
	}
}
$panel_icerik_sol_alt_kod[]=onay_bildirimi_gonder();

function autonumeric() {
	$tema_dizini=tema_dizini();
	$kod=null;
	$kod.="
	<script type='text/javascript' src='$tema_dizini/js/autoNumeric.js'></script>
	<script type='text/javascript'>
	$(function(){
		$('#form_fiyat input').autoNumeric({aSep: '', aDec: ','});
	});
	</script>
	";
	return $kod;
}

$panel_icerik_sol_alt_kod[]=autonumeric();

$slider_options=array(
	array("tip"=>"upload","id"=>"gorsel","baslik"=>_LANG('p_gorsel'),"aciklama"=>"Görsel boyutları bileşen tercihlerine göre değişiklik göstermektedir. Bileşenler bölümünden tam boyutları kontrol edebilirsiniz."),
	array("tip"=>"text","id"=>"url","baslik"=>"URL"),
);	
$liste["slider"]=array("id"=>"slider","baslik"=>"Slider","baslik_cogul"=>_LANG('tx_slider_gorselleri'),"ayarlar"=>$slider_options);

$liste["mobil_slider"]=array("id"=>"mobil_slider","baslik"=>"Mobil Slider","baslik_cogul"=>_LANG('tx_mobil_slider_gorselleri'),"ayarlar"=>$slider_options);

$uyelik_paketleri_options=array(
	array("tip"=>"text","id"=>"paket_adi","baslik"=>_LANG('pn_paket_adi')),
	array("tip"=>"text","id"=>"ilan_limit","baslik"=>_LANG('s_ilan_limiti'),"aciklama"=>"Üyelerin toplamda yayınlayabileceği ilan sayısı"),
	array("tip"=>"text","id"=>"kategori_limit","baslik"=>_LANG('s_kategori_ilan_limit'),"aciklama"=>"Vasıta, Emlak gibi ana kategorilerde aynı anda yayında olabilecek ilan sayısı."),
	array("tip"=>"text","id"=>"foto_limit","baslik"=>_LANG('s_fotograf_limiti'),"Üyelerin ilanlarına yükleyebilecekleri toplam resim sayısı."),
	array("tip"=>"select","id"=>"aninda_onay","baslik"=>_LANG('s_aninda_onay'),"secenekler"=>options("Hayır:0|Evet:1","|",":")),
	array("tip"=>"text","id"=>"ucret","baslik"=>_LANG('tx_yillik_paket_ucreti_tl')),
	array("tip"=>"textarea","id"=>"avantajlar","baslik"=>_LANG('pn_paket_avantajlari'),"aciklama"=>"Her satıra bir avantaj yazınız. Paket seçim sayfalarında görüntülenir.","satir"=>7),
);	
$liste["uyelik_paketleri"]=array("id"=>"uyelik_paketleri","baslik"=>"Paket","baslik_cogul"=>_LANG('tx_uyelik_paketleri'),"ayarlar"=>$uyelik_paketleri_options);

$magaza_paketleri_options=array(
	array("tip"=>"text","id"=>"paket_adi","baslik"=>_LANG('pn_paket_adi')),
	array("tip"=>"text","id"=>"ilan_limit","baslik"=>_LANG('s_ilan_limiti')),
	array("tip"=>"text","id"=>"foto_limit","baslik"=>_LANG('s_fotograf_limiti')),
	array("tip"=>"text","id"=>"danisman_limit","baslik"=>_LANG('pn_danisman_temsilci_limiti')),
	array("tip"=>"select","id"=>"aninda_onay","baslik"=>_LANG('s_aninda_onay'),"secenekler"=>options("Hayır:0|Evet:1","|",":")),
	array("tip"=>"text","id"=>"ucret","baslik"=>_LANG('tx_yillik_paket_ucreti_tl')),
	array("tip"=>"textarea","id"=>"avantajlar","baslik"=>_LANG('pn_paket_avantajlari'),"aciklama"=>"Her satıra bir avantaj yazınız. Paket seçim sayfalarında görüntülenir.","satir"=>7),
);	
$liste["magaza_paketleri"]=array("id"=>"magaza_paketleri","baslik"=>"Paket","baslik_cogul"=>_LANG('tx_magaza_paketleri'),"ayarlar"=>$magaza_paketleri_options);

$hesaplar_options=array(
	array("tip"=>"text","id"=>"banka_adi","baslik"=>_LANG('pn_banka_adi')),
	array("tip"=>"text","id"=>"ad_soyad","baslik"=>"Ad Soyad"),
	array("tip"=>"text","id"=>"iban","baslik"=>_LANG('pn_iban_numarasi')),
);	
$liste["hesaplar"]=array("id"=>"hesaplar","baslik"=>"Hesap","baslik_cogul"=>_LANG('tx_banka_hesaplari'),"ayarlar"=>$hesaplar_options);

if (mysqli_num_rows(mysqli_query($baglan,"SHOW TABLES LIKE '%odemeler%'"))==0) {
	mysqli_query($baglan,"
	CREATE TABLE IF NOT EXISTS `odemeler` (
	  `id` int(10) NOT NULL AUTO_INCREMENT,
	  `uye_id` int(10) NOT NULL,
	  `ad_soyad` varchar(20) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
	  `hesap` varchar(50) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
	  `tarih_saat` varchar(20) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
	  `tutar` int(10) NOT NULL,
	  `mesaj` text CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
	  `aciklama` text CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
	  PRIMARY KEY (`id`),
	  KEY `uye_id` (`uye_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci AUTO_INCREMENT=1 ;	
	");
}
if (mysqli_num_rows(mysqli_query($baglan,"SHOW TABLES LIKE '%dopingler%'"))==0) {
	mysqli_query($baglan,"
	CREATE TABLE IF NOT EXISTS `dopingler` (
	  `id` int(10) NOT NULL AUTO_INCREMENT,
	  `ilan_id` int(10) NOT NULL,
	  `doping` varchar(50) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
	  `gun` int(10) NOT NULL,
	  `tarih_saat` varchar(20) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
	  `bitis_tarih_saat` varchar(200) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
	  `durum` int(1) NOT NULL,
	  `siparis_id` int(10) NOT NULL,
	  PRIMARY KEY (`id`),
	  KEY `doping` (`doping`),
	  KEY `durum` (`durum`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci AUTO_INCREMENT=1 ;	
	");
}
if (mysqli_num_rows(mysqli_query($baglan,"SHOW TABLES LIKE '%siparisler%'"))==0) {
	mysqli_query($baglan,"
	CREATE TABLE IF NOT EXISTS `siparisler` (
	  `id` int(10) NOT NULL AUTO_INCREMENT,
	  `alici_id` int(10) NOT NULL,
	  `satici_id` int(10) NOT NULL,
	  `ilan_id` int(10) NOT NULL,
	  `magaza_id` int(10) NOT NULL,
	  `paket_id` int(10) NOT NULL,
	  `tutar` int(10) NOT NULL,
	  `tarih_saat` varchar(20) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
	  `tip` varchar(20) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
	  `durum` int(1) NOT NULL,
	  `satici_durum` int(1) NOT NULL,
	  `alici_onay` int(1) NOT NULL,
	  `satici_odeme` int(1) NOT NULL,
	  PRIMARY KEY (`id`),
	  KEY `tip` (`tip`),
	  KEY `durum` (`durum`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci AUTO_INCREMENT=1 ;	
	");
}
if (mysqli_num_rows(mysqli_query($baglan,"SHOW TABLES LIKE '%ajax_upload%'"))==0) {
	mysqli_query($baglan,"
	CREATE TABLE IF NOT EXISTS `ajax_upload` (
	  `id` int(10) NOT NULL AUTO_INCREMENT,
	  `uye_id` int(10) NOT NULL,
	  `kat` int(10) NOT NULL,
	  `url` varchar(200) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
	  `tarih_saat` varchar(20) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
	  `vitrin` int(1) NOT NULL,
	  PRIMARY KEY (`id`),
	  KEY `uye_id` (`uye_id`),
	  KEY `kat` (`kat`)	  
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci AUTO_INCREMENT=1 ;	
	");
}
if (mysqli_num_rows(mysqli_query($baglan,"SHOW TABLES LIKE '%kayitlar%'"))==0) {
	mysqli_query($baglan,"
	CREATE TABLE IF NOT EXISTS `kayitlar` (
	  `id` int(10) NOT NULL AUTO_INCREMENT,
	  `uye_id` int(10) NOT NULL,
	  `veri_1` varchar(200) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
	  `veri_2` varchar(200) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
	  `tarih_saat` varchar(20) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
	  `tip` varchar(50) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
	  PRIMARY KEY (`id`),
	  KEY `uye_id` (`uye_id`),
	  KEY `tip` (`tip`)	  
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci AUTO_INCREMENT=1 ;	
	");
}
    
}

function resim_getir($boyut=null,$icerik_id=null) {
	if (!$boyut) {
		$boyut="full";
	}
	if (!$icerik_id) {
		$icerik_id=$_GET["id"];
	}
	$resim_url=thumbnail($boyut,$icerik_id);
	if (!$resim_url) {
		$resim_url=tema_dizini()."/img/yok/$boyut.png";
	}
	return $resim_url;
}

$bilesen_alani[]=array("id"=>"anasayfa_sidebar","baslik"=>"Anasayfa Sidebar","aciklama"=>_LANG('tx_anasayfa_sol_menu_alani_menu_ve_reklam_alanlar'),"resim"=>tema_dizini()."/img/bilesen/anasayfa_sidebar.png");
$bilesen_alani[]=array("id"=>"anasayfa","baslik"=>_LANG('pn_anasayfa_genis'),"aciklama"=>"Slider, 6 ve 7 sütunlu ilan listelemeleri için uygun bileşen alanı.","resim"=>tema_dizini()."/img/bilesen/anasayfa.png");
$bilesen_alani[]=array("id"=>"anasayfa_sol","baslik"=>_LANG('pn_anasayfa_sol_bolum'),"aciklama"=>"2 veya 3 sütunlu ilan listelemeleri için uygun bileşen alanı. Opsiyoneldir.","resim"=>tema_dizini()."/img/bilesen/anasayfa_sol.png");
$bilesen_alani[]=array("id"=>"anasayfa_sag","baslik"=>_LANG('pn_anasayfa_sag_bolum'),"aciklama"=>"Sağ sidebar olarak tanımlayabiliriz. Reklam alanları için uygundur. Opsiyoneldir.","resim"=>tema_dizini()."/img/bilesen/anasayfa_sag.png");
$bilesen_alani[]=array("id"=>"anasayfa_ust","baslik"=>_LANG('pn_anasayfa_ust_genis'),"aciklama"=>"Haritalı ilan listeleme bileşenlerine uygun tam genişlikteki bileşen alanı.","resim"=>tema_dizini()."/img/bilesen/anasayfa_ust.png");

include_once("bilesen/resimsiz_listeleme.php");
include_once("bilesen/ilan_listeleme.php");
include_once("bilesen/yazi_listeleme.php");
include_once("bilesen/slider.php");
include_once("bilesen/tab_listeleme.php");
include_once("bilesen/anasayfa_sol_menu.php");
include_once("bilesen/ozel_banner.php");
include_once("bilesen/ikon_menu.php");
include_once("bilesen/sidebar_istatistikler.php");
include_once("bilesen/youtube_video.php");
include_once("bilesen/magazalar.php");
include_once("bilesen/harita_listeleme.php");
include_once("bilesen/html_kod.php");
include_once("bilesen/mobil_slider.php");
include_once("bilesen/ikili_ilan_listeleme.php");

function ust_kategoriler($kategori_id,$x=1) {
	global $array;
	if (!is_array($array)) {
		$array=array();
	}
	$ust_kategori=getir("kategoriler","ust_kategori",$kategori_id);
	if ($ust_kategori!=0) {
		$array[$x]=$ust_kategori;
		ust_kategoriler($ust_kategori,$x+1);
	}
	krsort($array);
	return $array;
}
function giris_url($back=null) {
	$site_url=site_url();
	$url="$site_url/sayfa/giris-yap/";
	if ($back) {
		$url.="?git=$back";
	}
	return $url;
}
function kayit_url($back=null) {
	$site_url=site_url();
	$url="$site_url/sayfa/kayit-ol/";
	if ($back) {
		$url.="?git=$back";
	}
	return $url;
}
function ozel_url($page) {
	$site_url=site_url();
	$url="$site_url/sayfa/$page/";
	return $url;
}
function form_group($array) {
	@$tip=$array["tip"];
	@$id=$array["id"];
	@$baslik=$array["baslik"];
	@$deger=$array["deger"];
	@$gerekli=$array["gerekli"];
	@$pasif=$array["pasif"];
	@$secenekler=$array["secenekler"];
	@$kosul=$array["kosul"];
	@$aciklama=$array["aciklama"];
	@$satir=$array["satir"];
	@$size=$array["size"];
	@$grup=$array["grup"];
	if (isset($size)) {@$size_yaz="size='$size'";} else {$size_yaz=null;}
	if ($gerekli==1) {@$gerekli_yaz="required";$gerekli_yildiz="<span style='color:#e74c3c;margin-left:3px;'>*</span>";} else {$gerekli_yaz=null;$gerekli_yildiz=null;}
	if ($pasif==1) {@$pasif_yaz="disabled";} else {$pasif_yaz=null;}
	if ($aciklama) {@$aciklama_yaz="<p class='help-block'>$aciklama</p>";} else {$aciklama_yaz=null;}
	
	if ($grup) {
		if ($grup==2) {
			echo "<div class='col-md-21'>";
		} else if ($grup==3) {
			echo "<div class='col-md-14'>";
		}
	}
	if ($tip=="text" or $tip=="email" or $tip=="password") {
		if ($baslik) {
			$baslik_yaz="<label for='$id'>$baslik $gerekli_yildiz</label>";
		} else {
			$baslik_yaz=null;
		}
		echo "
		<div id='form_$id' class='form-group'>
			$baslik_yaz
			$aciklama_yaz
			<input type='$tip' class='form-control' id='$id' name='$id' placeholder='$baslik' value='$deger' $pasif_yaz $gerekli_yaz>
		</div>";
	} else if ($tip=="hidden") {
		echo "
		<div id='form_$id'>
			<input type='hidden' id='$id' name='$id' value='$deger'>
		</div>";
	} else if ($tip=="textarea") {
		echo "
		<div id='form_$id' class='form-group'>
			<label for='$id'>$baslik $gerekli_yildiz</label>
			$aciklama_yaz
			<textarea type='$tip' class='form-control' id='$id' name='$id' placeholder='$baslik' rows='$satir' $pasif_yaz $gerekli_yaz>$deger</textarea>
		</div>";
	} else if ($tip=="select") {
		
		foreach ($secenekler as $yaz) {
			$secenek_deger=$yaz["deger"];
			$secenek_baslik=$yaz["baslik"];
			if ($secenek_deger==$deger) {$selected="selected='selected'";} else {$selected=null;}
			@$secenek_yaz.="<option value='$secenek_deger' $selected>$secenek_baslik</option>";
		}
		echo "
		<div id='form_$id' class='form-group'>
			<label for='$id'>$baslik $gerekli_yildiz</label>
			$aciklama_yaz
			<select id='$id' name='$id' class='form-control' $size_yaz $pasif_yaz $gerekli_yaz>
			$secenek_yaz
			</select>
		</div>";
	} else if ($tip=="image_select") {
		$img_sayisi=0;
		foreach ($secenekler as $yaz => $x) {
			$secenek_deger=$yaz["deger"];
			$secenek_baslik=$yaz["baslik"];
			$senecek_resim=$x["resim"];
			if ($secenek_deger==$value) {$selected="selected='selected'";} else {$selected="";}
			if ($senecek_resim) {$data_img_yaz="data-img-src='$senecek_resim' data-img-label='$secenek_baslik'";} else {$data_img_yaz=null;}
			@$secenek_yaz.="<option $data_img_yaz value='$secenek_deger'$selected>$secenek_baslik</option>";
			if ($senecek_resim) {$img_sayisi++;}
		}
		echo "
		<div id='form_$id' class='form-group'>
			<label for='$id'>$baslik $gerekli_yildiz</label>
			$aciklama_yaz
			<select id='$id' name='$id' class='form-control' $pasif_yaz $gerekli_yaz>
			$secenek_yaz
			</select>
		</div>";
		if ($img_sayisi>0) {
		echo "
		<script type='text/javascript'>
			$('#$id').imagepicker({
				show_label:true
			});
		</script>
		";
		}
	} else if ($tip=="date") {
		echo "
		<div id='form_$id' class='form-group'>
			<label for='$id'>$baslik $gerekli_yildiz</label>
			$aciklama_yaz
			<div class='input-group date' id='$id'>
			  <span class='input-group-addon'><i class='glyphicon glyphicon-th'></i></span><input type='text' class='form-control' id='$id' name='$id' value='$deger'>
			</div>
		</div>
		<script type='text/javascript'>
			$('#$id').datepicker();
		</script>
		";
	} else if ($tip=="date_time") {
		echo "
		<div id='form_$id' class='form-group'>
			<label for='$id'>$baslik $gerekli_yildiz</label>
			$aciklama_yaz	
			<div class='input-group date' id='$id' data-provide='datetimepicker'>
				<span class='input-group-addon'>
					<i class='glyphicon glyphicon-th'></i>
				</span>
				<input type='text' class='form-control' id='$id' name='$id' value='$deger'>
			</div>		
		</div>
		<script type='text/javascript'>
			$(function () {
				$('#$id').datetimepicker({
					format: 'yyyy-mm-dd hh:ii',
					todayBtn: true,
				});
			});
		</script>";
	} else if ($tip=="color") {
		echo "
		<div id='form_$id' class='form-group'>
			<label for='$id'>$baslik $gerekli_yildiz</label>
			$aciklama_yaz
			<div id='colorpicker_$id' class='input-group colorpicker-component'>
				<span class='input-group-addon'><i></i></span>
				<input type='text' id='$id' name='$id' value='$deger' class='form-control' $pasif_yaz $gerekli_yaz />
			</div>	
		</div>
		<script>$('#colorpicker_$id ').colorpicker();</script>";
	} else if ($tip=="checkbox") {
		if ($deger==1) {$checked="checked='checked'";} else {$checked="";}
		echo "
		<div id='form_$id' class='form-group'>
			<div class='checkbox' style='margin:5px 0 !important;'>
				$aciklama_yaz
				<label>
				  <input type='checkbox' name='$id' value='1' $checked> $baslik
				</label>
			</div>
		</div>";
	} else if ($tip=="multiple_checkbox") {
		echo "
		<div id='form_$id'>
		<label for='$id'>$baslik</label>
		$aciklama_yaz
		";
		foreach ($secenekler as $yaz => $x) {
			$secenek_deger=$x["deger"];
			$secenek_baslik=$x["baslik"];
			if(strstr($deger, $secenek_deger)) {$checked="checked='checked'";} else {$checked="";}
			echo "
			<div class='checkbox'>
			<label><input type='checkbox' name=".$id."[] value='$secenek_deger' $checked>$secenek_baslik</label>
			</div>";
		}
		echo "</div>";
	} else if ($tip=="multiple_select") {
		foreach ($secenekler as $yaz) {
			$secenek_deger=$yaz["deger"];
			$secenek_baslik=$yaz["baslik"];
			if(strstr($deger, $secenek_deger)) {$selected="selected='selected'";} else {$selected="";}
			@$secenek_yaz.="<option value='$secenek_deger' $selected>$secenek_baslik</option>";
		}
		echo "
		<div id='form_$id' class='form-group'>
			<label for='$id'>$baslik $gerekli_yildiz</label>
			$aciklama_yaz
			<select id='$id' name=".$id."[] class='form-control' $size_yaz $pasif_yaz $gerekli_yaz multiple>
			$secenek_yaz
			</select>
		</div>";
	} else if ($tip=="grup_ac") {
		echo "<div class='row'>";
	} else if ($tip=="grup_kapat") {
		echo "</div>";
	}
	
	if ($grup) {
		echo "</div>";
	}
	
	if ($kosul) {
		$kosul_parcala=explode(":",$kosul);
		$diger_ayar=$kosul_parcala[0];
		$kosul_kalan=$kosul_parcala[1];
		$kosul_kosul_parcala=explode("(",$kosul_kalan);
		$kosul_sart=$kosul_kosul_parcala[0];
		$kosul_deger=rtrim($kosul_kosul_parcala[1],")");
		echo "
		<script type='text/javascript'>
			$(function(){
				var kosul='$kosul_sart';
				var deger='$kosul_deger';
				var diger_ayar_value=$('#$diger_ayar').val();
				if (kosul=='==') {
					if (diger_ayar_value!=deger) {
						$('#form_$id').hide();
					}
				} else if (kosul=='!=') {
					if (diger_ayar_value==deger) {
						$('#form_$id').hide();
					}
				}
				$('body').on('change','#$diger_ayar',function(){
					var diger_ayar_value=$('#$diger_ayar').val();
					if (kosul=='==') {
						if (diger_ayar_value==deger) {
							$('#form_$id').show(100);
						} else {
							$('#form_$id').hide(100);
						}
					} else if (kosul=='!=') {
						if (diger_ayar_value!=deger) {
							$('#form_$id').show(100);
						} else {
							$('#form_$id').hide(100);
						}
					}
				});
			});
		</script>
		";
	}	
}
function diziye_donustur($veri) {
	$parcala=explode("\r\n",$veri);
	foreach ($parcala as $parca) {
		$bol=explode("|",$parca);
		if ($parca) {
		$key=trim($bol[0]);
		$yeni_dizi[$key]=$parca;
		}
	}
	return $yeni_dizi;	
}
function ozel_alan_secenekler($veri) {
	$parcala=explode("|",$veri);
	@$secenekler=$parcala[3];
	if ($secenekler) {
		$bol=explode(",",$secenekler);
		foreach($bol as $b) {
			$dizi[]=trim($b);
		}
		return $dizi;
	} else {
		return false;
	}
}

function il_getir($icerik_id) {
	$bilgi=bilgi("il",$icerik_id);
	if ($bilgi) {
		return $il=getir("iller","il_adi",$bilgi);
	}
}
function ilce_getir($icerik_id) {
	$bilgi=bilgi("ilce",$icerik_id);
	if ($bilgi) {
		return $ilce=getir("ilceler","ilce_adi",$bilgi);
	}
}
function mahalle_getir($icerik_id) {
	$bilgi=bilgi("mahalle",$icerik_id);
	if ($bilgi) {
		return $mahalle=getir("mahalleler","mahalle_adi",$bilgi);
	}
}
function magaza_link_getir($id) {
	$magaza_url_yapisi=tema_ayar("magaza_url_yapisi");
	if ($magaza_url_yapisi==2 and !is_localhost()) {
		$magaza_subdomain=getir("magazalar","subdomain",$id);
		return subdomain_url($magaza_subdomain);
	} else {
		$magaza_baslik=getir("magazalar","baslik",$id);
		$url=url_olustur($magaza_baslik);
		$site_url=site_url();
		return ("$site_url/magaza/$url-$id");
	}
}
function uye_sabit_ilan_limiti($cikti,$uye_id=null) {
	global $baglan;
	if (!$uye_id) {
		$uye_id=$_SESSION["oturum_id"];
	}
	$mevcut=mysqli_num_rows(mysqli_query($baglan,"select id from icerik where tip='ilan' and yazar='$uye_id'"));
	$mevcut_magaza=mysqli_num_rows(mysqli_query($baglan,"select i.id from icerik as i INNER JOIN ekstra_bilgi as eb on i.id=eb.id and eb.tip='ilan' and eb.anahtar='magaza' where i.tip='ilan' and i.yazar='$uye_id'"));
	$toplam_yayinda_bireysel=mysqli_num_rows(mysqli_query($baglan,"select id from icerik where tip='ilan' and (durum='1' or durum='2') and yazar='$uye_id'"));
	$toplam_yayinda_magaza=mysqli_num_rows(mysqli_query($baglan,"select i.id from icerik as i INNER JOIN ekstra_bilgi as eb on i.id=eb.id and eb.tip='ilan' and eb.anahtar='magaza' where i.tip='ilan' and i.yazar='$uye_id' and (i.durum='1' or i.durum='2')"));
	$toplam_yayinda=$toplam_yayinda_bireysel-$toplam_yayinda_magaza;
	$mevcut-=$mevcut_magaza;
	$uyeye_ozel_limitler=uye_bilgi("ozel_limitler",$uye_id);
    if ($uyeye_ozel_limitler) {
        $uye_ilan_limit=uye_bilgi("uye_ilan_limit",$uye_id);	
        $kategori_limit=uye_bilgi("uye_kategori_limit",$uye_id);	
        $foto_limit=uye_bilgi("uye_foto_limit",$uye_id);	
        $aninda_onay=uye_bilgi("uye_aninda_onay",$uye_id);
        $kalan_ilan=$uye_ilan_limit-$toplam_yayinda; 
    } else {
        $paket=uye_bilgi("paket",$uye_id);
        $paket_adi=liste_bilgi("paket_adi",$paket);
        $uye_ilan_limit=liste_bilgi("ilan_limit",$paket);
        $kategori_limit=liste_bilgi("kategori_limit",$paket);
        $foto_limit=liste_bilgi("foto_limit",$paket);
        $aninda_onay=liste_bilgi("aninda_onay",$paket);
        $kalan_ilan=$uye_ilan_limit-$toplam_yayinda;
    }
    if ($cikti=="kalan" or !$cikti) {
        return $kalan_ilan;
    } else if ($cikti=="toplam") {
        return $uye_ilan_limit;
    } else if ($cikti=="kategori") {
        return $kategori_limit;
    } else if ($cikti=="foto") {
        return $foto_limit;
    } else if ($cikti=="mevcut") {
        return $mevcut;
    } else if ($cikti=="mevcut_magaza") {
        return $mevcut_magaza;
    } else if ($cikti=="yayinda") {
        return $toplam_yayinda;
    } else if ($cikti=="yayinda_magaza") {
        return $toplam_yayinda_magaza;
    } else if ($cikti=="aninda_onay") {
        return $aninda_onay;
    }
}
function uye_ilan_limiti($cikti,$uye_id=null) {
    $limit_politikasi=limit_politikasi();
    
	global $baglan;
	if (!$uye_id) {
		$uye_id=$_SESSION["oturum_id"];
	}
	$mevcut=mysqli_num_rows(mysqli_query($baglan,"select id from icerik where tip='ilan' and yazar='$uye_id'"));
	$mevcut_magaza=mysqli_num_rows(mysqli_query($baglan,"select i.id from icerik as i INNER JOIN ekstra_bilgi as eb on i.id=eb.id and eb.tip='ilan' and eb.anahtar='magaza' where i.tip='ilan' and i.yazar='$uye_id'"));
	$toplam_yayinda_bireysel=mysqli_num_rows(mysqli_query($baglan,"select id from icerik where tip='ilan' and (durum='1' or durum='2') and yazar='$uye_id'"));
	$toplam_yayinda_magaza=mysqli_num_rows(mysqli_query($baglan,"select i.id from icerik as i INNER JOIN ekstra_bilgi as eb on i.id=eb.id and eb.tip='ilan' and eb.anahtar='magaza' where i.tip='ilan' and i.yazar='$uye_id' and (i.durum='1' or i.durum='2')"));
	$toplam_yayinda=$toplam_yayinda_bireysel-$toplam_yayinda_magaza;
	$mevcut-=$mevcut_magaza;
	$uyeye_ozel_limitler=uye_bilgi("ozel_limitler",$uye_id);
    if ($limit_politikasi==1) {
        if ($uyeye_ozel_limitler) {
            $uye_ilan_limit=uye_bilgi("uye_ilan_limit",$uye_id);	
            $kategori_limit=uye_bilgi("uye_kategori_limit",$uye_id);	
            $foto_limit=uye_bilgi("uye_foto_limit",$uye_id);	
            $aninda_onay=uye_bilgi("uye_aninda_onay",$uye_id);
            $kalan_ilan=$uye_ilan_limit-$toplam_yayinda; 
        } else {
            $paket=uye_bilgi("paket",$uye_id);
            $paket_adi=liste_bilgi("paket_adi",$paket);
            $uye_ilan_limit=liste_bilgi("ilan_limit",$paket);
            $kategori_limit=liste_bilgi("kategori_limit",$paket);
            $foto_limit=liste_bilgi("foto_limit",$paket);
            $aninda_onay=liste_bilgi("aninda_onay",$paket);
            $kalan_ilan=$uye_ilan_limit-$toplam_yayinda;
        }
        if ($cikti=="kalan" or !$cikti) {
            return $kalan_ilan;
        } else if ($cikti=="toplam") {
            return $uye_ilan_limit;
        } else if ($cikti=="kategori") {
            return $kategori_limit;
        } else if ($cikti=="foto") {
            return $foto_limit;
        } else if ($cikti=="mevcut") {
            return $mevcut;
        } else if ($cikti=="mevcut_magaza") {
            return $mevcut_magaza;
        } else if ($cikti=="yayinda") {
            return $toplam_yayinda;
        } else if ($cikti=="yayinda_magaza") {
            return $toplam_yayinda_magaza;
        } else if ($cikti=="aninda_onay") {
            return $aninda_onay;
        }
	} else {
        $uye_ilan_limit=uye_bilgi("azalan_ilan_limit",$uye_id);
        $kategori_limit=uye_bilgi("azalan_kategori_limit",$uye_id);
        $foto_limit=uye_bilgi("azalan_foto_limit",$uye_id);
        $aninda_onay=uye_bilgi("azalan_aninda_onay",$uye_id);
        if ($cikti=="kalan" or !$cikti) {
            return $uye_ilan_limit;
        } else if ($cikti=="toplam") {
            return $uye_ilan_limit;
        } else if ($cikti=="kategori") {
            return $kategori_limit;
        } else if ($cikti=="foto") {
            return $foto_limit;
        } else if ($cikti=="aninda_onay") {
            return $aninda_onay;
        } else if ($cikti=="mevcut") {
            return $mevcut;
        } else if ($cikti=="mevcut_magaza") {
            return $mevcut_magaza;
        } else if ($cikti=="yayinda") {
            return $toplam_yayinda;
        } else if ($cikti=="yayinda_magaza") {
            return $toplam_yayinda_magaza;
        }
    }
}
function uye_azalan_ilan_limiti($cikti,$uye_id=null) {
	global $baglan;
	if (!$uye_id) {
		$uye_id=$_SESSION["oturum_id"];
	}
    $uye_ilan_limit=uye_bilgi("azalan_ilan_limit",$uye_id);
    $kategori_limit=uye_bilgi("azalan_kategori_limit",$uye_id);
    $foto_limit=uye_bilgi("azalan_foto_limit",$uye_id);
    $aninda_onay=uye_bilgi("azalan_aninda_onay",$uye_id);
    if ($cikti=="kalan" or !$cikti or $cikti=="toplam") {
        return $uye_ilan_limit;
    } else if ($cikti=="kategori") {
        return $kategori_limit;
    } else if ($cikti=="foto") {
        return $foto_limit;
    } else if ($cikti=="aninda_onay") {
        return $aninda_onay;
    }
}
function uye_kategori_mevcut_ilan_sayisi($kategori_id,$uye_id=null) {
	global $baglan;
	if (!$uye_id) {
		$uye_id=$_SESSION["oturum_id"];
	}
	$toplam_yayinda=mysqli_num_rows(mysqli_query($baglan,"select id from icerik where tip='ilan' and yazar='$uye_id' and (durum='1' or durum='2') and kategori like '%($kategori_id)%'"));
	$toplam_magaza=mysqli_num_rows(mysqli_query($baglan,"select i.id from icerik as i INNER JOIN ekstra_bilgi as eb on i.id=eb.id and eb.anahtar='magaza' where i.tip='ilan' and i.yazar='$uye_id' and (i.durum='1' or i.durum='2') and kategori like '%($kategori_id)%'"));
	return $toplam_yayinda-$toplam_magaza;
}
function limit_dusur($icerik_id) {
    $limit_politikasi=limit_politikasi();
    if ($limit_politikasi==2) {
        $uye_id=getir("icerik","yazar",$icerik_id);
        $magaza_id=bilgi("magaza",$icerik_id);
        if ($magaza_id) {
            // İlan bir mağaza adına eklenmiş. Mağaza ilan sayısı düşürülecek.
            $mevcut_limit=magaza_azalan_ilan_limiti("toplam",$magaza_id);
            magaza_bilgi_guncelle("azalan_ilan_limit",$mevcut_limit-1,$magaza_id);        
        } else {
            // İlan bir üye hesabına eklenmiş. Üye ilan limiti düşürülecek.
            $mevcut_limit=uye_azalan_ilan_limiti("toplam",$uye_id);
            uye_bilgi_guncelle("azalan_ilan_limit",$mevcut_limit-1,$uye_id);        
        }
    }
}
function limit_artir($icerik_id) {
    $limit_politikasi=limit_politikasi();
    if ($limit_politikasi==2) {
        $uye_id=getir("icerik","yazar",$icerik_id);
        $magaza_id=bilgi("magaza",$icerik_id);
        if ($magaza_id) {
            // İlan bir mağaza adına eklenmiş. Mağaza ilan sayısı düşürülecek.
            $mevcut_limit=magaza_azalan_ilan_limiti("toplam",$magaza_id);
            magaza_bilgi_guncelle("azalan_ilan_limit",$mevcut_limit+1,$magaza_id);        
        } else {
            // İlan bir üye hesabına eklenmiş. Üye ilan limiti düşürülecek.
            $mevcut_limit=uye_azalan_ilan_limiti("toplam",$uye_id);
            uye_bilgi_guncelle("azalan_ilan_limit",$mevcut_limit+1,$uye_id);        
        }
    }
}
function magaza_sabit_ilan_limiti($cikti,$magaza_id) {
	global $baglan;
	$mevcut=mysqli_num_rows(mysqli_query($baglan,"select i.id from icerik as i INNER JOIN ekstra_bilgi as eb on i.id=eb.id and eb.tip='ilan' and eb.anahtar='magaza' and eb.deger='$magaza_id' where i.tip='ilan'"));
	$toplam_yayinda=mysqli_num_rows(mysqli_query($baglan,"select i.id from icerik as i INNER JOIN ekstra_bilgi as eb on i.id=eb.id and eb.tip='ilan' and eb.anahtar='magaza' and eb.deger='$magaza_id' where i.tip='ilan' and (i.durum='1' or i.durum='2')"));
	$magaza_ozel_limitler=magaza_bilgi("ozel_limitler",$magaza_id);
	if ($magaza_ozel_limitler) {
		$magaza_ilan_limit=magaza_bilgi("ilan_limit",$magaza_id);	
		$magaza_foto_limit=magaza_bilgi("foto_limit",$magaza_id);	
		$magaza_danisman_limit=magaza_bilgi("danisman_limit",$magaza_id);	
		$magaza_onay=magaza_bilgi("aninda_onay",$magaza_id);	
		$kalan_ilan=$magaza_ilan_limit-$toplam_yayinda;
	} else {
		$paket=magaza_bilgi("paket",$magaza_id);
		$paket_adi=liste_bilgi("paket_adi",$paket);
		$magaza_ilan_limit=liste_bilgi("ilan_limit",$paket);
		$magaza_foto_limit=liste_bilgi("foto_limit",$paket);
		$magaza_danisman_limit=liste_bilgi("danisman_limit",$paket);
		$magaza_onay=liste_bilgi("aninda_onay",$paket);
		$kalan_ilan=$magaza_ilan_limit-$toplam_yayinda;	
	}
	if ($cikti=="kalan" or !$cikti) {
		return $kalan_ilan;
	} else if ($cikti=="toplam") {
		return $magaza_ilan_limit;
	} else if ($cikti=="foto") {
		return $magaza_foto_limit;
	} else if ($cikti=="danisman") {
		return $magaza_danisman_limit;
	} else if ($cikti=="mevcut") {
		return $mevcut;
	} else if ($cikti=="yayinda") {
		return $toplam_yayinda;
	} else if ($cikti=="aninda_onay") {
		return $magaza_onay;
	}
}
function magaza_ilan_limiti($cikti,$magaza_id) {
    $limit_politikasi=limit_politikasi();
	global $baglan;
	if ($limit_politikasi==1) {
        $mevcut=mysqli_num_rows(mysqli_query($baglan,"select i.id from icerik as i INNER JOIN ekstra_bilgi as eb on i.id=eb.id and eb.tip='ilan' and eb.anahtar='magaza' and eb.deger='$magaza_id' where i.tip='ilan'"));
        $toplam_yayinda=mysqli_num_rows(mysqli_query($baglan,"select i.id from icerik as i INNER JOIN ekstra_bilgi as eb on i.id=eb.id and eb.tip='ilan' and eb.anahtar='magaza' and eb.deger='$magaza_id' where i.tip='ilan' and (i.durum='1' or i.durum='2')"));
        $magaza_ozel_limitler=magaza_bilgi("ozel_limitler",$magaza_id);
        if ($magaza_ozel_limitler) {
            $magaza_ilan_limit=magaza_bilgi("ilan_limit",$magaza_id);	
            $magaza_foto_limit=magaza_bilgi("foto_limit",$magaza_id);	
            $magaza_danisman_limit=magaza_bilgi("danisman_limit",$magaza_id);	
            $magaza_onay=magaza_bilgi("aninda_onay",$magaza_id);	
            $kalan_ilan=$magaza_ilan_limit-$toplam_yayinda;
        } else {
            $paket=magaza_bilgi("paket",$magaza_id);
            $paket_adi=liste_bilgi("paket_adi",$paket);
            $magaza_ilan_limit=liste_bilgi("ilan_limit",$paket);
            $magaza_foto_limit=liste_bilgi("foto_limit",$paket);
            $magaza_danisman_limit=liste_bilgi("danisman_limit",$paket);
            $magaza_onay=liste_bilgi("aninda_onay",$paket);
            $kalan_ilan=$magaza_ilan_limit-$toplam_yayinda;	
        }
        if ($cikti=="kalan" or !$cikti) {
            return $kalan_ilan;
        } else if ($cikti=="toplam") {
            return $magaza_ilan_limit;
        } else if ($cikti=="foto") {
            return $magaza_foto_limit;
        } else if ($cikti=="danisman") {
            return $magaza_danisman_limit;
        } else if ($cikti=="mevcut") {
            return $mevcut;
        } else if ($cikti=="yayinda") {
            return $toplam_yayinda;
        } else if ($cikti=="aninda_onay") {
            return $magaza_onay;
        }
        
    } else {
        // Azalan Limitler seçili ise
        $magaza_ilan_limit=magaza_bilgi("azalan_ilan_limit",$magaza_id);
        $foto_limit=magaza_bilgi("azalan_foto_limit",$magaza_id);
        $aninda_onay=magaza_bilgi("azalan_aninda_onay",$magaza_id);
        $magaza_danisman_limit=magaza_bilgi("azalan_danisman_limit",$magaza_id);
        if ($cikti=="kalan" or !$cikti or $cikti=="toplam") {
            return $magaza_ilan_limit;
        } else if ($cikti=="foto") {
            return $foto_limit;
        } else if ($cikti=="danisman") {
            return $magaza_danisman_limit;
        } else if ($cikti=="mevcut") {
            return $mevcut;
        } else if ($cikti=="yayinda") {
            return $toplam_yayinda;
        } else if ($cikti=="aninda_onay") {
            return $aninda_onay;
        } 
    }
}
function magaza_azalan_ilan_limiti($cikti,$magaza_id) {
	global $baglan;
    // Azalan Limitler seçili ise
    $magaza_ilan_limit=magaza_bilgi("azalan_ilan_limit",$magaza_id);
    $foto_limit=magaza_bilgi("azalan_foto_limit",$magaza_id);
    $magaza_danisman_limit=magaza_bilgi("azalan_danisman_limit",$magaza_id);
    $aninda_onay=magaza_bilgi("azalan_aninda_onay",$magaza_id);
    if ($cikti=="kalan" or !$cikti or $cikti=="toplam") {
        return $magaza_ilan_limit;
    } else if ($cikti=="danisman") {
        return $magaza_danisman_limit;
    } else if ($cikti=="foto") {
        return $foto_limit;
    } else if ($cikti=="aninda_onay") {
        return $aninda_onay;
    } 
}
function en_ust_kategori($array) {
	global $baglan;
	$kat_ids=null;
	foreach ($array as $arr){
		$kat_ids.="$arr,";
	}
	$kat_ids=rtrim($kat_ids,",");
	$sor=mysqli_query($baglan,"select * from kategoriler where ust_kategori='0' and id in ($kat_ids)");
	$kat_dizi=mysqli_fetch_array($sor);
	return $kat_id=$kat_dizi["id"];
}
function doping_sor($ilan_id,$doping) {
	global $baglan;
	$sor=mysqli_num_rows(mysqli_query($baglan,"select * from dopingler where ilan_id='$ilan_id' and doping='$doping' and durum='1'"));
	if ($sor>0) {
		return true;
	} else {
		return false;
	}
}
function nokta_sil($fiyat) {
	return str_replace(array("."),array(""),$fiyat);
}
function ilan_pasiflestir() {
	$ilan_suresi=tema_ayar("ilan_suresi");
	if ($ilan_suresi==2) {
		global $baglan;
		$ilan_suresi_kontrol=tema_ayar("ilan_suresi_kontrol");
		$simdi=time();
		if ($simdi-(strtotime($ilan_suresi_kontrol))>60*60) {
			$gun=tema_ayar("gun");
			$kontrol_tarih=date("Y-m-d H:i:s",strtotime("-$gun days"));
			$sql=mysqli_query($baglan,"update icerik set durum='4' where tip='ilan' and durum!='4' and tarih_saat<='$kontrol_tarih'");
			tema_ayar_guncelle("ilan_suresi_kontrol",tarih_saat());
		}
	}
}
ilan_pasiflestir();

function doping_pasiflestir() {
	global $baglan;
	$doping_kontrol=tema_ayar("doping_kontrol");
	$simdi=time();
	if ($simdi-(strtotime($doping_kontrol))>60*60) {
		$suan=date("Y-m-d H:i:s");
		$sor=mysqli_query($baglan,"update dopingler set durum='2' where bitis_tarih_saat<='$suan'");
		tema_ayar_guncelle("doping_kontrol",tarih_saat());
	}
}
doping_pasiflestir();

function fiyat($icerik_id,$fiyat=null) {
	$para_birimi=bilgi("para_birimi",$icerik_id);
	if ($para_birimi=="gmd") {
		$fiyat=bilgi("fiyat",$icerik_id);
	} else if ($para_birimi=="usd") {
		$fiyat=bilgi("fiyat_usd",$icerik_id);
	} else if ($para_birimi=="eur") {
		$fiyat=bilgi("fiyat_eur",$icerik_id);
	} else if ($para_birimi=="gbp") {
		$fiyat=bilgi("fiyat_gbp",$icerik_id);
	}
	$fiyat_yaz=null;
	if ($fiyat) {
		$parcala=explode(",",$fiyat);
		$kusurat=$parcala[1];
		if ($kusurat=="00") {
			$fiyat_son=substr($fiyat,0,-3);
			$fiyat_son=number_format($fiyat_son, 0, ',', '.');
		} else {
			$fiyat_son=$fiyat;
		}
		/* FontAwesome'da dalasi ikonu olmadigi icin GMD metin olarak basilir */
		if ($para_birimi=="gmd") {
			$fiyat_yaz="$fiyat_son GMD";
		} else {
			$fiyat_yaz="$fiyat_son <i class='fa fa-$para_birimi'></i>";
		}
	}
	return $fiyat_yaz;
}
function fiyat_donustur($fiyat)  {
    $fiyat=str_replace(",",".",$fiyat);
	if (is_numeric($fiyat)) {
		return $fiyat_son=number_format($fiyat,0,',','.');
	}
}
/* Ilan fiyati yabanci birimde (USD/EUR/GBP) ise GMD karsiligini hesaplar. */
function gmd_karsilik_guncelle() {
	global $baglan;
	$usd_sql=mysqli_query($baglan,"select i.id from icerik as i INNER JOIN ekstra_bilgi as eb on i.id=eb.id and eb.anahtar='para_birimi' and eb.deger='usd' where i.tip='ilan' and i.durum='1'");
	while ($usd_dizi=mysqli_fetch_array($usd_sql)) {
		$ilan_id=$usd_dizi["id"];
		@$fiyat_usd=bilgi("fiyat_usd",$ilan_id);
		@$usd_kur=tema_ayar("usd_kur");
		if (isset($fiyat_usd) and isset($usd_kur) and is_numeric($usd_kur)) {
			/* PHP 8'de "9000,00" gibi virgullu metin carpimda uyari uretir;
			   once virgul noktaya cevrilip numerikligi sinanir */
			$fiyat_baz=str_replace(",", ".", $fiyat_usd);
			if (is_numeric($fiyat_baz)) {
				$fiyat=$fiyat_baz*$usd_kur;
				$fiyat=str_replace(array("."),array(","),$fiyat);
				bilgi_guncelle("fiyat",$fiyat,$ilan_id);
			}
		}
	}
	$eur_sql=mysqli_query($baglan,"select i.id from icerik as i INNER JOIN ekstra_bilgi as eb on i.id=eb.id and eb.anahtar='para_birimi' and eb.deger='eur' where i.tip='ilan' and i.durum='1'");
	while ($eur_dizi=mysqli_fetch_array($eur_sql)) {
		$ilan_id=$eur_dizi["id"];
		@$fiyat_eur=bilgi("fiyat_eur",$ilan_id);
		@$eur_kur=tema_ayar("eur_kur");
		if (isset($fiyat_eur) and isset($eur_kur) and is_numeric($eur_kur)) {
			$fiyat_baz=str_replace(",", ".", $fiyat_eur);
			if (is_numeric($fiyat_baz)) {
				$fiyat=$fiyat_baz*$eur_kur;
				$fiyat=str_replace(array("."),array(","),$fiyat);
				bilgi_guncelle("fiyat",$fiyat,$ilan_id);
			}
		}
	}
	$gbp_sql=mysqli_query($baglan,"select i.id from icerik as i INNER JOIN ekstra_bilgi as eb on i.id=eb.id and eb.anahtar='para_birimi' and eb.deger='gbp' where i.tip='ilan' and i.durum='1'");
	while ($gbp_dizi=mysqli_fetch_array($gbp_sql)) {
		$ilan_id=$gbp_dizi["id"];
		@$fiyat_gbp=bilgi("fiyat_gbp",$ilan_id);
		@$gbp_kur=tema_ayar("gbp_kur");
		if (isset($fiyat_gbp) and isset($gbp_kur) and is_numeric($gbp_kur)) {
			$fiyat_baz=str_replace(",", ".", $fiyat_gbp);
			if (is_numeric($fiyat_baz)) {
				$fiyat=$fiyat_baz*$gbp_kur;
				$fiyat=str_replace(array("."),array(","),$fiyat);
				bilgi_guncelle("fiyat",$fiyat,$ilan_id);
			}
		}
	}
}
function galeri_sira_update($resim,$icerik_id) {
	global $baglan;	
	$galeri_hazirlik=mysqli_fetch_array(mysqli_query($baglan,"select id from galeri where resim='$resim' and icerik_id='$icerik_id'"));
	$vitrin_galeri_id=$galeri_hazirlik["id"];
	guncelle("galeri","sira",1,$vitrin_galeri_id);
	$sor=mysqli_query($baglan,"select id from galeri where icerik_id='$icerik_id' and resim!='$resim' order by id asc");
	$x=2;
	while ($dizi=mysqli_fetch_array($sor)) {
		$gal_id=$dizi["id"];
		guncelle("galeri","sira",$x,$gal_id);
	$x++;}
}
function kur_guncelle() {
$son_guncelleme=tema_ayar("kur_guncelleme");
$kur_guncelleme_dk=tema_ayar("kur_guncelleme_dk");
$simdi=time();
	if ($simdi-(strtotime($son_guncelleme))>60*$kur_guncelleme_dk) {
		/* Gambiya bazli kur kaynagi: 1 USD kac GMD eder.
		 * TCMB (TL) kaynakları GMD tabanli fiyat sistemini bozuyordu. */
		$veriler=@json_decode(@file_get_contents("https://open.er-api.com/v6/latest/USD"), true);
		if (isset($veriler["rates"]["GMD"]) && is_numeric($veriler["rates"]["GMD"])) {
			$usd_kur=$veriler["rates"]["GMD"];
			/* EUR/GBP degerleri USD cinsinden döner; GMD karsiligina cevrilir */
			$eur_gmd = (isset($veriler["rates"]["EUR"]) && $veriler["rates"]["EUR"] > 0) ? $usd_kur / $veriler["rates"]["EUR"] : false;
			$gbp_gmd = (isset($veriler["rates"]["GBP"]) && $veriler["rates"]["GBP"] > 0) ? $usd_kur / $veriler["rates"]["GBP"] : false;
			tema_ayar_guncelle("usd_kur",$usd_kur);
			if ($eur_gmd) { tema_ayar_guncelle("eur_kur",$eur_gmd); }
			if ($gbp_gmd) { tema_ayar_guncelle("gbp_kur",$gbp_gmd); }
			tema_ayar_guncelle("kur_guncelleme",tarih_saat());
			gmd_karsilik_guncelle();
		}
	}
}
kur_guncelle();
function ilan_eklenebilir_magazalar($uye_id) {
	global $baglan;
	$toplam_magazalar=array();
	$sql=mysqli_query($baglan,"select * from magazalar where uye_id='$uye_id' and durum='1'");
	while ($dizi=mysqli_fetch_array($sql)) {
		$magaza_id=$dizi["id"];
		$toplam_magazalar[]=$magaza_id;
	}
	$sql=mysqli_query($baglan,"select * from magaza_uye where uye_id='$uye_id'");
	while ($dizi=mysqli_fetch_array($sql)) {
		$magaza_id=$dizi["magaza_id"];
		$magaza_durum=getir("magazalar","durum",$magaza_id);
		if ($magaza_durum==1) {
			$toplam_magazalar[]=$magaza_id;
		}
	}	
	return $toplam_magazalar;
}
function ilan_eklenebilir_magazalar_option($array) {
	global $baglan;
	foreach ($array as $arr) {
		$magaza_baslik=getir("magazalar","baslik",$arr);
		$magaza_durum=getir("magazalar","durum",$arr);
		if ($magaza_durum==1) {
$son_dizi=array();
			$son_dizi[]=array("deger"=>$arr,"baslik"=>$magaza_baslik);
		}
	}
	return $son_dizi;
}
function sonraki_gorsel($galeri_id) {
	$icerik_id=getir("galeri","icerik_id",$galeri_id);
	$galeri_sira=getir("galeri","sira",$galeri_id);
	$sonraki=mysqli_query($baglan,"select * from galeri where icerik_id='$icerik_id' and sira>$galeri_sira order by sira asc limit 0,1");
	$sonraki_dizi=mysqli_fetch_array($sonraki);
	$sonraki_id=$sonraki_dizi["id"];
}
function resim_yukle_ajax($file,$klasor,$boyutlandir,$cikti=null,$orijinal_sil=0) {
	$resim_dizisi=$file;
	$resim_adi_dizi=$resim_dizisi["name"];
	$resim_adi=str_replace(array(" ",",","ş","ç","ğ","ö","ü","ı"),array("-",null,"s","c","g","o","u","i"),$resim_adi_dizi);
	$resim_uzanti_hazirlik=uzanti_ne($resim_adi);
	$resim_uzanti=strtolower($resim_uzanti_hazirlik);	
	if ($resim_uzanti=="png" or $resim_uzanti=="jpg" or $resim_uzanti=="gif" or $resim_uzanti=="jpeg") {
		$kontrol=1;
		if (!$klasor) {
			$klasor="images";
		}
	} else {
		if (!$klasor) {
			$klasor="files";
		}		
	}
	if ($resim_adi and $kontrol==1) {
	if (!file_exists("../../uploads/$klasor")) {
		mkdir("../../uploads/$klasor");
	}
	$yil=date("Y");
	$ay=date("m");
	$gun=date("d");	
	if (!file_exists("../../uploads/$klasor/$yil")) {
		mkdir("../../uploads/$klasor/$yil");
	}	
	if (!file_exists("../../uploads/$klasor/$yil/$ay")) {
		mkdir("../../uploads/$klasor/$yil/$ay");
	}	
	if (!file_exists("../../uploads/$klasor/$yil/$ay/$gun")) {
		mkdir("../../uploads/$klasor/$yil/$ay/$gun");
	}	
	$kaynak=$resim_dizisi["tmp_name"];
	// Resimlerin yükleneceği klasör yolu algoritması. Buradan değiştirilecek
	$tam_dizin="$klasor/$yil/$ay/$gun";
	$resim_url=site_url()."/uploads/$tam_dizin/".$resim_adi;
	
	$parcala=explode(".$resim_uzanti_hazirlik",$resim_adi);
	$benzersiz=benzersiz();
	$resim_ilk_kisim=str_replace(array("."),array(null),$parcala[0]);
	$resim_adi=$resim_ilk_kisim."-$benzersiz.$resim_uzanti";

	$hedef="../../uploads/$tam_dizin/$resim_adi";
	$site_url=site_url();
	$resim_url=$site_url."/uploads/$tam_dizin/".$resim_adi;
	$yukle=move_uploaded_file($kaynak,$hedef);
	chmod($hedef, 0666);
	
	// Yüklenen öğe resim ise boyutlandırma başladı
	if ($kontrol==1) {
        include_once("class/SimpleImage.php");
        $image = new \claviska\SimpleImage($hedef);
        $image
						->autoOrient()
            ->contrast(-8)
            ->brighten(4)
            ->toFile("../../uploads/$tam_dizin/$resim_adi",null,80);
		if ($boyutlandir) {
			$kucultme_tercihi=tema_ayar("resize_kucult");
			$buyutme_tercihi=tema_ayar("resize_buyut");
			if (!$kucultme_tercihi) {
				$kucultme_tercihi="crop";
			}
			if (!$buyutme_tercihi) {
				$buyutme_tercihi="aspectratio";
			}

			if (count($boyutlandir)>0) {
			
			$hazirlik=explode(".",$resim_adi);
			$resim_adi_sabit=$hazirlik[0];
			$imagesize=getimagesize($hedef);
			$width=$imagesize[0];
			$height=$imagesize[1];
			foreach ($boyutlandir as $yaz) {
				$parcala=explode("_",$yaz);
				$yeni_genislik=$parcala[0];
				$yeni_yukseklik=$parcala[1];
				$resim_son=$yeni_genislik."_".$yeni_yukseklik;	
				$image = new \claviska\SimpleImage($hedef);
				if ($width>=$yeni_genislik and $height>=$yeni_yukseklik) {

					if ($kucultme_tercihi=="crop") {
						// Resim odak noktasına göre kırpılsın
						$image
						-> autoOrient()
						-> thumbnail($yeni_genislik,$yeni_yukseklik)
						-> toFile("../../uploads/$tam_dizin/$resim_adi_sabit-$resim_son.$resim_uzanti",null,80);
					} else if ($kucultme_tercihi=="resize") {
						// Resize ile küçültülsün (En boy oranları bozulacaktır)
						$image
						-> autoOrient()
						-> resize($yeni_genislik,$yeni_yukseklik)
						-> toFile("../../uploads/$tam_dizin/$resim_adi_sabit-$resim_son.$resim_uzanti",null,80);
					} else if ($kucultme_tercihi=="aspectratio") {
						// En boy oranları mutlaka korunsun (Görsel kırpılmadan ya da boyutlandırılmadan küçültülür)
						$aspectratio=$image->getAspectRatio();	
						$image -> autoOrient();
						if ($aspectratio>=1) {
							// Yatay resim
							$image -> resize($yeni_genislik,null);
						} else {
							// Dikey resim
							$image -> resize(null,$yeni_yukseklik);
						}
						$zemin_image = new \claviska\SimpleImage();
						$zemin_image
						-> fromNew($yeni_genislik, $yeni_yukseklik,"#fff")
						-> overlay($image, 'center center')
						-> toFile("../../uploads/$tam_dizin/$resim_adi_sabit-$resim_son.$resim_uzanti",null,80);
					}				

				} else {
					
					if ($buyutme_tercihi=="resize") {
						// Resize ile büyütülsün (en boy oranları bozulacaktır)
						$image
						-> autoOrient()
						-> resize($yeni_genislik,$yeni_yukseklik)
						-> toFile("../../uploads/$tam_dizin/$resim_adi_sabit-$resim_son.$resim_uzanti",null,80);
					} else if ($buyutme_tercihi=="aspectratio") {
						// En boy oranları mutlaka korunsun (resim tam boyutlardaki beyaz bir zemin üzerine yerleştirilir)
						$image
						-> autoOrient()
						-> bestFit($yeni_genislik,$yeni_yukseklik);
						$zemin_image = new \claviska\SimpleImage();
						$zemin_image
						-> fromNew($yeni_genislik, $yeni_yukseklik,"#fff")
						-> overlay($image, 'center center')
						-> toFile("../../uploads/$tam_dizin/$resim_adi_sabit-$resim_son.$resim_uzanti",null,80);
					} else if ($buyutme_tercihi=="standart") {
						// Standart (Görseller en boy oranları korunarak mümkün olduğu kadar büyütülür)
						$aspectratio=$image->getAspectRatio();	
						$image -> autoOrient();						
						if ($aspectratio>=1) {
							// Yatay resim
							$image -> resize($yeni_genislik,null);
						} else {
							// Dikey resim
							$image -> resize(null,$yeni_yukseklik);
						}
						$image
						-> bestFit($yeni_genislik,$yeni_yukseklik);
						$zemin_image = new \claviska\SimpleImage();
						$zemin_image
						-> fromNew($yeni_genislik, $yeni_yukseklik,"#fff")
						-> overlay($image, 'center center')
						-> toFile("../../uploads/$tam_dizin/$resim_adi_sabit-$resim_son.$resim_uzanti",null,80);
					}
				}
			}
			}			

		}
	}
	// Yeniden boyutlandırma bitti
	if (!$cikti) {
		return $resim_url;
	} else {
		$return_url=thumbnail_url($cikti,$resim_url);
		if ($orijinal_sil==1) {
			unlink($hedef);
		}
		return $return_url;
	}
	} else {
		return false;
	}
}
function resim_boyutlandir_ajax($file,$klasor,$boyutlandir,$cikti=null,$orijinal_sil=0) {
	global $baglan;
	$uploads_parcala=explode("uploads/",$file);
	$dizin_say=substr_count($uploads_parcala[1],"/");
	if ($dizin_say>0) {
		$dizin_bol=explode("/",$uploads_parcala[1]);
		$resim_name=$dizin_bol[$dizin_say];
		for($i=1;$i<$dizin_say;$i++){
			$klasor.="/".$dizin_bol[$i];
		}
	}
	
	$hazirlik=explode(".",$resim_name);
	$resim_adi_sabit=$hazirlik[0];
	
	$son_ek=substr($resim_adi_sabit,-13);
	$benzersiz=benzersiz();
	$resim_adi_sabit=str_replace($son_ek,$benzersiz,$resim_adi_sabit);
	
	$resim_uzanti_hazirlik=uzanti_ne($file);
	$resim_uzanti=strtolower($resim_uzanti_hazirlik);	
	
	$kucultme_tercihi=tema_ayar("resize_kucult");
	$buyutme_tercihi=tema_ayar("resize_buyut");
	if (!$kucultme_tercihi) {
		$kucultme_tercihi="crop";
	}
	if (!$buyutme_tercihi) {
		$buyutme_tercihi="aspectratio";
	}
	
	$watermark=tema_ayar("watermark");
	if ($watermark==1) {
		$watermark_logo=tema_ayar("watermark_logo");
	}
	
	if (count($boyutlandir)>0) {
		include_once("class/SimpleImage.php");
		// full görsel rename
		$image = new \claviska\SimpleImage();
		$image->fromFile($file) -> toFile("../../uploads/$klasor/$resim_adi_sabit.$resim_uzanti",null,90);
		
		$eski_url=$file;
		$yeni_url=str_replace($son_ek,$benzersiz,$file);
		mysqli_query($baglan,"update galeri set resim='$yeni_url' where resim='$eski_url'");
		$gorsel_url_guncelle=mysqli_query($baglan,"update ekstra_bilgi set deger='$yeni_url' where anahtar='gorsel' and deger='$eski_url'");
		$ajax_upload_guncelle=mysqli_query($baglan,"update ajax_upload set url='$yeni_url' where url='$eski_url'");
		
		$hedef="../../uploads/$klasor/$resim_name";
		
		$imagesize=getimagesize($hedef);
		$width=$imagesize[0];
		$height=$imagesize[1];
		
		foreach ($boyutlandir as $yaz) {
			$parcala=explode("_",$yaz);
			$yeni_genislik=$parcala[0];
			$yeni_yukseklik=$parcala[1];
			$resim_son=$yeni_genislik."_".$yeni_yukseklik;	
			
			$image = new \claviska\SimpleImage();
			$image->fromFile($file);
			
			if ($watermark==1 and $watermark_logo and file_exists($watermark_logo)) {
				$image->overlay($watermark_logo, "center center");
			}
			
			if ($width>=$yeni_genislik and $height>=$yeni_yukseklik) {

				if ($kucultme_tercihi=="crop") {
					// Resim odak noktasına göre kırpılsın
					$image
					-> autoOrient()
					-> thumbnail($yeni_genislik,$yeni_yukseklik)
					-> toFile("../../uploads/$klasor/$resim_adi_sabit-$resim_son.$resim_uzanti",null,90);
				} else if ($kucultme_tercihi=="resize") {
					// Resize ile küçültülsün (En boy oranları bozulacaktır)
					$image
					-> autoOrient()
					-> resize($yeni_genislik,$yeni_yukseklik)
					-> toFile("../../uploads/$klasor/$resim_adi_sabit-$resim_son.$resim_uzanti",null,90);
				} else if ($kucultme_tercihi=="aspectratio") {
					// En boy oranları mutlaka korunsun (Görsel kırpılmadan ya da boyutlandırılmadan küçültülür)
					$aspectratio=$image->getAspectRatio();	
					$image -> autoOrient();
					if ($aspectratio>=1) {
						// Yatay resim
						$image -> resize($yeni_genislik,null);
					} else {
						// Dikey resim
						$image -> resize(null,$yeni_yukseklik);
					}
					$zemin_image = new \claviska\SimpleImage();
					$zemin_image
					-> fromNew($yeni_genislik, $yeni_yukseklik,"#fff")
					-> overlay($image, 'center center')
					-> toFile("../../uploads/$klasor/$resim_adi_sabit-$resim_son.$resim_uzanti",null,90);
				}				

			} else {
				
				if ($buyutme_tercihi=="resize") {
					// Resize ile büyütülsün (en boy oranları bozulacaktır)
					$image
					-> autoOrient()
					-> resize($yeni_genislik,$yeni_yukseklik)
					-> toFile("../../uploads/$klasor/$resim_adi_sabit-$resim_son.$resim_uzanti",null,90);
				} else if ($buyutme_tercihi=="aspectratio") {
					// En boy oranları mutlaka korunsun (resim tam boyutlardaki beyaz bir zemin üzerine yerleştirilir)
					$image
					-> autoOrient()
					-> bestFit($yeni_genislik,$yeni_yukseklik);
					$zemin_image = new \claviska\SimpleImage();
					$zemin_image
					-> fromNew($yeni_genislik, $yeni_yukseklik,"#fff")
					-> overlay($image, 'center center')
					-> toFile("../../uploads/$klasor/$resim_adi_sabit-$resim_son.$resim_uzanti",null,90);
				} else if ($buyutme_tercihi=="standart") {
					// Standart (Görseller en boy oranları korunarak mümkün olduğu kadar büyütülür)
					$aspectratio=$image->getAspectRatio();	
					$image -> autoOrient();						
					if ($aspectratio>=1) {
						// Yatay resim
						$image -> resize($yeni_genislik,null);
					} else {
						// Dikey resim
						$image -> resize(null,$yeni_yukseklik);
					}
					$image
					-> bestFit($yeni_genislik,$yeni_yukseklik);
					$zemin_image = new \claviska\SimpleImage();
					$zemin_image
					-> fromNew($yeni_genislik, $yeni_yukseklik,"#fff")
					-> overlay($image, 'center center')
					-> toFile("../../uploads/$klasor/$resim_adi_sabit-$resim_son.$resim_uzanti",null,90);
				}
			}
		}
	}
	// Yeniden boyutlandırma bitti
	if (!$cikti) {
		return $yeni_url;
	} else {
		$return_url=thumbnail_url($cikti,$yeni_url);
		return $return_url;
	}
}

function ajax_sil($url) {
	$ayir=explode("/uploads/",$url);
	$resim_adi=$ayir[1];
	$sil=unlink("../../uploads/$resim_adi");
	if ($sil) {
		return true;
	} else {
		return false;
	}
}
function benzer_kategori_id($kategoriler,$ilan_id,$kademe=null) {
	global $baglan;
	$kategori_sayisi=count($kategoriler);
	if (!$kademe) {
		$kademe=1;
	}
	for ($x=$kademe;$x<$kategori_sayisi;$x++) {
		$sor_kategori=$kategoriler[$kategori_sayisi-$x];
		$benzer_sql_kat=sql_kategori($sor_kategori);
		$benzer_sql=mysqli_query($baglan,"select id from icerik where tip='ilan' and durum='1' and id!='$ilan_id' and ($benzer_sql_kat)");
		$benzer_say=mysqli_num_rows($benzer_sql);
		if ($benzer_say>0) {
			return $sor_kategori;
		} else {
			benzer_kategori_id($kategoriler,$ilan_id,$x+1);
		}
	}
}
function getMaximumFileUploadSize() {  
    $deger=min(convertPHPSizeToBytes(ini_get('post_max_size')), convertPHPSizeToBytes(ini_get('upload_max_filesize')));
	return formatSizeUnits($deger);
}  
function convertPHPSizeToBytes($sSize) {
	$sSuffix = strtoupper(substr($sSize, -1));
	if (!in_array($sSuffix,array('P','T','G','M','K'))){
		return (int)$sSize;  
	} 
	$iValue = substr($sSize, 0, -1);
	switch ($sSuffix) {
	case 'P':
	$iValue *= 1024;
	// Fallthrough intended
	case 'T':
	$iValue *= 1024;
	// Fallthrough intended
	case 'G':
	$iValue *= 1024;
	// Fallthrough intended
	case 'M':
	$iValue *= 1024;
	// Fallthrough intended
	case 'K':
	$iValue *= 1024;
	break;
	}
	return (int)$iValue;
}
function formatSizeUnits($bytes) {
	if ($bytes >= 1073741824) {
		$bytes = number_format($bytes / 1073741824) . ' GB';
	} else if ($bytes >= 1048576) {
		$bytes = number_format($bytes / 1048576) . ' MB';
	} else if ($bytes >= 1024) { 
		$bytes = number_format($bytes / 1024) . ' KB';
	} else if ($bytes > 1) {
		$bytes = $bytes . ' bytes';
	} else if ($bytes == 1) {
		$bytes = $bytes . ' byte';
	} else {
		$bytes = '0 bytes';
	}
	return $bytes;
}
function onceki_galeri_id($galeri_id,$icerik_id=null) {
	if (!$icerik_id) {
		$icerik_id=$_GET["id"];
	}
	global $baglan;
	$galeri_sira=getir("galeri","sira",$galeri_id);
	$sor=mysqli_query($baglan,"select * from galeri where icerik_id='$icerik_id' and sira<'$galeri_sira' order by sira desc limit 1");
	$say=mysqli_num_rows($sor);
	if ($say>0) {
		$dizi=mysqli_fetch_array($sor);
		return $dizi["id"];
	}
}
function sonraki_galeri_id($galeri_id,$icerik_id=null) {
	if (!$icerik_id) {
		$icerik_id=$_GET["id"];
	}
	global $baglan;
	$galeri_sira=getir("galeri","sira",$galeri_id);
	$sor=mysqli_query($baglan,"select * from galeri where icerik_id='$icerik_id' and sira>'$galeri_sira' order by sira asc limit 1");
	$say=mysqli_num_rows($sor);
	if ($say>0) {
		$dizi=mysqli_fetch_array($sor);
		return $dizi["id"];
	}
}



function vitrin_gorseli_update() {
	$kod=null;
	if (isset($_GET["id"])) {
		@$icerik_id=$_GET["id"];
	}
	if (isset($_GET["tip"])) {
		@$icerik_tip=$_GET["tip"];
	}
	
	if (isset($icerik_tip) and $icerik_tip=="ilan" and isset($icerik_id)) {
		$resim_sayisi=resim_sayisi($icerik_id);
		if ($resim_sayisi>0) {
			$galeri_dizisi=galeri_dizisi($icerik_id);
			$vitrin_gorseli=bilgi("gorsel",$icerik_id);
			$vitrin_resim_kucuk=thumbnail_url("530_393",$vitrin_gorseli);
			if (is_array($galeri_dizisi)) {
				$kod.='
				<div class="panel panel-default" style="margin-top:20px;">
				<div class="panel-heading">' . _LANG('hb_vitrin_gorseli') . '</div>
				<div class="panel-body">
				';
				if ($vitrin_resim_kucuk) {
					$kod.='' . _LANG('s_mevcut_gorsel') . '"'.$vitrin_resim_kucuk.'" alt="" id="mevcut-vitrin-gorseli" class="img-responsive" /><div class="clearfix mb15"></div>';				
				} else {
					$kod.='<div class="alert alert-danger">Vitrin görseli bulunamadı</div><div class="clearfix mb15"></div>';
				}
				
				$kod.='<div class="row">
				';
				foreach ($galeri_dizisi as $galeri_resim) {
					$x_id=$galeri_resim["id"];
					$x_resim=$galeri_resim["resim"];
					$x_resim_buyuk=thumbnail_url("530_393",$x_resim);
					$x_resim_full=thumbnail_url("full",$x_resim);
					$x_resim_kucuk=thumbnail_url("160_120",$x_resim);
					
					$kod.='
						<div class="col-md-20" style="position:relative;">
							<img src="'.$x_resim_kucuk.'" alt="" class="img-responsive" />
							';
							
							$kod.='
							<div class="buton" style="position:absolute;top:0;left:10px;background:#000;color:#fff;font-size:11px;">
								<a class="vitrin_degistir" data-id="'.$x_id.'" data-buyuk="'.$x_resim_buyuk.'" data-kucuk="'.$x_resim_kucuk.'" data-full="'.$x_resim_full.'" style="color:#fff;padding:1px 2px;">Ayarla</a>
							</div>								
								';
							
					$kod.='
						</div>
					';
				}
				$kod.='
				</div></div></div>';
				$tema_dizini=tema_dizini();
				$kod.='
				<script type="text/javascript">
				$("body").on("click",".vitrin_degistir",function(){
					var galeri_id=$(this).attr("data-id");
					var galeri_full_url=$(this).attr("data-full");
					var galeri_buyuk_url=$(this).attr("data-buyuk");
					var icerik_id='.$icerik_id.';
					$.ajax({
						type:"post",
						url:"'.$tema_dizini.'/ajax.php?islem=vitrin_degistir",
						data:{"resim_url":galeri_full_url,"icerik_id":icerik_id},
						success:function(cevap){
							$("#mevcut-vitrin-gorseli").attr("src",galeri_buyuk_url);
							alert(cevap);
						}
					});					
				});
				</script>
				';
			}
		}
	}
	return $kod;
}
$panel_icerik_sag_alt_kod[]=vitrin_gorseli_update();

function galeri_dizisi($icerik_id=null) {
	if (!$icerik_id) {
		$icerik_id=$_GET["id"];
	}
	global $baglan;
	$sor=mysqli_query($baglan,"select id, resim from galeri where icerik_id='$icerik_id' order by sira asc");
	$say=mysqli_num_rows($sor);
	if ($say>0) {
		while ($galeri_dizisi=mysqli_fetch_array($sor)) {
			$galeri_id=$galeri_dizisi["id"];
			$galeri_resim=$galeri_dizisi["resim"];
			$dizi[]=array("id"=>$galeri_id,"resim"=>$galeri_resim);
		}
	}
	return $dizi;
}

function panel_yukle($file,$klasor,$boyutlandir=null,$cikti=null,$orijinal_sil=0) {
	global $admin_dizini;
	$resim_dizisi=$file;
	$resim_adi_dizi=$resim_dizisi["name"];
	$resim_adi=str_replace(array(" ",",","ş","ç","ğ","ö","ü","ı"),array("-",null,"s","c","g","o","u","i"),$resim_adi_dizi);
	$resim_uzanti_hazirlik=uzanti_ne($resim_adi);
	$resim_uzanti=strtolower($resim_uzanti_hazirlik);	
	if ($resim_uzanti=="png" or $resim_uzanti=="jpg" or $resim_uzanti=="gif" or $resim_uzanti=="jpeg") {
		$kontrol=1;
		if (!$klasor) {
			$klasor="images";
		}
	} else {
		if (!$klasor) {
			$klasor="files";
		}		
	}
	if ($resim_adi and $kontrol==1) { 
	if (!file_exists("../uploads/$klasor")) {
		mkdir("../uploads/$klasor");
	}
	$kaynak=$resim_dizisi["tmp_name"];
	$hedef="../uploads/$klasor/$resim_adi";
	
	$parcala=explode(".$resim_uzanti_hazirlik",$resim_adi);
	$benzersiz=benzersiz();
	$resim_ilk_kisim=str_replace(array("."),array(null),$parcala[0]);
	$resim_adi=$resim_ilk_kisim."-$benzersiz.$resim_uzanti";

	$hedef="../uploads/$klasor/$resim_adi";
	$site_url=site_url();
	$resim_url=$site_url."/uploads/$klasor/".$resim_adi;
	$yukle=move_uploaded_file($kaynak,$hedef);
	chmod($hedef, 0666);
	if ($optimize) {
		include_once("../$admin_dizini/js/ImageResize.php");
		$image = new ImageResize($hedef);
		$hazirlik=explode(".",$resim_adi);
		$resim_adi_sabit=$hazirlik[0];
		$imagesize=getimagesize($hedef);
		$width=$imagesize[0];
		$height=$imagesize[1];
		$image-> save($hedef,null,$resize_kalite);
	}
	// Yüklenen öğe resim ise boyutlandırma başladı
	if ($kontrol==1) {
		if ($boyutlandir) {
			if (count($boyutlandir)>0) {
			include_once("../$admin_dizini/js/ImageResize.php");
			$image = new ImageResize($hedef);
			$hazirlik=explode(".",$resim_adi);
			$resim_adi_sabit=$hazirlik[0];
			$imagesize=getimagesize($hedef);
			$width=$imagesize[0];
			$height=$imagesize[1];
			foreach ($boyutlandir as $yaz) {
				$parcala=explode("_",$yaz);
				$resim_width=$parcala[0];
				$resim_height=$parcala[1];
				$resim_son=$resim_width."_".$resim_height;
				if ($width>=$resim_width and $height>=$resim_height) {
					$image-> crop($resim_width,$resim_height) -> save("../uploads/$klasor/$resim_adi_sabit-$resim_son.$resim_uzanti",null,$resize_kalite);
				} else {
					$image-> resize($resim_width,$resim_height,$allow_enlarge=true) -> save("../uploads/$klasor/$resim_adi_sabit-$resim_son.$resim_uzanti",null,$resize_kalite);
				}
			}
			}
		}
	}
	// Yeniden boyutlandırma bitti
	if (!$cikti) {
		return $resim_url;
	} else {
		$return_url=thumbnail_url($cikti,$resim_url);
		if ($orijinal_sil==1) {
			unlink($hedef);
		}
		return $return_url;
	}
	} else {
		return false;
	}
}
function panel_sil($url) {
	$ayir=explode("/uploads/",$url);
	$resim_adi=$ayir[1];
	$sil=unlink("../uploads/$resim_adi");
	if ($sil) {
		return true;
	} else {
		return false;
	}
}

/* Cache Başladı */
$limonpanel_menu[]=array("baslik"=>_LANG('pn_cache_onbellekleme'),"url"=>"temalar.php?tema=$aktif_tema&sayfa=panel&islem=cache","ikon"=>"rocket","sira"=>150);

function cache_update() {
	global $baglan;
	@$icerik_id=$_GET["id"];
	if ($icerik_id and $_POST) {	
		$cache_dosyasi= "cache/ilan/$icerik_id-cache.html";
		if (file_exists($cache_dosyasi)) {
			unlink("../$cache_dosyasi");
		}
	}
}
function cache_kontrol_buton() {
	global $admin_dizini;
	if (strstr(browser_url(), "/$admin_dizini/")) {
		@$icerik_id=$_GET["id"];
		@$icerik_tip=$_GET["tip"];
		if ($icerik_id and $icerik_tip=="ilan") {
			$cache_dosyasi= "cache/ilan/$icerik_id-cache.html";
			$ch_son_link=site_url()."/".$cache_dosyasi;
			if (file_exists("../".$cache_dosyasi)) {
				return '
				<div class="panel panel-default">
				<div class="panel-heading">' . _LANG('hb_cache_onbellekleme') . '</div>
					<div class="panel-body">
						<p>' . _LANG('hb_sayfa_onbellege_alindi_icerigi_guncellediginiz') . '</p>
						<a href="'.$ch_son_link.'" target="_blank" class="btn btn-info"><i class="fa fa-external-link"></i>Önbelleğe alınan halini göster</a>
					</div>
				</div>';			
			} else {
				return '
				<div class="panel panel-default">
				<div class="panel-heading">' . _LANG('hb_cache_onbellekleme') . '</div>
					<div class="panel-body">
						<p>' . _LANG('hb_sayfa_henuz_onbellege_alinmadi') . '</p>
					</div>
				</div>';
			}
		}
	}
}
function temizle($dizin) {
	foreach(glob("{$dizin}/*") as $dosya) {
		if(is_dir($dosya)) { 
			temizle($dosya);
		} else {
			unlink($dosya);
		}
	}
	rmdir($dizin);
}
$panel_icerik_sag_kod[]=cache_kontrol_buton();
$panel_icerik_sag_alt_kod[]=cache_update();

if (tema_ayar("cache")==1) {
	$cache_durum_yaz="<div class='mb20'><i class='fa fa-circle' style='color:#2ecc71;'></i> Önbellekleme açık</div>";
} else {
	$cache_durum_yaz="<div class='mb20'><i class='fa fa-circle' style='color:#e74c3c;'></i> Önbellekleme kapalı</div>";
}
$panel_anasayfa_sag_alt_kod[]='
<div class="panel panel-default panel-limon" style="margin-top:20px;">
<div class="panel-heading"><i class="fa fa-rocket"></i>Cache (Önbellekleme) Sistemi</div>
<div class="panel-body">'.$cache_durum_yaz.'
<a href="temalar.php?tema='.$aktif_tema.'&sayfa=panel&islem=cache" class="btn btn-primary">Cache Tercihleri</a>
</div>
</div>
';
// 01.03.2018 güncellemesi
function no_flash_select($text) {
	echo "<select name='no_flash' id='no_flash' class='form-control'><option value='$text'>$text</option></select>";
}
function vitrin_gorseli_otomatik($icerik_id) {
	global $baglan;
	$gorsel=bilgi("gorsel",$icerik_id);
	if (!$gorsel or url_kontrol($gorsel)==false) {
		$galeri_sayisi=mysqli_fetch_array(mysqli_query($baglan,"select COUNT(id) from galeri where icerik_id='$icerik_id'"))[0];	
		if ($galeri_sayisi>0) {
			$ilk_galeri_gorseli_sql=mysqli_query($baglan,"select resim from galeri where icerik_id='$icerik_id' limit 1");	
			$ilk_galeri_gorseli_resim_url=mysqli_fetch_array($ilk_galeri_gorseli_sql)[0];
			bilgi_guncelle("gorsel",$ilk_galeri_gorseli_resim_url,$icerik_id);
			galeri_sira_update($ilk_galeri_gorseli_resim_url,$icerik_id);
		}
	}
}
function telefon_bicimlendir($tel) {
	return str_replace(array("(",")"),null,$tel);
}
function limit_politikasi() {
    $limit_politikasi=tema_ayar("limit_politikasi");
    if (!$limit_politikasi) {
        $limit_politikasi=1;
    }
    return $limit_politikasi;
}
if ($limit_politikasi==1) {
    $oturum_ad_soyad=getir("uyeler","ad_soyad",$oturum_id);
    $panel_anasayfa_sag_alt_kod[]='
    
<div class="panel panel-default">
    <div class="panel-heading">Limit Sistemi Yenilendi!</div>
    <div class="panel-body">
        <p>Merhaba '.$oturum_ad_soyad.', ilan yazılımının limit sistemi yenilendi! Eskiden üyeler ve mağazalar, yıl boyunca paketinin sağladığı ilan limiti kadar ilan yayınlayabiliyorlardı. Bu limit sistemi ilan paketi satışına çok uygun değildi, çünkü üyelerin yeni bir ilan yayınlamak için mevcut bir ilanını silmeleri yetiyordu.</p><p>Yazılıma bu sorunu kökten çözecek alternatif bir limit sistemi entegre ettik! Dilerseniz eski usül kullanmaya devam edebilir, dilerseniz her ilan yayınla butonuna basıldığında 1 azalan limit sistemine geçiş yapabilirsiniz. </p>
        <p>Azalan Limit Sistemine geçmek veya detaylı bilgi almak için <a href="temalar.php?tema='.$aktif_tema.'&sayfa=panel&islem=azalan-limitlere-gecis">buraya</a> tıklayın.</p>
    </div>
</div>
    ';
}

function tarih_fark($tarih_1,$tarih_2,$array=null) {
	@$ekle=$array["ekle"];
	@$birim=$array["birim"];
	$t_1_mktime=mktime_getir($tarih_1);
	$t_2_mktime=mktime_getir($tarih_2);
	if ($t_1_mktime>$t_2_mktime) {
		$fark=$t_1_mktime-$t_2_mktime;
		$eklenecek="kaldı";
	} else if ($t_2_mktime>$t_1_mktime) {
		$fark=$t_2_mktime-$t_1_mktime;
		$eklenecek="geçti";
	}
	if (!$birim) {
		$simdi_lang="Şimdi";
		$saniye_lang="saniye";
		$dakika_lang="dakika";
		$saat_lang="saat";
		$gun_lang="gün";
		$hafta_lang="hafta";
		$ay_lang="ay";
		$yil_lang="yıl";
		if (function_exists("lang")) {
			@$simdi_lang=lang($simdi_lang);
			@$saniye_lang=lang($saniye_lang);
			@$dakika_lang=lang($dakika_lang);
			@$saat_lang=lang($saat_lang);
			@$gun_lang=lang($gun_lang);
			@$hafta_lang=lang($hafta_lang);
			@$ay_lang=lang($ay_lang);
			@$yil_lang=lang($yil_lang);
		}
		if ($fark<3) {
			$yaz="$simdi_lang";
		} else if ($fark<60) {
			$yaz="$fark $saniye_lang";
		} else if ($fark<60*60) {
			$fark=floor($fark/60); $yaz="$fark $dakika_lang";
		} else if ($fark<60*60*24) {
			$fark=floor($fark/60/60); $yaz="$fark $saat_lang";
		} else if ($fark<60*60*24*7) {
			$fark=floor($fark/60/60/24); $yaz="$fark $gun_lang";
		} else if ($fark<60*60*24*30) {
			$fark=floor($fark/60/60/24/7); $yaz="$fark $hafta_lang";
		} else if ($fark<60*60*24*30*12) {
			$fark=floor($fark/60/60/24/30); $yaz="$fark $ay_lang";
		} else {
			$fark=floor($fark/60/60/24/30/12); $yaz="$fark $yil_lang";
		}	
	}	else {
		if ($birim=="gun") {
			$yaz=floor($fark/60/60/24);
		}
	}
	if ($ekle==1) {
		return "$yaz $eklenecek";
	} else {
		return "$yaz";
	}
}

if (tema_ayar("update_index")!="198") {
	mysqli_query($baglan,"ALTER TABLE `icerik` ADD INDEX(`tip`)");
	mysqli_query($baglan,"ALTER TABLE `icerik` ADD INDEX(`durum`)");
	mysqli_query($baglan,"ALTER TABLE `icerik` ADD INDEX(`tarih_saat`)");
	mysqli_query($baglan,"ALTER TABLE `icerik` ADD INDEX(`g_tarih_saat`)");
	tema_ayar_guncelle("update_index","198");
}
if (tema_ayar("update_subdomain")!="1983") {
	mysqli_query($baglan,"ALTER TABLE `magazalar` ADD `subdomain` VARCHAR(50) NULL DEFAULT NULL AFTER `baslik`, ADD UNIQUE (`subdomain`);");
	tema_ayar_guncelle("update_subdomain","1983");
}
function youtube_video_id($video_url) {
	if (strstr($video_url,"youtu.be")) {
		$hazirlik=explode(".be/",$video_url); 
		$video_id=$hazirlik[1];
	} else {
		$id_hazirlik=explode("watch?v=",$video_url); 
		$video_id=$id_hazirlik[1];
		if (strstr($video_id,"&")) {
			$hazirlik=explode("&",$video_id); 
			$video_id=$hazirlik[0];
		}
	}
	return $video_id;
}
if (file_exists(ROOT_DIR."/kurulum.php")) {
	unlink(ROOT_DIR."/kurulum.php");
}
?>