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

// Get all applications
$applications = [];
$result = $conn->query("SELECT ma.*, u.full_name, u.email FROM marriage_applications ma JOIN users u ON ma.user_id = u.id ORDER BY ma.created_at DESC");
while ($row = $result->fetch_assoc()) {
    $applications[] = $row;
}
?>

<div class="admin-container">
    <div class="admin-header">
        <h1><i class="fas fa-list"></i> All Applications</h1>
        <p>Manage all marriage registration applications</p>
    </div>
    
    <div class="admin-content">
        <div class="admin-section">
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
                        <?php foreach ($applications as $app): ?>
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
                                    <button class="btn-small btn-view" onclick="viewApplication(<?php echo $app['id']; ?>)">
                                        <i class="fas fa-eye"></i> View
                                    </button>
                                    <button class="btn-small btn-edit" onclick="editApplication(<?php echo $app['id']; ?>)">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function viewApplication(appId) {
    alert('Viewing application #' + appId);
}

function editApplication(appId) {
    alert('Editing application #' + appId);
}
</script>

<?php require_once '../includes/footer.php'; ?>
