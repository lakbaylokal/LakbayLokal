<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME']);
$baseUrl = dirname($scriptName);
// Strip subdirectory folders to always point to app root
$baseUrl = preg_replace('#/(hotels|pages|auth|admin|reviews|includes)$#', '', $baseUrl);
$baseUrl = rtrim($baseUrl, '/');

define('BASE_URL', $baseUrl);

$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'lakbaylokal';

mysqli_report(MYSQLI_REPORT_OFF);

$mysqli = mysqli_init();
if ($mysqli === false) {
    die('Database connection failed: Could not initialize MySQLi.');
}

try {
    $connected = @mysqli_real_connect($mysqli, $DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
    if (! $connected) {
        throw new mysqli_sql_exception(mysqli_connect_error());
    }
} catch (mysqli_sql_exception $e) {
    $errorMessage = $e->getMessage();
    if (strpos($errorMessage, 'Unknown database') !== false) {
        $mysqli = mysqli_init();
        if ($mysqli === false) {
            die('Database connection failed: Could not initialize MySQLi for database creation.');
        }

        if (!@mysqli_real_connect($mysqli, $DB_HOST, $DB_USER, $DB_PASS)) {
            die('Database connection failed: ' . mysqli_connect_error());
        }

        $sqlFile = __DIR__ . '/../lakbaylokal.sql';
        if (!file_exists($sqlFile)) {
            die("Database connection failed: $errorMessage. The SQL file 'lakbaylokal.sql' was not found in the project root.");
        }

        $sql = file_get_contents($sqlFile);
        if ($sql === false) {
            die('Unable to read the database schema file: ' . $sqlFile);
        }

        if (!mysqli_multi_query($mysqli, $sql)) {
            die('Failed to initialize database: ' . mysqli_error($mysqli));
        }

        while (mysqli_more_results($mysqli) && mysqli_next_result($mysqli)) {
            // consume remaining results
        }

        mysqli_close($mysqli);

        $mysqli = mysqli_init();
        if ($mysqli === false) {
            die('Database connection failed: Could not initialize MySQLi after database creation.');
        }

        if (!@mysqli_real_connect($mysqli, $DB_HOST, $DB_USER, $DB_PASS, $DB_NAME)) {
            die('Database connection failed after database creation: ' . mysqli_connect_error());
        }
    } else {
        die('Database connection failed: ' . $errorMessage);
    }
}

$mysqli->set_charset('utf8mb4');

function db()
{
    global $mysqli;
    return $mysqli;
}