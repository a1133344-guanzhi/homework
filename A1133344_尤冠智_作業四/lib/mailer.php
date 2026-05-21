<?php
// 直接引入同資料夾內 PHPMailer 的實體檔案 (相對路徑)
require_once __DIR__ . '/PHPMailer/Exception.php';
require_once __DIR__ . '/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * 建立並設定 PHPMailer 物件的共用函式
 * @param array $config 包含 SMTP 帳密的陣列
 * @return PHPMailer
 */
function build_mailer(array $config): PHPMailer
{
    $mail = new PHPMailer(true);

    // SMTP 伺服器驗證設定
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';                        
    $mail->SMTPAuth   = true;                                    
    $mail->Username   = $config['username']; // 主程式傳入的 Gmail 帳號
    $mail->Password   = $config['password']; // 主程式傳入的 Google 應用程式密碼
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;          
    $mail->Port       = 587;                                     
    $mail->CharSet    = 'UTF-8';                                 

    // 預設發件人名稱
    $mail->setFrom($config['username'], '垃圾郵件發送系統'); 

    return $mail;
}
