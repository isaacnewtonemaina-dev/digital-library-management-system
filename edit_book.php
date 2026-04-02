<?php
session_start();
include 'db.php';

// Authentication check
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'librarian'){
    header("Location: books.php");
    exit();
}

// Get and check the Book ID
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$result = $conn->query("SELECT * FROM books WHERE id = $id");

if ($result && $result->num_rows == 0) {
    die("Error: Book with ID $id not found in the database.");
}

$book = $result->fetch_assoc();

// Update Logic
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect data from form
    $new_title = $conn->real_escape_string($_POST['title']); 
    $author = $conn->real_escape_string($_POST['author']);
    $shelf = $conn->real_escape_string($_POST['shelf']);

    // UPDATE matches your DB columns: book_title, author, shelf
    $sql = "UPDATE books SET book_title='$new_title', author='$author', shelf='$shelf' WHERE id=$id";
    
    if ($conn->query($sql)) {
        $_SESSION['msg'] = "Book updated successfully!";
        header("Location: books.php");
        exit();
    } else {
        die("Update failed: " . $conn->error);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Book</title>
    <style>
        body { font-family: sans-serif; background: #f8fafc; display: flex; justify-content: center; padding: 50px; }
        .form-card { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); width: 400px; }
        input { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; }
        .btn-save { width: 100%; background: #3b82f6; color: white; border: none; padding: 10px; border-radius: 5px; cursor: pointer; }
        .back-link { display: block; text-align: center; margin-top: 10px; color: #666; text-decoration: none; }
    </style>
</head>
<body>
    <div class="form-card">
        <h2>Edit Book</h2>
        <form method="POST">
            <input type="text" name="title" value="<?php echo $book['book_title'] ?? ''; ?>" required placeholder="Title">
            
            <input type="text" name="author" value="<?php echo $book['author'] ?? ''; ?>" required placeholder="Author">
            
            <input type="text" name="shelf" value="<?php echo $book['shelf'] ?? ''; ?>" required placeholder="Shelf">
            
            <button type="submit" class="btn-save">Update Book</button>
            <a href="books.php" class="back-link">Back</a>
        </form>
    </div>
</body>
</html>