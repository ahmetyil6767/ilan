# Yedek politikasi: kod + veritabani yedeği, 2 günlük pencere.
# Kural:
#   - Calistiginda DB dump'i + proje dosyalarinin ziplerini SQL/yedekler/ altina yazar (otomatik_*.sql|zip).
#   - 2 günden eski otomatik_* dosyalarini siler (manuel yedek_yedek_*.sql dosyalarina dokunmaz).
#   - Ise: manuel "powershell -ExecutionPolicy Bypass -File otomatik_yedek.ps1"
#          veya gunluk zamanlanmis gorev "LimonYedek" (schtasks).
# Log: SQL/yedekler/yedek_log.txt

$ErrorActionPreference = "Stop"
$kok = Split-Path -Parent $MyInvocation.MyCommand.Path
$hedef = Join-Path $kok "SQL\yedekler"
New-Item -ItemType Directory -Force $hedef | Out-Null
$damga = Get-Date -Format "yyyyMMdd_HHmm"
$log = Join-Path $hedef "yedek_log.txt"
function Log($m) { Add-Content -Path $log -Value ("[" + (Get-Date -Format "yyyy-MM-dd HH:mm") + "] " + $m) -Encoding utf8 }

Log "yedek basladi"

# 1) Veritabani dump'i (mysqldump, root sifresiz; db.php gizli, dogrudan okunmaz)
$mysqlBin = "C:\wamp64\bin\mysql\mysql8.4.7\bin\mysqldump.exe"
if (-not (Test-Path $mysqlBin)) { $mysqlBin = (Get-Command mysqldump -ErrorAction SilentlyContinue).Source }
if (-not $mysqlBin) { throw "mysqldump bulunamadi" }
$dump = Join-Path $hedef ("otomatik_db_" + $damga + ".sql")
& $mysqlBin -u root --single-transaction --quick --default-character-set=utf8mb4 jaayko_limon 2>$null | Out-File -FilePath $dump -Encoding utf8
$boyut = (Get-Item $dump).Length
if ($boyut -lt 1MB) { Log "HATA: DB dump cok kucuk ($boyut bayt)"; throw "DB yedeği güvenilir degil" }
Log ("DB yedegi: " + (Split-Path -Leaf $dump) + " (" + [math]::Round($boyut/1MB,1) + " MB)")

# 2) Dosya yedegi (zip): proje, .git ve SQL/yedekler haric (uploads dahil)
$gecici = Join-Path $env:TEMP "limon_yedek_zip_kok"
if (Test-Path $gecici) { Remove-Item $gecici -Recurse -Force }
New-Item -ItemType Directory -Force $gecici | Out-Null
robocopy $kok $gecici /MIR /NFL /NDL /NJH /NJS /XD .git yedekler /XF yedek_log.txt | Out-Null
$zip = Join-Path $hedef ("otomatik_dosyalar_" + $damga + ".zip")
if (Test-Path $zip) { Remove-Item $zip -Force }
Compress-Archive -Path ($gecici + "\*") -DestinationPath $zip -Force
Remove-Item $gecici -Recurse -Force
Log ("Dosya yedegi: " + (Split-Path -Leaf $zip) + " (" + [math]::Round((Get-Item $zip).Length/1MB,1) + " MB)")

# 3) Saklama penceresi: 2 gunden eski otomatik_* dosyalarini sil
$esik = (Get-Date).AddDays(-2)
$silinen = 0
Get-ChildItem $hedef -Filter "otomatik_*" | Where-Object { $_.LastWriteTime -lt $esik } | ForEach-Object {
    Remove-Item $_.FullName -Force; $silinen++
}
Log ("2 gunden eski otomatik yedek silindi: $silinen dosya")

# 4) Durum ozeti
$kalan = Get-ChildItem $hedef -Filter "otomatik_*" | Sort-Object LastWriteTime -Descending
foreach ($k in $kalan) { Log ("kalan: " + $k.Name) }
Log "yedek tamam"
