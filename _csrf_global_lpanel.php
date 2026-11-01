<?php
// Gecici: lpanel dosyalarina global POST csrf kontrolu ekle
$dir = __DIR__.'/lpanel';
$files = glob($dir.'/*.php');
$count = 0;
foreach ($files as $path) {
    $t = file_get_contents($path);
    if (strpos($t, 'csrf_verify()') !== false) { continue; }
    $needle = 'include("../loader.php");';
    if (strpos($t, $needle) === false) { continue; }
    $guard = $needle . "\n" . 'if ($_SERVER["REQUEST_METHOD"] === "POST" && !csrf_verify()) { $_SESSION["mesaj"]=array(\'Güvenlik doğrulaması başarısız. Lütfen sayfayı yenileyip tekrar deneyin.\',\'danger\'); header("Location: " . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url())); exit(); }';
    $t = str_replace($needle, $guard, $t, $n);
    if ($n > 0) {
        file_put_contents($path, $t);
        $count++;
    }
}
echo "Global POST csrf check eklendi: $count dosya\n";
