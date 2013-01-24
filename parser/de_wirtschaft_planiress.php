<?php
/*****************************************************************************
 * de_wirtschaft_planiress.php                                               *
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

function parse_de_wirtschaft_planiress($return)
{
    if ($return->bSuccessfullyParsed) {

        global $selectedusername, $db, $db_tb_lager, $db_tb_ressuebersicht;

        $AccName = getAccNameFromKolos($return->objResultData->aKolos);
        if ($AccName === false) { //kein Eintrag gefunden -> ausgewählten Accname verwenden
            $AccName = $selectedusername;
        }
        debug_var('wirtschaft_planiress', $AccName);

        $scan_data_total = array();
        $scan_data_total['user'] = $AccName;
        $scan_data_total['datum'] = CURRENT_UNIX_TIME;

        if (!$return->objResultData->bLagerBunkerVisible) {
            doc_message("Keine LagerBunker Infos sichtbar! Bitte 'Lager und Bunker anzeigen' aktivieren.");
        }

        foreach ($return->objResultData->aKolos as $Kolo) {
            $scan_data                  = array();
            $scan_data['user']          = $AccName;

            $scan_data['coords_gal']    = $Kolo->aCoords["coords_gal"];
            $scan_data['coords_sys']    = $Kolo->aCoords["coords_sol"];
            $scan_data['coords_planet'] = $Kolo->aCoords["coords_pla"];
            $scan_data['kolo_typ']      = $Kolo->strObjectType;

            foreach ($Kolo->aData as $resource) {
                $resource_name = $resource->strResourceName;
                $resource_name = trim(strtolower($resource_name));
                if (strpos($resource_name, "chem") !== false) {
                    $resource_name = "chem";
                }

                $scan_data[$resource_name]             = $resource->iResourceVorrat;
                $scan_data[$resource_name . '_prod']   = $resource->fResourceProduction;
                $scan_data[$resource_name . '_bunker'] = $resource->iResourceBunker;
                if (($resource_name === "chem") OR ($resource_name === "eis") OR ($resource_name === "energie")) {
                    $scan_data[$resource_name . '_lager']  = $resource->iResourceLager;
                }

                if (!isset($scan_data_total[$resource_name])) {
                    $scan_data_total[$resource_name] = 0;
                }
                $scan_data_total[$resource_name] += $resource->fResourceProduction;
            }

            $scan_data['time']          = CURRENT_UNIX_TIME;

            debug_var('wirtschaft_planiress', $scan_data);
            $db->db_insertupdate($db_tb_lager, $scan_data)
                or error(GENERAL_ERROR, 'Could not update ress information.', '', __FILE__, __LINE__);
        }

        //Einträge in der Lagertabelle von nicht mehr vorhandenen Kolos/Basen etc weg (diese wurden nicht aktualisiert)
        $sql = "DELETE FROM `{$db_tb_lager}` WHERE `user` = '{$AccName}' AND `time` != ".CURRENT_UNIX_TIME.";";
        $db->db_query($sql)
            or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

        debug_var('wirtschaft_planiress', $scan_data_total);
        $db->db_insertupdate($db_tb_ressuebersicht, $scan_data_total)
            or error(GENERAL_ERROR, 'Could not update total ress information.', '', __FILE__, __LINE__);

        echo "<div class='system_notification'>Produktion Teil 1 aktualisiert/hinzugefügt.</div>";
    }
}