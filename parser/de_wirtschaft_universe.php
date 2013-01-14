<?php
/*****************************************************************************
 * index.php                                                                 *
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
 * Diese Erweiterung der urspünglichen DB ist ein Gemeinschafftsprojekt von  *
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

function parse_de_wirtschaft_universe($return)
{
    global $db, $db_tb_user, $selectedusername;

    //Zeitpunkt der nächsten UniXML-Parsemöglichkeit ggf anpassen

    $iNewUniXmlTime = $return->objResultData->iNewUniXmlTime;
    if ($iNewUniXmlTime === false) { //keine Zeit angegeben, UniXml vorhanden -> neuer Scan sofort verfügbar
        $iNewUniXmlTime = CURRENT_UNIX_TIME;
    }

    $sql = "SELECT `NewUniXmlTime` FROM `$db_tb_user` WHERE `sitterlogin`='" . $selectedusername . "';";
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query NewUniXmlTime information.', '', __FILE__, __LINE__, $sql);
    $row = $db->db_fetch_array($result);

    if (empty($row['NewUniXmlTime']) OR ($row['NewUniXmlTime'] < $iNewUniXmlTime)) {
        $sql = "UPDATE `$db_tb_user` SET `NewUniXmlTime` = " . $iNewUniXmlTime . " WHERE `sitterlogin`='" . $selectedusername . "';";
        $result = $db->db_query($sql)
            or error(GENERAL_ERROR, 'Could not update NewUniXmlTime information.', '', __FILE__, __LINE__, $sql);

        //echo "<div class='system_message'>Zeitpunkt der nächsten Unixml Parsemöglichkeit bei " . $selectedusername . " angepasst.</div>";
        if ($iNewUniXmlTime <= CURRENT_UNIX_TIME) {
            echo "<div class='system_notification'>Nächster UniXmlScan bei " . $selectedusername . " ab sofort möglich.</div>";
        } else {
            $nextXmlTime = makeduration2(CURRENT_UNIX_TIME, $iNewUniXmlTime);
            echo "<div class='system_notification'>Nächster UniXML Scan bei " . $selectedusername . " möglich in " . $nextXmlTime . "</div>";
        }
    } else {
        $nextXmlTime = makeduration2(CURRENT_UNIX_TIME, $iNewUniXmlTime);
        echo "<div class='system_notification'>Nächster UniXML Scan bei " . $selectedusername . " möglich in " . $nextXmlTime . "</div>";
    }
}