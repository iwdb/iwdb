<?php
/*****************************************************************************/
/* s_sbxml.php                                                               */
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
/* Diese Erweiterung der ursp?nglichen DB ist ein Gemeinschafftsprojekt von  */
/* IW-Spielern.                                                              */
/* Bei Problemen kannst du dich an das eigens dafuer eingerichtete           */
/* Entwicklerforum wenden:                                                   */
/*                                                                           */
/*                   http://www.iw-smf.pericolini.de                         */
/*                                                                           */
/*****************************************************************************/

/*****************************************************************************/
/* XML-Scan-Parser                                                           */
/* von [RoC]Thella                                                           */
/*****************************************************************************/

if (basename($_SERVER['PHP_SELF']) != "index.php" || !defined('IRA'))
	die('Hacking attempt...!!');

if (!defined('DEBUG_LEVEL'))
	define("DEBUG_LEVEL", 0);

include_once("./includes/debug.php");

function parse_sbxml($scanlines) {
  	foreach ($scanlines as $line) {
		if (strpos($line, "http://www.icewars.de/portal/kb/de/sb.php") !== FALSE) {
			$link = html_entity_decode(trim($line)) . "&typ=xml";	
			$xml = simplexml_load_file($link);
			debug_var("scan_data['coords_gal']", $scan_data['coords_gal'] = (int)$xml->plani_data->koordinaten->gal);
			debug_var("scan_data['coords_sys']", $scan_data['coords_sys'] = (int)$xml->plani_data->koordinaten->sol);
			debug_var("scan_data['coords_planet']", $scan_data['coords_planet'] = (int)$xml->plani_data->koordinaten->pla);
			debug_var("scan_data['coords']", $scan_data['coords'] = $scan_data['coords_gal'] . ":" . $scan_data['coords_sys'] . ":" . $scan_data['coords_planet']);
			debug_var("scan_data['user']", $scan_data['user'] = (string)$xml->plani_data->user->name);
			debug_var("scan_data['allianz']", $scan_data['allianz'] = (string)$xml->plani_data->user->allianz_tag);
			debug_var("scan_data['typ']", $scan_data['typ'] = (string)$xml->plani_data->planeten_typ->name);
			debug_var("scan_data['objekt']", $scan_data['objekt'] = (string)$xml->plani_data->objekt_typ->name);
			debug_var("scan_data['time']", $scan_data['time'] = (int)$xml->timestamp);
			debug_var("scan_data['vollstaendig']", $scan_data['vollstaendig'] = (int)$xml->informationen->vollstaendig);
			debug_var("scan_typ", $scan_typ = (string)$xml->scann_typ->id);
			// Geo
			if ($scan_typ == 1) {
				debug_var("scan_data['geoscantime']", $scan_data['time']);
				$scan_data['geoscantime'] = (int)$xml->timestamp;
				$ressourcen = $xml->plani_data->ressourcen_vorkommen->ressource;
				foreach ($ressourcen as $ressource) {
					$wert = ((string)$ressource->wert[0]*100);
					switch ((int)$ressource->id) {
					case 1:
						debug_var("scan_data['eisengehalt']", $scan_data['eisengehalt'] = $wert);
						break;
					case 4:
						debug_var("scan_data['eisdichte']", $scan_data['eisdichte'] = $wert);
						break;
					case 5:
						debug_var("scan_data['chemievorkommen']", $scan_data['chemievorkommen'] = $wert);
					}
				}
				$ressourcen_tech_team = $xml->plani_data->ressourcen_vorkommen->ressource_tech_team;
				foreach ($ressourcen_tech_team as $ressource_tech_team) {
					$wert = ((string)$ressource_tech_team->wert[0]*100);
					switch ((int)$ressource_tech_team->id) {
					case 1:
						debug_var("scan_data['tteisen']", $scan_data['tteisen'] = $wert);
						break;
					case 4:
						debug_var("scan_data['tteis']", $scan_data['tteis'] = $wert);
						break;
					case 5:
						debug_var("scan_data['ttchemie']", $scan_data['ttchemie'] = $wert);
						break;
					}
				}
				debug_var("scan_data['gravitation']", $scan_data['gravitation'] = (string)$xml->plani_data->gravitation);
				debug_var("scan_data['lebensbedingungen']", $scan_data['lebensbedingungen'] = ((string)$xml->plani_data->lebensbedingungen*100));
				debug_var("scan_data['bevoelkerungsanzahl']", $scan_data['bevoelkerungsanzahl'] = (string)$xml->plani_data->bev_max);
				debug_var("scan_data['fmod']", $scan_data['fmod'] = ((string)$xml->plani_data->modifikatoren->forschung*100));
				debug_var("scan_data['kgmod']", $scan_data['kgmod'] = (string)$xml->plani_data->modifikatoren->gebaeude_bau->kosten);
				debug_var("scan_data['dgmod']", $scan_data['dgmod'] = (string)$xml->plani_data->modifikatoren->gebaeude_bau->dauer);
				debug_var("scan_data['ksmod']", $scan_data['ksmod'] = (string)$xml->plani_data->modifikatoren->schiff_bau->kosten);
				debug_var("scan_data['dsmod']", $scan_data['dsmod'] = (string)$xml->plani_data->modifikatoren->schiff_bau->dauer);
				$scan_data['besonderheiten'] = "";								
				foreach ($xml->plani_data->besonderheiten->besonderheit as $besonderheit) {
					if (!empty($scan_data['besonderheiten']))
						$scan_data['besonderheiten'] .= ", " . (string)$besonderheit->name;
					else
						$scan_data['besonderheiten'] = (string)$besonderheit->name;
					if (stripos($besonderheit->name, "Nebel"))
						$scan_data['nebula'] = (string)$besonderheit->name;
				}
				debug_var("scan_data['besonderheiten']", $scan_data['besonderheiten']);
				debug_var("scan_data['reset_timestamp']", $scan_data['reset_timestamp'] = (int)$xml->plani_data->reset_timestamp);
			// Gebäude/Ress
			} else if ($scan_typ == 2) {
				debug_var("scan_data['gebscantime']", $scan_data['gebscantime'] = $scan_data['time']);
				$scan_data['gebscantime'] = (int)$xml->timestamp;
				foreach ($xml->gebaeude->gebaeude as $gebaeude) {
					if (!isset($scan_data['geb']))
						$scan_data['geb'] = "<table class=\"scan_table\">\n";
					$scan_data['geb'] .= "<tr class=\"scan_row\">\n";
					$scan_data['geb'] .= "\t<td class=\"scan_object\">\n";
					$scan_data['geb'] .= (string)$gebaeude->name;
					$scan_data['geb'] .= "\n\t</td>\n";
					$scan_data['geb'] .= "\t<td class=\"scan_value\">\n";
					$scan_data['geb'] .= (string)$gebaeude->anzahl;
					$scan_data['geb'] .= "\n\t</td>\n</tr>\n";
				}
				if (isset($scan_data['geb']))
					debug_var("scan_data['geb']", $scan_data['geb'] .= "</table>\n");
			// Schiffe/Ress
			} else if ($scan_typ == 3) {
				debug_var("scan_data['schiffscantime']", $scan_data['schiffscantime'] = $scan_data['time']);
				$scan_data['schiffscantime'] = (int)$xml->timestamp;
				foreach ($xml->pla_def as $pla_def) {
					foreach ($pla_def->user as $user) {
						foreach ($user->schiffe as $schiff) {
							foreach ($schiff->schifftyp as $schifftyp) {
								if (!isset($scan_data['plan']))
									$scan_data['plan'] = "<table class=\"scan_table\">\n";
								$scan_data['plan'] .= "<tr class=\"scan_row\">\n";
								$scan_data['plan'] .= "\t<td class=\"scan_object\">\n";
								$scan_data['plan'] .= utf8_decode((string)$schifftyp->name);
								$scan_data['plan'] .= "\n\t</td>\n";
								$scan_data['plan'] .= "\t<td class=\"scan_value\">\n";
								$scan_data['plan'] .= (string)$schifftyp->anzahl;
								$scan_data['plan'] .= "\n\t</td>\n</tr>\n";
							}
						}
						foreach ($user->defence as $defence) {
							foreach ($defence->defencetyp as $defencetyp) {
								if (!isset($scan_data['def']))
									$scan_data['def'] = "<table class=\"scan_table\">\n";
								$scan_data['def'] .= "<tr class=\"scan_row\">\n";
								$scan_data['def'] .= "\t<td class=\"scan_object\">\n";
								$scan_data['def'] .= (string)$defencetyp->name;
								$scan_data['def'] .= "\n\t</td>\n";
								$scan_data['def'] .= "\t<td class=\"scan_value\">\n";
								$scan_data['def'] .= (string)$defencetyp->anzahl;
								$scan_data['def'] .= "\n\t</td>\n</tr>\n";
							}
						}
					}
				}
				if (isset($scan_data['plan']))
					debug_var("scan_data['plan']", $scan_data['plan'] .= "</table>\n");
				else
					debug_var("scan_data['plan']", $scan_data['plan'] = "");
				if (isset($scan_data['def']))
					debug_var("scan_data['def']", $scan_data['def'] .= "</table>\n");
				else
					debug_var("scan_data['def']", $scan_data['def'] = "");
				foreach ($xml->flotten_def as $flotten_def) {
					foreach ($flotten_def->user as $user) {
						if (!isset($scan_data['stat']))
							$scan_data['stat'] = "<table class=\"scan_table\">\n";		
						$scan_data['stat'] .= "\t<tr class=\"scan_row\">\n";
						$scan_data['stat'] .= "\t\t<td colspan=\"2\" class=\"scan_title\">";
						$scan_data['stat'] .= "Stationierte Flotte von ";
						$scan_data['stat'] .= $user->name;
						$scan_data['stat'] .= ":</td>\n";
						$scan_data['stat'] .= "\t</tr>\n";
						foreach ($user->schiffe as $schiffe) {
							foreach ($schiffe->schifftyp as $schifftyp) {
								$scan_data['stat'] .= "<tr class=\"scan_row\">\n";
								$scan_data['stat'] .= "\t<td class=\"scan_object\">\n";
								$scan_data['stat'] .= utf8_decode((string)$schifftyp->name);
								$scan_data['stat'] .= "\n\t</td>\n";
								$scan_data['stat'] .= "\t<td class=\"scan_value\">\n";
								$scan_data['stat'] .= (string)$schifftyp->anzahl;
								$scan_data['stat'] .= "\n\t</td>\n</tr>\n";
							}
						}
					}
				}
				if (isset($scan_data['stat']))
					debug_var("scan_data['stat']", $scan_data['stat'] .= "</table>\n");
				else
					debug_var("scan_data['stat']", $scan_data['stat'] = "");
			}
			// Gebäude oder Schiffe/Ress
			if ($scan_typ == 2 || $scan_typ == 3) {
				foreach ($xml->ressourcen as $ressourcen) {
					foreach ($ressourcen->ressource as $ressource) {
						if ($ressource->id == 1) {
							debug_var("scan_data['eisen']", $scan_data['eisen'] = $ressource->anzahl);
						} else if ($ressource->id == 2) {
							debug_var("scan_data['stahl']", $scan_data['stahl'] = $ressource->anzahl);
						} else if ($ressource->id == 3) {
							debug_var("scan_data['vv4a']", $scan_data['vv4a'] = $ressource->anzahl);
						} else if ($ressource->id == 4) {
							debug_var("scan_data['eis']", $scan_data['eis'] = $ressource->anzahl);
						} else if ($ressource->id == 5) {
							debug_var("scan_data['chemie']", $scan_data['chemie'] = $ressource->anzahl);
						} else if ($ressource->id == 6) {
							debug_var("scan_data['wasser']", $scan_data['wasser'] = $ressource->anzahl);
						} else if ($ressource->id == 7) {
							debug_var("scan_data['energie']", $scan_data['energie'] = $ressource->anzahl);
						}
					}
				}
			}
			debug_var("save_sbxml", $results = save_sbxml($scan_data));
			foreach ($results as $result)
				echo "<div class='system_notification'>" . $result . "</div>";
		}
	}
}

