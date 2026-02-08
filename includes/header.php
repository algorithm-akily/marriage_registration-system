<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Marriage Registration System</title>
    <link rel="stylesheet" href="<?php echo isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], '/admin/') !== false ? '../css/style.css' : 'css/style.css'; ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <?php 
            $isAdmin = isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], '/admin/') !== false;
            $prefix = $isAdmin ? '../' : '';
            ?>
            
            <button class="mobile-menu-toggle" id="mobileMenuToggle">
                <i class="fas fa-bars"></i>
            </button>
            
            <a href="<?php echo $prefix; ?>index.php" class="logo">
                <i class="fas fa-ring"></i> Marriage Registration
            </a>
            
            <div class="nav-menu" id="navMenu">
                <?php if (isLoggedIn()): ?>
                    <?php 
                    $isAdmin = isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], '/admin/') !== false;
                    $prefix = $isAdmin ? '../' : '';
                    ?>
                    <a href="<?php echo $prefix; ?>index.php"><i class="fas fa-home"></i> Dashboard</a>
                    <a href="<?php echo $prefix; ?>profile.php"><i class="fas fa-user"></i> Profile</a>
                    <a href="<?php echo $prefix; ?>marriage-form.php"><i class="fas fa-file-alt"></i> Apply</a>
                    <a href="<?php echo $prefix; ?>status-check.php"><i class="fas fa-search"></i> Check Status</a>
                    <a href="<?php echo $prefix; ?>certificate-download.php"><i class="fas fa-certificate"></i> My Certificates</a>
                    
                    <?php if (isAdmin()): ?>
                        <a href="<?php echo $isAdmin ? 'dashboard.php' : 'admin/dashboard.php'; ?>"><i class="fas fa-cog"></i> Admin Panel</a>
                    <?php endif; ?>
                    
                    <a href="<?php echo $prefix; ?>logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    <span class="user-welcome">Welcome, <?php echo $_SESSION['full_name']; ?></span>
                <?php else: ?>
                    <a href="<?php echo $prefix; ?>login.php"><i class="fas fa-sign-in-alt"></i> Login</a>
                    <a href="<?php echo $prefix; ?>register.php"><i class="fas fa-user-plus"></i> Register</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    
    <div class="container">