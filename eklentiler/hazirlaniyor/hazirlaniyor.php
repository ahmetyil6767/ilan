<?php if(count(get_included_files())==1) {die();} ?>
<!DOCTYPE HTML>
<html lang="<?php echo _DIL_HTML_LANG(); ?>" dir="<?php echo _DIL_HTML_YON(); ?>">
<head>
<meta charset="UTF-8">
<title><?php echo meta_title(); ?></title>
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
<style type="text/css">
.giris {margin-top:160px;background:rgba(0,0,0,0.6);padding:30px;text-align:center;color:#fff;width:100%;border-radius:15px;}
.giris h1 {font-size:30px;font-weight:700;margin-bottom:20px;}
.giris p {font-size:20px;font-weight:500;}
</style>
</head>
<body style="background:url(<?php echo eklenti_ayar("bg","hazirlaniyor"); ?>);">
	<div class="container">
		<div class="row">
			<div class="col-md-6 col-md-offset-3">
				<div class="giris">
					<h1><?php echo eklenti_ayar("baslik","hazirlaniyor"); ?></h1>
					<p><?php echo eklenti_ayar("metin","hazirlaniyor"); ?></p>			
				</div>
			</div>
		</div>
	</div>
</body>
</html>