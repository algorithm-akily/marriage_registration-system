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

// Handle staff addition
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_staff'])) {
    $username = sanitizeInput($_POST['username']);
    $full_name = sanitizeInput($_POST['full_name']);
    $email = sanitizeInput($_POST['email']);
    $phone = sanitizeInput($_POST['phone']);
    $role = sanitizeInput($_POST['role']);
    $password = password_hash(sanitizeInput($_POST['password']), PASSWORD_DEFAULT);
    
    $stmt = $conn->prepare("INSERT INTO users (username, full_name, email, phone, role, password, created_at) VALUES (?, ?, ?, ?, ?, ?, CURDATE())");
    $stmt->bind_param("ssssss", $username, $full_name, $email, $phone, $role, $password);
    
    if ($stmt->execute()) {
        $success = "Staff member added successfully!";
    } else {
        $error = "Failed to add staff member: " . $conn->error;
    }
}

// Handle staff deletion
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $staff_id = (int)$_GET['delete'];
    
    // Don't allow admin to delete themselves
    if ($staff_id !== $_SESSION['user_id']) {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $staff_id);
        
        if ($stmt->execute()) {
            $success = "Staff member deleted successfully!";
        } else {
            $error = "Failed to delete staff member: " . $conn->error;
        }
    }
}

// Get all staff members
$staff = [];
$result = $conn->query("SELECT id, username, full_name, email, phone, role, created_at FROM users ORDER BY created_at DESC");
while ($row = $result->fetch_assoc()) {
    $staff[] = $row;
}
?>

<div class="admin-container">
    <div class="admin-header">
        <h1><i class="fas fa-users-cog"></i> Staff Management</h1>
        <p>Manage system staff and administrators</p>
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
        <div class="admin-section">
            <h2><i class="fas fa-user-plus"></i> Add New Staff Member</h2>
            <div class="profile-card">
                <form method="POST" action="">
                    <input type="hidden" name="add_staff" value="1">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" id="username" name="username" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="full_name">Full Name</label>
                            <input type="text" id="full_name" name="full_name" class="form-control" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone</label>
                            <input type="tel" id="phone" name="phone" class="form-control">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="role">Role</label>
                            <select id="role" name="role" class="form-control" required>
                                <option value="staff">Staff</option>
                                <option value="admin">Administrator</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" id="password" name="password" class="form-control" required>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-user-plus"></i> Add Staff Member
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="admin-section">
            <h2><i class="fas fa-users"></i> Current Staff Members</h2>
            <div class="applications-table">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Role</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($staff as $member): ?>
                            <tr>
                                <td>#<?php echo $member['id']; ?></td>
                                <td><?php echo $member['username']; ?></td>
                                <td><?php echo $member['full_name']; ?></td>
                                <td><?php echo $member['email']; ?></td>
                                <td><?php echo $member['phone']; ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo $member['role']; ?>">
                                        <?php echo ucfirst($member['role']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('M d, Y', strtotime($member['created_at'])); ?></td>
                                <td>
                                    <?php if ($member['id'] !== $_SESSION['user_id']): ?>
                                        <button class="btn-small btn-edit" onclick="if(confirm('Are you sure you want to delete this staff member?')) { location.href='?delete=<?php echo $member['id']; ?>'; }">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    <?php else: ?>
                                        <span class="status-badge status-admin">Current User</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
