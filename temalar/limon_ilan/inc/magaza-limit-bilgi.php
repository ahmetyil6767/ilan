<?php 
if(count(get_included_files())==1) {die();}
$paket_url=ozel_url("magaza-paket-satin-al")."?id=$id";
if (limit_politikasi()==2) {
if ($oturum_id==getir("magazalar","uye_id",$id)) {
?>
<div style="border:1px solid #EAEAEA;padding:15px 15px 0 15px;margin-bottom:20px;border-radius:4px;">
<div class="row">
    <div class="col-md-a5 col-xs-21">
        <?php 
        $ilan_kredisii=magaza_ilan_limiti("toplam",$id);
		if (!$ilan_kredisii) {$ilan_kredisii=0;}
        ?>
        <a href="<?php echo $paket_url; ?>" style="text-decoration:none;">
        <div class="alert alert-<?php if ($ilan_kredisii<5) {echo "warning";} else {echo "success";} ?>" style="padding:15px 20px;display:block;width:100% !important;position:relative;">
            Mağaza İlan Kredisi:
            <div style="font-size:20px;"><strong><?php echo $ilan_kredisii; ?></strong></div>
            <div class="pabsolute top right" style="top:13px;right:5px;opacity:.6;"><i class="fa fa-pie-chart fa-4x"></i></div>
        </div>
        </a>
    </div>
    <div class="col-md-a5 col-xs-21">
       <?php 
		$foto_limit=magaza_ilan_limiti("foto",$id);
		if (!$foto_limit) {$foto_limit=0;}
	   ?>
       <a href="<?php echo $paket_url; ?>" style="text-decoration:none;">
        <div class="alert alert-primary" style="padding:15px 20px;display:block;width:100% !important;position:relative;">
            <?php echo _LANG('lb_fotograf_limiti'); ?>
            <div style="font-size:20px;"><strong><?php echo $foto_limit; ?></strong></div>
            <div class="pabsolute top right" style="top:13px;right:5px;opacity:.6;"><i class="fa fa-picture-o fa-4x"></i></div>
        </div>
        </a>
    </div>
    <div class="col-md-a5 col-xs-21">
       <?php 
        $aninda_onayy=magaza_ilan_limiti("aninda_onay",$id);
        ?>
        <a href="<?php echo $paket_url; ?>" style="text-decoration:none;">
        <div class="alert alert-<?php if ($aninda_onayy==1) {echo "success";} else {echo "danger";} ?>" style="padding:15px 20px;display:block;width:100% !important;position:relative;">
           Anında Onay:
            <div style="font-size:20px;"><strong><?php if ($aninda_onayy==1) {echo "<i class='fa fa-check'></i> Evet";} else {echo "<i class='fa fa-times'></i> Hayır";} ?></strong></div>
            <div class="pabsolute top right" style="top:13px;right:5px;opacity:.6;"><i class="fa fa-rocket fa-4x"></i></div>
        </div>
        </a>
    </div>
    <div class="col-md-a5 col-xs-21">
       <?php 
		$danisman_limit=magaza_ilan_limiti("danisman",$id);
		if (!$danisman_limit) {$danisman_limit=0;}
	   ?>
        <a href="<?php echo $paket_url; ?>" style="text-decoration:none;">
        <div class="alert alert-warning" style="padding:15px 20px;display:block;width:100% !important;position:relative;">
           <?php echo _LANG('lb_danisman_limiti'); ?>
            <div style="font-size:20px;"><strong><?php echo $danisman_limit; ?></strong></div>
            <div class="pabsolute top right" style="top:13px;right:5px;opacity:.6;"><i class="fa fa-users fa-4x"></i></div>
        </div>
        </a>
    </div>
    <div class="col-md-a5 col-xs-21">
        <div class="alert alert-info" style="padding:15px 20px;display:block;width:100% !important;position:relative;">
            Eklenme Tarihi:
            <div style="font-size:20px;"><strong><?php echo tarih_bicimlendir(explode(" ",getir("magazalar","tarih_saat",$id))[0]); ?></strong></div>
            <div class="pabsolute top right" style="top:10px;right:5px;opacity:.6;"><i class="fa fa-calendar fa-4x"></i></div>
        </div>
    </div>
</div>

<?php if (browser_url()!=ozel_url("magaza-paket-satin-al")."?id=$id") {
    
    if ($ilan_kredisii==0) {
        echo "<div class='alert alert-danger'><i class='fa fa-bell'></i> İlan krediniz tükendi. Yeni bir ilan eklemek için öncelikle ilan paketi satın almalısınız. Paket satın almak için <a href='$paket_url' class='alert-link'>buraya</a> tıklayın.</div>";
    } else {
        if ($ilan_kredisii<25) {
            echo "<div class='alert alert-light'><i class='fa fa-bell'></i> İlan krediniz azalıyor. Web sitemize ilan eklemeye devam edebilmek için ilan paketi satın almanız gerekmektedir. Paket satın almak için <a href='$paket_url' class='alert-link' style='color:#666 !important;'>buraya</a> tıklayın.</div>";
        }
    } 
    
}
?>
</div>
<?php } } ?>