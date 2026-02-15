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

// GET - Fetch appointments
function handleGet($conn) {
    $id = isset($_GET['id']) ? intval($_GET['id']) : null;
    $status = isset($_GET['status']) ? $_GET['status'] : null;
    $service_id = isset($_GET['service_id']) ? intval($_GET['service_id']) : null;
    $search = isset($_GET['search']) ? $_GET['search'] : null;
    
    $sql = "SELECT 
                a.Appointment_ID,
                a.Service_type,
                a.Car_type,
                a.Car_Model,
                a.Appointment_date,
                a.Appointment_time,
                a.Location,
                a.Status,
                a.Service_Mode,
                a.Created_at,
                a.Updated_at,
                a.User_ID,
                a.Customer_ID,
                a.Service_ID,
                CONCAT(c.First_name, ' ', c.Last_name) as Customer_Name,
                c.Email as Customer_Email,
                c.Contact_number as Customer_Contact,
                s.Service_Name,
                u.Username as User_Name
            FROM appointment_tb a
            LEFT JOIN customer_tb c ON a.Customer_ID = c.Customer_ID
            LEFT JOIN service_tb s ON a.Service_ID = s.Service_ID
            LEFT JOIN users u ON a.User_ID = u.User_ID
            WHERE 1=1";
    
    // Apply filters
    if ($id) {
        $sql .= " AND a.Appointment_ID = $id";
    }
    
    if ($status) {
        $sql .= " AND a.Status = '" . $conn->real_escape_string($status) . "'";
    }
    
    if ($service_id) {
        $sql .= " AND a.Service_ID = $service_id";
    }
    
    if ($search) {
        $search = $conn->real_escape_string($search);
        $sql .= " AND (
            CONCAT(c.First_name, ' ', c.Last_name) LIKE '%$search%' OR
            a.Car_Model LIKE '%$search%' OR
            a.Location LIKE '%$search%' OR
            a.Car_type LIKE '%$search%'
        )";
    }
    
    $sql .= " ORDER BY a.Created_at DESC";
    
    $result = $conn->query($sql);
    
    if ($result) {
        $appointments = [];
        while ($row = $result->fetch_assoc()) {
            $appointments[] = $row;
        }
        jsonResponse(true, $appointments, 'Appointments fetched successfully');
    } else {
        jsonResponse(false, null, 'Error fetching appointments: ' . $conn->error);
    }
}

// POST - Create appointment
function handlePost($conn) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $service_type = $conn->real_escape_string($data['Service_type']);
    $car_type = $conn->real_escape_string($data['Car_type']);
    $car_model = $conn->real_escape_string($data['Car_Model']);
    $appointment_date = $conn->real_escape_string($data['Appointment_date']);
    $appointment_time = $conn->real_escape_string($data['Appointment_time']);
    $location = $conn->real_escape_string($data['Location']);
    $status = isset($data['Status']) ? $conn->real_escape_string($data['Status']) : 'pending';
    $user_id = intval($data['User_ID']);
    $customer_id = intval($data['Customer_ID']);
    $service_id = intval($data['Service_ID']);
    
    $sql = "INSERT INTO appointment_tb (
                Service_type, Car_type, Car_Model, Appointment_date, 
                Appointment_time, Location, Status, User_ID, 
                Customer_ID, Service_ID, Created_at, Updated_at
            ) VALUES (
                '$service_type', '$car_type', '$car_model', '$appointment_date',
                '$appointment_time', '$location', '$status', $user_id,
                $customer_id, $service_id, NOW(), NOW()
            )";
    
    if ($conn->query($sql)) {
        jsonResponse(true, ['id' => $conn->insert_id], 'Appointment created successfully');
    } else {
        jsonResponse(false, null, 'Error creating appointment: ' . $conn->error);
    }
}

// PUT - Update appointment status
function handlePut($conn) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $id = intval($data['Appointment_ID']);
    $newStatus = $conn->real_escape_string($data['Status']);
    
    // Get old status first
    $checkSql = "SELECT Status FROM appointment_tb WHERE Appointment_ID = $id";
    $checkResult = $conn->query($checkSql);
    
    if (!$checkResult || $checkResult->num_rows == 0) {
        jsonResponse(false, null, 'Appointment not found');
        return;
    }
    
    $oldStatus = $checkResult->fetch_assoc()['Status'];
    
    // Update status
    $sql = "UPDATE appointment_tb 
            SET Status = '$newStatus', Updated_at = NOW() 
            WHERE Appointment_ID = $id";
    
    if ($conn->query($sql)) {
        // If status changed to 'approved', send confirmation email
        if ($oldStatus !== 'approved' && $newStatus === 'approved') {
            // Get full appointment details for email
            $detailsSql = "SELECT 
                            a.*,
                            CONCAT(c.First_name, ' ', c.Last_name) as Customer_Name,
                            c.Email as Customer_Email,
                            s.Service_Name
                        FROM appointment_tb a
                        LEFT JOIN customer_tb c ON a.Customer_ID = c.Customer_ID
                        LEFT JOIN service_tb s ON a.Service_ID = s.Service_ID
                        WHERE a.Appointment_ID = $id";
            
            $detailsResult = $conn->query($detailsSql);
            
            if ($detailsResult && $detailsResult->num_rows > 0) {
                $appointmentData = $detailsResult->fetch_assoc();
                
                // Send confirmation email
                require_once 'send_email.php';
                $emailResult = sendAppointmentConfirmation($appointmentData);
                
                // Log email attempt
                logEmailSent(
                    $id, 
                    'confirmation', 
                    $appointmentData['Customer_Email'], 
                    $emailResult['success']
                );
                
                // Schedule automatic reminder (8 hours before)
                scheduleAutoReminder($conn, $id, $appointmentData['Appointment_date'], $appointmentData['Appointment_time']);
            }
        }
        
        jsonResponse(true, null, 'Appointment status updated successfully');
    } else {
        jsonResponse(false, null, 'Error updating appointment: ' . $conn->error);
    }
}

