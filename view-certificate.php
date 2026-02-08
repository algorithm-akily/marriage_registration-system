<?php
require_once 'includes/functions.php';

// Redirect to login if not logged in
if (!isLoggedIn()) {
    header("Location: login.php");
    exit();
}

$user = getCurrentUser();
$conn = getDBConnection();

// Get certificate ID from URL
$cert_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($cert_id === 0) {
    header("Location: certificate-download.php");
    exit();
}

// Get application details
$app = $conn->query("SELECT * FROM marriage_applications WHERE id = $cert_id AND user_id = {$user['id']}")->fetch_assoc();

if (!$app) {
    header("Location: certificate-download.php");
    exit();
}

// Check if payment is completed
if ($app['payment_status'] !== 'paid') {
    header("Location: certificate-download.php?error=payment_required");
    exit();
}

// Check if certificate exists
if (!$app['certificate_generated'] || !$app['certificate_path'] || !file_exists($app['certificate_path'])) {
    header("Location: certificate-download.php?error=certificate_not_found");
    exit();
}

// Display the certificate
readfile($app['certificate_path']);
?>
