<?php 
$limit_politikasi=limit_politikasi();

mysqli_query($baglan,"ALTER TABLE `uyeler` ADD `azalan_update` INT(1) DEFAULT '0' AFTER `durum`;");
mysqli_query($baglan,"ALTER TABLE `magazalar` ADD `azalan_update` INT(1) DEFAULT '0' AFTER `durum`;");

$uye_limit_hesapla=$_POST["uye_limit_hesapla"];
$magaza_limit_hesapla=$_POST["magaza_limit_hesapla"];
$uye_sayisi=mysqli_fetch_array(mysqli_query($baglan,"select COUNT(id) from uyeler where azalan_update='0'"))[0];
$magaza_sayisi=mysqli_fetch_array(mysqli_query($baglan,"select COUNT(id) from magazalar where azalan_update='0'"))[0];

if ($uye_limit_hesapla) {

    $uye_sql=mysqli_query($baglan,"select id from uyeler where azalan_update='0' order by id asc");
    $x=0;
    while ($dizi=mysqli_fetch_array($uye_sql)) {
        $uye_id=$dizi["id"];
        $toplam_ilan_limiti=uye_sabit_ilan_limiti("toplam",$uye_id);
        $toplam_kategori_limiti=uye_sabit_ilan_limiti("kategori",$uye_id);
        $toplam_foto_limiti=uye_sabit_ilan_limiti("foto",$uye_id);
        $aninda_onay=uye_sabit_ilan_limiti("aninda_onay",$uye_id);
        uye_bilgi_guncelle("azalan_ilan_limit",$toplam_ilan_limiti,$uye_id);
        uye_bilgi_guncelle("azalan_kategori_limit",$toplam_kategori_limiti,$uye_id);
        uye_bilgi_guncelle("azalan_foto_limit",$toplam_foto_limiti,$uye_id);
        uye_bilgi_guncelle("azalan_aninda_onay",$aninda_onay,$uye_id);
        guncelle("uyeler","azalan_update",1,$uye_id);
    $x++;}
    $_SESSION["mesaj"]=array(_LANG("pn_tum_uyelere_ait_azalan_ilan_limitleri_hesaplandi"),"success");

    yenile();
}
if ($magaza_limit_hesapla) {

    $magaza_sql=mysqli_query($baglan,"select id from magazalar where azalan_update='0' order by id asc");
    $x=0;
    while ($dizi=mysqli_fetch_array($magaza_sql)) {
        $magaza_id=$dizi["id"];

        $toplam_ilan_limiti=magaza_sabit_ilan_limiti("toplam",$magaza_id);
        $toplam_foto_limiti=magaza_sabit_ilan_limiti("foto",$magaza_id);
        $toplam_danisman_limiti=magaza_sabit_ilan_limiti("danisman",$magaza_id);
        $aninda_onay=magaza_sabit_ilan_limiti("aninda_onay",$magaza_id);
        magaza_bilgi_guncelle("azalan_ilan_limit",$toplam_ilan_limiti,$magaza_id);
        magaza_bilgi_guncelle("azalan_foto_limit",$toplam_foto_limiti,$magaza_id);
        magaza_bilgi_guncelle("azalan_danisman_limit",$toplam_foto_limiti,$magaza_id);
        magaza_bilgi_guncelle("azalan_aninda_onay",$aninda_onay,$magaza_id);       

        guncelle("magazalar","azalan_update",1,$magaza_id);
    $x++;}
    $_SESSION["mesaj"]=array(_LANG("pn_tum_uyelere_ait_azalan_ilan_limitleri_hesaplandi"),"success");

    yenile();
}

?>
<h3 class="pull-left bolum-baslik text-uppercase">Azalan Limitlere Geçiş Aracı <small style="margin-left:30px;">Mevcut limit politikası: <?php if ($limit_politikasi==1) {echo "<span class='label label-primary'>Sabit Limitler</span>";} else {echo "<span class='label label-danger'>Azalan Limitler</span>";} ?></small></h3>
<div class="clearfix mb20"></div>

<h5><?php echo _LANG("pn_gecis_icin_gerekli_islemler"); ?></h5>
<ul class="list-group">
  <li><?php echo _LANG('hb_1_paket_kontrolleri_sayfasindan_paket_suresi_s'); ?></li>
  <li class="list-group-item">2. İlan Sistemi Ayarları > Yapısal Ayarlar > Limit Politikası ayarını "Azalan Limitler" olarak güncelleyin.</li>
  <li><?php echo _LANG('hb_3_bu_sayfadan_mevcut_uyelere_ve_mevcut_magazal'); ?></li>
  <li><?php echo _LANG('hb_4_baska_bir_seye_ihtiyac_yok_bugunden_sonra_ka'); ?></li>
