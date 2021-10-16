<?php
//FECHA MODIFICACION:		2006-07-05
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

//SEGURIDAD
require "../inc/phpcode/seguridad.php";

//CABECERA COMUN
require "../inc/phpcode/admin_top.php";

//VARIABLES VARIAS
	$variables_sustituir->nuevaVariable ("{TITLE}", "Nuevo torneo");		

	$variables_sustituir->nuevaVariable ("{NUEVOTORNEO_NOMBRE}", "");
	$variables_sustituir->nuevaVariable ("{NUEVOTORNEO_NJUG}", "1");
	$variables_sustituir->nuevaVariable ("{NUEVOTORNEO_NSUP}", "0");	
	
	// ESTOY ENVIANDO UN FORMULARIO
	if ($var_modo == "submit")
	{				
		$datos = $_POST["torneo"];
		
		$res = $BD->getTabla ("juegos", "nombre", $datos["nombre"]);
		
		if (trim($datos["nombre"]) == "" || count ($res)>0)
		{
			$variables_sustituir->nuevaVariable ("{ERROR}", "Nombre del torneo incorrecto o ya existe");
		}else if (trim($datos["numero_jugadores"]) == "" || !settype($datos["numero_jugadores"],"integer") || $datos["numero_jugadores"]<1)
		{
			$variables_sustituir->nuevaVariable ("{ERROR}", "Número de jugadores incorrecto");
		}else if (trim($datos["numero_suplentes"]) == "" || !settype($datos["numero_suplentes"],"integer") || $datos["numero_suplentes"]<0)
		{
			$variables_sustituir->nuevaVariable ("{ERROR}", "Número de suplentes incorrecto");
		}else
		{	// TODOS LOS DATOS CORRECTOS
						
			$BD->putTabla ("juegos", $datos);
			header ("Location: index.php");
			exit ();
		}
		
		$variables_sustituir->nuevaVariable ("{NUEVOTORNEO_NOMBRE}", $datos["nombre"]);
		$variables_sustituir->nuevaVariable ("{NUEVOTORNEO_NJUG}", $datos["numero_jugadores"]);
		$variables_sustituir->nuevaVariable ("{NUEVOTORNEO_NSUP}", $datos["numero_suplentes"]);			
	}

		
	echo $variables_sustituir->parsearTemplate ("../inc/template/comun_top.tpl");	

	echo $variables_sustituir->parsearTemplate ("../inc/template/admin_nuevotorneo.tpl");	


	echo $variables_sustituir->parsearTemplate ("../inc/template/comun_bottom.tpl");	

//PIE COMUN
	require "../inc/phpcode/admin_bottom.php";

?>