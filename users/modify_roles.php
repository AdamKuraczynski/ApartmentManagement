<?php 
include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/auth.php'); 
include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/includes/functions.php'); 

if (!isset($_SESSION['user_id']) || !check_user_role($conn, $_SESSION['user_id'], 'administrator')) {
    header("Location: /apartmentmanagement/index.php");
    exit();
}

$usersQuery = "SELECT user_id, username FROM Users";
$users = $conn->query($usersQuery)->fetch_all(MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_POST['user_id'];
    $newRole = $_POST['role'];

    $deleteTenant = "DELETE FROM Tenants WHERE user_id = ?";
    $deleteOwner = "DELETE FROM Owners WHERE user_id = ?";
    $deleteAdmin = "DELETE FROM Administrators WHERE user_id = ?";

    $stmtTenant = $conn->prepare($deleteTenant);
    $stmtOwner = $conn->prepare($deleteOwner);
    $stmtAdmin = $conn->prepare($deleteAdmin);

    $stmtTenant->bind_param('i', $userId);
    $stmtOwner->bind_param('i', $userId);
    $stmtAdmin->bind_param('i', $userId);

    $stmtTenant->execute();
    $stmtOwner->execute();
    $stmtAdmin->execute();

    $insertQuery = "";
    if ($newRole === 'tenant') {
        $insertQuery = "INSERT INTO Tenants (user_id, contact_details) VALUES (?, '')";
    } elseif ($newRole === 'owner') {
        $insertQuery = "INSERT INTO Owners (user_id, additional_owner_info) VALUES (?, '')";
    } elseif ($newRole === 'administrator') {
        $insertQuery = "INSERT INTO Administrators (user_id, additional_admin_info) VALUES (?, '')";
    }

    if ($insertQuery) {
        $stmtInsert = $conn->prepare($insertQuery);
        $stmtInsert->bind_param('i', $userId);
        if ($stmtInsert->execute()) {
            $message = "Role updated successfully!";
        } else {
            $message = "Error updating role.";
        }
    } else {
        $message = "Invalid role selected.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modify User Roles</title>
    <link rel="stylesheet" type="text/css" href="/Apartmentmanagement/css/styles.css">
</head>
<body>
<?php include('../includes/header.php'); ?>
<main>
    <h2>Modify User Roles</h2>
    <?php if (isset($message)) echo "<p>$message</p>"; ?>
    <form action="modify_roles.php" method="post">
        <label for="user_id">Select User:</label>
        <select name="user_id" id="user_id">
            <?php foreach ($users as $user): ?>
                <option value="<?= $user['user_id'] ?>"><?= $user['username'] ?></option>
            <?php endforeach; ?>
        </select>
        <br>
        <label for="role">Select Role:</label>
        <select name="role" id="role">
            <option value="tenant">Tenant</option>
            <option value="owner">Owner</option>
            <option value="administrator">Administrator</option>
        </select>
        <br>
        <button type="submit">Update Role</button>
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