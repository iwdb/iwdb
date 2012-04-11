<?php
/*****************************************************************************/
/* s_flotten.php                                                             */
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
// $Id: s_flotten.php 205 2007-04-24 18:54:05Z reuq tgarfeg $

/*****************************************************************************/
/* Diese Erweiterung der urspünglichen DB ist ein Gemeinschafftsprojekt von  */
/* IW-Spielern.                                                              */
/* Bei Problemen kannst du dich an das eigens dafür eingerichtete            */
/* Entwicklerforum wenden:                                                   */
/*                                                                           */
/*                   http://www.iw-smf.pericolini.de                         */
/*                                                                           */
/*****************************************************************************/

define('DEBUG_LEVEL', 0);

$scan_datas = array();

function parse_flotten($scanlines, $eigene = true) {
	global $scan_datas, $db_tb_schiffstyp, $db;
	$state = 0;
	$arts = 'Angriff|Transport|Ressourcen\sabholen|&Uuml;bergabe\s\(tr\sSchiffe\)|&Uuml;bergabe|R&uuml;ckkehr|Stationieren|Sondierung|Ressourcenhandel\s\(ok\)|Ressourcenhandel|Safeflug|Kolonisierung';
	$replaces = array(")" => "\)", "(" => "\(");
	debug_var("sql", $sql = "SELECT * FROM $db_tb_schiffstyp");
	$result = $db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
	while ($row = $db->db_fetch_array($result)) {
		$token = $row['schiff'];
		foreach ($replaces as $from => $to)
			$token = str_replace($from, $to, $token);
		if (isset($schiffstypen))
			$schiffstypen .= "|" . $token;
		else
			$schiffstypen = $token;
	}
	debug_var("schiffstypen", $schiffstypen);
	foreach($scanlines as $scan) {
		if (str_replace(' ', '', $scan) == '')
			continue;
		if ($scan == "Werft- / Schiffbau&uuml;bersicht" ||
		    $scan == "Ausbaustatus" ||
		    $scan == "Allianz Shoutbox")
			break;
		debug_var('scan', $scan);
		switch ($state) {
		// Überschrift
		case 0:
			// (FF/IE) Eigene Flotten
			if (preg_match('/Eigene Flotten/', $scan, $match) > 0)
				debug_var('state', ++$state);
			elseif (preg_match('/Fremde Flotten/', $scan, $match) > 0)
				debug_var('state', ++$state);
			$scan_datas = array();
			break;
		// Tabellenüberschrift
		case 1:
			// (FF/IE) Ziel Start Ankunft Aktionen
			if (preg_match('/Ziel\s+Start\s+Ankunft\s+Aktionen/', $scan, $match) > 0)
				debug_var('state', ++$state);
			break;
		// Ziel-Daten
		// oder Positionsdaten
		// oder Sonden
		case 2:
			// (IE) Schoko (13:103:12) Ascarona (13:103:7)
			if (preg_match('/(.*)\s+\((\d+):(\d+):(\d+)\)\s+(.*)\s+\((\d+):(\d+):(\d+)\)/', $scan, $match) > 0 && !$eigene) {
				debug_echo('if 2.1');
				if (isset($scan_data)) {
					process_scan_data($scan_data);
					$scan_data = array();
				}
				debug_var('planet_to', $scan_data['planet_to'] = $match[1]);
				debug_var('coords_to_gal', $scan_data['coords_to_gal'] = $match[2]);
				debug_var('coords_to_sys', $scan_data['coords_to_sys'] = $match[3]);
				debug_var('coords_to_planet', $scan_data['coords_to_planet'] = $match[4]);
				debug_var('planet_from', $scan_data['planet_from'] = $match[5]);
				debug_var('coords_from_gal', $scan_data['coords_from_gal'] = $match[6]);
				debug_var('coords_from_sys', $scan_data['coords_from_sys'] = $match[7]);
				debug_var('coords_from_planet', $scan_data['coords_from_planet'] = $match[8]);
				debug_var('state', ++$state);
			// 
			} elseif (preg_match("/(\d+)\s(" . $schiffstypen . ")/", $scan, $match) > 0) {
				debug_echo('if 2.2');
				debug_var("schiffe['" . $match[2] . "']", $scan_data['schiffe'][$match[2]] = $match[1]);
			// (IE) 02:26:53 Transport 10.000 Energie
			} elseif (preg_match('/(\d+):(\d+):(\d+)\s+(' . $arts . ')\s+(.*)\s+(Eisen|Stahl|chem\. Elemente|VV4A|Eis|Wasser|Energie)/', $scan, $match) > 0) {
				debug_echo('if 2.3');
				debug_var('dur_hour', $scan_data['dur_hour'] = $match[1]);
				debug_var('dur_min', $scan_data['dur_min'] = $match[2]);
				debug_var('dur_sec', $scan_data['dur_sec'] = $match[3]);
				debug_var('art', $scan_data['art'] = $match[4]);
				debug_var('pos[' . trim($match[6]) . ']', $scan_data['pos'][trim($match[6])] = strip_number($match[5]));
			// (FF/IE) 60.000 Eisen
			} elseif (preg_match('/([^\s]*)\s+(Eisen|Stahl|chem\. Elemente|VV4A|Eis|Wasser|Energie)/', $scan, $match) > 0) {
				debug_echo('if 2.4');
				debug_var('pos[' . trim($match[2]) . ']', $scan_data['pos'][trim($match[2])] = strip_number($match[1]));
			// (FF) 02:31:38 Transport
			} elseif (preg_match('/(\d+):(\d+):(\d+)\s+(' . $arts . ')/', $scan, $match) > 0) {
				debug_echo('if 2.5');
				debug_var('dur_hour', $scan_data['dur_hour'] = $match[1]);
				debug_var('dur_min', $scan_data['dur_min'] = $match[2]);
				debug_var('dur_sec', $scan_data['dur_sec'] = $match[3]);
				debug_var('art', $scan_data['art'] = $match[4]);
			// (FF/IE) Erdbeere (13:114:4)
			} elseif (preg_match('/(.*)\s+\((\d+):(\d+):(\d+)\)/', $scan, $match) > 0) {
				debug_echo('if 2.6');
				if (isset($scan_data)) {
					process_scan_data($scan_data);
					$scan_data = array();
				}
				debug_var('planet_to', $scan_data['planet_to'] = $match[1]);
				debug_var('coords_to_gal', $scan_data['coords_to_gal'] = $match[2]);
				debug_var('coords_to_sys', $scan_data['coords_to_sys'] = $match[3]);
				debug_var('coords_to_planet', $scan_data['coords_to_planet'] = $match[4]);
				debug_var('state', ++$state);
			} else {
				debug_var('state', $state = 2);
			}
			break;
		// Start Ankunft Aktionen-Daten
		case 3:
			// --- Fremde Flotten ---
			// (IE) David 11.06.2008 18:54:32 - 00:13:15 Stationieren
			if (preg_match('/(.*)\s+(\d+)\.(\d+)\.(\d+)\s+(\d+):(\d+):(\d+)\s+-\s+(\d+):(\d+):(\d+)\s+(' . $arts . ')/', $scan, $match) > 0 && !$eigene) {
				debug_echo('if 3.1');
				debug_var('user_from', $scan_data['user_from'] = $match[1]);
				debug_var('day', $scan_data['day'] = $match[2]);
				debug_var('month', $scan_data['month'] = $match[3]);
				debug_var('year', $scan_data['year'] = $match[4]);
				debug_var('hour', $scan_data['hour'] = $match[5]);
				debug_var('min', $scan_data['min'] = $match[6]);
				debug_var('sec', $scan_data['sec'] = $match[7]);
				debug_var('dur_hour', $scan_data['dur_hour'] = $match[8]);
				debug_var('dur_min', $scan_data['dur_min'] = $match[9]);
				debug_var('dur_sec', $scan_data['dur_sec'] = $match[10]);
				debug_var('art', $scan_data['art'] = $match[11]);
				debug_var('state', $state = 2);
			// --- Ohne Umbruch bei JS-Countern ---
			// (IE) Hellpower 25.08.2008 17:06:57
			} elseif (preg_match('/(.*)\s+(\d+)\.(\d+)\.(\d+)\s+(\d+):(\d+):(\d+)/', $scan, $match) > 0 && !$eigene) {
				debug_echo('if 3.2');
				debug_var('user_from', $scan_data['user_from'] = $match[1]);
				debug_var('day', $scan_data['day'] = $match[2]);
				debug_var('month', $scan_data['month'] = $match[3]);
				debug_var('year', $scan_data['year'] = $match[4]);
				debug_var('hour', $scan_data['hour'] = $match[5]);
				debug_var('min', $scan_data['min'] = $match[6]);
				debug_var('sec', $scan_data['sec'] = $match[7]);
				debug_var('state', $state = 2);
			// (IE) Schoko (13:103:12) 20.05.2008 19:03:39 - 00:41:47 Transport 15.000 Eisen 
			// ' Endor IX (3:189:9) 01.09.2008 06:08:39 -  Transport 40.000 Eisen '
			} elseif (preg_match('/(?:\(.*\))?\s+(.*)\s+\((\d+):(\d+):(\d+)\)\s+(\d+)\.(\d+)\.(\d+)\s+(\d+):(\d+):(\d+)\s+-\s+(\d+):(\d+):(\d+)\s+(' . $arts . ')\s+([^\s]*)\s+(.*)\s*/', $scan, $match) > 0) {
				debug_echo('if 3.3');
				debug_var('planet_from', $scan_data['planet_from'] = $match[1]);
				debug_var('coords_from_gal', $scan_data['coords_from_gal'] = $match[2]);
				debug_var('coords_from_sys', $scan_data['coords_from_sys'] = $match[3]);
				debug_var('coords_from_planet', $scan_data['coords_from_planet'] = $match[4]);
				debug_var('day', $scan_data['day'] = $match[5]);
				debug_var('month', $scan_data['month'] = $match[6]);
				debug_var('year', $scan_data['year'] = $match[7]);
				debug_var('hour', $scan_data['hour'] = $match[8]);
				debug_var('min', $scan_data['min'] = $match[9]);
				debug_var('sec', $scan_data['sec'] = $match[10]);
				debug_var('dur_hour', $scan_data['dur_hour'] = $match[11]);
				debug_var('dur_min', $scan_data['dur_min'] = $match[12]);
				debug_var('dur_sec', $scan_data['dur_sec'] = $match[13]);
				debug_var('art', $scan_data['art'] = $match[14]);
				debug_var('pos[' . trim($match[16]) . ']', $scan_data['pos'][trim($match[16])] = strip_number($match[15]));
				debug_var('state', $state = 2);
			// (FF) Schoko (13:103:12) 20.05.2008 19:03:39 - 01:01:58 Transport
			} elseif (preg_match('/(?:\(.*\))?\s+(.*)\s+\((\d+):(\d+):(\d+)\)\s+(\d+)\.(\d+)\.(\d+)\s+(\d+):(\d+):(\d+)\s+-\s+(\d+):(\d+):(\d+)\s+(' . $arts . ')/', $scan, $match) > 0) {
				debug_echo('if 3.4');
				debug_var('planet_from', $scan_data['planet_from'] = $match[1]);
				debug_var('coords_from_gal', $scan_data['coords_from_gal'] = $match[2]);
				debug_var('coords_from_sys', $scan_data['coords_from_sys'] = $match[3]);
				debug_var('coords_from_planet', $scan_data['coords_from_planet'] = $match[4]);
				debug_var('day', $scan_data['day'] = $match[5]);
				debug_var('month', $scan_data['month'] = $match[6]);
				debug_var('year', $scan_data['year'] = $match[7]);
				debug_var('hour', $scan_data['hour'] = $match[8]);
				debug_var('min', $scan_data['min'] = $match[9]);
				debug_var('sec', $scan_data['sec'] = $match[10]);
				debug_var('dur_hour', $scan_data['dur_hour'] = $match[11]);
				debug_var('dur_min', $scan_data['dur_min'] = $match[12]);
				debug_var('dur_sec', $scan_data['dur_sec'] = $match[13]);
				debug_var('art', $scan_data['art'] = $match[14]);	
				debug_var('state', $state = 2);
			// --- Mit Umbruch bei JS-Countern ---
			// (FF) Schoko (13:103:12) 20.05.2008 19:03:39
			} elseif (preg_match('/(?:\(.*\))?\s+(.*)\s+\((\d+):(\d+):(\d+)\)\s+(\d+)\.(\d+)\.(\d+)\s+(\d+):(\d+):(\d+)/', $scan, $match) > 0) {
				debug_echo('if 3.5');
				debug_var('planet_from', $scan_data['planet_from'] = $match[1]);
				debug_var('coords_from_gal', $scan_data['coords_from_gal'] = $match[2]);
				debug_var('coords_from_sys', $scan_data['coords_from_sys'] = $match[3]);
				debug_var('coords_from_planet', $scan_data['coords_from_planet'] = $match[4]);
				debug_var('day', $scan_data['day'] = $match[5]);
				debug_var('month', $scan_data['month'] = $match[6]);
				debug_var('year', $scan_data['year'] = $match[7]);
				debug_var('hour', $scan_data['hour'] = $match[8]);
				debug_var('min', $scan_data['min'] = $match[9]);
				debug_var('sec', $scan_data['sec'] = $match[10]);
				debug_var('state', $state = 2);
			} else {
				debug_echo('if 3.6');
				debug_var('state', $state = 2);
			}
			break;
		// Noch ein state für fremde flotten
		case 4:
			debug_var('state', $state = 2);
			break;
		}
	}
	if (isset($scan_data))
		$scan_data = process_scan_data($scan_data);
	display_flotten();
}

