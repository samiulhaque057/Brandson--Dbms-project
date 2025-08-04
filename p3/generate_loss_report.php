<?php
require('fpdf186/fpdf.php'); // Make sure you have FPDF library
require('includes/dbconnectionone.php'); // your database connection file

// Create instance of FPDF
$pdf = new FPDF();
$pdf->AddPage();

// Set title
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'Recent Loss Events Report', 0, 1, 'C');
$pdf->Ln(10);

// Table Header
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(30, 10, 'Product', 1);
$pdf->Cell(25, 10, 'Batch', 1);
$pdf->Cell(30, 10, 'Quantity (kg)', 1);
$pdf->Cell(25, 10, 'Date', 1);
$pdf->Cell(40, 10, 'Reason', 1);
$pdf->Cell(30, 10, 'Value ($)', 1);
$pdf->Ln();

// Fetch Data
$result = $conn->query("SELECT product, batch, quantity, date, reason, value FROM loss_events ORDER BY date DESC");

$pdf->SetFont('Arial', '', 12);

while($row = $result->fetch_assoc()) {
    $pdf->Cell(30, 10, $row['product'], 1);
    $pdf->Cell(25, 10, $row['batch'], 1);
    $pdf->Cell(30, 10, $row['quantity'], 1);
    $pdf->Cell(25, 10, $row['date'], 1);
    $pdf->Cell(40, 10, $row['reason'], 1);
    $pdf->Cell(30, 10, $row['value'], 1);
    $pdf->Ln();
}

// Output PDF
$pdf->Output();
?>
