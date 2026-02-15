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

// GET - Fetch archived appointments
function handleGet($conn) {
    $sql = "SELECT * FROM appointment_archive_tb ORDER BY Archived_at DESC";
    
    $result = $conn->query($sql);
    
    if ($result) {
        $archived = [];
        while ($row = $result->fetch_assoc()) {
            $archived[] = $row;
        }
        jsonResponse(true, $archived, 'Archived appointments fetched successfully');
    } else {
        jsonResponse(false, null, 'Error fetching archived appointments: ' . $conn->error);
    }
}

// POST - Unarchive appointment (restore to main table)
function handleUnarchive($conn) {
    $data = json_decode(file_get_contents('php://input'), true);
    $archive_id = intval($data['Archive_ID']);
    
    // Get archived appointment
    $sql = "SELECT * FROM appointment_archive_tb WHERE Archive_ID = $archive_id";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        $apt = $result->fetch_assoc();
        
        // Insert back to main table
        $restore_sql = "INSERT INTO appointment_tb (
                            Service_type, Car_type, Car_Model,
                            Appointment_date, Appointment_time, Location, Status,
                            User_ID, Customer_ID, Service_ID
                        ) VALUES (
                            '" . $conn->real_escape_string($apt['Service_type']) . "',
                            '" . $conn->real_escape_string($apt['Car_type']) . "',
                            '" . $conn->real_escape_string($apt['Car_Model']) . "',
                            '" . $apt['Appointment_date'] . "',
                            '" . $apt['Appointment_time'] . "',
                            '" . $conn->real_escape_string($apt['Location']) . "',
                            '" . $apt['Status'] . "',
                            " . ($apt['User_ID'] ? $apt['User_ID'] : 'NULL') . ",
                            " . ($apt['Customer_ID'] ? $apt['Customer_ID'] : 'NULL') . ",
                            " . ($apt['Service_ID'] ? $apt['Service_ID'] : 'NULL') . "
                        )";
        
        if ($conn->query($restore_sql)) {
            // Delete from archive
            $delete_sql = "DELETE FROM appointment_archive_tb WHERE Archive_ID = $archive_id";
            if ($conn->query($delete_sql)) {
                jsonResponse(true, ['id' => $conn->insert_id], 'Appointment restored successfully');
            } else {
                jsonResponse(false, null, 'Error removing from archive: ' . $conn->error);
            }
        } else {
            jsonResponse(false, null, 'Error restoring appointment: ' . $conn->error);
        }
    } else {
        jsonResponse(false, null, 'Archived appointment not found');
    }
}

$conn->close();
?>