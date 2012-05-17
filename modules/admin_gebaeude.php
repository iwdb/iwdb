<?php
/*****************************************************************************/
/* admin_gebaeude.php                                                        */
/*****************************************************************************/
/* Iw DB: Icewars geoscan and sitter database                                */
/* Open-Source Project started by Robert Riess (robert@riess.net)            */
/* Software Version: Iw DB 1.00                                              */
/* ========================================================================= */
/* Software Distributed by:    http://lauscher.riess.net/iwdb/               */
/* Support, News, Updates at:  http://lauscher.riess.net/iwdb/               */
/* ========================================================================= */
/* Copyright (c) 2004 Robert Riess - All Rights Reserved                     */
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
//ToDo: diekte Verwendung von mysql_real_escape_string ist vorübergehend bis Fertigstellung der Umstellung auf mysqli prepared statments (und übernommener Filterfunktionen)

// -> Abfrage ob dieses Modul über die index.php aufgerufen wurde.
//    Kann unberechtigte Systemzugriffe verhindern.
if (basename($_SERVER['PHP_SELF']) != "index.php") {
    exit("Hacking attempt...!!");
}

if ( $user_status != "admin" ) {
    die('Hacking attempt...');
}

function dauer($zeit)
{
    global $DAYS, $HOURS, $MINUTES;

    $return['d'] = floor($zeit / $DAYS);
    $return['h'] = floor(($zeit - $return['d'] * $DAYS) / $HOURS);
    $return['m'] = ($zeit - $return['d'] * $DAYS - $return['h'] * $HOURS) / $MINUTES;
    return $return;
}

//******************************************************************************

echo "<div class='doc_title'>Admin Gebäude</div>\n";

$gebpictures = array();
$gebpictures[''] = "keins";

$handle = opendir('bilder/gebs/');
while (false !== ($datei = readdir($handle)))
{
    if (strpos($datei, ".jpg") !== FALSE)
    {
        $id = str_replace(".jpg", "", $datei);
        $gebpictures[$id] = $id;
    }
}
closedir($handle);
ksort($gebpictures);

$editgebaeude = getVar('editgebaude');
$editgebaeude = getVar('newgebaude');

$sql = "SELECT id FROM " . $db_tb_gebaeude . " ORDER BY id ASC";
$result_gebaeude = $db->db_query($sql)
    or error(GENERAL_ERROR,
    'Could not query config information.', '',
    __FILE__, __LINE__, $sql);

while($row_gebaeude = $db->db_fetch_array($result_gebaeude)) {
    if ( ! empty($editgebaeude) )	{

        $temp_name = getVar(($row_gebaeude['id'] . '_name'));
        if (!empty($temp_name)) {

            $geb_name   = mysql_real_escape_string(getVar($row_gebaeude['id'] . '_name',true));
            $geb_cat    = mysql_real_escape_string(getVar($row_gebaeude['id'] . '_category', true));
            $geb_idcat  = (int)getVar($row_gebaeude['id'] . '_idcat');
            $geb_inact  = getVar($row_gebaeude['id'] . '_inactive');
            $geb_bild   = mysql_real_escape_string(getVar($row_gebaeude['id'] . '_bild'));
            $id_iw      = (int)getVar($row_gebaeude['id'] . '_id_iw');

            $dauer_d   = (int)getVar($row_gebaeude['id'] . '_dauer_d');
            $dauer_h   = (int)getVar($row_gebaeude['id'] . '_dauer_h');
            $dauer_m   = (int)getVar($row_gebaeude['id'] . '_dauer_m');

            $delete    = getVar(($row_gebaeude['id'] . '_delete'));

            $dauer     = ($dauer_d * $DAYS) + ($dauer_h * $HOURS) + ($dauer_m * $MINUTES);

            if (empty($delete)) {

                $sql = "UPDATE " . $db_tb_gebaeude .
                    " SET name='" . $geb_name.
                    "', category='" . $geb_cat .
                    "', idcat='" . $geb_idcat .
                    "', inactive='" . $geb_inact .
                    "', dauer='" . $dauer .
                    "', bild='" . $geb_bild .
                    "', id_iw='" . $id_iw .
                    "' WHERE id = '" . $row_gebaeude['id'] . "'";

            } else {

                $sql = "DELETE FROM " . $db_tb_gebaeude .
                    " WHERE name='" . $geb_name.
                    "'AND category='" . $geb_cat .
                    "'AND idcat='" . $geb_idcat .
                    "'AND id = '" . $row_gebaeude['id'] . "'";
                echo "<div class='system_notification'>Gebäude $geb_name gelöscht</div>";

            }

            $result_gebaeudeedit = $db->db_query($sql)
                or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

        }
    }

    $lastid = $row_gebaeude['id'];

}

