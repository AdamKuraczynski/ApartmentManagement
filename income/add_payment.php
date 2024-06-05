<?php include('../auth.php'); ?>
<?php
include '../includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $agreement_id = $_POST['agreement_id'];
    $payment_date = $_POST['payment_date'];
    $amount = $_POST['amount'];
    $payment_type_id = $_POST['payment_type_id'];

    $sql = "INSERT INTO Payments (agreement_id, payment_date, amount, payment_type_id) 
            VALUES ('$agreement_id', '$payment_date', '$amount', '$payment_type_id')";

    if ($conn->query($sql) === TRUE) {
        echo "New payment added successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add payment</title>
    <link rel="stylesheet" type="text/css" href="/apartmentmanagement/css/styles.css">
</head>
<body>

    <?php include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/includes/header.php'); ?>

    <main>
        <h2>Add New Payment</h2>
        <form method="post" action="">
            Agreement ID: <input type="text" name="agreement_id"><br>
            Payment Date: <input type="date" name="payment_date"><br>
            Amount: <input type="text" name="amount"><br>
            Payment Type ID: <input type="text" name="payment_type_id"><br>
            <input type="submit" value="Add Payment">
        </form>
    </main>

    <?php include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/includes/footer.php'); ?>

</body>
</html>