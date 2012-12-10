<?php
/*****************************************************************************/
/* de_info_forschung.php                                                     */
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
/*        httpd://handels-gilde.org/?www/forum/index.php;board=1099.0        */
/*                   https://github.com/iwdb/iwdb                            */
/*                                                                           */
/*****************************************************************************/

if (basename($_SERVER['PHP_SELF']) != "index.php")
  die('Hacking attempt...!!');

if (!defined('IRA'))
	die('Hacking attempt...');

error_reporting(E_ALL);

function parse_de_info_forschung ( $return )
{
	$scan_data = array();
    $research = $return->objResultData;
    
    $scan_data['research'] = $research->strResearchName;
    $scan_data['description'] = $research->strResearchComment;
	$scan_data['addcost'] = '';
    $scan_data['FP'] = $research->iFP;
    $scan_data['gebiet'] = $research->strAreaName;

    if ($research->bIsPrototypResearch)
        $scan_data['Prototyp'] = $research->strPrototypName;
    
    $scan_data['NeededResearch'] = $research->aResearchsNeeded;
    $scan_data['NeededBuildings']  = $research->aBuildingsNeeded;
    $scan_data['NeededObject'] = $research->aObjectsNeeded;
    $scan_data['GivesResearch'] = $research->aResearchsDevelop;
    $scan_data['GivesBuildingLevel'] = $research->aBuildingLevelsDevelop;
    $scan_data['GivesBuilding'] = $research->aBuildingsDevelop;
    $scan_data['GivesDefense'] = $research->aDefencesDevelop;
    $scan_data['GivesGenetics'] = $research->aGeneticsDevelop;
    
//	foreach ($scan_data as $key =>$datenzeile) 
//        if (is_array($datenzeile))
//            echo "$key=".implode(",",$datenzeile),"<br>";
//        else
//            echo "$key=".$datenzeile,"<br>";
	update_research($scan_data);
    
}

// ****************************************************************************
//
function display_de_info_forschung() {
	 include "./modules/m_research.php";
}

