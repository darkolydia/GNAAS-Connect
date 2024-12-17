<?php
$host = 'localhost';
$username = 'lydia.amoakoaa';
$password = '23Jan31Dec.';
$dbname = 'webtech_fall2024_lydia_amoakoaa';

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>