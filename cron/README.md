# Post Scheduler - Hướng dẫn sử dụng

## Giới thiệu
Hệ thống tự động cập nhật trạng thái bài viết từ "scheduled" sang "published" khi đến giờ xuất bản.

## Các cách sử dụng

### 1. ✅ Auto-Scheduler (Đã tích hợp - Khuyến nghị)
**Đã được tích hợp tự động vào Admin Panel**

Auto-scheduler sẽ tự động chạy khi:
- Bất kỳ admin nào đang truy cập Admin Panel
- Kiểm tra mỗi 5 phút
- Không cần cấu hình gì thêm

**Lưu ý:** Chỉ hoạt động khi có người đang truy cập Admin Panel.

### 2. Chạy thủ công qua Command Line
```bash
# Chạy script PHP
php update_scheduled_posts.php
```

### 3. Gọi qua HTTP Request
```bash
# Sử dụng curl
curl http://localhost:8000/scheduler/update-scheduled

# Hoặc truy cập trực tiếp từ trình duyệt
http://localhost:8000/scheduler/update-scheduled
```

### 4. Cron Job (Linux/Mac)
Thêm vào crontab để chạy tự động mỗi phút:
```bash
# Mở crontab editor
crontab -e

# Thêm dòng sau (thay đổi đường dẫn cho phù hợp)
* * * * * php /path/to/btl-251-web/web-251-be/cron/update_scheduled_posts.php
```

### 5. Windows Task Scheduler
1. Mở **Task Scheduler**
2. Tạo **Basic Task**
3. Trigger: **Daily**, chạy mỗi **5 phút**
4. Action: **Start a program**
   - Program: `php.exe` (đường dẫn XAMPP: `C:\xampp\php\php.exe`)
   - Arguments: `C:\xampp\htdocs\btl-251-web\web-251-be\cron\update_scheduled_posts.php`

## API Endpoints

### Update Scheduled Posts
**Endpoint:** `GET /scheduler/update-scheduled`

**Response:**
```json
{
  "success": true,
  "message": "Đã cập nhật 2 bài viết",
  "updated": 2,
  "posts": [
    {
      "id": 1,
      "title": "Bài viết 1",
      "published_at": "2025-01-15 10:00:00"
    }
  ]
}
```

### Get Upcoming Posts
**Endpoint:** `GET /scheduler/upcoming?hours=24`

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 5,
      "title": "Bài viết sắp xuất bản",
      "status": "scheduled",
      "published_at": "2025-01-15 14:00:00",
      "author_id": 1
    }
  ]
}
```

## Kiểm tra Auto-Scheduler

### Trong Browser Console
```javascript
// Kiểm tra trạng thái
window.postScheduler.getStatus()

// Chạy ngay lập tức
window.postScheduler.updateScheduledPosts()

// Dừng scheduler
window.postScheduler.stop()

// Khởi động lại
window.postScheduler.start()
```

## Cấu hình

File cấu hình: `web-251-fe/admin/utils/auto-scheduler.js`

```javascript
const CONFIG = {
    // Khoảng thời gian kiểm tra (5 phút)
    checkInterval: 5 * 60 * 1000,
    
    // Thời gian chờ sau khi trang load (30 giây)
    initialDelay: 30 * 1000,
    
    // Hiển thị log trong console
    enableLogging: true
};
```

## Lưu ý quan trọng

1. **Auto-Scheduler chỉ hoạt động khi có admin đang truy cập trang quản trị**
   - Ưu điểm: Không cần cấu hình server
   - Nhược điểm: Không hoạt động khi không có ai truy cập

2. **Để đảm bảo scheduler luôn chạy 24/7:**
   - Sử dụng Cron Job (Linux/Mac)
   - Hoặc Windows Task Scheduler
   - Hoặc một dịch vụ monitoring gọi HTTP endpoint định kỳ

3. **Timezone:** Đảm bảo timezone của database và server đồng bộ

## Troubleshooting

### Auto-scheduler không chạy?
1. Mở Browser Console (F12)
2. Kiểm tra log: `[PostScheduler]`
3. Kiểm tra lỗi CORS hoặc Network

### Backend không cập nhật?
```bash
# Kiểm tra trực tiếp
php update_scheduled_posts.php

# Hoặc
curl http://localhost:8000/scheduler/update-scheduled
```

### Kiểm tra bài viết scheduled
```sql
-- Xem các bài viết scheduled
SELECT id, title, status, published_at 
FROM posts 
WHERE status = 'scheduled' 
AND published_at IS NOT NULL;

-- Xem các bài viết đã quá giờ xuất bản nhưng chưa cập nhật
SELECT id, title, status, published_at 
FROM posts 
WHERE status = 'scheduled' 
AND published_at IS NOT NULL 
AND published_at <= NOW();
```

## Liên hệ
Nếu có vấn đề, kiểm tra log trong browser console hoặc chạy script thủ công để debug.
