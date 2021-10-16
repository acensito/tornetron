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

// PARA SABER LA IP REAL
function realip()
{
	if ($for = getenv('HTTP_X_FORWARDED_FOR'))
	{
//   			echo $for;
		$afor = explode(",", $for);
		return trim($afor[0]);
	}
	else
	{
		return getenv('REMOTE_ADDR');
	}
}  

// COMPRUEBA EL DIRECTORIO EN CUESTION Y SI NO EXISTE O NO ES ESCRIBIBLE DA UN ERROR
function comprobar_directorio_escribible ($dir)
{
	if (!is_dir($dir))
		@mkdir ($dir, 0777);
	@chmod ($dir, 0777);	
	
	if (!is_writeable ($dir))
		die ("
			ERROR:El directorio $dir no se puede crear o PHP no puede escribir en el<br>
			ERROR:I can't create directory $dir or PHP can't write on it
		");
}

// DEVUELVE TRUE SI LOS DATOS ESTAN CORRECTOS
function comprobar_datos_jugador ($jugador)
{	
	if (trim(strip_tags ($jugador["nick"])) != "" && trim(strip_tags ($jugador["nombre"])) != "" && trim(strip_tags ($jugador["dni"])) != "")
	{
		if (strlen(trim($jugador["nick"])) < 255 && strlen(trim($jugador["nombre"])) < 255)
		{
			$letras = "TRWAGMYFPDXBNJZSQVHLCKE";
			if (preg_match("/^([0-9]{7,8})([A-Z^IOU])$/i", $jugador["dni"], $tmp))
			{									
				if (($letras{$tmp[1] %23}) == strtoupper ($tmp[2]))
				{
					
					return true;
				}
			}
		}
	}
	return false;
}

function sortear_liga ($equipos)
{
	$NUMERO_EQUIPOS = count ($equipos);	
	
	// SORTEO TONTO, YA LO MEJORARE ALGUN DIA
	for ($i=0; $i<$NUMERO_EQUIPOS; $i++)
		for ($j=$i+1; $j<$NUMERO_EQUIPOS; $j++)					
			$res[]=array ($equipos[$i]["id"], $equipos[$j]["id"]);
	shuffle ($res);
	return $res;
}

function crear_liga ($equipos, $BD)
{
	$EMPAREJAMIENTOS = array (count($equipos));
				
	// REALIZAR EMPAREJAMIENTOS								
	foreach (sortear_liga ($equipos) as $emparejamiento)
	{
		$tmp=array (	
			"id_1" => $emparejamiento[0],
			"id_2" => $emparejamiento[1],			
			"ganador" => -1,
			"id_juego" => $equipos[0]["id_juego"],
		);
		$EMPAREJAMIENTOS[]= $BD->putTabla ("encuentros", $tmp);
	}
	
	return $EMPAREJAMIENTOS;
}

function crear_eliminatoria ($equipos, $BD)
{
	$NUMERO_EQUIPOS = count ($equipos);
	$RONDAS = ceil(log ($NUMERO_EQUIPOS ,2));
	$MAXIMOS_EQUIPOS = pow (2, $RONDAS);	
	$PARTIDOS_PRIMERA_RONDA=pow (2, $RONDAS-1);		
			
	$EMPAREJAMIENTOS = array();	
	$EMPAREJAMIENTOS[0] = $RONDAS;
					
	// SI EL NUMERO DE EQUIPOS NO LLENA EL CUADRO LOS BALANCEO, COLOCO UN 
	// EQUIPO EN CADA RAMA Y LUEGO VOY RELLENANDO TANTAS RAMAS COMO SEA POSIBLE
	$tmparray = array_fill(1, $MAXIMOS_EQUIPOS-1, -1);
		
	for ($i=0; $i<($MAXIMOS_EQUIPOS/2); $i++)
		$tmparray[$i*2] = $equipos[$i]["id"];
	for ($i=0; $i<($MAXIMOS_EQUIPOS/2); $i++)
		$tmparray[$i*2 + 1] = -1;
	for ($i=0; $i<($NUMERO_EQUIPOS - $MAXIMOS_EQUIPOS/2); $i++)
		$tmparray[$i*2 + 1] = $equipos[$MAXIMOS_EQUIPOS/2 + $i]["id"];		
		
	// CREO LOS NODOS DE LOS ENCUENTROS
	for ($i=1;$i<$MAXIMOS_EQUIPOS;$i++)
	{
		$tmp=array (
			"id_1" => -1,
			"id_2" => -1,
			"ganador" => -1,
			"id_juego" => $equipos[0]["id_juego"],
		);
		
		// SI ESTOY EN LA PRIMERA RONDA DEL TORNEO METO LOS DATOS
		if ($i >= ($MAXIMOS_EQUIPOS/2))
		{		
			$id_1= $i*2-$MAXIMOS_EQUIPOS;
			$tmp["id_1"]= $tmparray[$id_1];
			$tmp["id_2"]= $tmparray[$id_1+1];
			
			if ($tmp["id_2"] == "-1")
			{
				$tmp["ganador"]=1;
				$tmp["fecha"]=time();
				
				if ($i % 2)
					$array_emparejamientos[floor($i/2)]["id_2"]=$tmp["id_1"];
				else
					$array_emparejamientos[floor($i/2)]["id_1"]=$tmp["id_1"];
			}
		}
		$array_emparejamientos[$i]=$tmp;		
	}
	
	// METO LOS ENCUENTROS EN LA BASE DE DATOS
	for ($i=1;$i<$MAXIMOS_EQUIPOS;$i++)
	{
		$EMPAREJAMIENTOS[$i]= $BD->putTabla ("encuentros", $array_emparejamientos[$i]);
	}	
		
	ksort ($EMPAREJAMIENTOS);	
	return $EMPAREJAMIENTOS;
}

function calcular_clasificacion ($partidos, $BD)
{
	$tabla = array ();
	// CALCULO LA CLASIFICACION
	for ($i=1; $i < count ($partidos); $i++)
	{
		$res = $BD->getTabla ("encuentros", "id", $partidos[$i]);
		
		if ($res[0]["ganador"] != -1)
		{
			if ($res[0]["ganador"] == 1)
			{
				$tabla[$res[0]["id_1"]][1]++;
				$tabla[$res[0]["id_2"]][-1]++;
				$tabla[$res[0]["id_1"]]["puntos"]+=3;
			}else if ($res[0]["ganador"] == 2)
			{
				$tabla[$res[0]["id_2"]][1]++;
				$tabla[$res[0]["id_1"]][-1]++;
				$tabla[$res[0]["id_2"]]["puntos"]+=3;
			}else {				
				$tabla[$res[0]["id_1"]][0]++;
				$tabla[$res[0]["id_2"]][0]++;
				$tabla[$res[0]["id_1"]]["puntos"]+=1;
				$tabla[$res[0]["id_2"]]["puntos"]+=1;
			}				
		}else
		{
				$tabla[$res[0]["id_1"]][1] +=0;
				$tabla[$res[0]["id_1"]][-1] +=0;
				$tabla[$res[0]["id_1"]][0] +=0;
				$tabla[$res[0]["id_2"]][1] +=0;
				$tabla[$res[0]["id_2"]][-1] +=0;
				$tabla[$res[0]["id_2"]][0] +=0;
				$tabla[$res[0]["id_1"]]["puntos"]+=0;
				$tabla[$res[0]["id_2"]]["puntos"]+=0;
		}
	}
	return $tabla;
}

function nombre_ronda ($ronda)
{
	$nombres = array (
		"final", "semifinal", "cuartos de final", "octavos de final", "dieciseisavos de final", "32º de final", "64º de final", "ronda previa"
	);
	
	if ($ronda<count($nombres))
		return $nombres[$ronda];
	else
		return $nombres[count($nombres)-1];
}

function devolver_ganador ($encuentro, $BD)
{
	$res = $BD->getTabla ("encuentros", "id", $encuentro);
	
	if ($res[0]["ganador"]==1)
		$id = $res[0]["id_1"];
	else if ($res[0]["ganador"]==2)
		$id = $res[0]["id_2"];
	else 
		return false;
	
	$res= $BD->getTabla ("equipos", "id", $id);		
	return $res[0];
}

function devolver_inicio_ronda_losers ($ronda)
{
	$ronda--;
	$ret = 0;
	$n=0;
		
	for ($i=0; $i<$ronda/2 && $n<$ronda; $i++)
		for ($j=0;$j<2 && $n++<$ronda; $j++)
		{
			$inc = pow (2, $i);
			$ret += $inc;			
		}	
	return $ret+1;
}

// RONDA DEL NODO 
function devolver_ronda_losers ($nodo)
{
	$n=0;
		
	for ($i=0; $nodo>0; $i++)
		for ($j=0;$j<2 && $nodo>0; $j++)
		{
			$n++;
			$inc = pow (2, $i);			
			$nodo -= $inc;
		}		
	return $n;
}

?>