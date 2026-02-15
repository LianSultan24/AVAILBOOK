<?php
/**
 * Customer Registration API
 * Handles new customer account creation
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
$required = ['First_name', 'Last_name', 'Email', 'Contact_number', 'Password'];
foreach ($required as $field) {
    if (empty($data[$field])) {
        jsonResponse(false, null, "Field '$field' is required");
        exit;
    }
}

// Sanitize input
$firstName = $conn->real_escape_string(trim($data['First_name']));
$lastName = $conn->real_escape_string(trim($data['Last_name']));
$email = $conn->real_escape_string(trim(strtolower($data['Email'])));
$contactNumber = $conn->real_escape_string(trim($data['Contact_number']));
$password = $data['Password'];

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    jsonResponse(false, null, 'Invalid email format');
    exit;
}

// Validate password strength
if (strlen($password) < 8) {
    jsonResponse(false, null, 'Password must be at least 8 characters long');
    exit;
}

if (!preg_match('/[a-zA-Z]/', $password)) {
    jsonResponse(false, null, 'Password must contain at least one letter');
    exit;
}

if (!preg_match('/[0-9]/', $password)) {
    jsonResponse(false, null, 'Password must contain at least one number');
    exit;
}

// Validate Philippine mobile number format
$cleanNumber = preg_replace('/[^0-9]/', '', $contactNumber);
if (!preg_match('/^(09|639)\d{9}$/', $cleanNumber)) {
    jsonResponse(false, null, 'Invalid Philippine mobile number format');
    exit;
}

// Check if email already exists
$checkSql = "SELECT Customer_ID FROM customer_tb WHERE Email = '$email'";
$checkResult = $conn->query($checkSql);

if ($checkResult && $checkResult->num_rows > 0) {
    jsonResponse(false, null, 'Email address is already registered');
    exit;
}

// Hash password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Insert new customer
$sql = "INSERT INTO customer_tb 
        (First_name, Last_name, Email, Contact_number, Password, Status, Created_at) 
        VALUES 
        ('$firstName', '$lastName', '$email', '$contactNumber', '$hashedPassword', 'active', NOW())";

if ($conn->query($sql)) {
    $customerId = $conn->insert_id;
    
    jsonResponse(true, [
        'customer_id' => $customerId,
        'email' => $email,
        'first_name' => $firstName,
        'last_name' => $lastName
    ], 'Registration successful');
} else {
    jsonResponse(false, null, 'Registration failed: ' . $conn->error);
}

$conn->close();
?>