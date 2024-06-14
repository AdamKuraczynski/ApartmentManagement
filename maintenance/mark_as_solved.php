<?php
include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/includes/db.php');
include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/includes/functions.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $task_id = sanitize_input($_POST['task_id']);
    $cost = isset($_POST['cost']) ? sanitize_input($_POST['cost']) : null;
    $resolved_at = date('Y-m-d H:i:s');
    $status_id = 3;

    if ($cost) {
        $query = "UPDATE MaintenanceTasks SET status_id = ?, cost = ?, resolved_at = ? WHERE task_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("idsi", $status_id, $cost, $resolved_at, $task_id);
    } else {
        $query = "UPDATE MaintenanceTasks SET status_id = ?, resolved_at = ? WHERE task_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("isi", $status_id, $resolved_at, $task_id);
    }

    if ($stmt->execute()) {
        echo "Task marked as solved successfully.";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
