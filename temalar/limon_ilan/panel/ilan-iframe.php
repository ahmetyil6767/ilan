<?php 
if(count(get_included_files())==1) {die();}
@$buton=$_POST["buton"];
?>
<h3 class="pull-left bolum-baslik text-uppercase"><?php echo _LANG("pn_ilan_iframe"); ?></h3>
<div class="clearfix mb20"></div>
<p><?php echo _LANG('hb_ilanlari_iframe_kodu_ile_farkli_web_sitelerine'); ?></p>


<div class="row">
	<div class="col-md-52">
		<div class="form-group">
			<label for="">Standart</label>
			<div class="input-group">
				<span class="input-group-addon"><a href="<?php echo site_url(); ?>/sayfa/iframe" target="_blank"><?php echo _LANG("pn_sayfayi_ac"); ?></a></span>
				<input type="text" class="form-control" value="<?php echo site_url(); ?>/sayfa/iframe" />
			</div>
		</div>
		<div class="form-group">
			<label><?php echo _LANG('hb_ozel_genislik'); ?></label>
			<div class="input-group">
				<span class="input-group-addon"><a href="<?php echo site_url(); ?>/sayfa/iframe?width=200" target="_blank"><?php echo _LANG("pn_sayfayi_ac"); ?></a></span>
				<input type="text" class="form-control" value="<?php echo site_url(); ?>/sayfa/iframe?width=300" />
			</div>
		</div>
		<div class="form-group">
			<label><?php echo _LANG('hb_ozel_yukseklik'); ?></label>
			<div class="input-group">
				<span class="input-group-addon"><a href="<?php echo site_url(); ?>/sayfa/iframe?height=200" target="_blank"><?php echo _LANG("pn_sayfayi_ac"); ?></a></span>
				<input type="text" class="form-control" value="<?php echo site_url(); ?>/sayfa/iframe?height=300" />
			</div>
		</div>
		<div class="form-group">
			<label><?php echo _LANG('hb_ozel_baslik'); ?></label>
			<div class="input-group">
				<span class="input-group-addon"><a href="<?php echo site_url(); ?>/sayfa/iframe?cat=1&title=Emlak Vitrini" target="_blank"><?php echo _LANG("pn_sayfayi_ac"); ?></a></span>
				<input type="text" class="form-control" value="<?php echo site_url(); ?>/sayfa/iframe?cat=1&title=Emlak Vitrini" />
			</div>
		</div>
		<div class="form-group">
			<label><?php echo _LANG('hb_ilan_listeleme_siniri_limit'); ?></label>
			<div class="input-group">
				<span class="input-group-addon"><a href="<?php echo site_url(); ?>/sayfa/iframe?cat=1&limit=3&title=Emlak Vitrini" target="_blank"><?php echo _LANG("pn_sayfayi_ac"); ?></a></span>
				<input type="text" class="form-control" value="<?php echo site_url(); ?>/sayfa/iframe?cat=1&limit=3&title=Emlak Vitrini" />
			</div>
		</div>
		<div class="form-group">
			<label><?php echo _LANG('hb_ozel_kategori_listelemesi'); ?></label>
			<div class="input-group">
				<span class="input-group-addon"><a href="<?php echo site_url(); ?>/sayfa/iframe?cat=2" target="_blank"><?php echo _LANG("pn_sayfayi_ac"); ?></a></span>
				<input type="text" class="form-control" value="<?php echo site_url(); ?>/sayfa/iframe?cat=2" />
			</div>
		</div>
		<div class="form-group">
			<label><?php echo _LANG('hb_ozel_uye_listelemesi'); ?></label>
			<div class="input-group">
				<span class="input-group-addon"><a href="<?php echo site_url(); ?>/sayfa/iframe?user=1" target="_blank"><?php echo _LANG("pn_sayfayi_ac"); ?></a></span>
				<input type="text" class="form-control" value="<?php echo site_url(); ?>/sayfa/iframe?user=1" />
			</div>
		</div>
	</div>
	<div class="col-md-8">
		<iframe src="<?php echo site_url(); ?>/sayfa/iframe?cat=2" frameborder="0" height="600"></iframe>
	</div>
</div>
