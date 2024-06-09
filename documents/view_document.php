<?php
include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/auth.php'); 
include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/includes/db.php'); 
include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/includes/functions.php'); 

// Fetch document details
$stmt = $conn->prepare("SELECT document_id, property_id, agreement_id, documents.document_type_id, file_path, uploaded_at, document_type_name 
                        FROM documents 
                        JOIN documenttypes ON documents.document_type_id = documenttypes.document_type_id;");
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Documents</title>
    <link rel="stylesheet" type="text/css" href="/apartmentmanagement/css/styles.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="/apartmentmanagement/js/scripts.js"></script>
</head>
<body>
<?php include('../includes/header.php'); ?>
    <main>
        <h2>View Documents</h2>
        <table>
        <?php if ($result->num_rows > 0): ?>
            <thead>
                <tr>
                    <th>document_id</th>
                    <th>property_id</th>
                    <th>agreement_id</th>
                    <th>document_type_id</th>
                    <th>uploaded_at</th>
                    <th>document_type_name</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['document_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['property_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['agreement_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['document_type_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['uploaded_at']); ?></td>
                        <td><?php echo htmlspecialchars($row['document_type_name']); ?></td>
                        <td><a href="#" onclick="handleFileClick(event, '../uploads/<?php echo htmlspecialchars($row['file_path']); ?>')">Open</a></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
            <?php else: ?>
                <tr>
                    <td colspan="7">You don't have documents at the moment.</td>
                </tr>
            <?php endif; ?>
        </table>
        <?php 
            $back_link = '/apartmentmanagement/index.php';
            if ($is_admin) {
                $back_link = '/apartmentmanagement/dashboards/administrator_dashboard.php';
            } elseif ($is_tenant) {
                $back_link = '/apartmentmanagement/dashboards/tenant_dashboard.php';
            } elseif ($is_owner) {
                $back_link = '/apartmentmanagement/dashboards/owner_dashboard.php';
            }
        ?>
        <a class="back-button" href="<?php echo $back_link; ?>">Go back</a>    
    </main>
    <?php include('../includes/footer.php'); ?>
</body>
</html>