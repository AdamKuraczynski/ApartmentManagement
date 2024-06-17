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

if (!isset($_GET['task_id'])) {
    header("Location: /apartmentmanagement/maintenance/view_task.php");
    exit();
}

$task_id = intval($_GET['task_id']);
$user_id = $_SESSION['user_id'];
$is_admin = check_user_role($conn, $user_id, 'administrator');
$is_owner = check_user_role($conn, $user_id, 'owner');
$is_tenant = check_user_role($conn, $user_id, 'tenant');

$stmt = $conn->prepare("
    SELECT mt.task_id, mt.property_id, mt.description, mt.cost, mt.status_id, mt.reported_by, mt.created_at, mt.resolved_at,
           p.owner_id,
           a.street, a.city, a.state, a.postal_code, a.country,
           pt.type_name,
           u.username AS owner_username, u.email AS owner_email,
           ud.first_name AS owner_first_name, ud.last_name AS owner_last_name,
           s.status_name,
           r.username AS reported_by_username
    FROM MaintenanceTasks mt
    JOIN Properties p ON mt.property_id = p.property_id
    JOIN Addresses a ON p.address_id = a.address_id
    JOIN PropertyTypes pt ON p.type_id = pt.type_id
    JOIN Users u ON p.owner_id = u.user_id
    JOIN UserDetails ud ON u.user_id = ud.user_id
    JOIN MaintenanceStatuses s ON mt.status_id = s.status_id
    JOIN Users r ON mt.reported_by = r.user_id
    WHERE mt.task_id = ?
");

$stmt->bind_param("i", $task_id);
$stmt->execute();
$result = $stmt->get_result();
$task = $result->fetch_assoc();

if (!$task) {
    echo "Task not found.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance Task Details</title>
    <link rel="stylesheet" type="text/css" href="/apartmentmanagement/css/styles.css">
</head>
<body>
    <?php include('../includes/header.php'); ?>
    <main>
        <h2>Maintenance Task Details</h2>
        <div class="show-details">
            <div class="details-section">
                <h3>General Information</h3>
                <p><strong>Task ID:</strong> <?php echo htmlspecialchars($task['task_id']); ?></p>
                <p><strong>Property ID:</strong> <?php echo htmlspecialchars($task['property_id']); ?></p>
                <p><strong>Description:</strong> <?php echo htmlspecialchars($task['description']); ?></p>
                <p><strong>Cost:</strong> $<?php echo htmlspecialchars($task['cost']); ?></p>
                <p><strong>Status:</strong> <?php echo htmlspecialchars($task['status_name']); ?></p>
                <p><strong>Reported By:</strong> <?php echo htmlspecialchars($task['reported_by_username']); ?></p>
                <p><strong>Created At:</strong> <?php echo htmlspecialchars($task['created_at']); ?></p>
                <p><strong>Resolved At:</strong> <?php echo htmlspecialchars($task['resolved_at']); ?></p>
            </div>
            <div class="details-section">
                <h3>Property Address</h3>
                <p><strong>Street:</strong> <?php echo htmlspecialchars($task['street']); ?></p>
                <p><strong>City:</strong> <?php echo htmlspecialchars($task['city']); ?></p>
                <p><strong>State:</strong> <?php echo htmlspecialchars($task['state']); ?></p>
                <p><strong>Postal Code:</strong> <?php echo htmlspecialchars($task['postal_code']); ?></p>
                <p><strong>Country:</strong> <?php echo htmlspecialchars($task['country']); ?></p>
            </div>
            <div class="details-section">
                <h3>Owner Information</h3>
                <p><strong>Username:</strong> <?php echo htmlspecialchars($task['owner_username']); ?></p>
                <p><strong>First Name:</strong> <?php echo htmlspecialchars($task['owner_first_name']); ?></p>
                <p><strong>Last Name:</strong> <?php echo htmlspecialchars($task['owner_last_name']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($task['owner_email']); ?></p>
            </div>
        </div>
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
