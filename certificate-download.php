<?php
require_once 'includes/functions.php';
require_once 'includes/header.php';

// Redirect to login if not logged in
if (!isLoggedIn()) {
    header("Location: login.php");
    exit();
}

$user = getCurrentUser();
$conn = getDBConnection();

// Get user's applications with certificates
$applications = [];
$result = $conn->query("SELECT * FROM marriage_applications WHERE user_id = {$user['id']} AND certificate_generated = 1 ORDER BY approval_date DESC");
while ($row = $result->fetch_assoc()) {
    // Only show certificates if payment is completed
    if ($row['payment_status'] === 'paid') {
        $applications[] = $row;
    }
}
?>

<div class="status-container">
    <div class="status-header">
        <h1><i class="fas fa-certificate"></i> My Certificates</h1>
        <p>Download your official marriage certificates</p>
    </div>
    
    <?php if (isset($_GET['error'])): ?>
        <?php if ($_GET['error'] === 'payment_required'): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-triangle"></i>
                <strong>Payment Required:</strong> You must complete the payment process before accessing this certificate.
                Please contact the administrator to arrange payment.
            </div>
        <?php elseif ($_GET['error'] === 'certificate_not_found'): ?>
            <div class="alert alert-error">
                <i class="fas fa-file-exclamation"></i>
                <strong>Certificate Not Found:</strong> The certificate file is not available. Please contact the administrator.
            </div>
        <?php elseif ($_GET['error'] === 'image_conversion_failed'): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-triangle"></i>
                <strong>Image Conversion Failed:</strong> Unable to convert certificate to image. 
                Please try downloading as HTML or contact the administrator.
            </div>
        <?php endif; ?>
    <?php endif; ?>
    
    <div class="admin-content">
        <?php if (empty($applications)): ?>
            <div class="no-applications">
                <i class="fas fa-certificate"></i>
                <h3>No Certificates Available</h3>
                <p>You don't have any paid marriage certificates yet.</p>
                <p>Certificates are generated after your marriage application is approved and payment is processed.</p>
                <a href="status-check.php" class="btn btn-primary">
                    <i class="fas fa-search"></i> Check Application Status
                </a>
            </div>
            
            <?php
            // Check if there are unpaid certificates
            $unpaid_result = $conn->query("SELECT COUNT(*) as count FROM marriage_applications WHERE user_id = {$user['id']} AND certificate_generated = 1 AND payment_status != 'paid'");
            $unpaid_count = $unpaid_result->fetch_assoc()['count'];
            
            if ($unpaid_count > 0): ?>
                <div class="alert alert-error" style="margin-top: 20px;">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Payment Required:</strong> You have <?php echo $unpaid_count; ?> certificate(s) ready but payment is required before download. 
                    Please complete the payment process to access your certificates.
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="applications-list">
                <?php foreach ($applications as $app): ?>
                    <div class="application-card">
                        <div class="application-header">
                            <div>
                                <h3>Certificate #<?php echo $app['certificate_number']; ?></h3>
                                <p class="app-id">Application ID: #<?php echo $app['id']; ?></p>
                            </div>
                            <span class="status-badge status-approved">
                                <i class="fas fa-check"></i> Available
                            </span>
                        </div>
                        
                        <div class="application-details">
                            <div class="detail-item">
                                <span class="detail-label">Couple:</span>
                                <span class="detail-value"><?php echo $app['groom_name']; ?> & <?php echo $app['bride_name']; ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Marriage Date:</span>
                                <span class="detail-value"><?php echo date('F d, Y', strtotime($app['marriage_date'])); ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Marriage Location:</span>
                                <span class="detail-value"><?php echo $app['marriage_location']; ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Certificate Issue Date:</span>
                                <span class="detail-value"><?php echo date('F d, Y', strtotime($app['approval_date'])); ?></span>
                            </div>
                        </div>
                        
                        <div class="application-actions">
                            <?php if ($app['certificate_path'] && file_exists($app['certificate_path'])): ?>
                                <a href="view-certificate.php?id=<?php echo $app['id']; ?>" target="_blank" class="btn btn-primary">
                                    <i class="fas fa-eye"></i> View Certificate
                                </a>
                                <a href="<?php echo $app['certificate_path']; ?>" download="certificate_<?php echo $app['certificate_number']; ?>.html" class="btn btn-secondary">
                                    <i class="fas fa-download"></i> Download (HTML)
                                </a>
                                
                                <!-- Print button -->
                                <button onclick="printCertificate('<?php echo $app['certificate_path']; ?>')" class="btn btn-secondary">
                                    <i class="fas fa-print"></i> Print Certificate
                                </button>
                                
                                <!-- Convert to PDF option -->
                                <button onclick="convertToPDF('<?php echo $app['certificate_path']; ?>', '<?php echo $app['certificate_number']; ?>')" class="btn btn-primary">
                                    <i class="fas fa-file-pdf"></i> Convert to PDF
                                </button>
                                
                                <!-- Download as Image -->
                                <a href="simple-image-convert.php?id=<?php echo $app['id']; ?>" class="btn btn-primary">
                                    <i class="fas fa-image"></i> Download as Image
                                </a>
                            <?php else: ?>
                                <div class="alert alert-error">
                                    Certificate file not found. Please contact administrator.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Hidden iframe for printing -->
<iframe id="printFrame" style="display: none;"></iframe>

<script>
function printCertificate(certificatePath) {
    const printFrame = document.getElementById('printFrame');
    printFrame.src = certificatePath;
    printFrame.onload = function() {
        printFrame.contentWindow.print();
    };
}

function convertToPDF(certificatePath, certificateNumber) {
    // Show instructions for PDF conversion
    alert('To convert your certificate to PDF:\n\n1. Click "View Certificate" to open it in a new tab\n2. Use your browser\'s "Print" function (Ctrl+P or Cmd+P)\n3. Select "Save as PDF" as the destination\n4. Save the file with name: certificate_' + certificateNumber + '.pdf\n\nThis will create a high-quality PDF version of your certificate!');
}

function downloadAsImage(certificatePath, certificateNumber) {
    // Show instructions for image conversion
    alert('To download your certificate as an image:\n\n1. Click "View Certificate" to open it in a new tab\n2. Take a screenshot of the certificate:\n   - Windows: Use Snipping Tool or Win+Shift+S\n   - Mac: Use Cmd+Shift+4\n   - Mobile: Use screenshot function\n3. Save the image as: certificate_' + certificateNumber + '.png\n\nFor best quality, zoom in before taking the screenshot!');
}

// Auto-refresh every 30 seconds to check for new certificates
setTimeout(function() {
    location.reload();
}, 30000);
</script>

<style>
.application-actions {
    margin-top: 20px;
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.application-actions .btn {
    text-decoration: none;
    padding: 10px 20px;
    border-radius: 5px;
    font-size: 14px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s;
    border: none;
    cursor: pointer;
}

.application-actions .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

@media (max-width: 768px) {
    .application-actions {
        flex-direction: column;
    }
    
    .application-actions .btn {
        width: 100%;
        justify-content: center;
    }
}
</style>

<?php require_once 'includes/footer.php'; ?>
