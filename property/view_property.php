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

$user_id = $_SESSION['user_id'];
$is_admin = check_user_role($conn, $user_id, 'administrator');
$is_owner = check_user_role($conn, $user_id, 'owner');
$is_tenant = check_user_role($conn, $user_id, 'tenant');

// Prepare the SQL query based on the user's role
if ($is_admin) {
    $stmt = $conn->prepare("
       SELECT p.property_id, p.owner_id, p.address_id, p.type_id, p.number_of_rooms, p.size, p.rental_price, p.description,
               a.street, a.city, a.state, a.postal_code, a.country,
               pt.type_name
        FROM Properties p
        JOIN Addresses a ON p.address_id = a.address_id
        JOIN PropertyTypes pt ON p.type_id = pt.type_id
    ");
} else if ($is_owner) {
    $stmt = $conn->prepare("
        SELECT p.property_id, p.owner_id, p.address_id, p.type_id, p.number_of_rooms, p.size, p.rental_price, p.description,
               a.street, a.city, a.state, a.postal_code, a.country,
               pt.type_name
        FROM Properties p
        JOIN Addresses a ON p.address_id = a.address_id
        JOIN PropertyTypes pt ON p.type_id = pt.type_id
        WHERE p.owner_id = ?
    ");
    $stmt->bind_param("i", $user_id);
} else if ($is_tenant) {
    $stmt = $conn->prepare("
        SELECT p.property_id, p.owner_id, p.address_id, p.type_id, p.number_of_rooms, p.size, p.rental_price, p.description,
               a.street, a.city, a.state, a.postal_code, a.country,
               pt.type_name
        FROM Properties p
        JOIN Addresses a ON p.address_id = a.address_id
        JOIN PropertyTypes pt ON p.type_id = pt.type_id
        JOIN RentalAgreements ra ON p.property_id = ra.property_id
        JOIN Tenants t ON ra.tenant_id = t.tenant_id
        WHERE t.user_id = ?
    ");
    $stmt->bind_param("i", $user_id);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Properties</title>
    <link rel="stylesheet" type="text/css" href="/apartmentmanagement/css/styles.css">
</head>
<body>
    <?php include('../includes/header.php'); ?>
    <main>
        <h2>View Properties</h2>
        <table>
        <?php if ($result->num_rows > 0): ?>
            <thead>
                <tr>
                    <th>Property ID</th>
                    <th>Owner ID</th>
                    <th>Address</th>
                    <th>Type</th>
                    <th>Number of Rooms</th>
                    <th>Size</th>
                    <th>Rental Price</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['property_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['owner_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['street'] . ', ' . $row['city'] . ', ' . $row['state'] . ', ' . $row['postal_code'] . ', ' . $row['country']); ?></td>
                    <td><?php echo htmlspecialchars($row['type_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['number_of_rooms']); ?></td>
                    <td><?php echo htmlspecialchars($row['size']); ?></td>
                    <td><?php echo htmlspecialchars($row['rental_price']); ?></td>
                    <td><?php echo htmlspecialchars($row['description']); ?></td>
                    <td>
                        <a href="/apartmentmanagement/property/property_details.php?property_id=<?php echo htmlspecialchars($row['property_id']); ?>">Details</a>
                        <?php if ($is_admin || $is_owner): ?>
                            <a href="edit_property.php?id=<?= $row['property_id'] ?>">Edit</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="9">
                    <?php if ($is_owner): ?> You don't have any properties.<?php else: ?> Nothing to show yet.<?php endif; ?>
                </td>
            </tr>
        <?php endif; ?>
            </tbody>
        </table>
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
