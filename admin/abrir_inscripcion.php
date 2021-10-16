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
	$variables_sustituir->nuevaVariable ("{TITLE}", "Abrir inscripcion");
	
	
	if (isset ($var_id_juego))
	{
		$res = $BD->getTabla ("juegos", "id", $var_id_juego);				
						
		if (count($res) == 1)
		{									
			$tmp["inscripcion_abierta"] = 1;
			$BD->editTabla ("juegos", "id", $var_id_juego, $tmp);
			
			echo $variables_sustituir->parsearTemplate ("../inc/template/comun_top.tpl");	
			echo "Abierta de nuevo la inscripción para " . $res[0]["nombre"];
			echo $variables_sustituir->parsearTemplate ("../inc/template/comun_bottom.tpl");				
			header( 'refresh: 3; url=index.php'); 
						
		}	
	}
			

//PIE COMUN
	require "../inc/phpcode/admin_bottom.php";

?>