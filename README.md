# Hướng dẫn Deploy Dự án

## Yêu cầu
- PHP >= 8.x
- MySQL

## Cách chạy dự án

1. **Clone hoặc copy dự án về máy**
   ```
   git clone https://github.com/khoale2k4/web-251-be.git
   cd web-251-be
   ```
2. **Chạy server PHP built-in**
    ```
    php -S localhost:8000 -t .
    ```

    Sau khi chạy, mở trình duyệt và truy cập:
    ```
    http://localhost:8000
    ```
    
    Nếu trả về kết quả sau là thành công:
   ```
   {
     "success": true
   }
   ```

4. **Cấu hình database**

    - Thông tin kết nối database được định nghĩa trong file:
    ```
    ./config/database.php
    ```

    - Kiểm tra và cập nhật các thông tin:
    ```
    host
    port
    username
    password
    database
    ```

## Cấu trúc thư mục
```
./web-251-be/
├── config/
│   └── database.php
├── public/ (nếu có)
├── src/ hoặc app/
├── index.php
└── README.md
```