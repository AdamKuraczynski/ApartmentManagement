<?php
include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/auth.php');
include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/includes/db.php');
include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/includes/functions.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $notification_id = intval($_POST['notification_id']);
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("SELECT * FROM Notifications WHERE notification_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $notification_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $stmt_update = $conn->prepare("UPDATE Notifications SET read_at = NOW() WHERE notification_id = ?");
        $stmt_update->bind_param("i", $notification_id);
        $stmt_update->execute();
        
        header("Location: view_notifications.php");
        exit();
    } else {
        echo "Notification not found or you do not have permission to mark it as read.";
    }
} else {
    echo "Invalid request method.";
}
?>
