<?php 
if(count(get_included_files())==1) {die();}
tema_header(); 

$yazi_resmi=thumbnail("560_330",$id);
$baslik=getir("icerik","baslik",$id);
$kategoriler=kategori_getir($id);
?>
<div class="gri">
	<div class="container">
		<ol class="breadcrumb">
		  <li><a href="<?php echo site_url(); ?>">Anasayfa</a></li>
		  <li><a href="<?php echo site_url(); ?>/sayfa/yazilar/"><?php echo _LANG("y_yazilar"); ?></a></li>
		  <?php 
			foreach ($kategoriler as $kategori) {
				$kat_baslik=getir("kategoriler","baslik",$kategori);
				$kat_link=kategori_link_getir($kategori);
				echo "<li><a href='$kat_link'>$kat_baslik</a></li>";
			}
		  ?>		  
		</ol>
	</div>
</div>
<div class="container yazi mt20">
	<div class="row">
		<div class="col-md-21 col-md-offset-5">
			<?php include("temalar/$aktif_tema/inc/alert.php"); ?>
			<h1><?php echo $baslik; ?></h1>
			<div class="bilgi">
			<div class="tarih pull-right"><i class="ion-calendar"></i><?php echo tarih_getir($id); ?></div>
			<?php 
			if (count($kategoriler)>0) {echo "<ul>";}
			foreach ($kategoriler as $kat) { 
			$kat_baslik=getir("kategoriler","baslik",$kat);
			$kat_link=kategori_link_getir($kat);
			?>
			<li><a href="<?php echo $kat_link; ?>"><?php echo $kat_baslik; ?></a></li>
			<?php }
			if (count($kategoriler)>0) {echo "</ul>";}
			?>
			</div>
			<div class="clearfix mb20"></div>
			<?php if ($yazi_resmi) {echo "<img src='$yazi_resmi' alt='$baslik' class='img-responsive mb20' style='border-radius:3px;-webkit-border-radius:3px;-moz-border-radius:3px;' />";} ?>
			<div class="content">
				<?php echo icerik_getir($id); ?>
			</div>
			<div class="yorumlar">
				<div class="panel panel-default beyaz">
					<div><?php echo _LANG('hb_haber_yorumlari'); ?></div>
					<div class="panel-body">
						<div class="fb-comments" data-href="<?php echo link_getir($id); ?>" data-numposts="5" data-width="100%"></div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-10 col-md-offset-1 sidebar">
			<div><?php echo _LANG('hb_ilgili_icerikler'); ?></div>

			<?php
			$benzer_sql=sql_kategori($kategoriler);
			$sql=mysqli_query($baglan,"select * from icerik where tip='yazi' and durum='1' and id!='$id' and ($benzer_sql) order by rand() desc limit 0,5"); 
			$z=1; while ($dizi=mysqli_fetch_array($sql)) {
				$icerik_id=$dizi["id"];
				$baslik=$dizi["baslik"];
				$link=link_getir($icerik_id);
				$resim=thumbnail("260_160",$icerik_id);
				if ($z==1) {$ilgili_icerik=$icerik_id;}
			?>
			<div class="benzer">
				<a href="<?php echo $link; ?>" title="<?php echo $baslik; ?>"><img src="<?php echo $resim; ?>" alt="<?php echo $baslik; ?>" class="img-responsive" /></a>
				<h3><a href="<?php echo $link; ?>" title="<?php echo $baslik; ?>"><?php echo $baslik; ?></a></h3>						
			</div>
			<?php $z++; } ?>

		</div>
	</div>
</div>
<?php tema_footer(); ?>