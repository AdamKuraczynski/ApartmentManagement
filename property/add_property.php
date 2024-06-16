<?php 
include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/auth.php'); 
include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/includes/db.php'); 
include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/includes/functions.php'); 

if (!isset($_SESSION['user_id']) || 
    !(check_user_role($conn, $_SESSION['user_id'], 'administrator') || 
      check_user_role($conn, $_SESSION['user_id'], 'owner'))) {
    header("Location: /apartmentmanagement/index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$is_admin = check_user_role($conn, $user_id, 'administrator');
$is_owner = check_user_role($conn, $user_id, 'owner');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($is_admin) {
        $owner_id = sanitize_input($_POST["owner_id"]);
    } else if ($is_owner) {
        $owner_id = $user_id;
    }
    $street = sanitize_input($_POST["street"]);
    $city = sanitize_input($_POST["city"]);
    $state = sanitize_input($_POST["state"]);
    $postal_code = sanitize_input($_POST["postal_code"]);
    $country = sanitize_input($_POST["country"]);
    $type_id = sanitize_input($_POST["type_id"]);
    $number_of_rooms = sanitize_input($_POST["number_of_rooms"]);
    $size = sanitize_input($_POST["size"]);
    $rental_price = sanitize_input($_POST["rental_price"]);
    $description = sanitize_input($_POST["description"]);

    $stmt = $conn->prepare("INSERT INTO Addresses (street, city, state, postal_code, country) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $street, $city, $state, $postal_code, $country);
    if ($stmt->execute()) {
        $address_id = $stmt->insert_id;

        $stmt = $conn->prepare("INSERT INTO Properties (owner_id, address_id, type_id, number_of_rooms, size, rental_price, description) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iiidsss", $owner_id, $address_id, $type_id, $number_of_rooms, $size, $rental_price, $description);
        if ($stmt->execute()) {
            echo "Property added successfully!";
            header("Location: /apartmentmanagement/property/view_property.php");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Property</title>
    <link rel="stylesheet" type="text/css" href="/apartmentmanagement/css/styles.css">
</head>
<body>
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/includes/header.php'); ?>

    <main>
        <h2>Add Property</h2>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <?php if ($is_admin): ?>
                <label for="owner_id">Owner:</label>
                <select name="owner_id" id="owner_id" required>
                    <?php
                    $result = $conn->query("
                        SELECT u.user_id, u.username 
                        FROM Users u 
                        JOIN Owners o ON u.user_id = o.user_id 
                    ");
                    while ($row = $result->fetch_assoc()) {
                        echo '<option value="' . htmlspecialchars($row['user_id']) . '">' . htmlspecialchars($row['username']) . '</option>';
                    }
                    ?>
                </select><br>
            <?php endif; ?>
            <label for="street">Street:</label> 
            <input type="text" name="street" id="street" required><br>
            <label for="city">City:</label> 
            <input type="text" name="city" id="city" required><br>
            <label for="state">State:</label> 
            <input type="text" name="state" id="state" required><br>
            <label for="postal_code">Postal Code:</label> 
            <input type="text" name="postal_code" id="postal_code" required><br>
            <label for="country">Country:</label> 
            <input type="text" name="country" id="country" required><br>
            <label for="type_id">Property Type:</label>
            <select name="type_id" id="type_id" required>
                <?php
                $result = $conn->query("SELECT type_id, type_name FROM PropertyTypes");
                while ($row = $result->fetch_assoc()) {
                    echo '<option value="' . htmlspecialchars($row['type_id']) . '">' . htmlspecialchars($row['type_name']) . '</option>';
                }
                ?>
            </select><br>
            <label for="number_of_rooms">Number of Rooms:</label> 
            <input type="text" name="number_of_rooms" id="number_of_rooms" required><br>
            <label for="size">Size (in sqm):</label> 
            <input type="text" name="size" id="size" required><br>
            <label for="rental_price">Rental Price:</label> 
            <input type="text" name="rental_price" id="rental_price" required><br>
            <label for="description">Description:</label> 
            <textarea name="description" id="description" required></textarea><br>
            <input type="submit" value="Add Property">
        </form>
        <?php 
            $back_link = '/apartmentmanagement/index.php';
            if ($is_admin) {
                $back_link = '/apartmentmanagement/dashboards/administrator_dashboard.php';
            } elseif ($is_owner) {
                $back_link = '/apartmentmanagement/dashboards/owner_dashboard.php';
            }
        ?>
        <a class="back-button" href="<?php echo $back_link; ?>">Go back</a>
    </main>

    <?php include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/includes/footer.php'); ?>
</body>
</html>