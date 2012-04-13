<?php
/*****************************************************************************/
/* s_members.php                                                             */
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
// Scanner-Funktion, die von newscan.php aus aufgerufen wird.
//
function parse_members($lines) {
	//Allianz des User auslesen der geparsed wird
	global $user_id, $db, $db_prefix;
	$sql = "SELECT allianz FROM ".$db_prefix."user WHERE id='".$user_id."';";
	$result = $db->db_query($sql) 
	           or error(GENERAL_ERROR, 'Could not query config information.', '',
               __FILE__, __LINE__, $sql);
	$row = $db->db_fetch_array($result);
	$allianz = $row['allianz'];
	echo "Member werden folgender Allianz zugeordnet: [".$allianz."]";
  // Gehe alle Zeilen des Berichtes durch.
  foreach($lines as $scan) {
      $temp = explode(" ", $scan);

      if( strpos($scan, "Titel") === FALSE &&
        strpos($scan, "Mitgliederliste") === FALSE &&
        count($temp) > 3)
      { 	
      	
      // Namen zusammenbasteln.
          $scan_udata['sitterlogin'] = trim($temp[0]);
          $j = 1;
	   while ( ( ( !preg_match("/Hasenpriester/",trim($temp[$j])) ) &&
//          while ( ( ( !preg_match("/Gr\S+nder/",trim($temp[$j])) ) &&
                ( trim($temp[$j]) != "HC" ) &&
                ( trim($temp[$j]) != "iHC" ) &&
                ( trim($temp[$j]) != "interner" ) &&
                ( trim($temp[$j]) != "Mitgliederverwalter" ) &&
                ( trim($temp[$j]) != "Mitglieder" )) && ($j < count($temp) - 1) )
          {
              $scan_udata['sitterlogin'] .= " " . trim($temp[$j]);
              $j++;
          }
      // Als nächstes kommt der Rang des Mitgliedes
          $scan_udata['rang'] = trim($temp[$j]);
          $j++;

      if ($scan_udata['rang'] == "interner") {
        $scan_udata['rang'] = "interner HC";
        $j++;
      }
      // Von hier aus ist jetzt alles "fix".
          $scan_udata['gebp'] = stripNumber( trim( $temp[($j)]) );
		$scan_udata['fp'] = stripNumber( trim( $temp[($j+1)]) );
		$scan_udata['allianz'] = $allianz;
		$scan_udata['gesamtp'] = stripNumber( trim( $temp[($j+2)]) );
		$scan_udata['ptag'] = stripNumber( trim( $temp[($j+3)]) );
          $date_d = explode(".", trim($temp[($j + 4)]));
          $scan_udata['dabei'] = mktime(0, 0, 00, $date_d[1], $date_d[0], $date_d[2]);

      // Und zum Schluss noch der Titel des Mitgliedes.
          $scan_udata['titel'] = '';
          for($i = $j + 5; $i < count($temp); $i++)
        $scan_udata['titel'] .= $temp[$i] . " ";

      // Dann noch die gewonnenen Daten in die DB eintragen.
          updateuser($scan_udata);
      }

/*
      if( strpos($scan, "GesamtP") === FALSE &&
        strpos($scan, "Mitgliederliste") === FALSE &&
        count($temp) > 6)
      { 	
      	
      // Namen zusammenbasteln.
          $scan_udata['sitterlogin'] = trim($temp[0]);
          $j = 1;
          while ( ( ( !preg_match("/Gr\S+nder/",trim($temp[$j])) ) &&
                ( trim($temp[$j]) != "HC" ) &&
                ( trim($temp[$j]) != "iHC" ) &&
                ( trim($temp[$j]) != "interner" ) &&
                ( trim($temp[$j]) != "Mitgliederverwalter" ) &&
                ( trim($temp[$j]) != "Mitglieder" )) && ($j < count($temp) - 1) )
          {
              $scan_udata['sitterlogin'] .= " " . trim($temp[$j]);
              $j++;
          }
      // Als nächstes kommt der Rang des Mitgliedes
          $scan_udata['rang'] = trim($temp[$j]);
          $j++;

      if ($scan_udata['rang'] == "interner") {
        $scan_udata['rang'] = "interner HC";
        $j++;
      }
      // Von hier aus ist jetzt alles "fix".
          $scan_udata['gebp'] = stripNumber( trim( $temp[($j)]) );
					$scan_udata['fp'] = stripNumber( trim( $temp[($j+1)]) );
					$scan_udata['allianz'] = $allianz; 
					$scan_udata['gesamtp'] = stripNumber( trim( $temp[($j+2)]) );
					$scan_udata['ptag'] = stripNumber( trim( $temp[($j+3)]) );
          $date_d = explode(".", trim($temp[($j + 4)]));
          $scan_udata['dabei'] = mktime(0, 0, 00, $date_d[1], $date_d[0], $date_d[2]);

      // Und zum Schluss noch der Titel des Mitgliedes.
          $scan_udata['titel'] = '';
          for($i = $j + 5; $i < count($temp); $i++)
        $scan_udata['titel'] .= $temp[$i] . " ";

      // Dann noch die gewonnenen Daten in die DB eintragen.
          updateuser($scan_udata);
      }
*/
  }
}

