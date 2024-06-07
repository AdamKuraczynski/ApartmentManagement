<?php 
include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/auth.php'); 
include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/includes/db.php'); 
include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/includes/functions.php'); 

// Sprawdzanie, czy użytkownik jest zalogowany i ma odpowiednią rolę (administrator lub właściciel)
if (!isset($_SESSION['user_id']) || 
    !(check_user_role($conn, $_SESSION['user_id'], 'administrator') || 
      check_user_role($conn, $_SESSION['user_id'], 'owner'))) {
    header("Location: /apartmentmanagement/index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $owner_id = $_SESSION['user_id']; // zakładamy, że właściciel dodaje nieruchomość
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

    // Wstawienie adresu do bazy danych
    $stmt = $conn->prepare("INSERT INTO Addresses (street, city, state, postal_code, country) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $street, $city, $state, $postal_code, $country);
    if ($stmt->execute()) {
        $address_id = $stmt->insert_id;

        // Wstawienie nieruchomości do bazy danych
        $stmt = $conn->prepare("INSERT INTO Properties (owner_id, address_id, type_id, number_of_rooms, size, rental_price, description) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iiiidss", $owner_id, $address_id, $type_id, $number_of_rooms, $size, $rental_price, $description);
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
            Street: <input type="text" name="street" required><br>
            City: <input type="text" name="city" required><br>
            State: <input type="text" name="state" required><br>
            Postal Code: <input type="text" name="postal_code" required><br>
            Country: <input type="text" name="country" required><br>
            Property Type: 
            <select name="type_id" required>
                <?php
                $result = $conn->query("SELECT type_id, type_name FROM PropertyTypes");
                while ($row = $result->fetch_assoc()) {
                    echo '<option value="' . htmlspecialchars($row['type_id']) . '">' . htmlspecialchars($row['type_name']) . '</option>';
                }
                ?>
            </select><br>
            Number of Rooms: <input type="text" name="number_of_rooms" required><br>
            Size (in sqm): <input type="text" name="size" required><br>
            Rental Price: <input type="text" name="rental_price" required><br>
            Description: <textarea name="description" required></textarea><br>
            <input type="submit" value="Add Property">
        </form>
    </main>

    <?php include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/includes/footer.php'); ?>
</body>
</html>
