<?php
/* Parámetros de conexión a la base de datos */
$host = "localhost";
$dbname = "turinghelpdesk";
$user = "turingadmin";
$password = "TuringAsir2026*";
 /* Crea conexión PDO con MariaDB */
try {
     $conexion = new PDO(
	 "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
	 $user,
	 $password
     );
/* Activa gestión de errores mediante excepciones */
     $conexion->setAttribute(
	 PDO::ATTR_ERRMODE,
	 PDO::ERRMODE_EXCEPTION
     );

  } catch(PDOException $e) {
/* Muestra el error y detiene la ejecución */
      die("Error de conexion: " . $e->getMessage());

  }

?>
