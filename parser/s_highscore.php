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
/* Diese Erweiterung der urspünglichen DB ist ein Gemeinschafftsprojekt von  */
/* IW-Spielern.                                                              */
/* Bei Problemen kannst du dich an das eigens dafür eingerichtete            */
/* Entwicklerforum wenden:                                                   */
/*                                                                           */
/*        httpd://handels-gilde.org/?www/forum/index.php;board=1099.0        */
/*                                                                           */
/*****************************************************************************/

if (basename($_SERVER['PHP_SELF']) != "index.php")
  die('Hacking attempt...!!');

if (!defined('IRA'))
	die('Hacking attempt...');

define('DEBUG_LEVEL', 0);

$time = 0;
$order = "";

//*****************************************************************************
//
// Spielerinfo scannen
function parse_highscore($scanlines) {
	global $time, $order;

	$count = 0;
	$scan_data = array("time" => $time);
	foreach ($scanlines as $scanline) {
		debug_var('scanline' , $scanline);
		if (preg_match('/Letzte Aktualisierung (\d+)\.(\d+)\.(\d+)\s+(\d+)\:(\d+)/', $scanline, $match) > 0) {
			debug_var('day', $scan_data['day'] = $match[1]);
			debug_var('month', $scan_data['month'] = $match[2]);
			debug_var('year', $scan_data['year'] = $match[3]);
			debug_var('hour', $scan_data['hour'] = $match[4]);
			debug_var('min', $scan_data['min'] = $match[5]);
		} elseif (preg_match('/Ordnung nach Punkten/', $scanline, $match) > 0) {
			debug_var('order', $scan_data["order"] = $order = "Punkten");
		} elseif (preg_match('/(\d+)\s+([\(\w=\d_-\s\.\*\)]*)\s+(\[.*\]|.*)\s+(.*)\s+(.*)\s+(.*)\s+(.*)\s+(.*)\s+(\d+).(\d+).(\d+)/', $scanline, $match) > 0) {
			debug_var('pos', $scan_data["pos"] = strip_number($match[1]));
			debug_var('name', $scan_data["name"] = trim($match[2]));
			debug_var('allianz', $scan_data["allianz"] = trim($match[3]));
			debug_var('gebp', $scan_data["gebp"] = strip_number(trim($match[4])));
			debug_var('fp', $scan_data["fp"] = strip_number(trim($match[5])));
			debug_var('gesamtp', $scan_data["gesamtp"] = strip_number(trim($match[6])));
			debug_var('ptag', $scan_data["ptag"] = strip_number(trim($match[7])));
			debug_var('diff', $scan_data["diff"] = strip_number(trim($match[8])));
			debug_var('dabei_seit', $scan_data["dabei_seit"] = mktime(0, 0, 0, $match[10], $match[9], $match[11]));
			if ($order == "Punkten") {
				save_data($scan_data);
				$count++;
			}
		}
	}
	echo "<div class='system_notification'>" . $count . " Highscore(s) hinzugefügt.</div>";
}

function save_data($scan_data) {
	global $db, $db_tb_highscore, $db_tb_scans;

	$scan_data["time"] = mktime($scan_data['hour'], $scan_data['min'], 0, $scan_data['month'], $scan_data['day'], $scan_data['year']);
	$scan_data["gebp_nodiff"] = $scan_data["time"];
	$scan_data["fp_nodiff"] = $scan_data["time"];
	$sql = "SELECT * FROM " . $db_tb_highscore . " WHERE name='" . $scan_data['name'] . "'";
	$result = $db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
	if ($row = $db->db_fetch_array($result)) {
		if ($row["gebp"] == $scan_data["gebp"])
			$scan_data["gebp_nodiff"] = $row["gebp_nodiff"];
		if ($row["fp"] == $scan_data["fp"])
			$scan_data["fp_nodiff"] = $row["fp_nodiff"];
	}
	$sql = "INSERT INTO " . $db_tb_highscore;
	$sql .= " (pos,name,allianz,gebp,fp,gesamtp,ptag,diff,dabei_seit,gebp_nodiff,fp_nodiff,time) VALUES (";
	$sql .= $scan_data["pos"];
	$sql .= ",'".$scan_data["name"]."'";
	$sql .= ",'".$scan_data["allianz"]."'";
	$sql .= ",".$scan_data["gebp"];
	$sql .= ",".$scan_data["fp"];
	$sql .= ",".$scan_data["gesamtp"];
	$sql .= ",".$scan_data["ptag"];
	$sql .= ",".$scan_data["diff"];
	$sql .= ",".$scan_data["dabei_seit"];
	$sql .= ",".$scan_data["gebp_nodiff"];
	$sql .= ",".$scan_data["fp_nodiff"];
	$sql .= ",".$scan_data["time"];
	$sql .= ") ON DUPLICATE KEY UPDATE ";
	$sql .= "pos=".$scan_data["pos"];
	$sql .= ",allianz='".$scan_data["allianz"]."'";
	$sql .= ",gebp=".$scan_data["gebp"];
	$sql .= ",fp=".$scan_data["fp"];
	$sql .= ",gesamtp=".$scan_data["gesamtp"];
	$sql .= ",ptag=".$scan_data["ptag"];
	$sql .= ",diff=".$scan_data["diff"];
	$sql .= ",dabei_seit=".$scan_data["dabei_seit"];
	$sql .= ",gebp_nodiff=".$scan_data["gebp_nodiff"];
	$sql .= ",fp_nodiff=".$scan_data["fp_nodiff"];
	$sql .= ",time=".$scan_data["time"];
	$db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

	$sql = "UPDATE " . $db_tb_scans . " SET punkte=".$scan_data["gesamtp"]." WHERE user='".$scan_data["name"]."'";
	
	$db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

	}

// ****************************************************************************
// Entfernt 1000er Trennzeichen.
function strip_number($number) {
	$number = str_replace("&#039;", "", $number);
	$number = str_replace("(", "", $number);
	$number = str_replace(")", "", $number);
	return stripNumber($number);
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
?>