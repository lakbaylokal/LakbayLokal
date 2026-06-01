<?php
/**
 * Database Configuration File
 * LakbayLokal - Travel Booking System
 * 
 * This file contains all database connection settings.
 * Keep this file secure and never commit passwords to version control!
 */

// Database credentials
define('DB_HOST',     'localhost');      // MySQL server hostname
define('DB_USER',     'root');           // MySQL username (change this!)
define('DB_PASSWORD', '');               // MySQL password (change this!)
define('DB_NAME',     'lakbaylokal');    // Database name

// Optional: Database charset
define('DB_CHARSET',  'utf8mb4');

/**
 * Create database connection using MySQLi
 */
$connection = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

// Check connection
if ($connection->connect_error) {
    die('Database Connection Failed: ' . $connection->connect_error);
}

// Set charset to utf8mb4 for proper emoji and special character support
$connection->set_charset(DB_CHARSET);

// Store connection in global variable for easy access
$db = $connection;

/**
 * IMPORTANT SECURITY NOTES:
 * 
 * 1. Always use prepared statements when handling user input
 * 2. Use parameterized queries to prevent SQL injection
 * 3. Never echo user input directly to the page
 * 4. Use htmlspecialchars() when displaying user data
 * 5. Keep database credentials in a config file OUTSIDE the web root
 * 6. Never share database passwords in code repositories
 * 7. Use environment variables for sensitive data in production
 * 
 * Example of SECURE query:
 *   $stmt = $db->prepare("SELECT * FROM users WHERE email = ? AND role = ?");
 *   $stmt->bind_param("ss", $email, $role);
 *   $stmt->execute();
 *   $result = $stmt->get_result();
 * 
 * Example of INSECURE query (NEVER DO THIS):
 *   $result = $db->query("SELECT * FROM users WHERE email = '" . $_GET['email'] . "'");
 */
?>
