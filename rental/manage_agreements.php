<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Agreements</title>
    <link rel="stylesheet" type="text/css" href="/apartmentmanagement/css/styles.css">
</head>
<body>

    <?php include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/includes/header.php'); ?>

    <main>
        
    <?php include('../auth.php'); ?>
    <?php
    include '../includes/db.php';

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
    ?>

    </main>

    <?php include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/includes/footer.php'); ?>

</body>
</html>