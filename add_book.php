<?php
session_start();
include 'db.php';

// 1. Security Check: Ensure user is a librarian
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'librarian'){
    header("Location: books.php");
    exit();
}

$error = "";

// 2. Process Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_book'])) {
    // Sanitize inputs
    $title    = $conn->real_escape_string($_POST['title']);
    $author   = $conn->real_escape_string($_POST['author']);
    $shelf    = $conn->real_escape_string($_POST['shelf']);
    $category = $conn->real_escape_string($_POST['category']); // Added category from form

    // File Handling
    $filename = time() . "_" . basename($_FILES['book_pdf']['name']); 
    $target_dir = "uploads/books/";
    $target_file = $target_dir . $filename;

    // Create directory if it doesn't exist
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    if (move_uploaded_file($_FILES['book_pdf']['tmp_name'], $target_file)) {
        
        // UPDATED QUERY: Matches your database schema
        $sql = "INSERT INTO books (book_title, author, shelf, category, status, file_path) 
                VALUES ('$title', '$author', '$shelf', '$category', 'available', '$filename')";

        if ($conn->query($sql)) {
            $lib_id = $_SESSION['user_id'];
            $details = "Added: " . $title;
            
            // Log the action in inventory
            $conn->query("INSERT INTO inventory_logs (librarian_id, action_type, details) 
                          VALUES ('$lib_id', 'BOOK_ADDED', '$details')");

            $_SESSION['msg'] = "Book added successfully!";
            header("Location: books.php");
            exit();
        } else {
            $error = "Database Error: " . $conn->error;
        }
    } else {
        $error = "Upload failed. Please check folder permissions for 'uploads/books/'.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Digital Book | Stone Haven</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f8fafc; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .card { background: white; padding: 35px; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); width: 100%; max-width: 400px; }
        h2 { text-align: center; color: #1e293b; margin-bottom: 25px; }
        .group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; color: #64748b; font-size: 14px; }
        input, select { width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; box-sizing: border-box; }
        .btn { width: 100%; padding: 14px; background: #0f172a; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; margin-top: 10px; }
        .error { color: #ef4444; background: #fee2e2; padding: 10px; border-radius: 5px; margin-bottom: 15px; font-size: 13px; text-align: center; }
    </style>
</head>
<body>
    <div class="card">
        <h2>Add Digital Book</h2>
        <?php if($error) echo "<div class='error'>$error</div>"; ?>
        <form method="POST" enctype="multipart/form-data">
            <div class="group">
                <label>Book Title</label>
                <input type="text" name="title" required>
            </div>
            <div class="group">
                <label>Author</label>
                <input type="text" name="author" required>
            </div>
            <div class="group">
                <label>Category</label>
                <input type="text" name="category" placeholder="e.g. Science, Tech" required>
            </div>
            <div class="group">
                <label>Shelf Location</label>
                <input type="text" name="shelf" placeholder="e.g. Section A-1" required>
            </div>
            <div class="group">
                <label>Upload PDF</label>
                <input type="file" name="book_pdf" accept=".pdf" required>
            </div>
            <button type="submit" name="add_book" class="btn">Save to Library</button>
        </form>
    </div>
</body>
</html>