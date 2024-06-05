<?php
include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/auth.php'); 
include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/includes/db.php'); 
include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/includes/functions.php'); 

// Fetch rental agreement
$stmt = $conn->prepare("SELECT * FROM RentalAgreements WHERE agreement_id = ?");
$stmt->bind_param("i", $_GET['agreement_id']);
$stmt->execute();
$result = $stmt->get_result();
$agreement = $result->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Rental Agreement</title>
    <link rel="stylesheet" type="text/css" href="/apartmentmanagement/css/styles.css">
</head>
<body>
    <?php include('../includes/header.php'); ?>
    <main>
        <h2>View Rental Agreement</h2>
        <p>Property ID: <?php echo $agreement['property_id']; ?></p>
        <p>Tenant ID: <?php echo $agreement['tenant_id']; ?></p>
        <p>Start Date: <?php echo $agreement['start_date']; ?></p>
        <p>End Date: <?php echo $agreement['end_date']; ?></p>
        <!-- Add other agreement details here -->
    </main>
    <?php include('../includes/footer.php'); ?>
</body>
</html>