<?php
require_once 'includes/functions.php';
require_once 'includes/header.php';

// If user is already logged in, show dashboard
if (isLoggedIn()) {
    $user = getCurrentUser();
    $pendingCount = countPendingApplications();
    include 'dashboard_content.php';
} else {
    // Show landing page for non-logged in users
?>

<div class="landing-hero">
    <div class="hero-content">
        <h1><i class="fas fa-ring"></i> Marriage Registration System</h1>
        <p class="hero-subtitle">Official recognition and legal protection for your marriage</p>
        <div class="hero-actions">
            <a href="register.php" class="btn btn-primary btn-large">
                <i class="fas fa-user-plus"></i> Register Now
            </a>
            <a href="login.php" class="btn btn-secondary btn-large">
                <i class="fas fa-sign-in-alt"></i> Login
            </a>
        </div>
    </div>
</div>

<div class="info-section">
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
                        <span class="stat-number">24/7</span>
                        <span class="stat-label">System Availability</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">Secure</span>
                        <span class="stat-label">Your Data</span>
                    </div>
                </div>
            </div>
        </div>
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

<div class="cta-section">
    <div class="cta-content">
        <h2>Ready to Register Your Marriage?</h2>
        <p>Join thousands of couples who have secured their marriage rights through our system</p>
        <a href="register.php" class="btn btn-primary btn-large">
            <i class="fas fa-rocket"></i> Get Started Today
        </a>
    </div>
</div>

<?php
}
require_once 'includes/footer.php';
?>