<?php 
include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/auth.php'); 
include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/includes/db.php'); 
include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/includes/functions.php'); 

// Sprawdzenie, czy użytkownik jest zalogowany i czy jest administratorem
if (!isset($_SESSION['user_id']) || !check_user_role($conn, $_SESSION['user_id'], 'administrator')) {
    header("Location: /apartmentmanagement/index.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: /apartmentmanagement/user/view_user.php");
    exit();
}

$user_id = intval($_GET['id']);
$session_user_id = $_SESSION['user_id'];
$is_admin = check_user_role($conn, $session_user_id, 'administrator');

// Przygotowanie i wykonanie zapytania SQL
$stmt = $conn->prepare("
    SELECT u.user_id, u.username, u.email,
           ud.first_name, ud.last_name, ud.phone_number, ud.address,
           (CASE 
                WHEN a.administrator_id IS NOT NULL THEN 'Administrator'
                WHEN o.owner_id IS NOT NULL THEN 'Owner'
                WHEN t.tenant_id IS NOT NULL THEN 'Tenant'
                ELSE 'Unknown'
            END) AS role
    FROM Users u
    LEFT JOIN UserDetails ud ON u.user_id = ud.user_id
    LEFT JOIN Administrators a ON u.user_id = a.user_id
    LEFT JOIN Owners o ON u.user_id = o.user_id
    LEFT JOIN Tenants t ON u.user_id = t.user_id
    WHERE u.user_id = ?
");

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo "User not found.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Details</title>
    <link rel="stylesheet" type="text/css" href="/apartmentmanagement/css/styles.css">
</head>
<body>
    <?php include('../includes/header.php'); ?>
    <main>
        <h2>User Details</h2>
        <div class="show-details">
            <div class="details-section">
                <h3>General Information</h3>
                <p><strong>User ID:</strong> <?php echo htmlspecialchars($user['user_id']); ?></p>
                <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                <p><strong>Role:</strong> <?php echo htmlspecialchars($user['role']); ?></p>
            </div>
            <div class="details-section">
                <h3>Personal Information</h3>
                <p><strong>First Name:</strong> <?php echo htmlspecialchars($user['first_name']); ?></p>
                <p><strong>Last Name:</strong> <?php echo htmlspecialchars($user['last_name']); ?></p>
                <p><strong>Phone Number:</strong> <?php echo htmlspecialchars($user['phone_number']); ?></p>
                <p><strong>Address:</strong> <?php echo nl2br(htmlspecialchars($user['address'])); ?></p>
            </div>
        </div>
        <a class="back-button" href="/apartmentmanagement/users/view_user.php">Go back</a>
    </main>
    <?php include('../includes/footer.php'); ?>
</body>
</html>
