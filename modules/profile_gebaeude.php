<?php
/*****************************************************************************
 * profile_editgebaeude.php                                                  *
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

doc_title('Gebäude ausblenden');

function dauer($zeit)
{
    $tage    = floor($zeit / DAY);
    $return  = ($tage > 0) ? $tage . " Tage, " : "";
    $stunden = floor(($zeit - $tage * DAY) / HOUR);
    $minuten = ($zeit - $tage * DAY - $stunden * HOUR) / MINUTE;
    $return .= str_pad($stunden, 2, "0", STR_PAD_LEFT) . ":" . str_pad($minuten, 2, "0", STR_PAD_LEFT);

    return $return;
}

$editgebaeude = getVar('editgebaeude');
if (!empty($editgebaeude)) {
    echo "<div class='system_notification'>Gebäude aktualisiert.</div>";
}
?>
<br>
<form method="POST" action="index.php?action=profile&uaction=gebaeude&sid=<?php echo $sid;?>"
      enctype="multipart/form-data">
    <?php
    $inactive = (empty($editgebaeude)) ? $user_gebaeude : "";

    $sql = "SELECT gengebmod, genmaurer FROM " . $db_tb_user . " WHERE sitterlogin = '" . $sitterlogin . "'";
    $result_user = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
    $row_user = $db->db_fetch_array($result_user);

    $sql = "SELECT category FROM " . $db_tb_gebaeude . " GROUP BY category ORDER BY category asc";
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
    while ($row = $db->db_fetch_array($result)) {
        ?>
        <table border="0" cellpadding="4" cellspacing="1" class="bordercolor" style="width: 90%;">
            <tr>
                <td class="titlebg" align="center" colspan="4">
                    <b><?php echo (empty($row['category'])) ? "Sonstige" : $row['category'];?></b>
                </td>
            </tr>
            <tr>
                <td class="windowbg2" style="width:5%;">
                    ausblenden
                </td>
                <td class="windowbg2" style="width:10%;" align="center">
                    &nbsp;
                </td>
                <td class="windowbg2" style="width:20%;">
                    Name
                </td>
                <td class="windowbg2" style="width:65%;">
                    Baudauer
                </td>
            </tr>
            <?php
            $sql = "SELECT bild, name, id, dauer, category FROM " . $db_tb_gebaeude . " WHERE category='" . $row['category'] . "' AND inactive <> '1' ORDER BY idcat ASC";
            $result_gebaeude = $db->db_query($sql)
                or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

            while ($row_gebaeude = $db->db_fetch_array($result_gebaeude)) {
                $modmaurer = (($row_user['genmaurer'] == 1) && ((strpos($row_gebaeude['category'], "Bunker") !== false) || (strpos($row_gebaeude['category'], "Lager") !== false))) ? 0.5 : 1;

                $dauer = dauer($row_gebaeude['dauer'] * $row_user['gengebmod'] * $modmaurer);
                if (!empty($editgebaeude)) {
                    $geb_inactive = getVar(($row_gebaeude['id'] . '_inactive'));
                    $inactive .= ($geb_inactive) ? "|" . $row_gebaeude['id'] . "|" : "";
                }
                ?>
                <tr>
                    <td class="windowbg1" align="center">
                        <input type="checkbox" name="<?php echo $row_gebaeude['id'];?>_inactive"
                               value="1"<?php echo (strpos($inactive, "|" . $row_gebaeude['id'] . "|") !== false) ? " checked" : "";?>>
                    </td>
                    <td class="windowbg1" align="center">
                        <?php
                        if ($user_gebbilder == "1") {
                            $bild_url = (empty($row_gebaeude['bild'])) ? "bilder/gebs/blank.jpg" : "bilder/gebs/" . $row_gebaeude['bild'] . ".jpg";
                            ?>
                            <img src="<?php echo $bild_url;?>" width="50" height="50"
                                 style="vertical-align:middle;">
                        <?php
                        }
                        ?>
                    </td>
                    <td class="windowbg1">
                        <?php
                        if (defined('RESEARCH') && RESEARCH === true) {
                            $resid = find_research_for_building($row_gebaeude['id']);
                            if ($resid == 0) {
                                $resRowName = $row_gebaeude['name'];
                            } else {
                                $resRowName = "<a href='index.php?action=research&researchid=" . $resid . "&sid=" . $sid . "'>" . $row_gebaeude['name'] . "</a>";
                            }
                            echo $resRowName;
                        } else {
                            echo $row_gebaeude['name'];
                        }
                        ?>
                    </td>
                    <td class="windowbg1">
                        <?php echo $dauer;?>
                    </td>
                </tr>
            <?php
            }
            ?>
        </table>
        <br>
    <?php

    }
    if (!empty($editgebaeude)) {
        $sql = "UPDATE " . $db_tb_user . " SET gebaeude='" . $inactive . "' WHERE sitterlogin = '" . $sitterlogin . "'";
        $result = $db->db_query($sql)
            or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
    }
    ?>
    <table border="0" cellpadding="4" cellspacing="1" class="bordercolor" style="width: 90%;">
        <tr>
            <td class="titlebg" align="center">
                <input type="hidden" name="sitterlogin" value="<?php echo $sitterlogin;?>"><input type="hidden"
                                                                                                  name="editgebaeude"
                                                                                                  value="true"><input
                    type="submit" value="speichern" name="B1" class="submit">
            </td>
        </tr>
    </table>
</form>
<br>
