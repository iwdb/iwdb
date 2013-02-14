<?php
/*****************************************************************************
 * admin_schiffstypen.php                                                    *
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

if ($user_status != "admin") {
    die('Hacking attempt...');
}

doc_title("Admin Schiffstypen");

$editschiffe = getVar('editschiffe');
if (!empty($editschiffe)) {
    doc_message("Schiffstypen aktualisiert.");
}

echo "<br>\n";
start_form("admin&uaction=schiffstypen");

if (!empty($editschiffe)) {
    $sql = "SELECT * FROM " . $db_tb_schiffstyp;
    $result_schiffe = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

    while ($row_schiffe = $db->db_fetch_array($result_schiffe)) {
        $schiff_abk        = getVar(($row_schiffe['id'] . '_abk'));
        $schiff_typ        = getVar(($row_schiffe['id'] . '_typ'));
        $schiff_bestellbar = getVar(($row_schiffe['id'] . '_bestellbar'));
        if (empty($schiff_bestellbar)) {
            $schiff_bestellbar = '0';
        }

        $sql = "UPDATE " . $db_tb_schiffstyp .
            " SET abk='" . $schiff_abk .
            "', typ='" . $schiff_typ .
            "', bestellbar='" . $schiff_bestellbar .
            "' WHERE id = '" . $row_schiffe['id'] . "'";
        $result_schiffeedit = $db->db_query($sql)
            or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
    }
}

$sql = "SELECT DISTINCT typ FROM " . $db_tb_schiffstyp .
    " ORDER BY typ asc";
$result = $db->db_query($sql)
    or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

while ($row = $db->db_fetch_array($result)) {
    start_table(95);
    start_row("titlebg center", "", 4);
    echo "<b>" . (empty($row['typ']) ? "Sonstige" : $row['typ']) . "</b>";
    next_row("windowbg2", "style='width:40%;'");
    echo "Schiff";
    next_cell("windowbg2", "style='width:30%;'");
    echo "Abkürzung";
    next_cell("windowbg2", "style='width:30%;'");
    echo "Typ";
    next_cell("windowbg2");
    echo "bestellbar";
    end_row();

    $sql = "SELECT * FROM " . $db_tb_schiffstyp .
        " WHERE typ='" . $row['typ'] . "' ORDER BY schiff";
    $result_schiffe = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
    while ($row_schiffe = $db->db_fetch_array($result_schiffe)) {
        start_row("windowbg1");
        echo $row_schiffe['schiff'];
        next_cell("windowbg1");
        echo "<input type='text' name='" .
            $row_schiffe['id'] . "_abk' value='" . html_entity_decode($row_schiffe['abk'], ENT_QUOTES, 'UTF-8') .
            "' style='width: 99%;'>";
        next_cell("windowbg1");
        echo "<input type='text' name='" .
            $row_schiffe['id'] . "_typ' value='" . html_entity_decode($row_schiffe['typ'], ENT_QUOTES, 'UTF-8') .
            "' style='width: 99%;'>";
        next_cell("windowbg1");
        echo "<input type='checkbox' name='" .
            $row_schiffe['id'] . "_bestellbar' value='1'";
        if ($row_schiffe['bestellbar'] == "1") //echo " checked";
        {
            echo 'checked="checked"';
        }
        echo "'>";
        end_row();
        ;
    }

    end_table();
    echo "<br>\n";
}

start_table();
start_row("titlebg center");
echo "<input type='hidden' name='editschiffe' value='true'>" .
    "<input type='submit' value='speichern' name='B1' class='submit'>\n";
end_row();
end_table();
end_form();

echo "<br>\n";

?>