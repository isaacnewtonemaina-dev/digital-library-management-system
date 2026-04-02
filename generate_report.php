<?php
session_start();
include 'db.php';

// Safe-check for the FPDF library
$fpdf_file = 'fpdf/fpdf.php';
if (!file_exists($fpdf_file)) {
    die("FATAL ERROR: The file 'fpdf.php' is missing from your 'fpdf' folder. Please re-upload it to /htdocs/fpdf/fpdf.php");
}
require($fpdf_file);

// Security Check
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'librarian'){
    header("Location: login.php");
    exit();
}

// Initialize PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'Stone Haven - Inventory Report', 0, 1, 'C');
$pdf->Ln(10);

// Table Header
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetFillColor(230, 230, 230);
$pdf->Cell(20, 10, 'ID', 1, 0, 'C', true);
$pdf->Cell(85, 10, 'Book Title', 1, 0, 'C', true);
$pdf->Cell(45, 10, 'Author', 1, 0, 'C', true);
$pdf->Cell(40, 10, 'Shelf', 1, 1, 'C', true);

$pdf->SetFont('Arial', '', 10);

// THE KEY FIX: Using 'book_title' instead of 'title'
$query = "SELECT id, book_title, author, shelf FROM books ORDER BY id ASC";
$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()){
        $pdf->Cell(20, 10, $row['id'], 1, 0, 'C');
        $pdf->Cell(85, 10, substr($row['book_title'], 0, 45), 1, 0, 'L');
        $pdf->Cell(45, 10, substr($row['author'], 0, 25), 1, 0, 'L');
        $pdf->Cell(40, 10, $row['shelf'], 1, 1, 'C');
    }
} else {
    $pdf->Cell(190, 10, 'No books found or database error: ' . $conn->error, 1, 1, 'C');
}

$pdf->Output('I', 'Stone_Haven_Report.pdf');
?>