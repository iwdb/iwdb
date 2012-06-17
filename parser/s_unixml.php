<?php
/*****************************************************************************/
/* s_unixml.php                                                              */
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
/* Diese Erweiterung der urspünglichen DB ist ein Gemeinschafftsprojekt */
/* von IW-Spielern.                                                          */
/* Bei Problemen kannst du dich an das eigens dafür eingerichtete       */
/* Entwicklerforum wenden:                                                   */
/*                                                                           */
/*        httpd://handels-gilde.org/?www/forum/index.php;board=1099.0        */
/*                                                                           */
/*****************************************************************************/

error_reporting(E_ALL);

/*****************************************************************************/
/* bei speziellen Fragen:                     martinmartimeo / reuq tgarfeg  */
/* Modifikation für Runde 9 von:              Thella                         */
/*****************************************************************************/

if (basename($_SERVER['PHP_SELF']) != "index.php")
  die('Hacking attempt...!!');

if (!defined('IRA'))
	die('Hacking attempt...');

//*****************************************************************************
//

function arary_striponlys($xml_arr)
{
	if ( !is_array($xml_arr)) {
		return $xml_arr;
	} else {
		// Zahlen strippen, wenn nur eines da ist
		foreach ($xml_arr as $key => $value) {
 			if (count($value) == 1) {
				foreach ($value as $string) {
					// sollte nur eins sein, man weiss aber leider den Key nicht ^^
					$xml_arr[$key] = $string;
					if (is_array($string)) {
						$xml_arr[$key] = arary_striponlys($string);
					}
				}
			}
			if (count($value) > 1) {
				if (isset($debug)) {
					echo "<div style=color:blue'>";
					echo "<br>Count mit groesser als eins!";
					echo "<pre>";
					print_r($value);
					echo "</pre>";
					echo "</div>";
				}
				foreach ($value as $index => $string) {
					// sollte nur eins sein, man weiss aber leider den Key nicht ^^
					$xml_arr[$key][$index] = arary_striponlys($string);
				}
			}
		}
	}
	return $xml_arr;
}

