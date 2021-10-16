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

function dibujar_clasificacion ($tabla, $variables_sustituir, $BD)
{	
	//ORDENO LA TABLA
	foreach ($tabla as $key=>$valor)		
		$tabla_ordenada[$key]=$valor["puntos"];		
	arsort ($tabla_ordenada);
	
	// DIBUJO LA CLASIFICACION
	echo $variables_sustituir->parsearTemplate ("inc/template/ver_clasificacion_top.tpl");
	$cuerpo = file_get_contents  ("inc/template/ver_clasificacion_body.tpl");
	$i=1;
	foreach ($tabla_ordenada as $key=>$valor)
	{
		$res2 = $BD->getTabla ("equipos", "id", $key);
		
		$variables_sustituir->nuevaVariable ("{VER_T_NUM}", $i++ . ".");
		$variables_sustituir->nuevaVariable ("{VER_T_NOMBRE}", $res2[0]["nombre_clan"]);
		$variables_sustituir->nuevaVariable ("{VER_T_JUGADOS}", (int)$res2[0]["nombre_clan"]);
		$variables_sustituir->nuevaVariable ("{VER_T_VICTORIAS}", (int)$tabla[$key][1]);
		$variables_sustituir->nuevaVariable ("{VER_T_DERROTAS}", (int)$tabla[$key][-1]);
		$variables_sustituir->nuevaVariable ("{VER_T_EMPATES}", (int)$tabla[$key][0]);
		$variables_sustituir->nuevaVariable ("{VER_T_JUGADOS}", (int)$tabla[$key][1]+$tabla[$key][0]+$tabla[$key][-1]);
		echo $variables_sustituir->parsearCadena ($cuerpo);
	}						
	echo $variables_sustituir->parsearTemplate ("inc/template/ver_clasificacion_bottom.tpl");
} 

function dibujar_partidos ($partidos, $variables_sustituir, $BD)
{
	$variables_sustituir->nuevaVariable ("{RONDA}", "");
	//DIBUJO LOS DIFERENTES PARTIDOS
	for ($i=1; $i < count ($partidos); $i++)
	{
		$res = $BD->getTabla ("encuentros", "id", $partidos[$i]);
		
		echo $variables_sustituir->parsearTemplate ("inc/template/ver_partido_top.tpl");	
		$cuerpo = file_get_contents  ("inc/template/ver_partido_body.tpl");	
		
		$res2 = $BD->getTabla ("equipos", "id", $res[0]["id_1"]);			
		$variables_sustituir->nuevaVariable ("{VER_P_E1}", $res2[0]["nombre_clan"]);
		$res2 = $BD->getTabla ("equipos", "id", $res[0]["id_2"]);			
		$variables_sustituir->nuevaVariable ("{VER_P_E2}", $res2[0]["nombre_clan"]);
		$variables_sustituir->nuevaVariable ("{VER_P_RESULTADO}", $res[0]["resultado"]);
		
		if ($res[0]["ganador"] == 1)
			$variables_sustituir->nuevaVariable ("{VER_P_GANADOR}", "Ganador: ". $variables_sustituir->obtenerVariable ("{VER_P_E1}"));
		else if ($res[0]["ganador"] == 2)
			$variables_sustituir->nuevaVariable ("{VER_P_GANADOR}", "Ganador: ". $variables_sustituir->obtenerVariable ("{VER_P_E2}"));
		else if ($res[0]["ganador"] == 0)
			$variables_sustituir->nuevaVariable ("{VER_P_GANADOR}", "Empate");
		else
			$variables_sustituir->nuevaVariable ("{VER_P_GANADOR}", "Partido aun no disputado");
		
		echo $variables_sustituir->parsearCadena ($cuerpo);
								
		echo $variables_sustituir->parsearTemplate ("inc/template/ver_partido_bottom.tpl");
	}	
}

