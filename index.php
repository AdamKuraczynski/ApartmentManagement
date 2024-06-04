<?php include('auth.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
</head>
<body>
    
    <?php include 'includes/header.php'; ?>
    <h2>Welcome <?php echo $_SESSION['username']; ?> to the Apartment Management System</h2>
    <p>Use the navigation menu to manage properties, rental agreements, income, and expenses.</p>
    <a href="logout.php">Logout</a>
    <p>
    <?php include 'includes/footer.php'; ?>
</body>
</html>
