<?php
session_start();
include 'db.php';

// 1. Security: Ensure the user is logged in
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$fullname = $_SESSION['fullname'];

// 2. Fetch ACTIVE borrowings (Updated with book_title and correct column names)
$active_query = "SELECT br.id, b.id AS book_id, b.book_title, b.author, b.file_path, br.borrow_date, br.due_date 
                 FROM borrowings br 
                 JOIN books b ON br.book_id = b.id 
                 WHERE br.user_id = '$user_id' AND br.status = 'borrowed'
                 ORDER BY br.due_date ASC";
$active_result = $conn->query($active_query);

// 3. Fetch RETURNED history (Updated with book_title)
$history_query = "SELECT b.book_title, b.author, br.borrow_date, br.return_date 
                  FROM borrowings br 
                  JOIN books b ON br.book_id = b.id 
                  WHERE br.user_id = '$user_id' AND br.status = 'returned'
                  ORDER BY br.return_date DESC LIMIT 10";
$history_result = $conn->query($history_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Loans | Stone Haven</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { display: flex; min-height: 100vh; background: #f8fafc; }

        /* Sidebar */
        .sidebar { width: 260px; background: #0f172a; color: white; display: flex; flex-direction: column; position: fixed; height: 100vh; padding: 20px 0; }
        .sidebar-header { text-align: center; padding-bottom: 30px; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .sidebar-header h3 { color: #ef4444; }
        .nav-links { list-style: none; margin-top: 20px; flex-grow: 1; }
        .nav-links li a { display: flex; align-items: center; padding: 15px 25px; color: #94a3b8; text-decoration: none; transition: 0.3s; }
        .nav-links li a:hover, .nav-links li a.active { background: #1e293b; color: white; border-left: 4px solid #ef4444; }
        
        /* Main Content */
        .main-content { margin-left: 260px; flex-grow: 1; padding: 40px; }
        .alert { background: #dcfce7; color: #166534; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #bbf7d0; display: flex; align-items: center; }
        .alert i { margin-right: 10px; }

        .card { background: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); margin-bottom: 40px; }
        h2 { margin-bottom: 20px; color: #1e293b; font-size: 1.2rem; display: flex; align-items: center; gap: 10px; }
        
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 15px; background: #f1f5f9; color: #475569; font-size: 13px; text-transform: uppercase; }
        td { padding: 15px; border-bottom: 1px solid #f1f5f9; color: #1e293b; font-size: 14px; }
        
        .due-soon { color: #ef4444; font-weight: bold; }
        .on-time { color: #16a34a; font-weight: bold; }
        .history-text { color: #64748b; font-style: italic; }
        .fine-text { color: #ef4444; font-size: 12px; display: block; margin-top: 4px; }

        /* Button Styles */
        .btn-read { background: #3b82f6; color: white; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 12px; margin-right: 5px; display: inline-block; }
        .btn-return { background: #10b981; color: white; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 12px; display: inline-block; border: none; cursor: pointer; }
    </style>
</head>
<body>

    <nav class="sidebar">
        <div class="sidebar-header"><h3>Stone Haven</h3></div>
        <ul class="nav-links">
            <li><a href="dashboard.php"><i class="fas fa-th-large"></i> Dashboard</a></li>
            <li><a href="books.php"><i class="fas fa-book"></i> Books</a></li>
            <li><a href="my_loans.php" class="active"><i class="fas fa-hand-holding-heart"></i> My Loans</a></li>
            <li><a href="logout.php" style="color:#fca5a5; margin-top:20px;"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </nav>

    <main class="main-content">
        <header style="margin-bottom: 30px;">
            <h1>Personal Library Activity</h1>
            <p>Welcome, <strong><?php echo htmlspecialchars($fullname); ?></strong>. View your current loans and fines below.</p>
        </header>

        <?php if(isset($_GET['msg']) && $_GET['msg'] == 'returned'): ?>
            <div class="alert"><i class="fas fa-check-circle"></i> Book returned successfully!</div>
        <?php endif; ?>

        <div class="card">
            <h2><i class="fas fa-clock" style="color: #ef4444;"></i> Current Active Loans</h2>
            <table>
                <thead>
                    <tr>
                        <th>Book Title</th>
                        <th>Borrowed Date</th>
                        <th>Due Date</th>
                        <th>Status & Fines</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($active_result->num_rows > 0): ?>
                        <?php while($loan = $active_result->fetch_assoc()): 
                            // --- FINE CALCULATION LOGIC ---
                            $today = new DateTime();
                            $due_date = new DateTime($loan['due_date']);
                            $diff = $today->diff($due_date);
                            $days_diff = (int)$diff->format("%r%a"); // Positive if in future, negative if overdue
                            
                            $fine_per_day = 5; 
                            $total_fine = 0;

                            if ($days_diff < 0) {
                                $total_fine = abs($days_diff) * $fine_per_day;
                            }
                        ?>
                        <tr>
                            <td>
                                <strong><?php echo htmlspecialchars($loan['book_title']); ?></strong><br>
                                <small><?php echo htmlspecialchars($loan['author']); ?></small>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($loan['borrow_date'])); ?></td>
                            <td><?php echo date('M d, Y', strtotime($loan['due_date'])); ?></td>
                            <td>
                                <?php if($days_diff < 0): ?>
                                    <span class="due-soon">OVERDUE</span>
                                    <span class="fine-text">Fine: $<?php echo number_format($total_fine, 2); ?></span>
                                <?php else: ?>
                                    <span class="on-time"><?php echo $days_diff; ?> days left</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if(!empty($loan['file_path'])): ?>
                                    <a href="view_book.php?id=<?php echo $loan['book_id']; ?>" class="btn-read">
                                        <i class="fas fa-book-open"></i> Read
                                    </a>
                                <?php endif; ?>

                                <a href="return_book.php?id=<?php echo $loan['id']; ?>&fine=<?php echo $total_fine; ?>" 
                                   class="btn-return" 
                                   onclick="return confirm('Return this book? <?php echo ($total_fine > 0) ? 'Pending Fine: $'.$total_fine : ''; ?>')">
                                    Return
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5" style="text-align:center; padding: 30px;">No active loans. <a href="books.php">Borrow a book</a> to start reading.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="card">
            <h2><i class="fas fa-history" style="color: #3b82f6;"></i> Borrowing History (Recent)</h2>
            <table>
                <thead>
                    <tr>
                        <th>Book Title</th>
                        <th>Borrowed On</th>
                        <th>Returned On</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($history_result->num_rows > 0): ?>
                        <?php while($prev = $history_result->fetch_assoc()): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($prev['book_title']); ?></strong></td>
                            <td class="history-text"><?php echo date('M d, Y', strtotime($prev['borrow_date'])); ?></td>
                            <td class="history-text"><?php echo date('M d, Y', strtotime($prev['return_date'])); ?></td>
                            <td><span style="color: #64748b; font-size: 12px;"><i class="fas fa-check"></i> Completed</span></td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="4" style="text-align:center; padding: 30px;">No history found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>