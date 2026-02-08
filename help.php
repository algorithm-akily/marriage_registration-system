<?php
require_once 'includes/functions.php';
require_once 'includes/header.php';
?>

<div class="application-container">
    <div class="application-header">
        <h1><i class="fas fa-question-circle"></i> Help Center</h1>
        <p>Frequently asked questions about marriage registration</p>
    </div>

    <div class="card">
        <h3><i class="fas fa-question"></i> Frequently Asked Questions</h3>
        
        <div class="application-card">
            <div class="application-header">
                <h4>How long does the registration process take?</h4>
            </div>
            <div class="application-details">
                <p>Standard processing takes 7-14 working days. Emergency processing is available for additional fees.</p>
            </div>
        </div>

        <div class="application-card">
            <div class="application-header">
                <h4>What documents are required?</h4>
            </div>
            <div class="application-details">
                <p>Birth certificates, valid identification, proof of residence, and recent photographs for both partners.</p>
            </div>
        </div>

        <div class="application-card">
            <div class="application-header">
                <h4>How do I check my application status?</h4>
            </div>
            <div class="application-details">
                <p>Log in to your account and visit the "Check Status" page to track your application progress.</p>
            </div>
        </div>

        <div class="application-card">
            <div class="application-header">
                <h4>What if I need to correct information?</h4>
            </div>
            <div class="application-details">
                <p>Submit a correction request through your dashboard with supporting documents for the changes.</p>
            </div>
        </div>

        <div class="application-card">
            <div class="application-header">
                <h4>How do I get a replacement certificate?</h4>
            </div>
            <div class="application-details">
                <p>Log in to your account and select "Request Replacement" from your profile menu.</p>
            </div>
        </div>
    </div>

    <div class="card">
        <h3><i class="fas fa-headset"></i> Still Need Help?</h3>
        <p>If you can't find the answer you're looking for, our support team is here to help.</p>
        <div class="hero-actions">
            <a href="contact.php" class="btn btn-primary">
                <i class="fas fa-envelope"></i> Contact Support
            </a>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
