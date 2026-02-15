<?php
require_once 'config.php';
require_once 'send_email.php';

$conn = getDBConnection();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    handleSendEmail($conn);
} else {
    jsonResponse(false, null, 'Invalid request method');
}

function handleSendEmail($conn) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $appointmentId = intval($data['Appointment_ID']);
    $emailType = $data['Email_Type']; // 'confirmation' or 'reminder'
    
    // Get appointment details
    $sql = "SELECT 
                a.*,
                CONCAT(c.First_name, ' ', c.Last_name) as Customer_Name,
                c.Email as Customer_Email,
                s.Service_Name
            FROM appointment_tb a
            LEFT JOIN customer_tb c ON a.Customer_ID = c.Customer_ID
            LEFT JOIN service_tb s ON a.Service_ID = s.Service_ID
            WHERE a.Appointment_ID = $appointmentId";
    
    $result = $conn->query($sql);
    
    if (!$result || $result->num_rows == 0) {
        jsonResponse(false, null, 'Appointment not found');
        return;
    }
    
    $appointmentData = $result->fetch_assoc();
    
    // Send email based on type
    if ($emailType === 'confirmation') {
        $emailResult = sendAppointmentConfirmation($appointmentData);
    } else if ($emailType === 'reminder') {
        $emailResult = sendAppointmentReminder($appointmentData, false); // false = manual reminder
    } else {
        jsonResponse(false, null, 'Invalid email type');
        return;
    }
    
    // Log email
    logEmailSent(
        $appointmentId,
        $emailType === 'reminder' ? 'manual_reminder' : $emailType,
        $appointmentData['Customer_Email'],
        $emailResult['success']
    );
    
    if ($emailResult['success']) {
        jsonResponse(true, null, 'Email sent successfully to ' . $appointmentData['Customer_Email']);
    } else {
        jsonResponse(false, null, $emailResult['message']);
    }
}

$conn->close();
?>