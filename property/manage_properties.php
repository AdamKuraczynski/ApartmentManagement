<?php
include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/auth.php'); 
include($_SERVER['DOCUMENT_ROOT'] . '/ApartmentManagement/includes/functions.php'); 
?>
<!DOCTYPE html>
<html>
<head>
    <title>Apartment Management</title>
    <link rel="stylesheet" type="text/css" href="/apartmentmanagement/css/styles.css">
</head>
<body>
    <header>
        <h1>Apartment Management System</h1>
        <nav>
    <a href="/apartmentmanagement/index.php">Home</a>
    <?php if (check_user_role('administrator')): ?>
        <a href="/apartmentmanagement/admin_dashboard.php">Admin Dashboard</a>
    <?php endif; ?>
    <?php if (check_user_role('owner')): ?>
        <a href="/apartmentmanagement/owner_dashboard.php">Owner Dashboard</a>
    <?php endif; ?>
    <?php if (check_user_role('tenant')): ?>
        <a href="/apartmentmanagement/tenant_dashboard.php">Tenant Dashboard</a>
    <?php endif; ?>
        </nav>
    </header>
    <main>