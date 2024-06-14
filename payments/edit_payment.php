<?php 
include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/auth.php'); 
include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/includes/db.php'); 
include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/includes/functions.php'); 

// Check if user is logged in and has the appropriate role
if (!isset($_SESSION['user_id']) || !(check_user_role($conn, $_SESSION['user_id'], 'administrator') || check_user_role($conn, $_SESSION['user_id'], 'owner'))) {
    header("Location: /apartmentmanagement/index.php");
    exit();
}

$message = '';

// Check if payment ID is provided either via GET or POST
$payment_id = isset($_GET['id']) ? $_GET['id'] : (isset($_POST['payment_id']) ? $_POST['payment_id'] : null);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $payment_id) {
    $amount = sanitize_input($_POST['amount']);
    $payment_date = sanitize_input($_POST['payment_date']);
    $payment_type_id = sanitize_input($_POST['payment_type_id']);

    $update_query = "UPDATE Payments SET amount = ?, payment_date = ?, payment_type_id = ? WHERE payment_id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("dsii", $amount, $payment_date, $payment_type_id, $payment_id);
    if ($stmt->execute()) {
        $message = "Successfully updated payment.";

        // Fetch updated payment data
        $query = "
            SELECT p.*, ra.property_id, pr.owner_id 
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
    } else {
        $message = "Error: " . $conn->error;
    }
} else if ($payment_id) {
    $query = "
        SELECT p.*, ra.property_id, pr.owner_id 
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

    if (!$payment) {
        $payment_id = null; // Reset payment_id if not found
    }
}

$user_id = $_SESSION['user_id'];
$is_admin = check_user_role($conn, $user_id, 'administrator');
$is_owner = check_user_role($conn, $user_id, 'owner');

if ($payment_id && $is_owner && $payment && $payment['owner_id'] != $user_id) {
    echo "Access denied.";
    exit();
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
    <title>Edit Payment</title>
    <link rel="stylesheet" type="text/css" href="/apartmentmanagement/css/styles.css">
</head>
<body>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/includes/header.php'); ?>
<main>
    <h1>Edit Payment</h1>
    <?php if ($message): ?>
        <p><?= $message ?></p>
    <?php endif; ?>
    <form method="post">
        <label for="payment_id">Payment ID:</label>
        <input type="text" id="payment_id" name="payment_id" value="<?= htmlspecialchars($payment_id) ?>" required readonly>
        <br>
        <?php if ($payment_id && $payment): ?>
            <label for="amount">Amount:</label>
            <input type="text" id="amount" name="amount" value="<?= htmlspecialchars($payment['amount']) ?>" required>
            <br>
            <label for="payment_date">Date:</label>
            <input type="date" id="payment_date" name="payment_date" value="<?= htmlspecialchars($payment['payment_date']) ?>" required>
            <br>
            <label for="payment_type_id">Payment Type:</label>
            <select id="payment_type_id" name="payment_type_id" required>
                <?php while ($row = $payment_types_result->fetch_assoc()): ?>
                    <option value="<?= $row['payment_type_id'] ?>" <?= $row['payment_type_id'] == $payment['payment_type_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($row['payment_type_name']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        <?php else: ?>
            <label for="amount">Amount:</label>
            <input type="text" id="amount" name="amount" required>
            <br>
            <label for="payment_date">Date:</label>
            <input type="date" id="payment_date" name="payment_date" required>
            <br>
            <label for="payment_type_id">Payment Type:</label>
            <select id="payment_type_id" name="payment_type_id" required>
                <?php while ($row = $payment_types_result->fetch_assoc()): ?>
                    <option value="<?= htmlspecialchars($row['payment_type_id']) ?>"><?= htmlspecialchars($row['payment_type_name']) ?></option>
                <?php endwhile; ?>
            </select>
        <?php endif; ?>
        <br>
        <input type="submit" value="Update Payment">
    </form>
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
    <a class="back-button" href="<?= htmlspecialchars($back_link); ?>">Go back</a>
</main>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/includes/footer.php'); ?>
</body>
</html>
