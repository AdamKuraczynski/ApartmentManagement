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

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $agreement_id = sanitize_input($_POST['agreement_id']);
    $payment_date = sanitize_input($_POST['payment_date']);
    $amount = sanitize_input($_POST['amount']);
    $payment_type_id = sanitize_input($_POST['payment_type_id']);

    $insert_query = "INSERT INTO Payments (agreement_id, payment_date, amount, payment_type_id) VALUES ('$agreement_id', '$payment_date', '$amount', '$payment_type_id')";
    if ($conn->query($insert_query) === TRUE) {
        $message = "Successfully added payment.";
    } else {
        $message = "Error: " . $conn->error;
    }
}

// Fetch payment types
$payment_types_query = "SELECT * FROM PaymentTypes";
$payment_types_result = $conn->query($payment_types_query);

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Payment</title>
    <link rel="stylesheet" type="text/css" href="/apartmentmanagement/css/styles.css">
</head>
<body>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/includes/header.php'); ?>
<main>
    <h1>Add Payment</h1>
    <?php if ($message): ?>
        <p><?= $message ?></p>
    <?php endif; ?>
    <form method="post">
        <label for="agreement_id">Agreement ID:</label>
        <input type="text" id="agreement_id" name="agreement_id" required>
        <br>
        <label for="payment_date">Date:</label>
        <input type="date" id="payment_date" name="payment_date" required>
        <br>
        <label for="amount">Amount:</label>
        <input type="text" id="amount" name="amount" required>
        <br>
        <label for="payment_type_id">Payment Type:</label>
        <select id="payment_type_id" name="payment_type_id" required>
            <?php while ($row = $payment_types_result->fetch_assoc()): ?>
                <option value="<?= $row['payment_type_id'] ?>"><?= $row['payment_type_name'] ?></option>
            <?php endwhile; ?>
        </select>
        <br>
        <input type="submit" value="Add Payment">
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
