<?php 
/* 	Fichero con funciones para parsear los templates y sustituir variables por codigo
	generado en tiempo real.

	Las variables seguiran la siguiente estructura: {NOMBRE_VAR}
	y SERAN SENSIBLES A MAYUSCULAS Y MINUSCULAS
	
	ULTIMO CAMBIO: 2006-05-01
*/

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
	
	class parseador 
	{
		var $variables; 	//LAS VARIABLES CON EL CODIGO A SUSTITUIR EN UN ARRAY CON LOS KEYS==NOMBRE_VAR
		
		function parseador ()
		{
			$this->variables = array ();
		}
		
		function nuevaVariable ($nombre, $cadena) //nombre de la variable y pq se sustituye
		{			
			$this->variables[$nombre]=$cadena;		
		}
		
		function obtenerVariable ($nombre)
		{
			if (isset($this->variables[$nombre]))
				return $this->variables[$nombre];
			else return $nombre;
		}
		
		//	LEE EL FICHERO TEMPLATE, LO PARSEA Y DEVUELVE UNA CADENA CON EL CONTENIDO
		function parsearTemplate ($template)
		{			
			$cad = @file_get_contents ($template);
			if (!is_bool($cad))
			{				
				foreach ($this->variables as $nombre_var => $valor)
					$cad= str_replace ($nombre_var, $valor, $cad);
			}
			return $cad;
		}
		
		//	PARSEA LA CADENA template Y DEVUELVE UNA CADENA CON EL CONTENIDO
		function parsearCadena ($template)
		{
			$cad = $template;
			if (!is_bool($cad))
			{				
				foreach ($this->variables as $nombre_var => $valor)
					$cad= str_replace ($nombre_var, $valor, $cad);
			}
			return $cad;
		}
		
		//	INTRODUCE LAS VARIABLES DESDE UN ARRAY ASOCIATIVO key=NOMBREVAR
		function obtenerVariablesArray ($array)
		{
			foreach ($array as $key => $valor)
			{
				$this->nuevaVariable ($key, $valor);
			}
		}
	}	

?>