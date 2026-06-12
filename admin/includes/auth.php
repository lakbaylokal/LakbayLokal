<?php
// admin/includes/auth.php — Session Auth Guard

session_start();

// Redirect to login if not admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../index.php?auth=required');
    exit;
}

$adminName = htmlspecialchars($_SESSION['user']['FName'] ?? 'Admin');