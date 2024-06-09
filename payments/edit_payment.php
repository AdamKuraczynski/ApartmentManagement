<?php 
include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/auth.php'); 
include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/includes/db.php'); 
include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/includes/functions.php'); 

if (!isset($_SESSION['user_id']) || 
    !(check_user_role($conn, $_SESSION['user_id'], 'administrator') || 
      check_user_role($conn, $_SESSION['user_id'], 'owner'))) {
    header("Location: /apartmentmanagement/index.php");
    exit();
}

$payment_id = $_GET['id'];
$query = "SELECT * FROM Payments WHERE payment_id = '$payment_id'";
$result = $conn->query($query);
$payment = $result->fetch_assoc();

if (!$payment || ($is_owner && !is_owner_of_property($conn, $user_id, $payment['property_id']))) {
    echo "Access denied.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $amount = sanitize_input($_POST['amount']);
    $payment_date = sanitize_input($_POST['payment_date']);
    $payment_type_id = sanitize_input($_POST['payment_type_id']);

    $update_query = "UPDATE Payments SET amount = '$amount', payment_date = '$payment_date', payment_type_id = '$payment_type_id' WHERE payment_id = '$payment_id'";
    $conn->query($update_query);

    header("Location: view_payments.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Payment</title>
    <link rel="stylesheet" type="text/css" href="/apartmentmanagement/css/styles.css">
</head>
<body>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/includes/header.php'); ?>
<main>
    <h1>Edit Payment</h1>
    <form method="post">
        <label for="amount">Amount:</label>
        <input type="text" id="amount" name="amount" value="<?= $payment['amount'] ?>" required>
        <br>
        <label for="payment_date">Date:</label>
        <input type="date" id="payment_date" name="payment_date" value="<?= $payment['payment_date'] ?>" required>
        <br>
        <label for="payment_type_id">Payment Type:</label>
        <input type="text" id="payment_type_id" name="payment_type_id" value="<?= $payment['payment_type_id'] ?>" required>
        <br>
        <input type="submit" value="Update Payment">
    </form>
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
