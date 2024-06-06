<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Footer</title>
    <link rel="stylesheet" type="text/css" href="/Apartmentmanagement/css/styles.css">
</head>
<body>
    <div class="wrapper">
        <footer class="footer">
        <nav>
            <?php 
            require_once($_SERVER['DOCUMENT_ROOT'] . '/apartmentmanagement/includes/db.php');
            require_once($_SERVER['DOCUMENT_ROOT'] . '/apartmentmanagement/includes/functions.php');

            if (isset($_SESSION['user_id'])) {
                $user_id = $_SESSION['user_id'];
                if (check_user_role($conn, $user_id, 'administrator')): ?>
                    <a href="/apartmentmanagement/index.php">Home</a>
                    <a href="/apartmentmanagement/dashboards/administrator_dashboard.php">Admin Dashboard</a>
                <?php endif; 
                if (check_user_role($conn, $user_id, 'owner')): ?>
                    <a href="/apartmentmanagement/index.php">Home</a>
                    <a href="/apartmentmanagement/dashboards/owner_dashboard.php">Owner Dashboard</a>
                <?php endif; 
                if (check_user_role($conn, $user_id, 'tenant')): ?>
                    <a href="/apartmentmanagement/index.php">Home</a>
                    <a href="/apartmentmanagement/dashboards/tenant_dashboard.php">Tenant Dashboard</a>
                <?php endif;
            } else {
                echo 'Apartment Management System';
            }
            ?>
        </nav>
        </footer>
    </div>
</body>
</html>
