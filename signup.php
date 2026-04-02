<?php
session_start();
include 'db.php';

$error = "";
$success = "";

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // CSRF Protection Check
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token validation failed.");
    }

    $fullname = trim($_POST['fullname']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = $_POST['role'];

    // BACKEND ENFORCEMENT: Check if password meets all requirements
    $uppercase = preg_match('@[A-Z]@', $password);
    $number    = preg_match('@[0-9]@', $password);
    $special   = preg_match('@[^\w]@', $password);

    if (strlen($password) < 8 || !$uppercase || !$number || !$special) {
        $error = "Password must be at least 8 characters and include uppercase, a number, and a special character.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } else {
        $stmt = $conn->prepare("SELECT id FROM users WHERE username=? OR email=?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $stmt->store_result();
        
        if($stmt->num_rows > 0){
            $error = "Username or Email already exists!";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (fullname, username, email, password, role) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $fullname, $username, $email, $hashed_password, $role);

            if($stmt->execute()){
                $_SESSION['success'] = "Account created successfully! Please login.";
                header("Location: login.php");
                exit();
            } else {
                $error = "Something went wrong! Please check your database connection.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up | Stone Haven Library</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), 
                        url('images/repuding.jpg'); 
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            font-family: 'Poppins', sans-serif;
            padding: 20px;
        }

        .form-wrapper {
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.4);
            width: 100%;
            max-width: 500px;
            text-align: center;
            color: white;
        }

        h2 { font-size: 2em; font-weight: 700; margin-bottom: 25px; }

        .input-group { text-align: left; margin-bottom: 15px; position: relative; }
        label { display: block; margin-bottom: 5px; font-weight: 500; font-size: 0.85em; }

        input, select {
            width: 100%;
            padding: 12px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 8px;
            font-size: 0.95em;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            outline: none;
        }

        select option { background: #1e293b; color: white; }

        input:focus, select:focus {
            border-color: #ef4444;
            background: rgba(255, 255, 255, 0.2);
        }

        .strength-meter {
            height: 4px;
            width: 100%;
            background: rgba(255, 255, 255, 0.1);
            margin-top: 8px;
            border-radius: 2px;
            overflow: hidden;
            display: none;
        }
        .strength-meter-fill {
            height: 100%;
            width: 0%;
            transition: width 0.3s ease, background-color 0.3s ease;
        }
        .strength-text {
            font-size: 11px;
            margin-top: 4px;
            text-align: right;
            font-weight: 600;
        }

        /* Requirements List Styling */
        .pw-requirements {
            text-align: left;
            font-size: 11px;
            margin-top: 10px;
            color: rgba(255,255,255,0.6);
            list-style: none;
        }
        .pw-requirements li { margin-bottom: 2px; transition: 0.3s; }
        .pw-requirements li.valid { color: #10b981; }

        button {
            width: 100%;
            padding: 14px;
            background-color: #ef4444;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1.1em;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 15px;
        }

        button:hover:not(:disabled) { background-color: #dc2626; transform: translateY(-2px); }
        
        button:disabled {
            background-color: #666;
            cursor: not-allowed;
            opacity: 0.5;
        }

        .login-link { margin-top: 20px; font-size: 0.9em; color: rgba(255,255,255,0.7); }
        .login-link a { color: white; text-decoration: none; font-weight: 700; }

        .error-msg {
            background: rgba(239, 68, 68, 0.2);
            color: #fca5a5;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 15px;
            font-size: 13px;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }
    </style>
</head>
<body>

<div class="form-wrapper">
    <h2>Create Account</h2>

    <?php if($error): ?>
        <div class="error-msg"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" action="signup.php" id="signupForm">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

        <div class="input-group">
            <label>Full Name</label>
            <input type="text" name="fullname" placeholder="Enter your full name" required>
        </div>

        <div class="input-group">
            <label>Username</label>
            <input type="text" name="username" placeholder="Choose a username" required>
        </div>

        <div class="input-group">
            <label>Email Address</label>
            <input type="email" name="email" placeholder="Enter your email" required>
        </div>

        <div class="input-group">
            <label>Password</label>
            <input type="password" name="password" id="password" placeholder="Create a password" required>
            <div class="strength-meter" id="strengthMeter">
                <div class="strength-meter-fill" id="strengthFill"></div>
            </div>
            <div class="strength-text" id="strengthText"></div>
            
            <ul class="pw-requirements">
                <li id="req-len">• Min 8 characters</li>
                <li id="req-cap">• One uppercase letter</li>
                <li id="req-num">• One number</li>
                <li id="req-spec">• One special character</li>
            </ul>
        </div>

        <div class="input-group">
            <label>Confirm Password</label>
            <input type="password" name="confirm_password" placeholder="Repeat your password" required>
        </div>

        <div class="input-group">
            <label>Select Role</label>
            <select name="role" required>
                <option value="">Choose Role</option>
                <option value="student">Student</option>
                <option value="lecturer">Lecturer</option>
                <option value="librarian">Librarian</option>
            </select>
        </div>

        <button type="submit" id="submitBtn" disabled>Create Account</button>
    </form>

    <p class="login-link">
        Already have an account? <a href="login.php">Login here</a>
    </p>
</div>

<script>
    const passwordInput = document.getElementById('password');
    const strengthMeter = document.getElementById('strengthMeter');
    const strengthFill = document.getElementById('strengthFill');
    const strengthText = document.getElementById('strengthText');
    const submitBtn = document.getElementById('submitBtn');

    // Requirement elements
    const reqLen = document.getElementById('req-len');
    const reqCap = document.getElementById('req-cap');
    const reqNum = document.getElementById('req-num');
    const reqSpec = document.getElementById('req-spec');

    passwordInput.addEventListener('input', () => {
        const val = passwordInput.value;
        let score = 0;

        if (val.length === 0) {
            strengthMeter.style.display = 'none';
            strengthText.textContent = '';
            submitBtn.disabled = true;
            return;
        }

        strengthMeter.style.display = 'block';

        // Validation Checks
        const isLen = val.length >= 8;
        const isCap = /[A-Z]/.test(val);
        const isNum = /[0-9]/.test(val);
        const isSpec = /[^\w]/.test(val);

        // Update Requirement List UI
        reqLen.className = isLen ? 'valid' : '';
        reqCap.className = isCap ? 'valid' : '';
        reqNum.className = isNum ? 'valid' : '';
        reqSpec.className = isSpec ? 'valid' : '';

        if (isLen) score++;
        if (isCap) score++;
        if (isNum) score++;
        if (isSpec) score++;

        // Update Strength Meter Bar
        switch (score) {
            case 1:
                strengthFill.style.width = '25%';
                strengthFill.style.backgroundColor = '#ef4444';
                strengthText.textContent = 'Weak';
                break;
            case 2:
            case 3:
                strengthFill.style.width = '60%';
                strengthFill.style.backgroundColor = '#f59e0b';
                strengthText.textContent = 'Medium';
                break;
            case 4:
                strengthFill.style.width = '100%';
                strengthFill.style.backgroundColor = '#10b981';
                strengthText.textContent = 'Strong';
                break;
        }

        // ENFORCEMENT: Only enable button if score is 4 (all requirements met)
        submitBtn.disabled = (score < 4);
    });
</script>

</body>
</html>