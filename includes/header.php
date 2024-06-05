<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Header</title>
    <link rel="stylesheet" type="text/css" href="/apartmentmanagement/css/styles.css">
</head>
<body>
    <header>
        <div class="header-content">
            <img src="/apartmentmanagement/images/logo.jpeg" alt="Logo" class="logo">
            <h1>Apartment Management System</h1>
        </div><br>
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
                echo '<a href="/apartmentmanagement/login.php">Login</a>';
            }
            ?>
        </nav>
    </header>
</body>
</html>
