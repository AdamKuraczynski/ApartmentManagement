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

$message = '';
$property_id = isset($_GET['id']) ? $_GET['id'] : (isset($_POST['property_id']) ? $_POST['property_id'] : null);

if ($property_id) {
    $query = "SELECT p.*, a.street, a.city, a.postal_code, a.country FROM Properties p JOIN Addresses a ON p.address_id = a.address_id WHERE p.property_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $property_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $property = $result->fetch_assoc();

    if (!$property) {
        echo "Property not found.";
        exit();
    }

    $address_id = $property['address_id'];
    $type_id = $property['type_id'];
    $number_of_rooms = $property['number_of_rooms'];
    $size = $property['size'];
    $rental_price = $property['rental_price'];
    $description = $property['description'];
    $owner_id = $property['owner_id'];
    $street = $property['street'];
    $city = $property['city'];
    $postal_code = $property['postal_code'];
    $country = $property['country'];
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $property_id) {
    $street = sanitize_input($_POST['street']);
    $city = sanitize_input($_POST['city']);
    $postal_code = sanitize_input($_POST['postal_code']);
    $country = sanitize_input($_POST['country']);
    $type_id = sanitize_input($_POST['type_id']);
    $number_of_rooms = sanitize_input($_POST['number_of_rooms']);
    $size = sanitize_input($_POST['size']);
    $rental_price = sanitize_input($_POST['rental_price']);
    $description = sanitize_input($_POST['description']);

    if ($is_admin) {
        $owner_id = sanitize_input($_POST['owner_id']);
    }

    $check_address_query = "SELECT address_id FROM Addresses WHERE street = ? AND city = ? AND postal_code = ? AND country = ?";
    $stmt = $conn->prepare($check_address_query);
    $stmt->bind_param("ssss", $street, $city, $postal_code, $country);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $address = $result->fetch_assoc();
        $new_address_id = $address['address_id'];
    } else {
        $insert_address_query = "INSERT INTO Addresses (street, city, postal_code, country) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_address_query);
        $stmt->bind_param("ssss", $street, $city, $postal_code, $country);
        if ($stmt->execute()) {
            $new_address_id = $stmt->insert_id;
        } else {
            $message = "Error: " . $conn->error;
        }
    }

    if (isset($new_address_id)) {
        $update_query = "UPDATE Properties SET address_id = ?, type_id = ?, number_of_rooms = ?, size = ?, rental_price = ?, description = ?" . ($is_admin ? ", owner_id = ?" : "") . " WHERE property_id = ?";
        $stmt = $conn->prepare($update_query);

        if ($is_admin) {
            $stmt->bind_param("iiidssii", $new_address_id, $type_id, $number_of_rooms, $size, $rental_price, $description, $owner_id, $property_id);
        } else {
            $stmt->bind_param("iiidssi", $new_address_id, $type_id, $number_of_rooms, $size, $rental_price, $description, $property_id);
        }

        if ($stmt->execute()) {
            $message = "Successfully updated property.";
        } else {
            $message = "Error: " . $conn->error;
        }
    }
}

$user_id = $_SESSION['user_id'];
$is_admin = check_user_role($conn, $user_id, 'administrator');
$is_owner = check_user_role($conn, $user_id, 'owner');

if ($property_id && $is_owner && !is_owner_of_property($conn, $user_id, $property_id)) {
    echo "Access denied.";
    exit();
}

$property_types_query = "SELECT * FROM PropertyTypes";
$property_types_result = $conn->query($property_types_query);

if ($is_admin) {
    $owners_query = "SELECT u.user_id, u.username FROM Users u JOIN Owners o ON u.user_id = o.user_id";
    $owners_result = $conn->query($owners_query);
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Property</title>
    <link rel="stylesheet" type="text/css" href="/apartmentmanagement/css/styles.css">
</head>
<body>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/includes/header.php'); ?>
<main>
    <h1>Edit Property</h1>
    <?php if ($message): ?>
        <p><?= $message ?></p>
    <?php endif; ?>
    <form method="post">
        <label for="property_id">Property ID:</label>
        <input type="text" id="property_id" name="property_id" value="<?= $property_id ?>" readonly>
        <br>
        <?php if ($property_id): ?>
            <label for="street">Street:</label>
            <input type="text" id="street" name="street" value="<?= $street ?>" required>
            <br>
            <label for="city">City:</label>
            <input type="text" id="city" name="city" value="<?= $city ?>" required>
            <br>
            <label for="postal_code">Postal Code:</label>
            <input type="text" id="postal_code" name="postal_code" value="<?= $postal_code ?>" required>
            <br>
            <label for="country">Country:</label>
            <input type="text" id="country" name="country" value="<?= $country ?>" required>
            <br>
            <label for="type_id">Property Type:</label>
            <select id="type_id" name="type_id" required>
                <?php while ($row = $property_types_result->fetch_assoc()): ?>
                    <option value="<?= $row['type_id'] ?>" <?= $row['type_id'] == $type_id ? 'selected' : '' ?>>
                        <?= $row['type_name'] ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <br>
            <label for="number_of_rooms">Number of Rooms:</label>
            <input type="text" id="number_of_rooms" name="number_of_rooms" value="<?= $number_of_rooms ?>" required>
            <br>
            <label for="size">Size:</label>
            <input type="text" id="size" name="size" value="<?= $size ?>" required>
            <br>
            <label for="rental_price">Rental Price:</label>
            <input type="text" id="rental_price" name="rental_price" value="<?= $rental_price ?>" required>
            <br>
            <label for="description">Description:</label>
            <textarea id="description" name="description" required><?= $description ?></textarea>
            <br>
            <?php if ($is_admin): ?>
                <label for="owner_id">Owner:</label>
                <select id="owner_id" name="owner_id" required>
                    <?php while ($row = $owners_result->fetch_assoc()): ?>
                        <option value="<?= $row['user_id'] ?>" <?= $row['user_id'] == $owner_id ? 'selected' : '' ?>>
                            <?= $row['username'] ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                <br>
            <?php endif; ?>
        <?php endif; ?>
        <input type="submit" value="Update Property">
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