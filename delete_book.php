<?php
session_start();
include 'db.php';

// Security check: Only librarians can delete books
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'librarian'){
    header("Location: books.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Added security: ensure ID is an integer

    // 1. Delete associated borrowings first to avoid constraint errors
    // This removes the "foreign key" link that was causing the Fatal Error
    $conn->query("DELETE FROM borrowings WHERE book_id = $id");

    // 2. Now delete the book itself
    $sql = "DELETE FROM books WHERE id = $id";

    if ($conn->query($sql)) {
        $_SESSION['msg'] = "Book and its history deleted successfully!";
    } else {
        $_SESSION['error_msg'] = "Error deleting book: " . $conn->error;
    }
}

header("Location: books.php");
exit();
?>