<?php
require('fpdf/fpdf.php');

// Database Connection (using your existing Stone Haven credentials)
include('db_conn.php'); 

class PDF extends FPDF {
    // Page header
    function Header() {
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(0, 10, 'Stone Haven Digital Library', 0, 1, 'C');
        $this->SetFont('Arial', 'I', 10);
        $this->Cell(0, 10, 'Inventory & Action Report - ' . date('M d, Y H:i'), 0, 1, 'C');
        $this->Ln(10);
    }

    // Table Header Helper
    function FancyTableHead($header) {
        $this->SetFillColor(232, 232, 232); // Light grey background
        $this->SetFont('Arial', 'B', 10);
        foreach($header as $col) {
            $this->Cell(60, 10, $col, 1, 0, 'C', true);
        }
        $this->Ln();
    }
}

$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 10);

// --- SECTION 1: ACTION LOGS ---
$pdf->Cell(0, 10, 'Recent Inventory Action Logs', 0, 1, 'L');
$header = array('Time', 'Librarian', 'Action');
$pdf->FancyTableHead($header);

// Fetching data from your logs table
$query = "SELECT log_time, librarian_name, action_type FROM action_logs ORDER BY log_time DESC LIMIT 10";
$result = mysqli_query($conn, $query);

while($row = mysqli_fetch_assoc($result)) {
    $pdf->Cell(60, 8, $row['log_time'], 1);
    $pdf->Cell(60, 8, $row['librarian_name'], 1);
    $pdf->Cell(60, 8, $row['action_type'], 1);
    $pdf->Ln();
}

$pdf->Ln(15);

// --- SECTION 2: LOW STOCK ALERTS ---
$pdf->SetTextColor(200, 0, 0); // Red text for alerts
$pdf->Cell(0, 10, 'Low Stock Alerts (Restock Needed)', 0, 1, 'L');
$pdf->SetTextColor(0); // Reset to black

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(90, 8, 'Shelf Location', 1);
$pdf->Cell(90, 8, 'Current Count', 1);
$pdf->Ln();

// Fetching Low Stock items (e.g., where count <= 1)
$stock_query = "SELECT shelf_location, book_count FROM inventory WHERE book_count <= 1";
$stock_result = mysqli_query($conn, $stock_query);

$pdf->SetFont('Arial', '', 10);
while($stock = mysqli_fetch_assoc($stock_result)) {
    $pdf->Cell(90, 8, $stock['shelf_location'], 1);
    $pdf->Cell(90, 8, $stock['book_count'] . ' Books', 1);
    $pdf->Ln();
}

// Output the PDF to the browser
$pdf->Output('D', 'StoneHaven_Report.pdf');
?>