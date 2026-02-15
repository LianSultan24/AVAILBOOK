<?php
/**
 * Automatic Email Reminder Cron Job
 * 
 * This script should be run every hour via cron job
 * It sends reminder emails 8 hours before appointments
 * 
 * CRON SETUP:
 * Add this line to your crontab (crontab -e):
 * 0 * * * * /usr/bin/php /path/to/your/project/check_reminders.php
 * 
 * Or run manually for testing:
 * php check_reminders.php
 */

require_once 'config.php';
require_once 'send_email.php';

$conn = getDBConnection();

echo "===========================================\n";
echo "Email Reminder Cron Job\n";
echo "Started: " . date('Y-m-d H:i:s') . "\n";
echo "===========================================\n\n";

// Get pending reminders that are due now
$now = date('Y-m-d H:i:s');

$sql = "SELECT 
            rs.Schedule_ID,
            rs.Appointment_ID,
            rs.Reminder_Time,
            a.Appointment_date,
            a.Appointment_time,
            a.Car_type,
            a.Car_Model,
            a.Location,
            a.Status,
            CONCAT(c.First_name, ' ', c.Last_name) as Customer_Name,
            c.Email as Customer_Email,
            s.Service_Name
        FROM reminder_schedule rs
        JOIN appointment_tb a ON rs.Appointment_ID = a.Appointment_ID
        LEFT JOIN customer_tb c ON a.Customer_ID = c.Customer_ID
        LEFT JOIN service_tb s ON a.Service_ID = s.Service_ID
        WHERE rs.Status = 'pending'
        AND rs.Reminder_Time <= '$now'
        AND a.Status = 'approved'
        ORDER BY rs.Reminder_Time ASC";

$result = $conn->query($sql);

if (!$result) {
    echo "Error: " . $conn->error . "\n";
    exit;
}

$remindersCount = $result->num_rows;
echo "Found $remindersCount pending reminders\n\n";

$sentCount = 0;
$failedCount = 0;

while ($row = $result->fetch_assoc()) {
    echo "Processing Reminder #" . $row['Schedule_ID'] . " for Appointment #" . $row['Appointment_ID'] . "\n";
    echo "Customer: " . $row['Customer_Name'] . " (" . $row['Customer_Email'] . ")\n";
    echo "Appointment: " . $row['Appointment_date'] . " " . $row['Appointment_time'] . "\n";
    
    // Send reminder email
    $emailResult = sendAppointmentReminder($row, true); // true = automatic reminder
    
    if ($emailResult['success']) {
        echo "✓ Email sent successfully!\n";
        $sentCount++;
        
        // Update reminder status to 'sent'
        $updateSql = "UPDATE reminder_schedule 
                     SET Status = 'sent', Sent_at = NOW() 
                     WHERE Schedule_ID = " . $row['Schedule_ID'];
        $conn->query($updateSql);
        
        // Log email
        logEmailSent($row['Appointment_ID'], 'reminder', $row['Customer_Email'], true);
        
    } else {
        echo "✗ Failed to send email: " . $emailResult['message'] . "\n";
        $failedCount++;
        
        // Update reminder status to 'failed'
        $updateSql = "UPDATE reminder_schedule 
                     SET Status = 'failed' 
                     WHERE Schedule_ID = " . $row['Schedule_ID'];
        $conn->query($updateSql);
        
        // Log email failure
        logEmailSent($row['Appointment_ID'], 'reminder', $row['Customer_Email'], false);
    }
    
    echo "-------------------------------------------\n\n";
}

echo "===========================================\n";
echo "Summary:\n";
echo "Total Reminders: $remindersCount\n";
echo "Sent: $sentCount\n";
echo "Failed: $failedCount\n";
echo "Completed: " . date('Y-m-d H:i:s') . "\n";
echo "===========================================\n";

$conn->close();
?>