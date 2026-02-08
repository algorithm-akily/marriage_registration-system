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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = sanitizeInput($_POST['status']);
    $payment_status = sanitizeInput($_POST['payment_status']);
    $admin_notes = sanitizeInput($_POST['admin_notes']);
    $certificate_fee = isset($_POST['certificate_fee']) ? floatval($_POST['certificate_fee']) : 5000.00;
    $payment_method = sanitizeInput($_POST['payment_method']);
    
    $update_sql = "UPDATE marriage_applications SET current_status = ?, payment_status = ?, admin_notes = ?, certificate_fee = ?, payment_method = ?";
    
    // Set payment date if status changed to paid
    if ($payment_status === 'paid' && $app['payment_status'] !== 'paid') {
        $update_sql .= ", payment_date = CURDATE()";
    }
    
    // Set approval/rejection dates
    if ($status === 'approved' && $app['current_status'] !== 'approved') {
        $update_sql .= ", approval_date = CURDATE()";
    } elseif ($status === 'rejected' && $app['current_status'] !== 'rejected') {
        $update_sql .= ", review_date = CURDATE()";
    } elseif ($status === 'under_review' && $app['current_status'] === 'submitted') {
        $update_sql .= ", review_date = CURDATE()";
    }
    
    $update_sql .= " WHERE id = ?";
    
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("ssssdi", $status, $payment_status, $admin_notes, $certificate_fee, $payment_method, $app_id);
    
    if ($stmt->execute()) {
        $success = "Application updated successfully!";
        
        // Refresh application data
        $app = $conn->query("SELECT ma.*, u.full_name, u.email FROM marriage_applications ma JOIN users u ON ma.user_id = u.id WHERE ma.id = $app_id")->fetch_assoc();
        
        // If approved and paid, generate certificate
        if ($status === 'approved' && $payment_status === 'paid' && !$app['certificate_generated']) {
            header("Location: generate_certificate.php?id=$app_id");
            exit();
        }
    } else {
        $error = "Failed to update application: " . $conn->error;
    }
}
?>

