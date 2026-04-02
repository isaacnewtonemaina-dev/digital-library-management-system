<?php
session_start();
include 'db.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'librarian'){
    header("Location: login.php");
    exit();
}

// 2. Fetch Librarian Name
if (isset($_SESSION['fullname'])) {
    $fullname = $_SESSION['fullname'];
} else {
    $user_id = $_SESSION['user_id'];
    $user_query = $conn->query("SELECT fullname FROM users WHERE id = '$user_id'");
    $user_data = $user_query->fetch_assoc();
    $fullname = $user_data['fullname'] ?? 'Librarian';
}

// 3. Handle "Sync Database" Action
if (isset($_POST['sync'])) {
    $lib_id = $_SESSION['user_id'];
    $conn->query("INSERT INTO inventory_logs (user_id, action_type, details) 
                  VALUES ('$lib_id', 'DB_SYNC', 'Librarian initiated central database refresh to ensure accuracy.')");

    $_SESSION['msg'] = "Central Database Synced Successfully! Supply levels are accurate.";
    header("Location: reports.php");
    exit();
}

// 4. Fetch Inventory Data for Stats
$total_books = $conn->query("SELECT COUNT(*) as total FROM books")->fetch_assoc()['total'];
$available_books = $conn->query("SELECT COUNT(*) as total FROM books WHERE status='available'")->fetch_assoc()['total'];
$issued_books = $conn->query("SELECT COUNT(*) as total FROM books WHERE status='borrowed'")->fetch_assoc()['total'];

// 5. Fetch Recent Shipments
$shipment_query = "SELECT id, book_title, author, shelf, status FROM books ORDER BY created_at DESC LIMIT 5";
$shipment_result = $conn->query($shipment_query);

// 6. Fetch Inventory Action Logs
$history = $conn->query("SELECT inventory_logs.*, users.fullname 
                         FROM inventory_logs 
                         JOIN users ON inventory_logs.user_id = users.id 
                         ORDER BY inventory_logs.log_time DESC LIMIT 10");

// 7. Fetch low stock shelves
$low_stock_query = $conn->query("SELECT shelf, COUNT(*) as book_count 
                                 FROM books 
                                 GROUP BY shelf 
                                 HAVING book_count < 3");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Inventory Checklist | Stone Haven</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { display: flex; background: #f8fafc; min-height: 100vh; }
        .sidebar { width: 260px; background: #0f172a; color: white; position: fixed; height: 100vh; padding: 20px 0; }
        .nav-links { list-style: none; margin-top: 20px; }
        .nav-links li a { display: flex; align-items: center; padding: 15px 25px; color: #94a3b8; text-decoration: none; }
        .nav-links li a.active { background: #1e293b; color: white; border-left: 4px solid #ef4444; }
        .main-content { margin-left: 260px; flex-grow: 1; padding: 40px; }
        .report-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); text-align: center; }
        .stat-card i { font-size: 24px; color: #ef4444; margin-bottom: 10px; }
        .stat-card h3 { font-size: 28px; color: #1e293b; }
        .inventory-table { background: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); margin-bottom: 30px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th { text-align: left; padding: 12px; background: #f1f5f9; color: #475569; font-size: 13px; }
        td { padding: 12px; border-bottom: 1px solid #f1f5f9; font-size: 14px; }
        .sync-section { margin-top: 30px; padding: 20px; background: #eff6ff; border-radius: 12px; border: 1px solid #bfdbfe; display: flex; justify-content: space-between; align-items: center; }
        .btn-sync { background: #3b82f6; color: white; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; }
        .btn-sync:hover { background: #2563eb; transform: translateY(-1px); }
        .action-badge { background: #e2e8f0; padding: 4px 8px; border-radius: 6px; font-size: 11px; font-weight: bold; }
        .status-added { color: #10b981; font-weight: 500; }
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
        <header style="margin-bottom: 30px;">
            <h1>Inventory Check List</h1>
            <p>Validated Librarian: <strong><?php echo htmlspecialchars($fullname); ?></strong></p>
        </header>

        <div class="report-grid">
            <div class="stat-card">
                <i class="fas fa-boxes"></i>
                <p>Total Stock</p>
                <h3><?php echo $total_books; ?></h3>
            </div>
            <div class="stat-card">
                <i class="fas fa-check-circle" style="color:#10b981;"></i>
                <p>On-Site (Available)</p>
                <h3><?php echo $available_books; ?></h3>
            </div>
            <div class="stat-card">
                <i class="fas fa-truck-loading" style="color:#f59e0b;"></i>
                <p>Out (Borrowed)</p>
                <h3><?php echo $issued_books; ?></h3>
            </div>
        </div>

        <div class="inventory-table">
            <h3><i class="fas fa-ship"></i> Recent Book Shipments</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Shelf</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($shipment_result && $shipment_result->num_rows > 0): ?>
                        <?php while($row = $shipment_result->fetch_assoc()): ?>
                        <tr>
                            <td>#<?php echo $row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['book_title']); ?></td>
                            <td><?php echo htmlspecialchars($row['author']); ?></td>
                            <td><?php echo htmlspecialchars($row['shelf']); ?></td>
                            <td><span class="status-added">• Added</span></td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5" style="text-align:center;">No recent shipments.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="inventory-table" style="border: 2px solid #fecaca; background: #fffafb;">
            <h3 style="color: #dc2626;"><i class="fas fa-exclamation-triangle"></i> Low Stock Alerts</h3>
            <table>
                <thead>
                    <tr>
                        <th>Shelf Location</th>
                        <th>Current Count</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($low_stock_query && $low_stock_query->num_rows > 0): ?>
                        <?php while($low = $low_stock_query->fetch_assoc()): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($low['shelf']); ?></strong></td>
                            <td><?php echo $low['book_count']; ?> Books</td>
                            <td><span style="color: #dc2626; font-weight: bold;">RESTOCK NEEDED</span></td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="3" style="text-align:center;">All shelves are well-stocked.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="inventory-table">
            <h3><i class="fas fa-history"></i> Inventory Action Logs</h3>
            <table>
                <thead>
                    <tr>
                        <th>Time</th>
                        <th>Librarian</th>
                        <th>Action</th>
                        <th>Details</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($history && $history->num_rows > 0): ?>
                        <?php while($h = $history->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo date('M d, H:i', strtotime($h['log_time'])); ?></td>
                            <td><?php echo htmlspecialchars($h['fullname']); ?></td>
                            <td><span class="action-badge"><?php echo htmlspecialchars($h['action_type']); ?></span></td>
                            <td><?php echo htmlspecialchars($h['details']); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="4" style="text-align:center;">No actions logged yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="sync-section">
            <div>
                <h4>Administrative Tools</h4>
                <p>Sync the database or generate a physical PDF inventory checklist.</p>
            </div>
            <div style="display: flex;">
                <form method="POST">
                    <button type="submit" name="sync" class="btn-sync">
                        <i class="fas fa-sync-alt"></i> Sync Now
                    </button>
                </form>
                
                <a href="generate_report.php" target="_blank" style="text-decoration: none;">
                    <button type="button" class="btn-sync" style="background: #ef4444; margin-left: 10px;">
                        <i class="fas fa-file-pdf"></i> Generate PDF Report
                    </button>
                </a>
            </div>
        </div>

        <?php if(isset($_SESSION['msg'])): ?>
            <p style="margin-top:20px; color:#10b981; font-weight:bold;"><i class="fas fa-info-circle"></i> <?php echo htmlspecialchars($_SESSION['msg']); unset($_SESSION['msg']); ?></p>
        <?php endif; ?>
    </main>

</body>
</html>