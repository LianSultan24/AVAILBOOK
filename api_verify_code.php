<?php
/**
 * Send Verification Code API
 * Sends 6-digit verification code to customer email
 */

require_once 'config.php';
require_once 'phpmailer/PHPMailer.php';
require_once 'phpmailer/SMTP.php';
require_once 'phpmailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

$conn = getDBConnection();
$method = $_SERVER['REQUEST_METHOD'];

if ($method !== 'POST') {
    jsonResponse(false, null, 'Only POST method is allowed');
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

// Validate email
if (empty($data['email'])) {
    jsonResponse(false, null, 'Email is required');
    exit;
}

$email = $conn->real_escape_string(trim(strtolower($data['email'])));

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    jsonResponse(false, null, 'Invalid email format');
    exit;
}

// Generate 6-digit code
$code = sprintf("%06d", mt_rand(100000, 999999));
$expiresAt = date('Y-m-d H:i:s', strtotime('+15 minutes'));

// Store or update verification code
$sql = "INSERT INTO verification_codes (email, code, expires_at, created_at) 
        VALUES ('$email', '$code', '$expiresAt', NOW())
        ON DUPLICATE KEY UPDATE 
        code = '$code', 
        expires_at = '$expiresAt', 
        created_at = NOW()";

if (!$conn->query($sql)) {
    jsonResponse(false, null, 'Failed to generate verification code: ' . $conn->error);
    exit;
}

// Send email using PHPMailer
$mail = new PHPMailer(true);

try {
    // SMTP Configuration
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'arlenepantallano5@gmail.com';        // ← Your Gmail
    $mail->Password = 'wbapsvjvvoigtxip';                    // ← Your Gmail App Password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;
    
    // Email settings
    $mail->setFrom('arlenepantallano5@gmail.com', 'ETOK Car AC Services');
    $mail->addAddress($email);
    
    // Email content
    $mail->isHTML(true);
    $mail->Subject = 'Your Verification Code - ETOK';
    $mail->Body = "
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
                          color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
                .code { font-size: 32px; font-weight: bold; color: #667eea; 
                        text-align: center; padding: 20px; background: white; 
                        border-radius: 8px; margin: 20px 0; letter-spacing: 8px; }
                .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #999; }
                .warning { color: #e74c3c; font-size: 14px; margin-top: 15px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🚗 ETOK Car AC Services</h1>
                    <p>Email Verification</p>
                </div>
                <div class='content'>
                    <p>Hello,</p>
                    <p>Your verification code is:</p>
                    <div class='code'>$code</div>
                    <p>Enter this code to verify your email address.</p>
                    <p class='warning'>⚠️ This code will expire in 15 minutes.</p>
                    <p>If you didn't request this code, please ignore this email.</p>
                </div>
                <div class='footer'>
                    <p>© 2026 ETOK Car AC Services. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>
    ";
    
    $mail->AltBody = "Your ETOK verification code is: $code\n\nThis code will expire in 15 minutes.";
    
    // Send email
    $mail->send();
    
    jsonResponse(true, [
        'email' => $email,
        'expires_in' => '15 minutes'
    ], 'Verification code sent to your email');
    
} catch (Exception $e) {
    jsonResponse(false, null, 'Failed to send email: ' . $mail->ErrorInfo);
}

$conn->close();
?>