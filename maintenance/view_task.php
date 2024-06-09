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

$user_id = $_SESSION['user_id'];
$is_admin = check_user_role($conn, $user_id, 'administrator');
$is_owner = check_user_role($conn, $user_id, 'owner');
$is_tenant = check_user_role($conn, $user_id, 'tenant');

// Prepare the SQL query based on the user's role
if ($is_admin) {
    $stmt = $conn->prepare("
        SELECT mt.task_id, mt.property_id, mt.description, mt.cost, mt.status_id, mt.reported_by, mt.created_at, mt.resolved_at,
               p.address_id, a.street, a.city, a.state, a.postal_code, a.country,
               s.status_name,
               u.username AS reported_by_username
        FROM MaintenanceTasks mt
        JOIN Properties p ON mt.property_id = p.property_id
        JOIN Addresses a ON p.address_id = a.address_id
        JOIN MaintenanceStatuses s ON mt.status_id = s.status_id
        JOIN Users u ON mt.reported_by = u.user_id
    ");
} else if ($is_owner) {
    $stmt = $conn->prepare("
        SELECT mt.task_id, mt.property_id, mt.description, mt.cost, mt.status_id, mt.reported_by, mt.created_at, mt.resolved_at,
               p.address_id, a.street, a.city, a.state, a.postal_code, a.country,
               s.status_name,
               u.username AS reported_by_username
        FROM MaintenanceTasks mt
        JOIN Properties p ON mt.property_id = p.property_id
        JOIN Addresses a ON p.address_id = a.address_id
        JOIN MaintenanceStatuses s ON mt.status_id = s.status_id
        JOIN Users u ON mt.reported_by = u.user_id
        WHERE p.owner_id = ?
    ");
    $stmt->bind_param("i", $user_id);
} else if ($is_tenant) {
    $stmt = $conn->prepare("
        SELECT mt.task_id, mt.property_id, mt.description, mt.cost, mt.status_id, mt.reported_by, mt.created_at, mt.resolved_at,
               p.address_id, a.street, a.city, a.state, a.postal_code, a.country,
               s.status_name,
               u.username AS reported_by_username
        FROM MaintenanceTasks mt
        JOIN Properties p ON mt.property_id = p.property_id
        JOIN Addresses a ON p.address_id = a.address_id
        JOIN MaintenanceStatuses s ON mt.status_id = s.status_id
        JOIN Users u ON mt.reported_by = u.user_id
        JOIN RentalAgreements ra ON p.property_id = ra.property_id
        JOIN Tenants t ON ra.tenant_id = t.tenant_id
        WHERE t.user_id = ?
    ");
    $stmt->bind_param("i", $user_id);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Maintenance Tasks</title>
    <link rel="stylesheet" type="text/css" href="/apartmentmanagement/css/styles.css">
</head>
<body>
    <?php include('../includes/header.php'); ?>
    <main>
        <h2>View Maintenance Tasks</h2>
        <table>
            <?php if ($result->num_rows > 0): ?>
                <thead>
                    <tr>
                        <th>Task ID</th>
                        <th>Property Address</th>
                        <th>Description</th>
                        <th>Cost</th>
                        <th>Status</th>
                        <th>Reported By</th>
                        <th>Created At</th>
                        <th>Resolved At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['task_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['street'] . ', ' . $row['city'] . ', ' . $row['state'] . ', ' . $row['postal_code'] . ', ' . $row['country']); ?></td>
                        <td><?php echo htmlspecialchars($row['description']); ?></td>
                        <td><?php echo htmlspecialchars($row['cost']); ?></td>
                        <td><?php echo htmlspecialchars($row['status_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['reported_by_username']); ?></td>
                        <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                        <td><?php echo htmlspecialchars($row['resolved_at']); ?></td>
                        <td>
                            <a href="/apartmentmanagement/maintenance/task_details.php?task_id=<?php echo htmlspecialchars($row['task_id']); ?>">Details</a>
                            <?php if ($is_admin || $is_owner): ?>
                                <a href="edit_task.php?task_id=<?= $row['task_id'] ?>">Edit</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            <?php else: ?>
                <tr>
                    <td colspan="9">
                        <?php if ($is_owner || $is_tenant): ?> You don't have any maintenance tasks.<?php else: ?> Nothing to show yet.<?php endif; ?>
                    </td>
                </tr>
            <?php endif; ?>
        </table>
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
