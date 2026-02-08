<div class="dashboard-header">
    <h1><i class="fas fa-tachometer-alt"></i> Dashboard</h1>
    <p>Welcome to the Marriage Registration System</p>
</div>

<div class="welcome-message">
    <h2>Hello, <?php echo $user['full_name']; ?>!</h2>
    <p>This system helps couples in unregistered marriages to obtain official recognition and legal protection.</p>
</div>

<div class="dashboard-cards">
    <div class="card card-primary">
        <div class="card-icon">
            <i class="fas fa-info-circle"></i>
        </div>
        <div class="card-content">
            <h3>What This System Does</h3>
            <ul>
                <li>Helps register unregistered marriages legally</li>
                <li>Provides step-by-step guidance for registration</li>
                <li>Tracks application status in real-time</li>
                <li>Securely stores marriage documents</li>
                <li>Connects with legal authorities</li>
            </ul>
        </div>
    </div>

    <div class="card card-success">
        <div class="card-icon">
            <i class="fas fa-list-check"></i>
        </div>
        <div class="card-content">
            <h3>System Statistics</h3>
            <div class="stats">
                <div class="stat-item">
                    <span class="stat-number"><?php echo $pendingCount; ?></span>
                    <span class="stat-label">Pending Applications</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">24/7</span>
                    <span class="stat-label">System Availability</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="quick-actions">
    <h2>Quick Actions</h2>
    <div class="action-buttons">
        <a href="marriage-form.php" class="action-btn">
            <i class="fas fa-file-signature"></i>
            <span>Start New Application</span>
        </a>
        <a href="status-check.php" class="action-btn">
            <i class="fas fa-search"></i>
            <span>Check Application Status</span>
        </a>
        <a href="profile.php" class="action-btn">
            <i class="fas fa-user-edit"></i>
            <span>Update Profile</span>
        </a>
    </div>
</div>

<div class="benefits-section">
    <h2><i class="fas fa-check-circle"></i> Benefits of Registration</h2>
    <div class="benefits-grid">
        <div class="benefit">
            <i class="fas fa-gavel"></i>
            <h3>Legal Protection</h3>
            <p>Gain legal recognition and protection for your marriage</p>
        </div>
        <div class="benefit">
            <i class="fas fa-heart"></i>
            <h3>Social Security</h3>
            <p>Access social security benefits and inheritance rights</p>
        </div>
        <div class="benefit">
            <i class="fas fa-passport"></i>
            <h3>Documentation</h3>
            <p>Obtain official marriage certificate for various purposes</p>
        </div>
        <div class="benefit">
            <i class="fas fa-handshake"></i>
            <h3>Property Rights</h3>
            <p>Secure property and financial rights for both partners</p>
        </div>
    </div>
</div>
