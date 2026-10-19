<?php
if(count(get_included_files())==1) {die();}
function not_defteri() {
	$not_defteri_textarea=$_POST["not_defteri_textarea"];
	$not_defteri_buton=$_POST["not_defteri_buton"];
	if ($not_defteri_buton) {
		if ($not_defteri_textarea!="") {
			eklenti_ayar_guncelle("not",$not_defteri_textarea,"not_defteri");
			yonlendir("index.php");
		} else {
			yonlendir("eklentiler.php?eklenti=not_defteri&sayfa=onay");
		}
	}
}
not_defteri();
$notlar=eklenti_ayar("not","not_defteri");
$panel_anasayfa_sol_alt_kod[]='
<form action="" method="post">
<div class="panel panel-primary">
	<div class="panel-heading"><i class="fa fa-edit"></i>Not Defteri</div>
	<div class="panel-body">
		<textarea name="not_defteri_textarea" id="" class="ckeditor" cols="30" rows="10">'.$notlar.'</textarea>
	</div>
	<div class="panel-footer">
	<div class="checkbox" id="not_defteri_onay_checkbox">
	  <label><input type="checkbox" name="not_defteri_onay" id="not_defteri_onay" class="nofocus" value="0">Kaydetmeyi onayla</label>
	</div>
	<button type="submit" name="not_defteri_buton" id="not_defteri_buton" class="btn btn-primary" value="Kaydet">Kaydet</button>
	</div>
</div>
</form>
<script type="text/javascript">
$(function(){
	$("#not_defteri_buton").hide();
	$("#not_defteri_onay").change(function() {
		if(this.checked) {
			$("#not_defteri_onay_checkbox").hide();
			$("#not_defteri_buton").show();
		}
	});
});
</script>
';
?>