<?php
require 'config/db.php';
$baglan = mysqli_connect($host, $kullanici, $parola, $veritabani);
mysqli_query($baglan, 'SET NAMES utf8mb4');

// Move settings from ayarlar to ekstra_bilgi for bilesen id=15
$settings = array(
    'menu' => '1',
    'gizle' => '10',
    'tasarim' => '1',
    'ikon_yuvarlak' => '1',
    'ilan_sayisi' => '1',
    'bos_kategoriler' => '1',
    'gosterme' => '',
    'link_gosterme' => '',
    'ust_kategori' => '0',
);

echo "Moving settings to ekstra_bilgi table...\n";
foreach ($settings as $key => $value) {
    // Check if already exists
    $check = mysqli_query($baglan, "select * from ekstra_bilgi where anahtar='$key' and id=15 and tip='bilesen'");
    if (mysqli_num_rows($check) == 0) {
        mysqli_query($baglan, "insert into ekstra_bilgi (anahtar, deger, id, tip) values ('$key', '$value', 15, 'bilesen')");
        echo "✅ Inserted $key = $value\n";
    } else {
        mysqli_query($baglan, "update ekstra_bilgi set deger='$value' where anahtar='$key' and id=15 and tip='bilesen'");
        echo "ℹ️ Updated $key = $value\n";
    }
}

// Clean up ayarlar table
echo "\nCleaning up ayarlar table...\n";
$r = mysqli_query($baglan, "select * from ayarlar where anahtar like 'limon_ilan-anasayfa_sol_menu%'");
while ($row = mysqli_fetch_assoc($r)) {
    mysqli_query($baglan, "delete from ayarlar where anahtar='" . $row['anahtar'] . "'");
    echo "🗑️ Deleted " . $row['anahtar'] . "\n";
}

echo "\nDone!\n";