function updateuser( $scan_data )
{
    global $db, $db_tb_user, $db_tb_punktelog, $config_date;

  // Daten ins Punktelog übernehmen.
    $sql = "INSERT INTO " . $db_tb_punktelog . "(" .
         " user, date, gebp, fp, gesamtp, ptag" .
         ") VALUES (" .
         " '" . $scan_data['sitterlogin'] . "', '" . $config_date . "', '" .
                $scan_data['gebp'] . "', '" . $scan_data['fp'] . "', '" .
                $scan_data['gesamtp'] . "', '" . $scan_data['ptag'] . "' )";
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '',
             __FILE__, __LINE__, $sql);

  // Prüfe Mitglied, ob es bereits in der DB gespeichert ist.
    $sql = "SELECT sitterlogin FROM " . $db_tb_user .
         " WHERE sitterlogin='" . $scan_data['sitterlogin'] . "'";
    $result = $db->db_query($sql)
            or error(GENERAL_ERROR, 'Could not query config information.', '',
               __FILE__, __LINE__, $sql);
    $row = $db->db_fetch_array($result);

    if ( isset($row['sitterlogin']) )
    {
    // Das Mitglied existiert bereits. Daten in Tabelle user aktualisieren.
        foreach ($scan_data as $key => $data)
        {
            $update = ( empty($update) ) ? $key . "='" . $data . "'": $update . ", " . $key . "='" . $data . "'";
        }

        $sql = "UPDATE " . $db_tb_user . " SET " . $update . " WHERE sitterlogin='" . $scan_data['sitterlogin'] . "'";
        $result = $db->db_query($sql)
            or error(GENERAL_ERROR, 'Could not query config information.', '',
               __FILE__, __LINE__, $sql);

        return 1;
    }
    else
    {
    // Das Mitglied existiert noch nicht, Daten in Tabelle user einfügen.
        $scan_data['id'] = $scan_data['sitterlogin'];
        foreach ($scan_data as $key => $data)
        {
            $sql_key = ( empty($sql_key) ) ? $key
                                     : $sql_key . ", " . $key;
            $sql_data = ( empty($sql_data) ) ? "'" . $data . "'"
                                       : $sql_data . ", '" . $data . "'";
        }
        $sql = "INSERT INTO " . $db_tb_user . " (" . $sql_key . ") VALUES (" . $sql_data . ")";
        $result = $db->db_query($sql)
            or error(GENERAL_ERROR, 'Could not query config information.', '',
               __FILE__, __LINE__, $sql);

        return 2;
    }
}
?>