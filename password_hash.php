<?php
// Generate password hash
$password = 'your_password_here';
$hashed_password = password_hash($password, PASSWORD_DEFAULT);
echo "Password: " . $password . "\n";
echo "Hash: " . $hashed_password . "\n";

// To verify a password
$password_to_check = 'user_input_password';
if (password_verify($password_to_check, $hashed_password)) {
    echo "Password is correct!";
} else {
    echo "Password is incorrect!";
}
?>