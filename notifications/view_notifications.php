<?php
include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/auth.php');
include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/includes/db.php');
include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/includes/functions.php');

if ($is_tenant) {
    // Get the tenant_id for the current user
    $stmt = $conn->prepare("SELECT tenant_id FROM Tenants WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($tenant_id);
    $stmt->fetch();
    $stmt->close();

    if ($tenant_id) {
        $stmt = $conn->prepare("
            SELECT Payments.*, RentalAgreements.tenant_id 
            FROM Payments 
            JOIN RentalAgreements ON Payments.agreement_id = RentalAgreements.agreement_id 
            WHERE RentalAgreements.tenant_id = ? 
            AND Payments.payment_date BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 200 WEEK)");
        $stmt->bind_param("i", $tenant_id);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($payment = $result->fetch_assoc()) {
            // Check if a notification for this payment was created in the last 3 days
            $check_stmt = $conn->prepare("
                SELECT COUNT(*) 
                FROM Notifications 
                WHERE user_id = ? 
                AND message = ? 
                AND created_at >= DATE_SUB(NOW(), INTERVAL 3 DAY)");
            $message = 'Upcoming payment due: ' . $payment['payment_date'] . ' Amount: ' . $payment['amount'];
            $check_stmt->bind_param("is", $user_id, $message);
            $check_stmt->execute();
            $check_stmt->bind_result($count);
            $check_stmt->fetch();
            $check_stmt->close();

            if ($count == 0) {
                $stmt_insert = $conn->prepare("INSERT INTO Notifications (user_id, message, created_at) VALUES (?, ?, NOW())");
                $stmt_insert->bind_param("is", $user_id, $message);
                $stmt_insert->execute();
                $stmt_insert->close();
            }
        }
        $stmt->close();
    }
}

if ($is_owner) {
    $stmt = $conn->prepare("
        SELECT Payments.*, RentalAgreements.tenant_id 
        FROM Payments 
        JOIN RentalAgreements ON Payments.agreement_id = RentalAgreements.agreement_id 
        JOIN Properties ON RentalAgreements.property_id = Properties.property_id 
        WHERE Properties.owner_id = ? 
        AND Payments.payment_date < NOW()");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($payment = $result->fetch_assoc()) {
        // Check if a notification for this payment was created in the last 3 days
        $check_stmt = $conn->prepare("
            SELECT COUNT(*) 
            FROM Notifications 
            WHERE user_id = ? 
            AND message = ? 
            AND created_at >= DATE_SUB(NOW(), INTERVAL 3 DAY)");
        $message = 'Overdue payment for tenant: ' . $payment['tenant_id'] . ' Amount: ' . $payment['amount'];
        $check_stmt->bind_param("is", $user_id, $message);
        $check_stmt->execute();
        $check_stmt->bind_result($count);
        $check_stmt->fetch();
        $check_stmt->close();

        if ($count == 0) {
            $stmt_insert = $conn->prepare("INSERT INTO Notifications (user_id, message, created_at) VALUES (?, ?, NOW())");
            $stmt_insert->bind_param("is", $user_id, $message);
            $stmt_insert->execute();
            $stmt_insert->close();
        }
    }
    $stmt->close();
}

// Create notifications for admins about new maintenance tasks or unresolved tasks
if ($is_admin) {
    // Notify about new maintenance tasks
    $stmt = $conn->prepare("
        SELECT task_id, description, reported_by, created_at
        FROM MaintenanceTasks
        WHERE created_at >= DATE_SUB(NOW(), INTERVAL 3 DAY)");
    $stmt->execute();
    $result = $stmt->get_result();
    while ($task = $result->fetch_assoc()) {
        // Check if a notification for this maintenance task was created in the last 3 days
        $check_stmt = $conn->prepare("
            SELECT COUNT(*)
            FROM Notifications
            WHERE user_id = ?
            AND message = ?
            AND created_at >= DATE_SUB(NOW(), INTERVAL 3 DAY)");
        $message = 'New maintenance task reported: ' . $task['description'] . ' by user: ' . $task['reported_by'];
        $check_stmt->bind_param("is", $user_id, $message);
        $check_stmt->execute();
        $check_stmt->bind_result($count);
        $check_stmt->fetch();
        $check_stmt->close();

        if ($count == 0) {
            $stmt_insert = $conn->prepare("INSERT INTO Notifications (user_id, message, created_at) VALUES (?, ?, NOW())");
            $stmt_insert->bind_param("is", $user_id, $message);
            $stmt_insert->execute();
            $stmt_insert->close();
        }
    }
    $stmt->close();

    // Notify about unresolved maintenance tasks
    $stmt = $conn->prepare("
        SELECT task_id, description, reported_by, created_at
        FROM MaintenanceTasks
        WHERE status_id != 3 AND created_at < NOW()");
    $stmt->execute();
    $result = $stmt->get_result();
    while ($task = $result->fetch_assoc()) {
        // Check if a notification for this maintenance task was created in the last 3 days
        $check_stmt = $conn->prepare("
            SELECT COUNT(*)
            FROM Notifications
            WHERE user_id = ?
            AND message = ?
            AND created_at >= DATE_SUB(NOW(), INTERVAL 3 DAY)");
        $message = 'Unresolved maintenance task: ' . $task['description'] . ' reported by user: ' . $task['reported_by'];
        $check_stmt->bind_param("is", $user_id, $message);
        $check_stmt->execute();
        $check_stmt->bind_result($count);
        $check_stmt->fetch();
        $check_stmt->close();

        if ($count == 0) {
            $stmt_insert = $conn->prepare("INSERT INTO Notifications (user_id, message, created_at) VALUES (?, ?, NOW())");
            $stmt_insert->bind_param("is", $user_id, $message);
            $stmt_insert->execute();
            $stmt_insert->close();
        }
    }
    $stmt->close();
}

// Fetch notifications for the user
$stmt = $conn->prepare("SELECT * FROM Notifications WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Notifications</title>
    <link rel="stylesheet" type="text/css" href="/Apartmentmanagement/css/styles.css">
</head>
<body>
    <?php include('../includes/header.php'); ?>
    <main>
        <h2>View Notifications</h2>
        <?php while ($notification = $result->fetch_assoc()) : ?>
            <div class="notification">
                <p><?php echo htmlspecialchars($notification['message']); ?></p>
                <p>Created At: <?php echo htmlspecialchars($notification['created_at']); ?></p>
                <?php if ($notification['read_at']) : ?>
                    <p>Read At: <?php echo htmlspecialchars($notification['read_at']); ?></p>
                <?php else : ?>
                    <form action="mark_as_read.php" method="post">
                        <input type="hidden" name="notification_id" value="<?php echo $notification['notification_id']; ?>">
                        <button type="submit">Mark as Read</button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
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
