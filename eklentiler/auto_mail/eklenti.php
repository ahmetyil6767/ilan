<?php
if(count(get_included_files())==1) {die();}
$limonpanel_menu[]=array("baslik"=>_LANG('tx_gonderilen_mailler'),"url"=>"eklentiler.php?eklenti=auto_mail&sayfa=rapor","ikon"=>"send","sira"=>100);

$auto_mail_tablo=mysqli_num_rows(mysqli_query($baglan,"SHOW TABLES LIKE '%auto_mail%'"));
if ($auto_mail_tablo==0) {
	$auto_mail_tablo_ekle=mysqli_query($baglan,"
	CREATE TABLE IF NOT EXISTS `auto_mail` (
	  `id` int(10) NOT NULL AUTO_INCREMENT,
	  `ad_soyad` varchar(80) COLLATE utf8_turkish_ci NOT NULL,
	  `email_adresi` varchar(80) COLLATE utf8_turkish_ci NOT NULL,
	  `baslik` varchar(160) COLLATE utf8_turkish_ci NOT NULL,
	  `mesaj` text COLLATE utf8_turkish_ci NOT NULL,
	  `tarih_saat` varchar(20) COLLATE utf8_turkish_ci NOT NULL,
	  `gonderim_tarih_saat` varchar(20) COLLATE utf8_turkish_ci NOT NULL,
	  `durum` int(1) NOT NULL,
	  PRIMARY KEY (`id`),
	  KEY `durum` (`durum`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci AUTO_INCREMENT=1 ;
");
}

function auto_mail_send($ad_soyad,$email_adresi,$baslik,$mesaj) {
	global $include_dizin;
	global $eklentiler_dizini;
	$yol=ROOT_DIR."/$eklentiler_dizini/smtp/phpmailer/PHPMailerAutoload.php";
	include_once($yol);
	$mail = new PHPMailer;
	$mail->CharSet = 'UTF-8';
	
	//$mail->SMTPDebug = 3;                              			 	 // Enable verbose debug output

	/*
	 * SMTP sunucusu tanimli degilse PHP mail() fonksiyonuna duser.
	 * Boylece eklenti ayarlari tamamlanana kadar gonderim hatasi vermez.
	 */
	$smtp_host = eklenti_ayar_getir("smtp_host","smtp");
	if ($smtp_host) {
		$mail->isSMTP();                                     				 // Set mailer to use SMTP
		$mail->Host = $smtp_host;  							 // Specify main and backup SMTP servers
		$mail->SMTPAuth = eklenti_ayar_getir("smtp_auth","smtp");               			 // Enable SMTP authentication
		$mail->Username = eklenti_ayar_getir("smtp_username","smtp");          			 // SMTP username
		$mail->Password = eklenti_ayar_getir("smtp_password","smtp");            			 // SMTP password
		$mail->SMTPSecure = eklenti_ayar_getir("smtp_type","smtp");                    	 // Enable TLS encryption, `ssl` also accepted
		$mail->Port = eklenti_ayar_getir("smtp_port","smtp");                           	 // TCP port to connect to
	} else {
		$mail->isMail();                                    // Yerel PHP mail() ile gonderim
	}
	$mail->setFrom(eklenti_ayar_getir("from_email","smtp"), eklenti_ayar_getir("from_name","smtp"));
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

function auto_mail_add($ad_soyad,$email_adresi,$baslik,$mesaj) {
	global $baglan;
	$tarih_saat=tarih_saat();
	$ekle=mysqli_query($baglan,"insert into auto_mail set ad_soyad='$ad_soyad', email_adresi='$email_adresi', baslik='$baslik', mesaj='$mesaj', tarih_saat='$tarih_saat', durum='0'");
	if ($ekle) {
		return true;
	} else {
		return false;
	}
}

function auto_mail() {
	global $baglan;
	$mail_sql=mysqli_query($baglan,"select * from auto_mail where durum='0' order by tarih_saat desc limit 0, 1");
	$mail_sayisi=mysqli_num_rows($mail_sql);
	if ($mail_sayisi>0) {
		while ($mail_dizi=mysqli_fetch_array($mail_sql)) {
			$mail_id=$mail_dizi["id"];
			$ad_soyad=$mail_dizi["ad_soyad"];
			$email_adresi=$mail_dizi["email_adresi"];
			$baslik=$mail_dizi["baslik"];
			$mesaj=$mail_dizi["mesaj"];
			$gonder=auto_mail_send($ad_soyad,$email_adresi,$baslik,$mesaj);
			if ($gonder==true) {
				$tarih_saat=tarih_saat();
				mysqli_query($baglan,"update auto_mail set durum='1', gonderim_tarih_saat='$tarih_saat' where id='$mail_id'");
			} else {
				$tarih_saat=tarih_saat();
				mysqli_query($baglan,"update auto_mail set durum='2' where id='$mail_id'");				
			}
		}
	}
}

$footer_kod[]=auto_mail();
?>