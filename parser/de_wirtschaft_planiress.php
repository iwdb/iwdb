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
    global $selectedusername;

    $scan_data       = array(); //! Eintragung in die Lagertabelle (nach Kolos ausgeschluesselt)
    $scan_data_total = array(); //! werden in die Ressuebersicht eingetragen

    if (!$return->objResultData->bLagerBunkerVisible) {
        echo "<div class='doc_message'>Info:</font> keine LagerBunker Infos sichtbar! Bitte 'Lager und Bunker anzeigen' aktivieren</div>";
    }

    foreach ($return->objResultData->aKolos as $kolo) {
        $scan_data['coords_gal']    = $kolo->aCoords["coords_gal"];
        $scan_data['coords_sys']    = $kolo->aCoords["coords_sol"];
        $scan_data['coords_planet'] = $kolo->aCoords["coords_pla"];
        $scan_data['kolo_typ']      = $kolo->strObjectType;

        foreach ($kolo->aData as $resource) {
            $resource_name = $resource->strResourceName;
            $resource_name = trim(strtolower($resource_name));
            if (strpos($resource_name, "chem") !== false) {
                $resource_name = "chem";
            }

            $scan_data[$resource_name]             = $resource->iResourceVorrat;
            $scan_data[$resource_name . '_prod']   = $resource->fResourceProduction;
            $scan_data[$resource_name . '_bunker'] = $resource->iResourceBunker;
            $scan_data[$resource_name . '_lager']  = $resource->iResourceLager;

            if (!isset($scan_data_total["total_" . $resource_name . "_prod"])) {
                $scan_data_total["total_" . $resource_name . "_prod"] = 0;
            }
            if (!isset($scan_data_total["total_" . $resource_name])) {
                $scan_data_total["total_" . $resource_name] = 0;
            }

            $scan_data_total["total_" . $resource_name . "_prod"] += $resource->fResourceProduction;
            $scan_data_total["total_" . $resource_name] += $resource->iResourceVorrat;
        }
        insert_data($scan_data);
    }
    delete_old_entries($selectedusername, CURRENT_UNIX_TIME);
    insert_data_total($scan_data_total);

    echo "<div class='system_notification'>Produktion Teil 1 aktualisiert/hinzugefügt.</div>";
}

