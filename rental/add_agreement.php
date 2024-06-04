<?php
include '../includes/db.php';
include '../includes/header.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $property_id = $_POST['property_id'];
    $tenant_id = $_POST['tenant_id'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $rent_amount = $_POST['rent_amount'];
    $security_deposit = $_POST['security_deposit'];

    $sql = "INSERT INTO RentalAgreements (property_id, tenant_id, start_date, end_date, rent_amount, security_deposit) 
            VALUES ('$property_id', '$tenant_id', '$start_date', '$end_date', '$rent_amount', '$security_deposit')";

    if ($conn->query($sql) === TRUE) {
        echo "New rental agreement added successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>

<h2>Add New Rental Agreement</h2>
<form method="post" action="">
    Property ID: <input type="text" name="property_id"><br>
    Tenant ID: <input type="text" name="tenant_id"><br>
    Start Date: <input type="date" name="start_date"><br>
    End Date: <input type="date" name="end_date"><br>
    Rent Amount: <input type="text" name="rent_amount"><br>
    Security Deposit: <input type="text" name="security_deposit"><br>
    <input type="submit" value="Add Agreement">
</form>

<?php include '../includes/footer.php'; ?>
