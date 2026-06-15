<?php
/* Verifica la sesión activa y carga la conexión a la base de datos */
require_once '../includes/auth.php';
require_once '../config/database.php';

$mensaje = "";
/* Procesa el formulario de creación de incidencia */
if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	$titulo = trim($_POST['titulo']);
	$descripcion = trim($_POST['descripcion']);
	$categoria = $_POST['categoria'];
	$prioridad = $_POST['prioridad'];
/* Inserta la nueva incidencia asociada al usuario autenticado */
	$sql = "
		INSERT INTO incidencias
		(
			titulo,
			descripcion,
			id_usuario,
			id_estado,
			id_prioridad,
			id_categoria
		)
		VALUES

		(
			:titulo,
			:descripcion,
			:usuario,
			1,
			:prioridad,
			:categoria
		)
		";
	$stmt = $conexion->prepare($sql);
	$stmt->execute([
		'titulo'=> $titulo,
		'descripcion'=> $descripcion,
		'usuario'=> $_SESSION['usuario_id'],
		'prioridad'=> $prioridad,
		'categoria'=> $categoria
	]);

	$mensaje ="Incidencia creada correctamente.";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<link rel="stylesheet" href="../assets/css/style.css">
<meta charset="UFT-8">
<title>Nueva Incidencia</title>
</head>

<body>
<!-- Contenedor principal -->
<div class="container">

<div class="form-card">

<h1>Nueva Incidencia</h1>
<!-- Mensaje de confirmación -->
<?php if ($mensaje): ?>

<div class="success-message">
<?php echo $mensaje; ?>
</div>

<?php endif; ?>


<!-- Formulario de creación de incidencia -->
<form method="POST">

	<div class="form-group">
	<label>Título</label>
	<input type="text" name="titulo" required>
	</div>

	<div class="form-group">
	<label>Descripción</label>
	<textarea name="descripcion" rows="8" cols="50" required>
	</textarea>
	</div>

	<div class="form-group">
	<label>Categoria</label>
	<select name="categoria">
		<option value="1">Hardware</option>
		<option value="2">Software</option>
		<option value="3">Red</option>
		<option value="4">Cuenta usuario</option>
		<option value="5">Impresoras</option>
	</select>
	</div>

	<div class="form-group">
	<label>Prioridad</label>
	<select name="prioridad">

		<option value="1">Baja</option>
		<option value="2">Media</option>
		<option value="3">Alta</option>
		<option value="4">Critica</option>
	</select>
	</div>


	<button type="sumbit">Crear Incidencia</button>

	</form>

	<br>
<!-- Enlace de retorno al dashboard -->
	<a href="../dashboard.php">Volver al panel</a>

	</div>
</div>


</body>
</html>
