<?php if(count(get_included_files())==1) {die();} ?>
<div id="mesaj_kutusu">
<?php
if (@$_SESSION["mesaj"]!="") {
$mesaj_mesaj=stripslashes($_SESSION["mesaj"][0]);
$mesaj_bicim=$_SESSION["mesaj"][1];
echo "<div class='alert alert-$mesaj_bicim autoclose' >
$mesaj_mesaj
<button type='button' class='close' data-dismiss='alert' aria-label='Close'>
  <span aria-hidden='true'>&times;</span>
</button>
</div>";
$_SESSION["mesaj"]="";
}
?>
</div>