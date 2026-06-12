<?php

$host = "localhost";
$dbname = "turinghelpdesk";
$user = "turingadmin";
$password = "TuringAsir2026*";

try {
     $conexion = new PDO(
	 "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
	 $user,
	 $password
     );

     $conexion->setAttribute(
	 PDO::ATTR_ERRMODE,
	 PDO::ERRMODE_EXCEPTION
     );

  } catch(PDOException $e) {

      die("Error de conexion: " . $e->getMessage());

  }

?>
