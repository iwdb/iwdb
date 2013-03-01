<?php
/*****************************************************************************
 * menu_default.php                                                          *
 *****************************************************************************
 * Iw DB: Icewars geoscan and sitter database                                *
 * Open-Source Project started by Robert Riess (robert@riess.net)            *
 * ========================================================================= *
 * Copyright (c) 2007 Erik Frohne - All Rights Reserved                     *
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
 * Bei Problemen kannst du dich an das eigens dafür eingerichtete            *
 * Entwicklerforum/Repo wenden:                                              *
 *        https://handels-gilde.org/?www/forum/index.php;board=1099.0        *
 *                   https://github.com/iwdb/iwdb                            *
 *                                                                           *
 *****************************************************************************/

if (!defined('IRA')) {
    die('Hacking attempt...');
}

$anzauftrag_sitter = "";
$anzauftrag_schiffe = "";
$anzauftrag_ress = "";
$anz_angriffe = "";
$anz_sondierungen = "";
$anz_incomings = "";

if (($user_adminsitten == SITTEN_BOTH) || ($user_adminsitten == SITTEN_ONLY_LOGINS)) {
    $sql = "SELECT count(*) AS anzahl FROM " . $db_tb_sitterauftrag .
        " WHERE date_b2 < " . CURRENT_UNIX_TIME;
    if (!$user_fremdesitten) {
        $sql .= " AND (SELECT allianz FROM " . $db_tb_user . " WHERE " . $db_tb_user . ".id=" . $db_tb_sitterauftrag . ".user) = '" . $user_allianz . "'";
    }
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
    $row    = $db->db_fetch_array($result);
    $anzahl = $row['anzahl'];
    $db->db_free_result($result);

    // Wurde gerade ein Auftrag erledigt? Falls ja, muss dieser von der
    // gerade ermittelten Zahl abgezogen werden.
    if ($action == "sitterliste") {
        $erledigt = getVar('erledigt');

        if (!empty($erledigt)) {
            $anzahl = $anzahl - 1;
        }
    }

    if ($anzahl > 0) {
        $anzauftrag_sitter = " (" . $anzahl . " offen)";
    }
}

if (isset($db_tb_bestellung_schiffe)) {
    $sql = "SELECT COUNT(*) AS anzahl FROM " . $db_tb_bestellung_schiffe . " WHERE erledigt=0";
    $sql .= " AND (SELECT allianz FROM " . $db_tb_user . " WHERE " . $db_tb_user . ".id=" . $db_tb_bestellung_schiffe . ".user) = '" . $user_allianz . "'";
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
    $row    = $db->db_fetch_array($result);
    $anzahl = $row['anzahl'];
    $db->db_free_result($result);
    if ($anzahl > 0) {
        $anzauftrag_schiffe = " (" . $anzahl . " offen)";
    }
} else {
    $anzauftrag_schiffe = "";
}

if (isset($db_tb_bestellung)) {
    $sql = "SELECT COUNT(*) AS anzahl FROM " . $db_tb_bestellung . " WHERE erledigt=0";
    $sql .= " AND (SELECT allianz FROM " . $db_tb_user . " WHERE " . $db_tb_user . ".id=" . $db_tb_bestellung . ".user) = '" . $user_allianz . "'";
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
    $row    = $db->db_fetch_array($result);
    $anzahl = $row['anzahl'];
    $db->db_free_result($result);
    if ($anzahl > 0) {
        $anzauftrag_ress = " (" . $anzahl . " offen)";
    }
} else {
    $anzauftrag_ress = "";
}

$sql = "SELECT COUNT(*) AS 'anzahl' FROM $db_tb_lieferung, $db_tb_user WHERE art='Angriff' AND $db_tb_lieferung.user_to=$db_tb_user.id AND $db_tb_lieferung.time>" . (CURRENT_UNIX_TIME - 15 * MINUTE);
$result = $db->db_query($sql)
    or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
$row = $db->db_fetch_array($result);
$anzahl = $row['anzahl'];
$db->db_free_result($result);
if ($anzahl > 0) {
    $anz_angriffe = " (<span style='color:red;'>" . $anzahl . "</span>)";
} else {
    $anz_angriffe = "";
}

