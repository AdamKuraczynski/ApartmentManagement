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

$payment_id = $_GET['id'];
$query = "SELECT * FROM Payments WHERE payment_id = '$payment_id'";
$result = $conn->query($query);
$payment = $result->fetch_assoc();

if (!$payment || ($is_owner && !is_owner_of_property($conn, $user_id, $payment['property_id'])) || ($is_tenant && !is_tenant_of_agreement($conn, $user_id, $payment['agreement_id']))) {
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
    <h1>Payment Details</h1>
    <p>ID: <?= $payment['payment_id'] ?></p>
    <p>Agreement ID: <?= $payment['agreement_id'] ?></p>
    <p>Date: <?= $payment['payment_date'] ?></p>
    <p>Amount: <?= $payment['amount'] ?></p>
    <p>Payment Type: <?= $payment['payment_type_id'] ?></p>


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
