<?php
session_start();
include 'db.php';

// Security: Only Librarians can access this page
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'librarian') {
    header("Location: login.php");
    exit();
}

/** * THE FIX: Changed 'title' to 'book_title' in the query 
 * to match your database schema exactly.
 */
$books_query = $conn->query("SELECT id, book_title FROM books WHERE status = 'available'");

// Fetch all members for the dropdown
$members_query = $conn->query("SELECT id, fullname FROM users WHERE role != 'librarian'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Issue Book | Stone Haven</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { background: #f8fafc; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .issue-card { background: white; padding: 40px; border-radius: 15px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); width: 100%; max-width: 450px; text-align: center; }
        .issue-card h2 { color: #0f172a; margin-bottom: 25px; font-size: 1.5rem; }
        .form-group { text-align: left; margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; color: #64748b; font-weight: 600; font-size: 0.9rem; }
        select { width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 1rem; background: #fff; outline: none; transition: 0.3s; }
        select:focus { border-color: #ef4444; box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1); }
        .btn-confirm { width: 100%; padding: 14px; background: #ef4444; color: white; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; font-size: 1rem; transition: 0.3s; }
        .btn-confirm:hover { background: #dc2626; transform: translateY(-2px); }
        .btn-cancel { display: inline-block; margin-top: 20px; color: #94a3b8; text-decoration: none; font-size: 0.9rem; transition: 0.3s; }
        .btn-cancel:hover { color: #64748b; text-decoration: underline; }
    </style>
</head>
<body>

<div class="issue-card">
    <h2><i class="fas fa-book-reader" style="color: #ef4444;"></i> Issue Book</h2>
    
    <form action="process_issue.php" method="POST">
        
        <div class="form-group">
            <label>Select Member:</label>
            <select name="user_id" required>
                <option value="">-- Choose a Member --</option>
                <?php while($user = $members_query->fetch_assoc()): ?>
                    <option value="<?php echo $user['id']; ?>">
                        <?php echo htmlspecialchars($user['fullname']); ?> (ID: <?php echo $user['id']; ?>)
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="form-group">
            <label>Select Book Title:</label>
            <select name="book_id" required>
                <option value="">-- Choose a Book --</option>
                <?php while($book = $books_query->fetch_assoc()): ?>
                    <option value="<?php echo $book['id']; ?>">
                        <?php echo htmlspecialchars($book['book_title']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <button type="submit" class="btn-confirm">Confirm Issue</button>
        <a href="borrowings.php" class="btn-cancel">Cancel and Go Back</a>
    </form>
</div>

</body>
</html>