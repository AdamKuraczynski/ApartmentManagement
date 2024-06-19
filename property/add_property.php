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

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $errors = [];

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

    // Validate city, state, and country (no digits or special characters)
    if (!preg_match("/^[a-zA-Z\s]+$/", $city)) {
        $errors[] = "City name should only contain letters and spaces.";
    }
    if (!preg_match("/^[a-zA-Z\s]+$/", $state)) {
        $errors[] = "State name should only contain letters and spaces.";
    }
    if (!preg_match("/^[a-zA-Z\s]+$/", $country)) {
        $errors[] = "Country name should only contain letters and spaces.";
    }

    // Validate postal code (example format: 12345 or 12345-6789)
    if (!preg_match("/^\d{5}(-\d{4})?$/", $postal_code)) {
        $errors[] = "Invalid postal code format.";
    }

    // Validate number of rooms, size, and rental price (only numbers)
    if (!preg_match("/^\d+$/", $number_of_rooms)) {
        $errors[] = "Number of rooms should only contain digits.";
    }
    if (!preg_match("/^\d+(\.\d{1,2})?$/", $size)) {
        $errors[] = "Size should be a number, optionally with up to 2 decimal places.";
    }
    if (!preg_match("/^\d+(\.\d{1,2})?$/", $rental_price)) {
        $errors[] = "Rental price should be a number, optionally with up to 2 decimal places.";
    }

    if (empty($errors)) {
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
                $message = "Error: " . $stmt->error;
            }
        } else {
            $message = "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $message = implode("<br>", $errors);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Property</title>
    <link rel="stylesheet" type="text/css" href="/apartmentmanagement/css/styles.css">
    <script>
        function validateForm() {
            const city = document.getElementById('city').value;
            const state = document.getElementById('state').value;
            const country = document.getElementById('country').value;
            const postalCode = document.getElementById('postal_code').value;
            const numberOfRooms = document.getElementById('number_of_rooms').value;
            const size = document.getElementById('size').value;
            const rentalPrice = document.getElementById('rental_price').value;

            const errors = [];

            const namePattern = /^[a-zA-Z\s]+$/;
            if (!namePattern.test(city)) {
                errors.push("City name should only contain letters and spaces.");
            }
            if (!namePattern.test(state)) {
                errors.push("State name should only contain letters and spaces.");
            }
            if (!namePattern.test(country)) {
                errors.push("Country name should only contain letters and spaces.");
            }

            const postalCodePattern = /^\d{5}(-\d{4})?$/;
            if (!postalCodePattern.test(postalCode)) {
                errors.push("Invalid postal code format.");
            }

            const numberPattern = /^\d+$/;
            if (!numberPattern.test(numberOfRooms)) {
                errors.push("Number of rooms should only contain digits.");
            }

            const floatPattern = /^\d+(\.\d{1,2})?$/;
            if (!floatPattern.test(size)) {
                errors.push("Size should be a number, optionally with up to 2 decimal places.");
            }
            if (!floatPattern.test(rentalPrice)) {
                errors.push("Rental price should be a number, optionally with up to 2 decimal places.");
            }

            if (errors.length > 0) {
                alert(errors.join("\n"));
                return false;
            }

            return true;
        }
    </script>
</head>
<body>
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/includes/header.php'); ?>

    <main>
        <h2>Add Property</h2>
        <?php if ($message): ?>
            <p><?= $message ?></p>
        <?php endif; ?>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" onsubmit="return validateForm()">
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
