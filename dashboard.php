<?php
/* Verifica autenticación y carga conexión a la base de datos */

require_once 'includes/auth.php';
require_once 'config/database.php';
/* Obtener número total de incidencias */
$sqlTotal = "
	SELECT COUNT(*) total
	FROM incidencias";

$stmtTotal = $conexion->query($sqlTotal);

$totalIncidencias =
$stmtTotal->fetch(PDO::FETCH_ASSOC);

/*Obtener incidencias abiertas*/

$sqlAbiertas = "
	SELECT COUNT(*) total
	FROM incidencias
	WHERE id_estado = 1
	";
$stmtAbiertas = $conexion->query($sqlAbiertas);

$abiertas =
$stmtAbiertas->fetch(PDO::FETCH_ASSOC);

/*Obtener incidencias en curso*/

$sqlCurso = "
	SELECT COUNT(*) total
	FROM incidencias
	WHERE id_estado = 2
	";
$stmtCurso = $conexion->query($sqlCurso);

$curso =
$stmtCurso->fetch(PDO::FETCH_ASSOC);

/*Obtener incidencias resueltas*/

$sqlResueltas = "
	SELECT COUNT(*) total
	FROM incidencias
	WHERE id_estado = 3
	";
$stmtResueltas = $conexion->query($sqlResueltas);

$resueltas =
$stmtResueltas->fetch(PDO::FETCH_ASSOC);

/*Obtener incidencias cerradas*/

$sqlCerradas = "
	SELECT COUNT(*) total
	FROM incidencias
	WHERE id_estado = 4
	";
$stmtCerradas = $conexion->query($sqlCerradas);

$cerradas =
$stmtCerradas->fetch(PDO::FETCH_ASSOC);

/* Obtener las últimas incidencias registradas */

$sqlUltimas = "
	SELECT
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
	LIMIT 5
	";
$stmtUltimas = $conexion->query($sqlUltimas);

$ultimasIncidencias =
$stmtUltimas->fetchAll(PDO::FETCH_ASSOC);

/* Obtener incidencias agrupadas por categoría */

$sqlCategorias = "
	SELECT
	c.nombre_categoria,
	COUNT(i.id_incidencia) AS total
	FROM categorias c
	LEFT JOIN incidencias i
		ON c.id_categoria = i.id_categoria
	GROUP BY c.id_categoria, c.nombre_categoria
	ORDER BY total DESC
	";

$stmtCategorias = $conexion->query($sqlCategorias);

$estadisticasCategorias =
$stmtCategorias->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="es">
<head>
<link rel="stylesheet" href="assets/css/style.css">
<meta charset="UTF-8">
<title>TuringHelpDesk</title>
</head>

<body>
<!-- Contenedor principal del dashboard -->
<div class="container">

<h1>TuringHelpDesk</h1>
<!-- Información del usuario autenticado -->
<p>
Bienvenido
<?php
echo $_SESSION ['nombre'];
?>
</p>

<p>
Rol:
<?php
echo $_SESSION ['rol'];
?>
</p>
<!-- Panel exclusivo para administradores -->
<?php if ($_SESSION['rol'] == 'admin'): ?>

<h2> Estadísticas Generales</h2>
<!-- Resumen global de incidencias -->
<div class="stats-grid">

<div class="stat-card">
	<h3>Total incidencias</h3>
	<div class="stat-value">
	<?php echo $totalIncidencias['total'];?>
	</div>
</div>


<div class="stat-card">
	<h3>Abiertas</h3>
	<div class="stat-value">
	<?php echo $abiertas['total'];?>
	</div>
</div>

<div class="stat-card">
	<h3>En curso</h3>
	<div class="stat-value">
	<?php echo $curso['total'];?>
	</div>
</div>

<div class="stat-card">
	<h3>Resueltas</h3>
	<div class="stat-value">
	<?php echo $resueltas['total'];?>
	</div>
</div>

<div class="stat-card">
	<h3>Cerradas</h3>
	<div class="stat-value">
	<?php echo $cerradas['total'];?>
	</div>
</div>

</div>
<!-- Tabla con las últimas incidencias registradas -->
<h2>Últimas incidencias</h2>

<table border="1">
<tr>
	<th>ID</th>
	<th>Título</th>
	<th>Usuario</th>
	<th>Estado</th>
	<th>Prioridad</th>
	<th>Fecha</th>
	<th>Acción</th>
</tr>

<?php foreach($ultimasIncidencias as $incidencia): ?>
<tr>
	<td><?php echo $incidencia['id_incidencia']; ?></td>
	<td><?php echo htmlspecialchars($incidencia['titulo']); ?></td>
	<td><?php echo htmlspecialchars($incidencia['usuario']); ?></td>
	<td><?php echo htmlspecialchars($incidencia['nombre_estado']); ?></td>
	<td><?php echo htmlspecialchars($incidencia['nombre_prioridad']); ?></td>
	<td><?php echo $incidencia['fecha_creacion']; ?></td>
	<td><a href="incidencias/ver.php?id=<?php echo $incidencia['id_incidencia']; ?>">
	Ver</a></td>
</tr>
<?php endforeach; ?>

</table>

<hr><!-- Estadísticas por categoría -->
<h2>Incidencias por Categoria</h2>

<table border="1">
<tr>
	<th>Categoria</th>
	<th>Total</th>
</tr>

<?php foreach($estadisticasCategorias as $categoria): ?>

<tr>
	<td><?php echo htmlspecialchars($categoria['nombre_categoria']); ?></td>
	<td><?php echo $categoria['total']; ?></td>
</tr>

<?php endforeach; ?>
</table>
<!-- Accesos rápidos de administración -->
<div class="dashboard-grid">

	<div class="dashboard-card">

	<h3>Incidencias</h3>
	<p>Consulta y administra todas las incidencias registradas.</p>
	<a class="btn" href="incidencias/listar.php">
		Ver incidencias
	<a>
	</div>

	<div class="dashboard-card">
	<p>Administración de usuarios del sistema.</p>
	<a class="btn" href="usuarios/listar.php">
		Gestionar usuarios
	</a>
	</div>

	<div class="dashboard-card">
	<p>Genera un csv con todas las incidencias.</p>
	<a class="btn" href="exportar.php">
		Exportar a CSV
	</a>
	</div>
</div>

<?php endif; ?>
<!-- Panel exclusivo para profesores -->
<?php if($_SESSION['rol'] == 'usuario'): ?>
<h2> Panel Usuario</h2>

<div class="dashboard-grid">

	<div class="dashboard-card">
	<h3>Nueva incidencia</h3>
	<p>Registrar una nueva incidencia</p>
	<a class="btn" href="incidencias/crear.php">
		Crear incidencia
	</a>
	</div>

	<div class="dashboard-card">
	<h3>Mis incidencias</h3>
	<p>Consultar el estado de tus incidencias.</p>
	<a class= "btn" href="incidencias/mis_incidencias.php">
		Ver incidencias
	</a>
	</div>
</div>

<?php endif; ?>

<hr>

<?php if($_SESSION['rol'] == 'admin'): ?>
<?php endif; ?>
<br><br>
<!-- Cierre de sesión -->
<a href="logout.php">
	Cerrar sesión
</a>

</div>
</body>
</html>
