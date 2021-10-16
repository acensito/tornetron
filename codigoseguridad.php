<?php // UPDATE 2006-05-03
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


session_start ();
$_SESSION['numero_seguridad'] = sprintf ("%04d", rand(0,9999));
					
header("Content-type: image/jpg");

$img = imagecreatetruecolor (55, 30);
$get_color = imagecolorallocate ($img, 255, 0,0);
$color_relleno = ImageColorAllocate($img, 128, 128, 128);			
imagefill ($img,0,0,$color_relleno);
imageline ($img, 0, 15, 55, 15, $get_color);
imagestring ( $img, 5, 10, 5, $_SESSION['numero_seguridad'], $get_color);
imagejpeg($img,'',45);	


?>