<?php
require_once 'includes/functions.php';
require_once 'includes/header.php';

// Redirect to login if not logged in
if (!isLoggedIn()) {
    header("Location: login.php");
    exit();
}

$user = getCurrentUser();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $groom_name = sanitizeInput($_POST['groom_name']);
    $bride_name = sanitizeInput($_POST['bride_name']);
    $marriage_date = sanitizeInput($_POST['marriage_date']);
    $marriage_location = sanitizeInput($_POST['marriage_location']);
    $groom_address = sanitizeInput($_POST['groom_address']);
    $bride_address = sanitizeInput($_POST['bride_address']);
    $groom_phone = sanitizeInput($_POST['groom_phone']);
    $bride_phone = sanitizeInput($_POST['bride_phone']);
    $registration_date = date('Y-m-d');
    
    $conn = getDBConnection();
    $stmt = $conn->prepare("INSERT INTO marriage_applications (user_id, groom_name, bride_name, marriage_date, marriage_location, groom_address, bride_address, groom_phone, bride_phone, registration_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssssssss", $user['id'], $groom_name, $bride_name, $marriage_date, $marriage_location, $groom_address, $bride_address, $groom_phone, $bride_phone, $registration_date);
    
    if ($stmt->execute()) {
        $success = "Marriage application submitted successfully! Application ID: " . $conn->insert_id;
    } else {
        $error = "Failed to submit application: " . $conn->error;
    }
}
?>

<div class="application-container">
    <div class="application-header">
        <h1><i class="fas fa-file-signature"></i> Marriage Registration Application</h1>
        <p>Fill out this form to register your unregistered marriage</p>
    </div>
    
    <?php if (isset($success)): ?>
        <div class="alert alert-success">
            <?php echo $success; ?>
            <br>
            <a href="status-check.php">Click here to check your application status</a>
        </div>
    <?php endif; ?>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-error">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>
    
    <div class="form-guide">
        <h3><i class="fas fa-info-circle"></i> Important Information</h3>
        <ul>
            <li>Please provide accurate information</li>
            <li>You will need to submit supporting documents later</li>
            <li>Applications are processed within 7-14 working days</li>
            <li>Keep your application ID for future reference</li>
        </ul>
    </div>
    
    <form method="POST" action="" class="marriage-form">
        <div class="form-section">
            <h3><i class="fas fa-user-tie"></i> Groom Information</h3>
            <div class="form-group">
                <label for="groom_name">Full Name of Groom</label>
                <input type="text" id="groom_name" name="groom_name" required placeholder="Enter groom's full name">
            </div>
            <div class="form-group">
                <label for="groom_address">Groom's Address</label>
                <textarea id="groom_address" name="groom_address" rows="3" required placeholder="Enter groom's residential address"></textarea>
            </div>
            <div class="form-group">
                <label for="groom_phone">Groom's Phone Number</label>
                <input type="tel" id="groom_phone" name="groom_phone" required placeholder="Enter groom's phone number">
            </div>
        </div>
        
        <div class="form-section">
            <h3><i class="fas fa-user-female"></i> Bride Information</h3>
            <div class="form-group">
                <label for="bride_name">Full Name of Bride</label>
                <input type="text" id="bride_name" name="bride_name" required placeholder="Enter bride's full name">
            </div>
            <div class="form-group">
                <label for="bride_address">Bride's Address</label>
                <textarea id="bride_address" name="bride_address" rows="3" required placeholder="Enter bride's residential address"></textarea>
            </div>
            <div class="form-group">
                <label for="bride_phone">Bride's Phone Number</label>
                <input type="tel" id="bride_phone" name="bride_phone" required placeholder="Enter bride's phone number">
            </div>
        </div>
        
        <div class="form-section">
            <h3><i class="fas fa-calendar-alt"></i> Marriage Details</h3>
            <div class="form-row">
                <div class="form-group">
                    <label for="marriage_date">Date of Marriage</label>
                    <input type="date" id="marriage_date" name="marriage_date" required>
                </div>
                <div class="form-group">
                    <label for="marriage_location">Place of Marriage</label>
                    <input type="text" id="marriage_location" name="marriage_location" required placeholder="City, Country">
                </div>
            </div>
        </div>
        
        <div class="form-note">
            <p><i class="fas fa-exclamation-triangle"></i> <strong>Note:</strong> After submitting this form, you will need to provide supporting documents (ID proofs, marriage evidence) when requested by the authorities.</p>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-primary btn-large">
                <i class="fas fa-paper-plane"></i> Submit Application
            </button>
            <button type="reset" class="btn btn-secondary">
                <i class="fas fa-redo"></i> Reset Form
            </button>
        </div>
    </form>
</div>

<?php require_once 'includes/footer.php'; ?>