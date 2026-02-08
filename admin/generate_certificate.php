<?php
require_once '../includes/functions.php';
require_once '../includes/header.php';

// Redirect to login if not logged in
if (!isLoggedIn()) {
    header("Location: ../login.php");
    exit();
}

// Check if user is admin
if (!isAdmin()) {
    header("Location: ../index.php");
    exit();
}

$app_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($app_id === 0) {
    header("Location: dashboard.php");
    exit();
}

$conn = getDBConnection();

// Get application details
$app = $conn->query("SELECT ma.*, u.full_name, u.email FROM marriage_applications ma JOIN users u ON ma.user_id = u.id WHERE ma.id = $app_id")->fetch_assoc();

if (!$app) {
    header("Location: dashboard.php");
    exit();
}

// Check if application is approved and paid
if ($app['current_status'] !== 'approved' || $app['payment_status'] !== 'paid') {
    $error = "Certificate can only be generated for approved and paid applications.";
} else {
    // Generate certificate number if not exists
    if (!$app['certificate_number']) {
        $certificate_number = 'MRC-' . date('Y') . '-' . str_pad($app_id, 6, '0', STR_PAD_LEFT);
        $conn->query("UPDATE marriage_applications SET certificate_number = '$certificate_number' WHERE id = $app_id");
        $app['certificate_number'] = $certificate_number;
    }
    
    // Create certificates directory if not exists
    $cert_dir = '../certificates';
    if (!file_exists($cert_dir)) {
        mkdir($cert_dir, 0777, true);
    }
    
    // Generate certificate HTML
    $certificate_html = generateCertificateHTML($app);
    
    // Save certificate as HTML file
    $filename = 'certificate_' . $app_id . '_' . time() . '.html';
    $filepath = $cert_dir . '/' . $filename;
    file_put_contents($filepath, $certificate_html);
    
    // Update database
    $conn->query("UPDATE marriage_applications SET certificate_generated = 1, certificate_path = 'certificates/$filename' WHERE id = $app_id");
    
    $success = "Certificate generated successfully! Certificate Number: " . $app['certificate_number'];
    
    // Redirect to view application
    header("Location: view_application.php?id=$app_id&success=certificate_generated");
    exit();
}