<div class="admin-container">
    <div class="admin-header">
        <h1><i class="fas fa-file-alt"></i> Application Details</h1>
        <p>Review and manage marriage registration application</p>
    </div>
    
    <?php if (isset($success)): ?>
        <div class="alert alert-success">
            <?php echo $success; ?>
        </div>
    <?php endif; ?>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-error">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>
    
    <div class="admin-content">
        <form method="POST" action="">
            <div class="admin-section">
                <h2><i class="fas fa-user"></i> Applicant Information</h2>
                <div class="applications-table">
                    <table>
                        <tr>
                            <td><strong>Application ID:</strong></td>
                            <td>#<?php echo $app['id']; ?></td>
                            <td><strong>Submitted By:</strong></td>
                            <td><?php echo $app['full_name']; ?> (<?php echo $app['email']; ?>)</td>
                        </tr>
                        <tr>
                            <td><strong>Registration Date:</strong></td>
                            <td><?php echo date('M d, Y', strtotime($app['registration_date'])); ?></td>
                            <td><strong>Application Status:</strong></td>
                            <td>
                                <span class="status-badge status-<?php echo $app['current_status']; ?>">
                                    <?php echo ucfirst(str_replace('_', ' ', $app['current_status'])); ?>
                                </span>
                            </td>
                            <td><strong>Payment Status:</strong></td>
                            <td>
                                <span class="status-badge status-<?php echo $app['payment_status']; ?>">
                                    <?php echo ucfirst($app['payment_status']); ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Certificate Fee:</strong></td>
                            <td><?php echo number_format($app['certificate_fee'] ?: 5000, 2); ?> TSh</td>
                            <td><strong>Payment Method:</strong></td>
                            <td><?php echo $app['payment_method'] ? ucfirst(str_replace('_', ' ', $app['payment_method'])) : 'Not Set'; ?></td>
                        </tr>
                        <?php if ($app['payment_date']): ?>
                        <tr>
                            <td><strong>Payment Date:</strong></td>
                            <td><?php echo date('M d, Y', strtotime($app['payment_date'])); ?></td>
                            <td><strong>Certificate Number:</strong></td>
                            <td><?php echo $app['certificate_number'] ?: 'Not Generated'; ?></td>
                        </tr>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
            
            <div class="admin-section">
                <h2><i class="fas fa-ring"></i> Couple Information</h2>
                <div class="applications-table">
                    <table>
                        <tr>
                            <td><strong>Groom Name:</strong></td>
                            <td><?php echo $app['groom_name']; ?></td>
                            <td><strong>Bride Name:</strong></td>
                            <td><?php echo $app['bride_name']; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Groom Address:</strong></td>
                            <td><?php echo $app['groom_address']; ?></td>
                            <td><strong>Bride Address:</strong></td>
                            <td><?php echo $app['bride_address']; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Groom Phone:</strong></td>
                            <td><?php echo $app['groom_phone']; ?></td>
                            <td><strong>Bride Phone:</strong></td>
                            <td><?php echo $app['bride_phone']; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Marriage Date:</strong></td>
                            <td><?php echo date('M d, Y', strtotime($app['marriage_date'])); ?></td>
                            <td><strong>Marriage Location:</strong></td>
                            <td><?php echo $app['marriage_location']; ?></td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <div class="admin-section">
                <h2><i class="fas fa-cogs"></i> Admin Actions</h2>
                <div class="profile-card">
                    <div class="form-group">
                        <label for="status">Application Status</label>
                        <select id="status" name="status" class="form-control" required>
                            <option value="submitted" <?php echo $app['current_status'] === 'submitted' ? 'selected' : ''; ?>>Submitted</option>
                            <option value="under_review" <?php echo $app['current_status'] === 'under_review' ? 'selected' : ''; ?>>Under Review</option>
                            <option value="approved" <?php echo $app['current_status'] === 'approved' ? 'selected' : ''; ?>>Approved</option>
                            <option value="rejected" <?php echo $app['current_status'] === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="payment_status">Payment Status</label>
                        <select id="payment_status" name="payment_status" class="form-control" required>
                            <option value="pending" <?php echo $app['payment_status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="paid" <?php echo $app['payment_status'] === 'paid' ? 'selected' : ''; ?>>Paid</option>
                            <option value="waived" <?php echo $app['payment_status'] === 'waived' ? 'selected' : ''; ?>>Waived</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="certificate_fee">Certificate Fee (TSh)</label>
                        <input type="number" id="certificate_fee" name="certificate_fee" class="form-control" value="<?php echo $app['certificate_fee'] ?: '5000.00'; ?>" step="0.01" min="0">
                    </div>
                    
                    <div class="form-group">
                        <label for="payment_method">Payment Method</label>
                        <select id="payment_method" name="payment_method" class="form-control">
                            <option value="">Select Payment Method</option>
                            <option value="cash" <?php echo $app['payment_method'] === 'cash' ? 'selected' : ''; ?>>Cash</option>
                            <option value="bank_transfer" <?php echo $app['payment_method'] === 'bank_transfer' ? 'selected' : ''; ?>>Bank Transfer</option>
                            <option value="mobile_money" <?php echo $app['payment_method'] === 'mobile_money' ? 'selected' : ''; ?>>Mobile Money</option>
                            <option value="credit_card" <?php echo $app['payment_method'] === 'credit_card' ? 'selected' : ''; ?>>Credit Card</option>
                            <option value="hand" <?php echo $app['payment_method'] === 'hand' ? 'selected' : ''; ?>>Hand Payment</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="admin_notes">Admin Notes</label>
                        <textarea id="admin_notes" name="admin_notes" rows="4" class="form-control"><?php echo $app['admin_notes']; ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label><strong>Certificate Status:</strong></label>
                        <div>
                            <?php if ($app['certificate_generated']): ?>
                                <span class="status-badge status-approved">
                                    <i class="fas fa-check"></i> Certificate Generated
                                </span>
                                <?php if ($app['certificate_path']): ?>
                                    <a href="../<?php echo $app['certificate_path']; ?>" target="_blank" class="btn-small btn-view" style="margin-left: 10px;">
                                        <i class="fas fa-download"></i> Download
                                    </a>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="status-badge status-pending">
                                    <i class="fas fa-clock"></i> Not Generated
                                </span>
                                <?php if ($app['current_status'] === 'approved' && $app['payment_status'] === 'paid'): ?>
                                    <button type="button" onclick="location.href='generate_certificate.php?id=<?php echo $app_id; ?>'" class="btn-small btn-view" style="margin-left: 10px;">
                                        <i class="fas fa-file-pdf"></i> Generate Certificate
                                    </button>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Application
                        </button>
                        <button type="button" onclick="location.href='dashboard.php'" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Dashboard
                        </button>
                        
                        <?php if ($app['current_status'] === 'approved' && $app['payment_status'] === 'paid' && !$app['certificate_generated']): ?>
                            <button type="button" onclick="location.href='generate_certificate.php?id=<?php echo $app_id; ?>'" class="btn btn-primary" style="background: linear-gradient(45deg, #27ae60, #229954);">
                                <i class="fas fa-certificate"></i> Generate Certificate
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
