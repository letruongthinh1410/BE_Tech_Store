<?php
require_once __DIR__ . '/../config/config.php';
// Nếu bạn cài PHPMailer qua Composer:
require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;



// Nếu không dùng Composer, download PHPMailer và require thủ công:
// require_once __DIR__ . '/../libs/PHPMailer/src/Exception.php';
// require_once __DIR__ . '/../libs/PHPMailer/src/PHPMailer.php';
// require_once __DIR__ . '/../libs/PHPMailer/src/SMTP.php';

class EmailService {
    
    /**
     * Gửi email verification
     */
    public function sendVerificationEmail($email, $verificationToken) {
        $verificationUrl = BASE_URL . "/api/verify-email.php?token=" . $verificationToken;
        
        $subject = "Xác thực tài khoản TechStore";
        $body = "
            <h2>Chào mừng đến với TechStore!</h2>
            <p>Vui lòng click vào link bên dưới để xác thực email của bạn:</p>
            <p><a href='{$verificationUrl}' style='padding: 10px 20px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px;'>Xác thực Email</a></p>
            <p>Hoặc copy link sau vào trình duyệt:</p>
            <p>{$verificationUrl}</p>
            <p>Link này sẽ hết hạn sau 24 giờ.</p>
            <br>
            <p>Nếu bạn không đăng ký tài khoản này, vui lòng bỏ qua email này.</p>
        ";
        
        return $this->sendEmail($email, $subject, $body);
    }

    /**
     * Gửi email chung
     */
    private function sendEmail($to, $subject, $body) {
        try {
            $mail = new PHPMailer(true);
            
            // Server settings
            $mail->isSMTP();
            $mail->Host       = SMTP_HOST;
            $mail->SMTPAuth   = true;
            $mail->Username   = SMTP_USERNAME;
            $mail->Password   = SMTP_PASSWORD;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = SMTP_PORT;
            $mail->CharSet    = 'UTF-8';

            // Recipients
            $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
            $mail->addAddress($to);

            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $body;
            $mail->AltBody = strip_tags($body);

            $mail->send();
            return true;
            
        } catch (Exception $e) {
            error_log("Email send error: " . $mail->ErrorInfo);
            return false;
        }
    }

    /**
     * Gửi email reset password (tính năng mở rộng)
     */
    public function sendPasswordResetEmail($email, $resetToken) {
        $resetUrl = BASE_URL . "/reset-password.php?token=" . $resetToken;
        
        $subject = "Đặt lại mật khẩu TechStore";
        $body = "
            <h2>Yêu cầu đặt lại mật khẩu</h2>
            <p>Bạn đã yêu cầu đặt lại mật khẩu. Click vào link bên dưới để tiếp tục:</p>
            <p><a href='{$resetUrl}' style='padding: 10px 20px; background-color: #dc3545; color: white; text-decoration: none; border-radius: 5px;'>Đặt lại mật khẩu</a></p>
            <p>Hoặc copy link sau vào trình duyệt:</p>
            <p>{$resetUrl}</p>
            <p>Link này sẽ hết hạn sau 1 giờ.</p>
            <br>
            <p>Nếu bạn không yêu cầu đặt lại mật khẩu, vui lòng bỏ qua email này.</p>
        ";
        
        return $this->sendEmail($email, $subject, $body);
    }

    /**
     * Gửi email welcome sau khi verify
     */
    public function sendWelcomeEmail($email, $fullName) {
        $subject = "Chào mừng đến với TechStore!";
        $body = "
            <h2>Chào {$fullName}!</h2>
            <p>Tài khoản của bạn đã được xác thực thành công.</p>
            <p>Bạn có thể bắt đầu mua sắm tại TechStore ngay bây giờ!</p>
            <p><a href='" . BASE_URL . "' style='padding: 10px 20px; background-color: #28a745; color: white; text-decoration: none; border-radius: 5px;'>Khám phá ngay</a></p>
            <br>
            <p>Cảm ơn bạn đã tin tưởng TechStore!</p>
        ";
        
        return $this->sendEmail($email, $subject, $body);
    }
}
?>