<?php
session_start();
require_once('includes/db.php');
require_once('includes/functions.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = sanitize_input($_POST["username"]);
    $password = sanitize_input($_POST["password"]);

    function verify_user($conn, $username, $password) {
        $sql = "SELECT * FROM Users WHERE username='$username'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if (password_verify($password, $row['password_hash'])) {
                $_SESSION['username'] = $username;
                $_SESSION['user_id'] = $row['user_id'];
                return $row['user_id'];
            } else {
                echo "Invalid password.";
                return false;
            }
        }
        echo "No user found.";
        return false;
    }
    
    $user_id = verify_user($conn, $username, $password);

    if ($user_id) {
        if (check_user_role($conn, $user_id, 'administrator')) {
            $_SESSION['role'] = 'administrator';
            header("Location: /apartmentmanagement/dashboards/administrator_dashboard.php");
            exit();
        }

        if (check_user_role($conn, $user_id, 'owner')) {
            $_SESSION['role'] = 'owner';
            header("Location: /apartmentmanagement/dashboards/owner_dashboard.php");
            exit();
        }

        if (check_user_role($conn, $user_id, 'tenant')) {
            $_SESSION['role'] = 'tenant';
            header("Location: /apartmentmanagement/dashboards/tenant_dashboard.php");
            exit();
        }

        echo "User role not found.";
    }

    $conn->close();
}
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In</title>
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
    Password: <input type="password" name="password" required><br>
    <input type="submit" value="Login">
</form>

<p>Don't have an account? <a href="register.php">Create one!</a></p>
