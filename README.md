# Hướng dẫn Deploy Dự án

## Yêu cầu
- PHP >= 8.x
- MySQL/MariaDB (nếu dự án có sử dụng database)

## Cách chạy dự án

1. **Clone hoặc copy dự án về máy**
   ```
   git clone <repository-url>
   cd <ten-thu-muc-du-an>
   ```
2. **Chạy server PHP built-in**
    ```
    php -S localhost:8000 -t .
    ```

    Sau khi chạy, mở trình duyệt và truy cập:
    ```
    http://localhost:8000
    ```

3. **Cấu hình database**

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
.
├── config/
│   └── database.php
├── public/ (nếu có)
├── src/ hoặc app/
├── index.php
└── README.md
```