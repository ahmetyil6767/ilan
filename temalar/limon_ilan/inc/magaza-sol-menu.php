<?php if(count(get_included_files())==1) {die();} ?>
<div class="panel panel-default limon menu">
    <div class="panel-heading"><?php echo _LANG('ms_magaza_ayarlari'); ?></div>
    <div class="panel-body">
        <ul>
            <li <?php if (strstr(browser_url(),ozel_url("magaza-bilgileri"))) {echo "class='active'";} ?>><a href="<?php echo ozel_url("magaza-bilgileri"); ?>?id=<?php echo $id; ?>"><?php echo _LANG("store_update_info"); ?></a></li>
            <?php if (limit_politikasi()==1) { ?>
            <li <?php if (strstr(browser_url(),ozel_url("magaza-plan-detaylari"))) {echo "class='active'";} ?>><a href="<?php echo ozel_url("magaza-plan-detaylari"); ?>?id=<?php echo $id; ?>"><?php echo _LANG("pn_plan_detaylari_istatistikler"); ?></a></li>
			<?php } else { ?>
			    <li <?php if (browser_url()==ozel_url("magaza-paket-satin-al")."?id=$id") {echo "class='active'";} ?>><a href="<?php echo ozel_url("magaza-paket-satin-al")."?id=$id"; ?>"><?php echo _LANG("title_store_package"); ?></a></li>
			<?php } ?>
            <li <?php if (strstr(browser_url(),ozel_url("danismanlar"))) {echo "class='active'";} ?>><a href="<?php echo ozel_url("danismanlar"); ?>?id=<?php echo $id; ?>"><?php echo _LANG("pn_satis_danismanlarini_duzenle"); ?></a></li>
            <li <?php if (strstr(browser_url(),ozel_url("magaza-profil-gorseli"))) {echo "class='active'";} ?>><a href="<?php echo ozel_url("magaza-profil-gorseli"); ?>?id=<?php echo $id; ?>"><?php echo _LANG("s_profil_degistir"); ?></a></li>
            <li <?php if (strstr(browser_url(),ozel_url("magaza-kapak-gorseli"))) {echo "class='active'";} ?>><a href="<?php echo ozel_url("magaza-kapak-gorseli"); ?>?id=<?php echo $id; ?>"><?php echo _LANG("s_kapak_degistir"); ?></a></li>
        </ul>
    </div>
</div>