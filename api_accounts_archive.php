<?php
require_once 'config.php';

$conn = getDBConnection();
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        handleGet($conn);
        break;
    case 'POST':
        handleUnarchive($conn);
        break;
    default:
        jsonResponse(false, null, 'Invalid request method');
}

// GET - Fetch archived accounts
function handleGet($conn) {
    $sql = "SELECT * FROM users_archive_tb ORDER BY Archived_at DESC";
    
    $result = $conn->query($sql);
    
    if ($result) {
        $archived = [];
        while ($row = $result->fetch_assoc()) {
            $archived[] = $row;
        }
        jsonResponse(true, $archived, 'Archived accounts fetched successfully');
    } else {
        jsonResponse(false, null, 'Error fetching archived accounts: ' . $conn->error);
    }
}

// POST - Unarchive account (restore to main table)
function handleUnarchive($conn) {
    $data = json_decode(file_get_contents('php://input'), true);
    $archive_id = intval($data['Archive_ID']);
    
    // Get archived account
    $sql = "SELECT * FROM users_archive_tb WHERE Archive_ID = $archive_id";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        $account = $result->fetch_assoc();
        
        // Insert back to main table
        $restore_sql = "INSERT INTO users (
                            Username, Email, Password, Contact_Number,
                            Status, Role_ID, Created_at
                        ) VALUES (
                            '" . $conn->real_escape_string($account['Username']) . "',
                            '" . $conn->real_escape_string($account['Email']) . "',
                            '" . $conn->real_escape_string($account['Password']) . "',
                            '" . $conn->real_escape_string($account['Contact_Number']) . "',
                            'active',
                            " . $account['Role_ID'] . ",
                            '" . $account['Created_at'] . "'
                        )";
        
        if ($conn->query($restore_sql)) {
            // Delete from archive
            $delete_sql = "DELETE FROM users_archive_tb WHERE Archive_ID = $archive_id";
            if ($conn->query($delete_sql)) {
                jsonResponse(true, ['id' => $conn->insert_id], 'Account restored successfully');
            } else {
                jsonResponse(false, null, 'Error removing from archive: ' . $conn->error);
            }
        } else {
            jsonResponse(false, null, 'Error restoring account: ' . $conn->error);
        }
    } else {
        jsonResponse(false, null, 'Archived account not found');
    }
}

$conn->close();
?>