// Helper function to schedule auto reminder
function scheduleAutoReminder($conn, $appointmentId, $date, $time) {
    // Calculate reminder time (8 hours before appointment)
    $appointmentDateTime = $date . ' ' . $time;
    $reminderTime = date('Y-m-d H:i:s', strtotime($appointmentDateTime) - (8 * 3600));
    
    // Insert into reminder_schedule table
    $sql = "INSERT INTO reminder_schedule (Appointment_ID, Reminder_Time, Status)
            VALUES ($appointmentId, '$reminderTime', 'pending')
            ON DUPLICATE KEY UPDATE 
            Reminder_Time = '$reminderTime', 
            Status = 'pending'";
    
    $conn->query($sql);
}

// DELETE - Archive appointment (move to archive table)
function handleDelete($conn) {
    $id = isset($_GET['id']) ? intval($_GET['id']) : null;
    
    if (!$id) {
        jsonResponse(false, null, 'Appointment ID is required');
    }
    
    // Get appointment with all related data before archiving
    $sql = "SELECT 
                a.Appointment_ID,
                a.Service_type,
                a.Car_type,
                a.Car_Model,
                a.Appointment_date,
                a.Appointment_time,
                a.Location,
                a.Status,
                a.Created_at,
                a.Updated_at,
                a.User_ID,
                a.Customer_ID,
                a.Service_ID,
                CONCAT(c.First_name, ' ', c.Last_name) as Customer_Name,
                c.Email as Customer_Email,
                c.Contact_number as Customer_Contact,
                s.Service_Name,
                u.Username as User_Name
            FROM appointment_tb a
            LEFT JOIN customer_tb c ON a.Customer_ID = c.Customer_ID
            LEFT JOIN service_tb s ON a.Service_ID = s.Service_ID
            LEFT JOIN users u ON a.User_ID = u.User_ID
            WHERE a.Appointment_ID = $id";
    
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        $apt = $result->fetch_assoc();
        
        // Insert into archive table
        $archive_sql = "INSERT INTO appointment_archive_tb (
                            Appointment_ID, Service_type, Car_type, Car_Model,
                            Appointment_date, Appointment_time, Location, Status,
                            Created_at, Updated_at, User_ID, Customer_ID, Service_ID,
                            Customer_Name, Customer_Email, Customer_Contact,
                            Service_Name, User_Name
                        ) VALUES (
                            {$apt['Appointment_ID']},
                            '" . $conn->real_escape_string($apt['Service_type']) . "',
                            '" . $conn->real_escape_string($apt['Car_type']) . "',
                            '" . $conn->real_escape_string($apt['Car_Model']) . "',
                            '" . $apt['Appointment_date'] . "',
                            '" . $apt['Appointment_time'] . "',
                            '" . $conn->real_escape_string($apt['Location']) . "',
                            '" . $apt['Status'] . "',
                            '" . $apt['Created_at'] . "',
                            '" . $apt['Updated_at'] . "',
                            " . ($apt['User_ID'] ? $apt['User_ID'] : 'NULL') . ",
                            " . ($apt['Customer_ID'] ? $apt['Customer_ID'] : 'NULL') . ",
                            " . ($apt['Service_ID'] ? $apt['Service_ID'] : 'NULL') . ",
                            '" . $conn->real_escape_string($apt['Customer_Name']) . "',
                            '" . $conn->real_escape_string($apt['Customer_Email']) . "',
                            '" . $conn->real_escape_string($apt['Customer_Contact']) . "',
                            '" . $conn->real_escape_string($apt['Service_Name']) . "',
                            '" . $conn->real_escape_string($apt['User_Name']) . "'
                        )";
        
        if ($conn->query($archive_sql)) {
            // Delete from main table
            $delete_sql = "DELETE FROM appointment_tb WHERE Appointment_ID = $id";
            if ($conn->query($delete_sql)) {
                jsonResponse(true, null, 'Appointment archived successfully');
            } else {
                jsonResponse(false, null, 'Error deleting appointment: ' . $conn->error);
            }
        } else {
            jsonResponse(false, null, 'Error archiving appointment: ' . $conn->error);
        }
    } else {
        jsonResponse(false, null, 'Appointment not found');
    }
}

$conn->close();
?>