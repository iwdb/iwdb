<?php
/*****************************************************************************
 * profile_editplaneten.php                                                  *
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
doc_title('Planeten von '.$id);

if (!empty($sitterlogin)) {

    $editplaneten = getVar('editplaneten');
    if (!empty($editplaneten)) {
        echo "<div class='system_notification'>Planetendaten aktualisiert.</div>";
        $sql = "SELECT t1.* FROM " . $db_tb_sitterauftrag . " as t1 LEFT JOIN " . $db_tb_sitterauftrag . " as t2 ON t1.id = t2.refid WHERE t2.refid is null AND t1.user='" . $sitterlogin . "'";
        $result = $db->db_query($sql)
            or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
        while ($row = $db->db_fetch_array($result)) {
            if ($row['typ'] == "Gebaeude") {
                dates($row['id'], $sitterlogin);
            }
        }
    }
    ?>
    <br>
    <form method="POST" action="index.php?action=profile&uaction=editplaneten&sid=<?php echo $sid;?>"
          enctype="multipart/form-data">
        <table class="table_format" style="width: 80%;">
            <tr>
                <td class="windowbg2" style="width:10%;">
                    Koordinaten
                </td>
                <td class="windowbg2" style="width:20%;">
                    Planetenname
                </td>
                <td class="windowbg2" style="width:10%;">
                    Objekt
                </td>
                <td class="windowbg2" style="width:20%;">
                    Schiffsbaudauermod.
                </td>
                <td class="windowbg2" style="width:20%;">
                    Gebäudebaudauermod.
                </td>
                <td class="windowbg2" style="width:10%;">
                    BG-Farbe<br>[#Hexwert]
                </td>
                <td class="windowbg2" style="width:10%;">
                    Sort.<br>[0-99]
                </td>
            </tr>
            <?php
            // Ausgabe der Presets und Löschlink //
            $sql = "SELECT coords, planetenname, objekt, dsmod, dgmod, planet_farbe, sortierung FROM " . $db_tb_scans . " WHERE user LIKE '" . $sitterlogin . "'";

            $result = $db->db_query($sql)
                or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
            while ($row = $db->db_fetch_array($result)) {
                if (!empty($editplaneten)) {
                    $temp         = $row['coords'] . '_dsmod';
                    $coords_dsmod = getVar($temp);

                    $temp         = $row['coords'] . '_dgmod';
                    $coords_dgmod = getVar($temp);

                    $temp                = $row['coords'] . '_planet_farbe';
                    $coords_planet_farbe = getVar($temp);

                    $temp              = $row['coords'] . '_sortierung';
                    $coords_sortierung = getVar($temp);

                    $sql = "UPDATE " . $db_tb_scans . " SET dsmod='" . str_replace(",", ".", $coords_dsmod) . "', dgmod='" . str_replace(",", ".", $coords_dgmod) . "', planet_farbe='" . str_replace(",", ".", $coords_planet_farbe) . "', sortierung='" . str_replace(",", ".", $coords_sortierung) . "' WHERE (coords='" . $row['coords'] . "' AND user LIKE '" . $sitterlogin . "')";
                    $result_planet = $db->db_query($sql)
                        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
                    $row['dsmod']        = str_replace(",", ".", $coords_dsmod);
                    $row['dgmod']        = str_replace(",", ".", $coords_dgmod);
                    $row['planet_farbe'] = str_replace(",", ".", $coords_planet_farbe);
                    $row['sortierung']   = str_replace(",", ".", $coords_sortierung);
                }
                ?>
                <tr>
                    <td class="windowbg1">
                        <?php echo $row['coords'];?>
                    </td>
                    <td class="windowbg1">
                        <?php echo $row['planetenname'];?>
                    </td>
                    <td class="windowbg1">
                        <?php echo $row['objekt'];?>
                    </td>
                    <td class="windowbg1">
                        <input type="text" name="<?php echo $row['coords'];?>_dsmod" value="<?php echo $row['dsmod'];?>"
                               style="width: 15em">
                    </td>
                    <td class="windowbg1">
                        <input type="text" name="<?php echo $row['coords'];?>_dgmod" value="<?php echo $row['dgmod'];?>"
                               style="width: 15em">
                    </td>
                    <td class="windowbg1">
                        <input type="text" name="<?php echo $row['coords'];?>_planet_farbe"
                               value="<?php echo $row['planet_farbe'];?>" style="width: 5em">
                    </td>
                    <td class="windowbg1">
                        <input type="text" name="<?php echo $row['coords'];?>_sortierung"
                               value="<?php echo $row['sortierung'];?>" style="width: 5em">
                    </td>
                </tr>
            <?php
            }
            ?>
            <tr>
                <td colspan="7" class="titlebg center">
                    <input type="hidden" name="sitterlogin" value="<?php echo $sitterlogin;?>">
                    <input type="submit" value="speichern" name="editplaneten" class="submit">
                </td>
            </tr>
        </table>
    </form>
    <br>
<?php
} else {
    doc_message('Kein IW Account angegeben, kein Einstellen der Planeten möglich.');
}