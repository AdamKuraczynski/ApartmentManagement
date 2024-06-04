<?php include('../auth.php'); ?>
<?php
include '../includes/db.php';
include '../includes/header.php';

$sql = "SELECT * FROM RentalAgreements";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<h2>Rental Agreements</h2>";
    while($row = $result->fetch_assoc()) {
        echo "ID: " . $row["agreement_id"]. " - Property ID: " . $row["property_id"]. " - Tenant ID: " . $row["tenant_id"]. " - Rent: " . $row["rent_amount"]. "<br>";
    }
} else {
    echo "0 results";
}

include '../includes/footer.php';
?>
