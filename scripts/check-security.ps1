# Kiểm tra nhanh các biện pháp hardening — chụp màn hình kết quả làm minh chứng.
#   powershell -ExecutionPolicy Bypass -File scripts\check-security.ps1
Set-Location (Split-Path $PSScriptRoot -Parent)

function Section($title) { Write-Host ""; Write-Host "===== $title =====" -ForegroundColor Cyan }

Section "1. Security headers (HTTPS qua Nginx)"
curl.exe -skI https://localhost/students

Section "2. HTTP chuyen huong sang HTTPS"
curl.exe -sI http://localhost/ | Select-String 'HTTP/|Location'

Section "3. User chay trong container (khong phai root)"
foreach ($svc in 'app', 'nginx', 'grafana', 'prometheus', 'loki') {
    $user = docker compose exec -T $svc id 2>$null
    Write-Host ("{0,-12} {1}" -f $svc, $user)
}

Section "4. Cong dang publish ra host (MySQL/exporter khong co mat)"
docker compose ps --format "table {{.Service}}\t{{.Ports}}"

Section "5. Mang internal (backend, monitoring khong co Internet)"
foreach ($net in 'sms_frontend', 'sms_backend', 'sms_monitoring') {
    $internal = docker network inspect $net --format '{{.Internal}}'
    Write-Host ("{0,-16} internal={1}" -f $net, $internal)
}

Section "6. Quyen cua tai khoan DB ung dung"
docker compose exec -T mysql sh -c 'mysql -uroot -p"$(cat /run/secrets/mysql_root_password)" -e "SHOW GRANTS FOR ''sms_app''@''%''; SHOW GRANTS FOR ''exporter''@''%'';" 2>/dev/null'

Section "7. Trang thai healthcheck"
docker compose ps --format "table {{.Service}}\t{{.Status}}"

Section "8. File bi mat khong bi Git theo doi"
git check-ignore -v .env secrets/mysql_root_password.txt nginx/certs/server.key