function generateCertificateHTML($app) {
    $html = '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Marriage Certificate - ' . $app['certificate_number'] . '</title>
    <style>
        body {
            font-family: "Georgia", serif;
            margin: 0;
            padding: 20px;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }
        .certificate {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border: 10px solid #2c3e50;
            padding: 40px;
            text-align: center;
            position: relative;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .certificate::before {
            content: "";
            position: absolute;
            top: 10px;
            left: 10px;
            right: 10px;
            bottom: 10px;
            border: 2px solid #3498db;
            pointer-events: none;
        }
        .certificate-header {
            margin-bottom: 30px;
        }
        .certificate-title {
            font-size: 36px;
            color: #2c3e50;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-weight: bold;
        }
        .certificate-subtitle {
            font-size: 18px;
            color: #7f8c8d;
            font-style: italic;
        }
        .certificate-body {
            margin: 40px 0;
        }
        .certificate-text {
            font-size: 16px;
            line-height: 1.6;
            color: #34495e;
            margin-bottom: 30px;
        }
        .couple-names {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
            margin: 20px 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .certificate-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin: 30px 0;
            text-align: left;
        }
        .detail-group {
            margin-bottom: 15px;
        }
        .detail-label {
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        .detail-value {
            color: #34495e;
        }
        .certificate-footer {
            margin-top: 40px;
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 20px;
        }
        .signature-section {
            text-align: center;
        }
        .signature-line {
            border-bottom: 1px solid #34495e;
            margin-bottom: 5px;
            height: 40px;
        }
        .signature-label {
            font-size: 12px;
            color: #7f8c8d;
        }
        .certificate-number {
            position: absolute;
            top: 20px;
            right: 20px;
            background: #3498db;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            font-weight: bold;
        }
        .seal {
            position: absolute;
            bottom: 20px;
            right: 20px;
            width: 100px;
            height: 100px;
            border: 3px solid #e74c3c;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: #e74c3c;
            font-size: 12px;
            text-align: center;
        }
        @media print {
            body { background: white; }
            .certificate { box-shadow: none; }
        }
    </style>
</head>
<body>
    <div class="certificate">
        <div class="certificate-number">' . $app['certificate_number'] . '</div>
        
        <div class="certificate-header">
            <div class="certificate-title">Marriage Certificate</div>
            <div class="certificate-subtitle">Official Certificate of Marriage Registration</div>
        </div>
        
        <div class="certificate-body">
            <p class="certificate-text">
                This is to certify that the marriage between the individuals named below has been duly registered 
                in accordance with the laws and regulations governing marriage registration in this jurisdiction.
            </p>
            
            <div class="couple-names">
                ' . strtoupper($app['groom_name']) . ' <br>
                &<br>
                ' . strtoupper($app['bride_name']) . '
            </div>
            
            <div class="certificate-details">
                <div class="detail-group">
                    <div class="detail-label">Date of Marriage:</div>
                    <div class="detail-value">' . date('F d, Y', strtotime($app['marriage_date'])) . '</div>
                </div>
                <div class="detail-group">
                    <div class="detail-label">Place of Marriage:</div>
                    <div class="detail-value">' . $app['marriage_location'] . '</div>
                </div>
                <div class="detail-group">
                    <div class="detail-label">Registration Date:</div>
                    <div class="detail-value">' . date('F d, Y', strtotime($app['registration_date'])) . '</div>
                </div>
                <div class="detail-group">
                    <div class="detail-label">Certificate Issue Date:</div>
                    <div class="detail-value">' . date('F d, Y') . '</div>
                </div>
            </div>
            
            <p class="certificate-text">
                This certificate confirms that the marriage has been officially recorded and is recognized 
                under the applicable laws. This document serves as legal proof of marriage registration.
            </p>
        </div>
        
        <div class="certificate-footer">
            <div class="signature-section">
                <div class="signature-line"></div>
                <div class="signature-label">Registrar Signature</div>
            </div>
            <div class="signature-section">
                <div class="signature-line"></div>
                <div class="signature-label">Official Seal</div>
            </div>
            <div class="signature-section">
                <div class="signature-line"></div>
                <div class="signature-label">Date Issued</div>
            </div>
        </div>
        
        <div class="seal">
            OFFICIAL<br>SEAL<br>MARRIAGE<br>REGISTRY
        </div>
    </div>
</body>
</html>';
    
    return $html;
}
?>

<div class="admin-container">
    <div class="admin-header">
        <h1><i class="fas fa-certificate"></i> Generate Certificate</h1>
        <p>Create official marriage certificate</p>
    </div>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-error">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>
    
    <?php if (isset($success)): ?>
        <div class="alert alert-success">
            <?php echo $success; ?>
        </div>
    <?php endif; ?>
    
    <div class="admin-content">
        <div class="admin-section">
            <h2>Application Details</h2>
            <div class="applications-table">
                <table>
                    <tr>
                        <td><strong>Application ID:</strong></td>
                        <td>#<?php echo $app['id']; ?></td>
                        <td><strong>Certificate Number:</strong></td>
                        <td><?php echo $app['certificate_number'] ? $app['certificate_number'] : 'Not Generated'; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Couple:</strong></td>
                        <td><?php echo $app['groom_name']; ?> & <?php echo $app['bride_name']; ?></td>
                        <td><strong>Application Status:</strong></td>
                        <td>
                            <span class="status-badge status-<?php echo $app['current_status']; ?>">
                                <?php echo ucfirst(str_replace('_', ' ', $app['current_status'])); ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Payment Status:</strong></td>
                        <td>
                            <span class="status-badge status-<?php echo $app['payment_status']; ?>">
                                <?php echo ucfirst($app['payment_status']); ?>
                            </span>
                        </td>
                        <td><strong>Certificate Status:</strong></td>
                        <td>
                            <?php if ($app['certificate_generated']): ?>
                                <span class="status-badge status-approved">
                                    <i class="fas fa-check"></i> Generated
                                </span>
                            <?php else: ?>
                                <span class="status-badge status-pending">
                                    <i class="fas fa-clock"></i> Not Generated
                                </span>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        
        <div class="form-actions">
            <button type="button" onclick="location.href='view_application.php?id=<?php echo $app_id; ?>'" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Application
            </button>
            <button type="button" onclick="location.href='dashboard.php'" class="btn btn-secondary">
                <i class="fas fa-home"></i> Dashboard
            </button>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
