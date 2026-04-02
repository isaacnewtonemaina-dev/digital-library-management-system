<?php
session_start();
include 'db.php';

$id = $_GET['id'] ?? null;

// Logic to handle the return button click
if (isset($_POST['return_book_id'])) {
    $borrow_id = $_POST['return_book_id'];
    $book_id = $_POST['actual_book_id'];

    $conn->begin_transaction();
    try {
        // 1. Mark borrowing as returned
        $conn->query("UPDATE borrowings SET returned = 1 WHERE id = $borrow_id");
        // 2. Make book available again
        $conn->query("UPDATE books SET status = 'available' WHERE id = $book_id");
        
        $conn->commit();
        $msg = "Book returned successfully!";
    } catch (Exception $e) {
        $conn->rollback();
        $msg = "Error processing return.";
    }
}

// ... (Rest of your profile fetching code) ...
?>

<div class="book-item">
    <span><strong><?php echo $book['title']; ?></strong></span>
    <form method="POST" style="display:inline;">
        <input type="hidden" name="return_book_id" value="<?php echo $book['borrow_id']; ?>">
        <input type="hidden" name="actual_book_id" value="<?php echo $book['book_id']; ?>">
        <button type="submit" class="btn-return">Mark Returned</button>
    </form>
</div>