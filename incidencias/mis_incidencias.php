<?php
/* Verifica la sesión activa y carga la conexión a la base de datos */
require_once '../includes/auth.php';
require_once '../config/database.php';
/* Obtiene únicamente las incidencias creadas por el usuario autenticado */
$sql = " SELECT
	i.id_incidencia,
	i.titulo,
	i.fecha_creacion,
	e.nombre_estado,
	p.nombre_prioridad
FROM incidencias i
	INNER JOIN estados e
		ON i.id_estado = e.id_estado
	INNER JOIN prioridades p
		ON i.id_prioridad = p.id_prioridad
	WHERE i.id_usuario = :usuario
	ORDER BY i.fecha_creacion DESC
	";

$stmt = $conexion->prepare($sql);

$stmt->execute(['usuario'=> $_SESSION['usuario_id']]);

$incidencias = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="es">
<head>
<link rel="stylesheet" href="../assets/css/style.css">
<meta charset="UTF-8">
<title>Mis incidencias</title>
</head>

<body>
<h1>Mis incidencias</h1>
<!-- Tabla con las incidencias del usuario -->
<table class="ticket-table">

<tr>
	<th>ID</th>
	<th>Título</th>
	<th>Estado</th>
	<th>Prioridad</th>
	<th>Fecha</th>
</tr>

<?php foreach($incidencias as $incidencia): ?>

<tr>
	<td>
	<span class="ticket-id">
		<?php echo $incidencia['id_incidencia']; ?>
	</span>
	</td>

	<td>
		<a href="ver.php?id=<?php echo $incidencia['id_incidencia']; ?>">
		<?php echo $incidencia['titulo']; ?>
	</td>

	<td> <?php 
	/* Prepara la clase CSS para el estado */
		$estadoClase = strtolower($incidencia['nombre_estado']);
		if ($estadoClase == 'en curso') {
		    $estadoClase = 'curso';
		}?>
		<span class="estado estado-<?php echo $estadoClase; ?>">
		<?php echo htmlspecialchars($incidencia['nombre_estado']); ?>
		</span>
	</td>

	<td> <?php 
	/* Prepara clase CSS para la prioridad */
		$prioridadClase = strtolower($incidencia['nombre_prioridad']);
		if ($prioridadClase == 'crítica') {
		    $prioridadClase = 'critica';
		}?>
		<span class="priority-dot priority-<?php echo $prioridadClase; ?>"></span>
		<?php echo htmlspecialchars($incidencia['nombre_prioridad']); ?>
	</td>

	<td> <?php echo $incidencia['fecha_creacion']; ?> </td>

</tr>

<?php endforeach; ?>

</table>
<br>
<!-- Enlace de retorno al dashboard -->
<a href="../dashboard.php"> Volver al panel</a>
</body>
</html>
