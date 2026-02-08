<?php
require_once 'includes/functions.php';
require_once 'includes/header.php';

// Redirect to login if not logged in
if (!isLoggedIn()) {
    header("Location: login.php");
    exit();
}

$user = getCurrentUser();

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = sanitizeInput($_POST['full_name']);
    $email = sanitizeInput($_POST['email']);
    
    $conn = getDBConnection();
    $stmt = $conn->prepare("UPDATE users SET full_name = ?, email = ? WHERE id = ?");
    $stmt->bind_param("ssi", $full_name, $email, $user['id']);
    
    if ($stmt->execute()) {
        $_SESSION['full_name'] = $full_name;
        $_SESSION['email'] = $email;
        $success = "Profile updated successfully!";
    } else {
        $error = "Failed to update profile: " . $conn->error;
    }
}
?>

<div class="profile-container">
    <div class="profile-header">
        <h1><i class="fas fa-user-circle"></i> My Profile</h1>
        <p>Manage your account information</p>
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
    
    <div class="profile-card">
        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" value="<?php echo $user['username']; ?>" disabled>
                <small>Username cannot be changed</small>
            </div>
            
            <div class="form-group">
                <label for="full_name">Full Name</label>
                <input type="text" id="full_name" name="full_name" value="<?php echo $user['full_name']; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" value="<?php echo $_SESSION['email']; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="role">Account Type</label>
                <input type="text" id="role" value="<?php echo ucfirst($user['role']); ?>" disabled>
            </div>
            
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Update Profile
            </button>
        </form>
    </div>
    
    <div class="account-stats">
        <h3><i class="fas fa-chart-bar"></i> Account Statistics</h3>
        <?php
        $conn = getDBConnection();
        $app_stmt = $conn->prepare("SELECT COUNT(*) as total_applications FROM marriage_applications WHERE user_id = ?");
        $app_stmt->bind_param("i", $user['id']);
        $app_stmt->execute();
        $app_result = $app_stmt->get_result();
        $app_data = $app_result->fetch_assoc();
        ?>
        
        <div class="stats-grid">
            <div class="stat-box">
                <span class="stat-number"><?php echo $app_data['total_applications']; ?></span>
                <span class="stat-label">Total Applications</span>
            </div>
            <div class="stat-box">
                <span class="stat-number"><?php echo date('Y-m-d', strtotime($_SESSION['created_at'] ?? 'now')); ?></span>
                <span class="stat-label">Member Since</span>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>