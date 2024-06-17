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

$is_admin = check_user_role($conn, $_SESSION['user_id'], 'administrator');
$is_owner = check_user_role($conn, $_SESSION['user_id'], 'owner');
$is_tenant = check_user_role($conn, $_SESSION['user_id'], 'tenant');

$user_id = $_SESSION['user_id'];

$query = "SELECT Payments.payment_id, Payments.agreement_id, Payments.payment_date, Payments.amount, Paymenttypes.payment_type_name, Properties.description as property_description 
          FROM Payments 
          JOIN RentalAgreements ON Payments.agreement_id = RentalAgreements.agreement_id
          JOIN Properties ON RentalAgreements.property_id = Properties.property_id
          JOIN Paymenttypes on Payments.payment_type_id = Paymenttypes.payment_type_id";

if ($is_owner) {
    $query .= " WHERE RentalAgreements.property_id IN (SELECT property_id FROM Properties WHERE owner_id = '$user_id')";
} elseif ($is_tenant) {
    $query .= " WHERE RentalAgreements.tenant_id = (SELECT tenant_id FROM Tenants WHERE user_id = '$user_id')";
}

$result = $conn->query($query);

if (!$result) {
    echo "Error: " . $conn->error;
    exit();
}

if ($result->num_rows === 0) {
    echo "No payments found for the tenant.";
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Details</title>
    <link rel="stylesheet" type="text/css" href="/apartmentmanagement/css/styles.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
    <script src="/apartmentmanagement/js/scripts.js"></script>
</head>
<body>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/includes/header.php'); ?>
<main>
    <h1>All Payments</h1>
    <table id="tasksTable">
    <?php if ($result->num_rows > 0): ?>
        <thead>
            <tr>
                <th>Payment ID</th>
                <th>Agreement ID</th>
                <th>Property</th>
                <th>Date</th>
                <th>Amount</th>
                <th>Type</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($payment = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($payment['payment_id']) ?></td>
                    <td><?= htmlspecialchars($payment['agreement_id']) ?></td>
                    <td><?= htmlspecialchars($payment['property_description']) ?></td>
                    <td><?= htmlspecialchars($payment['payment_date']) ?></td>
                    <td><?= htmlspecialchars($payment['amount']) ?></td>
                    <td><?= htmlspecialchars($payment['payment_type_name']) ?></td>
                    <td>
                        <a href="payment_details.php?id=<?= htmlspecialchars($payment['payment_id']) ?>">Details</a>
                        <?php if ($is_admin || $is_owner): ?>
                            <a href="edit_payment.php?id=<?= htmlspecialchars($payment['payment_id']) ?>">Edit</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
        <?php else: ?>
            <tr>
                <td colspan="7">You don't have any payments at the moment.</td>
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
<?php include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/includes/footer.php'); ?>
</body>
</html>