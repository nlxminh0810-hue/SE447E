# Hướng dẫn cài đặt và chạy ENGLISHAI với XAMPP

Tài liệu này hướng dẫn từ sau khi cài XAMPP đến khi mở được ENGLISHAI trên máy tính Windows. Dự án dùng PHP, MySQL/MariaDB và `mysqli`; không cần Composer.

## 1. Kiểm tra XAMPP

1. Mở **XAMPP Control Panel**. Nếu XAMPP được cài ở thư mục mặc định, PHP nằm tại `C:\xampp\php\php.exe` và phpMyAdmin được phục vụ qua Apache.
2. Nhấn **Start** cho **Apache** và **MySQL**. Cả hai cần hiện trạng thái đang chạy.
3. Mở `http://localhost/phpmyadmin/`. Nếu trang phpMyAdmin xuất hiện là Apache và MySQL đã sẵn sàng.
4. Nếu Apache không khởi động do cổng 80/443 bị chiếm, đóng dịch vụ đang dùng cổng đó hoặc cấu hình Apache dùng cổng khác. Khi đó URL ứng dụng cần kèm cổng, ví dụ `http://localhost:8080/`.

## 2. Đặt dự án vào thư mục web

1. Sao chép toàn bộ thư mục dự án vào thư mục `htdocs` của XAMPP. Với cài đặt mặc định, kết quả cần là:

   `C:\xampp\htdocs\ENGLISHAI\index.php`

   Nếu dự án đang ở nơi khác (ví dụ `E:\ENGLISHAI`), hãy sao chép nguyên thư mục `ENGLISHAI` vào `C:\xampp\htdocs\`. Không chỉ chép riêng file `index.php`; cần giữ các thư mục `Controller`, `Core`, `Model`, `View`, `bootstrap`, `public` cùng các file SQL/cấu hình.
2. Có thể đổi tên thư mục, nhưng khi đó thay `ENGLISHAI` trong các URL dưới đây bằng tên mới.

## 3. Tạo database và nhập dữ liệu

1. Trong phpMyAdmin, chọn **New / Mới** ở thanh bên.
2. Tạo database tên chính xác `englishai`, chọn collation `utf8mb4_unicode_ci` nếu giao diện yêu cầu, rồi nhấn **Create / Tạo**.
3. Chọn database `englishai` vừa tạo. Mở tab **Import / Nhập**.
4. Nhấn **Choose File / Chọn tệp**, chọn `englishai.sql` trong thư mục dự án (ví dụ `C:\xampp\htdocs\ENGLISHAI\englishai.sql`). Để định dạng là **SQL**, kéo xuống cuối trang và nhấn **Import / Nhập** hoặc **Go / Thực hiện**.
5. Chờ thông báo nhập thành công. Chọn `englishai` ở thanh bên: cần thấy các bảng như `users`, `courses`, `lessons`, `tests`, `questions` và `answers`. Tệp SQL tự tạo/chọn database `englishai`, nhưng tạo database trước giúp dễ kiểm tra kết quả trong phpMyAdmin.

> Nếu phpMyAdmin báo database đã tồn tại, không nhập đè `englishai.sql` vào DB đang sử dụng. Chạy bước 5 để nâng cấp cột video và cập nhật bài học theo cách giữ lại dữ liệu hiện có.

### Xem dữ liệu đã nhập trong phpMyAdmin

Ảnh minh họa đang ở đúng trang **Cấu trúc** của database `englishai`. Cột **Hàng** cho biết số bản ghi ước tính trong từng bảng; ví dụ `courses` là khóa học, `lessons` là bài học, `users` là tài khoản, `questions` là câu hỏi.

- **Xem nội dung một bảng:** bấm tên bảng ở danh sách bên trái, hoặc bấm **Duyệt** trên cùng hàng với bảng ở trang Cấu trúc. phpMyAdmin sẽ hiện các dòng dữ liệu. Nếu bảng có nhiều dòng, dùng các nút trang/giới hạn số dòng để xem tiếp.
- **Xem lần lượt tất cả bảng:** lần lượt bấm **Duyệt** trên từng hàng (hoặc mở tên bảng ở cây bên trái rồi chọn tab **Duyệt**). Trang Cấu trúc không có một nút để gộp nội dung mọi bảng vào chung một lưới; mỗi bảng có cột và kiểu dữ liệu khác nhau.
- **Kiểm tra bảng nào có dữ liệu:** nhìn cột **Hàng** ở trang Cấu trúc. Số `0` nghĩa là hiện chưa có bản ghi trong bảng đó; số lớn hơn 0 nghĩa là có dữ liệu.
- **Xem tất cả bản ghi bằng SQL:** chọn database `englishai`, mở tab **SQL**, chạy truy vấn riêng cho từng bảng, ví dụ:

  ```sql
  SELECT * FROM courses;
  SELECT * FROM lessons;
  SELECT * FROM users;
  ```

  Bấm **Thực hiện**. Thay tên bảng để xem dữ liệu khác. Không dùng lệnh `DELETE`, `DROP` hoặc `TRUNCATE` nếu chỉ muốn xem dữ liệu.

Trong ảnh bạn gửi, có dữ liệu ở `answers`, `chat_messages`, `chat_sessions`, `courses`, `course_enrollments`, `lessons`, `questions`, `roles`, `service_packages`, `tests`, `test_results`, `users`, `user_roles` và `vocabulary`. Các bảng có số `0` như `lesson_progress`, `payments`, `transactions`, `user_profiles` hiện chưa có bản ghi.
## 4. Cấu hình kết nối MySQL

Mặc định dự án kết nối bằng các giá trị trong `Model/DBConfig.php`:

- Host: `localhost`
- Username: `root`
- Password: để trống
- Database: `englishai`

Đây là thiết lập thường dùng trong XAMPP cài mới. Nếu bạn đã đặt mật khẩu cho MySQL root hoặc dùng cổng/tài khoản khác, cập nhật `$hostname`, `$username`, `$pass` và `$dbname` trong `Model/DBConfig.php` cho khớp. Không đổi tên database trừ khi cũng sửa `$dbname`.

## 5. (Tùy chọn) Nạp nội dung bài học cho toàn bộ khóa

`englishai.sql` tạo cấu trúc bảng và dữ liệu ban đầu. Để cập nhật database cũ an toàn, thêm `ENABLE_DATA_SEED=true` vào `.env`, chạy `http://localhost/ENGLISHAI/public/seed_learning_data.php`, đợi thông báo `Done`, rồi xóa dòng bật seed khỏi `.env`. Script bổ sung cột video nếu thiếu và cập nhật bài học theo tên khóa, giữ URL riêng đã lưu, đồng thời xóa URL video mẫu bị dùng chung; có thể chạy lại.

