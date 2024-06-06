<?php
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function check_user_role($conn, $user_id, $role) {
    $table = '';
    if ($role === 'administrator') {
        $table = 'Administrators';
    } elseif ($role === 'owner') {
        $table = 'Owners';
    } elseif ($role === 'tenant') {
        $table = 'Tenants';
    }

    if ($table) {
        $sql = "SELECT * FROM $table WHERE user_id='$user_id'";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            return true;
        }
    }
    return false;
}

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $is_admin = check_user_role($conn, $user_id, 'administrator');
    $is_tenant = check_user_role($conn, $user_id, 'tenant');
    $is_owner = check_user_role($conn, $user_id, 'owner');
} else {
    $user_id = null;
    $is_admin = false;
    $is_tenant = false;
    $is_owner = false;
}
?>
