<?php
session_start();
require_once 'config.php';

$conn = getDBConnection();

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);
$email = $conn->real_escape_string($data['email']);
$password = $data['password'];

// Check in users table (Admin/Staff only)
$sql = "SELECT 
            u.User_ID,
            u.Username,
            u.Email,
            u.Password,
            u.Contact_Number,
            u.Status,
            r.Role_Name
        FROM users u
        LEFT JOIN role_tb r ON u.Role_ID = r.Role_ID
        WHERE u.Email = '$email'";

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $user = $result->fetch_assoc();
    
    // Verify password first
    if (password_verify($password, $user['Password'])) {
        
        // UPDATED: Check if account is deactivated
        if ($user['Status'] === 'disabled') {
            jsonResponse(false, null, 'Your account has been deactivated. Please contact the administrator.');
            exit();
        }
        
        // Check if account is active
        if ($user['Status'] !== 'active') {
            jsonResponse(false, null, 'Account is not active. Please contact administrator.');
            exit();
        }
        
        // Set session variables
        $_SESSION['user_id'] = $user['User_ID'];
        $_SESSION['username'] = $user['Username'];
        $_SESSION['email'] = $user['Email'];
        $_SESSION['role'] = $user['Role_Name'];
        $_SESSION['logged_in'] = true;
        
        // Determine redirect URL based on role
        $redirect_url = '';
        if ($user['Role_Name'] === 'Admin') {
            $redirect_url = 'admin_dashboard.php';
        } elseif ($user['Role_Name'] === 'Staff') {
            $redirect_url = 'staff_dashboard.php';
        } else {
            jsonResponse(false, null, 'Invalid user role');
            exit();
        }
        
        // Return success with user data and redirect URL
        jsonResponse(true, [
            'user_id' => $user['User_ID'],
            'username' => $user['Username'],
            'email' => $user['Email'],
            'role' => $user['Role_Name'],
            'redirect_url' => $redirect_url
        ], 'Login successful');
    } else {
        jsonResponse(false, null, 'Invalid email or password');
    }
} else {
    jsonResponse(false, null, 'Invalid email or password');
}

$conn->close();
?>