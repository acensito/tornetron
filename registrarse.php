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

//VARIABLES VARIAS
	$variables_sustituir->nuevaVariable ("{TITLE}", "Incribirse a los torneos");

//CABECERA
	echo $variables_sustituir->parsearTemplate ("inc/template/comun_top.tpl");

// COMPRUEBO SI ESTOY ENVIANDO UN FORMULARIO DE REGISTRO
	session_start ();	
	if (isset ($var_modo) && $var_modo == "submit" && isset ($_SESSION['id_juego']))
	{		
		$error = true;
		
		// OBTENGO EL JUEGO A PARTIR DEL ID
		$res = $BD->getTabla ("juegos", "id", $_SESSION['id_juego']);
		
		//COMPROBACIONES
		if (count ($res) == 0 || $res[0]["inscripcion_abierta"] != 1)
		{
			header ("Location: registrarse.php");
			exit ();
		}
		
		
		if (!(isset ($_SESSION['numero_seguridad']) && $_SESSION['numero_seguridad'] == $_POST["comprobacion"]))
			$variables_sustituir->nuevaVariable ("{ERROR}", "ERROR: Codigo de comprobación incorrecto");
		else 
		{	// CODIGO DE SEGURIDAD CORRECTO
			
			// TORNEO INDIVIDUAL
			if  ($res[0]["numero_jugadores"]==1)
			{
				$jugador = $_POST["jugador"];
				
				// COMPRUEBO LOS DATOS
				if (comprobar_datos_jugador ($jugador))
				{
					$res2 = $BD->getTabla ("jugadores", "id_juego", $_SESSION['id_juego'], " AND dni = '". strtoupper($jugador["dni"]) ."'");
										
					// SI EL JUGADOR NO ESTABA DADO DE ALTA ANTERIORMENTE EN ESE JUEGO
					if (count ($res2) == 0)
					{
						$tmp=array (
							"nick" => addslashes (strip_tags ($jugador["nick"])),
							"nombre" => addslashes (strip_tags ($jugador["nombre"])),
							"dni" => strtoupper ($jugador["dni"]),
							"id_juego" => $_SESSION['id_juego'],
							"localizacion" => addslashes (strip_tags ($jugador["localizacion"]))
							);
						$id_jug = $BD->putTabla ("jugadores", $tmp);
						
						//$res2 = $BD->getTabla ("jugadores", "id_juego", $_SESSION['id_juego'], " AND dni = '". strtoupper($jugador["dni"]) ."'");
						$tmp=array (			
							"nombre_clan" => addslashes (strip_tags ($jugador["nick"])),
							"id_juego" => $_SESSION['id_juego'],
							"id_jugadores" => $id_jug,
							"ip_registro" => realip (),
							);
						$BD->putTabla ("equipos", $tmp);
						
						$error = false;
					}else
						$variables_sustituir->nuevaVariable ("{ERROR}", "ERROR: El jugador ya esta dado de alta en este juego");
				}else
					$variables_sustituir->nuevaVariable ("{ERROR}", "ERROR: Datos del jugador incorrectos");
				
			
			// TORNEO DE EQUIPOS
			}else if ($res[0]["numero_jugadores"] > 1)
			{			
				// COMPRUEBO EL CLAN								
				if (isset ($_POST["clan"]) && trim ($_POST["clan"]) != "")
				{
					$res2 = $BD->getTabla ("equipos", "id_juego", $_SESSION['id_juego'], " AND nombre_clan = '". trim ($_POST["clan"]) ."'");
					if (count ($res2) == 0)
					{
						$jugadores = array();
						for ($i=0; $i<$res[0]["numero_jugadores"] && !$tmp_error; $i++)
						{
							$jugador = $_POST["jugador" . $i];
							// USO ESTA VARIABLE PARA CONTROLAR CUANDO HAY UN FALLO
							$tmp_error = true;
				
							// COMPRUEBO LOS DATOS
							if (comprobar_datos_jugador ($jugador))
							{
								$res2 = $BD->getTabla ("jugadores", "id_juego", $_SESSION['id_juego'], " AND dni = '". strtoupper($jugador["dni"]) ."'");
													
								// SI EL JUGADOR NO ESTABA DADO DE ALTA ANTERIORMENTE EN ESE JUEGO
								if (count ($res2) == 0)
								{
									$jugadores[]=array (
										"nick" => addslashes (strip_tags ($jugador["nick"])),
										"nombre" => addslashes (strip_tags ($jugador["nombre"])),
										"dni" => strtoupper ($jugador["dni"]),
										"id_juego" => $_SESSION['id_juego'],
										"localizacion" => addslashes (strip_tags ($jugador["localizacion"]))
										);
									$tmp_error = false;
								}else
									$variables_sustituir->nuevaVariable ("{ERROR}", "ERROR: Un jugador ya esta dado de alta en este torneo");
							}else
								$variables_sustituir->nuevaVariable ("{ERROR}", "ERROR: Datos de un jugador incorrectos");							
						}
						
						$suplentes = array();
						for ($i=0; $i<$res[0]["numero_suplentes"]; $i++)
						{
							$jugador = $_POST["suplente" . $i];						
				
							// COMPRUEBO LOS DATOS
							if (comprobar_datos_jugador ($jugador))
							{
								$res2 = $BD->getTabla ("jugadores", "id_juego", $_SESSION['id_juego'], " AND dni = '". strtoupper($jugador["dni"]) ."'");
													
								// SI EL JUGADOR NO ESTABA DADO DE ALTA ANTERIORMENTE EN ESE JUEGO
								if (count ($res2) == 0)
								{
									$suplentes[]=array (
										"nick" => addslashes (strip_tags ($jugador["nick"])),
										"nombre" => addslashes (strip_tags ($jugador["nombre"])),
										"dni" => strtoupper ($jugador["dni"]),
										"id_juego" => $_SESSION['id_juego'],
										"localizacion" => addslashes (strip_tags ($jugador["localizacion"]))
										);									
								}else
									$variables_sustituir->nuevaVariable ("{ERROR}", "ERROR: Un suplente ya esta dado de alta en este torneo");
							}
						}
						
						// TODO CORRECTO ASI Q PROCEDO A INTRODUCIR LOS DATOS EN LA BASE DE DATOS
						if (!$tmp_error)
						{
							$id_jugadores = array();
							foreach ($jugadores as $tmp)
							{
								$id_jugadores[] = $BD->putTabla ("jugadores", $tmp);
								
								//$res2 = $BD->getTabla ("jugadores", "id_juego", $_SESSION['id_juego'], " AND dni = '". $tmp["dni"] ."'");
								//$id_jugadores[]=$res2[0]["id"];
							}
							
							$id_suplentes = array();
							foreach ($suplentes as $tmp)
							{
								$id_suplentes[]=$BD->putTabla ("jugadores", $tmp);
								
								//$res2 = $BD->getTabla ("jugadores", "id_juego", $_SESSION['id_juego'], " AND dni = '". $tmp["dni"] ."'");
								//$id_suplentes[]=$res2[0]["id"];
							}
						
						
							$tmp=array (							
								"id_juego" => $_SESSION['id_juego'],
								"nombre_clan" => addslashes(strip_tags ($_POST["clan"])),
								"id_jugadores" => implode(",", $id_jugadores),
								"id_suplentes" => implode(",", $id_suplentes),
								"ip_registro" => realip (),
							);
							$BD->putTabla ("equipos", $tmp);
						
							$error = false;
						}
												
					}else
						$variables_sustituir->nuevaVariable ("{ERROR}", "ERROR: El clan ya esta inscrito a este torneo");
				}else
					$variables_sustituir->nuevaVariable ("{ERROR}", "ERROR: Nombre del clan incorrecto");
												
			}
			
		}		
		unset ($_SESSION['numero_seguridad']);
		
		if ($error)
			$var_id_juego = $_SESSION['id_juego'];
		else 
		{
			header("Location: registrarse.php?modo=exito");
			exit ();	
		}
		
	}else if (isset ($var_modo) && $var_modo == "exito" && isset ($_SESSION['id_juego']))
	{		
		$res = $BD->getTabla ("juegos", "id", $_SESSION['id_juego']);		
		echo "<div class='divExito'>Registrado al torneo de " . $res[0]["nombre"] . "</div>";
		unset ($_SESSION['id_juego']);
		header( 'refresh: 3; url=index.php'); 
	}
	
	
	// EL MODO POR DEFECTO
	if (!isset ($var_id_juego))
	{
		$cuerpo = file_get_contents  ("inc/template/registrarse_body.tpl");
		$res = $BD->getTabla ("juegos", "numero_jugadores", "1", " AND inscripcion_abierta = '1'");
		if (count ($res)>0)
		{
			echo "<h4>Torneos individuales</h4>";
			foreach ($res as $fila)
			{
				$variables_sustituir->nuevaVariable ("{REGISTRARSE_LINK}", "registrarse.php?id_juego=". $fila["id"]);
				$variables_sustituir->nuevaVariable ("{REGISTRARSE_NOMBRE}", $fila["nombre"]);
				
				echo $variables_sustituir->parsearCadena ($cuerpo);
			}			
		}
		$res = $BD->getTabla ("juegos", "", "", "WHERE `numero_jugadores` > '1' AND inscripcion_abierta = '1'");
		if (count ($res)>0)
		{
			echo "<h4>Torneos de equipo</h4>";
			foreach ($res as $fila)
			{
				$variables_sustituir->nuevaVariable ("{REGISTRARSE_LINK}", "registrarse.php?id_juego=". $fila["id"]);
				$variables_sustituir->nuevaVariable ("{REGISTRARSE_NOMBRE}", $fila["nombre"]);
				
				echo $variables_sustituir->parsearCadena ($cuerpo);
			}			
		}
	}else
	// MODO DE REGISTRO A UN JUEGO EN PARTICULAR
	{
		// COMPRUEBO Q ES UN ID VALIDO
		$res = $BD->getTabla ("juegos", "id", $var_id_juego);
		if (count ($res) == 0 || $res[0]["inscripcion_abierta"] != 1)
		{
			header ("Location: registrarse.php");
			exit ();
		}
				
		$_SESSION['id_juego'] = $var_id_juego;		
						
		// EL REGISTRO ES DIFERENTE EN FUNCION DE QUE SEA INDIVIDUAL O COLECTIVO
		if  ($res[0]["numero_jugadores"]==1)
		{
			$cuerpo = file_get_contents  ("inc/template/registrarse_jugador.tpl");
														
			// PRINCIPIO DEL FORMULARIO
			$variables_sustituir->nuevaVariable ("{FORMULARIO_TITULO}", "Inscribiendose en el torneo de " . $res[0]["nombre"]);
			echo $variables_sustituir->parsearTemplate ("inc/template/registrarse_form_top.tpl");
			
			$variables_sustituir->nuevaVariable ("{JUGADOR_TITULO}", "Jugador");
			$variables_sustituir->nuevaVariable ("{JUGADOR}", "jugador");
			echo $variables_sustituir->parsearCadena ($cuerpo);
			
			// FIN DEL FORMULARIO
			echo $variables_sustituir->parsearTemplate ("inc/template/registrarse_form_bottom.tpl");
			
		}else if  ($res[0]["numero_jugadores"]>1)
		{
			$cuerpo = file_get_contents  ("inc/template/registrarse_jugador.tpl");
			
			// PRINCIPIO DEL FORMULARIO
			$variables_sustituir->nuevaVariable ("{FORMULARIO_TITULO}", "Inscribiendose en el torneo de " . $res[0]["nombre"]);
			echo $variables_sustituir->parsearTemplate ("inc/template/registrarse_form_top.tpl");
			
			echo $variables_sustituir->parsearTemplate ("inc/template/registrarse_equipo.tpl");
			
			// LOS JUGADORES
			echo "<h3>Debes rellenar todos los jugadores</h3>El primer jugador se considerará el capitán del equipo";			
			for ($i=0; $i<$res[0]["numero_jugadores"]; $i++)
			{
				$variables_sustituir->nuevaVariable ("{JUGADOR_TITULO}", "Jugador ". ($i+1));
				$variables_sustituir->nuevaVariable ("{JUGADOR}", "jugador$i");
				echo $variables_sustituir->parsearCadena ($cuerpo);
			}
			
			// LOS JUGADORES SUPLENTES			
			echo "<h3>No es obligatorio tener suplentes</h3>";
			for ($i=0; $i<$res[0]["numero_suplentes"]; $i++)
			{
				$variables_sustituir->nuevaVariable ("{JUGADOR_TITULO}", "Suplente ". ($i+1));
				$variables_sustituir->nuevaVariable ("{JUGADOR}", "suplente$i");
				echo $variables_sustituir->parsearCadena ($cuerpo);
			}
			
			// FIN DEL FORMULARIO
			echo $variables_sustituir->parsearTemplate ("inc/template/registrarse_form_bottom.tpl");
		}
	}
	
// EL FIN
require "inc/phpcode/comun_bottom.php";	

?>