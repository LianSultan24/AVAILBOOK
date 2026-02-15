<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Arial, sans-serif;
            background-color: #f4f4f4;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
        }
        .header p {
            margin: 5px 0 0 0;
            opacity: 0.9;
        }
        .content {
            padding: 30px;
        }
        .greeting {
            font-size: 18px;
            color: #2c3e50;
            margin-bottom: 20px;
        }
        .reminder-badge {
            background: #ffc107;
            color: #000;
            padding: 10px 20px;
            border-radius: 25px;
            display: inline-block;
            margin-bottom: 20px;
            font-weight: bold;
        }
        .countdown {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 25px;
            border-radius: 8px;
            text-align: center;
            margin: 20px 0;
        }
        .countdown h2 {
            margin: 0;
            font-size: 36px;
        }
        .countdown p {
            margin: 5px 0 0 0;
            font-size: 16px;
        }
        .appointment-details {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .detail-row {
            display: flex;
            padding: 12px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            font-weight: bold;
            color: #495057;
            width: 140px;
            flex-shrink: 0;
        }
        .detail-value {
            color: #2c3e50;
            flex-grow: 1;
        }
        .checklist {
            background: #e7f3ff;
            border-left: 4px solid #0066cc;
            padding: 20px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .checklist h4 {
            margin-top: 0;
            color: #0066cc;
        }
        .checklist ul {
            margin: 10px 0;
            padding-left: 20px;
        }
        .checklist li {
            margin: 8px 0;
            color: #2c3e50;
        }
        .button {
            display: inline-block;
            background: #ff9800;
            color: white;
            padding: 14px 30px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            margin: 20px 0;
            transition: background 0.3s;
        }
        .button:hover {
            background: #e68900;
        }
        .footer {
            background: #2c3e50;
            color: white;
            padding: 25px;
            text-align: center;
        }
        .footer p {
            margin: 5px 0;
            font-size: 14px;
        }
        @media only screen and (max-width: 600px) {
            .email-container {
                margin: 0;
                border-radius: 0;
            }
            .detail-row {
                flex-direction: column;
            }
            .detail-label {
                margin-bottom: 5px;
            }
            .countdown h2 {
                font-size: 28px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <h1>⏰ Appointment Reminder!</h1>
            <p><?php echo COMPANY_NAME; ?></p>
        </div>
        
        <!-- Content -->
        <div class="content">
            <div class="greeting">
                Hello <strong><?php echo htmlspecialchars($customer_name); ?></strong>,
            </div>
            
            <div class="reminder-badge">
                🔔 UPCOMING APPOINTMENT REMINDER
            </div>
            
            <?php if (isset($is_automatic) && $is_automatic): ?>
            <p>This is a friendly reminder that your appointment is coming up soon!</p>
            
            <div class="countdown">
                <h2>⏱️ Tomorrow!</h2>
                <p>Your appointment is scheduled for tomorrow</p>
            </div>
            <?php else: ?>
            <p>This is a reminder about your upcoming appointment with us.</p>
            <?php endif; ?>
            
            <!-- Appointment Details -->
            <div class="appointment-details">
                <h3 style="margin-top: 0; color: #2c3e50;">📋 Appointment Details</h3>
                
                <div class="detail-row">
                    <div class="detail-label">Booking ID:</div>
                    <div class="detail-value"><strong>#<?php echo $appointment_id; ?></strong></div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Service:</div>
                    <div class="detail-value"><?php echo htmlspecialchars($service); ?></div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Date:</div>
                    <div class="detail-value"><strong><?php echo $date; ?></strong></div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Time:</div>
                    <div class="detail-value"><strong><?php echo $time; ?></strong></div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Location:</div>
                    <div class="detail-value"><?php echo htmlspecialchars($location); ?></div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Vehicle:</div>
                    <div class="detail-value"><?php echo htmlspecialchars($car_type . ' - ' . $car_model); ?></div>
                </div>
            </div>
            
            <!-- Preparation Checklist -->
            <div class="checklist">
                <h4>✓ Before You Arrive:</h4>
                <ul>
                    <li>Please arrive 10 minutes early</li>
                    <li>Bring your vehicle registration</li>
                    <li>Clear any personal items from your car</li>
                    <li>Have your booking ID ready: <strong>#<?php echo $appointment_id; ?></strong></li>
                </ul>
            </div>
            
            <p style="margin-top: 30px;">
                <strong>What We'll Do:</strong><br>
                Our certified technicians will thoroughly inspect and service your car's AC system to ensure optimal performance.
            </p>
            
            <center>
                <a href="<?php echo COMPANY_WEBSITE; ?>" class="button">View Appointment Details</a>
            </center>
            
            <p style="margin-top: 30px; color: #6c757d; font-size: 14px;">
                <strong>Need to reschedule?</strong><br>
                Please contact us at least 4 hours before your appointment:<br>
                📞 <strong><?php echo COMPANY_PHONE; ?></strong>
            </p>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <p><strong><?php echo COMPANY_NAME; ?></strong></p>
            <p><?php echo COMPANY_ADDRESS; ?></p>
            <p>📞 <?php echo COMPANY_PHONE; ?> | 🌐 <?php echo COMPANY_WEBSITE; ?></p>
            <p style="margin-top: 15px; font-size: 12px; opacity: 0.8;">
                We look forward to serving you!
            </p>
        </div>
    </div>
</body>
</html>