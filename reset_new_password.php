<?php
include 'db.php';
session_start();

$error = "";
$success = "";
$show_form = false;

// 1. Verify the token from the URL link
if (isset($_GET['token'])) {
    $token = mysqli_real_escape_string($conn, $_GET['token']);
    $now = date("Y-m-d H:i:s");

    // Fetch user ID using the token found in the URL link
    $res = $conn->query("SELECT id FROM users WHERE reset_token = '$token' AND token_expires > '$now'");

    if ($res && $res->num_rows > 0) {
        $user = $res->fetch_assoc();
        $user_id = $user['id']; 
        $show_form = true;
        
        // 2. Handle the New Password submission
        if (isset($_POST['update_password'])) {
            $new_password = $_POST['password']; 
            $confirm_pass = $_POST['confirm_password'];

            // UPGRADED LOGIC: Check for match and hash password
            if ($new_password !== $confirm_pass) {
                $error = "Passwords do not match.";
            } else {
                // Hash the password for security
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                
                // Update DB and clear the token so it can't be used again
                $sql = "UPDATE users SET password = '$hashed_password', reset_token = NULL, token_expires = NULL WHERE id = '$user_id'";
                
                if ($conn->query($sql)) {
                    // Success message with a direct link back to login
                    $success = "Password updated successfully! <a href='login.php' style='color:#10b981; font-weight:bold; text-decoration:none;'>Click here to login</a>";
                    $show_form = false; 
                } else {
                    $error = "Database error. Please try again.";
                }
            }
        }
    } else {
        $error = "This reset link is invalid or has expired.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>New Password | Stone Haven</title>
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f1f5f9; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .reset-box { background: white; padding: 40px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); width: 100%; max-width: 400px; text-align: center; }
        .error-msg { color: #ef4444; background: #fee2e2; padding: 10px; border-radius: 6px; margin-bottom: 15px; font-size: 14px; }
        .success-msg { color: #10b981; background: #d1fae5; padding: 10px; border-radius: 6px; margin-bottom: 15px; font-size: 14px; line-height: 1.6; }
        input { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #cbd5e1; border-radius: 8px; box-sizing: border-box; }
        .btn { width: 100%; padding: 12px; background: #10b981; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: 0.3s; }
        .btn:hover { background: #059669; }
    </style>
</head>
<body>
<div class="reset-box">
    <h2>Set New Password</h2>
    
    <?php if($error) echo "<div class='error-msg'>$error</div>"; ?>
    
    <?php if($success) echo "<div class='success-msg'>$success</div>"; ?>

    <?php if($show_form): ?>
        <p style="color: #64748b; font-size: 14px;">Enter your new secure password below.</p>
        <form method="POST">
            <input type="password" name="password" placeholder="New Password" required minlength="6">
            <input type="password" name="confirm_password" placeholder="Confirm New Password" required>
            <button type="submit" name="update_password" class="btn">Update Password</button>
        </form>
    <?php endif; ?>

    <?php if(!$show_form && !$success): ?>
        <div style="margin-top: 20px;">
            <a href="forgot_password.php" style="color: #3b82f6; text-decoration: none; font-size: 14px;">Request a new link</a>
        </div>
    <?php endif; ?>
</div>
</body>
</html>