# Hướng dẫn thực hiện từng bước trên VS Code (Windows, ổ D)

Sinh viên: **Nguyễn Thị Quế — DTC245200583**
Thư mục dự án: `D:\DTC245200583_StudentManagement`

Mọi lệnh bên dưới gõ trong **Terminal của VS Code** (`Ctrl + `` ` ``), mặc định là PowerShell, và đang đứng ở thư mục dự án.
Hoặc dùng menu tác vụ có sẵn: `Ctrl+Shift+P` → **Tasks: Run Task**.

---

## Bước 0 — Cài Docker Desktop (chỉ làm 1 lần)

1. Mở PowerShell **Run as Administrator**, chạy `wsl --install`, rồi khởi động lại máy.
2. Tải Docker Desktop tại https://www.docker.com/products/docker-desktop/ và cài với tùy chọn **Use WSL 2 instead of Hyper-V**.
3. Mở Docker Desktop, đợi biểu tượng cá voi chuyển sang **Engine running**.
4. Để dữ liệu Docker nằm ở ổ D (tránh đầy ổ C): Docker Desktop → **Settings → Resources → Advanced → Disk image location** → chọn `D:\DockerData` → **Apply & restart**.
5. Đóng và mở lại VS Code, rồi kiểm tra:
   ```powershell
   docker --version
   docker compose version
   ```
6. Cài các extension VS Code được gợi ý: khi mở thư mục, VS Code sẽ hiện thông báo **Install recommended extensions** → bấm **Install** (Docker, PHP Intelephense, Laravel Blade, YAML, GitLens).

## Bước 1 — Mở dự án và tạo repository GitHub

1. VS Code → **File → Open Folder** → chọn `D:\DTC245200583_StudentManagement`.
2. Đặt thông tin Git của bạn (đã có sẵn tài khoản khác trên máy nên cần đặt riêng cho dự án này):
   ```powershell
   git config user.name "Nguyen Thi Que"
   git config user.email "<email GitHub của bạn>"
   ```
3. Trên https://github.com/new tạo repository tên **DTC245200583_StudentManagement** (Public, **không** tick README/.gitignore).
4. Liên kết repository:
   ```powershell
   git remote add origin https://github.com/<tai-khoan>/DTC245200583_StudentManagement.git
   git branch -M main
   ```
5. Kiểm tra file bí mật **không** bị đưa lên GitHub:
   ```powershell
   git status
   git check-ignore -v .env secrets/mysql_root_password.txt nginx/certs/server.key
   ```
   Lệnh thứ 2 phải in ra cả 3 file → đã bị bỏ qua.

📸 Minh chứng: ảnh trang repository và (sau này) tab **Commits**.

## Bước 2 + 3 — Website CRUD và Docker Compose

> File `.env`, mật khẩu, chứng chỉ HTTPS đã được sinh sẵn. Trên máy mới (sau khi clone) chạy tác vụ **1. Setup lần đầu**, hoặc lệnh `powershell -ExecutionPolicy Bypass -File scripts\setup.ps1`.

1. Build và khởi chạy toàn bộ hệ thống (lần đầu mất 5–15 phút để tải image):
   ```powershell
   docker compose up -d --build
   ```
2. Xem trạng thái, đợi đến khi `app`, `mysql`, `nginx` là **healthy**:
   ```powershell
   docker compose ps
   ```
3. Tạo dữ liệu mẫu (4 lớp, 32 sinh viên, điểm 4 môn):
   ```powershell
   docker compose exec app php artisan db:seed --force
   ```
4. Mở https://localhost → trình duyệt cảnh báo chứng chỉ → **Advanced → Proceed to localhost**.
5. Thử đủ các chức năng: thêm/sửa/xóa/tìm kiếm sinh viên; thêm/sửa lớp; nhập/sửa điểm; thử nhập sai (điểm 11, email sai, mã SV trùng) để thấy thông báo lỗi.
6. Mở http://localhost:8081 (phpMyAdmin), đăng nhập bằng `DB_USERNAME` / `DB_PASSWORD` trong file `.env` → xem 3 bảng `classes`, `students`, `grades`.

📸 Minh chứng: `docker compose ps`; 4 màn hình (danh sách SV, form thêm/sửa, lớp học, điểm); thông báo lỗi validation; dữ liệu trong phpMyAdmin.

## Bước 4 — Nginx reverse proxy + HTTPS

1. Kiểm tra chuyển hướng HTTP → HTTPS và security headers:
   ```powershell
   curl.exe -sI http://localhost/
   curl.exe -skI https://localhost/students
   ```
   Kết quả phải có `301` + `Location: https://...`, và các header `Strict-Transport-Security`, `Content-Security-Policy`, `X-Content-Type-Options`, `X-Frame-Options`, `Referrer-Policy`.
2. Trên trình duyệt: F12 → tab **Network** → chọn request → **Headers** → chụp phần Response Headers. Bấm biểu tượng ổ khóa trên thanh địa chỉ để xem chứng chỉ.

📸 Minh chứng: kết quả 2 lệnh curl; Response Headers trong DevTools; thông tin chứng chỉ.

