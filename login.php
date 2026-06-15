<?php
/* Carga la conexión a base de datos y gestión de sesiones */
require_once 'config/database.php';
require_once 'includes/session.php';

$error = "";
/* Procesa el formulario de inicio de sesión */
if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	$email = $_POST['email'];
	$password = $_POST['password'];
/* Busca el usuario por correo electrónico */
	$sql = "
		SELECT *
		FROM usuarios
		WHERE email = :email
	";

	$stmt = $conexion->prepare($sql);

	$stmt->execute([ 'email' => $email]);

	$usuario = $stmt->fetch(PDO::FETCH_ASSOC);
/* Valida las credenciales y crea la sesión */
	if (
	    $usuario &&
	    password_verify(
		$password,
		$usuario['password']
	    )
	)
	{
	    $_SESSION['usuario_id']
		= $usuario['id_usuario'];

	    $_SESSION['nombre']
		= $usuario['nombre'];

	    $_SESSION['rol']
		= $usuario['rol'];
/* Redirige al panel principal */
	    header(
		"Location: dashboard.php"
	    );
	    exit();
	}/* Muestra error si las credenciales no son válidas */
	else
	{
	    $error =
		"Credenciales incorrectas";
	}

}

?>

<!DOCTYPE html>
<html lang="es">
<head>
<link rel="stylesheet" href="assets/css/style.css">
<meta charset= "UTF-8">
<title>Login</title>
</head>

<body>
<!-- Contenedor principal del formulario -->
<div class="login-wrapper">

<div class="login-card">

<h1>TuringHelpDesk</h1>
<!-- Mensaje de error de autenticación -->
<?php if($error): ?>

<div class="error-message">
<?php echo htmlspecialchars($error); ?>
</div>

<?php endif; ?>
<!-- Formulario de inicio de sesión -->
<form method="POST">

	<div class="form-group">
	<label>Email</label>
	<input type= "email" name="email" placeholder= "Email" required>
	</div>


	<div class="form-group">
	<label>Contraseña</label>
	<input type="password" name="password" placeholder= "Contraseña" required>
	</div>

	<button type="sumbit" class="btn">
	Entrar
	</button>
</form>
</div>

</div>

</body>
</html>
