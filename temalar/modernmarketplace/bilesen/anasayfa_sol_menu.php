<?php 
if(count(get_included_files())==1) {die();}
$link_op=array();

$link_op[]=array("baslik"=>_LANG('mobile_menu_urgent'),"deger"=>"acil-acil");
$link_op[]=array("baslik"=>_LANG('listing_price_drop'),"deger"=>"fiyati-dusenler");
$link_op[]=array("baslik"=>_LANG('home_featured_ads'),"deger"=>"vitrin-ilanlari");
$link_op[]=array("baslik"=>_LANG('pn_get_ilanlari'),"deger"=>"get-ilanlari");
$link_op[]=array("baslik"=>_LANG('stores'),"deger"=>"magazalar");
$link_op[]=array("baslik"=>_LANG('menu_last_48_week_month'),"deger"=>"son-ilanlar");

$ayarlar=array(
array("tip"=>"tab_ac","id"=>"genel_ayarlar","baslik"=>"Genel Ayarlar"),
array("tip"=>"text","id"=>"gizle","baslik"=>_LANG('pn_su_kadar_alt_linkten_sonrasini_gizle'),"standart"=>10,"aciklama"=>"Anasayfayı çok uzatmamak için belli sayıda alt linkten sonrasını gizleyerek devamını göster butonu eklemeye yarar. Varsayılan: 10"),
array("tip"=>"select","id"=>"menu","baslik"=>_LANG('pn_listeleme_linkleri'),"aciklama"=>_LANG('ad_listelenecek_kategorileri_seciniz'),"secenekler"=>options("Ana kategoriler ve bir kademe alt kategoriler:1|Sadece benim belirlediğim ana kategori ve alt kategoriler:2","|",":"),"standart"=>"1"),
array("tip"=>"multiple_checkbox","id"=>"gosterme","baslik"=>_LANG('pn_su_ana_kategorileri_gizle'),"secenekler"=>$kat_op,"aciklama"=>"Seçtiğiniz kategorileri menüden çıkartabilirsiniz."),
array("tip"=>"multiple_checkbox","id"=>"link_gosterme","baslik"=>_LANG('pn_su_linkleri_gizle'),"secenekler"=>$link_op,"aciklama"=>"Seçtiğiniz kategorileri menüden çıkartabilirsiniz."),
array("tip"=>"select","id"=>"ust_kategori","baslik"=>_LANG('pn_ust_kategori'),"secenekler"=>$kat_op,"aciklama"=>"<strong>Sadece benim belirlediğim ana kategori ve alt kategoriler</strong> işaretlendiğinde geçerli olur."),
array("tip"=>"select","id"=>"tasarim","baslik"=>_LANG('pn_tasarim_secenegi'),"secenekler"=>options("Standart:1|Alternatif:2","|",":"),"standart"=>"1"),
array("tip"=>"select","id"=>"ikon_yuvarlak","baslik"=>_LANG('pn_ikon_arkaplani'),"secenekler"=>options("Yuvarlak:1|Kare:2","|",":"),"standart"=>"1","kosul"=>"tasarim:==(1)"),
array("tip"=>"color","id"=>"link_hover","baslik"=>_LANG('pn_link_hover_rengi'),"standart"=>"#000000","kosul"=>"tasarim:==(2)"),
array("tip"=>"select","id"=>"ilan_sayisi","baslik"=>_LANG('pn_ilan_sayilari_gosterilsin_mi'),"secenekler"=>options("Evet:1|Hayır:2","|",":"),"standart"=>"1"),
array("tip"=>"select","id"=>"bos_kategoriler","baslik"=>_LANG('pn_bos_alt_kategoriler_gosterilsin_mi'),"secenekler"=>options("Evet:1|Hayır:2","|",":"),"standart"=>"1"),
array("tip"=>"tab_kapat"),
);

$bilesen["anasayfa_sol_menu"]=array("id"=>"anasayfa_sol_menu","baslik"=>_LANG('pn_anasayfa_sol_menu'),"ayarlar"=>$ayarlar,"fonksiyon"=>"anasayfa_sol_menu","aciklama"=>_LANG('e_anasayfa_sol_menu_aciklama'));

