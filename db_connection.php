<?php
// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "apartment_management";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>