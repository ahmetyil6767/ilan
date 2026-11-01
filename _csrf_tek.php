<?php
// Gecici: tek dosya CSRF uygulama testi
// Kullanim: php _csrf_tek.php <dosya_yolu>
if ($argc < 2) { echo "Kullanim: php _csrf_tek.php <dosya>\n"; exit(1); }
$dosya = $argv[1];
if (!file_exists($dosya)) { echo "Dosya yok: $dosya\n"; exit(1); }

$icerik = file_get_contents($dosya);

// 1) csrf_field enjeksiyonu (method="post"> sonrasi)
if (strpos($icerik, 'csrf_field()') === false) {
    $icerik = preg_replace('/method="post">\s*/', "method=\"post\">\n<?php echo csrf_field(); ?>\n", $icerik);
}

// 2) csrf_verify guard: if ($buton) { veya if (@$buton) { sonrasi
$icerik = preg_replace(
    '/if \(@?\$buton\) \{/',
    "$0\n\t\t\t\t\t\tif (!csrf_verify()) { \$_SESSION[\"mesaj\"]=array('Güvenlik doğrulaması başarısız. Lütfen sayfayı yenileyip tekrar deneyin.','danger'); header('Location: ' . (isset(\$_SERVER['HTTP_REFERER']) ? \$_SERVER['HTTP_REFERER'] : site_url())); exit(); }",
    $icerik
);

file_put_contents($dosya, $icerik);
echo "OK: $dosya\n";
