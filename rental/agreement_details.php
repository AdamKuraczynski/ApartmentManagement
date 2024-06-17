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

if (!isset($_GET['agreement_id'])) {
    header("Location: /apartmentmanagement/rental/view_agreements.php");
    exit();
}

$agreement_id = intval($_GET['agreement_id']);
$user_id = $_SESSION['user_id'];
$is_admin = check_user_role($conn, $user_id, 'administrator');
$is_owner = check_user_role($conn, $user_id, 'owner');
$is_tenant = check_user_role($conn, $user_id, 'tenant');

$stmt = $conn->prepare("
    SELECT ra.agreement_id, ra.property_id, ra.tenant_id, ra.start_date, ra.end_date, ra.rent_amount, ra.security_deposit, 
           p.description AS property_description, 
           tu.username AS tenant_username, tu.email AS tenant_email,
           tud.first_name AS tenant_first_name, tud.last_name AS tenant_last_name,
           ou.username AS owner_username, ou.email AS owner_email,
           oud.first_name AS owner_first_name, oud.last_name AS owner_last_name
    FROM RentalAgreements ra
    JOIN Properties p ON ra.property_id = p.property_id
    JOIN Tenants t ON ra.tenant_id = t.tenant_id
    JOIN Users tu ON t.user_id = tu.user_id
    JOIN UserDetails tud ON tu.user_id = tud.user_id
    JOIN Owners o ON p.owner_id = o.user_id
    JOIN Users ou ON o.user_id = ou.user_id
    JOIN UserDetails oud ON ou.user_id = oud.user_id
    WHERE ra.agreement_id = ?
");

$stmt->bind_param("i", $agreement_id);
$stmt->execute();
$result = $stmt->get_result();
$agreement = $result->fetch_assoc();

if (!$agreement) {
    echo "Agreement not found.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agreement Details</title>
    <link rel="stylesheet" type="text/css" href="/apartmentmanagement/css/styles.css">
</head>
<body>
    <?php include('../includes/header.php'); ?>
    <main>
        <h2>Agreement Details</h2>
        <div class="show-details">
            <div class="details-section">
                <h3>General Information</h3>
                <p><strong>Agreement ID:</strong> <?php echo htmlspecialchars($agreement['agreement_id']); ?></p>
                <p><strong>Property ID:</strong> <?php echo htmlspecialchars($agreement['property_id']); ?></p>
                <p><strong>Property Description:</strong> <?php echo htmlspecialchars($agreement['property_description']); ?></p>
                <p><strong>Tenant:</strong> <?php echo htmlspecialchars($agreement['tenant_username']); ?></p>
                <p><strong>Start Date:</strong> <?php echo htmlspecialchars($agreement['start_date']); ?></p>
                <p><strong>End Date:</strong> <?php echo htmlspecialchars($agreement['end_date']); ?></p>
                <p><strong>Rent Amount:</strong> $<?php echo htmlspecialchars($agreement['rent_amount']); ?></p>
                <p><strong>Security Deposit:</strong> $<?php echo htmlspecialchars($agreement['security_deposit']); ?></p>
            </div>
            <div class="details-section">
                <h3>Tenant Information</h3>
                <p><strong>Username:</strong> <?php echo htmlspecialchars($agreement['tenant_username']); ?></p>
                <p><strong>First Name:</strong> <?php echo htmlspecialchars($agreement['tenant_first_name']); ?></p>
                <p><strong>Last Name:</strong> <?php echo htmlspecialchars($agreement['tenant_last_name']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($agreement['tenant_email']); ?></p>
            </div>
            <div class="details-section">
                <h3>Owner Information</h3>
                <p><strong>Username:</strong> <?php echo htmlspecialchars($agreement['owner_username']); ?></p>
                <p><strong>First Name:</strong> <?php echo htmlspecialchars($agreement['owner_first_name']); ?></p>
                <p><strong>Last Name:</strong> <?php echo htmlspecialchars($agreement['owner_last_name']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($agreement['owner_email']); ?></p>
            </div>
        </div>
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
    <?php include('../includes/footer.php'); ?>
</body>
</html>