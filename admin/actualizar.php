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
	$variables_sustituir->nuevaVariable ("{TITLE}", "Actualizar el torneo");
	$variables_sustituir->nuevaVariable ("{ADMINACTUALIZAR_WIN1}", "checked");
	$variables_sustituir->nuevaVariable ("{ADMINACTUALIZAR_WIN1}", "");
	$variables_sustituir->nuevaVariable ("{ADMINACTUALIZAR_RESULTADO}", "");

	
	echo $variables_sustituir->parsearTemplate ("../inc/template/comun_top.tpl");	

	// LOS ENCUENTRO DE UN JUEGO EN PARTICULAR
	if (isset ($var_id_juego))
	{	//VAMOS AL TEMA
		
		$res = $BD->getTabla ("juegos", "id", $var_id_juego);
		$cuadro = unserialize ($res[0]["cuadro"]);
				
		$cuerpo= file_get_contents ("../inc/template/admin_actualizar.tpl");
		
		// TORNEO ELIMINATORIO
		if ($res[0]["tipo_torneo"] == 1)
		{
			for ($i=pow (2, $cuadro[0])-1; $i>0 ; $i--)
			{ 
				$res_nodo = $BD->getTabla ("encuentros", "id", $cuadro[$i]);				
				
				// SI EL NODO ACTUAL NO TIENE GANADOR Y TIENE YA EQUIPOS ASIGNADOS
				if ($res_nodo[0]["id_1"] != -1 && $res_nodo[0]["id_2"] != -1)
				{							
					if ((isset ($var_editar) && $res_nodo[0]["ganador"] < 0) || (!isset ($var_editar) && $res_nodo[0]["ganador"] > -1))
						continue;
				
					$variables_sustituir->nuevaVariable ("{ADMINACTUALIZAR_WIN2}", "");
					$variables_sustituir->nuevaVariable ("{ADMINACTUALIZAR_WIN1}", "");
										
					if ($res_nodo[0]["ganador"] == 2)
						$variables_sustituir->nuevaVariable ("{ADMINACTUALIZAR_WIN2}", "checked");
					else
						$variables_sustituir->nuevaVariable ("{ADMINACTUALIZAR_WIN1}", "checked");
					
					$variables_sustituir->nuevaVariable ("{ADMINACTUALIZAR_RESULTADO}", $res_nodo[0]["resultado"]);
					$tmp2= $BD->getTabla ("equipos", "id", $res_nodo[0]["id_1"]);
					$variables_sustituir->nuevaVariable ("{ADMINACTUALIZAR_E1}", $tmp2[0]["nombre_clan"]);
					$tmp2= $BD->getTabla ("equipos", "id", $res_nodo[0]["id_2"]);
					$variables_sustituir->nuevaVariable ("{ADMINACTUALIZAR_E2}", $tmp2[0]["nombre_clan"]);	
					$variables_sustituir->nuevaVariable ("{ADMINACTUALIZAR_IDENCUENTRO}", $cuadro[$i]);							
					$variables_sustituir->nuevaVariable ("{ADMINACTUALIZAR_RONDA}", nombre_ronda (log ($i,2)));
					$variables_sustituir->nuevaVariable ("{ADMINACTUALIZAR_EMPATE}", " readonly disabled");

					echo $variables_sustituir->parsearCadena ($cuerpo);											
				}
			}
		}else 
		// LIGA
		if ($res[0]["tipo_torneo"] == 2)
		{			
			for ($i=1; $i<count($cuadro) ; $i++)
			{ 
				$res_nodo = $BD->getTabla ("encuentros", "id", $cuadro[$i]);				
				
				// SI EL NODO ACTUAL NO TIENE GANADOR Y TIENE YA EQUIPOS ASIGNADOS
				if ($res_nodo[0]["id_1"] != -1 && $res_nodo[0]["id_2"] != -1)
				{								
					if ((isset ($var_editar) && $res_nodo[0]["ganador"] < 0) || (!isset ($var_editar) && $res_nodo[0]["ganador"] > -1))
						continue;
				
					$variables_sustituir->nuevaVariable ("{ADMINACTUALIZAR_WIN2}", "");
					$variables_sustituir->nuevaVariable ("{ADMINACTUALIZAR_WIN1}", "");
					$variables_sustituir->nuevaVariable ("{ADMINACTUALIZAR_EMPATE}", "");
										
					if ($res_nodo[0]["ganador"] == 2)
						$variables_sustituir->nuevaVariable ("{ADMINACTUALIZAR_WIN2}", "checked");
					else if ($res_nodo[0]["ganador"] == 0)
						$variables_sustituir->nuevaVariable ("{ADMINACTUALIZAR_EMPATE}", "checked");
					else
						$variables_sustituir->nuevaVariable ("{ADMINACTUALIZAR_WIN1}", "checked");
					
					$variables_sustituir->nuevaVariable ("{ADMINACTUALIZAR_RESULTADO}", $res_nodo[0]["resultado"]);
				
					$tmp2= $BD->getTabla ("equipos", "id", $res_nodo[0]["id_1"]);
					$variables_sustituir->nuevaVariable ("{ADMINACTUALIZAR_E1}", $tmp2[0]["nombre_clan"]);
					$tmp2= $BD->getTabla ("equipos", "id", $res_nodo[0]["id_2"]);
					$variables_sustituir->nuevaVariable ("{ADMINACTUALIZAR_E2}", $tmp2[0]["nombre_clan"]);	
					$variables_sustituir->nuevaVariable ("{ADMINACTUALIZAR_IDENCUENTRO}", $cuadro[$i]);							
					$variables_sustituir->nuevaVariable ("{ADMINACTUALIZAR_RONDA}", "");
					

					echo $variables_sustituir->parsearCadena ($cuerpo);											
				}
			}
		}else 
		// LIGUILLAS + ELIMINATORIAS
		if ($res[0]["tipo_torneo"] == 3)
		{	
			// FASE DE LIGUILLAS
			if ($cuadro["fase"]==0)
			{
				for ($j=0; $j<count ($cuadro["ligas"]); $j++)
				{
					$letras = "ABCDEFGH";
					echo "<h1>Grupo ". $letras{$j};
					for ($i=1; $i<count($cuadro["ligas"][$j]) ; $i++)
					{ 
						$res_nodo = $BD->getTabla ("encuentros", "id", $cuadro["ligas"][$j][$i]);				
						
						// SI EL NODO ACTUAL NO TIENE GANADOR Y TIENE YA EQUIPOS ASIGNADOS
						if ($res_nodo[0]["id_1"] != -1 && $res_nodo[0]["id_2"] != -1)
						{							
						
							if ((isset ($var_editar) && $res_nodo[0]["ganador"] < 0) || (!isset ($var_editar) && $res_nodo[0]["ganador"] > -1))
								continue;
				
							$variables_sustituir->nuevaVariable ("{ADMINACTUALIZAR_WIN2}", "");
							$variables_sustituir->nuevaVariable ("{ADMINACTUALIZAR_WIN1}", "");
							$variables_sustituir->nuevaVariable ("{ADMINACTUALIZAR_EMPATE}", "");
										
							if ($res_nodo[0]["ganador"] == 2)
								$variables_sustituir->nuevaVariable ("{ADMINACTUALIZAR_WIN2}", "checked");
							else if ($res_nodo[0]["ganador"] == 0)
								$variables_sustituir->nuevaVariable ("{ADMINACTUALIZAR_EMPATE}", "checked");
							else
								$variables_sustituir->nuevaVariable ("{ADMINACTUALIZAR_WIN1}", "checked");
							
							$variables_sustituir->nuevaVariable ("{ADMINACTUALIZAR_RESULTADO}", $res_nodo[0]["resultado"]);						
							$tmp2= $BD->getTabla ("equipos", "id", $res_nodo[0]["id_1"]);
							$variables_sustituir->nuevaVariable ("{ADMINACTUALIZAR_E1}", $tmp2[0]["nombre_clan"]);
							$tmp2= $BD->getTabla ("equipos", "id", $res_nodo[0]["id_2"]);
							$variables_sustituir->nuevaVariable ("{ADMINACTUALIZAR_E2}", $tmp2[0]["nombre_clan"]);	
							$variables_sustituir->nuevaVariable ("{ADMINACTUALIZAR_IDENCUENTRO}", $cuadro["ligas"][$j][$i]);							
							$variables_sustituir->nuevaVariable ("{ADMINACTUALIZAR_RONDA}", "");
							$variables_sustituir->nuevaVariable ("{ADMINACTUALIZAR_EMPATE}", "");
		
							echo $variables_sustituir->parsearCadena ($cuerpo);											
						}
					}
				}
			}else
			// FASE SIGUIENTE
			if ($cuadro["fase"]==1)
			{				
				for ($i=pow (2, $cuadro["eliminatorias"][0])-1; $i>0 ; $i--)
				{ 
					$res_nodo = $BD->getTabla ("encuentros", "id", $cuadro["eliminatorias"][$i]);				
					
					// SI EL NODO ACTUAL NO TIENE GANADOR Y TIENE YA EQUIPOS ASIGNADOS
					if ($res_nodo[0]["id_1"] != -1 && $res_nodo[0]["id_2"] != -1)
					{	
						if ((isset ($var_editar) && $res_nodo[0]["ganador"] < 0) || (!isset ($var_editar) && $res_nodo[0]["ganador"] > -1))
							continue;
					
						$variables_sustituir->nuevaVariable ("{ADMINACTUALIZAR_WIN2}", "");
						$variables_sustituir->nuevaVariable ("{ADMINACTUALIZAR_WIN1}", "");
						$variables_sustituir->nuevaVariable ("{ADMINACTUALIZAR_EMPATE}", "");
											
						if ($res_nodo[0]["ganador"] == 2)
							$variables_sustituir->nuevaVariable ("{ADMINACTUALIZAR_WIN2}", "checked");						
						else
							$variables_sustituir->nuevaVariable ("{ADMINACTUALIZAR_WIN1}", "checked");
						
						$variables_sustituir->nuevaVariable ("{ADMINACTUALIZAR_RESULTADO}", $res_nodo[0]["resultado"]);

					
						$tmp2= $BD->getTabla ("equipos", "id", $res_nodo[0]["id_1"]);
						$variables_sustituir->nuevaVariable ("{ADMINACTUALIZAR_E1}", $tmp2[0]["nombre_clan"]);
						$tmp2= $BD->getTabla ("equipos", "id", $res_nodo[0]["id_2"]);
						$variables_sustituir->nuevaVariable ("{ADMINACTUALIZAR_E2}", $tmp2[0]["nombre_clan"]);	
						$variables_sustituir->nuevaVariable ("{ADMINACTUALIZAR_IDENCUENTRO}", $cuadro["eliminatorias"][$i]);
						$variables_sustituir->nuevaVariable ("{ADMINACTUALIZAR_RONDA}", nombre_ronda (log ($i,2)));
						$variables_sustituir->nuevaVariable ("{ADMINACTUALIZAR_EMPATE}", " readonly disabled");
	
						echo $variables_sustituir->parsearCadena ($cuerpo);											
					}
				}
			}
		}else 
		// BRACKETS WINNERS/LOSERS
		if ($res[0]["tipo_torneo"] == 4)
		{
			// WIINNERS
			echo "<h2>Cuadro de Winners</h2>";
			for ($i=pow (2, $cuadro["winners"][0])-1; $i>0 ; $i--)
			{ 
				$res_nodo = $BD->getTabla ("encuentros", "id", $cuadro["winners"][$i]);				
				
				// SI EL NODO ACTUAL NO TIENE GANADOR Y TIENE YA EQUIPOS ASIGNADOS
				if ($res_nodo[0]["id_1"] != -1 && $res_nodo[0]["id_2"] != -1)
				{		
					if ((isset ($var_editar) && $res_nodo[0]["ganador"] < 0) || (!isset ($var_editar) && $res_nodo[0]["ganador"] > -1))
						continue;
				
					$variables_sustituir->nuevaVariable ("{ADMINACTUALIZAR_WIN2}", "");
					$variables_sustituir->nuevaVariable ("{ADMINACTUALIZAR_WIN1}", "");
					$variables_sustituir->nuevaVariable ("{ADMINACTUALIZAR_EMPATE}", "");
										
					if ($res_nodo[0]["ganador"] == 2)
						$variables_sustituir->nuevaVariable ("{ADMINACTUALIZAR_WIN2}", "checked");						
					else
						$variables_sustituir->nuevaVariable ("{ADMINACTUALIZAR_WIN1}", "checked");					
					$variables_sustituir->nuevaVariable ("{ADMINACTUALIZAR_RESULTADO}", $res_nodo[0]["resultado"]);				
					$tmp2= $BD->getTabla ("equipos", "id", $res_nodo[0]["id_1"]);
					$variables_sustituir->nuevaVariable ("{ADMINACTUALIZAR_E1}", $tmp2[0]["nombre_clan"]);
					$tmp2= $BD->getTabla ("equipos", "id", $res_nodo[0]["id_2"]);
					$variables_sustituir->nuevaVariable ("{ADMINACTUALIZAR_E2}", $tmp2[0]["nombre_clan"]);	
					$variables_sustituir->nuevaVariable ("{ADMINACTUALIZAR_IDENCUENTRO}", $cuadro["winners"][$i]);	
					$variables_sustituir->nuevaVariable ("{ADMINACTUALIZAR_RONDA}", nombre_ronda (log ($i,2)). " (winners bracket)");
					$variables_sustituir->nuevaVariable ("{ADMINACTUALIZAR_EMPATE}", " readonly disabled");

					echo $variables_sustituir->parsearCadena ($cuerpo);											
				}
			}
			// LOSERS			
			echo "<h2>Cuadro de Losers</h2>";
			for ($i=pow (2, $cuadro["winners"][0])-2; $i>0 ; $i--)
			{ 
				$res_nodo = $BD->getTabla ("encuentros", "id", $cuadro["losers"][$i]);
				
				// SI EL NODO ACTUAL NO TIENE GANADOR Y TIENE YA EQUIPOS ASIGNADOS
				if ($res_nodo[0]["id_1"] != -1 && $res_nodo[0]["id_2"] != -1)
				{					
					if ((isset ($var_editar) && $res_nodo[0]["ganador"] < 0) || (!isset ($var_editar) && $res_nodo[0]["ganador"] > -1))
						continue;
				
					$variables_sustituir->nuevaVariable ("{ADMINACTUALIZAR_WIN2}", "");
					$variables_sustituir->nuevaVariable ("{ADMINACTUALIZAR_WIN1}", "");
					$variables_sustituir->nuevaVariable ("{ADMINACTUALIZAR_EMPATE}", "");
										
					if ($res_nodo[0]["ganador"] == 2)
						$variables_sustituir->nuevaVariable ("{ADMINACTUALIZAR_WIN2}", "checked");						
					else
						$variables_sustituir->nuevaVariable ("{ADMINACTUALIZAR_WIN1}", "checked");					
					$variables_sustituir->nuevaVariable ("{ADMINACTUALIZAR_RESULTADO}", $res_nodo[0]["resultado"]);	
				
					$tmp2= $BD->getTabla ("equipos", "id", $res_nodo[0]["id_1"]);
					$variables_sustituir->nuevaVariable ("{ADMINACTUALIZAR_E1}", $tmp2[0]["nombre_clan"]);
					$tmp2= $BD->getTabla ("equipos", "id", $res_nodo[0]["id_2"]);
					$variables_sustituir->nuevaVariable ("{ADMINACTUALIZAR_E2}", $tmp2[0]["nombre_clan"]);	
					$variables_sustituir->nuevaVariable ("{ADMINACTUALIZAR_IDENCUENTRO}", $cuadro["losers"][$i]);	
					$variables_sustituir->nuevaVariable ("{ADMINACTUALIZAR_RONDA}", " (losers bracket)");
					$variables_sustituir->nuevaVariable ("{ADMINACTUALIZAR_EMPATE}", " readonly disabled");

					echo $variables_sustituir->parsearCadena ($cuerpo);											
				}
			}	
			
			// LA GRAN FINAL
			$res_nodo = $BD->getTabla ("encuentros", "id", $cuadro["final"]);
			if ($res_nodo[0]["id_1"] != -1 && $res_nodo[0]["id_2"] != -1 && $res_nodo[0]["ganador"] < 0)
			{
				$variables_sustituir->nuevaVariable ("{ADMINACTUALIZAR_WIN2}", "");
				$variables_sustituir->nuevaVariable ("{ADMINACTUALIZAR_WIN1}", "");
				$variables_sustituir->nuevaVariable ("{ADMINACTUALIZAR_EMPATE}", "");
									
				if ($res_nodo[0]["ganador"] == 2)
					$variables_sustituir->nuevaVariable ("{ADMINACTUALIZAR_WIN2}", "checked");						
				else
					$variables_sustituir->nuevaVariable ("{ADMINACTUALIZAR_WIN1}", "checked");					
				$variables_sustituir->nuevaVariable ("{ADMINACTUALIZAR_RESULTADO}", $res_nodo[0]["resultado"]);	
			
				$tmp2= $BD->getTabla ("equipos", "id", $res_nodo[0]["id_1"]);
				$variables_sustituir->nuevaVariable ("{ADMINACTUALIZAR_E1}", $tmp2[0]["nombre_clan"]);
				$tmp2= $BD->getTabla ("equipos", "id", $res_nodo[0]["id_2"]);
				$variables_sustituir->nuevaVariable ("{ADMINACTUALIZAR_E2}", $tmp2[0]["nombre_clan"]);	
				$variables_sustituir->nuevaVariable ("{ADMINACTUALIZAR_IDENCUENTRO}", $cuadro["final"]);	
				$variables_sustituir->nuevaVariable ("{ADMINACTUALIZAR_RONDA}", "La gran final (campeones winners/losers)");
				$variables_sustituir->nuevaVariable ("{ADMINACTUALIZAR_EMPATE}", " readonly disabled");

				echo $variables_sustituir->parsearCadena ($cuerpo);		
			}
		}
	
	// METO LOS DATOS DE UN PARTIDO
	}else if (isset ($var_id_encuentro))
	{
		
		// ACTUALIZO EL NODO ACTUAL
		$res2 = $BD->getTabla ("encuentros", "id", $var_id_encuentro);
		$res = $BD->getTabla ("juegos", "id", $res2[0]["id_juego"]);
		$cuadro = unserialize ($res[0]["cuadro"]);
				
		$tmp=array (
			"ganador" => $_POST["encuentro"]["ganador"],
			"resultado" => $_POST["encuentro"]["resultado"],
			"fecha" => time(),
		);
		$BD->editTabla ("encuentros", "id", $var_id_encuentro, $tmp);
		
		if ($res[0]["tipo_torneo"] == 3 && $cuadro["fase"]==1)
		{
			$cuadro = $cuadro["eliminatorias"];
			$res[0]["tipo_torneo"] = 1;
		}
		
		$actual = array_search ($var_id_encuentro, $cuadro);				
		$padre = floor($actual/2);
		
		// ACTUALIZO EL PADRE CON EL EQUIPO Q PASA DE RONDA SI ES UNA ELIMINATORIA
		if ($res[0]["tipo_torneo"] == 1)
		{
			if ($padre > 0)
			{	
				$ganador= devolver_ganador ($var_id_encuentro, $BD);
				
				if ($actual % 2)
					$tmp = array("id_2" => $ganador["id"]);
				else
					$tmp = array("id_1" => $ganador["id"]);
				
				$BD->editTabla ("encuentros", "id", $cuadro[$padre], $tmp);
			}else
			{
				// TORNEO FINALIZADO						
				$tmp = array("inscripcion_abierta" => "-1");
				$BD->editTabla ("juegos", "id", $res2[0]["id_juego"], $tmp);			
			}
		}else if ($res[0]["tipo_torneo"] == 2)
		{
			// SI SE HA ACABADO LA LIGA
			$res = $BD->getTabla ("encuentros", "id_juego", $res[0]["id"], " AND ganador = '-1'");
			if (count ($res) == 0)
			{
				$tmp = array("inscripcion_abierta" => "-1");
				$BD->editTabla ("juegos", "id", $res2[0]["id_juego"], $tmp);			
			}
		}else if ($res[0]["tipo_torneo"] == 3)
		{
			// SI TODAVIA ESTAMOS EN LA PRIMERA FASE
			if ($cuadro["fase"]==0)
			{
				$fase_acabada = true;
				
				for ($j=0; $j<count ($cuadro["ligas"]); $j++)
				{
					// COMPRUEBO SI LA LIGA ESTA ACABADA
					$acabada=true;
					$presente=false;
					for ($i=1; $i<count ($cuadro["ligas"][$j]); $i++)
					{
						$res = $BD->getTabla ("encuentros", "id", $cuadro["ligas"][$j][$i]);
						if ($res[0]["ganador"] < 0)
							$acabada=false;	
						if ($cuadro["ligas"][$j][$i] == $var_id_encuentro)
							$presente=true;
					}
					
					// COMPRUEBO SI SE HA ACABADO LA FASE
					if (!$acabada)
						$fase_acabada = false;
						
					// SI EL CAMBIO FUE EN ESTA LIGA Y ESTA ACABADA CALCULO LA CLASIFICACION 
					// Y METO A LOS CLASIFICADOS EN EL CUADRO
					if ($presente && $acabada)
					{
						// CALCULO LA CLASIFICACION
						$tabla= calcular_clasificacion ($cuadro["ligas"][$j], $BD);
						
						//ORDENO LA TABLA
						foreach ($tabla as $key=>$valor)		
							$tabla_ordenada[$key]=$valor["puntos"];		
						arsort ($tabla_ordenada);
												
						$BASE = pow(2,$cuadro["eliminatorias"][0]-1);						
						
						// SI SE CLASIFICA UNO POR GRUPO
						if ($cuadro["clasifican"] == 1)
						{
							$ncampeon = $BASE + floor($j/2);												
							$obj = each($tabla_ordenada);
							
							if ($j%2)
								$tmp = array ("id_2" => $obj["key"]);
							else
								$tmp = array ("id_1" => $obj["key"]);								
							$BD->editTabla ("encuentros", "id", $cuadro["eliminatorias"][$ncampeon], $tmp);
						
						// SI SE CLASIFICAN DOS
						}else if ($cuadro["clasifican"] == 2)
						{
							$ncampeon = $BASE + $j;
							if ($j < count ($cuadro["ligas"])/2)
								$nsubcampeon = $BASE*1.5+$j;
							else
								$nsubcampeon = $BASE*0.5+$j;
																
							$obj = each($tabla_ordenada);						
							$tmp = array (
								"id_1" => $obj["key"]
								);	
							$BD->editTabla ("encuentros", "id", $cuadro["eliminatorias"][$ncampeon], $tmp);
							
							$obj = each($tabla_ordenada);
							$tmp = array (
								"id_2" => $obj["key"]
							);
							$BD->editTabla ("encuentros", "id", $cuadro["eliminatorias"][$nsubcampeon], $tmp);
						}
					}
				}
								
				if ($fase_acabada)
				{
					$cuadro["fase"]=1;
					$tmp = array ("cuadro" => serialize ($cuadro));				
					$BD->editTabla ("juegos", "id", $res2[0]["id_juego"], $tmp);					
				}
			}
			
		// SI ES UN WINNERS/LOSERS
		}else if ($res[0]["tipo_torneo"] == 4)
		{	
			
			// COMPRUEBO SI ES UN ENCUENTRO DE WINNERS
			$actual = array_search($var_id_encuentro, $cuadro["winners"]);
						
			if ($actual > 0)
			{			
				$padre= floor($actual/2);
				$ronda= floor (log($actual,2))+1;
								
				$res = $BD->getTabla ("encuentros", "id", $var_id_encuentro);

				// AVERIGUO EL GANADOR Y EL PERDEDOR
				if ($res[0]["ganador"]==1)
				{						
					$id_w = $res[0]["id_1"];
					$id_l = $res[0]["id_2"];
				}
				else if ($res[0]["ganador"]==2)
				{
					$id_w = $res[0]["id_2"];
					$id_l = $res[0]["id_1"];
				}
				
				if ($actual % 2)
					$tmp = array("id_2" => $id_w);
				else
					$tmp = array("id_1" => $id_w);
				
				$BD->editTabla ("encuentros", "id", $cuadro["winners"][$padre], $tmp);
				
				// METO LOS LOSERS
				// SI ES LA PRIMERA RONDA METO LOS ELIMINADOS 
				// SI NO METO UNO DE CADA 2	
				if ($ronda == $cuadro["winners"][0])
				{
					if ($actual % 2)
						$tmp = array("id_1" => $id_l);
					else
						$tmp = array("id_2" => $id_l);
					
					// COMPRUEBO SI LA PRIMERA RONDA DE WINNERS SE HA ACABADO
					$NEQ= pow (2, $ronda-1);	
					$acabada=true;
					for ($k=0; $k<$NEQ; $k++)
					{	
						$restmp = $BD->getTabla ("encuentros", "id", $cuadro["winners"][$NEQ+$k]);
						if ($restmp[0]["ganador"] == -1)
							$acabada=false;												
					}

					//SI SE HA ACABAO COMPRUEBO SI HAY LOSERS VACIOS
					if ($acabada)
					for ($k=0; $k<$NEQ/2; $k++)
					{							
						$idtmp=$cuadro["losers"][devolver_inicio_ronda_losers ($cuadro["losers"][0])+$k];
						$restmp = $BD->getTabla ("encuentros", "id", $idtmp);
												
						if ($restmp[0]["ganador"] == -1 && ($restmp[0]["id_2"] == -1 || $restmp[0]["id_1"] == -1))
						{
							if ($restmp[0]["id_2"] == -1)
							{
								$id_w = $restmp[0]["id_1"];
								$tmp = array("ganador" => "1");
							}
							else
							{
								$id_w = $restmp[0]["id_2"];
								$tmp = array("ganador" => "2");
							}
														
							$BD->editTabla ("encuentros", "id", $idtmp, $tmp);	
							//var_dump ($tmp);exit();
							//ACTUALIZO EL PADRE
							$tmp = array("id_1" => $id_w);							
							$BD->editTabla ("encuentros", "id", $idtmp-$NEQ/2, $tmp);	
							$tmp=array();
						}
					}
								
				//	var_dump ($acabada);exit();
				
					$BD->editTabla ("encuentros", "id", $cuadro["losers"][devolver_inicio_ronda_losers ($cuadro["losers"][0])+floor(($actual - pow (2,$ronda-1))/2)], $tmp);						
				}else
				{	//NO ES LA PRIMERA RONDA					
					$RONDA_LOSERS= $ronda*2-1;	
					$ELIMINADOS= pow (2, $ronda-1);					
					
					$tmp = array("id_2" => $id_l);						
					$num = ($actual - pow (2,$ronda-1));
					
					$BD->editTabla ("encuentros", "id", $cuadro["losers"][devolver_inicio_ronda_losers ($RONDA_LOSERS) + $num], $tmp);	
					
					// SI ES LA FINAL
					if ($ronda == 1)
					{
						$tmp = array("id_1" => $id_w);				
						$BD->editTabla ("encuentros", "id", $cuadro["final"], $tmp);	
					}
				}									
			}
			
			// SI ES UN ENCUENTRO DE LOSERS
			$actual = array_search($var_id_encuentro, $cuadro["losers"]);					
			if ($actual > 0)
			{
				$ganador= devolver_ganador ($var_id_encuentro, $BD);				
				$NEQ= floor(log ($actual,2));				
									
				if (devolver_ronda_losers ($actual) %2 == 0)
					$tmp = array("id_1" => $ganador["id"]);
				else 				{
					if (($actual % 2)==0)
						$tmp = array("id_1" => $ganador["id"]);
					else
						$tmp = array("id_2" => $ganador["id"]);
				}
				
				// SI NO ES LA FINAL
				if (devolver_ronda_losers ($actual) > 1)
					$BD->editTabla ("encuentros", "id", $cuadro["losers"][$actual-$NEQ], $tmp);	
				else
				// SI ES LA FINAL 
				if (devolver_ronda_losers ($actual) == 1)
				{
					$tmp = array("id_2" => $ganador["id"]);				
					$BD->editTabla ("encuentros", "id", $cuadro["final"], $tmp);		
				}
			}
			
			// LA GRAN FINAL
			if ($cuadro["final"] == $var_id_encuentro)
			{			
				$tmp = array("inscripcion_abierta" => "-1");
				$BD->editTabla ("juegos", "id", $res2[0]["id_juego"], $tmp);
			}
		}
		
		echo "Encuentro añadido con exito";
		header( 'refresh: 1; url=actualizar.php?id_juego='. $res2[0]["id_juego"]);
	}
	else
	{
		header ("Location: index.php");
		exit ();
	}		

	echo $variables_sustituir->parsearTemplate ("../inc/template/comun_bottom.tpl");	

//PIE COMUN
	require "../inc/phpcode/admin_bottom.php";

?>