// ****************************************************************************
//
//
function update_research($scan_data) {
	global $db, $db_tb_research, $db_tb_research2prototype, $researchid, $db_tb_researchfield,
				 $db_tb_research2research, $db_tb_research2building, $config_gameversion;

  // Anpassung des Forschungstitels
	$scan_data['research'] = str_replace("(en) (mittels eines P&uuml;mpels)",
                                       "(en)(mittels eines P&uuml;mpels)",
                                       $scan_data['research']);

    $rid = find_research_id($scan_data['research'], TRUE);
	if(empty($rid)) {
	  echo "<div class='system_error'>Update der Forschung " . $scan_data['research'] . " fehlgeschlagen.</div>\n";
		return;
	}

	echo "<div class='system_notification'>Update der Forschung " . $scan_data['research'] . "</div>\n";
	// Forschungsgebiet heraussuchen
	$sql = "SELECT ID FROM " . $db_tb_researchfield .
         " WHERE name='" . $scan_data['gebiet'] . "'";
	$result = $db->db_query($sql)
		or error(GENERAL_ERROR,
             'Could not query config information.', '',
             __FILE__, __LINE__, $sql);
	$row = $db->db_fetch_array($result);
	$gebiet = $row['ID'];
	if(empty($gebiet)) {
        $sql = "INSERT INTO " . $db_tb_researchfield .
            " (name) VALUES ( '".$scan_data['gebiet']."' ) ";
        $result = $db->db_query($sql)
            or error(GENERAL_ERROR,
                'Could not query config information.', '',
                __FILE__, __LINE__, $sql);
        $sql = "SELECT ID FROM " . $db_tb_researchfield .
            " WHERE name='" . $scan_data['gebiet'] . "'";
        $result = $db->db_query($sql)
            or error(GENERAL_ERROR,
                'Could not query config information.', '',
                __FILE__, __LINE__, $sql);
        $row = $db->db_fetch_array($result);
        $gebiet = $row['ID'];
        if(empty($gebiet)) {
            $gebiet = 1;
        }
    }

    // Abgeleitete Gebaeude, Forschungen und Prototypen entfernen
    $sql = "DELETE FROM " . $db_tb_research2building .
         " WHERE rId=" . $rid;
	$result = $db->db_query($sql)
		or error(GENERAL_ERROR,
             'Could not query config information.', '',
             __FILE__, __LINE__, $sql);

    $sql = "DELETE FROM " . $db_tb_research2prototype .
         " WHERE rId=" . $rid;
	$result = $db->db_query($sql)
		or error(GENERAL_ERROR,
             'Could not query config information.', '',
             __FILE__, __LINE__, $sql);

    $sql = "DELETE FROM " . $db_tb_research2research .
         " WHERE rOld=" . $rid . " OR rNew=" . $rid;
	$result = $db->db_query($sql)
		or error(GENERAL_ERROR,
             'Could not query config information.', '',
             __FILE__, __LINE__, $sql);

    // Deklarationen aufsplitten
	$declares="";
    if(!empty($scan_data['GivesDeclares'])) {
        foreach($scan_data['GivesDeclares'] as $decl) {
            if( !empty($declares)) {
                $declares .= "<br />\n";
            }
            $declares .= $decl;
        }
	}

    // Objekte aufsplitten
	$objects="";
    if(!empty($scan_data['NeededObject'])) {
        foreach($scan_data['NeededObject'] as $obj) {
            if( !empty($objects)) {
            	$objects .= "<br />\n";
            }
            $objects .= $obj;
        }
    }

    // Gebaeude-Stufen herausfiltern und in die DB eintragen
	$bldlvl="";
    if(!empty($scan_data['GivesBuildingLevel'])) {
        foreach($scan_data['GivesBuildingLevel'] as $lvl) {
            if( !empty($bldlvl)) {
                $bldlvl .= "<br />\n";
            }
            $bldlvl .= $lvl;

            $lvltext = explode(" Stufe ", $lvl);
            insert_building_on_research($scan_data['research'], $lvltext[0], $lvltext[1]);
        }
    }

    // Verteidigungsoptionen eintragen
	$defense="";
    if(!empty($scan_data['GivesDefense'])) {
        foreach($scan_data['GivesDefense'] as $def) {
            if( !empty($defense)) {
                $defense .= "<br />\n";
            }
            $defense .= $def;
        }
    }

    // Genetische Optionen eintragen
	$genetics="";
    if(!empty($scan_data['GivesGenetics'])) {
        foreach($scan_data['GivesGenetics'] as $gen) {
            if( !empty($genetics)) {
                $genetics .= "<br />\n";
            }
            $genetics .= $gen;
        }
    }

    $highscore = empty($scan_data['highscore']) ? 0
                                              : $scan_data['highscore'];

    $fp = empty($scan_data['FP']) ? 0 : $scan_data['FP'];

    // So, wir haben jetzt alles zusammen und können die Daten eintragen.
	$sql = "UPDATE " . $db_tb_research . " SET " .
         "gameversion='" . $config_gameversion . "', " .
	       "Gebiet=" . $gebiet . "," .
				 "description='" . $scan_data['description'] . "', " .
				 "FP=" . $fp . ", " .
				 "highscore=" . $highscore . ", " .
				 "addcost='" . $scan_data['addcost'] . "', " .
				 "declarations='" . $declares . "', " .
				 "objects='" . $objects . "', " .
				 "defense='" . $defense . "', " .
				 "genetics='" . $genetics . "', " .
				 "geblevels='" . $bldlvl . "' " .
				 "WHERE ID=" . $rid;

	$result = $db->db_query($sql)
		or error(GENERAL_ERROR,
             'Could not query config information.', '',
             __FILE__, __LINE__, $sql);

    // Check if the prototype is in the database and set the research/ship pair.
	if(!empty($scan_data['Prototyp'])) {
        $pid = find_ship_id($scan_data['Prototyp']);

        $sql = "INSERT INTO " . $db_tb_research2prototype . "(rid, pid)" .
           " VALUES(" . $rid . ", " . $pid . ")";
        $result = $db->db_query($sql)
            or error(GENERAL_ERROR,
               'Could not query config information.', '',
               __FILE__, __LINE__, $sql);
	}

	// Actual research depends on these researches
	if(!empty($scan_data['NeededResearch'])) {
	  foreach($scan_data['NeededResearch'] as $depres) {
		  insert_research_on_research($depres, $scan_data['research']);
		}
	}

	// Actual research gives these researches
	if(!empty($scan_data['GivesResearch'])) {
	  foreach($scan_data['GivesResearch'] as $depres) {
		  insert_research_on_research($scan_data['research'], $depres);
		}
	}

	// Actual research needs these buildings
	if(!empty($scan_data['NeededBuildings'])) {
	  foreach($scan_data['NeededBuildings'] as $depres) {
		  insert_research_on_building($scan_data['research'], $depres);
		}
	}

	// Actual research gives these buildings
	if(!empty($scan_data['GivesBuilding'])) {
	  foreach($scan_data['GivesBuilding'] as $depres) {
		  insert_building_on_research($scan_data['research'], $depres, 0);
		}
	}

    $researchid = $rid;
}

