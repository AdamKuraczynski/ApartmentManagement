
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expenses</title>
    <link rel="stylesheet" type="text/css" href="/apartmentmanagement/css/styles.css">
</head>
<body>

    <?php include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/includes/header.php'); ?>

    <main>
        <?php include('../auth.php'); ?>
        <?php
        include '../includes/db.php';
        $sql = "SELECT * FROM MaintenanceTasks WHERE cost IS NOT NULL";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
        echo "<h2>Expenses</h2>";
        while($row = $result->fetch_assoc()) {
            echo "Task ID: " . $row["task_id"]. " - Description: " . $row["description"]. " - Cost: " . $row["cost"]. "<br>";
        }
        } else {
            echo "No expenses found.";
        }
        ?>
    </main>

    <?php include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/includes/footer.php'); ?>

</body>
</html>