function dibujar_partidos_eliminatoria ($partidos, $variables_sustituir, $BD)
{
	//DIBUJO LOS DIFERENTES PARTIDOS
	for ($i=1; $i < count ($partidos); $i++)
	{
		$res = $BD->getTabla ("encuentros", "id", $partidos[$i]);
		
		if ($res[0]["ganador"] != -1 && $res[0]["id_2"] != -1)
		{					
			$variables_sustituir->nuevaVariable ("{RONDA}", nombre_ronda (intval(log($i,2))));
			echo $variables_sustituir->parsearTemplate ("inc/template/ver_partido_top.tpl");	
			$cuerpo = file_get_contents  ("inc/template/ver_partido_body.tpl");	
			
			$res2 = $BD->getTabla ("equipos", "id", $res[0]["id_1"]);			
			$variables_sustituir->nuevaVariable ("{VER_P_E1}", $res2[0]["nombre_clan"]);
			$res2 = $BD->getTabla ("equipos", "id", $res[0]["id_2"]);			
			$variables_sustituir->nuevaVariable ("{VER_P_E2}", $res2[0]["nombre_clan"]);
			$variables_sustituir->nuevaVariable ("{VER_P_RESULTADO}", $res[0]["resultado"]);
			
			if ($res[0]["ganador"] == 1)
				$variables_sustituir->nuevaVariable ("{VER_P_GANADOR}", "Ganador: ". $variables_sustituir->obtenerVariable ("{VER_P_E1}"));
			else if ($res[0]["ganador"] == 2)
				$variables_sustituir->nuevaVariable ("{VER_P_GANADOR}", "Ganador: ". $variables_sustituir->obtenerVariable ("{VER_P_E2}"));		
			
			echo $variables_sustituir->parsearCadena ($cuerpo);
									
			echo $variables_sustituir->parsearTemplate ("inc/template/ver_partido_bottom.tpl");
		}
	}	
}


// EL PRINCIPIO
require "inc/phpcode/comun_top.php";



