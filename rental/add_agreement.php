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

$user_id = $_SESSION['user_id'];
$is_admin = check_user_role($conn, $user_id, 'administrator');
$is_owner = check_user_role($conn, $user_id, 'owner');

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $property_id = sanitize_input($_POST['property_id']);
    $tenant_id = sanitize_input($_POST['tenant_id']);
    $start_date = sanitize_input($_POST['start_date']);
    $end_date = sanitize_input($_POST['end_date']);
    $rent_amount = sanitize_input($_POST['rent_amount']);
    $security_deposit = sanitize_input($_POST['security_deposit']);

    $errors = [];

    // Validate rent amount and security deposit (must be numbers)
    if (!preg_match("/^\d+(\.\d{1,2})?$/", $rent_amount)) {
        $errors[] = "Rent amount should be a number, optionally with up to 2 decimal places.";
    }
    if (!preg_match("/^\d+(\.\d{1,2})?$/", $security_deposit)) {
        $errors[] = "Security deposit should be a number, optionally with up to 2 decimal places.";
    }

    // Validate dates
    if (strtotime($start_date) > strtotime($end_date)) {
        $errors[] = "Start date cannot be later than end date.";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO RentalAgreements (property_id, tenant_id, start_date, end_date, rent_amount, security_deposit) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iissdd", $property_id, $tenant_id, $start_date, $end_date, $rent_amount, $security_deposit);
        if ($stmt->execute()) {
            echo "Rental agreement added successfully.";
        } else {
            $message = "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $message = implode("<br>", $errors);
    }
}

if ($is_admin) {
    $properties_query = "SELECT property_id, description FROM Properties";
} else if ($is_owner) {
    $properties_query = "SELECT property_id, description FROM Properties WHERE owner_id = ?";
}

$properties_stmt = $conn->prepare($properties_query);
if ($is_owner) {
    $properties_stmt->bind_param("i", $user_id);
}
$properties_stmt->execute();
$properties_result = $properties_stmt->get_result();

$tenants_query = "
    SELECT t.tenant_id, u.username 
    FROM Tenants t 
    JOIN Users u ON t.user_id = u.user_id";
$tenants_result = $conn->query($tenants_query);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Rental Agreement</title>
    <link rel="stylesheet" type="text/css" href="/apartmentmanagement/css/styles.css">
    <script>
        function validateForm() {
            const rentAmount = document.getElementById('rent_amount').value;
            const securityDeposit = document.getElementById('security_deposit').value;
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;

            const errors = [];

            const floatPattern = /^\d+(\.\d{1,2})?$/;
            if (!floatPattern.test(rentAmount)) {
                errors.push("Rent amount should be a number, optionally with up to 2 decimal places.");
            }
            if (!floatPattern.test(securityDeposit)) {
                errors.push("Security deposit should be a number, optionally with up to 2 decimal places.");
            }

            if (new Date(startDate) > new Date(endDate)) {
                errors.push("Start date cannot be later than end date.");
            }

            if (errors.length > 0) {
                alert(errors.join("\n"));
                return false;
            }

            return true;
        }
    </script>
</head>
<body>
    <?php include('../includes/header.php'); ?>
    <main>
        <h2>Add Rental Agreement</h2>
        <?php if ($message): ?>
            <p><?= $message ?></p>
        <?php endif; ?>
        <form action="add_agreement.php" method="post" onsubmit="return validateForm()">
            <label for="property_id">Property:</label>
            <select name="property_id" id="property_id" required>
                <option value="" disabled selected>Select a property</option>
                <?php while ($property = $properties_result->fetch_assoc()): ?>
                    <option value="<?php echo htmlspecialchars($property['property_id']); ?>">
                        <?php echo htmlspecialchars($property['description']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
            
            <label for="tenant_id">Tenant:</label>
            <select name="tenant_id" id="tenant_id" required>
                <option value="" disabled selected>Select a tenant</option>
                <?php while ($tenant = $tenants_result->fetch_assoc()): ?>
                    <option value="<?php echo htmlspecialchars($tenant['tenant_id']); ?>">
                        <?php echo htmlspecialchars($tenant['username']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
            
            <input type="date" name="start_date" id="start_date" placeholder="Start Date" required>
            <input type="date" name="end_date" id="end_date" placeholder="End Date" required>
            <input type="text" name="rent_amount" id="rent_amount" placeholder="Rent Amount" required>
            <input type="text" name="security_deposit" id="security_deposit" placeholder="Security Deposit" required>
            <button type="submit">Add Agreement</button>
        </form>
        <?php 
            $back_link = '/apartmentmanagement/index.php';
            if ($is_admin) {
                $back_link = '/apartmentmanagement/dashboards/administrator_dashboard.php';
            } elseif ($is_owner) {
                $back_link = '/apartmentmanagement/dashboards/owner_dashboard.php';
            }
        ?>
        <a class="back-button" href="<?php echo $back_link; ?>">Go back</a>
    </main>
    <?php include('../includes/footer.php'); ?>
</body>
</html>
