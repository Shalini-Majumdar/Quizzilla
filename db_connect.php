<?php
$host = 'localhost';
$db = 'quiz_app';
$user = 'root';
$pass = ''; //enter your database password here

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
