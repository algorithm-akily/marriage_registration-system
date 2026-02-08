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

// Get statistics
$stats = [];
$result = $conn->query("SELECT COUNT(*) as total FROM marriage_applications");
$stats['total'] = $result->fetch_assoc()['total'];

$result = $conn->query("SELECT COUNT(*) as pending FROM marriage_applications WHERE current_status = 'pending'");
$stats['pending'] = $result->fetch_assoc()['pending'];

$result = $conn->query("SELECT COUNT(*) as approved FROM marriage_applications WHERE current_status = 'approved'");
$stats['approved'] = $result->fetch_assoc()['approved'];

$result = $conn->query("SELECT COUNT(*) as total_users FROM users");
$stats['total_users'] = $result->fetch_assoc()['total_users'];

// New payment and certificate stats
$result = $conn->query("SELECT COUNT(*) as paid FROM marriage_applications WHERE payment_status = 'paid'");
$stats['paid'] = $result->fetch_assoc()['paid'];

$result = $conn->query("SELECT COUNT(*) as certificates FROM marriage_applications WHERE certificate_generated = 1");
$stats['certificates'] = $result->fetch_assoc()['certificates'];

// Get recent applications
$recent_apps = [];
$result = $conn->query("SELECT ma.*, u.full_name, u.email FROM marriage_applications ma JOIN users u ON ma.user_id = u.id ORDER BY ma.created_at DESC LIMIT 10");
while ($row = $result->fetch_assoc()) {
    $recent_apps[] = $row;
}
?>

<div class="admin-container">
    <div class="admin-header">
        <h1><i class="fas fa-cog"></i> Administrator Dashboard</h1>
        <p>Manage marriage registration applications and system settings</p>
    </div>
    
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
            <div class="stat-icon stat-users">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo $stats['total_users']; ?></h3>
                <p>Registered Users</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon stat-paid">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo $stats['paid']; ?></h3>
                <p>Paid Applications</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon stat-certificates">
                <i class="fas fa-certificate"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo $stats['certificates']; ?></h3>
                <p>Certificates Issued</p>
            </div>
        </div>
    </div>
    
    <div class="admin-content">
        <div class="admin-section">
            <h2><i class="fas fa-tasks"></i> Recent Applications</h2>
            
            <div class="applications-table">
                <table>
                    <thead>
                        <tr>
                            <th>App ID</th>
                            <th>Couple</th>
                            <th>Submitted By</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_apps as $app): ?>
                            <tr>
                                <td>#<?php echo $app['id']; ?></td>
                                <td><?php echo $app['groom_name']; ?> & <?php echo $app['bride_name']; ?></td>
                                <td><?php echo $app['full_name']; ?></td>
                                <td><?php echo date('M d, Y', strtotime($app['created_at'])); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo $app['current_status']; ?>">
                                        <?php echo ucfirst(str_replace('_', ' ', $app['current_status'])); ?>
                                    </span>
                                </td>
                                <td>
                                    <button class="btn-small btn-view" onclick="location.href='view_application.php?id=<?php echo $app['id']; ?>'">
                                        <i class="fas fa-eye"></i> View
                                    </button>
                                    <button class="btn-small btn-edit" onclick="location.href='view_application.php?id=<?php echo $app['id']; ?>'">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="admin-section">
            <h2><i class="fas fa-chart-line"></i> Quick Actions</h2>
            <div class="quick-actions">
                <button class="action-btn" onclick="location.href='applications.php'">
                    <i class="fas fa-list"></i>
                    <span>View All Applications</span>
                </button>
                <button class="action-btn" onclick="location.href='users.php'">
                    <i class="fas fa-user-cog"></i>
                    <span>Manage Users</span>
                </button>
                <button class="action-btn" onclick="location.href='reports.php'">
                    <i class="fas fa-chart-bar"></i>
                    <span>Generate Reports</span>
                </button>
                <button class="action-btn" onclick="location.href='settings.php'">
                    <i class="fas fa-sliders-h"></i>
                    <span>System Settings</span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function viewApplication(appId) {
    alert('Viewing application #' + appId);
    // In a real system, this would redirect to a view page
}

function editApplication(appId) {
    alert('Editing application #' + appId);
    // In a real system, this would redirect to an edit page
}
</script>

<?php require_once '../includes/footer.php'; ?>