<?php
include '../includes/db.php';
include '../includes/header.php';

$sql = "SELECT * FROM Payments";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<h2>Payments</h2>";
    while($row = $result->fetch_assoc()) {
        echo "Payment ID: " . $row["payment_id"]. " - Amount: " . $row["amount"]. " - Date: " . $row["payment_date"]. "<br>";
    }
} else {
    echo "0 results";
}

include '../includes/footer.php';
?>
