<?php
require('../pdf/fpdf.php');
include '../includes/db.php';
include('../auth.php');

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

// Fetch and add Properties
$sql_properties = "SELECT * FROM Properties";
$result_properties = $conn->query($sql_properties);
$body_properties = "";

if ($result_properties->num_rows > 0) {
    while($row = $result_properties->fetch_assoc()) {
        $body_properties .= "ID: " . $row["property_id"] . "\n";
        $body_properties .= "Owner ID: " . $row["owner_id"] . "\n";
        $body_properties .= "Address ID: " . $row["address_id"] . "\n";
        $body_properties .= "Type ID: " . $row["type_id"] . "\n";
        $body_properties .= "Number of Rooms: " . $row["number_of_rooms"] . "\n";
        $body_properties .= "Size: " . $row["size"] . "\n";
        $body_properties .= "Rental Price: " . $row["rental_price"] . "\n";
        $body_properties .= "Description: " . $row["description"] . "\n\n";
    }
} else {
    $body_properties = "No properties found.";
}
$pdf->AddChapter(1, 'Properties', $body_properties);

// Fetch and add Rental Agreements
$sql_agreements = "SELECT * FROM RentalAgreements";
$result_agreements = $conn->query($sql_agreements);
$body_agreements = "";

if ($result_agreements->num_rows > 0) {
    while($row = $result_agreements->fetch_assoc()) {
        $body_agreements .= "ID: " . $row["agreement_id"] . "\n";
        $body_agreements .= "Property ID: " . $row["property_id"] . "\n";
        $body_agreements .= "Tenant ID: " . $row["tenant_id"] . "\n";
        $body_agreements .= "Start Date: " . $row["start_date"] . "\n";
        $body_agreements .= "End Date: " . $row["end_date"] . "\n";
        $body_agreements .= "Rent Amount: " . $row["rent_amount"] . "\n";
        $body_agreements .= "Security Deposit: " . $row["security_deposit"] . "\n\n";
    }
} else {
    $body_agreements = "No rental agreements found.";
}
$pdf->AddChapter(2, 'Rental Agreements', $body_agreements);

// Fetch and add Maintenance Tasks
$sql_tasks = "SELECT * FROM MaintenanceTasks";
$result_tasks = $conn->query($sql_tasks);
$body_tasks = "";

if ($result_tasks->num_rows > 0) {
    while($row = $result_tasks->fetch_assoc()) {
        $body_tasks .= "ID: " . $row["task_id"] . "\n";
        $body_tasks .= "Property ID: " . $row["property_id"] . "\n";
        $body_tasks .= "Description: " . $row["description"] . "\n";
        $body_tasks .= "Cost: " . $row["cost"] . "\n";
        $body_tasks .= "Status ID: " . $row["status_id"] . "\n";
        $body_tasks .= "Reported By: " . $row["reported_by"] . "\n";
        $body_tasks .= "Created At: " . $row["created_at"] . "\n";
        $body_tasks .= "Resolved At: " . $row["resolved_at"] . "\n\n";
    }
} else {
    $body_tasks = "No maintenance tasks found.";
}
$pdf->AddChapter(3, 'Maintenance Tasks', $body_tasks);

// Fetch and add Users
$sql_users = "SELECT * FROM Users";
$result_users = $conn->query($sql_users);
$body_users = "";

if ($result_users->num_rows > 0) {
    while($row = $result_users->fetch_assoc()) {
        $body_users .= "ID: " . $row["user_id"] . "\n";
        $body_users .= "Username: " . $row["username"] . "\n";
        $body_users .= "Email: " . $row["email"] . "\n\n";
    }
} else {
    $body_users = "No users found.";
}
$pdf->AddChapter(4, 'Users', $body_users);

// Output PDF
$pdf->Output();
?>
