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

// Prepare the SQL query to fetch payment details along with agreement and property details
$query = "
    SELECT p.*, ra.property_id, ra.tenant_id, pr.owner_id 
    FROM Payments p 
    JOIN RentalAgreements ra ON p.agreement_id = ra.agreement_id 
    JOIN Properties pr ON ra.property_id = pr.property_id 
    WHERE p.payment_id = ?
";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $payment_id);
$stmt->execute();
$result = $stmt->get_result();
$payment = $result->fetch_assoc();

// Validate access
if (!$payment || ($is_owner && $payment['owner_id'] != $user_id) || ($is_tenant && $payment['tenant_id'] != $user_id)) {
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
<div class="property-detail">
    <div class="property-section">
        <h1>Payment Details</h1>
        <p><strong>ID: </strong><?= htmlspecialchars($payment['payment_id']) ?></p>
        <p><strong>Agreement ID: </strong><?= htmlspecialchars($payment['agreement_id']) ?></p>
        <p><strong>Date: </strong><?= htmlspecialchars($payment['payment_date']) ?></p>
        <p><strong>Amount: </strong><?= htmlspecialchars($payment['amount']) ?></p>
        <p><strong>Payment Type: </strong><?= htmlspecialchars($payment['payment_type_id']) ?></p>
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
<?php include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/includes/footer.php'); ?>
</body>
</html>
