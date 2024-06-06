<?php 
include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/auth.php'); 
include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/includes/db.php'); 
include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/includes/functions.php'); 

if (!isset($_SESSION['user_id']) || 
    !(check_user_role($conn, $_SESSION['user_id'], 'administrator') || 
      check_user_role($conn, $_SESSION['user_id'], 'owner') ||
      check_user_role($conn, $_SESSION['user_id'], 'tenant'))) {
    header("Location: /apartmentmanagement/index.php");
    exit();
}

// Fetch maintenance task
$stmt = $conn->prepare("SELECT * FROM MaintenanceTasks WHERE task_id = ?");
$stmt->bind_param("i", $_GET['task_id']);
$stmt->execute();
$result = $stmt->get_result();
$task = $result->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Maintenance Task</title>
    <link rel="stylesheet" type="text/css" href="/apartmentmanagement/css/styles.css">
</head>
<body>
    <?php include('../includes/header.php'); ?>
    <main>
        <h2>View Maintenance Task</h2>
            <?php if ($result->num_rows > 0): ?>
                <p>Property ID: <?php echo $task['property_id']; ?></p>
                <p>Description: <?php echo $task['description']; ?></p>
                <p>Cost: <?php echo $task['cost']; ?></p>
                <p>Status ID: <?php echo $task['status_id']; ?></p>
                <p>Reported By: <?php echo $task['reported_by']; ?></p>
                <p>Created At: <?php echo $task['created_at']; ?></p>
                <p>Resolved At: <?php echo $task['resolved_at']; ?></p>
            <?php else: ?>
                <p colspan="7">You don't have any maintenance tasks at the moment.</p>
            <?php endif; ?>
            <!-- Add other task details here -->
    </main>
    <?php include('../includes/footer.php'); ?>
</body>
</html>