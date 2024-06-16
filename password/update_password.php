<?php
session_start();
require($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/includes/db.php'); 

$token = $_POST['token'];
$newPassword = password_hash($_POST['new_password'], PASSWORD_BCRYPT);

$stmt = $conn->prepare("SELECT user_id FROM PasswordResets WHERE reset_token = ?");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
    $reset = $result->fetch_assoc();
    $userId = $reset['user_id'];

    $stmt = $conn->prepare("UPDATE Users SET password_hash = ? WHERE user_id = ?");
    $stmt->bind_param("si", $newPassword, $userId);
    $stmt->execute();

    $stmt = $conn->prepare("DELETE FROM PasswordResets WHERE reset_token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();

    $_SESSION['message'] = "Password has been reset successfully.";
} else {
    $_SESSION['message'] = "Invalid token.";
}

header("Location: reset_password.php?token=" . $token);
exit();
?>