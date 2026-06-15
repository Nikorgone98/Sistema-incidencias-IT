<?php
/* Iniciar la sesión únicamente si aún no existe */
if (session_status() === PHP_SESSION_NONE)
{
	session_start();
}

?>
