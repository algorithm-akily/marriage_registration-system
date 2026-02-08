<?php
require_once __DIR__ . '/../config/database.php';

// Function to hash password
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

// Function to verify password
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

// Function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Function to check if user is admin
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

// Function to get current user data
function getCurrentUser() {
    if (isLoggedIn()) {
        return [
            'id' => $_SESSION['user_id'],
            'username' => $_SESSION['username'],
            'full_name' => $_SESSION['full_name'],
            'role' => $_SESSION['role']
        ];
    }
    return null;
}

// Function to sanitize input
function sanitizeInput($input) {
    $conn = getDBConnection();
    return mysqli_real_escape_string($conn, htmlspecialchars(trim($input)));
}

// Function to count pending applications
function countPendingApplications() {
    $conn = getDBConnection();
    $result = $conn->query("SELECT COUNT(*) as count FROM marriage_applications WHERE current_status = 'pending'");
    $row = $result->fetch_assoc();
    return $row['count'];
}
?>