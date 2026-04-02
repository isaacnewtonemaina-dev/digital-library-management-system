<?php
session_start();
include 'db.php';
$searchResult = [];
if(isset($_GET['q'])){
    $q = $conn->real_escape_string($_GET['q']);
    $searchResult = $conn->query("SELECT * FROM books WHERE title LIKE '%$q%' OR author LIKE '%$q%'");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Book Search | Digital Library</title>
<link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container">
<h2>Search Books</h2>
<form method="GET">
<input type="text" name="q" placeholder="Enter title or author" required>
<button type="submit">Search</button>
</form>
<?php if($searchResult->num_rows>0): ?>
<ul class="search-results">
<?php while($book=$searchResult->fetch_assoc()): ?>
<li><?php echo $book['title']; ?> by <?php echo $book['author']; ?> | Shelf: <?php echo $book['shelf']; ?></li>
<?php endwhile; ?>
</ul>
<?php elseif(isset($q)): ?>
<p>No books found for "<?php echo $q; ?>"</p>
<?php endif; ?>
</div>
</body>
</html>