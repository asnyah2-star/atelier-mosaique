$ErrorActionPreference = 'Stop'
$projectRoot = Split-Path $PSScriptRoot -Parent
$runtimeSpec = Get-Content -LiteralPath (Join-Path $projectRoot 'electron-test/php-runtime.json') -Raw | ConvertFrom-Json
$archiveRoot = Join-Path $projectRoot 'backups'
New-Item -ItemType Directory -Path $archiveRoot -Force | Out-Null
$archivePhp = Join-Path $archiveRoot ('php-' + $runtimeSpec.version + '-nts-x64.zip')
if (-not (Test-Path -LiteralPath $archivePhp)) {
    Invoke-WebRequest -Uri $runtimeSpec.url -OutFile $archivePhp -UseBasicParsing
}
if ((Get-FileHash -LiteralPath $archivePhp -Algorithm SHA256).Hash.ToLowerInvariant() -ne $runtimeSpec.sha256) {
    throw 'Empreinte SHA256 PHP incorrecte.'
}
$phpSource = Join-Path $archiveRoot ('php-source-' + $runtimeSpec.version)
if (-not (Test-Path -LiteralPath $phpSource)) {
    Expand-Archive -LiteralPath $archivePhp -DestinationPath $phpSource
}
$runtimeRoot = Join-Path $projectRoot 'electron-test\runtime\php'
if (Test-Path -LiteralPath $runtimeRoot) { throw 'La copie PHP existe déjà ; aucun fichier ne sera écrasé.' }
New-Item -ItemType Directory -Path (Join-Path $runtimeRoot 'ext') -Force | Out-Null
# Conserver les bibliothèques et leurs informations de redistribution ;
# exclure les configurations de Wamp et ses extensions de débogage.
Get-ChildItem -LiteralPath $phpSource -File | Where-Object {
    $_.Extension -eq '.dll' -or $_.Name -in @('php.exe','license.txt','readme-redist-bins.txt','README.md')
} | ForEach-Object { Copy-Item -LiteralPath $_.FullName -Destination $runtimeRoot }
foreach ($extensionName in @('php_pdo_sqlite.dll','php_gd.dll','php_fileinfo.dll','php_mbstring.dll')) {
    Copy-Item -LiteralPath (Join-Path $phpSource ('ext\' + $extensionName)) -Destination (Join-Path $runtimeRoot 'ext')
}
@'
[PHP]
extension_dir="ext"
extension=php_pdo_sqlite.dll
extension=php_gd.dll
extension=php_fileinfo.dll
extension=php_mbstring.dll
expose_php=Off
display_errors=Off
display_startup_errors=Off
log_errors=On
error_reporting=E_ALL
memory_limit=256M
upload_max_filesize=5M
post_max_size=32M
max_file_uploads=20
date.timezone=Europe/Brussels
session.use_strict_mode=1
session.use_only_cookies=1
session.cookie_httponly=1
session.cookie_samesite=Strict
'@ | Set-Content -LiteralPath (Join-Path $runtimeRoot 'php.ini') -Encoding ASCII
Push-Location $runtimeRoot
try { & '.\php.exe' -c '.\php.ini' -m; if ($LASTEXITCODE -ne 0) { throw 'Vérification PHP échouée.' } } finally { Pop-Location }
Write-Output 'Copie PHP créée dans electron-test/runtime/php. Les binaires restent exclus de Git.'
