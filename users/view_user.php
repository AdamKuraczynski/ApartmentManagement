<?php 
include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/auth.php'); 
include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/includes/db.php'); 
include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/includes/functions.php'); 

// Sprawdzenie, czy użytkownik jest zalogowany i czy jest administratorem
if (!isset($_SESSION['user_id']) || !check_user_role($conn, $_SESSION['user_id'], 'administrator')) {
    header("Location: /apartmentmanagement/index.php");
    exit();
}

// Zapytanie SQL pobierające użytkowników oraz ich role
$query = "
    SELECT u.user_id, u.username, u.email,
           CONCAT(ud.first_name, ' ', ud.last_name) AS name,
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
";

$result = $conn->query($query);

if (!$result) {
    echo "Error: " . $conn->error;
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Users</title>
    <link rel="stylesheet" type="text/css" href="/apartmentmanagement/css/styles.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
    <script src="/apartmentmanagement/js/scripts.js"></script>
</head>
<body>
    <?php include('../includes/header.php'); ?>
    <main>
        <h2>All Users</h2>
        <table id="usersTable">
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Name</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($user = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['user_id']) ?></td>
                        <td><?= htmlspecialchars($user['username']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td><?= htmlspecialchars($user['name']) ?></td>
                        <td><?= htmlspecialchars($user['role']) ?></td>
                        <td>
                            <a href="user_details.php?id=<?= htmlspecialchars($user['user_id']) ?>">Details</a>
                            <?php if ($user['role'] !== 'Administrator'): ?>
                                <a href="edit_user.php?id=<?= htmlspecialchars($user['user_id']) ?>">Edit</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <a class="back-button" href="/apartmentmanagement/dashboards/administrator_dashboard.php">Go back</a>
    </main>
    <?php include('../includes/footer.php'); ?>
    <script>
        $(document).ready( function () {
            $('#usersTable').DataTable();
        });
    </script>
</body>
</html>
