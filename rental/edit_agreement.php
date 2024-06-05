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
    // Handle form submission to edit rental agreement
    $agreement_id = $_POST['agreement_id'];
    $property_id = $_POST['property_id'];
    $tenant_id = $_POST['tenant_id'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    // Add other agreement fields here
    
    // SQL query to update rental agreement
    $stmt = $conn->prepare("UPDATE RentalAgreements SET property_id = ?, tenant_id = ?, start_date = ?, end_date = ? WHERE agreement_id = ?");
    $stmt->bind_param("iissi", $property_id, $tenant_id, $start_date, $end_date, $agreement_id);
    $stmt->execute();
    
    echo "Rental agreement updated successfully.";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Rental Agreement</title>
    <link rel="stylesheet" type="text/css" href="/apartmentmanagement/css/styles.css">
</head>
<body>
    <?php include('../includes/header.php'); ?>
    <main>
        <h2>Edit Rental Agreement</h2>
        <form action="edit_agreement.php" method="post">
            <input type="hidden" name="agreement_id" value="<?php echo $_GET['agreement_id']; ?>">
            <input type="text" name="property_id" placeholder="Property ID" required>
            <input type="text" name="tenant_id" placeholder="Tenant ID" required>
            <input type="date" name="start_date" placeholder="Start Date" required>
            <input type="date" name="end_date" placeholder="End Date" required>
            <!-- Add other agreement fields here -->
            <button type="submit">Edit Agreement</button>
        </form>
    </main>
    <?php include('../includes/footer.php'); ?>
</body>
</html>