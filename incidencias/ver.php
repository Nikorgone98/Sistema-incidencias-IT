<?php

require_once '../includes/auth.php';
require_once '../config/database.php';

if (!isset($_GET['id']))
{
	die("Incidencia no especificada");
}

$id = (int) $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST'
    &&
    isset($_POST['comentario'])
    &&
    !$incidenciaCerrada
)
{
	$comentario = trim($_POST['comentario']);
	$sqlInsert = "
	INSERT INTO comentarios
	(
		id_incidencia,
		id_usuario,
		comentario
	)
	VALUES
	(
		:incidencia,
		:usuario,
		:comentario
	)";
$stmtInsert = $conexion->prepare($sqlInsert);
$stmtInsert->execute([
		'incidencia' => $id,
		'usuario' => $_SESSION['usuario_id'],
		'comentario' => $comentario
		]);
header("Location: ver.php?id=" . $id);
exit();
}

if($_SERVER['REQUEST_METHOD'] === 'POST'
   &&
   isset($_POST['cambiar_estado'])
   &&
   $_SESSION['rol'] =='admin'
   &&
   !$incidenciaCerrada
)
{
	$nuevoEstado = (int) $_POST['nuevo_estado'];

/* Obtener el estado actual*/

	$sqlActual = "
	SELECT e.nombre_estado
	FROM incidencias i
	INNER JOIN estados e
		ON i.id_estado = e.id_estado
	WHERE i.id_incidencia = :id
	";

	$stmtActual = $conexion->prepare($sqlActual);

	$stmtActual->execute([
		'id' => $id
	]);

	$estadoActual = $stmtActual->fetch(PDO::FETCH_ASSOC);

/*Obtener nombre de nuevo estado*/

	$sqlNuevo = "
	SELECT nombre_estado
	FROM estados
	WHERE id_estado = :estado
	";

	$stmtNuevo = $conexion->prepare($sqlNuevo);

	$stmtNuevo->execute([
	'estado' => $nuevoEstado
	]);

	$estadoNuevo = $stmtNuevo->fetch(PDO::FETCH_ASSOC);

/* Actualizar el estado*/

	$sqlEstado = "
	UPDATE incidencias
	SET id_estado = :estado
	WHERE id_incidencia = :incidencia
	";

	$stmtEstado = $conexion->prepare($sqlEstado);

	$stmtEstado->execute([
	'estado' => $nuevoEstado,
	'incidencia' => $id
	]);

/* Crear comentario automatico*/

	$mensajeSistema =
	"Administrador cambió el estado de '" .
	$estadoActual['nombre_estado'] .
	"' a '" .
	$estadoNuevo['nombre_estado'] .
	"'";

	$sqlComentario = "
	INSERT INTO comentarios
	(comentario, id_usuario, id_incidencia)
	VALUES
	(:comentario, :usuario, :incidencia)
	";

	$stmtComentario = $conexion->prepare($sqlComentario);

	$stmtComentario->execute([
	'comentario' => $mensajeSistema,
	'usuario' => $_SESSION['usuario_id'],
	'incidencia' => $id
	]);


header("Location: ver.php?id=" . $id);
exit();
}


$sql = " SELECT
	i.*,
	u.nombre AS usuario,
	e.nombre_estado,
	p.nombre_prioridad,
	c.nombre_categoria
FROM incidencias i
	INNER JOIN usuarios u
		ON i.id_usuario = u.id_usuario
	INNER JOIN estados e
		ON i.id_estado = e.id_estado
	INNER JOIN prioridades p
		ON i.id_prioridad = p.id_prioridad
	INNER JOIN categorias c
		ON i.id_categoria = c.id_categoria
	WHERE i.id_incidencia = :id
	";

$stmt = $conexion->prepare($sql);

$stmt->execute([ 'id' => $id]);

$incidencia = $stmt->fetch(PDO::FETCH_ASSOC);

$incidenciaCerrada =
(
	$incidencia['id_estado'] == 4
);


$sqlComentarios = "
	SELECT c.*, u.nombre
	FROM comentarios c
	INNER JOIN usuarios u
		on c.id_usuario = u.id_usuario
	WHERE c.id_incidencia = :id
	ORDER BY c.fecha_comentario ASC
	";
$stmtComentarios = $conexion->prepare($sqlComentarios);
$stmtComentarios ->execute(['id' => $id]);
$comentarios = $stmtComentarios->fetchAll(PDO::FETCH_ASSOC);

