# Hướng dẫn chạy ENGLISHAI trên Windows

Hướng dẫn này dùng cấu hình hiện tại:

- Mã nguồn: `E:\ENGLISHAI`
- XAMPP/PHP: `C:\xampp`
- Database: MySQL trong XAMPP

## 1. Khởi động MySQL

1. Mở **XAMPP Control Panel**.
2. Nhấn **Start** ở dòng **MySQL**.
3. Để nhập database qua phpMyAdmin, Apache cũng cần được khởi động.

## 2. Tạo và nhập database

1. Mở `http://localhost/phpmyadmin`.
2. Nếu database `englishai` chưa có, chọn **New**, tạo database tên `englishai` với collation `utf8mb4_unicode_ci`.
3. Chọn database `englishai` ở cột bên trái.
4. Chọn Import, chọn file `E:\ENGLISHAI\englishai.sql`, rồi nhấn **Import** hoặc **Go**.
5. Kiểm tra database có các bảng như `users`, `courses`, `lessons`.

Nếu phpMyAdmin báo `#1046 No database selected`, hãy bấm chọn database `englishai` ở cột trái trước khi mở tab Import và nhập file SQL.

## 3. Kiểm tra cấu hình

Mở `Model\DBConfig.php`. Cấu hình mặc định của dự án là:

- Host: `localhost`
- Username: `root`
- Password: để trống
- Database: `englishai`

Nếu MySQL trên máy dùng mật khẩu hoặc tài khoản khác, cập nhật các giá trị này cho khớp.

## 4. Chạy ứng dụng

Mở terminal PowerShell trên máy (terminal trong VS Code cũng được), rồi chạy:

```powershell
cd E:\ENGLISHAI
& "C:\xampp\php\php.exe" -S 127.0.0.1:8080 -t "E:\ENGLISHAI"
```

Sau lệnh thứ hai, terminal phải hiện thông báo có nội dung `Development Server started`. Để nguyên terminal đó mở khi sử dụng ứng dụng; không gõ lệnh khác vào cửa sổ này.

Trong trình duyệt trên cùng máy, mở chính xác địa chỉ giao diện chính này (không chỉ mở địa chỉ gốc):

`http://127.0.0.1:8080/?controller=course`

**Lưu ý:** `http://127.0.0.1:8080/` chỉ hiện lời chào mặc định. Cần thêm `?controller=course` để mở giao diện học tập.

Nếu terminal báo cổng `8080` đang được sử dụng, dừng server cũ bằng **Ctrl+C**. Hoặc chạy lại với cổng `8081`, rồi mở `http://127.0.0.1:8081/?controller=course`.

## 5. Các trang thường dùng

- Trang khóa học: `http://127.0.0.1:8080/?controller=course`
- Danh sách khóa học: `http://127.0.0.1:8080/?controller=course&action=list`
- Đăng ký: `http://127.0.0.1:8080/?controller=auth&action=register`
- Đăng nhập: `http://127.0.0.1:8080/?controller=auth&action=login`
- Chat AI: `http://127.0.0.1:8080/?controller=chat`
- Trang quản trị: `http://127.0.0.1:8080/?controller=admin`

Tài khoản quản trị mẫu sau khi nhập database:

- Tên đăng nhập: `admin`
- Mật khẩu: `admin123`

Bạn cũng có thể thử đăng nhập bằng email `admin@englishai.com`. Tài khoản học viên có thể tự tạo bằng trang **Đăng ký**.

## 6. Chatbot AI (tùy chọn)

Các chức năng khác có thể chạy mà không cần API key AI. Muốn dùng chatbot, sao chép `.env.example` thành `.env` tại `E:\ENGLISHAI`, rồi cấu hình API key của nhà cung cấp AI. PHP cần bật extension `curl`. Không chia sẻ hoặc đưa API key lên GitHub.

## 7. Dừng ứng dụng

Quay lại terminal đang chạy PHP và nhấn **Ctrl+C**. Lần sau muốn chạy lại, thực hiện lại các lệnh ở bước 4.
