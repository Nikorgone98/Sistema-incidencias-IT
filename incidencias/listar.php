<?php
/* Verifica la sesión activa y carga la conexión a la base de datos */
require_once '../includes/auth.php';
require_once '../config/database.php';
/* Restringe el listado completo para que solo puedan verlo los administradores */
if ($_SESSION['rol'] != 'admin')
{
	die("Acceso denegado");
}
/* Obtiene todas las incidencias con usuario, estado y prioridad */
$sql = " SELECT
	i.id_incidencia,
	i.titulo,
	i.fecha_creacion,
	u.nombre AS usuario,
	e.nombre_estado,
	p.nombre_prioridad
FROM incidencias i
	INNER JOIN usuarios u
		ON i.id_usuario = u.id_usuario
	INNER JOIN estados e
		ON i.id_estado = e.id_estado
	INNER JOIN prioridades p
		ON i.id_prioridad = p.id_prioridad
	ORDER BY i.fecha_creacion DESC
	";

$stmt = $conexion->query($sql);
$incidencias = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="es">
<head>
<link rel="stylesheet" href="../assets/css/style.css">
<meta charset="UTF-8">
<title>Todas las incidencias</title>
</head>

<body>
<h1>Todas las incidencias</h1>
<!-- Tabla principal de incidencias -->
<table class="ticket-table">

<tr>
	<th>ID</th>
	<th>Título</th>
	<th>Usuario</th>
	<th>Estado</th>
	<th>Prioridad</th>
	<th>Fecha</th>
</tr>

<?php foreach($incidencias as $incidencia): ?>

<tr>
	<td><span class="ticket-id">
		<?php echo $incidencia['id_incidencia']; ?></span>
	</td>

	<td>
		<a href="ver.php?id=<?php echo $incidencia['id_incidencia']; ?>">
		<?php echo $incidencia['titulo']; ?>
	</td>

	<td> <?php echo $incidencia['usuario']; ?> </td>

	<td> <?php
		/* Prepara clase CSS para el estado */
		$estadoClase = strtolower($incidencia['nombre_estado']);
		if ($estadoClase == 'en curso') {
		    $estadoClase = 'curso';
		}?>
		<span class="estado estado-<?php echo $estadoClase; ?>">
		<?php echo htmlspecialchars($incidencia['nombre_estado']); ?></span>
	</td>

	<td>
		<?php
		/* Prepara clase CSS para la prioridad */
		$prioridadClase = strtolower($incidencia['nombre_prioridad']);
		if ($prioridadClase == 'crítica') {
		    $prioridadClase = 'critica';
		}?>
		<span class="priority-dot priority-<?php echo $prioridadClase; ?>"></span>

		<?php echo htmlspecialchars($incidencia['nombre_prioridad']); ?>
	</td>

	<td>
		 <?php echo $incidencia['fecha_creacion']; ?>
	</td>

</tr>

<?php endforeach; ?>

</table>
<br>
<!-- Enlace de retorno al dashboard -->
<a href="../dashboard.php"> Volver al panel</a>
</body>
</html>
