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

if ($is_admin || $is_owner) {
    $query = "
        SELECT mt.task_id, mt.property_id, mt.description, mt.cost, mt.status_id, mt.reported_by, mt.created_at, mt.resolved_at,
               p.address_id, a.street, a.city, a.state, a.postal_code, a.country,
               s.status_name,
               u.username AS reported_by_username
        FROM MaintenanceTasks mt
        JOIN Properties p ON mt.property_id = p.property_id
        JOIN Addresses a ON p.address_id = a.address_id
        JOIN MaintenanceStatuses s ON mt.status_id = s.status_id
        JOIN Users u ON mt.reported_by = u.user_id
    ";
    if ($is_owner) {
        $query .= "WHERE p.owner_id = ?";
    }
    $stmt = $conn->prepare($query);
    if ($is_owner) {
        $stmt->bind_param("i", $user_id);
    }
} else if ($is_tenant) {
    $stmt = $conn->prepare("
        SELECT mt.task_id, mt.property_id, mt.description, mt.status_id, mt.created_at, mt.resolved_at,
               p.address_id, a.street, a.city, a.state, a.postal_code, a.country,
               s.status_name
        FROM MaintenanceTasks mt
        JOIN Properties p ON mt.property_id = p.property_id
        JOIN Addresses a ON p.address_id = a.address_id
        JOIN MaintenanceStatuses s ON mt.status_id = s.status_id
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
    <link rel="stylesheet" type="text/css" href="/apartmentmanagement/css/styles.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
    <script src="/apartmentmanagement/js/scripts.js"></script>
    <script>
        $(document).ready(function() {
            $('.mark-as-solved').on('click', function() {
                var taskId = $(this).data('task-id');
                var isOwner = $(this).data('is-owner');
                var popupHtml = '<div id="popup">' +
                                '<p>Mark task as solved?</p>' +
                                (isOwner ? '<label for="cost">Cost:</label><input type="text" id="cost">' : '') +
                                '<button id="confirm">Confirm</button>' +
                                '<button id="cancel">Cancel</button>' +
                                '</div>';
                $('body').append(popupHtml);
                $('#confirm').on('click', function() {
                    var cost = isOwner ? $('#cost').val() : null;
                    $.ajax({
                        url: '/apartmentmanagement/maintenance/mark_as_solved.php',
                        method: 'POST',
                        data: { task_id: taskId, cost: cost },
                        success: function(response) {
                            location.reload();
                        }
                    });
                });
                $('#cancel').on('click', function() {
                    $('#popup').remove();
                });
            });
        });
    </script>
    <style>
        #popup {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            padding: 20px;
            border: 1px solid black;
            z-index: 1000;
        }
    </style>
</head>
<body>
    <?php include('../includes/header.php'); ?>
    <main>
        <h2>View Maintenance Tasks</h2>
        <table id="tasksTable">
            <?php if ($result->num_rows > 0): ?>
                <thead>
                    <tr>
                        <th>Task ID</th>
                        <th>Property Address</th>
                        <th>Description</th>
                        <?php if ($is_admin || $is_owner): ?>
                            <th>Cost</th>
                        <?php endif; ?>
                        <th>Status</th>
                        <?php if ($is_admin || $is_owner): ?>
                            <th>Reported By</th>
                        <?php endif; ?>
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
                        <?php if ($is_admin || $is_owner): ?>
                            <td><?php echo htmlspecialchars($row['cost']); ?></td>
                        <?php endif; ?>
                        <td><?php echo htmlspecialchars($row['status_name']); ?></td>
                        <?php if ($is_admin || $is_owner): ?>
                            <td><?php echo htmlspecialchars($row['reported_by_username']); ?></td>
                        <?php endif; ?>
                        <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                        <td><?php echo htmlspecialchars($row['resolved_at']); ?></td>
                        <td>
                            <a href="/apartmentmanagement/maintenance/task_details.php?task_id=<?php echo htmlspecialchars($row['task_id']); ?>">Details</a>
                            <?php if ($is_admin || $is_tenant): ?>
                                <a href="edit_task.php?task_id=<?= htmlspecialchars($row['task_id']) ?>">Edit</a>
                            <?php endif; ?>
                            <?php if (in_array($row['status_id'], [1, 2])): ?>
                                <a href="#" class="mark-as-solved" data-task-id="<?= htmlspecialchars($row['task_id']) ?>" data-is-owner="<?= $is_owner ?>">Mark as Solved</a>
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
        <a class="back-button" href="<?php echo htmlspecialchars($back_link); ?>">Go back</a>
    </main>
    <?php include('../includes/footer.php'); ?>
</body>
</html>
