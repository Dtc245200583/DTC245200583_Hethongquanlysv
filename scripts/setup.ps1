# Khởi tạo lần đầu (chạy 1 lần sau khi clone):
#   - Tạo .env từ .env.example với mật khẩu ngẫu nhiên
#   - Tạo secrets/mysql_root_password.txt
#   - Tạo chứng chỉ HTTPS tự ký nginx/certs/server.crt + server.key
# Cách chạy (trong terminal VS Code):  powershell -ExecutionPolicy Bypass -File scripts\setup.ps1
$ErrorActionPreference = 'Stop'
Set-Location (Split-Path $PSScriptRoot -Parent)
$utf8NoBom = New-Object System.Text.UTF8Encoding $false

function New-RandomBytes([int]$count) {
    $bytes = New-Object byte[] $count
    [System.Security.Cryptography.RandomNumberGenerator]::Create().GetBytes($bytes)
    return $bytes
}

# Mật khẩu chỉ gồm chữ và số để tránh lỗi khi nội suy biến trong Docker Compose.
function New-Password([int]$length = 24) {
    $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789'
    return -join ((New-RandomBytes $length) | ForEach-Object { $chars[$_ % $chars.Length] })
}

if (Test-Path .env) {
    Write-Host "[bo qua] .env da ton tai"
} else {
    $content = Get-Content .env.example -Raw -Encoding UTF8
    $content = $content -replace '(?m)^APP_KEY=.*$', ('APP_KEY=base64:' + [Convert]::ToBase64String((New-RandomBytes 32)))
    $content = $content -replace '(?m)^DB_PASSWORD=.*$', ('DB_PASSWORD=' + (New-Password))
    $content = $content -replace '(?m)^MYSQL_EXPORTER_PASSWORD=.*$', ('MYSQL_EXPORTER_PASSWORD=' + (New-Password))
    $content = $content -replace '(?m)^GRAFANA_ADMIN_PASSWORD=.*$', ('GRAFANA_ADMIN_PASSWORD=' + (New-Password 16))
    [System.IO.File]::WriteAllText((Join-Path $PWD '.env'), $content, $utf8NoBom)
    Write-Host "[ok] Da tao .env"
}

$rootSecret = 'secrets\mysql_root_password.txt'
if (Test-Path $rootSecret) {
    Write-Host "[bo qua] $rootSecret da ton tai"
} else {
    [System.IO.File]::WriteAllText((Join-Path $PWD $rootSecret), (New-Password 32), $utf8NoBom)
    Write-Host "[ok] Da tao $rootSecret"
}

if (Test-Path nginx\certs\server.crt) {
    Write-Host "[bo qua] Chung chi HTTPS da ton tai"
} else {
    $openssl = (Get-Command openssl -ErrorAction SilentlyContinue).Source
    if (-not $openssl) { $openssl = Join-Path $env:ProgramFiles 'Git\usr\bin\openssl.exe' }
    if (-not (Test-Path $openssl)) { throw "Khong tim thay openssl. Hay cai Git for Windows." }
    & $openssl req -x509 -nodes -newkey rsa:2048 -days 365 `
        -keyout nginx\certs\server.key -out nginx\certs\server.crt `
        -subj '/C=VN/O=DTC245200583/CN=localhost' `
        -addext 'subjectAltName=DNS:localhost,IP:127.0.0.1'
    if ($LASTEXITCODE -ne 0) { throw "openssl loi (exit $LASTEXITCODE)" }
    Write-Host "[ok] Da tao chung chi HTTPS tu ky"
}

Write-Host ""
Write-Host "Xong. Tiep theo: docker compose up -d --build"
