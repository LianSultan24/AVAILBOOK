<?php
/**
 * Email Configuration
 * Setup your email credentials here
 */

// Email Provider Settings (using Gmail as example)
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_SECURE', 'tls'); // tls or ssl
define('SMTP_USERNAME', 'arlenepantallano5@gmail.com'); // Your Gmail address
define('SMTP_PASSWORD', 'wbapsvjvvoigtxip'); // Gmail App Password (not regular password!)
define('SMTP_FROM_EMAIL', 'arlenepantallano5@gmail.com');
define('SMTP_FROM_NAME', 'ETOK Car AC Services');

// Company Details
define('COMPANY_NAME', 'ETOK Car Air Conditioning Services');
define('COMPANY_PHONE', '09946420252');
define('COMPANY_ADDRESS', 'Macanhan, Carmen, Cagayan de Oro City,  Philippines');
define('COMPANY_WEBSITE', 'http://localhost/etok2');

/**
 * HOW TO GET GMAIL APP PASSWORD:
 * 
 * 1. Go to your Google Account settings
 * 2. Security → 2-Step Verification (enable if not yet)
 * 3. Search "App passwords"
 * 4. Select "Mail" and "Other (Custom name)"
 * 5. Enter "ETOK System"
 * 6. Click Generate
 * 7. Copy the 16-character password
 * 8. Use it as SMTP_PASSWORD above
 * 
 * IMPORTANT: 
 * - Don't use your regular Gmail password!
 * - Keep this file secure (don't share publicly)
 * - Add email_config.php to .gitignore if using Git
 */

// Email Templates Directory
define('EMAIL_TEMPLATES_DIR', __DIR__ . '/email_templates/');

// Enable/Disable Email Sending (for testing)
define('EMAIL_ENABLED', true); // Set to false to disable all emails

// Debug Mode
define('EMAIL_DEBUG', false); // Set to true for detailed error messages
?>