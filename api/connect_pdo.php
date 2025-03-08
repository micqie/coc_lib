<?php


$server = "localhost";
$username = "root";
$password = "";
$dbname = "libdb";


try {
    $conn = new PDO ("mysql:host=$server;dbname=$dbname", $username,$password);
    // set the pdo error to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // display "connected successfully";
}  catch(PDOException $e) {
    echo"Connection failed: " . $e->getMessage();
}


?>