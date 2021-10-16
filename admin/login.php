<?php
//FECHA MODIFICACION:		2006-07-03
/***************************************************************************
 *   copyright            : (C) 2006 The afoto project
 *   email                : shaorang@users.sourceforge.net
 ***************************************************************************/

/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 *   This program is distributed in the hope that it will be useful, but WITHOUT
 *   ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS 
 *   FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *   
 *   You should have received a copy of the GNU General Public License along with 
 *   this program; if not, write to the Free Software Foundation, Inc., 675 Mass Ave,
 *   Cambridge, MA 02139, USA.  
 ***************************************************************************/

//CABECERA COMUN
require "../inc/phpcode/admin_top.php";

//VARIABLES VARIAS
$variables_sustituir->nuevaVariable ("{TITLE}", "Login");

//CABECERA
echo $variables_sustituir->parsearTemplate ("../inc/template/comun_top.tpl");

if ($var_modo == "submit")
{
	require_once ("config_admin.php");
	
	if (!isset($_POST["claveadministrador"]) || md5($_POST["claveadministrador"]) != $CLAVE_ADMINISTRADOR)
	{					
		echo "Espere unos segundos...";	
		header( 'refresh: 5; url=index.php'); 
		exit ();
	}else
	{
		session_start();	
		$_SESSION["claveadministrador"]=$_POST["claveadministrador"];
		echo "Espere unos segundos...";			
		header('refresh: 5; url=index.php'); 
		exit ();
	}
}else if ($var_modo == "logout")
{
	session_start();	
	session_unset();
	session_destroy();
	header("Location: index.php");
	exit ();
}else if ($var_modo == "cambiar")
{
	//SEGURIDAD
	require "../inc/phpcode/seguridad.php";
	
	if (isset($_POST["claveadministrador"]) && isset($_POST["claveadministrador2"]) && $_POST["claveadministrador"]==$_POST["claveadministrador2"])
	{		
		if (!is_writeable ("config_admin.php"))
			die ("ERROR: EL ARCHIVO <i>config_admin.php</i> NO TIENE PERMISOS DE ESCRITURA");
		
		$cad = '<?php $CLAVE_ADMINISTRADOR = "'. md5($_POST["claveadministrador"]) . '"; ?>';
		
		$fp = fopen("config_admin.php", "w");
		fwrite ($fp, $cad);
		fclose ($fp);
		
		session_start();	
		session_unset();
		session_destroy();
		header("Location: index.php");
		exit ();
	}
	echo $variables_sustituir->parsearTemplate ("../inc/template/admin_login_cambiar.tpl");
}else
	echo $variables_sustituir->parsearTemplate ("../inc/template/admin_login.tpl");

//PIE
echo $variables_sustituir->parsearTemplate ("../inc/template/comun_bottom.tpl");

//PIE COMUN
require "../inc/phpcode/admin_bottom.php";

?>