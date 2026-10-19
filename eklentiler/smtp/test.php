<?php
if(count(get_included_files())==1) {die();} 
@$buton=$_POST["buton"];
@$email=$_POST["email"];

if ($buton) {
	$baslik="SMTP Test Mail";
	$mesaj="Bu bir test mailidir.";
	$mail_gonder=mail_gonder(null,$email,$baslik,$mesaj,3);
	if ($mail_gonder==1) {
		echo "<div class='alert alert-success'>Test maili başarıyla gönderildi</div>";
	} else {
		echo "<div class='alert alert-danger'>Test maili gönderilemedi. $mail_gonder</div>";
	}
}
?>
<h3 class="baslik"><?php echo _LANG('p_test_mail'); ?></h3>
<div class="row">
	<div class="col-md-30">
		<div class="panel panel-default">
			<div class="panel-heading"><?php echo _LANG('p_test_mail'); ?></div>
			<div class="panel-body">
				<form action="" method="post">
					<?php echo form_elemani("email","email","E-posta Adresi",@$_POST["email"],null,null,"required");
					form_buton("buton","Gönder","primary"); 
					?>
				</form>
			</div>
		</div>
	</div>
	<div class="col-md-30">
	
	</div>
</div>