Để hiện video giới thiệu cho từng khóa, điền URL YouTube/Vimeo/MP4 trong **Quản trị → Quản lý khóa học → Sửa**. Có thể dùng biến môi trường thay thế: `ENGLISHAI_COURSE_VIDEO_TOEIC`, `ENGLISHAI_COURSE_VIDEO_IELTS`, `ENGLISHAI_COURSE_VIDEO_B1`, `ENGLISHAI_COURSE_VIDEO_B2`, `ENGLISHAI_COURSE_VIDEO_READING`, `ENGLISHAI_COURSE_VIDEO_LISTENING`, `ENGLISHAI_COURSE_VIDEO_WRITING`, `ENGLISHAI_COURSE_VIDEO_SPEAKING`. Mỗi biến nhận URL video riêng của khóa đó; không dùng URL mẫu chung.

## 6. Chạy ứng dụng trên web

Có hai cách. Nếu bạn đang để mã nguồn tại `E:\ENGLISHAI` như trong IDE, dùng **Cách A**. Nếu đã chép dự án vào `C:\xampp\htdocs\ENGLISHAI`, dùng **Cách B**.

### Cách A — chạy trực tiếp từ thư mục E:\ENGLISHAI

1. Mở **PowerShell** hoặc Terminal trong VS Code.
2. Chạy lệnh sau (đổi `C:\xampp` nếu bạn cài XAMPP ở thư mục khác):

   ```powershell
   cd E:\ENGLISHAI
   & "C:\xampp\php\php.exe" -S 127.0.0.1:8080 -t "E:\ENGLISHAI"
   ```

