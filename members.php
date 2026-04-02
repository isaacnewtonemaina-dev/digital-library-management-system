<?php
session_start();
include 'db.php';

// 1. Security: Only the Librarian should see the member list
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'librarian'){
    header("Location: dashboard.php");
    exit();
}

$error = ""; // Initialize error variable

// 2. Handle Member Deletion with Foreign Key Fix
if (isset($_GET['delete_id'])) {
    $delete_id = mysqli_real_escape_string($conn, $_GET['delete_id']);
    
    // Prevent a librarian from deleting themselves
    if ($delete_id != $_SESSION['user_id']) {
        
        // NEW FIX: Delete associated borrowings first to avoid Foreign Key error
        $conn->query("DELETE FROM borrowings WHERE user_id = '$delete_id'");
        
        // Now delete the actual user
        if($conn->query("DELETE FROM users WHERE id = '$delete_id'")) {
            header("Location: members.php?msg=Member and their history deleted");
            exit();
        } else {
            $error = "Error: " . $conn->error;
        }
    } else {
        $error = "You cannot delete your own account.";
    }
}

$fullname = $_SESSION['fullname'];

// 3. Fetch all users from the database
$query = "SELECT id, fullname, email, role FROM users ORDER BY id DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member Management | Stone Haven</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { display: flex; min-height: 100vh; background: #f8fafc; }

        /* Sidebar Styling */
        .sidebar { width: 260px; background: #0f172a; color: white; position: fixed; height: 100vh; padding: 20px 0; }
        .sidebar-header { text-align: center; padding-bottom: 30px; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .sidebar-header h3 { color: #ef4444; }
        .nav-links { list-style: none; margin-top: 20px; }
        .nav-links li a { display: flex; align-items: center; padding: 15px 25px; color: #94a3b8; text-decoration: none; transition: 0.3s; }
        .nav-links li a:hover, .nav-links li a.active { background: #1e293b; color: white; border-left: 4px solid #ef4444; }

        /* Main Content */
        .main-content { margin-left: 260px; flex-grow: 1; padding: 40px; }
        header { margin-bottom: 30px; }
        
        .table-container { background: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 15px; background: #0f172a; color: white; font-size: 14px; text-transform: uppercase; }
        td { padding: 15px; border-bottom: 1px solid #f1f5f9; color: #475569; }
        
        /* Role Badges */
        .role-badge { padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: bold; }
        .role-librarian { background: #fee2e2; color: #ef4444; }
        .role-student { background: #dcfce7; color: #16a34a; }
        .role-default { background: #f1f5f9; color: #64748b; }
        
        .id-badge { background: #f1f5f9; color: #1e293b; padding: 5px 8px; border-radius: 4px; font-weight: bold; font-family: monospace; }

        /* Action Icons */
        .action-btns a { margin: 0 5px; text-decoration: none; font-size: 18px; transition: 0.2s; }
        .edit-btn { color: #3b82f6; }
        .edit-btn:hover { color: #2563eb; }
        .delete-btn { color: #ef4444; }
        .delete-btn:hover { color: #dc2626; }

        .error-banner { background: #fee2e2; color: #ef4444; padding: 10px; border-radius: 8px; margin-bottom: 20px; }
    </style>
</head>
<body>

    <nav class="sidebar">
        <div class="sidebar-header"><h3>Stone Haven</h3></div>
        <ul class="nav-links">
            <li><a href="dashboard.php"><i class="fas fa-th-large"></i> Dashboard</a></li>
            <li><a href="books.php"><i class="fas fa-book"></i> Books</a></li>
            <li><a href="members.php" class="active"><i class="fas fa-users"></i> Members</a></li>
            <li><a href="borrowings.php"><i class="fas fa-exchange-alt"></i> Issue/Return</a></li>
        </ul>
    </nav>

    <main class="main-content">
        <header>
            <h1>Library Members</h1>
            <?php if($error): ?>
                <div class="error-banner"><?php echo $error; ?></div>
            <?php endif; ?>
            <p>View registered members and their unique identification numbers.</p>
        </header>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Member ID</th>
                        <th>Full Name</th>
                        <th>Email Address</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><span class="id-badge">ID: <?php echo $row['id']; ?></span></td>
                            <td><strong><?php echo htmlspecialchars($row['fullname']); ?></strong></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td>
                                <span class="role-badge <?php 
                                    if($row['role'] == 'librarian') echo 'role-librarian';
                                    elseif($row['role'] == 'student') echo 'role-student';
                                    else echo 'role-default';
                                ?>">
                                    <?php echo ucfirst($row['role']); ?>
                                </span>
                            </td>
                            <td class="action-btns">
                                <a href="edit_member.php?id=<?php echo $row['id']; ?>" class="edit-btn" title="Edit Member">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="members.php?delete_id=<?php echo $row['id']; ?>" 
                                   class="delete-btn" 
                                   title="Delete Member" 
                                   onclick="return confirm('Are you sure you want to delete this member? All their borrowing history will also be deleted.')">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 20px;">No members found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

</body>
</html>