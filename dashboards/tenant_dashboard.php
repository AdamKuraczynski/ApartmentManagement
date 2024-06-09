<?php 
include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/auth.php'); 
include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/includes/functions.php'); 

if (!isset($_SESSION['user_id']) || !check_user_role($conn, $_SESSION['user_id'], 'tenant')) {
    header("Location: /apartmentmanagement/index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tenant Dashboard</title>
    <link rel="stylesheet" type="text/css" href="/Apartmentmanagement/css/styles.css">
</head>
<body>
    <?php include('../includes/header.php'); ?>
    <main>
        <h2>Tenant Dashboard</h2>
        <br/>
        <div class="dashboard">
            <h3>Manage agreements</h3>
            <ul>
                <li><a href="../rental/view_agreement.php">View Rental Agreement</a></li>
            </ul>
            <h3>Manage maintenance tasks</h3>
            <ul>
                <li><a href="../maintenance/add_task.php">Add Maintenance Task</a></li>
                <li><a href="../maintenance/view_task.php">View Maintenance Task</a></li>
            </ul>
            <h3>Manage documents</h3>
            <ul>
                <li><a href="../documents/view_document.php">View Document</a></li>
            </ul>
            <h3>Manage notifications</h3>
            <ul>
                <li><a href="../notifications/view_notifications.php">View Notifications</a></li>
            </ul>
        </div>
    </main>
    <?php include('../includes/footer.php'); ?>
</body>
</html>
