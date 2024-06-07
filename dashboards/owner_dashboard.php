<?php 
include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/auth.php'); 
include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/includes/functions.php'); 

if (!isset($_SESSION['user_id']) || !check_user_role($conn, $_SESSION['user_id'], 'owner')) {
    header("Location: /apartmentmanagement/index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Owner Dashboard</title>
    <link rel="stylesheet" type="text/css" href="/Apartmentmanagement/css/styles.css">
</head>
<body>
    <?php include('../includes/header.php'); ?>
    <main>
        <h2>Owner Dashboard</h2>
        <ul>
            <li><a href="../property/add_property.php">Add Property</a></li>
            <li><a href="../property/edit_property.php">Edit Property</a></li>
            <li><a href="../property/view_property.php">View Property</a></li>
            <li><a href="../rental/add_agreement.php">Add Rental Agreement</a></li>
            <li><a href="../rental/edit_agreement.php">Edit Rental Agreement</a></li>
            <li><a href="../rental/view_agreement.php">View Rental Agreement</a></li>
            <li><a href="../documents/upload_document.php">Upload Document</a></li>
            <li><a href="../documents/view_document.php">View Document</a></li>
            <li><a href="../notifications/view_notifications.php">View Notifications</a></li>
            <li><a href="../maintenance/view_task.php">View Maintenance Tasks</a></li>
        </ul>
    </main>
    <?php include('../includes/footer.php'); ?>
</body>
</html>
