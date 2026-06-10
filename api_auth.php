<?php
require_once 'config/db.php';
// api_auth.php — LakbayLokal Authentication API
// Handles login, signup, logout, and current-user checks

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'login':
        handleLogin($conn);
        break;
    case 'signup':
        handleSignup($conn);
        break;
    case 'logout':
        handleLogout();
        break;
    case 'getCurrentUser':
        handleGetCurrentUser();
        break;
    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}

function handleLogin($conn) {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($email) || empty($password)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Email and password required']);
        return;
    }

    $query = "SELECT id, FName, LName, Email, Password, role FROM users WHERE Email = ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
        return;
    }

    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Incorrect email or password.']);
        $stmt->close();
        return;
    }

    $user = $result->fetch_assoc();
    $stmt->close();

    if (!password_verify($password, $user['Password'])) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Incorrect email or password.']);
        return;
    }

    $_SESSION['user'] = [
        'id' => $user['id'],
        'FName' => $user['FName'],
        'LName' => $user['LName'],
        'Email' => $user['Email'],
        'role' => $user['role'] ?? 'user',
        'name' => $user['FName']
    ];

    echo json_encode(['success' => true, 'message' => 'Login successful', 'user' => $_SESSION['user']]);
}

function handleSignup($conn) {
    $FName = trim($_POST['FName'] ?? '');
    $LName = trim($_POST['LName'] ?? '');
    $Mname = trim($_POST['Mname'] ?? '');
    $Email = trim($_POST['email'] ?? '');
    $Password = trim($_POST['password'] ?? '');

    if (empty($FName) || empty($LName) || empty($Email) || empty($Password)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        return;
    }

    if (!isValidName($FName) || !isValidName($LName)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Name must contain letters only.']);
        return;
    }

    if (!filter_var($Email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid email address']);
        return;
    }

    if (strlen($Password) < 6) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters long.']);
        return;
    }

    $checkQuery = "SELECT id FROM users WHERE Email = ?";
    $checkStmt = $conn->prepare($checkQuery);
    if (!$checkStmt) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
        return;
    }

    $checkStmt->bind_param('s', $Email);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    $checkStmt->close();

    if ($checkResult->num_rows > 0) {
        http_response_code(409);
        echo json_encode(['success' => false, 'message' => 'Email already exists']);
        return;
    }

    $hashedPassword = password_hash($Password, PASSWORD_BCRYPT);

    $insertQuery = "INSERT INTO users (FName, LName, Mname, Email, Password, role) VALUES (?, ?, ?, ?, ?, 'user')";
    $insertStmt = $conn->prepare($insertQuery);
    if (!$insertStmt) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
        return;
    }

    $insertStmt->bind_param('sssss', $FName, $LName, $Mname, $Email, $hashedPassword);
    if (!$insertStmt->execute()) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to create account']);
        $insertStmt->close();
        return;
    }

    $newUserId = $insertStmt->insert_id;
    $insertStmt->close();

    $_SESSION['user'] = [
        'id' => $newUserId,
        'FName' => $FName,
        'LName' => $LName,
        'Email' => $Email,
        'role' => 'user',
        'name' => $FName
    ];

    echo json_encode(['success' => true, 'message' => 'Signup successful', 'user' => $_SESSION['user']]);
}

function handleLogout() {
    unset($_SESSION['user']);
    echo json_encode(['success' => true, 'message' => 'Logout successful']);
}

function handleGetCurrentUser() {
    if (isset($_SESSION['user'])) {
        echo json_encode(['success' => true, 'user' => [
            'FName' => $_SESSION['user']['FName'],
            'LName' => $_SESSION['user']['LName'],
            'Email' => $_SESSION['user']['Email'],
            'role' => $_SESSION['user']['role'] ?? 'user'
        ]]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No user logged in']);
    }
}

function isValidName($name) {
    return preg_match("/^[\\p{L}]+(?:[ '\\-][\\p{L}]+)*$/u", trim($name));
}
