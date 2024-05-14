<?php
session_start();

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Include database connection
    include('db_connection.php');

    // Get username and password from the form
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Query the database to check if the user exists
    $query = "SELECT * FROM users WHERE username='$username' AND password='$password'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {
        // User found, set session variables and redirect to dashboard
        $row = mysqli_fetch_assoc($result);
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['user_type'] = $row['user_type'];
        header('Location: dashboard.php');
        exit;
    } else {
        // Invalid login credentials
        echo "Invalid username or password.";
    }

    // Close database connection
    mysqli_close($conn);
}
?>