</ul>

<h4><?php echo _LANG("pn_uye_limit_hesaplayin"); ?></h4>
<?php if ($uye_sayisi==0) { ?>
    <div><?php echo _LANG('hb_baska_bir_islem_yapmaya_gerek_yok_tum_uyelerin'); ?></div>
<?php } else { ?>
    <form action="" method="post">
        <?php if ($uye_sayisi>0) { ?>
            <button type="submit" name="uye_limit_hesapla" value="Hesapla" class="btn btn-danger"><i class="fa fa-check"></i>Tüm Üyelerin Azalan İlan Limitlerini Hesapla (Toplam: <?php echo $uye_sayisi; ?>)</button>
        <?php } ?>
    </form>
    <div class="clearfix mb10"></div>
<?php } ?>

<?php 
$uye_sql=mysqli_query($baglan,"select * from uyeler where azalan_update='0' order by id asc limit 500");
$kalan_uye_sayisi=mysqli_num_rows($uye_sql);
if ($kalan_uye_sayisi>0) {
?>
<div class="panel panel-default">
    <div class="table-responsive">
    <table class="table table-default table-condensed">
        <thead>
            <tr>
                <th>ID</th>
                <th>Ad Soyad</th>
                <th>Email Adresi</th>
                <th class="bg-danger"><?php echo _LANG("pn_sabit_ilan_limiti"); ?></th>
                <th class="bg-danger"><?php echo _LANG("pn_sabit_kategori_ilan_limiti"); ?></th>
                <th class="bg-danger">Sabit Foto Limiti</th>
                <th class="bg-danger"><?php echo _LANG("pn_sabit_aninda_onay"); ?></th>
                <th class="bg-success"><?php echo _LANG("pn_azalan_ilan_limiti"); ?></th>
                <th class="bg-success"><?php echo _LANG("pn_azalan_kategori_ilan_limiti"); ?></th>
                <th class="bg-success">Azalan Foto Limiti</th>
                <th class="bg-success"><?php echo _LANG("pn_azalan_aninda_onay"); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php 
            while ($uye_dizi=mysqli_fetch_array($uye_sql)) {
            $uye_id=$uye_dizi["id"];
            $uye_ad_soyad=$uye_dizi["ad_soyad"];
            $uye_email_adresi=$uye_dizi["email_adresi"];
            $sabit_limit=uye_sabit_ilan_limiti("toplam",$uye_id);
            $sabit_kategori_limit=uye_sabit_ilan_limiti("kategori",$uye_id);
            $sabit_foto_limit=uye_sabit_ilan_limiti("foto",$uye_id);
            $sabit_aninda_onay=uye_sabit_ilan_limiti("aninda_onay",$uye_id);
            $azalan_limit=uye_azalan_ilan_limiti("toplam",$uye_id);
            $azalan_kategori_limit=uye_azalan_ilan_limiti("kategori",$uye_id);
            $azalan_foto_limit=uye_azalan_ilan_limiti("foto",$uye_id);
            $azalan_aninda_onay=uye_azalan_ilan_limiti("aninda_onay",$uye_id);
            ?>
            <tr>
                <th><?php echo $uye_id; ?></th>
                <td><?php echo $uye_ad_soyad; ?></td>
                <td><?php echo $uye_email_adresi; ?></td>
                <td class="bg-danger"><?php echo $sabit_limit; ?></td>
                <td class="bg-danger"><?php echo $sabit_kategori_limit; ?></td>
                <td class="bg-danger"><?php echo $sabit_foto_limit; ?></td>
                <td class="bg-danger"><?php echo $sabit_aninda_onay; ?></td>
                <td class="bg-success"><?php if ($azalan_limit) {echo $azalan_limit;} else {echo "<span class='label label-danger'>Tanımlanmadı</span>";} ?></td>
                <td class="bg-success"><?php if ($azalan_kategori_limit) {echo $azalan_kategori_limit;} else {echo "<span class='label label-danger'>Tanımlanmadı</span>";} ?></td>
                <td class="bg-success"><?php if ($azalan_foto_limit) {echo $azalan_foto_limit;} else {echo "<span class='label label-danger'>Tanımlanmadı</span>";} ?></td>
                <td class="bg-success"><?php echo $azalan_aninda_onay; ?></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
    </div>
</div>

<?php } ?>

<h4><?php echo _LANG("pn_magaza_limitleri"); ?></h4>
<?php if ($magaza_sayisi==0) { ?>
    <div><?php echo _LANG('hb_ek_bir_islem_yapmaya_gerek_yok_tum_magazalarin'); ?></div>
<?php } else { ?>
    <form action="" method="post">
        <?php if ($magaza_sayisi>0) { ?>
             <button type="submit" name="magaza_limit_hesapla" value="Hesapla" class="btn btn-danger"><i class="fa fa-check"></i>Tüm Mağazaların Azalan İlan Limitlerini Hesapla (Toplam: <?php echo $magaza_sayisi; ?>)</button>
        <?php } ?>
    </form>
    <div class="clearfix mb10"></div>
<?php } ?>

<?php 
$magaza_sql=mysqli_query($baglan,"select * from magazalar where azalan_update='0' order by id asc limit 500");
$kalan_magaza_sayisi=mysqli_num_rows($magaza_sql);
if ($kalan_magaza_sayisi) {
?>
<div class="panel panel-default">
    <div class="table-responsive">
    <table class="table table-default table-condensed">
        <thead>
            <tr>
                <th>ID</th>
                <th><?php echo _LANG('hb_magaza'); ?></th>
                <th><?php echo _LANG("pn_uye_ad_soyad"); ?></th>
                <th><?php echo _LANG("pn_magaza_durumu"); ?></th>
                <th class="bg-danger"><?php echo _LANG("pn_sabit_ilan_limiti"); ?></th>
                <th class="bg-danger">Sabit Foto Limiti</th>
                <th class="bg-danger"><?php echo _LANG("pn_sabit_danisman_limiti"); ?></th>
                <th class="bg-danger"><?php echo _LANG("pn_sabit_aninda_onay"); ?></th>
                <th class="bg-success"><?php echo _LANG("pn_azalan_ilan_limiti"); ?></th>
                <th class="bg-success">Azalan Foto Limiti</th>
                <th class="bg-success"><?php echo _LANG("pn_azalan_danisman_limiti"); ?></th>
                <th class="bg-success"><?php echo _LANG("pn_azalan_aninda_onay"); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php 
            while ($magaza_dizi=mysqli_fetch_array($magaza_sql)) {
            $id=$magaza_dizi["id"];
            $baslik=$magaza_dizi["baslik"];
            $uye_id=$magaza_dizi["uye_id"];
            $durum=$magaza_dizi["durum"];
            $uye_ad_soyad=getir("uyeler","ad_soyad",$uye_id);
            $sabit_limit=magaza_sabit_ilan_limiti("toplam",$id);
            $sabit_foto_limit=magaza_sabit_ilan_limiti("foto",$id);
            $sabit_danisman_limit=magaza_sabit_ilan_limiti("danisman",$id);
            $sabit_aninda_onay=magaza_sabit_ilan_limiti("aninda_onay",$id);
            $azalan_limit=magaza_azalan_ilan_limiti("toplam",$id);
            $azalan_foto_limit=magaza_azalan_ilan_limiti("foto",$id);
            $azalan_danisman_limit=magaza_azalan_ilan_limiti("danisman",$id);
            $azalan_aninda_onay=magaza_azalan_ilan_limiti("aninda_onay",$id);
            ?>
            <tr>
                <th><?php echo $id; ?></th>
                <td><?php echo $baslik; ?></td>
                <td><?php echo $uye_ad_soyad; ?></td>
                <td><?php if ($durum==1) {echo "<span class='label label-success'>Onaylı</span>";} else if ($durum==0) {echo "<span class='label label-danger'>Pasif</span>";} else if ($durum==2) {echo "<span class='label label-danger'>Süresi doldu</span>";}?></td>
                <td><?php echo $sabit_limit; ?></td>
                <td><?php echo $sabit_foto_limit; ?></td>
                <td><?php echo $sabit_danisman_limit; ?></td>
                <td><?php echo $sabit_aninda_onay; ?></td>
                <td><?php if ($azalan_limit) {echo $azalan_limit;} else {echo "<span class='label label-danger'>Tanımlanmadı</span>";} ?></td>
                <td><?php if ($azalan_foto_limit) {echo $azalan_foto_limit;} else {echo "<span class='label label-danger'>Tanımlanmadı</span>";} ?></td>
                <td><?php if ($azalan_danisman_limit) {echo $azalan_danisman_limit;} else {echo "<span class='label label-danger'>Tanımlanmadı</span>";} ?></td>
                <td><?php echo $azalan_aninda_onay; ?></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
    </div>
</div>
<?php } ?>