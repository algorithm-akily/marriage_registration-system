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

$conn = getDBConnection();

// Get statistics for reports
$stats = [];
$result = $conn->query("SELECT COUNT(*) as total FROM marriage_applications");
$stats['total'] = $result->fetch_assoc()['total'];

$result = $conn->query("SELECT COUNT(*) as pending FROM marriage_applications WHERE current_status = 'pending'");
$stats['pending'] = $result->fetch_assoc()['pending'];

$result = $conn->query("SELECT COUNT(*) as approved FROM marriage_applications WHERE current_status = 'approved'");
$stats['approved'] = $result->fetch_assoc()['approved'];

$result = $conn->query("SELECT COUNT(*) as rejected FROM marriage_applications WHERE current_status = 'rejected'");
$stats['rejected'] = $result->fetch_assoc()['rejected'];
?>

<div class="admin-container">
    <div class="admin-header">
        <h1><i class="fas fa-chart-bar"></i> Generate Reports</h1>
        <p>View system statistics and generate reports</p>
    </div>
    
    <div class="admin-content">
        <div class="admin-section">
            <h2>System Statistics</h2>
            <div class="admin-stats">
                <div class="stat-card">
                    <div class="stat-icon stat-total">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo $stats['total']; ?></h3>
                        <p>Total Applications</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon stat-pending">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo $stats['pending']; ?></h3>
                        <p>Pending Applications</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon stat-approved">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo $stats['approved']; ?></h3>
                        <p>Approved Applications</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon stat-rejected">
                        <i class="fas fa-times-circle"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo $stats['rejected']; ?></h3>
                        <p>Rejected Applications</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="admin-section">
            <h2>Report Options</h2>
            <div class="quick-actions">
                <button class="action-btn" onclick="generateReport('monthly')">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Monthly Report</span>
                </button>
                <button class="action-btn" onclick="generateReport('quarterly')">
                    <i class="fas fa-calendar-week"></i>
                    <span>Quarterly Report</span>
                </button>
                <button class="action-btn" onclick="generateReport('yearly')">
                    <i class="fas fa-calendar"></i>
                    <span>Yearly Report</span>
                </button>
                <button class="action-btn" onclick="generateReport('custom')">
                    <i class="fas fa-filter"></i>
                    <span>Custom Report</span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function generateReport(type) {
    alert('Generating ' + type + ' report...');
}
</script>

<?php require_once '../includes/footer.php'; ?>
