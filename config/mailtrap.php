<?php
/**
 * Mailtrap Configuration
 * 
 * Để lấy SMTP credentials:
 * 1. Đăng nhập vào https://mailtrap.io
 * 2. Vào Email Testing → Inboxes → My Inbox
 * 3. Tab SMTP Settings → Show Credentials
 * 4. Copy Username và Password
 */

return [
    // SMTP Settings từ Mailtrap
    'smtp' => [
        'host' => 'sandbox.smtp.mailtrap.io',  // hoặc 'live.smtp.mailtrap.io' cho production
        'port' => 2525,                         // hoặc 465 (SSL), 587 (TLS)
        'username' => 'YOUR_MAILTRAP_USERNAME', // Từ Mailtrap SMTP Settings
        'password' => 'YOUR_MAILTRAP_PASSWORD', // Từ Mailtrap SMTP Settings
        'encryption' => 'tls'                   // 'tls' hoặc 'ssl'
    ],
    
    // Email người gửi
    'from_email' => 'noreply@shoestore.com',
    'from_name' => 'Shoe Store',
    
    // Email nhận reply (tùy chọn)
    'reply_to' => 'support@shoestore.com',
    
    // Base URL cho reset password link
    'reset_password_url' => 'http://localhost/fe/pages/home/reset-password.html',
    
    // Thời gian hết hạn token (giây) - 1 giờ
    'token_expiry' => 3600
];
