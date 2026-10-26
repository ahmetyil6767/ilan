<?php if(count(get_included_files())==1) {die();} ?>
<div id="mesaj_kutusu">
<?php
/*
 * Oturum mesaji: array(metin, bicim). PHP 8'de tanimsiz indeks
 * uyarisi uretmemek icin isset kontrolu yapilir.
 */
if (isset($_SESSION["mesaj"]) and is_array($_SESSION["mesaj"]) and isset($_SESSION["mesaj"][0]) and $_SESSION["mesaj"][0]!=="") {
$mesaj_mesaj=stripslashes((string)$_SESSION["mesaj"][0]);
$mesaj_bicim=(isset($_SESSION["mesaj"][1]) and is_string($_SESSION["mesaj"][1])) ? $_SESSION["mesaj"][1] : "";
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