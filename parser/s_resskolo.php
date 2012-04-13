<?php
/*****************************************************************************/
/* s_resskolo.php                                                            */
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
/* Diese Erweiterung der urspA1/4nglichen DB ist ein Gemeinschafftsprojekt von  */
/* IW-Spielern.                                                              */
/* Bei Problemen kannst du dich an das eigens dafA1/4r eingerichtete            */
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
function parse_resskolo($scanlines) {
	global $db, $db_tb_lager, $selectedusername, $config_date;

	$state = 0;
	$scan_data = array();
	foreach ($scanlines as $scanline) {
		if (str_replace(' ', '', $scanline) == '')
			continue;
		debug_var('scanline', $scanline);
		switch ($state) {
		case 0: // Aberschrift
			if (preg_match('/Ressourcenkolo&uuml;bersicht Teil 2/', $scanline, $match) > 0)
				debug_var('state', $state = 32);
			elseif (preg_match('/Ressourcenkolo&uuml;bersicht/', $scanline, $match) > 0)
				debug_var('state', ++$state);
			break;
		case 1: // Tabellentitel
			if (preg_match('/Kolonie Eisen Stahl VV4A chem. Elemente Eis Wasser Energie/', $scanline, $match) > 0)
				debug_var('state', ++$state);
			break;
		case 2: // Planet
			if (preg_match('/.*\s(\d+):(\d+):(\d+)/', $scanline, $match) > 0) {
				debug_var('coords_gal', $scan_data['coords_gal'] = $match[1]);
				debug_var('coords_sys', $scan_data['coords_sys'] = $match[2]);
				debug_var('coords_planet', $scan_data['coords_planet'] = $match[3]);
				debug_var('state', ++$state);
			} elseif (preg_match('/Gesamt\s+(.*)/', $scanline, $match) > 0) {
				debug_var('total_eisen', $scan_data['total_eisen'] = strip_number($match[1]));
				debug_var('state', $state = 25);
			} else
				debug_var('state', $state = 0);
			break;
		case 3: // Kolotyp Eisen
			if (preg_match('/\((.*)\)\s(.*)/', $scanline, $match) > 0) {
				debug_var('kolo_typ', $scan_data['kolo_typ'] = $match[1]);
				debug_var('eisen', $scan_data['eisen'] = strip_number($match[2]));
				debug_var('state', ++$state);
			} else
				debug_var('state', $state = 0);
			break;
		case 4: // Eisen-Produktion
			if (preg_match('/\((.*)\)/', $scanline, $match) > 0) {
				debug_var('eisen_prod', $scan_data['eisen_prod'] = strip_float_number($match[1]));
				debug_var('state', ++$state);
			} else
				debug_var('state', $state = 0);
			break;
		case 5: // Eisen-Lager
			if (preg_match('/---/', $scanline, $match) > 0)
				debug_var('state', ++$state);
			else
				debug_var('state', $state = 0);
			break;
		case 6: // Eisen-Bunker Stahl
			if (preg_match('/(.*)\s+(.*)/', $scanline, $match) > 0) {
				debug_var('eisen_bunker', $scan_data['eisen_bunker'] = strip_number($match[1]));
				debug_var('stahl', $scan_data['stahl'] = strip_number($match[2]));
				debug_var('state', ++$state);
			} else
				debug_var('state', $state = 0);
			break;
		case 7: // Stahl-Produktion
			if (preg_match('/\((.*)\)/', $scanline, $match) > 0) {
				debug_var('stahl_prod', $scan_data['stahl_prod'] = strip_float_number($match[1]));
				debug_var('state', ++$state);
			} else
				debug_var('state', $state = 0);
			break;
		case 8: // Stahl-Lager
			if (preg_match('/---/', $scanline, $match) > 0)
				debug_var('state', ++$state);
			else
				debug_var('state', $state = 0);
			break;
		case 9: // Stahl-Bunker VV4A
			if (preg_match('/(.*)\s+(.*)/', $scanline, $match) > 0) {
				debug_var('stahl_bunker', $scan_data['stahl_bunker'] = strip_number($match[1]));
				debug_var('vv4a', $scan_data['vv4a'] = strip_number($match[2]));
				debug_var('state', ++$state);
			} else
				debug_var('state', $state = 0);
			break;
		case 10: // VV4A-Produktion
			if (preg_match('/\((.*)\)/', $scanline, $match) > 0) {
				debug_var('vv4a_prod', $scan_data['vv4a_prod'] = strip_float_number($match[1]));
				debug_var('state', ++$state);
			} else
				debug_var('state', $state = 0);
			break;
		case 11: // VV4A-Lager
			if (preg_match('/---/', $scanline, $match) > 0)
				debug_var('state', ++$state);
			else
				debug_var('state', $state = 0);
			break;
		case 12: // VV4A-Bunker Chemie
			if (preg_match('/(.*)\s+(.*)/', $scanline, $match) > 0) {
				debug_var('vv4a_bunker', $scan_data['vv4a_bunker'] = strip_number($match[1]));
				debug_var('chem', $scan_data['chem'] = strip_number($match[2]));
				debug_var('state', ++$state);
			} else
				debug_var('state', $state = 0);
			break;
		case 13: // Chemie-Produktion
			if (preg_match('/\((.*)\)/', $scanline, $match) > 0) {
				debug_var('chem_prod', $scan_data['chem_prod'] = strip_float_number($match[1]));
				debug_var('state', ++$state);
			} else
				debug_var('state', $state = 0);
			break;
		case 14: // Chemie-Lager
			if (preg_match('/(.*)/', $scanline, $match) > 0) {
				debug_var('chem_lager', $scan_data['chem_lager'] = strip_number($match[1]));
				debug_var('state', ++$state);
			} else
				debug_var('state', $state = 0);
			break;
		case 15: // Chemie-Bunker Eis
			if (preg_match('/(.*)\s+(.*)/', $scanline, $match) > 0) {
				debug_var('chem_bunker', $scan_data['chem_bunker'] = strip_number($match[1]));
				debug_var('eis', $scan_data['eis'] = strip_number($match[2]));
				debug_var('state', ++$state);
			} else
				debug_var('state', $state = 0);
			break;
		case 16: // Eis-Produktion
			if (preg_match('/\((.*)\)/', $scanline, $match) > 0) {
				debug_var('eis_prod', $scan_data['eis_prod'] = strip_float_number($match[1]));
				debug_var('state', ++$state);
			} else
				debug_var('state', $state = 0);
			break;
		case 17: // Eis-Lager
			if (preg_match('/(.*)/', $scanline, $match) > 0) {
				debug_var('eis_lager', $scan_data['eis_lager'] = strip_number($match[1]));
				debug_var('state', ++$state);
			} else
				debug_var('state', $state = 0);
			break;
		case 18: // Eis-Bunker Wasser
			if (preg_match('/(.*)\s+(.*)/', $scanline, $match) > 0) {
				debug_var('eis_bunker', $scan_data['eis_bunker'] = strip_number($match[1]));
				debug_var('wasser', $scan_data['wasser'] = strip_number($match[2]));
				debug_var('state', ++$state);
			} else
				debug_var('state', $state = 0);
			break;
		case 19: // Wasser-Produktion
			if (preg_match('/\((.*)\)/', $scanline, $match) > 0) {
				debug_var('wasser_prod', $scan_data['wasser_prod'] = strip_float_number($match[1]));
				debug_var('state', ++$state);
			} else
				debug_var('state', $state = 0);
			break;
		case 20: // Wasser-Lager
			if (preg_match('/(.*)/', $scanline, $match) > 0)
				debug_var('state', ++$state);
			else
				debug_var('state', $state = 0);
			break;
		case 21: // Wasser-Bunker Energie
			if (preg_match('/(.*)\s+(.*)/', $scanline, $match) > 0) {
				debug_var('wasser_bunker', $scan_data['wasser_bunker'] = strip_number($match[1]));
				debug_var('energie', $scan_data['energie'] = strip_number($match[2]));
				debug_var('state', ++$state);
			} else
				debug_var('state', $state = 0);
			break;		
		case 22: // Energie-Produktion
			if (preg_match('/\((.*)\)/', $scanline, $match) > 0) {
				debug_var('energie_prod', $scan_data['energie_prod'] = strip_float_number($match[1]));
				debug_var('state', ++$state);
			} else
				debug_var('state', $state = 0);
			break;
		case 23: // Energie-Lager
			if (preg_match('/(.*)/', $scanline, $match) > 0) {
				debug_var('energie_lager', $scan_data['energie_lager'] = strip_number($match[1]));
				debug_var('state', ++$state);
			} else
				debug_var('state', $state = 0);
			break;
		case 24: // Energie-Bunker
			if (preg_match('/(.*)/', $scanline, $match) > 0) {
				debug_var('energie_bunker', $scan_data['energie_bunker'] = strip_number($match[1]));
				insert_data($scan_data);
				debug_var('state', $state = 2);
			} else
				debug_var('state', $state = 0);
			break;
		case 25: // Eisen-Produktion Stahl-Gesamt
			if (preg_match('/\((.*)\)\s+(.*)/', $scanline, $match) > 0) {
				debug_var('total_eisen_prod', $scan_data['total_eisen_prod'] = strip_float_number($match[1]));
				debug_var('total_stahl', $scan_data['total_stahl'] = strip_number($match[2]));
				debug_var('state', ++$state);
			} else
				debug_var('state', $state = 0);
			break;
		case 26: // Stahl-Produktion VV4A-Gesamt
			if (preg_match('/\((.*)\)\s+(.*)/', $scanline, $match) > 0) {
				debug_var('total_stahl_prod', $scan_data['total_stahl_prod'] = strip_float_number($match[1]));
				debug_var('total_vv4a', $scan_data['total_vv4a'] = strip_number($match[2]));
				debug_var('state', ++$state);
			} else
				debug_var('state', $state = 0);
			break;
		case 27: // VV4A-Produktion Chemie-Gesamt
			if (preg_match('/\((.*)\)\s+(.*)/', $scanline, $match) > 0) {
				debug_var('total_vv4a_prod', $scan_data['total_vv4a_prod'] = strip_float_number($match[1]));
				debug_var('total_chem', $scan_data['total_chem'] = strip_number($match[2]));
				debug_var('state', ++$state);
			} else
				debug_var('state', $state = 0);
			break;
		case 28: // Chemie-Produktion Eis-Gesamt
			if (preg_match('/\((.*)\)\s+(.*)/', $scanline, $match) > 0) {
				debug_var('total_chem_prod', $scan_data['total_chem_prod'] = strip_float_number($match[1]));
				debug_var('total_eis', $scan_data['total_eis'] = strip_number($match[2]));
				debug_var('state', ++$state);
			} else
				debug_var('state', $state = 0);
			break;
		case 29: // Eis-Produktion Wasser-Gesamt
			if (preg_match('/\((.*)\)\s+(.*)/', $scanline, $match) > 0) {
				debug_var('total_eis_prod', $scan_data['total_eis_prod'] = strip_float_number($match[1]));
				debug_var('total_wasser', $scan_data['total_wasser'] = strip_number($match[2]));
				debug_var('state', ++$state);
			} else
				debug_var('state', $state = 0);
			break;
		case 30: // Wasser-Produktion Energie-Gesamt
			if (preg_match('/\((.*)\)\s+(.*)/', $scanline, $match) > 0) {
				debug_var('total_wasser_prod', $scan_data['total_wasser_prod'] = strip_float_number($match[1]));
				debug_var('total_energie', $scan_data['total_energie'] = strip_number($match[2]));
				debug_var('state', ++$state);
			} else
				debug_var('state', $state = 0);
			break;
		case 31: // Energie-Produktion
			if (preg_match('/\((.*)\)/', $scanline, $match) > 0) {
				debug_var('total_energie_prod', $scan_data['total_energie_prod'] = strip_float_number($match[1]));
				insert_data_total($scan_data);
				debug_var('state', $state = 0);
			} else
				debug_var('state', $state = 0);
			break;
		case 32: // Kolonie FP Credits Steuersatz BevAlkerung Zufr
			if (preg_match('/Kolonie\s+FP\s+Credits\s+Steuersatz\s+Bev&ouml;lkerung\s+Zufr/', $scanline, $match) > 0)
				debug_var('state', ++$state);
			else
				debug_var('state', $state = 0);
			break;
		case 33: // Schoko 13:103:12 5.705,37
			  // Gesamt 11.544
			if (preg_match('/(.*)\s+(\d+):(\d+):(\d+)\s+(.*)/', $scanline, $match) > 0) {
				debug_var('name', $scan_data['name'] = $match[1]);
				debug_var('coords_gal', $scan_data['coords_gal'] = $match[2]);
				debug_var('coords_sys', $scan_data['coords_sys'] = $match[3]);
				debug_var('coords_planet', $scan_data['coords_planet'] = $match[4]);
				debug_var('fp', $scan_data['fp'] = strip_float_number($match[5]));
				debug_var('state', ++$state);
			} elseif (preg_match('/Gesamt\s+(.*)/', $scanline, $match) > 0) {
				debug_var('total_fp', $scan_data['total_fp'] = strip_float_number($match[1]));
				debug_var('state', $state = 37);
			} else
				debug_var('state', $state = 0);
			break;
		case 34: // (4.675,00*(1,22+0,00)) 28,32 132 / 12.370 / 12.370
			  // (200,00*(1,00+0,00)) 0,00 18\% 250 / 1063 / 1140
			if (preg_match('/\((.*)\*\((.*)\+(.*)\)\)\s+([^\s]*)\s+([^\s]*)\\\%\s+([^\s]*)\s+\/\s+[^\s]*\s+\/\s+([^\s]*)/', $scanline, $match) > 0) {
			//if (preg_match('/\((.*)\*\((.*)\+(.*)\)\)\s+([^\s]*)\s+([^\s]*)\s+\/\s+[^\s]*\s+\/\s+([^\s]*)/', $scanline, $match) > 0) {
				debug_var('fp_b', $scan_data['fp_b'] = strip_float_number($match[1]));
				debug_var('fp_m1', $scan_data['fp_m1'] = strip_float_number($match[2]));
				debug_var('fp_m2', $scan_data['fp_m2'] = strip_float_number($match[3]));
				debug_var('credits', $scan_data['credits'] = strip_float_number($match[4]));
				// $match[5] ist der steuersatz
				debug_var('bev_a', $scan_data['bev_a'] = strip_number($match[6]));
				debug_var('bev_g', $scan_data['bev_g'] = strip_number($match[7]));
				debug_var('bev_q', $scan_data['bev_q'] = $scan_data['bev_a'] * 100 / ($scan_data['bev_g'] > 0 ? $scan_data['bev_g'] : 1));
				debug_var('state', ++$state);
			} else
				debug_var('state', $state = 0);
			break;
		case 35: // (0) 91,31
			  // (0)
			if (preg_match('/\((.*)\)\s+(\d.*)/', $scanline, $match) > 0) {
				debug_var('bev_w', $scan_data['bev_w'] = strip_number($match[1]));
				debug_var('zufr', $scan_data['zufr'] = strip_float_number($match[2]));
				debug_var('state', $state = 36);
			} elseif (preg_match('/\((.*)\)/', $scanline, $match) > 0) {
				debug_var('bev_w', $scan_data['bev_w'] = strip_number($match[1]));
				insert_data_2($scan_data);
				debug_var('state', $state = 33);
			} else 
				debug_var('state', $state = 0);
			break;
		case 36: // (-0,20)
			if (preg_match('/\((.*)\)/', $scanline, $match) > 0) {
				debug_var('zufr_w', $scan_data['zufr_w'] = strip_float_number($match[1]));
				insert_data_2($scan_data);
			}
			debug_var('state', $state = 33);
			break;
		case 37: // (8.613,00*(1,34+0,00)) 14.404,01
			if (preg_match('/\((.*)\*\((.*)\+(.*)\)\)\s+(.*)/', $scanline, $match) > 0) {
				debug_var('total_fp_b', $scan_data['total_fp_b'] = strip_float_number($match[1]));
				debug_var('total_fp_m1', $scan_data['total_fp_m1'] = strip_float_number($match[2]));
				debug_var('total_fp_m2', $scan_data['total_fp_m2'] = strip_float_number($match[3]));
				debug_var('total_credits', $scan_data['total_credits'] = strip_float_number($match[4]));
				debug_var('state', ++$state);
			} else
				debug_var('state', $state = 0);
			break;
		case 38: // (5,49)
			if (preg_match('/\((.*)\)/', $scanline, $match) > 0) {
				debug_var('total_credits_w', $scan_data['total_credits_w'] = strip_float_number($match[1]));
				debug_var('state', ++$state);
			} else
				debug_var('state', $state = 0);
			break;		
		case 39: // Allisteuer: 51,22 2.416 / 23.607 / --
			if (preg_match('/Allisteuer:\s+([^\s]*)\s+([^\s]*)\s+\/\s+([^\s]*)\s+\/\s+--/', $scanline, $match) > 0) {
				debug_var('total_alli', $scan_data['total_alli'] = strip_float_number($match[1]));
				debug_var('total_bev_a', $scan_data['total_bev_a'] = strip_number($match[2]));
				debug_var('total_bev_g', $scan_data['total_bev_g'] = strip_number($match[3]));
				debug_var('total_bev_q', $scan_data['total_bev_q'] = $scan_data['total_bev_a'] * 100 / $scan_data['total_bev_g']);
				debug_var('state', ++$state);
			} else
				debug_var('state', $state = 0);
			break;
		case 40: // (-486)
			if (preg_match('/\((.*)\)/', $scanline, $match) > 0) {
				debug_var('total_bev_w', $scan_data['total_bev_w'] = strip_number($match[1]));
				insert_data_total_2($scan_data);
				debug_var('state', $state = 0);
			} else
				debug_var('state', $state = 0);
			break;		
		}
	}
#	$sql = "DELETE FROM " . $db_tb_lager . " WHERE " . $db_tb_lager . ".user = '" . $selectedusername . "' AND " . $db_tb_lager . ".time <> '". $config_date ."'";
#	debug_var('sql', $sql);
#	$db->db_query($sql)
#		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);	
	echo "<div class='system_notification'>Lager&uuml;bersicht aktualisiert.</div>";
}

