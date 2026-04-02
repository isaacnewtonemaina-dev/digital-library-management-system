<?php
session_start();
include 'db.php';

$error = "";
$success = isset($_SESSION['success']) ? $_SESSION['success'] : "";
unset($_SESSION['success']); 

// --- NEW: Read the cookie if it exists to pre-fill the username field ---
$saved_username = isset($_COOKIE['user_login']) ? htmlspecialchars($_COOKIE['user_login']) : "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_input = trim($_POST['username_email']);
    $password = $_POST['password'];
    $remember = isset($_POST['remember']); 

    $stmt = $conn->prepare("SELECT id, fullname, password, role FROM users WHERE username=? OR email=?");
    $stmt->bind_param("ss", $user_input, $user_input);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['fullname'] = $user['fullname'];
            $_SESSION['role'] = $user['role'];

            // --- UPDATED: Handle the Remember Me Cookie ---
            if ($remember) {
                // Set cookie for 30 days
                setcookie("user_login", $user_input, time() + (86400 * 30), "/"); 
            } else {
                // If they UNCHECKED it, delete the existing cookie
                if (isset($_COOKIE['user_login'])) {
                    setcookie("user_login", "", time() - 3600, "/");
                }
            }

            header("Location: dashboard.php");
            exit();
        } else {
            $error = "The password entered is incorrect.";
        }
    } else {
        $error = "No account found with those details.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Stone Haven Library</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* [Your existing styles] */
        body { margin: 0; padding: 0; height: 100vh; display: flex; align-items: center; justify-content: center; background: linear-gradient(rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.3)), url('images/stonehaven.jpg'); background-size: cover; background-position: center; background-attachment: fixed; font-family: 'Poppins', sans-serif; }
        .login-card { background: rgba(255, 255, 255, 0.15); backdrop-filter: blur(15px); -webkit-backdrop-filter: blur(15px); border: 1px solid rgba(255, 255, 255, 0.2); padding: 40px; border-radius: 20px; box-shadow: 0 20px 40px rgba(0,0,0,0.4); width: 100%; max-width: 400px; text-align: center; color: white; }
        h2 { color: #ffffff; margin-bottom: 5px; font-size: 2.2em; font-weight: 700; }
        p.subtitle { color: rgba(255, 255, 255, 0.8); margin-bottom: 30px; font-size: 1.1em; }
        .input-group { text-align: left; margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-weight: 500; color: #ffffff; }
        input[type="text"], input[type="password"] { width: 100%; padding: 16px; border: 1px solid rgba(255, 255, 255, 0.3); border-radius: 10px; font-size: 1.1em; box-sizing: border-box; background-color: rgba(255, 255, 255, 0.1); color: white; outline: none; }
        input::placeholder { color: rgba(255, 255, 255, 0.6); }
        .btn-login { width: 100%; padding: 16px; background-color: #16a34a; color: white; border: none; border-radius: 10px; font-size: 1.2em; font-weight: bold; cursor: pointer; transition: 0.3s; box-shadow: 0 4px 15px rgba(22, 163, 74, 0.3); }
        .btn-login:hover { background-color: #15803d; transform: translateY(-2px); }
        .auth-extras { margin-top: 20px; display: flex; justify-content: space-between; align-items: center; font-size: 0.9em; }
        .forgot-password { color: #fca5a5; text-decoration: none; font-weight: 600; }
        .forgot-password:hover { text-decoration: underline; }
        .footer-links { margin-top: 25px; color: rgba(255, 255, 255, 0.8); }
        .footer-links a { color: #ffffff; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>

<div class="login-card">
    <h2>Welcome Back</h2>
    <p class="subtitle">Stone Haven Digital Library</p>

    <?php if (!empty($error)): ?>
        <div style="color: #ffffff; background: rgba(185, 28, 28, 0.6); padding: 10px; border-radius: 5px; margin-bottom: 15px; border: 1px solid #b91c1c;">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <form action="login.php" method="POST">
        <div class="input-group">
            <label>Username or Email</label>
            <input type="text" name="username_email" placeholder="Enter your credentials" value="<?php echo $saved_username; ?>" required>
        </div>

        <div class="input-group">
            <label>Password</label>
            <input type="password" name="password" placeholder="••••••••" required>
        </div>

        <button type="submit" class="btn-login">LOGIN</button>

        <div class="auth-extras">
            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                <input type="checkbox" name="remember" <?php if($saved_username) echo "checked"; ?>> Remember Me
            </label>
            <a href="forgot_password.php" class="forgot-password">Forgot Password?</a>
        </div>
    </form>

    <div class="footer-links">
        Don't have an account? <a href="signup.php">Sign Up</a>
    </div>
</div>

</body>
</html>