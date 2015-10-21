<?php session_start();
$dbHost = 'localhost';
$dbUser = 'username';
$dbPass = 'pass';
$dbName = 'dbARname';
$dbC = mysqli_connect($dbHost, $dbUser, $dbPass, $dbName)
        or die('Error Connecting to MySQL DataBase');
mysqli_set_charset($dbC, "utf8");
?>