<?php
/*****************************************************************************
 * sitterhistory.php                                                         *
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
//genutzte globale Variablen
global $user_sitterlogin, $user_status, $db, $db_tb_sitterlog;

if ($user_adminsitten == SITTEN_DISABLED) {
    die('Hacking attempt...');
}

$limit = filter_int(getVar('limit'), 20, 1, 250);
$selecteduser = validAccname(getVar('selecteduser'));
if (empty($selecteduser)) {
    $selecteduser = $user_sitterlogin;
}

doc_title("Sitterhistorie von " . $selecteduser);

start_form("sitterhistory");
echo "<input type='hidden' name='selecteduser' value='" . $selecteduser . "'>\n";
echo "maximal: <input type='number' name='limit' value='" . $limit . "' min='1' max='250' style='width: 5em;'> Einträge\n";
echo "<input type='submit' value='anzeigen' name='B1' class='submit'>\n";
end_form();
?>
<br>
<table class="tablesorter" style="width: 90%;">
    <thead>
	<tr class="titlebg center">
        <th class='sorter-false' colspan="4">
            <b>Was andere bei <?php echo $selecteduser;?> gemacht haben:</b>
        </th>
    </tr>
    <tr class="titlebg left">
        <th style="width:15%;">
            <b>Username</b>
        </th>
        <th class="center" style="width:20%;">
            <b>Zeit</b>
        </th>
        <th style="width:65%;">
            <b>Auftrag</b>
        </th>
    </tr>
	</thead>
	<tbody>
    <?php
    // Auftraege durchgehen //
    $sql = "SELECT `fromuser`, `date`, `action` FROM `{$db_tb_sitterlog}` WHERE `sitterlogin` = '" . $selecteduser . "' ORDER BY `date` DESC LIMIT " . $limit;
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

    while ($row = $db->db_fetch_array($result)) {
        ?>
        <tr class="windowbg1 left">
            <td>
                <?php
                if ($user_status == "admin") {
                    echo "<a href='index.php?action=profile&sitterlogin=" . urlencode($row['fromuser']) . "'>" . $row['fromuser'] . "</a>";
                } else {
                    echo $row['fromuser'];
                }
                ?>
            </td>
            <td>
                <?php echo strftime(CONFIG_DATETIMEFORMAT, $row['date']);?>
            </td>
            <td>
                <?php echo convert_bbcode($row['action']);?>
            </td>
        </tr>
    <?php
    }
    ?>
</tbody>
</table>
<br>
<br>
<table class="tablesorter" style="width: 90%;">
    <thead>
	<tr class="titlebg center">
        <th class='sorter-false' colspan="4">
            <b>Was <?php echo $selecteduser;?> bei anderen gemacht hat</b>
        </th>
    </tr>
    <tr class="titlebg left">
        <th style="width:15%;">
            <b>Username</b>
        </th>
        <th class="center" style="width:20%;">
            <b>Zeit</b>
        </th>
        <th style="width:65%;">
            <b>Auftrag</b>
        </th>
    </tr>
	</thead>
	<tbody>
    <?php
    // Aufträge durchgehen //
    $sql = "SELECT `sitterlogin`, `date`, `action` FROM `{$db_tb_sitterlog}` WHERE `fromuser` = '" . $selecteduser . "' ORDER BY `date` DESC LIMIT " . $limit;
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
    while ($row = $db->db_fetch_array($result)) {
        ?>
        <tr class="windowbg1 left">
            <td>
                <?php
                if ($user_status === "admin") {
                    echo "<a href='index.php?action=profile&sitterlogin=" . urlencode($row['sitterlogin']) . "'>" . $row['sitterlogin'] . "</a>";
                } else {
                    echo $row['sitterlogin'];
                }
                ?>
            </td>
            <td>
                <?php echo strftime(CONFIG_DATETIMEFORMAT, $row['date']);?>
            </td>
            <td>
                <?php echo convert_bbcode($row['action']);?>
            </td>
        </tr>
    <?php
    }
    ?>
</tbody>
</table>
<br>