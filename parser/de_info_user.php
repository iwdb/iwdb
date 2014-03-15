<?php
/*****************************************************************************
 * de_info_user.php                                                          *
 *****************************************************************************
 * Iw DB: Icewars geoscan and sitter database                                *
 * Open-Source Project started by Robert Riess (robert@riess.net)            *
 * ========================================================================= *
 * Copyright (c) 2004 Robert Riess - All Rights Reserved                     *
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
 * Autor: masel <masel789@googlemail.com>                                    *
 * Datum: Januar 2013                                                        *
 *                                                                           *
 * Bei Problemen kannst du dich an das eigens dafür eingerichtete            *
 * Entwicklerforum wenden:                                                   *
 *        https://handels-gilde.org/?www/forum/index.php;board=1099.0        *
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

function parse_de_info_user($aParserData)
{
    debug_var('Input', $aParserData);

    global $db, $db_tb_spieler;

    $playerinfo              = $aParserData->objResultData;
    $playerinfo->strUserName = $db->escape($playerinfo->strUserName);

    $sql    = "SELECT name, allianz FROM `{$db_tb_spieler}` WHERE name='" . $playerinfo->strUserName . "';";
    $result = $db->db_query($sql);
    $row    = $db->db_fetch_array($result);

    $data = array(
        'name'              => $playerinfo->strUserName,
        'allianz'           => $playerinfo->strUserAllianceTag,
        'allianzrang'       => $playerinfo->strUserAllianceJob,
        'acctype'           => $playerinfo->strAccType,
        'dabeiseit'         => $playerinfo->iEntryDate,
        'playerupdate_time' => CURRENT_UNIX_TIME,
        'gebp'              => $playerinfo->iGebPkt,
        'fp'                => $playerinfo->iFP,
        'gesamtp'           => ($playerinfo->iGebPkt + $playerinfo->iFP),
        'pktupdate_time'    => CURRENT_UNIX_TIME,
        'Hauptplanet'       => $playerinfo->strCoords,
    );

    $db->db_insertupdate($db_tb_spieler, $data);

    if (!empty($row)) { //Spieler war schon vorhanden

        if ($row['allianz'] !== $playerinfo->strUserAllianceTag) { //Allianz hat sich geändert

            $data = array(
                'exallianz'       => $row['allianz'],
                'allychange_time' => CURRENT_UNIX_TIME,
            );
            $db->db_update($db_tb_spieler, $data, "WHERE `name`='" . $playerinfo->strUserName . "'");

            //geänderte Allianzdaten angleichen
            AddAllychangetoHistory(CURRENT_UNIX_TIME);
            SyncAllies(CURRENT_UNIX_TIME);

        }

        doc_message("Spieler " . $playerinfo->strUserName . " aktualisiert");

    } else { //neuer Spieler
        doc_message("Spieler " . $playerinfo->strUserName . " hinzugefügt");
    }
}