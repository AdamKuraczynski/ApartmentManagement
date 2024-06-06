<?php
include('includes/db.php');
include('includes/functions.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = sanitize_input($_POST["username"]);
    $email = sanitize_input($_POST["email"]);
    $password = password_hash(sanitize_input($_POST["password"]), PASSWORD_BCRYPT);
    $first_name = sanitize_input($_POST["first_name"]);
    $last_name = sanitize_input($_POST["last_name"]);
    $phone_number = sanitize_input($_POST["phone_number"]);
    $address = sanitize_input($_POST["address"]);

    $conn->begin_transaction();

    try {
        $sql = "INSERT INTO Users (username, email, password_hash) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $username, $email, $password);
        $stmt->execute();
        $user_id = $stmt->insert_id;

        $sql = "INSERT INTO UserDetails (user_id, first_name, last_name, phone_number, address) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issss", $user_id, $first_name, $last_name, $phone_number, $address);
        $stmt->execute();

        $sql = "INSERT INTO Tenants (user_id) VALUES (?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();

        $conn->commit();

        echo "Registration successful!";
        header("Location: /apartmentmanagement/login.php");
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }
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
        First Name: <input type="text" name="first_name" required><br>
        Last Name: <input type="text" name="last_name" required><br>
        Your Adress: <input type="text" name="address"><br>
        Phone Number: <input type="text" name="phone_number"><br>
        <input type="submit" value="Register">
    </form>

    <div class="form-footer">
            <p>Already have an account?<a href="login.php"> Log in!</a></p>
    </div>
        
    </main>

    <?php include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/includes/footer.php'); ?>

</body>
</html>