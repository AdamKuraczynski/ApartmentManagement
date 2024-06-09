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
        SELECT ra.agreement_id, ra.property_id, ra.tenant_id, ra.start_date, ra.end_date, ra.rent_amount, ra.security_deposit, 
               p.description AS property_description,
               u.username AS tenant_username
        FROM RentalAgreements ra
        JOIN Properties p ON ra.property_id = p.property_id
        JOIN Tenants t ON ra.tenant_id = t.tenant_id
        JOIN Users u ON t.user_id = u.user_id
    ");
} else if ($is_owner) {
    $stmt = $conn->prepare("
        SELECT ra.agreement_id, ra.property_id, ra.tenant_id, ra.start_date, ra.end_date, ra.rent_amount, ra.security_deposit, 
               p.description AS property_description,
               u.username AS tenant_username
        FROM RentalAgreements ra
        JOIN Properties p ON ra.property_id = p.property_id
        JOIN Tenants t ON ra.tenant_id = t.tenant_id
        JOIN Users u ON t.user_id = u.user_id
        WHERE p.owner_id = ?
    ");
    $stmt->bind_param("i", $user_id);
} else if ($is_tenant) {
    $stmt = $conn->prepare("
        SELECT ra.agreement_id, ra.property_id, ra.tenant_id, ra.start_date, ra.end_date, ra.rent_amount, ra.security_deposit, 
               p.description AS property_description,
               u.username AS tenant_username
        FROM RentalAgreements ra
        JOIN Properties p ON ra.property_id = p.property_id
        JOIN Tenants t ON ra.tenant_id = t.tenant_id
        JOIN Users u ON t.user_id = u.user_id
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
    <title>View Rental Agreements</title>
    <link rel="stylesheet" type="text/css" href="/apartmentmanagement/css/styles.css">
</head>
<body>
    <?php include('../includes/header.php'); ?>
    <main>
        <h2>View Rental Agreements</h2>
        <table>
            <?php if ($result->num_rows > 0): ?>
                <thead>
                    <tr>
                        <th>Agreement ID</th>
                        <th>Property Description</th>
                        <th>Tenant Username</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Rent Amount</th>
                        <th>Security Deposit</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['agreement_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['property_description']); ?></td>
                        <td><?php echo htmlspecialchars($row['tenant_username']); ?></td>
                        <td><?php echo htmlspecialchars($row['start_date']); ?></td>
                        <td><?php echo htmlspecialchars($row['end_date']); ?></td>
                        <td><?php echo htmlspecialchars($row['rent_amount']); ?></td>
                        <td><?php echo htmlspecialchars($row['security_deposit']); ?></td>
                        <td>
                            <a href="/apartmentmanagement/rental/agreement_details.php?agreement_id=<?php echo htmlspecialchars($row['agreement_id']); ?>">Details</a>
                            <?php if ($is_admin || $is_owner): ?>
                                <a href="edit_agreement.php?agreement_id=<?= $row['agreement_id'] ?>">Edit</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            <?php else: ?>
                <tr>
                    <td colspan="8">
                        <?php if ($is_owner || $is_tenant): ?> You don't have any rental agreements.<?php else: ?> Nothing to show yet.<?php endif; ?>
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
