<?php
require_once 'config.php';
session_start();

$conn = getDBConnection();
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        handleGet($conn);
        break;
    case 'POST':
        handlePost($conn);
        break;
    default:
        jsonResponse(false, null, 'Invalid request method');
}

// GET - Fetch history
function handleGet($conn) {
    $sql = "SELECT 
                h.History_ID,
                h.Action,
                h.Details,
                h.Created_at,
                h.User_ID,
                u.Username as User_Name
            FROM history_tb h
            LEFT JOIN users u ON h.User_ID = u.User_ID
            ORDER BY h.Created_at DESC
            LIMIT 100";
    
    $result = $conn->query($sql);
    
    if ($result) {
        $history = [];
        while ($row = $result->fetch_assoc()) {
            $history[] = $row;
        }
        jsonResponse(true, $history, 'History fetched successfully');
    } else {
        jsonResponse(false, null, 'Error fetching history: ' . $conn->error);
    }
}

// POST - Log history
function handlePost($conn) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $action = $conn->real_escape_string($data['Action']);
    $details = $conn->real_escape_string($data['Details']);
    
    // Get user ID from session
    $user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : null;
    
    if ($user_id) {
        // Get username for better logging
        $user_sql = "SELECT Username FROM users WHERE User_ID = $user_id";
        $user_result = $conn->query($user_sql);
        $username = 'Unknown User';
        
        if ($user_result && $user_result->num_rows > 0) {
            $user_data = $user_result->fetch_assoc();
            $username = $user_data['Username'];
        }
        
        // Add username to details if not already included
        if (strpos($details, 'by ') === false) {
            $details = $details . " (by " . $username . ")";
        }
        
        $sql = "INSERT INTO history_tb (Action, Details, User_ID, Created_at) 
                VALUES ('$action', '" . $conn->real_escape_string($details) . "', $user_id, NOW())";
    } else {
        $sql = "INSERT INTO history_tb (Action, Details, Created_at) 
                VALUES ('$action', '$details', NOW())";
    }
    
    if ($conn->query($sql)) {
        jsonResponse(true, ['id' => $conn->insert_id], 'History logged successfully');
    } else {
        jsonResponse(false, null, 'Error logging history: ' . $conn->error);
    }
}

$conn->close();
?>