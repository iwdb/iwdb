<?php
/*****************************************************************************
 * de_highscore.php                                                          *
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

function parse_de_highscore($result)
{
    $aktualisierungszeit = $result->objResultData->iTimestamp;
    $bDateOfEntryVisible = $result->objResultData->bDateOfEntryVisible;
    $strHighscoreType    = $result->objResultData->strType;

    $aHighscoreTypen     = array('Demokraten', 'Diktatoren', 'Kommunisten', 'Monarchen');
    $aStaatsformen       = array('Demokrat', 'Diktator', 'Kommunist', 'Monarch');
    $strStaatsform       = str_replace($aHighscoreTypen, $aStaatsformen, $strHighscoreType);

    $count = 0;
    foreach ($result->objResultData->aMembers as $object_user) {
        $scan_data = array();

        $scan_data['name']    = $object_user->strName;
        $scan_data['allianz'] = $object_user->strAllianz;

        if ($bDateOfEntryVisible) {
            $scan_data['dabei_seit'] = $object_user->iDabeiSeit;
        }

        if ($strHighscoreType === false) {              //Position ist nur bei Highscore aller Spieler gültig
            $scan_data['pos'] = $object_user->iPos;
        } else {
            $scan_data['pos'] = null;
        }

        $scan_data['gebp']    = $object_user->iGebP;
        $scan_data['fp']      = $object_user->iFP;
        $scan_data['gesamtp'] = $object_user->iGesamtP;
        $scan_data['ptag']    = $object_user->iPperDay;
        $scan_data['diff']    = $object_user->iPosChange;
        $scan_data['time']    = $aktualisierungszeit;

        save_highscore($scan_data);

        //nochmal das ganze für die Spielertabelle
        //ToDo: Highscoredaten nur noch in die Spielertabelle oder nur noch in die Highscoretabelle?

        $scan_data = array();
        $scan_data['name']    = $object_user->strName;
        $scan_data['allianz'] = $object_user->strAllianz;
        if (!empty($strStaatsform)) {              //Staatsform eintragen falls spezifische Staatsform-Highscore
            $scan_data['staatsform'] = $strStaatsform;
        }
        $scan_data['geb_pkt'] = $object_user->iGebP;
        $scan_data['forsch_pkt'] = $object_user->iFP;
        $scan_data['ges_pkt'] = $object_user->iGesamtP;
        $scan_data['forsch_pkt'] = $object_user->iFP;
        $scan_data['pktupdate_time']    = $aktualisierungszeit;

        save_playerdata($scan_data);

        $count++;
    }

    echo "<div class='system_notification'>" . $count . " Highscore(s) hinzugefügt.</div>";

    return true;
}

function save_highscore($scan_data)
{
    global $db, $db_tb_highscore, $db_tb_scans;

    $scan_data["gebp_nodiff"] = $scan_data["time"];
    $scan_data["fp_nodiff"]   = $scan_data["time"];

    $sql = "SELECT * FROM " . $db_tb_highscore . " WHERE name='" . $scan_data['name'] . "'";
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
    if ($row = $db->db_fetch_array($result)) {
        if ($row["gebp"] == $scan_data["gebp"]) {
            $scan_data["gebp_nodiff"] = $row["gebp_nodiff"];
        }
        if ($row["fp"] == $scan_data["fp"]) {
            $scan_data["fp_nodiff"] = $row["fp_nodiff"];
        }
    }

    $db->db_insertupdate($db_tb_highscore, $scan_data)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__);

    //Punkte in die Kartendaten übertragen
    $db->db_update($db_tb_scans, array('punkte' => $scan_data["gesamtp"]), "WHERE user='" . $scan_data["name"] . "'")
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__);

}

function save_playerdata($scan_data)
{
    global $db, $db_tb_spieler;

    $name                = $db->escape($scan_data['name']);
    $allianz             = $db->escape($scan_data['allianz']);
    $aktualisierungszeit = (int)$scan_data['pktupdate_time'];
    $sql_player_update   = "INSERT INTO `{$db_tb_spieler}` (`name`, `allianz`, `playerupdate_time`) VALUES ('$name', '$allianz', $aktualisierungszeit)";

    //bei schon vorhandenem Spieler in der DB prüfen auf Allianzänderung
    $sql_player_update .= " ON DUPLICATE KEY UPDATE";
    $sql_player_update .= " `allychange_time` = IF((STRCMP(VALUES(`allianz`), `allianz`) AND ((`allychange_time` IS NULL) OR ({$aktualisierungszeit} > `allychange_time`))), {$aktualisierungszeit}, `allychange_time`),"; //Allianzänderungszeit auf die des Scans setzen (wenn sie neuer bzw nicht vorhanden ist und sich die Allianz geändert hat), nachfolgende Abfragen können sich dann darauf beziehen
    $sql_player_update .= " `exallianz` =   IF(((`allychange_time` = {$aktualisierungszeit}) AND (`playerupdate_time` < {$aktualisierungszeit})), `allianz`, `exallianz`),"; //exallianz aktualisieren
    $sql_player_update .= " `allianzrang` = IF(((`allychange_time` = {$aktualisierungszeit}) AND (`playerupdate_time` < {$aktualisierungszeit})), NULL, `allianzrang`),"; //alten Allianzrang löschen
    $sql_player_update .= " `allianz` =     IF(((`allychange_time` = {$aktualisierungszeit}) AND (`playerupdate_time` < {$aktualisierungszeit})), VALUES(`allianz`), `allianz`),"; //neue Allianz schreiben
    $sql_player_update .= " `playerupdate_time` = IF((`playerupdate_time` < {$aktualisierungszeit}), {$aktualisierungszeit}, `playerupdate_time`);"; //Angabe des Updates der Spielerinformationen aktualisieren

    $db->db_query($sql_player_update);

    //Allianzänderungen in Historytabele übertragen
    AddAllychangetoHistory($aktualisierungszeit);

    //aktuelle Allianzen in alle Kartendaten übertragen
    SyncAllies($aktualisierungszeit);

    unset($scan_data['allianz']);
    $db->db_insertupdate($db_tb_spieler, $scan_data)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__);
}