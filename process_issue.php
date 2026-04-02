<?php
session_start();
include 'db.php';

// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize user input to prevent SQL injection
    $user_id = mysqli_real_escape_string($conn, $_POST['user_id']);
    $book_id = mysqli_real_escape_string($conn, $_POST['book_id']);
    
    // Set dates: current time for borrowing and 14 days later for the due date
    $borrow_date = date('Y-m-d H:i:s');
    $due_date = date('Y-m-d', strtotime('+14 days'));

    /**
     * THE FIX: Fetch 'book_title' from the 'books' table.
     * This ensures the title is captured even if it wasn't sent by the form.
     */
    $book_query = "SELECT book_title FROM books WHERE id = '$book_id'";
    $book_result = $conn->query($book_query);
    
    if ($book_result && $book_result->num_rows > 0) {
        $book_data = $book_result->fetch_assoc();
        // Use real_escape_string to handle titles with special characters (e.g., apostrophes)
        $actual_title = $conn->real_escape_string($book_data['book_title']);

        /**
         * 2. Insert BOTH ID and TITLE into the borrowings table.
         * Using NOW() for the borrow_date as per your requested logic.
         */
        $sql = "INSERT INTO borrowings (user_id, book_id, book_title, borrow_date, due_date, status) 
                VALUES ('$user_id', '$book_id', '$actual_title', NOW(), '$due_date', 'borrowed')";

        if ($conn->query($sql)) {
            // 3. Mark the book as 'borrowed' in the books table to prevent double-booking
            $conn->query("UPDATE books SET status = 'borrowed' WHERE id = '$book_id'");
            
            // Set success message and redirect
            $_SESSION['msg'] = "Book successfully issued to member!";
            header("Location: borrowings.php");
            exit();
        } else {
            echo "Error: " . $conn->error;
        }
    } else {
        echo "Error: Book not found.";
    }
} else {
    // Redirect back if the page is accessed directly without POST
    header("Location: issue_book.php");
    exit();
}
?>