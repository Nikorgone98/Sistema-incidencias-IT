<?php
require_once '../includes/auth.php';
require_once '../config/database.php';


if ($_SESSION["rol"] != "admin")
{
	die("Acceso denegado");
}

if(!isset($_GET['id']))
{
	die("Incidencia no especificada");
}
$id = (int) $_GET['id'];


/*Carga de la incidencia*/

	$sql = "
		SELECT * FROM incidencias
		WHERE id_incidencia = :id
		";

	$stmt = $conexion->prepare($sql);

	$stmt->execute([
	'id' => $id
	]);

	$incidencia = $stmt->fetch(PDO::FETCH_ASSOC);
	if(!$incidencia)
	{
	die("Incidencia no encontrada");
	}

	if($incidencia['id_estado'] == 4)
	{
	die("La incidencia está cerrada y no puede modificarse.");
	}

/*Aqui se procesa la actualizacion de la incidencia*/

if($_SERVER['REQUEST_METHOD'] === 'POST')
{	$tituloAnterior = $incidencia['titulo'];
	$descripcionAnterior = $incidencia['descripcion'];
	$prioridadAnterior = $incidencia['prioridad'];
	$categoriaAnterior = $incidencia['categoria'];
	$titulo = trim($_POST['titulo']);
	$descripcion = trim($_POST['descripcion']);
	$prioridad = (int)$_POST['prioridad'];
	$categoria =(int)$_POST['categoria'];
	$cambios = [];

/*Esto sirve para detectar modificaciones*/

if($tituloAnterior != $titulo)
{
	$cambios[]=
	"Administrador modificó el título; '" .
	$tituloAnterior .
	"' -> '" . $titulo . "'";
}

if($descripcionAnterior != $descripcion)
{
	$cambios[] =
	"Administrador modificó la descripción de la incidencia";
}

if($prioridadAnterior != $prioridad)
{
	$cambios[] =
	"Administrador modificó la prioridad";
}

if($categoriaAnterior != $categoria)
{
	$cambios[] =
	"Administrador modificó la categoría";
}


	$sqlUpdate = "
	UPDATE incidencias
	SET
		titulo = :titulo,
		descripcion = :descripcion,
		id_prioridad = :prioridad,
		id_categoria = :categoria
	WHERE id_incidencia = :id
	";

	$stmtUpdate = $conexion->prepare($sqlUpdate);

	$stmtUpdate->execute([
		'titulo' => $titulo,
		'descripcion' => $descripcion,
		'prioridad' => $prioridad,
		'categoria' => $categoria,
		'id' => $id
	]);

	foreach($cambios as $mensaje)
	{
		$sqlComentario ="
		INSERT INTO comentarios (comentario, id_usuario, id_incidencia)
		VALUES
		(:comentario, :usuario, :incidencia)";

	$stmtComentario = $conexion->prepare($sqlComentario);

	$stmtComentario->execute([
		'comentario' => $mensaje,
		'usuario' => $_SESSION['usuario_id'],
		'incidencia'=> $id
	]);
	}


	header("Location: ver.php?id=".$id);
	exit();
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
<link rel="stylesheet" href="../assets/css/style.css">
<meta charset="UFT-8">
<title>Editar Incidencia</title>
</head>

<body>

<div class="container">

<div class="form-card">

<h1>Editar Incidencia</h1>


<form method="POST">
	<div class="form-group">
	<label>Título</label>
	<input type="text" name="titulo"
	value="<?php echo htmlspecialchars($incidencia['titulo']); ?>"  required>
	</div>

	<div class="form-group">
	<label>Descripción</label>
	<textarea
	name="descripcion" rows="8" cols="50"
	required><?php echo htmlspecialchars($incidencia['descripcion']); ?></textarea>
	</div>


	<div class="form-group">
	<label>Categoria</label>
	<select name="categoria">

		<option value="1"
		<?php if($incidencia['id_categoria']==1) echo "selected"; ?>>Hardware</option>
		<option value="2"
		<?php if($incidencia['id_categoria']==2) echo "selected"; ?>>Software</option>
		<option value="3"
		<?php if($incidencia['id_categoria']==3) echo "selected"; ?>>Red</option>
		<option value="4"
		<?php if($incidencia['id_categoria']==4) echo "selected"; ?>>Cuenta usuario</option>
		<option value="5"
		<?php if($incidencia['id_categoria']==5) echo "selected"; ?>>Impresoras</option>
	</select>
	</div>


	<div class="form-group">
	<label>Prioridad</label>
	<select name="prioridad">

		<option value="1"
		<?php if($incidencia['id_prioridad']==1) echo "selected"; ?>>Baja</option>
		<option value="2"
		<?php if($incidencia['id_prioridad']==2) echo "selected"; ?>>Media</option>
		<option value="3"
		<?php if($incidencia['id_prioridad']==3) echo "selected"; ?>>Alta</option>
		<option value="4"
		<?php if($incidencia['id_prioridad']==4) echo "selected"; ?>>Critica</option>
	</select>
	</div>


	<button type="sumbit">Guardar cambios</button>

</form>

<br>

	<a href="ver.php?id=<?php echo $id; ?>">Volver</a>

	</div>
</div>

</body>
</html>
