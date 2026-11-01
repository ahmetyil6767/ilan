<?php
include("../loader.php");
yonetici_degilse_yonlendir($oturum_id, ayar_getir("site_url")."/$admin_dizini/giris-yap.php");
@$islem=$_GET["islem"];
@$id=$_GET["id"];
if ($islem=="onayla") {
	$onay_id_int=(int)$id;
	$sql=mysqli_prepare($baglan,"update sikayetler set durum=1 where id=?");
	mysqli_stmt_bind_param($sql,"i",$onay_id_int);
	mysqli_stmt_execute($sql);
	mysqli_stmt_close($sql);
	$_SESSION["mesaj"]=array("Şikayet incelendi olarak işaretlendi","success");
	yonlendir("sikayetler.php");
}
if ($islem=="sil") {
	$sil_id_int=(int)$id;
	$sql=mysqli_prepare($baglan,"delete from sikayetler where id=?");
	mysqli_stmt_bind_param($sql,"i",$sil_id_int);
	mysqli_stmt_execute($sql);
	mysqli_stmt_close($sql);
	$_SESSION["mesaj"]=array("Şikayet silindi","success");
	yonlendir("sikayetler.php");
}
?>
<?php include("inc/header.php"); ?>
<div class="content">
	<div class="page-header"><h1>Şikayetler</h1></div>
	<?php if (isset($_SESSION["mesaj"])) { echo '<div class="alert alert-'.$_SESSION["mesaj"][1].'">'.$_SESSION["mesaj"][0].'</div>'; unset($_SESSION["mesaj"]); } ?>
	<div class="table-responsive">
		<table class="table table-striped table-bordered">
			<thead><tr><th>#</th><th>İlan</th><th>Üye</th><th>Neden</th><th>Tarih</th><th>Durum</th><th>İşlem</th></tr></thead>
			<tbody>
			<?php
			$sorgu=mysqli_query($baglan,"select * from sikayetler order by id desc");
			while ($d=mysqli_fetch_array($sorgu)) {
				$ilan_baslik = _DIL_BASLIK(getir("icerik","baslik",$d["icerik_id"]), $d["icerik_id"], "sayfa");
				$uye_ad = $d["uye_id"] ? getir("uyeler","ad_soyad",$d["uye_id"]) : "Misafir";
				$durum_txt = $d["durum"]==1 ? "İncelendi" : "Bekliyor";
				echo "<tr>";
				echo "<td>".$d["id"]."</td>";
				echo "<td><a href='".link_getir($d["icerik_id"])."' target='_blank'>".htmlspecialchars($ilan_baslik,ENT_QUOTES,'UTF-8')."</a></td>";
				echo "<td>".htmlspecialchars($uye_ad,ENT_QUOTES,'UTF-8')."</td>";
				echo "<td>".htmlspecialchars($d["neden"],ENT_QUOTES,'UTF-8')."</td>";
				echo "<td>".$d["tarih_saat"]."</td>";
				echo "<td>".$durum_txt."</td>";
				echo "<td>";
				if ($d["durum"]==0) { echo "<a href='sikayetler.php?islem=onayla&id=".$d["id"]."' class='btn btn-xs btn-success'>İncelendi</a> "; }
				echo "<a href='sikayetler.php?islem=sil&id=".$d["id"]."' class='btn btn-xs btn-danger' onclick=\"return confirm('Silmek istediğinize emin misiniz?');\">Sil</a>";
				echo "</td>";
				echo "</tr>";
				if ($d["aciklama"]) {
					echo "<tr><td colspan='7' style='background:#f9f9f9;'><small><strong>Açıklama:</strong> ".htmlspecialchars($d["aciklama"],ENT_QUOTES,'UTF-8')."</small></td></tr>";
				}
			}
			?>
			</tbody>
		</table>
	</div>
</div>
<?php include("inc/footer.php"); ?>