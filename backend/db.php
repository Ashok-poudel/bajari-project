<?php
// db.php

$servername = "localhost";
$username = "root";      // your DB username
$password = "";          // your DB password
$dbname = "bajari_store"; // your database name

// Create connection
$conn = @new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    $connection_error = $conn->connect_error;
    $conn = null;
}
?>