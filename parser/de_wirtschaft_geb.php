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

if (!defined('IRA')) {
    die('Hacking attempt...');
}

if (!defined('DEBUG_LEVEL')) {
    define('DEBUG_LEVEL', 0);
}

function parse_de_wirtschaft_geb($return)
{
    global $db, $db_tb_gebaeude_spieler, $selectedusername;
    $count = 0;

    $AccName = getAccNameFromKolos($return->objResultData->aKolos);
    if ($AccName === false) { //kein Eintrag gefunden -> ausgewählten Accname verwenden
        $AccName = $selectedusername;
    }

    $sql = "DELETE FROM " . $db_tb_gebaeude_spieler . " WHERE user='" . $AccName . "'";
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

    foreach ($return->objResultData->aAreas as $area) {
        foreach ($area->aBuildings as $building) {
            $sql = "REPLACE INTO $db_tb_gebaeude_spieler ("
                 . "coords_gal,coords_sys,coords_planet,kolo_typ,user,category,building,count,time"
                 . ") VALUES ";

            foreach ($building->aCounts as $coords => $count) {
                $aCoords = explode(":", $coords);
                $sql .= "(";
                $sql .= $aCoords[0];
                $sql .= "," . $aCoords[1];
                $sql .= "," . $aCoords[2];
                $sql .= ",'" . $return->objResultData->aKolos[$coords]->strObjectType . "'";
                $sql .= ",'" . $AccName . "'";
                $sql .= ",'" . $area->strAreaName . "'";
                $sql .= ",'" . $building->strBuildingName . "'";
                $sql .= "," . $count;
                $sql .= "," . CURRENT_UNIX_TIME . "),
                    ";
            }
            $sql = preg_replace('@\,\s+\z@', ';', $sql);
            debug_var('sql', $sql);
            $db->db_query($sql)
                or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
            $count++;
        }
    }

    if ($count) {
        echo "<div class='system_notification'>Gebäudeübersicht aktualisiert.</div>";
    }

    return;
}