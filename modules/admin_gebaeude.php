<?php
/*****************************************************************************
 * admin_gebaeude.php                                                        *
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

if ($user_status != "admin") {
    die('Hacking attempt...');
}

//****************************************************************************

function dauer($zeit)
{
    $return['d'] = floor($zeit / DAY);
    $return['h'] = floor(($zeit - $return['d'] * DAY) / HOUR);
    $return['m'] = ($zeit - $return['d'] * DAY - $return['h'] * HOUR) / MINUTE;

    return $return;
}

//******************************************************************************

doc_title('Admin Gebäude');

$gebpictures     = array();
$gebpictures[''] = "keins";

$handle = opendir(GEBAEUDE_BILDER_PATH);
while (false !== ($datei = readdir($handle))) {
    if (strpos($datei, ".jpg") !== false) {
        $id               = str_replace(".jpg", "", $datei);
        $gebpictures[$id] = $id;
    }
}
closedir($handle);
ksort($gebpictures);

$editgebaeude = (bool)getVar('editgebaeude');
$newgebaeude  = (bool)getVar('newgebaeude');

$sql = "SELECT id FROM " . $db_tb_gebaeude . " ORDER BY id ASC";
$result_gebaeude = $db->db_query($sql)
    or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

while ($row_gebaeude = $db->db_fetch_array($result_gebaeude)) {
    if ($editgebaeude) {

        $geb_name = getVar(($row_gebaeude['id'] . '_name'));
        if (!empty($geb_name)) {

            $geb_cat   = $db->escape(getVar($row_gebaeude['id'] . '_category'));
            $geb_idcat = (int)getVar($row_gebaeude['id'] . '_idcat');
            $geb_inact = (int)getVar($row_gebaeude['id'] . '_inactive');
            $geb_bild  = $db->escape(getVar($row_gebaeude['id'] . '_bild'));
            $id_iw     = (int)getVar($row_gebaeude['id'] . '_id_iw');

            $dauer_d = (int)getVar($row_gebaeude['id'] . '_dauer_d');
            $dauer_h = (int)getVar($row_gebaeude['id'] . '_dauer_h');
            $dauer_m = (int)getVar($row_gebaeude['id'] . '_dauer_m');

            $deletegebaeude = (bool)getVar(($row_gebaeude['id'] . '_delete'));

            $dauer = ($dauer_d * DAY) + ($dauer_h * HOUR) + ($dauer_m * MINUTE);

            if ($deletegebaeude) {

                $sql = "DELETE FROM " . $db_tb_gebaeude .
                    " WHERE name='" . $geb_name .
                    "'AND category='" . $geb_cat .
                    "'AND idcat='" . $geb_idcat .
                    "'AND id = '" . $row_gebaeude['id'] . "'";
                echo "<div class='system_notification'>Gebäude $geb_name gelöscht</div>";

            } else {

                $sql = "UPDATE " . $db_tb_gebaeude .
                    " SET name='" . $geb_name .
                    "', category='" . $geb_cat .
                    "', idcat='" . $geb_idcat .
                    "', inactive='" . $geb_inact .
                    "', dauer='" . $dauer .
                    "', bild='" . $geb_bild .
                    "', id_iw='" . $id_iw .
                    "' WHERE id = '" . $row_gebaeude['id'] . "'";

            }

            $result_gebaeudeedit = $db->db_query($sql)
                or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

        }
    }

    $lastid = $row_gebaeude['id'];

}

$lastid_name = getVar((($lastid + 1) . '_name'));

if ((!empty($lastid_name)) AND $newgebaeude) {
    $geb_name  = $db->escape(getVar((($lastid + 1) . '_name')));
    $geb_cat   = $db->escape(getVar((($lastid + 1) . '_category')));
    $geb_idcat = (int)getVar((($lastid + 1) . '_idcat'));
    $geb_inact = (int)getVar((($lastid + 1) . '_inactive'));
    $geb_bild  = $db->escape(getVar((($lastid + 1) . '_bild')));
    $id_iw     = (int)getVar((($lastid + 1) . '_id_iw'));

    $dauer_d = (int)getVar((($lastid + 1) . '_dauer_d'));
    $dauer_h = (int)getVar((($lastid + 1) . '_dauer_h'));
    $dauer_m = (int)getVar((($lastid + 1) . '_dauer_m'));

    $dauer = ($dauer_d * DAY) + ($dauer_h * HOUR) + ($dauer_m * MINUTE);

    $data = array(
        'name' => $geb_name,
        'category' => $geb_cat,
        'idcat' => $geb_idcat,
        'inactive' => $geb_inact,
        'dauer' => $dauer,
        'bild' => $geb_bild,
        'id_iw' => $id_iw
    );

    $result = $db->db_insert($db_tb_gebaeude, $data)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__);

    $lastid++;

    echo "<div class='system_notification'>Gebäude {$lastid_name} hinzugefügt.</div>";
}

if ($editgebaeude) {
    echo "<div class='system_notification'>Gebäude aktualisiert.</div>";
}

echo "<br>\n";
echo "<form method='POST' action='index.php?action=admin&uaction=gebaeude' enctype='multipart/form-data'>\n";
echo "<table class='table_format' style='width: 95%;'>\n";
echo "<thead>\n";
echo " <tr>\n";
echo "  <th class='windowbg2'>Ausblenden?</td>\n";
echo "  <th class='windowbg2'>Name</td>\n";
echo "  <th class='windowbg2'>Kategorie</td>\n";
echo "  <th class='windowbg2'>Reihenfolge</td>\n";
echo "  <th class='windowbg2' style='width:100px;'>Baudauer</td>\n";
echo "  <th class='windowbg2'>IW-ID</td>\n";
echo "  <th class='windowbg2' style='width: 150px'>Bild</td>\n";
echo " </tr>\n";
echo "</thead>\n";

echo "<tbody>\n";
echo " <tr>\n";
echo "  <td class='windowbg1 center'>\n";
echo "   <input type='checkbox' name='" . ($lastid + 1) . "_inactive' value='1'>\n";
echo "  </td>\n";
echo "  <td class='windowbg1'>\n";
echo "   <input type='text' name='" . ($lastid + 1) . "_name' required placeholder='Gebäudename' maxlength='100' size='33'>\n";
echo "  </td>\n";
echo "  <td class='windowbg1'>\n";
echo "   <input type='text' name='" . ($lastid + 1) . "_category' placeholder='Gebäudekategorie' maxlength='50' size='22'>\n";
echo "  </td>\n";
echo "  <td class='windowbg1 center'>\n";
echo "   <input type='number' class='right' name='" . ($lastid + 1) . "_idcat' value='0' min='0' maxlength='5' style='width:50px;'>\n";
echo "  </td>\n";
echo "  <td class='windowbg1 left'>\n";
echo "    <input type='number' name='" . ($lastid + 1) . "_dauer_d' value='0' min='0' max='10' maxlength='2' style='width:50px;'> Tage<br>\n";
echo "    <input type='number' name='" . ($lastid + 1) . "_dauer_h' value='0' min='0' max='60' maxlength='2' style='width:50px;'> h<br>\n";
echo "    <input type='number' name='" . ($lastid + 1) . "_dauer_m' value='0' min='0' max='60' maxlength='2' style='width:50px;'> min\n";
echo "  </td>\n";
echo "  <td class='windowbg1 center'>\n";
echo "   <input type='number' name='" . ($lastid + 1) . "_id_iw' value='0' min='0' size='5' maxlength='5' style='width:50px;'>\n";
echo "  </td>\n";
echo "  <td class='windowbg1'>\n";
echo "   <select name='" . ($lastid + 1) . "_bild'>\n";

foreach ($gebpictures as $key => $data) {
    echo "     <option value='" . $key . "'>" . $data . "</option>\n";
}

echo "   </select>\n";
echo "  </td>\n";
echo " </tr>\n";
echo "</tbody>\n";

echo "<tfoot>\n";
echo " <tr>\n";
echo "  <td class='windowbg2 center' colspan='7'>\n";
echo "   <input type='submit' value='hinzufügen' name='newgebaeude' class='submit'>\n";
echo "  </td>\n";
echo " </tr>\n";
echo " </tfoot>\n";
echo "</table>\n";
echo "</form>\n";
echo "<br>\n";

$sql = "SELECT DISTINCT category FROM " . $db_tb_gebaeude .
    " ORDER BY category asc";
$result = $db->db_query($sql)
    or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

while ($row = $db->db_fetch_array($result)) {
    echo "<form method='POST' action='index.php?action=admin&uaction=gebaeude' enctype='multipart/form-data'>\n";
    echo "<table class='table_format' style='width: 95%;'>\n";
    echo "<thead>\n";
    echo " <tr>\n";
    echo "  <td class='titlebg center' colspan='7'>\n";
    echo "    <b>" . (empty($row['category']) ? "Sonstige" : $row['category']) . "</b>\n";
    echo "  </td>\n";
    echo " </tr>\n";
    echo " <tr>\n";
    echo "  <th class='windowbg2'></td>\n";
    echo "  <th class='windowbg2'>Name</td>\n";
    echo "  <th class='windowbg2'>Kategorie</td>\n";
    echo "  <th class='windowbg2'>Reihenfolge</td>\n";
    echo "  <th class='windowbg2' style='width:100px;'>Baudauer</td>\n";
    echo "  <th class='windowbg2'>IW-ID</td>\n";
    echo "  <th class='windowbg2' style='width: 150px'>Bild</td>\n";
    echo " </tr>\n";
    echo "</thead>\n";

    $sql = "SELECT * FROM " . $db_tb_gebaeude . " WHERE category='" . $row['category'] . "' ORDER BY idcat ASC";
    $result_gebaeude = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

    while ($row_gebaeude = $db->db_fetch_array($result_gebaeude)) {
        $dauer    = dauer($row_gebaeude['dauer']);

        if (!empty($row_gebaeude['bild'])) {
            $bild_url = GEBAEUDE_BILDER_PATH . $row_gebaeude['bild'] . ".jpg";
        } else {
            $bild_url = GEBAEUDE_BILDER_PATH . "blank.jpg";
        }

        echo "<tbody>\n";
        echo " <tr>\n";
        echo "  <td class='windowbg1 center'>\n";
        echo "  Ausblenden:  <input type='checkbox' name='" . $row_gebaeude['id'] . "_inactive' value='1'" . (($row_gebaeude['inactive']) ? " checked" : "") . ">\n";
        echo "  Löschen:  <input type='checkbox' name='" . $row_gebaeude['id'] . "_delete' onclick='return confirmlink(this, 'Möchten sie dieses Gebäude wirklich löschen?')' value='1'>\n";
        echo "  </td>\n";

        echo "  <td class='windowbg1'>\n";
        echo "    <input type='text' name='" . $row_gebaeude['id'] . "_name' value='" . $row_gebaeude['name'] . "' required maxlength='100' size='33'>\n";
        echo "  </td>\n";

        echo "  <td class='windowbg1'>\n";
        echo "   <input type='text' name='" . $row_gebaeude['id'] . "_category' value='" . $row_gebaeude['category'] . "' maxlength='50' size='22'>\n";
        echo "  </td>\n";

        echo "  <td class='windowbg1 center'>\n";
        echo "    <input type='number' name='" . $row_gebaeude['id'] . "_idcat' value='" . $row_gebaeude['idcat'] . "' min='0' maxlength='5' style='width:50px;'>\n";
        echo "  </td>\n";

        echo "  <td class='windowbg1 left'>\n";
        echo "    <input type='number' name='" . $row_gebaeude['id'] . "_dauer_d' value='" . $dauer['d'] . "' min='0' maxlength='2' style='width:50px;'> Tage<br>\n";
        echo "    <input type='number' name='" . $row_gebaeude['id'] . "_dauer_h' value='" . $dauer['h'] . "' min='0' maxlength='2' style='width:50px;'> h\n";
        echo "    <input type='number' name='" . $row_gebaeude['id'] . "_dauer_m' value='" . $dauer['m'] . "' min='0' maxlength='2' style='width:50px;'> min\n";
        echo "  </td>\n";

        echo "  <td class='windowbg1 center'>\n";
        echo "    <input type='number' name='" . $row_gebaeude['id'] . "_id_iw' value='" . $row_gebaeude['id_iw'] . "' min='0' maxlength='5' style='width:50px;'>\n";
        echo "  </td>\n";

        echo "  <td class='windowbg1 center'>\n";
        echo "    <img src='" . $bild_url . "' width='50' height='50'>\n";
        echo "    <select name='" . $row_gebaeude['id'] . "_bild'>\n";
        foreach ($gebpictures as $key => $data) {
            echo "      <option value='" . $key . "'";
            if ($row_gebaeude['bild'] == $key) {
                echo  " selected";
            }
            echo ">" . $data . "</option>\n";
        }
        echo "    </select>\n";
        echo "  </td>\n";
        echo " </tr>\n";
    }
    echo "</tbody>\n";

    echo "<tfoot>\n";
    echo " <tr>\n";
    echo "  <td class='windowbg2 center' colspan='7'>\n";
    echo "   <input type='submit' value='ändern' name='editgebaeude' class='submit'>\n";
    echo "  </td>\n";
    echo " </tr>\n";
    echo " </tfoot>\n";
    echo "</table>\n";
    echo "</form>\n";
    echo "<br>\n";
}

echo "<br>\n";