<?php
session_start();
include 'db.php';

// Security: Librarian access only
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'librarian'){
    header("Location: dashboard.php");
    exit();
}

// SQL Logic: Find books borrowed > 14 days ago that are NOT returned
$query = "SELECT b.id, u.fullname, bk.title, b.borrow_date, 
          DATEDIFF(CURRENT_DATE, b.borrow_date) as days_kept
          FROM borrowings b
          JOIN users u ON b.user_id = u.id
          JOIN books bk ON b.book_id = bk.id
          WHERE b.status = 'borrowed' 
          AND DATEDIFF(CURRENT_DATE, b.borrow_date) > 14
          ORDER BY days_kept DESC";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Overdue Report | Stone Haven</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { display: flex; background: #fff1f2; min-height: 100vh; }
        
        .sidebar { width: 260px; background: #0f172a; color: white; position: fixed; height: 100vh; padding: 20px 0; }
        .nav-links { list-style: none; margin-top: 20px; }
        .nav-links li a { display: flex; align-items: center; padding: 15px 25px; color: #94a3b8; text-decoration: none; }
        .nav-links li a.active { background: #1e293b; color: white; border-left: 4px solid #ef4444; }

        .main-content { margin-left: 260px; flex-grow: 1; padding: 40px; }
        
        .overdue-container { background: white; padding: 30px; border-radius: 15px; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); }
        h1 { color: #991b1b; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { text-align: left; padding: 15px; background: #fef2f2; color: #991b1b; border-bottom: 2px solid #fee2e2; }
        td { padding: 15px; border-bottom: 1px solid #f1f5f9; }

        .days-badge { background: #fee2e2; color: #dc2626; padding: 5px 12px; border-radius: 20px; font-weight: bold; font-size: 13px; }
        .btn-fine { background: #dc2626; color: white; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 12px; }

        /* PDF / Print Button Styling */
        .btn-print {
            background: #0f172a;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .btn-print:hover { background: #1e293b; }

        /* PRINT STYLES: This hides the sidebar and buttons when saving to PDF */
        @media print {
            .sidebar, .btn-print, .btn-fine {
                display: none !important;
            }
            .main-content {
                margin-left: 0 !important;
                padding: 0 !important;
            }
            body {
                background: white !important;
            }
            .overdue-container {
                box-shadow: none !important;
                padding: 0 !important;
            }
            table {
                border: 1px solid #ddd;
            }
            th {
                background-color: #f8fafc !important;
                color: black !important;
                -webkit-print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>

    <nav class="sidebar">
        <div style="text-align:center; padding-bottom:20px;"><h3>Stone Haven</h3></div>
        <ul class="nav-links">
            <li><a href="dashboard.php"><i class="fas fa-th-large"></i> Dashboard</a></li>
            <li><a href="books.php"><i class="fas fa-book"></i> Books</a></li>
            <li><a href="members.php"><i class="fas fa-users"></i> Members</a></li>
            <li><a href="borrowings.php"><i class="fas fa-exchange-alt"></i> Issue/Return</a></li>
            <li><a href="reports.php" class="active"><i class="fas fa-file-alt"></i> Reports</a></li>
        </ul>
    </nav>

    <main class="main-content">
        <div class="overdue-container">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <div>
                    <h1><i class="fas fa-exclamation-triangle"></i> Overdue Books</h1>
                    <p>List of books held longer than the 14-day policy.</p>
                </div>
                <button onclick="window.print()" class="btn-print">
                    <i class="fas fa-file-pdf"></i> Save as PDF / Print
                </button>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Member Name</th>
                        <th>Book Title</th>
                        <th>Issued On</th>
                        <th>Days Held</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($row['fullname']); ?></strong></td>
                            <td><?php echo htmlspecialchars($row['title']); ?></td>
                            <td><?php echo date('M d, Y', strtotime($row['borrow_date'])); ?></td>
                            <td><span class="days-badge"><?php echo $row['days_kept']; ?> Days</span></td>
                            <td><a href="return_process.php?id=<?php echo $row['id']; ?>" class="btn-fine">Force Return</a></td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align:center; padding: 40px; color: #64748b;">
                                <i class="fas fa-check-circle" style="font-size: 30px; color: #10b981;"></i>
                                <p style="margin-top:10px;">Excellent! No books are currently overdue.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

</body>
</html>