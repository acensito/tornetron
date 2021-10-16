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

class bd
{	
	var $link;

	function bd ($dbhost, $dbname, $dbuser, $dbpasswd)
	{						
		@$this->link = mysql_connect ($dbhost , $dbuser, $dbpasswd)
			or die ("ERROR CONECTANDO A LA BASE DE DATOS");
		
		mysql_select_db ($dbname)
			or die ("ERROR LEYENDO LA BD");									
	}
	
	// OBTIENE UNA TABLA Y LA DEVUELVE COMO UN ARRAY. SI SE LE PASA $campo Y $valor SOLO DEVUELVE LAS ENTRADAS CUYO CAMPO $campo VALGA $valor
	function getTabla ($tabla, $campo="", $valor="", $extras="")
	{
		$query = "SELECT * FROM $tabla";
		
		if ($campo != "" && $valor != "")
			$query .= " WHERE $campo = '$valor'";
					
		if ($extras != "")
			$query .= " $extras";
					
		$idres = mysql_query ($query ,$this->link);
		
		$resultado = array();
		@$res= mysql_fetch_array ($idres);
		while ($res)
		{			
			$resultado[]=$res;
			@$res= mysql_fetch_array ($idres);
		}
	
		return $resultado;
	}
	
	// METE EL ARRAY ASOCIATIVO QUE SE LE PASA EN LA TABLA
	function putTabla ($tabla, $array)
	{		
		$valores= implode ("','", $array);
		$campos= implode (",", array_keys ($array));
				
		$query = "INSERT INTO $tabla ($campos) VALUES ('$valores')";							
		mysql_query ($query ,$this->link);
		return (mysql_insert_id ($this->link));
	}
	
	// METE EL ARRAY ASOCIATIVO QUE SE LE PASA EN LA TABLA UPDATEANDO EL VALOR QUE COINCIDE CON $valor 
	function editTabla ($tabla, $campo, $valor, $array)
	{								
		foreach ($array as $key=>$value)			
		{				
			$query = "UPDATE $tabla SET $key='$value' WHERE $campo='$valor'";	
			mysql_query ($query ,$this->link);
		}
		
	}
	
	// BORRA LOS DATOS DE UNA TABLA QUE COINCIDAN $valor Y CAMPO
	function delTabla ($tabla, $campo, $valor)
	{										
		$query = "DELETE FROM $tabla WHERE $campo='$valor'";
		mysql_query ($query ,$this->link);
	}		
}

?>