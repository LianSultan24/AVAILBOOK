<?php
require_once 'email_config.php';
require_once 'phpmailer/PHPMailer.php';
require_once 'phpmailer/SMTP.php';
require_once 'phpmailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Send Email Function
 * 
 * @param string $to Recipient email
 * @param string $subject Email subject
 * @param string $body Email body (HTML)
 * @param string $recipientName Recipient name (optional)
 * @return array ['success' => bool, 'message' => string]
 */
function sendEmail($to, $subject, $body, $recipientName = '') {
    // Check if email is enabled
    if (!EMAIL_ENABLED) {
        return [
            'success' => false,
            'message' => 'Email sending is currently disabled'
        ];
    }
    
    $mail = new PHPMailer(true);
    
    try {
        // Server settings
        if (EMAIL_DEBUG) {
            $mail->SMTPDebug = 2; // Enable verbose debug output
        }
        
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USERNAME;
        $mail->Password   = SMTP_PASSWORD;
        $mail->SMTPSecure = SMTP_SECURE;
        $mail->Port       = SMTP_PORT;
        
        // Recipients
        $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
        $mail->addAddress($to, $recipientName);
        
        // Reply-to
        $mail->addReplyTo(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;
        $mail->AltBody = strip_tags($body); // Plain text version
        
        $mail->send();
        
        return [
            'success' => true,
            'message' => 'Email sent successfully'
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => 'Email could not be sent. Error: ' . $mail->ErrorInfo
        ];
    }
}

/**
 * Send Appointment Confirmation Email
 * Sent when admin/staff approves an appointment
 * 
 * @param array $appointmentData Appointment details
 * @return array Result
 */
function sendAppointmentConfirmation($appointmentData) {
    $to = $appointmentData['Customer_Email'];
    $customerName = $appointmentData['Customer_Name'];
    $appointmentId = $appointmentData['Appointment_ID'];
    $service = $appointmentData['Service_Name'];
    $date = date('F j, Y', strtotime($appointmentData['Appointment_date']));
    $time = date('g:i A', strtotime($appointmentData['Appointment_time']));
    $location = $appointmentData['Location'];
    $carType = $appointmentData['Car_type'];
    $carModel = $appointmentData['Car_Model'];
    
    $subject = "Appointment Confirmed - Booking #" . $appointmentId;
    
    $body = getEmailTemplate('confirmation', [
        'customer_name' => $customerName,
        'appointment_id' => $appointmentId,
        'service' => $service,
        'date' => $date,
        'time' => $time,
        'location' => $location,
        'car_type' => $carType,
        'car_model' => $carModel
    ]);
    
    return sendEmail($to, $subject, $body, $customerName);
}

/**
 * Send Appointment Reminder Email
 * Sent 8 hours before appointment or manually by admin/staff
 * 
 * @param array $appointmentData Appointment details
 * @param bool $isAutomatic True if auto-reminder, False if manual
 * @return array Result
 */
function sendAppointmentReminder($appointmentData, $isAutomatic = false) {
    $to = $appointmentData['Customer_Email'];
    $customerName = $appointmentData['Customer_Name'];
    $appointmentId = $appointmentData['Appointment_ID'];
    $service = $appointmentData['Service_Name'];
    $date = date('F j, Y', strtotime($appointmentData['Appointment_date']));
    $time = date('g:i A', strtotime($appointmentData['Appointment_time']));
    $location = $appointmentData['Location'];
    $carType = $appointmentData['Car_type'];
    $carModel = $appointmentData['Car_Model'];
    
    $subject = $isAutomatic 
        ? "Reminder: Your Appointment Tomorrow - Booking #" . $appointmentId
        : "Appointment Reminder - Booking #" . $appointmentId;
    
    $body = getEmailTemplate('reminder', [
        'customer_name' => $customerName,
        'appointment_id' => $appointmentId,
        'service' => $service,
        'date' => $date,
        'time' => $time,
        'location' => $location,
        'car_type' => $carType,
        'car_model' => $carModel,
        'is_automatic' => $isAutomatic
    ]);
    
    return sendEmail($to, $subject, $body, $customerName);
}

/**
 * Get Email Template
 * Load HTML email template and replace placeholders
 * 
 * @param string $templateName Template file name (without .php)
 * @param array $data Data to replace in template
 * @return string HTML email body
 */
function getEmailTemplate($templateName, $data) {
    $templateFile = EMAIL_TEMPLATES_DIR . $templateName . '.php';
    
    if (!file_exists($templateFile)) {
        // Return basic template if file not found
        return basicEmailTemplate($data);
    }
    
    // Start output buffering
    ob_start();
    
    // Extract data to variables
    extract($data);
    
    // Include template
    include $templateFile;
    
    // Get content and clean buffer
    $content = ob_get_clean();
    
    return $content;
}

/**
 * Basic Email Template (fallback)
 * Used if template files are not available
 * 
 * @param array $data Email data
 * @return string HTML email
 */
function basicEmailTemplate($data) {
    $html = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: #2c3e50; color: white; padding: 20px; text-align: center; }
            .content { background: #f9f9f9; padding: 20px; }
            .footer { background: #34495e; color: white; padding: 15px; text-align: center; font-size: 12px; }
            table { width: 100%; border-collapse: collapse; margin: 20px 0; }
            td { padding: 10px; border-bottom: 1px solid #ddd; }
            .label { font-weight: bold; width: 40%; }
            .btn { background: #ff9800; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block; margin-top: 20px; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h1>' . COMPANY_NAME . '</h1>
            </div>
            <div class="content">
                <h2>Hello ' . htmlspecialchars($data['customer_name'] ?? 'Valued Customer') . '!</h2>
                <p>Thank you for choosing our services.</p>
                
                <table>
                    <tr><td class="label">Appointment ID:</td><td>#' . ($data['appointment_id'] ?? 'N/A') . '</td></tr>
                    <tr><td class="label">Service:</td><td>' . ($data['service'] ?? 'N/A') . '</td></tr>
                    <tr><td class="label">Date:</td><td>' . ($data['date'] ?? 'N/A') . '</td></tr>
                    <tr><td class="label">Time:</td><td>' . ($data['time'] ?? 'N/A') . '</td></tr>
                    <tr><td class="label">Location:</td><td>' . ($data['location'] ?? 'N/A') . '</td></tr>
                    <tr><td class="label">Vehicle:</td><td>' . ($data['car_type'] ?? '') . ' - ' . ($data['car_model'] ?? 'N/A') . '</td></tr>
                </table>
                
                <p>If you have any questions, please contact us.</p>
            </div>
            <div class="footer">
                <p>' . COMPANY_NAME . '</p>
                <p>' . COMPANY_ADDRESS . ' | ' . COMPANY_PHONE . '</p>
            </div>
        </div>
    </body>
    </html>
    ';
    
    return $html;
}

/**
 * Log Email Sent
 * Save email sending record to database
 * 
 * @param int $appointmentId Appointment ID
 * @param string $emailType Type of email (confirmation, reminder, etc.)
 * @param string $recipient Recipient email
 * @param bool $success Whether email was sent successfully
 * @return bool
 */
function logEmailSent($appointmentId, $emailType, $recipient, $success) {
    require_once 'config.php';
    $conn = getDBConnection();
    
    $status = $success ? 'sent' : 'failed';
    
    $sql = "INSERT INTO email_logs (
                Appointment_ID, Email_Type, Recipient, Status, Sent_at
            ) VALUES (
                $appointmentId,
                '" . $conn->real_escape_string($emailType) . "',
                '" . $conn->real_escape_string($recipient) . "',
                '$status',
                NOW()
            )";
    
    $result = $conn->query($sql);
    $conn->close();
    
    return $result;
}
?>