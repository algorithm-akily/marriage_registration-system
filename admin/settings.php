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
?>

<div class="admin-container">
    <div class="admin-header">
        <h1><i class="fas fa-sliders-h"></i> System Settings</h1>
        <p>Configure system settings and preferences</p>
    </div>
    
    <div class="admin-content">
        <div class="admin-section">
            <h2>General Settings</h2>
            <div class="profile-card">
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="site_name">Site Name</label>
                        <input type="text" id="site_name" name="site_name" value="Marriage Registration System" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="site_email">Site Email</label>
                        <input type="email" id="site_email" name="site_email" value="admin@marriage.gov" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="max_applications">Max Daily Applications</label>
                        <input type="number" id="max_applications" name="max_applications" value="100" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Settings
                    </button>
                </form>
            </div>
        </div>
        
        <div class="admin-section">
            <h2>Email Settings</h2>
            <div class="profile-card">
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="smtp_host">SMTP Host</label>
                        <input type="text" id="smtp_host" name="smtp_host" placeholder="smtp.gmail.com">
                    </div>
                    
                    <div class="form-group">
                        <label for="smtp_port">SMTP Port</label>
                        <input type="number" id="smtp_port" name="smtp_port" value="587">
                    </div>
                    
                    <div class="form-group">
                        <label for="smtp_username">SMTP Username</label>
                        <input type="text" id="smtp_username" name="smtp_username">
                    </div>
                    
                    <div class="form-group">
                        <label for="smtp_password">SMTP Password</label>
                        <input type="password" id="smtp_password" name="smtp_password">
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-envelope"></i> Test Email Settings
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
