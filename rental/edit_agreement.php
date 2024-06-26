<?php 
include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/auth.php'); 
include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/includes/db.php'); 
include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/includes/functions.php'); 

if (!isset($_SESSION['user_id']) || !(check_user_role($conn, $_SESSION['user_id'], 'administrator') || check_user_role($conn, $_SESSION['user_id'], 'owner'))) {
    header("Location: /apartmentmanagement/index.php");
    exit();
}

$message = '';
$agreement_id = isset($_GET['agreement_id']) ? $_GET['agreement_id'] : (isset($_POST['agreement_id']) ? $_POST['agreement_id'] : null);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $agreement_id) {
    $property_id = sanitize_input($_POST['property_id']);
    $tenant_id = sanitize_input($_POST['tenant_id']);
    $start_date = sanitize_input($_POST['start_date']);
    $end_date = sanitize_input($_POST['end_date']);
    $rent_amount = sanitize_input($_POST['rent_amount']);
    $security_deposit = sanitize_input($_POST['security_deposit']);

    $update_query = "UPDATE RentalAgreements SET property_id = ?, tenant_id = ?, start_date = ?, end_date = ?, rent_amount = ?, security_deposit = ? WHERE agreement_id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("iissddi", $property_id, $tenant_id, $start_date, $end_date, $rent_amount, $security_deposit, $agreement_id);
    if ($stmt->execute()) {
        $message = "Successfully updated rental agreement.";

        $query = "SELECT * FROM RentalAgreements WHERE agreement_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $agreement_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $agreement = $result->fetch_assoc();
    } else {
        $message = "Error: " . $conn->error;
    }
} else if ($agreement_id) {
    $query = "SELECT * FROM RentalAgreements WHERE agreement_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $agreement_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $agreement = $result->fetch_assoc();

    if (!$agreement) {
        $agreement_id = null;
    }
}

$user_id = $_SESSION['user_id'];
$is_admin = check_user_role($conn, $user_id, 'administrator');
$is_owner = check_user_role($conn, $user_id, 'owner');

$properties_query = $is_admin ? "SELECT property_id, description FROM Properties" : "SELECT property_id, description FROM Properties WHERE owner_id = $user_id";
$properties_result = $conn->query($properties_query);

$tenants_query = "SELECT t.tenant_id, u.username FROM Tenants t JOIN Users u ON t.user_id = u.user_id";
$tenants_result = $conn->query($tenants_query);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Rental Agreement</title>
    <link rel="stylesheet" type="text/css" href="/apartmentmanagement/css/styles.css">
</head>
<body>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/includes/header.php'); ?>
<main>
    <h1>Edit Rental Agreement</h1>
    <?php if ($message): ?>
        <p><?= $message ?></p>
    <?php endif; ?>
    <form method="post">
        <label for="agreement_id">Agreement ID:</label>
        <input type="text" id="agreement_id" name="agreement_id" value="<?= htmlspecialchars($agreement_id) ?>" readonly>
        <br>
        <?php if ($agreement_id && $agreement): ?>
            <label for="property_id">Property:</label>
            <select id="property_id" name="property_id" required>
                <?php while ($row = $properties_result->fetch_assoc()): ?>
                    <option value="<?= $row['property_id'] ?>" <?= $row['property_id'] == $agreement['property_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($row['description']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <br>
            <label for="tenant_id">Tenant:</label>
            <select id="tenant_id" name="tenant_id" required>
                <?php while ($row = $tenants_result->fetch_assoc()): ?>
                    <option value="<?= $row['tenant_id'] ?>" <?= $row['tenant_id'] == $agreement['tenant_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($row['username']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <br>
            <label for="start_date">Start Date:</label>
            <input type="date" id="start_date" name="start_date" value="<?= htmlspecialchars($agreement['start_date']) ?>" required>
            <br>
            <label for="end_date">End Date:</label>
            <input type="date" id="end_date" name="end_date" value="<?= htmlspecialchars($agreement['end_date']) ?>" required>
            <br>
            <label for="rent_amount">Rent Amount:</label>
            <input type="text" id="rent_amount" name="rent_amount" value="<?= htmlspecialchars($agreement['rent_amount']) ?>" required>
            <br>
            <label for="security_deposit">Security Deposit:</label>
            <input type="text" id="security_deposit" name="security_deposit" value="<?= htmlspecialchars($agreement['security_deposit']) ?>" required>
        <?php else: ?>
            <label for="property_id">Property:</label>
            <select id="property_id" name="property_id" required>
                <?php while ($row = $properties_result->fetch_assoc()): ?>
                    <option value="<?= $row['property_id'] ?>"><?= htmlspecialchars($row['description']) ?></option>
                <?php endwhile; ?>
            </select>
            <br>
            <label for="tenant_id">Tenant:</label>
            <select id="tenant_id" name="tenant_id" required>
                <?php while ($row = $tenants_result->fetch_assoc()): ?>
                    <option value="<?= $row['tenant_id'] ?>"><?= htmlspecialchars($row['username']) ?></option>
                <?php endwhile; ?>
            </select>
            <br>
            <label for="start_date">Start Date:</label>
            <input type="date" id="start_date" name="start_date" required>
            <br>
            <label for="end_date">End Date:</label>
            <input type="date" id="end_date" name="end_date" required>
            <br>
            <label for="rent_amount">Rent Amount:</label>
            <input type="text" id="rent_amount" name="rent_amount" required>
            <br>
            <label for="security_deposit">Security Deposit:</label>
            <input type="text" id="security_deposit" name="security_deposit" required>
        <?php endif; ?>
        <br>
        <input type="submit" value="Update Agreement">
    </form>
    <?php 
        $back_link = '/apartmentmanagement/index.php';
        if ($is_admin) {
            $back_link = '/apartmentmanagement/rental/view_agreement.php';
        } elseif ($is_tenant) {
            $back_link = '/apartmentmanagement/rental/view_agreement.php';
        } elseif ($is_owner) {
            $back_link = '/apartmentmanagement/rental/view_agreement.php';
        }
    ?>
    <a class="back-button" href="<?php echo $back_link; ?>">Go back</a>
</main>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/includes/footer.php'); ?>
</body>
</html>