function save_sbxml($scan_data) {
	global $db, $db_tb_scans, $db_tb_sysscans, $db_tb_user, $selectedusername;
	$results = array();
	debug_var("sql", $sql = "SELECT * FROM $db_tb_scans WHERE coords_gal=" . $scan_data['coords_gal'] . " AND coords_sys=" . $scan_data['coords_sys'] . " AND coords_planet=" . $scan_data['coords_planet']);
	$result = $db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
	debug_var("row", $row = $db->db_fetch_array($result));
	// Unvollständiger Scan
	unset($scan_data['vollstaendig']);
	/*if (isset($scan_data['vollstaendig']) && $scan_data['vollstaendig'] == 1) {
		unset($scan_data['vollstaendig']);
	} else {
		$results[] = "Der Scan " . $scan_data['coords'] . " ist nicht vollständig.";
		return $results;
	}*/
	// Typänderung
	if (!empty($row) && ($row['typ'] != $scan_data['typ']) && isset($scan_data['geoscantime'])) {
		debug_var("scan_data['eisengehalt']", $scan_data['eisengehalt'] = "");
		$scan_data['eisdichte'] = "";
		$scan_data['chemievorkommen'] = "";
		$scan_data['tteisen'] = "";
		$scan_data['tteis'] = "";
		$scan_data['ttchemie'] = "";
		$scan_data['gravitation'] = "";
		$scan_data['lebensbedingungen'] = "";
		$scan_data['bevoelkerungsanzahl'] ="";		
		$scan_data['fmod'] = "";
		$scan_data['kgmod'] = "";
		$scan_data['dgmod'] = "";
		$scan_data['ksmod'] = "";
		$scan_data['dsmod'] = "";
		$scan_data['besonderheiten'] = "";
		$scan_data['reset_timestamp'] = "";
		$results[] = "Vorhandenen Geoscan wegen Typänderung gelöscht.";
	}
	// Neuerer Scan vorhanden
	if (!empty($row) && $row['time'] > $scan_data['time']) {
		unset($scan_data['user']);
		unset($scan_data['allianz']);
		unset($scan_data['typ']);
		unset($scan_data['objekt']);
	}
	// Neuerer Geoscan vorhanden
	if (!empty($row) && isset($scan_data['geoscantime']) && $row['geoscantime'] >= $scan_data['geoscantime']) {
		$results[] = "Neuerer oder aktueller Geoscan bereits vorhanden.";
		return $results;
	}
	// Neuerer Schiffscan vorhanden
	if (!empty($row) && isset($scan_data['schiffscantime']) && $row['schiffscantime'] >= $scan_data['schiffscantime']) {
		$results[] = "Neuerer oder aktueller Schiffscan bereits vorhanden.";
		return $results;
	}
	// Neuerer Gebscan vorhanden
	if (!empty($row) && isset($scan_data['gebscantime']) && $row['gebscantime'] >= $scan_data['gebscantime']) {
		$results[] = "Neuerer oder aktueller Gebäudescan bereits vorhanden.";
		return $results;
	}
	// Nebel vorhanden
	if (isset($scan_data['nebula'])) {
		$sql = "UPDATE " . $db_tb_sysscans . " SET ";
		$sql .= " nebula='" . $scan_data['nebula'] . "'";
		$sql .= " WHERE gal=" . $scan_data['coords_gal'];
		$sql .= " AND sys=" . $scan_data['coords_sys'];
		unset($scan_data['nebula']);
	}
	// INSERT
	if (empty($row)) {
		$sql = "INSERT INTO $db_tb_scans (" . implode(array_keys($scan_data), ",") . ") VALUES (";
		$next = false;
		foreach ($scan_data as $key => $value)
		{
			if ($key != 'geb' &&
			    $key != 'plan' &&
			    $key != 'stat' &&
			    $key != 'def')
				$value = htmlentities(utf8_decode($value));
			if (isset($next) && $next)
				$sql .= ",";
			$sql .= "'$value'";
			$next = true;
		}
		$sql .= ")";
		debug_var("sql", $sql);
		$results[] = "Scan " . $scan_data['coords'] . " hinzugefügt.";
		if (isset($scan_data['geoscantime'])) {
	 		$sql1 = "UPDATE " . $db_tb_user . " SET geopunkte=geopunkte+1 " . " WHERE sitterlogin='" . $selectedusername . "'";
	 		$result_u = $db->db_query($sql1)
				or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
		}
	// UPDATE
	} else {
		$sql = "UPDATE $db_tb_scans SET ";
		$next = false;
		foreach ($scan_data as $key => $value) {
			if ($key != 'geb' &&
			    $key != 'plan' &&
			    $key != 'stat' &&
			    $key != 'def')
				$value = htmlentities(utf8_decode($value));
			if (isset($next) && $next)
				$sql .= ",";
			$sql .= "$key='$value'";
			$next = true;
		}
		$sql .= " WHERE coords_gal=" . $scan_data['coords_gal'] . " AND coords_sys=" . $scan_data['coords_sys'] . " AND coords_planet=" . $scan_data['coords_planet'];
		debug_var("sql", $sql);
		$results[] = "Scan " . $scan_data['coords'] . " aktualisiert.";
		if (isset($scan_data['geoscantime'])) {
	 		$sql1 = "UPDATE " . $db_tb_user . " SET geopunkte=geopunkte+1 " . " WHERE sitterlogin='" . $selectedusername . "'";
	 		$result_u = $db->db_query($sql1)
				or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
		}

	}
	$db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
	return $results;
}
?>