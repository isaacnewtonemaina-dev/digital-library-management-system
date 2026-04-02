<?php
session_start();
include 'db.php';

if(isset($_GET['id'])){
    $borrow_id = $_GET['id'];

    // Find the book_id associated with this borrowing
    $res = $conn->query("SELECT book_id FROM borrowings WHERE id = $borrow_id");
    $data = $res->fetch_assoc();
    $book_id = $data['book_id'];

    // 1. Update borrowing status
    $conn->query("UPDATE borrowings SET status = 'returned', return_date = NOW() WHERE id = $borrow_id");

    // 2. Make book available again
    $conn->query("UPDATE books SET status = 'available' WHERE id = $book_id");

    header("Location: borrowings.php");
}
?>