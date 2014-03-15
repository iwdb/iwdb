<?php
/*****************************************************************************
 * de_wirtschaftgeb.php                                                      *
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

function parse_de_wirtschaft_geb($aParserData)
{
    global $db, $db_tb_gebaeude_spieler, $selectedusername;

    $AccName = getAccNameFromKolos($aParserData->objResultData->aKolos);
    if ($AccName === false) { //kein Eintrag gefunden -> ausgewählten Accname verwenden
        $AccName = $selectedusername;
    }

    //alle alten Einträge des Accs weg
    $sql = "DELETE FROM " . $db_tb_gebaeude_spieler . " WHERE user='" . $AccName . "'";
    $db->db_query($sql);

    $aCoords_old = array();
    foreach ($aParserData->objResultData->aAreas as $area) {
        foreach ($area->aBuildings as $building) {
            foreach ($building->aCounts as $coords => $iGebcount) {

                $aCoords = explode(":", $coords);
                if (count($aCoords) !== 3) {        //Koordinaten ungültig
                    throw new Exception("Fehlerhafte Koordinaten!");
                    break;
                }

                if ($aCoords !== $aCoords_old) {
                    //alle alten Einträge des Planies weg die nicht zum Acc gehören
                    $sql = "DELETE FROM " . $db_tb_gebaeude_spieler . " WHERE coords_gal=" . (int)$aCoords[0] . " AND coords_sys=" . (int)$aCoords[1] . " AND coords_planet=" . (int)$aCoords[2] . " AND user!='" . $AccName . "';";
                    $db->db_query($sql);

                    $aCoords_old = $aCoords;
                }

                $SQLdata = array (
                    'coords_gal' => (int)$aCoords[0],
                    'coords_sys' => (int)$aCoords[1],
                    'coords_planet' => (int)$aCoords[2],
                    'kolo_typ' => $aParserData->objResultData->aKolos[$coords]->strObjectType,
                    'user'  => $AccName,
                    'category' => htmlspecialchars(trim($area->strAreaName), ENT_QUOTES, 'UTF-8'),
                    'building' => htmlspecialchars($building->strBuildingName, ENT_QUOTES, 'UTF-8'),
                    'count' => $iGebcount,
                    'time' => CURRENT_UNIX_TIME
                );

                $db->db_insertupdate($db_tb_gebaeude_spieler, $SQLdata);

            }
        }
    }

    echo "<div class='system_notification'>Gebäudeübersicht für {$AccName} aktualisiert.</div>";

    return;
}