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

// Prepare the SQL query based on the user's role
if ($is_admin) {
    $stmt = $conn->prepare("
        SELECT ra.agreement_id, ra.property_id, ra.tenant_id, ra.start_date, ra.end_date, ra.rent_amount, ra.security_deposit 
        FROM RentalAgreements ra
    ");
} else {
    $stmt = $conn->prepare("
        SELECT ra.agreement_id, ra.property_id, ra.tenant_id, ra.start_date, ra.end_date, ra.rent_amount, ra.security_deposit 
        FROM RentalAgreements ra 
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
    <title>View Rental Agreements</title>
    <link rel="stylesheet" type="text/css" href="/Apartmentmanagement/css/styles.css">
</head>
<body>
    <?php include('../includes/header.php'); ?>
    <main>
        <h2>View Rental Agreements</h2>
        <table>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <thead>
                        <tr>
                            <th>Agreement ID</th>
                            <th>Property ID</th>
                            <th>Tenant ID</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Rent Amount</th>
                            <th>Security Deposit</th>
                        </tr>
                    </thead>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['agreement_id']; ?></td>
                            <td><?php echo $row['property_id']; ?></td>
                            <td><?php echo $row['tenant_id']; ?></td>
                            <td><?php echo $row['start_date']; ?></td>
                            <td><?php echo $row['end_date']; ?></td>
                            <td><?php echo $row['rent_amount']; ?></td>
                            <td><?php echo $row['security_deposit']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7">You don't have any lease agreements at the moment.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
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
