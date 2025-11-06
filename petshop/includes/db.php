<?php
// includes/db.php
// Update these values to match your MySQL server
$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '123456789';
$DB_NAME = 'pet_shop_db';

$mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($mysqli->connect_errno) {
    http_response_code(500);
    die('Database connection failed: ' . $mysqli->connect_error);
}
$mysqli->set_charset('utf8mb4');
?>