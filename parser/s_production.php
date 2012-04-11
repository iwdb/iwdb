<?php
/*****************************************************************************/
/* s_production.php                                                          */
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
/*                   http://www.iw-smf.pericolini.de                         */
/*                                                                           */
/*****************************************************************************/

if (basename($_SERVER['PHP_SELF']) != "index.php")
  die('Hacking attempt...!!');

if (!defined('IRA'))
	die('Hacking attempt...');

//*****************************************************************************
//
// Ressie-Übersicht parsen: Suche Klammerpaare und ordne diese der in der
// Reihenfolge des $what-Arrays zu.
function parse_production($scanlines) {
  global $config_date, $db_tb_ressuebersicht, $db, $selectedusername;

 $scanwhole = implode("\n",$scanlines);
  		      if (
              preg_match( '/Teil\s2 .* Gesamt\s* (.*) \s* \(.*\) \s* .* \((.*)\)
                          \s* Allisteuer:\s (.*) \s (.*) \s \/ \s (.*) \s /sUx', $scanwhole, $foundscan)
  		      ) {
  		          $fp      = stripNumber($foundscan[1]);
  		          $credits = stripNumber($foundscan[2]) + stripNumber($foundscan[3]);
  		          $bev_a   = stripNumber($foundscan[4]);
  		          $bev_g   = stripNumber($foundscan[5]);
  		          $bev_q   = $bev_a * 100 / $bev_g;
  		          global $user_id;
  		          $sql = "UPDATE ". $db_tb_ressuebersicht
					." SET `fp_ph` = '" . $fp
					."', `credits` = '" . $credits
					."', `bev_a` = '" . $bev_a
					."', `bev_g` = '" . $bev_g
					."', `bev_q` = '" . $bev_q
					."' WHERE user = '" . $selectedusername . "' LIMIT 1 ;";
  		          $db->db_query($sql)
  		                  or error(GENERAL_ERROR,
  		             'Could not query config information.', '',
  		             __FILE__, __LINE__, $sql);

			  echo "<div class='system_notification'>Produktion Teil 2 aktualisiert/hinzugef&uuml;gt mit den Werten:</div>";
  		          echo "FP/h (".$fp."), Credits (".$credits."), 1&euro;-Leute (".$bev_a."), Volk (".$bev_g."), Quote (".$bev_q.")";
  		      }

  $what = array("eisen", "stahl", "VV4A", "chem", "eis", "wasser", "energie");
  $scan_data = array();
  
  $line=0;
	$foundGesamt = FALSE;
	
  foreach($scanlines as $scan) {
    $scan = str_replace("(Kolonie)", "", $scan);
    $scan = str_replace("(Kampfbasis)", "", $scan);
    $scan = str_replace("(Sammelbasis)", "", $scan);
    //$scan = str_replace("(Ressbasis)", "", $scan);
    
    if(strstr($scan, "Lager und Bunker anzeigen"))
      break;
    
    if(strstr($scan, "Teil 2"))
      return;

		// Die wirklich wichtigen Informationen stehen hinter dem Teil mit
		// dem Satzanfang "Gesamt "
		if(preg_match("/^Gesamt /", $scan, $hit))
		  $foundGesamt = TRUE;
			
    // Wir haben hier einen Wert in Klammern gefunden.
    if(($foundGesamt === TRUE) && (preg_match("/^\(.*\)/", $scan, $hit))) {
      // repeat process
      if($line == 7)
        $line = 0;
      
      // Dem gefundenen Wert die Klammern, die Trennpunkte und das Dezimalkomma
      // entfernen und dann dem Array-Wert zuweisen.
      $item = $what[$line++];
      $scan_data[$item] = str_replace("(", "", 
                          str_replace(")", "",
                          stripNumber($hit[0])));
    }  
  }

	// Das Gesamt-Paket war nicht enthalten? Kommentarlos zurückschicken.
	if($foundGesamt === FALSE)
	  return;
	
  // Wir haben keinen vollstaendigen Bericht, wenn nicht alle 7 Zeilen gesetzt sind.
  if($line != 7) {
    echo "<div class='system_error'>Produktionsbericht unvollst&auml;ndig.</div>";
    return;    
  }
  
  $sql = "SELECT user FROM " . $db_tb_ressuebersicht . 
         " WHERE user = '" . $selectedusername . "'";
	$result = $db->db_query($sql)
		or error(GENERAL_ERROR, 
             'Could not query config information.', '', 
             __FILE__, __LINE__, $sql);
             
	$row = $db->db_fetch_array($result);

  if(!empty($row)) {
    $sql = "UPDATE " . $db_tb_ressuebersicht . 
           " SET datum=" . $config_date . 
           ", eisen='" . ($scan_data['eisen']) .
           "', stahl='" . ($scan_data['stahl']) .
           "', vv4a='" . ($scan_data['VV4A']) .
           "', chem='" . ($scan_data['chem']).
           "', eis='" . ($scan_data['eis']).
           "', wasser='". ($scan_data['wasser']) .
           "', energie='". ($scan_data['energie']).
           "' WHERE user='". $selectedusername . "'";
  } else {
    $sql = "INSERT INTO " . $db_tb_ressuebersicht .  
           " (user, datum, eisen, stahl, vv4a, chem, eis, wasser, energie)" .
           " VALUES('" . $selectedusername . "', " . $config_date . 
           ",'" . ($scan_data['eisen']) .
           "', '" . ($scan_data['stahl']) .
           "', '".($scan_data['VV4A']) .
           "', '".($scan_data['chem']) .
           "', '".($scan_data['eis']) .
           "', '".($scan_data['wasser']) .
           "', '".($scan_data['energie']) . "')";
  }
  
  $db->db_query($sql)
	  or error(GENERAL_ERROR, 
             'Could not query config information.', '', 
             __FILE__, __LINE__, $sql);
             
  echo "<div class='system_notification'>Produktion Teil 1 aktualisiert/hinzugef&uuml;gt.</div>";
  echo "<b>Produktion Teil 2 fehlt noch, wird -- falls vorhanden! -- nun geparsed.</b>";

}

function display_production() {
  include "./modules/m_ress.php";
}
?>
