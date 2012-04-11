<?php
/*****************************************************************************/
/* m_universe.php                                                            */
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
/* Diese Erweiterung der urspruenglichen DB ist ein Gemeinschafftsprojekt    */
/* von IW-Spielern.                                                          */
/* Bei Problemen kannst du dich an das eigens dafuer eingerichtete           */
/* Entwicklerforum wenden:                                                   */
/*                                                                           */
/*                   http://www.iw-smf.pericolini.de                         */
/*                                                                           */
/*****************************************************************************/
if (!defined('IRA'))
die('Hacking attempt...');

if (!@include("./config/m_research.cfg.php")) {
	die( "<b>Fehler: Das Forschungsmodul ist nicht installiert!</b>");
}

//*****************************************************************************
//
//
function parse_research($lines) {
	$scan_data = array();
	$nextline  = '';

	foreach($lines as $scan) {
		$scan = str_replace("\\%", "%", $scan);
//		$scan = str_replace("Vorraussetzungen", "Voraussetzungen", $scan);
		if(strpos($scan, "Forschungsinfo: ") !== FALSE )
		{
			$scan_data['research'] = trim(str_replace("Forschungsinfo: ", "", $scan));
			$scan_data['description'] = '';
			$scan_data['addcost'] = '';
      			$nextline = '';
		}
		if(strpos($scan, "Leuten erforscht") !== FALSE) {
			$scan='';
		}
		// Aktuelle Zeile enthält Beschreibung
		if( $nextline == 'DESC' ) {
			if(( strpos($scan, "Zuerst erforscht von") === FALSE ) &&
				( strpos($scan, "Kosten") === FALSE ))
			{
				if(empty($scan_data['description'])) {
					$scan_data['description'] = trim($scan);
				} else {
					$scan_data['description'] .= "<br />\n" . trim($scan);
				}

			} else {
				if ( strpos($scan, "Kosten") !== FALSE )
				{
					$nextline = 'COST';
					$scan_data['FP'] = trim(str_replace(" ", "", str_replace(".", "", str_replace("Forschungspunkte", "", str_replace("Kosten", "", $scan)))));
				}
			//	$nextline = '';
			}
		} elseif( $nextline == 'COST' ) {
			// neue Zeile Highscorepunkte
			// if( strpos($scan, "Voraussetzungen Forschungen") === FALSE ) {
			//if( strpos($scan, "Highscorepunkte") === FALSE ) {
				$scan_data['addcost'] = trim($scan);
			//} else {
			//	$scan_data['highscore'] = trim(str_replace("Highscorepunkte", "", $scan));
				$nextline = '';
			//}
		} else {
			if ( strpos($scan, "Gebiet") !== FALSE ) {
				$nextline = 'DESC';
				$scan_data['gebiet'] = trim(str_replace("Gebiet", "", $scan));
			} elseif ( strpos($scan, "Kosten") !== FALSE ) {
				$nextline = 'COST';
				$scan_data['FP'] = trim(str_replace(" ", "", str_replace(".", "", str_replace("Forschungspunkte", "", str_replace("Kosten", "", $scan)))));
			//} elseif ( strpos($scan, "Highscorepunkte") !== FALSE ) {
			//	$scan_data['highscore'] = trim(str_replace(" ", "", str_replace(".", "", str_replace("Highscorepunkte", "", $scan))));
			} elseif ( strpos($scan, "Prototyp") !== FALSE ) {
				$scan_data['Prototyp'] = trim(str_replace("Prototyp", "", str_replace("Die Forschung bringt einen Prototyp von ", "", $scan)));
			} elseif ( strpos($scan, "Voraussetzungen Forschungen") !== FALSE ) {
				$scan_data['NeededResearch'] = split_parens(trim(str_replace("Voraussetzungen Forschungen", "", $scan)));
			} elseif ( strpos($scan, "Voraussetzungen Geb&auml;ude") !== FALSE ) {
				$scan_data['NeededBuildings'] = split_parens(trim(str_replace("Voraussetzungen Geb&auml;ude", "", $scan)));
			} elseif ( strpos($scan, "Voraussetzungen Objekte") !== FALSE ) {
				$scan_data['NeededObject'] = split_parens(trim(str_replace("Voraussetzungen Objekte", "", $scan)));
			} elseif ( strpos($scan, "Erm&ouml;glicht Forschungen") !== FALSE ) {
				$scan_data['GivesResearch'] = split_parens(trim(str_replace("Erm&ouml;glicht Forschungen", "", $scan)));
			} elseif ( strpos($scan, "Erm&ouml;glicht Geb&auml;udestufen") !== FALSE ) {
				$scan_data['GivesBuildingLevel'] = split_parens(trim(str_replace("Erm&ouml;glicht Geb&auml;udestufen", "", $scan)));
			} elseif ( strpos($scan, "Erm&ouml;glicht Geb&auml;ude") !== FALSE ) {
				$scan_data['GivesBuilding'] = split_parens(trim(str_replace("Erm&ouml;glicht Geb&auml;ude", "", $scan)));
			//} elseif ( strpos($scan, "Erm&ouml;glicht Deklarationen") !== FALSE ) {
			//	$scan_data['GivesDeclares'] = split_parens(trim(str_replace("Erm&ouml;glicht Deklarationen", "", $scan)));
			} elseif ( strpos($scan, "Erm&ouml;glicht Verteidigungsanlagen") !== FALSE ) {
				$scan_data['GivesDefense'] = split_parens(trim(str_replace("Erm&ouml;glicht Verteidigungsanlagen", "", $scan)));
			} elseif ( strpos($scan, "Erm&ouml;glicht Genetikoptionen") !== FALSE ) {
				$scan_data['GivesGenetics'] = split_parens(trim(str_replace("Erm&ouml;glicht Genetikoptionen", "", $scan)));
			}
		}
	}
		// zum Debuggen das Array ausgeben :D
		// foreach ($scan_data as $datenzeile) echo $datenzeile,"<br>";
	update_research($scan_data);
}

// ****************************************************************************
//
function display_research() {
	 include "./modules/m_research.php";
}

// ****************************************************************************
// Trennt die uebergebene Zeichenkette an den Klammern ") (" auf und gibt ein
// array mit den gefundenen Elementen zurueck. Zuvor werden noch zwei gueltige
// Ausnahmen behandelt.
//
function split_parens($line)
{
  if(empty($line)) {
	  return '';
	}

	// Sonderfälle, die sonst auseinander gerissen werden.
	$line = str_replace("(Solar) (orbital)", "[Solar] [orbital]", $line);
	$line = str_replace("(en) (mittels eines P&uuml;mpels)", "(en)(mittels eines P&uuml;mpels)", $line);

  $line  = trim($line);
	if(substr($line, 0, 1) == '(') {
	  $line  = substr($line, 1, strlen($line)-1);
	}
	if(substr($line, strlen($line)-1, 1) == ')') {
	  $line  = substr($line, 0, strlen($line)-1);
	}

	$elems = explode(") (", $line);

	return $elems;
}

$researchid = 1;

// ****************************************************************************
//
//
function update_research($scan_data) {
	global $db, $db_tb_research, $db_tb_research2prototype, $researchid,
				 $db_tb_gebaeude, $db_tb_schiffstyp, $db_tb_researchfield,
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
	  $gebiet = 1;
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

  // Aenderungen für Gebaeude, die bereits unter anderem Namen in der DB sind.
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
function finish_research() {
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
	  $rid_to_change = $research_data['rid'];
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