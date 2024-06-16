<?php 
include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/auth.php'); 
include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/includes/db.php'); 
include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/includes/functions.php'); 

if (!isset($_SESSION['user_id']) || 
    !(check_user_role($conn, $_SESSION['user_id'], 'administrator') || 
      check_user_role($conn, $_SESSION['user_id'], 'tenant'))) {
    header("Location: /apartmentmanagement/index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$is_admin = check_user_role($conn, $user_id, 'administrator');
$is_tenant = check_user_role($conn, $user_id, 'tenant');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $property_id = $_POST['property_id'];
    $description = $_POST['description'];
    $reported_by = $_SESSION['user_id'];
    $created_at = date('Y-m-d H:i:s');
    $status_id = 1;
    
    $stmt = $conn->prepare("INSERT INTO MaintenanceTasks (property_id, description, status_id, reported_by, created_at) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("isiss", $property_id, $description, $status_id, $reported_by, $created_at);
    if ($stmt->execute()) {
        echo "Maintenance task added successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }
}

$properties_query = "
    SELECT p.property_id, p.description
    FROM Properties p
";
if ($is_tenant) {
    $properties_query .= " 
        JOIN RentalAgreements ra ON p.property_id = ra.property_id 
        JOIN Tenants t ON ra.tenant_id = t.tenant_id 
        WHERE t.user_id = '$user_id'";
}
$properties_result = $conn->query($properties_query);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Maintenance Task</title>
    <link rel="stylesheet" type="text/css" href="/apartmentmanagement/css/styles.css">
</head>
<body>
    <?php include('../includes/header.php'); ?>
    <main>
        <h2>Add Maintenance Task</h2>
        <form action="add_task.php" method="post">
            <label for="property_id">Property:</label>
            <select id="property_id" name="property_id" required>
                <?php while ($property = $properties_result->fetch_assoc()): ?>
                    <option value="<?= htmlspecialchars($property['property_id']) ?>">
                        <?= htmlspecialchars($property['description']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <br>
            <label for="description">Description:</label>
            <textarea id="description" name="description" required></textarea>
            <br>
            <button type="submit">Add Task</button>
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
        <a class="back-button" href="<?= htmlspecialchars($back_link) ?>">Go back</a>
    </main>
    <?php include('../includes/footer.php'); ?>
</body>
</html>
