<?php
include 'db.php';
session_start();

// Security: Only librarians can edit members
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'librarian'){
    header("Location: dashboard.php");
    exit();
}

$error = "";
$success = "";

// Get member details based on the ID passed in the URL
if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $res = $conn->query("SELECT * FROM users WHERE id = '$id'");
    if ($res && $res->num_rows > 0) {
        $member = $res->fetch_assoc();
    } else {
        die("Member not found.");
    }
}

// Handle form submission
if (isset($_POST['update_member'])) {
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);

    $update_sql = "UPDATE users SET fullname='$fullname', email='$email', role='$role' WHERE id='$id'";
    
    if ($conn->query($update_sql)) {
        $success = "Member updated successfully! <a href='members.php'>Back to list</a>";
    } else {
        $error = "Error updating record: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Member | Stone Haven</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f1f5f9; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .edit-container { background: white; padding: 40px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); width: 100%; max-width: 450px; }
        .success-msg { color: #10b981; background: #d1fae5; padding: 10px; border-radius: 6px; margin-bottom: 15px; font-size: 14px; }
        .error-msg { color: #ef4444; background: #fee2e2; padding: 10px; border-radius: 6px; margin-bottom: 15px; font-size: 14px; }
        label { display: block; margin-top: 15px; font-weight: 600; color: #475569; }
        input, select { width: 100%; padding: 12px; margin-top: 5px; border: 1px solid #cbd5e1; border-radius: 8px; box-sizing: border-box; }
        .btn { width: 100%; padding: 12px; background: #3b82f6; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; margin-top: 20px; }
        .btn:hover { background: #2563eb; }
    </style>
</head>
<body>
    <div class="edit-container">
        <h2>Edit Member Details</h2>
        <?php if($success) echo "<div class='success-msg'>$success</div>"; ?>
        <?php if($error) echo "<div class='error-msg'>$error</div>"; ?>

        <form method="POST">
            <label>Full Name</label>
            <input type="text" name="fullname" value="<?php echo htmlspecialchars($member['fullname']); ?>" required>

            <label>Email Address</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($member['email']); ?>" required>

            <label>Role</label>
            <select name="role">
                <option value="student" <?php if($member['role'] == 'student') echo 'selected'; ?>>Student</option>
                <option value="librarian" <?php if($member['role'] == 'librarian') echo 'selected'; ?>>Librarian</option>
            </select>

            <button type="submit" name="update_member" class="btn">Save Changes</button>
            <div style="text-align: center; margin-top: 15px;">
                <a href="members.php" style="color: #64748b; text-decoration: none; font-size: 14px;">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>