<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

// Include database connection
include('db_connection.php');

// Get user details from the database
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE id=$user_id";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

// Display dashboard based on user type
if ($user['user_type'] == 'administrator') {
    echo "Welcome, Administrator!";
    // Display administrator dashboard
} else {
    echo "Welcome, Renter!";
    // Display renter dashboard
}

// Close database connection
mysqli_close($conn);
?>