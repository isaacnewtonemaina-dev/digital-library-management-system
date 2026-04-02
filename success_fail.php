<?php
session_start();

// Redirect back to library if no message exists (prevents manual access)
if (!isset($_SESSION['msg']) && !isset($_SESSION['error_msg'])) {
    header("Location: books.php");
    exit();
}

// This variable tells the HTML which section to show
$isSuccess = isset($_SESSION['msg']); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Borrowing Status | Stone Haven</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        
        body { 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            min-height: 100vh; 
            background: #f8fafc; 
        }

        .status-card {
            background: white;
            padding: 50px;
            border-radius: 25px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            text-align: center;
            width: 100%;
            max-width: 400px;
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .icon-circle {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 50px;
            color: white;
            box-shadow: 0 8px 15px rgba(0,0,0,0.1);
        }

        .success-bg { background: #22c55e; } /* Green Circle */
        .error-bg { background: #ef4444; }   /* Red Circle */

        h2 { margin-bottom: 10px; color: #1e293b; font-weight: 700; }
        p { color: #64748b; margin-bottom: 30px; line-height: 1.6; }

        .btn {
            display: inline-block;
            padding: 12px 30px;
            background: #0f172a;
            color: white;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 600;
            transition: 0.3s;
        }

        .btn:hover {
            background: #334155;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>

    <div class="status-card">
        <?php if ($isSuccess): ?>
            <div class="icon-circle success-bg">
                <i class="fas fa-check"></i>
            </div>
            <h2>Successfully Borrowed!</h2>
            <p><?php echo $_SESSION['msg']; unset($_SESSION['msg']); ?></p>
            
            <script>setTimeout(() => { window.location.href = 'books.php'; }, 5000);</script>
            
        <?php else: ?>
            <div class="icon-circle error-bg">
                <i class="fas fa-times"></i>
            </div>
            <h2>Borrowing Failed</h2>
            <p><?php echo $_SESSION['error_msg']; unset($_SESSION['error_msg']); ?></p>
        <?php endif; ?>

        <a href="books.php" class="btn">Return to Library</a>
    </div>

</body>
</html>