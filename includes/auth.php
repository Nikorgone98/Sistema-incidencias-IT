<?php
/* Inicia o recupera la sesión del usuario */
require_once 'session.php';
/* Comprueba que existe una sesión válida */
if (!isset($_SESSION['usuario_id']))
{
	/* Redirige al login si el usuario no está autenticado */
	header("Location: /turinghelpdesk/login.php");
	exit();
}

?>
