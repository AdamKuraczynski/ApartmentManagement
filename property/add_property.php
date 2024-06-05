<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add property</title>
    <link rel="stylesheet" type="text/css" href="/apartmentmanagement/css/styles.css">
</head>
<body>

    <?php include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/includes/header.php'); ?>

    <main>
        
    <?php include('../auth.php'); ?>
<?php
include '../includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $owner_id = $_POST['owner_id'];
    $address_id = $_POST['address_id'];
    $type_id = $_POST['type_id'];
    $number_of_rooms = $_POST['number_of_rooms'];
    $size = $_POST['size'];
    $rental_price = $_POST['rental_price'];
    $description = $_POST['description'];

    $sql = "INSERT INTO Properties (owner_id, address_id, type_id, number_of_rooms, size, rental_price, description) 
            VALUES ('$owner_id', '$address_id', '$type_id', '$number_of_rooms', '$size', '$rental_price', '$description')";

    if ($conn->query($sql) === TRUE) {
        echo "New property added successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>

<h2>Add New Property</h2>
<form method="post" action="">
    Owner ID: <input type="text" name="owner_id"><br>
    Address ID: <input type="text" name="address_id"><br>
    Type ID: <input type="text" name="type_id"><br>
    Number of Rooms: <input type="text" name="number_of_rooms"><br>
    Size: <input type="text" name="size"><br>
    Rental Price: <input type="text" name="rental_price"><br>
    Description: <textarea name="description"></textarea><br>
    <input type="submit" value="Add Property">
</form>
        
    </main>

    <?php include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/includes/footer.php'); ?>

</body>
</html>