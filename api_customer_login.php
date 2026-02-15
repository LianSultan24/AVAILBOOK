<?php
/**
 * Customer Login API
 * Handles customer authentication
 */

require_once 'config.php';
session_start();

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
if (empty($data['email']) || empty($data['password'])) {
    jsonResponse(false, null, 'Email and password are required');
    exit;
}

$email = $conn->real_escape_string(trim(strtolower($data['email'])));
$password = $data['password'];

// Fetch customer from database
$sql = "SELECT 
            Customer_ID,
            First_name,
            Last_name,
            Email,
            Contact_number,
            Password,
            Status,
            Created_at
        FROM customer_tb 
        WHERE Email = '$email'";

$result = $conn->query($sql);

if (!$result || $result->num_rows === 0) {
    jsonResponse(false, null, 'Invalid email or password');
    exit;
}

$customer = $result->fetch_assoc();

// Verify password
if (!password_verify($password, $customer['Password'])) {
    jsonResponse(false, null, 'Invalid email or password');
    exit;
}

// Check if account is active
if ($customer['Status'] === 'inactive') {
    jsonResponse(false, null, 'Your account has been deactivated. Please contact support.');
    exit;
}

// Remove password from response
unset($customer['Password']);

// Set session variables
$_SESSION['customer_id'] = $customer['Customer_ID'];
$_SESSION['customer_email'] = $customer['Email'];
$_SESSION['customer_name'] = $customer['First_name'] . ' ' . $customer['Last_name'];

// Return success response
jsonResponse(true, $customer, 'Login successful');

$conn->close();
?>