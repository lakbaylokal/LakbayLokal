<?php
require_once __DIR__ . '/../includes/config.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: signup.php');
    exit;
}

$fullname = trim($_POST['fullname'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$confirm = $_POST['confirm_password'] ?? '';

if ($fullname === '' || $email === '' || $password === '' || $confirm === '') {
    header('Location: signup.php?error=missing');
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: signup.php?error=email');
    exit;
}

if ($password !== $confirm) {
    header('Location: signup.php?error=match');
    exit;
}

if (strlen($password) < 6) {
    header('Location: signup.php?error=server');
    exit;
}

$mysqli = db();
$stmt = $mysqli->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
if (!$stmt) {
    header('Location: signup.php?error=server');
    exit;
}
$stmt->bind_param('s', $email);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    $stmt->close();
    header('Location: signup.php?error=exists');
    exit;
}
$stmt->close();

$hash = password_hash($password, PASSWORD_DEFAULT);
$stmt = $mysqli->prepare('INSERT INTO users (fullname, email, password) VALUES (?, ?, ?)');
if (!$stmt) {
    header('Location: signup.php?error=server');
    exit;
}
$stmt->bind_param('sss', $fullname, $email, $hash);
if (!$stmt->execute()) {
    $stmt->close();
    header('Location: signup.php?error=server');
    exit;
}
$stmt->close();

session_regenerate_id(true);
$_SESSION['user'] = $fullname;
$_SESSION['role'] = 'user';

header('Location: ../index.php?success=registered');
exit;
