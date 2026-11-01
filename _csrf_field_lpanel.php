<?php
// Gecici: lpanel formlarina csrf_field enjekte et
$dir = __DIR__.'/lpanel';
$files = glob($dir.'/*.php');
$modified = 0;
foreach ($files as $path) {
    $t = file_get_contents($path);
    if (strpos($t, 'method="post"') === false) { continue; }
    if (strpos($t, 'csrf_field()') !== false) { continue; }
    $t2 = preg_replace('/method="post"[^>]*>/', '$0'."\n<?php echo csrf_field(); ?>", $t);
    if ($t2 !== $t) {
        file_put_contents($path, $t2);
        $modified++;
        echo "ENJEKTE: ".basename($path)."\n";
    }
}
echo "Toplam enjekte: $modified\n";
