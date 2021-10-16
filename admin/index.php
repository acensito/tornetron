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

//SEGURIDAD
require "../inc/phpcode/seguridad.php";

//CABECERA COMUN
require "../inc/phpcode/admin_top.php";

//VARIABLES VARIAS
$variables_sustituir->nuevaVariable ("{TITLE}", "Indice");

//CABECERA
echo $variables_sustituir->parsearTemplate ("../inc/template/comun_top.tpl");

echo $variables_sustituir->parsearTemplate ("../inc/template/admin_barra.tpl");

$cuerpo = file_get_contents  ("../inc/template/admin_index_elemento.tpl");
$cabecera = file_get_contents  ("../inc/template/admin_index_cabecera.tpl");

// MUESTRO LA LISTA DE TORNEOS EN CURSO
$variables_sustituir->nuevaVariable ("{INDEX_CABECERA}", "Torneos en curso (inscripción cerrada)");
echo $variables_sustituir->parsearCadena ($cabecera);
$res = $BD->getTabla ("juegos", "inscripcion_abierta", "0");
foreach ($res as $fila)
{
	$tmp = " (";
	if ($fila["tipo_torneo"]==1)
		$tmp .= "eliminatoria directa ";
	else if ($fila["tipo_torneo"]==2)
		$tmp .= "liga ";
	else if ($fila["tipo_torneo"]==3)
		$tmp .= "liguilla+eliminatoria ";	
	else if ($fila["tipo_torneo"]==4)
		$tmp .= "brackets winners/losers ";	
	if ($fila["numero_jugadores"]==1)
		$tmp .= "individual";
	else
		$tmp .= "por clanes";					
	$tmp .=")";
	
	$variables_sustituir->nuevaVariable ("{INDEX_NOMBRE}", $fila["nombre"] . $tmp);
	$variables_sustituir->nuevaVariable ("{INDEX_ID}", $fila["id"]);
	
	$variables_sustituir->nuevaVariable ("{INDEX_LINK1}", "actualizar.php?id_juego=" . $fila["id"]);		
	$variables_sustituir->nuevaVariable ("{INDEX_LINK2}", "abrir_inscripcion.php?id_juego=" . $fila["id"]);		
	$variables_sustituir->nuevaVariable ("{INDEX_LINK3}", "actualizar.php?editar&id_juego=" . $fila["id"]);		
	$variables_sustituir->nuevaVariable ("{INDEX_LINK1_TEXT}", "Actualizar los resultados de los encuentros");
	$variables_sustituir->nuevaVariable ("{INDEX_LINK2_TEXT}", "Abrir de nuevo el periodo de inscripción (eliminando la competicion actual pero conservando los equipos ya inscritos)");	
	$variables_sustituir->nuevaVariable ("{INDEX_LINK3_TEXT}", "Editar resultados de encuentros");	
		
	echo $variables_sustituir->parsearCadena ($cuerpo);
}
if (count ($res) == 0)
	echo "No hay ningun torneo en curso ahora mismo";
	
// MUESTRO LA LISTA DE TORNEOS ABIERTOS
$variables_sustituir->nuevaVariable ("{INDEX_CABECERA}", "Torneos con inscripcion abierta");
echo $variables_sustituir->parsearCadena ($cabecera);
$res = $BD->getTabla ("juegos", "inscripcion_abierta", "1");
foreach ($res as $fila)
{
	if ($fila["numero_jugadores"]==1)
		$tmp = " (torneo individual)";
	else
		$tmp = " (torneo por clanes)";
	$variables_sustituir->nuevaVariable ("{INDEX_NOMBRE}", $fila["nombre"] . $tmp);
	$variables_sustituir->nuevaVariable ("{INDEX_ID}", $fila["id"]);
	
	$variables_sustituir->nuevaVariable ("{INDEX_LINK1}", "emparejar.php?id_juego=" . $fila["id"]);
	$variables_sustituir->nuevaVariable ("{INDEX_LINK2}", "../registrarse.php?id_juego=" . $fila["id"]);	
	$variables_sustituir->nuevaVariable ("{INDEX_LINK3}", "");	
	$variables_sustituir->nuevaVariable ("{INDEX_LINK1_TEXT}", "Cerrar la inscripción y comenzar la competición");
	$variables_sustituir->nuevaVariable ("{INDEX_LINK2_TEXT}", "Inscribirse");
	$variables_sustituir->nuevaVariable ("{INDEX_LINK3_TEXT}", "");	
	
	echo $variables_sustituir->parsearCadena ($cuerpo);
}
if (count ($res) == 0)
	echo "No hay ningun torneo abierto ahora mismo";


// MUESTRO LA LISTA DE TORNEOS FINALIZADOS
$variables_sustituir->nuevaVariable ("{INDEX_CABECERA}", "Torneos finalizados");
echo $variables_sustituir->parsearCadena ($cabecera);
$res = $BD->getTabla ("juegos", "inscripcion_abierta", "-1");
foreach ($res as $fila)
{
	$tmp = " (";
	if ($fila["tipo_torneo"]==1)
		$tmp .= "eliminatoria directa ";
	else if ($fila["tipo_torneo"]==2)
		$tmp .= "liga ";
	else if ($fila["tipo_torneo"]==3)
		$tmp .= "liguilla+eliminatoria ";	
	else if ($fila["tipo_torneo"]==4)
		$tmp .= "brackets winners/losers ";	
	if ($fila["numero_jugadores"]==1)
		$tmp .= "individual";
	else
		$tmp .= "por clanes";					
	$tmp .=")";
	$variables_sustituir->nuevaVariable ("{INDEX_NOMBRE}", $fila["nombre"] . $tmp);
	$variables_sustituir->nuevaVariable ("{INDEX_ID}", $fila["id"]);
	$variables_sustituir->nuevaVariable ("{INDEX_LINK1}", "../ver.php?id_juego=" . $fila["id"]);
	$variables_sustituir->nuevaVariable ("{INDEX_LINK2}", "abrir_inscripcion.php?id_juego=" . $fila["id"]);		
	$variables_sustituir->nuevaVariable ("{INDEX_LINK3}", "actualizar.php?editar&id_juego=" . $fila["id"]);		
	$variables_sustituir->nuevaVariable ("{INDEX_LINK1_TEXT}", "Cuadro del torneo");	
	$variables_sustituir->nuevaVariable ("{INDEX_LINK2_TEXT}", "Abrir de nuevo el periodo de inscripción (eliminando la competicion actual pero conservando los equipos ya inscritos)");	
	$variables_sustituir->nuevaVariable ("{INDEX_LINK3_TEXT}", "Editar resultados de encuentros");	
	
	echo $variables_sustituir->parsearCadena ($cuerpo);
}
if (count ($res) == 0)
	echo "No hay ningun torneo finalizado";

//PIE COMUN
require "../inc/phpcode/admin_bottom.php";

?>