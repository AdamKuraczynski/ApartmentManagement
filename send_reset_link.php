<?php
session_start();
require 'includes/db.php';
require 'includes/mail.php';

$email = $_POST['email'];
$stmt = $conn->prepare("SELECT user_id FROM Users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
    $user = $result->fetch_assoc();
    $userId = $user['user_id'];
    $token = bin2hex(random_bytes(16));
    $createdAt = date("Y-m-d H:i:s");

    $stmt = $conn->prepare("INSERT INTO PasswordResets (user_id, reset_token, created_at) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $userId, $token, $createdAt);
    $stmt->execute();

    $resetLink = "http://localhost/ApartmentManagement/reset_password.php?token=" . $token;
    
    if (sendResetEmail($email, $resetLink)) {
        $_SESSION['message'] = "A password reset link has been sent to your email.";
    } else {
        $_SESSION['message'] = "Failed to send reset link. Please try again.";
    }
} else {
    $_SESSION['message'] = "No user found with this email address.";
}

header("Location: request_reset.php");
exit();
?>