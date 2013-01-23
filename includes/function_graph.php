<?php
/*****************************************************************************/
/* function_graph.php                                                        */
/*****************************************************************************/
/* Iw DB: Icewars geoscan and sitter database                                */
/* Open-Source Project started by Robert Riess (robert@riess.net)            */
/* Software Version: Iw DB 1.00                                              */
/* ========================================================================= */
/* Software Distributed by:    http://lauscher.riess.net/iwdb/               */
/* Support, News, Updates at:  http://lauscher.riess.net/iwdb/               */
/* ========================================================================= */
/* Copyright (c) 2004 Robert Riess - All Rights Reserved                     */
/*****************************************************************************/
/* This program is free software; you can redistribute it and/or modify it   */
/* under the terms of the GNU General Public License as published by the     */
/* Free Software Foundation; either version 2 of the License, or (at your    */
/* option) any later version.                                                */
/*                                                                           */
/* This program is distributed in the hope that it will be useful, but       */
/* WITHOUT ANY WARRANTY; without even the implied warranty of                */
/* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General */
/* Public License for more details.                                          */
/*                                                                           */
/* The GNU GPL can be found in LICENSE in this directory                     */
/*****************************************************************************/

function build_graph($users, $table, $user_col, $date_col, $value_col, $fitthis)
{
	global $db, $config_xsize, $config_ysize;

	$config_borderleft = 100;
	$config_borderright = 150;
	$config_bordertop = 20;
	$config_borderbottom = 35;

	$where = "";
	if ( ( ! empty($fitthis) ) && ( count($users) > 0 ) )
	{
		foreach($users as $userid)
		{
			$where = ( empty($where) ) ? " WHERE " . $user_col . " = '" . $userid . "'": $where . " OR " .$user_col . " = '" . $userid . "'";
		}
	}

	$sql = "SELECT max(" . $value_col . "), min(" . $value_col . "), max(" . $date_col . "), min(" . $date_col . ") FROM " . $table . $where;
	$result = $db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
	$row = $db->db_fetch_array($result);

	$value_max = $row['max(' . $value_col . ')'];
	$value_min = $row['min(' . $value_col . ')'];
	$value_grid = ($config_ysize - $config_bordertop - $config_borderbottom) / ($value_max);
	switch ($value_col)
	{
		case "ptag" : $value_text = "P/Tag"; break;
		case "gesamtp" : $value_text = "GesamtP"; break;
		case "fp" : $value_text = "FP"; break;
		case "gebp" : $value_text = "GebP"; break;
		default:  $value_text = $value_col; break;
	}

	$date_max = $row['max(' . $date_col . ')'];
	$date_min = $row['min(' . $date_col . ')'];
  if($date_max == $date_min) {
  	$date_grid = ($config_xsize - $config_borderleft - $config_borderright);
  } else {
  	$date_grid = ($config_xsize - $config_borderleft - $config_borderright) / ($date_max - $date_min);
  }

	$user_max = count($users);

	$graph = @ImageCreate($config_xsize, $config_ysize)
		or error(GENERAL_ERROR, 'Could not create new GD image.', '', __FILE__, __LINE__, $sql);

	$font_width = ImageFontWidth( 2 );
	$font_height = ImageFontHeight( 2 );

	//$background_color = ImageColorAllocate($graph, 234, 235, 255);
	$background_color = ImageColorAllocate($graph, 255, 255, 255);
	$text_color = ImageColorAllocate($graph, 0, 0, 0);

	// Koordinatensystem //
		// x-line
	ImageLine( $graph, $config_borderleft - 5, $config_ysize - $config_borderbottom + 1, $config_xsize - $config_borderright, $config_ysize - $config_borderbottom + 1, $text_color );
		// Pfeilspitze
	ImageFilledPolygon( $graph, array($config_xsize - $config_borderright, $config_ysize - $config_borderbottom + 1, $config_xsize - $config_borderright - 5, $config_ysize - $config_borderbottom - 2, $config_xsize - $config_borderright - 5, $config_ysize - $config_borderbottom + 4), 3, $text_color);
		// Beschriftung
	$zeit = strftime(CONFIG_DATEFORMAT, $date_min); ImageString( $graph, 2, $config_borderleft , $config_ysize - $config_borderbottom + 6, $zeit, $text_color );
	$zeit = strftime(CONFIG_DATEFORMAT, $date_max); ImageString( $graph, 2, $config_xsize - $config_borderright - strlen($zeit) * $font_width - 5, $config_ysize - $config_borderbottom + 6, $zeit, $text_color );
	$zeit = strftime(CONFIG_DATEFORMAT, ($date_max - $date_min) / 2 + $date_min); ImageString( $graph, 2, ($config_xsize + $config_borderleft - $config_borderright - strlen($zeit) * $font_width) / 2, $config_ysize - $config_borderbottom + 6, $zeit, $text_color );
	ImageLine( $graph, ($config_xsize + $config_borderleft - $config_borderright) / 2, $config_ysize - $config_borderbottom + 1, ($config_xsize + $config_borderleft - $config_borderright) / 2, $config_ysize - $config_borderbottom - 4, $text_color );


		// y-line
	ImageLine( $graph, $config_borderleft - 1, $config_bordertop, $config_borderleft - 1, $config_ysize - $config_borderbottom + 5, $text_color );
		// Pfeilspitze
	ImageFilledPolygon( $graph, array($config_borderleft - 1, $config_bordertop, $config_borderleft - 4, $config_bordertop + 5, $config_borderleft + 2, $config_bordertop + 5), 3, $text_color);
		// Beschriftung
	ImageString( $graph, 2, $config_borderleft + 6, $config_bordertop + 4, $value_text, $text_color );

	for ($c = $config_ysize - $config_borderbottom; $c > $config_bordertop; $c = $c - 64)
	{
		$wert = round(($config_ysize - $config_borderbottom - $c) / $value_grid);
		ImageString( $graph, 2, $config_borderleft - 6 - strlen($wert) * $font_width, $c - $font_height/2 + 1, $wert, $text_color );
		ImageLine( $graph, $config_borderleft - 1, $c + 1, $config_borderleft + 4, $c + 1, $text_color );
	}


	// User Graphen //
	$ic = 0;
	if (count($users) > 0)
	{
		foreach($users as $userid)
		{
			$xbefore = "";
			$ybefore = "";
			$ic++;
	
			$i = 1530 / $user_max * $ic;
			$gruen = ($i < 256) ? $i: (($i < 766) ? 255: (($i < 1021) ? 254 - ($i - 766) : 0));
			$blau = ($i < 511) ? 0: (($i < 766) ? $i - 510: (($i < 1276) ? 255 : 254 - ($i - 1276)));
			$rot = ($i < 256) ? 255: (($i < 511) ? 254 - ($i - 256): (($i < 1021) ? 0 : (($i < 1276) ? $i - 1020: 255)));
			$color = ImageColorAllocate($graph, $rot, $gruen, $blau);
	
			$sql = "SELECT " . $value_col . ", " . $date_col . " FROM " . $table . " WHERE " . $user_col . " = '" . $userid . "' ORDER BY " . $date_col;
			$result = $db->db_query($sql)
				or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
			while ( $row = $db->db_fetch_array($result) )
			{
				$xactual = $config_borderleft + ($row[$date_col] - $date_min) * $date_grid;
				$yactual = $config_ysize - $config_borderbottom - $row[$value_col] * $value_grid;
	
	
				if ( ! empty($xbefore) )
				{
					ImageLine( $graph, $xbefore, $ybefore, $xactual, $yactual, $color );
				}
				$xbefore = $xactual;
				$ybefore = $yactual;
			}
			ImageFilledRectangle( $graph, $config_xsize - $config_borderright + 20, $config_ysize - $config_borderbottom - 18 - ($font_height + 5) * $ic, $config_xsize - $config_borderright + 20 + $font_height, $config_ysize - $config_borderbottom - 22 - ($font_height + 5) * $ic + $font_height, $color );
			ImageString( $graph, 2, $config_xsize - $config_borderright + 25 + $font_height, $config_ysize - $config_borderbottom - 20 - ($font_height + 5) * $ic, $userid, $text_color );
		}
		ImageGif($graph, "graph.gif");
		echo "<img src=\"graph.gif\" border=\"0\" alt=\"Graph\">";
	}
	else echo "<div class='system_error'>Du musst mindestens einen User auswaehlen.</div>";

	if (count($users) > 22) echo "<div class='system_notification'>Du solltest weniger User auswaehlen.</div>";


	ImageDestroy($graph);
}