function insert_data($scan_data) {
	global $db, $db_tb_lager, $selectedusername, $config_date;

	debug_var('insert_data()', '');
	$sql = "INSERT INTO " . $db_tb_lager . " (";
	$sql .= "user,coords_gal,coords_sys,coords_planet,kolo_typ,";
	$sql .= "eisen,eisen_prod,eisen_bunker,stahl,stahl_prod,stahl_bunker,";
	$sql .= "vv4a,vv4a_prod,vv4a_bunker,chem,chem_prod,chem_lager,chem_bunker,";
	$sql .= "eis,eis_prod,eis_lager,eis_bunker,wasser,wasser_prod,wasser_bunker,";
	$sql .= "energie,energie_prod,energie_lager,energie_bunker,time) VALUES (";
	$sql .= "'" . $selectedusername . "',";
	$sql .= $scan_data['coords_gal'] . ",";
	$sql .= $scan_data['coords_sys'] . ",";
	$sql .= $scan_data['coords_planet'] . ",";
	$sql .= "'" . $scan_data['kolo_typ'] . "',";
	$sql .= $scan_data['eisen'] . "," . $scan_data['eisen_prod'] . "," . $scan_data['eisen_bunker'] . ",";
	$sql .= $scan_data['stahl'] . "," . $scan_data['stahl_prod'] . "," . $scan_data['stahl_bunker'] . ",";
	$sql .= $scan_data['vv4a'] . "," . $scan_data['vv4a_prod'] . "," . $scan_data['vv4a_bunker'] . ",";
	$sql .= $scan_data['chem'] . "," . $scan_data['chem_prod'] . "," . $scan_data['chem_lager'] . "," . $scan_data['chem_bunker'] . ",";
	$sql .= $scan_data['eis'] . "," . $scan_data['eis_prod'] . "," . $scan_data['eis_lager'] . "," . $scan_data['eis_bunker'] . ",";
	$sql .= $scan_data['wasser'] . "," . $scan_data['wasser_prod'] . "," . $scan_data['wasser_bunker'] . ",";
	$sql .= $scan_data['energie'] . "," . $scan_data['energie_prod'] . "," . $scan_data['energie_lager'] . "," . $scan_data['energie_bunker'] . ",";
	$sql .= $config_date;
	$sql .= ") ON DUPLICATE KEY UPDATE";
	$sql .= " user='" . $selectedusername . "'";
	$sql .= ",kolo_typ='" . $scan_data["kolo_typ"] . "'";
	$sql .= ",eisen=" . $scan_data["eisen"] . ",eisen_prod=" . $scan_data['eisen_prod'] . ",eisen_bunker=" . $scan_data['eisen_bunker'];
	$sql .= ",stahl=" . $scan_data["stahl"] . ",stahl_prod=" . $scan_data['stahl_prod'] . ",stahl_bunker=" . $scan_data['stahl_bunker'];
	$sql .= ",vv4a=" . $scan_data["vv4a"] . ",vv4a_prod=" . $scan_data['vv4a_prod'] . ",vv4a_bunker=" . $scan_data['vv4a_bunker'];
	$sql .= ",chem=" . $scan_data["chem"] . ",chem_prod=" . $scan_data['chem_prod'] . ",chem_lager=" . $scan_data['chem_lager'] . ",chem_bunker=" . $scan_data['vv4a_bunker'];
	$sql .= ",eis=" . $scan_data["eis"] . ",eis_prod=" . $scan_data['eis_prod'] . ",eis_lager=" . $scan_data['eis_lager'] . ",eis_bunker=" . $scan_data['eis_bunker'];
	$sql .= ",wasser=" . $scan_data["wasser"] . ",wasser_prod=" . $scan_data['wasser_prod'] . ",wasser_bunker=" . $scan_data['wasser_bunker'];
	$sql .= ",energie=" . $scan_data["energie"] . ",energie_prod=" . $scan_data['energie_prod'] . ",energie_lager=" . $scan_data['energie_lager'] . ",energie_bunker=" . $scan_data['energie_bunker'];
	$sql .= ",time=" . $config_date;
	debug_var('sql', $sql);
	$db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
}

