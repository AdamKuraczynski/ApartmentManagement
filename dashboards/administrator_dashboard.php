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
    <title>Admin Dashboard</title>
    <link rel="stylesheet" type="text/css" href="/Apartmentmanagement/css/styles.css">
</head>
<body>
    <?php include('../includes/header.php'); ?>
    <main>
        <h2>Admin Dashboard</h2>
        <br/>
        <div class = "dashboard">
        
            <h3> Manage all properties </h3>
            <ul>
                <li><a href="../property/add_property.php">Add Property</a></li>
                <li><a href="../property/view_property.php">View Properties</a></li>
            </ul>
            
            <h3> Manage agreements </h3>
            <ul>
                <li><a href="../rental/add_agreement.php">Add Rental Agreement</a></li>
                <li><a href="../rental/edit_agreement.php">Edit Rental Agreement</a></li>
                <li><a href="../rental/view_agreement.php">View Rental Agreements</a></li>
            </ul>
       
            <h3> Manage maintenance tasks </h3>
            <ul>
                <li><a href="../maintenance/add_task.php">Add Maintenance Task</a></li>
                <li><a href="../maintenance/edit_task.php">Edit Maintenance Task</a></li>
                <li><a href="../maintenance/view_task.php">View Maintenance Task</a></li>
            </ul> 
            
            <h3> Manage payments</h3>
            <ul>
                <li><a href="../payments/add_payment.php">Add Payment</a></li>
                <li><a href="../payments/edit_payment.php">Edit Payment</a></li>
                <li><a href="../payments/view_payment.php">View Payment</a></li>
            </ul> 
       
            <h3> Manage documents </h3>
            <ul>
                <li><a href="../documents/upload_document.php">Upload Document</a></li>
                <li><a href="../documents/view_document.php">View Document</a></li>
            </ul>
       
            <h3> Manage notifications </h3>
            <ul>
                <li><a href="../notifications/view_notifications.php">View Notifications</a></li>
            </ul>
       
            <h3> Manage reports </h3>
            <ul>
                <li><a href="../pdf/generate_report.php">Generate Report</a></li>
            </ul>

            <h3> Manage users </h3>
            <ul>
                <li><a href="../roles/modify_roles.php">Modify Roles</a></li>
            </ul>
        </div>
    </main>
    <?php include('../includes/footer.php'); ?>
</body>
</html>
