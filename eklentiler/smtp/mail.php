<?php 
if(count(get_included_files())==1) {die();}
@$buton=$_POST["buton"];
@$baslik=$_POST["baslik"];
@$email=$_POST["email"];
@$mesaj=$_POST["mesaj"];

if ($buton) {
	if (!$baslik) {$baslik="SMTP Test Mail";}
	if (!$mesaj) {$mesaj="Bu bir test mailidir.";}
	
	$mail_gonder=mail_gonder(null,$email,$baslik,$mesaj);
	if ($mail_gonder==1) {
		$_SESSION["mesaj"]=array(_LANG('msj_mail_gonderildi'),"success");
		yonlendir(browser_url());		
	} else {
		$_SESSION["mesaj"]=array(sprintf(_LANG('msj_mail_gonderilemedi'), $mail_gonder),"danger");
		yonlendir(browser_url());		
	}
}

?>

<h3 class="baslik"><?php echo _LANG('e_eposta_gonder'); ?></h3>

<div class="row">
	<div class="col-md-30">
		<div class="panel panel-default">
			<div class="panel-heading"><?php echo _LANG('e_eposta_gonder'); ?></div>
			<div class="panel-body">
				<form action="" method="post">
					<?php echo form_elemani("email","email","E-posta Adresi",null,null,null,"required");
					echo form_elemani("text","baslik",_LANG('p_baslik'),null,null,null);
					echo textarea_olustur(null,"mesaj","Mesaj",null,4);
					form_buton("buton","Gönder","primary"); 
					?>
				</form>
			</div>
		</div>
	</div>
	<div class="col-md-30">
	
	
	</div>
</div>