$lastid_name  = getVar((($lastid + 1) . '_name'));

if((!empty($lastid_name)) && (empty($editgebaeude))) {
    $geb_name  = mysql_real_escape_string(getVar((($lastid + 1) . '_name'), true));
    $geb_cat   = mysql_real_escape_string(getVar((($lastid + 1) . '_category'), true));
    $geb_idcat = (int)getVar((($lastid + 1) . '_idcat'));
    $geb_inact = getVar((($lastid + 1) . '_inactive'));
    $geb_bild  = mysql_real_escape_string(getVar((($lastid + 1) . '_bild')));
    $id_iw     = (int)getVar((($lastid + 1) . '_id_iw'));

    $dauer_d   = (int)getVar((($lastid + 1) . '_dauer_d'));
    $dauer_h   = (int)getVar((($lastid + 1) . '_dauer_h'));
    $dauer_m   = (int)getVar((($lastid + 1) . '_dauer_m'));

    $dauer     = ($dauer_d * $DAYS) + ($dauer_h * $HOURS) + ($dauer_m * $MINUTES);
    $sql = "INSERT INTO " . $db_tb_gebaeude .
        " (name, category, idcat, inactive, dauer, bild, id_iw) " .
        " VALUES ('" . $geb_name .
        "', '" . $geb_cat .
        "', '" . $geb_idcat .
        "', '" . $geb_inact .
        "', '" . $dauer .
        "', '" . $geb_bild .
        "', '" . $id_iw . "')";
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR,
        'Could not query config information.', '',
        __FILE__, __LINE__, $sql);

    $lastid++;

    echo "<div class='system_notification'>Gebäude $lastid_name hinzugefügt.</div>";
}

if($editgebaeude) {
    echo "<div class='system_notification'>Gebäude aktualisiert.</div>";
}

echo "<br>\n";
echo "<form method='POST' action='index.php?action=admin&uaction=gebaeude&sid=" . $sid . "' enctype='multipart/form-data'>\n";
echo "<table border='0' cellpadding='4' cellspacing='1' class='bordercolor' style='width: 90%;'>\n";
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
echo "  <td class='windowbg1' align='center'>\n";
echo "   <input type='checkbox' name='" . ($lastid + 1) . "_inactive' value='1'>\n";
echo "  </td>\n";
echo "  <td class='windowbg1'>\n";
echo "   <input type='text' name='" . ($lastid + 1) . "_name' required placeholder='Gebäudename' maxlength='100' size='33'>\n";
echo "  </td>\n";
echo "  <td class='windowbg1'>\n";
echo "   <input type='text' name='" . ($lastid + 1) . "_category' placeholder='Gebäudekategorie' maxlength='50' size='22'>\n";
echo "  </td>\n";
echo "  <td class='windowbg1' style='text-align:center !important'>\n";
echo "   <input type='number' name='" . ($lastid + 1) . "_idcat' value='0' min='0' maxlength='5' style='width:50px; text-align:right;'>\n";
echo "  </td>\n";
echo "  <td class='windowbg1' style='text-align:left !important'>\n";
echo "    <input type='number' name='" . ($lastid + 1) . "_dauer_d' value='0' min='0' max='10' maxlength='2' style='width:50px; text-align:right;'> Tage<br>\n";
echo "    <input type='number' name='" . ($lastid + 1) . "_dauer_h' value='0' min='0' max='60' maxlength='2' style='width:50px; text-align:right;'> h<br>\n";
echo "    <input type='number' name='" . ($lastid + 1) . "_dauer_m' value='0' min='0' max='60' maxlength='2' style='width:50px; text-align:right;'> min\n";
echo "  </td>\n";
echo "  <td class='windowbg1' style='text-align:center !important'>\n";
echo "   <input type='number' name='" . ($lastid + 1) . "_id_iw' value='0' min='0' size='5' maxlength='5' style='width:50px; text-align:right;'>\n";
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
echo "  <td class='windowbg2' colspan='7' style='text-align:center !important'>\n";
echo "   <input type='submit' value='speichern' name='B2' class='submit'>\n";
echo "  </td>\n";
echo " </tr>\n";
echo " </tfoot>\n";
echo "</table>\n";
echo "</form>\n";
echo "<br>\n";

$sql = "SELECT DISTINCT category FROM " . $db_tb_gebaeude .
    " ORDER BY category asc";
$result = $db->db_query($sql)
    or error(GENERAL_ERROR,
    'Could not query config information.', '',
    __FILE__, __LINE__, $sql);

