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

// EL PRINCIPIO
require "inc/phpcode/comun_top.php";


$res = $BD->getTabla ("juegos", "id", $var_id_juego);
if (!isset ($var_id_juego) || count ($res) == 0)
{
	header ("Location: index.php");
	exit ();
}


//VARIABLES VARIAS
if ($res[0]["numero_jugadores"] == 1)
	$variables_sustituir->nuevaVariable ("{TITLE}", "Jugadores inscritos a ". stripslashes($res[0]["nombre"]));
else 
	$variables_sustituir->nuevaVariable ("{TITLE}", "Clanes inscritos a ". stripslashes($res[0]["nombre"]));

//CABECERA
echo $variables_sustituir->parsearTemplate ("inc/template/comun_top.tpl");

$res2 = $BD->getTabla ("equipos", "id_juego", $var_id_juego);

if (count ($res2) != 0)
{	
	//LISTA DE INSCRITOS
	$i=1;
	if ($res[0]["numero_jugadores"] == 1)
	{
		echo $variables_sustituir->parsearTemplate ("inc/template/verclanes2_top.tpl");
		foreach ($res2 as $fila)
		{			
			$cuerpo = file_get_contents  ("inc/template/verclanes2_body.tpl");		
			$variables_sustituir->nuevaVariable ("{VERCLANES_NOMBRE}", stripslashes($fila["nombre_clan"]));
			$variables_sustituir->nuevaVariable ("{VERCLANES_NUM}", $i++);
			echo $variables_sustituir->parsearCadena ($cuerpo);
		}
		echo $variables_sustituir->parsearTemplate ("inc/template/verclanes2_bottom.tpl");
	}else
	{
		echo $variables_sustituir->parsearTemplate ("inc/template/verclanes_top.tpl");
		foreach ($res2 as $fila)
		{	
			$cuerpo = file_get_contents  ("inc/template/verclanes_body.tpl");
				
			$variables_sustituir->nuevaVariable ("{VERCLANES_NOMBRE}", stripslashes($fila["nombre_clan"]));
				
			$tmp=array();
			if ($fila["id_jugadores"] != "")
				foreach (explode(",", $fila["id_jugadores"]) as $id)
				{
					$res3 = $BD->getTabla ("jugadores", "id", $id);		
					$tmp[]=$res3[0]["nick"];		
				}
			else $tmp[]="-";
			$variables_sustituir->nuevaVariable ("{VERCLANES_JUGADORES}", stripslashes(implode (", ", $tmp)));
			
			$tmp=array();
			if ($fila["id_suplentes"] != "")	
				foreach (explode(",", $fila["id_suplentes"]) as $id)
				{
					$res3 = $BD->getTabla ("jugadores", "id", $id);				
					$tmp[]=$res3[0]["nick"];		
				}	
			else $tmp[]="-";
			$variables_sustituir->nuevaVariable ("{VERCLANES_SUPLENTES}", stripslashes(implode (", ", $tmp)));
			$variables_sustituir->nuevaVariable ("{VERCLANES_NUM}", $i++);
			
			echo $variables_sustituir->parsearCadena ($cuerpo);
		}
		echo $variables_sustituir->parsearTemplate ("inc/template/verclanes_bottom.tpl");
	}
}else
{
	echo "Actualmente no hay inscritos";
}

// EL FIN
require "inc/phpcode/comun_bottom.php";	

?>