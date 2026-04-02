<?php
session_start();
session_destroy();
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="logout.css">
    <meta http-equiv="refresh" content="2;url=login.php">
</head>
<body>
    <div class="logout-container">
        <div class="loader"></div>
        <br>
        <h2>Logging Out</h2>
        <p>Thank you for using Stone Haven Library. Securing your session...</p>
    </div>
</body>
</html>