function insert_data($scan_data)
{
    global $db, $db_tb_lager, $selectedusername;

    $sql = "INSERT INTO " . $db_tb_lager . " (";
    $sql .= "user,coords_gal,coords_sys,coords_planet,kolo_typ,";
    $sql .= "eisen,eisen_prod,eisen_bunker,stahl,stahl_prod,stahl_bunker,";
    $sql .= "vv4a,vv4a_prod,vv4a_bunker,chem,chem_prod,chem_lager,chem_bunker,";
    $sql .= "eis,eis_prod,eis_lager,eis_bunker,wasser,wasser_prod,wasser_bunker,";
    $sql .= "energie,energie_prod,energie_lager,energie_bunker,time) VALUES (";
    $sql .= "'" . $selectedusername . "',";
    $sql .= $scan_data['coords_gal'] . ",";
    $sql .= $scan_data['coords_sys'] . ",";
    $sql .= $scan_data['coords_planet'] . ",";
    $sql .= "'" . $scan_data['kolo_typ'] . "',";
    $sql .= $scan_data['eisen'] . "," . $scan_data['eisen_prod'] . "," . $scan_data['eisen_bunker'] . ",";
    $sql .= $scan_data['stahl'] . "," . $scan_data['stahl_prod'] . "," . $scan_data['stahl_bunker'] . ",";
    $sql .= $scan_data['vv4a'] . "," . $scan_data['vv4a_prod'] . "," . $scan_data['vv4a_bunker'] . ",";
    $sql .= $scan_data['chem'] . "," . $scan_data['chem_prod'] . "," . $scan_data['chem_lager'] . "," . $scan_data['chem_bunker'] . ",";
    $sql .= $scan_data['eis'] . "," . $scan_data['eis_prod'] . "," . $scan_data['eis_lager'] . "," . $scan_data['eis_bunker'] . ",";
    $sql .= $scan_data['wasser'] . "," . $scan_data['wasser_prod'] . "," . $scan_data['wasser_bunker'] . ",";
    $sql .= $scan_data['energie'] . "," . $scan_data['energie_prod'] . "," . $scan_data['energie_lager'] . "," . $scan_data['energie_bunker'] . ",";
    $sql .= CURRENT_UNIX_TIME;
    $sql .= ") ON DUPLICATE KEY UPDATE";
    $sql .= " user='" . $selectedusername . "'";
    $sql .= ",kolo_typ='" . $scan_data["kolo_typ"] . "'";
    $sql .= ",eisen=" . $scan_data["eisen"] . ",eisen_prod=" . $scan_data['eisen_prod'] . ",eisen_bunker=" . $scan_data['eisen_bunker'];
    $sql .= ",stahl=" . $scan_data["stahl"] . ",stahl_prod=" . $scan_data['stahl_prod'] . ",stahl_bunker=" . $scan_data['stahl_bunker'];
    $sql .= ",vv4a=" . $scan_data["vv4a"] . ",vv4a_prod=" . $scan_data['vv4a_prod'] . ",vv4a_bunker=" . $scan_data['vv4a_bunker'];
    $sql .= ",chem=" . $scan_data["chem"] . ",chem_prod=" . $scan_data['chem_prod'] . ",chem_lager=" . $scan_data['chem_lager'] . ",chem_bunker=" . $scan_data['vv4a_bunker'];
    $sql .= ",eis=" . $scan_data["eis"] . ",eis_prod=" . $scan_data['eis_prod'] . ",eis_lager=" . $scan_data['eis_lager'] . ",eis_bunker=" . $scan_data['eis_bunker'];
    $sql .= ",wasser=" . $scan_data["wasser"] . ",wasser_prod=" . $scan_data['wasser_prod'] . ",wasser_bunker=" . $scan_data['wasser_bunker'];
    $sql .= ",energie=" . $scan_data["energie"] . ",energie_prod=" . $scan_data['energie_prod'] . ",energie_lager=" . $scan_data['energie_lager'] . ",energie_bunker=" . $scan_data['energie_bunker'];
    $sql .= ",time=" . CURRENT_UNIX_TIME;

    $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

}

function insert_data_total($scan_data)
{
    global $db, $db_tb_ressuebersicht, $selectedusername;

//	debug_var('insert_data_total()', '');

    if (empty($db_tb_ressuebersicht)) {
        return;
    }

    $sql = "INSERT INTO " . $db_tb_ressuebersicht;
    $sql .= " (user,datum,eisen,stahl,chem,vv4a,eis,wasser,energie) VALUES (";
    $sql .= "'" . $selectedusername . "'";
    $sql .= "," . CURRENT_UNIX_TIME;
    $sql .= "," . $scan_data['total_eisen_prod'];
    $sql .= "," . $scan_data['total_stahl_prod'];
    $sql .= "," . $scan_data['total_chem_prod'];
    $sql .= "," . $scan_data['total_vv4a_prod'];
    $sql .= "," . $scan_data['total_eis_prod'];
    $sql .= "," . $scan_data['total_wasser_prod'];
    $sql .= "," . $scan_data['total_energie_prod'];
    $sql .= ") ON DUPLICATE KEY UPDATE";
    $sql .= " datum=" . CURRENT_UNIX_TIME;
    $sql .= ",eisen=" . $scan_data['total_eisen_prod'];
    $sql .= ",stahl=" . $scan_data['total_stahl_prod'];
    $sql .= ",chem=" . $scan_data['total_chem_prod'];
    $sql .= ",vv4a=" . $scan_data['total_vv4a_prod'];
    $sql .= ",eis=" . $scan_data['total_eis_prod'];
    $sql .= ",wasser=" . $scan_data['total_wasser_prod'];
    $sql .= ",energie=" . $scan_data['total_energie_prod'];
    $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

}

function delete_old_entries($username, $time)
{
    global $db, $db_tb_lager;

    if (empty($username) OR empty($time)) {
        return;
    }

    $username = $db->escape($username);
    $time     = (int)$time;

    //Einträge in der Lagertabelle von nicht mehr vorhandenen Kolos/Basen etc weg (diese wurden nicht aktualisiert)
    $sql  = "DELETE FROM " . $db_tb_lager;
    $sql .= " WHERE user = '" . $username . "'";
    $sql .= " AND time != " . $time . ";";
    $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
}