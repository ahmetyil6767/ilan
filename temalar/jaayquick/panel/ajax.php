<?php 
include_once("../../../loader.php");
$islem=$_GET["islem"];
if (!strstr(ref_url(),site_url())) {
	die("Hata: Erisim engellendi.");
}
if (!$islem) {
	die("Hata");
}
if ($islem=="il_sec") {
	$ust=$_GET["ust"];
	$gerekli=$_GET["gerekli"];
	$icerik_id=$_GET["icerik_id"];
	$il=$_GET["il"];
	$ilce=$_GET["ilce"];
	$mahalle=$_GET["mahalle"];
	
	if ($ilce) {
		$gonder.="&ilce=$ilce";
	}
	if ($mahalle) {
		$gonder.="&mahalle=$mahalle";
	}
	
	if ($icerik_id!="") {
		$icerik_il=bilgi("il",$icerik_id);
	}
	if ($gerekli==1) {$gerekli_yaz="required='required'";$gerekli_gonder=1;
	echo "<label for='il'>İl <span style='color:#e74c3c;'>*</span></label>";
	} else {$gerekli_yaz=null;$gerekli_gonder=0;}
	echo "<select name='il' id='il' class='form-control' $gerekli_yaz><option value=''>İl Seçiniz</option>";
	$sql=mysqli_query($baglan,"select * from iller");
	$selected_yaz=null;
	while ($dizi=mysqli_fetch_array($sql)) {
		$id=$dizi["id"];
		$ad=$dizi["il_adi"];
		if ($icerik_id and $id==$icerik_il) {
			$selected_yaz="selected='selected'";
		} else {
			$selected_yaz=null;
			if ($il and $il==$id) {$selected_yaz="selected='selected'";} else {$selected_yaz=null;}
		}
		echo "<option value='$id' $selected_yaz>$ad</option>";
	}	
	echo "</select>";
	$tema_dizini=tema_dizini();
	echo "
	<script type='text/javascript'>
	$('body').on('change','#il',function(){
		var deger=$(this).val();
		var icerik_id='$icerik_id';
		var il=$('#il option:checked').text();
		$('#mahalle_sec').html('');
		$('#mahalle_sec').hide();
		$('#ilce_sec').load('$tema_dizini/panel/ajax.php?islem=ilce_sec&ust='+deger+'&gerekli=$gerekli_gonder'+'$gonder');
	});
	</script>	
	";
	if ($icerik_id and $icerik_il) {
	echo "
	<script type='text/javascript'>
		$('#ilce_sec').load('$tema_dizini/panel/ajax.php?islem=ilce_sec&ust=$icerik_il&icerik_id=$icerik_id&gerekli=$gerekli_gonder');
	</script>			
	";
	}
	if ($il) {
	echo "
	<script type='text/javascript'>
		$('#ilce_sec').load('$tema_dizini/panel/ajax.php?islem=ilce_sec&ust=$il&gerekli=$gerekli_gonder'+'$gonder');
	</script>	
	";
	}
}
if ($islem=="ilce_sec") {
	$ust=$_GET["ust"];
	$gerekli=$_GET["gerekli"];
	$icerik_id=$_GET["icerik_id"];
	$il=$_GET["il"];
	$ilce=$_GET["ilce"];
	$mahalle=$_GET["mahalle"];
	if ($mahalle) {
		$gonder.="&mahalle=$mahalle";
	}	
	if ($icerik_id!="") {
		$icerik_ilce=bilgi("ilce",$icerik_id);
	}
	if ($gerekli==1) {$gerekli_yaz="required='required'";$gerekli_gonder=1;
	echo "<label for='ilce'>İlçe <span style='color:#e74c3c;'>*</span></label>";
	} else {$gerekli_yaz=null;$gerekli_gonder=0;}
	echo "<select name='ilce' id='ilce' class='form-control' $gerekli_yaz><option value=''>İlçe Seçiniz</option>";
	$sql=mysqli_query($baglan,"select * from ilceler where il_id='$ust'");
	$selected_yaz=null;
	while ($dizi=mysqli_fetch_array($sql)) {
		$id=$dizi["id"];
		$ad=$dizi["ilce_adi"];
		if ($icerik_id and $id==$icerik_ilce) {
			$selected_yaz="selected='selected'";
		} else {
			$selected_yaz=null;
			if ($ilce and $ilce==$id) {$selected_yaz="selected='selected'";} else {$selected_yaz=null;}
		}
		echo "<option value='$id' $selected_yaz>$ad</option>";
	}	
	echo "</select>";
	$tema_dizini=tema_dizini();
	echo "
	<script type='text/javascript'>
	$('body').on('change','#ilce',function(){
		var ilce=$('#ilce option:checked').text();
		var deger=$(this).val();
		$('#mahalle_sec').html('');
		$('#mahalle_sec').show();
		$('#mahalle_sec').load('$tema_dizini/panel/ajax.php?islem=mahalle_sec&ust='+deger+'&gerekli=$gerekli_gonder');
	});
	</script>	
	";
	if ($icerik_id and $icerik_ilce) {
	echo "
	<script type='text/javascript'>
		$('#mahalle_sec').load('$tema_dizini/panel/ajax.php?islem=mahalle_sec&ust=$icerik_ilce&icerik_id=$icerik_id&gerekli=$gerekli_gonder');
	</script>			
	";
	}
	if ($ilce) {
	echo "
	<script type='text/javascript'>
		$('#mahalle_sec').load('$tema_dizini/panel/ajax.php?islem=mahalle_sec&ust=$ilce&gerekli=$gerekli_gonder'+'$gonder');
	</script>			
	";
	}
}
if ($islem=="mahalle_sec") {
	$ust=$_GET["ust"];
	$gerekli=$_GET["gerekli"];
	$icerik_id=$_GET["icerik_id"];
	$ilce=$_GET["ilce"];
	$mahalle=$_GET["mahalle"];	
	if ($icerik_id!="") {
		$icerik_mahalle=bilgi("mahalle",$icerik_id);
	}
	if ($gerekli==1) {$gerekli_yaz="required='required'";$gerekli_gonder=1;
		echo "<label for='mahalle'>Mahalle <span style='color:#e74c3c;'>*</span></label>";
	} else {$gerekli_yaz=null;$gerekli_gonder=0;}
	echo "<select name='mahalle' id='mahalle' class='form-control' $gerekli_yaz><option value=''>Mahalle Seçiniz</option>";
	$sql=mysqli_query($baglan,"select * from mahalleler where ilce_id='$ust'");
	$selected_yaz=null;
	while ($dizi=mysqli_fetch_array($sql)) {
		$id=$dizi["id"];
		$ad=$dizi["mahalle_adi"];
		if ($icerik_id and $id==$icerik_mahalle) {
			$selected_yaz="selected='selected'";
		} else {
			$selected_yaz=null;
			if ($mahalle and $mahalle==$id) {$selected_yaz="selected='selected'";} else {$selected_yaz=null;}
		}
		echo "<option value='$id' $selected_yaz>$ad</option>";
	}
	echo "</select>";
}


if ($islem=="vitrin_degistir") {
	$icerik_id=$_POST["icerik_id"];
	$resim_url=$_POST["resim_url"];
	bilgi_guncelle("gorsel",$resim_url,$icerik_id);
	echo _LANG('tx_vitrin_gorseli_basariyla_guncellenmistir');
}

?>