// ****************************************************************************
//
//
function find_research_id($researchname, $hidenew) {
	global $db, $db_tb_research, $user_id;

	// Find first research identifier
	$sql = "SELECT ID FROM " . $db_tb_research . " WHERE name='" . $researchname . "'";
	$result = $db->db_query($sql)
		or error(GENERAL_ERROR,
             'Could not query config information.', '',
             __FILE__, __LINE__, $sql);
	$row = $db->db_fetch_array($result);

	// Not found, so insert new
	if(empty($row)) {
		$sql2 = "INSERT INTO " . $db_tb_research . "(name,reingestellt) VALUES('" . $researchname . "','" . $user_id . "')";
  	$result = $db->db_query($sql2)
  		or error(GENERAL_ERROR,
               'Could not query config information.', '',
               __FILE__, __LINE__, $sql);

  	$result = $db->db_query($sql)
		  or error(GENERAL_ERROR,
               'Could not query config information.', '',
               __FILE__, __LINE__, $sql);
		$row = $db->db_fetch_array($result);

		if($hidenew === FALSE) {
			echo "<div class='doc_blue'>Neue Forschung: " . $researchname . "</div>\n";
		}
	}

  return $row['ID'];
}

//****************************************************************************
// Replace htmlentities with normal character. Could have used
// html_entities_decode instead, but this function exists only
// after PHP 4.3.0.
//
function unhtmlentities($string)
{
   // Ersetzen numerischer Darstellungen
   $string = preg_replace('~&#x([0-9a-f]+);~ei', 'chr(hexdec("\\1"))', $string);
   $string = preg_replace('~&#([0-9]+);~e', 'chr("\\1")', $string);
   // Ersetzen benannter Zeichen
   $trans_tbl = get_html_translation_table(HTML_ENTITIES);
   $trans_tbl = array_flip($trans_tbl);
   return strtr($string, $trans_tbl);
}

