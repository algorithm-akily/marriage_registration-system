<?php
require_once 'includes/functions.php';
require_once 'includes/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitizeInput($_POST['username']);
    $email = sanitizeInput($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $full_name = sanitizeInput($_POST['full_name']);
    
    $errors = [];
    
    // Validation
    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match!";
    }
    
    if (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters long!";
    }
    
    // Check if user exists
    $conn = getDBConnection();
    $check_stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $check_stmt->bind_param("ss", $username, $email);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows > 0) {
        $errors[] = "Username or email already exists!";
    }
    
    if (empty($errors)) {
        // Hash password and insert user
        $hashed_password = hashPassword($password);
        $stmt = $conn->prepare("INSERT INTO users (username, email, password_hash, full_name) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $username, $email, $hashed_password, $full_name);
        
        if ($stmt->execute()) {
            $success = "Registration successful! You can now login.";
        } else {
            $errors[] = "Registration failed: " . $conn->error;
        }
    }
}
?>

<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <i class="fas fa-user-plus"></i>
            <h2>Create New Account</h2>
        </div>
        
        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo $error; ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($success)): ?>
            <div class="alert alert-success">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="full_name"><i class="fas fa-user"></i> Full Name</label>
                <input type="text" id="full_name" name="full_name" required placeholder="Enter your full name">
            </div>
            
            <div class="form-group">
                <label for="username"><i class="fas fa-at"></i> Username</label>
                <input type="text" id="username" name="username" required placeholder="Choose a username">
            </div>
            
            <div class="form-group">
                <label for="email"><i class="fas fa-envelope"></i> Email</label>
                <input type="email" id="email" name="email" required placeholder="Enter your email">
            </div>
            
            <div class="form-group">
                <label for="password"><i class="fas fa-lock"></i> Password</label>
                <input type="password" id="password" name="password" required placeholder="Enter password (min 6 chars)">
            </div>
            
            <div class="form-group">
                <label for="confirm_password"><i class="fas fa-lock"></i> Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required placeholder="Confirm your password">
            </div>
            
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-user-plus"></i> Register
            </button>
        </form>
        
        <div class="auth-footer">
            <p>Already have an account? <a href="login.php">Login here</a></p>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>