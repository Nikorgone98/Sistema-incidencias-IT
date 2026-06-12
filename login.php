<?php

require_once 'config/database.php';
require_once 'includes/session.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	$email = $_POST['email'];
	$password = $_POST['password'];

	$sql = "
		SELECT *
		FROM usuarios
		WHERE email = :email
	";

	$stmt = $conexion->prepare($sql);

	$stmt->execute([ 'email' => $email]);

	$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

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

	    header(
		"Location: dashboard.php"
	    );
	    exit();
	}
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

<div class="login-wrapper">

<div class="login-card">

<h1>TuringHelpDesk</h1>

<?php if($error): ?>

<div class="error-message">
<?php echo htmlspecialchars($error); ?>
</div>

<?php endif; ?>

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
