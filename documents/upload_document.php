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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $agreement_id = $_POST['agreement_id'];
    $document_type_id = $_POST['document_type_id'];
    $uploaded_at = date('Y-m-d H:i:s');
    
    $file_path = '../uploads/' . basename($_FILES['file']['name']);
    if (move_uploaded_file($_FILES['file']['tmp_name'], $file_path)) {
        $stmt = $conn->prepare("INSERT INTO Documents (agreement_id, property_id, document_type_id, file_path, uploaded_at) VALUES (?, (SELECT property_id FROM RentalAgreements WHERE agreement_id = ?), ?, ?, ?)");
        $stmt->bind_param("iiiss", $agreement_id, $agreement_id, $document_type_id, $file_path, $uploaded_at);
        $stmt->execute();
        
        $message = "Document uploaded successfully.";
    } else {
        $message = "Failed to upload document.";
    }
}


if (check_user_role($conn, $_SESSION['user_id'], 'administrator')) {
    $stmt = $conn->prepare("SELECT ra.agreement_id, p.description, u.username FROM RentalAgreements ra JOIN Properties p ON ra.property_id = p.property_id JOIN Tenants t ON ra.tenant_id = t.tenant_id JOIN Users u ON t.user_id = u.user_id");
} else if (check_user_role($conn, $_SESSION['user_id'], 'owner')) {
    $stmt = $conn->prepare("SELECT ra.agreement_id, p.description, u.username FROM RentalAgreements ra JOIN Properties p ON ra.property_id = p.property_id JOIN Tenants t ON ra.tenant_id = t.tenant_id JOIN Users u ON t.user_id = u.user_id WHERE p.owner_id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
}
$stmt->execute();
$agreements = $stmt->get_result();


$doc_stmt = $conn->prepare("SELECT document_type_id, document_type_name FROM DocumentTypes");
$doc_stmt->execute();
$document_types = $doc_stmt->get_result();

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
            <label for="agreement_id">Agreement:</label>
            <select name="agreement_id" id="agreement_id" required>
                <?php while ($row = $agreements->fetch_assoc()): ?>
                    <option value="<?= htmlspecialchars($row['agreement_id']) ?>"><?= htmlspecialchars($row['description'] . ' (' . $row['username'] . ')') ?></option>
                <?php endwhile; ?>
            </select>
            
            <label for="document_type_id">Document Type:</label>
            <select name="document_type_id" id="document_type_id" required>
                <?php while ($row = $document_types->fetch_assoc()): ?>
                    <option value="<?= htmlspecialchars($row['document_type_id']) ?>"><?= htmlspecialchars($row['document_type_name']) ?></option>
                <?php endwhile; ?>
            </select>

            <label for="file">Select file:</label>
            <input type="file" name="file" id="file" required>
            <button type="submit">Upload Document</button>
        </form>
        <?php if ($message): ?>
            <p><?= $message ?></p>
        <?php endif; ?>
        <?php 
            $back_link = '/apartmentmanagement/index.php';
            if (check_user_role($conn, $_SESSION['user_id'], 'administrator')) {
                $back_link = '/apartmentmanagement/dashboards/administrator_dashboard.php';
            } elseif (check_user_role($conn, $_SESSION['user_id'], 'tenant')) {
                $back_link = '/apartmentmanagement/dashboards/tenant_dashboard.php';
            } elseif (check_user_role($conn, $_SESSION['user_id'], 'owner')) {
                $back_link = '/apartmentmanagement/dashboards/owner_dashboard.php';
            }
        ?>
        <a class="back-button" href="<?php echo htmlspecialchars($back_link); ?>">Go back</a>
    </main>
    <?php include('../includes/footer.php'); ?>
</body>
</html>
