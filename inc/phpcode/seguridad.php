<?php
	require_once ("config_admin.php");
	session_start();
	
	if ($CLAVE_ADMINISTRADOR != "")
	{
		if (!isset($_SESSION["claveadministrador"]) || md5($_SESSION["claveadministrador"]) != $CLAVE_ADMINISTRADOR)
		{		
			header ("Location: login.php");		
			exit ();
		}
	}
?>