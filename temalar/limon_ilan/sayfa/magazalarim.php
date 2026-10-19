<?php 
if(count(get_included_files())==1) {die();}
if (!$oturum_id) {
	$_SESSION["mesaj"]=array(_LANG('pn_bu_sayfayi_goruntuleyebilmek_icin_uye_girisi_yapmalisiniz'),".");
	yonlendir(ozel_url("giris-yap"));
	exit();
}
@$islem=$_GET["islem"];
$limit_politikasi=limit_politikasi();
$uye_temsilcilikleri=uye_temsilcilikleri();
if (!$uye_temsilcilikleri) {
	$uye_temsilcilikleri=array();
}
$temsilcilik_sayisi=count($uye_temsilcilikleri);
$magaza_sayisi=magaza_sayisi();
$magaza_sayisi+=$temsilcilik_sayisi;
if ($islem=="yeni") {
	@$buton=mysqli_real_escape_string($baglan,$_POST["buton"]);
	if ($buton) {
		$baslik=mysqli_real_escape_string($baglan,strip_tags($_POST["baslik"]));
		$paket=mysqli_real_escape_string($baglan,$_POST["paket"]);
		$kategori=mysqli_real_escape_string($baglan,$_POST["kategori"]);
		$magaza=magaza_ekle($baslik,$oturum_id,$kategori);
		
		if (!$subdomain) {
			$subdomain_hazirlik=str_replace(array("-"),null,url_olustur($baslik));
			$subdomain=benzersiz_url_olustur(array("tablo"=>"magazalar","sutun"=>"subdomain","baslik"=>$subdomain_hazirlik,"id"=>$magaza,"url"=>"$subdomain"));
			guncelle("magazalar","subdomain",$subdomain,$magaza);
		}
		
		magaza_bilgi_guncelle("paket",$paket,$magaza);
		$_SESSION["mesaj"]=array(_LANG('pn_magaza_basariyla_olusturuldu'),".");
		$tarih_saat=tarih_saat();
		$paket_tutar=liste_bilgi("ucret",$paket);
		$paket_kategori_ozel_fiyat=kategori_bilgi("magaza_paket_fiyat-$paket",$kategori);
		if ($paket_kategori_ozel_fiyat) {
			$paket_tutar=$paket_kategori_ozel_fiyat;
		}
		$siparis_olustur=mysqli_query($baglan,"insert into siparisler set magaza_id='$magaza', alici_id='$oturum_id', tarih_saat='$tarih_saat', tip='magaza', paket_id='$paket', tutar='$paket_tutar', durum='0'");
		$siparis_id=mysqli_insert_id($baglan);
		
		// E-posta gönderimi başladı
		$mail_baslik=tema_ayar("mail_baslik");
		$site_url=site_url();
		$ad_soyad_getir=getir("uyeler","ad_soyad",$oturum_id);
		$auto_mail_magaza_baslik=getir("magazalar","baslik",$magaza);
		$ileti_admin="
		<p><strong>$ad_soyad_getir</strong> adlı üye yeni bir mağaza siparişi oluşturdu</p>
		<p>Mağaza: <strong>$auto_mail_magaza_baslik</strong></p>
		<p><a href=\"$site_url\">Siteyi Görüntüle</a> - <a href=\"$site_url/$admin_dizini\">Yönetim Paneli</a></p>
		";
		$site_email=ayar("site_email");
		auto_mail_add($mail_baslik,$site_email,"Yeni mağaza siparişi: $paket_tutar TL",$ileti_admin);						
		// E-posta gönderimi bitti
		
		yonlendir(ozel_url("siparis")."?siparis_id=$siparis_id");
	}
}
tema_header(); ?>
<div class="container profil arsiv mt20">
	<?php include("temalar/$aktif_tema/inc/uye-ust.php"); ?>
	<div class="row">
		<div class="col-md-9 sidebar">
			<?php include("temalar/$aktif_tema/inc/uye-sol-menu.php"); ?>
			
			<a href="<?php echo ozel_url("magazalarim"); ?>?islem=yeni" class="btn btn-primary btn-block mb15"><i class="ion-plus"></i><?php echo _LANG("nav_create_store"); ?></a>
		</div>
		<div class="col-md-33">
			<?php include("temalar/$aktif_tema/inc/alert.php"); ?>
			<?php if (!$islem) { ?>
			<?php if ($magaza_sayisi==0) { ?>
			<div class="alert alert-danger">Mağazanız bulunmamaktadır. Hemen bir mağaza oluşturmak için <a href="<?php echo ozel_url("magazalarim"); ?>?islem=yeni" class="alert-link">buraya</a> tıklayın.</div>
			<?php } else { ?>
		
			<div class="panel panel-default limon with_footer">
				<div><?php echo _LANG('hb_magazalarim'); ?></div>
				<div class="panel-body" style="padding:0 !important;">
				<div class="table-responsive">
				<table class="table table-default table-hover profil padding" style="margin-bottom:0;">
					<thead>
						<tr>
							<th><?php echo _LANG("pn_magaza"); ?></th>
							<th>Kategori</th>
							<th><?php echo _LANG("s_olusma_tarihi"); ?></th>
							<th>Paket</th>
							<th>Durum</th>
							<th><?php echo _LANG("general_options"); ?></th>
						</tr>
					</thead>
					<tbody>
					<?php 
					$sql=mysqli_query($baglan,"select * from magazalar where uye_id='$oturum_id'");
					while ($magaza_dizi=mysqli_fetch_array($sql)) {
					$id=$magaza_dizi["id"];
					$baslik=$magaza_dizi["baslik"];
					$kategori=$magaza_dizi["kategori"];
					$kategori_adi=getir("kategoriler","baslik",$magaza_dizi["kategori"]);
					$tarih_saat=tarih_saat_bicimlendir($magaza_dizi["tarih_saat"]);
					$magaza_paket=magaza_bilgi("paket",$id);
					$magaza_paket_adi=liste_bilgi("paket_adi",magaza_bilgi("paket",$id));
					$durum=$magaza_dizi["durum"];
					?>
					<tr>
						<td><a href="<?php echo magaza_link_getir($id); ?>"><?php echo $baslik; ?></a></td>
						<td><?php echo $kategori_adi; ?></td>
						<td><?php echo $tarih_saat; ?></td>
						<td><a href="<?php echo ozel_url("magaza-plan-detaylari"); ?>?id=<?php echo $id; ?>"><?php echo $magaza_paket_adi; ?></a></td>
						<td><?php if ($durum==0) {echo "<span class='label label-danger borderless'>Onay Bekliyor</span>";} else if ($durum==1) {echo "<span class='label label-success borderless'>Onaylandı</span>";} ?></td>
						<td>
						<?php if ($durum==0) {
						$siparis_id_getir=id_getir("siparisler","magaza_id",$id);
						?>
						<a href="<?php echo ozel_url("siparis"); ?>?s=<?php echo $siparis_id_getir; ?>" class="btn btn-danger btn-xs"><?php echo _LANG("order_pay"); ?></a>
						<?php } else if ($durum==1) { ?>
						<div class="btn-group" role="group" aria-label="...">
						
						  <a href="<?php echo ozel_url("ilan-ekle")."?magaza=$id"; ?>" class="btn btn-primary btn-xs"><?php echo _LANG("menu_ilan_ekle"); ?></a>
						  
						  <div class="btn-group" role="group">
							<button type="button" class="btn btn-primary btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="caret ml0"></span></button>
							<ul class="dropdown-menu dropdown-menu-right">
							  <li><a href="<?php echo magaza_link_getir($id); ?>"><?php echo _LANG("store_view"); ?></a></li>
							  <?php if ($limit_politikasi==1) { ?>
							  <li><a href="<?php echo ozel_url("magaza-plan-detaylari"); ?>?id=<?php echo $id; ?>"><?php echo _LANG("title_store_plan"); ?></a></li>
							  <?php } else if ($limit_politikasi==2) { ?>
							  <li><a href="<?php echo ozel_url("magaza-paket-satin-al"); ?>?id=<?php echo $id; ?>"><?php echo _LANG("title_store_package"); ?></a></li>
							  <?php } ?>
							  
							  <li><a href="<?php echo ozel_url("magaza-bilgileri"); ?>?id=<?php echo $id; ?>"><?php echo _LANG("store_settings"); ?></a></li>
							  <li><a href="<?php echo ozel_url("danismanlar"); ?>?id=<?php echo $id; ?>&islem=ekle"><?php echo _LANG("s_danisman_ekle"); ?></a></li>
							</ul>
						  </div>
						</div>
						<?php } ?>
						</td>
					</tr>
					<?php } ?>
					</tbody>
				</table>
				</div>
				</div>
			</div>
			
			
			<?php if ($temsilcilik_sayisi>0) { ?>
			<div class="panel panel-default limon with_footer">
				<div><?php echo _LANG('hb_satis_danismanliklarim'); ?></div>
				<div class="panel-body" style="padding:0 !important;">
				<table class="table table-default table-hover profil padding" style="margin-bottom:0;">
					<thead>
						<tr>
							<th><?php echo _LANG("pn_magaza"); ?></th>
							<th>Kategori</th>
							<th><?php echo _LANG("s_olusma_tarihi"); ?></th>
							<th>Paket</th>
							<th>Durum</th>
							<th><?php echo _LANG("general_options"); ?></th>
						</tr>
					</thead>
					<tbody>
					<?php 
					foreach ($uye_temsilcilikleri as $x) {
					$id=$x;
					$baslik=getir("magazalar","baslik",$id);
					$kategori=getir("magazalar","kategori",$id);
					$kategori_adi=getir("kategoriler","baslik",$kategori);
					$tarih_saat=tarih_saat_bicimlendir(getir("magazalar","tarih_saat",$id));
					$magaza_paket=magaza_bilgi("paket",$id);
					$magaza_paket_adi=liste_bilgi("paket_adi",magaza_bilgi("paket",$id));
					$durum=getir("magazalar","durum",$id);
					?>
					<tr>
						<td><a href="<?php echo magaza_link_getir($id); ?>"><?php echo $baslik; ?></a></td>
						<td><?php echo $kategori_adi; ?></td>
						<td><?php echo $tarih_saat; ?></td>
						<td><?php echo $magaza_paket_adi; ?></td>
						<td><?php if ($durum==0) {echo "<span class='label label-danger borderless'>Onay Bekliyor</span>";} else if ($durum==1) {echo "<span class='label label-success borderless'>Onaylandı</span>";} else if ($durum==2) {echo "<span class='label label-success borderless'>Süresi doldu</span>";} ?></td>
						<td><?php if ($durum==1) { ?><a href="<?php echo ozel_url("ilan-ekle")."?magaza=$id"; ?>" class="btn btn-primary btn-xs"><?php echo _LANG("menu_ilan_ekle"); ?></a><?php } else if ($durum==2) { ?><a href="<?php echo ozel_url("magaza-plan-detaylari")."?magaza=$id"; ?>" class="btn btn-primary btn-xs"><?php echo _LANG("p_sure_uzat"); ?></a><?php } ?></td>
					</tr>
					<?php } ?>
					</tbody>
				</table>

				</div>
			</div>			
			
			<?php } ?>
			
			
			<?php } ?>

			<?php } else if ($islem=="yeni") { ?>
			<div class="panel panel-default limon">
				<div><?php echo _LANG('hb_magaza_olustur'); ?></div>
				<div class="panel-body">
					
					<div class="row">
						<div class="col-md-25">						
							
							<form action="" method="post" class="noselect">
							<?php form_group(array("tip"=>"text","id"=>"baslik","baslik"=>_LANG('s_magaza_adi'),"gerekli"=>1)); ?>
							<?php
							$magaza_kategorileri[]=array("deger"=>"","baslik"=>_LANG('filter_select'));
							$kategori_sql=mysqli_query($baglan,"SELECT k.id, k.baslik from kategoriler as k INNER JOIN ekstra_bilgi as eb ON k.id=eb.id and eb.anahtar='magaza' and eb.deger='1'");
							while ($kategori_dizi=mysqli_fetch_array($kategori_sql)) {
								$kategori_id=$kategori_dizi["id"];
								$kategori_baslik=$kategori_dizi["baslik"];
								
								
								$magaza_kategorileri[]=array("deger"=>$kategori_id,"baslik"=>$kategori_baslik);
								
							}
							form_group(array("tip"=>"select","id"=>"kategori","baslik"=>"Kategori","secenekler"=>$magaza_kategorileri,"gerekli"=>1)); 

							$paket_seciniz_sql=mysqli_query($baglan,"select l_id from listeler where tip='magaza_paketleri'");
							while ($paket_seciniz_dizi=mysqli_fetch_array($paket_seciniz_sql)) {
								$paket_id=$paket_seciniz_dizi["l_id"];
								$paket_baslik=liste_bilgi("paket_adi",$paket_id);
								$paket_seciniz[]=array("deger"=>"$paket_id","baslik"=>"$paket_baslik");
							}
							form_group(array("tip"=>"select","id"=>"paket","baslik"=>"Paket","secenekler"=>$paket_seciniz,"gerekli"=>1));
							
							$magaza_kurallari_sayfasi=link_getir(tema_ayar("magaza_kurallari"));
							form_group(array("tip"=>"checkbox","id"=>"onay","baslik"=>"<a href='$magaza_kurallari_sayfasi' target='_blank'>Mağaza oluşturma ve ilan verme kurallarını</a> okudum, onaylıyorum.","gerekli"=>1));
							?>
							<?php form_buton("buton",_LANG('tx_magaza_olustur'),"primary"); ?>
							</form>
						</div>
						<div class="col-md-17">
							<div id="ajax_load"></div>
						</div>
					</div>
				</div>
			</div>

			<div class="panel panel-default limon tablo">
			
					<div class="panel-body text-center" style="padding:0;">
					<div class="table-responsive">
					<table class="table table-default table-hover" style="margin-bottom:0;">
						<thead class="ortala">
							<tr>
								<th></th>
								<th><?php echo _LANG("s_ilan_limiti"); ?></th>
								<th><?php echo _LANG("s_fotograf_limiti"); ?></th>
								<th><?php echo _LANG("s_danisman_limiti_k"); ?></th>
								<th><?php echo _LANG("s_aninda_onay"); ?></th>
								<th>Teknik destek</th>
                                <th><?php echo _LANG("s_ilan_adet_ucreti"); ?></th>
                                <th><?php echo _LANG("s_ucret"); ?></th>
							</tr>
						</thead>
						<tbody>
							<?php 
							$paket_sql=mysqli_query($baglan,"select l_id from listeler where tip='magaza_paketleri'");
							while ($paket_dizi=mysqli_fetch_array($paket_sql)) {
								$paket_id=$paket_dizi["l_id"];
								$paket_baslik=liste_bilgi("paket_adi",$paket_id);
								$ilan_limit=liste_bilgi("ilan_limit",$paket_id);
								$foto_limit=liste_bilgi("foto_limit",$paket_id);
								$danisman_limit=liste_bilgi("danisman_limit",$paket_id);
								if (!$danisman_limit) {
									$danisman_limit=0;
								}
								$aninda_onay=liste_bilgi("aninda_onay",$paket_id);

                                $paket_ucret=liste_bilgi("ucret",$paket_id);
                                $ilan_basina_ucret=number_format($paket_ucret/$ilan_limit,2);
                                $ibu_explode=explode(".",$ilan_basina_ucret);
                                if ($ibu_explode[1]=="00") {
                                    $ilan_basina_ucret=substr($ilan_basina_ucret,0,-3);
                                } else {
                                    $ilan_basina_ucret=str_replace(".",",",$ilan_basina_ucret);
                                }
							?>
							<tr>
								<th><?php echo $paket_baslik; ?></th>
								<td><?php echo $ilan_limit; ?></td>
								<td><?php echo $foto_limit; ?></td>
								<td><?php echo $danisman_limit; ?></td>
								<td><?php if ($aninda_onay==1) {echo "<span class='label label-success borderless'>Evet</span>";} else {echo "<span class='label label-danger borderless'>Hayır</span>";} ?></td>
								<td>7/24</td>
								<td><?php if ($ilan_basina_ucret) {echo $ilan_basina_ucret." <i class='fa fa-try'></i>";} else {echo "<span class='label label-success borderless'>Ücretsiz</span>";} ?></td>
                                <td><?php if ($paket_ucret) {echo $paket_ucret." <i class='fa fa-try'></i>";} else {echo "<span class='label label-success borderless'>Ücretsiz</span>";} ?></td>
							</tr>
							<?php } ?>
						</tbody>
					</table>								
					</div>
				</div>
				
			</div>			
			<?php } ?>
	
		</div>
	</div>
</div>

<?php tema_footer(); ?>
<script type="text/javascript">
$(function(){
	$("body").on("change","select#kategori",function(){
		var deger=$(this).val();
		var paket=$("#paket").val();
		if (deger!="") {
			$("#ajax_load").load("<?php echo tema_dizini(); ?>/ajax.php?islem=magaza_bilgi&kategori="+deger+"&paket="+paket);
		}		
	});
	$("body").on("change","#paket",function(){
		var paket=$("#paket").val();
		var deger=$("#kategori").val();
		if (deger!="") {
			$("#ajax_load").load("<?php echo tema_dizini(); ?>/ajax.php?islem=magaza_bilgi&kategori="+deger+"&paket="+paket);
		}
	});
	
	$("button[name=buton]").attr("disabled", true);
	
	$("#form_onay input").change(function() {
		if(this.checked) {
			$("button[name=buton]").attr("disabled", false);
		} else {
			$("button[name=buton]").attr("disabled", true);
		}
	});
})
</script>