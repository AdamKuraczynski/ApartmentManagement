<?php 
include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/auth.php'); 
include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/includes/functions.php'); 

if (!isset($_SESSION['user_id']) || !check_user_role($conn, $_SESSION['user_id'], 'administrator')) {
    header("Location: /apartmentmanagement/index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrator Dashboard</title>
    <link rel="stylesheet" type="text/css" href="/Apartmentmanagement/css/styles.css">
</head>
<body>
    <?php include('../includes/header.php'); ?>
    <main>
        <h2>Administrator Dashboard</h2>
        <br/>
        <div class = "dashboard">
        
            <h3> Manage All Properties </h3>
            <ul>
                <li><a href="../property/add_property.php">Add Property</a></li>
                <li><a href="../property/view_property.php">View Properties</a></li>
            </ul>
            
            <h3> Manage Agreements </h3>
            <ul>
                <li><a href="../rental/add_agreement.php">Add Rental Agreement</a></li>
                <li><a href="../rental/view_agreement.php">View Rental Agreements</a></li>
            </ul>
       
            <h3> Manage Aaintenance Tasks </h3>
            <ul>
                <li><a href="../maintenance/add_task.php">Add Maintenance Task</a></li>
                <li><a href="../maintenance/view_task.php">View Maintenance Tasks</a></li>
            </ul> 
            
            <h3> Manage Payments</h3>
            <ul>
                <li><a href="../payments/add_payment.php">Add Payment</a></li>
                <li><a href="../payments/view_payment.php">View Payments</a></li>
            </ul> 
       
            <h3> Manage Documents </h3>
            <ul>
                <li><a href="../documents/upload_document.php">Upload Document</a></li>
                <li><a href="../documents/view_document.php">View Documents</a></li>
            </ul>
       
            <h3> Notifications </h3>
            <ul>
                <li><a href="../notifications/view_notifications.php">View Notifications</a></li>
            </ul>
       
            <h3> Report </h3>
            <ul>
                <li><a href="../pdf/generate_report.php">Generate Report</a></li>
            </ul>

            <h3> Manage Users </h3>
            <ul>
                <li><a href="../users/modify_roles.php">Modify User Roles</a></li>
                <li><a href="../users/view_user.php">View all Users</a></li>
            </ul>
        </div>
    </main>
    <?php include('../includes/footer.php'); ?>
</body>
</html>