function process_scan_data($scan_data) {
	global $db, $db_tb_scans, $scan_datas;

	debug_var('process_scan_data', $scan_data);
	if (isset($scan_data['coords_to_gal']) && is_numeric($scan_data['coords_to_gal']) &&
	    isset($scan_data['coords_to_sys']) && is_numeric($scan_data['coords_to_sys']) &&
	    isset($scan_data['coords_to_planet']) && is_numeric($scan_data['coords_to_planet']) &&
	    isset($scan_data['coords_from_gal']) && is_numeric($scan_data['coords_from_gal']) &&
	    isset($scan_data['coords_from_sys']) && is_numeric($scan_data['coords_from_sys']) &&
	    isset($scan_data['coords_from_planet']) && is_numeric($scan_data['coords_from_planet']) &&
	    isset($scan_data['day']) && is_numeric($scan_data['day']) &&
	    isset($scan_data['month']) && is_numeric($scan_data['month']) &&
	    isset($scan_data['year']) && is_numeric($scan_data['year']) &&
	    isset($scan_data['hour']) && is_numeric($scan_data['hour']) &&
	    isset($scan_data['min']) && is_numeric($scan_data['min']) &&
	    isset($scan_data['sec']) && is_numeric($scan_data['sec']) &&
	    isset($scan_data['art']) &&
	    ($scan_data['art'] == 'Angriff' ||
	     $scan_data['art'] == 'Transport' ||
	     $scan_data['art'] == 'Ressourcen abholen' ||
	     $scan_data['art'] == 'Ressourcenhandel' ||
	     $scan_data['art'] == 'Ressourcenhandel (ok)' ||
	     $scan_data['art'] == '&Uuml;bergabe' ||
	     $scan_data['art'] == 'Stationieren' ||
	     $scan_data['art'] == 'Kolonisation' ||
	     $scan_data['art'] == 'Sondierung')
	) {
		if (!isset($scan_data['user_to'])) {
			$sql = "SELECT user FROM " . $db_tb_scans;
			$sql .= " WHERE coords_gal=" . $scan_data['coords_to_gal'];
			$sql .= " AND coords_sys=" . $scan_data['coords_to_sys'];
			$sql .= " AND coords_planet=" . $scan_data['coords_to_planet'];
			debug_var('sql', $sql);
			$result = $db->db_query($sql)
				or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
			if ($row = $db->db_fetch_array($result))
				debug_var('user_to', $scan_data['user_to'] = $row['user']);
		}
		if (!isset($scan_data['user_from'])) {
			// Von
			$sql = "SELECT user FROM " . $db_tb_scans;
			$sql .= " WHERE coords_gal=" . $scan_data['coords_from_gal'];
			$sql .= " AND coords_sys=" . $scan_data['coords_from_sys'];
			$sql .= " AND coords_planet=" . $scan_data['coords_from_planet'];
			debug_var('sql', $sql);
			$result = $db->db_query($sql)
				or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
			if ($row = $db->db_fetch_array($result))
				debug_var('user_from', $scan_data['user_from'] = $row['user']);
		}
		// Zeitstempel
		$scan_data['time'] = mktime($scan_data['hour'], $scan_data['min'], $scan_data['sec'], $scan_data['month'], $scan_data['day'], $scan_data['year']);
		// Schiffe
		if (!isset($scan_data['schiffe']))
			$scan_data['schiffe'] = array();
		if (isset($scan_data['pos']))
			foreach ($scan_data['pos'] as $typ => $menge)
				if ($typ != 'Eisen' && $typ != 'Stahl' && $typ != 'VV4A' && $typ != 'chem. Elemente' && $typ != 'Eis' && $typ != 'Wasser' && $typ != 'Energie')
					$scan_data['schiffe'][] = $menge . " " . $typ;
		// Daten speichern
		save_data($scan_data);
		// Daten merken
		$scan_datas[] = $scan_data;
	}
	return array();
}

