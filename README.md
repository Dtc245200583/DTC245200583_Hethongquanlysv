# Student Management System — Dockerized Web Application

## 1. Giới thiệu

Hệ thống quản lý sinh viên được xây dựng bằng **Laravel 12 + MySQL 8.4**, đóng gói và triển khai toàn bộ bằng **Docker Compose**.
Dự án tích hợp **Nginx reverse proxy (HTTPS)**, **Prometheus + Grafana** để giám sát, **Loki + Promtail** để tập trung log, và các biện pháp **hardening** container.

## 2. Chức năng

- Quản lý sinh viên: thêm, xem, sửa, xóa, tìm kiếm (mã SV / họ tên / email), lọc theo lớp, phân trang.
- Quản lý lớp học: thêm, sửa, xóa (chặn xóa lớp còn sinh viên), xem danh sách sinh viên của lớp.
- Quản lý điểm: nhập, sửa, xóa điểm; xem bảng điểm, điểm trung bình và xếp loại.
- Quản trị CSDL qua phpMyAdmin.
- Giám sát container, Nginx, MySQL bằng Prometheus và Grafana.
- Thu thập và truy vấn log bằng Loki, Promtail và LogQL.

## 3. Kiến trúc

```
Trình duyệt ──HTTPS:443──▶ nginx (reverse proxy, TLS, security headers)
                              │ FastCGI :9000 (mạng backend - internal)
                              ▼
                            app (Laravel / PHP-FPM, user www-data) ──▶ mysql (volume mysql_data)
                                                                          ▲
Trình duyệt ──127.0.0.1:8081──▶ phpmyadmin ───────────────────────────────┘

Mạng monitoring (internal):
  cadvisor ─┐
  nginx-exporter ──▶ nginx:8081/stub_status
  mysql-exporter ──▶ mysql
            └──▶ prometheus ──▶ grafana ◀── loki ◀── promtail ◀── Docker logs của mọi container
```

| Service | Image | Chức năng | Cổng trên host |
|---|---|---|---|
| app | build từ `Dockerfile` (php:8.3-fpm-alpine) | Ứng dụng Laravel | — |
| nginx | nginxinc/nginx-unprivileged:1.27-alpine | Reverse proxy, HTTPS | 80, 443 |
| mysql | mysql:8.4.2 | Cơ sở dữ liệu | — (không publish) |
| phpmyadmin | phpmyadmin:5.2.1 | Quản trị DB | 127.0.0.1:8081 |
| prometheus | prom/prometheus:v2.54.1 | Thu thập metrics | 127.0.0.1:9090 |
| grafana | grafana/grafana:11.2.0 | Dashboard | 127.0.0.1:3000 |
| loki | grafana/loki:3.1.1 | Lưu trữ log | — |
| promtail | grafana/promtail:3.1.1 | Thu thập log | — |
| cadvisor | gcr.io/cadvisor/cadvisor:v0.49.1 | Metrics container | — |
| mysql-exporter | prom/mysqld-exporter:v0.15.1 | Metrics MySQL | — |
| nginx-exporter | nginx/nginx-prometheus-exporter:1.3.0 | Metrics Nginx | — |

## 4. Thiết kế cơ sở dữ liệu

```
classes (id PK, class_name UNIQUE, school_year)
   1 ──< students (id PK, student_code UNIQUE, full_name, email UNIQUE, date_of_birth, class_id FK → classes.id ON DELETE RESTRICT)
             1 ──< grades (id PK, student_id FK → students.id ON DELETE CASCADE, subject_name, score DECIMAL(4,2))
                          UNIQUE(student_id, subject_name), CHECK (score BETWEEN 0 AND 10)
```

Ràng buộc được kiểm tra ở cả 2 tầng: validation của Laravel và ràng buộc của MySQL (UNIQUE, FOREIGN KEY, CHECK).

## 5. Yêu cầu môi trường

- Windows 10/11 + **Docker Desktop** (WSL 2 backend), hoặc Linux có Docker Engine + Docker Compose v2
- Git
- VS Code (khuyến nghị; đã có sẵn tác vụ trong `.vscode/tasks.json`)

## 6. Cài đặt và chạy

```powershell
# 1. Clone
git clone https://github.com/<tai-khoan>/DTC245200583_StudentManagement.git
cd DTC245200583_StudentManagement

# 2. Sinh .env (mật khẩu ngẫu nhiên), secrets/mysql_root_password.txt và chứng chỉ HTTPS tự ký
powershell -ExecutionPolicy Bypass -File scripts\setup.ps1

# 3. Build và khởi chạy (migration tự chạy khi container app khởi động)
docker compose up -d --build

# 4. Tạo dữ liệu mẫu
docker compose exec app php artisan db:seed --force

# 5. Kiểm tra trạng thái (các service có healthcheck phải "healthy")
docker compose ps
```

Trên Linux, thay bước 2 bằng cách `cp .env.example .env`, tự điền mật khẩu, tạo `secrets/mysql_root_password.txt`, và tạo chứng chỉ:
`openssl req -x509 -nodes -newkey rsa:2048 -days 365 -keyout nginx/certs/server.key -out nginx/certs/server.crt -subj "/CN=localhost"`.

