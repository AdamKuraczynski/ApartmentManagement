<?php
include('includes/db.php');
include('includes/functions.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = sanitize_input($_POST["username"]);
    $email = sanitize_input($_POST["email"]);
    $password = password_hash(sanitize_input($_POST["password"]), PASSWORD_BCRYPT);

    $sql = "INSERT INTO Users (username, email, password_hash) VALUES ('$username', '$email', '$password')";
    if ($conn->query($sql) === TRUE) {
        $user_id = $conn->insert_id;

        $sql = "INSERT INTO Tenants (user_id) VALUES ('$user_id')";
        if ($conn->query($sql) === TRUE) {
            echo "Registration successful!";
            header("Location: /apartmentmanagement/login.php");
            exit();
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
    <link rel="stylesheet" type="text/css" href="css/styles.css">
</head>
<header>
        <div class="header-content">
            <img src="/apartmentmanagement/images/logo.jpeg" alt="Logo" class="logo">
            <h1>Apartment Management System</h1>
        </div>
</header>

<br>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    Username: <input type="text" name="username" required><br>
    Email: <input type="email" name="email" required><br>
    Password: <input type="password" name="password" required><br>
    <input type="submit" value="Register">
</form>

<p><a href="login.php">Log in</a></p>