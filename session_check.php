<?php
session_start();

// Check if user is logged in
function checkLogin() {
    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
        header('Location: login.php');
        exit();
    }
}

// Check if user is admin
function checkAdminAccess() {
    checkLogin();
    if ($_SESSION['role'] !== 'Admin') {
        if ($_SESSION['role'] === 'Staff') {
            header('Location: staff_dashboard.php');
        } else {
            header('Location: login.php');
        }
        exit();
    }
}

// Check if user is staff
function checkStaffAccess() {
    checkLogin();
    if ($_SESSION['role'] !== 'Staff') {
        if ($_SESSION['role'] === 'Admin') {
            header('Location: admin_dashboard.php');
        } else {
            header('Location: login.php');
        }
        exit();
    }
}

// Get logged in user info
function getUserInfo() {
    if (isset($_SESSION['user_id'])) {
        return [
            'id' => $_SESSION['user_id'],
            'name' => $_SESSION['username'],
            'email' => $_SESSION['email'],
            'role' => $_SESSION['role']
        ];
    }
    return null;
}
?>