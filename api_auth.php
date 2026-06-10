<?php
require_once 'config/db.php';
require_once 'database/helpers.php';
// api_auth.php — LakbayLokal Authentication API
// Handles login, signup, and logout operations with database persistence

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? '';

<<<<<<< HEAD
=======
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

>>>>>>> 3db191470f2b341d07139009958281966e0541da
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
}


function handleLogin($conn) {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

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

<<<<<<< HEAD
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Incorrect email or password.']);
=======
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

>>>>>>> 3db191470f2b341d07139009958281966e0541da
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
        'role' => $user['role'],
        'name' => $user['FName']
    ];

    echo json_encode([
        'success' => true,
        'message' => 'Login successful',
        'user' => $_SESSION['user']
    ]);
}

<<<<<<< HEAD


function handleSignup($conn) {
    $FName = $_POST['FName'] ?? '';
    $LName = $_POST['LName'] ?? '';
    $Mname = $_POST['Mname'] ?? '';
    $Email = $_POST['email'] ?? '';
    $Password = $_POST['password'] ?? '';

    if (empty($FName) || empty($LName) || empty($Email) || empty($Password)) {
        http_response_code(400);
=======
function handleSignup() {
    $first_Name = $_POST['first_Name'] ?? '';
    $last_Name = $_POST['last_Name'] ?? '';
    $Email = $_POST['email'] ?? '';
    $Password = $_POST['password'] ?? '';

    if (empty($first_Name) || empty($last_Name) || empty($Email) || empty($Password)) {
>>>>>>> 3db191470f2b341d07139009958281966e0541da
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        return;
    }

<<<<<<< HEAD
    if (!isValidName($FName) || !isValidName($LName)) {
        http_response_code(400);
=======
    if (!isValidName($first_Name) || !isValidName($last_Name)) {
>>>>>>> 3db191470f2b341d07139009958281966e0541da
        echo json_encode(['success' => false, 'message' => 'Name must contain letters only.']);
        return;
    }

    if (strlen($Password) < 6) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters long.']);
        return;
    }

    // Check if email already exists
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

<<<<<<< HEAD
    // Hash the password
    $hashedPassword = password_hash($Password, PASSWORD_BCRYPT);

    // Insert new user into database
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
=======
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
>>>>>>> 3db191470f2b341d07139009958281966e0541da
    ];

    echo json_encode([
        'success' => true,
        'message' => 'Signup successful',
<<<<<<< HEAD
        'user' => $_SESSION['user']
=======
        'user' => [
            'first_Name' => $newUser['first_Name'],
            'last_Name' => $newUser['last_Name'],
            'Email' => $newUser['Email']
        ]
>>>>>>> 3db191470f2b341d07139009958281966e0541da
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
<<<<<<< HEAD
                'FName' => $_SESSION['user']['FName'],
                'LName' => $_SESSION['user']['LName'],
                'Email' => $_SESSION['user']['Email'],
                'role' => $_SESSION['user']['role'] ?? 'user'
=======
                'first_Name' => $_SESSION['user']['first_Name'],
                'last_Name' => $_SESSION['user']['last_Name'],
                'Email' => $_SESSION['user']['Email']
>>>>>>> 3db191470f2b341d07139009958281966e0541da
            ]
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No user logged in']);
    }
}

function isValidName($name) {
    return preg_match("/^[\\p{L}]+(?:[ '\\-][\\p{L}]+)*$/u", trim($name));
}
