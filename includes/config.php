<?php if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


$scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME']);
$baseUrl = dirname($scriptName);
$baseUrl = preg_replace('#/pages$#', '', $baseUrl);
$baseUrl = rtrim($baseUrl, '/');
define('BASE_URL', $baseUrl);
?>

$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'lakbaylokal';

if (!defined('BASE_URL')) {
    define('BASE_URL', '/LAKBAYLOKAL');
}

$mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($mysqli->connect_error) {
    die('Database connection failed: ' . $mysqli->connect_error);
}
$mysqli->set_charset('utf8mb4');

function db()
{
    global $mysqli;
    return $mysqli;
}

