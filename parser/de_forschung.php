<?php
/*****************************************************************************
 * de_forschung.php                                                          *
 *****************************************************************************
 * This program is free software; you can redistribute it and/or modify it   *
 * under the terms of the GNU General Public License as published by the     *
 * Free Software Foundation; either version 2 of the License, or (at your    *
 * option) any later version.                                                *
 *                                                                           *
 * This program is distributed in the hope that it will be useful, but       *
 * WITHOUT ANY WARRANTY; without even the implied warranty of                *
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General *
 * Public License for more details.                                          *
 *                                                                           *
 * The GNU GPL can be found in LICENSE in this directory                     *
 *****************************************************************************
 * Diese Erweiterung der ursprünglichen DB ist ein Gemeinschaftsprojekt von  *
 * IW-Spielern.                                                              *
 *                                                                           *
 * Autor: Mac (MacXY@herr-der-mails.de)                                      *
 * Datum: April 2012                                                         *
 *                                                                           *
 * Bei Problemen kannst du dich an das eigens dafür eingerichtete            *
 * Entwicklerforum wenden:                                                   *
 *                   https://www.handels-gilde.org                           *
 *                   https://github.com/iwdb/iwdb                            *
 *                                                                           *
 *****************************************************************************/

if (!defined('IRA')) {
    die('Hacking attempt...');
}

if (!defined('DEBUG_LEVEL')) {
    define('DEBUG_LEVEL', 0);
}

function parse_de_forschung($return)
{
    global $db, $db_tb_research, $db_tb_user_research, $db_tb_research2user, $selectedusername;

    $research2id = array();

    $sql = "SELECT ID, name FROM " . $db_tb_research . " ORDER BY ID ASC";
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
    while ($row = $db->db_fetch_array($result)) {
        $research2id[$row["name"]] = $row['ID'];
    }

    $akt_fp        = array();
    $akt_forschung = 0;
    $akt_date      = 0;

    if (count($return->objResultData->aResearchsResearched) > 2) { //! ausgeklappte/vollstaendige Forschungsseite -> vollst. Reset der Daten
        $sql = "DELETE FROM
                    $db_tb_research2user
                WHERE
                    `userid` = '$selectedusername'
                ";
        $db->db_query($sql)
            or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
    } else {
        echo "Es wurden nur die sichtbaren Forschungen eingetragen! Verwende \"Alle Forschungen anzeigen\" für eine vollständige Eintragung<br />";
    }
    /*
	 *von Patsch: rausgenommen, da man ansonsten die Startseite direkt wieder einlesen muss, um die aktuell laufenden Forschungen zu bekommen
	
    //! aktuell laufende Forschungen resetten
    $sql = "DELETE FROM " . $db_tb_user_research . 
			" WHERE user='" . $selectedusername . "'";
	$result = $db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
	*/

    //! Mac: hier könnten mit Genetik auch zwei Forschungen laufen, deswegen in der Schleife
    foreach ($return->objResultData->aResearchsProgress as $research) {
        $akt_forschung = isset($research2id[$research->strResearchName]) ? $research2id[$research->strResearchName] : "";
        $akt_date      = $research->iUserResearchTime;
        if (!empty($akt_forschung) && !empty($akt_data)) {
            $sql = "INSERT INTO " . $db_tb_user_research .
                " SET user='" . $selectedusername . "', rid='" . $akt_forschung . "', date=" . $akt_date;
            $result = $db->db_query($sql)
                or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
        }
    }

    foreach ($return->objResultData->aResearchsResearched as $research) {
        $rid = isset($research2id[$research->strResearchName]) ? $research2id[$research->strResearchName] : "";
        if (!empty($rid)) {
            $sql = "INSERT
                        INTO
                            $db_tb_research2user
                            (userid,rid)
                        VALUES
                            ('$selectedusername',$rid)
                        ON DUPLICATE KEY UPDATE
                            rid = $rid
                        ";
            $db->db_query($sql)
                or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
        } else {
            echo "Forschungs ID für '$research->strResearchName' konnte nicht bestimmt werden<br />";
        }
        $akt_fp[$research->strResearchName] = $research->iFP * ($research->iResearchCosts / 100.);
    }

    foreach ($return->objResultData->aResearchsOpen as $research) {
        $akt_fp[$research->strResearchName] = $research->iFP * ($research->iResearchCosts / 100.);
    }

    foreach ($akt_fp as $key => $value) {
        $sql = "UPDATE " . $db_tb_research . " SET" .
            " FPakt=" . $value . "," .
            " time=" . CURRENT_UNIX_TIME .
            " WHERE name='" . $key . "'";
        $result = $db->db_query($sql)
            or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
    }

    doc_message('done');
}