function insert_data_2($scan_data) {
	global $db, $db_tb_lager, $config_date;
	$sql = "UPDATE " . $db_tb_lager;
	$sql .= " SET fp=" . $scan_data['fp'];
	$sql .= ",fp_b=" . $scan_data['fp_b'];
	$sql .= ",fp_m1=" . $scan_data['fp_m1'];
	$sql .= ",fp_m2=" . $scan_data['fp_m2'];
	$sql .= ",credits=" . $scan_data['credits'];
	$sql .= ",bev_a=" . $scan_data['bev_a'];
	$sql .= ",bev_g=" . $scan_data['bev_g'];
	if (!empty($scan_data['bev_q']))
		$sql .= ",bev_q=" . $scan_data['bev_q'];
	$sql .= ",bev_w=" . $scan_data['bev_w'];
	if (!empty($scan_data['zufr']))
		$sql .= ",zufr=" . $scan_data['zufr'];
	if (!empty($scan_data['zufr_w']))
		$sql .= ",zufr_w=" . $scan_data['zufr_w'];
	$sql .= ",time=" . $config_date;
	$sql .= " WHERE coords_gal=" . $scan_data['coords_gal'];
	$sql .= " AND coords_sys=" . $scan_data['coords_sys'];
	$sql .= " AND coords_planet=" . $scan_data['coords_planet'];
	debug_var('sql', $sql);
	$db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
}