$sql = "SELECT COUNT(*) AS 'anzahl'"
    . " FROM `{$db_tb_lieferung}` AS lieferung, `{$db_tb_user}` AS user"
    . " WHERE (`lieferung`.`art` = 'Sondierung (Schiffe/Def/Ress)' OR `lieferung`.`art` = 'Sondierung (Gebäude/Ress)')"
    . " AND `lieferung`.`user_to` = `user`.`id`"
    . " AND `lieferung`.`time` > " . (CURRENT_UNIX_TIME - 5 * MINUTE);
$result = $db->db_query($sql)
    or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
$row = $db->db_fetch_array($result);
$anzahl = $row['anzahl'];
$db->db_free_result($result);
if ($anzahl > 0) {
    $anz_sondierungen = " (<span style='color:red;'>" . $anzahl . "</span>)";
} else {
    $anz_sondierungen = "";
}

echo "<div id='iwdb_notices'>";

if (isset($db_tb_incomings)) {
    $sql = "SELECT COUNT(*) AS 'anzahl' FROM $db_tb_incomings WHERE (art='Sondierung (Schiffe/Def/Ress)' OR art='Sondierung (Gebäude/Ress)') AND arrivaltime >" . (CURRENT_UNIX_TIME - 5 * MINUTE);
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
    $row = $db->db_fetch_array($result);

    $anzahl = $row['anzahl'];
    $db->db_free_result($result);
    $anz_incomings1 = $anzahl;

    $sql = "SELECT COUNT(*) AS 'anzahl' FROM $db_tb_incomings WHERE art='Angriff' AND arrivaltime >" . (CURRENT_UNIX_TIME - 15 * MINUTE);
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
    $row  = $db->db_fetch_array($result);

    $anzahl = $row['anzahl'];
    $db->db_free_result($result);
    $anz_incomings2 = $anzahl;

    $anz_incomings = $anz_incomings1 + $anz_incomings2;
    if ($anz_incomings > 0) {
        $anz_incomings = " (<span style='color:red;'>" . $anz_incomings . "</span>)";
    } else {
        $anz_incomings = "";
    }
}

include ('configmenu.php');

//Hinweis für zur Verfügung stehenden UniXML-Scan im IW Account
if (!empty($user_sitterlogin)) {
    $sql = "SELECT `NewUniXmlTime` FROM `$db_tb_user` WHERE `sitterlogin`='" . $user_sitterlogin . "';";
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query NewUniXmlTime information.', '', __FILE__, __LINE__, $sql);
    $row = $db->db_fetch_array($result);
    if (!empty($row['NewUniXmlTime']) AND ($row['NewUniXmlTime'] <= CURRENT_UNIX_TIME)) {
        echo "<div style='color:red; font-weight:bold; font-size:1.5em; border: 2px solid red; padding: 2px; margin: 5px 0px;'>Der Universumsscan als XML Datei steht zur Verfügung!</div>";
    }
}
//Warnung für nicht eingelesene Ressourcenkoloübersicht seit 24 Stunden
$sql = "SELECT MAX(time) AS time FROM `" . $db_tb_lager . "` WHERE `user`='" . $user_id . "';";
$result = $db->db_query($sql)
    or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
$row = $db->db_fetch_array($result);
if ($row['time'] < (CURRENT_UNIX_TIME - 24 * HOUR)) {
    echo "<div style='color:red; font-weight:bold; font-size:1.5em; border: 2px solid red; padding:2px; margin: 5px 0px;'>Deine Ressourcenkoloübersicht wurde seit 24h nicht mehr aktualisiert!</div>";
}

// Warnung nicht eingelesene Highscore seit 24 Stunden
$sql = "SELECT MAX(time) AS time FROM `" . $db_tb_highscore . "`;";
$result = $db->db_query($sql)
    or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
$row = $db->db_fetch_array($result);
if ($row['time'] < (CURRENT_UNIX_TIME - 24 * HOUR)) {
    echo "<div style='color:red; font-weight:bold; font-size:1.5em; border: 2px solid red; padding:2px; margin: 5px 0px;'>Die Highscore wurde seit über 24h nicht mehr aktualisiert!</div>";
}

