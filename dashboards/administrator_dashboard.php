<?php 
include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/auth.php'); 
include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/includes/functions.php'); 

if (!isset($_SESSION['user_id']) || !check_user_role($conn, $_SESSION['user_id'], 'administrator')) {
    header("Location: /apartmentmanagement/index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrator Dashboard</title>
    <link rel="stylesheet" type="text/css" href="/apartmentmanagement/css/styles.css">
</head>
<body>
    <header>
        <?php include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/includes/header.php'); ?>
    </header>
    <main>
        <h1>Welcome, <?php echo $_SESSION['username']; ?>!</h1>
        <p>Here you can manage users, properties, and perform administrative tasks.</p>

        
    </main>
    <footer>
        <?php include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/includes/footer.php'); ?>
    </footer>
</body>
</html>
