<?php
// Gecici: tek lpanel dosyasi CSRF uygulama
// Kullanim: php _csrf_lpanel.php <dosya>
if ($argc < 2) { echo "Kullanim: php _csrf_lpanel.php <dosya>\n"; exit(1); }
$dosya = $argv[1];
if (!file_exists($dosya)) { echo "Dosya yok: $dosya\n"; exit(1); }

$icerik = file_get_contents($dosya);

// 1) csrf_field enjeksiyonu: method="post"...> sonrasi (sadece HTML context'te)
// lpanel formlari genellikle HTML icinde; <?php echo csrf_field(); ?> ekle
if (strpos($icerik, 'csrf_field()') === false) {
    $icerik = preg_replace('/method="post"[^>]*>/', "$0\n<?php echo csrf_field(); ?>", $icerik);
}

// 2) csrf_verify guard: if ($buton) { veya if (@$buton) { sonrasi
if (strpos($icerik, 'csrf_verify()') === false) {
    $icerik = preg_replace(
        '/if \(@?\$buton\) \{/',
        "$0\n\t\tif (!csrf_verify()) { \$_SESSION[\"mesaj\"]=array('Güvenlik doğrulaması başarısız. Lütfen sayfayı yenileyip tekrar deneyin.','danger'); header('Location: ' . (isset(\$_SERVER['HTTP_REFERER']) ? \$_SERVER['HTTP_REFERER'] : site_url())); exit(); }",
        $icerik
    );
}

file_put_contents($dosya, $icerik);
echo "OK: $dosya\n";