//Warnung für nicht eingelesene Schiffsübersicht seit 48 Stunden
$sql = "SELECT `lastshipscan` FROM " . $db_tb_user . " WHERE `id`='" . $user_id . "' LIMIT 1;";
$result = $db->db_query($sql)
    or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
$row = $db->db_fetch_array($result);
if ($row['lastshipscan'] < (CURRENT_UNIX_TIME - 48 * HOUR)) {
    echo "<div style='color:red; font-weight:bold; font-size:1.5em; border: 2px solid red; padding:2px; margin: 5px 0px;'>Deine Schiffsübersicht wurde seit 48h nicht mehr aktualisiert!</div>";
}

//Warnung für nicht eingelesene Gebäudeübersicht seit 48 Stunden
$sql = "SELECT `time` FROM `" . $db_tb_gebaeude_spieler . "` WHERE `user`='" . $user_id . "' LIMIT 1";
$result = $db->db_query($sql)
    or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
$row = $db->db_fetch_array($result);
if ($row['time'] < (CURRENT_UNIX_TIME - 48 * HOUR)) {
    echo "<div style='color:red; font-weight:bold; font-size:1.5em; border: 2px solid red; padding:2px; margin: 5px 0px;'>Deine Gebäudeübersicht wurde seit 48h nicht mehr aktualisiert!</div>";
}

// Warnung nicht eingelesene Allikasse seit 24 Stunden
$sql = "SELECT UNIX_TIMESTAMP(MAX(time_of_insert)) AS time FROM `" . $db_tb_kasse_content . '`;';
$result = $db->db_query($sql)
    or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
$row = $db->db_fetch_array($result);

if ((CURRENT_UNIX_TIME - 24 * HOUR) > $row['time']) {
    echo "<div style='color:red; font-weight:bold; font-size:1.5em; border: 2px solid red; padding:2px; margin: 5px 0px;'>Die Allianzkasse wurde seit über 24h nicht mehr aktualisiert!</div>";
}

// Warnung nicht eingelesene Mitgliederliste seit 96 Stunden
$sql = "SELECT MAX(date) AS time FROM " . $db_tb_punktelog . '`;';
$result = $db->db_query($sql)
    or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
$row = $db->db_fetch_array($result);
if ($row['time'] < (CURRENT_UNIX_TIME - 96 * HOUR)) {
    echo "<div style='color:red; font-weight:bold; font-size:1.5em; border: 2px solid red; padding:2px; margin: 5px 0px;'>Die Mitgliederliste wurde seit über 96h nicht mehr aktualisiert!</div>";
}

echo "</div>";

?>
<table style='width:100%; margin:0.5em 0; border: 0;'>
    <tr>
        <td id="doc_greeting">Hallo, <?php echo $user_id;?>.</td>
        <td id="doc_usersonline">
            Online: <?php echo $onlineUsers['counter_member'] . " (" . $onlineUsers['strOnlineMember'] . ")";?></td>
        <td id="doc_mainmenu">

            <a href="index.php"><img
                    src="bilder/icon_mini_home.gif" width="12" height="13"
                    alt="Startseite">&nbsp;<span>Startseite</span></a> |
            <a href="index.php?action=memberlogout2"><img
                    src="bilder/icon_mini_login.gif" width="12" height="13"
                    alt="login">&nbsp;<span>logout</span></a> |
            <a href="index.php?action=profile"><img
                    src="bilder/icon_mini_profile.gif" width="12" height="13"
                    alt="profil">&nbsp;<span>profil</span></a> |
            <a href="index.php?action=help&topic=<?php echo $action;?>"><img
                    src="bilder/icon_mini_search.gif" width="12" height="13"
                    alt="profile">&nbsp;<span>hilfe</span></a>
            <?php

            if ($user_status == "admin") {
                ?>
                |
                <a href="index.php?action=admin"><img src="bilder/icon_mini_members.gif" width="12" height="13" alt="admin">&nbsp;<span>admin</span></a>
            <?php
            }
            ?>
        </td>
    </tr>
