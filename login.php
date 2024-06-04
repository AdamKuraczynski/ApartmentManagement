<?php
session_start();
include('includes/db.php');
include('includes/functions.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = sanitize_input($_POST["username"]);
    if (isset($_POST["password"])) {
        $password = sanitize_input($_POST["password"]);

        $sql = "SELECT * FROM users WHERE username='$username'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if (password_verify($password, $row['password_hash'])) {
                $_SESSION['username'] = $username;
                header("Location: index.php");
                exit();
            } else {
                echo "Invalid password.";
            }
        } else {
            echo "No user found.";
        }
    } else {
        echo "Password field is missing.";
    }

    $conn->close();
}
?>

<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    Username: <input type="text" name="username" required><br>
    Password: <input type="password" name="password" required><br>
    <input type="submit" value="Login">
</form>

<p>Don't have an account? <a href="register.php">Create one!</a></p>
