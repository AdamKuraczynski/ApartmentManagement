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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Handle file upload
    $property_id = $_POST['property_id'];
    $agreement_id = $_POST['agreement_id'];
    $document_type_id = $_POST['document_type_id'];
    $uploaded_at = date('Y-m-d H:i:s');
    
    $file_path = '../uploads/' . basename($_FILES['file']['name']);
    if (move_uploaded_file($_FILES['file']['tmp_name'], $file_path)) {
        // SQL query to insert document
        $stmt = $conn->prepare("INSERT INTO Documents (property_id, agreement_id, document_type_id, file_path, uploaded_at) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iiiss", $property_id, $agreement_id, $document_type_id, $file_path, $uploaded_at);
        $stmt->execute();
        
        echo "Document uploaded successfully.";
    } else {
        echo "Failed to upload document.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Document</title>
    <link rel="stylesheet" type="text/css" href="/apartmentmanagement/css/styles.css">
</head>
<body>
    <?php include('../includes/header.php'); ?>
    <main>
        <h2>Upload Document</h2>
        <form action="upload_document.php" method="post" enctype="multipart/form-data">
            <input type="text" name="property_id" placeholder="Property ID" required>
            <input type="text" name="agreement_id" placeholder="Agreement ID" required>
            <input type="text" name="document_type_id" placeholder="Document Type ID" required>
            <input type="file" name="file" required>
            <button type="submit">Upload Document</button>
        </form>
    </main>
    <?php include('../includes/footer.php'); ?>
</body>
</html>