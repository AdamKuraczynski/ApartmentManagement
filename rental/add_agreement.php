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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $property_id = $_POST['property_id'];
    $tenant_id = $_POST['tenant_id'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $rent_amount = $_POST['rent_amount'];
    $security_deposit = $_POST['security_deposit'];
    
    $stmt = $conn->prepare("INSERT INTO RentalAgreements (property_id, tenant_id, start_date, end_date, rent_amount, security_deposit) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iissdd", $property_id, $tenant_id, $start_date, $end_date, $rent_amount, $security_deposit);
    $stmt->execute();
    
    echo "Rental agreement added successfully.";
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
</head>
<body>
    <?php include('../includes/header.php'); ?>
    <main>
        <h2>Add Rental Agreement</h2>
        <form action="add_agreement.php" method="post">
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
            
            <input type="date" name="start_date" placeholder="Start Date" required>
            <input type="date" name="end_date" placeholder="End Date" required>
            <input type="text" name="rent_amount" placeholder="Rent Amount" required>
            <input type="text" name="security_deposit" placeholder="Security Deposit" required>
            <button type="submit">Add Agreement</button>
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
    <?php include('../includes/footer.php'); ?>
</body>
</html>