// SI NO PIDO INFO DE UN TORNEO MUESTRO LA LISTA DE TORNEOS EN CURSO
if (isset ($var_id_juego))
{
	$res = $BD->getTabla ("juegos", "id", $var_id_juego);
	
	//VARIABLES VARIAS
	$variables_sustituir->nuevaVariable ("{TITLE}", "Cuadro de <i>". stripslashes($res[0]["nombre"]) . "</i>");

	//CABECERA
	echo $variables_sustituir->parsearTemplate ("inc/template/comun_top.tpl");
	
	//$variables_sustituir->nuevaVariable ("{NOMBRE_JUEGO}", $res[0]["nombre"]);
	
	// COMPROBACIONES	
	if (count ($res) == 0 || $res[0]["inscripcion_abierta"]=="1")
	{
		header ("Location: index.php");
		exit ();
	}
		
	// SI ES UN TORNEO ELIMINATORIO
	if ($res[0]["tipo_torneo"] == 1)
	{	
		$cuerpo = file_get_contents  ("inc/template/ver_1.tpl");
		$variables_sustituir->nuevaVariable ("{VER_IMGTORNEO}", "ver_cuadro.php?id_juego=". $var_id_juego);
		
		echo $variables_sustituir->parsearCadena ($cuerpo);
		
		$partidos = unserialize ($res[0]["cuadro"]);
		dibujar_partidos_eliminatoria ($partidos, $variables_sustituir, $BD);		
		
	// SI ES UNA LIGA
	}else if ($res[0]["tipo_torneo"] == 2)
	{				
		$partidos = unserialize ($res[0]["cuadro"]);
		
		// CALCULO LA CLASIFICACION
		$tabla= calcular_clasificacion ($partidos, $BD);
		
		dibujar_clasificacion ($tabla, $variables_sustituir, $BD);
		
		dibujar_partidos ($partidos, $variables_sustituir, $BD);
		
	//LIGA + ELIMINATORIA
	}else if ($res[0]["tipo_torneo"] == 3)
	{
		$cuadro = unserialize ($res[0]["cuadro"]);
			
		$cuerpo = file_get_contents  ("inc/template/ver_1.tpl");
		$variables_sustituir->nuevaVariable ("{VER_IMGTORNEO}", "ver_cuadro.php?id_juego=". $var_id_juego);		
		$cad_eliminatorias = $variables_sustituir->parsearCadena ($cuerpo);
			
		// SI YA SE HA ACABADO LA LIGUILLA PREVIA PONGO PRIMERO EL CUADRO DE LAS ELIMINATORIAS		
		if ($cuadro["fase"] == 1)
		{
			echo $cad_eliminatorias;
						
			$partidos = $cuadro["eliminatorias"];			
			dibujar_partidos_eliminatoria ($partidos, $variables_sustituir, $BD);
		}
					
		for ($i=0; $i<count ($cuadro["ligas"]); $i++)
		{
			$letras = "ABCDEFGH";
			$variables_sustituir->nuevaVariable ("{LETRA_GRUPO}", $letras{$i});
			//COMIENZA GRUPO
			echo $variables_sustituir->parsearTemplate ("inc/template/ver_liguilla_top.tpl");
			
			// CALCULO LA CLASIFICACION
			$tabla= calcular_clasificacion ($cuadro["ligas"][$i], $BD);	
			// DIBUJO LA CLASIFICACION
			dibujar_clasificacion ($tabla, $variables_sustituir, $BD);	
			// DIBUJO LOS PARTIDOS
			dibujar_partidos ($cuadro["ligas"][$i], $variables_sustituir, $BD);	
			
			//TERMINA GRUPO
			echo $variables_sustituir->parsearTemplate ("inc/template/ver_liguilla_bottom.tpl");
		}
		
		// SI ESTAMOS EN LA FASE DE LIGUILLA PONGO EL CUADRO DETRAS
		if ($cuadro["fase"] == 0)
			echo $cad_eliminatorias;
			
	// BRACKETS WINERS /LOSERS
	}if ($res[0]["tipo_torneo"] == 4)
	{	
		$cuerpo = file_get_contents  ("inc/template/ver_1.tpl");
		$variables_sustituir->nuevaVariable ("{VER_IMGTORNEO}", "ver_cuadro.php?id_juego=". $var_id_juego);
		
		echo $variables_sustituir->parsearCadena ($cuerpo);
		
		$partidos = unserialize ($res[0]["cuadro"]);
		dibujar_partidos_eliminatoria ($partidos["winners"], $variables_sustituir, $BD);		
		
		$res = $BD->getTabla ("encuentros", "id", $partidos["final"]);
		$variables_sustituir->nuevaVariable ("{RONDA}", "LA GRAN FINAL (campeones winners/losers)");
		echo $variables_sustituir->parsearTemplate ("inc/template/ver_partido_top.tpl");	
		$cuerpo = file_get_contents  ("inc/template/ver_partido_body.tpl");	
		
		$res2 = $BD->getTabla ("equipos", "id", $res[0]["id_1"]);			
		$variables_sustituir->nuevaVariable ("{VER_P_E1}", $res2[0]["nombre_clan"]);
		$res2 = $BD->getTabla ("equipos", "id", $res[0]["id_2"]);			
		$variables_sustituir->nuevaVariable ("{VER_P_E2}", $res2[0]["nombre_clan"]);
		$variables_sustituir->nuevaVariable ("{VER_P_RESULTADO}", $res[0]["resultado"]);
		
		if ($res[0]["ganador"] == 1)
			$variables_sustituir->nuevaVariable ("{VER_P_GANADOR}", "Ganador: ". $variables_sustituir->obtenerVariable ("{VER_P_E1}"));
		else if ($res[0]["ganador"] == 2)
			$variables_sustituir->nuevaVariable ("{VER_P_GANADOR}", "Ganador: ". $variables_sustituir->obtenerVariable ("{VER_P_E2}"));		
		
		echo $variables_sustituir->parsearCadena ($cuerpo);
								
		echo $variables_sustituir->parsearTemplate ("inc/template/ver_partido_bottom.tpl");
	}
	
}

// EL FIN
require "inc/phpcode/comun_bottom.php";	

?>