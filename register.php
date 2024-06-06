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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" type="text/css" href="/apartmentmanagement/css/styles.css">
</head>
<body>

    <?php include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/includes/header.php'); ?>

    <main>
    
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        Username: <input type="text" name="username" required><br>
        Email: <input type="email" name="email" required><br>
        Password: <input type="password" name="password" required><br>
        I am a: <select required><option selected>Tenant</option><option>Owner</option></select><br>
        <input type="submit" value="Register">
    </form>

    <div class="form-footer">
            <p>Already have an account?<a href="login.php"> Log in!</a></p>
    </div>
        
    </main>

    <?php include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/includes/footer.php'); ?>

</body>
</html>