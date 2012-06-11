<?php
/*****************************************************************************/
/* de_index.php                                                     */
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
/* Diese Erweiterung der urspuenglichen DB ist ein Gemeinschafftsprojekt von */
/* IW-Spielern.                                                              */
/*                                                                           */
/* Autor: Mac (MacXY@herr-der-mails.de)                                      */
/* Datum: Jun 2009 - April 2012                                              */
/*                                                                           */
/* Bei Problemen kannst du dich an das eigens dafür eingerichtete            */
/* Entwicklerforum wenden:                                                   */
/*                   http://www.iw-smf.pericolini.de                         */
/*                   https://github.com/iwdb/iwdb                            */
/*                                                                           */
/*****************************************************************************/


if (!defined('DEBUG_LEVEL'))
	define('DEBUG_LEVEL', 0);

if (basename($_SERVER['PHP_SELF']) != "index.php")
  die('Hacking attempt...!!');

if (!defined('IRA'))
	die('Hacking attempt...');

error_reporting(E_ALL);

function parse_de_index ( $return )
{
    global $db, $db_tb_scans, $db_tb_user_research, $selectedusername, $scan_datas;

    if (!$return->objResultData->bOngoingResearch)     //! keine laufende Forschung
    {
		$time=time();
		$sql = "UPDATE
			$db_tb_user_research
		SET
			rId = '0',
			date = '',
			time = '$time'
		WHERE
			user = '$selectedusername'";
		mysql_query($sql) OR die(mysql_error());						
    }
    
	foreach ($return->objResultData->aContainer as $aContainer)
	{
		if ($aContainer->bSuccessfullyParsed)
   		{
			if ($aContainer->strIdentifier == "de_index_fleet")
			{
				$fleetType = $aContainer->objResultData->strType;	//! own OR opposite
                if (!$aContainer->objResultData->bObjectsVisible)
                     echo "<font color='orange'>Info: </font> keine Transportinformation (" . $fleetType . ") sichtbar. Bitte Fluginformationen vor dem Parsen ausklappen";
               
				foreach ($aContainer->objResultData->aFleets as $msg)
				{	
					$tf_type = $msg->eTransfairType;
         
                    //! Mac: fehlt noch
//	     $scan_data['art'] == 'Ressourcen abholen' ||
//	     $scan_data['art'] == 'Ressourcenhandel' ||
//	     $scan_data['art'] == 'Ressourcenhandel (ok)' ||
//	     $scan_data['art'] == 'Stationieren' ||
//	     $scan_data['art'] == 'Kolonisation' ||
     
					if ( $tf_type == "Rückkehr") {	//! keine weiteren Infos vorhanden
						continue;		
					}
					else if ($tf_type == "Übergabe" || $tf_type == "Transport" || $tf_type == "Übergabe (tr Schiffe)" || $tf_type == "Massdriverpaket"
                          || $tf_type == "Sondierung (Schiffe/Def/Ress)" || $tf_type == "Angriff"
                          || $tf_type == "Sondierung (Gebäude/Ress)" || $tf_type == "Sondierung (Schiff) (Scout)"
                          || $tf_type == "Sondierung (Gebäude) (Scout)" || $tf_type == "Sondierung (Geologie) (Scout)"
                          || $tf_type == "Sondierung (Geologie)"
                            ) {
                        
                        $scan_data = array();
                        if ($fleetType == "own")
                            $scan_data['user_from'] = $selectedusername;
                        else 
                            $scan_data['user_from'] = $msg->strUserNameFrom;

                        $scan_data['planet_to'] = $msg->strPlanetNameTo;
                        $scan_data['coords_to_gal'] = $msg->aCoordsTo["coords_gal"];
                        $scan_data['coords_to_sys'] = $msg->aCoordsTo["coords_sol"];
                        $scan_data['coords_to_planet'] = $msg->aCoordsTo["coords_pla"];
                        
                        $scan_data['planet_from'] = $msg->strPlanetNameFrom;
                        $scan_data['coords_from_gal'] = $msg->aCoordsFrom["coords_gal"];
                        $scan_data['coords_from_sys'] = $msg->aCoordsFrom["coords_sol"];
                        $scan_data['coords_from_planet'] = $msg->aCoordsFrom["coords_pla"];
                        
                        $scan_data['art'] = $tf_type;
                        
                        // Zeitstempel
                        if ($tf_type == "Transport" && !empty($msg->iAnkunft)) 	//! Ausladezeit: +5min
							$scan_data['time'] = $msg->iAnkunft + 5*60;
						else
							$scan_data['time'] = $msg->iAnkunft;

                        if (!isset($scan_data['user_to']) || empty($scan_data['user_to'])) {
                            $scan_data['user_to'] = "";
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
                        if (!isset($scan_data['user_from']) || empty($scan_data['user_from'])) {
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
        
                        if (!isset($scan_data['schiffe']))
                            $scan_data['schiffe'] = array();
                        
                        //! Mac: gelieferte Ress/Schiffe eintragen
						foreach ($msg->aObjects as $object)
						{												
                            $typ = $object["object"];
                            $menge = $object["count"];
                            
                            if ($typ != 'Eisen' && $typ != 'Stahl' && $typ != 'VV4A' && $typ != 'chem. Elemente' && $typ != 'Eis' && $typ != 'Wasser' && $typ != 'Energie')
                            {
                                $scan_data['schiffe'][$typ] = $menge;
                            }
                            else 
                                $scan_data['pos'][$typ] = $menge;
						}
                        
                        // Daten speichern
                        save_data($scan_data);
                        $scan_datas[] = $scan_data;
					}
                    else {
                        echo "<font color='red'>unknown transfer_type detected: " .$tf_type."</font>";
                        continue;
                    }	
				}
			}	//! index_fleet
			else if (($aContainer->bSuccessfullyParsed) &&  ($aContainer->strIdentifier == "de_index_ressourcen"))
			{
				//! Mac: @todo: Ressourcen auf dem aktuellen Planeten auswerten
			}
			else if ($aContainer->strIdentifier == "de_index_research") 
			{
                $sql = "DELETE FROM " . $db_tb_user_research . 
            			" WHERE user='" . $selectedusername . "'";
                $result = $db->db_query($sql)
                            or error(GENERAL_ERROR, 
                            'Could not query config information.', '', 
                            __FILE__, __LINE__, $sql);

                $time = time();
				
				foreach ($aContainer->objResultData->aResearch as $msg)
                {	
                    $rid = find_research_id($msg->strResearchName);
                    if ($rid != 0) {
                        $sql = "INSERT INTO " . $db_tb_user_research . 
                                " SET user='" . $selectedusername . "', rId='" . $rid . "', date=" . $msg->iResearchEnd . ", time=" . $time ;
                        $result = $db->db_query($sql)
                                    or error(GENERAL_ERROR, 
                                    'Could not query config information.', '', 
                                    __FILE__, __LINE__, $sql);
                    }
                }
			}	
			else if ($aContainer->strIdentifier == "de_index_geb") 
			{
				if (!isset($aContainer->objResultData->aGeb)) continue;
				foreach ($aContainer->objResultData->aGeb as $msg)
				{
					//! Mac: @todo: laufende Gebaeude auswerten, ggf. aus Sitting entfernen
				}
			}
			else if ($aContainer->strIdentifier == "de_index_schiff")
			{
				foreach ($aContainer->objResultData->aSchiff as $plan)
				{
				   foreach ($plan as $ship_types)
				   {
                       //! Mac: @todo: laufende Schiffe auswerten, ggf. aus Sitting entfernen oder Auftraege schieben
                   }
				}
			}
		}	
		else		//! successfully parsed
   		{
            foreach ($aContainer->aErrors as $msg)
                echo $msg."<br>";
   		} 
	}		//! for each container
}

function find_research_id($researchname) {
	global $db, $db_tb_research;

	// Find first research identifier
	$sql = "SELECT ID FROM " . $db_tb_research . " WHERE name='" . $researchname . "'";
	$result = $db->db_query($sql)
		or error(GENERAL_ERROR,
             'Could not query config information.', '',
             __FILE__, __LINE__, $sql);
	$row = $db->db_fetch_array($result);

	// Not found, so insert new
	if(empty($row)) 
		return 0;
    else
        return $row['ID'];
}

function save_data($scan_data) {
	global $db, $db_tb_lieferung, $db_tb_scans;
    
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
	} elseif (($scan_data['art'] == "Sondierung (Schiffe/Def/Ress)") || ($scan_data['art'] == "Sondierung (Gebäude/Ress)")){
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

function display_de_index() {
	global $scan_datas;
	
	if (is_array($scan_datas)) {
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