<?php
include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/auth.php'); 
include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/includes/db.php'); 
include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/includes/functions.php'); 

// Fetch document details
$stmt = $conn->prepare("SELECT * FROM Documents WHERE document_id = ?");
$stmt->bind_param("i", $_GET['document_id']);
$stmt->execute();
$result = $stmt->get_result();
$document = $result->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Document</title>
    <link rel="stylesheet" type="text/css" href="/apartmentmanagement/css/styles.css">
</head>
<body>
    <?php include('../includes/header.php'); ?>
    <main>
        <h2>View Document</h2>
        <p>Property ID: <?php echo $document['property_id']; ?></p>
        <p>Agreement ID: <?php echo $document['agreement_id']; ?></p>
        <p>Document Type ID: <?php echo $document['document_type_id']; ?></p>
        <p>Uploaded At: <?php echo $document['uploaded_at']; ?></p>
        <p>File Path: <a href="<?php echo $document['file_path']; ?>"><?php echo basename($document['file_path']); ?></a></p>
    </main>
    <?php include('../includes/footer.php'); ?>
</body>
</html>