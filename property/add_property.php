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
    // Handle form submission to add property
    $property_name = $_POST['property_name'];
    // Add other property fields here
    
    // SQL query to insert property
    $stmt = $conn->prepare("INSERT INTO Properties (property_name, ...) VALUES (?, ...)");
    $stmt->bind_param("s", $property_name);
    $stmt->execute();
    
    echo "Property added successfully.";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Property</title>
    <link rel="stylesheet" type="text/css" href="/Apartmentmanagement/css/styles.css">
</head>
<body>
    <?php include('../includes/header.php'); ?>
    <main>
        <h2>Add Property</h2>
        <form action="add_property.php" method="post">
            <input type="text" name="property_name" placeholder="Property Name" required>
            <!-- Add other property fields here -->
            <button type="submit">Add Property</button>
        </form>
    </main>
    <?php include('../includes/footer.php'); ?>
</body>
</html>