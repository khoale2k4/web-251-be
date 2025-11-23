<?php
require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailService
{
    private $config;

    public function __construct()
    {
        $this->config = require __DIR__ . '/../config/mailtrap.php';
    }

    /**
     * Gửi email reset password
     * 
     * @param string $toEmail Email người nhận
     * @param string $toName Tên người nhận
     * @param string $resetToken Token reset password
     * @return bool
     */
    public function sendPasswordResetEmail($toEmail, $toName, $resetToken)
    {
        try {
            // Tạo PHPMailer instance
            $mail = new PHPMailer(true);

            // SMTP Configuration
            $mail->isSMTP();
            $mail->Host = $this->config['smtp']['host'];
            $mail->SMTPAuth = true;
            $mail->Username = $this->config['smtp']['username'];
            $mail->Password = $this->config['smtp']['password'];
            $mail->SMTPSecure = $this->config['smtp']['encryption'];
            $mail->Port = $this->config['smtp']['port'];
            $mail->CharSet = 'UTF-8';

            // Recipients
            $mail->setFrom($this->config['from_email'], $this->config['from_name']);
            $mail->addAddress($toEmail, $toName);
            $mail->addReplyTo($this->config['reply_to'], $this->config['from_name']);

            // Tạo reset link
            $resetLink = $this->config['reset_password_url'] . '?token=' . $resetToken;

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Đặt lại mật khẩu - Shoe Store';
            $mail->Body = $this->getPasswordResetTemplate($toName, $resetLink);
            $mail->AltBody = "Xin chào $toName,\n\n"
                . "Chúng tôi nhận được yêu cầu đặt lại mật khẩu cho tài khoản của bạn.\n\n"
                . "Vui lòng click vào link sau để đặt lại mật khẩu:\n"
                . "$resetLink\n\n"
                . "Link này sẽ hết hạn sau 1 giờ.\n\n"
                . "Nếu bạn không yêu cầu đặt lại mật khẩu, vui lòng bỏ qua email này.\n\n"
                . "Trân trọng,\n"
                . "Shoe Store Team";

            // Gửi email
            $mail->send();
            return true;

        } catch (Exception $e) {
            error_log("Email sending failed: " . $e->getMessage());
            throw new \Exception("Không thể gửi email. Vui lòng thử lại sau.");
        }
    }

    /**
     * Template HTML cho email reset password
     */
    private function getPasswordResetTemplate($name, $resetLink)
    {
        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .container {
            background-color: #f9f9f9;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #2563eb;
            margin: 0;
        }
        .content {
            background-color: white;
            padding: 25px;
            border-radius: 8px;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background-color: #2563eb;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            font-weight: bold;
        }
        .button:hover {
            background-color: #1d4ed8;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            color: #666;
            font-size: 14px;
        }
        .warning {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔐 Shoe Store</h1>
        </div>
        
        <div class="content">
            <h2>Xin chào {$name},</h2>
            
            <p>Chúng tôi nhận được yêu cầu đặt lại mật khẩu cho tài khoản của bạn.</p>
            
            <p>Vui lòng click vào nút bên dưới để đặt lại mật khẩu:</p>
            
            <div style="text-align: center;">
                <a href="{$resetLink}" class="button">Đặt lại mật khẩu</a>
            </div>
            
            <p style="font-size: 14px; color: #666;">
                Hoặc copy link sau vào trình duyệt:<br>
                <a href="{$resetLink}">{$resetLink}</a>
            </p>
            
            <div class="warning">
                <strong>⚠️ Lưu ý:</strong>
                <ul style="margin: 10px 0 0 0; padding-left: 20px;">
                    <li>Link này chỉ có hiệu lực trong <strong>1 giờ</strong></li>
                    <li>Chỉ sử dụng được <strong>1 lần</strong></li>
                    <li>Nếu bạn không yêu cầu, vui lòng bỏ qua email này</li>
                </ul>
            </div>
        </div>
        
        <div class="footer">
            <p>Email này được gửi tự động, vui lòng không reply.</p>
            <p>© 2024 Shoe Store. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
HTML;
    }
}
