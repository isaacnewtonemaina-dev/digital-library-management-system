<?php
session_start();
include 'db.php';

// Check if librarian
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'librarian') {
    header("Location: login.php");
    exit();
}

// FIXED SQL QUERY
// We are using bk.book_title to resolve the "Unknown Column" error
$sql = "SELECT 
            b.id AS borrowing_id,
            bk.book_title, 
            u.fullname AS borrower_name, 
            b.borrow_date,
            DATE_ADD(b.borrow_date, INTERVAL 14 DAY) AS due_date
        FROM borrowings b
        JOIN books bk ON b.book_id = bk.id
        JOIN users u ON b.user_id = u.id
        WHERE b.return_date IS NULL 
        ORDER BY b.borrow_date ASC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Borrowings | Stone Haven Library</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        /* Modern Table Styling */
        body { 
            font-family: 'Poppins', sans-serif; 
            background: #0f172a; 
            color: white; 
            padding: 40px; 
        }
        .table-container { 
            background: rgba(255,255,255,0.05); 
            padding: 20px; 
            border-radius: 15px; 
            border: 1px solid rgba(255,255,255,0.1); 
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
        h2 { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { text-align: left; padding: 12px; border-bottom: 2px solid #ef4444; color: #ef4444; }
        td { padding: 12px; border-bottom: 1px solid rgba(255,255,255,0.1); }
        tr:hover td { background: rgba(255,255,255,0.02); }
        .fine-alert { color: #f87171; font-weight: bold; }
        .safe { color: #10b981; }
        .badge { 
            background: #ef4444; 
            color: white; 
            padding: 2px 8px; 
            border-radius: 4px; 
            font-size: 0.8em; 
            margin-left: 5px;
        }
        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #94a3b8;
            text-decoration: none;
            transition: 0.3s;
        }
        .back-link:hover { color: #ef4444; }
    </style>
</head>
<body>

<div class="table-container">
    <a href="dashboard.php" class="back-link">← Back to Dashboard</a>
    <h2>Active Borrowings & Fines</h2>
    <table>
        <thead>
            <tr>
                <th>Book</th>
                <th>Borrower</th>
                <th>Date Borrowed</th>
                <th>Due Date</th>
                <th>Status & Fine</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result && $result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $today = new DateTime();
                    $due_date = new DateTime($row['due_date']);
                    
                    $fine = 0;
                    if ($today > $due_date) {
                        $diff = $today->diff($due_date);
                        $overdue_days = $diff->days;
                        $fine = $overdue_days * 100;
                    }

                    echo "<tr>";
                    // Using book_title as defined in the SQL alias
                    echo "<td>" . htmlspecialchars($row['book_title']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['borrower_name']) . "</td>";
                    echo "<td>" . date('d M Y', strtotime($row['borrow_date'])) . "</td>";
                    echo "<td>" . date('d M Y', strtotime($row['due_date'])) . "</td>";
                    
                    if ($fine > 0) {
                        echo "<td class='fine-alert'>OVERDUE <span class='badge'>Ksh " . number_format($fine) . "</span></td>";
                    } else {
                        echo "<td class='safe'>On Time</td>";
                    }
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5' style='text-align:center;'>No active borrowings found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

</body>
</html>