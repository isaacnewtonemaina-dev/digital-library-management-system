<?php
// Database connection for InfinityFree
$servername = "sql110.infinityfree.com"; // MySQL Hostname
$username = "if0_41276323";             // MySQL Username
$password = "B0mboclat2026";            // MySQL Password
$dbname = "if0_41276323_if0_41276323_stonehaven"; // Database Name

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>