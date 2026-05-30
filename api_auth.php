<?php
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
            'FName' => 'Juan',
            'LName' => 'Dela Cruz',
            'Mname' => 'Sample',
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

    if (empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Email and password are required']);
        return;
    }

    $user = findUserByEmail($email);
    
    if ($user && password_verify($password, $user['Password'])) {
        $_SESSION['user'] = [
            'id' => $user['id'],
            'FName' => $user['FName'],
            'LName' => $user['LName'],
            'Mname' => $user['Mname'] ?? '',
            'Email' => $user['Email'],
            'name' => $user['FName']
        ];
        echo json_encode([
            'success' => true,
            'message' => 'Login successful',
            'user' => [
                'FName' => $user['FName'],
                'LName' => $user['LName'],
                'Email' => $user['Email']
            ]
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
    }
}

function handleSignup() {
    $FName = $_POST['FName'] ?? '';
    $LName = $_POST['LName'] ?? '';
    $Mname = $_POST['Mname'] ?? '';
    $Email = $_POST['email'] ?? '';
    $Password = $_POST['password'] ?? '';

    if (empty($FName) || empty($LName) || empty($Email) || empty($Password)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        return;
    }

    if (findUserByEmail($Email)) {
        echo json_encode(['success' => false, 'message' => 'Email already exists']);
        return;
    }

    $newUser = [
        'id' => count($_SESSION['users']) + 1,
        'FName' => $FName,
        'LName' => $LName,
        'Mname' => $Mname,
        'Email' => $Email,
        'Password' => password_hash($Password, PASSWORD_BCRYPT)
    ];

    $_SESSION['users'][] = $newUser;
    
    $_SESSION['user'] = [
        'id' => $newUser['id'],
        'FName' => $newUser['FName'],
        'LName' => $newUser['LName'],
        'Mname' => $newUser['Mname'],
        'Email' => $newUser['Email'],
        'name' => $newUser['FName']
    ];

    echo json_encode([
        'success' => true,
        'message' => 'Signup successful',
        'user' => [
            'FName' => $newUser['FName'],
            'LName' => $newUser['LName'],
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
                'FName' => $_SESSION['user']['FName'],
                'LName' => $_SESSION['user']['LName'],
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
