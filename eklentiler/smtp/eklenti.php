<?php
if(count(get_included_files())==1) {die();}
function smtp_ayar($ayar) {
	global $baglan;
	$dizi=mysqli_fetch_array(mysqli_query($baglan,"select * from ayarlar where anahtar='smtp-$ayar'"));
	return $dizi["deger"];
}
function mail_gonder($ad_soyad,$email_adresi,$baslik,$mesaj,$debug=null) {
	global $include_dizin;
	global $eklentiler_dizini;
	$yol=ROOT_DIR."/$eklentiler_dizini/smtp/phpmailer/PHPMailerAutoload.php";
	include_once($yol);
	if (!$debug) {
		$debug=0;
	}
	$mail = new PHPMailer;
	$mail->CharSet = 'UTF-8';
	$mail->SMTPDebug = $debug;                              			 	 // Enable verbose debug output
	$mail->isSMTP();                                     				 // Set mailer to use SMTP
	$mail->Host = smtp_ayar("smtp_host");  							 // Specify main and backup SMTP servers
	$mail->SMTPAuth = smtp_ayar("smtp_auth");               			 // Enable SMTP authentication
	$mail->Username = smtp_ayar("smtp_username");          			 // SMTP username
	$mail->Password = smtp_ayar("smtp_password");            			 // SMTP password
	$mail->SMTPSecure = smtp_ayar("smtp_type");                    	 // Enable TLS encryption, `ssl` also accepted
	$mail->Port = smtp_ayar("smtp_port");                           	 // TCP port to connect to
	$mail->setFrom(smtp_ayar("from_email"), smtp_ayar("from_name"));
	$mail->addAddress($email_adresi,$ad_soyad);
	$mail->isHTML(true);                                  				 // Set email format to HTML
	$mail->Subject = "$baslik";
	$mail->Body    = "$mesaj";
	$mail->AltBody = "Mesaj: $mesaj";

	if($mail->send()) {
		return true;
	} else {
		return $mail->ErrorInfo;
	}
}

$hizli_linkler[]=array("baslik"=>_LANG('tx_e_posta_gonder'),"url"=>"eklentiler.php?eklenti=smtp&sayfa=mail","ikon"=>"send-o","sira"=>150);
$jaayquickpanel_menu[]=array("baslik"=>_LANG('tx_smtp_mail_ayarlari'),"url"=>"eklentiler.php?eklenti=smtp&islem=ayar","ikon"=>"envelope-o","sira"=>51);

?>