function save_data($scan_data) {
	global $db, $db_tb_lieferung, $db_tb_scans, $scan_datas;
	$fields = array(
		'time' => $scan_data['time'],
		'coords_from_gal' => $scan_data['coords_from_gal'],
		'coords_from_sys' => $scan_data['coords_from_sys'],
		'coords_from_planet' => $scan_data['coords_from_planet'],
		'coords_to_gal' => $scan_data['coords_to_gal'],
		'coords_to_sys' => $scan_data['coords_to_sys'],
		'coords_to_planet' => $scan_data['coords_to_planet'],
		'user_from' => $scan_data['user_from'],
		'user_to' => $scan_data['user_to'],
		'eisen' => isset($scan_data['pos']['Eisen']) ? $scan_data['pos']['Eisen'] : 0,
		'stahl' => isset($scan_data['pos']['Stahl']) ? $scan_data['pos']['Stahl'] : 0,
		'vv4a' => isset($scan_data['pos']['VV4A']) ? $scan_data['pos']['VV4A'] : 0,
		'chem' => isset($scan_data['pos']['chem. Elemente']) ? $scan_data['pos']['chem. Elemente'] : 0,
		'eis' => isset($scan_data['pos']['Eis']) ? $scan_data['pos']['Eis'] : 0,
		'wasser' => isset($scan_data['pos']['Wasser']) ? $scan_data['pos']['Wasser'] : 0,
		'energie' => isset($scan_data['pos']['Energie']) ? $scan_data['pos']['Energie'] : 0,
		'art' => $scan_data['art'],
	);
	if (isset($scan_data['schiffe']))
		foreach ($scan_data['schiffe'] as $name => $anzahl)
			if (isset($fields['schiffe']))
				$fields['schiffe'] .= "<br>" . $anzahl . " " . $name;
			else
				$fields['schiffe'] = $anzahl . " " . $name;		 
	$sql = "INSERT INTO " . $db_tb_lieferung . " (";
	$sql .= implode(array_keys($fields), ",");
	$sql .= ") VALUES (";
	foreach ($fields as $key => $value)
		if (is_numeric($value))
			$inserts[] = $value;
		else
			$inserts[] .= "'" . $value . "'";
	$sql .= implode($inserts, ",");
	$sql .= ") ON DUPLICATE KEY UPDATE ";
	foreach ($fields as $key => $value)
		if (!empty($value))
			if (is_numeric($value))
				$updates[] = $key . "=" . $value;
			else
				$updates[] = $key . "='" . $value . "'";
	$sql .= implode($updates, ",");
	debug_var('sql', $sql);
	$result = $db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
	if ($scan_data['art'] == "Angriff") {
		$sql = "UPDATE $db_tb_scans
			 SET angriff=" . $scan_data['time'] . "
			    ,angriffuser='" . $scan_data['user_from'] . "'
			 WHERE coords_gal=" . $scan_data['coords_to_gal'] . "
			   AND coords_sys=" . $scan_data['coords_to_sys'] . "
			   AND coords_planet=" . $scan_data['coords_to_planet'];
		debug_var('sql', $sql);
		$result = $db->db_query($sql)
			or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
	} elseif ($scan_data['art'] == "Sondierung") {
		$sql = "UPDATE $db_tb_scans
			 SET sondierung=" . $scan_data['time'] . "
			    ,sondierunguser='" . $scan_data['user_from'] . "'
			 WHERE coords_gal=" . $scan_data['coords_to_gal'] . "
			   AND coords_sys=" . $scan_data['coords_to_sys'] . "
			   AND coords_planet=" . $scan_data['coords_to_planet'];
		debug_var('sql', $sql);
		$result = $db->db_query($sql)
			or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
	}
}