// ****************************************************************************
//
//
function find_building_id($name) {
	global $db, $db_tb_gebaeude;

  // Änderungen für Gebäude, die bereits unter anderem Namen in der DB sind.
	if(strpos($name, "Glace") > 0) {
	  $name = "Eiscrusher der Sirius Corp, Typ Glace la mine";
	}
	$name = str_replace("[Solar]", "(Solar)", $name);
	$name = str_replace("[orbital]", "(orbital)", $name);

	$name = str_replace("(Kampfbasis)", "", $name);
	$name = str_replace(" mittels Phasenfluxdehydrierung", "", $name);
	$name = str_replace("kleiner chemischer Fabrikkomplex", "Chemiekomplex", $name);
	$name = str_replace("Katzeundmausabwehrstockproduktionsfabrik",
                      "Katze und Maus Stock Abwehrfabrik", $name);

	// Try without entities first, before inserting a new one.
    $name2 = unhtmlentities($name);

	$sql3 = "SELECT ID FROM " . $db_tb_gebaeude . " WHERE name='" . $name2 . "'";
	$result = $db->db_query($sql3)
		or error(GENERAL_ERROR,
             'Could not query config information.', '',
             __FILE__, __LINE__, $sql3);
	$row = $db->db_fetch_array($result);

	// Find first building identifier (with entities)
  if(empty($row)) {
  	$sql = "SELECT ID FROM " . $db_tb_gebaeude . " WHERE name='" . $name . "'";
  	$result = $db->db_query($sql)
  		or error(GENERAL_ERROR,
               'Could not query config information.', '',
               __FILE__, __LINE__, $sql);
  	$row = $db->db_fetch_array($result);
  }

	// Not found, so insert new (with entities this time)
	if(empty($row)) {
		$sql2 = "INSERT INTO " . $db_tb_gebaeude . "(name) VALUES('" . $name . "')";
  	$result = $db->db_query($sql2)
  		or error(GENERAL_ERROR,
               'Could not query config information.', '',
               __FILE__, __LINE__, $sql2);

  	$result = $db->db_query($sql)
  		or error(GENERAL_ERROR,
               'Could not query config information.', '',
               __FILE__, __LINE__, $sql2);
		$row = $db->db_fetch_array($result);

		echo "<div class='doc_blue'>Neues Geb&auml;ude: " . $name . "</div>\n";
	}

  return $row['ID'];
}

// ****************************************************************************
//
//
function insert_building_on_research($research, $building, $level) {
	global $db, $db_tb_research2building;

  $resid = find_research_id($research, FALSE);
  $bldid = find_building_id($building);

  $sql = "SELECT COUNT(*) AS Zahl FROM " . $db_tb_research2building .
         " WHERE rId=" . $resid . " AND bId=" . $bldid . " AND lvl=" . $level;
 	$result = $db->db_query($sql)
 		or error(GENERAL_ERROR,
             'Could not query config information.', '',
             __FILE__, __LINE__, $sql);
 	$row = $db->db_fetch_array($result);
 	$anzahl = $row['Zahl'];

  if($anzahl == 0) {
    $sql = "INSERT INTO " . $db_tb_research2building . "(rId, bId, lvl)" .
           " VALUES(" . $resid . ", " . $bldid . ", " . $level . ")";
   	$result = $db->db_query($sql)
   		or error(GENERAL_ERROR,
               'Could not query config information.', '',
               __FILE__, __LINE__, $sql);
  }
}

// ****************************************************************************
//
//
function find_ship_id($shipname) {
	global $db, $db_tb_schiffstyp;

	// Find first ship identifier
	$sql = "SELECT ID FROM " . $db_tb_schiffstyp . " WHERE schiff='" . $shipname . "'";
	$result = $db->db_query($sql)
		or error(GENERAL_ERROR,
             'Could not query config information.', '',
             __FILE__, __LINE__, $sql);
	$row = $db->db_fetch_array($result);

	// Not found, so insert new
	if(empty($row)) {
		$sql2 = "INSERT INTO " . $db_tb_schiffstyp . "(schiff) VALUES('" . $shipname . "')";
  	$result = $db->db_query($sql2)
  		or error(GENERAL_ERROR,
               'Could not query config information.', '',
               __FILE__, __LINE__, $sql2);

  	// Repeat find ship identifier
  	$result = $db->db_query($sql)
  		or error(GENERAL_ERROR,
               'Could not query config information.', '',
               __FILE__, __LINE__, $sql);
		$row = $db->db_fetch_array($result);

		echo "<div class='doc_blue'>Neuer Schiffstyp: " . $shipname . "</div>\n";
	}

  return $row['ID'];
}

