<?php
include('auth.php');
include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/includes/db.php');
include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/includes/functions.php');

function get_user_roles_data($conn) {
    $roles_data = [];
    $tables = ['Administrators' => 'administrator_id', 'Owners' => 'owner_id', 'Tenants' => 'tenant_id'];
    foreach ($tables as $table => $id) {
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM $table");
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $roles_data[] = ['role' => $table, 'count' => $row['count']];
    }
    return $roles_data;
}

function get_database_size($conn) {
    $stmt = $conn->prepare("SELECT table_schema 'DB Name', ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) 'DB Size (MB)' FROM information_schema.tables GROUP BY table_schema");
    $stmt->execute();
    $result = $stmt->get_result();
    $size_data = [];
    while ($row = $result->fetch_assoc()) {
        $size_data[] = $row;
    }
    return $size_data;
}

function get_tenant_task_data($conn, $user_id) {
    $stmt = $conn->prepare("SELECT ms.status_name, COUNT(*) as count 
                            FROM MaintenanceTasks mt
                            JOIN MaintenanceStatuses ms ON mt.status_id = ms.status_id
                            JOIN Properties p ON mt.property_id = p.property_id
                            JOIN RentalAgreements ra ON p.property_id = ra.property_id
                            JOIN Tenants t ON ra.tenant_id = t.tenant_id
                            WHERE t.user_id = ?
                            GROUP BY ms.status_name");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $task_data = [];
    while ($row = $result->fetch_assoc()) {
        $task_data[] = $row;
    }
    return $task_data;
}

function get_owner_task_data($conn, $user_id) {
    $stmt = $conn->prepare("SELECT ms.status_name, COUNT(*) as count 
                            FROM MaintenanceTasks mt
                            JOIN MaintenanceStatuses ms ON mt.status_id = ms.status_id
                            JOIN Properties p ON mt.property_id = p.property_id
                            WHERE p.owner_id = ?
                            GROUP BY ms.status_name");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $task_data = [];
    while ($row = $result->fetch_assoc()) {
        $task_data[] = $row;
    }
    return $task_data;
}

function get_payment_status_data($conn, $user_id) {
    $stmt = $conn->prepare("SELECT 
                                CASE 
                                    WHEN p.payment_date <= NOW() THEN 'Paid on Time'
                                    ELSE 'Paid Late'
                                END as payment_status,
                                COUNT(*) as count
                            FROM Payments p
                            JOIN RentalAgreements ra ON p.agreement_id = ra.agreement_id
                            JOIN Properties pr ON ra.property_id = pr.property_id
                            WHERE pr.owner_id = ?
                            GROUP BY payment_status");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $payment_data = [];
    while ($row = $result->fetch_assoc()) {
        $payment_data[] = $row;
    }
    return $payment_data;
}

function get_next_payment_date($conn, $user_id) {
    $stmt = $conn->prepare("SELECT MIN(payment_date) as next_payment_date 
                            FROM Payments p
                            JOIN RentalAgreements ra ON p.agreement_id = ra.agreement_id
                            JOIN Tenants t ON ra.tenant_id = t.tenant_id
                            WHERE t.user_id = ? AND p.payment_date > NOW()");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['next_payment_date'] ? $row['next_payment_date'] : null;
}

$roles_data = get_user_roles_data($conn);
$database_size_data = get_database_size($conn);
$user_id = $_SESSION['user_id'];
$is_admin = check_user_role($conn, $user_id, 'administrator');
$is_owner = check_user_role($conn, $user_id, 'owner');
$is_tenant = check_user_role($conn, $user_id, 'tenant');

$tenant_task_data = $is_tenant ? get_tenant_task_data($conn, $user_id) : [];
$owner_task_data = $is_owner ? get_owner_task_data($conn, $user_id) : [];
$payment_status_data = $is_owner ? get_payment_status_data($conn, $user_id) : [];
$next_payment_date = $is_tenant ? get_next_payment_date($conn, $user_id) : null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" type="text/css" href="/apartmentmanagement/css/styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/includes/header.php'); ?>
    <main>
        <h2>Welcome <?php echo $_SESSION['username']; ?></h2>
        <p>Use the navigation menu to access specific management panels</p>

        <?php if ($is_admin): ?>
            <div class="chart-container">
                <h3>Number of Users by Role</h3>
                <canvas id="rolesChart"></canvas>
            </div>
            <div class="chart-container">
                <h3>Database Sizes</h3>
                <canvas id="dbSizeChart"></canvas>
            </div>
        <?php endif; ?>

        <?php if ($is_tenant): ?>
            <div class="chart-container">
                <h3>Maintenance Task Status</h3>
                <canvas id="tenantTasksChart"></canvas>
            </div>
            <div class="countdown-container">
                <h3>Days Until Next Payment</h3>
                <div id="countdown"></div>
            </div>
        <?php endif; ?>

        <?php if ($is_owner): ?>
            <div class="chart-container">
                <h3>Payment Status</h3>
                <canvas id="paymentStatusChart"></canvas>
            </div>
            <div class="chart-container">
                <h3>Maintenance Task Status</h3>
                <canvas id="ownerTasksChart"></canvas>
            </div>
        <?php endif; ?>
    </main>
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/includes/footer.php'); ?>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            <?php if ($is_admin): ?>
                const rolesData = <?php echo json_encode($roles_data); ?>;
                const roleLabels = rolesData.map(data => data.role);
                const roleCounts = rolesData.map(data => data.count);

                const rolesCtx = document.getElementById('rolesChart').getContext('2d');
                new Chart(rolesCtx, {
                    type: 'bar',
                    data: {
                        labels: roleLabels,
                        datasets: [{
                            label: '# of Users',
                            data: roleCounts,
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true,
                                stepSize: 1
                            }
                        }
                    }
                });

                const dbSizeData = <?php echo json_encode($database_size_data); ?>;
                const dbSizeLabels = dbSizeData.map(data => data['DB Name']);
                const dbSizes = dbSizeData.map(data => data['DB Size (MB)']);

                const dbSizeCtx = document.getElementById('dbSizeChart').getContext('2d');
                new Chart(dbSizeCtx, {
                    type: 'pie',
                    data: {
                        labels: dbSizeLabels,
                        datasets: [{
                            label: 'Database Size (MB)',
                            data: dbSizes,
                            backgroundColor: ['rgba(255, 99, 132, 0.2)', 'rgba(54, 162, 235, 0.2)', 'rgba(255, 206, 86, 0.2)'],
                            borderColor: ['rgba(255, 99, 132, 1)', 'rgba(54, 162, 235, 1)', 'rgba(255, 206, 86, 1)'],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false
                    }
                });
            <?php endif; ?>

            <?php if ($is_tenant): ?>
                const tenantTaskData = <?php echo json_encode($tenant_task_data); ?>;
                const tenantTaskLabels = tenantTaskData.map(data => data.status_name);
                const tenantTaskCounts = tenantTaskData.map(data => data.count);

                const tenantTasksCtx = document.getElementById('tenantTasksChart').getContext('2d');
                new Chart(tenantTasksCtx, {
                    type: 'bar',
                    data: {
                        labels: tenantTaskLabels,
                        datasets: [{
                            label: 'Tasks',
                            data: tenantTaskCounts,
                            backgroundColor: 'rgba(153, 102, 255, 0.2)',
                            borderColor: 'rgba(153, 102, 255, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true,
                                stepSize: 1
                            }
                        }
                    }
                });

                const nextPaymentDate = new Date("<?php echo $next_payment_date; ?>").getTime();
                const countdownElement = document.getElementById('countdown');

                const updateCountdown = () => {
                    const now = new Date().getTime();
                    const distance = nextPaymentDate - now;

                    const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                    countdownElement.innerHTML = `${days}d ${hours}h ${minutes}m ${seconds}s`;

                    if (distance < 0) {
                        clearInterval(countdownInterval);
                        countdownElement.innerHTML = "Payment is due!";
                    }
                };

                const countdownInterval = setInterval(updateCountdown, 1000);
                updateCountdown();
            <?php endif; ?>

            <?php if ($is_owner): ?>
                const paymentStatusData = <?php echo json_encode($payment_status_data); ?>;
                const paymentStatusLabels = paymentStatusData.map(data => data.payment_status);
                const paymentStatusCounts = paymentStatusData.map(data => data.count);

                const paymentStatusCtx = document.getElementById('paymentStatusChart').getContext('2d');
                new Chart(paymentStatusCtx, {
                    type: 'bar',
                    data: {
                        labels: paymentStatusLabels,
                        datasets: [{
                            label: '# of Payments',
                            data: paymentStatusCounts,
                            backgroundColor: 'rgba(255, 159, 64, 0.2)',
                            borderColor: 'rgba(255, 159, 64, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true,
                                stepSize: 1
                            }
                        }
                    }
                });

                const ownerTaskData = <?php echo json_encode($owner_task_data); ?>;
                const ownerTaskLabels = ownerTaskData.map(data => data.status_name);
                const ownerTaskCounts = ownerTaskData.map(data => data.count);

                const ownerTasksCtx = document.getElementById('ownerTasksChart').getContext('2d');
                new Chart(ownerTasksCtx, {
                    type: 'bar',
                    data: {
                        labels: ownerTaskLabels,
                        datasets: [{
                            label: 'Tasks',
                            data: ownerTaskCounts,
                            backgroundColor: 'rgba(153, 102, 255, 0.2)',
                            borderColor: 'rgba(153, 102, 255, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true,
                                stepSize: 1
                            }
                        }
                    }
                });
            <?php endif; ?>
        });
    </script>
</body>
</html>