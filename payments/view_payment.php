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

$query = "SELECT * FROM Payments";
if ($is_owner) {
    $query .= " WHERE property_id IN (SELECT property_id FROM Properties WHERE owner_id = '$user_id')";
} elseif ($is_tenant) {
    $query .= " WHERE agreement_id IN (SELECT agreement_id FROM RentalAgreements WHERE tenant_id = '$user_id')";
}

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Details</title>
    <link rel="stylesheet" type="text/css" href="/apartmentmanagement/css/styles.css">
</head>
<body>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/includes/header.php'); ?>
<main>
    <h1>All Payments</h1>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Agreement ID</th>
                <th>Date</th>
                <th>Amount</th>
                <th>Type</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($payment = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $payment['payment_id'] ?></td>
                    <td><?= $payment['agreement_id'] ?></td>
                    <td><?= $payment['payment_date'] ?></td>
                    <td><?= $payment['amount'] ?></td>
                    <td><?= $payment['payment_type_id'] ?></td>
                    <td>
                        <a href="view_payment.php?id=<?= $payment['payment_id'] ?>">View</a>
                        <?php if ($is_admin || $is_owner): ?>
                            <a href="edit_payment.php?id=<?= $payment['payment_id'] ?>">Edit</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <?php if ($is_admin || $is_owner): ?>
        <a href="add_payment.php">Add Payment</a>
    <?php endif; ?>
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
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/includes/footer.php'); ?>
</body>
</html>