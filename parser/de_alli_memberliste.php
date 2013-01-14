<?php
/*****************************************************************************/
/* de_alli_memberliste.php                                                   */
/*****************************************************************************/
/* This program is free software; you can redistribute it and/or modify it   */
/* under the terms of the GNU General Public License as published by the     */
/* Free Software Foundation; either version 2 of the License, or (at your    */
/* option) any later version.                                                */
/*                                                                           */
/* This program is distributed in the hope that it will be useful, but       */
/* WITHOUT ANY WARRANTY; without even the implied warranty of                */
/* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General */
/* Public License for more details.                                          */
/*                                                                           */
/* The GNU GPL can be found in LICENSE in this directory                     */
/*****************************************************************************/

/*****************************************************************************/
/* Diese Erweiterung der urspuenglichen DB ist ein Gemeinschafftsprojekt von */
/* IW-Spielern.                                                              */
/*                                                                           */
/* Autor: Mac (MacXY@herr-der-mails.de)                                      */
/* Datum: Jun 2009 - April 2012                                              */
/*                                                                           */
/* Bei Problemen kannst du dich an das eigens dafür eingerichtete            */
/* Entwicklerforum wenden:                                                   */
/*        httpd://handels-gilde.org/?www/forum/index.php;board=1099.0        */
/*                   https://github.com/iwdb/iwdb                            */
/*                                                                           */
/*****************************************************************************/


if (!defined('DEBUG_LEVEL')) {
    define('DEBUG_LEVEL', 0);
}

if (!defined('IRA')) {
    die('Hacking attempt...');
}

function parse_de_alli_memberliste($result)
{
    //Allianz des User auslesen der geparsed wird
    global $user_id, $db, $db_prefix;
    $sql = "SELECT allianz FROM " . $db_prefix . "user WHERE id='" . $user_id . "';";
    $sqlres = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
    $row     = $db->db_fetch_array($sqlres);
    $allianz = $row['allianz'];
    echo "Member werden folgender Allianz zugeordnet: [" . $allianz . "]<br />";

    //! bisherige Member der Allianz suchen
    $oldMember = array();
    $sql       = "SELECT sitterlogin FROM " . $db_prefix . "user WHERE allianz = '" . $allianz . "'";
    $sqlres = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
    while ($row = $db->db_fetch_array($sqlres)) {
        array_push($oldMember, $row["sitterlogin"]);
    }

    $bDateOfEntryVisible = $result->objResultData->bDateOfEntryVisible;
    $bUserTitleVisible   = $result->objResultData->bUserTitleVisible;

    $aktMember = array();
    foreach ($result->objResultData->aMembers as $object_user) {
        $scan_udata = array();
        array_push($aktMember, $object_user->strName);

        $scan_udata['sitterlogin'] = $object_user->strName;
        $scan_udata['rang']        = $object_user->eRank;
        $scan_udata['gebp']        = $object_user->iGebP;
        $scan_udata['fp']          = $object_user->iFP;
        $scan_udata['allianz']     = $allianz;
        $scan_udata['gesamtp']     = $object_user->iGesamtP;
        $scan_udata['ptag']        = $object_user->iPperDay;

        if ($bDateOfEntryVisible) {
            $scan_udata['dabei'] = $object_user->iDabeiSeit;
        }

        if ($bUserTitleVisible) {
            $scan_udata['titel'] = $object_user->strTitel;
        }

        // Dann noch die gewonnenen Daten in die DB eintragen.
        updateuser($scan_udata);
    }

    //! Mac: in der original Version wurden nie Spieler entfernt ?
    foreach ($oldMember as $formerUser) {
        if (!in_array($formerUser, $aktMember, true)) {
            echo "Mac: todo: " . $formerUser . " entfernen<br />";
        }
    }
}

function updateuser($scan_data)
{
    global $db, $db_tb_user, $db_tb_punktelog;

    // Daten ins Punktelog übernehmen.
    $sql = "INSERT INTO " . $db_tb_punktelog . "(" .
        " user, date, gebp, fp, gesamtp, ptag" .
        ") VALUES (" .
        " '" . $scan_data['sitterlogin'] . "', '" . CURRENT_UNIX_TIME . "', '" .
        $scan_data['gebp'] . "', '" . $scan_data['fp'] . "', '" .
        $scan_data['gesamtp'] . "', '" . $scan_data['ptag'] . "' )";
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

    // Prüfe Mitglied, ob es bereits in der DB gespeichert ist.
    $sql = "SELECT sitterlogin FROM " . $db_tb_user .
        " WHERE sitterlogin='" . $scan_data['sitterlogin'] . "'";
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
    $row = $db->db_fetch_array($result);

    if (isset($row['sitterlogin'])) {
        // Das Mitglied existiert bereits. Daten in Tabelle user aktualisieren.
        foreach ($scan_data as $key => $data) {
            $update = (empty($update)) ? $key . "='" . $data . "'" : $update . ", " . $key . "='" . $data . "'";
        }

        $sql = "UPDATE " . $db_tb_user . " SET " . $update . " WHERE sitterlogin='" . $scan_data['sitterlogin'] . "'";
        $result = $db->db_query($sql)
            or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

        return 1;
    } else {
        // Das Mitglied existiert noch nicht, Daten in Tabelle user einfügen.
        echo "neues Mitglied: " . $scan_data["sitterlogin"] . "<br />";
        $scan_data['id'] = $scan_data['sitterlogin'];
        foreach ($scan_data as $key => $data) {
            $sql_key  = (empty($sql_key)) ? $key
                : $sql_key . ", " . $key;
            $sql_data = (empty($sql_data)) ? "'" . $data . "'"
                : $sql_data . ", '" . $data . "'";
        }
        $sql = "INSERT INTO " . $db_tb_user . " (" . $sql_key . ") VALUES (" . $sql_data . ")";
        $result = $db->db_query($sql)
            or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

        return 2;
    }
}