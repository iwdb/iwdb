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

if (!defined('IRA')) {
    die('Hacking attempt...');
}

function parse_de_info_user($return)
{
    if ($return->bSuccessfullyParsed) {

        global $db, $db_tb_spieler;

        $playerinfo              = $return->objResultData;
        $playerinfo->strUserName = $db->escape($playerinfo->strUserName);

        $sql = "SELECT name, allianz FROM `{$db_tb_spieler}` WHERE name='" . $playerinfo->strUserName . "';";
        $result = $db->db_query($sql)
            or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

        $row = $db->db_fetch_array($result);

        $data = array (
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

        $db->db_insertupdate($db_tb_spieler, $data)
            or error(GENERAL_ERROR, 'Could not insert player information.', '', __FILE__, __LINE__);

        if (!empty($row)) { //Spieler war schon vorhanden

            if ($row['allianz'] !== $playerinfo->strUserAllianceTag) { //Allianz hat sich geändert

                $data = array(
                    'exallianz'       => $row['allianz'],
                    'allychange_time' => CURRENT_UNIX_TIME,
                );
                $result = $db->db_update($db_tb_spieler, $data, "WHERE `name`='" . $playerinfo->strUserName . "'")
                    or error(GENERAL_ERROR, 'Could not update player alliance information.', '', __FILE__, __LINE__, $sql);

                //geänderte Allianzdaten angleichen
                AddAllychangetoHistory(CURRENT_UNIX_TIME);
                SyncAllies(CURRENT_UNIX_TIME);

            }

            doc_message("Spieler " . $playerinfo->strUserName . " aktualisiert");

        } else { //neuer Spieler
            doc_message("Spieler " . $playerinfo->strUserName . " hinzugefügt");
        }
    }
}