function display_flotten() {
	global $scan_datas;

	echo "<br>";
	start_table();
	start_row("titlebg", "colspan=\"6\"");
	echo "<b>Anfliegende Lieferungen</b>";
	next_row("windowbg2", "");
	echo "Ziel";
	next_cell("windowbg2", "");
	echo "Start";
	next_cell("windowbg2", "");
	echo "Ankunft";
	next_cell("windowbg2", "");
	echo "Aktionen";
	foreach ($scan_datas as $scan_data) {
		next_row("windowbg1", "valign=top nowrap");
		echo $scan_data['coords_to_gal'] . ":" . $scan_data['coords_to_sys'] . ":" . $scan_data['coords_to_planet'];
		next_cell("windowbg1", "valign=top nowrap");
		echo $scan_data['coords_from_gal'] . ":" . $scan_data['coords_from_sys'] . ":" . $scan_data['coords_from_planet'];
		next_cell("windowbg1", "valign=top nowrap");
		echo strftime("%d.%m.%Y %H:%M:%S", $scan_data['time']);
		next_cell("windowbg1", "valign=top width=100%;");
		echo $scan_data['art'] . "<br>";
		if (isset($scan_data['pos']))
			foreach ($scan_data['pos'] as $typ => $menge)
				echo $menge . " " . $typ . "<br>";
		if (isset($scan_data['schiffe']))
			foreach ($scan_data['schiffe'] as $typ => $menge)
				echo $menge . " " . $typ . "<br>";
	}
	end_table();
	echo "<br>";
}

// ****************************************************************************
// Entfernt 1000er Trennzeichen.
function strip_number($number) {
	return preg_replace("/[^\d]/", "", str_replace("&#039;", "", $number));
}

// ****************************************************************************
// Gibt den Wert einer Variablen aus.
function debug_var($name, $wert, $level = 2) {
	if (DEBUG_LEVEL >= $level) {
		echo "<div class='system_debug_blue'>$" . $name . ":";
		if (is_array($wert))
			print_r($wert);
		else
			echo "'" . $wert . "'";
		echo "</div>";
	}
}

function debug_echo($text, $level = 2) {
	if (DEBUG_LEVEL >= $level)
		echo "<div class='system_debug_blue'>" . $text . "</div>";
}
?>