<?php include('../auth.php'); ?>
<?php
include '../includes/db.php';
include '../includes/header.php';

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

include '../includes/footer.php';
?>
