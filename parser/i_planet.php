<?php
/*****************************************************************************/
/* i_planet.php                                                              */
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

  
//*****************************************************************************
//
// Gemeinsame Support-Funktionen für die Parser geoscan, schiffs
function reset_data()
{
	global $config_date;
  
	$scan_data = array();
	$scan_data['plan'] = ''; 
  $scan_data['stat'] = ''; 
  $scan_data['def']  = ''; 
  $scan_data['geb']  = '';
	$scan_data['time'] = $config_date;
  
	return $scan_data;
}
  
//*****************************************************************************
//
function updateplanet()
{
	global 
    $db, $db_tb_scans, $db_tb_user, $selectedusername, $config_date, 
    $config_scan_timeout, $scan_type, $scan_data;
	
  $user_sitterlogin = $selectedusername;

	if( $scan_type == 'geoscan' || 
      $scan_type == 'schiffsscan' || 
      $scan_type == 'gebaeudescan' )
	{
		$temp = explode(' ', $scan_data['coords']);
		$scan_data['coords'] = $temp[0];

		if ( isset($temp[1]) ) {
			if (substr($temp[1], -1, 1) == ")")	{
				$scan_data['user'] = substr($temp[1], 1, strlen($temp[1]) - 2);
			}	else {
				$scan_data['user'] = substr($temp[1], 1);
				$j = 2;
				while( (substr($temp[$j], -1, 1) != ")" ) && ( $j < count($temp) - 1) )	{
					$scan_data['user'] .= " " . trim($temp[$j]);
					$j++;
				}
				$scan_data['user'] .= " " . substr($temp[$j], 0, strlen($temp[$j]) - 1);
			}
		} else {
			$scan_data['user'] = '';
		}
		list($scan_data['coords_gal'], 
         $scan_data['coords_sys'], 
         $scan_data['coords_planet']) = explode(':', $scan_data['coords']);
	}
  
	if ($scan_type == 'schiffsscan') {
		$scan_data['schiffsscantime'] = time();
		echo 'schiff';
	}

	if( empty($scan_data['coords']) ||
      empty($scan_data['coords_sys']) || 
      empty($scan_data['coords_gal']) || 
      empty($scan_data['coords_planet']) )
	{
		return 0;
	}

	$sql = "SELECT coords, typ, objekt, user, time, geoscantime FROM " . $db_tb_scans . 
         " WHERE coords='" . $scan_data['coords'] . "'";
	$result = $db->db_query($sql)
		or error(GENERAL_ERROR, 
             'Could not query config information.', '', 
             __FILE__, __LINE__, $sql);
	$row = $db->db_fetch_array($result);

	if( isset($scan_data['geoscantime']) && !empty($row['geoscantime']) && ($row['geoscantime'] >= $scan_data['geoscantime']) ) {
		echo "<div class='system_notification'> Vorhandener Geoscan ist bereits aktuell oder neuer</div>\n";
		return 0;
	}

	if( ( $row['time'] < $config_date - $config_scan_timeout ) && 
      ( $scan_type == 'geoscan' )) 
  {
		$sql = "UPDATE " . $db_tb_user . " SET geopunkte=geopunkte+1 " . 
           " WHERE sitterlogin='" . $user_sitterlogin . "'";
		$result_u = $db->db_query($sql)
			or error(GENERAL_ERROR, 
               'Could not query config information.', '', 
               __FILE__, __LINE__, $sql);
	}

	if($scan_type == 'universum' )
		echo "<br>" . $scan_data['coords_planet'] . ":";
	else 
		echo "<br>" . $scan_data['coords'] . ":";

	// Thella: Für Modul 'm_aktivitaet.php'
	global $db_tb_scans_historie;
	if (!empty($db_tb_scans_historie) && $scan_type == 'universum' && !empty($scan_data['user']) && is_numeric($scan_data['punkte']))
	{
		$sql = "INSERT INTO " . $db_tb_scans_historie . " (`coords`,`time`,`coords_gal`,`coords_sys`,`coords_planet`,`user`,`allianz`,`punkte`) VALUES (" .
			"'" . $scan_data['coords'] . "'" .
			"," . $config_date .
			"," . $scan_data['coords_gal'] .
			"," . $scan_data['coords_sys'] .
			"," . $scan_data['coords_planet'] .
			",'" . $scan_data['user'] . "'" . 
			",'" . $scan_data['allianz'] . "'" .
			"," . $scan_data['punkte'] .
			")";
		$result = $db->db_query($sql)
			or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
	}

if ( ( trim($row['objekt']) != 'Kampfbasis' ) AND ( ( trim($row['typ']) <> trim($scan_data['typ']) ) OR ( ( trim($row['user']) != trim($scan_data['user']) ) && !empty($row['user']) ) ) ) {
		$sql = "DELETE FROM " . $db_tb_scans . 
           " WHERE coords='" . $scan_data['coords'] . "'";
		$result = $db->db_query($sql)
			or error(GENERAL_ERROR, 
               'Could not query config information.', '', 
               __FILE__, __LINE__, $sql);

		if(( $row['typ'] <> $scan_data['typ'] )) {
			echo "<div class='system_notification'> vorhandenen Geoscan wegen Typänderung gelöscht </div>\n";
		} else if(( $row['user'] != $scan_data['user'] && $row['user'] != "" )) {
			echo "<div class='system_notification'> vorhandenen Geoscan wegen Eigentümeränderung gelöscht </div>\n";
		}

		// Thella: Für Modul 'm_aktivitaet.php'
		if (!empty($db_tb_scans_historie))
		{
			$sql = "DELETE FROM " . $db_tb_scans_historie . 
				" WHERE coords='" . $scan_data['coords'] . "'";
			$result = $db->db_query($sql)
				or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
		}

  	foreach ($scan_data as $key => $data)	{
  		$sql_key = ( empty($sql_key) ) ? $key
                                     : $sql_key . ", " . $key;
  		$sql_data = ( empty($sql_data) ) ? "'" . $data . "'"
                                       : $sql_data . ", '" . $data . "'";
  	}

		$sql = "INSERT INTO " . $db_tb_scans . " (" . 
           $sql_key . ") VALUES (" . $sql_data . ")";
		$result = $db->db_query($sql)
			or error(GENERAL_ERROR, 
               'Could not query config information.', '', 
               __FILE__, __LINE__, $sql);
		return 3;
	}	elseif ( isset($row['coords']) ) {
		echo " aktualisiert\n";
		
		// alter.bridge: Reservierungen bei Besiedelung löschen
		if(($row['user'] == '') AND ($row['user'] != $scan_data['user'])) $update = "reserviert=''";

		foreach ($scan_data as $key => $data)	{
  		$update = ( empty($update) ) ? $key . "='" . $data . "'" 
                                   : $update . ", " . $key . "='" . $data . "'";
	  }

	  $sql = "UPDATE " . $db_tb_scans . " SET " . $update . 
           " WHERE coords='" . $scan_data['coords'] . "'";
	  $result = $db->db_query($sql)
		  or error(GENERAL_ERROR, 
               'Could not query config information.', '', 
               __FILE__, __LINE__, $sql);
		return 1;
	}	else {
		echo " neu eingefügt\n";
		foreach ($scan_data as $key => $data)	{
			$sql_key = ( empty($sql_key) ) ? $key
                                     : $sql_key . ", " . $key;
			$sql_data = ( empty($sql_data) ) ? "'" . $data . "'"
                                       : $sql_data . ", '" . $data . "'";
		}

		$sql = "INSERT INTO " . $db_tb_scans . " (" . $sql_key .
           ") VALUES (" . $sql_data . ")";
		$result = $db->db_query($sql)
			or error(GENERAL_ERROR, 
               'Could not query config information.', '', 
               __FILE__, __LINE__, $sql);
		return 2;
	}
}