if (!$incidencia)
{
	die("Incidencia no encontrada");
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
<link rel="stylesheet" href="../assets/css/style.css">
<meta charset="UTF-8">
<title>Incidencia</title>
</head>

<body>

<div class="container">

<!-- CABECERA DE LA INCIDENCIA -->

<h1>
	Incidencia #<?php echo $incidencia['id_incidencia']; ?>
</h1>

<div class= "ticket-layout">

<!-- COLUMNA IZQUIERDA CON LOS DETALLES DE LA INCIDENCIA -->

	<div class= "ticket-detail">

		<h2><?php echo htmlspecialchars($incidencia['titulo']); ?></h2>

		<p>
			<strong>Usuario:</strong>
			<?php echo htmlspecialchars($incidencia['usuario']); ?>
		</p>

		<p>
			<strong>Estado:</strong>
			<?php
			$estadoClase = strtolower($incidencia['nombre_estado']);

				if ($estadoClase == 'en curso') { $estadoClase = 'curso'; }
			?>
			<span class="estado estado-<?php echo $estadoClase; ?>">
				<?php echo htmlspecialchars($incidencia['nombre_estado']); ?>
			</span>
		</p>

		<p>
			<strong>Prioridad:</strong>

			<?php $prioridadClase = strtolower($incidencia['nombre_prioridad']);

				if ($prioridadClase == 'crítica') {$prioridadClase = 'critica'; }?>

			<span class="priority-dot priority-<?php echo $prioridadClase; ?>"></span>

			<?php echo htmlspecialchars($incidencia['nombre_prioridad']); ?>
		</p>

		<p>
			<strong>Categoría:</strong>
			<?php echo htmlspecialchars($incidencia['nombre_categoria']); ?>
		</p>

		<p>
			<strong>Fecha:</strong>
			<?php echo $incidencia['fecha_creacion']; ?>
		</p>

		<hr>
<!-- Aqui va la descripción -->

		<h2>Descripción<h2>

		<p>
			<?php echo nl2br(htmlspecialchars($incidencia['descripcion'])); ?>
		</p>

		<hr>

<!-- GESTION SOLO PARA ADMIN Y SI LA INCIDENCIA NO ESTA CERRADA -->

		<?php if($_SESSION['rol'] == 'admin' && !$incidenciaCerrada): ?>

		<h2>Gestión</h2>

			<form method="POST">

			<label>Cambiar estado</label>

			<select name="nuevo_estado">
				<option value="1">Abierta</option>
				<option value="2">En curso</option>
				<option value="3">Resuelta</option>
				<option value="4">Cerrada</option>
			</select>

			<br><br>

			<button type="sumbit" name="cambiar_estado">Cambiar estado</button>

			</form>

			<br>

			<a class="btn btn-secondary"
				 href="editar.php?id=<?php echo $incidencia['id_incidencia']; ?>">
				Editar incidencia
			</a>

		<?php endif; ?>

		<br><br>

<!-- VOLVER -->
		<a href="../dashboard.php">
			Volver al panel
		</a>
	</div>

<!-- COLUMNA DERECHA DONDE VA EL CHAT DE LOS COMENTARIOS -->

	<div class="chat-panel">

		<h2>Comentarios</h2>

<!-- HISTORIAL DE COMENTARIOS -->

		<?php foreach($comentarios as $comentario): ?>

			<?php
			$claseMensaje =
				($comentario['id_usuario'] == $_SESSION['usuario_id'])
				? 'chat-own' : 'chat-other'; ?>

			<div class="chat-message <?php echo $claseMensaje; ?>">

				<div class="chat-meta">
					<strong>
						<?php echo $comentario['nombre']; ?>
					</strong>

					<br>

					<?php echo $comentario['fecha_comentario']; ?>
				</div>

				<?php echo nl2br(htmlspecialchars($comentario['comentario'])); ?>
			</div>

		<?php endforeach; ?>

		<hr>

<!-- FORMULARIO DE COMENTARIOS SI NO ESTÁ LA INCIDENCIA CERRADA -->

		<?php if(!$incidenciaCerrada): ?>

			<form method="POST" class="chat-form">

				<textarea name="comentario"
					placeholder="escribe un comentario..."
					required></textarea>

				<br><br>

				<button type="sumbit" class="btn">Enviar comentario</button>

			</form>

		<?php endif; ?>

<!-- MENSAJE SI LA INCIDENCIA ESTA CERRADA -->


		<?php if($incidenciaCerrada): ?>
			<div class="chat-closed">
				La incidencia está cerrada y ya no admite comentarios.
			</div>
		<?php endif; ?>

		</div>

	</div>

</div>


</body>
</html>
