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

// Check if property ID is provided either via GET or POST
$property_id = isset($_GET['id']) ? $_GET['id'] : (isset($_POST['property_id']) ? $_POST['property_id'] : null);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $property_id) {
    $address_id = sanitize_input($_POST['address_id']);
    $type_id = sanitize_input($_POST['type_id']);
    $number_of_rooms = sanitize_input($_POST['number_of_rooms']);
    $size = sanitize_input($_POST['size']);
    $rental_price = sanitize_input($_POST['rental_price']);
    $description = sanitize_input($_POST['description']);

    $update_query = "UPDATE Properties SET address_id = ?, type_id = ?, number_of_rooms = ?, size = ?, rental_price = ?, description = ? WHERE property_id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("iiidssi", $address_id, $type_id, $number_of_rooms, $size, $rental_price, $description, $property_id);
    if ($stmt->execute()) {
        $message = "Successfully updated property.";

        // Fetch updated property data
        $query = "SELECT * FROM Properties WHERE property_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $property_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $property = $result->fetch_assoc();
    } else {
        $message = "Error: " . $conn->error;
    }
} else if ($property_id) {
    $query = "SELECT * FROM Properties WHERE property_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $property_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $property = $result->fetch_assoc();

    if (!$property) {
        $property_id = null; // Reset property_id if not found
    }
}

$user_id = $_SESSION['user_id'];
$is_admin = check_user_role($conn, $user_id, 'administrator');
$is_owner = check_user_role($conn, $user_id, 'owner');

if ($property_id && $is_owner && !is_owner_of_property($conn, $user_id, $property_id)) {
    echo "Access denied.";
    exit();
}

// Fetch property types
$property_types_query = "SELECT * FROM PropertyTypes";
$property_types_result = $conn->query($property_types_query);

// Fetch addresses
$addresses_query = "SELECT * FROM Addresses";
$addresses_result = $conn->query($addresses_query);
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
        <input type="text" id="property_id" name="property_id" value="<?= $property_id ?>" required readonly>
        <br>
        <?php if ($property_id && $property): ?>
            <label for="address_id">Address:</label>
            <select id="address_id" name="address_id" required>
                <?php while ($row = $addresses_result->fetch_assoc()): ?>
                    <option value="<?= $row['address_id'] ?>" <?= $row['address_id'] == $property['address_id'] ? 'selected' : '' ?>>
                        <?= $row['street'] . ', ' . $row['city'] ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <br>
            <label for="type_id">Property Type:</label>
            <select id="type_id" name="type_id" required>
                <?php while ($row = $property_types_result->fetch_assoc()): ?>
                    <option value="<?= $row['type_id'] ?>" <?= $row['type_id'] == $property['type_id'] ? 'selected' : '' ?>>
                        <?= $row['type_name'] ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <br>
            <label for="number_of_rooms">Number of Rooms:</label>
            <input type="text" id="number_of_rooms" name="number_of_rooms" value="<?= $property['number_of_rooms'] ?>" required>
            <br>
            <label for="size">Size:</label>
            <input type="text" id="size" name="size" value="<?= $property['size'] ?>" required>
            <br>
            <label for="rental_price">Rental Price:</label>
            <input type="text" id="rental_price" name="rental_price" value="<?= $property['rental_price'] ?>" required>
            <br>
            <label for="description">Description:</label>
            <textarea id="description" name="description" required><?= $property['description'] ?></textarea>
        <?php else: ?>
            <label for="address_id">Address:</label>
            <select id="address_id" name="address_id" required>
                <?php while ($row = $addresses_result->fetch_assoc()): ?>
                    <option value="<?= $row['address_id'] ?>"><?= $row['street'] . ', ' . $row['city'] ?></option>
                <?php endwhile; ?>
            </select>
            <br>
            <label for="type_id">Property Type:</label>
            <select id="type_id" name="type_id" required>
                <?php while ($row = $property_types_result->fetch_assoc()): ?>
                    <option value="<?= $row['type_id'] ?>"><?= $row['type_name'] ?></option>
                <?php endwhile; ?>
            </select>
            <br>
            <label for="number_of_rooms">Number of Rooms:</label>
            <input type="text" id="number_of_rooms" name="number_of_rooms" required>
            <br>
            <label for="size">Size:</label>
            <input type="text" id="size" name="size" required>
            <br>
            <label for="rental_price">Rental Price:</label>
            <input type="text" id="rental_price" name="rental_price" required>
            <br>
            <label for="description">Description:</label>
            <textarea id="description" name="description" required></textarea>
        <?php endif; ?>
        <br>
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
