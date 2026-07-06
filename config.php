<?php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "babybliss_marketplace";

$conn = mysqli_connect($host, $user, $password, $dbname);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Set timezone
mysqli_query($conn, "SET time_zone = '+03:00'");
?>