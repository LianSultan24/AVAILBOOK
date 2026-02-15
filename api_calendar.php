<?php
require_once 'config.php';

$conn = getDBConnection();

// Get date range from query parameters
$start = isset($_GET['start']) ? $_GET['start'] : null;
$end = isset($_GET['end']) ? $_GET['end'] : null;

$sql = "SELECT 
            a.Appointment_ID,
            a.Appointment_date,
            a.Appointment_time,
            a.Status,
            a.Car_type,
            a.Car_Model,
            a.Location,
            CONCAT(c.First_name, ' ', c.Last_name) as Customer_Name,
            s.Service_Name
        FROM appointment_tb a
        LEFT JOIN customer_tb c ON a.Customer_ID = c.Customer_ID
        LEFT JOIN service_tb s ON a.Service_ID = s.Service_ID";

// Add date filter if provided
if ($start && $end) {
    $start = $conn->real_escape_string($start);
    $end = $conn->real_escape_string($end);
    $sql .= " WHERE a.Appointment_date BETWEEN '$start' AND '$end'";
}

$sql .= " ORDER BY a.Appointment_date ASC, a.Appointment_time ASC";

$result = $conn->query($sql);

$events = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        // Format for FullCalendar
        $color = '';
        switch ($row['Status']) {
            case 'pending':
                $color = '#ffc107'; // Yellow
                break;
            case 'approved':
                $color = '#17a2b8'; // Blue
                break;
            case 'completed':
                $color = '#28a745'; // Green
                break;
            case 'cancelled':
                $color = '#dc3545'; // Red
                break;
        }
        
        // Format time to 12-hour format
        $time_formatted = date('g:i A', strtotime($row['Appointment_time']));
        
        $events[] = [
            'id' => $row['Appointment_ID'],
            'title' => $row['Customer_Name'],
            'start' => $row['Appointment_date'] . 'T' . $row['Appointment_time'],
            'backgroundColor' => $color,
            'borderColor' => $color,
            'extendedProps' => [
                'customer' => $row['Customer_Name'],
                'service' => $row['Service_Name'],
                'car' => $row['Car_type'] . ' - ' . $row['Car_Model'],
                'location' => $row['Location'],
                'status' => $row['Status'],
                'time' => $time_formatted
            ]
        ];
    }
}

header('Content-Type: application/json');
echo json_encode($events);

$conn->close();
?>