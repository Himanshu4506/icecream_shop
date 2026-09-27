<?php
// ---------------------------------------------------------
// Database Connection (XAMPP defaults: user=root, no password)
// ---------------------------------------------------------
$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'icecream_shop';

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error .
        '<br>Make sure XAMPP MySQL is running and you imported database/icecream_shop.sql');
}

$conn->set_charset('utf8mb4');