function insert_data_total($scan_data) {
	global $db, $db_tb_ressuebersicht, $selectedusername, $config_date;

	debug_var('insert_data_total()', '');

	if (empty($db_tb_ressuebersicht))
		return;

	$sql = "INSERT INTO " . $db_tb_ressuebersicht;
	$sql .= " (user,datum,eisen,stahl,chem,vv4a,eis,wasser,energie) VALUES (";
	$sql .= "'" . $selectedusername . "'";
	$sql .= "," . $config_date;
	$sql .= "," . $scan_data['total_eisen_prod'];
	$sql .= "," . $scan_data['total_stahl_prod'];
	$sql .= "," . $scan_data['total_chem_prod'];
	$sql .= "," . $scan_data['total_vv4a_prod'];
	$sql .= "," . $scan_data['total_eis_prod'];
	$sql .= "," . $scan_data['total_wasser_prod'];
	$sql .= "," . $scan_data['total_energie_prod'];
	$sql .= ") ON DUPLICATE KEY UPDATE";
	$sql .= " datum=" . $config_date;
	$sql .= ",eisen=" . $scan_data['total_eisen_prod'];
	$sql .= ",stahl=" . $scan_data['total_stahl_prod'];
	$sql .= ",chem=" . $scan_data['total_chem_prod'];
	$sql .= ",vv4a=" . $scan_data['total_vv4a_prod'];
	$sql .= ",eis=" . $scan_data['total_eis_prod'];
	$sql .= ",wasser=" . $scan_data['total_wasser_prod'];
	$sql .= ",energie=" . $scan_data['total_energie_prod'];
	debug_var('sql', $sql);
	$db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);		
}

