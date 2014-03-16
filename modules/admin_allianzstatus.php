<?php
/*****************************************************************************
 * admin_allianzstatus.php                                                   *
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

global $db, $db_tb_allianzstatus, $user_status;

if ($user_status != "admin" && $user_status != "hc") {
    die('Hacking attempt...');
}

//****************************************************************************

doc_title("Admin Allianzstatus");

if (getVar('editallianz')) {
    $sql = "SELECT * FROM " . $db_tb_allianzstatus . " WHERE name='" . $user_allianz . "'";
    $result = $db->db_query($sql);

    while ($row = $db->db_fetch_array($result)) {
        $temp1 = $row['id'] . '_allianz';
        $temp2 = $row['id'] . '_status';

        $row_allianz = getVar($temp1);
        $row_status  = getVar($temp2);

        if (empty($row_allianz)) {
            $sql = "DELETE FROM " . $db_tb_allianzstatus . " WHERE id='" . $row['id'] . "'";
            $result = $db->db_query($sql);
        } else {
            $sql = "UPDATE " . $db_tb_allianzstatus .
                " SET allianz='" . $row_allianz .
                "', status='" . $row_status .
                "' WHERE id = '" . $row['id'] . "'";
            $result_allianzedit = $db->db_query($sql);
        }
        $lastid = $row['id'];
    }

    $lastid = $row['id'];
    $temp1  = ($lastid + 1) . '_allianz';
    $temp2  = ($lastid + 1) . '_status';

    $last_allianz = getVar($temp1);
    $last_status  = getVar($temp2);

    if (!empty($last_allianz)) {
        $sql = "INSERT INTO " . $db_tb_allianzstatus . " (name, allianz, status)" .
            " VALUES ('" . $user_allianz . "','" . $last_allianz . "', '" . $last_status . "')";
        $result = $db->db_query($sql);
    }

    doc_message("Allianzstatus aktualisiert");
}

start_form("admin&uaction=allianzstatus");
start_table();
start_row("windowbg2", "style='width:20%;'");
echo "Allianz";
next_cell("windowbg2", "style='width:80%;'");
echo "Status";
end_row();


$sql = 'SELECT * FROM ' . $db_tb_allianzstatus . ' WHERE name="' . $user_allianz . '"';
$result = $db->db_query($sql);

while ($row = $db->db_fetch_array($result)) {
    if (!empty($row['status'])
        && !empty($config_allianzstatus[$row['status']])
    ) {
        $color = $config_allianzstatus[$row['status']];
    } else {
        $color = "#ffffff";
    }

    $lastid = $row['id'];

    start_row("windowbg1", "style='background-color: " . $color . "'");
    echo "<input type='text' name='" . $row['id'] . "_allianz' value='" . $row['allianz'] . "' style='width: 100px'>\n";
    next_cell("windowbg1", "style='background-color: " . $color . "'");
    echo "<input type='text' name='" . $row['id'] . "_status' value='" . $row['status'] . "' style='width: 100px'>\n";
    end_row();
}

$lastid = $row['id'];
$color  = "#C4F493";

start_row("windowbg1", "style='background-color: " . $color . "'");
echo "<input type='text' name='" . ($lastid + 1) . "_allianz' value='' style='width: 100px'>\n";
next_cell("windowbg1", "style='background-color: " . $color . "'");
echo "<input type='text' name='" . ($lastid + 1) . "_status' value='' style='width: 100px'>\n";
next_row("titlebg center", "", 2);
echo "<input type='hidden' name='editallianz' value='true'>" .
    "<input type='submit' value='speichern' name='B1' class='submit'>\n";
end_row();
end_table();
end_form();

echo "<br>\n";
start_table();
start_row_only();

foreach ($config_allianzstatus as $key => $value) {
    cell("", "style='width: 30; background-color: " . $value . ";'");
    next_cell("windowbg1", "style='width: 70;'");
    echo $key;
    end_cell();
}
end_row(false);
end_table();