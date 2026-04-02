<?php
session_start();
include 'db.php';

// 1. Security Check: Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// 2. Check if an ID was provided
if (isset($_GET['id'])) {
    $borrow_id = $conn->real_escape_string($_GET['id']);
    
    // --- NEW: Catch the fine amount from the URL ---
    $fine = isset($_GET['fine']) ? $conn->real_escape_string($_GET['fine']) : 0;

    // 3. Validation Logic
    if ($role !== 'librarian') {
        $check_ownership = "SELECT * FROM borrowings WHERE id = '$borrow_id' AND user_id = '$user_id' AND status = 'borrowed'";
        $ownership_result = $conn->query($check_ownership);
        
        if ($ownership_result->num_rows == 0) {
            die("Unauthorized action or book already returned.");
        }
    }

    // 4. GET the book_id first so we know which book to make available
    $get_book_query = $conn->query("SELECT book_id FROM borrowings WHERE id = '$borrow_id'");
    $borrow_record = $get_book_query->fetch_assoc();
    $book_id = $borrow_record['book_id'];

    // 5. Update Database (The Merge)
    // 5.1 Mark the borrowing record as returned with date and fine
    $update_borrow = "UPDATE borrowings 
                      SET status = 'returned', 
                          return_date = NOW(), 
                          fine_amount = '$fine' 
                      WHERE id = '$borrow_id'";
    
    // 5.2 IMPORTANT: Update the 'books' table so the UI shows it as 'Available'
    $update_book_status = "UPDATE books SET status = 'available' WHERE id = '$book_id'";

    // Execute both queries
    if ($conn->query($update_borrow) === TRUE && $conn->query($update_book_status) === TRUE) {
        if ($role === 'librarian') {
            header("Location: borrowings.php?msg=returned");
        } else {
            header("Location: my_loans.php?msg=returned");
        }
        exit();
    } else {
        echo "Error updating record: " . $conn->error;
    }
} else {
    header("Location: dashboard.php");
}
?>