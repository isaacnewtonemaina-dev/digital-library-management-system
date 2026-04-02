<?php
session_start();
include 'db.php';

// 1. Security Check: Only Librarians can access this management page
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'librarian'){
    header("Location: dashboard.php");
    exit();
}

/** * 2. The Merged Query
 * Updated to use the requested JOIN structure while including return_date
 */
$query = "SELECT br.id, u.fullname, b.book_title, br.borrow_date, br.due_date, br.return_date, br.status 
          FROM borrowings br 
          JOIN users u ON br.user_id = u.id 
          JOIN books b ON br.book_id = b.id
          ORDER BY br.status ASC, br.borrow_date DESC";

$result = $conn->query($query);

if (!$result) {
    die("Query Error: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Issue/Return Management | Stone Haven</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { display: flex; background: #f8fafc; min-height: 100vh; }
        
        /* Sidebar Styling */
        .sidebar { width: 260px; background: #0f172a; color: white; height: 100vh; position: fixed; padding: 20px 0; }
        .sidebar-header { text-align: center; padding-bottom: 20px; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .nav-links { list-style: none; margin-top: 20px; }
        .nav-links li a { display: flex; align-items: center; padding: 15px 25px; color: #94a3b8; text-decoration: none; transition: 0.3s; }
        .nav-links li a:hover, .nav-links li a.active { background: #1e293b; color: white; border-left: 4px solid #ef4444; }
        .nav-links li a i { margin-right: 15px; width: 20px; text-align: center; }

        /* Main Content */
        .main-content { margin-left: 260px; flex-grow: 1; padding: 40px; }
        .header-box { margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center; }
        .btn-primary { background: #ef4444; color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600; transition: 0.3s; }
        .btn-primary:hover { background: #dc2626; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3); }

        /* Table Styling */
        .table-container { background: white; padding: 25px; border-radius: 15px; box-shadow: 0 10px 15px rgba(0,0,0,0.05); overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 15px; background: #f1f5f9; color: #475569; font-size: 13px; text-transform: uppercase; letter-spacing: 0.05em; }
        td { padding: 18px 15px; border-bottom: 1px solid #f1f5f9; font-size: 14px; color: #1e293b; }
        
        /* Badges */
        .badge { padding: 5px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; }
        .badge-borrowed { background: #fef3c7; color: #92400e; }
        .badge-returned { background: #d1fae5; color: #065f46; }

        .action-link { color: #10b981; font-weight: 600; text-decoration: none; }
        .action-link:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <nav class="sidebar">
        <div class="sidebar-header"><h3>STONE HAVEN</h3></div>
        <ul class="nav-links">
            <li><a href="dashboard.php"><i class="fas fa-th-large"></i> Dashboard</a></li>
            <li><a href="books.php"><i class="fas fa-book"></i> Books</a></li>
            <li><a href="borrowings.php" class="active"><i class="fas fa-exchange-alt"></i> Issue/Return</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </nav>

    <main class="main-content">
        <div class="header-box">
            <h1>Borrowing Management</h1>
            <a href="issue_book.php" class="btn-primary"><i class="fas fa-plus"></i> Issue New Book</a>
        </div>

        <?php if(isset($_SESSION['msg'])): ?>
            <div style="background: #d1fae5; color: #065f46; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                <i class="fas fa-check-circle"></i> <?php echo $_SESSION['msg']; unset($_SESSION['msg']); ?>
            </div>
        <?php endif; ?>

        <div class="table-container">
            <?php if ($result->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Borrower</th>
                            <th>Book Title</th>
                            <th>Timeline</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($row['fullname']); ?></strong></td>
                            <td><?php echo htmlspecialchars($row['book_title']); ?></td>
                            <td>
                                <span style="font-size: 12px; color: #64748b;">
                                    Issued: <?php echo date('M d, Y', strtotime($row['borrow_date'])); ?><br>
                                    Due: <strong><?php echo date('M d, Y', strtotime($row['due_date'])); ?></strong>
                                </span>
                            </td>
                            <td>
                                <span class="badge <?php echo ($row['status'] == 'borrowed') ? 'badge-borrowed' : 'badge-returned'; ?>">
                                    <?php echo $row['status']; ?>
                                </span>
                            </td>
                            <td>
                                <?php if($row['status'] == 'borrowed'): ?>
                                    <a href="return_book.php?id=<?php echo $row['id']; ?>" class="action-link" onclick="return confirm('Mark this book as returned?')">
                                        <i class="fas fa-check-circle"></i> Mark Returned
                                    </a>
                                <?php else: ?>
                                    <span style="color: #94a3b8; font-size: 12px;">
                                        Returned <?php echo !empty($row['return_date']) ? date('M d', strtotime($row['return_date'])) : 'N/A'; ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div style="text-align: center; padding: 40px; color: #64748b;">
                    <i class="fas fa-folder-open fa-3x" style="margin-bottom: 10px;"></i>
                    <p>No active borrowing records found.</p>
                </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>