<?php 
include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/auth.php'); 
include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/includes/db.php'); 
include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/includes/functions.php'); 

if (!isset($_SESSION['user_id']) || 
    !(check_user_role($conn, $_SESSION['user_id'], 'administrator') || 
      check_user_role($conn, $_SESSION['user_id'], 'owner'))) {
    header("Location: /apartmentmanagement/index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Handle form submission to add rental agreement
    $property_id = $_POST['property_id'];
    $tenant_id = $_POST['tenant_id'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    // Add other agreement fields here
    
    // SQL query to insert rental agreement
    $stmt = $conn->prepare("INSERT INTO RentalAgreements (property_id, tenant_id, start_date, end_date) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiss", $property_id, $tenant_id, $start_date, $end_date);
    $stmt->execute();
    
    echo "Rental agreement added successfully.";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Rental Agreement</title>
    <link rel="stylesheet" type="text/css" href="/Apartmentmanagement/css/styles.css">
</head>
<body>
    <?php include('../includes/header.php'); ?>
    <main>
        <h2>Add Rental Agreement</h2>
        <form action="add_agreement.php" method="post">
            <input type="text" name="property_id" placeholder="Property ID" required>
            <input type="text" name="tenant_id" placeholder="Tenant ID" required>
            <input type="date" name="start_date" placeholder="Start Date" required>
            <input type="date" name="end_date" placeholder="End Date" required>
            <!-- Add other agreement fields here -->
            <button type="submit">Add Agreement</button>
        </form>
    </main>
    <?php include('../includes/footer.php'); ?>
</body>
</html>