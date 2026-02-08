<?php
require_once 'includes/functions.php';
require_once 'includes/header.php';
?>

<div class="application-container">
    <div class="application-header">
        <h1><i class="fas fa-phone-alt"></i> Contact Us</h1>
        <p>Get in touch with our marriage registration support team</p>
    </div>

    <div class="card">
        <h3><i class="fas fa-envelope"></i> Email Support</h3>
        <p><strong>Email:</strong> akilykaaya@gmail.com</p>
        <p><strong>Phone:</strong> +255 794 872 433</p>
        
        <h4><i class="fas fa-clock"></i> Response Time</h4>
        <p>We typically respond within 24-48 hours during business days.</p>
    </div>

    <div class="card">
        <h3><i class="fas fa-comments"></i> Send Us a Message</h3>
        <form action="contact-process.php" method="POST" class="auth-card">
            <div class="form-group">
                <label><i class="fas fa-user"></i> Full Name</label>
                <input type="text" name="name" required>
            </div>
            
            <div class="form-group">
                <label><i class="fas fa-envelope"></i> Email Address</label>
                <input type="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label><i class="fas fa-tag"></i> Subject</label>
                <select name="subject" required>
                    <option value="">Select a topic</option>
                    <option value="application">Application Status</option>
                    <option value="document">Document Requirements</option>
                    <option value="payment">Payment Issues</option>
                    <option value="technical">Technical Support</option>
                    <option value="general">General Inquiry</option>
                </select>
            </div>
            
            <div class="form-group">
                <label><i class="fas fa-comment"></i> Message</label>
                <textarea name="message" rows="5" required placeholder="Please describe your inquiry..."></textarea>
            </div>
            
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-paper-plane"></i> Send Message
            </button>
        </form>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
