# Sao lưu CSDL ra thư mục backups\ (chạy khi các container đang hoạt động).
#   powershell -ExecutionPolicy Bypass -File scripts\backup-db.ps1
$ErrorActionPreference = 'Stop'
Set-Location (Split-Path $PSScriptRoot -Parent)

$stamp = Get-Date -Format 'yyyyMMdd-HHmmss'
$file = "student_management-$stamp.sql"
New-Item -ItemType Directory -Force backups | Out-Null

# Dump bên trong container rồi copy ra, tránh PowerShell đổi encoding khi chuyển hướng output.
docker compose exec -T mysql sh -c "mysqldump -uroot -p`"`$(cat /run/secrets/mysql_root_password)`" --single-transaction --routines --databases student_management > /tmp/$file"
docker compose cp "mysql:/tmp/$file" "backups/$file"
docker compose exec -T mysql rm "/tmp/$file"

Write-Host "Da sao luu: backups\$file"
# Khôi phục:  Get-Content backups\<file>.sql -Raw | docker compose exec -T mysql sh -c 'mysql -uroot -p"$(cat /run/secrets/mysql_root_password)"'
