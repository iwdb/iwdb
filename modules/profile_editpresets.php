<?php
/*****************************************************************************
 * profile_editpresets.php                                                   *
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

doc_title('Presets');

$delid = (int)getVar('delid');
if (!empty($delid)) {
    $sql = "SELECT fromuser, name FROM " . $db_tb_preset . " WHERE id LIKE '" . $delid . "'";
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query preset information.', '', __FILE__, __LINE__, $sql);
    $row = $db->db_fetch_array($result);

    if (($row['fromuser'] === $user_sitterlogin) OR ($user_status === "admin")) {
        $sql = "DELETE FROM " . $db_tb_preset . " WHERE id = '" . $delid . "'";
        $result = $db->db_query($sql)
            or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
        echo "<div class='system_notification'>Preset '" . $row['name'] . " von '" . $row['fromuser'] . "' gelöscht.</div>";
    }
}
?>
<br>
<table class="table_format" style="width: 80%;">
    <tr>
        <td class="windowbg2" style="width:20%;">
            Preset
        </td>
        <td class="windowbg2" style="width:20%;">
            Username
        </td>
        <td class="windowbg2" style="width:60%;">
            &nbsp;
        </td>
    </tr>
    <?php
    // Ausgabe der Presets und Löschlink //
    if ($user_status === "admin") { //admin kann die globalen Presets löschen
        $sql = "SELECT id, name, fromuser FROM " . $db_tb_preset . " WHERE (fromuser = '" . $id . "' OR fromuser = '') ORDER BY fromuser, name";
    } else {
        $sql = "SELECT id, name, fromuser FROM " . $db_tb_preset . " WHERE fromuser = '" . $id . "'";
    }
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query preset information.', '', __FILE__, __LINE__, $sql);

    while ($row = $db->db_fetch_array($result)) {
        ?>
        <tr>
            <td class="windowbg1">
                <?php echo $row['name'];?>
            </td>
            <td class="windowbg1">
                <?php echo (empty($row['fromuser'])) ? "<b>global</b>" : $row['fromuser'];?>
            </td>
            <td class="windowbg1">
                <a href="index.php?action=profile&uaction=editpresets&delid=<?php echo $row['id'];?>&sitterlogin=<?php echo urlencode($sitterlogin);?>">löschen</a>
            </td>
        </tr>
    <?php
    }
    ?>
</table>
<br>