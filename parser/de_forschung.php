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

//direktes Aufrufen verhindern
if (!defined('IRA')) {
    header('HTTP/1.1 403 forbidden');
    exit;
}

if (!defined('DEBUG_LEVEL')) {
    define('DEBUG_LEVEL', 0);
}

function parse_de_forschung($return)
{
    global $db, $db_tb_research, $db_tb_user_research, $db_tb_research2user, $selectedusername;

    debug_var("input", $return);

    $iResearchCount = 0;
    $research2id = array();

    $sql = "SELECT ID, name FROM " . $db_tb_research . " ORDER BY ID ASC";
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
    while ($row = $db->db_fetch_array($result)) {
        $research2id[$row["name"]] = $row['ID'];
    }

    if (count($return->objResultData->aResearchsResearched) > 2) { //! ausgeklappte/vollständige Forschungsseite -> vollständiger Reset der Daten
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

        //Forschungspunkte aktualisieren
        if (empty($research->iFP_akt)) {      //für Kompatibilität mit älterer Parserlib
            $research->iFP_akt = $research->iFP * ($research->iResearchCosts / 100.);
        }
        updateResearchFP($research->strResearchName, $research->iFP_akt);

        ++$iResearchCount;
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

        //Forschungspunkte aktualisieren
        if (empty($research->iFP_akt)) {      //für Kompatibilität mit älterer Parserlib
            $research->iFP_akt = $research->iFP * ($research->iResearchCosts / 100.);
        }
        updateResearchFP($research->strResearchName, $research->iFP_akt);

        ++$iResearchCount;
    }

    foreach ($return->objResultData->aResearchsOpen as $research) {
        //Forschungspunkte aktualisieren
        if (empty($research->iFP_akt)) {      //für Kompatibilität mit älterer Parserlib
            $research->iFP_akt = $research->iFP * ($research->iResearchCosts / 100.);
        }
        updateResearchFP($research->strResearchName, $research->iFP_akt);

        ++$iResearchCount;
    }

    echo "<div class='system_notification'> " . $iResearchCount . " Forschungen aktualisiert.</div>";

}

function updateResearchFP($strResearchName, $iFP) {
    Global $db, $db_tb_research;

    if (!empty($strResearchName)) {
        return false;
    }

    $strResearchName = $db->escape($strResearchName);
    $iFP = (int)$iFP;

    $sql = "UPDATE " . $db_tb_research . " SET" .
        " FPakt=" . $iFP . "," .
        " time=" . CURRENT_UNIX_TIME .
        " WHERE name='" . $strResearchName . "'";
    $result = $db->db_query($sql);

    return $result;
}