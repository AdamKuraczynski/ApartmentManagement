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

if (!isset($_GET['property_id'])) {
    header("Location: /apartmentmanagement/property/view_property.php");
    exit();
}

$property_id = intval($_GET['property_id']);
$user_id = $_SESSION['user_id'];
$is_admin = check_user_role($conn, $user_id, 'administrator');
$is_owner = check_user_role($conn, $user_id, 'owner');
$is_tenant = check_user_role($conn, $user_id, 'tenant');

$stmt = $conn->prepare("
    SELECT p.property_id, p.owner_id, p.address_id, p.type_id, p.number_of_rooms, p.size, p.rental_price, p.description,
           a.street, a.city, a.state, a.postal_code, a.country,
           pt.type_name,
           u.username AS owner_username, u.email AS owner_email,
           ud.first_name AS owner_first_name, ud.last_name AS owner_last_name
    FROM Properties p
    JOIN Addresses a ON p.address_id = a.address_id
    JOIN PropertyTypes pt ON p.type_id = pt.type_id
    JOIN Users u ON p.owner_id = u.user_id
    JOIN UserDetails ud ON u.user_id = ud.user_id
    WHERE p.property_id = ?
");

$stmt->bind_param("i", $property_id);
$stmt->execute();
$result = $stmt->get_result();
$property = $result->fetch_assoc();

if (!$property) {
    echo "Property not found.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Property Details</title>
    <link rel="stylesheet" type="text/css" href="/apartmentmanagement/css/styles.css">
</head>
<body>
    <?php include('../includes/header.php'); ?>
    <main>
        <h2>Property Details</h2>
        <div class="show-details">
            <div class="details-section">
                <h3>General Information</h3>
                <p><strong>Property ID:</strong> <?php echo htmlspecialchars($property['property_id']); ?></p>
                <p><strong>Owner:</strong> <?php echo htmlspecialchars($property['owner_username']); ?></p>
                <p><strong>Type:</strong> <?php echo htmlspecialchars($property['type_name']); ?></p>
                <p><strong>Number of Rooms:</strong> <?php echo htmlspecialchars($property['number_of_rooms']); ?></p>
                <p><strong>Size:</strong> <?php echo htmlspecialchars($property['size']); ?> sqm</p>
                <p><strong>Rental Price:</strong> $<?php echo htmlspecialchars($property['rental_price']); ?></p>
            </div>
            <div class="details-section">
                <h3>Address</h3>
                <p><strong>Street:</strong> <?php echo htmlspecialchars($property['street']); ?></p>
                <p><strong>City:</strong> <?php echo htmlspecialchars($property['city']); ?></p>
                <p><strong>State:</strong> <?php echo htmlspecialchars($property['state']); ?></p>
                <p><strong>Postal Code:</strong> <?php echo htmlspecialchars($property['postal_code']); ?></p>
                <p><strong>Country:</strong> <?php echo htmlspecialchars($property['country']); ?></p>
            </div>
            <div class="details-section">
                <h3>Description</h3>
                <p><strong>Description:</strong> <?php echo nl2br(htmlspecialchars($property['description'])); ?></p>
            </div>
            <div class="details-section">
                <h3>Owner Information</h3>
                <p><strong>Username:</strong> <?php echo htmlspecialchars($property['owner_username']); ?></p>
                <p><strong>First Name:</strong> <?php echo htmlspecialchars($property['owner_first_name']); ?></p>
                <p><strong>Last Name:</strong> <?php echo htmlspecialchars($property['owner_last_name']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($property['owner_email']); ?></p>
            </div>
        </div>
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