<?php 
include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/auth.php'); 
include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/includes/db.php'); 
include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/includes/functions.php'); 

// Fetch properties
$stmt = $conn->prepare("SELECT * FROM Properties WHERE property_id = ?");
$stmt->bind_param("i", $_GET['property_id']);
$stmt->execute();
$result = $stmt->get_result();
$property = $result->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Property</title>
    <link rel="stylesheet" type="text/css" href="/Apartmentmanagement/css/styles.css">
</head>
<body>
    <?php include('../includes/header.php'); ?>
    <main>
        <h2>View Property</h2>
        <p>Property Name: <?php echo $property['property_name']; ?></p>
        <!-- Add other property details here -->
    </main>
    <?php include('../includes/footer.php'); ?>
</body>
</html>