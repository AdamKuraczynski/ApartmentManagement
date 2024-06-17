<?php
include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/auth.php'); 
include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/includes/db.php'); 
include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/includes/functions.php'); 

$stmt = $conn->prepare("SELECT d.document_id, p.description AS property_description, d.agreement_id, d.file_path, d.uploaded_at, dt.document_type_name 
                        FROM documents d
                        JOIN properties p ON d.property_id = p.property_id
                        JOIN documenttypes dt ON d.document_type_id = dt.document_type_id;");
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
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
    <script src="/apartmentmanagement/js/scripts.js"></script>
</head>
<body>
<?php include('../includes/header.php'); ?>
    <main>
        <h2>View Documents</h2>
        <table id="tasksTable">
        <?php if ($result->num_rows > 0): ?>
            <thead>
                <tr>
                    <th>Document ID</th>
                    <th>Property</th>
                    <th>Agreement ID</th>
                    <th>Uploaded At</th>
                    <th>Document Type</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['document_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['property_description']); ?></td>
                        <td><?php echo htmlspecialchars($row['agreement_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['uploaded_at']); ?></td>
                        <td><?php echo htmlspecialchars($row['document_type_name']); ?></td>
                        <td><a href="#" onclick="handleFileClick(event, '../uploads/<?php echo htmlspecialchars($row['file_path']); ?>')">Open</a></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
            <?php else: ?>
                <tr>
                    <td colspan="6">You don't have documents at the moment.</td>
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
        <a class="back-button" href="<?php echo htmlspecialchars($back_link); ?>">Go back</a>    
    </main>
    <?php include('../includes/footer.php'); ?>
</body>
</html>