// ****************************************************************************
//
//
function insert_research_on_research($oldres, $newres) {
	global $db, $db_tb_research2research;

  $oldid = find_research_id($oldres, FALSE);
  $newid = find_research_id($newres, FALSE);

  $sql = "SELECT COUNT(*) AS Zahl FROM " . $db_tb_research2research .
         " WHERE rNew=" . $newid . " AND rOld=" . $oldid;
 	$result = $db->db_query($sql)
 		or error(GENERAL_ERROR,
             'Could not query config information.', '',
             __FILE__, __LINE__, $sql);
 	$row = $db->db_fetch_array($result);
 	$anzahl = $row['Zahl'];

  if($anzahl == 0) {
    $sql = "INSERT INTO " . $db_tb_research2research . "(rOld, rNew)" .
           " VALUES(" . $oldid . ", " . $newid . ")";
   	$result = $db->db_query($sql)
   		or error(GENERAL_ERROR,
               'Could not query config information.', '',
               __FILE__, __LINE__, $sql);
  }
}

// ****************************************************************************
//
//
function insert_research_on_building($research, $building) {
	global $db, $db_tb_building2research;

  $resid = find_research_id($research, FALSE);
  $bldid = find_building_id($building);

  $sql = "SELECT COUNT(*) AS Zahl FROM " . $db_tb_building2research .
         " WHERE rId=" . $resid . " AND bId=" . $bldid;
 	$result = $db->db_query($sql)
 		or error(GENERAL_ERROR,
             'Could not query config information.', '',
             __FILE__, __LINE__, $sql);
 	$row = $db->db_fetch_array($result);
 	$anzahl = $row['Zahl'];

  if($anzahl == 0) {
    $sql = "INSERT INTO " . $db_tb_building2research . "(rId, bId)" .
           " VALUES(" . $resid . ", " . $bldid . ")";
   	$result = $db->db_query($sql)
   		or error(GENERAL_ERROR,
               'Could not query config information.', '',
               __FILE__, __LINE__, $sql);
  }
}

