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

// GET - Fetch services
function handleGet($conn) {
    $id = isset($_GET['id']) ? intval($_GET['id']) : null;
    
    $sql = "SELECT * FROM service_tb";
    
    if ($id) {
        $sql .= " WHERE Service_ID = $id";
    }
    
    $sql .= " ORDER BY Service_ID ASC";
    
    $result = $conn->query($sql);
    
    if ($result) {
        $services = [];
        while ($row = $result->fetch_assoc()) {
            $services[] = $row;
        }
        jsonResponse(true, $services, 'Services fetched successfully');
    } else {
        jsonResponse(false, null, 'Error fetching services: ' . $conn->error);
    }
}

// POST - Create service
function handlePost($conn) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $service_name = $conn->real_escape_string($data['Service_Name']);
    
    $sql = "INSERT INTO service_tb (Service_Name) VALUES ('$service_name')";
    
    if ($conn->query($sql)) {
        jsonResponse(true, ['id' => $conn->insert_id], 'Service created successfully');
    } else {
        jsonResponse(false, null, 'Error creating service: ' . $conn->error);
    }
}

// PUT - Update service
function handlePut($conn) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $id = intval($data['Service_ID']);
    $service_name = $conn->real_escape_string($data['Service_Name']);
    
    $sql = "UPDATE service_tb SET Service_Name = '$service_name' WHERE Service_ID = $id";
    
    if ($conn->query($sql)) {
        jsonResponse(true, null, 'Service updated successfully');
    } else {
        jsonResponse(false, null, 'Error updating service: ' . $conn->error);
    }
}

// DELETE - Delete service
function handleDelete($conn) {
    $id = isset($_GET['id']) ? intval($_GET['id']) : null;
    
    if (!$id) {
        jsonResponse(false, null, 'Service ID is required');
    }
    
    $sql = "DELETE FROM service_tb WHERE Service_ID = $id";
    
    if ($conn->query($sql)) {
        jsonResponse(true, null, 'Service deleted successfully');
    } else {
        jsonResponse(false, null, 'Error deleting service: ' . $conn->error);
    }
}

$conn->close();
?>