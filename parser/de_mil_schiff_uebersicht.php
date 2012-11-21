<?php
/*****************************************************************************/
/* de_mil_schiff_uebersicht.php                                              */
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
/* Datum: April 2012                                                         */
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

function parse_de_mil_schiff_uebersicht ( $return )
{    
    global $db, $db_tb_schiffstyp, $db_tb_schiffe, $db_tb_user, $selectedusername;
    
//    foreach ($return->objResultData->aKolos as $kolo)
//	{
        //! Mac: @todo: Kolonien pruefen ?
//	}
    
    if ($return->bSuccessfullyParsed) {
        // Setze Zeitpunkt des letzten Schiffsimportes 
        $sql = "DELETE FROM " . $db_tb_schiffe . 
            " WHERE user='" . $selectedusername . "'";
        $result = $db->db_query($sql)
            or error(GENERAL_ERROR, 
                'Could not query config information.', '', 
                __FILE__, __LINE__, $sql);

        $sql = "UPDATE " . $db_tb_user . " SET lastshipscan='" . CURRENT_UNIX_TIME .
            "' WHERE sitterlogin='" . $selectedusername . "'";
        $result = $db->db_query($sql)
            or error(GENERAL_ERROR, 
                'Could not query config information.', '',
                    __FILE__, __LINE__, $sql);

        foreach ($return->objResultData->aSchiffe as $aschiff)
        {
            if (empty($aschiff->strSchiffName)) continue;

            // Name des Schiffstyps bestimmen
            $schiffsname = $aschiff->strSchiffName;

            // Massdriver Pakete sind keine gültigen Schiffe. 
            if ( $schiffsname !== "Massdriver Paket" ) {
                // Suche ID des Schiffstyps in der DB
                $sql = "SELECT id FROM " . $db_tb_schiffstyp . 
                    " WHERE schiff LIKE '" . $schiffsname . "' OR schiff LIKE '" . $schiffsname . "'";               
                $result = $db->db_query($sql)
                    or error(GENERAL_ERROR, 
                        'Could not query config information.', '', 
                        __FILE__, __LINE__, $sql);
                $row = $db->db_fetch_array($result); 

                // ID nicht gefunden -> neu einfügen.
                if (empty($row['id'])) {
                    echo "unbekanntes Schiff gefunden: $schiffsname<br />";
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

                    $row['id'] = mysql_insert_id();
                    */
                }

                $sql = "INSERT INTO " . $db_tb_schiffe . 
                " (user, schiff, anzahl) VALUES ('" . 
                $selectedusername . "', '" . $row['id'] . "', '" . 
                $aschiff->iCountGesamt. "')";
                $result = $db->db_query($sql)
                    or error(GENERAL_ERROR, 
                    'Could not query config information.', '', 
                    __FILE__, __LINE__, $sql);
            }   
        }
    }
}

?>