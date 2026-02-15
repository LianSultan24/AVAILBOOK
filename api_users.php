<?php
require_once 'config.php';

$conn = getDBConnection();
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        handleGet($conn);
        break;
    case 'POST':
        handlePost($conn);
        break;
    case 'PUT':
        handlePut($conn);
        break;
    case 'DELETE':
        handleDelete($conn);
        break;
    default:
        jsonResponse(false, null, 'Invalid request method');
}

// GET - Fetch users
function handleGet($conn) {
    $id = isset($_GET['id']) ? intval($_GET['id']) : null;
    
    $sql = "SELECT 
                u.User_ID,
                u.Username,
                u.Email,
                u.Contact_Number,
                u.Status,
                u.Created_at,
                u.Role_ID,
                r.Role_Name
            FROM users u
            LEFT JOIN role_tb r ON u.Role_ID = r.Role_ID";
    
    if ($id) {
        $sql .= " WHERE u.User_ID = $id";
    }
    
    $sql .= " ORDER BY u.Created_at DESC";
    
    $result = $conn->query($sql);
    
    if ($result) {
        $users = [];
        while ($row = $result->fetch_assoc()) {
            // Don't send password in response
            unset($row['Password']);
            $users[] = $row;
        }
        jsonResponse(true, $users, 'Users fetched successfully');
    } else {
        jsonResponse(false, null, 'Error fetching users: ' . $conn->error);
    }
}

// POST - Create user/staff account
function handlePost($conn) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $username = $conn->real_escape_string($data['Username']);
    $email = $conn->real_escape_string($data['Email']);
    $password = password_hash($data['Password'], PASSWORD_DEFAULT);
    $contact_number = $conn->real_escape_string($data['Contact_Number']);
    $role_id = intval($data['Role_ID']);
    $status = isset($data['Status']) ? $conn->real_escape_string($data['Status']) : 'active';
    
    // Check if email already exists
    $check_sql = "SELECT User_ID FROM users WHERE Email = '$email'";
    $check_result = $conn->query($check_sql);
    
    if ($check_result->num_rows > 0) {
        jsonResponse(false, null, 'Email already exists');
        return;
    }
    
    $sql = "INSERT INTO users (
                Username, Email, Password, Contact_Number, 
                Status, Role_ID, Created_at
            ) VALUES (
                '$username', '$email', '$password', '$contact_number',
                '$status', $role_id, NOW()
            )";
    
    if ($conn->query($sql)) {
        jsonResponse(true, ['id' => $conn->insert_id], 'User account created successfully');
    } else {
        jsonResponse(false, null, 'Error creating user: ' . $conn->error);
    }
}

// PUT - Update user status or information
function handlePut($conn) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $id = intval($data['User_ID']);
    
    // Check what to update
    if (isset($data['Status'])) {
        // Toggle status
        $status = $conn->real_escape_string($data['Status']);
        $sql = "UPDATE users SET Status = '$status' WHERE User_ID = $id";
    } else {
        // Update full information
        $username = $conn->real_escape_string($data['Username']);
        $email = $conn->real_escape_string($data['Email']);
        $contact_number = $conn->real_escape_string($data['Contact_Number']);
        $role_id = intval($data['Role_ID']);
        
        $sql = "UPDATE users SET 
                    Username = '$username',
                    Email = '$email',
                    Contact_Number = '$contact_number',
                    Role_ID = $role_id
                WHERE User_ID = $id";
    }
    
    if ($conn->query($sql)) {
        jsonResponse(true, null, 'User updated successfully');
    } else {
        jsonResponse(false, null, 'Error updating user: ' . $conn->error);
    }
}

// DELETE - Archive user (move to archive table)
function handleDelete($conn) {
    $id = isset($_GET['id']) ? intval($_GET['id']) : null;
    
    if (!$id) {
        jsonResponse(false, null, 'User ID is required');
        return;
    }
    
    // Get user with role information before archiving
    $sql = "SELECT 
                u.User_ID,
                u.Username,
                u.Email,
                u.Password,
                u.Contact_Number,
                u.Status,
                u.Created_at,
                u.Role_ID,
                r.Role_Name
            FROM users u
            LEFT JOIN role_tb r ON u.Role_ID = r.Role_ID
            WHERE u.User_ID = $id";
    
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Insert into archive table
        $archive_sql = "INSERT INTO users_archive_tb (
                            User_ID, Username, Email, Password, Contact_Number,
                            Status, Role_ID, Role_Name, Created_at
                        ) VALUES (
                            {$user['User_ID']},
                            '" . $conn->real_escape_string($user['Username']) . "',
                            '" . $conn->real_escape_string($user['Email']) . "',
                            '" . $conn->real_escape_string($user['Password']) . "',
                            '" . $conn->real_escape_string($user['Contact_Number']) . "',
                            '" . $user['Status'] . "',
                            " . $user['Role_ID'] . ",
                            '" . $conn->real_escape_string($user['Role_Name']) . "',
                            '" . $user['Created_at'] . "'
                        )";
        
        if ($conn->query($archive_sql)) {
            // Delete from main table
            $delete_sql = "DELETE FROM users WHERE User_ID = $id";
            if ($conn->query($delete_sql)) {
                jsonResponse(true, null, 'User archived successfully');
            } else {
                jsonResponse(false, null, 'Error deleting user: ' . $conn->error);
            }
        } else {
            jsonResponse(false, null, 'Error archiving user: ' . $conn->error);
        }
    } else {
        jsonResponse(false, null, 'User not found');
    }
}

$conn->close();
?>