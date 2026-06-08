<?php
include 'config/db.php';
// api_auth.php — LakbayLokal Authentication API
// Handles login, signup, and logout operations

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? '';

// Simple in-memory user database (in production, use a real database)
// This is stored in session for demo purposes
if (!isset($_SESSION['users'])) {
    $_SESSION['users'] = [
        [
            'id' => 1,
            'first_Name' => 'Juan',
            'last_Name' => 'Dela Cruz',
            'Email' => 'juan@example.com',
            'Password' => password_hash('password123', PASSWORD_BCRYPT)
        ]
    ];
}

switch ($action) {
    case 'login':
        handleLogin();
        break;
    case 'signup':
        handleSignup();
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
}

function handleLogin() {

    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($email === 'admin@lakbaylokal.com' && $password === 'admin123') {

        $_SESSION['user'] = [
            'id' => 1,
            'FName' => 'Admin',
            'LName' => 'User',
            'Email' => $email,
            'role' => 'admin'
        ];

        echo json_encode([
            'success' => true,
            'message' => 'Admin login successful',
            'user' => $_SESSION['user']
        ]);

        return;
    }

    if ($email === 'user@test.com' && $password === 'user123') {

        $_SESSION['user'] = [
            'id' => 2,
            'FName' => 'Test',
            'LName' => 'User',
            'Email' => $email,
            'role' => 'user'
        ];

        echo json_encode([
            'success' => true,
            'message' => 'User login successful',
            'user' => $_SESSION['user']
        ]);

        return;
    }

    $user = findUserByEmail($email);
    if ($user && password_verify($password, $user['Password'])) {
        $_SESSION['user'] = [
            'id' => $user['id'],
            'first_Name' => $user['first_Name'],
            'last_Name' => $user['last_Name'],
            'Email' => $user['Email'],
            'role' => 'user',
            'name' => $user['first_Name']
        ];

        echo json_encode([
            'success' => true,
            'message' => 'User login successful',
            'user' => $_SESSION['user']
        ]);

        return;
    }

    echo json_encode([
        'success' => false,
        'message' => 'Incorrect email or password.'
    ]);
}

function handleSignup() {
    $first_Name = $_POST['first_Name'] ?? '';
    $last_Name = $_POST['last_Name'] ?? '';
    $Email = $_POST['email'] ?? '';
    $Password = $_POST['password'] ?? '';

    if (empty($first_Name) || empty($last_Name) || empty($Email) || empty($Password)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        return;
    }

    if (!isValidName($first_Name) || !isValidName($last_Name)) {
        echo json_encode(['success' => false, 'message' => 'Name must contain letters only.']);
        return;
    }

    if (strlen($Password) < 6) {
        echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters long.']);
        return;
    }

    if (findUserByEmail($Email)) {
        echo json_encode(['success' => false, 'message' => 'Email already exists']);
        return;
    }

    $newUser = [
        'id' => count($_SESSION['users']) + 1,
        'first_Name' => $first_Name,
        'last_Name' => $last_Name,
        'Email' => $Email,
        'Password' => password_hash($Password, PASSWORD_BCRYPT)
    ];

    $_SESSION['users'][] = $newUser;
    
    $_SESSION['user'] = [
        'id' => $newUser['id'],
        'first_Name' => $newUser['first_Name'],
        'last_Name' => $newUser['last_Name'],
        'Email' => $newUser['Email'],
        'name' => $newUser['first_Name']
    ];

    echo json_encode([
        'success' => true,
        'message' => 'Signup successful',
        'user' => [
            'first_Name' => $newUser['first_Name'],
            'last_Name' => $newUser['last_Name'],
            'Email' => $newUser['Email']
        ]
    ]);
}

function handleLogout() {
    unset($_SESSION['user']);
    echo json_encode(['success' => true, 'message' => 'Logout successful']);
}

function handleGetCurrentUser() {
    if (isset($_SESSION['user'])) {
        echo json_encode([
            'success' => true,
            'user' => [
                'first_Name' => $_SESSION['user']['first_Name'],
                'last_Name' => $_SESSION['user']['last_Name'],
                'Email' => $_SESSION['user']['Email']
            ]
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No user logged in']);
    }
}

function findUserByEmail($email) {
    foreach ($_SESSION['users'] as $user) {
        if ($user['Email'] === $email) {
            return $user;
        }
    }
    return null;
}

function isValidName($name) {
    return preg_match("/^[\\p{L}]+(?:[ '\\-][\\p{L}]+)*$/u", trim($name));
}
