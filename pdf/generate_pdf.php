<?php
require('../pdf/fpdf.php');
include '../includes/db.php';

class PDF extends FPDF {
    function Header() {
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, 'Property Management Report', 0, 1, 'C');
        $this->Ln(10);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
    }

    function ChapterTitle($num, $label) {
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, "Chapter $num : $label", 0, 1, 'L');
        $this->Ln(4);
    }

    function ChapterBody($body) {
        $this->SetFont('Arial', '', 12);
        $this->MultiCell(0, 10, $body);
        $this->Ln();
    }

    function AddChapter($num, $title, $body) {
        $this->AddPage();
        $this->ChapterTitle($num, $title);
        $this->ChapterBody($body);
    }
}

$pdf = new PDF();
$pdf->SetTitle('Property Management Report');

$sql_properties = "SELECT * FROM Properties";
$result_properties = $conn->query($sql_properties);
$body = "";

if ($result_properties->num_rows > 0) {
    while($row = $result_properties->fetch_assoc()) {
        $body .= "ID: " . $row["property_id"] . "\n";
        $body .= "Description: " . $row["description"] . "\n";
        $body .= "Rental Price: " . $row["rental_price"] . "\n\n";
    }
} else {
    $body = "No properties found.";
}

$pdf->AddChapter(1, 'Properties', $body);

$pdf->Output();
?>
