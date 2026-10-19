<?php 
if(count(get_included_files())==1) {die();}
tema_header(); 
$baslik=getir("kategoriler","baslik",$id);
$sql_kategori=sql_kategori($id);

$toplam_icerik=mysqli_num_rows(mysqli_query($baglan,"select * from icerik where tip='yazi' and durum='1' and $sql_kategori"));

$sayfa=$_GET["sayfa"];
if (!$sayfa) {$sayfa=1;}
$listeleme_adedi=9;
$baslangic=($sayfa-1)*$listeleme_adedi;
$kac_sayfa=ceil($toplam_icerik/$listeleme_adedi);

?>
<div class="container yazi kategori mt20">
<div class="row">
	<div class="col-md-30 col-md-offset-6">
		
		<?php include("temalar/$aktif_tema/inc/alert.php"); ?>
		<div class="kategori-baslik text-uppercase"><?php echo $baslik; ?></div>
		
		<?php include("temalar/$aktif_tema/inc/alert.php"); ?>
		<div class="row">
		<?php
		$sql=mysqli_query($baglan,"select * from icerik where tip='yazi' and durum='1' and $sql_kategori order by tarih_saat desc limit $baslangic, $listeleme_adedi"); 
		$z=1; while ($dizi=mysqli_fetch_array($sql)) {
			$icerik_id=$dizi["id"];
			$baslik=$dizi["baslik"];
			$link=link_getir($icerik_id);
			$resim=thumbnail("260_160",$icerik_id);
		?>
		<div class="col-md-14">
			<div class="icerik">
				<a href="<?php echo $link; ?>" title="<?php echo $baslik; ?>"><img src="<?php echo $resim; ?>" alt="<?php echo $baslik; ?>" class="img-responsive" /></a>
				<h3><a href="<?php echo $link; ?>" title="<?php echo $baslik; ?>"><?php echo $baslik; ?></a></h3>						
			</div>
		</div>
		<?php $z++; } ?>

		</div>
	
		<?php 
		
		if ($kac_sayfa>1) {
			echo "<div class='clearfix mb20'></div>";
			echo "<div class='text-center'><ul class='pagination'>";
			$sayfa_url=kategori_link_getir($_GET["id"]);
			if ($sayfa>1) {
				$onceki_sayfa=$sayfa-1;
				echo "<li><a href='$sayfa_url/$onceki_sayfa' aria-label='Önceki'><i class='ion-chevron-left'></i></a></li>";
			}		
			for ($x=1;$x<=$kac_sayfa;$x++) {
				if ($x==$sayfa) {$active="class='active'";$link_yaz="";} else {$active="";$link_yaz="href='$sayfa_url/$x'";}
				echo "<li $active><a $link_yaz>$x</a></li>";
			}
			if ($sayfa<$kac_sayfa) {
				$sonraki_sayfa=$sayfa+1; 
				echo "<li><a href='$sayfa_url/$sonraki_sayfa' aria-label='Önceki'><i class='ion-chevron-right'></i></a></li>";
			}
			echo "</ul></div>";
		}		
		
		?>
	
	</div>
</div>




		

</div>
<?php tema_footer(); ?>