while($row = $db->db_fetch_array($result))
{
    echo "<form method='POST' action='index.php?action=admin&uaction=gebaeude&sid=" . $sid . "' enctype='multipart/form-data'>\n";
    echo "<table border='0' cellpadding='4' cellspacing='1' class='bordercolor' style='width: 90%;'>\n";
    echo "<thead>\n";
    echo " <tr>\n";
    echo "  <td class='titlebg' style='text-align:center !important' colspan='7'>\n";
    echo "    <b>" . ( empty($row['category']) ? "Sonstige" : $row['category'] ). "</b>\n";
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

    $sql = "SELECT * FROM " . $db_tb_gebaeude .
        " WHERE category='" . $row['category'] .
        "' ORDER BY idcat ASC";
    $result_gebaeude = $db->db_query($sql)
        or error(GENERAL_ERROR,
        'Could not query config information.', '',
        __FILE__, __LINE__, $sql);
    while($row_gebaeude = $db->db_fetch_array($result_gebaeude)) {
        $dauer = dauer($row_gebaeude['dauer']);
        $bild_url = "bilder/gebs/" .
            (( empty($row_gebaeude['bild'])) ? "blank.gif"
                : $row_gebaeude['bild'] . ".jpg");

        echo "<tbody>\n";
        echo " <tr>\n";
        echo "  <td class='windowbg1' style='text-align:center !important'>\n";
        echo "  Ausblenden:  <input type='checkbox' name='" . $row_gebaeude['id'] . "_inactive' value='1'" . (($row_gebaeude['inactive']) ?  " checked": "") . ">\n";
        echo "  Löschen:  <input type='checkbox' name='" . $row_gebaeude['id'] . "_delete' onclick='return confirmlink(this, 'Möchten sie dieses Gebäude wirklich löschen?')' value='1'>\n";
        echo "  </td>\n";

        echo "  <td class='windowbg1'>\n";
        echo "    <input type='text' name='" . $row_gebaeude['id'] . "_name' value='" . $row_gebaeude['name']. "' required maxlength='100' size='33'>\n";
        echo "  </td>\n";

        echo "  <td class='windowbg1'>\n";
        echo "   <input type='text' name='" . $row_gebaeude['id'] . "_category' value='" . $row_gebaeude['category']. "' maxlength='50' size='22'>\n";
        echo "  </td>\n";

        echo "  <td class='windowbg1' style='text-align:center !important;'>\n";
        echo "    <input type='number' name='" . $row_gebaeude['id'] . "_idcat' value='" . $row_gebaeude['idcat']. "' min='0' maxlength='5' style='width:50px; text-align:right;'>\n";
        echo "  </td>\n";

        echo "  <td class='windowbg1' style='text-align:left !important;'>\n";
        echo "    <input type='number' name='" . $row_gebaeude['id'] . "_dauer_d' value='" . $dauer['d'] . "' min='0' maxlength='2' style='width:50px; text-align:right;'> Tage<br>\n";
        echo "    <input type='number' name='" . $row_gebaeude['id'] . "_dauer_h' value='" . $dauer['h'] . "' min='0' maxlength='2' style='width:50px; text-align:right;'> h\n";
        echo "    <input type='number' name='" . $row_gebaeude['id'] . "_dauer_m' value='"  . $dauer['m'] . "' min='0' maxlength='2' style='width:50px; text-align:right;'> min\n";
        echo "  </td>\n";

        echo "  <td class='windowbg1' style='text-align:center !important;'>\n";
        echo "    <input type='number' name='" . $row_gebaeude['id'] . "_id_iw' value='" . $row_gebaeude['id_iw'] . "' min='0' maxlength='5' style='width:50px; text-align:right;'>\n";
        echo "  </td>\n";

        echo "  <td class='windowbg1' style='text-align:middle !important;'>\n";
        echo "    <img src='" . $bild_url . "' border='0' width='50' height='50'>\n";
        echo "    <select name='" . $row_gebaeude['id'] . "_bild'>\n";
        foreach ($gebpictures as $key => $data) {
            echo "      <option value='" . $key . "'";
            if($row_gebaeude['bild'] == $key) {
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
    echo "  <td class='windowbg2' colspan='7' style='text-align:center !important;'>\n";
    echo "   <input type='submit' value='speichern' name='editgebaude' class='submit'>\n";
    echo "  </td>\n";
    echo " </tr>\n";
    echo " </tfoot>\n";
    echo "</table>\n";
    echo "</form>\n";
    echo "<br>\n";
}

echo "<br>\n";
?>
