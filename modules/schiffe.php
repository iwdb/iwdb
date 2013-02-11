<?php
/*****************************************************************************
 * schiffe.php                                                               *
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
 *                                                                           *
 * Entwicklerforum/Repo:                                                     *
 *                                                                           *
 *        https://handels-gilde.org/?www/forum/index.php;board=1099.0        *
 *                   https://github.com/iwdb/iwdb                            *
 *                                                                           *
 *****************************************************************************/

//direktes Aufrufen verhindern
if (!defined('IRA')) {
    header('HTTP/1.1 403 forbidden');
    exit;
}

//****************************************************************************

$users   = array();
$order   = getVar('order');
$ordered = getVar('ordered');

$order   = (empty($order)) ? "" : "WHERE schiff='" . $order . "'";
$ordered = (empty($ordered)) ? "asc" : $ordered;

if (!empty($order)) {
    $sql = "SELECT user FROM " . $db_tb_schiffe .
        " " . $order . " GROUP BY user ORDER BY anzahl DESC";
    $result_schiffe = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

    while ($row_schiffe = $db->db_fetch_array($result_schiffe)) {
        $users[] = $row_schiffe['user'];
    }
}

$sql = "SELECT user FROM " . $db_tb_schiffe .
    "," . $db_tb_user . " WHERE " . $db_tb_schiffe . ".user=" . $db_tb_user . ".id AND " . $db_tb_user . ".allianz='" . $user_allianz . "' GROUP BY user ORDER BY user DESC";
$result_schiffe = $db->db_query($sql)
    or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

while ($row_schiffe = $db->db_fetch_array($result_schiffe)) {
    if (!in_array($row_schiffe['user'], $users, true)) {
        $users[] = $row_schiffe['user'];
    }
}

if ($ordered == "asc") {
    krsort($users);
}

$lastscans = array();
$scancolor = array();

foreach ($users as $userx) {
    $sql = "SELECT lastshipscan FROM " . $db_tb_user .
        " WHERE sitterlogin='" . $userx . "'";
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

    if ($row = $db->db_fetch_array($result)) {
        if (empty($row['lastshipscan'])) {
            $tmp   = "--";
            $color = "white";
        } else {
            $color = scanAge($row['lastshipscan']);

            $tmp = strftime("(%d.%m.%y %H:%M:%S)", $row['lastshipscan']);
        }
        $lastscans[$userx] = $tmp;
        $scancolor[$userx] = $color;
    }
}

doc_title('Schiffsübersicht');

$sql = "SELECT typ FROM " . $db_tb_schiffstyp . " GROUP BY typ ORDER BY typ asc";
$result = $db->db_query($sql)
    or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

while ($row = $db->db_fetch_array($result)) {
    $schiffe = array();

    $sql = "SELECT id, abk FROM " . $db_tb_schiffstyp .
        " WHERE typ='" . $row['typ'] . "' ORDER BY schiff";
    $result_schiffe = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

    $schiffsanz = $db->db_num_rows($result_schiffe);

    echo "<table class='table_format' style='width: 90%;'>\n";
    echo " <tr>\n";
    echo "  <td class='titlebg center' colspan='" . ($schiffsanz + 1) . "'>\n";
    echo "   <b>" . ((empty($row['typ'])) ? "Sonstige" : $row['typ']) . "</b>\n";
    echo "  </td>\n";
    echo " </tr>\n";
    echo "\n";
    echo " <tr>\n";
    echo "  <td class='windowbg2' valign='bottom' style='width:15%'>\n";
    echo "   <a href='index.php?action=schiffe&ordered=asc&sid=" . $sid . "'>" .
        "<img src='bilder/asc.gif'></a>" .
        "  Username  " .
        "<a href='index.php?action=schiffe&ordered=desc&sid=" . $sid . "'>" .
        "<img src='bilder/desc.gif'></a>\n";
    echo "  </td>\n";

    while ($row_schiffe = $db->db_fetch_array($result_schiffe)) {
        $schiffe[] = $row_schiffe['id'];

        echo "  <td class='windowbg2 center' " .
            "valign='bottom' style='width:" . (85 / $schiffsanz) . "%'>\n";
        echo "    <a href='index.php?action=schiffe&order=" . $row_schiffe['id'] .
            "&ordered=asc&sid=" . $sid . "'><img src='bilder/asc.gif'></a>\n";
        echo $row_schiffe['abk'];
        echo "    <a href='index.php?action=schiffe&order=" . $row_schiffe['id'] .
            "&ordered=desc&sid=" . $sid . "'><img src='bilder/desc.gif'></a>\n";
        echo "  </td>\n";
    }
    echo " </tr>\n";

    // Gesamtanzahl
    echo " <tr>\n";
    echo "  <td class='windowbg2'>Gesamtzahl</td>\n";
    foreach ($schiffe as $data) {
        $sql = "SELECT SUM(anzahl) AS gesamtanzahl FROM " . $db_tb_schiffe .
            "," . $db_tb_user . " WHERE " . $db_tb_schiffe . ".user=" . $db_tb_user . ".id AND " . $db_tb_user . ".allianz='" . $user_allianz . "' AND schiff='" . $data . "'";
        $result_anzahl = $db->db_query($sql)
            or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

        $row_anzahl = $db->db_fetch_array($result_anzahl);
        echo "    <td class='windowbg2 right'>" . $row_anzahl['gesamtanzahl'] . "</td>\n";
    }
    echo " </tr>\n";

    // Schiffsauflistung
    foreach ($users as $userx) {
        // Schiffe zählen
        $shipcount = '';
        $shipprint = '';
        foreach ($schiffe as $data) {
            $sql = "SELECT anzahl FROM " . $db_tb_schiffe .
                " WHERE ( user LIKE '" . $userx . "' AND schiff='" . $data . "')";
            $result_anzahl = $db->db_query($sql)
                or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
            $row_anzahl = $db->db_fetch_array($result_anzahl);
            $shipprint .= "   <td class='windowbg1 right'>" .
                $row_anzahl['anzahl'] . "</td>\n";
            $shipcount .= $row_anzahl['anzahl'];
        }
        // end Schiffe zählen

        // Nur wenn Schiffe ($shipcount) vorhanden dann ausgeben
        if (!empty($shipcount)) {
            echo "  <tr>\n";
            echo "    <td class='windowbg1' style='background-color:" . $scancolor[$userx] . "'>\n";

            if ($user_status == "admin") {
                echo "<a href='index.php?action=profile&sitterlogin=" . urlencode($userx) .
                    "&sid=" . $sid . "'>" . $userx . "</a>";
            } else {
                echo $userx;
            }
            echo "<br>" . $lastscans[$userx] . "\n";
            echo "    </td>\n";
            echo $shipprint;
            echo "  </tr>\n";
        }
        // end $shipcount
    }
    // end Schiffaufslistung

    echo "</table>\n";
    echo "<br>\n";
}