3. Để cửa sổ terminal mở trong lúc sử dụng. Khi thấy `PHP Development Server ... started`, mở trình duyệt tại:

   `http://127.0.0.1:8080/?controller=course`

4. Dừng server bằng **Ctrl+C** trong cửa sổ terminal.

Nếu PowerShell báo không tìm thấy `php.exe`, kiểm tra vị trí XAMPP và sửa đường dẫn trong lệnh. Nếu cổng `8080` đang bận, đổi `8080` thành `8081` trong lệnh và dùng `http://127.0.0.1:8081/?controller=course`.

### Cách B — chạy bằng Apache của XAMPP

1. Đặt mã nguồn tại `C:\xampp\htdocs\ENGLISHAI`.
2. Trong XAMPP Control Panel, nhấn **Start** cho **Apache** và **MySQL**.
3. Mở trang khóa học:

   `http://localhost/ENGLISHAI/?controller=course`

Không cần chạy lệnh PHP server riêng trong cách này. Nếu thư mục dự án có tên khác, thay `ENGLISHAI` trong URL bằng tên thư mục đó.

### Các trang chính

Thay tiền tố URL theo cách chạy ở trên: `http://127.0.0.1:8080/` cho Cách A hoặc `http://localhost/ENGLISHAI/` cho Cách B.

- Khóa học: `?controller=course`
- Danh sách khóa học: `?controller=course&action=list`
- Đăng ký: `?controller=auth&action=register`
- Đăng nhập: `?controller=auth&action=login`
- Chat AI: `?controller=chat`
- Quản trị: `?controller=admin`

Tài khoản quản trị mẫu:

- Email: `admin@englishai.com`
- Mật khẩu: `admin123`

Hãy đổi mật khẩu mẫu trước khi đưa ứng dụng lên mạng hoặc dùng với dữ liệu thật. Người học có thể tạo tài khoản qua trang đăng ký.
## 7. Bật Chat AI (tùy chọn)

Các trang khóa học, tài khoản và bài học không cần API key. Muốn dùng Chat AI:

1. Mở `.env` tại thư mục gốc dự án. Nếu chưa có, sao chép `.env.example` thành `.env`.
2. Điền API key cho nhà cung cấp đang chọn, ví dụ `AI_PROVIDER=gemini` và `GEMINI_API_KEY=...`; hoặc chọn nhà cung cấp được cấu hình trong mã và điền key tương ứng.
3. Đảm bảo PHP extension `curl` đã bật trong `C:\xampp\php\php.ini` (bỏ dấu `;` trước `extension=curl` nếu có), rồi khởi động lại Apache.
4. Không đăng key lên GitHub hoặc chia sẻ file `.env`.

## 8. Khắc phục lỗi thường gặp

- **Không mở được `localhost`**: kiểm tra Apache đang chạy; nếu Apache dùng cổng khác, thêm cổng đó vào URL.
- **Lỗi kết nối database / `Connection refused`**: kiểm tra MySQL đang chạy và thông tin trong `Model/DBConfig.php` khớp với MySQL.
- **Unknown database `englishai`**: tạo database và nhập `englishai.sql` theo mục 3.
- **Lỗi `Access denied for user 'root'`**: cập nhật mật khẩu MySQL trong `Model/DBConfig.php`.
- **Trang báo thiếu bảng hoặc dữ liệu**: chọn đúng database `englishai`, xem danh sách bảng trong phpMyAdmin và nhập lại file SQL vào database đó.
- **Trang trắng/lỗi PHP**: kiểm tra URL có `/ENGLISHAI/` đúng tên thư mục; xem lỗi tại `C:\xampp\apache\logs\error.log`.
- **Lỗi import tệp quá lớn**: file SQL hiện tại thông thường nhập trực tiếp được. Nếu phpMyAdmin báo vượt giới hạn, tăng `upload_max_filesize` và `post_max_size` trong `C:\xampp\php\php.ini`, khởi động lại Apache rồi thử lại.

## 9. Dừng ứng dụng

Khi dùng Apache của XAMPP, không cần chạy lệnh PHP server riêng. Đóng tab trình duyệt khi xong; trong XAMPP Control Panel có thể nhấn **Stop** cho Apache và MySQL nếu không còn dùng chúng.


