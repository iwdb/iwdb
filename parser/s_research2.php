<?php
/*****************************************************************************/
/* m_reasearch2.php                                                            */
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
function parse_research2($lines) {
	global $db, $db_tb_research, $db_tb_user_research, $selectedusername;
	
	$forschungen = array();

    $sql = "SELECT name FROM " . $db_tb_research . 
			" ORDER BY ID ASC";
	$result = $db->db_query($sql)
		or error(GENERAL_ERROR, 
             'Could not query config information.', '', 
             __FILE__, __LINE__, $sql);
	while($row = $db->db_fetch_array($result)) {
		$forschungen[] = $row['name'];
	}
	
	$forschung = '';
	$akt_fp = array();
	$for_punkte = FALSE;
	$akt_forschung = 0;
	$akt_date = 0;
	$raumfahrt = FALSE;
	
	foreach($lines as $scan) {

		$scan = trim($scan);
		
		// Wird eine Zeitangabe 'bis:' gefunden, dann ist die letzte geparste Forschung, die aktuelle, die geforscht wird
		if( preg_match('/bis:\s(\d{2}).(\d{2}).(\d{4})\s(\d{2}):(\d{2}):(\d{2})/', $scan, $treffer) ) {
		    $sql = "SELECT id FROM " . $db_tb_research . 
					" WHERE name='" . end( array_keys( $akt_fp ) ) . "'";
			$result = $db->db_query($sql)
				or error(GENERAL_ERROR, 
		             'Could not query config information.', '', 
		             __FILE__, __LINE__, $sql);
			$row = $db->db_fetch_array($result);
			$akt_forschung = $row['id'];
			$akt_date = mktime($treffer[4], $treffer[5], $treffer[6], $treffer[2], $treffer[1], $treffer[3], -1);
		}

		// Bugfixing bei der Kategorie 'Raumfahrt'
		if( ($scan == "Raumfahrt") AND (! $raumfahrt) ) $raumfahrt = TRUE;
		// Ist eine Forschung gefunden worden, dann stell die Suche auf die dazugeh�rigen Punkte
		elseif( in_array($scan, $forschungen) AND ! $for_punkte ) {
			$forschung = $scan;
			$for_punkte = TRUE;
		}

		// Auf der Suche nach den zugeh�rigen Punkten, nach dem Fund einer Forschung
		if ( $for_punkte === TRUE ) {
			if( strstr($scan, "Forschungspunkte") ) {
				$temp = trim( substr( $scan, 0, strpos( $scan, " " ) ) );
				$akt_fp[$forschung] = stripNumber($temp);
				$for_punkte = FALSE;
				$forschung = '';
			} elseif ( strstr($scan, "erforscht") ) {
				$for_punkte = FALSE;
				continue;
			}
		}
		
	}
	
	$time = time();

	foreach($akt_fp as $key => $value) {
		$sql = "UPDATE " . $db_tb_research . " SET " .
			"FPakt=" . $value . ", " .
			"time=" . $time .
			" WHERE name='" . $key . "'";
		$result = $db->db_query($sql)
			or error(GENERAL_ERROR, 
             'Could not query config information.', '', 
             __FILE__, __LINE__, $sql);
	}

	$sql = "DELETE FROM " . $db_tb_user_research . 
			" WHERE user='" . $selectedusername . "'";
	$result = $db->db_query($sql)
		or error(GENERAL_ERROR, 
			 'Could not query config information.', '', 
			 __FILE__, __LINE__, $sql);

	$sql = "INSERT INTO " . $db_tb_user_research . 
			" SET user='" . $selectedusername . "', rid=" . $akt_forschung . ", date=" . $akt_date;
	$result = $db->db_query($sql)
		or error(GENERAL_ERROR, 
			 'Could not query config information.', '', 
			 __FILE__, __LINE__, $sql);
	
}

?>