| Địa chỉ | Nội dung | Đăng nhập |
|---|---|---|
| https://localhost | Website (chấp nhận cảnh báo chứng chỉ tự ký) | — |
| http://localhost:8081 | phpMyAdmin | `DB_USERNAME` / `DB_PASSWORD` trong `.env` |
| http://localhost:3000 | Grafana | `GRAFANA_ADMIN_USER` / `GRAFANA_ADMIN_PASSWORD` trong `.env` |
| http://localhost:9090/targets | Prometheus targets | — |

## 7. Giám sát (Prometheus + Grafana)

Prometheus scrape các endpoint: `cadvisor:8080`, `nginx-exporter:9113` (đọc `nginx:8081/stub_status`), `mysql-exporter:9104`, `loki:3100`, và chính nó.
Datasource và dashboard được provisioning tự động: Grafana → **Dashboards → Student Management → Tổng quan hệ thống**, gồm:

- Trạng thái Nginx/MySQL, số container đang chạy, uptime MySQL
- CPU, RAM, network theo từng container (cAdvisor)
- Request/giây và kết nối đang mở của Nginx
- Truy vấn/giây của MySQL (select/insert/update/delete)
- Số request theo HTTP status (tính từ log Nginx trong Loki) và log trực tiếp

## 8. Logging (Loki + Promtail)

Promtail đọc log stdout/stderr của mọi container trong project `sms` qua Docker API và gắn nhãn `service`, `container`, `project`, `stream`.
Nginx ghi access log dạng JSON; Laravel ghi log ra stderr (`student.created`, `grade.updated`, …).

Các truy vấn LogQL đã kiểm thử (Grafana → Explore → Loki):

```logql
# 1. Request lỗi (HTTP >= 400) qua Nginx
{service="nginx"} | json | status >= 400

# 2. Số dòng log mỗi service trong 5 phút
sum by (service) (count_over_time({project="sms"}[5m]))

# 3. Hoạt động thêm/sửa/xóa dữ liệu của ứng dụng
{service="app"} |~ "student\\.|class\\.|grade\\."

# 4. Top 5 URL được truy cập nhiều nhất trong 15 phút
topk(5, sum by (uri) (count_over_time({service="nginx"} | json | __error__="" [15m])))

# 5. Cảnh báo/lỗi của MySQL
{service="mysql"} |~ "(?i)warning|error"
```

## 9. Bảo mật (Hardening)

| Biện pháp | Cách triển khai | Cách kiểm chứng |
|---|---|---|
| Non-root container | `app` chạy `USER www-data`; Nginx dùng image `nginx-unprivileged` (uid 101); Grafana, Prometheus, Loki chạy user riêng | `docker compose exec app id` |
| Network isolation | 3 mạng: `frontend`, `backend` (internal), `monitoring` (internal); MySQL chỉ nằm trong `backend` | `docker network inspect sms_backend` |
| Giới hạn cổng | Chỉ publish 80/443; phpMyAdmin, Grafana, Prometheus bind `127.0.0.1`; MySQL và exporter không publish | `docker compose ps` |
| Quản lý bí mật | `.env` và `secrets/` nằm trong `.gitignore`; mật khẩu root MySQL dùng Docker secret (`MYSQL_ROOT_PASSWORD_FILE`); mật khẩu sinh ngẫu nhiên | `git check-ignore -v .env` |
| Hạn chế quyền DB | `mysql/init/01-security.sh`: tài khoản ứng dụng chỉ có quyền trên DB `student_management`; exporter chỉ có PROCESS, REPLICATION CLIENT, SELECT, tối đa 3 kết nối | `SHOW GRANTS FOR 'sms_app'@'%'` |
| Security headers + HTTPS | HSTS, CSP, X-Content-Type-Options, X-Frame-Options, Referrer-Policy, Permissions-Policy; `server_tokens off`; ẩn `X-Powered-By`; HTTP → HTTPS; TLS 1.2/1.3 | `curl.exe -skI https://localhost` |
| Giảm đặc quyền | `no-new-privileges`, `cap_drop: ALL`, Nginx `read_only` + tmpfs, rate limit 20 req/s | `docker inspect` |
| Healthcheck và dữ liệu | Healthcheck cho app, nginx, mysql, prometheus, grafana; named volume cho dữ liệu; script `scripts/backup-db.ps1` | `docker compose ps` |

Chạy toàn bộ phần kiểm tra: `powershell -ExecutionPolicy Bypass -File scripts\check-security.ps1`.

> Chứng chỉ tự ký chỉ dùng cho demo học tập. Khi triển khai công khai cần chứng chỉ hợp lệ (ví dụ Let's Encrypt).

## 10. Ảnh minh chứng

Xem thư mục [`screenshots/`](screenshots/).

## 11. Tác giả

- Họ và tên: **Nguyễn Thị Quế**
- Mã sinh viên: **DTC245200583**
- Lớp: CNNT-K23D
- Môn học: Triển khai và quản trị hệ thống
