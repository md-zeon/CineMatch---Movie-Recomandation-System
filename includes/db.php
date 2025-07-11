<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'cinematch';


// $host = 'sql12.freesqldatabase.com';
// $user = 'sql12789451';
// $pass = '3EexUQDjF2';
// $dbname = 'sql12789451';

$conn = new mysqli($host, $user, $pass, $dbname, 3306);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
