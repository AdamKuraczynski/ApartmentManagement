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

// Check if agreement ID is provided either via GET or POST
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

        // Fetch updated agreement data
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
        $agreement_id = null; // Reset agreement_id if not found
    }
}

$user_id = $_SESSION['user_id'];
$is_admin = check_user_role($conn, $user_id, 'administrator');
$is_owner = check_user_role($conn, $user_id, 'owner');

// Fetch properties and tenants
$properties_query = "SELECT property_id, description FROM Properties";
$properties_result = $conn->query($properties_query);

$tenants_query = "SELECT tenant_id, user_id FROM Tenants";
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
        <input type="text" id="agreement_id" name="agreement_id" value="<?= $agreement_id ?>">
        <br>
        <?php if ($agreement_id && $agreement): ?>
            <label for="property_id">Property:</label>
            <select id="property_id" name="property_id" required>
                <?php while ($row = $properties_result->fetch_assoc()): ?>
                    <option value="<?= $row['property_id'] ?>" <?= $row['property_id'] == $agreement['property_id'] ? 'selected' : '' ?>>
                        <?= $row['description'] ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <br>
            <label for="tenant_id">Tenant:</label>
            <select id="tenant_id" name="tenant_id" required>
                <?php while ($row = $tenants_result->fetch_assoc()): ?>
                    <option value="<?= $row['tenant_id'] ?>" <?= $row['tenant_id'] == $agreement['tenant_id'] ? 'selected' : '' ?>>
                        <?= $row['user_id'] ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <br>
            <label for="start_date">Start Date:</label>
            <input type="date" id="start_date" name="start_date" value="<?= $agreement['start_date'] ?>" required>
            <br>
            <label for="end_date">End Date:</label>
            <input type="date" id="end_date" name="end_date" value="<?= $agreement['end_date'] ?>" required>
            <br>
            <label for="rent_amount">Rent Amount:</label>
            <input type="text" id="rent_amount" name="rent_amount" value="<?= $agreement['rent_amount'] ?>" required>
            <br>
            <label for="security_deposit">Security Deposit:</label>
            <input type="text" id="security_deposit" name="security_deposit" value="<?= $agreement['security_deposit'] ?>" required>
        <?php else: ?>
            <label for="property_id">Property:</label>
            <select id="property_id" name="property_id" required>
                <?php while ($row = $properties_result->fetch_assoc()): ?>
                    <option value="<?= $row['property_id'] ?>"><?= $row['description'] ?></option>
                <?php endwhile; ?>
            </select>
            <br>
            <label for="tenant_id">Tenant:</label>
            <select id="tenant_id" name="tenant_id" required>
                <?php while ($row = $tenants_result->fetch_assoc()): ?>
                    <option value="<?= $row['tenant_id'] ?>"><?= $row['user_id'] ?></option>
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
