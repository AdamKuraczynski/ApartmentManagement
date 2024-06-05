<?php
session_start();
require_once('includes/db.php');
require_once('includes/functions.php');

$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = sanitize_input($_POST["username"]);
    $password = sanitize_input($_POST["password"]);

    function verify_user($conn, $username, $password, &$error_message) {
        $sql = "SELECT * FROM Users WHERE username='$username'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if (password_verify($password, $row['password_hash'])) {
                $_SESSION['username'] = $username;
                $_SESSION['user_id'] = $row['user_id'];
                return $row['user_id'];
            } else {
                $error_message = "Invalid password.";
                return false;
            }
        }
        $error_message = "No user found.";
        return false;
    }
    
    $user_id = verify_user($conn, $username, $password, $error_message);

    if ($user_id) {
        if (check_user_role($conn, $user_id, 'administrator')) {
            $_SESSION['role'] = 'administrator';
            header("Location: /apartmentmanagement/index.php");
            exit();
        }

        if (check_user_role($conn, $user_id, 'owner')) {
            $_SESSION['role'] = 'owner';
            header("Location: /apartmentmanagement/index.php");
            exit();
        }

        if (check_user_role($conn, $user_id, 'tenant')) {
            $_SESSION['role'] = 'tenant';
            header("Location: /apartmentmanagement/index.php");
            exit();
        }

        $error_message = "User role not found.";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" type="text/css" href="/apartmentmanagement/css/styles.css">
</head>
<body>

    <?php include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/includes/header.php'); ?>

    <main>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            Username: <input type="text" name="username" required><br>
            Password: <input type="password" name="password" required><br>
            <input type="submit" value="Login">
        </form>
        <?php
        if (!empty($error_message)) {
            echo '<p style="color: red;">' . $error_message . '</p>';
        }
        ?>
        <div class="form-footer">
            <p>Don't have an account?<a href="register.php"> Create one!</a></p>
            <p>Forgot password?<a href="password/request_reset.php"> Reset it!</a></p>
        </div>
    </main>

    <?php include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/includes/footer.php'); ?>

</body>
</html>