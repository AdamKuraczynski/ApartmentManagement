<?php 
include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/auth.php'); 
include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/includes/db.php'); 
include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/includes/functions.php'); 

if (!isset($_SESSION['user_id']) || 
    !(check_user_role($conn, $_SESSION['user_id'], 'administrator') || 
      check_user_role($conn, $_SESSION['user_id'], 'tenant'))) {
    header("Location: /apartmentmanagement/index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Handle form submission to add maintenance task
    $property_id = $_POST['property_id'];
    $description = $_POST['description'];
    $cost = $_POST['cost'];
    $status_id = $_POST['status_id'];
    $reported_by = $_SESSION['user_id'];
    $created_at = date('Y-m-d H:i:s');
    
    // SQL query to insert maintenance task
    $stmt = $conn->prepare("INSERT INTO MaintenanceTasks (property_id, description, cost, status_id, reported_by, created_at) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issdis", $property_id, $description, $cost, $status_id, $reported_by, $created_at);
    $stmt->execute();
    
    echo "Maintenance task added successfully.";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Maintenance Task</title>
    <link rel="stylesheet" type="text/css" href="/Apartmentmanagement/css/styles.css">
</head>
<body>
    <?php include('../includes/header.php'); ?>
    <main>
        <h2>Add Maintenance Task</h2>
        <form action="add_task.php" method="post">
            <input type="text" name="property_id" placeholder="Property ID" required>
            <textarea name="description" placeholder="Description" required></textarea>
            <input type="text" name="cost" placeholder="Cost" required>
            <input type="text" name="status_id" placeholder="Status ID" required>
            <button type="submit">Add Task</button>
        </form>
        <?php 
            $back_link = '/apartmentmanagement/index.php';
            if ($is_admin) {
                $back_link = '/apartmentmanagement/dashboards/administrator_dashboard.php';
            } elseif ($is_tenant) {
                $back_link = '/apartmentmanagement/dashboards/tenant_dashboard.php';
            } elseif ($is_owner) {
                $back_link = '/apartmentmanagement/dashboards/owner_dashboard.php';
            }
        ?>
        <a class="back-button" href="<?php echo $back_link; ?>">Go back</a>
    </main>
    <?php include('../includes/footer.php'); ?>
</body>
</html>