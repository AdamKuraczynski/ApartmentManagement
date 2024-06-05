<?php 
include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/auth.php'); 
include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/includes/db.php'); 
include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/includes/functions.php'); 

if (!isset($_SESSION['user_id']) || 
    !(check_user_role($conn, $_SESSION['user_id'], 'administrator'))) {
    header("Location: /apartmentmanagement/index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Handle form submission to edit maintenance task
    $task_id = $_POST['task_id'];
    $property_id = $_POST['property_id'];
    $description = $_POST['description'];
    $cost = $_POST['cost'];
    $status_id = $_POST['status_id'];
    
    // SQL query to update maintenance task
    $stmt = $conn->prepare("UPDATE MaintenanceTasks SET property_id = ?, description = ?, cost = ?, status_id = ? WHERE task_id = ?");
    $stmt->bind_param("issii", $property_id, $description, $cost, $status_id, $task_id);
    $stmt->execute();
    
    echo "Maintenance task updated successfully.";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Maintenance Task</title>
    <link rel="stylesheet" type="text/css" href="/apartmentmanagement/css/styles.css">
</head>
<body>
    <?php include('../includes/header.php'); ?>
    <main>
        <h2>Edit Maintenance Task</h2>
        <form action="edit_task.php" method="post">
            <input type="hidden" name="task_id" value="<?php echo $_GET['task_id']; ?>">
            <input type="text" name="property_id" placeholder="Property ID" required>
            <textarea name="description" placeholder="Description" required></textarea>
            <input type="text" name="cost" placeholder="Cost" required>
            <input type="text" name="status_id" placeholder="Status ID" required>
            <button type="submit">Edit Task</button>
        </form>
    </main>
    <?php include('../includes/footer.php'); ?>
</body>
</html>