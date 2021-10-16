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

/* 		TIPOS DE TORNEO 	*/
/* 	1 -> Eliminatorio		*/
/* 	2 -> Liga			*/
/* 	3 -> Liguilla + Eliminatorias	*/
/* 	4 -> Tipo CS (winners+losers )		*/

//SEGURIDAD
require "../inc/phpcode/seguridad.php";

	//CABECERA COMUN
	require "../inc/phpcode/admin_top.php";
	
	// COMPRUEBO LOS DATOS Y LOS METO EN LA BD 
	if ($var_modo=="submit")
	{
		if (isset ($_POST["id_juego"]) && isset ($_POST["tipo_torneo"]) && isset ($_POST["ngrupos"]) && isset ($_POST["nclasif"]))
		{
			// CASO ESPECIAL 
			if ($_POST["tipo_torneo"] == 3 && $_POST["ngrupos"] == 1 && $_POST["nclasif"] == 1)
				$_POST["tipo_torneo"] = 2;
			
			// METO EL TIPO DE TORNEO EN LA BASE DE DATOS
			$tmp=array("tipo_torneo" => $_POST["tipo_torneo"]);
			$BD->editTabla ("juegos", "id", $_POST["id_juego"], $tmp);			
			$var_id_juego= $_POST["id_juego"];
			
			// BORRO SI YA SE HABIAN HECHO EMPAREJAMIENTOS ANTES
			$BD->delTabla ("encuentros", "id_juego", $var_id_juego);
		}
	}

	$res = $BD->getTabla ("juegos", "id", $var_id_juego);
	if (isset ($var_id_juego) && count ($res) == 1)
	{				
		$res2 = $BD->getTabla ("equipos", "id_juego", $var_id_juego);
		
		// ESTOY ENVIANDO EL FORMULARIO
		if ($var_modo=="submit")
		{
			// TORNEO ELIMINATORIO
			if ($res[0]["tipo_torneo"] == 1)
			{							
				// MEZCLO LOS EQUIPOS
				shuffle ($res2);
		
				$tmp = array();
				$tmp["cuadro"] = serialize (crear_eliminatoria ($res2, $BD));
				$tmp["inscripcion_abierta"] = 0;
				$BD->editTabla ("juegos", "id", $var_id_juego, $tmp);
				
			// 	LIGA
			}else if ($res[0]["tipo_torneo"] == 2)
			{								
				$tmp["cuadro"] = serialize (crear_liga ($res2, $BD));
				$tmp["inscripcion_abierta"] = 0;
				$BD->editTabla ("juegos", "id", $var_id_juego, $tmp);
				
				
			// 	LIGUILLA + ELIMINATORIAS
			}else if ($res[0]["tipo_torneo"] == 3)
			{
				/***********************************************************/
				/* ESTAS DEBERIAN SER PASADAS POR EL USUARIO EN UN FORMULARIO */
				
				if (isset($_POST["ngrupos"]))
					$GRUPOS = $_POST["ngrupos"];
				else
					$GRUPOS = 1; //1,2,4,8
				
				if (isset($_POST["nclasif"]))
					$CLASIFICADOS = $_POST["nclasif"];
				else
					$CLASIFICADOS = 2; //1,2
				
				/***********************************************************/
										
				shuffle ($res2);
				
				//CREO LOS GRUPOS
				$ligas = array();
				for ($i=0; $i<count ($res2); $i++)
					$ligas[$i%$GRUPOS][]=$res2[$i];
				
				//HAGO LOS EMPAREJAMIENTOS
				for ($i=0; $i<$GRUPOS; $i++)
					$ligas[$i]=crear_liga ($ligas[$i], $BD);
				
				$EMPAREJAMIENTOS=array();
				//GENERO EL CUADRO PREVIO DE LA ELIMINATORIA
				$RONDAS = log ($GRUPOS * $CLASIFICADOS, 2);
				//$EMPAREJAMIENTOS["eliminatorias"] = array_fill(1, pow (2, $RONDAS+1)-1, -1);
				
				$tmp=array (
					"id_1" => -1,
					"id_2" => -1,
					"ganador" => -1,
					"id_juego" => $var_id_juego,
				);
				for ($i=1; $i<pow (2, $RONDAS); $i++)
					$EMPAREJAMIENTOS["eliminatorias"][$i]= $BD->putTabla ("encuentros", $tmp);
				$EMPAREJAMIENTOS["eliminatorias"][0] = $RONDAS;	
				
				$EMPAREJAMIENTOS["ligas"]=$ligas;
				$EMPAREJAMIENTOS["fase"]=0;
				$EMPAREJAMIENTOS["clasifican"]=$CLASIFICADOS;
				$tmp = array ();
				$tmp["cuadro"] = serialize ($EMPAREJAMIENTOS);				
				$tmp["inscripcion_abierta"] = 0;
				$BD->editTabla ("juegos", "id", $var_id_juego, $tmp);
				
			//	BRACKETS WINNERS/LOSERS
			}else if ($res[0]["tipo_torneo"] == 4)
			{
				// MEZCLO LOS EQUIPOS
				shuffle ($res2);

				$EMPAREJAMIENTOS=array();
				//GENERO EL CUADRO DE WINNERS
				$EMPAREJAMIENTOS["winners"] = crear_eliminatoria ($res2, $BD);
			
				//CUADRO DE LOSERS								
				$tmp=array (
					"id_1" => -1,
					"id_2" => -1,
					"ganador" => -1,
					"id_juego" => $var_id_juego,
				);											
				for ($i=1; $i<count ($EMPAREJAMIENTOS["winners"])-1; $i++)
					$EMPAREJAMIENTOS["losers"][$i]= $BD->putTabla ("encuentros", $tmp);
				$EMPAREJAMIENTOS["losers"][0] = ($EMPAREJAMIENTOS["winners"][0]-1)*2;	
				$EMPAREJAMIENTOS["final"]= $BD->putTabla ("encuentros", $tmp);
											
				$tmp = array ();
				$tmp["cuadro"] = serialize ($EMPAREJAMIENTOS);
				$tmp["inscripcion_abierta"] = 0;
				$BD->editTabla ("juegos", "id", $var_id_juego, $tmp);
			}
			
			header("Location: index.php");
			exit ();	
		}else
		{	//DIBUJO EL FORMULARIO
			
			//VARIABLES VARIAS
			$variables_sustituir->nuevaVariable ("{TITLE}", "Cerrar la inscripción y comenzar la competición de ". $res[0]["nombre"]);
			$variables_sustituir->nuevaVariable ("{EMPAREJAR_NEQUIPOS}", count($res2));
			$variables_sustituir->nuevaVariable ("{EMPAREJAR_ID}", $var_id_juego);
						
			echo $variables_sustituir->parsearTemplate ("../inc/template/comun_top.tpl");						
			
			// CALCULO EL NUMERO MAXIMO DE GRUPOS PARA LAS LIGUILLAS			
			$max_grupos = round (log(count ($res2) /4,2));
			if ($max_grupos >3) $max_grupos=3;
			else if ($max_grupos <0) $max_grupos=0;
			
			$cuerpo = file_get_contents ("../inc/template/admin_emparejar_opt.tpl");
			$variables_sustituir->nuevaVariable ("{RADIONAME}", "ngrupos");
			$cad = "";
			
			// EL NUMERO DE GRUPOS
			for ($i=0; $i<=$max_grupos; $i++)
			{
				$variables_sustituir->nuevaVariable ("{RADIOVALUE}", pow(2,$i));
				
				if ($i==$max_grupos)
					$variables_sustituir->nuevaVariable ("{RADIOCHECKED}", "checked");
				else
				$variables_sustituir->nuevaVariable ("{RADIOCHECKED}", "");
				
				$cad .= $variables_sustituir->parsearCadena ($cuerpo);
			}
			$variables_sustituir->nuevaVariable ("{EMPAREJAR_NGRUPOS}", $cad);
					
			echo $variables_sustituir->parsearTemplate ("../inc/template/admin_emparejar.tpl");
			echo $variables_sustituir->parsearTemplate ("../inc/template/comun_bottom.tpl");
		}
	}

	
//PIE COMUN
	require "../inc/phpcode/admin_bottom.php";


?>