//*****************************************************************************
// kann xml Dateien in Arrays bis zu einer Teife von 5 umwandlen
function xml_array($arrax) {
	$xml_arr = array();
	$where = 0;
	$count = array(0,0,0,0,0,0);
	$keys = array(0,0,0,0,0,0);
	$arraymix = 0;
	foreach ($arrax as $compressline) {
		$compressline = str_replace("&gt; &lt;", "&gt;\n&lt;", $compressline);
		$exploded = explode("\n", $compressline);
		foreach ($exploded as $stringline) {
			//hgaben wir hier ein Element, dann weiter zaehlen:
			if ( !( preg_match('/\&lt;.+\&gt;/',$stringline) == 0 ) ) $count[$where]++;
			if (isset($debug)) {
				echo "<pre style='color:blue'>Zaehler:<br>";
				print_r($keys);
				print_r($count);
				echo "</pre>";
			}
			$stringline = trim($stringline);
			//den Anfangsquatsch mit <?xml gefunden
			if ( !( preg_match('/^\&lt;\?xml.+\?\&gt;$/',$stringline) == 0 ) ) continue;
			if ( !( preg_match('/^\&lt;.+\/\&gt;$/',$stringline) == 0 ) ) {
				if (isset($debug))
					echo '<br>Eigenschaft (1): ' . $stringline;
				//jeweils einmal mit, einmal ohne; je nach PHP Version und magic_quotes
				$value = preg_replace('/.*value=\\\&quot;/','',$stringline);
				$value = preg_replace('/.*value=\&quot;/','',$value);
				$value = preg_replace('/\\\&quot;.*\/\&gt;/','',$value);
				$value = preg_replace('/\&quot;.*\/\&gt;/','',$value);
				if (isset($debug))
					echo "<div style='color:green'>Value: " . $value . "</div>";
				$key = str_replace('&lt;','',$stringline);
				$key = preg_replace('/\s*value=\&quot;.*/','',$key);
				$key = preg_replace('/\s*value=\\\&quot;.*/','',$key);
				if (isset($debug))
					echo "<div style='color:green'>Key: " . $key . "</div>";
				if ($where == 0) $xml_arr[$key][$count[0]] = $value;
	 			if ($where == 1) $xml_arr[$keys[0]][$count[0]][$key][$count[1]] = $value;
				if ($where == 2) $xml_arr[$keys[0]][$count[0]][$keys[1]][$count[1]][$key][$count[2]] = $value;
				if ($where == 3) $xml_arr[$keys[0]][$count[0]][$keys[1]][$count[1]][$keys[2]][$count[2]][$key][$count[3]] = $value;
				if ($where == 4) $xml_arr[$keys[0]][$count[0]][$keys[1]][$count[1]][$keys[2]][$count[2]][$keys[3]][$count[3]][$key][$count[4]] = $value;
				if ($where == 5) $xml_arr[$keys[0]][$count[0]][$keys[1]][$count[1]][$keys[2]][$count[2]][$keys[3]][$count[3]][$keys[4]][$count[4]][$key][$count[5]] = $value;
			} elseif ( !(preg_match('/^\&lt;\/.+\&gt;$/',$stringline) == 0) ) {
				$where = $where - 1;
				if (isset($debug))
					echo '<br>End Node: ' . $stringline;
			} elseif ( !(preg_match('/^\&lt;.+\&gt;.*\&lt;.+\&gt;$/',$stringline) == 0) ) {
				if (isset($debug))
					echo '<br>Eigenschaft (2): ' . $stringline;
				$value = preg_replace('/\&lt;\/.+\&gt;/','',$stringline);
				$value = preg_replace('/\&lt;.+\&gt;/','',$value);
				if (isset($debug))
					echo "<div style='color:green'>Value: " . $value . "</div>";
				$key = str_replace('&lt;','',$stringline);
				$key = preg_replace('/\&gt;.*/','',$key);
				$key = trim($key);
				if (isset($debug))
					echo "<div style='color:green'>Key: " . $key . "</div>";
				if ($where == 0) $xml_arr[$key][$count[0]] = $value;
				if ($where == 1) $xml_arr[$keys[0]][$count[0]][$key][$count[1]] = $value;
				if ($where == 2) $xml_arr[$keys[0]][$count[0]][$keys[1]][$count[1]][$key][$count[2]] = $value;
				if ($where == 3) $xml_arr[$keys[0]][$count[0]][$keys[1]][$count[1]][$keys[2]][$count[2]][$key][$count[3]] = $value;
				if ($where == 4) $xml_arr[$keys[0]][$count[0]][$keys[1]][$count[1]][$keys[2]][$count[2]][$key[3]][$count[3]][$key][$count[4]] = $value;
				if ($where == 5) $xml_arr[$keys[0]][$count[0]][$keys[1]][$count[1]][$keys[2]][$count[2]][$key[3]][$count[3]][$key[4]][$count[4]][$key][$count[5]] = $value;
			} elseif ( !(preg_match('/^\&lt;.+\&gt;$/',$stringline) == 0) ) {
				$key = str_replace('&lt;','',$stringline);
				$key = str_replace('&gt;','',$key);
				if (isset($debug))
					echo "<div style='color:green'>Key: " . $key . "</div>";
				$keys[$where] = $key;
				$where++;
				if (isset($debug))
					echo '<br>Anfangs Node: ' . $stringline;
			}
		}
	}
	if (isset($debug))  {
		echo "<pre style='color:purple'>";
		print_r( $xml_arr );
		echo "</pre>";
	}
	$xml_arr = arary_striponlys($xml_arr);
	return $xml_arr;
} // end xml_array();

//*****************************************************************************
//
function updateSysScan($sys, $coords_gal, $coords_sys) {
	global $db, $db_prefix, $selectedusername;
	$sql = "SELECT objekt,date,nebula FROM " . $db_prefix . "sysscans WHERE gal = " . $coords_gal . " AND sys = " . $coords_sys . ";";
	$result = $db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
	while ($row = $db->db_fetch_array($result)) {
		$existing_sys['objekt'] = $row['objekt'];
		$existing_sys['date'] = $row['date'];
		$existing_sys['nebula'] = $row['nebula'];
	}
	// Sysscan einfügen oder ersetzen
	if (isset($existing_sys) == 0) {
		$sql = "INSERT INTO " . $db_prefix . "sysscans (id,gal,sys,objekt,date,nebula) VALUES (";
		$sql .= "'" . $coords_gal . ":" . $coords_sys . "'";
		$sql .= "," . $coords_gal;
		$sql .= "," . $coords_sys;
		$sql .= ",'" . $sys['objekt'] . "'";
		$sql .= "," . $sys['date'];
		$sql .= ",'" . $sys['nebula'] . "'";
		$sql .= ")";
		if (isset($debug)) {
			echo "sql:$sql<br>";
		}
		$result = $db->db_query($sql)
			or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
	} else {
		$sql = "UPDATE " . $db_prefix . "sysscans SET ";
		$sql .= "id='" . $coords_gal . ":" . $coords_sys . "'";
		$sql .= ",objekt='" . $sys['objekt'] . "'";
		$sql .= ",date=" . $sys['date'];
		$sql .= ",nebula='" . $sys['nebula'] . "'";
		$sql .= " WHERE gal=" . $coords_gal;
		$sql .= " AND sys=" . $coords_sys;
		if (isset($debug)) {
			echo "sql:$sql<br>";
		}
		$result = $db->db_query($sql)
			or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
	}

	// Systemscans fuer den User +1 setzen
	$sql = "UPDATE `" . $db_prefix . "user` SET ";
	$sql .= "`syspunkte`=`syspunkte`+1";
	$sql .= " WHERE `sitterlogin`='" . $selectedusername . "'";
	if (isset($debug)) {
		echo "sql:$sql<br>";
	}
	$result = $db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
}