// ****************************************************************************
//
//
function finish_de_info_forschung() {
  global $db, $db_tb_research2research, $db_tb_building2research,
         $db_tb_research2building, $db_tb_research;

  $sql = "UPDATE " . $db_tb_research . " SET rLevel=0";
  $result = $db->db_query($sql)
 	  or error(GENERAL_ERROR,
             'Could not query config information.', '',
             __FILE__, __LINE__, $sql);

  $sql = "SELECT distinct ID AS rid FROM " . $db_tb_research;
  $result = $db->db_query($sql)
 	  or error(GENERAL_ERROR,
             'Could not query config information.', '',
             __FILE__, __LINE__, $sql);

  while(($research_data = $db->db_fetch_array($result)) !== FALSE) {
    $sql = "SELECT distinct rNew FROM " . $db_tb_research2research .
           " WHERE rNew=" . $research_data['rid'];

    $result2 = $db->db_query($sql)
   	  or error(GENERAL_ERROR,
               'Could not query config information.', '',
               __FILE__, __LINE__, $sql);
    $row = $db->db_fetch_array($result2);

    $sql = "SELECT COUNT(*) AS Zahl FROM " . $db_tb_building2research .
           " WHERE rId=" . $research_data['rid'] ;
 	  $result3 = $db->db_query($sql)
   	  or error(GENERAL_ERROR,
               'Could not query config information.', '',
               __FILE__, __LINE__, $sql);
   	$row2 = $db->db_fetch_array($result3);
 	  $anzahl = $row2['Zahl'];

    if($anzahl == 0 && empty($row)) {
	    $sql = "UPDATE " . $db_tb_research .
             " SET rLevel=1 WHERE ID=" . $research_data['rid'];

      $db->db_query($sql)
     	  or error(GENERAL_ERROR,
                 'Could not query config information.', '',
                 __FILE__, __LINE__, $sql);
  	}
  }

  $db->db_free_result($result);
  $db->db_free_result($result2);
  $db->db_free_result($result3);

  $level  = 0;
  $anzahl = 0;

  while($level == 0 || $anzahl>0 ) {
	  $level++;

	  $sql = "SELECT Count(*) as Zahl FROM " . $db_tb_research .
           " WHERE rLevel=" . $level;
	  $result3 = $db->db_query($sql)
      or error(GENERAL_ERROR,
               'Could not query config information.', '',
               __FILE__, __LINE__, $sql);
	  $row2 = $db->db_fetch_array($result3);
	  $anzahl = $row2['Zahl'];

    if($anzahl > 0) {
		  $sql = "SELECT distinct ID AS rid FROM " . $db_tb_research .
             " WHERE rLevel=" . $level;
		  $result = $db->db_query($sql)
        or error(GENERAL_ERROR,
                 'Could not query config information.', '',
                 __FILE__, __LINE__, $sql);

		  while(($research_data = $db->db_fetch_array($result)) !== FALSE) {
				$sql = "SELECT rNew FROM " . $db_tb_research2research .
               " WHERE rOld=" . $research_data['rid'];

		    $result3 = $db->db_query($sql)
 					or error(GENERAL_ERROR,
                   'Could not query config information.', '',
                   __FILE__, __LINE__, $sql);

				while(($row2 = $db->db_fetch_array($result3)) !== FALSE) {
		 	    $sql = "UPDATE " . $db_tb_research .
                 " SET rLevel=" . ($level+1) .
                 " WHERE ID=" . $row2['rNew'];
		      $db->db_query($sql)
 					  or error(GENERAL_ERROR,
                     'Could not query config information.', '',
                     __FILE__, __LINE__, $sql);
				}
		  }
	  }
  }

  $sql = "SELECT distinct ID AS rid FROM " . $db_tb_research . " WHERE rLevel=0";
  $result = $db->db_query($sql)
		or error(GENERAL_ERROR,
             'Could not query config information.', '',
             __FILE__, __LINE__, $sql);

  while(($research_data = $db->db_fetch_array($result)) !== FALSE) {
		$sql = "SELECT bID FROM " . $db_tb_building2research .
           " WHERE rId=" . $research_data['rid'];
    $result2 = $db->db_query($sql)
			or error(GENERAL_ERROR,
               'Could not query config information.', '',
               __FILE__, __LINE__, $sql);

		while(($row = $db->db_fetch_array($result2)) !== FALSE) {
			$sql = "SELECT rid FROM " . $db_tb_research2building .
             " WHERE bId=" . $row['bID'];
      $result3 = $db->db_query($sql)
  			or error(GENERAL_ERROR,
                 'Could not query config information.', '',
                 __FILE__, __LINE__, $sql);

			if(($row2 = $db->db_fetch_array($result3)) !== FALSE) {
			  $sql = "SELECT rLevel FROM " . $db_tb_research .
               " WHERE ID=" . $row2['rid'];
				$result4 = $db->db_query($sql)
  			  or error(GENERAL_ERROR,
                   'Could not query config information.', '',
                   __FILE__, __LINE__, $sql);

  			if(($row3 = $db->db_fetch_array($result4)) !== FALSE) {
				  $sql = "UPDATE " . $db_tb_research .
                 " SET rLevel=" . ($row3['rLevel']+1) .
                 " WHERE ID=" . $research_data['rid'];
					$db->db_query($sql)
					  or error(GENERAL_ERROR,
                     'Could not query config information.', '',
                     __FILE__, __LINE__, $sql);
				}
			}
		}
	}
}

?>