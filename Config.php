<?php


define('DB_HOST', 'localhost');
define('DB_USER', 'root');       // Default XAMPP user
define('DB_PASS', '');           // Default XAMPP = empty password
define('DB_NAME', 'aashdev_portfolio');

define('OWNER_EMAIL', 'moahmmedaashid1@email.com'); // your email

function getDB() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        error_log('DB Error: ' . $conn->connect_error);
        return null;
    }
    $conn->set_charset('utf8mb4');
    return $conn;
}
?>