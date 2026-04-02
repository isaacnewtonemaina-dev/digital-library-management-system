<?php
include 'db.php';

// Configuration: Change these values to the user you want to reset
$user_id_to_reset = 1; 
$new_plain_password = 'StoneHaven2026!'; 

// Hash the password so it stays secure in the database
$hashed_password = password_hash($new_plain_password, PASSWORD_DEFAULT);

$sql = "UPDATE users SET password = '$hashed_password' WHERE id = '$user_id_to_reset'";

if ($conn->query($sql)) {
    echo "Password updated successfully! The new password is: " . $new_plain_password;
    echo "<br><b>IMPORTANT: Delete this file from your server immediately!</b>";
} else {
    echo "Error updating password: " . $conn->error;
}
?>