<?php
/**
 * Reset Password API
 * Resets customer password after email verification
 */

require_once 'config.php';

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

// Validate required fields
if (empty($data['email']) || empty($data['verification_code']) || empty($data['new_password'])) {
    jsonResponse(false, null, 'Email, verification code, and new password are required');
    exit;
}

$email = $conn->real_escape_string(trim(strtolower($data['email'])));
$code = $conn->real_escape_string(trim($data['verification_code']));
$newPassword = $data['new_password'];

// Validate password strength
if (strlen($newPassword) < 8) {
    jsonResponse(false, null, 'Password must be at least 8 characters long');
    exit;
}

if (!preg_match('/[a-zA-Z]/', $newPassword)) {
    jsonResponse(false, null, 'Password must contain at least one letter');
    exit;
}

if (!preg_match('/[0-9]/', $newPassword)) {
    jsonResponse(false, null, 'Password must contain at least one number');
    exit;
}

// Verify the code is valid and not expired
$verifySql = "SELECT * FROM verification_codes 
              WHERE email = '$email' 
              AND code = '$code' 
              AND expires_at > NOW()
              ORDER BY created_at DESC 
              LIMIT 1";

$verifyResult = $conn->query($verifySql);

if (!$verifyResult || $verifyResult->num_rows === 0) {
    jsonResponse(false, null, 'Invalid or expired verification code');
    exit;
}

// Check if customer exists
$checkSql = "SELECT Customer_ID FROM customer_tb WHERE Email = '$email'";
$checkResult = $conn->query($checkSql);

if (!$checkResult || $checkResult->num_rows === 0) {
    jsonResponse(false, null, 'Customer account not found');
    exit;
}

// Hash new password
$hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

// Update password
$updateSql = "UPDATE customer_tb 
              SET Password = '$hashedPassword' 
              WHERE Email = '$email'";

if ($conn->query($updateSql)) {
    // Delete used verification code
    $deleteSql = "DELETE FROM verification_codes WHERE email = '$email'";
    $conn->query($deleteSql);
    
    jsonResponse(true, [
        'email' => $email
    ], 'Password reset successful');
} else {
    jsonResponse(false, null, 'Failed to reset password: ' . $conn->error);
}

$conn->close();
?>