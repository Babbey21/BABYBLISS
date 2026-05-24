<?php
$host ="localhost";
$user = "root";
$password = "";
$dbname = "Babybliss_db";

$conn = mysqli_connect($host, $user, $password, $dbname);
    if(!$conn){
        die("connection error");
    }
?>