//*****************************************************************************
//
if (function_exists('parse_unixml') === false) {
	function parse_unixml($scanlines) {
		//$debug = 1;
		$arrayofxml = xml_array($scanlines);
		global $db, $db_prefix, $selectedusername;
		if (isset($debug)) {
			echo "<pre style='color:red'>";
			print_r($arrayofxml);
			echo "</pre>";
		}
		if ( !isset($arrayofxml['planeten_data']['informationen']['aktualisierungszeit'])
		     or empty($arrayofxml['planeten_data']['informationen']['aktualisierungszeit']) )
			die('Aktualisierungszeit nicht gefunden');
		$scantime = $arrayofxml['planeten_data']['informationen']['aktualisierungszeit'];
		//Wenn wir hier kein array haben... hat die Fritte keine Sicht gehabt.
		if (!isset($arrayofxml['planeten_data']['planet'])) {
			echo "<pre style='color:red'>Keine Planeten hinzugefügt... baue mehr Beobachtungseinrichtungen!\n</pre>";
			return;
		//Schwarzes Loch
		} else if (isset($arrayofxml['planeten_data']['planet']['planet_typ']) && $arrayofxml['planeten_data']['planet']['planet_typ'] == "schwarzes Loch") {
			$coords_gal = $arrayofxml['planeten_data']['planet']['koordinaten']['gal'];
			$coords_sys = $arrayofxml['planeten_data']['planet']['koordinaten']['sol'];
			$coords_planet = $arrayofxml['planeten_data']['planet']['koordinaten']['pla'];
			$sys['objekt'] = "Schwarzes Loch";
			$sys['date'] = $scantime;
			$sys['nebula'] = "";
			if (isset($debug)) {
				echo "<pre style='color:red'>Schwarzes Loch " . $coords_gal . ":" . $coords_sys . " erkannt\n</pre>";
			}
			updateSysScan($sys, $coords_gal, $coords_sys);
		} else {
			$last_sys = "0:0";
			//Iterieren der Planeten
			foreach ($arrayofxml['planeten_data']['planet'] as $key => $planet) {
				//Gala und System
				$coords_gal = $planet['koordinaten']['gal'];
				$coords_sys = $planet['koordinaten']['sol'];
				$coords_planet = $planet['koordinaten']['pla'];
				$coords = $planet['koordinaten']['string'];
				// Neues System?
				$curr_sys = $coords_gal . ":" . $coords_sys;
				if ($curr_sys != $last_sys) {
					$last_sys = $curr_sys;
					// alte Daten zuerst loeschen
					unset($existing_planis);
					// vorhandene Daten auslesen
					$sql = "SELECT coords,user,allianz,planetenname,typ,objekt FROM " . $db_prefix . "scans WHERE coords_gal = " . $coords_gal . " AND coords_sys = " . $coords_sys . ";";
					$result = $db->db_query($sql)
						or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
					while ($row = $db->db_fetch_array($result)) {
						$existing_planis[$row['coords']]['user'] = $row['user'];
						$existing_planis[$row['coords']]['allianz'] = $row['allianz'];
						$existing_planis[$row['coords']]['name'] = $row['planetenname'];
						$existing_planis[$row['coords']]['typ'] = $row['typ'];
						$existing_planis[$row['coords']]['objekt'] = $row['objekt'];
					}
					// Testen auf schwarzes Loch
					if ($planet['planet_typ'] == "schwarzes Loch") {
						$sys['objekt'] = "Schwarzes Loch";
						echo "<pre style='color:red'>Schwarzes Loch " . $coords_gal . ":" . $coords_sys . " erkannt\n</pre>";
					} else {
						$sys['objekt'] = "sys";
					}
					$sys['date'] = $scantime;
					// Nebel erkennen
					if (isset($planet['nebel'])) {
						switch ($planet['nebel']) {
							case "gelb": $sys['nebula'] = "GEN"; break;
							case "blau": $sys['nebula'] = "BLN"; break;
							case "rot": $sys['nebula'] = "RON"; break;
							case "gruen": $sys['nebula'] = "GRN"; break;
							case "violett": $sys['nebula'] = "VIN"; break;
							default: $sys['nebula'] = ""; break;
						}
					} else {
						$sys['nebula'] = "";
					}
					updateSysScan($sys, $coords_gal, $coords_sys);
				}
				if ($coords_planet > 0) {
					// Typaenderung?
					if (isset($existing_planis[$coords]) && $existing_planis[$coords]['typ'] <> $planet['planet_typ']) {
						$sql = "DELETE FROM " . $db_prefix . "scans";
						$sql .= " WHERE coords_gal=" . $coords_gal;
						$sql .= " AND coords_sys=" . $coords_sys;
						$sql .= " AND coords_planet=" . $coords_planet;
						if (isset($debug)) {
							echo "sql:$sql<br>";
						}
						$result = $db->db_query($sql)
							or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
						unset($existing_planis[$coords]);
					}
					// Raumstation bei Sysscans setzen
					if (($coords_planet == 1) && ($planet['planet_typ'] == "Nichts") && ($planet['objekt_typ'] == "Raumstation")) {
						$sql = "UPDATE " . $db_prefix . "sysscans SET ";
						$sql .= "objekt='Stargate'";
						$sql .= " WHERE gal=" . $coords_gal;
						$sql .= " AND sys=" . $coords_sys;
						$result = $db->db_query($sql)
							or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
						echo "<pre style='color:red'>Stargate " . $coords_gal . ":" . $coords_sys . " erkannt\n</pre>";
					}
					if (isset($existing_planis[$coords])) {
						$sql = "UPDATE " . $db_prefix . "scans SET ";
						$sql .= "user='" . $planet['user']['name'] . "'";
						$sql .= ",allianz='" . $planet['user']['allianz_tag'] . "'";
						$sql .= ",planetenname='" . $planet['name'] . "'";
						$sql .= ",typ='" . $planet['planet_typ'] . "'";
						$sql .= ",objekt='" . $planet['objekt_typ'] . "'";
						$sql .= ",plaid=" . $planet['id'];
						$sql .= " WHERE coords_gal=" . $coords_gal;
						$sql .= " AND coords_sys=" . $coords_sys;
						$sql .= " AND coords_planet=" . $coords_planet;
						if (isset($debug)) {
							echo "sql:$sql<br>";
						}
						$result = $db->db_query($sql)
							or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
						echo "<pre style='color:red'>Planet " . $coords_gal . ":" . $coords_sys . ":" . $coords_planet . " aktualisiert\n</pre>";
					} else {
						$sql = "INSERT INTO " . $db_prefix . "scans (coords,coords_gal,coords_sys,coords_planet,user,allianz,planetenname,typ,objekt,plaid) VALUES (";
						$sql .= "'" . $coords_gal . ":" . $coords_sys . ":" . $coords_planet . "'";
						$sql .= "," . $coords_gal;
						$sql .= "," . $coords_sys;
						$sql .= "," . $coords_planet;
						$sql .= ",'" . $planet['user']['name'] . "'";
						$sql .= ",'" . $planet['user']['allianz_tag'] . "'";
						$sql .= ",'" . $planet['name'] . "'";
						$sql .= ",'" . $planet['planet_typ'] . "'";
						$sql .= ",'" . $planet['objekt_typ'] . "'";
						$sql .= "," . $planet['id'] . ")";
						if (isset($debug)) {
							echo "sql:$sql<br>";
						}
						$result = $db->db_query($sql)
							or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
						echo "<pre style='color:red'>Planet " . $coords_gal . ":" . $coords_sys . ":" . $coords_planet . " hinzugefügt\n</pre>";
					}
				}
			}
		}
	}
}