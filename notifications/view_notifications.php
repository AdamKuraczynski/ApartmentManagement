<?php 
include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/auth.php'); 
include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/includes/db.php'); 
include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/includes/functions.php'); 

// Fetch notifications for the user
$stmt = $conn->prepare("SELECT * FROM Notifications WHERE user_id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Notifications</title>
    <link rel="stylesheet" type="text/css" href="/apartmentmanagement/css/styles.css">
</head>
<body>
    <?php include('../includes/header.php'); ?>
    <main>
        <h2>View Notifications</h2>
        <?php while ($notification = $result->fetch_assoc()) : ?>
            <div class="notification">
                <p><?php echo $notification['message']; ?></p>
                <p>Created At: <?php echo $notification['created_at']; ?></p>
                <?php if ($notification['read_at']) : ?>
                    <p>Read At: <?php echo $notification['read_at']; ?></p>
                <?php else : ?>
                    <form action="mark_as_read.php" method="post">
                        <input type="hidden" name="notification_id" value="<?php echo $notification['notification_id']; ?>">
                        <button type="submit">Mark as Read</button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    </main>
    <?php include('../includes/footer.php'); ?>
</body>
</html>