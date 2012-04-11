<?php
/*****************************************************************************/
/* s_schiffsuebersicht.php                                                   */
/*****************************************************************************/
/* Iw DB: Icewars geoscan and sitter database                                */
/* Open-Source Project started by Robert Riess (robert@riess.net)            */
/* Software Version: Iw DB 1.00                                              */
/* 
====================================================================
===== */
/* Software Distributed by:    http://lauscher.riess.net/iwdb/               */
/* Support, News, Updates at:  http://lauscher.riess.net/iwdb/               */
/* 
====================================================================
===== */
/* Copyright (c) 2004 Robert Riess - All Rights Reserved                     */
/*****************************************************************************/
/* This program is free software; you can redistribute it and/or modify it   */
/* under the terms of the GNU General Public License as published by the     */
/* Free Software Foundation; either version 2 of the License, or (at your    */
/* option) any later version.                                                */
/*                                                                           */
/* This program is distributed in the hope that it will be useful, but       */
/* WITHOUT ANY WARRANTY; without even the implied warranty of                */
/* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU 
General */
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
// Scanner-Funktion, die von newscan.php aus aufgerufen wird.
//
function parse_schiffsuebersicht($scanlines) {
    global 
      $db, $db_tb_schiffstyp, $db_tb_schiffe, $db_tb_user, $selectedusername, 
      $config_date, $deleted;
      
    // Variableninitialisierung
    $deleted = false;
    $start = 1;
    $lines = 0;
  
    // Gehe alle Zeilen des Berichtes durch. Dabei sind die Planeten und 
    // die Basen in den ersten Zeilen aufgeführt. Erst wenn die aktuelle 
    // Zeile "Flug", "Stat" und "Gesamt" enthält, sind in den folgenden
    // Zeilen Schiffsnamen am Anfang. 
  foreach($scanlines as $scan) {
    
    // Prolog durch?
    if ( $start === 3 )
    {
      
      $temp = explode(" ", trim($scan));
            
      // Leerzeilen ignorieren
      if( !empty($temp[0])) {
        // Name des Schiffstyps bestimmen
        $schiffsname = $temp[0];
        for ($i = 1; $i < count($temp) - $lines; $i++) {
          //Handelt es sich um keine Nummer
          $bool = FALSE;

          //da alle ummern rausgeschnitten werden 2 Sonderfaelle behandeln
          //letztes Zeichen eine Klammer und das erste ein X
          if (strlen($temp[$i]) > 1) {
          $tempstr = trim($temp[$i]);
          if ( $tempstr[0] == 'X') $bool = TRUE;
          if ( $tempstr[strlen($tempstr)-1] == ')' ) $bool = TRUE; 
          }       
                   

          if( StripNumber($temp[$i]) == 0 OR $bool == TRUE){
            $schiffsname .= " " . $temp[$i];
          } else {
            $schiffsname .= "%";
          }
        }                

        $schiffsname = trim($schiffsname);    

        $schiffsname = str_replace(" ","%", $schiffsname);     
        
                // Massdriver Pakete sind keine gültigen Schiffe. 
              	if( $schiffsname !== "Massdriver%Paket" ) {
                    // Suche ID des Schiffstyps in der DB
                  	$sql = "SELECT id FROM " . $db_tb_schiffstyp . 
                           " WHERE schiff LIKE '" . $schiffsname . "' OR schiff LIKE '" . $schiffsname . "'";               
                  	$result = $db->db_query($sql)
                  		or error(GENERAL_ERROR, 
                               'Could not query config information.', '', 
                               __FILE__, __LINE__, $sql);
                  	$row = $db->db_fetch_array($result); 

                    // ID nicht gefunden -> neu einfügen.
                		if( empty($row['id'])) {
/*
        $schiffsname = str_replace("%%","%", $schiffsname);
        $schiffsname = str_replace("%"," ", $schiffsname); 

        echo "<div class='doc_red'>Neues Schiff wurde hinzugef&uuml;gt:<br><pre>";
        print("[".$schiffsname."]");
        echo "</pre></div> ";   
                  			$sql = "INSERT INTO " . $db_tb_schiffstyp . 
                               " (schiff, abk, typ) " .
                               "VALUES" . 
                               " ('" . $schiffsname . "', '" . $schiffsname . "', '')";
                  			$result = $db->db_query($sql)
                  				or error(GENERAL_ERROR, 
                                   'Could not query config information.', '', 
                                   __FILE__, __LINE__, $sql);
                                     
                  			$row['id'] = mysql_insert_id();*/
                		}
       if (!$deleted) {         
	// Setze Zeitpunkt des letzten Schiffsimportes 
  	$sql = "DELETE FROM " . $db_tb_schiffe . 
           " WHERE user='" . $selectedusername . "'";
  	$result = $db->db_query($sql)
  		or error(GENERAL_ERROR, 
               'Could not query config information.', '', 
               __FILE__, __LINE__, $sql);
  
  	$sql = "UPDATE " . $db_tb_user . " SET lastshipscan='" . $config_date . 
           "' WHERE sitterlogin='" . $selectedusername . "'";
  	$result = $db->db_query($sql)
  		or error(GENERAL_ERROR, 
               'Could not query config information.', '',
                __FILE__, __LINE__, $sql);
	$deleted = true;
	}
                    // Letzte Zahl der Zeile ist die Gesamtzahl der Schiffe für 
                    // diesen Schiffstyp.
                		$sql = "INSERT INTO " . $db_tb_schiffe . 
                           " (user, schiff, anzahl) VALUES ('" . 
                           $selectedusername . "', '" . $row['id'] . "', '" . 
                           StripNumber($temp[count($temp) - 1]). "')";
                		$result = $db->db_query($sql)
                			or error(GENERAL_ERROR, 
                               'Could not query config information.', '', 
                               __FILE__, __LINE__, $sql);
        }
      }
    } else {
            // Noch im Prolog. Prüfen, ob der Prolog jetzt abgeschlossen ist.
      if( strpos($scan, "Flug") > 0 && strpos($scan, "Gesamt") > 0 && strpos($scan, "Stat") > 0)
      {
        $start = 3;
        $lines = $lines + 2;
        } else {
        if ( strpos($scan, "Schiffs&uuml;bersicht") === 0 ) {
          $start = 2;
        }
        if ( $start == 2 ) $lines++;
      }
    }
  }
}

?>