### ✅ Commit 1
```powershell
git add .
git commit -m "feat: implement student CRUD and nginx reverse proxy"
git push -u origin main
```

> Nếu muốn 3 commit tách bạch đúng giai đoạn: ở commit 1 chỉ `git add` phần ứng dụng (`app bootstrap config database lang public resources routes composer.json artisan Dockerfile docker docker-compose.yml nginx mysql .gitignore .gitattributes .dockerignore .env.example scripts/setup.ps1 secrets/.gitkeep`), các commit sau thêm phần còn lại.

## Bước 5 — Prometheus + Grafana

1. Mở http://localhost:9090/targets → tất cả target (`prometheus`, `cadvisor`, `nginx`, `mysql`, `loki`) phải **UP**.
2. Mở http://localhost:3000 → đăng nhập bằng `GRAFANA_ADMIN_USER` / `GRAFANA_ADMIN_PASSWORD` trong `.env`.
3. **Connections → Data sources**: đã có sẵn Prometheus và Loki (provisioning tự động).
4. **Dashboards → Student Management → Tổng quan hệ thống**.
5. Tạo lưu lượng để biểu đồ có số liệu: thao tác nhiều trên website, hoặc chạy:
   ```powershell
   1..200 | ForEach-Object { curl.exe -sk -o NUL https://localhost/students?page=$($_ % 4 + 1) }
   ```

📸 Minh chứng: trang Prometheus targets; danh sách data source; dashboard có số liệu thực.

### ✅ Commit 2
```powershell
git add monitoring docker-compose.yml
git commit -m "feat: add prometheus exporters and grafana dashboards"
git push
```

## Bước 6 — Loki + Promtail

1. Grafana → **Explore** → chọn datasource **Loki**.
2. Chạy từng truy vấn (đổi sang chế độ **Code** để dán):
   ```logql
   {service="nginx"} | json | status >= 400
   sum by (service) (count_over_time({project="sms"}[5m]))
   {service="app"} |~ "student\\.|class\\.|grade\\."
   topk(5, sum by (uri) (count_over_time({service="nginx"} | json | __error__="" [15m])))
   ```
   - Truy vấn 1: mở https://localhost/khong-ton-tai vài lần để có lỗi 404.
   - Truy vấn 3: thêm/sửa/xóa 1 sinh viên trước khi chạy.

📸 Minh chứng: mỗi truy vấn 1 ảnh có kết quả.

## Bước 7 — Hardening và kiểm thử

```powershell
powershell -ExecutionPolicy Bypass -File scripts\check-security.ps1
powershell -ExecutionPolicy Bypass -File scripts\backup-db.ps1
```

Script kiểm tra in ra: security headers, chuyển hướng HTTPS, user trong container (`app` phải là `uid=82(www-data)`, `nginx` là `uid=101`), cổng publish, mạng internal, quyền tài khoản DB và trạng thái healthcheck.

📸 Minh chứng: chụp từng phần kết quả; nội dung `docker-compose.yml` (networks, secrets, cap_drop); file backup trong `backups\`.

### ✅ Commit 3
```powershell
git add .
git commit -m "feat: add loki logging and container hardening"
git push
```

## Bước 8 — Hoàn thiện báo cáo và demo

1. Lưu ảnh vào `screenshots\` (đặt tên theo bước, ví dụ `05-grafana-dashboard.png`), cập nhật lớp/môn học trong `README.md`.
2. Thử lại trên môi trường sạch:
   ```powershell
   docker compose down -v
   cd D:\
   git clone https://github.com/<tai-khoan>/DTC245200583_StudentManagement.git test-clone
   cd test-clone
   powershell -ExecutionPolicy Bypass -File scripts\setup.ps1
   docker compose up -d --build
   ```
3. Viết báo cáo ≥ 10 trang theo bố cục đề bài, lưu `docs\bao-cao-du-an.docx`, rồi commit và push.

---

## Xử lý lỗi thường gặp

| Hiện tượng | Cách xử lý |
|---|---|
| `docker` không nhận lệnh | Mở Docker Desktop, đợi **Engine running**, mở lại VS Code |
| `Bind for 0.0.0.0:80 failed: port is already allocated` | Cổng 80/443 đang bị IIS/Skype/XAMPP dùng. Tắt chương trình đó, hoặc đổi trong `docker-compose.yml` thành `"8080:8080"` và `"8443:8443"` rồi truy cập https://localhost:8443 |
| `app` không healthy, website báo 502 | `docker compose logs app` để xem lỗi (thường là sai mật khẩu DB) |
| Sửa mật khẩu trong `.env` nhưng MySQL vẫn báo sai | MySQL chỉ đọc mật khẩu khi tạo volume lần đầu: `docker compose down -v` rồi `up -d --build` lại (mất dữ liệu, cần seed lại) |
| Sửa code PHP/Blade nhưng web không đổi | Image cần build lại: `docker compose up -d --build app` |
| Dashboard cAdvisor trống | Đợi 1–2 phút; kiểm tra target `cadvisor` trong Prometheus là UP |
| Explore Loki không có log | `docker compose logs promtail`; đảm bảo Docker Desktop đang chạy WSL 2 |
