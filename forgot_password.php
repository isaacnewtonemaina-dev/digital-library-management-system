<?php
include 'db.php';
session_start();

require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$error = "";
$success = "";

if (isset($_POST['send_link'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    
    // Check if user exists
    $res = $conn->query("SELECT id FROM users WHERE email = '$email'");
    
    if ($res && $res->num_rows > 0) {
        $token = bin2hex(random_bytes(32));
        $expiry = date("Y-m-d H:i:s", strtotime("+30 minutes"));

        // Save token to DB
        $conn->query("UPDATE users SET reset_token = '$token', token_expires = '$expiry' WHERE email = '$email'");

        $mail = new PHPMailer(true);
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            
            // USE YOUR NEW APP PASSWORD HERE (No spaces)
            $mail->Username   = 'isaacnewtonemaina@gmail.com'; 
            $mail->Password   = 'wqaqerstupftmsjp'; 
            
            // Changed to SMTPS/465 for better reliability on shared hosts
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            
            $mail->Port       = 465;                                    
            
            // Critical for Bypass SSL verification on InfinityFree
            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );

            // Recipients
            $mail->setFrom('isaacnewtonemaina@gmail.com', 'Stone Haven Library');
            $mail->addAddress($email);

            $resetLink = "https://stonehaven-library.infinityfreeapp.com/reset_new_password.php?token=" . $token;

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Request';
            $mail->Body    = "<h3>Stone Haven Library</h3>
                              <p>Click the link below to reset your password. This link is valid for 30 minutes.</p>
                              <a href='$resetLink' style='background:#3b82f6; color:white; padding:12px 20px; text-decoration:none; border-radius:5px; display:inline-block;'>Reset Password</a>
                              <p>If the button doesn't work, copy and paste this link: <br> $resetLink </p>";

            $mail->send();
            $success = "A reset link has been sent to your email!";
        } catch (Exception $e) {
            $error = "System Error: Failed to send email. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        $error = "Email not found in our records.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password | Stone Haven</title>
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f1f5f9; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .reset-box { background: white; padding: 40px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); width: 100%; max-width: 400px; text-align: center; }
        .error-msg { color: #ef4444; background: #fee2e2; padding: 10px; border-radius: 6px; margin-bottom: 15px; font-size: 14px; text-align: left; }
        .success-msg { color: #10b981; background: #d1fae5; padding: 10px; border-radius: 6px; margin-bottom: 15px; font-size: 14px; }
        input { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #cbd5e1; border-radius: 8px; box-sizing: border-box; }
        .btn { width: 100%; padding: 12px; background: #3b82f6; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: 0.3s; }
        .btn:hover { background: #2563eb; }
    </style>
</head>
<body>
    <div class="reset-box">
        <h2>Forgot Password?</h2>
        
        <?php if($error) echo "<div class='error-msg'>$error</div>"; ?>
        <?php if($success) echo "<div class='success-msg'>$success</div>"; ?>

        <p style="color: #64748b; font-size: 14px;">Enter your email to receive a secure reset link.</p>
        
        <form method="POST">
            <input type="email" name="email" placeholder="Email Address" required autofocus>
            <button type="submit" name="send_link" class="btn">Send Reset Link</button>
        </form>
        
        <div style="margin-top: 20px;">
            <a href="login.php" style="color: #3b82f6; text-decoration: none; font-size: 14px;">Back to Login</a>
        </div>
    </div>
</body>
</html>