function anasayfa_sol_menu($id) {
global $baglan;
global $footer_kod;
$baslik=getir("bilesenler","baslik",$id);
$menu=bilesen_ayar("menu",$id);
$ust_kategori=bilesen_ayar("ust_kategori",$id);
$gizle=bilesen_ayar("gizle",$id);
$tasarim=bilesen_ayar("tasarim",$id);
$link_hover=bilesen_ayar("link_hover",$id);
$ilan_sayisi=bilesen_ayar("ilan_sayisi",$id);
$bos_kategoriler=bilesen_ayar("bos_kategoriler",$id);
$gosterme=rtrim((string)bilesen_ayar("gosterme",$id),",");
$link_gosterme=rtrim((string)bilesen_ayar("link_gosterme",$id),",");
$ikon_yuvarlak=rtrim((string)bilesen_ayar("ikon_yuvarlak",$id),",");
$ilan_sayilari_ayar=tema_ayar("ilan_sayilari");
if ($menu==1 or !$menu) {
	$ust_kategori_yaz="ust_kategori='0'";
	if ($gosterme) {
		$ust_kategori_yaz.=" and id not in ($gosterme)";
	}
} else {
	$ust_kategori_yaz="ust_kategori='$ust_kategori'";
}
$ikon_bg=tema_ayar("ikon_bg");
?>
<?php if ($ikon_yuvarlak==2) {echo "
<style>
ul.ana_menu li a i {border-radius:0 !important;}
</style>
";} ?>
<?php if ($tasarim==1) { ?>
<div class="bilesen visible-md visible-lg">
	<ul class="ana_menu">
	
		<?php if (!strstr($link_gosterme,"acil-acil")) { ?><li><a href="<?php echo ozel_url("acil-acil"); ?>"><i class="ion-android-alarm-clock" style="background:<?php echo $ikon_bg; ?>;"></i><?php echo _LANG('mobile_menu_urgent'); ?></a></li><?php } ?>
		<?php if (!strstr($link_gosterme,"fiyati-dusenler")) { ?><li><a href="<?php echo ozel_url("fiyati-dusenler"); ?>"><i class="ion-arrow-graph-down-right" style="background:<?php echo $ikon_bg; ?>;"></i><?php echo _LANG("listing_price_drop"); ?></a></li><?php } ?>
		<?php if (!strstr($link_gosterme,"vitrin-ilanlari")) { ?><li><a href="<?php echo ozel_url("vitrin-ilanlari"); ?>"><i class="ion-podium" style="background:<?php echo $ikon_bg; ?>;"></i><?php echo _LANG("sidebar_featured_all"); ?></a></li><?php } ?>
		<?php if (!strstr($link_gosterme,"get-ilanlari")) { ?><li><a href="<?php echo ozel_url("get-ilanlari"); ?>"><i class="ion-pricetag" style="background:<?php echo $ikon_bg; ?>;"></i><?php echo _LANG("sidebar_get_ads"); ?></a></li><?php } ?>
		<?php if (!strstr($link_gosterme,"magazalar")) { ?><li><a href="<?php echo ozel_url("magazalar"); ?>"><i class="ion-bag" style="background:<?php echo $ikon_bg; ?>;"></i><?php echo _LANG("search_all_stores"); ?></a></li><?php } ?>
		<?php if (!strstr($link_gosterme,"son-ilanlar")) { ?><li><a href="<?php echo ozel_url("son-48-saat"); ?>"><i class="ion-android-time" style="background:<?php echo $ikon_bg; ?>;"></i><?php echo _LANG('menu_last_48_hours'); ?></a><span style="color:#aaa;"> / </span><a href="<?php echo ozel_url("son-1-hafta"); ?>"><?php echo _LANG('menu_1_week'); ?></a><span style="color:#aaa;"> / </span><a href="<?php echo ozel_url("son-1-ay"); ?>"><?php echo _LANG('menu_1_month'); ?></a></li><?php } ?>
		<li class="cizgi"></li>
		<?php 
		
		$kategori_siralama=tema_ayar("kategori_siralama");
		if ($kategori_siralama=="baslik_a_z") {
			$kategori_sql=mysqli_query($baglan,"select * from kategoriler where tip='ilan' and $ust_kategori_yaz order by baslik asc");
		}	else if ($kategori_siralama=="icerik_sayisi") {
			$kategori_sql=mysqli_query($baglan,"select * from kategoriler where tip='ilan' and $ust_kategori_yaz order by icerik desc, baslik");
		}	else {
			$kategori_sql=mysqli_query($baglan,"select * from kategoriler where tip='ilan' and $ust_kategori_yaz");
		}		

		while ($kategori_dizi=mysqli_fetch_array($kategori_sql)) { 
			$kategori_id=$kategori_dizi["id"];
			$kategori_baslik=_DIL_BASLIK($kategori_dizi["baslik"], $kategori_id, "kategori");
			$kategori_ikon=kategori_bilgi("ikon",$kategori_id);
			$kategori_ikon_bg=kategori_bilgi("ikon_bg",$kategori_id);
			$kategori_link=kategori_link_getir($kategori_id);
			$kategori_ilan_sayisi=$kategori_dizi["icerik"];
			if ($ilan_sayilari_ayar==2 or $ilan_sayilari_ayar==3) {
				$kategori_ilan_sayisi*=$ilan_sayilari_ayar;
			}
			@$alt_kategori_sayisi=alt_kategori_sayisi($kategori_id);		
			if (!$kategori_ikon) {$kategori_ikon="ion-search";}
		?>
		<li class="dropdown"><a href="<?php echo $kategori_link; ?>"><i class="<?php echo $kategori_ikon; ?>" style="background:<?php echo $kategori_ikon_bg; ?> !important;"></i><?php echo $kategori_baslik; ?></a><?php if ($ilan_sayisi==1 or !$ilan_sayisi) { ?><span class="toplam">(<?php echo $kategori_ilan_sayisi; ?>)</span><?php } ?>
			<?php if ($alt_kategori_sayisi>0) { ?>
			<ul>
				<?php 

				if ($kategori_siralama=="baslik_a_z") {
					$alt_kategori_sql=mysqli_query($baglan,"select * from kategoriler where ust_kategori='$kategori_id' order by baslik asc");
				}	else if ($kategori_siralama=="icerik_sayisi") {
					$alt_kategori_sql=mysqli_query($baglan,"select * from kategoriler where ust_kategori='$kategori_id' order by icerik desc, baslik");
				}	else {
					$alt_kategori_sql=mysqli_query($baglan,"select * from kategoriler where ust_kategori='$kategori_id'");
				}	
			
			
				while ($alt_kategori_dizi=mysqli_fetch_array($alt_kategori_sql)) { 
				$alt_kategori_id=$alt_kategori_dizi["id"];
				$alt_kategori_baslik=_DIL_BASLIK($alt_kategori_dizi["baslik"], $alt_kategori_id, "kategori");
				$alt_kategori_link=kategori_link_getir($alt_kategori_id);
				$alt_kategori_ilan_sayisi=$alt_kategori_dizi["icerik"];
				if ($ilan_sayilari_ayar==2 or $ilan_sayilari_ayar==3) {
					$alt_kategori_ilan_sayisi*=$ilan_sayilari_ayar;
				}
				if ($alt_kategori_ilan_sayisi>0 or $bos_kategoriler==1) {
				?>					
				<li><a href="<?php echo $alt_kategori_link; ?>"><?php echo $alt_kategori_baslik; ?></a><?php if ($ilan_sayisi==1 or !$ilan_sayisi) { ?><span class="toplam">(<?php echo $alt_kategori_ilan_sayisi; ?>)</span><?php } ?></li>
				<?php } } ?>
			</ul>
			<?php } ?>
		</li>
		<?php } ?>
	</ul>
</div>
<?php 
$sol_menu_js='
<script type="text/javascript">
$("ul li.dropdown ul").each(function(){
  var max = '.$gizle.'
  if ($(this).find("li").length > max) {
    $(this)
      .find("li:gt("+max+")")
      .hide()
      .end()
      .append(
        $("<li class=\"c_pointer kategori_more\">'._LANG('tumunu_goster').'</li>"));
	}
	$("body").on("click","li.kategori_more",function(){
	  $(this).siblings(":hidden").show().end().remove();
	});
});	
</script>';
$footer_kod[]=$sol_menu_js;

} else if ($tasarim==2) { ?>
<style>
ul.ana_menu_a li a:hover {color:<?php echo $link_hover; ?> !important;}
</style>
<div class="bilesen visible-md visible-lg">
	<ul class="ana_menu_a mb15">
		<?php 
		$kategori_sql=mysqli_query($baglan,"select * from kategoriler where tip='ilan' and $ust_kategori_yaz");
		while ($kategori_dizi=mysqli_fetch_array($kategori_sql)) { 
			$kategori_id=$kategori_dizi["id"];
			$kategori_baslik=_DIL_BASLIK($kategori_dizi["baslik"], $kategori_id, "kategori");
			$kategori_ikon=kategori_bilgi("ikon",$kategori_id);
			$kategori_ikon_bg=kategori_bilgi("ikon_bg",$kategori_id);
			$kategori_link=kategori_link_getir($kategori_id);
			$kategori_ilan_sayisi=$kategori_dizi["icerik"];
			$alt_kategori_sayisi=alt_kategori_sayisi($kategori_id);
			if (!$kategori_ikon) {$kategori_ikon="ion-search";}
		?>
		<li class="full"><a href="<?php echo $kategori_link; ?>"><?php echo $kategori_baslik; ?><span class="toplam">(<?php echo $kategori_ilan_sayisi; ?>)</span></a></li>
		<?php } ?>
	</ul>
	<ul class="ana_menu_a">
		<?php if (!strstr($link_gosterme,"acil-acil")) { ?><li class="full"><a href="<?php echo ozel_url("acil-acil"); ?>" class="full"><?php echo _LANG('mobile_menu_urgent'); ?></a></li><?php } ?>
		<?php if (!strstr($link_gosterme,"fiyati-dusenler")) { ?><li class="full"><a href="<?php echo ozel_url("fiyati-dusenler"); ?>" class="full"><?php echo _LANG("listing_price_drop"); ?></a></li><?php } ?>
		<?php if (!strstr($link_gosterme,"vitrin-ilanlari")) { ?><li class="full"><a href="<?php echo ozel_url("vitrin-ilanlari"); ?>" class="full"><?php echo _LANG("sidebar_featured_all"); ?></a></li><?php } ?>
		<?php if (!strstr($link_gosterme,"get-ilanlari")) { ?><li class="full"><a href="<?php echo ozel_url("get-ilanlari"); ?>" class="full"><?php echo _LANG("sidebar_get_ads"); ?></a></li><?php } ?>
		<?php if (!strstr($link_gosterme,"magazalar")) { ?><li class="full"><a href="<?php echo ozel_url("magazalar"); ?>" class="full"><?php echo _LANG("search_all_stores"); ?></a></li><?php } ?>
		<?php if (!strstr($link_gosterme,"son-ilanlar")) { ?>
		<li class="w3">
		<a href="<?php echo ozel_url("son-48-saat"); ?>" class="inline"><?php echo _LANG('menu_last_48_hours'); ?></a>
		<a href="<?php echo ozel_url("son-1-hafta"); ?>" class="inline"><?php echo _LANG('menu_1_week'); ?></a>
		<a href="<?php echo ozel_url("son-1-ay"); ?>" class="inline"><?php echo _LANG('menu_1_month'); ?></a>
		</li>
		<?php } ?>
	</ul>
</div>
<?php 
$sol_menu_js='
<script type="text/javascript">
$("ul li.list-group-item ul").each(function(){
  var max = '.$gizle.'
  if ($(this).find("li").length > max) {
    $(this)
      .find("li:gt("+max+")")
      .hide()
      .end()
      .append(
        $("<li class=\"c_pointer kategori_more\">'._LANG('tumunu_goster').'</li>"));
	}
	$("body").on("click","li.kategori_more",function(){
	  $(this).siblings(":hidden").show().end().remove();
	});
});	
</script>';
$footer_kod[]=$sol_menu_js;
} } ?>