<?php
require_once 'config.php';

$conn = getDBConnection();

// Get statistics
$stats = [];

// Total appointments
$result = $conn->query("SELECT COUNT(*) as total FROM appointment_tb");
$stats['total'] = $result->fetch_assoc()['total'];

// Pending appointments
$result = $conn->query("SELECT COUNT(*) as pending FROM appointment_tb WHERE Status = 'pending'");
$stats['pending'] = $result->fetch_assoc()['pending'];

// Approved appointments
$result = $conn->query("SELECT COUNT(*) as approved FROM appointment_tb WHERE Status = 'approved'");
$stats['approved'] = $result->fetch_assoc()['approved'];

// Completed appointments
$result = $conn->query("SELECT COUNT(*) as completed FROM appointment_tb WHERE Status = 'completed'");
$stats['completed'] = $result->fetch_assoc()['completed'];

// Cancelled appointments
$result = $conn->query("SELECT COUNT(*) as cancelled FROM appointment_tb WHERE Status = 'cancelled'");
$stats['cancelled'] = $result->fetch_assoc()['cancelled'];

// Total customers
$result = $conn->query("SELECT COUNT(*) as customers FROM customer_tb");
$stats['customers'] = $result->fetch_assoc()['customers'];

// Total users/staff
$result = $conn->query("SELECT COUNT(*) as users FROM users");
$stats['users'] = $result->fetch_assoc()['users'];

// Total services
$result = $conn->query("SELECT COUNT(*) as services FROM service_tb");
$stats['services'] = $result->fetch_assoc()['services'];

jsonResponse(true, $stats, 'Statistics fetched successfully');

$conn->close();
?>