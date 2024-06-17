<?php 
include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/auth.php'); 
include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/includes/db.php'); 
include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/includes/functions.php'); 

if (!isset($_SESSION['user_id']) || !check_user_role($conn, $_SESSION['user_id'], 'administrator')) {
    header("Location: /apartmentmanagement/index.php");
    exit();
}

$message = '';
$user_id = isset($_GET['id']) ? intval($_GET['id']) : (isset($_POST['user_id']) ? intval($_POST['user_id']) : null);

if ($user_id) {
    $query = "
        SELECT u.user_id, u.username, u.email, ud.first_name, ud.last_name,
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
    ";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (!$user) {
        echo "User not found.";
        exit();
    }

    $username = $user['username'];
    $email = $user['email'];
    $first_name = $user['first_name'];
    $last_name = $user['last_name'];
    $role = $user['role'];
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $user_id) {
    $username = sanitize_input($_POST['username']);
    $email = sanitize_input($_POST['email']);
    $first_name = sanitize_input($_POST['first_name']);
    $last_name = sanitize_input($_POST['last_name']);
    $role = sanitize_input($_POST['role']);

    $update_user_query = "UPDATE Users SET username = ?, email = ? WHERE user_id = ?";
    $stmt = $conn->prepare($update_user_query);
    $stmt->bind_param("ssi", $username, $email, $user_id);
    $update_user_details_query = "UPDATE UserDetails SET first_name = ?, last_name = ? WHERE user_id = ?";
    $stmt2 = $conn->prepare($update_user_details_query);
    $stmt2->bind_param("ssi", $first_name, $last_name, $user_id);

    if ($stmt->execute() && $stmt2->execute()) {
        // Aktualizacja roli użytkownika
        if ($role == 'Owner') {
            $conn->query("DELETE FROM Tenants WHERE user_id = $user_id");
            $conn->query("DELETE FROM Administrators WHERE user_id = $user_id");
            $conn->query("INSERT INTO Owners (user_id) VALUES ($user_id)");
        } elseif ($role == 'Tenant') {
            $conn->query("DELETE FROM Owners WHERE user_id = $user_id");
            $conn->query("DELETE FROM Administrators WHERE user_id = $user_id");
            $conn->query("INSERT INTO Tenants (user_id) VALUES ($user_id)");
        } elseif ($role == 'Administrator') {
            $conn->query("DELETE FROM Owners WHERE user_id = $user_id");
            $conn->query("DELETE FROM Tenants WHERE user_id = $user_id");
            $conn->query("INSERT INTO Administrators (user_id) VALUES ($user_id)");
        }

        $message = "Successfully updated user.";
    } else {
        $message = "Error: " . $conn->error;
    }
}

$roles = ['Owner', 'Tenant', 'Administrator'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link rel="stylesheet" type="text/css" href="/apartmentmanagement/css/styles.css">
</head>
<body>
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/includes/header.php'); ?>
    <main>
        <h1>Edit User</h1>
        <?php if ($message): ?>
            <p><?= $message ?></p>
        <?php endif; ?>
        <form method="post">
            <label for="user_id">User ID:</label>
            <input type="text" id="user_id" name="user_id" value="<?= $user_id ?>" readonly>
            <br>
            <?php if ($user_id): ?>
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" value="<?= htmlspecialchars($username) ?>" required>
                <br>
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" required>
                <br>
                <label for="first_name">First Name:</label>
                <input type="text" id="first_name" name="first_name" value="<?= htmlspecialchars($first_name) ?>" required>
                <br>
                <label for="last_name">Last Name:</label>
                <input type="text" id="last_name" name="last_name" value="<?= htmlspecialchars($last_name) ?>" required>
                <br>
                <label for="role">Role:</label>
                <select id="role" name="role" required>
                    <?php foreach ($roles as $r): ?>
                        <option value="<?= $r ?>" <?= $r == $role ? 'selected' : '' ?>><?= $r ?></option>
                    <?php endforeach; ?>
                </select>
                <br>
            <?php endif; ?>
            <input type="submit" value="Update User">
        </form>
        <a class="back-button" href="/apartmentmanagement/users/view_user.php">Go back</a>
    </main>
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/includes/footer.php'); ?>
</body>
</html>