function insert_data_total_2($scan_data) {
	global $db, $db_tb_ressuebersicht, $selectedusername, $config_date;

	debug_var('insert_data_total_2()', '');

	if (empty($db_tb_ressuebersicht))
		return;
	
	$sql = "UPDATE " . $db_tb_ressuebersicht;
	$sql .= " SET fp_ph=" . $scan_data['total_fp'];
#	$sql .= ",credits=" . $scan_data['total_credits_w'];
		$total_credits=$scan_data['total_alli']+$scan_data['total_credits_w'];
	$sql .= ",credits=" . $total_credits;
	$sql .= ",bev_a=" . $scan_data['total_bev_a'];
	$sql .= ",bev_g=" . $scan_data['total_bev_g'];
	$sql .= ",bev_q=" . $scan_data['total_bev_q'];
	$sql .= ",datum=" . $config_date;
	$sql .= " WHERE user='" . $selectedusername . "'";
	debug_var('sql', $sql);
	$db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);		
}

// ****************************************************************************
// Entfernt 1000er Trennzeichen aus einer FlieAkommazahl.
function strip_float_number($number) {
	$number = str_replace("&#039;", "", $number);
	$number = str_replace("(", "", $number);
	$number = str_replace(")", "", $number);
	return stripNumber($number);
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