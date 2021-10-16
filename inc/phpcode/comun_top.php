<?php
//FECHA MODIFICACION:		2006-07-04
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

//	PARA MEDIR EL TIEMPO
	$mtime = microtime();
	$mtime = explode(" ",$mtime);
	$mtime = $mtime[1] + $mtime[0];
	$starttime = $mtime;
//	PARA MEDIR EL TIEMPO (FIN)

	ob_start ();

	require "inc/phpcode/parser.php";	
	require "config.php";	
	require "inc/phpcode/bd.php";
	require "inc/phpcode/funciones.php";
	
	//COMPATIBLE CON register_globals=off
	import_request_variables("g", "var_"); 
				
// COMENZAMOS CON EL TEMPLATE
	$variables_sustituir = new parseador;
	
// ACCEDEMOS A LA BD
	$BD = new bd ($CONFIG["dbhost"], $CONFIG["dbname"], $CONFIG["dbuser"], $CONFIG["dbpasswd"]);	

//VARIABLES VARIAS
	$variables_sustituir->nuevaVariable ("{ERROR}", "");	
	$variables_sustituir->nuevaVariable ("{VERSION}", $CONFIG["version"]);
	$variables_sustituir->nuevaVariable ("{BASE_DIR}", "");
	
?>