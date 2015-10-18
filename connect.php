<?php session_start();
$dbHost = 'localhost';
$dbUser = 'hotcity';
$dbPass = 'hotcity@1234';
$dbName = 'hotcityAR';
$dbC = mysqli_connect($dbHost, $dbUser, $dbPass, $dbName)
        or die('Error Connecting to MySQL DataBase');
mysqli_set_charset($dbC, "utf8");
?>