<?php
session_start();
include 'db.php';

// Security check: Ensure the user is logged in
if(!isset($_SESSION['user_id'])){ 
    header("Location: login.php"); 
    exit(); 
}

$book_id = $_GET['id'] ?? 0;

// FETCH FIX: Changed 'title' to 'book_title' to match your database schema
$stmt = $conn->prepare("SELECT book_title, author, file_path FROM books WHERE id = ?");
$stmt->bind_param("i", $book_id);
$stmt->execute();
$result = $stmt->get_result();
$book = $result->fetch_assoc();

// Verification: Ensure the PDF exists in the database
if (!$book || empty($book['file_path'])) {
    die("This book does not have a digital version available for reading.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reading: <?php echo htmlspecialchars($book['book_title']); ?></title>
    <style>
        body { margin: 0; background: #1e293b; font-family: 'Poppins', sans-serif; color: white; overflow: hidden; }
        .reader-header { 
            padding: 10px 25px; 
            background: #0f172a; 
            display: flex; 
            justify-content: space-between; 
            align-items: center;
            border-bottom: 1px solid #334155;
            height: 60px; /* Fixed height for header */
        }
        /* Container adjusts for the header height */
        .reader-container { 
            height: calc(100vh - 60px); 
            width: 100%; 
        }
        embed { width: 100%; height: 100%; border: none; }
        .back-btn { color: #94a3b8; text-decoration: none; font-size: 14px; transition: 0.3s; }
        .back-btn:hover { color: white; }
    </style>
</head>
<body>

<div class="reader-header">
    <div>
        <a href="books.php" class="back-btn">← Back to Library</a>
        <h2 style="margin: 2px 0; font-size: 20px;"><?php echo htmlspecialchars($book['book_title']); ?></h2>
        <small style="color: #94a3b8;">By <?php echo htmlspecialchars($book['author']); ?></small>
    </div>
</div>

<div class="reader-container">
    <embed src="uploads/books/<?php echo htmlspecialchars($book['file_path']); ?>" type="application/pdf" width="100%" height="100%">
</div>

</body>
</html>