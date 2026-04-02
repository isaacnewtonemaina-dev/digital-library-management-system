<?php
session_start();
include 'db.php';

// Security: Ensure user is logged in
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$fullname = $_SESSION['fullname'];
$role = $_SESSION['role'];

// Search Logic
$search = $_GET['search'] ?? '';

/**
 * UPDATED QUERY: Using 'book_title' to match your verified database schema.
 */
$query = "SELECT id, book_title, author, shelf, category, status, file_path FROM books";

if (!empty($search)) {
    $search_safe = $conn->real_escape_string($search);
    $query .= " WHERE book_title LIKE '%$search_safe%' 
                OR author LIKE '%$search_safe%' 
                OR shelf LIKE '%$search_safe%'
                OR category LIKE '%$search_safe%'";
}

$result = $conn->query($query);

if (!$result) {
    die("Database Error: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Digital Collection | Stone Haven</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Styles remain unchanged for your layout */
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { display: flex; min-height: 100vh; background: #f8fafc; }
        .sidebar { width: 260px; background: #0f172a; color: white; position: fixed; height: 100vh; padding: 20px 0; }
        .sidebar-header { text-align: center; padding-bottom: 20px; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .nav-links { list-style: none; margin-top: 20px; }
        .nav-links li a { color: #94a3b8; text-decoration: none; display: block; padding: 15px 25px; transition: 0.3s; }
        .nav-links li a.active { color: white; background: #1e293b; border-left: 4px solid #ef4444; }
        .main-content { margin-left: 260px; flex-grow: 1; padding: 40px; }
        .header-section { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .btn-add { background: #16a34a; color: white; padding: 12px 20px; border-radius: 8px; text-decoration: none; font-weight: 600; display: flex; align-items: center; gap: 8px; transition: 0.3s; }
        .btn-add:hover { background: #15803d; transform: translateY(-2px); }
        .table-container { background: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 15px; background: #0f172a; color: white; font-size: 13px; text-transform: uppercase; }
        td { padding: 15px; border-bottom: 1px solid #f1f5f9; font-size: 14px; }
        .badge { padding: 5px 10px; border-radius: 5px; font-weight: 600; font-size: 12px; }
        .badge-available { background: #dcfce7; color: #16a34a; }
        .badge-borrowed { background: #fee2e2; color: #dc2626; }
        .admin-actions { display: flex; gap: 15px; margin-top: 10px; padding-top: 10px; border-top: 1px dashed #e2e8f0; }
        .btn-edit { color: #3b82f6; text-decoration: none; font-size: 13px; font-weight: 600; }
        .btn-delete { color: #94a3b8; text-decoration: none; font-size: 13px; font-weight: 600; }
        .btn-delete:hover { color: #ef4444; }
        .search-bar { display: flex; gap: 10px; margin-bottom: 25px; }
        .search-bar input { flex-grow: 1; padding: 12px; border-radius: 8px; border: 1px solid #ddd; outline: none; }
        .search-bar button { background: #0f172a; color: white; padding: 10px 25px; border-radius: 8px; border: none; cursor: pointer; }
        .view-link { color: #16a34a; text-decoration: none; font-weight: 600; font-size: 12px; display: inline-block; margin-top: 5px; }
    </style>
</head>
<body>

    <nav class="sidebar">
        <div class="sidebar-header"><h3>STONE HAVEN</h3></div>
        <ul class="nav-links">
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="books.php" class="active">Books</a></li>
            <li><a href="borrowings.php">Issue/Return</a></li>
        </ul>
    </nav>

    <main class="main-content">
        <?php if(isset($_SESSION['msg'])): ?>
            <div style="background:#dcfce7; color:#16a34a; padding:15px; border-radius:8px; margin-bottom:20px;">
                <i class="fas fa-check-circle"></i> <?php echo $_SESSION['msg']; unset($_SESSION['msg']); ?>
            </div>
        <?php endif; ?>

        <div class="header-section">
            <div>
                <h1>Digital Collection</h1>
                <p>Logged in as: <strong><?php echo htmlspecialchars($fullname); ?></strong></p>
            </div>
            
            <?php if($role === 'librarian'): ?>
                <a href="add_book.php" class="btn-add">
                    <i class="fas fa-plus-circle"></i> Add New Book
                </a>
            <?php endif; ?>
        </div>

        <form action="books.php" method="GET" class="search-bar">
            <input type="text" name="search" placeholder="Search by title, author, category, or shelf..." value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit">Search</button>
        </form>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Book Title</th>
                        <th>Author</th>
                        <th>Category</th>
                        <th>Shelf</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($result->num_rows > 0): ?>
                        <?php while($book = $result->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <strong><?php echo htmlspecialchars($book['book_title'] ?? 'Untitled Book'); ?></strong>
                                <?php if(!empty($book['file_path'])): ?>
                                    <br>
                                    <a href="uploads/books/<?php echo htmlspecialchars($book['file_path']); ?>" target="_blank" class="view-link">
                                        <i class="fas fa-file-pdf"></i> View Digital Copy
                                    </a>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($book['author']); ?></td>
                            <td><?php echo htmlspecialchars($book['category']); ?></td>
                            <td><?php echo htmlspecialchars($book['shelf']); ?></td>
                            <td>
                                <span class="badge <?php echo ($book['status'] == 'available') ? 'badge-available' : 'badge-borrowed'; ?>">
                                    <?php echo ucfirst($book['status']); ?>
                                </span>
                            </td>
                            <td>
                                <?php if($book['status'] == 'available'): ?>
                                    <form action="process_borrow.php" method="POST" style="display:inline;">
                                        <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">
                                        <button type="submit" style="background:#ef4444; color:white; border:none; padding:8px 15px; border-radius:4px; cursor:pointer; font-weight:600;">Borrow</button>
                                    </form>
                                <?php else: ?>
                                    <span style="color:#94a3b8; font-style:italic;">Checked Out</span>
                                <?php endif; ?>

                                <?php if($role === 'librarian'): ?>
                                    <div class="admin-actions">
                                        <a href="edit_book.php?id=<?php echo $book['id']; ?>" class="btn-edit">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <a href="delete_book.php?id=<?php echo $book['id']; ?>" class="btn-delete" onclick="return confirm('Permanently delete this book?')">
                                            <i class="fas fa-trash"></i> Delete
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align:center; padding:30px; color:#64748b;">No books matches your search.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>