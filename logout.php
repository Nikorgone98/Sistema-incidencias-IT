<?php
/* Inicia la sesión actual */
session_start();
/* Destruye la sesión y cerrar la autenticación del usuario */
session_destroy();
/* Redirige al usuario a la página de login */
header(
	"Location: login.php"
	);
exit();

?>
