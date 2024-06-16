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

$message = '';

$task_id = isset($_GET['task_id']) ? $_GET['task_id'] : (isset($_POST['task_id']) ? $_POST['task_id'] : null);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $task_id) {
    $description = sanitize_input($_POST['description']);
    $reported_by = $_SESSION['user_id'];
    $status_id = isset($_POST['status_id']) ? sanitize_input($_POST['status_id']) : null;

    if ($status_id) {
        $update_query = "UPDATE MaintenanceTasks SET description = ?, status_id = ? WHERE task_id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("sii", $description, $status_id, $task_id);
    } else {
        $update_query = "UPDATE MaintenanceTasks SET description = ? WHERE task_id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("si", $description, $task_id);
    }

    if ($stmt->execute()) {
        $message = "Successfully updated maintenance task.";

        $query = "SELECT * FROM MaintenanceTasks WHERE task_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $task_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $task = $result->fetch_assoc();
    } else {
        $message = "Error: " . $conn->error;
    }
} else if ($task_id) {
    $query = "SELECT * FROM MaintenanceTasks WHERE task_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $task_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $task = $result->fetch_assoc();

    if (!$task) {
        $task_id = null;
    }
}

$properties_query = "SELECT property_id, description FROM Properties";
$properties_result = $conn->query($properties_query);

$statuses_query = "SELECT status_id, status_name FROM MaintenanceStatuses";
$statuses_result = $conn->query($statuses_query);

$is_admin = check_user_role($conn, $_SESSION['user_id'], 'administrator');
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Maintenance Task</title>
    <link rel="stylesheet" type="text/css" href="/apartmentmanagement/css/styles.css">
</head>
<body>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/includes/header.php'); ?>
<main>
    <h1>Edit Maintenance Task</h1>
    <?php if ($message): ?>
        <p><?= $message ?></p>
    <?php endif; ?>
    <form method="post">
        <input type="hidden" id="task_id" name="task_id" value="<?= htmlspecialchars($task_id) ?>">
        <br>
        <?php if ($task_id && $task): ?>
            <label for="property_id">Property:</label>
            <input type="text" id="property_id" name="property_id" value="<?= htmlspecialchars($task['property_id']) ?>" readonly>
            <br>
            <label for="description">Description:</label>
            <textarea id="description" name="description" required><?= htmlspecialchars($task['description']) ?></textarea>
            <br>
            <?php if ($is_admin): ?>
                <label for="status_id">Status:</label>
                <select id="status_id" name="status_id">
                    <?php while ($row = $statuses_result->fetch_assoc()): ?>
                        <option value="<?= htmlspecialchars($row['status_id']) ?>" <?= $row['status_id'] == $task['status_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($row['status_name']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            <?php endif; ?>
        <?php else: ?>
            <p>Invalid Task ID.</p>
        <?php endif; ?>
        <br>
        <input type="submit" value="Update Task">
    </form>
    <?php 
        $back_link = '/apartmentmanagement/index.php';
        if ($is_admin) {
            $back_link = '/apartmentmanagement/dashboards/administrator_dashboard.php';
        } elseif ($is_tenant) {
            $back_link = '/apartmentmanagement/dashboards/tenant_dashboard.php';
        }
    ?>
    <a class="back-button" href="<?= htmlspecialchars($back_link) ?>">Go back</a>
</main>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/includes/footer.php'); ?>
</body>
</html>
