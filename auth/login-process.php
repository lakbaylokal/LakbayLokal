<?php
require_once __DIR__ . '/../includes/config.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if ($email === '' || $password === '') {
    header('Location: login.php?error=missing');
    exit;
}

$mysqli = db();
$stmt = $mysqli->prepare('SELECT fullname, password, role FROM users WHERE email = ? LIMIT 1');
if (!$stmt) {
    header('Location: login.php?error=server');
    exit;
}
$stmt->bind_param('s', $email);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows === 0) {
    $stmt->close();
    header('Location: login.php?error=invalid');
    exit;
}
$stmt->bind_result($fullname, $hash, $role);
if (!$stmt->fetch() || $hash === null) {
    $stmt->close();
    header('Location: login.php?error=invalid');
    exit;
}
$stmt->close();

if (!is_string($hash) || !password_verify($password, $hash)) {
    header('Location: login.php?error=invalid');
    exit;
}

session_regenerate_id(true);
$_SESSION['user'] = $fullname;
$_SESSION['role'] = $role;

header('Location: ../index.php');
exit;
