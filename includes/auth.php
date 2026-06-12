<?php

require_once 'session.php';

if (!isset($_SESSION['usuario_id']))
{
	header("Location: /turinghelpdesk/login.php");
	exit();
}

?>