//*****************************************************************************
//
function checkline($scan, &$scan_data, &$cat) {
	global $scan_type;

  if ( strpos($scan, "Planetentyp ") !== FALSE ) {
	  $scan_data['typ'] = trim(str_replace("Planetentyp", "", $scan));
		$cat = '';
	}	elseif ( strpos($scan, "Objekttyp ") !== FALSE ) {
		$scan_data['objekt'] = trim(str_replace("Objekttyp", "", $scan));
		$cat = '';
	}	elseif ( strpos($scan, "Vorkommen Eisen ") !== FALSE ) {
		$scan_data['eisengehalt'] = trim(str_replace("\%", "", str_replace("Vorkommen Eisen", "", $scan)));
		$cat = '';
	}	elseif ( strpos($scan, "Vorkommen chem. Elemente ") !== FALSE ) {
		$scan_data['chemievorkommen'] = trim(str_replace("\%", "", str_replace("Vorkommen chem. Elemente", "", $scan)));
		$cat = '';
	}	elseif ( strpos($scan, "Vorkommen Eis ") !== FALSE ) {
		$scan_data['eisdichte'] = trim(str_replace("\%", "", str_replace("Vorkommen Eis", "", $scan)));
		$cat = '';
	}	elseif ( strpos($scan, "Lebensbedingungen ") !== FALSE ) {
		$scan_data['lebensbedingungen'] = trim(str_replace("\%", "", str_replace("Lebensbedingungen", "", $scan)));
		$cat = '';
	}	elseif ( strpos($scan, "Gravitation ") !== FALSE ) {
		$scan_data['gravitation'] = trim(str_replace("Gravitation", "", $scan));
		$cat = '';
	}	elseif ( strpos($scan, "Besonderheiten ") !== FALSE ) {
		$scan_data['besonderheiten'] = trim(str_replace("Besonderheiten", "", $scan));
		$cat = 'besonderheiten';
	}	elseif ( strpos($scan, "Forschungmod. ") !== FALSE ) {
		$scan_data['fmod'] = trim(str_replace("\%", "", str_replace("Forschungmod.", "", $scan)));
		$cat = '';
	}	elseif ( strpos($scan, "Gebäudebau Kosten Mod. ") !== FALSE ) {
		$scan_data['kgmod'] = trim(str_replace("Gebäudebau Kosten Mod.", "", $scan))/100;
		$cat = '';
	}	elseif ( strpos($scan, "Gebäudebau Dauer Mod. ") !== FALSE ) {
		$scan_data['dgmod'] = trim(str_replace("Gebäudebau Dauer Mod.", "", $scan))/100;
		$cat = '';
	}	elseif ( strpos($scan, "Schiffbau Kosten Mod. ") !== FALSE ) {
		$scan_data['ksmod'] = trim(str_replace("Schiffbau Kosten Mod.", "", $scan))/100;
		$cat = '';
	}	elseif ( strpos($scan, "Schiffbau Dauer Mod. ") !== FALSE ) {
		$scan_data['dsmod'] = trim(str_replace("Schiffbau Dauer Mod.", "", $scan))/100;
		$cat = '';
	}	elseif ( strpos($scan, "Mit Hilfe eines Tech-Teams kann das Vorkommen von Eisen auf insgesamt ") !== FALSE ) {
		$scan_data['tteisen'] = trim(str_replace("Mit Hilfe eines Tech-Teams kann das Vorkommen von Eisen auf insgesamt", "", $scan));
		$cat = '';
	}	elseif ( strpos($scan, "Mit Hilfe eines Tech-Teams kann das Vorkommen von chem. Elemente auf insgesamt ") !== FALSE ) {
		$scan_data['ttchemie'] = trim(str_replace("Mit Hilfe eines Tech-Teams kann das Vorkommen von chem. Elemente auf insgesamt", "", $scan));
		$cat = '';
	}	elseif ( strpos($scan, "Mit Hilfe eines Tech-Teams kann das Vorkommen von Eis auf insgesamt ") !== FALSE ) {
		$scan_data['tteis'] = trim(str_replace("Mit Hilfe eines Tech-Teams kann das Vorkommen von Eis auf insgesamt", "", $scan));
		$cat = '';
//moep-edit-start
	}	elseif ( strpos($scan, "Dieser Planet wird vorraussichtlich am ") !== FALSE ) {
			// doc_message('zerstörung');                               T1(d)          T2(h)  T3(m)   T4(s)
			// if( preg_match('/Dieser Planet wird vorraussichtlich in\s(\d{1,2})\sTage\s(\d{2}):(\d{2}):(\d{2})/', $scan, $treffer) ) {
				// $scan = trim($scan);            ((             [Stunden]                       )*60)
				// $scan_data['reset_timestamp'] = (((($treffer[1]*24)+$treffer[2])*60+$treffer[3])*60+$treffer[2]);
			// }
			if( preg_match('/Dieser Planet wird vorraussichtlich am\s(\d{2}).(\d{2}).(\d{4})\sgegen\s(\d{2}):(\d{2})/', $scan, $treffer)) {
				$scan = trim($scan);
				$spreng_date = mktime($treffer[4], $treffer[5], 0, $treffer[2], $treffer[1], $treffer[3], -1);
				$scan_data['reset_timestamp'] = $spreng_date - $scan_data['geoscantime'];
			}
//moep-edit-end
	}	elseif ( strpos($scan, "Schiffe") !== FALSE ) {
		$cat = '';
	}	elseif ( strpos($scan, "Ress") !== FALSE ) {
		$cat = '';
	}	elseif ( strpos($scan, "Planetare Flotte") !== FALSE ) {
		$cat = 'planflotte';
	}	elseif ( strpos($scan, "Stationierte Flotte") !== FALSE ) {
		$cat = 'statflotte';
	}	elseif ( strpos($scan, "Defence") !== FALSE ) {
		$cat = 'defence';
	}	elseif ( strpos($scan, "Eisen ") !== FALSE ) {
		$scan_data['eisen'] = trim(str_replace("Eisen", "", $scan));
		$cat = '';
	}	elseif ( strpos($scan, "Stahl ") !== FALSE ) {
		$scan_data['stahl'] = trim(str_replace("Stahl", "", $scan));
		$cat = '';
	}	elseif ( strpos($scan, "VV4A ") !== FALSE ) {
		$scan_data['vv4a'] = trim(str_replace("VV4A", "", $scan));
		$cat = '';
	}	elseif ( strpos($scan, "chem. Elemente ") !== FALSE ) {
		$scan_data['chemie'] = trim(str_replace("chem. Elemente", "", $scan));
		$cat = '';
	}	elseif ( strpos($scan, "Eis ") !== FALSE ) {
		$scan_data['eis'] = trim(str_replace("Eis", "", $scan));
		$cat = '';
	}	elseif ( strpos($scan, "Wasser ") !== FALSE ) {
		$scan_data['wasser'] = trim(str_replace("Wasser", "", $scan));
		$cat = '';
	}	elseif ( strpos($scan, "Energie ") !== FALSE ) {
		$scan_data['energie'] = trim(str_replace("Energie", "", $scan));
		$cat = '';
	} else {
		if ( $cat == 'besonderheiten' ) {
			$scan_data[$cat] .= ", " . trim($scan);
		}	elseif ( $cat == 'planflotte' ) {
			$schiff_name = '';
			$schiffe = explode(' ', trim($scan));
			for ($j = 0; $j < count($schiffe) - 1; $j++) 
        $schiff_name .= ($schiffe[$j] . " ");
        
			$scan_data['plan'] .= trim($schiff_name) . "|" . $schiffe[(count($schiffe) - 1)] . "\n";
		}	elseif ( $cat == 'statflotte' ) {
			$schiff_name = '';
			$schiffe = explode(' ', trim($scan));
			for ($j = 0; $j < count($schiffe) - 1; $j++) 
        $schiff_name .= ($schiffe[$j] . " ");
        
			$scan_data['stat'] .= trim($schiff_name) . "|" . $schiffe[(count($schiffe) - 1)] . "\n";
		}	elseif ( $cat == 'defence' ) {
			$def_name = '';
			$defs = explode(' ', trim($scan));
			for ($j = 0; $j < count($defs) - 1; $j++) 
        $def_name .= ($defs[$j] . " ");
        
			$scan_data['def'] .= trim($def_name) . "|" . $defs[(count($defs) - 1)] . "\n";
		}	elseif ( $scan_type == 'gebaeudescan' ) {
			$geb_name = '';
			$gebs = explode(' ', trim($scan));
			for ($j = 0; $j < count($gebs) - 1; $j++) 
        $geb_name .= ($gebs[$j] . " ");
			$scan_data['geb'] .= trim($geb_name) . "|" . $gebs[(count($gebs) - 1)] . "\n";
		}
	}  
}
?>
