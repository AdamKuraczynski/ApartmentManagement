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
    // Handle form submission to edit property
    $property_id = $_POST['property_id'];
    $property_name = $_POST['property_name'];
    // Add other property fields here
    
    // SQL query to update property
    $stmt = $conn->prepare("UPDATE Properties SET property_name = ? WHERE property_id = ?");
    $stmt->bind_param("si", $property_name, $property_id);
    $stmt->execute();
    
    echo "Property updated successfully.";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Property</title>
    <link rel="stylesheet" type="text/css" href="/apartmentmanagement/css/styles.css">
</head>
<body>
    <?php include('../includes/header.php'); ?>
    <main>
        <h2>Edit Property</h2>
        <form action="edit_property.php" method="post">
            <input type="hidden" name="property_id" value="<?php echo $_GET['property_id']; ?>">
            <input type="text" name="property_name" placeholder="Property Name" required>
            <!-- Add other property fields here -->
            <button type="submit">Edit Property</button>
        </form>
    </main>
    <?php include('../includes/footer.php'); ?>
</body>
</html>