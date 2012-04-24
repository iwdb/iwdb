<?php
/*****************************************************************************/
/* s_spielerinfo.php                                                         */
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

/*****************************************************************************/
/* Diese Erweiterung der ursp�nglichen DB ist ein Gemeinschafftsprojekt von  */
/* IW-Spielern.                                                              */
/* Bei Problemen kannst du dich an das eigens daf�r eingerichtete            */
/* Entwicklerforum wenden:                                                   */
/*                                                                           */
/*                   http://www.iw-smf.pericolini.de                         */
/*                                                                           */
/*****************************************************************************/

if (basename($_SERVER['PHP_SELF']) != "index.php")
  die('Hacking attempt...!!');

if (!defined('IRA'))
	die('Hacking attempt...');

include_once("./includes/debug.php");

if (!defined('DEBUG_LEVEL'))
	define('DEBUG_LEVEL', 0);

$fehlscans = array();

//*****************************************************************************
//
// Fehlgeschlagene Sondierung scannen
function parse_fehlscan($scanlines) {
	global $db, $fehlscans;

	$scan_data = array();
	foreach ($scanlines as $scanline) {
		if (preg_match('/Sondierung des Planeten (\d+):(\d+):(\d+)/', $scanline, $match) > 0) {
			$fehlscan["coords"] = $match[1] . ":" . $match[2] . ":" . $match[3];
			$fehlscan["gal"] = $match[1];
			$fehlscan["sys"] = $match[2];
			$fehlscan["planet"] = $match[3];
		}
		if (preg_match('/(\d+)\.(\d+).(\d+)\s(\d+):(\d+):(\d+)/', $scanline, $match) > 0)
			$fehlscan["time"] = mktime($match[4], $match[5], $match[6], $match[2], $match[1], $match[3]);
	}
	if (isset($fehlscan)) {
		$fehlscans[] = $fehlscan;
		echo "<div class='system_notification'>Fehlgeschlagene Sondierung erkannt.</div><br>";
	} else {
		echo "<div class='system_notification'>Fehlgeschlagene Sondierung nicht erkannt.</div><br>";
	}
}

function finish_fehlscan() {
	global $fehlscans, $db, $db_tb_scans, $db_tb_lieferung, $sid;

	echo '<form id="fehlscan_form" method="POST" action="index.php?action=m_raid&amp;sid=' . $sid . '" enctype="multipart/form-data"><p>' . "\n";
	echo '<input type="hidden" name="fehlscan" id="fehlscan_count" value="' . count($fehlscans) . '">';
	echo '<tr class="windowbg1"\>';
	echo '<td colspan=2>';

	start_table();
	start_row("titlebg", "colspan=\"7\"");
	echo "<b>Fehlgeschlagene Sondierungen</b>";
	next_row("windowbg2", "");
	echo "Planet";
	next_cell("windowbg2", "");
	echo "Spieler";
	next_cell("windowbg2", "");
	echo "Allianz";
	next_cell("windowbg2", "");
	echo "Uhrzeit";
	next_cell("windowbg2", "");
	echo "X11";
	next_cell("windowbg2", "");
	echo "Terminus";
	next_cell("windowbg2", "");
	echo "X13";
	$index = 1;
	foreach ($fehlscans as $fehlscan) {
		// Abfragen der letzten Sondierung
		$x11 = "";
		$terminus = "";
		$x13 = "";
		$sql = "SELECT * FROM " . $db_tb_lieferung . " WHERE coords_to_gal=" . $fehlscan["gal"] . " AND coords_to_sys=" . $fehlscan["sys"] . " AND coords_to_planet=" . $fehlscan["planet"] . " AND art='Sondierung' ORDER BY time DESC";
		$result = $db->db_query($sql)
			or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
		if ($row = $db->db_fetch_array($result)) {
			debug_var("row['schiffe']", $row['schiffe']);
			if (preg_match('/(\d+)\s+Sonde\s+X11/', $row['schiffe'], $match) > 0)
				debug_var("x11", $x11 = $match[1]);
			elseif (preg_match('/(\d+)\s+Terminus\s+Sonde/', $row['schiffe'], $match) > 0)
				debug_var("terminus", $terminus = $match[1]);
			elseif (preg_match('/(\d+)\s+Sonde\s+X13/', $row['schiffe'], $match) > 0)
				debug_var("x13", $x13 = $match[1]);
		}
		echo '<input type="hidden" name="time_' . $index . '"  id="fehlscan_time_' . $index . '" value="' . $fehlscan["time"] . '">';
		echo '<input type="hidden" name="coords_gal_' . $index . '" id="fehlscan_coords_gal_' . $index . '" value="' . $fehlscan["gal"] . '">';
		echo '<input type="hidden" name="coords_sys_' . $index . '" id="fehlscan_coords_sys_' . $index . '" value="' . $fehlscan["sys"] . '">';
		echo '<input type="hidden" name="coords_planet_' . $index . '" id="fehlscan_coords_planet_' . $index . '" value="' . $fehlscan["planet"] . '">';
		next_row("windowbg1", "");
		echo $fehlscan['coords'];
		$sql = "SELECT * FROM " . $db_tb_scans . " WHERE coords_gal=" . $fehlscan["gal"] . " AND coords_sys=" . $fehlscan["sys"] . " AND coords_planet=" . $fehlscan["planet"];
		$result = $db->db_query($sql)
			or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
		if ($row = $db->db_fetch_array($result)) {
			$spieler = $row['user'];
			$allianz = $row['allianz'];
		} else {
			$spieler = "&nbsp;";
			$allianz = "&nbsp;";
		}
		next_cell("windowbg1", "");
		echo $spieler;
		next_cell("windowbg1", "");
		echo $allianz;
		next_cell("windowbg1", "");
		echo strftime("%d.%m.%Y %H:%M", $fehlscan["time"]);
		next_cell("windowbg1", "");
		echo '<input type="text" name="x11_' . $index . '" id="fehlscan_x11_' . $index . '" value="' . $x11 . '" style="width: 50">';
		next_cell("windowbg1", "");
		echo '<input type="text" name="terminus_' . $index . '" id="fehlscan_terminus_' . $index . '" value="' . $terminus . '" style="width: 50">';
		next_cell("windowbg1", "");
		echo '<input type="text" name="x13_' . $index++ . '" id="fehlscan_x13_' . $index . '" value="' . $x13 . '" style="width: 50">';
	}
	next_row("windowbg3", "colspan=\"4\"");
	echo "F�r alle dieselbe Sondenzahl �bernehmen:";
	next_cell("windowbg3", "");
	echo "<input type=\"text\" name=\"x11_all\" style=\"width: 50\">";
	next_cell("windowbg3", "");
	echo "<input type=\"text\" name=\"terminus_all\" style=\"width: 50\">";
	next_cell("windowbg3", "");
	echo "<input type=\"text\" name=\"x13_all\" style=\"width: 50\">";
	next_row("titlebg", "colspan=\"7\" align=\"center\"");
	echo "<input type=\"submit\" value=\"abspeichern\" name=\"B1\" class=\"submit\">";
	end_table();
	echo '</form>';
}
?>
