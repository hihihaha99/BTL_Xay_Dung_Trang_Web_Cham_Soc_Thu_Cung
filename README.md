<h2 align="center">
    <a href="https://dainam.edu.vn/vi/khoa-cong-nghe-thong-tin">
    🎓 Faculty of Information Technology (DaiNam University)
    </a>
</h2>
<h2 align="center">
    Youth Union Member Management
</h2>
<div align="center">
    <p align="center">
        <img src="docs/logo/aiotlab_logo.png" alt="AIoTLab Logo" width="170"/>
        <img src="docs/logo/fitdnu_logo.png" alt="AIoTLab Logo" width="180"/>
        <img src="docs/logo/dnu_logo.png" alt="DaiNam University Logo" width="200"/>
    </p>

[![AIoTLab](https://img.shields.io/badge/AIoTLab-green?style=for-the-badge)](https://www.facebook.com/DNUAIoTLab)
[![Faculty of Information Technology](https://img.shields.io/badge/Faculty%20of%20Information%20Technology-blue?style=for-the-badge)](https://dainam.edu.vn/vi/khoa-cong-nghe-thong-tin)
[![DaiNam University](https://img.shields.io/badge/DaiNam%20University-orange?style=for-the-badge)](https://dainam.edu.vn)

</div>
 
## 📖 1. Giới thiệu
Hệ thống pet shop được xây dựng nhằm hỗ trợ quá trình kinh doanh và chăm sóc thú cưng một cách hiện đại và tiện lợi hơn. Thay vì quản lý sản phẩm, khách hàng và lịch dịch vụ bằng sổ sách hay các tệp excel rời rạc, hệ thống mang đến một nền tảng tập trung, dễ thao tác và dễ mở rộng.

## 🔧 2. Các công nghệ được sử dụng
<div align="center">

### Hệ điều hành
![macOS](https://img.shields.io/badge/macOS-000000?style=for-the-badge&logo=macos&logoColor=F0F0F0)
[![Windows](https://img.shields.io/badge/Windows-0078D6?style=for-the-badge&logo=windows&logoColor=white)](https://www.microsoft.com/en-us/windows/)
[![Ubuntu](https://img.shields.io/badge/Ubuntu-E95420?style=for-the-badge&logo=ubuntu&logoColor=white)](https://ubuntu.com/)

### Công nghệ chính
[![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://www.php.net/)
[![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white)](#)
[![CSS](https://img.shields.io/badge/CSS-1572B6?style=for-the-badge&logo=css3&logoColor=white)](#)
[![SCSS](https://img.shields.io/badge/SCSS-CC6699?style=for-the-badge&logo=sass&logoColor=white)](#)
[![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)](#)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-563D7C?style=for-the-badge&logo=bootstrap&logoColor=white)](https://getbootstrap.com/)

### Web Server & Database
[![Apache](https://img.shields.io/badge/Apache-D22128?style=for-the-badge&logo=apache&logoColor=white)](https://httpd.apache.org/)
[![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://www.mysql.com/) 
[![XAMPP](https://img.shields.io/badge/XAMPP-FB7A24?style=for-the-badge&logo=xampp&logoColor=white)](https://www.apachefriends.org/)

### Database Management Tools
[![MySQL Workbench](https://img.shields.io/badge/MySQL_Workbench-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://dev.mysql.com/downloads/workbench/)
</div>

## 🚀 3. Hình ảnh các chức năng

### 🌟 Trang đăng nhập
<img src="assets/screenshots/login.png" width="800">

### 🏠 Trang chủ
<img src="assets/screenshots/home.png" width="800">

### ✍️ Trang đăng ký
<img src="assets/screenshots/register.png" width="800">

### 📚 Trang Blog
<img src="assets/screenshots/blog.png" width="800">

### 📞 Trang liên hệ
<img src="assets/screenshots/contact.png" width="800">

## ⚙️ 4. Cài đặt

### 4.1. Cài đặt công cụ, môi trường và các thư viện cần thiết

- Tải và cài đặt **XAMPP**  
  👉 https://www.apachefriends.org/download.html  
  (Khuyến nghị bản XAMPP với PHP 8.x)

- Cài đặt **Visual Studio Code** và các extension:
  - PHP Intelephense  
  - MySQL  
  - Prettier – Code Formatter  
### 4.2. Tải project
Clone project về thư mục `htdocs` của XAMPP (ví dụ ổ C):

```bash
cd C:\xampp\htdocs
https://github.com/hihihaha99/BTL_Xay_Dung_Trang_Web_Cham_Soc_Thu_Cung
Truy cập project qua đường dẫn:
👉 http://localhost/authentication_login.
```
### 4.3. Setup database
Mở XAMPP Control Panel, Start Apache và MySQL

Truy cập MySQL WorkBench
Tạo database:
```bash
CREATE DATABASE IF NOT EXISTS pet_shop_db
   CHARACTER SET utf8mb4
   COLLATE utf8mb4_unicode_ci;
```

### 4.4. Setup tham số kết nối
Mở file config.php (hoặc .env) trong project, chỉnh thông tin DB:
```bash

<?php
   // includes/db.php
   // Update these values to match your MySQL server
   $DB_HOST = 'localhost';
   $DB_USER = 'root';
   $DB_PASS = '';
   $DB_NAME = 'pet_shop_db';

   $mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
   if ($mysqli->connect_errno) {
       http_response_code(500);
       die('Database connection failed: ' . $mysqli->connect_error);
   }
   $mysqli->set_charset('utf8mb4');
?>
```
### 4.5. Chạy hệ thống
Mở XAMPP Control Panel → Start Apache và MySQL

Truy cập hệ thống:
👉 http://localhost/index.php

### 4.6. Đăng nhập lần đầu
Hệ thống có thể cấp tài khoản admin 

Sau khi đăng nhập Admin có thể:

Tạo thông tin người dùng và thú cưng 

Thêm thông tin và cấp tài khoản

Quản lý phân quyền theo cấp

## Cài đặt nhanh (XAMPP / WAMP)
1. Tạo DB:
   - Mở phpMyAdmin → tab SQL → dán nội dung `schema.sql`.
2. Copy toàn bộ thư mục `petcare_php` vào `htdocs` (XAMPP) hoặc `www` (WAMP).
3. Mở `includes/db.php` và sửa user/pass/host nếu cần.
4. Đặt ảnh hero vào `assets/img/hero-dog.jpg` (tuỳ ý).
5. Truy cập `http://localhost/petcare_php/`

**Tài khoản demo**: `demo@petcare.local` / `123456`

## Cấu trúc
- index.php — trang chủ kiểu "Pet Care"
- login.php, register.php, logout.php — đăng nhập hiện đại (password_hash + prepared statements)
- schedule.php — đặt lịch (chỉ khi đã đăng nhập)
- dashboard.php — xem lịch hẹn của bạn
- includes/db.php — kết nối MySQL
- includes/auth.php — session + guard
- assets/css/style.css — giao diện
- schema.sql — lệnh tạo bảng + dữ liệu mẫu

## Bảo mật
- Mọi truy vấn SQL đều dùng `prepare/bind`.
- Mật khẩu lưu bằng `password_hash()` và kiểm tra `password_verify()`.
- Session được khởi tạo sớm trong `auth.php`.
