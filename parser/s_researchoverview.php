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
/* Diese Erweiterung der ursp�nglichen DB ist ein Gemeinschafftsprojekt von  */
/* IW-Spielern.                                                              */
/* Bei Problemen kannst du dich an das eigens daf�r eingerichtete            */
/* Entwicklerforum wenden:                                                   */
/*                                                                           */
/*                   http://www.iw-smf.pericolini.de                         */
/*                                                                           */
/*****************************************************************************/
if (basename($_SERVER['PHP_SELF']) != "index.php")
  die('Hacking attempt...!!');

if (!@include("./config/m_research.cfg.php")) { 
	die( "<b>Fehler: Das Forschungsmodul ist nicht installiert!</b>");
}

//*****************************************************************************
//
//
function parse_researchoverview($lines) {
  global $db, $selectedusername, $db_tb_research2user;
  
  $scan_data = array();
  $raumfahrtTitle = "";

  // Alle Forschungen des Benutzers im Vorfeld entfernen.
  $sql = "DELETE FROM " . $db_tb_research2user . 
         " WHERE userid='" . $selectedusername . "'";
  $result = $db->db_query($sql)
  	or error(GENERAL_ERROR, 
             'Could not query config information.', '', 
             __FILE__, __LINE__, $sql);
  
  // Alle Zeilen durchgehen, ob diese Forschung bereits eingetragen wurde.
  foreach($lines as $scan) {
    // Die Forschungssektion "Raumfahrt" kommt in jedem Fall vor der 
    // Forschung "Raumfahrt", diese darf dann nicht eingefuegt werden.
		if( strpos( $scan, "Raumfahrt") !== FALSE ) {
			if( empty( $raumfahrtTitle )) {
				$raumfahrtTitle = htmlspecialchars($scan,$encoding = 'UTF-8');
			} else {
				insert_research_for_user(trim($scan), $selectedusername);
			}
		} else {
			insert_research_for_user(trim($scan), $selectedusername);
		}
  }
}

//*****************************************************************************
//
//
function insert_research_for_user($what, $who) {
  global $db, $db_tb_research2user, $db_tb_research;

  // Forschungsidentifier finden. 
  $sql = "SELECT ID FROM " . $db_tb_research . " WHERE name='" . $what . "'";
  $result = $db->db_query($sql)
	  or error(GENERAL_ERROR, 
             'Could not query config information.', '', 
             __FILE__, __LINE__, $sql);

  $row = $db->db_fetch_array($result);
	
  // Gefunden, also Beziehung zum Benutzer eintragen. 
  if(!empty($row)) {
    $sql = "INSERT INTO " . $db_tb_research2user . "(rid,userid)" .
           " VALUES(" . $row['ID'] . ", '" . $who . "')";
    $result = $db->db_query($sql)
  	  or error(GENERAL_ERROR, 
               'Could not query config information.', '', 
               __FILE__, __LINE__, $sql);
  }
}

?>