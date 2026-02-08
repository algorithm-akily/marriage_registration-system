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

// Create images directory if not exists
$imageDir = 'certificates/images';
if (!file_exists($imageDir)) {
    mkdir($imageDir, 0777, true);
}

// Generate unique image filename
$imageFilename = 'certificate_' . $app['certificate_number'] . '_' . time() . '.png';
$imagePath = $imageDir . '/' . $imageFilename;

// Load signatures if they exist
$signatures = [];
$signaturesDir = 'certificates/signatures';
if (file_exists($signaturesDir)) {
    $files = scandir($signaturesDir);
    foreach ($files as $file) {
        if (strpos($file, 'signature_' . $app['id'] . '_') === 0) {
            $parts = explode('_', str_replace('.txt', '', $file));
            $signatures[$parts[2]] = file_get_contents($signaturesDir . '/' . $file);
        }
    }
}

// Create a certificate image with signatures using GD
function createCertificateImage($app, $imagePath, $signatures) {
    // Create image
    $width = 800;
    $height = 600;
    $image = imagecreatetruecolor($width, $height);
    
    // Set colors
    $white = imagecolorallocate($image, 255, 255, 255);
    $black = imagecolorallocate($image, 0, 0, 0);
    $gold = imagecolorallocate($image, 255, 215, 0);
    $blue = imagecolorallocate($image, 70, 130, 180);
    $red = imagecolorallocate($image, 200, 50, 50);
    
    // Fill background
    imagefill($image, 0, 0, $white);
    
    // Draw border
    imagerectangle($image, 10, 10, $width-10, $height-10, $black);
    imagerectangle($image, 15, 15, $width-15, $height-15, $gold);
    
    // Add title
    $title = "MARRIAGE CERTIFICATE";
    $titleFont = 5;
    $titleWidth = strlen($title) * imagefontwidth($titleFont);
    $titleX = ($width - $titleWidth) / 2;
    imagestring($image, $titleFont, $titleX, 40, $title, $black);
    
    // Add certificate number
    $certNumber = "Certificate #: " . $app['certificate_number'];
    $certFont = 3;
    $certWidth = strlen($certNumber) * imagefontwidth($certFont);
    $certX = ($width - $certWidth) / 2;
    imagestring($image, $certFont, $certX, 70, $certNumber, $blue);
    
    // Add couple names
    $couple = strtoupper($app['groom_name']) . " & " . strtoupper($app['bride_name']);
    $nameFont = 4;
    $nameWidth = strlen($couple) * imagefontwidth($nameFont);
    $nameX = ($width - $nameWidth) / 2;
    imagestring($image, $nameFont, $nameX, 150, $couple, $black);
    
    // Add marriage details
    $details = "Married on: " . date('F d, Y', strtotime($app['marriage_date']));
    $details .= "\nLocation: " . $app['marriage_location'];
    $details .= "\nIssued: " . date('F d, Y');
    
    $detailLines = explode("\n", $details);
    $y = 250;
    foreach ($detailLines as $line) {
        $detailWidth = strlen($line) * imagefontwidth(2);
        $detailX = ($width - $detailWidth) / 2;
        imagestring($image, 2, $detailX, $y, $line, $black);
        $y += 25;
    }
    
    // Add government seal/stamp
    if (isset($signatures['seal'])) {
        $sealText = $signatures['seal'];
        $sealFont = 2;
        $sealTextWidth = strlen($sealText) * imagefontwidth($sealFont);
        $sealX = $width - 150;
        $sealY = $height - 80;
        imagefilledrectangle($image, $sealX - 10, $sealY - 20, $sealX + 10, $sealY + 20, $gold);
        imagerectangle($image, $sealX - 5, $sealY - 15, $sealX + 5, $sealY + 15, $black);
        
        // Add seal text
        $sealLines = explode("\n", wordwrap($sealText, 20));
        $lineY = $sealY - 10;
        foreach ($sealLines as $sealLine) {
            $sealLineWidth = strlen($sealLine) * imagefontwidth($sealFont);
            $sealLineX = $sealX - ($sealLineWidth / 2);
            imagestring($image, $sealFont, $sealLineX, $lineY, $sealLine, $black);
            $lineY += 15;
        }
    } else {
        // Default seal
        $sealX = $width - 100;
        $sealY = $height - 100;
        imagefilledellipse($image, $sealX, $sealY, 60, 60, $gold);
        imageellipse($image, $sealX, $sealY, 60, 60, $black);
        
        // Add seal text
        $sealText = "OFFICIAL\nSEAL";
        $sealFont = 1;
        $sealTextWidth = strlen($sealText) * imagefontwidth($sealFont);
        $sealTextX = $sealX - ($sealTextWidth / 2);
        imagestring($image, $sealFont, $sealTextX, $sealY - 15, $sealText, $black);
    }
    
    // Add registrar signature
    if (isset($signatures['registrar'])) {
        $sigText = $signatures['registrar'];
        $sigFont = 2;
        $sigWidth = strlen($sigText) * imagefontwidth($sigFont);
        $sigX = 50;
        $sigY = $height - 50;
        imagestring($image, $sigFont, $sigX, $sigY, $sigText, $black);
        
        // Add signature line
        imageline($image, $sigX - 20, $sigY + 20, $sigX + 150, $sigY + 20, $black);
    }
    
    // Add witness signature
    if (isset($signatures['witness'])) {
        $sigText = $signatures['witness'];
        $sigFont = 1;
        $sigWidth = strlen($sigText) * imagefontwidth($sigFont);
        $sigX = $width - 200;
        $sigY = $height - 50;
        imagestring($image, $sigFont, $sigX, $sigY, $sigText, $black);
        
        // Add signature line
        imageline($image, $sigX - 20, $sigY + 15, $sigX + 120, $sigY + 15, $black);
    }
    
    // Add government stamp
    if (isset($signatures['official'])) {
        $stampText = $signatures['official'];
        $stampFont = 3;
        $stampWidth = strlen($stampText) * imagefontwidth($stampFont);
        $stampX = $width - 180;
        $stampY = 30;
        imagestring($image, $stampFont, $stampX, $stampY, $stampText, $red);
        
        // Add stamp box
        imagerectangle($image, $stampX - 10, $stampY - 15, $stampX + 120, $stampY + 25, $red);
    }
    
    // Save image
    imagepng($image, $imagePath);
    imagedestroy($image);
    
    return file_exists($imagePath);
}

// Generate the certificate image
if (createCertificateImage($app, $imagePath, $signatures)) {
    // Serve the image for download
    header('Content-Type: image/png');
    header('Content-Disposition: attachment; filename="' . $imageFilename . '"');
    header('Content-Length: ' . filesize($imagePath));
    readfile($imagePath);
    
    // Clean up temporary file
    unlink($imagePath);
    exit();
} else {
    // If conversion fails, provide fallback
    header("Location: certificate-download.php?error=image_conversion_failed");
    exit();
}
?>