</table>
<table align="center" style="width:100%;">
    <tr>
        <td width="12%" valign="top" class='doc_menu'>
            <?php

            // Menu auslesen
            $sql = "SELECT menu, submenu, title, status, action, extlink, sittertyp FROM " .
                $db_tb_menu . " WHERE active=1 ORDER BY menu ASC, submenu ASC";
            $result = $db->db_query($sql)
                or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

            $lastmenu = 0;
            $tableopen = 0;
            $insidetable = 0;

            // Alle Menu-Eintraege durchgehen
            while ($row = $db->db_fetch_array($result)) {
                // Ist sitten für diesen Menu-Eintrag erlaubt?
                $sitterentry = ($user_adminsitten == SITTEN_BOTH)
                    || ($row['sittertyp'] == 0)
                    || ($user_adminsitten == SITTEN_ONLY_LOGINS
                        && ($row['sittertyp'] == 1 || $row['sittertyp'] == 3))
                    || ($user_adminsitten == SITTEN_ONLY_NEWTASKS
                        && ($row['sittertyp'] == 2 || $row['sittertyp'] == 3));

                // Falls nicht, mit dem naechsten Eintrag weitermachen.
                if (!$sitterentry) {
                    continue;
                }

                // Hat der angemeldete Benutzer die entsprechende Berechtigung?
                if (($row['status'] == "") || ($user_status == "admin") || ($user_status == $row['status'])) {
                    // Neues Hauptmenü?
                    if ($lastmenu != $row['menu']) {
                        // Bin ich noch in der vorhergehenden Tabelle? Dann entsprechend schliessen.
                        if ($tableopen != 0) {
                            if ($insidetable != 0) {
                                echo " </td>\n";
                            }
                            echo " </tr>\n</table><br>\n";
                        }

                        // Neue Tabelle aufmachen.
                        echo "<table width='100%' border='0' cellpadding='0' cellspacing='1' class='bordercolor'>\n <tr>\n";
                        $tableopen   = 1;
                        $insidetable = 0;
                        $lastmenu    = $row['menu'];
                    }

                    $title = $row['title'];
                    $title = str_replace("#sitter", $anzauftrag_sitter, $title);
                    $title = str_replace("#schiffe", $anzauftrag_schiffe, $title);
                    $title = str_replace("#ress", $anzauftrag_ress, $title);
                    if (!empty($anz_angriffe) AND strpos($title, '#angriffe')) {
                        $title = "<span style='font-weight: bold;'>" . str_replace("#angriffe", $anz_angriffe, $title) . "</span>";
                    } else {
                        $title = str_replace("#angriffe", $anz_angriffe, $title);
                    }

                    if (!empty($anz_sondierungen) AND strpos($title, '#sondierungen')) {
                        $title = "<span style='font-weight: bold;'>" . str_replace("#sondierungen", $anz_sondierungen, $title) . "</span>";
                    } else {
                        $title = str_replace("#sondierungen", $anz_sondierungen, $title);
                    }

                    if (!empty($anz_incomings) AND strpos($title, '#incomings')) {
                        $title = "<span style='font-weight: bold;'>" . str_replace("#incomings", $anz_incomings, $title) . "</span>";
                    } else {
                        $title = str_replace("#incomings", $anz_incomings, $title);
                    }

                    // Habe ich hier den neuen Hauptmenu-Titel?
                    if ($row['submenu'] == 0) {
                        // Ja, dann in entsprechender Formatierung ausgeben.
                        echo " <td class='titlebg' style='padding: 3px;'><b>" . $title . "</b></td>\n" .
                            " </tr>\n" .
                            " <tr>\n";
                    } else {
                        // Kein Hauptmenu. Eintraege in einzelne Tabellenzelle zusammenfassen.
                        if ($insidetable == 0) {
                            echo " <td class='menu'>\n";
                            $insidetable = 1;
                        }

                        if ($row['extlink'] == "n") {
                            // interner Link
                            echo "<a href='index.php?action=" . $row['action'] . "'>" . $title . "</a>";
                        } else {
                            // externer Link
                            echo "<a href='" . $row['action'] . "' target=_new>" . $title . "</a>";
                        }

                    }
                }
            }

            // Restliche Tabelle wieder schliessen.
            if ($tableopen != 0) {
                if ($insidetable != 0) {
                    echo " </td>\n";
                }
                echo " </tr>\n</table><br>\n";
            }

            ?>
        </td>
        <td width="2%">&nbsp;</td>
        <td width="86%" valign="top" align="center">
            <table class="doc_main" align="center">
                <tr>
                    <td class="doc_main" align="center">