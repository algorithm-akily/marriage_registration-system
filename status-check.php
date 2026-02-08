<?php
require_once 'includes/functions.php';
require_once 'includes/header.php';

// Redirect to login if not logged in
if (!isLoggedIn()) {
    header("Location: login.php");
    exit();
}

$user = getCurrentUser();
$applications = [];

$conn = getDBConnection();
$stmt = $conn->prepare("SELECT * FROM marriage_applications WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user['id']);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $applications[] = $row;
}
?>

<div class="status-container">
    <div class="status-header">
        <h1><i class="fas fa-search"></i> Check Application Status</h1>
        <p>Track the progress of your marriage registration applications</p>
    </div>
    
    <?php if (empty($applications)): ?>
        <div class="no-applications">
            <i class="fas fa-file-alt fa-3x"></i>
            <h3>No Applications Found</h3>
            <p>You haven't submitted any marriage registration applications yet.</p>
            <a href="marriage-form.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Submit New Application
            </a>
        </div>
    <?php else: ?>
        <div class="applications-list">
            <?php foreach ($applications as $app): ?>
                <div class="application-card">
                    <div class="application-header">
                        <span class="app-id">Application ID: #<?php echo $app['id']; ?></span>
                        <span class="status-badge status-<?php echo $app['current_status']; ?>">
                            <?php echo ucfirst(str_replace('_', ' ', $app['current_status'])); ?>
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
                            <span class="detail-label">Submitted On:</span>
                            <span class="detail-value"><?php echo date('F d, Y', strtotime($app['created_at'])); ?></span>
                        </div>
                        
                        <div class="detail-item">
                            <span class="detail-label">Location:</span>
                            <span class="detail-value"><?php echo $app['marriage_location']; ?></span>
                        </div>
                    </div>
                    
                    <div class="application-progress">
                        <h4>Application Progress</h4>
                        <div class="progress-track">
                            <?php
                            $steps = ['pending', 'under_review', 'approved'];
                            $current_step = array_search($app['current_status'], $steps);
                            ?>
                            <div class="progress-step <?php echo $app['current_status'] == 'pending' ? 'active' : ''; ?>">
                                <div class="step-circle">1</div>
                                <span class="step-label">Submitted</span>
                            </div>
                            <div class="progress-line"></div>
                            <div class="progress-step <?php echo $app['current_status'] == 'under_review' ? 'active' : ''; ?>">
                                <div class="step-circle">2</div>
                                <span class="step-label">Under Review</span>
                            </div>
                            <div class="progress-line"></div>
                            <div class="progress-step <?php echo $app['current_status'] == 'approved' ? 'active' : ''; ?>">
                                <div class="step-circle">3</div>
                                <span class="step-label">Approved</span>
                            </div>
                        </div>
                    </div>
                    
                    <?php if ($app['current_status'] == 'rejected'): ?>
                        <div class="application-note">
                            <p><i class="fas fa-exclamation-circle"></i> <strong>Note:</strong> Your application was rejected. Please contact support for more information.</p>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>