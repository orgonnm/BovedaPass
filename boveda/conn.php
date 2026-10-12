<?php
$dbHost = 'localhost';
$dbUser = 'root';
$dbPass = '';
$dbName = 'bovedaDb';

$mysqli = new mysqli($dbHost, $dbUser, $dbPass, $dbName);

if ($mysqli->connect_error) {
    die("Error de conexión a la base de datos: " . $mysqli->connect_error);
}

$mysqli->set_charset("utf8mb4");
?>