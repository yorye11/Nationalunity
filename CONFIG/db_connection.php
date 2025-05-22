<?php
$host = "localhost";
$user = "root";
$password = "";
$database = "NationalUnity";

$conn = new mysqli ($host,$user,$password,$database);

if ($conn->connect_error){
    die("Error en la conexion: " . $conn->connect_error);
}
?>