<?php
session_start();
include 'db.php';

// --- LOGIC SIDE: Fetching Data ---
// We select the 6 most recent books added to the database
$new_arrivals_query = "SELECT * FROM books ORDER BY id DESC LIMIT 6";
$results = $conn->query($new_arrivals_query);

$books = [];
if ($results && $results->num_rows > 0) {
    while($row = $results->fetch_assoc()) {
        $books[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Arrivals | Stone Haven Library</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        
        body {
            background: linear-gradient(rgba(15, 23, 42, 0.9), rgba(15, 23, 42, 0.9)), 
                        url('images/Library.jpg') no-repeat center center fixed;
            background-size: cover;
            color: white;
            padding: 40px 8%;
        }

        .header-section { margin-bottom: 50px; text-align: center; }
        .header-section h1 { font-size: 3rem; color: #ef4444; }
        .header-section p { color: #94a3b8; font-size: 1.1rem; }

        /* --- USER SIDE: Grid Display --- */
        .arrivals-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }

        .book-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(15px);
            padding: 30px;
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .book-card:hover {
            transform: translateY(-10px);
            border-color: #ef4444;
            background: rgba(255, 255, 255, 0.15);
        }

        .new-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: #ef4444;
            color: white;
            padding: 5px 12px;
            border-radius: 50px;
            font-size: 0.7rem;
            font-weight: bold;
            text-transform: uppercase;
        }

        .book-card i { font-size: 3rem; color: #ef4444; margin-bottom: 20px; }
        .book-card h3 { font-size: 1.4rem; margin-bottom: 5px; }
        .book-card p { color: #cbd5e1; margin-bottom: 20px; font-style: italic; }

        .btn-borrow {
            display: inline-block;
            padding: 10px 20px;
            background: #ef4444;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: 0.3s;
        }

        .btn-borrow:hover { background: #dc2626; box-shadow: 0 0 15px rgba(239, 68, 68, 0.4); }

        .back-nav { margin-bottom: 20px; display: block; color: #ef4444; text-decoration: none; font-weight: 600; }
    </style>
</head>
<body>

    <a href="index.php" class="back-nav"><i class="fas fa-arrow-left"></i> Back to Home</a>

    <div class="header-section">
        <h1>New Arrivals</h1>
        <p>Explore the latest additions to the Stone Haven Digital Collection</p>
    </div>

    <div class="arrivals-grid">
        <?php if (!empty($books)): ?>
            <?php foreach ($books as $book): ?>
                <div class="book-card">
                    <div class="new-badge">New</div>
                    <i class="fas fa-book-open"></i>
                    <h3><?php echo htmlspecialchars($book['title']); ?></h3>
                    <p>by <?php echo htmlspecialchars($book['author']); ?></p>
                    <div style="font-size: 0.9rem; color: #94a3b8; margin-bottom: 15px;">
                        <i class="fas fa-map-marker-alt"></i> Shelf: <?php echo htmlspecialchars($book['shelf']); ?>
                    </div>
                    <a href="borrow_process.php?id=<?php echo $book['id']; ?>" class="btn-borrow">Borrow Now</a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No new books have been added recently.</p>
        <?php endif; ?>
    </div>

</body>
</html>