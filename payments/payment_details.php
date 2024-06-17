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

if (!isset($_GET['id'])) {
    header("Location: /apartmentmanagement/payments/view_payment.php");
    exit();
}

$payment_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];
$is_admin = check_user_role($conn, $user_id, 'administrator');
$is_owner = check_user_role($conn, $user_id, 'owner');
$is_tenant = check_user_role($conn, $user_id, 'tenant');

$query = "
    SELECT p.*, ra.property_id, ra.tenant_id, pr.owner_id, 
           t_user.username AS tenant_username, 
           o_user.username AS owner_username
    FROM Payments p 
    JOIN RentalAgreements ra ON p.agreement_id = ra.agreement_id 
    JOIN Properties pr ON ra.property_id = pr.property_id 
    JOIN Tenants ten ON ra.tenant_id = ten.tenant_id
    JOIN Users t_user ON ten.user_id = t_user.user_id
    JOIN Users o_user ON pr.owner_id = o_user.user_id
    WHERE p.payment_id = ?
";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $payment_id);
$stmt->execute();
$result = $stmt->get_result();
$payment = $result->fetch_assoc();

if ($is_tenant) {
    $tenant_query = "SELECT tenant_id FROM Tenants WHERE user_id = ?";
    $tenant_stmt = $conn->prepare($tenant_query);
    $tenant_stmt->bind_param("i", $user_id);
    $tenant_stmt->execute();
    $tenant_result = $tenant_stmt->get_result();
    $tenant_data = $tenant_result->fetch_assoc();
    $tenant_id = $tenant_data['tenant_id'];
} else {
    $tenant_id = null;
}

echo "<!-- is_admin: $is_admin, is_owner: $is_owner, is_tenant: $is_tenant -->";
echo "<!-- payment_owner_id: " . htmlspecialchars($payment['owner_id']) . ", payment_tenant_id: " . htmlspecialchars($payment['tenant_id']) . ", user_id: $user_id, tenant_id: $tenant_id -->";

if (!$payment || ($is_owner && $payment['owner_id'] != $user_id) || ($is_tenant && $payment['tenant_id'] != $tenant_id)) {
    echo "Access denied.";
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Details</title>
    <link rel="stylesheet" type="text/css" href="/apartmentmanagement/css/styles.css">
</head>
<body>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/includes/header.php'); ?>
<main>
<div class="show-details">
    <div class="details-section">
        <h1>Payment Details</h1>
        <p><strong>ID: </strong><?= htmlspecialchars($payment['payment_id']) ?></p>
        <p><strong>Agreement ID: </strong><?= htmlspecialchars($payment['agreement_id']) ?></p>
        <p><strong>Property ID: </strong><?= htmlspecialchars($payment['property_id']) ?></p>
        <p><strong>Date: </strong><?= htmlspecialchars($payment['payment_date']) ?></p>
        <p><strong>Amount: </strong><?= htmlspecialchars($payment['amount']) ?></p>
        <p><strong>Payment Type: </strong><?= htmlspecialchars($payment['payment_type_id']) ?></p>
        <p><strong>Payer: </strong><?= htmlspecialchars($payment['tenant_username']) ?></p>
        <p><strong>Payee: </strong><?= htmlspecialchars($payment['owner_username']) ?></p>
    </div>
</div>

<?php 
    $back_link = '/apartmentmanagement/index.php';
    if ($is_admin) {
        $back_link = '/apartmentmanagement/payments/view_payment.php';
    } elseif ($is_tenant) {
        $back_link = '/apartmentmanagement/payments/view_payment.php';
    } elseif ($is_owner) {
        $back_link = '/apartmentmanagement/payments/view_payment.php';
    }
?>
<a class="back-button" href="<?php echo $back_link; ?>">Go back</a>
</main>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/includes/footer.php'); ?>
</body>
</html>