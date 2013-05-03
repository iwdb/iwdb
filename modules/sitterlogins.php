<?php
/*****************************************************************************
 * sitterlogins.php                                                          *
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

if (($user_adminsitten != SITTEN_BOTH) && ($user_adminsitten != SITTEN_ONLY_LOGINS)) {
    die('Hacking attempt...');
}

function NumToStaatsform($num)
{
    if ($num == 1) {
        return 'Diktator';
    } elseif ($num == 2) {
        return 'Monarch';
    } elseif ($num == 3) {
        return 'Demokrat';
    } elseif ($num == 4) {
        return 'Kommunist';
    } else {
        return 'Barbar';
    }
}

$sql = "SELECT AVG(sitterpunkte) FROM " . $db_tb_user . " WHERE sitterpunkte <> 0";
$result_avg = $db->db_query($sql)
    or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
$row_avg = $db->db_fetch_array($result_avg);

$sitterlogins = array();
$sql = "SELECT sitterlogin FROM " . $db_tb_user . " WHERE sitterpwd <> '' " . (($user_status == "admin") ? "" : "AND sitten = '1' ");
$sql .= "ORDER BY sitterlogin ASC";
$result = $db->db_query($sql)
    or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
while ($row = $db->db_fetch_array($result)) {
    $sitterlogins[] = $row['sitterlogin'];
}

doc_title('Sitterlogins');

?>
<table class="table_format" style="width: 95%;">
    <tr class="titlebg center">
        <td style='width: 30%;'>
            <a href="index.php?action=sitterlogins&order=sitterlogin&ordered=asc"><img
                    src="bilder/asc.gif" alt="asc"></a>
            <b>Username</b>
            <a href="index.php?action=sitterlogins&order=sitterlogin&ordered=desc"><img
                    src="bilder/desc.gif" alt="desc"></a>
        </td>
        <td>
            <a href="index.php?action=sitterlogins&order=sitterpunkte&ordered=asc"><img
                    src="bilder/asc.gif" alt="asc"></a>
            <b>Aktivität</b>
            <a href="index.php?action=sitterlogins&order=sitterpunkte&ordered=desc"><img
                    src="bilder/desc.gif" alt="asc"></a>
        </td>
        <td>
            <b>Sitterlogin</b>
        </td>
        <td>
            <b>Besonderheiten</b>
        </td>
        <td>
            <a href="index.php?action=sitterlogins&order=lastlogin&ordered=asc"><img
                    src="bilder/asc.gif" alt="asc"></a>
            <b>letzter Login</b>
            <a href="index.php?action=sitterlogins&order=lastlogin&ordered=desc"><img
                    src="bilder/desc.gif" alt="desc"></a>
        </td>
    </tr>
    <?php
    $count = 0;
    $users = array();

    $order = getVar('order');
    $order = (empty($order)) ? "sitterpunkte" : $order;
    $ordered = getVar('ordered');
    $ordered = (empty($ordered)) ? SORT_DESC : (($ordered == "desc") ? SORT_DESC : SORT_ASC);

    $sql = "SELECT sitterlogin, sitterpwd, sitten, sitterpunkte, peitschen, staatsform, ikea, sittercomment, iwsa FROM " . $db_tb_user;
    if (!$user_fremdesitten) {
        $sql .= " WHERE allianz='" . $user_allianz . "' ";
    }
    $sql .= " ORDER BY sitterlogin ASC";
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
    while ($row = $db->db_fetch_array($result)) {
        if ((($user_status == "admin") || ($row['sitten'] == 1)) && (!empty($row['sitterpwd']))) {
            $sql = "SELECT id FROM " . $db_tb_sitterlog . " WHERE sitterlogin = '" . $user_sitterlogin . "' AND fromuser = '" . $row['sitterlogin'] . "' AND fromuser <> '" . $user_sitterlogin . "'";
            $result_punkte = $db->db_query($sql)
                or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

            $sql = "SELECT fromuser, date, MAX(date) FROM " . $db_tb_sitterlog . " WHERE sitterlogin = '" . $row['sitterlogin'] . "' AND action = 'login' GROUP BY date";
            $result_lastlogin = $db->db_query($sql)
                or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
            unset($row_lastlogin);
            while ($row_lastlogins = $db->db_fetch_array($result_lastlogin)) {
                if ($row_lastlogins['date'] == $row_lastlogins['MAX(date)']) {
                    $row_lastlogin = $row_lastlogins;
                }
            }

            $users_sitterpunkte[$count]     = $row['sitterpunkte'] + $config_sitterpunkte_friend * $db->db_num_rows($result_punkte);
            $users_sitterpunkte_anz[$count] = $row['sitterpunkte'] . " [+ " . ($config_sitterpunkte_friend * $db->db_num_rows($result_punkte)) . "]";
            $users_sitterlogin[$count]      = $row['sitterlogin'];
            $users_sitterpeitschen[$count]  = $row['peitschen'];
            $users_sitterstaatsform[$count] = $row['staatsform'];
            $users_sitterikea[$count]       = $row['ikea'];
            $users_sitteriwsa[$count]       = $row['iwsa'];
            $comments                       = explode("\n", $row['sittercomment']);
            $users_sittercomment[$count]    = trim($comments[0]);
            $users_sitten[$count]           = $row['sitten'];
            if (isset($row_lastlogin)) {
                $users_lastlogin[$count]      = $row_lastlogin['MAX(date)'];
                $users_lastlogin_user[$count] = $row_lastlogin['fromuser'];
                $users_logged_in[$count]      = (($row_lastlogin['MAX(date)'] > (CURRENT_UNIX_TIME - $config_sitterlogin_timeout)) && ($row_lastlogin['fromuser'] != $user_sitterlogin)) ? $row_lastlogin['fromuser'] : "";
            } else {
                $users_lastlogin[$count]      = 0;
                $users_lastlogin_user[$count] = '';
                $users_logged_in[$count]      = '';
            }
            $count++;
        }
    }

    if ($count > 0) {
        if (($order == "sitterlogin") && ($ordered == SORT_DESC)) {
            krsort($users_sitterlogin);
        }
        if ($order == "sitterpunkte") {
            array_multisort($users_sitterpunkte, $ordered, SORT_NUMERIC, $users_sitterlogin, $users_sitterpunkte_anz, $users_lastlogin, $users_lastlogin_user, $users_logged_in, $users_sitterpeitschen, $users_sitterstaatsform, $users_sitterikea, $users_sitteriwsa, $users_sittercomment, $users_sitten);
        }
        if ($order == "lastlogin") {
            array_multisort($users_lastlogin, $ordered, SORT_NUMERIC, $users_sitterlogin, $users_sitterpunkte, $users_sitterpunkte_anz, $users_lastlogin_user, $users_logged_in, $users_sitterpeitschen, $users_sitterstaatsform, $users_sitterikea, $users_sitteriwsa, $users_sittercomment, $users_sitten);
        }

        $count = 0;
        $num   = 1;
        foreach ($users_sitterlogin as $key => $data) {
            if ($count == 3) {
                $num   = ($num == 1) ? 2 : 1;
                $count = 1;
            } else {
                $count++;
            }
            ?>
            <tr>
                <td class="windowbg<?php echo $num;?> top">
                    <?php
                    if ($user_status == "admin") {
                        echo "<a href='index.php?action=profile&sitterlogin=" . urlencode($data) . "'>" . $data . "</a>";
                    } else {
                        echo $data;
                    }

                    if (!empty ($users_sittercomment[$key])) {
                        echo "<br><font size='1'><i>[" . convert_bbcode($users_sittercomment[$key]) . "]</i></font>";
                    }
                    ?>
                </td>
                <td class="windowbg<?php echo $num;?> top">
                    <?php echo ($users_sitterpunkte[$key] > (3 * round($row_avg['AVG(sitterpunkte)']))) ? "<img src='".BILDER_PATH."star1.gif' alt='star1' class='middle'>" : (($users_sitterpunkte[$key] > (2 * round($row_avg['AVG(sitterpunkte)']))) ? "<img src='".BILDER_PATH."star2.gif' alt='star2' class='middle'>" : (($users_sitterpunkte[$key] > round($row_avg['AVG(sitterpunkte)'])) ? "<img src='".BILDER_PATH."star3.gif'  alt='star3' class='middle'>" : "")); echo $users_sitterpunkte_anz[$key];?>
                </td>
                <td class="windowbg<?php echo $num;?> top">
                    <?php
                    if (!empty($users_logged_in[$key])) {
                        echo "<b><font color='#ff0000'>" . $users_logged_in[$key] . " ist eingeloggt </font></b>" .
                            "<br/><a href='index.php?action=sitterlogins&sitterlogin=" . urlencode($data) .
                            "' target='_blank' onclick='return confirmlink(this, " .
                            "'Jemand ist gerade im Account eingeloggt. Trotzdem einloggen?'" .
                            ")'>[trotzdem einloggen]</a>" .
                            "<td class='windowbg" . $num . " top'>" .
                            ((!empty($users_sitterstaatsform[$key])) ? " <i>Staatsform: " . NumToStaatsform($users_sitterstaatsform[$key]) . "</i><br/>" : "") .
							(($users_sitterpeitschen[$key] == "1") ? " <i><font color=red>Meister der Peitschen</font></i><br/>" : "") .
                            (($users_sitterikea[$key] == "M") ? " <i><font color=blue>Meister des Ikea</font></i><br/>" : "") .
                            (($users_sitterikea[$key] == "L") ? " <i><font color=blue>Lehrling des Ikea</font></i><br/>" : "") .
                            (($users_sitteriwsa[$key] == "1") ? " <i><font color=green>IWSA/IWBP-Account</font><br/></i>" : "") .
                            "</td>";
                    } elseif ((($user_status == "admin") OR ($user_status == "SV")) && (empty($users_sitten[$key]))) {
                        echo "<a href='index.php?action=sitterlogins&sitterlogin=" . urlencode($data) .
                            "' target='_blank' onclick='return confirmlink(this, " .
                            "'Dieser User hat das Sitten deaktiviert. Trotzdem einloggen?'" .
                            ")'>[sitten deaktiviert - einloggen]</a> " .
                            "<a href='index.php?action=sitterauftrag&sitterid=" .
                            urlencode($data) . "'><img src='".BILDER_PATH."file_new_s.gif' " .
                            "alt='Sitterauftrag erstellen' title='Sitterauftrag erstellen'></a>" .
                            " <a href='index.php?action=sitterhistory&selecteduser=" .
                            urlencode($data) . "'><img src='".BILDER_PATH."file_history.gif' " .
                            "alt='Sitterhistorie anschauen' title='Sitterhistorie anschauen'></a>" .
                            "<td class='windowbg" . $num . " top'>" .
                            ((!empty($users_sitterstaatsform[$key])) ? " <i>Staatsform: " . NumToStaatsform($users_sitterstaatsform[$key]) . "</i><br/>" : "") .
							(($users_sitterpeitschen[$key] == "1") ? " <i><font color=red>Meister der Peitschen</font></i><br/>" : "") .
                            (($users_sitterikea[$key] == "M") ? " <i><font color=blue>Meister des Ikea</font></i><br/>" : "") .
                            (($users_sitterikea[$key] == "L") ? " <i><font color=blue>Lehrling des Ikea</font></i><br/>" : "") .
                            (($users_sitteriwsa[$key] == "1") ? " <i><font color=green>IWSA/IWBP-Account</font><br/></i>" : "") .
                            "</td>";
                    } else {
                        echo "<a href='index.php?action=sitterlogins&sitterlogin=" . urlencode($data) .
                            "' target='_blank'>[jetzt einloggen]</a>&nbsp;" .
                            "<a href='index.php?action=sitterauftrag&sitterid=" . urlencode($data) .
                            "'><img src='".BILDER_PATH."file_new_s.gif' " .
                            "alt='Sitterauftrag erstellen' title='Sitterauftrag erstellen'></a>" .
                            " <a href='index.php?action=sitterhistory&selecteduser=" .
                            urlencode($data) . "'><img src='".BILDER_PATH."file_history.gif' " .
                            "alt='Sitterhistorie anschauen' title='Sitterhistorie anschauen'></a>" .
                            "<td class='windowbg" . $num . " top'>" .
                            ((!empty($users_sitterstaatsform[$key])) ? " <i>Staatsform: " . NumToStaatsform($users_sitterstaatsform[$key]) . "</i><br/>" : "") .
							(($users_sitterpeitschen[$key] == "1") ? " <i><font color=red>Meister der Peitschen</font></i><br/>" : "") .
                            (($users_sitterikea[$key] == "M") ? " <i><font color=blue>Meister des Ikea</font></i><br/>" : "") .
                            (($users_sitterikea[$key] == "L") ? " <i><font color=blue>Lehrling des Ikea</font></i><br/>" : "") .
                            (($users_sitteriwsa[$key] == "1") ? " <i><font color=green>IWSA/IWBP-Account</font><br/></i>" : "") .
                            "</td>";
                    }
                    ?>

                </td>
                <td class="windowbg<?php echo $num;?> top">
                    <?php echo (empty($users_lastlogin_user[$key])) ? "" : strftime(CONFIG_DATETIMEFORMAT, $users_lastlogin[$key]) . "<br> von: " . $users_lastlogin_user[$key];?>
                </td>
            </tr>
        <?php
        }
    } else { // $count == 0
        doc_message('Keine Sitterdaten gefunden!');
    }
    ?>
</table>
<br>
