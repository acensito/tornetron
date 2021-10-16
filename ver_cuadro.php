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
	
	
	function calcular_equipo_nodo ($CUADRO, $BD, $ronda, $j, $i, $NOMBRES, $tipo_torneo, $RAMA)
	{	
		global $CUADRO_ANCHO, $CUADRO_ALTO, $ESPACIO_ANCHO, $X,	$Y, $FUENTE, $FUENTE_ALTO, $FUENTE_ANCHO, $img, $color1, $FASE;
		
		for ($i=0; $i < pow(2,$ronda-$j-1); $i++)
		{				
			if ($RAMA == 1)
				$nodo = pow (2,$ronda-$j-1) + floor($i/2);
			else
				$nodo = pow (2,$ronda-$j-1) + floor($i/2) + pow (2,$ronda-$j-2);
			
			$EQUIPO = -1;
			
			// LA PRIMERA COLUMNA ES ESPECIAL PQ SOLO SON LOS EQUIPOS, NO UN NODO
			if ($j == 0)					
			{
				$res_enc = $BD->getTabla ("encuentros", "id", $CUADRO[$nodo]);
				if ($res_enc[0]["id_1"] != "-1" && $res_enc[0]["id_2"] == "-1" && $res_enc[0]["ganador"]!=-1)
					continue;
			
				// SI SOLO HAY UNA RONDA
				if ($ronda == 1)
				{
					if ($RAMA == 1)
						$EQUIPO = $res_enc[0]["id_1"];
					else
						$EQUIPO = $res_enc[0]["id_2"];
				}else
				{
					if ($i%2)
						$EQUIPO = $res_enc[0]["id_2"];
					else
						$EQUIPO = $res_enc[0]["id_1"];
				}
			}else					
			// NO ES LA PRIMERA COLUMNA
			{ 
				$res_enc = $BD->getTabla ("encuentros", "id", $CUADRO[$nodo *2 + ($i%2)]);
				
				// OBTENGO EL EQUIPO GANADOR
				if ($res_enc[0]["ganador"] == 1)
					$EQUIPO = $res_enc[0]["id_1"];
				else if ($res_enc[0]["ganador"] == 2)
					$EQUIPO = $res_enc[0]["id_2"];									
			}
							
			// UN MODO CUADRADO DE COLOCAR EL CUADRO
			$y = $Y +($i * 2 * pow (2,$j) + pow (2,$j)-1) * $CUADRO_ALTO;
								
			imagerectangle ($img, $X, $y, $X + $CUADRO_ANCHO, $y + $CUADRO_ALTO, $color1);
	
			// EL TEXTO A ESCRIBIR 			
			
			// TORNEO SIN EMPAREJAR									
			if ($tipo_torneo == 3 && $FASE == 0 && $j == 0)
			{								
				$cad = $NOMBRES[$i + pow (2,$ronda-1) * ($RAMA-1)];				
			}else
			{					
				$res2 = $BD->getTabla ("equipos", "id", $EQUIPO);
				$cad = substr ($res2[0]["nombre_clan"],0,$CUADRO_ANCHO/$FUENTE_ANCHO-1);
			}							
			imagestring ($img, $FUENTE, $X+($CUADRO_ANCHO-strlen($cad)*$FUENTE_ANCHO)/2, $y+($CUADRO_ALTO-$FUENTE_ALTO)/2 , $cad , $color2);
			//imagestring ($img, $FUENTE, $X+5, $y+($CUADRO_ALTO-$FUENTE_ALTO)/2 , $cad , $color2);
									
			// LA LINEA HORIZ DERECHA						
			if ($j>1 || $RAMA==1 || ($j==1 && !($res_enc[0]["id_2"] == "-1" && $res_enc[0]["id_1"] != "-1" && $res_enc[0]["ganador"]!=-1)))
				imageline ($img, $X + $CUADRO_ANCHO, $y + $CUADRO_ALTO/2, $X + $CUADRO_ANCHO + $ESPACIO_ANCHO, $y + $CUADRO_ALTO/2, $color1);
			
			// LA LINEA HORIZ IZQDA				
			if ($j>1 || $RAMA==2 || ($j==1 && !($res_enc[0]["id_2"] == "-1" && $res_enc[0]["id_1"] != "-1" && $res_enc[0]["ganador"]!=-1)))
				imageline ($img, $X, $y + $CUADRO_ALTO/2, $X - $ESPACIO_ANCHO, $y + $CUADRO_ALTO/2, $color1);
				
			
			// LA LINEA VERTICAL
			if ($i%2)
			{
				if ($RAMA == 1)
					imageline ($img, $X + $CUADRO_ANCHO + $ESPACIO_ANCHO, $y + $CUADRO_ALTO/2, $X + $CUADRO_ANCHO + $ESPACIO_ANCHO, $y_anterior, $color1);
				else
					imageline ($img, $X - $ESPACIO_ANCHO, $y + $CUADRO_ALTO/2, $X - $ESPACIO_ANCHO, $y_anterior, $color1);
			}
			else 
				$y_anterior = $y + $CUADRO_ALTO/2;
								
		}
		$X = $X+ $CUADRO_ANCHO + $ESPACIO_ANCHO*2;
	}
	
	
	require "config.php";	
	require "inc/phpcode/bd.php";
	require "inc/phpcode/funciones.php";
	
	//COMPATIBLE CON register_globals=off
	import_request_variables("g", "var_"); 
	
	// ACCEDEMOS A LA BD
	$BD = new bd ($CONFIG["dbhost"], $CONFIG["dbname"], $CONFIG["dbuser"], $CONFIG["dbpasswd"]);	
		
	$CUADRO_ANCHO= 100;	
	$CUADRO_ALTO= 35;
	$ESPACIO_ANCHO= $CUADRO_ANCHO/5;	
	
	$FASE = 0;
	$FUENTE = 2;
	$FUENTE_ALTO = imagefontheight ($FUENTE);
	$FUENTE_ANCHO = imagefontwidth ($FUENTE);
	
	// PUNTO DE COMIENZO
	$X = 5;
	$Y = 5+$FUENTE_ALTO;
	
	
	$res = $BD->getTabla ("juegos", "id", $var_id_juego);
	if (isset ($var_id_juego) && count ($res) == 1 && $res[0]["cuadro"] != "")
	{
		// ELIMINATORIAS 
		if ($res[0]["tipo_torneo"] == 1 || $res[0]["tipo_torneo"] == 3 || $res[0]["tipo_torneo"] == 4)
		{
			$CUADRO = unserialize ($res[0]["cuadro"]) ;			
			
			if ($res[0]["tipo_torneo"] == 4)
				$CUADRO = $CUADRO["winners"];	
			else
			if ($res[0]["tipo_torneo"] == 3)
			{
				// SI AUN NO SE ESTA JUGANDO LA SEGUNDA FASE RELLENO CON NOMBRES CREADOS POR MI
				if ($CUADRO["fase"] == 0)
				{
					$letras = "ABCDEFGH";
					for ($i=0; $i< (count ($CUADRO["ligas"]) * $CUADRO["clasifican"]); $i++)
					{						
						if ($CUADRO["clasifican"] == 1)
							$NOMBRES[$i] = "Primero grupo " . $letras{$i};
						else if ($CUADRO["clasifican"] == 2)
						{
							// LOS SUBCAMPEONES
							if ($i%2)
							{
								$NOMBRES[$i] = "Segundo grupo ";
								if ($i < count ($CUADRO["ligas"]))									
									$NOMBRES[$i] .= $letras{(count ($CUADRO["ligas"])+$i-1)/2};
								else
									$NOMBRES[$i] .= $letras{($i- count ($CUADRO["ligas"])-1)/2};									
							}
							// LOS CAMPEONES
							else
								$NOMBRES[$i] = "Primero grupo " . $letras{$i/2};
						}
					}
				}
				$FASE = $CUADRO["fase"];
				$CUADRO = $CUADRO["eliminatorias"];				
			}
						
			$ronda = $CUADRO[0];
			if ($ronda == 1)
				$Y = $CUADRO_ALTO + $Y;
									
			$IMG_ANCHO = $CUADRO_ANCHO * ($ronda *2 +1 ) + $ESPACIO_ANCHO * ($ronda *4) + $X*2;
			$IMG_ALTO = $CUADRO_ALTO * (pow(2, $ronda) -1) + $Y*2;
			
									
			$img=ImageCreateTrueColor($IMG_ANCHO, $IMG_ALTO);
			$color_fondo = ImageColorAllocate($img, 0xff, 0xff, 0xff);
			imagefill ($img, 0,0,$color_fondo);			
			$color1 = ImageColorAllocate($img, 0xff, 0x7e, 0x00);
			$color2 = ImageColorAllocate($img, 0x00, 0x00, 0x00);
					
			// LADO IZQDO DEL CUADRO
			for ($j=0; $j < $ronda; $j++)
			{	
				if (($ronda- $j-1)>0)
					imagestring ($img, $FUENTE, $X, 0 , strtoupper(nombre_ronda ($ronda- $j-1)) , $color1);
				calcular_equipo_nodo ($CUADRO, $BD, $ronda, $j, $i, $NOMBRES, $res[0]["tipo_torneo"], 1);	
			}
			
			// EL CUADRO DEL GANADOR
			$y= $IMG_ALTO/2;
			imageline ($img, $X, $y, $X - $ESPACIO_ANCHO, $y, $color1);
			imagerectangle ($img, $X, $y - $CUADRO_ALTO, $X + $CUADRO_ANCHO, $y + $CUADRO_ALTO, $color1);
			imageline ($img, $X + $CUADRO_ANCHO, $y, $X + $CUADRO_ANCHO + $ESPACIO_ANCHO, $y, $color1);
			$cad ="CAMPEON";
			imagestring ($img, $FUENTE+1, $X+($CUADRO_ANCHO-strlen($cad)*$FUENTE_ANCHO)/2, $y - $CUADRO_ALTO-$FUENTE_ALTO, $cad, $color1);
									
			$EQUIPO = devolver_ganador ($CUADRO[1], $BD);
					
			//SI HAY GANADOR	
			if ($EQUIPO)
			{
				$res2 = $BD->getTabla ("equipos", "id", $EQUIPO["id"]);	
				$cad = substr ($res2[0]["nombre_clan"],0,$CUADRO_ANCHO/$FUENTE_ANCHO-1);								
				imagestring ($img, $FUENTE, $X+($CUADRO_ANCHO-strlen($cad)*$FUENTE_ANCHO)/2, $y-$FUENTE_ALTO/2, $cad , $color1);
			}
			
			$X = $X+ $CUADRO_ANCHO + $ESPACIO_ANCHO*2;						
			
			// LADO DERECHO DEL CUADRO
			for ($j=$ronda-1; $j >= 0; $j--)
			{				
				if (($ronda- $j-1)>0)
					imagestring ($img, $FUENTE, $X, 0 , strtoupper(nombre_ronda ($ronda- $j-1)) , $color1);
				calcular_equipo_nodo ($CUADRO, $BD, $ronda, $j, $i, $NOMBRES,$res[0]["tipo_torneo"], 2);
			}
			
			header("Content-type: image/png");
			imagepng ($img);
		}
	}
?>