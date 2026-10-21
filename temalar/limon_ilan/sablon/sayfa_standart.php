<?php 
if(count(get_included_files())==1) {die();}
tema_header(); ?>
<div class="container sayfa mt20" style="padding:30px 0;">
	<div class="row">
		<div class="col-md-9 col-md-offset-5 sidebar">
		<?php if ($menu) {menu_getir($menu,1,1);} ?>
		</div>
		<div class="col-md-23">
			<?php include("temalar/$aktif_tema/inc/alert.php"); ?>
			<h1><?php echo _DIL_BASLIK(getir("icerik","baslik",$id), $id, "sayfa"); ?></h1>
			<div class="content">
				<?php echo icerik_getir($id); ?>
				
				<?php 
				$iletisim_formu=bilgi("iletisim_formu");
				if ($iletisim_formu) {
					$buton=$_POST["buton"];
					$ad_soyad=$_POST["ad_soyad"];
					$email_adresi=$_POST["email_adresi"];
					$konu=$_POST["konu"];
					$mesaj=$_POST["mesaj"];
					
					if ($buton) {
						
						$ileti_admin="
						<p>Ad soyad: $ad_soyad</p>
						<p>E-posta adresi: $email_adresi</p>
						<p>Konu: <strong>$konu</strong></p>
						<p>Mesaj: $mesaj</p>
						";
						$mail_baslik=tema_ayar("mail_baslik");
						$site_email=ayar("site_email");
						auto_mail_add($mail_baslik,$site_email,"Yeni iletişim formu: $ad_soyad",$ileti_admin);						
						// E-posta gönderimi bitti						
						echo "<div class='alert alert-success'>Mesajınız bize ulaştı. Size en kısa zamanda dönüş yapacağız.</div>";
					}

					?>
					<div class="panel panel-default panel-limon" style="margin-top:20px;">
						<div><?php echo _LANG('hb_iletisim_formu'); ?></div>
						<div class="panel-body">
							<form action="" method="post">
								<?php 
								form_group(array("tip"=>"text","id"=>"ad_soyad","baslik"=>"Ad Soyad","gerekli"=>1)); 
								form_group(array("tip"=>"text","id"=>"email_adresi","baslik"=>"E-posta Adresi","gerekli"=>1)); 
								form_group(array("tip"=>"text","id"=>"konu","baslik"=>"Konu","gerekli"=>1)); 
								form_group(array("tip"=>"textarea","satir"=>3,"id"=>"mesaj","baslik"=>_LANG('msg_your_message'),"gerekli"=>1)); 
								form_buton("buton","Gönder","primary");
								?>
							</form>
						</div>
					</div>
					<?php 
				}
				
				
				?>
				
			</div>
		</div>
	</div>
</div>
<?php tema_footer(); ?>