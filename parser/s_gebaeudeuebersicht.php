<?php
/*****************************************************************************/
/* s_gebaeudeuebersicht.php                                                  */
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

if (!defined('DEBUG_LEVEL'))
	define('DEBUG_LEVEL', 0);

if (basename($_SERVER['PHP_SELF']) != "index.php")
  die('Hacking attempt...!!');

if (!defined('IRA'))
	die('Hacking attempt...');

//*****************************************************************************
//
// Spielerinfo scannen
function parse_gebaeudeuebersicht($scanlines) {
	global $db;

	$state = 0;
	$scan_data = array();
	foreach ($scanlines as $scanline) {
		if (str_replace(' ', '', $scanline) == '')
			continue;
		debug_var('scanline', $scanline);
		switch ($state) {
		case 0: // 1. �berschrift
			if (preg_match('/Gebäudeübersicht/', $scanline, $match) > 0)
				debug_var("$state", ++$state);
			break;
		case 1: // Kategorie oder Tabellenueberschrift oder Zeile
			// (Kolonie) 3:129:1
			if (preg_match('/\((.*)\)\s(\d+):(\d+):(\d+)/', $scanline, $match) > 0) {
				debug_var("scan_data['planet'][$coords]['typ']", $scan_data['planet'][$coords]['typ'] = $match[1]);
				debug_var("coords", $coords = $match[2] . ":" . $match[3] . ":" . $match[4]);
				debug_var("scan_data['planet'][$coords]", $scan_data['planet'][$coords] = array('gal' => $match[2], 'sys' => $match[3], 'planet' => $match[4]));
			// 3:192:1
			} else if (preg_match('/.*\s(\d+):(\d+):(\d+)/', $scanline, $match) > 0) {
				debug_var("coords", $coords = $match[1] . ":" . $match[2] . ":" . $match[3]);
				debug_var("scan_data['planet'][$coords]", $scan_data['planet'][$coords] = array('gal' => $match[1], 'sys' => $match[2], 'planet' => $match[3]));
			// (Kolonie)
			} else if (substr($scanline, 0, 1) == "(" && substr($scanline, -1) == ")") {
				debug_var("scan_data['planet'][$coords]['typ']", $scan_data['planet'][$coords]['typ'] = $scanline);
			// Summe
			} else if (preg_match('/(Summe)/', $scanline, $match) > 0) {
				debug_var("summe", $summe = $match[1]);
			// orbitales Teleskop	1	2	3	4
			// Beobachtung
			} else {
				if (isset($scan_data['planet'])) {
					$tokens = explode(" ", $scanline);
					$count = count($scan_data['planet']);
					$coords = array_keys($scan_data['planet']);
					$index = 0;
					$name = "";
					$counts = array();
					foreach (array_reverse($tokens) as $element) {
						if ($index < $count && !empty($element) && is_numeric($element)) {
							$coord = $coords[$count - $index - 1];
							$counts[$coord] = $element;
						} else {
							if (!empty($name))
								$name = $element . " " . $name;					
							else
								$name = $element;
						}
						$index++;
					}
					debug_var("counts", $counts);
					if (count($counts)) {
						foreach ($counts as $coord => $count)
							debug_var("scan_data['gebaeude'][$category][$name][$coord]", $scan_data['gebaeude'][$category][$name][$coord] = $count);
					} else 	
						debug_var("category", $category = $scanline);
				} else
					debug_var("category", $category = $scanline);
			}
			break;			
		}
	}
	debug_var("insert_data", $result = insert_data_gebaeudeuebersicht($scan_data));
	if ($result)
		echo "<div class='system_notification'>Gebäudeübersicht aktualisiert.</div>";
}

function insert_data_gebaeudeuebersicht($scan_data) {
	global $db, $db_tb_gebaeude_spieler, $selectedusername;
	$count = 0;
	if (!isset($scan_data['gebaeude']))
		return false;
	foreach ($scan_data['planet'] as $coords => $planet) {
		$sql = "DELETE FROM prefix_gebaeude_spieler";
		$sql .= " WHERE coords_gal=" . $planet['gal'];
		$sql .= " AND coords_sys=" . $planet['sys'];
		$sql .= " AND coords_planet=" . $planet['planet'];
		debug_var('sql', $sql);
		$db->db_query($sql)
			or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
	}
	foreach ($scan_data['gebaeude'] as $category => $buildings) {
		foreach ($buildings as $building => $planets) {
			foreach ($planets as $coords => $count) {
				$sql = "INSERT INTO prefix_gebaeude_spieler (";
				$sql .= "coords_gal,coords_sys,coords_planet,kolo_typ,user,category,building,count,time";
				$sql .= ") VALUES (";
				$sql .= $scan_data['planet'][$coords]['gal'];
				$sql .= "," . $scan_data['planet'][$coords]['sys'];
				$sql .= "," . $scan_data['planet'][$coords]['planet'];
				$sql .= ",'" . $scan_data['planet'][$coords]['typ'] . "'";
				$sql .= ",'" . $selectedusername . "'";
				$sql .= ",'" . $category . "'";
				$sql .= ",'" . $building . "'";
				$sql .= "," . $count;
				$sql .= "," . time() . ")";
				debug_var('sql', $sql);
				$db->db_query($sql)
					or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
				$count++;
			}
		}
	}
	return $count;
}

// ****************************************************************************
// Gibt den Wert einer Variablen aus.
if (!defined('DEBUG_VAR')) {
	define('DEBUG_VAR', true);
	function debug_var($name, $wert, $level = 2) {
		if (DEBUG_LEVEL >= $level) {
			echo "<div class='system_debug_blue'>" . $name . ":'";
			if (is_array($wert))
				print_r($wert);
			else
				echo $wert;
			echo "'</div>";
		}
	}
}
?>