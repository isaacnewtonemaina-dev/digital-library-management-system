<?php
session_start();
include 'db.php';

// Check if a user (student) is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Use the logged-in user's ID from the session
$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['book_id'])) {
    $book_id = mysqli_real_escape_string($conn, $_POST['book_id']);
    $borrow_date = date('Y-m-d');
    $due_date = date('Y-m-d', strtotime('+14 days')); // Set due date for 2 weeks later

    // 1. Fetch book details (status AND book_title)
    $book_query = $conn->query("SELECT book_title, status FROM books WHERE id = '$book_id'");
    $book_data = $book_query->fetch_assoc();

    // Check if the book exists and is available
    if (!$book_data || $book_data['status'] !== 'available') {
        echo "<script>alert('Sorry, this book is currently unavailable.'); window.location='books.php';</script>";
        exit();
    }

    // Capture the title to prevent NULL in borrowings table
    $actual_title = $conn->real_escape_string($book_data['book_title']);

    // 2. Check if the student has already borrowed 3 books (Limit)
    $limit_check = $conn->query("SELECT COUNT(*) as total FROM borrowings WHERE user_id = '$user_id' AND status = 'borrowed'");
    $limit_data = $limit_check->fetch_assoc();

    if ($limit_data['total'] >= 3) {
        echo "<script>alert('You have reached your borrowing limit of 3 books.'); window.location='borrowings.php';</script>";
        exit();
    }

    // 3. Process the borrowing (Including book_title now)
    $sql = "INSERT INTO borrowings (book_id, user_id, book_title, borrow_date, due_date, status) 
            VALUES ('$book_id', '$user_id', '$actual_title', '$borrow_date', '$due_date', 'borrowed')";

    if ($conn->query($sql)) {
        // Mark book as borrowed in the books table
        $conn->query("UPDATE books SET status = 'borrowed' WHERE id = '$book_id'");
        
        // Setting session message for the redirect
        $_SESSION['msg'] = "Book borrowed successfully! Due date: $due_date";
        header("Location: borrowings.php");
        exit();
    } else {
        echo "Database Error: " . $conn->error;
    }
} else {
    echo "<div style='text-align:center; margin-top:50px; font-family:sans-serif;'>";
    echo "<h3>Error: No book selected.</h3>";
    echo "<p>Please go back to the <a href='books.php'>Books</a> page and click 'Borrow'.</p>";
    echo "</div>";
}
?>