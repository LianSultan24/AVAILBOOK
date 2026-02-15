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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
        .success-badge {
            background: #28a745;
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            display: inline-block;
            margin-bottom: 20px;
            font-weight: bold;
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
        .highlight {
            background: #fff3cd;
            padding: 15px;
            border-left: 4px solid #ffc107;
            border-radius: 4px;
            margin: 20px 0;
        }
        .highlight strong {
            color: #856404;
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
        .social-links {
            margin-top: 15px;
        }
        .social-links a {
            color: white;
            text-decoration: none;
            margin: 0 10px;
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
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <h1>✅ Appointment Confirmed!</h1>
            <p><?php echo COMPANY_NAME; ?></p>
        </div>
        
        <!-- Content -->
        <div class="content">
            <div class="greeting">
                Hello <strong><?php echo htmlspecialchars($customer_name); ?></strong>,
            </div>
            
            <div class="success-badge">
                ✓ YOUR APPOINTMENT HAS BEEN APPROVED
            </div>
            
            <p>Great news! Your appointment has been confirmed and scheduled. We're looking forward to servicing your vehicle.</p>
            
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
            
            <!-- Important Notice -->
            <div class="highlight">
                <strong>⚠️ Important:</strong> Please arrive 10 minutes before your scheduled time. Bring this confirmation email with you.
            </div>
            
            <p style="margin-top: 30px;">
                <strong>What to Expect:</strong><br>
                • Our technicians will inspect your car's AC system<br>
                • We'll provide a detailed assessment and quote<br>
                • All work is guaranteed with professional service<br>
            </p>
            
            <center>
                <a href="<?php echo COMPANY_WEBSITE; ?>" class="button">View My Appointments</a>
            </center>
            
            <p style="margin-top: 30px; color: #6c757d; font-size: 14px;">
                Need to reschedule or cancel? Contact us at <strong><?php echo COMPANY_PHONE; ?></strong>
            </p>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <p><strong><?php echo COMPANY_NAME; ?></strong></p>
            <p><?php echo COMPANY_ADDRESS; ?></p>
            <p>📞 <?php echo COMPANY_PHONE; ?> | 🌐 <?php echo COMPANY_WEBSITE; ?></p>
            <p style="margin-top: 15px; font-size: 12px; opacity: 0.8;">
                You received this email because you booked an appointment with us.
            </p>
        </div>
    </div>
</body>
</html>