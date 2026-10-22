<?php 
if(count(get_included_files())==1) {die();}
@$buton=$_POST["buton"];
if ($buton) {
	$cikti=null;
	$bilgi=$_POST["bilgi"];
	if ($bilgi=="E-posta Adresi") {
		$uyeler_sql=mysqli_query($baglan,"select email_adresi from uyeler");
		while ($dizi=mysqli_fetch_array($uyeler_sql)) {
			$email_adresi=$dizi["email_adresi"];
			$cikti.="$email_adresi\n";
		}
	} else if ($bilgi==_LANG('tx_telefon_numarasi')) {
		$uyeler_sql=mysqli_query($baglan,"select id from uyeler");
		while ($dizi=mysqli_fetch_array($uyeler_sql)) {
			$uye_id=$dizi["id"];
			$telefon=uye_bilgi("cep_tel",$uye_id);
			if ($telefon) {
				$cikti.="$telefon\n";
			}
		}
	}
}

?>
<h3 class="pull-left bolum-baslik text-uppercase"><?php echo _LANG("e_uye_bilgileri"); ?></h3>
<div class="clearfix mb20"></div>
<p><?php echo _LANG('hb_tum_uyelere_ait_e_posta_ve_telefon_numarasi_ka'); ?></p>
<form action="" method="post" style="margin-bottom:20px;">
<?php
form_group(array("id"=>"bilgi","tip"=>"select","baslik"=>"Listelenecek bilgi","secenekler"=>options(_LANG('tx_e_posta_adresi_telefon_numarasi')),"deger"=>$bilgi));
form_buton("buton","Listele");
?>
</form>

<?php if ($buton and $cikti) {
form_group(array("id"=>"cikti","tip"=>"textarea","baslik"=>_LANG('pn_cikti'),"satir"=>30,"deger"=>$cikti));
} else {echo _LANG('tx_sonuc_bulunamadi_2');} ?>